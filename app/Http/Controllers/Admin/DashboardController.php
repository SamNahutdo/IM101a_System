<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\User;
use App\Models\BorrowingTransaction;
use App\Models\Reservation;
use App\Models\DamageReport;
use App\Models\MaintenanceRecord;
use App\Models\AuditLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_equipment' => Equipment::count(),
            'available_equipment' => Equipment::where('status', 'Available')->count(),
            'borrowed_equipment' => Equipment::where('status', 'Borrowed')->count(),
            'maintenance_equipment' => Equipment::where('status', 'Maintenance')->count(),
            'damaged_equipment' => Equipment::where('status', 'Damaged')->count(),
            'active_users' => User::where('is_active', true)->count(),
            'pending_reservations' => Reservation::where('status', 'Pending')->count(),
            'overdue_count' => BorrowingTransaction::where('status', 'Active')
                                                   ->where('expected_return_time', '<', Carbon::now())
                                                   ->whereNull('actual_return_time')
                                                   ->count(),
        ];

        $recentBorrowings = BorrowingTransaction::with(['borrower', 'items.equipment'])
                                                ->latest('checkout_time')
                                                ->take(5)
                                                ->get();

        $recentReservations = Reservation::with(['requester', 'items.equipment'])
                                         ->latest()
                                         ->take(5)
                                         ->get();

        $recentDamageReports = DamageReport::with(['equipment', 'reporter'])
                                           ->latest('reported_at')
                                           ->take(5)
                                           ->get();

        $upcomingMaintenance = MaintenanceRecord::with(['equipment', 'staff'])
                                                ->whereIn('status', ['Scheduled', 'In Progress'])
                                                ->orderBy('scheduled_date')
                                                ->take(5)
                                                ->get();

        $recentAudits = AuditLog::with('user')
                                ->latest('created_at')
                                ->take(8)
                                ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentBorrowings',
            'recentReservations',
            'recentDamageReports',
            'upcomingMaintenance',
            'recentAudits'
        ));
    }
}
