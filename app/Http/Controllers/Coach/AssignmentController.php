<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\EquipmentAssignment;
use App\Models\Equipment;
use App\Models\Team;
use App\Models\AthleteProfile;
use App\Services\AuditService;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index()
    {
        $coachProfile = auth()->user()->coachProfile;
        $teamIds = Team::where('coach_id', $coachProfile->id)->pluck('id');

        $assignments = EquipmentAssignment::with(['equipment', 'athlete.user', 'team'])
                                          ->whereIn('team_id', $teamIds)
                                          ->latest()
                                          ->paginate(15);

        return view('coach.assignments.index', compact('assignments'));
    }

    public function create()
    {
        $coachProfile = auth()->user()->coachProfile;
        $teams = Team::with('athletes.user')->where('coach_id', $coachProfile->id)->get();
        $availableEquipment = Equipment::where('status', 'Available')->get();

        return view('coach.assignments.create', compact('teams', 'availableEquipment'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => ['required', 'exists:equipment,id'],
            'team_id' => ['required', 'exists:teams,id'],
            'athlete_id' => ['nullable', 'exists:athlete_profiles,id'],
            'expected_end_date' => ['required', 'date', 'after:today'],
            'condition_on_assignment' => ['required', 'in:New,Excellent,Good,Fair,Poor'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $eq = Equipment::findOrFail($validated['equipment_id']);
        if ($eq->status !== 'Available') {
            return back()->with('error', "Equipment '{$eq->name}' is not currently available for assignment.");
        }

        $assignment = EquipmentAssignment::create([
            'equipment_id' => $eq->id,
            'assignable_type' => $validated['athlete_id'] ? 'Athlete' : 'Team',
            'athlete_id' => $validated['athlete_id'] ?? null,
            'team_id' => $validated['team_id'],
            'assigned_by' => auth()->id(),
            'assigned_date' => now(),
            'expected_end_date' => $validated['expected_end_date'],
            'assignment_status' => 'Active',
            'condition_on_assignment' => $validated['condition_on_assignment'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $eq->update(['status' => 'Assigned']);

        AuditService::log('INSERT', 'equipment_assignments', $assignment->id, null, $assignment->toArray());

        return redirect()->route('coach.assignments.index')
                         ->with('success', "Equipment '{$eq->name}' successfully assigned.");
    }
}
