@extends('layouts.admin')

@section('title', 'Referral Tracking')
@section('page_title', 'Referral Clicks & Conversion Tracking')

@section('content')
<!-- Metrics -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Total Clicks</span>
                <h3 class="fw-bold my-1 text-dark">{{ number_format($metrics['total_clicks']) }}</h3>
                <small class="text-muted">All tracked referral visits</small>
            </div>
            <div class="stat-icon icon-blue"><i class="bi bi-cursor-fill"></i></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Converted Clicks</span>
                <h3 class="fw-bold my-1 text-success">{{ number_format($metrics['converted_clicks']) }}</h3>
                <small class="text-muted">Resulted in orders</small>
            </div>
            <div class="stat-icon icon-green"><i class="bi bi-bag-check-fill"></i></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Expired Clicks</span>
                <h3 class="fw-bold my-1 text-secondary">{{ number_format($metrics['expired_clicks']) }}</h3>
                <small class="text-muted">Outside 30-day cookie</small>
            </div>
            <div class="stat-icon icon-purple"><i class="bi bi-clock-history"></i></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Conversion Rate</span>
                <h3 class="fw-bold my-1 text-warning">{{ $metrics['conversion_rate'] }}%</h3>
                <small class="text-muted">Click-to-purchase ratio</small>
            </div>
            <div class="stat-icon icon-gold"><i class="bi bi-graph-up-arrow"></i></div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h5 class="fw-bold mb-0">Referral Traffic Log</h5>
            <p class="text-muted small mb-0">Real-time visitor referral attribution, cookie tracking, and conversion status</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.referrals.partners') }}" class="btn btn-sm btn-outline-warning text-dark fw-semibold">
                <i class="bi bi-people-fill me-1"></i> Manage Partners
            </a>
            <a href="{{ route('admin.settings.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-gear-fill me-1"></i> Cookie & Attribution Settings
            </a>
        </div>
    </div>

    <div class="card-body p-4 pt-2">
        <!-- Filter Bar -->
        <form method="GET" action="{{ route('admin.referrals.index') }}" class="row g-2 align-items-center mb-4 bg-light p-3 rounded-3 border">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Code, IP address, or Astrologer" value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending (In Window)</option>
                    <option value="converted" {{ request('status') === 'converted' ? 'selected' : '' }}>Converted (Ordered)</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="astrologer_id" class="form-select form-select-sm">
                    <option value="">All Astrologers</option>
                    @foreach($astrologers as $astro)
                        <option value="{{ $astro->id }}" {{ request('astrologer_id') == $astro->id ? 'selected' : '' }}>
                            {{ $astro->display_name }} ({{ $astro->referral_code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-warning btn-sm fw-bold w-50">Filter</button>
                <a href="{{ route('admin.referrals.index') }}" class="btn btn-outline-secondary btn-sm w-50">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th class="text-uppercase">Astrologer</th>
                        <th class="text-uppercase">Referral Code</th>
                        <th class="text-uppercase">Target Item</th>
                        <th class="text-uppercase">Visitor Info</th>
                        <th class="text-uppercase">Status</th>
                        <th class="text-uppercase">Order Link</th>
                        <th class="text-uppercase">Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($referrals as $ref)
                    <tr>
                        <td>
                            @if($ref->astrologer)
                                <div>
                                    <strong class="d-block text-dark">{{ $ref->astrologer->display_name }}</strong>
                                    <span class="text-muted" style="font-size:0.75rem;">{{ $ref->astrologer->email }}</span>
                                </div>
                            @else
                                <span class="text-muted">Unknown</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-warning border font-monospace px-2 py-1">
                                {{ $ref->referral_code }}
                            </span>
                        </td>
                        <td>
                            @if($ref->product)
                                <span class="text-primary fw-medium">{{ Str::limit($ref->product->name, 25) }}</span>
                            @else
                                <span class="text-muted fst-italic">Store / Direct</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-muted">
                                <div><i class="bi bi-geo-alt me-1"></i>{{ $ref->ip_address ?? '—' }}</div>
                                <div class="text-truncate" style="max-width: 180px; font-size: 0.72rem;" title="{{ $ref->user_agent }}">
                                    {{ Str::limit($ref->user_agent ?? 'Unknown browser', 30) }}
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($ref->status === 'converted')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                                    <i class="bi bi-check-circle-fill me-1"></i> Converted
                                </span>
                            @elseif($ref->status === 'expired')
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1">
                                    <i class="bi bi-x-circle me-1"></i> Expired
                                </span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1">
                                    <i class="bi bi-clock-history me-1"></i> Active
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($ref->order)
                                <a href="{{ route('admin.orders.show', $ref->order_id) }}" class="fw-bold text-decoration-none">
                                    #{{ $ref->order->order_number ?? $ref->order_id }}
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-muted">
                                <div>{{ $ref->created_at->format('d M Y') }}</div>
                                <small style="font-size: 0.72rem;">{{ $ref->created_at->format('h:i A') }}</small>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-link-45deg fs-1 d-block mb-2 opacity-50"></i>
                            <h6 class="fw-semibold">No Referral Visits Recorded</h6>
                            <p class="small text-muted mb-0">Referral clicks will automatically appear as astrologers share links</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($referrals->hasPages())
        <div class="mt-4 d-flex justify-content-end">
            {{ $referrals->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
