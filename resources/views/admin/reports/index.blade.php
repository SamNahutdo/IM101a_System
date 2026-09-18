@extends('layouts.app')

@section('page_title', 'Advanced Relational Database Reports')

@section('content')
<!-- Report 1 & 2: Inventory Status & Most Borrowed -->
<div class="grid-2">
    <div class="card-table-container">
        <div class="card-table-header">
            <h3 class="card-table-title">1. Real-Time Inventory Distribution</h3>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Asset Count</th>
                        <th>Distribution</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = array_sum($inventoryStats); @endphp
                    @foreach (['Available', 'Borrowed', 'Reserved', 'Maintenance', 'Damaged', 'Lost', 'Retired'] as $st)
                        @php $cnt = $inventoryStats[$st] ?? 0; @endphp
                        <tr>
                            <td><strong>{{ $st }}</strong></td>
                            <td>{{ $cnt }} items</td>
                            <td>
                                <div style="background: var(--slate-100); border-radius: 9999px; height: 8px; width: 100%; overflow: hidden;">
                                    <div style="background: var(--accent-cyan); height: 100%; width: {{ $total > 0 ? ($cnt / $total) * 100 : 0 }}%;"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-table-container">
        <div class="card-table-header">
            <h3 class="card-table-title">2. Most Utilized Equipment (Borrowing Frequency)</h3>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Equipment Name</th>
                        <th>Asset Code</th>
                        <th>Total Borrowings</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mostBorrowed as $mb)
                        <tr>
                            <td><strong>{{ $mb->name }}</strong></td>
                            <td><code>{{ $mb->asset_code }}</code></td>
                            <td><span class="badge badge-borrowed">{{ $mb->borrow_count }} loans</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align: center; color: var(--slate-400); padding: 20px;">No borrowing records yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Report 3: Overdue Report -->
<div class="card-table-container">
    <div class="card-table-header">
        <h3 class="card-table-title" style="color: #b91c1c;">3. Overdue Equipment Report</h3>
        <span class="badge badge-damaged">{{ count($overdueLoans) }} Delinquent Loans</span>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Transaction Code</th>
                    <th>Borrower</th>
                    <th>Team</th>
                    <th>Equipment Borrowed</th>
                    <th>Expected Return</th>
                    <th>Days Overdue</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($overdueLoans as $loan)
                    <tr>
                        <td><strong>{{ $loan->transaction_code }}</strong></td>
                        <td>{{ $loan->borrower->full_name }} ({{ $loan->borrower->email }})</td>
                        <td>{{ $loan->team ? $loan->team->name : 'Individual' }}</td>
                        <td>
                            @foreach ($loan->items as $it)
                                <div>&bull; {{ $it->equipment->name }} (<code>{{ $it->equipment->asset_code }}</code>)</div>
                            @endforeach
                        </td>
                        <td style="color: #b91c1c; font-weight: 600;">{{ $loan->expected_return_time->format('M j, Y g:i A') }}</td>
                        <td><span class="badge badge-damaged">{{ $loan->days_overdue }} days late</span></td>
                        <td><span class="badge badge-damaged">OVERDUE</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--slate-500); padding: 24px;">No delinquent or overdue transactions recorded.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Report 4: Maintenance Costs by Category -->
<div class="card-table-container">
    <div class="card-table-header">
        <h3 class="card-table-title">4. Maintenance Expenditures by Category</h3>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Repair Work Orders</th>
                    <th>Cumulative Cost (PHP)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($maintenanceCosts as $mc)
                    <tr>
                        <td><strong>{{ $mc->category_name }}</strong></td>
                        <td>{{ $mc->total_repairs }} jobs</td>
                        <td><strong>PHP {{ number_format($mc->total_cost, 2) }}</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: var(--slate-400); padding: 20px;">No maintenance expenditure recorded.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
