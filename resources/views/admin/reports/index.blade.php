@extends('layouts.app')

@section('title', 'System Reports')
@section('page_title', 'Operational Reports (MySQL Views & Stored Procedures)')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Operational & Inventory Reports</h6>
            <small class="text-muted">Generated directly from MySQL Views (<code>vw_equipment_inventory</code>, <code>vw_current_borrowings</code>, <code>vw_overdue_borrowings</code>)</small>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-outline-dark btn-sm"><i class="bi bi-printer"></i> Print Report</button>
        </div>
    </div>
    
    <!-- REPORT NAVIGATION TABS -->
    <div class="card-body bg-light border-bottom py-2">
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link {{ $reportType === 'equipment' ? 'active' : '' }}" href="{{ route('admin.reports.index', ['type' => 'equipment']) }}">
                    <i class="bi bi-box-seam me-1"></i> Equipment Inventory (View)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $reportType === 'borrowing' ? 'active' : '' }}" href="{{ route('admin.reports.index', ['type' => 'borrowing']) }}">
                    <i class="bi bi-arrow-left-right me-1"></i> Current Borrowings (View)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $reportType === 'overdue' ? 'active' : '' }}" href="{{ route('admin.reports.index', ['type' => 'overdue']) }}">
                    <i class="bi bi-clock-history me-1"></i> Overdue Loans (Stored Procedure)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $reportType === 'maintenance' ? 'active' : '' }}" href="{{ route('admin.reports.index', ['type' => 'maintenance']) }}">
                    <i class="bi bi-tools me-1"></i> Maintenance Records
                </a>
            </li>
        </ul>
    </div>

    <!-- REPORT TABLES -->
    <div class="table-responsive">
        @if ($reportType === 'equipment')
            <!-- EQUIPMENT INVENTORY VIEW -->
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Code</th>
                        <th>Equipment Name</th>
                        <th>Sport</th>
                        <th>Category</th>
                        <th class="text-center">Total Qty</th>
                        <th class="text-center">Available</th>
                        <th class="text-center">In Use</th>
                        <th>Condition</th>
                        <th>Availability Status (Function)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $row)
                        <tr>
                            <td><span class="badge bg-dark font-monospace">{{ $row->equipment_code }}</span></td>
                            <td><strong>{{ $row->equipment_name }}</strong></td>
                            <td>{{ $row->sport_name }}</td>
                            <td><span class="badge bg-secondary">{{ $row->category }}</span></td>
                            <td class="text-center fw-bold">{{ $row->total_quantity }}</td>
                            <td class="text-center"><span class="badge bg-success">{{ $row->available_quantity }}</span></td>
                            <td class="text-center">{{ $row->borrowed_or_in_use_quantity }}</td>
                            <td>{{ $row->condition }}</td>
                            <td>
                                @php
                                    $bCls = match($row->stock_status) {
                                        'Available' => 'bg-success',
                                        'Low Stock' => 'bg-warning text-dark',
                                        'Out of Stock' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $bCls }}">{{ $row->stock_status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center py-4 text-muted">No equipment records found.</td></tr>
                    @endforelse
                </tbody>
            </table>

        @elseif ($reportType === 'borrowing')
            <!-- CURRENT BORROWINGS VIEW -->
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tx ID</th>
                        <th>Student Number</th>
                        <th>Borrower Name</th>
                        <th>Department</th>
                        <th>Equipment Item</th>
                        <th>Sport</th>
                        <th class="text-center">Borrowed Qty</th>
                        <th class="text-center">Returned Qty</th>
                        <th>Borrow Date</th>
                        <th>Expected Return</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $row)
                        <tr>
                            <td class="fw-bold">#{{ $row->transaction_id }}</td>
                            <td class="font-monospace">{{ $row->student_number }}</td>
                            <td><strong>{{ $row->borrower_name }}</strong></td>
                            <td>{{ $row->department }}</td>
                            <td>[{{ $row->equipment_code }}] {{ $row->equipment_name }}</td>
                            <td>{{ $row->sport_name }}</td>
                            <td class="text-center fw-bold">{{ $row->borrowed_quantity }}</td>
                            <td class="text-center text-success">{{ $row->returned_quantity }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->borrow_date)->format('M d, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->expected_return_date)->format('M d, Y') }}</td>
                            <td><span class="badge bg-primary">{{ $row->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="11" class="text-center py-4 text-muted">No active borrowing records.</td></tr>
                    @endforelse
                </tbody>
            </table>

        @elseif ($reportType === 'overdue')
            <!-- OVERDUE REPORT VIA SP_GET_OVERDUE_EQUIPMENT -->
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tx ID</th>
                        <th>Student Number</th>
                        <th>Borrower</th>
                        <th>Contact</th>
                        <th>Equipment Item</th>
                        <th class="text-center">Unreturned Qty</th>
                        <th>Borrow Date</th>
                        <th>Due Date</th>
                        <th class="text-danger">Overdue Days (Function)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $row)
                        <tr class="table-danger">
                            <td class="fw-bold">#{{ $row->transaction_id }}</td>
                            <td class="font-monospace">{{ $row->student_number }}</td>
                            <td><strong>{{ $row->borrower_name }}</strong></td>
                            <td>{{ $row->contact_number ?? 'N/A' }}</td>
                            <td>[{{ $row->equipment_code }}] {{ $row->equipment_name }}</td>
                            <td class="text-center fw-bold">{{ $row->unreturned_quantity }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->borrow_date)->format('M d, Y') }}</td>
                            <td class="fw-bold">{{ \Carbon\Carbon::parse($row->expected_return_date)->format('M d, Y') }}</td>
                            <td>
                                <span class="badge bg-danger fs-6">{{ $row->overdue_days }} days overdue</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center py-4 text-success"><i class="bi bi-check-circle"></i> No overdue equipment at this time.</td></tr>
                    @endforelse
                </tbody>
            </table>

        @elseif ($reportType === 'maintenance')
            <!-- MAINTENANCE REPORT -->
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Equipment Code</th>
                        <th>Equipment Name</th>
                        <th>Maintenance Type</th>
                        <th>Description / Issue</th>
                        <th>Scheduled Date</th>
                        <th>Completed Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $row)
                        <tr>
                            <td><span class="badge bg-dark font-monospace">{{ $row->equipment_code }}</span></td>
                            <td><strong>{{ $row->equipment_name }}</strong></td>
                            <td><span class="badge bg-secondary">{{ $row->maintenance_type }}</span></td>
                            <td>{{ $row->description }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->scheduled_date)->format('M d, Y') }}</td>
                            <td>{{ $row->completed_date ? \Carbon\Carbon::parse($row->completed_date)->format('M d, Y') : '-' }}</td>
                            <td>
                                <span class="badge {{ $row->status === 'Completed' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $row->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">No maintenance records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
