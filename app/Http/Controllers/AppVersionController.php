<?php

namespace App\Http\Controllers;

use App\Models\AppVersion;
use Illuminate\Http\Request;

class AppVersionController extends Controller
{
    public function check(Request $request)
    {
        $latest = AppVersion::latest()->first();
        $currentBuild = $request->header('X-App-Build') ?? $request->query('build');

        return response()->json([
            'latest_version' => $latest->version,
            'latest_build'   => $latest->build_number,
            'has_update'     => (int)$currentBuild < $latest->build_number,
            'force_update'   => $latest->force_update,
            'changelog'      => $latest->changelog,
            'download_url'   => $latest->download_url,
        ]);
    }
}
