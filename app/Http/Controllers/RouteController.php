<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Project;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index()
    {
        $routes = Route::with('project')
            ->latest()
            ->paginate(15);

        return view('routes.index', compact('routes'));
    }

    public function create()
    {
        $projects = Project::where('is_active', true)->get();

        return view('routes.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required',
            'route_name' => 'required',
            'path' => 'required',
            'method' => 'required',
        ]);

        Route::create($request->all());

        return redirect()
            ->route('routes.index')
            ->with('success', 'Route berhasil ditambahkan.');
    }

    public function edit(Route $route)
    {
        $projects = Project::all();

        return view('routes.edit', compact('route', 'projects'));
    }

    public function update(Request $request, Route $route)
    {
        $route->update($request->all());

        return redirect()
            ->route('routes.index')
            ->with('success', 'Route berhasil diupdate.');
    }

    public function destroy(Route $route)
    {
        $route->delete();

        return back()->with('success', 'Route berhasil dihapus.');
    }
}