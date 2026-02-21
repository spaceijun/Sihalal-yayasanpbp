<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataEntryProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DataEntryProgressController extends Controller
{
    /**
     * API: Get filtered and paginated data entry progress
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $progresses = $this->getDataEntryProgress($request);
            $i = $this->calculateStartingIndex($progresses);

            $tableHtml = view('data-entry.progress.partials.table-body', compact('progresses', 'i'))->render();
            $paginationHtml = view('layouts.pagination', ['paginator' => $progresses])->render();

            return $this->successResponse([
                'table'        => $tableHtml,
                'pagination'   => $paginationHtml,
                'total'        => $progresses->total(),
                'current_page' => $progresses->currentPage(),
                'last_page'    => $progresses->lastPage(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error in DataEntryProgressController:', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
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
     * Get data entry progress milik user data_entry yang sedang login
     * Hanya menampilkan data_lapangan dengan status PROGRESS OSS
     */
    private function getDataEntryProgress(Request $request)
    {
        $user = Auth::user();

        // Pastikan user memiliki role data_entry
        if (!$user->hasRole('data_entry')) {
            abort(403, 'Unauthorized');
        }

        $query = DataEntryProgress::query()
            ->where('user_id', $user->id)
            ->whereHas('dataLapangan', function ($q) {
                // Hanya tampilkan yang status PROGRESS OSS
                $q->where('status', 'PROGRESS OSS');
            })
            ->with([
                'user',
                'dataEntry',
                'dataLapangan',
                'dataLapangan.enumerator',
                'dataLapangan.koordinator',
            ]);

        // Apply filters
        $this->applySearchFilter($query, $request);
        $this->applyActionFilter($query, $request);
        $this->applyDateFilters($query, $request);

        $query->orderBy('actioned_at', 'desc');

        return $query->paginate($request->get('per_page', 10));
    }
    /**
     * Apply search filter (berdasarkan nama_pu atau NIK di data_lapangan)
     */
    private function applySearchFilter($query, Request $request): void
    {
        if ($request->filled('search')) {
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('dataLapangan', function ($sub) use ($searchTerm) {
                    $sub->where('nama_pu', 'like', "%{$searchTerm}%")
                        ->orWhere('nik', 'like', "%{$searchTerm}%");
                });
            });
        }
    }

    /**
     * Apply filter berdasarkan kolom action (e.g. 'create', 'update', 'delete')
     */
    private function applyActionFilter($query, Request $request): void
    {
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
    }

    /**
     * Apply date filters berdasarkan actioned_at
     */
    private function applyDateFilters($query, Request $request): void
    {
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('actioned_at', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('actioned_at', '<=', $request->tanggal_sampai);
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

    private function successResponse(array $data = [], string $message = null, int $statusCode = 200): JsonResponse
    {
        $response = ['success' => true];

        if ($message) {
            $response['message'] = $message;
        }

        return response()->json(array_merge($response, $data), $statusCode);
    }

    private function errorResponse(string $message, int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $statusCode);
    }
}
