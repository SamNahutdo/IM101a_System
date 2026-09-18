<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Equipment;
use App\Models\Team;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Exception;

class ReservationController extends Controller
{
    public function index()
    {
        $coachProfile = auth()->user()->coachProfile;
        $teamIds = Team::where('coach_id', $coachProfile->id)->pluck('id');

        $reservations = Reservation::with(['items.equipment', 'team'])
                                   ->whereIn('team_id', $teamIds)
                                   ->latest()
                                   ->paginate(15);

        return view('coach.reservations.index', compact('reservations'));
    }

    public function create()
    {
        $coachProfile = auth()->user()->coachProfile;
        $myTeams = Team::where('coach_id', $coachProfile->id)->get();
        $availableEquipment = Equipment::where('status', 'Available')->orderBy('name')->get();

        return view('coach.reservations.create', compact('myTeams', 'availableEquipment'));
    }

    public function store(Request $request, ReservationService $service)
    {
        $coachProfile = auth()->user()->coachProfile;
        $myTeams = Team::where('coach_id', $coachProfile->id)->pluck('id')->toArray();

        $validated = $request->validate([
            'team_id' => ['required', 'in:' . implode(',', $myTeams)],
            'start_time' => ['required', 'date', 'after:now'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'purpose' => ['required', 'string', 'max:500'],
            'equipment_ids' => ['required', 'array', 'min:1'],
            'equipment_ids.*' => ['exists:equipment,id'],
        ]);

        try {
            $reservation = $service->createReservation(
                requesterId: auth()->id(),
                teamId: $validated['team_id'],
                startTime: $validated['start_time'],
                endTime: $validated['end_time'],
                purpose: $validated['purpose'],
                equipmentIds: $validated['equipment_ids']
            );

            return redirect()->route('coach.reservations.index')
                             ->with('success', "Reservation request {$reservation->reservation_code} submitted for Staff review.");
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
