<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Astrologer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AstrologerDirectoryApiController extends Controller
{
    /**
     * List active and approved astrologers with filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Astrologer::approved()
            ->active()
            ->with(['services:id,name,slug,icon']);

        if ($request->filled('search')) {
            $query->search(trim($request->input('search')));
        }

        if ($request->filled('service_id')) {
            $query->byService((int) $request->input('service_id'));
        }

        if ($request->filled('language')) {
            $query->byLanguage(trim($request->input('language')));
        }

        if ($request->boolean('available_only')) {
            $query->available();
        }

        if ($request->filled('min_rating')) {
            $query->where('rating_avg', '>=', (float) $request->input('min_rating'));
        }

        // Sorting
        $sort = $request->input('sort', 'rating');
        match ($sort) {
            'rating'     => $query->orderByDesc('rating_avg'),
            'experience' => $query->orderByDesc('experience_years'),
            'price_low'  => $query->orderBy('chat_rate', 'asc'),
            'price_high' => $query->orderBy('chat_rate', 'desc'),
            'popular'    => $query->orderByDesc('total_consultations'),
            default      => $query->orderByDesc('is_featured')->latest(),
        };

        $perPage = min(50, max(1, (int) $request->input('per_page', 12)));
        $astrologers = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $astrologers->items(),
            'meta'    => [
                'current_page' => $astrologers->currentPage(),
                'last_page'    => $astrologers->lastPage(),
                'per_page'     => $astrologers->perPage(),
                'total'        => $astrologers->total(),
            ],
        ]);
    }

    /**
     * List featured astrologers.
     */
    public function featured(): JsonResponse
    {
        $astrologers = Astrologer::approved()
            ->active()
            ->featured()
            ->with(['services:id,name,slug,icon'])
            ->take(6)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $astrologers,
        ]);
    }

    /**
     * Show astrologer profile details.
     */
    public function show(string $identifier): JsonResponse
    {
        $astrologer = Astrologer::approved()
            ->active()
            ->with([
                'services',
                'availability' => fn ($q) => $q->where('is_active', true),
                'reviews' => fn ($q) => $q->where('is_visible', true)->latest()->take(5),
            ])
            ->where(function ($query) use ($identifier) {
                if (is_numeric($identifier)) {
                    $query->where('id', (int) $identifier);
                }
                $query->orWhere('slug', $identifier);
            })
            ->first();

        if (!$astrologer) {
            return response()->json([
                'success' => false,
                'message' => 'Astrologer not found or inactive.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $astrologer,
        ]);
    }

    /**
     * Paginated reviews for an astrologer.
     */
    public function reviews(string $identifier, Request $request): JsonResponse
    {
        $astrologer = Astrologer::where(function ($query) use ($identifier) {
            if (is_numeric($identifier)) {
                $query->where('id', (int) $identifier);
            }
            $query->orWhere('slug', $identifier);
        })->first();

        if (!$astrologer) {
            return response()->json([
                'success' => false,
                'message' => 'Astrologer not found.',
            ], 404);
        }

        $perPage = min(50, max(1, (int) $request->input('per_page', 10)));
        $reviews = $astrologer->reviews()
            ->where('is_visible', true)
            ->with('booking.user:id,first_name,last_name,profile_image')
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $reviews->items(),
            'meta'    => [
                'current_page' => $reviews->currentPage(),
                'last_page'    => $reviews->lastPage(),
                'per_page'     => $reviews->perPage(),
                'total'        => $reviews->total(),
            ],
        ]);
    }
}
