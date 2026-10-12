<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Horoscope;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function horoscopes(Request $request): View
    {
        $validated = $request->validate(['date' => 'nullable|date']);
        $date = $validated['date'] ?? today()->toDateString();
        $horoscopes = Horoscope::query()
            ->where('status', 'published')
            ->whereDate('horoscope_date', $date)
            ->orderBy('zodiac_sign')
            ->get();

        return view('content.horoscopes', compact('date', 'horoscopes'));
    }

    public function blogs(): View
    {
        $blogs = Blog::query()
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->paginate(9);

        return view('content.blogs.index', compact('blogs'));
    }

    public function showBlog(string $slug): View
    {
        $blog = Blog::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->firstOrFail();

        return view('content.blogs.show', compact('blog'));
    }
}
