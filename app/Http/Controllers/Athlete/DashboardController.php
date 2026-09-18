<?php

namespace App\Http\Controllers\Athlete;

use App\Http\Controllers\Controller;
use App\Models\BorrowingTransaction;
use App\Models\Reservation;
use App\Models\EquipmentAssignment;
use App\Models\DamageReport;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $athleteProfile = $user->athleteProfile;

        $activeLoans = BorrowingTransaction::with(['items.equipment', 'team'])
                                           ->where('borrower_id', $user->id)
                                           ->where('status', 'Active')
                                           ->get();

        $overdueLoans = $activeLoans->filter(function ($t) {
            return Carbon::now()->greaterThan($t->expected_return_time) && is_null($t->actual_return_time);
        });

        $pendingReservations = Reservation::with(['items.equipment'])
                                          ->where('requester_id', $user->id)
                                          ->whereIn('status', ['Pending', 'Approved'])
                                          ->latest()
                                          ->get();

        $myAssignments = $athleteProfile
            ? EquipmentAssignment::with('equipment')
                                 ->where('athlete_id', $athleteProfile->id)
                                 ->where('assignment_status', 'Active')
                                 ->get()
            : collect();

        $stats = [
            'active_loans' => $activeLoans->count(),
            'overdue_loans' => $overdueLoans->count(),
            'pending_requests' => $pendingReservations->count(),
            'assigned_items' => $myAssignments->count(),
        ];

        return view('athlete.dashboard', compact(
            'stats',
            'activeLoans',
            'overdueLoans',
            'pendingReservations',
            'myAssignments'
        ));
    }
}
