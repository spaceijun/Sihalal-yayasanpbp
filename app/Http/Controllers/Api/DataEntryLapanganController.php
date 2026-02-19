<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DataEntryLapanganController extends Controller
{
    /**
     * API: Get filtered and paginated data
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $dataLapangans = $this->getDataLapangans($request);
            $i = $this->calculateStartingIndex($dataLapangans);

            // Render table body menggunakan blade yang sudah ada
            $tableHtml = view('data-entry.data-lapangan.partials.table-body', compact('dataLapangans', 'i'))->render();

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
            Log::error('Error in DataEntryLapanganController:', [
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
     * Hanya menampilkan data dengan status Terverifikasi,
     * tanpa memandang user yang sedang login.
     */
    private function getDataLapangans(Request $request)
    {
        $query = DataLapangan::query();

        // Hanya tampilkan data dengan status Terverifikasi
        $query->where('status', 'TERVERIFIKASI');

        // Load relationships
        $query->with(['enumerator', 'spotchecks']);

        // Apply search and filters
        $this->applySearchFilter($query, $request);
        $this->applyStatusPembayaranFilter($query, $request);
        $this->applyDateFilters($query, $request);

        $query->orderBy('created_at', 'desc');

        return $query->paginate($request->get('per_page', 10));
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
