<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class AttendanceProcessor
{
    /*
    |--------------------------------------------------------------------------
    | MAIN ENTRY
    |--------------------------------------------------------------------------
    */

    public function process($nameId, $startDate, $endDate)
    {
        $employee = Employee::where('name_id', $nameId)->first();
        if (!$employee) return [];

        $schedId = $employee->sched_id;

        $logs = $this->mergedAttendance($nameId, $startDate, $endDate);

        return collect($logs)
            ->groupBy('date')
            ->map(function ($dayLogs, $date) use ($schedId) {

                return $this->computeDaily($dayLogs, $schedId, $date);

            })
            ->values()
            ->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | MERGE LOCAL + API
    |--------------------------------------------------------------------------
    */

    private function mergedAttendance($nameId, $startDate, $endDate)
    {
        $bioId = Employee::where('name_id', $nameId)->value('bio_id');
        if (!$bioId) return [];

        $local = Attendance::where('name_id', $nameId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get(['name_id','date','timestamp'])
            ->map(fn($r) => [
                'name_id'   => $r->name_id,
                'date'      => $r->date,
                'timestamp' => $r->timestamp,
            ]);

        $response = Http::timeout(10)->get('http://172.31.10.43:5000/attendance/filter', [
            'name_id'    => $bioId,
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ]);

        $api = collect($response->json('data', []))
            ->map(fn($r) => [
                'name_id'   => $nameId,
                'date'      => $r['date'],
                'timestamp' => $r['timestamp'],
            ]);

        return collect($local)
            ->merge($api)
            ->unique(fn($r) => $r['name_id'].'|'.$r['date'].'|'.$r['timestamp'])
            ->reject(fn($r) => $r['date'] === '2000-01-01')
            ->sortBy('timestamp')
            ->values()
            ->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | DAILY COMPUTATION
    |--------------------------------------------------------------------------
    */

    private function computeDaily($dayLogs, $schedId, $date)
    {
        $logs = collect($dayLogs)->sortBy('timestamp')->values();

        if ($logs->isEmpty()) {
            return [
                'date' => $date,
                'status' => 'ABSENT'
            ];
        }

        $firstIn  = Carbon::parse($logs->first()['timestamp']);
        $lastOut  = Carbon::parse($logs->last()['timestamp']);

        $dayOfWeek = strtolower(Carbon::parse($date)->format('l'));
        $config = $this->getScheduleConfig($schedId, $dayOfWeek);

        /*
        |--------------------------------------------------------------------------
        | TARDINESS
        |--------------------------------------------------------------------------
        */

        $tardyMinutes = 0;

        if (!isset($config['no_tardy']) || !$config['no_tardy']) {

            $maxIn = Carbon::parse($date.' '.$config['max_in']);

            if ($firstIn->gt($maxIn)) {
                $tardyMinutes = $firstIn->diffInMinutes($maxIn);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | LUNCH BREAK (12PM-1PM FIXED)
        |--------------------------------------------------------------------------
        */

        $lunchStart = Carbon::parse($date.' 12:00:00');
        $lunchEnd   = Carbon::parse($date.' 13:00:00');

        $lunchOut = $logs->first(fn($log) =>
            Carbon::parse($log['timestamp'])->between($lunchStart, $lunchEnd)
        );

        $lunchIn = $logs->last(fn($log) =>
            Carbon::parse($log['timestamp'])->between($lunchStart, $lunchEnd)
        );

        $overbreakMinutes = 0;

        if ($lunchOut && $lunchIn) {

            $out = Carbon::parse($lunchOut['timestamp']);
            $in  = Carbon::parse($lunchIn['timestamp']);

            $breakMinutes = $in->diffInMinutes($out);

            if ($breakMinutes > 60) {
                $overbreakMinutes = $breakMinutes - 60;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | WORK HOURS
        |--------------------------------------------------------------------------
        */

        $workMinutes = $lastOut->diffInMinutes($firstIn);

        // subtract fixed 1 hour lunch
        $workMinutes -= 60;

        // subtract overbreak excess
        $workMinutes -= $overbreakMinutes;

        /*
        |--------------------------------------------------------------------------
        | UNDERTIME
        |--------------------------------------------------------------------------
        */

        $requiredMinutes = $config['hours'] * 60;

        $undertimeMinutes = 0;

        if ($workMinutes < $requiredMinutes) {
            $undertimeMinutes = $requiredMinutes - $workMinutes;
        }

        /*
        |--------------------------------------------------------------------------
        | OT
        |--------------------------------------------------------------------------
        */

        $otMinutes = 0;

        if ($workMinutes > $requiredMinutes) {
            $otMinutes = $workMinutes - $requiredMinutes;
        }

        return [
            'date'               => $date,
            'first_in'           => $firstIn->format('H:i:s'),
            'last_out'           => $lastOut->format('H:i:s'),
            'worked_minutes'     => $workMinutes,
            'tardy_minutes'      => $tardyMinutes,
            'undertime_minutes'  => $undertimeMinutes,
            'overbreak_minutes'  => $overbreakMinutes,
            'ot_minutes'         => $otMinutes,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | SCHEDULE CONFIGURATION
    |--------------------------------------------------------------------------
    */

    private function getScheduleConfig($schedId, $dayOfWeek)
    {
        switch ($schedId) {

            case 1: // Normal Flex
                if ($dayOfWeek === 'monday') {
                    return ['max_in' => '08:00', 'hours' => 8];
                }
                return ['max_in' => '09:00', 'hours' => 8];

            case 2: // Compressed
                return ['max_in' => '08:00', 'hours' => 10];

            case 3: // Utility
                return ['max_in' => '06:00', 'hours' => 10];

            case 4: // DC SC RD
                if ($dayOfWeek === 'friday') {
                    return [
                        'max_in' => '09:00',
                        'hours' => 8,
                        'no_tardy' => true
                    ];
                }
                return ['max_in' => '09:00', 'hours' => 8];

            case 5: // Default WFH
                if ($dayOfWeek === 'friday') {
                    return ['max_in' => '09:30', 'hours' => 8];
                }
                return ['max_in' => '09:00', 'hours' => 8];

            default:
                return ['max_in' => '08:00', 'hours' => 8];
        }
    }
}