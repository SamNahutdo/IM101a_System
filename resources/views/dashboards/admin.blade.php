@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page_title', 'Athletics Administrator Dashboard')

@section('content')
<!-- STATS ROW -->
<div class="row g-3 mb-4">
    <div class="col-md-2 col-6">
        <div class="card-stat">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Total Equipment</small>
            <h3 class="fw-bold mb-0 text-dark">{{ $totalEquipment }}</h3>
            <small class="text-muted"><i class="bi bi-box-seam"></i> Master stock</small>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card-stat">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Available</small>
            <h3 class="fw-bold mb-0 text-success">{{ $availableEquipment }}</h3>
            <small class="text-success"><i class="bi bi-check-circle"></i> Ready for loan</small>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card-stat">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Borrowed</small>
            <h3 class="fw-bold mb-0 text-primary">{{ $borrowedCount }}</h3>
            <small class="text-primary"><i class="bi bi-arrow-right-circle"></i> Active loans</small>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card-stat">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Maintenance</small>
            <h3 class="fw-bold mb-0 text-warning">{{ $maintenanceCount }}</h3>
            <small class="text-warning"><i class="bi bi-tools"></i> Active work orders</small>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card-stat">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Pending</small>
            <h3 class="fw-bold mb-0 text-info">{{ $pendingCount }}</h3>
            <small class="text-info"><i class="bi bi-clock-history"></i> Awaiting review</small>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card-stat">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Overdue</small>
            <h3 class="fw-bold mb-0 text-danger">{{ $overdueCount }}</h3>
            <small class="text-danger"><i class="bi bi-exclamation-triangle"></i> Via MySQL View</small>
        </div>
    </div>
</div>

<!-- QUICK ACTION & RECENT ACTIVITY -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-arrow-left-right me-2 text-primary"></i>Recent Equipment Transactions</h6>
                <a href="{{ route('admin.borrowing.index') }}" class="btn btn-outline-primary btn-sm">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tx ID</th>
                            <th>Borrower</th>
                            <th>Equipment Items (N:M)</th>
                            <th>Return Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentTransactions as $tx)
                            <tr>
                                <td>#{{ $tx->id }}</td>
                                <td>
                                    <strong>{{ $tx->athlete->fullName ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $tx->athlete->student_number ?? '' }}</small>
                                </td>
                                <td>
                                    @foreach ($tx->items as $i)
                                        <div>{{ $i->equipment->equipment_name ?? 'Equipment' }} &times; {{ $i->quantity }}</div>
                                    @endforeach
                                </td>
                                <td>{{ $tx->expected_return_date->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge {{ $tx->status === 'Returned' ? 'bg-success' : ($tx->status === 'Borrowed' ? 'bg-primary' : 'bg-warning text-dark') }}">
                                        {{ $tx->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">No transactions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-cpu me-2 text-primary"></i>Database Engine Highlights</h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush small">
                    <div class="list-group-item px-0">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <strong>3NF Normalization:</strong> 12 relational tables without repeating groups or transitive dependencies.
                    </div>
                    <div class="list-group-item px-0">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <strong>Two N:M Relationships:</strong> <code>team_members</code> & <code>borrowing_items</code> with intersection attributes.
                    </div>
                    <div class="list-group-item px-0">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <strong>MySQL Stored Procedures:</strong> Atomic checkout and return transactions with row locking.
                    </div>
                    <div class="list-group-item px-0">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <strong>Database Triggers:</strong> Automatic audit logging & zero-negative inventory enforcement.
                    </div>
                    <div class="list-group-item px-0">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <strong>Database Views:</strong> <code>vw_equipment_inventory</code> & <code>vw_overdue_borrowings</code>.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
