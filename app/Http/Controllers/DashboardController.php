<?php

namespace App\Http\Controllers;

use App\Models\Dashboard;
use App\Models\DashboardWidget;
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

        $dashboard->load(['widgets.savedQuery.dataSource']);

        return Inertia::render('dashboards/Show', [
            'dashboard' => $dashboard,
        ]);
    }

    public function edit(Dashboard $dashboard): Response
    {
        $this->authorize('update', $dashboard);

        $queries = auth()->user()->queries()
            ->with('dataSource')
            ->latest()
            ->get();
            
        $dashboard->load(['widgets.savedQuery.dataSource']);
            
        return Inertia::render('dashboards/Edit', [
            'dashboard' => $dashboard,
            'availableQueries' => $queries,
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

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'dashboard' => $dashboard]);
        }

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

    public function addWidget(Request $request, Dashboard $dashboard)
    {
        $this->authorize('update', $dashboard);

        $validated = $request->validate([
            'query_id' => 'nullable|exists:queries,id',
            'type' => 'required|in:chart,text,metric,table',
            'title' => 'nullable|string|max:255',
            'config' => 'required|array',
            'layout' => 'required|array',
        ]);

        $widget = $dashboard->widgets()->create([
            ...$validated,
            'order' => $dashboard->widgets()->max('order') + 1,
        ]);

        return response()->json([
            'widget' => $widget->load('savedQuery.dataSource'),
        ]);
    }

    public function updateWidget(Request $request, Dashboard $dashboard, DashboardWidget $widget)
    {
        $this->authorize('update', $dashboard);
        
        if ($widget->dashboard_id !== $dashboard->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'config' => 'nullable|array',
            'layout' => 'nullable|array',
        ]);

        $widget->update($validated);

        return response()->json([
            'widget' => $widget->load('savedQuery.dataSource'),
        ]);
    }

    public function deleteWidget(Dashboard $dashboard, DashboardWidget $widget)
    {
        $this->authorize('update', $dashboard);
        
        if ($widget->dashboard_id !== $dashboard->id) {
            abort(403);
        }

        $widget->delete();

        return response()->json(['success' => true]);
    }

    public function reorderWidgets(Request $request, Dashboard $dashboard)
    {
        $this->authorize('update', $dashboard);

        $validated = $request->validate([
            'widgets' => 'required|array',
            'widgets.*.id' => 'required|exists:dashboard_widgets,id',
            'widgets.*.order' => 'required|integer',
        ]);

        foreach ($validated['widgets'] as $widgetData) {
            DashboardWidget::where('id', $widgetData['id'])
                ->where('dashboard_id', $dashboard->id)
                ->update(['order' => $widgetData['order']]);
        }

        return response()->json(['success' => true]);
    }
}
