@extends('layouts.app')

@section('title', 'Equipment Borrowing')
@section('page_title', 'Equipment Borrowing & Returns (N:M)')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-arrow-left-right me-2 text-primary"></i>Borrowing Transactions</h6>
            <small class="text-muted">Many-to-Many relationship (BorrowingTransactions &harr; Equipment) via <code>borrowing_items</code></small>
        </div>
        <div>
            <a href="{{ route(auth()->user()->role->name . '.borrowing.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i> New Borrowing (SP)
            </a>
        </div>
    </div>

    <!-- FILTERS -->
    <div class="card-body bg-light border-bottom py-3">
        <form method="GET" action="{{ url()->current() }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search athlete name or student number..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- All Statuses --</option>
                    <option value="Borrowed" {{ request('status') == 'Borrowed' ? 'selected' : '' }}>Borrowed (Active)</option>
                    <option value="Returned" {{ request('status') == 'Returned' ? 'selected' : '' }}>Returned</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="Overdue" {{ request('status') == 'Overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-dark btn-sm w-100"><i class="bi bi-filter"></i> Filter</button>
                <a href="{{ url()->current() }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Tx ID</th>
                    <th>Borrower (Athlete)</th>
                    <th>Equipment Items (N:M)</th>
                    <th>Borrow Date</th>
                    <th>Expected Return</th>
                    <th>Status</th>
                    @if (auth()->user()->isAdmin() || auth()->user()->isStaff())
                        <th class="text-end">Actions / Return</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $tx)
                    @php
                        $isOverdue = $tx->isOverdue();
                    @endphp
                    <tr class="{{ $isOverdue ? 'table-warning' : '' }}">
                        <td class="fw-bold">#{{ $tx->id }}</td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $tx->athlete->fullName ?? 'N/A' }}</div>
                            <small class="text-muted font-monospace">{{ $tx->athlete->student_number ?? '' }} | {{ $tx->athlete->department ?? '' }}</small>
                        </td>
                        <td>
                            @foreach ($tx->items as $item)
                                <div class="mb-1">
                                    <span class="badge bg-light text-dark border">{{ $item->equipment->equipment_code ?? '' }}</span>
                                    <strong>{{ $item->equipment->equipment_name ?? 'Equipment' }}</strong>
                                    &times; {{ $item->quantity }}
                                    (Returned: <span class="text-success fw-bold">{{ $item->returned_quantity }}</span>)
                                    @if ($item->condition_after)
                                        <span class="badge bg-info text-dark">{{ $item->condition_after }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </td>
                        <td>{{ $tx->borrow_date->format('M d, Y') }}</td>
                        <td>
                            <div class="{{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                                {{ $tx->expected_return_date->format('M d, Y') }}
                            </div>
                            @if ($isOverdue)
                                <span class="badge bg-danger small"><i class="bi bi-clock-history"></i> OVERDUE</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $tx->status === 'Returned' ? 'bg-success' : ($tx->status === 'Borrowed' ? 'bg-primary' : 'bg-warning text-dark') }}">
                                {{ $tx->status }}
                            </span>
                        </td>
                        @if (auth()->user()->isAdmin() || auth()->user()->isStaff())
                            <td class="text-end">
                                @if ($tx->status === 'Pending')
                                    <form action="{{ route(auth()->user()->role->name . '.borrowing.approve', $tx->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm py-0 px-2">
                                            <i class="bi bi-check2"></i> Approve
                                        </button>
                                    </form>
                                @elseif ($tx->status === 'Borrowed')
                                    @foreach ($tx->items as $item)
                                        @if ($item->returned_quantity < $item->quantity)
                                            <!-- Return Button Triggering Modal -->
                                            <button type="button" class="btn btn-outline-success btn-sm py-0 px-2" data-bs-toggle="modal" data-bs-target="#returnModal{{ $item->id }}">
                                                <i class="bi bi-box-arrow-in-left"></i> Return Item
                                            </button>

                                            <!-- RETURN MODAL FOR SP_PROCESS_RETURN -->
                                            <div class="modal fade" id="returnModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content text-start">
                                                        <div class="modal-header">
                                                            <h6 class="modal-title fw-bold">Process Equipment Return (Stored Procedure)</h6>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route(auth()->user()->role->name . '.borrowing.return', $item->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <p class="small text-muted mb-2">
                                                                    Executing MySQL Stored Procedure <code>sp_process_return</code> will increment available quantity and update condition.
                                                                </p>
                                                                <div class="mb-3">
                                                                    <label class="form-label small fw-semibold">Equipment</label>
                                                                    <input type="text" class="form-control form-control-sm" value="{{ $item->equipment->equipment_name }}" readonly>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-6">
                                                                        <label class="form-label small fw-semibold">Quantity to Return</label>
                                                                        <input type="number" name="returned_quantity" class="form-control form-control-sm" value="{{ $item->quantity - $item->returned_quantity }}" min="1" max="{{ $item->quantity - $item->returned_quantity }}" required>
                                                                        <small class="text-muted">Unreturned: {{ $item->quantity - $item->returned_quantity }}</small>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <label class="form-label small fw-semibold">Return Condition</label>
                                                                        <select name="condition_after" class="form-select form-select-sm" required>
                                                                            <option value="Good" selected>Good</option>
                                                                            <option value="New">New</option>
                                                                            <option value="Fair">Fair</option>
                                                                            <option value="Damaged">Damaged</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer py-2">
                                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary btn-sm">Submit Return (CALL SP)</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    <span class="text-muted small">Completed</span>
                                @endif
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-1"></i> No borrowing transactions found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($transactions->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $transactions->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
