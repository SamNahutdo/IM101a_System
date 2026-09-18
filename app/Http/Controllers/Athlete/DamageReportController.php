<?php

namespace App\Http\Controllers\Athlete;

use App\Http\Controllers\Controller;
use App\Models\DamageReport;
use App\Models\Equipment;
use App\Models\BorrowingItem;
use App\Models\EquipmentStatusHistory;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DamageReportController extends Controller
{
    public function index()
    {
        $reports = DamageReport::with('equipment')
                               ->where('reported_by', auth()->id())
                               ->latest('reported_at')
                               ->paginate(15);

        return view('athlete.damage.index', compact('reports'));
    }

    public function create()
    {
        // Equipment currently borrowed by this user or active in assignments
        $borrowedEquipmentIds = BorrowingItem::whereHas('transaction', fn ($q) => $q->where('borrower_id', auth()->id())->where('status', 'Active'))
                                            ->where('status', 'Borrowed')
                                            ->pluck('equipment_id');

        $equipmentList = Equipment::whereIn('id', $borrowedEquipmentIds)
                                  ->orWhere('status', 'Available')
                                  ->get();

        return view('athlete.damage.create', compact('equipmentList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => ['required', 'exists:equipment,id'],
            'damage_type' => ['required', 'string', 'max:80'],
            'severity' => ['required', 'in:Minor,Moderate,Severe'],
            'description' => ['required', 'string', 'max:1000'],
        ]);

        return DB::transaction(function () use ($validated) {
            $code = 'DMG-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

            $report = DamageReport::create([
                'report_code' => $code,
                'equipment_id' => $validated['equipment_id'],
                'reported_by' => auth()->id(),
                'damage_type' => $validated['damage_type'],
                'severity' => $validated['severity'],
                'description' => $validated['description'],
                'status' => 'Reported',
                'reported_at' => now(),
            ]);

            $eq = Equipment::find($validated['equipment_id']);
            if ($validated['severity'] === 'Severe' && $eq->status !== 'Damaged') {
                $oldStatus = $eq->status;
                $eq->update(['status' => 'Damaged', 'current_condition' => 'Poor']);

                EquipmentStatusHistory::create([
                    'equipment_id' => $eq->id,
                    'old_status' => $oldStatus,
                    'new_status' => 'Damaged',
                    'changed_by' => auth()->id(),
                    'reason' => "Severe damage report {$code}: {$validated['damage_type']}",
                ]);
            }

            AuditService::log('INSERT', 'damage_reports', $report->id, null, $report->toArray());

            return redirect()->route('athlete.damage.index')
                             ->with('success', "Damage report {$code} filed successfully. Staff will review the condition.");
        });
    }
}
