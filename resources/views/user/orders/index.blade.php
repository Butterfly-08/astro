@extends('layouts.app')

@section('title', 'My Orders — AstroVani')

@section('content')
<div class="container py-4 my-3">

    {{-- Page Header --}}
    <div class="p-4 p-md-5 mb-4 rounded-4 text-white shadow-sm"
         style="background: linear-gradient(135deg, #1A0B2E 0%, #2D124D 60%, #481B7F 100%); border-bottom: 3px solid #F5B041;">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark mb-2 px-3 py-1 fw-bold">
                    <i class="bi bi-bag-check me-1"></i> My Orders
                </span>
                <h2 class="display-6 fw-bold mb-1">Order History</h2>
                <p class="lead text-white-50 mb-0">Track and manage all your AstroVani purchases.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="{{ route('shop.index') }}" class="btn btn-astro-gold">
                    <i class="bi bi-shop me-1"></i> Continue Shopping
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Sidebar --}}
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="list-group list-group-flush small">
                    <a href="{{ route('user.dashboard') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-3 text-secondary">
                        <i class="bi bi-speedometer2 fs-5 text-muted"></i><span>Dashboard</span>
                    </a>
                    <a href="{{ route('user.bookings.index') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-3 text-secondary">
                        <i class="bi bi-calendar-check fs-5 text-muted"></i><span>My Bookings</span>
                    </a>
                    <a href="{{ route('user.orders.index') }}" class="list-group-item list-group-item-action active fw-semibold d-flex align-items-center gap-2 py-3">
                        <i class="bi bi-bag-check fs-5 text-warning"></i><span>My Orders</span>
                    </a>
                    <a href="{{ route('user.wishlist.index') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-3 text-secondary">
                        <i class="bi bi-heart fs-5 text-muted"></i><span>Wishlist</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-3 text-danger border-0 bg-transparent w-100 text-start">
                            <i class="bi bi-box-arrow-right fs-5"></i><span>Sign Out</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Orders List --}}
        <div class="col-lg-9">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @forelse($orders as $order)
                <div class="card border-0 shadow-sm rounded-3 mb-3 overflow-hidden">
                    {{-- Order Header --}}
                    <div class="card-header bg-light border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                        <div class="d-flex align-items-center gap-3">
                            <div>
                                <span class="text-muted small d-block">Order #</span>
                                <span class="fw-bold text-dark">{{ $order->order_number }}</span>
                            </div>
                            <div class="vr"></div>
                            <div>
                                <span class="text-muted small d-block">Placed On</span>
                                <span class="fw-semibold small">{{ $order->created_at->format('d M Y, h:i A') }}</span>
                            </div>
                            <div class="vr d-none d-sm-block"></div>
                            <div class="d-none d-sm-block">
                                <span class="text-muted small d-block">Total</span>
                                <span class="fw-bold text-dark">₹{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-{{ $order->status_badge['class'] }} px-3 py-2 fs-7">
                                {{ $order->status_badge['label'] }}
                            </span>
                            <span class="badge bg-{{ $order->payment_status_badge['class'] }} px-3 py-2 fs-7">
                                {{ $order->payment_status_badge['label'] }}
                            </span>
                        </div>
                    </div>

                    {{-- Order Items Preview --}}
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            {{-- Item count summary --}}
                            <div class="text-muted small me-2">
                                <i class="bi bi-box-seam me-1"></i>
                                {{ $order->items->count() }} item(s)
                            </div>

                            {{-- First 3 items preview --}}
                            @foreach($order->items->take(3) as $item)
                                <span class="badge bg-light text-dark border py-2 px-2 rounded-pill small">
                                    {{ $item->product_name }} × {{ $item->quantity }}
                                </span>
                            @endforeach
                            @if($order->items->count() > 3)
                                <span class="text-muted small">+{{ $order->items->count() - 3 }} more</span>
                            @endif

                            {{-- Shipping info --}}
                            @if($order->tracking_number)
                                <span class="ms-auto text-muted small">
                                    <i class="bi bi-truck me-1"></i>
                                    {{ $order->carrier_name ?? 'Carrier' }}: <strong>{{ $order->tracking_number }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Order Footer --}}
                    <div class="card-footer bg-white border-top d-flex align-items-center justify-content-between gap-2">
                        <div class="d-flex gap-2">
                            <a href="{{ route('user.orders.show', $order) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="bi bi-eye me-1"></i> View Details
                            </a>
                            @if($order->isCancellable())
                                <form method="POST" action="{{ route('user.orders.cancel', $order) }}"
                                      onsubmit="return confirm('Are you sure you want to cancel order #{{ $order->order_number }}?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                        <i class="bi bi-x-circle me-1"></i> Cancel
                                    </button>
                                </form>
                            @endif
                        </div>
                        <span class="fw-bold text-dark d-sm-none">₹{{ number_format($order->total_amount, 2) }}</span>
                        <span class="text-muted small d-none d-sm-block">
                            Payment: <strong>{{ ucfirst($order->payment_method) }}</strong>
                        </span>
                    </div>
                </div>
            @empty
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body text-center py-5">
                        <div class="mb-4" style="font-size: 5rem; opacity: 0.15;">🛍️</div>
                        <h5 class="fw-bold text-dark">No Orders Yet</h5>
                        <p class="text-muted mb-4">You haven't placed any orders. Explore our shop and find something magical!</p>
                        <a href="{{ route('shop.index') }}" class="btn btn-astro-gold rounded-pill px-5">
                            <i class="bi bi-shop me-2"></i> Visit Shop
                        </a>
                    </div>
                </div>
            @endforelse

            {{-- Pagination --}}
            @if($orders->hasPages())
                <div class="mt-3 d-flex justify-content-center">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
