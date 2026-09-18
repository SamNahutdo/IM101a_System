<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\AthleteProfile;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $coachProfile = auth()->user()->coachProfile;
        $teams = Team::with(['teamMembers.athlete.user', 'assignments.equipment'])
                     ->where('coach_id', $coachProfile->id)
                     ->get();

        return view('coach.teams.index', compact('teams'));
    }

    public function show(Team $team)
    {
        $coachProfile = auth()->user()->coachProfile;
        if ($team->coach_id !== $coachProfile->id) {
            abort(403, 'Unauthorized access to this team roster.');
        }

        $team->load(['teamMembers.athlete.user', 'assignments.equipment', 'reservations.items.equipment']);

        return view('coach.teams.show', compact('team'));
    }
}
