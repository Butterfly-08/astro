<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Horoscope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HoroscopeController extends Controller
{
    public function index(Request $request): View
    {
        $query = Horoscope::query()->orderByDesc('horoscope_date')->orderBy('zodiac_sign');

        if ($request->filled('date')) {
            $query->whereDate('horoscope_date', $request->date);
        }

        if ($request->filled('status') && in_array($request->status, ['draft', 'published'], true)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($query) use ($search) {
                $query->where('zodiac_sign', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            });
        }

        $horoscopes = $query->paginate(20)->withQueryString();

        return view('admin.horoscopes.index', compact('horoscopes'));
    }

    public function create(): View
    {
        $horoscope = new Horoscope([
            'horoscope_date' => today(),
            'status' => 'draft',
        ]);

        return view('admin.horoscopes.create', compact('horoscope'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        Horoscope::create($data);

        return redirect()->route('admin.horoscopes.index')->with('success', 'Horoscope created successfully.');
    }

    public function edit(Horoscope $horoscope): View
    {
        return view('admin.horoscopes.edit', compact('horoscope'));
    }

    public function update(Request $request, Horoscope $horoscope): RedirectResponse
    {
        $horoscope->update($this->validatedData($request, $horoscope));

        return redirect()->route('admin.horoscopes.index')->with('success', 'Horoscope updated successfully.');
    }

    public function destroy(Horoscope $horoscope): RedirectResponse
    {
        $horoscope->delete();

        return redirect()->route('admin.horoscopes.index')->with('success', 'Horoscope deleted successfully.');
    }

    private function validatedData(Request $request, ?Horoscope $horoscope = null): array
    {
        $uniqueRule = \Illuminate\Validation\Rule::unique('horoscopes', 'zodiac_sign')
            ->where(function ($query) use ($request) {
                return $query->whereDate('horoscope_date', $request->input('horoscope_date'));
            });

        if ($horoscope) {
            $uniqueRule->ignore($horoscope->id);
        }

        return $request->validate([
            'zodiac_sign' => ['required', 'in:' . implode(',', Horoscope::ZODIAC_SIGNS), $uniqueRule],
            'horoscope_date' => 'required|date',
            'title' => 'required|string|max:160',
            'content' => 'required|string|min:10',
            'status' => 'required|in:draft,published',
        ]);
    }
}
