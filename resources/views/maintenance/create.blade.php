@extends('layouts.app')

@section('title', 'Log Maintenance / Damage')
@section('page_title', 'Report Damage or Schedule Maintenance')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-tools me-2 text-warning"></i>Damage & Maintenance Ticket</h6>
                <small class="text-muted">Logging a repair will safely flag the equipment status in the database</small>
            </div>
            <div class="card-body p-4">
                <form action="{{ route(auth()->user()->role->name . '.maintenance.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Affected Equipment <span class="text-danger">*</span></label>
                        <select name="equipment_id" class="form-select @error('equipment_id') is-invalid @enderror" required>
                            <option value="">-- Select Equipment --</option>
                            @foreach ($equipment as $eq)
                                <option value="{{ $eq->id }}" {{ old('equipment_id') == $eq->id ? 'selected' : '' }}>
                                    [{{ $eq->equipment_code }}] {{ $eq->equipment_name }} - Current Condition: {{ $eq->condition }}
                                </option>
                            @endforeach
                        </select>
                        @error('equipment_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Maintenance / Issue Type <span class="text-danger">*</span></label>
                            <input type="text" name="maintenance_type" class="form-control @error('maintenance_type') is-invalid @enderror" value="{{ old('maintenance_type') }}" placeholder="e.g. Broken Grip, Torn Seam, Restring, Surface Crack" required>
                            @error('maintenance_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Scheduled Date <span class="text-danger">*</span></label>
                            <input type="date" name="scheduled_date" class="form-control @error('scheduled_date') is-invalid @enderror" value="{{ old('scheduled_date', now()->toDateString()) }}" required>
                            @error('scheduled_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Description of Damage / Work Required <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Describe the physical condition or required repair in detail..." required>{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Work Order Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="Scheduled" {{ old('status') == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="In Progress" {{ old('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Completed" {{ old('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Remarks</label>
                            <input type="text" name="remarks" class="form-control" value="{{ old('remarks') }}" placeholder="Optional technician notes...">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route(auth()->user()->role->name . '.maintenance.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-warning px-4 text-dark fw-semibold">
                            <i class="bi bi-save me-1"></i> Submit Maintenance Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
