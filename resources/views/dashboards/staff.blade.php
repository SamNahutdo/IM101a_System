@extends('layouts.app')

@section('title', 'Staff Dashboard')
@section('page_title', 'Equipment Desk Operations')

@section('content')
<!-- STATS ROW -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card-stat-modern h-100">
            <div class="stat-icon-circle stat-icon-coral">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="stat-label">Equipment Stock</div>
            <div class="stat-trend">+Total Inventory</div>
            <h3 class="stat-value" style="color: var(--coral);">{{ $totalEquipment }}</h3>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card-stat-modern h-100">
            <div class="stat-icon-circle stat-icon-cyan">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="stat-label">Ready for Issue</div>
            <div class="stat-trend text-info">Available for loan</div>
            <h3 class="stat-value text-dark">{{ $availableEquipment }}</h3>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card-stat-modern h-100">
            <div class="stat-icon-circle stat-icon-green">
                <i class="bi bi-arrow-repeat"></i>
            </div>
            <div class="stat-label">Active Loans</div>
            <div class="stat-trend text-success">Out with athletes</div>
            <h3 class="stat-value text-dark">{{ $activeLoans }}</h3>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card-stat-modern h-100">
            <div class="stat-icon-circle stat-icon-amber">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="stat-label">Overdue Loans</div>
            <div class="stat-trend text-danger">Requires notice</div>
            <h3 class="stat-value text-danger">{{ $overdueCount }}</h3>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- ACTIVE LOANS TABLE -->
    <div class="col-lg-8">
        <div class="card-modern">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Active Loans Pending Return</h5>
                    <small class="text-muted">Loans currently in circulation with athletes</small>
                </div>
                <a href="{{ route('staff.borrowing.index') }}" class="pill-btn">
                    <i class="bi bi-arrow-left-right"></i> Process Returns
                </a>
            </div>

            <div class="table-responsive">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Tx ID</th>
                            <th>Borrower</th>
                            <th>Equipment Items</th>
                            <th>Return Due</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentReturns as $tx)
                            <tr>
                                <td><span class="badge bg-light text-secondary border">#{{ $tx->id }}</span></td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $tx->athlete->fullName ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $tx->athlete->student_number ?? '' }}</small>
                                </td>
                                <td>
                                    @foreach ($tx->items as $i)
                                        <div class="d-inline-block me-1 mb-1">
                                            <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.75rem;">
                                                {{ $i->equipment->equipment_name ?? 'Equipment' }} &times; {{ $i->quantity }}
                                            </span>
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @if ($tx->isOverdue())
                                        <span class="pill-soft pill-coral">
                                            <i class="bi bi-exclamation-circle-fill"></i> {{ $tx->expected_return_date->format('M d, Y') }}
                                        </span>
                                    @else
                                        <span class="pill-soft pill-blue">
                                            {{ $tx->expected_return_date->format('M d, Y') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('staff.borrowing.index') }}" class="pill-btn">
                                        Return
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-check-circle fs-3 d-block mb-1 text-success"></i> No active loans pending return.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- QUICK ACTIONS & STATUS -->
    <div class="col-lg-4 d-flex flex-column gap-4">
        <!-- HERO CORAL ACTION CARD -->
        <div style="background: var(--coral-gradient); border-radius: 22px; padding: 1.75rem; color: #FFFFFF; box-shadow: var(--coral-shadow);">
            <h5 class="fw-bold mb-1 text-white">Desk Actions</h5>
            <p class="small mb-3" style="color: rgba(255,255,255,0.85);">Fast checkout & return management</p>

            <div class="d-flex flex-column gap-2 mb-3">
                <a href="{{ route('staff.borrowing.create') }}" class="btn btn-light w-100 rounded-pill fw-bold py-2 shadow-sm d-flex align-items-center justify-content-center gap-2" style="color: var(--coral);">
                    <i class="bi bi-cart-plus-fill"></i> Issue New Loan (SP)
                </a>
                <a href="{{ route('staff.borrowing.index') }}" class="btn btn-outline-light w-100 rounded-pill fw-bold py-2 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-arrow-left-right"></i> Active Loans Queue
                </a>
            </div>
        </div>

        <div class="card-modern">
            <h6 class="fw-bold mb-3 text-dark">Quick Inventory Status</h6>
            <div class="d-flex flex-column gap-3">
                <a href="{{ route('staff.equipment.index') }}" class="text-decoration-none d-flex justify-content-between align-items-center p-2 rounded-3 hover-bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-box-seam text-primary fs-5"></i>
                        <span class="fw-semibold text-dark small">Inspect Equipment Inventory</span>
                    </div>
                    <i class="bi bi-chevron-right text-muted small"></i>
                </a>
                <a href="{{ route('staff.maintenance.index') }}" class="text-decoration-none d-flex justify-content-between align-items-center p-2 rounded-3 hover-bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-tools text-warning fs-5"></i>
                        <span class="fw-semibold text-dark small">Maintenance Queue</span>
                    </div>
                    <i class="bi bi-chevron-right text-muted small"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
