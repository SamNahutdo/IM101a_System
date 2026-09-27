<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Sport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipment::with('sport');

        if ($request->filled('sport_id')) {
            $query->where('sport_id', $request->sport_id);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('equipment_code', 'like', "%{$s}%")
                  ->orWhere('equipment_name', 'like', "%{$s}%");
            });
        }

        $equipment = $query->orderBy('equipment_name')->paginate(15);
        $sports = Sport::where('status', 'Active')->orderBy('sport_name')->get();
        $categories = Equipment::select('category')->distinct()->pluck('category');

        return view('equipment.index', compact('equipment', 'sports', 'categories'));
    }

    public function create()
    {
        $sports = Sport::where('status', 'Active')->orderBy('sport_name')->get();
        return view('equipment.create', compact('sports'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sport_id' => ['required', 'exists:sports,id'],
            'equipment_code' => ['required', 'string', 'max:50', 'unique:equipment,equipment_code'],
            'equipment_name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:100'],
            'quantity' => ['required', 'integer', 'min:1'],
            'condition' => ['required', 'in:New,Good,Fair,Damaged'],
            'status' => ['required', 'in:Available,In Use,Under Maintenance,Retired'],
        ]);

        $validated['available_quantity'] = $validated['quantity'];

        // Inserting triggers the MySQL Trigger: trg_audit_equipment_insert!
        Equipment::create($validated);

        return redirect()->route(auth()->user()->role->name . '.equipment.index')
            ->with('success', "Equipment '{$validated['equipment_name']}' created successfully. Database audit trigger recorded.");
    }

    public function edit(Equipment $equipment)
    {
        $sports = Sport::where('status', 'Active')->orderBy('sport_name')->get();
        return view('equipment.edit', compact('equipment', 'sports'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'sport_id' => ['required', 'exists:sports,id'],
            'equipment_code' => ['required', 'string', 'max:50', Rule::unique('equipment', 'equipment_code')->ignore($equipment->id)],
            'equipment_name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:100'],
            'quantity' => ['required', 'integer', 'min:1'],
            'condition' => ['required', 'in:New,Good,Fair,Damaged'],
            'status' => ['required', 'in:Available,In Use,Under Maintenance,Retired'],
        ]);

        // Adjust available quantity according to total quantity delta
        $diff = $validated['quantity'] - $equipment->quantity;
        $newAvailable = max(0, $equipment->available_quantity + $diff);
        $validated['available_quantity'] = $newAvailable;

        // Updating triggers the MySQL Trigger: trg_audit_equipment_update!
        $equipment->update($validated);

        return redirect()->route(auth()->user()->role->name . '.equipment.index')
            ->with('success', "Equipment '{$equipment->equipment_name}' updated successfully.");
    }

    public function destroy(Equipment $equipment)
    {
        // Referential integrity check: check active borrowings
        $hasActiveLoans = $equipment->borrowingItems()
            ->whereHas('borrowingTransaction', function ($q) {
                $q->whereIn('status', ['Borrowed', 'Pending', 'Approved']);
            })
            ->whereRaw('quantity > returned_quantity')
            ->exists();

        if ($hasActiveLoans) {
            return back()->with('error', 'Referential Integrity Error: Cannot delete equipment currently out on active loan.');
        }

        // Deleting triggers the MySQL Trigger: trg_audit_equipment_delete!
        $equipment->delete();

        return redirect()->route(auth()->user()->role->name . '.equipment.index')
            ->with('success', "Equipment '{$equipment->equipment_name}' deleted safely. Database audit trigger recorded.");
    }
}
