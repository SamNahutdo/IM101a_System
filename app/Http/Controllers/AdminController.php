<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Athlete;
use App\Models\Coach;
use App\Models\Sport;
use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // USERS & ROLES
    public function usersIndex(Request $request)
    {
        $roles = Role::all();
        $users = User::with(['role', 'athlete', 'coach'])
            ->when($request->filled('role_id'), fn($q) => $q->where('role_id', $request->role_id))
            ->orderBy('username')
            ->paginate(15);

        return view('admin.users.index', compact('users', 'roles'));
    }

    // ATHLETES
    public function athletesIndex(Request $request)
    {
        $athletes = Athlete::with(['user', 'teams'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where('student_number', 'like', "%{$s}%")
                  ->orWhere('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%");
            })
            ->orderBy('last_name')
            ->paginate(15);

        return view('admin.athletes.index', compact('athletes'));
    }

    // COACHES
    public function coachesIndex(Request $request)
    {
        $coaches = Coach::with(['user', 'teams.sport'])
            ->orderBy('last_name')
            ->paginate(15);

        return view('admin.coaches.index', compact('coaches'));
    }

    // SPORTS
    public function sportsIndex()
    {
        $sports = Sport::withCount(['teams', 'equipment'])->orderBy('sport_name')->get();
        return view('admin.sports.index', compact('sports'));
    }

    public function sportsStore(Request $request)
    {
        $validated = $request->validate([
            'sport_name' => ['required', 'string', 'max:100', 'unique:sports,sport_name'],
            'description' => ['nullable', 'string'],
        ]);

        Sport::create($validated);

        return back()->with('success', "Sport '{$validated['sport_name']}' created successfully.");
    }

    // TEAMS (Many-to-Many 1: Teams <-> Athletes)
    public function teamsIndex()
    {
        $teams = Team::with(['sport', 'coach', 'athletes'])->withCount('athletes')->get();
        $sports = Sport::where('status', 'Active')->get();
        $coaches = Coach::where('status', 'Active')->get();
        $athletes = Athlete::where('status', 'Active')->orderBy('last_name')->get();

        return view('admin.teams.index', compact('teams', 'sports', 'coaches', 'athletes'));
    }

    public function teamsStore(Request $request)
    {
        $validated = $request->validate([
            'sport_id' => ['required', 'exists:sports,id'],
            'coach_id' => ['nullable', 'exists:coaches,id'],
            'team_name' => ['required', 'string', 'max:100'],
            'school_year' => ['required', 'string', 'max:20'],
        ]);

        Team::create($validated);

        return back()->with('success', "Team '{$validated['team_name']}' created successfully.");
    }

    public function addTeamMember(Request $request, Team $team)
    {
        $validated = $request->validate([
            'athlete_id' => ['required', 'exists:athletes,id'],
            'position' => ['nullable', 'string', 'max:50'],
        ]);

        // Check unique composite key in team_members junction table
        $exists = TeamMember::where('team_id', $team->id)->where('athlete_id', $validated['athlete_id'])->exists();
        if ($exists) {
            return back()->with('error', 'Integrity Constraint: Athlete is already enrolled in this team roster.');
        }

        TeamMember::create([
            'team_id' => $team->id,
            'athlete_id' => $validated['athlete_id'],
            'joined_at' => now()->toDateString(),
            'position' => $validated['position'] ?? 'Player',
            'status' => 'Active',
        ]);

        return back()->with('success', 'Athlete added to team roster (N:M Junction table team_members updated).');
    }

    public function removeTeamMember(TeamMember $member)
    {
        $member->delete();
        return back()->with('success', 'Athlete removed from team roster.');
    }
}
