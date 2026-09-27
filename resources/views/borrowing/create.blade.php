@extends('layouts.app')

@section('title', 'Process Borrowing')
@section('page_title', 'New Equipment Borrowing Transaction')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-cart-plus me-2 text-primary"></i>Equipment Checkout / Borrowing</h6>
                <small class="text-muted">Executes MySQL Stored Procedure <code>sp_process_borrowing</code> with row locking & atomic transaction</small>
            </div>
            <div class="card-body p-4">
                <form action="{{ route(auth()->user()->role->name . '.borrowing.store') }}" method="POST">
                    @csrf

                    @if (!auth()->user()->isStudent())
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">Select Athlete (Borrower) <span class="text-danger">*</span></label>
                            <select name="athlete_id" class="form-select @error('athlete_id') is-invalid @enderror" required>
                                <option value="">-- Choose Athlete --</option>
                                @foreach ($athletes as $a)
                                    <option value="{{ $a->id }}" {{ old('athlete_id') == $a->id ? 'selected' : '' }}>
                                        {{ $a->fullName }} ({{ $a->student_number }}) - {{ $a->department }}
                                    </option>
                                @endforeach
                            </select>
                            @error('athlete_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    @else
                        <div class="alert alert-info py-2 small mb-3">
                            <i class="bi bi-person-check-fill me-1"></i> Requesting as: <strong>{{ auth()->user()->athlete->fullName ?? auth()->user()->username }}</strong> ({{ auth()->user()->athlete->student_number ?? '' }})
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold text-secondary small">Equipment to Borrow <span class="text-danger">*</span></label>
                            <select name="equipment_id" id="equipmentSelect" class="form-select @error('equipment_id') is-invalid @enderror" required onchange="updateMaxQuantity()">
                                <option value="">-- Select Available Equipment --</option>
                                @foreach ($equipment as $eq)
                                    <option value="{{ $eq->id }}" data-avail="{{ $eq->available_quantity }}" {{ old('equipment_id') == $eq->id ? 'selected' : '' }}>
                                        [{{ $eq->equipment_code }}] {{ $eq->equipment_name }} (Avail: {{ $eq->available_quantity }} / Total: {{ $eq->quantity }}) - {{ $eq->sport->sport_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('equipment_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-secondary small">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" id="quantityInput" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', 1) }}" min="1" required>
                            @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted" id="availNotice">Enter quantity</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Expected Return Date <span class="text-danger">*</span></label>
                        <input type="date" name="expected_return_date" class="form-control @error('expected_return_date') is-invalid @enderror" value="{{ old('expected_return_date', now()->addDays(3)->toDateString()) }}" min="{{ now()->toDateString() }}" required>
                        @error('expected_return_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary small">Purpose / Remarks</label>
                        <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" rows="2" placeholder="e.g. Training practice, inter-collegiate match preparation...">{{ old('remarks') }}</textarea>
                        @error('remarks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="p-3 bg-light border rounded mb-4">
                        <div class="fw-semibold text-dark small mb-1"><i class="bi bi-shield-lock-fill text-primary"></i> Database Defense Mechanics:</div>
                        <ul class="text-muted small mb-0 ps-3">
                            <li>Row locking (<code>SELECT ... FOR UPDATE</code>) eliminates race conditions.</li>
                            <li>Prevents negative inventory: Requests exceeding live stock are rejected with rollback.</li>
                            <li>MySQL Stored Procedure <code>sp_process_borrowing</code> commits atomically.</li>
                        </ul>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route(auth()->user()->role->name . '.borrowing.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Loans
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle me-1"></i> Submit Borrowing (CALL SP)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function updateMaxQuantity() {
        const select = document.getElementById('equipmentSelect');
        const selected = select.options[select.selectedIndex];
        const avail = selected.getAttribute('data-avail');
        const notice = document.getElementById('availNotice');
        const qtyInput = document.getElementById('quantityInput');
        if (avail) {
            notice.textContent = `Live stock available: ${avail}`;
            notice.className = 'text-success fw-bold';
        } else {
            notice.textContent = 'Enter quantity';
            notice.className = 'text-muted';
        }
    }
    document.addEventListener('DOMContentLoaded', updateMaxQuantity);
</script>
@endsection
