<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Traits\HasRoutePrefix;
use App\Models\AppVersion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\AppVersionRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AppVersionController extends Controller
{
    use HasRoutePrefix;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $appVersions = AppVersion::paginate();

        $routePrefix = $this->routePrefix();

        return view('superadmin.app-version.index', compact('appVersions', 'routePrefix'))
            ->with('i', ($request->input('page', 1) - 1) * $appVersions->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $appVersion = new AppVersion();

        $routePrefix = $this->routePrefix();

        return view('superadmin.app-version.create', compact('appVersion', 'routePrefix'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AppVersionRequest $request): RedirectResponse
    {
        AppVersion::create($request->validated());

        return Redirect::route('superadmin.app-versions.index')
            ->with('success', 'AppVersion created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($hashedId): View
    {
        $appVersion = AppVersion::findByHashedIdOrFail($hashedId);

        $routePrefix = $this->routePrefix();

        return view('superadmin.app-version.show', compact('appVersion', 'routePrefix'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($hashedId): View
    {
        $appVersion = AppVersion::findByHashedIdOrFail($hashedId);

        $routePrefix = $this->routePrefix();

        return view('superadmin.app-version.edit', compact('appVersion', 'routePrefix'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AppVersionRequest $request, AppVersion $appVersion): RedirectResponse
    {
        $appVersion->update($request->validated());

        return Redirect::route('superadmin.app-versions.index')
            ->with('success', 'AppVersion updated successfully');
    }

    public function destroy($hashedId): RedirectResponse
    {
        AppVersion::findByHashedIdOrFail($hashedId)->delete();

        return Redirect::route('superadmin.app-versions.index')
            ->with('success', 'AppVersion deleted successfully');
    }
}
