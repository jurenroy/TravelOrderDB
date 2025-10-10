<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\LeaveForm;
use App\Models\Employee; // Import the Employee model
use Illuminate\Support\Collection;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        // Validate month and year params
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year'  => 'required|integer|min:1900|max:2100',
        ]);

        $month = $request->month;
        $year = $request->year;
        $division = $request->division ?? 'all';  // `all` means no filter
        $name = $request->name ?? 'all';

        // Create start and end date of the requested month
        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

        // Division data to return in response
        $divisions = [
            ["division_id" => 1, "division_name" => "MMD"],
            ["division_id" => 2, "division_name" => "FAD"],
            ["division_id" => 3, "division_name" => "GD"],
            ["division_id" => 4, "division_name" => "MSESDD"],
            ["division_id" => 5, "division_name" => "ORD"],
        ];

        // ==============================
        // 🟦 TRAVEL ORDERS
        // ==============================
        $forms = Form::query()
            ->whereBetween('departure', [$start, $end]);

        // Apply division filter if provided
        if ($division !== 'all') {
            $forms->where('division_id', $division);
        }

        // Apply name filter if provided
        if ($name !== 'all') {
            $forms->where('name_id', $name);
        }

        // Get and map travel orders
        $forms = $forms->get()->map(function ($item) use ($division) {
            // Extract the year from the departure date
            $year = Carbon::parse($item->departure)->year;

            // Pad the 'to_num' value with leading zeros (to ensure it's always 4 digits)
            $to_num = sprintf('%04d', $item->to_num);  // Pads numbers like 265 to 0265

            // Combine year and 'to_num' into the desired format
            $formattedTitle = "Travel Order ({$year}-{$to_num})";
            $event = [
                'record_type'     => 'travel',
                'travel_order_id' => $item->travel_order_id,
                'name_id'         => $item->name_id,
                'title'           => $formattedTitle,
                'start'           => $item->departure,
                'end'             => $item->arrival,
                'destination'     => $item->destination,
                'purpose'         => $item->purpose,
            ];

            // Include division_id only if division filter is NOT applied
            if ($division === 'all') {
                $event['division_id'] = $item->division_id;
            }

            return $event;
        });

        // ==============================
        // 🟩 LEAVE FORMS
        // ==============================
        $leaves = LeaveForm::query()
            ->whereBetween('date', [$start, $end]);

        // Apply name filter if provided
        if ($name !== 'all') {
            $leaves->where('name_id', $name);
        }

        // If division filter is provided, we get employees in that division
        if ($division !== 'all') {
            // Get all name_ids belonging to employees in the selected division
            $employeesInDivision = Employee::where('division_id', $division)
                ->pluck('name_id');  // Get all name_ids for the selected division

            // Filter leave forms by those name_ids
            $leaves->whereIn('name_id', $employeesInDivision);
        }

        // Get and map leave forms
        $leaves = $leaves->get()->map(function ($item) use ($division) {
            // Parse "dates" like "December 16-23, 2024" → start & end
            $parsedStart = null;
            $parsedEnd = null;

            if (preg_match('/([A-Za-z]+)\s+(\d+)(?:-(\d+))?,\s*(\d{4})/', $item->dates, $matches)) {
                $month = $matches[1];
                $startDay = $matches[2];
                $endDay = $matches[3] ?? $startDay;
                $year = $matches[4];
                $parsedStart = date('Y-m-d', strtotime("$month $startDay, $year"));
                $parsedEnd = date('Y-m-d', strtotime("$month $endDay, $year"));
            }

            // Skip invalid dates (either start or end date is null)
            if (!$parsedStart || !$parsedEnd) {
                return null;  // This will exclude the leave form from the final list
            }

            // Fetch the Employee to get the division_id based on name_id
            $employee = Employee::where('name_id', $item->name_id)->first();

            // Default to null if no employee is found
            $division_id = $employee ? $employee->division_id : null;

            $event = [
                'record_type'  => 'leave',
                'leaveform_id' => $item->leaveform_id,
                'name_id'      => $item->name_id,
                'title'        => 'Leave Form',
                'start'        => $parsedStart,
                'end'          => $parsedEnd,
                'type'         => $item->type,
                'detail'       => $item->detail,
                'days'         => $item->days,
            ];

            // Include division_id only if division filter is NOT applied
            if ($division === 'all') {
                $event['division_id'] = $division_id;
            }

            return $event;
        })->filter();  // Filter out null values from the collection

        // Combine both travel orders and leave forms
        $events = (new Collection)->merge($forms)->merge($leaves);

        // Return the filtered events as a JSON response
        return response()->json([
            'events'   => $events->values(),
            'divisions' => $divisions,  // Include the division array in the response
        ]);
    }
}
