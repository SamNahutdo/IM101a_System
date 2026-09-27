@extends('layouts.app')

@section('title', 'Add Equipment')
@section('page_title', 'Create New Equipment Record')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-plus-circle me-2 text-primary"></i>New Athletic Equipment Master Record</h6>
                <small class="text-muted">Will trigger database audit logging on insert</small>
            </div>
            <div class="card-body p-4">
                <form action="{{ route(auth()->user()->role->name . '.equipment.store') }}" method="POST">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small">Sport <span class="text-danger">*</span></label>
                            <select name="sport_id" class="form-select @error('sport_id') is-invalid @enderror" required>
                                <option value="">-- Select Sport --</option>
                                @foreach ($sports as $s)
                                    <option value="{{ $s->id }}" {{ old('sport_id') == $s->id ? 'selected' : '' }}>{{ $s->sport_name }}</option>
                                @endforeach
                            </select>
                            @error('sport_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small">Equipment Code <span class="text-danger">*</span></label>
                            <input type="text" name="equipment_code" class="form-control @error('equipment_code') is-invalid @enderror" value="{{ old('equipment_code') }}" placeholder="e.g. EQ-BB-005" required>
                            @error('equipment_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Equipment Name <span class="text-danger">*</span></label>
                        <input type="text" name="equipment_name" class="form-control @error('equipment_name') is-invalid @enderror" value="{{ old('equipment_name') }}" placeholder="e.g. Easton Baseball Bat 32-inch" required>
                        @error('equipment_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small">Category <span class="text-danger">*</span></label>
                            <input type="text" name="category" class="form-control @error('category') is-invalid @enderror" value="{{ old('category') }}" placeholder="e.g. Bats, Gloves, Balls, Nets" required>
                            @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small">Total Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', 1) }}" min="1" required>
                            @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Available quantity will be initialized to match total quantity.</small>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small">Condition <span class="text-danger">*</span></label>
                            <select name="condition" class="form-select @error('condition') is-invalid @enderror" required>
                                <option value="New" {{ old('condition') == 'New' ? 'selected' : '' }}>New</option>
                                <option value="Good" {{ old('condition', 'Good') == 'Good' ? 'selected' : '' }}>Good</option>
                                <option value="Fair" {{ old('condition') == 'Fair' ? 'selected' : '' }}>Fair</option>
                                <option value="Damaged" {{ old('condition') == 'Damaged' ? 'selected' : '' }}>Damaged</option>
                            </select>
                            @error('condition') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="Available" {{ old('status', 'Available') == 'Available' ? 'selected' : '' }}>Available</option>
                                <option value="In Use" {{ old('status') == 'In Use' ? 'selected' : '' }}>In Use</option>
                                <option value="Under Maintenance" {{ old('status') == 'Under Maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                                <option value="Retired" {{ old('status') == 'Retired' ? 'selected' : '' }}>Retired</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route(auth()->user()->role->name . '.equipment.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Save Equipment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
