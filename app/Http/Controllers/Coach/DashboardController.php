<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\Reservation;
use App\Models\BorrowingTransaction;
use App\Models\EquipmentAssignment;

class DashboardController extends Controller
{
    public function index()
    {
        $coachProfile = auth()->user()->coachProfile;

        if (!$coachProfile) {
            abort(403, 'Coach profile not found.');
        }

        $myTeams = Team::with(['athletes', 'teamMembers.athlete.user'])
                       ->where('coach_id', $coachProfile->id)
                       ->get();

        $teamIds = $myTeams->pluck('id');

        $stats = [
            'total_teams' => $myTeams->count(),
            'total_athletes' => $myTeams->sum(fn ($t) => $t->athletes->count()),
            'active_reservations' => Reservation::whereIn('team_id', $teamIds)
                                                ->whereIn('status', ['Pending', 'Approved'])
                                                ->count(),
            'active_borrowings' => BorrowingTransaction::whereIn('team_id', $teamIds)
                                                       ->where('status', 'Active')
                                                       ->count(),
        ];

        $teamReservations = Reservation::with(['items.equipment', 'team'])
                                       ->whereIn('team_id', $teamIds)
                                       ->latest()
                                       ->take(6)
                                       ->get();

        $activeAssignments = EquipmentAssignment::with(['equipment', 'athlete.user', 'team'])
                                                ->whereIn('team_id', $teamIds)
                                                ->where('assignment_status', 'Active')
                                                ->get();

        return view('coach.dashboard', compact('myTeams', 'stats', 'teamReservations', 'activeAssignments'));
    }
}
