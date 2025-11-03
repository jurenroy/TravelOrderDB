<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TravelClearance;
use App\Models\Employee;
use App\Models\AuditLog;
use App\Models\Form;
use Illuminate\Support\Facades\Http;

class TravelClearanceController extends Controller
{
    public function index(Request $request)
    {
        $limit = min($request->input('limit', 10), 10000);
        return response()->json(TravelClearance::orderBy('created_at', 'desc')
        ->limit($limit)
        ->get());
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name_id' => 'required|string',
            'travel_order_id' => 'required|string',
            'station' => 'required|string',
            'destination' => 'required|string',
            'purpose' => 'required|string',
            'departure' => 'required|date',
            'arrival' => 'required|date',
            'pap_code' => 'required|string',
            'basis_of_approval' => 'required|string',
            'remarks' => 'nullable|string',
            'reviewed_by' => 'nullable|string',
            'clearance_number' => 'nullable|string',
        ]);

        // Generate clearance number automatically
        $validatedData['clearance_number'] = $this->generateClearanceNumber();
        // Map of readable labels
        $basisLabels = [
            'fieldworkWithinPlan' => 'Fieldwork within the Approved Travel Plan',
            'fieldworkNotWithinPlan' => 'Fieldwork not within the Approved Travel Plan',
            'interveningActivity' => 'Intervening Activity',
        
            'withinScheduledPeriod' => 'Within the Scheduled Period',
            'outsideScheduledPeriod' => 'Outside the Scheduled Period',
            'previousReportToRD' => 'Previous Travel Report endorsed to RD',
            'copyOfInstruction' => 'Copy of Instruction from the RD/DC',
            'copyOfInvitation' => 'Copy of Invitation, Memo, and SO',
        ];

        // Determine the readable label
        $basisValue = $validatedData['basis_of_approval'] ?? null; // your main value (could be sub or main)
        $selectedBasis = $request->input('selectedBasis'); // optional if sent separately

        if (in_array($basisValue, ['fieldworkNotWithinPlan', 'interveningActivity'])) {
            $readableBasis = $basisLabels[$basisValue] ?? $basisValue;
        } elseif (array_key_exists($basisValue, $basisLabels)) {
            // It’s one of the sub-basis → combine
            $readableBasis = $basisLabels['fieldworkWithinPlan'] . ': ' . $basisLabels[$basisValue];
        } else {
            $readableBasis = null;
        }

        $travelClearance = TravelClearance::create($validatedData);
        
        // Split travel_order_id by comma and update all related Forms
        $travelOrderIds = array_map('trim', explode(',', $validatedData['travel_order_id']));

        foreach ($travelOrderIds as $orderId) {
            $travelOrder = Form::where('travel_order_id', $orderId)->first();
            if ($travelOrder) {
                $travelOrder->update(['hasclearance' => $validatedData['clearance_number'],
                'note' => 'KAYSHE JOY F. PELINGON:' . $readableBasis, // 🟢 store the readable text
            ]);
            }
        }

        // Audit log
        AuditLog::create([
            'model' => 'TravelClearance',
            'model_id' => $travelClearance->id,
            'action' => 'created',
            'new_values' => $validatedData,
            'user_id' => auth()->id(), // Assuming authentication
        ]);

        // Send notification via websocket
        $this->sendNotification('Travel Clearance Created', 'A new travel clearance has been created for ' . $travelClearance->destination);

        // Send admin notification to name_id 76
        $this->sendNotification('Travel Clearance Created', 'A new travel clearance has been created for ' . $travelClearance->destination, true);

        return response()->json($travelClearance, 201);
    }

    public function show($id)
    {
        // Check if identifier is numeric → search by ID, otherwise by clearance_number
        if (is_numeric($id)) {
            $travelClearance = TravelClearance::find($id);
        } else {
            $travelClearance = TravelClearance::where('clearance_number', $id)->first();
        }
    
        if (!$travelClearance) {
            return response()->json(['message' => 'Travel clearance not found'], 404);
        }
    
        return response()->json($travelClearance);
    }

    public function update(Request $request, $id)
    {
        // Check if identifier is numeric → search by ID, otherwise by clearance_number
        if (is_numeric($id)) {
            $travelClearance = TravelClearance::find($id);
        } else {
            $travelClearance = TravelClearance::where('clearance_number', $id)->first();
        }
        $oldValues = $travelClearance->toArray();

        $validatedData = $request->validate([
            'name_id' => 'nullable|string',
            'travel_order_id' => 'nullable|string',
            'station' => 'nullable|string',
            'destination' => 'nullable|string',
            'purpose' => 'nullable|string',
            'departure' => 'nullable|date',
            'arrival' => 'nullable|date',
            'pap_code' => 'nullable|string',
            'basis_of_approval' => 'nullable|string',
            'remarks' => 'nullable|string',
            'reviewed_by' => 'nullable|string',
        ]);

        $travelClearance->update($validatedData);

        // Map of readable labels
        $basisLabels = [
            'fieldworkWithinPlan' => 'Fieldwork within the Approved Travel Plan',
            'fieldworkNotWithinPlan' => 'Fieldwork not within the Approved Travel Plan',
            'interveningActivity' => 'Intervening Activity',
        
            'withinScheduledPeriod' => 'Within the Scheduled Period',
            'outsideScheduledPeriod' => 'Outside the Scheduled Period',
            'previousReportToRD' => 'Previous Travel Report endorsed to RD',
            'copyOfInstruction' => 'Copy of Instruction from the RD/DC',
            'copyOfInvitation' => 'Copy of Invitation, Memo, and SO',
        ];

        // Determine the readable label
        $basisValue = $validatedData['basis_of_approval'] ?? null; // your main value (could be sub or main)
        $selectedBasis = $request->input('selectedBasis'); // optional if sent separately

        if (in_array($basisValue, ['fieldworkNotWithinPlan', 'interveningActivity'])) {
            $readableBasis = $basisLabels[$basisValue] ?? $basisValue;
        } elseif (array_key_exists($basisValue, $basisLabels)) {
            // It’s one of the sub-basis → combine
            $readableBasis = $basisLabels['fieldworkWithinPlan'] . ': ' . $basisLabels[$basisValue];
        } else {
            $readableBasis = null;
        }

        // Split travel_order_id by comma and update all related Forms
        $travelOrderIds = array_map('trim', explode(',', $validatedData['travel_order_id']));

        foreach ($travelOrderIds as $orderId) {
            $travelOrder = Form::where('travel_order_id', $orderId)
                ->whereNull('hasclearance') // only select if hasclearance is null
                ->first();
        
                if ($travelOrder) {
                    $travelOrder->update(['hasclearance' => $validatedData['clearance_number'],
                    'note' => $readableBasis, // 🟢 store the readable text
                ]);
                }
        }

        // Audit log
        AuditLog::create([
            'model' => 'TravelClearance',
            'model_id' => $travelClearance->id,
            'action' => 'updated',
            'old_values' => $oldValues,
            'new_values' => $validatedData,
            'user_id' => auth()->id(),
        ]);

        // Send notification via websocket
        $this->sendNotification('Travel Clearance Updated', 'Travel clearance for ' . $travelClearance->destination . ' has been updated.');

        return response()->json($travelClearance);
    }

    public function approve(Request $request, $id)
    {
        $travelClearance = TravelClearance::findOrFail($id);
        $oldValues = $travelClearance->toArray();

        $validatedData = $request->validate([
            'signature' => 'nullable|string',
            'approved_by' => 'nullable|integer',
        ]);

        // Generate clearance number if not already set
        if (!$travelClearance->clearance_number) {
            $clearanceNumber = $this->generateClearanceNumber();
            $validatedData['clearance_number'] = $clearanceNumber;
        }

        $travelClearance->update($validatedData);

        // Audit log
        AuditLog::create([
            'model' => 'TravelClearance',
            'model_id' => $travelClearance->id,
            'action' => 'updated',
            'old_values' => $oldValues,
            'new_values' => $validatedData,
            'user_id' => auth()->id(),
        ]);

        // Send notification via websocket
        $this->sendNotification('Travel Clearance Approved', 'Travel clearance for ' . $travelClearance->destination . ' has been approved.');

        return response()->json($travelClearance);
    }

    public function destroy($id)
    {
        $travelClearance = TravelClearance::findOrFail($id);
        $oldValues = $travelClearance->toArray();

        $travelClearance->delete();

        // Audit log
        AuditLog::create([
            'model' => 'TravelClearance',
            'model_id' => $id,
            'action' => 'deleted',
            'old_values' => $oldValues,
            'new_values' => null,
            'user_id' => auth()->id(),
        ]);

        // Send notification via websocket
        $this->sendNotification('Travel Clearance Deleted', 'Travel clearance for ' . $oldValues['destination'] . ' has been deleted.');

        return response()->json(['message' => 'Travel clearance deleted successfully']);
    }

    public function generateClearanceNumber()
    {
        $currentYear = date('Y');
        $maxClearance = TravelClearance::whereYear('departure', $currentYear)->max('clearance_number');

        if ($maxClearance) {
            $number = intval(substr($maxClearance, -3)) + 1;
        } else {
            $number = 1;
        }

        return sprintf('%s-%03d', $currentYear, $number);
    }

    public function getSuggestions($travel_order_id)
    {
        $travelOrder = \App\Models\Form::find($travel_order_id);
        if (!$travelOrder) {
            return response()->json(['message' => 'Travel order not found'], 404);
        }

        // Find similar travel orders based on destination, purpose, and date range
        $similarOrders = \App\Models\Form::where('destination', $travelOrder->destination)
            ->where('purpose', $travelOrder->purpose)
            ->where('name_id', '!=', $travelOrder->name_id)
            ->whereBetween('departure', [$travelOrder->departure, $travelOrder->arrival])
            ->where('note', null) // Only suggest orders without notes
            ->limit(10)
            ->get();

        return response()->json($similarOrders);
    }

    public function getAuditLogs($id)
    {
        $auditLogs = AuditLog::where('model', 'TravelClearance')
            ->where('model_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($auditLogs);
    }

    private function sendNotification($title, $message, $isAdmin = false)
    {
        // Send HTTP request to Django websocket server to broadcast notification
        // Assuming Django is running on 202.137.117.84:8012
        $url = $isAdmin ? 'http://202.137.117.84:8012/api/send-notification-admin/' : 'http://202.137.117.84:8012/api/send-notification/';
        try {
            Http::post($url, [
                'title' => $title,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            // Log error if notification fails
            \Log::error('Failed to send notification: ' . $e->getMessage());
        }
    }
}
