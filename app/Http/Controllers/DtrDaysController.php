<?php

namespace App\Http\Controllers;

use App\Models\Dtr;
use App\Models\DtrDays;
use App\Models\DtrRemarks;
use App\Models\Form;
use App\Models\LeaveForm;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DtrDaysController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DtrDays  $dtrDays
     * @return \Illuminate\Http\Response
     */
    public function showByDtrId($dtrId)
    {
        // Fetch all DtrDays for the given dtr_id
        $dtrDays = \App\Models\DtrDays::where('dtrs_id', $dtrId)
            ->orderBy('date')
            ->orderBy('timestamp')
            ->get(['date', 'timestamp', 'name_id', 'status']);

        if ($dtrDays->isEmpty()) {
            return response()->json([
                'message' => 'No DTR days found for the given DTR ID.'
            ], 404);
        }

        return $dtrDays;
        // return response()->json([
        //     'dtr_id' => $dtrId,
        //     'count'  => $dtrDays->count(),
        //     'data'   => $dtrDays
        // ]);
    }

    // public function showRemarksByDtrId($dtrId)
    // {
    //     // Fetch all DtrDays for the given dtr_id
    //     $dtrDays = \App\Models\DtrRemarks::where('dtrs_id', $dtrId)
    //         ->orderBy('date')
    //         ->get(['date', 'tardiness', 'undertime','name_id']);

    //     if ($dtrDays->isEmpty()) {
    //         return response()->json([
    //             'message' => 'No DTR days found for the given DTR ID.'
    //         ], 404);
    //     }

    //     return $dtrDays;
    //     // return response()->json([
    //     //     'dtr_id' => $dtrId,
    //     //     'count'  => $dtrDays->count(),
    //     //     'data'   => $dtrDays
    //     // ]);
    // }
    public function showRemarksByDtrId($dtrId)
{
    // Fetch DTR days and convert to array
    $dtrDays = DtrRemarks::where('dtrs_id', $dtrId)
        ->orderBy('date')
        ->get(['date', 'tardiness', 'undertime', 'name_id', 'dtrs_id'])
        ->map(function ($item) {
            return [
                'date' => $item->date,
                'tardiness' => $item->tardiness,
                'undertime' => $item->undertime,
                'name_id' => $item->name_id,
                'dtrs_id' => $item->dtrs_id
            ];
        })
        ->keyBy(function($item) {
            return $item['name_id'] . '-' . $item['date'];
        });

    // Fetch the DTR record to get the date range
    $dtr = Dtr::where('id', $dtrId)->first();

    if (!$dtr) {
        return response()->json([
            'message' => 'DTR not found for the given ID.'
        ], 404);
    }

    $startDate = Carbon::parse($dtr->start_date);
    $endDate = Carbon::parse($dtr->end_date);

    // Get all travel orders for DTR people
    // Get travel orders within the DTR date range
$travelOrders = Form::whereIn('name_id', $dtrDays->pluck('name_id')->unique()->toArray())
    ->where(function($query) use ($startDate, $endDate) {
        $query->whereBetween('departure', [$startDate, $endDate])
              ->orWhereBetween('arrival', [$startDate, $endDate]);
    })
    ->get();

// Get leave forms within the DTR date range
$leaveForms = LeaveForm::whereIn('name_id', $dtrDays->pluck('name_id')->unique()->toArray())
    ->get();

    // Filter leave forms to only those overlapping DTR range
$leaveForms = $leaveForms->filter(function ($leaveForm) use ($startDate, $endDate) {

    // Parse the leave dates into an array of strings like ["2026-02-12", "2026-02-13"]
    $leaveDates = $this->parseLeaveDates($leaveForm->dates);

    // Convert to Carbon and check if any date is inside DTR range
    foreach ($leaveDates as $d) {
        $date = Carbon::parse($d);
        if ($date >= $startDate && $date <= $endDate) {
            return true; // keep this leaveForm
        }
    }

    return false; // no overlap
});

    $virtualDtrDays = [];

    // ------------------ Travel Orders ------------------
    foreach ($travelOrders as $travelOrder) {
        $start = Carbon::parse($travelOrder->departure);
        $end = Carbon::parse($travelOrder->arrival);

        $current = $start->copy();
        while ($current <= $end) {
            $key = $travelOrder->name_id . '-' . $current->toDateString();
            if (isset($dtrDays[$key])) {
                $dtrDays[$key] = array_merge($dtrDays[$key], ['tardiness' => 'TO', 'undertime' => $travelOrder->to_num]);
            } else {
                $virtualDtrDays[$key] = [
                    'date' => $current->toDateString(),
                    'tardiness' => 'TO',
                    'undertime' => $travelOrder->to_num,
                    'name_id' => $travelOrder->name_id,
                    'dtrs_id' => $dtrId
                ];
            }
            $current->addDay();
        }
    }

    // ------------------ Leave Forms ------------------
    foreach ($leaveForms as $leaveForm) {

        $leaveDates = $this->parseLeaveDates($leaveForm->dates);
    
        foreach ($leaveDates as $leaveDate) {
    
            $date = Carbon::parse($leaveDate);
    
            $key = $leaveForm->name_id . '-' . $date->toDateString();
    
            if (isset($dtrDays[$key])) {
    
                $dtrDays[$key] = array_merge(
                    $dtrDays[$key],
                    ['tardiness' => 'O', 'undertime' => 'L']
                );
    
            } else {
    
                $virtualDtrDays[$key] = [
                    'date' => $date->toDateString(),
                    'tardiness' => 'O',
                    'undertime' => 'L',
                    'name_id' => $leaveForm->name_id,
                    'dtrs_id' => $dtrId
                ];
            }
        }
    }

// return response()->json($leaveForms);

    // Merge and sort
    $allDtrDays = collect($dtrDays->values())
        ->merge(collect($virtualDtrDays))
        ->sortBy('date')
        ->values();

    return response()->json($allDtrDays);
}

// ------------------ Helper function ------------------
public function parseLeaveDates($leaveDates)
{
    $dates = [];

    $leaveDates = str_replace('–', '-', $leaveDates);
    $leaveDates = preg_replace('/\([^)]+\)/', '', $leaveDates);

    // Extract year
    preg_match('/\b(20\d{2})\b/', $leaveDates, $yearMatch);
    $year = $yearMatch[1] ?? date('Y');

    $leaveDates = str_replace($year, '', $leaveDates);

    $parts = explode(',', $leaveDates);

    foreach ($parts as $part) {

        $part = trim($part);
        if (!$part) continue;

        // Range like "February 12-13"
        if (strpos($part, '-') !== false) {
            [$start, $end] = explode('-', $part);
            $start = trim($start);
            $end   = trim($end);

            $startDate = Carbon::parse("$start $year");

            // If end has no month, inherit from start
            if (!preg_match('/[A-Za-z]/', $end)) {
                $month = $startDate->format('F');
                $end = "$month $end";
            }

            $endDate = Carbon::parse("$end $year");

            while ($startDate <= $endDate) {
                $dates[] = $startDate->toDateString();
                $startDate->addDay();
            }

        } else {
            $dates[] = Carbon::parse("$part $year")->toDateString();
        }
    }

    return $dates; // <-- return array of strings
}

// Helper method to parse date and add context if missing
public function parseDateWithContext($dateStr, $currentMonth, $currentYear)
{
    // Try parsing the date (with current month/year if not provided)
    try {
        // If there's no month or year, add the current month/year
        if (!preg_match('/\d{4}/', $dateStr)) {
            $dateStr = "{$currentMonth} {$dateStr} {$currentYear}";
        }
        return Carbon::parse($dateStr);
    } catch (\Exception $e) {
        // Handle error gracefully, e.g., invalid date format
        throw new \Exception("Could not parse '{$dateStr}': " . $e->getMessage());
    }
}


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DtrDays  $dtrDays
     * @return \Illuminate\Http\Response
     */
    public function edit(DtrDays $dtrDays)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DtrDays  $dtrDays
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DtrDays $dtrDays)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DtrDays  $dtrDays
     * @return \Illuminate\Http\Response
     */
    public function destroy(DtrDays $dtrDays)
    {
        //
    }
}
