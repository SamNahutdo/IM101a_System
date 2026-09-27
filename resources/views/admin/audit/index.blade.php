@extends('layouts.app')

@section('title', 'Database Audit Logs')
@section('page_title', 'MySQL Engine Audit Logs (Database Triggers)')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-journal-text me-2 text-primary"></i>Database Engine Triggers Audit Trail</h6>
            <small class="text-muted">Populated automatically by MySQL Triggers (<code>AFTER INSERT</code>, <code>AFTER UPDATE</code>, <code>AFTER DELETE</code>)</small>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="card-body bg-light border-bottom py-3">
        <form method="GET" action="{{ url()->current() }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <select name="action" class="form-select form-select-sm">
                    <option value="">-- All Actions --</option>
                    @foreach ($actions as $act)
                        <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>{{ $act }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="table_name" class="form-select form-select-sm">
                    <option value="">-- All Affected Tables --</option>
                    @foreach ($tables as $tbl)
                        <option value="{{ $tbl }}" {{ request('table_name') == $tbl ? 'selected' : '' }}>{{ $tbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-dark btn-sm w-100"><i class="bi bi-filter"></i> Filter</button>
                <a href="{{ url()->current() }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 font-monospace" style="font-size: 0.85rem;">
            <thead>
                <tr>
                    <th>Log ID</th>
                    <th>Action</th>
                    <th>Table Affected</th>
                    <th>Record ID</th>
                    <th>Old Values (JSON)</th>
                    <th>New Values (JSON)</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td class="fw-bold">#{{ $log->id }}</td>
                        <td>
                            @php
                                $cls = match($log->action) {
                                    'INSERT' => 'bg-success',
                                    'UPDATE' => 'bg-warning text-dark',
                                    'DELETE' => 'bg-danger',
                                    'BORROW' => 'bg-primary',
                                    'RETURN' => 'bg-info text-dark',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $cls }}">{{ $log->action }}</span>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $log->table_name }}</span></td>
                        <td>{{ $log->record_id ?? '-' }}</td>
                        <td>
                            @if ($log->old_values)
                                <code class="text-danger small">{{ json_encode($log->old_values) }}</code>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if ($log->new_values)
                                <code class="text-success small">{{ json_encode($log->new_values) }}</code>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted font-sans">
                            <i class="bi bi-inbox fs-3 d-block mb-1"></i> No audit logs recorded yet. Perform an Equipment CRUD action to trigger logging.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($logs->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $logs->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
