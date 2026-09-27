<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\MaintenanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceRecord::with(['equipment.sport', 'reportedByUser']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->latest('scheduled_date')->paginate(15);
        $equipment = Equipment::orderBy('equipment_name')->get();

        return view('maintenance.index', compact('records', 'equipment'));
    }

    public function create()
    {
        $equipment = Equipment::orderBy('equipment_name')->get();
        return view('maintenance.create', compact('equipment'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => ['required', 'exists:equipment,id'],
            'maintenance_type' => ['required', 'string', 'max:80'],
            'description' => ['required', 'string'],
            'scheduled_date' => ['required', 'date'],
            'status' => ['required', 'in:Scheduled,In Progress,Completed,Cancelled'],
            'remarks' => ['nullable', 'string'],
        ]);

        $validated['reported_by'] = Auth::id();

        $record = MaintenanceRecord::create($validated);

        // If reported as under maintenance, flag equipment status
        $equipment = Equipment::find($validated['equipment_id']);
        if ($equipment && $validated['status'] !== 'Completed') {
            $equipment->update(['status' => 'Under Maintenance', 'condition' => 'Damaged']);
        }

        $userRole = Auth::user()->role->name;

        return redirect()->route($userRole . '.maintenance.index')
            ->with('success', 'Maintenance and damage report created successfully.');
    }

    public function complete(Request $request, MaintenanceRecord $record)
    {
        $record->update([
            'status' => 'Completed',
            'completed_date' => now()->toDateString(),
            'remarks' => $request->input('remarks', 'Maintenance successfully completed.'),
        ]);

        // Restore equipment status to Available and condition to Good
        if ($record->equipment) {
            $record->equipment->update([
                'status' => 'Available',
                'condition' => 'Good',
            ]);
        }

        return back()->with('success', 'Maintenance record marked as Completed and equipment restored to Available.');
    }
}
