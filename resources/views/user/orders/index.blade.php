@extends('layouts.app')

@section('title', 'My Orders — AstroVani')

@section('content')

<div class="container py-4 my-3">

    {{-- Page Header --}}
    <div
        class="p-4 p-md-5 mb-4 rounded-4 text-white shadow-sm"
        style="
            background:linear-gradient(135deg,#1A0B2E 0%,#2D124D 60%,#481B7F 100%);
            border-bottom:3px solid #F5B041;
        "
    >

        <div class="row align-items-center">

            <div class="col-lg-8">

                <span class="badge bg-warning text-dark mb-2 px-3 py-1 fw-bold">
                    <i class="bi bi-bag-check me-1"></i>
                    My Orders
                </span>

                <h2 class="display-6 fw-bold mb-1">
                    Order History
                </h2>

                <p class="lead text-white-50 mb-0">
                    Track and manage all your AstroVani purchases.
                </p>

            </div>

            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">

                <a
                    href="{{ route('shop.index') }}"
                    class="btn btn-astro-gold"
                >
                    <i class="bi bi-shop me-1"></i>
                    Continue Shopping
                </a>

            </div>

        </div>

    </div>


    <div class="row g-4">


        {{-- Sidebar --}}
        <div class="col-lg-3">

            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">

                <div class="list-group list-group-flush small">

                    <a
                        href="{{ route('user.dashboard') }}"
                        class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-3 text-secondary"
                    >
                        <i class="bi bi-speedometer2 fs-5 text-muted"></i>
                        <span>Dashboard</span>
                    </a>


                    <a
                        href="{{ route('user.bookings.index') }}"
                        class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-3 text-secondary"
                    >
                        <i class="bi bi-calendar-check fs-5 text-muted"></i>
                        <span>My Bookings</span>
                    </a>


                    <a
                        href="{{ route('user.orders.index') }}"
                        class="list-group-item list-group-item-action active fw-semibold d-flex align-items-center gap-2 py-3"
                    >
                        <i class="bi bi-bag-check fs-5 text-warning"></i>
                        <span>My Orders</span>
                    </a>


                    <a
                        href="{{ route('user.wishlist.index') }}"
                        class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-3 text-secondary"
                    >
                        <i class="bi bi-heart fs-5 text-muted"></i>
                        <span>Wishlist</span>
                    </a>


                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-3 text-danger border-0 bg-transparent w-100 text-start"
                        >
                            <i class="bi bi-box-arrow-right fs-5"></i>
                            <span>Sign Out</span>
                        </button>

                    </form>

                </div>

            </div>

        </div>



        {{-- Orders List --}}
        <div class="col-lg-9">


            {{-- Success Alert --}}
            @if(session('success'))

                <div
                    class="alert alert-success alert-dismissible fade show rounded-3 mb-3"
                    role="alert"
                >

                    <i class="bi bi-check-circle-fill me-2"></i>

                    {{ session('success') }}

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                    ></button>

                </div>

            @endif


            {{-- Error Alert --}}
            @if(session('error'))

                <div
                    class="alert alert-danger alert-dismissible fade show rounded-3 mb-3"
                    role="alert"
                >

                    <i class="bi bi-exclamation-triangle-fill me-2"></i>

                    {{ session('error') }}

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                    ></button>

                </div>

            @endif



            {{-- Orders --}}
            @forelse($orders as $order)

                @php

                    $shippingAmount = max(
                        0,
                        (float) $order->total - (float) $order->subtotal
                    );

                @endphp


                <div
                    class="card border-0 shadow-sm rounded-3 mb-3 overflow-hidden"
                >


                    {{-- Order Header --}}
                    <div
                        class="card-header bg-light border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2 py-3"
                    >

                        <div class="d-flex align-items-center gap-3 flex-wrap">


                            {{-- Order ID --}}
                            <div>

                                <span class="text-muted small d-block">
                                    Order #
                                </span>

                                <span class="fw-bold text-dark">
                                    {{ $order->id }}
                                </span>

                            </div>


                            <div class="vr"></div>


                            {{-- Order Date --}}
                            <div>

                                <span class="text-muted small d-block">
                                    Placed On
                                </span>

                                <span class="fw-semibold small">
                                    {{ $order->created_at->format('d M Y, h:i A') }}
                                </span>

                            </div>


                            <div class="vr d-none d-sm-block"></div>


                            {{-- Total --}}
                            <div class="d-none d-sm-block">

                                <span class="text-muted small d-block">
                                    Total
                                </span>

                                <span class="fw-bold text-dark">
                                    ₹{{ number_format($order->total, 2) }}
                                </span>

                            </div>

                        </div>



                        {{-- Status --}}
                        <div>

                            <span
                                class="badge bg-{{ $order->status_badge['class'] }} px-3 py-2"
                            >
                                {{ $order->status_badge['label'] }}
                            </span>

                        </div>

                    </div>



                    {{-- Order Items Preview --}}
                    <div class="card-body py-3">

                        <div class="d-flex align-items-center gap-3 flex-wrap">


                            {{-- Item Count --}}
                            <div class="text-muted small me-2">

                                <i class="bi bi-box-seam me-1"></i>

                                {{ $order->items->count() }}
                                item(s)

                            </div>



                            {{-- First 3 Items --}}
                            @foreach($order->items->take(3) as $item)

                                @php

                                    /*
                                     * OrderItem currently stores product_name,
                                     * but does not have a product relationship.
                                     *
                                     * Find the matching product only for the image.
                                     */
                                    $orderProduct = \App\Models\Product::where(
                                        'name',
                                        $item->product_name
                                    )->first();

                                    $productImage = $orderProduct?->image;

                                @endphp


                                {{-- Product Preview --}}
                                <div
                                    class="order-product-preview d-flex align-items-center gap-2"
                                >

                                    {{-- Product Image --}}
                                    @if($productImage)

                                        <img
                                            src="{{ asset('storage/' . ltrim($productImage, '/')) }}"
                                            alt="{{ $item->product_name }}"
                                            class="order-product-image"
                                            loading="lazy"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                        >

                                        {{-- Image fallback --}}
                                        <div
                                            class="order-product-placeholder"
                                            style="display:none;"
                                        >
                                            <i class="bi bi-box-seam"></i>
                                        </div>

                                    @else

                                        <div class="order-product-placeholder">
                                            <i class="bi bi-box-seam"></i>
                                        </div>

                                    @endif


                                    {{-- Product Name --}}
                                    <div class="order-product-name">

                                        <span>
                                            {{ $item->product_name }}
                                        </span>

                                        <strong>
                                            × {{ $item->quantity }}
                                        </strong>

                                    </div>

                                </div>

                            @endforeach



                            {{-- More Items --}}
                            @if($order->items->count() > 3)

                                <span class="text-muted small">

                                    +{{ $order->items->count() - 3 }}
                                    more

                                </span>

                            @endif

                        </div>



                        {{-- Shipping --}}
                        <div class="mt-3 pt-2 border-top">

                            <div class="d-flex flex-wrap gap-3 small text-muted">

                                <span>

                                    <i class="bi bi-box-seam me-1"></i>

                                    Subtotal:

                                    <strong class="text-dark">
                                        ₹{{ number_format($order->subtotal, 2) }}
                                    </strong>

                                </span>


                                <span>

                                    <i class="bi bi-truck me-1"></i>

                                    Shipping:

                                    @if($shippingAmount > 0)

                                        <strong class="text-dark">
                                            ₹{{ number_format($shippingAmount, 2) }}
                                        </strong>

                                    @else

                                        <strong class="text-success">
                                            Free
                                        </strong>

                                    @endif

                                </span>

                            </div>

                        </div>

                    </div>



                    {{-- Order Footer --}}
                    <div
                        class="card-footer bg-white border-top d-flex align-items-center justify-content-between gap-2 flex-wrap"
                    >

                        <div class="d-flex gap-2">


                            {{-- View Details --}}
                            <a
                                href="{{ route('user.orders.show', $order) }}"
                                class="btn btn-sm btn-outline-primary rounded-pill px-3"
                            >

                                <i class="bi bi-eye me-1"></i>

                                View Details

                            </a>



                            {{-- Cancel --}}
                            @if($order->isCancellable())

                                <form
                                    method="POST"
                                    action="{{ route('user.orders.cancel', $order) }}"
                                    onsubmit="return confirm('Are you sure you want to cancel order #{{ $order->id }}?')"
                                >

                                    @csrf

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                    >

                                        <i class="bi bi-x-circle me-1"></i>

                                        Cancel

                                    </button>

                                </form>

                            @endif

                        </div>



                        {{-- Mobile Total --}}
                        <span class="fw-bold text-dark d-sm-none">

                            ₹{{ number_format($order->total, 2) }}

                        </span>


                        {{-- Status Info --}}
                        <span class="text-muted small d-none d-sm-block">

                            <i class="bi bi-info-circle me-1"></i>

                            Order #{{ $order->id }}

                        </span>

                    </div>

                </div>


            @empty


                {{-- No Orders --}}
                <div class="card border-0 shadow-sm rounded-3">

                    <div class="card-body text-center py-5">

                        <div
                            class="mb-4"
                            style="font-size:5rem;opacity:0.15;"
                        >
                            🛍️
                        </div>

                        <h5 class="fw-bold text-dark">
                            No Orders Yet
                        </h5>

                        <p class="text-muted mb-4">
                            You haven't placed any orders.
                            Explore our shop and find something magical!
                        </p>

                        <a
                            href="{{ route('shop.index') }}"
                            class="btn btn-astro-gold rounded-pill px-5"
                        >

                            <i class="bi bi-shop me-2"></i>

                            Visit Shop

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



{{-- ============================================================
     ORDER PRODUCT IMAGE STYLES
============================================================ --}}
@push('styles')

<style>

    .order-product-preview {
        min-height: 54px;
        padding: 3px 8px 3px 4px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        transition: all 0.2s ease;
    }

    .order-product-preview:hover {
        border-color: #d1d5db;
        box-shadow: 0 3px 10px rgba(26, 11, 46, 0.08);
        transform: translateY(-1px);
    }


    .order-product-image,
    .order-product-placeholder {
        width: 50px;
        height: 50px;
        min-width: 50px;
        border-radius: 9px;
    }


    .order-product-image {
        display: block;
        object-fit: cover;
        background: #f8f9fa;
        border: 1px solid #eeeeee;
    }


    .order-product-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(
            135deg,
            #f5f3f7,
            #eee9f3
        );
        color: #6b21a8;
        border: 1px solid #e5dff0;
        font-size: 1.2rem;
    }


    .order-product-name {
        display: flex;
        align-items: center;
        gap: 4px;
        max-width: 220px;
        font-size: 0.78rem;
        line-height: 1.25;
        color: #374151;
    }


    .order-product-name span {
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 175px;
    }


    .order-product-name strong {
        color: #111827;
        white-space: nowrap;
        font-size: 0.76rem;
    }


    @media (max-width: 575.98px) {

        .order-product-preview {
            min-height: 48px;
            padding: 2px 6px 2px 3px;
            border-radius: 10px;
        }

        .order-product-image,
        .order-product-placeholder {
            width: 44px;
            height: 44px;
            min-width: 44px;
            border-radius: 8px;
        }

        .order-product-name {
            max-width: 175px;
            font-size: 0.74rem;
        }

        .order-product-name span {
            max-width: 130px;
        }

    }

</style>

@endpush

@endsection