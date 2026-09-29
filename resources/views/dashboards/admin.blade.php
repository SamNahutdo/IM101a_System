@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page_title', 'Athletics Administrator Dashboard')

@section('content')
<!-- STATS ROW (Matching Productly 5 Stat Cards) -->
<div class="row g-3 mb-4">
    <!-- Card 1: Total Equipment -->
    <div class="col">
        <div class="card-stat-modern h-100">
            <div class="stat-icon-circle stat-icon-coral">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
            <div class="stat-label">Total Equipment</div>
            <div class="stat-trend">+Master Stock</div>
            <h3 class="stat-value" style="color: var(--coral);">{{ $totalEquipment }}</h3>
        </div>
    </div>

    <!-- Card 2: Available Stock -->
    <div class="col">
        <div class="card-stat-modern h-100">
            <div class="stat-icon-circle stat-icon-cyan">
                <i class="bi bi-percent"></i>
            </div>
            <div class="stat-label">Available Stock</div>
            <div class="stat-trend text-info">Ready to Issue</div>
            <h3 class="stat-value text-dark">{{ $availableEquipment }}</h3>
        </div>
    </div>

    <!-- Card 3: Active Loans -->
    <div class="col">
        <div class="card-stat-modern h-100">
            <div class="stat-icon-circle stat-icon-green">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="stat-label">Active Loans</div>
            <div class="stat-trend text-success">With Athletes</div>
            <h3 class="stat-value text-dark">{{ $borrowedCount }}</h3>
        </div>
    </div>

    <!-- Card 4: In Maintenance -->
    <div class="col">
        <div class="card-stat-modern h-100">
            <div class="stat-icon-circle stat-icon-indigo">
                <i class="bi bi-cart3"></i>
            </div>
            <div class="stat-label">Maintenance</div>
            <div class="stat-trend text-primary">Active Work Orders</div>
            <h3 class="stat-value text-dark">{{ $maintenanceCount }}</h3>
        </div>
    </div>

    <!-- Card 5: Overdue / Attention -->
    <div class="col">
        <div class="card-stat-modern h-100">
            <div class="stat-icon-circle stat-icon-amber">
                <i class="bi bi-chat-dots-fill"></i>
            </div>
            <div class="stat-label">Review Items</div>
            <div class="stat-trend text-warning">Pending & Overdue</div>
            <h3 class="stat-value text-dark">{{ $pendingCount + $overdueCount }}</h3>
        </div>
    </div>
</div>

<!-- MAIN DASHBOARD CONTENT GRID (70% Left / 30% Right) -->
<div class="row g-4">
    <!-- LEFT COLUMN (Summary Chart + Recent Transactions Table) -->
    <div class="col-lg-8 d-flex flex-column gap-4">
        
        <!-- SUMMARY ACTIVITY CHART CARD -->
        <div class="card-modern">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Summary Activity</h5>
                    <small class="text-muted">Equipment loan & return trends across the season</small>
                </div>
                <div class="dropdown">
                    <button class="pill-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Month
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm rounded-3">
                        <li><a class="dropdown-item small" href="#">Week</a></li>
                        <li><a class="dropdown-item small active" href="#">Month</a></li>
                        <li><a class="dropdown-item small" href="#">Semester</a></li>
                    </ul>
                </div>
            </div>

            <!-- CHART CANVAS WITH SMOOTH SPLINE & 45k HIGHLIGHT -->
            <div class="position-relative" style="height: 240px; width: 100%;">
                <canvas id="summaryActivityChart"></canvas>
            </div>
        </div>

        <!-- RECENT TRANSACTIONS TABLE CARD (Matching "Last Order" in screenshot) -->
        <div class="card-modern">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="fw-bold mb-0 text-dark">Recent Transactions</h5>
                    <i class="bi bi-search text-muted" style="font-size: 0.95rem;"></i>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.borrowing.index') }}" class="pill-btn">
                        <i class="bi bi-sliders"></i> Filter
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Borrower</th>
                            <th>Equipment Items (N:M)</th>
                            <th>Return Due</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentTransactions as $tx)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm" style="width: 38px; height: 38px; background: linear-gradient(135deg, #3B82F6, #1D4ED8); font-size: 0.82rem; flex-shrink: 0;">
                                            {{ strtoupper(substr($tx->athlete->first_name ?? 'A', 0, 1) . substr($tx->athlete->last_name ?? 'T', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 0.88rem;">{{ $tx->athlete->fullName ?? 'Unknown Athlete' }}</div>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ $tx->athlete->student_number ?? '#TX-' . $tx->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @foreach ($tx->items as $i)
                                        <div class="d-inline-block me-1 mb-1">
                                            <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.75rem; font-weight: 600;">
                                                {{ $i->equipment->equipment_name ?? 'Item' }} <span class="text-primary">&times; {{ $i->quantity }}</span>
                                            </span>
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    <div class="fw-semibold text-secondary" style="font-size: 0.85rem;">
                                        {{ $tx->expected_return_date ? $tx->expected_return_date->format('M d, Y') : 'N/A' }}
                                    </div>
                                </td>
                                <td>
                                    @if ($tx->status === 'Returned')
                                        <span class="pill-soft pill-green">
                                            <i class="bi bi-check-circle-fill"></i> Returned
                                        </span>
                                    @elseif ($tx->status === 'Borrowed')
                                        <span class="pill-soft pill-blue">
                                            <i class="bi bi-arrow-repeat"></i> Active
                                        </span>
                                    @elseif ($tx->status === 'Pending')
                                        <span class="pill-soft pill-amber">
                                            <i class="bi bi-clock"></i> Pending
                                        </span>
                                    @else
                                        <span class="pill-soft pill-coral">
                                            <i class="bi bi-exclamation-triangle-fill"></i> {{ $tx->status }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-1"></i> No transactions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN (Hero Coral Card + Upcoming Returns + Status Sparkline) -->
    <div class="col-lg-4 d-flex flex-column gap-4">
        
        <!-- HERO CORAL BALANCE CARD (Exact match to "$ 9.470 Active Balance" card) -->
        <div style="background: var(--coral-gradient); border-radius: 22px; padding: 1.75rem; color: #FFFFFF; box-shadow: var(--coral-shadow);">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2 class="fw-bold mb-0 text-white" style="font-size: 2.2rem; letter-spacing: -0.5px;">
                        {{ $totalEquipment }} <span style="font-size: 1.25rem; font-weight: 600;">Units</span>
                    </h2>
                    <div style="font-size: 0.88rem; color: rgba(255,255,255,0.85); font-weight: 600; margin-top: 2px;">
                        Master Equipment Stock
                    </div>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle p-2 text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-shield-check" style="font-size: 1.2rem;"></i>
                </div>
            </div>

            <!-- Breakdown rows with pill icons -->
            <div class="my-4 d-flex flex-column gap-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-circle p-1 bg-white bg-opacity-25 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                            <i class="bi bi-arrow-up text-white" style="font-size: 0.75rem;"></i>
                        </span>
                        <span style="font-size: 0.85rem; font-weight: 600;">Available Stock</span>
                    </div>
                    <span class="fw-bold" style="font-size: 0.95rem;">{{ $availableEquipment }} Units</span>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-circle p-1 bg-white bg-opacity-25 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                            <i class="bi bi-arrow-down text-white" style="font-size: 0.75rem;"></i>
                        </span>
                        <span style="font-size: 0.85rem; font-weight: 600;">Active On Loan</span>
                    </div>
                    <span class="fw-bold" style="font-size: 0.95rem;">{{ $borrowedCount }} Active</span>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-circle p-1 bg-white bg-opacity-25 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                            <i class="bi bi-arrow-down text-white" style="font-size: 0.75rem;"></i>
                        </span>
                        <span style="font-size: 0.85rem; font-weight: 600;">In Maintenance</span>
                    </div>
                    <span class="fw-bold" style="font-size: 0.95rem;">{{ $maintenanceCount }} Items</span>
                </div>
            </div>

            <!-- Action Button -->
            <a href="{{ route('admin.equipment.create') }}" class="btn btn-light w-100 rounded-pill fw-bold py-2 shadow-sm d-flex align-items-center justify-content-center gap-2" style="color: var(--coral);">
                Add New Equipment <i class="bi bi-caret-right-fill"></i>
            </a>
        </div>

        <!-- UPCOMING RETURNS CARD (Matching "Upcoming Payments" in screenshot) -->
        <div class="card-modern">
            <h6 class="fw-bold mb-3 text-dark">Upcoming Returns</h6>
            
            <div class="d-flex flex-column gap-3 mb-3">
                @forelse ($upcomingReturns as $idx => $return)
                    @php
                        $dotColor = $idx === 0 ? '#10B981' : ($idx === 1 ? '#F59E0B' : '#EF4444');
                        $pillBg = $idx === 0 ? '#DCFCE7' : ($idx === 1 ? '#FEF3C7' : '#FEE2E2');
                        $pillText = $idx === 0 ? '#15803D' : ($idx === 1 ? '#B45309' : '#B91C1C');
                    @endphp
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $dotColor }}; flex-shrink: 0;"></span>
                            <div class="text-truncate fw-semibold text-dark" style="font-size: 0.85rem;">
                                {{ $return->athlete->fullName ?? 'Athlete' }}
                            </div>
                        </div>
                        <span class="pill-soft" style="background: {{ $pillBg }}; color: {{ $pillText }};">
                            {{ $return->expected_return_date ? $return->expected_return_date->format('M d') : 'Pending' }}
                        </span>
                    </div>
                @empty
                    <div class="text-muted small py-2">No upcoming returns scheduled.</div>
                @endforelse
            </div>

            <div class="border-top pt-2">
                <a href="{{ route('admin.borrowing.index') }}" class="text-decoration-none fw-bold" style="color: #94A3B8; font-size: 0.82rem;">
                    More <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- EXPENSES / DATABASE ENGINE STATUS (Matching "Expenses Status" in screenshot) -->
        <div class="card-modern">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold text-dark" style="font-size: 0.9rem;">System Status</span>
                <span class="pill-soft pill-green">On Track</span>
            </div>

            <!-- Mini Sparkline Wave (matching coral wave in screenshot) -->
            <div style="height: 60px; width: 100%;">
                <canvas id="miniSparklineChart"></canvas>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                <small class="text-muted" style="font-size: 0.75rem;">3NF Engine & MySQL Triggers</small>
                <small class="fw-bold text-success" style="font-size: 0.75rem;"><i class="bi bi-check2-circle"></i> Active</small>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. MAIN SUMMARY ACTIVITY SPLINE CHART (Coral Smooth Line with 45k badge)
    const ctx = document.getElementById('summaryActivityChart').getContext('2d');
    
    // Create soft gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 240);
    gradient.addColorStop(0, 'rgba(255, 111, 89, 0.25)');
    gradient.addColorStop(1, 'rgba(255, 111, 89, 0.0)');

    const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const dataValues = [12, 11, 14, 9, 21, 45, 18, 26, 38, 32, 28, 36];

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Activity',
                data: dataValues,
                borderColor: '#FF6F59',
                backgroundColor: gradient,
                borderWidth: 3,
                tension: 0.45,
                fill: true,
                pointBackgroundColor: function(context) {
                    return context.dataIndex === 5 ? '#FF6F59' : 'transparent';
                },
                pointBorderColor: function(context) {
                    return context.dataIndex === 5 ? '#FFFFFF' : 'transparent';
                },
                pointBorderWidth: 3,
                pointRadius: function(context) {
                    return context.dataIndex === 5 ? 7 : 0;
                },
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#FF6F59',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    cornerRadius: 8,
                    displayColors: false,
                    padding: 8
                }
            },
            scales: {
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { color: '#94A3B8', font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' } }
                },
                y: {
                    min: 0,
                    max: 50,
                    grid: { color: '#F1F5F9', drawBorder: false },
                    ticks: {
                        stepSize: 10,
                        color: '#94A3B8',
                        font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' },
                        callback: function(val) {
                            return val === 50 ? '30+' : val;
                        }
                    }
                }
            }
        }
    });

    // 2. MINI SPARKLINE CHART (Matching "Expenses Status" coral wave in screenshot)
    const sparkCtx = document.getElementById('miniSparklineChart').getContext('2d');
    new Chart(sparkCtx, {
        type: 'line',
        data: {
            labels: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            datasets: [{
                data: [15, 22, 18, 20, 16, 28, 22, 24, 30, 25],
                borderColor: '#FF6F59',
                borderWidth: 2.5,
                tension: 0.5,
                pointRadius: 0,
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { enabled: false } },
            scales: {
                x: { display: false },
                y: { display: false }
            }
        }
    });
});
</script>
@endpush
