@extends('layouts.app')

@section('title', 'Coach Dashboard')
@section('page_title', 'Athletics Coach Portal')

@section('content')
<!-- STATS ROW -->
<div class="row g-3 mb-4">
    <div class="col-md-4 col-6">
        <div class="card-stat-modern h-100">
            <div class="stat-icon-circle stat-icon-coral">
                <i class="bi bi-diagram-3-fill"></i>
            </div>
            <div class="stat-label">Assigned Teams</div>
            <div class="stat-trend">+Varsity & Club</div>
            <h3 class="stat-value" style="color: var(--coral);">{{ $myTeamsCount }}</h3>
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="card-stat-modern h-100">
            <div class="stat-icon-circle stat-icon-cyan">
                <i class="bi bi-box-seam-fill"></i>
            </div>
            <div class="stat-label">Equipment Available</div>
            <div class="stat-trend text-info">In stock for training</div>
            <h3 class="stat-value text-dark">{{ $availableEquipment }}</h3>
        </div>
    </div>
    <div class="col-md-4 col-12">
        <div class="card-stat-modern h-100 justify-content-center">
            <div class="stat-label mb-2">Quick Operations</div>
            <div class="d-flex flex-column gap-2 w-100">
                <a href="{{ route('coach.borrowing.create') }}" class="btn btn-light rounded-pill fw-bold py-2 shadow-sm text-coral d-flex align-items-center justify-content-center gap-2" style="color: var(--coral); border: 1px solid var(--border-soft);">
                    <i class="bi bi-cart-plus-fill"></i> Request Equipment Loan
                </a>
                <a href="{{ route('coach.maintenance.create') }}" class="pill-btn justify-content-center">
                    <i class="bi bi-exclamation-triangle text-warning"></i> Report Equipment Damage
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- MY TEAMS ROSTER OVERVIEW -->
    <div class="col-lg-6">
        <div class="card-modern h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 text-dark">My Teams & Rosters (N:M)</h5>
                <a href="{{ route('coach.teams.index') }}" class="pill-btn">View Details</a>
            </div>
            <div class="d-flex flex-column gap-3">
                @forelse ($teams as $t)
                    <div class="d-flex justify-content-between align-items-center p-3 rounded-4 bg-light border">
                        <div>
                            <h6 class="mb-1 fw-bold text-dark">{{ $t->team_name }}</h6>
                            <small class="text-muted">{{ $t->sport->sport_name }} &bull; S.Y. {{ $t->school_year }}</small>
                        </div>
                        <span class="pill-soft pill-blue">{{ $t->athletes_count }} Athletes</span>
                    </div>
                @empty
                    <div class="py-4 text-center text-muted">No teams assigned yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- RECENT TEAM EQUIPMENT TRANSACTIONS -->
    <div class="col-lg-6">
        <div class="card-modern h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 text-dark">Team Borrowing History</h5>
                <a href="{{ route('coach.borrowing.index') }}" class="pill-btn">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Athlete</th>
                            <th>Equipment Items</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($teamBorrowings as $tx)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $tx->athlete->fullName ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $tx->athlete->student_number ?? '' }}</small>
                                </td>
                                <td>
                                    @foreach ($tx->items as $i)
                                        <div class="d-inline-block me-1 mb-1">
                                            <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.75rem;">
                                                {{ $i->equipment->equipment_name ?? 'Item' }} &times; {{ $i->quantity }}
                                            </span>
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @if ($tx->status === 'Returned')
                                        <span class="pill-soft pill-green">Returned</span>
                                    @elseif ($tx->status === 'Borrowed')
                                        <span class="pill-soft pill-blue">Active</span>
                                    @else
                                        <span class="pill-soft pill-amber">{{ $tx->status }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">No equipment borrowed yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
