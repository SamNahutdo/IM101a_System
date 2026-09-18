<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BorrowingTransaction;
use App\Models\Reservation;
use App\Models\DamageReport;
use App\Models\MaintenanceRecord;
use App\Models\Equipment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'active_checkouts' => BorrowingTransaction::where('status', 'Active')->count(),
            'pending_reservations' => Reservation::where('status', 'Pending')->count(),
            'overdue_count' => BorrowingTransaction::where('status', 'Active')
                                                   ->where('expected_return_time', '<', Carbon::now())
                                                   ->whereNull('actual_return_time')
                                                   ->count(),
            'active_maintenance' => MaintenanceRecord::whereIn('status', ['Scheduled', 'In Progress'])->count(),
            'available_equipment' => Equipment::where('status', 'Available')->count(),
        ];

        $pendingReservations = Reservation::with(['requester', 'items.equipment', 'team'])
                                          ->where('status', 'Pending')
                                          ->latest()
                                          ->take(6)
                                          ->get();

        $activeBorrowings = BorrowingTransaction::with(['borrower', 'items.equipment', 'team'])
                                                ->where('status', 'Active')
                                                ->latest('checkout_time')
                                                ->take(8)
                                                ->get();

        $overdueLoans = BorrowingTransaction::with(['borrower', 'items.equipment', 'team'])
                                            ->where('status', 'Active')
                                            ->where('expected_return_time', '<', Carbon::now())
                                            ->whereNull('actual_return_time')
                                            ->get();

        return view('staff.dashboard', compact('stats', 'pendingReservations', 'activeBorrowings', 'overdueLoans'));
    }
}
