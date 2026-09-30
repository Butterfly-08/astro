<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Display a listing of active consultation services.
     */
    public function index(): View
    {
        $services = Service::active()
            ->ordered()
            ->withCount(['astrologers' => function ($query) {
                $query->where('status', 'active');
            }])
            ->get();

        return view('services.index', compact('services'));
    }

    /**
     * Display a specific service and its available astrologers.
     */
    public function show(string $slug): View
    {
        $service = Service::active()
            ->where('slug', $slug)
            ->withCount(['astrologers' => function ($query) {
                $query->where('status', 'active');
            }])
            ->firstOrFail();

        $astrologers = $service->astrologers()
            ->active()
            ->with('services')
            ->paginate(12);

        $otherServices = Service::active()
            ->where('id', '!=', $service->id)
            ->ordered()
            ->take(6)
            ->get();

        return view('services.show', compact('service', 'astrologers', 'otherServices'));
    }
}
