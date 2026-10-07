@extends('astrologer.layouts.app')

@section('title', 'Commissions History')
@section('page_title', 'Referral Commission History')

@section('content')
<!-- Metrics Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="metric-card">
            <span class="text-muted small fw-semibold text-uppercase">Total Earned</span>
            <h4 class="fw-bold mt-1 mb-0 text-dark">₹{{ number_format($metrics['total_earned'], 2) }}</h4>
            <small class="text-muted">All-time commissions</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="metric-card">
            <span class="text-muted small fw-semibold text-uppercase">Pending Hold</span>
            <h4 class="fw-bold mt-1 mb-0 text-warning">₹{{ number_format($metrics['pending_amount'], 2) }}</h4>
            <small class="text-muted">Awaiting delivery hold period</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="metric-card">
            <span class="text-muted small fw-semibold text-uppercase">Available Now</span>
            <h4 class="fw-bold mt-1 mb-0 text-success">₹{{ number_format($metrics['available_amount'], 2) }}</h4>
            <small class="text-muted">Ready for withdrawal</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="metric-card">
            <span class="text-muted small fw-semibold text-uppercase">Paid Out</span>
            <h4 class="fw-bold mt-1 mb-0 text-primary">₹{{ number_format($metrics['paid_amount'], 2) }}</h4>
            <small class="text-muted">Disbursed to bank / UPI</small>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('astrologer.commissions.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search order / product..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="reversed" {{ request('status') === 'reversed' ? 'selected' : '' }}>Reversed</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" title="From Date">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" title="To Date">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-dark flex-grow-1">Filter</button>
                <a href="{{ route('astrologer.commissions.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Commissions Table -->
<div class="card border-0 shadow-sm" style="border-radius: 14px;">
    <div class="card-body p-0">
        @if($commissions->isEmpty())
            <div class="p-5 text-center text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                No commissions found matching your criteria.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase text-muted">
                        <tr>
                            <th>Commission ID</th>
                            <th>Order Details</th>
                            <th>Product</th>
                            <th>Item Price</th>
                            <th>Rate</th>
                            <th>Commission Earned</th>
                            <th>Status</th>
                            <th>Available On</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($commissions as $comm)
                            <tr>
                                <td class="fw-semibold font-monospace small">#{{ $comm->commission_number }}</td>
                                <td>
                                    @if($comm->order)
                                        <div class="font-monospace small fw-bold">{{ $comm->order->order_number }}</div>
                                        <div class="text-muted" style="font-size:0.75rem;">{{ $comm->order->created_at->format('d M Y, h:i A') }}</div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold text-truncate" style="max-width: 180px;">{{ $comm->product->name ?? 'Product' }}</div>
                                </td>
                                <td>₹{{ number_format($comm->order_amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $comm->commission_rate }}{{ $comm->commission_type === 'percent' ? '%' : ' INR' }}
                                    </span>
                                </td>
                                <td class="fw-bold text-success fs-6">
                                    +₹{{ number_format($comm->commission_amount, 2) }}
                                </td>
                                <td>
                                    <span class="badge badge-{{ $comm->status }} text-capitalize px-2 py-1">
                                        {{ $comm->status }}
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    {{ $comm->available_at ? $comm->available_at->format('d M, Y') : '-' }}
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('astrologer.commissions.show', $comm) }}" class="btn btn-sm btn-outline-secondary py-1 px-2" title="View Details">
                                        <i class="bi bi-eye"></i> Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">
                {{ $commissions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
