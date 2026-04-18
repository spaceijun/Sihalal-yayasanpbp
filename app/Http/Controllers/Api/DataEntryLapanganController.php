<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    /**
     * Mengunci data agar tidak dapat diedit oleh user lain.
     *
     * @param int $id ID data yang ingin dikunci
     * @return JsonResponse
     */
    // DataEntryLapanganController.php

    public function lockData(int $id): JsonResponse
    {
        $dataLapangan = DataLapangan::findOrFail($id);

        // Jika sedang dikunci orang lain dan belum expired
        if (
            $dataLapangan->is_being_edited &&
            $dataLapangan->edited_by !== Auth::id() &&
            $dataLapangan->edit_expires_at?->isFuture()
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Data sedang dikerjakan oleh pengguna lain.',
            ], 423);
        }

        // Kunci data (boleh re-lock oleh diri sendiri, atau lock expired)
        $dataLapangan->update([
            'is_being_edited' => true,
            'edited_by'       => Auth::id(),
            'edit_expires_at' => now()->addMinutes(50),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dikunci.',
        ]);
    }
    /**
     * Membuka lock pada data agar dapat diedit oleh user lain.
     *
     * Hanya pemilik lock yang bisa unlock data.
     *
     * @param int $id ID data yang ingin dilepas
     * @return JsonResponse
     */
    public function unlockData(int $id): JsonResponse
    {
        DataLapangan::where('id', $id)
            ->where('edited_by', Auth::id()) // Hanya pemilik lock yang bisa unlock
            ->update([
                'is_being_edited' => false,
                'edited_by'       => null,
                'edit_expires_at' => null,
            ]);

        return $this->successResponse([], 'Data dilepas');
    }

    /**
     * Unlock via sendBeacon (dipanggil saat browser ditutup)
     */
    public function unlockBeacon(int $id): JsonResponse
    {
        DataLapangan::where('id', $id)
            ->where('edited_by', Auth::id())
            ->update([
                'is_being_edited' => false,
                'edited_by'       => null,
                'edit_expires_at' => null,
            ]);

        return $this->successResponse([], 'Data dilepas');
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
        $query = DataLapangan::query()->available();

        $user = Auth::user();

        // Filter status berdasarkan entry_type pada tabel data_entrys
        if ($user->hasRole('data_entry')) {
            $dataEntry = $user->dataEntry()->with('koordinators')->first();

            // Tentukan status berdasarkan entry_type
            if ($dataEntry->entry_type === 'SIHALAL') {
                $query->where('status', 'PROGRESS OSS');
            } else {
                // Default: OSS atau entry_type lainnya
                $query->where('status', 'TERVERIFIKASI');
            }

            // Filter berdasarkan koordinator yang di-assign ke user data_entry
            $koordinatorIds = $dataEntry->koordinators->pluck('id');
            $query->whereHas('enumerator', function ($q) use ($koordinatorIds) {
                $q->whereIn('koordinator_id', $koordinatorIds);
            });
        } else {
            // Jika bukan role data_entry, tampilkan status TERVERIFIKASI by default
            $query->where('status', 'TERVERIFIKASI');
        }

        // Load relationships
        $query->with(['enumerator', 'spotchecks', 'koordinator', 'dataEntryProgress']);

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
