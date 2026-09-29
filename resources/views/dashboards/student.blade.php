@extends('layouts.app')

@section('title', 'Student Dashboard')
@section('page_title', 'Student Athlete Equipment Portal')

@section('content')
<!-- STATS ROW -->
<div class="row g-3 mb-4">
    <div class="col-md-4 col-6">
        <div class="card-stat-modern h-100">
            <div class="stat-icon-circle stat-icon-coral">
                <i class="bi bi-box-seam-fill"></i>
            </div>
            <div class="stat-label">Currently Borrowed</div>
            <div class="stat-trend text-primary">Active loans</div>
            <h3 class="stat-value" style="color: var(--coral);">{{ $myBorrowings->count() }}</h3>
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="card-stat-modern h-100">
            <div class="stat-icon-circle stat-icon-cyan">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="stat-label">Pending Requests</div>
            <div class="stat-trend text-info">Awaiting approval</div>
            <h3 class="stat-value text-dark">{{ $pendingRequests->count() }}</h3>
        </div>
    </div>
    <div class="col-md-4 col-12">
        <div class="card-stat-modern h-100 justify-content-center">
            <div class="stat-label mb-2">Need Equipment?</div>
            <a href="{{ route('student.borrowing.create') }}" class="btn btn-light rounded-pill fw-bold py-2 shadow-sm text-coral w-100 d-flex align-items-center justify-content-center gap-2" style="color: var(--coral); border: 1px solid var(--border-soft);">
                <i class="bi bi-hand-index-thumb-fill"></i> Request Loan Now
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- MY ACTIVE BORROWINGS -->
    <div class="col-lg-8">
        <div class="card-modern">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">My Active Equipment Loans</h5>
                    <small class="text-muted">Equipment currently signed out under your student ID</small>
                </div>
                <a href="{{ route('student.borrowing.index') }}" class="pill-btn">View History</a>
            </div>

            <div class="table-responsive">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Tx ID</th>
                            <th>Equipment Items</th>
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
                            <tr>
                                <td><span class="badge bg-light text-secondary border">#{{ $tx->id }}</span></td>
                                <td>
                                    @foreach ($tx->items as $i)
                                        <div class="d-inline-block me-1 mb-1">
                                            <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.75rem;">
                                                {{ $i->equipment->equipment_name ?? 'Equipment' }} &times; {{ $i->quantity }}
                                            </span>
                                        </div>
                                    @endforeach
                                </td>
                                <td>{{ $tx->borrow_date->format('M d, Y') }}</td>
                                <td>
                                    @if ($isOverdue)
                                        <span class="pill-soft pill-coral">
                                            <i class="bi bi-exclamation-circle-fill"></i> {{ $tx->expected_return_date->format('M d, Y') }}
                                        </span>
                                    @else
                                        <span class="pill-soft pill-blue">
                                            {{ $tx->expected_return_date->format('M d, Y') }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="pill-soft pill-green">{{ $tx->status }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-emoji-smile fs-3 d-block mb-1 text-success"></i> You currently have no borrowed equipment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- STUDENT PROFILE SUMMARY -->
    <div class="col-lg-4 d-flex flex-column gap-4">
        <!-- HERO CORAL PROFILE CARD -->
        <div style="background: var(--coral-gradient); border-radius: 22px; padding: 1.75rem; color: #FFFFFF; box-shadow: var(--coral-shadow);">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="profile-avatar" style="width: 48px; height: 48px; font-size: 1.1rem;">
                    {{ auth()->user()->initials ?? 'S' }}
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-white">{{ $athlete->fullName ?? auth()->user()->displayName }}</h5>
                    <small style="color: rgba(255,255,255,0.85);">{{ $athlete->student_number ?? 'Student Athlete' }}</small>
                </div>
            </div>

            <div class="d-flex flex-column gap-2 mb-3 small" style="color: rgba(255,255,255,0.9);">
                <div><strong>Department:</strong> {{ $athlete->department ?? 'General' }} (Year {{ $athlete->year_level ?? 1 }})</div>
                <div>
                    <strong>Team(s):</strong> 
                    @if ($athlete && $athlete->teams->count() > 0)
                        @foreach ($athlete->teams as $tm)
                            <span class="badge bg-white text-dark rounded-pill px-2 py-1 ms-1">{{ $tm->team_name }}</span>
                        @endforeach
                    @else
                        <span>None assigned</span>
                    @endif
                </div>
            </div>

            <a href="{{ route('student.damage.create') }}" class="btn btn-outline-light w-100 rounded-pill fw-bold py-2 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-exclamation-triangle"></i> Report Damaged Gear
            </a>
        </div>
    </div>
</div>
@endsection
