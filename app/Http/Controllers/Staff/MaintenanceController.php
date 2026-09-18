<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRecord;
use App\Models\Equipment;
use App\Models\DamageReport;
use App\Services\MaintenanceService;
use Illuminate\Http\Request;
use Exception;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceRecord::with(['equipment.category', 'staff', 'damageReport']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->orderBy('scheduled_date', 'desc')->paginate(15);
        $damageReportsNeedingRepair = DamageReport::whereIn('status', ['Reported', 'Repair Required'])->get();
        $equipmentList = Equipment::all();

        return view('staff.maintenance.index', compact('records', 'damageReportsNeedingRepair', 'equipmentList'));
    }

    public function store(Request $request, MaintenanceService $service)
    {
        $validated = $request->validate([
            'equipment_id' => ['required', 'exists:equipment,id'],
            'damage_report_id' => ['nullable', 'exists:damage_reports,id'],
            'maintenance_type' => ['required', 'string', 'max:80'],
            'scheduled_date' => ['required', 'date'],
            'cost' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'string', 'max:500'],
        ]);

        try {
            $service->scheduleMaintenance(
                equipmentId: $validated['equipment_id'],
                damageReportId: $validated['damage_report_id'] ?? null,
                maintenanceType: $validated['maintenance_type'],
                scheduledDate: $validated['scheduled_date'],
                assignedStaffId: auth()->id(),
                cost: (float) $validated['cost'],
                description: $validated['description']
            );

            return redirect()->route('staff.maintenance.index')
                             ->with('success', 'Maintenance work order scheduled successfully.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function updateStatus(Request $request, MaintenanceRecord $record, MaintenanceService $service)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:Scheduled,In Progress,Completed,Cancelled'],
            'result_notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $service->updateStatus(
                recordId: $record->id,
                status: $validated['status'],
                resultNotes: $validated['result_notes'] ?? null
            );

            return redirect()->route('staff.maintenance.index')
                             ->with('success', "Maintenance status updated to '{$validated['status']}'.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
