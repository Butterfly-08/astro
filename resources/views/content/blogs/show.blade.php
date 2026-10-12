@extends('layouts.app')

@section('title', $blog->title . ' — AstroVani')
@section('meta_description', $blog->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($blog->body), 155))

@section('content')
<article class="container py-5" style="max-width:900px;">
    <a href="{{ route('blogs.index') }}" class="text-decoration-none"><i class="bi bi-arrow-left me-1"></i>All articles</a>
    <header class="text-center py-4">
        <div class="small text-muted mb-2">{{ $blog->published_at->format('d M Y') }}</div>
        <h1 class="display-5 fw-bold">{{ $blog->title }}</h1>
        @if($blog->excerpt)<p class="lead text-muted">{{ $blog->excerpt }}</p>@endif
    </header>
    @if($blog->cover_image)
        <img src="{{ asset('storage/' . $blog->cover_image) }}" alt="{{ $blog->title }}" class="w-100 rounded-4 mb-4" style="max-height:480px;object-fit:cover;">
    @endif
    <div class="bg-white border rounded-4 p-4 p-md-5 shadow-sm" style="white-space:pre-line;line-height:1.9;">{{ $blog->body }}</div>
</article>
@endsection
