@extends('layouts.app')

@section('page_title', 'Equipment Returns & Quality Inspection')

@section('content')
<div class="card-table-container">
    <div class="card-table-header">
        <h3 class="card-table-title">Active Borrowed Items Eligible for Return</h3>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Loan Code</th>
                    <th>Equipment Item</th>
                    <th>Borrower</th>
                    <th>Checkout Condition</th>
                    <th>Due Time</th>
                    <th>Inspection & Return</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($activeItems as $item)
                    <tr>
                        <td><strong>{{ $item->transaction->transaction_code }}</strong></td>
                        <td>
                            <strong>{{ $item->equipment->name }}</strong>
                            <div><code>{{ $item->equipment->asset_code }}</code></div>
                        </td>
                        <td>{{ $item->transaction->borrower->full_name }}</td>
                        <td><span class="badge badge-secondary">{{ $item->checkout_condition }}</span></td>
                        <td>
                            @if ($item->transaction->is_overdue)
                                <span class="badge badge-damaged">OVERDUE</span>
                                <div style="font-size: 0.75rem; color: #dc2626;">{{ $item->transaction->expected_return_time->format('M j, g:i A') }}</div>
                            @else
                                <span style="font-size: 0.85rem;">{{ $item->transaction->expected_return_time->format('M j, g:i A') }}</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-primary btn-sm" onclick="openReturnModal({{ $item->id }}, '{{ addslashes($item->equipment->name) }}', '{{ $item->equipment->asset_code }}')">
                                Inspect & Check In
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--slate-400); padding: 28px;">No equipment is currently checked out. All items returned.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 16px 20px;">
        {{ $activeItems->links() }}
    </div>
</div>

<!-- Return & Inspection Modal -->
<div id="returnModal" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center;">
    <div style="background: #fff; border-radius: var(--radius-lg); width: 100%; max-width: 580px; padding: 28px; box-shadow: var(--shadow-lg);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-size: 1.15rem; font-weight: 700;">Return Inspection: <span id="modalEqName"></span></h3>
            <button type="button" onclick="closeModal('returnModal')" style="border: none; background: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
        </div>

        <form id="returnForm" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Inspected Return Condition *</label>
                <select name="return_condition" class="form-select" required onchange="toggleDamageFields(this.value)">
                    <option value="Excellent">Excellent (No defects, match-ready)</option>
                    <option value="Good" selected>Good (Normal cosmetic wear)</option>
                    <option value="Fair">Fair (Noticeable wear, may need maintenance)</option>
                    <option value="Poor">Poor (Damaged / Needs structural repair)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Return Notes / Remarks</label>
                <input type="text" name="remarks" class="form-control" placeholder="Optional staff return notes...">
            </div>

            <!-- Conditional Damage Reporting Form -->
            <div id="damageReportBox" style="display: none; background: #fef2f2; border: 1px solid #fecaca; border-radius: var(--radius); padding: 16px; margin-bottom: 18px;">
                <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; font-weight: 700; color: #991b1b; margin-bottom: 10px;">
                    <input type="checkbox" name="report_damage" id="reportDamageCheck" value="1" onchange="toggleDamageInputs()">
                    <span>File Official Damage Report for this asset</span>
                </label>
                <div id="damageInputs" style="display: none;">
                    <div class="form-group">
                        <label class="form-label">Damage Type *</label>
                        <input type="text" name="damage_type" class="form-control" placeholder="e.g. Tear, Crack, Deflated Bladder">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Severity *</label>
                        <select name="damage_severity" class="form-select">
                            <option value="Minor">Minor</option>
                            <option value="Moderate">Moderate</option>
                            <option value="Severe">Severe (Equipment rendered unusable)</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Damage Description *</label>
                        <textarea name="damage_description" class="form-control" rows="2" placeholder="Specific details regarding the defect..."></textarea>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 20px;">
                <button type="submit" class="btn btn-primary">Submit Inspection & Complete Return</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('returnModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openReturnModal(itemId, eqName, eqCode) {
        document.getElementById('modalEqName').innerText = eqName + ' (' + eqCode + ')';
        document.getElementById('returnForm').action = '/staff/returns/' + itemId;
        openModal('returnModal');
    }

    function toggleDamageFields(val) {
        const box = document.getElementById('damageReportBox');
        if (val === 'Fair' || val === 'Poor') {
            box.style.display = 'block';
        } else {
            box.style.display = 'none';
            document.getElementById('reportDamageCheck').checked = false;
            document.getElementById('damageInputs').style.display = 'none';
        }
    }

    function toggleDamageInputs() {
        const checked = document.getElementById('reportDamageCheck').checked;
        document.getElementById('damageInputs').style.display = checked ? 'block' : 'none';
    }
</script>
@endsection
