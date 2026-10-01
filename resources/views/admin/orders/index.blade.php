@extends('layouts.admin')

@section('title', 'Order Management')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 brand-font">Order Management</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active">Orders</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-warning text-dark px-3 py-2 fs-6">
            <i class="bi bi-bag-check me-1"></i> {{ number_format($statusCounts['all']) }} Total
        </span>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Status Summary Cards --}}
<div class="row g-3 mb-4">
    @php
        $cards = [
            ['label' => 'Pending',    'key' => 'pending',    'icon' => 'hourglass-split',  'color' => 'warning'],
            ['label' => 'Confirmed',  'key' => 'confirmed',  'icon' => 'check-circle',      'color' => 'info'],
            ['label' => 'Processing', 'key' => 'processing', 'icon' => 'gear',              'color' => 'primary'],
            ['label' => 'Shipped',    'key' => 'shipped',    'icon' => 'truck',             'color' => 'primary'],
            ['label' => 'Delivered',  'key' => 'delivered',  'icon' => 'bag-check',         'color' => 'success'],
            ['label' => 'Cancelled',  'key' => 'cancelled',  'icon' => 'x-circle',         'color' => 'danger'],
        ];
    @endphp
    @foreach($cards as $card)
        <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('admin.orders.index', ['status' => $card['key']]) }}"
               class="text-decoration-none d-block">
                <div class="card border-0 shadow-sm rounded-3 text-center py-3 px-2
                    {{ request('status') === $card['key'] ? 'border-' . $card['color'] . ' border border-2' : '' }}">
                    <i class="bi bi-{{ $card['icon'] }} fs-3 text-{{ $card['color'] }} mb-1"></i>
                    <div class="fw-bold fs-5 mb-0">{{ number_format($statusCounts[$card['key']]) }}</div>
                    <small class="text-muted">{{ $card['label'] }}</small>
                </div>
            </a>
        </div>
    @endforeach
</div>

{{-- Filters Card --}}
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                           placeholder="Search order #, customer name, phone…"
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(['pending','confirmed','processing','shipped','delivered','cancelled','refunded'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="payment_status" class="form-select">
                    <option value="">All Payments</option>
                    @foreach(['unpaid','paid','failed','refunded'] as $ps)
                        <option value="{{ $ps }}" {{ request('payment_status') === $ps ? 'selected' : '' }}>
                            {{ ucfirst($ps) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-warning flex-grow-1 fw-semibold">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Orders Table --}}
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        @if($orders->isEmpty())
            <div class="text-center py-5">
                <div style="font-size: 4rem; opacity: 0.1;">📦</div>
                <h6 class="fw-bold mt-2 text-muted">No orders found.</h6>
                @if(request()->anyFilled(['search','status','payment_status']))
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary mt-2 rounded-pill">
                        Clear Filters
                    </a>
                @endif
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Order #</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-semibold text-dark small font-monospace">
                                        {{ $order->order_number }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold small">{{ $order->shipping_name }}</div>
                                    <small class="text-muted">{{ $order->user?->email ?? '—' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $order->items_count }} item(s)
                                    </span>
                                </td>
                                <td class="fw-semibold">₹{{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $order->payment_status_badge['class'] }}">
                                        {{ $order->payment_status_badge['label'] }}
                                    </span>
                                    <div class="small text-muted mt-1">{{ ucfirst($order->payment_method) }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $order->status_badge['class'] }} px-2 py-1">
                                        {{ $order->status_badge['label'] }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $order->created_at->format('d M Y') }}</small><br>
                                    <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                       class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="bi bi-eye me-1"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($orders->hasPages())
                <div class="px-4 py-3 border-top">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <small class="text-muted">
                            Showing {{ $orders->firstItem() }}–{{ $orders->lastItem() }} of {{ $orders->total() }} orders
                        </small>
                        {{ $orders->links() }}
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
