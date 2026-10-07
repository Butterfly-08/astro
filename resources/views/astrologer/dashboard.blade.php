@extends('astrologer.layouts.app')

@section('title', 'Astrologer Dashboard')
@section('page_title', 'Partner Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <!-- Referral Code Banner Card -->
    <div class="col-12">
        <div class="card border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #2D124D 0%, #1A0B2E 100%); border-radius: 16px;">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-lg-7 mb-3 mb-lg-0">
                        <span class="badge bg-warning text-dark mb-2 px-3 py-1"><i class="bi bi-star-fill"></i> Active Referral Partner</span>
                        <h3 class="fw-bold mb-1 brand-font text-white">Namaste, {{ $astrologer->display_name }}!</h3>
                        <p class="text-white-50 mb-0">Share your exclusive referral link with clients and devotees. Earn guaranteed commissions on every authentic remedy purchase.</p>
                    </div>
                    <div class="col-lg-5">
                        <div class="bg-white bg-opacity-10 p-3 rounded-3 border border-white border-opacity-10">
                            <small class="text-warning text-uppercase fw-bold d-block mb-1" style="font-size:0.75rem;">Your Unique Referral Link</small>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm bg-white text-dark fw-semibold" id="refLinkInput" value="{{ url('/ref/' . $astrologer->referral_code) }}" readonly>
                                <button class="btn btn-warning btn-sm fw-bold px-3" type="button" onclick="navigator.clipboard.writeText(document.getElementById('refLinkInput').value); alert('Referral link copied to clipboard!');">
                                    <i class="bi bi-clipboard"></i> Copy
                                </button>
                            </div>
                            <div class="d-flex gap-2 mt-2">
                                <a href="https://api.whatsapp.com/send?text={{ urlencode('Discover authentic Vedic gemstones & rudraksha remedies recommended by ' . $astrologer->display_name . ': ' . url('/ref/' . $astrologer->referral_code)) }}" target="_blank" class="btn btn-success btn-sm w-50">
                                    <i class="bi bi-whatsapp"></i> Share WhatsApp
                                </a>
                                <a href="{{ route('astrologer.referrals.index') }}" class="btn btn-outline-light btn-sm w-50">
                                    <i class="bi bi-qr-code"></i> Get QR Code
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="metric-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Available Balance</span>
                    <h3 class="fw-bold text-success mt-1 mb-0">₹{{ number_format($stats['available_balance'], 2) }}</h3>
                </div>
                <div class="metric-icon" style="background: rgba(34, 197, 94, 0.12); color: #16A34A;">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                <a href="{{ route('astrologer.withdrawals.index') }}" class="btn btn-sm btn-outline-success py-0 px-2 fw-semibold" style="font-size: 0.8rem;">
                    <i class="bi bi-arrow-up-right-circle"></i> Withdraw
                </a>
                <span class="text-muted small">Instant Payout</span>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="metric-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Pending Commission</span>
                    <h3 class="fw-bold text-warning mt-1 mb-0">₹{{ number_format($stats['pending_commission'], 2) }}</h3>
                </div>
                <div class="metric-icon" style="background: rgba(245, 176, 65, 0.15); color: #D4AC0D;">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-top text-muted small">
                <span>Hold period after delivery</span>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="metric-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Lifetime Earnings</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">₹{{ number_format($stats['lifetime_earnings'], 2) }}</h3>
                </div>
                <div class="metric-icon" style="background: rgba(99, 102, 241, 0.12); color: #4F46E5;">
                    <i class="bi bi-graph-up"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-top text-muted small">
                <span>Total Paid: ₹{{ number_format($stats['total_withdrawn'], 2) }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="metric-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Conversions / Clicks</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ $stats['total_orders'] }} / {{ $stats['total_clicks'] }}</h3>
                </div>
                <div class="metric-icon" style="background: rgba(236, 72, 153, 0.12); color: #DB2777;">
                    <i class="bi bi-bullseye"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-top text-muted small">
                <span>Conv. Rate: <strong class="text-dark">{{ $stats['conversion_rate'] }}%</strong></span>
            </div>
        </div>
    </div>
</div>

<!-- Chart & Recent Activity -->
<div class="row g-4 mb-4">
    <!-- Monthly Chart -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Earnings Overview (Past 6 Months)</h6>
                <span class="badge bg-light text-secondary border">INR (₹)</span>
            </div>
            <div class="card-body">
                <canvas id="earningsChart" height="230"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Links -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">Recent Referral Clicks</h6>
            </div>
            <div class="card-body p-0">
                @if($recentReferrals->isEmpty())
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-link fs-2 d-block mb-2 text-secondary"></i>
                        No clicks recorded yet. Share your link to start receiving traffic!
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentReferrals as $ref)
                                    <tr>
                                        <td>
                                            @if($ref->product)
                                                <span class="fw-semibold text-truncate d-inline-block" style="max-width: 140px;">{{ $ref->product->name }}</span>
                                            @else
                                                <span class="text-muted">General Store</span>
                                            @endif
                                        </td>
                                        <td class="text-muted">{{ $ref->created_at->diffForHumans() }}</td>
                                        <td>
                                            @if($ref->isConverted())
                                                <span class="badge bg-success-subtle text-success">Purchased</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary">Clicked</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Commissions Table -->
<div class="card border-0 shadow-sm" style="border-radius: 14px;">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">Recent Commission Earnings</h6>
        <a href="{{ route('astrologer.commissions.index') }}" class="btn btn-sm btn-outline-secondary">View All Commissions</a>
    </div>
    <div class="card-body p-0">
        @if($recentCommissions->isEmpty())
            <div class="p-4 text-center text-muted">
                <i class="bi bi-percent fs-2 d-block mb-2 text-secondary"></i>
                No commissions earned yet. Start recommending remedies to earn!
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase text-muted">
                        <tr>
                            <th>Commission ID</th>
                            <th>Order</th>
                            <th>Product</th>
                            <th>Sale Amount</th>
                            <th>Rate</th>
                            <th>Commission</th>
                            <th>Status</th>
                            <th>Available On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentCommissions as $comm)
                            <tr>
                                <td class="fw-semibold font-monospace small">#{{ $comm->commission_number }}</td>
                                <td>
                                    @if($comm->order)
                                        <span class="small font-monospace">{{ $comm->order->order_number }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $comm->product->name ?? 'Product' }}</td>
                                <td>₹{{ number_format($comm->order_amount, 2) }}</td>
                                <td>{{ $comm->commission_rate }}{{ $comm->commission_type === 'percent' ? '%' : ' INR' }}</td>
                                <td class="fw-bold text-success">+₹{{ number_format($comm->commission_amount, 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ $comm->status }} text-capitalize px-2 py-1">
                                        {{ $comm->status }}
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    {{ $comm->available_at ? $comm->available_at->format('d M, Y') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('earningsChart').getContext('2d');
        const labels = {!! json_encode(array_keys($monthlyEarnings->toArray())) !!};
        const data = {!! json_encode(array_values($monthlyEarnings->toArray())) !!};

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels.length ? labels : ['Past Months'],
                datasets: [{
                    label: 'Commission (₹)',
                    data: data.length ? data : [0],
                    backgroundColor: 'rgba(245, 176, 65, 0.85)',
                    borderColor: '#F5B041',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) { return '₹' + value; }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
