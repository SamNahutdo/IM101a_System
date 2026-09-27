<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('table_name')) {
            $query->where('table_name', $request->table_name);
        }

        $logs = $query->latest('id')->paginate(20);
        $tables = AuditLog::select('table_name')->distinct()->pluck('table_name');
        $actions = AuditLog::select('action')->distinct()->pluck('action');

        return view('admin.audit.index', compact('logs', 'tables', 'actions'));
    }
}
