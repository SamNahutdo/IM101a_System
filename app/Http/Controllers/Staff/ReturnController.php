<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BorrowingItem;
use App\Models\DamageReport;
use App\Services\BorrowingService;
use Illuminate\Http\Request;
use Exception;

class ReturnController extends Controller
{
    public function index()
    {
        $activeItems = BorrowingItem::with(['transaction.borrower', 'transaction.team', 'equipment'])
                                    ->where('status', 'Borrowed')
                                    ->latest()
                                    ->paginate(15);

        return view('staff.returns.index', compact('activeItems'));
    }

    public function process(Request $request, BorrowingItem $item, BorrowingService $borrowingService)
    {
        $validated = $request->validate([
            'return_condition' => ['required', 'in:New,Excellent,Good,Fair,Poor'],
            'remarks' => ['nullable', 'string', 'max:255'],
            'report_damage' => ['nullable', 'boolean'],
            'damage_type' => ['nullable', 'required_if:report_damage,1', 'string', 'max:80'],
            'damage_severity' => ['nullable', 'required_if:report_damage,1', 'in:Minor,Moderate,Severe'],
            'damage_description' => ['nullable', 'required_if:report_damage,1', 'string', 'max:500'],
        ]);

        try {
            $returnedItem = $borrowingService->processReturn(
                borrowingItemId: $item->id,
                staffId: auth()->id(),
                returnCondition: $validated['return_condition'],
                remarks: $validated['remarks'] ?? null
            );

            // If damage reported during return
            if (!empty($validated['report_damage'])) {
                DamageReport::create([
                    'report_code' => 'DMG-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5)),
                    'equipment_id' => $returnedItem->equipment_id,
                    'borrowing_item_id' => $returnedItem->id,
                    'reported_by' => auth()->id(),
                    'damage_type' => $validated['damage_type'],
                    'severity' => $validated['damage_severity'],
                    'description' => $validated['damage_description'],
                    'status' => 'Reported',
                ]);
            }

            return redirect()->route('staff.returns.index')
                             ->with('success', "Equipment '{$returnedItem->equipment->name}' marked as Returned successfully.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
