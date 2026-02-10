<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use App\Models\Superadmin\Koordinator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class KoorDataLapanganController extends Controller
{
    /**
     * API: Get filtered and paginated data
     */
    public function apiIndex(Request $request): JsonResponse
    {
        try {
            $dataLapangans = $this->getDataLapangans($request);
            $i = $this->calculateStartingIndex($dataLapangans);

            // Render table body menggunakan blade yang sudah ada
            $tableHtml = view('koordinator.data-lapangan.partials.table-body', compact('dataLapangans', 'i'))->render();

            // Render pagination
            $paginationHtml = view('layouts.pagination', ['paginator' => $dataLapangans])->render();

            return $this->successResponse([
                'table' => $tableHtml,
                'pagination' => $paginationHtml,
                'total' => $dataLapangans->total(),
                'current_page' => $dataLapangans->currentPage(),
                'last_page' => $dataLapangans->lastPage(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error in KoorDataLapanganController:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | QUERY METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Get filtered and paginated data
     */
    private function getDataLapangans(Request $request)
    {
        $query = DataLapangan::query();

        // Apply koordinator filter FIRST (melalui relasi enumerator)
        $this->applyKoordinatorFilter($query);

        // Then load relationships
        $query->with(['enumerator', 'spotchecks']);

        // Apply search and filters
        $this->applySearchFilter($query, $request);
        $this->applyStatusFilter($query, $request);
        $this->applyStatusPembayaranFilter($query, $request);
        $this->applyDateFilters($query, $request);

        $query->orderBy('created_at', 'desc');

        return $query->paginate($request->get('per_page', 10));
    }

    /**
     * Apply koordinator filter based on logged in user
     * Filter melalui relasi: data_lapangans -> enumerator -> koordinator
     */
    private function applyKoordinatorFilter($query): void
    {
        $user = Auth::user();

        // If user is koordinator (not superadmin), filter by koordinator_id
        if ($user && $user->role === 'koordinator') {
            try {
                // Cari koordinator_id dari user yang login
                $koordinator = Koordinator::where('user_id', $user->id)->first();

                if ($koordinator) {
                    // Filter data_lapangans berdasarkan enumerator yang punya koordinator_id tertentu
                    $query->whereHas('enumerator', function ($q) use ($koordinator) {
                        $q->where('koordinator_id', $koordinator->id);
                    });
                } else {
                    // Jika user koordinator tapi tidak punya record di tabel koordinators
                    // Return empty result
                    $query->whereRaw('1 = 0');

                    Log::warning('Koordinator tidak ditemukan untuk user:', ['user_id' => $user->id]);
                }
            } catch (\Exception $e) {
                Log::error('Error saat filter koordinator:', [
                    'message' => $e->getMessage(),
                    'user_id' => $user->id
                ]);

                // Return empty result jika error
                $query->whereRaw('1 = 0');
            }
        }
        // If superadmin, show all data (no filter applied)
    }

    /**
     * Apply search filter
     */
    private function applySearchFilter($query, Request $request): void
    {
        if ($request->filled('search')) {
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama_pu', 'like', "%{$searchTerm}%")
                    ->orWhere('nik', 'like', "%{$searchTerm}%")
                    ->orWhereHas('enumerator', function ($subQ) use ($searchTerm) {
                        $subQ->where('nama_lengkap', 'like', "%{$searchTerm}%");
                    });
            });
        }
    }

    /**
     * Apply status filter
     */
    private function applyStatusFilter($query, Request $request): void
    {
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
    }

    /**
     * Apply status pembayaran filter
     */
    private function applyStatusPembayaranFilter($query, Request $request): void
    {
        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }
    }

    /**
     * Apply date filters
     */
    private function applyDateFilters($query, Request $request): void
    {
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }
    }

    /**
     * Calculate starting index for pagination
     */
    private function calculateStartingIndex($paginator): int
    {
        return ($paginator->currentPage() - 1) * $paginator->perPage();
    }

    /*
    |--------------------------------------------------------------------------
    | RESPONSE METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Success response
     */
    private function successResponse(array $data = [], string $message = null, int $statusCode = 200): JsonResponse
    {
        $response = ['success' => true];

        if ($message) {
            $response['message'] = $message;
        }

        return response()->json(array_merge($response, $data), $statusCode);
    }

    /**
     * Error response
     */
    private function errorResponse(string $message, int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $statusCode);
    }
}
