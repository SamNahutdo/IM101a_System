@extends('layouts.app')

@section('page_title', 'Equipment Checkout Management')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <p style="color: var(--slate-600); font-size: 0.9rem;">Review recent checkout transactions or initiate a new checkout dispatch.</p>
    <a href="{{ route('staff.checkout.create') }}" class="btn btn-primary">+ New Equipment Checkout</a>
</div>

<div class="card-table-container">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Transaction Code</th>
                    <th>Borrower</th>
                    <th>Team</th>
                    <th>Processed By</th>
                    <th>Checkout Date</th>
                    <th>Expected Return</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $t)
                    <tr>
                        <td><strong>{{ $t->transaction_code }}</strong></td>
                        <td>{{ $t->borrower->full_name }}</td>
                        <td>{{ $t->team ? $t->team->name : 'Individual' }}</td>
                        <td>{{ $t->staff->full_name }}</td>
                        <td>{{ $t->checkout_time->format('M j, Y g:i A') }}</td>
                        <td>
                            <span style="{{ $t->is_overdue ? 'color: #dc2626; font-weight: 700;' : '' }}">
                                {{ $t->expected_return_time->format('M j, Y g:i A') }}
                            </span>
                        </td>
                        <td>
                            @if ($t->is_overdue)
                                <span class="badge badge-damaged">OVERDUE</span>
                            @elseif ($t->status === 'Active')
                                <span class="badge badge-borrowed">Active</span>
                            @else
                                <span class="badge badge-completed">{{ $t->status }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--slate-400); padding: 24px;">No checkout transactions recorded.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 16px 20px;">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
