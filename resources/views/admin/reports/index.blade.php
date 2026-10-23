@extends('layouts.admin')

@section('title', 'Astrologer Bookings & Orders Reports')
@section('page_title', 'Astrologer Activity & Financial Reports')

@section('content')
<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Filter by Astrologer</label>
                <select name="astrologer_id" class="form-select form-select-sm">
                    <option value="">All Astrologers</option>
                    @foreach($astrologers as $astro)
                        <option value="{{ $astro->id }}" {{ $selectedAstrologerId == $astro->id ? 'selected' : '' }}>
                            {{ $astro->display_name }} (AST-{{ $astro->id }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">From Date</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">To Date</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed / Delivered</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending / Open</option>
                    <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end gap-2 pt-3 pt-md-0">
                <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
                    <i class="bi bi-funnel-fill me-1"></i> Filter Reports
                </button>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary">
                    Reset
                </a>
                <button type="button" class="btn btn-sm btn-light border" onclick="window.print()">
                    <i class="bi bi-printer"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Financial & Operations Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-2">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-primary border-4">
            <span class="text-muted small text-uppercase fw-semibold">Bookings Taken</span>
            <h4 class="fw-bold my-1 text-primary">{{ number_format($summary['total_bookings']) }}</h4>
            <small class="text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>{{ number_format($summary['completed_bookings']) }} Done</small>
        </div>
    </div>

    <div class="col-sm-6 col-xl-2">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-success border-4">
            <span class="text-muted small text-uppercase fw-semibold">Calls Attended</span>
            <h4 class="fw-bold my-1 text-success">{{ number_format($summary['calls_attended']) }}</h4>
            <small class="text-muted"><i class="bi bi-telephone-inbound-fill text-success me-1"></i>Completed</small>
        </div>
    </div>

    <div class="col-sm-6 col-xl-2">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-danger border-4">
            <span class="text-muted small text-uppercase fw-semibold">Calls Ignored</span>
            <h4 class="fw-bold my-1 text-danger">{{ number_format($summary['calls_ignored']) }}</h4>
            <small class="text-danger fw-semibold"><i class="bi bi-telephone-x-fill me-1"></i>Rejected / Missed</small>
        </div>
    </div>

    <div class="col-sm-6 col-xl-2">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-purple border-4" style="border-left-color: #6f42c1 !important;">
            <span class="text-muted small text-uppercase fw-semibold">Store Orders Handled</span>
            <h4 class="fw-bold my-1 text-dark" style="color: #6f42c1 !important;">{{ number_format($summary['total_orders']) }}</h4>
            <small class="text-success fw-semibold"><i class="bi bi-truck me-1"></i>{{ number_format($summary['delivered_orders']) }} Delivered</small>
        </div>
    </div>

    <div class="col-sm-6 col-xl-2">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-warning border-4">
            <span class="text-muted small text-uppercase fw-semibold">Bookings Revenue</span>
            <h4 class="fw-bold my-1 text-warning">₹{{ number_format($summary['booking_revenue'], 0) }}</h4>
            <small class="text-muted">Gross consultation</small>
        </div>
    </div>

    <div class="col-sm-6 col-xl-2">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-info border-4" style="border-left-color: #0dcaf0 !important;">
            <span class="text-muted small text-uppercase fw-semibold">Store Orders Value</span>
            <h4 class="fw-bold my-1 text-dark">₹{{ number_format($summary['orders_value'], 0) }}</h4>
            <small class="text-success fw-semibold"><i class="bi bi-percent me-1"></i>₹{{ number_format($summary['commissions_paid'], 0) }} Earned</small>
        </div>
    </div>
</div>

<!-- Astrologers Performance Matrix -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="mb-0 fw-bold"><i class="bi bi-person-lines-fill text-warning me-2"></i> Astrologer Cumulative Performance Matrix (Bookings, Calls & Orders)</h6>
        <span class="badge bg-light text-dark border">{{ count($astrologersMatrix) }} Records</span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th class="ps-4">Astrologer</th>
                        <th class="text-center">Bookings Taken</th>
                        <th class="text-center">Calls Attended</th>
                        <th class="text-center">Calls Ignored</th>
                        <th class="text-end">Bookings Revenue</th>
                        <th class="text-center">Store Orders</th>
                        <th class="text-center">Orders Delivered</th>
                        <th class="text-end">Orders Sales Value</th>
                        <th class="text-end">Commissions</th>
                        <th class="text-end pe-4">Wallet Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($astrologersMatrix as $astro)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $astro->display_name }}</div>
                                <div class="small text-muted font-monospace">AST-{{ $astro->id }} &bull; {{ $astro->referral_code ?? 'NO-CODE' }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary fw-bold">{{ $astro->total_bookings_count }}</span>
                                <div class="text-muted" style="font-size: 0.72rem;">{{ $astro->completed_bookings_count }} done</div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success-subtle text-success fw-bold">
                                    <i class="bi bi-telephone-inbound-fill me-1"></i>{{ $astro->attended_calls_count }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $astro->ignored_calls_count > 0 ? 'bg-danger-subtle text-danger' : 'bg-light text-muted border' }} fw-bold">
                                    <i class="bi bi-telephone-x-fill me-1"></i>{{ $astro->ignored_calls_count }}
                                </span>
                            </td>
                            <td class="text-end fw-bold">₹{{ number_format($astro->total_booking_revenue ?? 0, 2) }}</td>
                            <td class="text-center">
                                <span class="badge bg-info-subtle text-dark fw-bold">{{ $astro->total_orders_count }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success-subtle text-success fw-semibold">{{ $astro->completed_orders_count }}</span>
                            </td>
                            <td class="text-end fw-bold">₹{{ number_format($astro->total_orders_revenue, 2) }}</td>
                            <td class="text-end fw-bold text-success">₹{{ number_format($astro->total_commissions_earned, 2) }}</td>
                            <td class="text-end pe-4 font-monospace text-primary fw-semibold">₹{{ number_format($astro->wallet?->available_balance ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">No astrologer performance data found for selected filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Detailed Tabbed Lists (Bookings and Orders) -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white border-bottom p-0">
        <ul class="nav nav-tabs border-0 px-3 pt-2" id="reportTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold py-3 px-4" id="bookings-tab" data-bs-toggle="tab" data-bs-target="#bookingsPane" type="button" role="tab">
                    <i class="bi bi-calendar2-check-fill text-primary me-2"></i> Consultation Bookings ({{ $bookingsList->total() }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold py-3 px-4" id="orders-tab" data-bs-toggle="tab" data-bs-target="#ordersPane" type="button" role="tab">
                    <i class="bi bi-bag-check-fill text-success me-2"></i> Astrologer Store Orders ({{ $ordersList->total() }})
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">
        <div class="tab-content" id="reportTabContent">
            <!-- Bookings Pane -->
            <div class="tab-pane fade show active" id="bookingsPane" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th class="ps-4">Booking Number</th>
                                <th>Astrologer</th>
                                <th>Customer</th>
                                <th>Schedule Date & Time</th>
                                <th>Duration</th>
                                <th>Fee Amount</th>
                                <th>Payment</th>
                                <th class="pe-4 text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookingsList as $b)
                                <tr>
                                    <td class="ps-4 fw-bold">
                                        <a href="{{ route('admin.bookings.show', $b) }}" class="text-decoration-none">
                                            {{ $b->booking_number ?? '#' . $b->id }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $b->astrologer?->display_name ?? '—' }}</div>
                                        <small class="text-muted font-monospace">AST-{{ $b->astrologer_id }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $b->user?->full_name ?? 'Customer' }}</div>
                                        <small class="text-muted">{{ $b->user?->email ?? '' }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $b->booking_date?->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $b->start_time ?? '' }}</small>
                                    </td>
                                    <td>{{ $b->duration_minutes ?? 30 }} mins</td>
                                    <td class="fw-bold">₹{{ number_format($b->amount, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $b->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ ucfirst($b->payment_status ?? 'pending') }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        @if($b->status === 'completed')
                                            <span class="badge bg-success-subtle text-success">Completed</span>
                                        @elseif($b->status === 'confirmed')
                                            <span class="badge bg-primary-subtle text-primary">Confirmed</span>
                                        @elseif($b->status === 'cancelled')
                                            <span class="badge bg-danger-subtle text-danger">Cancelled</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">No booking records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($bookingsList->hasPages())
                    <div class="p-3 border-top">
                        {{ $bookingsList->links() }}
                    </div>
                @endif
            </div>

            <!-- Orders Pane -->
            <div class="tab-pane fade" id="ordersPane" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th class="ps-4">Order Number</th>
                                <th>Referring Astrologer</th>
                                <th>Customer</th>
                                <th>Order Date</th>
                                <th>Order Total</th>
                                <th>Payment</th>
                                <th class="pe-4 text-end">Order Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ordersList as $o)
                                <tr>
                                    <td class="ps-4 fw-bold">
                                        <a href="{{ route('admin.orders.show', $o) }}" class="text-decoration-none">
                                            {{ $o->order_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $o->referrerAstrologer?->display_name ?? 'Astrologer' }}</div>
                                        <small class="text-muted font-monospace">AST-{{ $o->referrer_astrologer_id }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $o->user?->full_name ?? ($o->shipping_name ?? 'Customer') }}</div>
                                        <small class="text-muted">{{ $o->user?->phone ?? $o->shipping_phone }}</small>
                                    </td>
                                    <td>{{ $o->created_at->format('M d, Y h:i A') }}</td>
                                    <td class="fw-bold">₹{{ number_format($o->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $o->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ ucfirst($o->payment_status) }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        @if($o->status === 'delivered')
                                            <span class="badge bg-success-subtle text-success">Delivered</span>
                                        @elseif($o->status === 'shipped')
                                            <span class="badge bg-info-subtle text-info">Shipped</span>
                                        @elseif($o->status === 'cancelled')
                                            <span class="badge bg-danger-subtle text-danger">Cancelled</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning">{{ ucfirst($o->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No orders found taken by astrologers.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($ordersList->hasPages())
                    <div class="p-3 border-top">
                        {{ $ordersList->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
