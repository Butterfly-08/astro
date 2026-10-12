<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Horoscope;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentApiController extends Controller
{
    /**
     * List horoscopes for a specific date (defaults to today) or all signs.
     */
    public function horoscopes(Request $request): JsonResponse
    {
        $date = $request->input('date', today()->toDateString());

        $horoscopes = Horoscope::where('status', 'published')
            ->whereDate('horoscope_date', $date)
            ->get();

        return response()->json([
            'success' => true,
            'date'    => $date,
            'data'    => $horoscopes,
            'signs'   => Horoscope::ZODIAC_SIGNS,
        ]);
    }

    /**
     * Get latest horoscope for a specific zodiac sign.
     */
    public function horoscopeBySign(string $sign, Request $request): JsonResponse
    {
        $sign = ucfirst(strtolower(trim($sign)));
        $date = $request->input('date', today()->toDateString());

        $horoscope = Horoscope::where('status', 'published')
            ->where('zodiac_sign', $sign)
            ->whereDate('horoscope_date', $date)
            ->first();

        // If today's reading not found, fallback to latest published reading for this sign
        if (!$horoscope) {
            $horoscope = Horoscope::where('status', 'published')
                ->where('zodiac_sign', $sign)
                ->latest('horoscope_date')
                ->first();
        }

        if (!$horoscope) {
            return response()->json([
                'success' => false,
                'message' => "No published horoscope found for zodiac sign: {$sign}",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $horoscope,
        ]);
    }

    /**
     * List published blogs with search and pagination.
     */
    public function blogs(Request $request): JsonResponse
    {
        $query = Blog::where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            });

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $perPage = min(50, max(1, (int) $request->input('per_page', 9)));
        $blogs = $query->latest('published_at')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $blogs->items(),
            'meta'    => [
                'current_page' => $blogs->currentPage(),
                'last_page'    => $blogs->lastPage(),
                'per_page'     => $blogs->perPage(),
                'total'        => $blogs->total(),
            ],
        ]);
    }

    /**
     * Show single blog post by slug or ID.
     */
    public function showBlog(string $identifier): JsonResponse
    {
        $blog = Blog::where('status', 'published')
            ->where(function ($query) use ($identifier) {
                if (is_numeric($identifier)) {
                    $query->where('id', (int) $identifier);
                }
                $query->orWhere('slug', $identifier);
            })
            ->first();

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog post not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $blog,
        ]);
    }
}
