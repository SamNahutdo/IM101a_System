@extends('layouts.app')

@section('title', 'Athletes Directory')
@section('page_title', 'Student Athletes Directory')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-person-badge me-2 text-primary"></i>Enrolled Student Athletes</h6>
            <small class="text-muted">Directly normalized into <code>athletes</code> linked to <code>users</code></small>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Student No.</th>
                    <th>Full Name</th>
                    <th>Department</th>
                    <th>Year Level</th>
                    <th>Contact</th>
                    <th>Varsity Teams (N:M)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($athletes as $a)
                    <tr>
                        <td class="font-monospace fw-bold">{{ $a->student_number }}</td>
                        <td><strong>{{ $a->fullName }}</strong></td>
                        <td>{{ $a->department }}</td>
                        <td><span class="badge bg-secondary">Year {{ $a->year_level }}</span></td>
                        <td>{{ $a->contact_number ?? 'N/A' }}</td>
                        <td>
                            @forelse ($a->teams as $t)
                                <span class="badge bg-light text-dark border">{{ $t->team_name }}</span>
                            @empty
                                <span class="text-muted small">None</span>
                            @endforelse
                        </td>
                        <td><span class="badge bg-success">{{ $a->status }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($athletes->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $athletes->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
