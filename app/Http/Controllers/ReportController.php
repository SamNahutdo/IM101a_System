<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reportType = $request->input('type', 'equipment');

        $data = match ($reportType) {
            'borrowing' => DB::table('vw_current_borrowings')->get(),
            'overdue' => DB::select("CALL sp_get_overdue_equipment()"),
            'maintenance' => DB::table('maintenance_records')
                ->join('equipment', 'maintenance_records.equipment_id', '=', 'equipment.id')
                ->select(
                    'equipment.equipment_code',
                    'equipment.equipment_name',
                    'maintenance_records.maintenance_type',
                    'maintenance_records.description',
                    'maintenance_records.scheduled_date',
                    'maintenance_records.completed_date',
                    'maintenance_records.status'
                )
                ->orderBy('maintenance_records.scheduled_date', 'desc')
                ->get(),
            default => DB::table('vw_equipment_inventory')->get(),
        };

        return view('admin.reports.index', compact('reportType', 'data'));
    }
}
