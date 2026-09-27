@extends('layouts.app')

@section('title', 'Coach Dashboard')
@section('page_title', 'Athletics Coach Portal')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4 col-6">
        <div class="card-stat">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">My Assigned Teams</small>
            <h3 class="fw-bold mb-0 text-dark">{{ $myTeamsCount }}</h3>
            <small class="text-primary"><i class="bi bi-diagram-3"></i> Varsity & Club Teams</small>
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="card-stat">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Equipment Available</small>
            <h3 class="fw-bold mb-0 text-success">{{ $availableEquipment }}</h3>
            <small class="text-success"><i class="bi bi-check-circle"></i> In stock for practice</small>
        </div>
    </div>
    <div class="col-md-4 col-12">
        <div class="card-stat">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Quick Links</small>
            <div class="mt-2 d-flex gap-2">
                <a href="{{ route('coach.borrowing.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-cart-plus"></i> Request Gear</a>
                <a href="{{ route('coach.maintenance.create') }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-exclamation-triangle"></i> Report Damage</a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- MY TEAMS ROSTER OVERVIEW -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-people me-2 text-primary"></i>My Teams & Rosters (N:M)</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse ($teams as $t)
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">{{ $t->team_name }}</h6>
                                <small class="text-muted">{{ $t->sport->sport_name }} &bull; S.Y. {{ $t->school_year }}</small>
                            </div>
                            <span class="badge bg-primary rounded-pill">{{ $t->athletes_count }} Roster Athletes</span>
                        </li>
                    @empty
                        <li class="list-group-item py-4 text-center text-muted">No teams assigned yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- RECENT TEAM EQUIPMENT TRANSACTIONS -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-arrow-left-right me-2 text-primary"></i>Team Athletes Borrowing History</h6>
                <a href="{{ route('coach.borrowing.index') }}" class="btn btn-outline-primary btn-sm">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Athlete</th>
                            <th>Equipment Items (N:M)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($teamBorrowings as $tx)
                            <tr>
                                <td>
                                    <strong>{{ $tx->athlete->fullName ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $tx->athlete->student_number ?? '' }}</small>
                                </td>
                                <td>
                                    @foreach ($tx->items as $i)
                                        <div>{{ $i->equipment->equipment_name ?? 'Equipment' }} &times; {{ $i->quantity }}</div>
                                    @endforeach
                                </td>
                                <td>
                                    <span class="badge {{ $tx->status === 'Returned' ? 'bg-success' : ($tx->status === 'Borrowed' ? 'bg-primary' : 'bg-warning text-dark') }}">
                                        {{ $tx->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-3 text-muted">No equipment borrowed yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
