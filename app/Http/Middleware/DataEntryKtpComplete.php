<?php

namespace App\Http\Middleware;

use App\Models\DataEntry;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DataEntryKtpComplete
{
    /**
     * Routes that are always accessible (exempt from KTP check).
     * These are partial path matches (contains).
     */
    protected array $exemptPaths = [
        'data-entry/manajemen-akun',
        'data-entry/dashboard/mark-pengumuman-read',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Only apply to data_entry role
        if (!Auth::check() || Auth::user()->role !== 'data_entry') {
            return $next($request);
        }

        // Skip exempt paths
        foreach ($this->exemptPaths as $path) {
            if ($request->is($path) || $request->is($path . '/*')) {
                return $next($request);
            }
        }

        // Check KTP completeness
        $dataEntry = DataEntry::where('user_id', Auth::id())->first();

        if (!$dataEntry || empty($dataEntry->nik) || empty($dataEntry->nama_lengkap_ktp) || empty($dataEntry->pendidikan_terakhir)) {
            // Allow JSON/API requests to return 403
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Data KTP belum lengkap. Harap lengkapi di Setting Akun.',
                ], 403);
            }

            return redirect()->route('data-entry.manajemen-akun.index')
                ->with('warning', 'Harap lengkapi data KTP dan pendidikan terakhir Anda terlebih dahulu.');
        }

        return $next($request);
    }
}
