<?php

namespace App\Http\Controllers;

use App\Models\Astrologer;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show the AstroVani Home Page with dynamic featured content.
     */
    public function index(): View
    {
        $data = Cache::remember('home_page_data_v3', 180, function () {
            // Featured Astrologers
            $featuredAstrologers = collect();
            $totalAstrologersCount = 0;
            $totalConsultationsCount = 0;

            try {
                $featuredAstrologers = Astrologer::active()
                    ->featured()
                    ->with('services')
                    ->take(4)
                    ->get();

                if ($featuredAstrologers->count() < 4) {
                    $needed = 4 - $featuredAstrologers->count();
                    $additional = Astrologer::active()
                        ->whereNotIn('id', $featuredAstrologers->pluck('id'))
                        ->orderByDesc('rating_avg')
                        ->with('services')
                        ->take($needed)
                        ->get();
                    $featuredAstrologers = $featuredAstrologers->merge($additional);
                }

                $totalAstrologersCount = Astrologer::active()->count();
                $totalConsultationsCount = (int) Astrologer::active()->sum('total_consultations');
            } catch (\Throwable $e) {
                // Astrologers table query fallback
            }

            // Active Services with astrologer counts
            $services = collect();
            try {
                $services = Service::active()
                    ->ordered()
                    ->withCount(['astrologers' => function ($q) {
                        $q->where('status', 'active');
                    }])
                    ->take(8)
                    ->get();
            } catch (\Throwable $e) {
                // Services query fallback
            }

            // Featured Products & E-Commerce Categories
            $featuredProducts = collect();
            $shopCategories = collect();
            try {
                $featuredProducts = \App\Models\Product::active()
                    ->featured()
                    ->with('category')
                    ->take(4)
                    ->get();

                if ($featuredProducts->count() < 4) {
                    $needed = 4 - $featuredProducts->count();
                    $additional = \App\Models\Product::active()
                        ->whereNotIn('id', $featuredProducts->pluck('id'))
                        ->orderByDesc('rating_avg')
                        ->with('category')
                        ->take($needed)
                        ->get();
                    $featuredProducts = $featuredProducts->merge($additional);
                }

                $shopCategories = \App\Models\ProductCategory::active()
                    ->featured()
                    ->ordered()
                    ->withCount('activeProducts')
                    ->take(6)
                    ->get();
            } catch (\Throwable $e) {
                // Products query fallback
            }

            return [
                'featuredAstrologers' => $featuredAstrologers,
                'services' => $services,
                'totalAstrologersCount' => $totalAstrologersCount,
                'totalConsultationsCount' => $totalConsultationsCount,
                'featuredProducts' => $featuredProducts,
                'shopCategories' => $shopCategories,
            ];
        });

        return view('home', $data);
    }
}
