<?php

namespace App\Http\Controllers\Athlete;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Exception;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipment::with(['category', 'location'])
                          ->where('status', 'Available');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('asset_code', 'like', "%{$s}%")
                  ->orWhere('brand', 'like', "%{$s}%");
            });
        }

        $equipment = $query->paginate(12);
        $categories = EquipmentCategory::all();

        return view('athlete.catalog.index', compact('equipment', 'categories'));
    }

    public function requestEquipment(Request $request, ReservationService $service)
    {
        $validated = $request->validate([
            'equipment_id' => ['required', 'exists:equipment,id'],
            'start_time' => ['required', 'date', 'after:now'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'purpose' => ['required', 'string', 'max:500'],
        ]);

        try {
            $reservation = $service->createReservation(
                requesterId: auth()->id(),
                teamId: null,
                startTime: $validated['start_time'],
                endTime: $validated['end_time'],
                purpose: $validated['purpose'],
                equipmentIds: [$validated['equipment_id']]
            );

            return redirect()->route('athlete.requests.index')
                             ->with('success', "Borrowing request {$reservation->reservation_code} submitted. Awaiting equipment staff review.");
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
