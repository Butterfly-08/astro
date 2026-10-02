@extends('layouts.app')

@section('title', 'Order #' . $order->id . ' — AstroVani')

@section('content')
<div class="container py-4 my-3">

    {{-- Back Button --}}
    <a href="{{ route('user.orders.index') }}"
       class="btn btn-sm btn-outline-secondary rounded-pill mb-4">
        <i class="bi bi-arrow-left me-1"></i>
        Back to My Orders
    </a>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>
        </div>
    @endif


    {{-- Order Header --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">

        <div
            class="p-4"
            style="
                background:linear-gradient(135deg,#1A0B2E 0%,#2D124D 100%);
                border-bottom:3px solid #F5B041;
            "
        >
            <div class="row align-items-center">

                <div class="col-md-6 text-white">

                    <span class="badge bg-warning text-dark mb-2 px-3 py-1 fw-bold">
                        <i class="bi bi-bag-check me-1"></i>
                        Order Details
                    </span>

                    <h4 class="fw-bold mb-0">
                        #{{ $order->id }}
                    </h4>

                    <small class="text-white-50">
                        Placed on
                        {{ $order->created_at->format('d M Y, h:i A') }}
                    </small>

                </div>


                <div class="col-md-6 text-md-end mt-3 mt-md-0">

                    <div class="d-flex gap-2 justify-content-md-end">

                        <span
                            class="badge bg-{{ $order->status_badge['class'] }} px-3 py-2 fs-6"
                        >
                            {{ $order->status_badge['label'] }}
                        </span>

                    </div>


                    @if($order->isCancellable())

                        <form
                            method="POST"
                            action="{{ route('user.orders.cancel', $order) }}"
                            class="mt-2"
                            onsubmit="return confirm('Cancel order #{{ $order->id }}?')"
                        >

                            @csrf

                            <button
                                type="submit"
                                class="btn btn-sm btn-outline-danger rounded-pill px-3"
                            >
                                <i class="bi bi-x-circle me-1"></i>
                                Cancel Order
                            </button>

                        </form>

                    @endif

                </div>

            </div>
        </div>


        {{-- Order Progress --}}
        <div class="px-4 py-3 bg-light border-bottom">

            @php

                $steps = [
                    'pending',
                    'confirmed',
                    'processing',
                    'shipped',
                    'delivered'
                ];

                $current = array_search($order->status, $steps);

                $cancelled = $order->status === 'cancelled';

            @endphp


            @if($cancelled)

                <div class="text-center text-danger fw-semibold py-2">

                    <i class="bi bi-x-circle-fill me-2"></i>

                    This order was cancelled.

                </div>

            @else

                <div
                    class="d-flex align-items-center justify-content-between position-relative"
                    id="order-progress"
                >

                    <div
                        class="progress position-absolute w-100"
                        style="
                            height:3px;
                            top:18px;
                            z-index:0;
                            left:0;
                            right:0;
                        "
                    >

                        <div
                            class="progress-bar bg-warning"
                            style="
                                width:
                                {{ $current !== false
                                    ? ($current / (count($steps) - 1)) * 100
                                    : 0
                                }}%;
                            "
                        ></div>

                    </div>


                    @foreach($steps as $i => $step)

                        @php
                            $done = (
                                $current !== false &&
                                $i <= $current
                            );
                        @endphp

                        <div
                            class="text-center position-relative"
                            style="z-index:1;flex:1;"
                        >

                            <div
                                class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                                style="
                                    width:36px;
                                    height:36px;
                                    font-size:0.8rem;
                                    background:
                                        {{ $done ? '#F5B041' : '#fff' }};
                                    border:
                                        2px solid
                                        {{ $done ? '#F5B041' : '#dee2e6' }};
                                    color:
                                        {{ $done ? '#1A0B2E' : '#adb5bd' }};
                                "
                            >

                                @if($done && $i < $current)

                                    <i class="bi bi-check-lg"></i>

                                @else

                                    {{ $i + 1 }}

                                @endif

                            </div>


                            <div
                                class="small mt-1
                                    {{ $done
                                        ? 'fw-semibold text-dark'
                                        : 'text-muted'
                                    }}"
                            >
                                {{ ucfirst($step) }}
                            </div>

                        </div>

                    @endforeach

                </div>

            @endif

        </div>

    </div>



    <div class="row g-4">


        {{-- LEFT COLUMN --}}
        <div class="col-lg-8">


            {{-- Ordered Items --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">

                <div class="card-header bg-white border-bottom fw-semibold py-3">

                    <i class="bi bi-box-seam me-2 text-warning"></i>

                    Ordered Items

                </div>


                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">

                            <thead class="table-light">

                                <tr>

                                    <th class="ps-3">
                                        Product
                                    </th>

                                    <th class="text-center">
                                        Qty
                                    </th>

                                    <th class="text-end">
                                        Unit Price
                                    </th>

                                    <th class="text-end pe-3">
                                        Subtotal
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse($order->items as $item)

                                    <tr>

                                        <td class="ps-3">

                                            <div class="d-flex align-items-center gap-3">


                                                {{-- Product image is not stored in order_items,
                                                     so use a simple placeholder --}}
                                                <div
                                                    class="rounded-2 bg-light text-muted d-flex align-items-center justify-content-center flex-shrink-0"
                                                    style="
                                                        width:52px;
                                                        height:52px;
                                                        font-size:1.4rem;
                                                    "
                                                >
                                                    <i class="bi bi-gem"></i>
                                                </div>


                                                <div>

                                                    <div class="fw-semibold text-dark">

                                                        {{ $item->product_name }}

                                                    </div>


                                                    @if($item->variant_name)

                                                        <small class="text-muted">

                                                            {{ $item->variant_name }}

                                                        </small>

                                                    @endif

                                                </div>

                                            </div>

                                        </td>


                                        <td class="text-center">

                                            {{ $item->quantity }}

                                        </td>


                                        <td class="text-end">

                                            ₹{{ number_format($item->price, 2) }}

                                        </td>


                                        <td class="text-end pe-3 fw-semibold">

                                            ₹{{ number_format($item->subtotal, 2) }}

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td
                                            colspan="4"
                                            class="text-center text-muted py-4"
                                        >
                                            No items found for this order.
                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>



            {{-- Customer Information --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">

                <div class="card-header bg-white border-bottom fw-semibold py-3">

                    <i class="bi bi-person-circle me-2 text-warning"></i>

                    Customer Information

                </div>


                <div class="card-body">

                    <div class="row g-3">

                        <div class="col-md-6">

                            <small class="text-muted d-block mb-1">
                                Customer Name
                            </small>

                            <div class="fw-semibold">
                                {{ $order->customer_name }}
                            </div>

                        </div>


                        <div class="col-md-6">

                            <small class="text-muted d-block mb-1">
                                Email
                            </small>

                            <div class="fw-semibold">
                                {{ $order->customer_email }}
                            </div>

                        </div>


                        <div class="col-md-6">

                            <small class="text-muted d-block mb-1">
                                Phone
                            </small>

                            <div class="fw-semibold">
                                {{ $order->customer_phone }}
                            </div>

                        </div>


                        <div class="col-md-6">

                            <small class="text-muted d-block mb-1">
                                Order ID
                            </small>

                            <div class="fw-semibold">
                                #{{ $order->id }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>



        {{-- RIGHT COLUMN --}}
        <div class="col-lg-4">


            {{-- Order Summary --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">

                <div class="card-header bg-white border-bottom fw-semibold py-3">

                    <i class="bi bi-receipt me-2 text-warning"></i>

                    Order Summary

                </div>


                <div class="card-body">


                    <div class="d-flex justify-content-between mb-2 text-muted">

                        <span>
                            Subtotal
                        </span>

                        <span>
                            ₹{{ number_format($order->subtotal, 2) }}
                        </span>

                    </div>


                    @php
                        /*
                         * Current orders table does not contain:
                         * coupon_discount
                         * coupon_code
                         * shipping_charge
                         * tax_amount
                         *
                         * Therefore those old fields are intentionally
                         * not displayed here.
                         */
                    @endphp


                    <div class="d-flex justify-content-between mb-2 text-muted">

                        <span>
                            Shipping
                        </span>

                        @if((float) $order->total > (float) $order->subtotal)

                            <span>
                                ₹{{ number_format(
                                    $order->total - $order->subtotal,
                                    2
                                ) }}
                            </span>

                        @else

                            <span class="text-success fw-semibold">
                                Free
                            </span>

                        @endif

                    </div>


                    <hr>


                    <div class="d-flex justify-content-between fw-bold fs-5">

                        <span>
                            Order Total
                        </span>

                        <span class="text-dark">

                            ₹{{ number_format($order->total, 2) }}

                        </span>

                    </div>


                    <div class="mt-3 text-muted small">

                        <i class="bi bi-info-circle me-1"></i>

                        Payment status will be updated when payment
                        processing is integrated.

                    </div>

                </div>

            </div>



            {{-- Shipping Address --}}
            <div class="card border-0 shadow-sm rounded-3">

                <div class="card-header bg-white border-bottom fw-semibold py-3">

                    <i class="bi bi-geo-alt me-2 text-warning"></i>

                    Shipping Address

                </div>


                <div class="card-body">

                    <address
                        class="mb-0"
                        style="line-height:1.7;"
                    >

                        <strong>
                            {{ $order->customer_name }}
                        </strong>

                        <br>

                        {!! nl2br(e($order->shipping_address)) !!}

                        <br>

                        <i class="bi bi-telephone me-1"></i>

                        {{ $order->customer_phone }}

                        <br>

                        <i class="bi bi-envelope me-1"></i>

                        {{ $order->customer_email }}

                    </address>

                </div>

            </div>


        </div>

    </div>

</div>
@endsection