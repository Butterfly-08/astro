@extends('layouts.app')

@section('title', 'Daily Horoscopes — AstroVani')
@section('meta_description', 'Read your daily horoscope for all twelve zodiac signs.')

@section('content')
<section class="py-5" style="background:linear-gradient(135deg,#1A0B2E,#2D124D);border-bottom:3px solid #F5B041;">
    <div class="container text-center text-white py-3">
        <span class="badge bg-warning text-dark mb-3"><i class="bi bi-stars me-1"></i>AstroVani Guidance</span>
        <h1 class="display-5 fw-bold">Daily Horoscope</h1>
        <p class="text-white-50 mb-0">Thoughtful guidance for every zodiac sign.</p>
    </div>
</section>
<section class="py-5">
    <div class="container">
        <form method="GET" class="d-flex justify-content-center align-items-end gap-2 mb-4">
            <div>
                <label for="date" class="form-label fw-semibold">Choose a date</label>
                <input id="date" name="date" type="date" class="form-control" value="{{ $date }}" max="{{ today()->toDateString() }}">
            </div>
            <button class="btn btn-primary mb-0">Show</button>
        </form>
        @if($horoscopes->isEmpty())
            <div class="text-center bg-white border rounded-4 p-5">
                <i class="bi bi-moon-stars fs-1 text-warning"></i>
                <h2 class="h4 mt-3">No horoscope published for this date</h2>
                <p class="text-muted mb-0">Please check back soon for your zodiac reading.</p>
            </div>
        @else
            <div class="row g-3">
                @foreach($horoscopes as $horoscope)
                    <div class="col-md-6 col-xl-4">
                        <article class="h-100 bg-white border rounded-4 p-4 shadow-sm">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <span class="rounded-circle d-inline-flex align-items-center justify-content-center text-warning" style="width:48px;height:48px;background:#2D124D;"><i class="bi bi-stars fs-4"></i></span>
                                <div><h2 class="h5 fw-bold mb-0">{{ $horoscope->zodiac_sign }}</h2><small class="text-muted">{{ $horoscope->horoscope_date->format('d M Y') }}</small></div>
                            </div>
                            <h3 class="h6 fw-bold">{{ $horoscope->title }}</h3>
                            <p class="text-secondary mb-0" style="white-space:pre-line;">{{ $horoscope->content }}</p>
                        </article>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
