<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $query = Blog::query()->latest();

        if ($request->filled('status') && in_array($request->status, ['draft', 'published'], true)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->string('search')->toString() . '%');
        }

        $blogs = $query->paginate(20)->withQueryString();

        return view('admin.blogs.index', compact('blogs'));
    }

    public function create(): View
    {
        $blog = new Blog(['status' => 'draft']);

        return view('admin.blogs.create', compact('blog'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['title']);
        $data['published_at'] = $data['status'] === 'published'
            ? ($data['published_at'] ?? now())
            : null;

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('blogs', 'public');
        }

        Blog::create($data);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post created successfully.');
    }

    public function edit(Blog $blog): View
    {
        return view('admin.blogs.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog): RedirectResponse
    {
        $data = $this->validatedData($request, $blog);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $blog->title, $blog);
        $data['published_at'] = $data['status'] === 'published'
            ? ($data['published_at'] ?? $blog->published_at ?? now())
            : null;

        if ($request->hasFile('cover_image')) {
            if ($blog->cover_image) {
                Storage::disk('public')->delete($blog->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('blogs', 'public');
        }

        $blog->update($data);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post updated successfully.');
    }

    public function destroy(Blog $blog): RedirectResponse
    {
        if ($blog->cover_image) {
            Storage::disk('public')->delete($blog->cover_image);
        }

        $blog->delete();

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post deleted successfully.');
    }

    private function validatedData(Request $request, ?Blog $blog = null): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|alpha_dash|unique:blogs,slug,' . ($blog?->id ?? 'NULL'),
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string|min:10',
            'cover_image' => 'nullable|image|max:5120',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
        ]);
    }

    private function uniqueSlug(string $value, ?Blog $blog = null): string
    {
        $base = Str::slug($value) ?: 'blog-post';
        $slug = $base;
        $suffix = 2;

        while (Blog::query()
            ->where('slug', $slug)
            ->when($blog, fn ($query) => $query->where('id', '!=', $blog->id))
            ->exists()) {
            $slug = $base . '-' . $suffix++;
        }

        return $slug;
    }
}
