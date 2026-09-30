<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAstrologerRequest;
use App\Http\Requests\UpdateAstrologerRequest;
use App\Models\Astrologer;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AstrologerController extends Controller
{
    /**
     * Display listing of astrologers.
     */
    public function index(Request $request): View
    {
        $query = Astrologer::with('services')->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by service
        if ($request->filled('service_id')) {
            $query->byService((int) $request->service_id);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $astrologers = $query->paginate(15)->withQueryString();
        $services = Service::active()->ordered()->get();

        $stats = [
            'total'    => Astrologer::count(),
            'pending'  => Astrologer::pending()->count(),
            'active'   => Astrologer::active()->count(),
            'featured' => Astrologer::featured()->count(),
        ];

        return view('admin.astrologers.index', compact('astrologers', 'services', 'stats'));
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        $services = Service::active()->ordered()->get();
        return view('admin.astrologers.create', compact('services'));
    }

    /**
     * Store new astrologer.
     */
    public function store(StoreAstrologerRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')->store('astrologers', 'public');
        }

        // Auto-approve if admin sets status to active
        if (($data['status'] ?? 'pending') === 'active') {
            $data['approved_at'] = now();
            $data['approved_by'] = Auth::guard('admin')->id();
        }

        $astrologer = Astrologer::create($data);

        // Sync services
        if ($request->filled('service_ids')) {
            $astrologer->services()->sync($request->service_ids);
        }

        return redirect()->route('admin.astrologers.show', $astrologer)
            ->with('success', "Astrologer '{$astrologer->display_name}' created successfully.");
    }

    /**
     * Show astrologer profile (admin view with tabs).
     */
    public function show(Astrologer $astrologer): View
    {
        $astrologer->load('services', 'availability', 'approvedBy');
        return view('admin.astrologers.show', compact('astrologer'));
    }

    /**
     * Show edit form.
     */
    public function edit(Astrologer $astrologer): View
    {
        $astrologer->load('services', 'availability');
        $services = Service::active()->ordered()->get();
        $days = \App\Models\AstrologerAvailability::$dayOrder;
        return view('admin.astrologers.edit', compact('astrologer', 'services', 'days'));
    }

    /**
     * Update astrologer.
     */
    public function update(UpdateAstrologerRequest $request, Astrologer $astrologer): RedirectResponse
    {
        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image
            if ($astrologer->profile_image) {
                Storage::disk('public')->delete($astrologer->profile_image);
            }
            $data['profile_image'] = $request->file('profile_image')->store('astrologers', 'public');
        }

        // Handle approval logic
        $previousStatus = $astrologer->status;
        $newStatus = $data['status'] ?? $astrologer->status;

        if ($previousStatus !== 'active' && $newStatus === 'active') {
            $data['approved_at'] = now();
            $data['approved_by'] = Auth::guard('admin')->id();
            $data['rejection_reason'] = null;
        }

        $astrologer->update($data);

        // Sync services
        $astrologer->services()->sync($request->service_ids ?? []);

        // Sync availability schedule
        if ($request->has('availability')) {
            foreach ($request->input('availability', []) as $day => $slotData) {
                $isActive = !empty($slotData['is_active']);
                $startTime = !empty($slotData['start_time']) ? $slotData['start_time'] : '09:00';
                $endTime   = !empty($slotData['end_time'])   ? $slotData['end_time']   : '18:00';

                $astrologer->availability()->updateOrCreate(
                    ['day_of_week' => $day],
                    [
                        'start_time' => $startTime,
                        'end_time'   => $endTime,
                        'is_active'  => $isActive,
                    ]
                );
            }
        }

        return redirect()->route('admin.astrologers.show', $astrologer)
            ->with('success', "Astrologer '{$astrologer->display_name}' updated successfully.");
    }

    /**
     * Delete astrologer.
     */
    public function destroy(Astrologer $astrologer): RedirectResponse
    {
        $name = $astrologer->display_name;

        // Clean up image
        if ($astrologer->profile_image) {
            Storage::disk('public')->delete($astrologer->profile_image);
        }

        $astrologer->delete();

        return redirect()->route('admin.astrologers.index')
            ->with('success', "Astrologer '{$name}' has been deleted.");
    }

    /**
     * Quick approve.
     */
    public function approve(Astrologer $astrologer): RedirectResponse
    {
        $astrologer->update([
            'status'      => 'active',
            'approved_at' => now(),
            'approved_by' => Auth::guard('admin')->id(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', "'{$astrologer->display_name}' has been approved and is now active.");
    }

    /**
     * Reject astrologer with reason.
     */
    public function reject(Request $request, Astrologer $astrologer): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $astrologer->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', "'{$astrologer->display_name}' has been rejected.");
    }

    /**
     * Toggle featured status.
     */
    public function toggleFeatured(Astrologer $astrologer): RedirectResponse
    {
        $astrologer->update(['is_featured' => !$astrologer->is_featured]);
        $state = $astrologer->is_featured ? 'featured' : 'unfeatured';
        return back()->with('success', "'{$astrologer->display_name}' is now {$state}.");
    }

    /**
     * Toggle availability.
     */
    public function toggleAvailability(Astrologer $astrologer): RedirectResponse
    {
        $astrologer->update(['is_available' => !$astrologer->is_available]);
        $state = $astrologer->is_available ? 'available' : 'unavailable';
        return back()->with('success', "'{$astrologer->display_name}' is now {$state}.");
    }
}
