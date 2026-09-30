<?php

namespace App\Http\Controllers;

use App\Models\Astrologer;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show the AstroVani Home Page with dynamic featured content.
     */
    public function index(): View
    {
        $hasAstrologers = Schema::hasTable('astrologers');
        $hasServices = Schema::hasTable('services');

        // Featured Astrologers
        $featuredAstrologers = collect();
        $totalAstrologersCount = 0;
        $totalConsultationsCount = 0;

        if ($hasAstrologers) {
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
            $totalConsultationsCount = Astrologer::active()->sum('total_consultations');
        }

        // Active Services with astrologer counts
        $services = collect();
        if ($hasServices) {
            $services = Service::active()
                ->ordered()
                ->withCount(['astrologers' => function ($q) {
                    $q->where('status', 'active');
                }])
                ->take(8)
                ->get();
        }

        // Phase 4: Featured Products & E-Commerce Categories
        $featuredProducts = collect();
        $shopCategories = collect();
        if (Schema::hasTable('products') && Schema::hasTable('product_categories')) {
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
        }

        return view('home', compact(
            'featuredAstrologers',
            'services',
            'totalAstrologersCount',
            'totalConsultationsCount',
            'featuredProducts',
            'shopCategories'
        ));
    }
}
