@extends('astrologer.layouts.app')

@section('title', 'Referral Analytics')
@section('page_title', 'Performance & Conversion Analytics')

@section('content')
<!-- Filter Range Controls -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-0">Referral Traffic & Sales Conversion</h5>
        <p class="text-muted small mb-0">Real-time metrics on how your product recommendations perform</p>
    </div>
    <div class="btn-group" role="group">
        <a href="{{ route('astrologer.analytics.index', ['range' => '7']) }}" class="btn btn-sm {{ $range == '7' ? 'btn-warning fw-bold' : 'btn-outline-secondary' }}">Last 7 Days</a>
        <a href="{{ route('astrologer.analytics.index', ['range' => '30']) }}" class="btn btn-sm {{ $range == '30' ? 'btn-warning fw-bold' : 'btn-outline-secondary' }}">Last 30 Days</a>
        <a href="{{ route('astrologer.analytics.index', ['range' => '90']) }}" class="btn btn-sm {{ $range == '90' ? 'btn-warning fw-bold' : 'btn-outline-secondary' }}">Last 90 Days</a>
    </div>
</div>

<!-- Key Performance Indicators Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="metric-card bg-white border-0 shadow-sm p-4" style="border-radius: 14px;">
            <span class="text-muted small fw-semibold text-uppercase">Total Link Clicks</span>
            <h3 class="fw-bold text-dark mt-1 mb-0">{{ number_format($totalClicks) }}</h3>
            <small class="text-muted">Unique & returning client hits</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="metric-card bg-white border-0 shadow-sm p-4" style="border-radius: 14px;">
            <span class="text-muted small fw-semibold text-uppercase">Converted Orders</span>
            <h3 class="fw-bold text-success mt-1 mb-0">{{ number_format($totalConversions) }}</h3>
            <small class="text-muted">Orders with successful checkout</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="metric-card bg-white border-0 shadow-sm p-4" style="border-radius: 14px;">
            <span class="text-muted small fw-semibold text-uppercase">Conversion Rate</span>
            <h3 class="fw-bold text-primary mt-1 mb-0">{{ $conversionRate }}%</h3>
            <small class="text-muted">Orders divided by total clicks</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="metric-card bg-white border-0 shadow-sm p-4" style="border-radius: 14px;">
            <span class="text-muted small fw-semibold text-uppercase">Commissions Generated</span>
            <h3 class="fw-bold text-warning mt-1 mb-0">₹{{ number_format($totalEarnings, 2) }}</h3>
            <small class="text-muted">Earnings during selected period</small>
        </div>
    </div>
</div>

<!-- Chart Card -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius: 14px;">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                <h6 class="fw-bold mb-0"><i class="bi bi-graph-up text-warning me-2"></i>Daily Traffic & Conversions</h6>
            </div>
            <div class="card-body p-4 pt-2">
                <canvas id="trafficChart" style="max-height: 320px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Products Card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                <h6 class="fw-bold mb-0"><i class="bi bi-trophy-fill text-warning me-2"></i>Top Recommended Remedies</h6>
            </div>
            <div class="card-body p-4 pt-2">
                @forelse($topProducts as $item)
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <div>
                            <strong class="d-block text-dark small">{{ $item->product->name ?? 'Product #'.$item->product_id }}</strong>
                            <span class="text-muted small">{{ $item->sales_count }} sales converted</span>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold text-success small">₹{{ number_format($item->total_commission, 2) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-bar-chart fs-2 text-secondary opacity-50 d-block mb-1"></i>
                        <span class="small">No sales logged in this period yet.</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('trafficChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [
                    {
                        label: 'Referral Clicks',
                        data: {!! json_encode($clicksData) !!},
                        borderColor: '#F5B041',
                        backgroundColor: 'rgba(245, 176, 65, 0.1)',
                        fill: true,
                        tension: 0.3,
                    },
                    {
                        label: 'Conversions (Orders)',
                        data: {!! json_encode($conversionsData) !!},
                        borderColor: '#22C55E',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        fill: true,
                        tension: 0.3,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
</script>
@endpush
@endsection
