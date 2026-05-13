<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DataLapanganController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | WEB VIEW METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Display a listing for web view
     */
    public function index(Request $request)
    {
        $dataLapangans = $this->getDataLapangans($request);
        $i = $this->calculateStartingIndex($dataLapangans);

        return view('superadmin.data-lapangan.index', compact('dataLapangans', 'i'));
    }



    /*
    |--------------------------------------------------------------------------
    | API METHODS - DATA RETRIEVAL
    |--------------------------------------------------------------------------
    */

    /**
     * API: Get filtered and paginated data
     */
    public function apiIndex(Request $request): JsonResponse
    {
        try {
            $dataLapangans = $this->getDataLapangans($request);
            $i = $this->calculateStartingIndex($dataLapangans);

            $tableHtml = $this->renderTableBody($dataLapangans, $i);
            $paginationHtml = $this->renderPagination($dataLapangans);

            return $this->successResponse([
                'table' => $tableHtml,
                'pagination' => $paginationHtml,
                'total' => $dataLapangans->total(),
                'current_page' => $dataLapangans->currentPage(),
                'last_page' => $dataLapangans->lastPage(),
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * API: Get single record
     */
    public function apiShow(string $id): JsonResponse
    {
        try {
            $dataLapangan = DataLapangan::with('enumerator')->findOrFail($id);

            return $this->successResponse(['data' => $dataLapangan]);
        } catch (\Exception $e) {
            return $this->errorResponse('Data tidak ditemukan', 404);
        }
    }

    /**
     * Check if NIK exists in database
     */
    public function checkNik(Request $request): JsonResponse
    {
        $request->validate([
            'nik' => 'required|string|size:16'
        ]);

        $existingData = DataLapangan::where('nik', $request->nik)->first();

        if ($existingData) {
            return response()->json([
                'exists' => true,
                'nama_pu' => $existingData->nama_pu ?? 'Pengguna lain',
                'message' => 'NIK sudah terdaftar'
            ]);
        }

        return response()->json([
            'exists' => false,
            'message' => 'NIK tersedia'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | API METHODS - DATA MANIPULATION
    |--------------------------------------------------------------------------
    */

    /**
     * API: Create new record
     */
    public function apiStore(Request $request): JsonResponse
    {
        $validator = $this->validateStoreRequest($request);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $dataLapangan = DataLapangan::create($request->all());

            return $this->successResponse(
                ['data' => $dataLapangan],
                'Data berhasil ditambahkan',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal menambahkan data: ' . $e->getMessage(), 500);
        }
    }

    /**
     * API: Update record
     */
    public function apiUpdate(Request $request, string $id): JsonResponse
    {
        try {
            $dataLapangan = DataLapangan::findOrFail($id);

            $validator = $this->validateUpdateRequest($request);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator);
            }

            $dataLapangan->update($request->all());

            return $this->successResponse(
                ['data' => $dataLapangan],
                'Data berhasil diupdate'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengupdate data: ' . $e->getMessage(), 500);
        }
    }

    /**
     * API: Delete record
     */
    public function apiDestroy(string $id): JsonResponse
    {
        try {
            $dataLapangan = DataLapangan::findOrFail($id);
            $dataLapangan->delete();

            return $this->successResponse([], 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal menghapus data: ' . $e->getMessage(), 500);
        }
    }

    /**
     * API: Bulk delete
     */
    public function apiBulkDelete(Request $request): JsonResponse
    {
        $validator = $this->validateBulkDeleteRequest($request);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $deletedCount = DataLapangan::whereIn('id', $request->ids)->delete();

            return $this->successResponse(
                [],
                "{$deletedCount} data berhasil dihapus"
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal menghapus data: ' . $e->getMessage(), 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | API METHODS - STATUS UPDATES
    |--------------------------------------------------------------------------
    */

    /**
     * API: Update status
     */
    public function apiUpdateStatus(Request $request, string $id): JsonResponse
    {
        $validator = $this->validateStatusRequest($request);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $dataLapangan = DataLapangan::findOrFail($id);
            $dataLapangan->update(['status' => $request->status]);

            return $this->successResponse(
                ['data' => $dataLapangan],
                'Status berhasil diupdate'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengupdate status: ' . $e->getMessage(), 500);
        }
    }

    /**
     * API: Update status payment
     */
    public function apiUpdateStatusPayment(Request $request, string $id): JsonResponse
    {
        $validator = $this->validateStatusPaymentRequest($request);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $dataLapangan = DataLapangan::findOrFail($id);
            $dataLapangan->update(['status_pembayaran' => $request->status_pembayaran]);

            return $this->successResponse(
                ['data' => $dataLapangan],
                'Status pembayaran berhasil diupdate'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengupdate status pembayaran: ' . $e->getMessage(), 500);
        }
    }

    /**
     * API: Update keterangan
     */
    public function apiUpdateKeterangan(Request $request, string $id): JsonResponse
    {
        try {
            $dataLapangan = DataLapangan::findOrFail($id);
            $dataLapangan->update(['keterangan' => $request->keterangan]);

            return $this->successResponse([], 'Keterangan berhasil diupdate');
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengupdate keterangan: ' . $e->getMessage(), 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | API METHODS - FILE MANAGEMENT
    |--------------------------------------------------------------------------
    */

    /**
     * API: Upload file
     */
    public function apiUploadFile(Request $request, string $id): JsonResponse
    {
        $validator = $this->validateFileUploadRequest($request);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $dataLapangan = DataLapangan::findOrFail($id);

            if (!$request->hasFile('file')) {
                return $this->errorResponse('No file uploaded', 400);
            }

            $this->deleteOldFile($dataLapangan);
            $path = $this->storeFile($request->file('file'));
            $dataLapangan->update(['file_path' => $path]);

            return $this->successResponse(
                ['file_url' => Storage::url($path)],
                'File berhasil diupload'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengupload file: ' . $e->getMessage(), 500);
        }
    }

    /**
     * API: Delete file
     */
    public function apiDeleteFile(string $id): JsonResponse
    {
        try {
            $dataLapangan = DataLapangan::findOrFail($id);

            if (!$dataLapangan->file_path || !Storage::exists($dataLapangan->file_path)) {
                return $this->errorResponse('File tidak ditemukan', 404);
            }

            Storage::delete($dataLapangan->file_path);
            $dataLapangan->update(['file_path' => null]);

            return $this->successResponse([], 'File berhasil dihapus');
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal menghapus file: ' . $e->getMessage(), 500);
        }
    }

    /*
|--------------------------------------------------------------------------
| API METHODS - LOCK MANAGEMENT (Superadmin)
|--------------------------------------------------------------------------
*/

    /**
     * Force unlock data (hanya superadmin)
     */
    public function apiForceUnlock(string $id): JsonResponse
    {
        try {
            DataLapangan::where('id', $id)->update([
                'is_being_edited' => false,
                'edited_by'       => null,
                'edit_expires_at' => null,
            ]);

            return $this->successResponse([], 'Data berhasil di-unlock');
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal unlock: ' . $e->getMessage(), 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Validate store request
     */
    private function validateStoreRequest(Request $request)
    {
        return Validator::make($request->all(), [
            'nama_pu' => 'required|string|max:255',
            'nik' => 'required|string|max:16',
            // Add more validation rules
        ]);
    }

    /**
     * Validate update request
     */
    private function validateUpdateRequest(Request $request)
    {
        return Validator::make($request->all(), [
            'nama_pu' => 'sometimes|required|string|max:255',
            'nik' => 'sometimes|required|string|max:16',
        ]);
    }

    /**
     * Validate bulk delete request
     */
    private function validateBulkDeleteRequest(Request $request)
    {
        return Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:data_lapangans,id'
        ]);
    }

    /**
     * Validate status request
     */
    private function validateStatusRequest(Request $request)
    {
        return Validator::make($request->all(), [
            'status' => 'required|in:PENDING,TERVERIFIKASI,PROGRESS OSS,PROGRESS SIHALAL,TERBIT SH,DITOLAK'
        ]);
    }

    /**
     * Validate status payment request
     */
    private function validateStatusPaymentRequest(Request $request)
    {
        return Validator::make($request->all(), [
            'status_pembayaran' => 'required|in:PENDING,PENGAJUAN,DIBAYAR'
        ]);
    }

    /**
     * Validate file upload request
     */
    private function validateFileUploadRequest(Request $request)
    {
        return Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);
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
        $query = DataLapangan::with(['enumerator', 'spotchecks', 'editedBy']);
        // Superadmin melihat SEMUA data termasuk yang sedang dikunci
        // Tidak perlu ->available() scope di sini

        $this->applySearchFilter($query, $request);
        $this->applyStatusFilter($query, $request);
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
                    ->orWhere('nama_produk', 'like', "%{$searchTerm}%")
                    ->orWhere('no_registrasi', 'like', "%{$searchTerm}%") // ← tambahkan ini
                    ->orWhereHas('enumerator', function ($subQ) use ($searchTerm) {
                        $subQ->where('nama_lengkap', 'like', "%{$searchTerm}%");
                    });
            });
        }
    }
    /**
     * Apply status filter
     */
    // SESUDAH
    private function applyStatusFilter($query, Request $request): void
    {
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

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

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate starting index for pagination
     */
    private function calculateStartingIndex($paginator): int
    {
        return ($paginator->currentPage() - 1) * $paginator->perPage();
    }

    /**
     * Render table body
     */
    private function renderTableBody($dataLapangans, int $i): string
    {
        return view(
            'superadmin.data-lapangan.partials.table-body',
            compact('dataLapangans', 'i')
        )->render();
    }

    /**
     * Render pagination
     */
    private function renderPagination($paginator): string
    {
        return view('layouts.pagination', ['paginator' => $paginator])->render();
    }

    /**
     * Delete old file if exists
     */
    private function deleteOldFile(DataLapangan $dataLapangan): void
    {
        if ($dataLapangan->file_path && Storage::exists($dataLapangan->file_path)) {
            Storage::delete($dataLapangan->file_path);
        }
    }

    /**
     * Store uploaded file
     */
    private function storeFile($file): string
    {
        return $file->store('data-lapangan-files', 'public');
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

    /**
     * Validation error response
     */
    private function validationErrorResponse($validator): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $validator->errors()
        ], 422);
    }
}
