<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\EquipmentLocation;
use App\Models\EquipmentStatusHistory;
use App\Services\AuditService;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipment::with(['category', 'location']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('asset_code', 'like', "%{$s}%")
                  ->orWhere('serial_number', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('condition')) {
            $query->where('current_condition', $request->condition);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        $equipment = $query->orderBy('name')->paginate(15);
        $categories = EquipmentCategory::all();
        $locations = EquipmentLocation::all();

        return view('admin.equipment.index', compact('equipment', 'categories', 'locations'));
    }

    public function create()
    {
        $categories = EquipmentCategory::all();
        $locations = EquipmentLocation::all();
        return view('admin.equipment.create', compact('categories', 'locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_code' => ['required', 'string', 'max:50', 'unique:equipment'],
            'name' => ['required', 'string', 'max:120'],
            'category_id' => ['required', 'exists:equipment_categories,id'],
            'location_id' => ['required', 'exists:equipment_locations,id'],
            'serial_number' => ['nullable', 'string', 'max:100', 'unique:equipment'],
            'brand' => ['nullable', 'string', 'max:80'],
            'model' => ['nullable', 'string', 'max:80'],
            'purchase_date' => ['required', 'date'],
            'purchase_cost' => ['required', 'numeric', 'min:0'],
            'current_condition' => ['required', 'in:New,Excellent,Good,Fair,Poor'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['status'] = 'Available';

        $eq = Equipment::create($validated);

        EquipmentStatusHistory::create([
            'equipment_id' => $eq->id,
            'old_status' => 'None',
            'new_status' => 'Available',
            'changed_by' => auth()->id(),
            'reason' => 'Initial asset registration in FalconSystem inventory.',
        ]);

        AuditService::log('INSERT', 'equipment', $eq->id, null, $eq->toArray());

        return redirect()->route('admin.equipment.index')->with('success', "Equipment asset {$eq->asset_code} successfully registered.");
    }

    public function show(Equipment $equipment)
    {
        $equipment->load([
            'category',
            'location',
            'statusHistory.user',
            'borrowingItems.transaction.borrower',
            'damageReports.reporter',
            'maintenanceRecords.staff'
        ]);

        return view('admin.equipment.show', compact('equipment'));
    }

    public function edit(Equipment $equipment)
    {
        $categories = EquipmentCategory::all();
        $locations = EquipmentLocation::all();
        return view('admin.equipment.edit', compact('equipment', 'categories', 'locations'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'category_id' => ['required', 'exists:equipment_categories,id'],
            'location_id' => ['required', 'exists:equipment_locations,id'],
            'serial_number' => ['nullable', 'string', 'max:100', 'unique:equipment,serial_number,' . $equipment->id],
            'brand' => ['nullable', 'string', 'max:80'],
            'model' => ['nullable', 'string', 'max:80'],
            'purchase_cost' => ['required', 'numeric', 'min:0'],
            'current_condition' => ['required', 'in:New,Excellent,Good,Fair,Poor'],
            'status' => ['required', 'in:Available,Reserved,Borrowed,Assigned,Maintenance,Damaged,Lost,Retired'],
            'description' => ['nullable', 'string'],
        ]);

        $oldStatus = $equipment->status;
        $old = $equipment->toArray();

        $equipment->update($validated);

        if ($oldStatus !== $equipment->status) {
            EquipmentStatusHistory::create([
                'equipment_id' => $equipment->id,
                'old_status' => $oldStatus,
                'new_status' => $equipment->status,
                'changed_by' => auth()->id(),
                'reason' => 'Administrative update',
            ]);
        }

        AuditService::log('UPDATE', 'equipment', $equipment->id, $old, $equipment->toArray());

        return redirect()->route('admin.equipment.show', $equipment)->with('success', "Equipment record updated successfully.");
    }
}
