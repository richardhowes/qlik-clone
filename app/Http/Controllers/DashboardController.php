<?php

namespace App\Http\Controllers;

use App\Models\Dashboard;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $dashboards = Dashboard::where('user_id', auth()->id())
            ->latest()
            ->paginate(12);

        return Inertia::render('dashboards/Index', [
            'dashboards' => $dashboards,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('dashboards/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $dashboard = Dashboard::create([
            ...$validated,
            'slug' => Str::slug($validated['name']),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('dashboards.edit', $dashboard);
    }

    public function show(Dashboard $dashboard): Response
    {
        $this->authorize('view', $dashboard);

        return Inertia::render('dashboards/Show', [
            'dashboard' => $dashboard->load('charts'),
        ]);
    }

    public function edit(Dashboard $dashboard): Response
    {
        $this->authorize('update', $dashboard);

        return Inertia::render('dashboards/Edit', [
            'dashboard' => $dashboard->load('charts'),
        ]);
    }

    public function update(Request $request, Dashboard $dashboard)
    {
        $this->authorize('update', $dashboard);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'config' => 'nullable|array',
        ]);

        $dashboard->update([
            ...$validated,
            'slug' => Str::slug($validated['name']),
        ]);

        return redirect()->route('dashboards.show', $dashboard);
    }

    public function destroy(Dashboard $dashboard)
    {
        $this->authorize('delete', $dashboard);

        $dashboard->delete();

        return redirect()->route('dashboard');
    }

    public function toggleFavorite(Dashboard $dashboard)
    {
        $this->authorize('update', $dashboard);

        $dashboard->update([
            'is_favorite' => ! $dashboard->is_favorite,
        ]);

        return back();
    }
}
