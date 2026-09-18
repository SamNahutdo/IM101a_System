<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\BorrowingTransaction;
use App\Models\DamageReport;
use App\Models\MaintenanceRecord;
use App\Models\EquipmentCategory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        // 1. Inventory distribution by category & status
        $inventoryStats = Equipment::select('status', DB::raw('count(*) as count'))
                                   ->groupBy('status')
                                   ->pluck('count', 'status')
                                   ->toArray();

        // 2. Equipment usage (most borrowed equipment)
        $mostBorrowed = DB::table('borrowing_items')
                          ->join('equipment', 'borrowing_items.equipment_id', '=', 'equipment.id')
                          ->select('equipment.id', 'equipment.name', 'equipment.asset_code', DB::raw('count(*) as borrow_count'))
                          ->groupBy('equipment.id', 'equipment.name', 'equipment.asset_code')
                          ->orderByDesc('borrow_count')
                          ->take(10)
                          ->get();

        // 3. Damage reports summary
        $damageSummary = DamageReport::with(['equipment', 'reporter'])
                                     ->latest('reported_at')
                                     ->take(10)
                                     ->get();

        // 4. Maintenance expenditures by category
        $maintenanceCosts = DB::table('maintenance_records')
                              ->join('equipment', 'maintenance_records.equipment_id', '=', 'equipment.id')
                              ->join('equipment_categories', 'equipment.category_id', '=', 'equipment_categories.id')
                              ->select('equipment_categories.name as category_name', DB::raw('SUM(maintenance_records.cost) as total_cost'), DB::raw('COUNT(*) as total_repairs'))
                              ->groupBy('equipment_categories.name')
                              ->get();

        // 5. Overdue borrowings
        $overdueLoans = BorrowingTransaction::with(['borrower', 'items.equipment', 'team'])
                                            ->where('status', 'Active')
                                            ->where('expected_return_time', '<', Carbon::now())
                                            ->whereNull('actual_return_time')
                                            ->get();

        return view('admin.reports.index', compact(
            'inventoryStats',
            'mostBorrowed',
            'damageSummary',
            'maintenanceCosts',
            'overdueLoans'
        ));
    }
}
