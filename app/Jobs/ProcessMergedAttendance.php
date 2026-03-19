<?php

namespace App\Jobs;

use App\Models\Dtr;
use App\Models\DtrDays;
use App\Models\DtrRemarks;
use App\Http\Controllers\AttendanceController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessMergedAttendance implements ShouldQueue
{
    use Dispatchable, Queueable;

    protected $dtrId;

    public function __construct($dtrId)
    {
        $this->dtrId = $dtrId;
    }

    public function handle()
    {
        $dtr = Dtr::find($this->dtrId);
        if (!$dtr) return;

        $nameId = $dtr->name_id;
        $schedId = \App\Models\Employee::where('name_id', $nameId)->value('sched_id');

         // Define schedules as a multidimensional array
         $schedules = [
            1 => [ // Default
                'mon' => ['min' => '08:00:00', 'max' => '8:00:00', 'hours' => 8, 'monday_fixed' => true],
                'tue' => ['min' => '07:00:00', 'max' => '09:00:00', 'hours' => 8],
                'wed' => ['min' => '07:00:00', 'max' => '09:00:00', 'hours' => 8],
                'thu' => ['min' => '07:00:00', 'max' => '09:00:00', 'hours' => 8],
                'fri' => ['min' => '07:00:00', 'max' => '09:00:00', 'hours' => 8],
            ],
            2 => [ // Compressed
                'mon' => ['min' => '07:00:00', 'max' => '08:00:00', 'hours' => 10],
                'tue' => ['min' => '07:00:00', 'max' => '08:00:00', 'hours' => 10],
                'wed' => ['min' => '07:00:00', 'max' => '08:00:00', 'hours' => 10],
                'thu' => ['min' => '07:00:00', 'max' => '08:00:00', 'hours' => 10],
            ],
            3 => [ // Utility
                'mon' => ['min' => '04:00:00', 'max' => '06:00:00', 'hours' => 10],
                'tue' => ['min' => '04:00:00', 'max' => '06:00:00', 'hours' => 10],
                'wed' => ['min' => '04:00:00', 'max' => '06:00:00', 'hours' => 10],
                'thu' => ['min' => '04:00:00', 'max' => '06:00:00', 'hours' => 10],
            ],
            4 => [ // DC/SC/RD
                'mon' => ['min' => '08:00:00', 'max' => '8:00:00', 'hours' => 8, 'monday_fixed' => true],
                'tue' => ['min' => '07:00:00', 'max' => '09:00:00', 'hours' => 8],
                'wed' => ['min' => '07:00:00', 'max' => '09:00:00', 'hours' => 8],
                'thu' => ['min' => '07:00:00', 'max' => '09:00:00', 'hours' => 8],
                'fri' => ['min' => '00:00:00', 'max' => '23:59', 'hours' => 0, 'friday_no_penalty' => true],
            ],
            5 => [ // Default WFH
                'mon' => ['min' => '08:00:00', 'max' => '8:00:00', 'hours' => 8, 'monday_fixed' => true],
                'tue' => ['min' => '07:00:00', 'max' => '09:00:00', 'hours' => 8],
                'wed' => ['min' => '07:00:00', 'max' => '09:00:00', 'hours' => 8],
                'thu' => ['min' => '07:00:00', 'max' => '09:00:00', 'hours' => 8],
                'fri' => ['min' => '07:30:00', 'max' => '09:30:00', 'hours' => 8],
            ],
        ];

        // Call your AttendanceController helper
        $attendanceController = new AttendanceController();
        $mergedAttendance = $attendanceController->mergedAttendanceHelper(
            $dtr->name_id,
            $dtr->start_date,
            $dtr->end_date
        );

        
        // Group punches by date
        $punchesByDate = collect($mergedAttendance)->groupBy('date');

        foreach ($punchesByDate as $date => $punches) {
            $dayOfWeek = strtolower(date('D', strtotime($date))); // mon, tue, wed, etc.

            if (!isset($schedules[$schedId][$dayOfWeek])) continue; // skip if schedule not defined
            $sched = $schedules[$schedId][$dayOfWeek];

            // Calculate tardiness and undertime
            list($tardiness, $undertime) = $this->calculateDailyAttendance($punches, $sched);
            
            
            // Create DtrRemarks
            \App\Models\DtrRemarks::create([
                'dtrs_id'   => $dtr->id,
                'name_id'   => $nameId,
                'date'      => $date,
                'tardiness' => $tardiness,
                'undertime' => $undertime,
            ]);
        }

        // Store each merged attendance row into DtrDays
        foreach ($mergedAttendance as $row) {
            DtrDays::create([
                'dtrs_id'    => $dtr->id,
                'name_id'   => $row['name_id'],
                'date'      => $row['date'],
                'timestamp' => $row['timestamp'],
                'status'    => $row['status'] ?? '', // blank if missing
            ]);
        }
    }

    private function calculateDailyAttendance($punches, $sched)
    {
        // Friday no penalty
        if (!empty($sched['friday_no_penalty'])) {
            return [0, 0];
        }
    
        $punches = collect($punches)->sortBy('timestamp')->values();
        if ($punches->isEmpty()) {
            return [0, 0];
        }
    
        $firstIn = strtotime($punches->first()['timestamp']);
        $lastOut = strtotime($punches->last()['timestamp']);
    
        if (!$firstIn || !$lastOut) {
            return [0, 0];
        }
    
        // Anchor schedule times to the same date as first punch
        $workDate = date('Y-m-d', $firstIn);
    
        $minStart = strtotime("$workDate {$sched['min']}"); // earliest allowed start
        $maxStart = strtotime("$workDate {$sched['max']}"); // latest allowed start

        $lunchStart = strtotime("$workDate 12:00:00");
        $lunchEnd   = strtotime("$workDate 13:00:00");

        $scheduledHours = $sched['hours']; // expected total hours
    
        /*
        |----------------------------------------------------------------------
        | TARDINESS (in minutes)
        |----------------------------------------------------------------------
        | If employee arrives after maxStart
        */
        $tardiness = max(0, ($firstIn - $maxStart) / 60);
    
        /*
        |----------------------------------------------------------------------
        | WORKED MINUTES
        |----------------------------------------------------------------------
        | Count actual worked time minus tardiness
        */
        $totalMinutes = max(0, (($lastOut - $firstIn) / 60));
    
        $lunchDeduction = 0;

        // If employee time overlaps with lunch
        if ($firstIn < $lunchEnd && $lastOut > $lunchStart) {
            $overlapStart = max($firstIn, $lunchStart);
            $overlapEnd   = min($lastOut, $lunchEnd);
            $lunchDeduction = ($overlapEnd - $overlapStart) / 60;
        }else{
            $totalMinutes-=60;
        }


        $workedMinutes = $totalMinutes - $lunchDeduction;
        /*
        |----------------------------------------------------------------------
        | UNDERTIME (in minutes)
        |----------------------------------------------------------------------
        | Scheduled minutes minus worked minutes
        */
        $scheduledMinutes = $scheduledHours * 60;
        $halfDayThreshold = $scheduledMinutes / 2;
    
        if (!empty($sched['monday_fixed'])) {
            $scheduledMinutes = 8 * 60; // fixed 8 hours
        }
    
        // $undertime = max(0, ($scheduledMinutes - $workedMinutes)-$tardiness);
        if ($workedMinutes < $halfDayThreshold) {
            // If worked less than half-day, undertime is half-day
            $undertime = $scheduledMinutes - $workedMinutes;
        } else {
            $undertime = max(0, $scheduledMinutes - $workedMinutes - $tardiness);
        }
    
        return [
            (int) floor($tardiness),
            (int) floor($undertime)
        ];
    }
}
