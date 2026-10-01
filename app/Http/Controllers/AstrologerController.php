<?php

namespace App\Http\Controllers;

use App\Models\Astrologer;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AstrologerController extends Controller
{
    /**
     * Public listing of active astrologers with search & filters.
     */
    public function index(Request $request): View
    {
        $query = Astrologer::active()->with('services');

        // Search by name / specialization / language
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by service category
        if ($request->filled('service')) {
            $query->byService((int) $request->service);
        }

        // Filter by language
        if ($request->filled('language')) {
            $query->byLanguage($request->language);
        }

        // Filter by availability
        if ($request->boolean('available')) {
            $query->available();
        }

        // Sort
        $sort = $request->get('sort', 'rating');
        match ($sort) {
            'experience' => $query->orderByDesc('experience_years'),
            'price_asc'  => $query->orderBy('chat_rate'),
            'price_desc' => $query->orderByDesc('chat_rate'),
            'newest'     => $query->latest(),
            default      => $query->orderByDesc('rating_avg')->orderByDesc('total_consultations'),
        };

        $astrologers = $query->paginate(12)->withQueryString();
        $services    = Service::active()->ordered()->get();

        // Featured astrologers for hero section
        $featured = Astrologer::active()->featured()->available()->take(3)->get();

        return view('astrologers.index', compact('astrologers', 'services', 'featured'));
    }

    /**
     * Show public astrologer profile with tabbed content.
     */
    public function show(string $slug): View
    {
        $astrologer = Astrologer::where('slug', $slug)
            ->where('status', 'active')
            ->with(['services', 'availability' => fn($q) => $q->where('is_active', true)->orderByRaw("CASE day_of_week WHEN 'monday' THEN 1 WHEN 'tuesday' THEN 2 WHEN 'wednesday' THEN 3 WHEN 'thursday' THEN 4 WHEN 'friday' THEN 5 WHEN 'saturday' THEN 6 WHEN 'sunday' THEN 7 ELSE 8 END")])
            ->firstOrFail();

        // Related astrologers (same services, excluding self)
        $related = Astrologer::active()
            ->byService($astrologer->services->first()?->id ?? 0)
            ->where('id', '!=', $astrologer->id)
            ->take(4)
            ->get();

        return view('astrologers.show', compact('astrologer', 'related'));
    }
}
