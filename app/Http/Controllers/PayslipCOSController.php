<?php

namespace App\Http\Controllers;

use App\Models\PayslipCOS;
use App\Models\Dtr;
use App\Models\Employee;
use App\Models\Salary;
use App\Models\DtrRemarks;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayslipCOSController extends Controller
{
    public function index()
    {
        return response()->json(PayslipCOS::all());
    }

    public function store(Request $request)
{
    $data = $request->all();

    // Check if the request is multiple records
    if (isset($data[0])) {

        PayslipCOS::insert($data);

        return response()->json([
            'message' => 'Bulk payslip COS inserted successfully',
            'count' => count($data)
        ]);
    }

    // Single insert fallback
    $payslip = PayslipCOS::create($data);

    return response()->json([
        'message' => 'Payslip COS inserted successfully',
        'data' => $payslip
    ]);
}

    public function show($id)
    {
        $payslip = PayslipCOS::findOrFail($id);
        return response()->json($payslip);
    }

    public function save(Request $request)
{
    $data = $request->all();

    // Bulk update
    if (isset($data[0]) && is_array($data[0])) {
        $updatedCount = 0;
        $failedCount = 0;

        foreach ($data as $record) {
            $payslip = PayslipCOS::find($record['id']);
            if ($payslip) {
                $payslip->update($record);
                $updatedCount++;
            } else {
                $failedCount++;
            }
        }

        return response()->json([
            'message' => 'Bulk save processed successfully',
            'updated' => $updatedCount,
            'failed' => $failedCount
        ]);
    }

    // Single record save
    $payslip = PayslipCOS::findOrFail($request->id);
    $payslip->update($request->all());

    return response()->json([
        'message' => 'Payslip saved successfully',
        'data' => $payslip
    ]);
}

    public function destroy($id)
    {
        $payslip = PayslipCOS::findOrFail($id);
        $payslip->delete();

        return response()->json(['message' => 'Payslip COS deleted successfully']);
    }

    public function getByPeriod($period)
{
    return response()->json(
        PayslipCOS::where('period', $period)->get()
    );
}

public function getByDtrs($dtrs_id)
{
    return response()->json(
        PayslipCOS::where('dtrs_id', $dtrs_id)->get()
    );
}

public function getByName($name_id)
{
    return response()->json(
        PayslipCOS::where('name_id', $name_id)->get()
    );
}

public function generateBatchPayslips()
    {
        // Step 1: Get all active employees (status == 0)
        $employees = Employee::where('status', 0)->get();

        // Step 2: Loop through each employee
        foreach ($employees as $employee) {
            $dtrs = Dtr::where('name_id', $employee->name_id)->get(); // get all DTRs
        
            foreach ($dtrs as $dtr) {
                $existingPayslip = PayslipCOS::where('name_id', $employee->name_id)
                                             ->where('dtrs_id', $dtr->id)
                                             ->first();
                if (!$existingPayslip) {
                    echo "Generating payslip for DTR ID: {$dtr->id}\n";
                    $this->generatePayslipForDtrs($dtr);
                } else {
                    echo "Payslip already exists for DTR ID: {$dtr->id}\n";
                }
            }
        }

        return response()->json(['message' => 'Batch payslips generation completed.']);
    }

    // Method to generate the payslip for an individual Dtrs record
    public function generatePayslipForDtrs(Dtr $dtrs)
    {
        // Step 1: Get the period (start_date to end_date)
        $startDate = Carbon::parse($dtrs->start_date);
        $endDate = Carbon::parse($dtrs->end_date);
        $formattedPeriod = $startDate->format('F j') . '-' . $endDate->format('j, Y');

        // Step 2: Get Salary information for the employee (using name_id from Dtrs)
        $employee = Employee::where('name_id', $dtrs->name_id)->first();  // Assuming 'name_id' relates to Employee

        if (!$employee) {
            return;  // If no employee is found, don't proceed
        }

        $salad = Salary::where('salary_id', $employee->salary_id)->first();
        
        $salary = $salad ? $salad->amount : 500;

        // Step 3: Calculate penalty (Tardiness and Undertime from DtrRemark)
        $remarks = DtrRemarks::where('dtrs_id', $dtrs->id)->get();
        $penaltyMinutes = 0;
        foreach ($remarks as $remark) {
            // Count penalty based on Tardiness (T) and Undertime (U)
            if ($remark->tardiness) {
                $penaltyMinutes += substr_count($remark->tardiness, 'T');
            }
            if ($remark->undertime) {
                $penaltyMinutes += substr_count($remark->undertime, 'U');
            }
        }

        // Step 4: Calculate Penalty Amount
        $penaltyAmount = ($salary / 480) * $penaltyMinutes;

        // Step 5: Default values
        $premiumRate = 20;  // Default Premium Rate
        $pagibig = 200;     // Default Pagibig
        $certify = 23;      // Default Certify
        $certipos = 'Administrative Officer IV / OIC, Finance Section';

        // Step 6: Compute final payslip components

        // Step 7: Create PayslipCOS record
        PayslipCOS::create([
            'period' => $formattedPeriod,
            'name_id' => $employee->name_id,
            'days' => $this->calculateDaysWorked($dtrs->id),  // Implement this logic
            'rate' => $salary,
            'premium_rate' => $premiumRate,
            'penalty' => $penaltyAmount,
            'certify' => $certify,
            'certipos' => $certipos,
            'dtrs_id' => $dtrs->id
        ]);
    }

    // Helper method to calculate the days worked (this can be customized)
    private function calculateDaysWorked($dtrs_id)
    {
        return DtrRemarks::where('dtrs_id', $dtrs_id)->count();
    }

}