<nav class="astro-dock" id="astro-dock" aria-label="Quick navigation">
    <a class="astro-dock-item {{ request()->routeIs('home') ? 'is-active' : '' }}" href="{{ route('home') }}" aria-label="Home" @if(request()->routeIs('home')) aria-current="page" @endif>
        <i class="bi bi-house-door-fill" aria-hidden="true"></i>
        <span class="astro-dock-label" role="tooltip">Home</span>
    </a>
    <a class="astro-dock-item {{ request()->routeIs('astrologers.*') ? 'is-active' : '' }}" href="{{ route('astrologers.index') }}" aria-label="Astrologers" @if(request()->routeIs('astrologers.*')) aria-current="page" @endif>
        <i class="bi bi-stars" aria-hidden="true"></i>
        <span class="astro-dock-label" role="tooltip">Astrologers</span>
    </a>
    <a class="astro-dock-item {{ request()->routeIs('services.*') ? 'is-active' : '' }}" href="{{ route('services.index') }}" aria-label="Services" @if(request()->routeIs('services.*')) aria-current="page" @endif>
        <i class="bi bi-compass-fill" aria-hidden="true"></i>
        <span class="astro-dock-label" role="tooltip">Services</span>
    </a>
    <a class="astro-dock-item {{ request()->routeIs('shop.*') ? 'is-active' : '' }}" href="{{ route('shop.index') }}" aria-label="Spiritual shop" @if(request()->routeIs('shop.*')) aria-current="page" @endif>
        <i class="bi bi-bag-heart-fill" aria-hidden="true"></i>
        <span class="astro-dock-label" role="tooltip">Shop</span>
    </a>
    <a class="astro-dock-item astro-dock-cart {{ request()->routeIs('cart.*') ? 'is-active' : '' }}" href="{{ route('cart.index') }}" aria-label="Cart, {{ app(\App\Services\CartService::class)->count() }} items" @if(request()->routeIs('cart.*')) aria-current="page" @endif>
        <i class="bi bi-cart3" aria-hidden="true"></i>
        <span class="astro-dock-count" aria-hidden="true">{{ app(\App\Services\CartService::class)->count() }}</span>
        <span class="astro-dock-label" role="tooltip">Cart</span>
    </a>
</nav>
