@extends('layouts.app')

@section('title', 'Equipment Maintenance')
@section('page_title', 'Equipment Maintenance & Damage Tracking')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-tools me-2 text-warning"></i>Maintenance & Damage Records</h6>
            <small class="text-muted">Track repairs, restringing, and damage reports</small>
        </div>
        <div>
            <a href="{{ route(auth()->user()->role->name . '.maintenance.create') }}" class="btn btn-warning btn-sm text-dark fw-semibold">
                <i class="bi bi-exclamation-triangle me-1"></i> Report Damage / Service
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Record ID</th>
                    <th>Equipment Item</th>
                    <th>Maintenance Type</th>
                    <th>Description</th>
                    <th>Scheduled Date</th>
                    <th>Completed Date</th>
                    <th>Status</th>
                    @if (auth()->user()->isAdmin() || auth()->user()->isStaff())
                        <th class="text-end">Action</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $rec)
                    <tr>
                        <td class="fw-bold">#{{ $rec->id }}</td>
                        <td>
                            <strong>{{ $rec->equipment->equipment_name ?? 'Equipment' }}</strong><br>
                            <small class="text-muted font-monospace">{{ $rec->equipment->equipment_code ?? '' }}</small>
                        </td>
                        <td><span class="badge bg-secondary">{{ $rec->maintenance_type }}</span></td>
                        <td>{{ $rec->description }}</td>
                        <td>{{ $rec->scheduled_date->format('M d, Y') }}</td>
                        <td>{{ $rec->completed_date ? $rec->completed_date->format('M d, Y') : '-' }}</td>
                        <td>
                            <span class="badge {{ $rec->status === 'Completed' ? 'bg-success' : ($rec->status === 'In Progress' ? 'bg-primary' : 'bg-warning text-dark') }}">
                                {{ $rec->status }}
                            </span>
                        </td>
                        @if (auth()->user()->isAdmin() || auth()->user()->isStaff())
                            <td class="text-end">
                                @if ($rec->status !== 'Completed')
                                    <form action="{{ route(auth()->user()->role->name . '.maintenance.complete', $rec->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success btn-sm py-0 px-2" title="Mark as Completed">
                                            <i class="bi bi-check2-circle"></i> Complete
                                        </button>
                                    </form>
                                @else
                                    <span class="text-success small fw-semibold"><i class="bi bi-check-all"></i> Done</span>
                                @endif
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No maintenance or damage records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($records->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $records->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
