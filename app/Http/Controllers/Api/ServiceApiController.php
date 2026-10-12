<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

class ServiceApiController extends Controller
{
    /**
     * List all active consultation and astrology services.
     */
    public function index(): JsonResponse
    {
        $services = Service::active()
            ->ordered()
            ->withCount(['astrologers' => function ($query) {
                $query->where('astrologers.status', 'active')
                      ->where('astrologers.approval_status', 'approved');
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $services,
        ]);
    }

    /**
     * Show single service details and available astrologers for it.
     */
    public function show(string $identifier): JsonResponse
    {
        $service = Service::active()
            ->with(['astrologers' => function ($query) {
                $query->where('astrologers.status', 'active')
                      ->where('astrologers.approval_status', 'approved')
                      ->select('astrologers.id', 'astrologers.display_name', 'astrologers.slug', 'astrologers.profile_image', 'astrologers.rating_avg', 'astrologers.total_reviews', 'astrologers.chat_rate', 'astrologers.call_rate', 'astrologers.video_rate');
            }])
            ->where(function ($query) use ($identifier) {
                if (is_numeric($identifier)) {
                    $query->where('id', (int) $identifier);
                }
                $query->orWhere('slug', $identifier);
            })
            ->first();

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $service,
        ]);
    }
}
