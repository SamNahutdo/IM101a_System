@extends('layouts.app')

@section('title', 'Coaches Directory')
@section('page_title', 'Athletics Coaching Staff')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-whistle me-2 text-primary"></i>Coaches Directory</h6>
            <small class="text-muted">Normalized in <code>coaches</code> linked to <code>users</code></small>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Coach Name</th>
                    <th>Assigned Teams</th>
                    <th>Contact Number</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($coaches as $c)
                    <tr>
                        <td class="font-monospace fw-bold">{{ $c->employee_number }}</td>
                        <td><strong>Coach {{ $c->fullName }}</strong></td>
                        <td>
                            @forelse ($c->teams as $t)
                                <span class="badge bg-primary">{{ $t->team_name }} ({{ $t->sport->sport_name }})</span>
                            @empty
                                <span class="text-muted small">No team currently assigned</span>
                            @endforelse
                        </td>
                        <td>{{ $c->contact_number ?? 'N/A' }}</td>
                        <td><span class="badge bg-success">{{ $c->status }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
