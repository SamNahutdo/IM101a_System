@extends('layouts.app')

@section('page_title', 'Update Equipment Record')

@section('content')
<div class="card-table-container" style="max-width: 800px; margin: 0 auto; padding: 28px;">
    <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 24px; color: var(--slate-900);">Edit Asset Details: {{ $equipment->asset_code }}</h3>

    <form action="{{ route('admin.equipment.update', $equipment) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid-2" style="gap: 16px;">
            <div class="form-group">
                <label class="form-label">Asset Code</label>
                <input type="text" class="form-control" value="{{ $equipment->asset_code }}" disabled style="background: var(--slate-100);">
            </div>
            <div class="form-group">
                <label class="form-label">Serial Number</label>
                <input type="text" name="serial_number" class="form-control" value="{{ old('serial_number', $equipment->serial_number) }}">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Equipment Name *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $equipment->name) }}" required>
        </div>

        <div class="grid-2" style="gap: 16px;">
            <div class="form-group">
                <label class="form-label">Category *</label>
                <select name="category_id" class="form-select" required>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $equipment->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Storage Location *</label>
                <select name="location_id" class="form-select" required>
                    @foreach ($locations as $loc)
                        <option value="{{ $loc->id }}" {{ old('location_id', $equipment->location_id) == $loc->id ? 'selected' : '' }}>
                            {{ $loc->building }} — {{ $loc->room }} ({{ $loc->shelf_bin ?? 'General' }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid-2" style="gap: 16px;">
            <div class="form-group">
                <label class="form-label">Brand</label>
                <input type="text" name="brand" class="form-control" value="{{ old('brand', $equipment->brand) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Model</label>
                <input type="text" name="model" class="form-control" value="{{ old('model', $equipment->model) }}">
            </div>
        </div>

        <div class="grid-2" style="gap: 16px;">
            <div class="form-group">
                <label class="form-label">Inventory Status *</label>
                <select name="status" class="form-select" required>
                    @foreach (['Available', 'Reserved', 'Borrowed', 'Assigned', 'Maintenance', 'Damaged', 'Lost', 'Retired'] as $st)
                        <option value="{{ $st }}" {{ old('status', $equipment->status) === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Current Condition *</label>
                <select name="current_condition" class="form-select" required>
                    @foreach (['New', 'Excellent', 'Good', 'Fair', 'Poor'] as $cond)
                        <option value="{{ $cond }}" {{ old('current_condition', $equipment->current_condition) === $cond ? 'selected' : '' }}>{{ $cond }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Purchase Cost (PHP) *</label>
            <input type="number" step="0.01" min="0" name="purchase_cost" class="form-control" value="{{ old('purchase_cost', $equipment->purchase_cost) }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $equipment->description) }}</textarea>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 24px;">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="{{ route('admin.equipment.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
