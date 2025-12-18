<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiServiceController extends Controller
{
    public function index()
    {
        $services = ApiService::latest()->get();
        return view('admin.api_services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.api_services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:api_services,slug',
            'base_url' => 'nullable|url|max:255',
            'required_fields' => 'required|array|min:1',
            'required_fields.*' => 'required|string|max:50',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        ApiService::create($validated);

        return redirect()->route('admin.api-services.index')->with('success', 'Servicio API creado correctamente.');
    }

    public function edit(ApiService $apiService)
    {
        return view('admin.api_services.edit', compact('apiService'));
    }

    public function update(Request $request, ApiService $apiService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:api_services,slug,' . $apiService->id,
            'base_url' => 'nullable|url|max:255',
            'required_fields' => 'required|array|min:1',
            'required_fields.*' => 'required|string|max:50',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $apiService->update($validated);

        return redirect()->route('admin.api-services.index')->with('success', 'Servicio API actualizado correctamente.');
    }

    public function destroy(ApiService $apiService)
    {
        $apiService->delete();
        return redirect()->route('admin.api-services.index')->with('success', 'Servicio API eliminado.');
    }
}