@extends('layouts.app')

@section('title', 'Astrology Blogs & Articles — AstroVani')
@section('meta_description', 'Explore practical astrology guidance and articles from AstroVani.')

@section('content')
<section class="py-5" style="background:linear-gradient(135deg,#1A0B2E,#2D124D);border-bottom:3px solid #F5B041;">
    <div class="container text-center text-white py-3">
        <span class="badge bg-warning text-dark mb-3"><i class="bi bi-journal-richtext me-1"></i>AstroVani Insights</span>
        <h1 class="display-5 fw-bold">Blogs & Articles</h1>
        <p class="text-white-50 mb-0">Explore astrology, spiritual wisdom, and practical guidance.</p>
    </div>
</section>
<section class="py-5">
    <div class="container">
        @if($blogs->isEmpty())
            <div class="text-center bg-white border rounded-4 p-5">
                <i class="bi bi-journal-x fs-1 text-warning"></i>
                <h2 class="h4 mt-3">Articles are coming soon</h2>
                <p class="text-muted mb-0">Check back for new insights from AstroVani.</p>
            </div>
        @else
            <div class="row g-4">
                @foreach($blogs as $blog)
                    <div class="col-md-6 col-xl-4">
                        <article class="card h-100 border-0 shadow-sm overflow-hidden">
                            @if($blog->cover_image)
                                <img src="{{ asset('storage/' . $blog->cover_image) }}" class="card-img-top" alt="{{ $blog->title }}" style="height:220px;object-fit:cover;">
                            @else
                                <div class="d-flex align-items-center justify-content-center text-warning" style="height:180px;background:linear-gradient(135deg,#1A0B2E,#4A1E80);"><i class="bi bi-stars display-4"></i></div>
                            @endif
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="small text-muted mb-2">{{ $blog->published_at->format('d M Y') }}</div>
                                <h2 class="h5 fw-bold">{{ $blog->title }}</h2>
                                <p class="text-muted flex-grow-1">{{ $blog->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($blog->body), 150) }}</p>
                                <a href="{{ route('blogs.show', $blog->slug) }}" class="btn btn-outline-primary align-self-start">Read article <i class="bi bi-arrow-right ms-1"></i></a>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
            @if($blogs->hasPages())<div class="mt-4">{{ $blogs->links() }}</div>@endif
        @endif
    </div>
</section>
@endsection
