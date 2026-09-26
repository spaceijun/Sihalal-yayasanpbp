<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppVersionRequest;
use App\Models\AppVersion;
use App\Services\Superadmin\AppVersionService;
use App\Traits\HasRoutePrefix;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AppVersionController extends Controller
{
    use HasRoutePrefix;

    public function __construct(private AppVersionService $service) {}

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
        try {
            $this->service->store($request->validated());

            return Redirect::route($this->routePrefix().'.app-versions.index')
                ->with('success', 'AppVersion berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan versi aplikasi: '.$e->getMessage())->withInput();
        }
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
        try {
            $this->service->update($appVersion, $request->validated());

            return Redirect::route($this->routePrefix().'.app-versions.index')
                ->with('success', 'AppVersion berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui versi aplikasi: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($hashedId): JsonResponse
    {
        $appVersion = AppVersion::findByHashedIdOrFail($hashedId);

        try {
            $this->service->delete($appVersion);

            return response()->json(['message' => 'AppVersion berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
