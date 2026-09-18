@extends('layouts.app')

@section('page_title', 'Register Athletic Equipment Asset')

@section('content')
<div class="card-table-container" style="max-width: 800px; margin: 0 auto; padding: 28px;">
    <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 24px; color: var(--slate-900);">Asset Registration Form</h3>

    <form action="{{ route('admin.equipment.store') }}" method="POST">
        @csrf

        <div class="grid-2" style="gap: 16px;">
            <div class="form-group">
                <label class="form-label">Asset Code * (Unique Barcode / Tag)</label>
                <input type="text" name="asset_code" class="form-control" value="{{ old('asset_code') }}" required placeholder="e.g. EQ-BBALL-005">
            </div>
            <div class="form-group">
                <label class="form-label">Serial Number (Optional)</label>
                <input type="text" name="serial_number" class="form-control" value="{{ old('serial_number') }}" placeholder="e.g. SN-998822-X">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Equipment Full Name *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g. Molten FIBA Leather Basketball 2026">
        </div>

        <div class="grid-2" style="gap: 16px;">
            <div class="form-group">
                <label class="form-label">Equipment Category *</label>
                <select name="category_id" class="form-select" required>
                    <option value="">Select Category...</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Storage Location *</label>
                <select name="location_id" class="form-select" required>
                    <option value="">Select Storage Facility...</option>
                    @foreach ($locations as $loc)
                        <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>
                            {{ $loc->building }} — {{ $loc->room }} ({{ $loc->shelf_bin ?? 'General' }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid-2" style="gap: 16px;">
            <div class="form-group">
                <label class="form-label">Brand</label>
                <input type="text" name="brand" class="form-control" value="{{ old('brand') }}" placeholder="e.g. Spalding, Mikasa, Nike">
            </div>
            <div class="form-group">
                <label class="form-label">Model</label>
                <input type="text" name="model" class="form-control" value="{{ old('model') }}" placeholder="e.g. TF-1000 Legacy">
            </div>
        </div>

        <div class="grid-2" style="gap: 16px;">
            <div class="form-group">
                <label class="form-label">Purchase Date *</label>
                <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date', date('Y-m-d')) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Purchase Cost (PHP) *</label>
                <input type="number" step="0.01" min="0" name="purchase_cost" class="form-control" value="{{ old('purchase_cost', '0.00') }}" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Initial Condition *</label>
            <select name="current_condition" class="form-select" required>
                <option value="New" selected>New</option>
                <option value="Excellent">Excellent</option>
                <option value="Good">Good</option>
                <option value="Fair">Fair</option>
                <option value="Poor">Poor</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Description / Specifications</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Technical specifications, intended sports division, serial details...">{{ old('description') }}</textarea>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 24px;">
            <button type="submit" class="btn btn-primary">Save to Inventory</button>
            <a href="{{ route('admin.equipment.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
