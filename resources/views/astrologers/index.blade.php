@extends('layouts.app')

@section('title', 'Talk to Verified Astrologers Online — AstroVani')
@section('meta_description', 'Browse and connect with India\'s top verified Vedic astrologers, tarot readers, and numerologists on AstroVani. Chat, call, or video consult.')

@push('styles')
<style>
    /* Hero Banner */
    .astrologer-hero {
        background: linear-gradient(135deg, #1A0B2E 0%, #2D124D 60%, #4A1E80 100%);
        color: #fff;
        padding: 60px 0 50px;
        position: relative;
        overflow: hidden;
    }
    .astrologer-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60'%3E%3Ccircle cx='30' cy='30' r='1' fill='rgba(245,176,65,0.3)'/%3E%3C/svg%3E") repeat;
        opacity: 0.5;
    }

    /* Search Bar */
    .search-bar { background: rgba(255,255,255,0.95); border-radius: 12px; padding: 8px 8px 8px 16px; }
    .search-bar input { border: none; background: transparent; outline: none; font-size: 1rem; width: 100%; }
    .search-bar .btn { border-radius: 8px; }

    /* Filter Pills */
    .filter-pill { display: inline-block; padding: 6px 16px; border-radius: 20px; background: #fff; border: 1.5px solid #E5E7EB; color: #374151; font-size: 0.85rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: all 0.2s; }
    .filter-pill:hover, .filter-pill.active { background: var(--astro-purple); border-color: var(--astro-purple); color: #fff; }

    /* Astrologer Card */
    .astrologer-card { background: #fff; border-radius: 16px; border: 1px solid #E5E7EB; overflow: hidden; transition: transform 0.25s, box-shadow 0.25s; }
    .astrologer-card:hover { transform: translateY(-5px); box-shadow: 0 12px 32px rgba(26, 11, 46, 0.1); }
    .card-avatar { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(245,176,65,0.4); }
    .card-avatar-placeholder { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #2D124D, #6C3483); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 700; color: #F5B041; border: 3px solid rgba(245,176,65,0.3); flex-shrink: 0; }
    .online-dot { width: 12px; height: 12px; border-radius: 50%; background: #10B981; border: 2px solid #fff; position: absolute; bottom: 4px; right: 4px; }
    .availability-badge { font-size: 0.75rem; padding: 3px 8px; border-radius: 10px; }
    .star-rating { color: #F5B041; font-size: 0.82rem; }
    .spec-tag { display: inline-block; padding: 2px 8px; background: rgba(108,52,131,0.08); color: #6C3483; border-radius: 10px; font-size: 0.75rem; margin: 2px; font-weight: 500; }
    .rate-label { font-size: 0.75rem; color: #9CA3AF; }
    .rate-amount { font-size: 0.9rem; font-weight: 700; color: #1F2937; }
    .btn-consult { background: var(--astro-purple); color: #fff; border: none; border-radius: 8px; font-size: 0.82rem; font-weight: 600; padding: 7px 14px; transition: all 0.2s; }
    .btn-consult:hover { background: var(--astro-purple-light); color: #fff; }
    .btn-consult-outline { border: 1.5px solid var(--astro-purple); color: var(--astro-purple); border-radius: 8px; font-size: 0.82rem; font-weight: 600; padding: 6px 12px; background: transparent; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
    .btn-consult-outline:hover { background: var(--astro-purple); color: #fff; }

    /* Sidebar filter box */
    .filter-box { background: #fff; border-radius: 14px; border: 1px solid #E5E7EB; padding: 20px; margin-bottom: 16px; }
    .filter-box-title { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #6B7280; margin-bottom: 12px; }

    /* Featured Banner */
    .featured-strip { background: linear-gradient(90deg, rgba(245,176,65,0.12), rgba(108,52,131,0.08)); border: 1px solid rgba(245,176,65,0.3); border-radius: 12px; padding: 14px 18px; }
</style>
@endpush

@section('content')

{{-- Hero --}}
<section class="astrologer-hero">
    <div class="container position-relative">
        <div class="row justify-content-center text-center mb-4">
            <div class="col-lg-8">
                <h1 class="display-6 fw-bold mb-2" style="font-family:'Outfit',sans-serif;">Talk to Verified Astrologers</h1>
                <p class="opacity-75 mb-4">Connect with India's top Vedic astrologers, tarot readers & numerologists — anytime, anywhere.</p>

                {{-- Search Bar --}}
                <form method="GET" action="{{ route('astrologers.index') }}" class="mx-auto" style="max-width:560px;">
                    <div class="search-bar d-flex align-items-center gap-2">
                        <i class="bi bi-search text-muted"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, specialization, language…">
                        <button type="submit" class="btn btn-primary px-4">Search</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Quick stats --}}
        <div class="row justify-content-center g-3 text-center">
            <div class="col-auto">
                <div class="px-4 py-2" style="background:rgba(255,255,255,0.1);border-radius:10px;backdrop-filter:blur(4px);">
                    <div class="fw-bold fs-5" style="color:#F5B041;">1000+</div>
                    <div class="opacity-75 small">Verified Astrologers</div>
                </div>
            </div>
            <div class="col-auto">
                <div class="px-4 py-2" style="background:rgba(255,255,255,0.1);border-radius:10px;backdrop-filter:blur(4px);">
                    <div class="fw-bold fs-5" style="color:#F5B041;">50L+</div>
                    <div class="opacity-75 small">Consultations Done</div>
                </div>
            </div>
            <div class="col-auto">
                <div class="px-4 py-2" style="background:rgba(255,255,255,0.1);border-radius:10px;backdrop-filter:blur(4px);">
                    <div class="fw-bold fs-5" style="color:#F5B041;">4.8★</div>
                    <div class="opacity-75 small">Avg. Rating</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Main Content --}}
<div class="container py-5">

    {{-- Featured Astrologers Strip --}}
    @if($featured->isNotEmpty())
        <div class="featured-strip mb-4 d-flex align-items-center gap-4 flex-wrap">
            <div class="fw-bold small text-warning d-flex align-items-center gap-1 flex-shrink-0">
                <i class="bi bi-star-fill"></i> Featured
            </div>
            @foreach($featured as $f)
                <a href="{{ route('astrologers.show', $f->slug) }}" class="d-flex align-items-center gap-2 text-decoration-none">
                    @if($f->profile_image)
                        <img src="{{ asset('storage/' . $f->profile_image) }}" alt="{{ $f->display_name }}" style="width:34px;height:34px;border-radius:50%;object-fit:cover;border:2px solid #F5B041;">
                    @else
                        <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#2D124D,#6C3483);display:flex;align-items:center;justify-content:center;color:#F5B041;font-size:0.85rem;font-weight:700;border:2px solid #F5B041;">{{ strtoupper(substr($f->display_name,0,1)) }}</div>
                    @endif
                    <div>
                        <div class="fw-semibold small text-dark">{{ $f->display_name }}</div>
                        <div class="text-muted" style="font-size:0.72rem;">{{ Str::limit($f->specializations, 30) }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    <div class="row g-4">
        {{-- Sidebar Filters --}}
        <div class="col-lg-3 d-none d-lg-block">
            <form method="GET" action="{{ route('astrologers.index') }}" id="sidebarFilterForm">
                {{-- Service Filter --}}
                <div class="filter-box">
                    <div class="filter-box-title"><i class="bi bi-gem me-1"></i>Service</div>
                    @foreach($services as $svc)
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio" name="service" id="svc_{{ $svc->id }}" value="{{ $svc->id }}" @checked(request('service') == $svc->id) onchange="this.form.submit()">
                            <label class="form-check-label small" for="svc_{{ $svc->id }}">
                                @if($svc->icon)<i class="{{ $svc->icon }} text-primary me-1" style="font-size:0.85rem;"></i>@endif
                                {{ $svc->name }}
                            </label>
                        </div>
                    @endforeach
                    @if(request('service'))
                        <a href="{{ route('astrologers.index', request()->except('service')) }}" class="text-danger small d-block mt-2">✕ Clear</a>
                    @endif
                </div>

                {{-- Language Filter --}}
                <div class="filter-box">
                    <div class="filter-box-title"><i class="bi bi-translate me-1"></i>Language</div>
                    @foreach(['Hindi','English','Tamil','Telugu','Bengali','Marathi','Gujarati','Punjabi'] as $lang)
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio" name="language" id="lang_{{ Str::slug($lang) }}" value="{{ $lang }}" @checked(request('language') === $lang) onchange="this.form.submit()">
                            <label class="form-check-label small" for="lang_{{ Str::slug($lang) }}">{{ $lang }}</label>
                        </div>
                    @endforeach
                    @if(request('language'))
                        <a href="{{ route('astrologers.index', request()->except('language')) }}" class="text-danger small d-block mt-2">✕ Clear</a>
                    @endif
                </div>

                {{-- Availability --}}
                <div class="filter-box">
                    <div class="filter-box-title"><i class="bi bi-circle-fill text-success me-1" style="font-size:0.7rem;"></i>Availability</div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="available" id="available" value="1" @checked(request('available')) onchange="this.form.submit()">
                        <label class="form-check-label small" for="available">Show only available</label>
                    </div>
                </div>

                {{-- Sort --}}
                <div class="filter-box">
                    <div class="filter-box-title"><i class="bi bi-sort-down me-1"></i>Sort By</div>
                    @foreach(['rating' => 'Top Rated', 'experience' => 'Most Experienced', 'price_asc' => 'Price: Low to High', 'price_desc' => 'Price: High to Low', 'newest' => 'Newest First'] as $val => $label)
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio" name="sort" id="sort_{{ $val }}" value="{{ $val }}" @checked(request('sort', 'rating') === $val) onchange="this.form.submit()">
                            <label class="form-check-label small" for="sort_{{ $val }}">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>

                {{-- Keep other params as hidden inputs --}}
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
            </form>
        </div>

        {{-- Astrologer Cards --}}
        <div class="col-lg-9">
            {{-- Mobile filters & result count --}}
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div class="text-muted small">
                    Showing <strong>{{ $astrologers->firstItem() ?? 0 }}–{{ $astrologers->lastItem() ?? 0 }}</strong> of <strong>{{ $astrologers->total() }}</strong> astrologers
                </div>
                {{-- Mobile sort --}}
                <div class="d-lg-none">
                    <form method="GET" action="{{ route('astrologers.index') }}">
                        <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                            @foreach(['rating'=>'Top Rated','experience'=>'Most Experienced','price_asc'=>'Price: Low↑','price_desc'=>'Price: High↓','newest'=>'Newest'] as $val=>$label)
                                <option value="{{ $val }}" @selected(request('sort','rating')===$val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            @forelse($astrologers as $astrologer)
                <div class="astrologer-card mb-3 p-4">
                    <div class="d-flex gap-4 flex-wrap">
                        {{-- Avatar --}}
                        <div class="position-relative flex-shrink-0">
                            @if($astrologer->profile_image)
                                <img src="{{ asset('storage/' . $astrologer->profile_image) }}" alt="{{ $astrologer->display_name }}" class="card-avatar">
                            @else
                                <div class="card-avatar-placeholder">{{ strtoupper(substr($astrologer->display_name, 0, 1)) }}</div>
                            @endif
                            @if($astrologer->is_available)
                                <div class="online-dot"></div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex flex-wrap align-items-start gap-2 mb-1">
                                <h5 class="mb-0 fw-bold fs-6">
                                    <a href="{{ route('astrologers.show', $astrologer->slug) }}" class="text-dark text-decoration-none hover-purple">{{ $astrologer->display_name }}</a>
                                </h5>
                                @if($astrologer->is_featured)
                                    <span style="background:#FEF3C7;color:#92400E;font-size:0.7rem;padding:2px 7px;border-radius:10px;font-weight:600;"><i class="bi bi-star-fill me-1"></i>Top</span>
                                @endif
                                @if($astrologer->is_available)
                                    <span class="availability-badge" style="background:#D1FAE5;color:#065F46;"><i class="bi bi-circle-fill me-1" style="font-size:0.55rem;"></i>Online</span>
                                @else
                                    <span class="availability-badge" style="background:#F3F4F6;color:#6B7280;">Offline</span>
                                @endif
                            </div>

                            {{-- Specs --}}
                            <div class="mb-2">
                                @foreach($astrologer->specializations_array as $spec)
                                    <span class="spec-tag">{{ $spec }}</span>
                                @endforeach
                            </div>

                            {{-- Languages & experience --}}
                            <div class="text-muted small mb-2">
                                @if($astrologer->languages)
                                    <span class="me-3"><i class="bi bi-translate me-1"></i>{{ $astrologer->languages }}</span>
                                @endif
                                <span><i class="bi bi-briefcase me-1"></i>{{ $astrologer->experience_years }} yrs exp.</span>
                            </div>

                            {{-- Short bio --}}
                            @if($astrologer->short_bio)
                                <p class="text-muted small mb-2" style="line-height:1.5;">{{ Str::limit($astrologer->short_bio, 120) }}</p>
                            @endif

                            {{-- Rating --}}
                            <div class="d-flex align-items-center gap-2">
                                <div class="star-rating">
                                    @for($i=1;$i<=5;$i++)
                                        <i class="bi bi-star{{ $i <= round($astrologer->rating_avg) ? '-fill' : '' }}"></i>
                                    @endfor
                                </div>
                                <span class="fw-semibold small">{{ number_format($astrologer->rating_avg,1) }}</span>
                                <span class="text-muted small">({{ number_format($astrologer->total_reviews) }} reviews)</span>
                            </div>
                        </div>

                        {{-- Rates + CTA --}}
                        <div class="d-flex flex-column align-items-center justify-content-center gap-2 ms-auto" style="min-width:110px;">
                            <div class="text-center">
                                <div class="rate-label">Chat</div>
                                <div class="rate-amount">₹{{ number_format($astrologer->chat_rate, 0) }}<span class="text-muted fw-normal" style="font-size:0.72rem;">/min</span></div>
                            </div>
                            <a href="{{ route('astrologers.show', $astrologer->slug) }}" class="btn-consult w-100 text-center text-decoration-none" style="border-radius:8px;padding:8px 16px;display:block;">
                                <i class="bi bi-chat me-1"></i>Consult
                            </a>
                            <a href="{{ route('astrologers.show', $astrologer->slug) }}" class="btn-consult-outline w-100 justify-content-center">
                                <i class="bi bi-person"></i>Profile
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-stars display-4 d-block mb-3 opacity-25"></i>
                    <h5 class="text-muted">No astrologers found</h5>
                    <p class="text-muted">Try adjusting your filters or <a href="{{ route('astrologers.index') }}">clear all filters</a>.</p>
                </div>
            @endforelse

            {{-- Pagination --}}
            @if($astrologers->hasPages())
                <div class="mt-4">
                    {{ $astrologers->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
