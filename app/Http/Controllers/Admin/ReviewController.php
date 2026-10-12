<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Astrologer;
use App\Models\AstrologerReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $query = AstrologerReview::query()->with(['booking.user', 'astrologer'])->latest();

        if ($request->filled('visibility') && in_array($request->visibility, ['visible', 'hidden'], true)) {
            $query->where('is_visible', $request->visibility === 'visible');
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($query) use ($search) {
                $query->where('body', 'like', "%{$search}%")
                    ->orWhereHas('astrologer', fn ($astrologers) => $astrologers->where('display_name', 'like', "%{$search}%"))
                    ->orWhereHas('booking.user', fn ($users) => $users->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%"));
            });
        }

        $reviews = $query->paginate(20)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function updateVisibility(AstrologerReview $review): RedirectResponse
    {
        DB::transaction(function () use ($review): void {
            $review = AstrologerReview::query()->lockForUpdate()->findOrFail($review->id);
            $review->update(['is_visible' => ! $review->is_visible]);

            $astrologer = Astrologer::query()
                ->whereKey($review->astrologer_id)
                ->lockForUpdate()
                ->firstOrFail();
            $aggregate = AstrologerReview::query()
                ->where('astrologer_id', $astrologer->id)
                ->where('is_visible', true)
                ->selectRaw('COUNT(*) as review_count, AVG(rating) as rating_average')
                ->first();

            $astrologer->update([
                'total_reviews' => (int) $aggregate->review_count,
                'rating_avg' => round((float) ($aggregate->rating_average ?? 0), 2),
            ]);
        });

        return back()->with('success', 'Review visibility updated.');
    }
}
