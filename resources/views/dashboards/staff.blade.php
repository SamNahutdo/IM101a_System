@extends('layouts.app')

@section('title', 'Staff Dashboard')
@section('page_title', 'Equipment Desk Operations')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card-stat">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Equipment Stock</small>
            <h3 class="fw-bold mb-0 text-dark">{{ $totalEquipment }}</h3>
            <small class="text-muted">Total inventory items</small>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card-stat">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Ready for Issue</small>
            <h3 class="fw-bold mb-0 text-success">{{ $availableEquipment }}</h3>
            <small class="text-success"><i class="bi bi-check-circle"></i> Available to borrow</small>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card-stat">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Active Loans</small>
            <h3 class="fw-bold mb-0 text-primary">{{ $activeLoans }}</h3>
            <small class="text-primary">Out with athletes</small>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card-stat">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Overdue Loans</small>
            <h3 class="fw-bold mb-0 text-danger">{{ $overdueCount }}</h3>
            <small class="text-danger"><i class="bi bi-clock"></i> Requires return notice</small>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-arrow-left-right me-2 text-primary"></i>Active Loans Pending Return</h6>
                <a href="{{ route('staff.borrowing.index') }}" class="btn btn-outline-primary btn-sm">Process Returns</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tx ID</th>
                            <th>Borrower</th>
                            <th>Equipment Items (N:M)</th>
                            <th>Return Due</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentReturns as $tx)
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
                                <td>
                                    <span class="{{ $tx->isOverdue() ? 'text-danger fw-bold' : '' }}">
                                        {{ $tx->expected_return_date->format('M d, Y') }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('staff.borrowing.index') }}" class="btn btn-sm btn-outline-success">
                                        Return
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">No active loans pending return.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-lightning-charge me-2 text-warning"></i>Quick Operational Actions</h6>
            </div>
            <div class="card-body d-flex flex-column gap-2">
                <a href="{{ route('staff.borrowing.create') }}" class="btn btn-primary text-start">
                    <i class="bi bi-cart-plus me-2"></i> Issue New Equipment Loan (CALL SP)
                </a>
                <a href="{{ route('staff.borrowing.index') }}" class="btn btn-outline-dark text-start">
                    <i class="bi bi-box-arrow-in-left me-2"></i> Process Returned Equipment
                </a>
                <a href="{{ route('staff.equipment.index') }}" class="btn btn-outline-secondary text-start">
                    <i class="bi bi-box-seam me-2"></i> Inspect Equipment Inventory
                </a>
                <a href="{{ route('staff.maintenance.index') }}" class="btn btn-outline-warning text-start">
                    <i class="bi bi-tools me-2"></i> Maintenance & Damage Queue
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
