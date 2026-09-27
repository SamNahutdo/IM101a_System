@extends('layouts.app')

@section('title', 'Student Dashboard')
@section('page_title', 'Student Athlete Equipment Portal')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4 col-6">
        <div class="card-stat">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Currently Borrowed</small>
            <h3 class="fw-bold mb-0 text-primary">{{ $myBorrowings->count() }}</h3>
            <small class="text-primary"><i class="bi bi-box-seam"></i> Active equipment loans</small>
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="card-stat">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Pending Requests</small>
            <h3 class="fw-bold mb-0 text-warning">{{ $pendingRequests->count() }}</h3>
            <small class="text-warning"><i class="bi bi-hourglass-split"></i> Awaiting staff approval</small>
        </div>
    </div>
    <div class="col-md-4 col-12">
        <div class="card-stat">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Quick Action</small>
            <div class="mt-2">
                <a href="{{ route('student.borrowing.create') }}" class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-hand-index-thumb"></i> Request Equipment Loan
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- MY ACTIVE BORROWINGS -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-box-seam me-2 text-primary"></i>My Active Equipment Loans</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tx ID</th>
                            <th>Equipment Items (N:M)</th>
                            <th>Borrow Date</th>
                            <th>Return Due</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($myBorrowings as $tx)
                            @php
                                $isOverdue = $tx->isOverdue();
                            @endphp
                            <tr class="{{ $isOverdue ? 'table-warning' : '' }}">
                                <td>#{{ $tx->id }}</td>
                                <td>
                                    @foreach ($tx->items as $i)
                                        <div>
                                            <strong>{{ $i->equipment->equipment_name ?? 'Equipment' }}</strong> &times; {{ $i->quantity }}
                                            <span class="badge bg-light text-dark border">{{ $i->equipment->equipment_code ?? '' }}</span>
                                        </div>
                                    @endforeach
                                </td>
                                <td>{{ $tx->borrow_date->format('M d, Y') }}</td>
                                <td>
                                    <span class="{{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                                        {{ $tx->expected_return_date->format('M d, Y') }}
                                    </span>
                                    @if ($isOverdue)
                                        <span class="badge bg-danger small ms-1">OVERDUE</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $tx->status }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-emoji-smile fs-3 d-block mb-1"></i> You currently have no borrowed equipment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- STUDENT PROFILE SUMMARY -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-person-badge me-2 text-primary"></i>Athlete Profile</h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted d-block">Student Name</small>
                    <span class="fw-bold text-dark">{{ $athlete->fullName ?? auth()->user()->username }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Student Number</small>
                    <span class="font-monospace fw-semibold">{{ $athlete->student_number ?? 'N/A' }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Department & Year</small>
                    <span>{{ $athlete->department ?? 'N/A' }} (Year {{ $athlete->year_level ?? 1 }})</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Team Membership (N:M)</small>
                    @if ($athlete && $athlete->teams->count() > 0)
                        @foreach ($athlete->teams as $tm)
                            <span class="badge bg-info text-dark">{{ $tm->team_name }} ({{ $tm->pivot->position ?? 'Member' }})</span>
                        @endforeach
                    @else
                        <span class="text-muted small">Not currently registered in a varsity team</span>
                    @endif
                </div>
                <a href="{{ route('student.damage.create') }}" class="btn btn-outline-danger btn-sm w-100">
                    <i class="bi bi-exclamation-triangle"></i> Report Damaged Gear
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
