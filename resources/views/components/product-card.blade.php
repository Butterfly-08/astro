<div class="product-card h-100 d-flex flex-column">

    {{-- =========================================
         PRODUCT IMAGE SECTION
    ========================================== --}}
    <div class="product-card-img-wrap position-relative overflow-hidden">

        {{-- Stock Badge --}}
        <div class="position-absolute top-0 end-0 m-2" style="z-index: 2;">

            @if($product->in_stock)

                <span class="badge bg-success shadow-sm"
                      style="font-size: 0.72rem;">
                    <i class="bi bi-check-circle me-1"></i>
                    IN STOCK
                </span>

            @else

                <span class="badge bg-secondary shadow-sm"
                      style="font-size: 0.72rem;">
                    <i class="bi bi-x-circle me-1"></i>
                    OUT OF STOCK
                </span>

            @endif

        </div>


        {{-- Product Image --}}
        <a
            href="{{ route('shop.product.show', $product->slug) }}"
            class="d-block text-center product-img-link bg-light"
        >

            @if($product->image)

                <img
                    src="{{ asset('storage/' . $product->image) }}"
                    alt="{{ $product->name }}"
                    class="product-card-img img-fluid"
                >

            @else

                <div class="product-img-fallback d-flex align-items-center justify-content-center">
                    <i class="bi bi-gem text-warning fs-1"></i>
                </div>

            @endif

        </a>

    </div>


    {{-- =========================================
         PRODUCT BODY
    ========================================== --}}
    <div class="p-3 d-flex flex-column flex-grow-1">


        {{-- =========================================
             CATEGORY + RATING
        ========================================== --}}
        <div class="d-flex align-items-center justify-content-between mb-2">

            @if($product->category)

                <a
                    href="{{ route('shop.category', $product->category->slug) }}"
                    class="text-decoration-none text-muted small fw-semibold text-uppercase"
                    style="font-size: 0.75rem;"
                >
                    {{ $product->category->name }}
                </a>

            @else

                <span
                    class="text-muted small"
                    style="font-size: 0.75rem;"
                >
                    Spiritual Item
                </span>

            @endif


            {{-- Rating --}}
            @if($product->total_reviews > 0)

                <div
                    class="star-rating d-flex align-items-center gap-1"
                    style="font-size: 0.8rem; color: #F5B041;"
                >

                    <i class="bi bi-star-fill"></i>

                    <span class="fw-bold text-dark">
                        {{ number_format($product->rating_avg, 1) }}
                    </span>

                    <span
                        class="text-muted"
                        style="font-size: 0.72rem;"
                    >
                        ({{ $product->total_reviews }})
                    </span>

                </div>

            @endif

        </div>


        {{-- =========================================
             PRODUCT NAME
        ========================================== --}}
        <h5 class="product-card-title mb-2">

            <a
                href="{{ route('shop.product.show', $product->slug) }}"
                class="text-decoration-none text-dark fw-bold"
                title="{{ $product->name }}"
            >
                {{ Str::limit($product->name, 48) }}
            </a>

        </h5>


        {{-- =========================================
             PRODUCT DESCRIPTION
        ========================================== --}}
        <p
            class="text-muted small mb-3 flex-grow-1"
            style="font-size: 0.83rem; line-height: 1.4;"
        >

            @if($product->description)

                {{ Str::limit(strip_tags($product->description), 75) }}

            @else

                Authentic spiritual and devotional product.

            @endif

        </p>


        {{-- =========================================
             PRICE SECTION
        ========================================== --}}
        <div class="pt-2 border-top mt-auto">

            <div class="d-flex align-items-baseline gap-2 mb-2">

                <span
                    class="fs-5 fw-bold text-dark"
                    style="font-family: 'Plus Jakarta Sans', sans-serif;"
                >
                    ₹{{ number_format($product->effective_price, 2) }}
                </span>

            </div>


            {{-- =========================================
                 ACTION BUTTONS
            ========================================== --}}
            <div class="d-flex gap-2">


                {{-- =====================================
                     VIEW BUTTON
                ====================================== --}}
                <a
                    href="{{ route('shop.product.show', $product->slug) }}"
                    class="btn btn-outline-primary btn-sm flex-grow-1 fw-semibold d-flex align-items-center justify-content-center gap-1"
                >

                    <i class="bi bi-eye"></i>

                    View

                </a>


                {{-- =====================================
                     BUY NOW BUTTON
                ====================================== --}}
                @if($product->in_stock)

                    <button
                        type="button"
                        onclick="buyNowProduct({{ $product->id }}, this)"
                        class="btn btn-primary btn-sm flex-grow-1 fw-semibold d-flex align-items-center justify-content-center gap-1"
                        style="
                            background: var(--astro-purple, #1A0B2E);
                            border-color: var(--astro-purple, #1A0B2E);
                        "
                    >

                        <i class="bi bi-lightning-fill"></i>

                        Buy Now

                    </button>

                @else

                    <button
                        type="button"
                        class="btn btn-secondary btn-sm flex-grow-1 fw-semibold"
                        disabled
                    >

                        <i class="bi bi-x-circle me-1"></i>

                        Out of Stock

                    </button>

                @endif

            </div>

        </div>

    </div>

</div>


{{-- =========================================
     BUY NOW JAVASCRIPT
     Add Product → Direct Checkout
========================================== --}}
@once

<script>

function buyNowProduct(productId, button) {

    /*
     * Prevent double click
     */
    if (button.disabled) {
        return;
    }


    /*
     * Save original button content
     */
    const originalHtml = button.innerHTML;


    /*
     * Disable button
     */
    button.disabled = true;


    /*
     * Loading state
     */
    button.innerHTML = `
        <span
            class="spinner-border spinner-border-sm"
            role="status"
            aria-hidden="true"
        ></span>

        <span>Processing...</span>
    `;


    /*
     * Prepare request
     */
    const formData = new FormData();

    formData.append(
        '_token',
        '{{ csrf_token() }}'
    );

    formData.append(
        'quantity',
        '1'
    );


    /*
     * Add product to cart
     */
    fetch(
        `/cart/add/${productId}`,
        {
            method: 'POST',

            body: formData,

            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        }
    )


    /*
     * Check response
     */
    .then(response => {

        if (!response.ok) {

            throw new Error(
                'Unable to process Buy Now.'
            );

        }

        return response.json();

    })


    /*
     * Success
     */
    .then(data => {

        if (data.success) {

            /*
             * Direct Checkout
             */
            window.location.href =
                "{{ route('checkout.index') }}";

        } else {

            throw new Error(
                data.message ||
                'Unable to process Buy Now.'
            );

        }

    })


    /*
     * Error handling
     */
    .catch(error => {

        console.error(
            'Buy Now Error:',
            error
        );


        /*
         * Restore button
         */
        button.disabled = false;

        button.innerHTML =
            originalHtml;


        /*
         * Show message
         */
        alert(
            error.message ||
            'Unable to process Buy Now. Please try again.'
        );

    });

}

</script>

@endonce