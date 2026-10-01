@extends('layouts.app')

@section('title', 'My Wishlist — AstroVani')

@section('content')
<div class="container py-4 my-3">

    {{-- Page Header --}}
    <div class="p-4 p-md-5 mb-4 rounded-4 text-white shadow-sm"
         style="background: linear-gradient(135deg, #1A0B2E 0%, #2D124D 60%, #481B7F 100%); border-bottom: 3px solid #F5B041;">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark mb-2 px-3 py-1 fw-bold">
                    <i class="bi bi-heart-fill me-1"></i> My Wishlist
                </span>
                <h2 class="display-6 fw-bold mb-1">Saved Products</h2>
                <p class="lead text-white-50 mb-0">Products you love, saved for later.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="{{ route('shop.index') }}" class="btn btn-astro-gold">
                    <i class="bi bi-shop me-1"></i> Explore Shop
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
                    <a href="{{ route('user.orders.index') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-3 text-secondary">
                        <i class="bi bi-bag-check fs-5 text-muted"></i><span>My Orders</span>
                    </a>
                    <a href="{{ route('user.wishlist.index') }}" class="list-group-item list-group-item-action active fw-semibold d-flex align-items-center gap-2 py-3">
                        <i class="bi bi-heart-fill fs-5 text-danger"></i><span>Wishlist</span>
                        @if($wishlistItems->total() > 0)
                            <span class="badge bg-danger ms-auto">{{ $wishlistItems->total() }}</span>
                        @endif
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

        {{-- Wishlist Content --}}
        <div class="col-lg-9">
            {{-- Flash --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($wishlistItems->isNotEmpty())
                {{-- Top actions bar --}}
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <p class="text-muted mb-0 small">
                        {{ $wishlistItems->total() }} saved item(s)
                    </p>
                    <form method="POST" action="{{ route('user.wishlist.clear') }}"
                          onsubmit="return confirm('Clear your entire wishlist?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger rounded-pill">
                            <i class="bi bi-trash me-1"></i> Clear All
                        </button>
                    </form>
                </div>

                <div class="row g-3">
                    @foreach($wishlistItems as $item)
                        @php $product = $item->product; @endphp
                        @if(!$product) @continue @endif

                        <div class="col-sm-6 col-xl-4" id="wishlist-item-{{ $item->id }}">
                            <div class="card border-0 shadow-sm rounded-3 h-100 overflow-hidden product-card">
                                {{-- Product Image --}}
                                <div class="position-relative overflow-hidden" style="height: 200px;">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}"
                                             alt="{{ $product->name }}"
                                             class="w-100 h-100 object-fit-cover product-img-hover">
                                    @else
                                        <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center text-muted"
                                             style="font-size: 4rem;">🔮</div>
                                    @endif

                                    {{-- Out of Stock Overlay --}}
                                    @if(!$product->in_stock)
                                        <div class="position-absolute inset-0 w-100 h-100 d-flex align-items-center justify-content-center"
                                             style="background: rgba(0,0,0,0.5); top:0; left:0;">
                                            <span class="badge bg-danger fs-6 px-3 py-2">Out of Stock</span>
                                        </div>
                                    @endif

                                    {{-- Remove from Wishlist --}}
                                    <button class="btn btn-sm btn-light position-absolute top-0 end-0 m-2 rounded-circle p-1 lh-1 shadow-sm remove-wishlist-btn"
                                            data-wishlist-id="{{ $item->id }}"
                                            style="width:32px; height:32px;"
                                            title="Remove from Wishlist">
                                        <i class="bi bi-heart-fill text-danger"></i>
                                    </button>
                                </div>

                                <div class="card-body d-flex flex-column p-3">
                                    {{-- Category --}}
                                    @if($product->category)
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded-pill small mb-2 align-self-start">
                                            {{ $product->category->name }}
                                        </span>
                                    @endif

                                    {{-- Name --}}
                                    <h6 class="fw-bold text-dark mb-2 lh-sm">
                                        <a href="{{ route('shop.product.show', $product->slug) }}" class="text-dark text-decoration-none stretched-link-exclude">
                                            {{ $product->name }}
                                        </a>
                                    </h6>

                                    {{-- Price --}}
                                    <div class="mb-3">
                                        <span class="fw-bold text-dark fs-5">₹{{ number_format($product->effective_price, 2) }}</span>
                                        @if($product->sale_price && $product->sale_price < $product->price)
                                            <del class="text-muted small ms-2">₹{{ number_format($product->price, 2) }}</del>
                                            <span class="badge bg-success-subtle text-success ms-1 small">
                                                {{ round((1 - $product->sale_price / $product->price) * 100) }}% OFF
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Actions --}}
                                    <div class="mt-auto d-flex gap-2">
                                        <form method="POST" action="{{ route('cart.add', $product->id) }}" class="flex-grow-1">
                                            @csrf
                                            <button type="submit"
                                                    class="btn btn-astro-gold btn-sm w-100 rounded-pill {{ !$product->in_stock ? 'disabled' : '' }}"
                                                    {{ !$product->in_stock ? 'disabled' : '' }}>
                                                <i class="bi bi-cart-plus me-1"></i>
                                                {{ $product->in_stock ? 'Add to Cart' : 'Out of Stock' }}
                                            </button>
                                        </form>
                                        <a href="{{ route('shop.product.show', $product->slug) }}"
                                           class="btn btn-outline-secondary btn-sm rounded-pill px-2">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($wishlistItems->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $wishlistItems->links() }}
                    </div>
                @endif

            @else
                {{-- Empty State --}}
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body text-center py-5">
                        <div class="mb-4" style="font-size: 5rem; opacity: 0.15;">💝</div>
                        <h5 class="fw-bold text-dark">Your Wishlist is Empty</h5>
                        <p class="text-muted mb-4">
                            Start saving your favourite astrology products and spiritual items!
                        </p>
                        <a href="{{ route('shop.index') }}" class="btn btn-astro-gold rounded-pill px-5">
                            <i class="bi bi-shop me-2"></i> Explore the Shop
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    document.querySelectorAll('.remove-wishlist-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const wishlistId = this.dataset.wishlistId;
            const card = document.getElementById('wishlist-item-' + wishlistId);

            fetch(`/dashboard/wishlist/remove/${wishlistId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && card) {
                    card.style.transition = 'all 0.35s ease';
                    card.style.opacity   = '0';
                    card.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        card.remove();
                        // If no items left, reload to show empty state
                        if (document.querySelectorAll('[id^="wishlist-item-"]').length === 0) {
                            location.reload();
                        }
                    }, 350);
                }
            })
            .catch(err => console.error('Wishlist remove error:', err));
        });
    });
});
</script>
@endpush
@endsection
