<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Display all services.
     */
    public function index(Request $request): View
    {
        $query = Service::withCount('astrologers');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $services = $query->ordered()->paginate(20)->withQueryString();

        return view('admin.services.index', compact('services'));
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        return view('admin.services.create');
    }

    /**
     * Store new service.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'              => 'required|string|max:120|unique:services,name',
            'icon'              => 'nullable|string|max:60',
            'short_description' => 'nullable|string|max:255',
            'description'       => 'nullable|string',
            'type'              => 'required|in:consultation,product,both',
            'status'            => 'required|in:active,inactive',
            'sort_order'        => 'nullable|integer|min:0',
            'is_featured'       => 'nullable|boolean',
            'cover_image'       => 'nullable|image|max:2048',
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('services', 'public');
        }

        $service = Service::create($data);

        return redirect()->route('admin.services.index')
            ->with('success', "Service '{$service->name}' created successfully.");
    }

    /**
     * Show edit form.
     */
    public function edit(Service $service): View
    {
        return view('admin.services.edit', compact('service'));
    }

    /**
     * Update service.
     */
    public function update(Request $request, Service $service): RedirectResponse
    {
        $data = $request->validate([
            'name'              => 'required|string|max:120|unique:services,name,' . $service->id,
            'icon'              => 'nullable|string|max:60',
            'short_description' => 'nullable|string|max:255',
            'description'       => 'nullable|string',
            'type'              => 'required|in:consultation,product,both',
            'status'            => 'required|in:active,inactive',
            'sort_order'        => 'nullable|integer|min:0',
            'is_featured'       => 'nullable|boolean',
            'cover_image'       => 'nullable|image|max:2048',
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($request->hasFile('cover_image')) {
            if ($service->cover_image) {
                Storage::disk('public')->delete($service->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('services', 'public');
        }

        $service->update($data);

        return redirect()->route('admin.services.index')
            ->with('success', "Service '{$service->name}' updated successfully.");
    }

    /**
     * Delete service.
     */
    public function destroy(Service $service): RedirectResponse
    {
        $name = $service->name;

        if ($service->cover_image) {
            Storage::disk('public')->delete($service->cover_image);
        }

        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', "Service '{$name}' deleted successfully.");
    }

    /**
     * Toggle status quickly.
     */
    public function toggleStatus(Service $service): RedirectResponse
    {
        $service->update([
            'status' => $service->status === 'active' ? 'inactive' : 'active',
        ]);
        return back()->with('success', "Service '{$service->name}' status updated.");
    }
}
