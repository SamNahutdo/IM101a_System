<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\BorrowingTransaction;
use App\Models\MaintenanceRecord;
use App\Models\Team;
use App\Models\Athlete;
use App\Models\User;
use App\Models\Sport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function admin()
    {
        $totalEquipment = Equipment::sum('quantity');
        $availableEquipment = Equipment::sum('available_quantity');
        $borrowedCount = BorrowingTransaction::where('status', 'Borrowed')->count();
        $maintenanceCount = MaintenanceRecord::whereIn('status', ['Scheduled', 'In Progress'])->count();
        $pendingCount = BorrowingTransaction::where('status', 'Pending')->count();

        $recentTransactions = BorrowingTransaction::with(['athlete', 'items.equipment'])
            ->latest('id')
            ->take(5)
            ->get();

        $overdueCount = DB::table('vw_overdue_borrowings')->count();

        $upcomingReturns = BorrowingTransaction::with(['athlete', 'items.equipment'])
            ->whereIn('status', ['Borrowed', 'Pending'])
            ->orderBy('expected_return_date', 'asc')
            ->take(4)
            ->get();

        return view('dashboards.admin', compact(
            'totalEquipment',
            'availableEquipment',
            'borrowedCount',
            'maintenanceCount',
            'pendingCount',
            'overdueCount',
            'recentTransactions',
            'upcomingReturns'
        ));
    }

    public function staff()
    {
        $totalEquipment = Equipment::sum('quantity');
        $availableEquipment = Equipment::sum('available_quantity');
        $activeLoans = BorrowingTransaction::where('status', 'Borrowed')->count();
        $maintenanceCount = MaintenanceRecord::whereIn('status', ['Scheduled', 'In Progress'])->count();
        $overdueCount = DB::table('vw_overdue_borrowings')->count();

        $recentReturns = BorrowingTransaction::with(['athlete', 'items.equipment'])
            ->where('status', 'Borrowed')
            ->latest('id')
            ->take(5)
            ->get();

        return view('dashboards.staff', compact(
            'totalEquipment',
            'availableEquipment',
            'activeLoans',
            'maintenanceCount',
            'overdueCount',
            'recentReturns'
        ));
    }

    public function coach()
    {
        $user = Auth::user();
        $coach = $user->coach;

        $teams = $coach ? $coach->teams()->with('sport')->withCount('athletes')->get() : collect();
        $myTeamsCount = $teams->count();
        $availableEquipment = Equipment::sum('available_quantity');

        // Borrowings by athletes in coach's teams
        $athleteIds = $coach ? DB::table('team_members')
            ->join('teams', 'team_members.team_id', '=', 'teams.id')
            ->where('teams.coach_id', $coach->id)
            ->pluck('team_members.athlete_id') : collect();

        $teamBorrowings = BorrowingTransaction::with(['athlete', 'items.equipment'])
            ->whereIn('athlete_id', $athleteIds)
            ->latest('id')
            ->take(5)
            ->get();

        return view('dashboards.coach', compact('myTeamsCount', 'teams', 'availableEquipment', 'teamBorrowings'));
    }

    public function student()
    {
        $user = Auth::user();
        $athlete = $user->athlete;

        $availableEquipment = Equipment::sum('available_quantity');
        
        $myBorrowings = $athlete ? BorrowingTransaction::with(['items.equipment'])
            ->where('athlete_id', $athlete->id)
            ->where('status', 'Borrowed')
            ->get() : collect();

        $pendingRequests = $athlete ? BorrowingTransaction::with(['items.equipment'])
            ->where('athlete_id', $athlete->id)
            ->where('status', 'Pending')
            ->get() : collect();

        $historyCount = $athlete ? BorrowingTransaction::where('athlete_id', $athlete->id)->count() : 0;

        return view('dashboards.student', compact(
            'availableEquipment',
            'myBorrowings',
            'pendingRequests',
            'historyCount',
            'athlete'
        ));
    }
}
