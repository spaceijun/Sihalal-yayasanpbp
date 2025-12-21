<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DataLapanganController extends Controller
{
    /**
     * Display a listing for web view
     */
    public function index(Request $request)
    {
        $dataLapangans = $this->getDataLapangans($request);
        $i = ($dataLapangans->currentPage() - 1) * $dataLapangans->perPage();

        return view('superadmin.data-lapangan.index', compact('dataLapangans', 'i'));
    }

    /**
     * Show the form for creating a new resource
     */
    public function create()
    {
        return view('superadmin.data-lapangan.create');
    }

    /**
     * Store a newly created resource (from form)
     */
    public function store(Request $request)
    {
        // Validation and store logic

        return redirect()->route('superadmin.data-lapangans.index')
            ->with('success', 'Data berhasil ditambahkan');
    }

    /**
     * Display the specified resource
     */
    public function show(string $id)
    {
        $dataLapangan = DataLapangan::with('enumerator')->findOrFail($id);

        return view('superadmin.data-lapangan.show', compact('dataLapangan'));
    }

    /**
     * Show the form for editing
     */
    public function edit(string $id)
    {
        $dataLapangan = DataLapangan::findOrFail($id);

        return view('superadmin.data-lapangan.edit', compact('dataLapangan'));
    }

    /**
     * Update the specified resource (from form)
     */
    public function update(Request $request, string $id)
    {
        // Validation and update logic

        return redirect()->route('superadmin.data-lapangans.index')
            ->with('success', 'Data berhasil diupdate');
    }

    /**
     * Remove the specified resource (from form)
     */
    public function destroy(string $id)
    {
        $dataLapangan = DataLapangan::findOrFail($id);
        $dataLapangan->delete();

        return redirect()->route('superadmin.data-lapangans.index')
            ->with('success', 'Data berhasil dihapus');
    }

    /*
    |--------------------------------------------------------------------------
    | API METHODS (untuk AJAX request)
    |--------------------------------------------------------------------------
    */

    /**
     * API: Get filtered and paginated data
     */
    public function apiIndex(Request $request)
    {
        try {
            $dataLapangans = $this->getDataLapangans($request);
            $i = ($dataLapangans->currentPage() - 1) * $dataLapangans->perPage();

            // Render partial views
            $tableHtml = view(
                'superadmin.data-lapangan.partials.table-body',
                compact('dataLapangans', 'i')
            )->render();
            $paginationHtml = view(
                'layouts.pagination',
                ['paginator' => $dataLapangans]
            )->render();

            return response()->json([
                'success' => true,
                'table' => $tableHtml,
                'pagination' => $paginationHtml,
                'total' => $dataLapangans->total(),
                'current_page' => $dataLapangans->currentPage(),
                'last_page' => $dataLapangans->lastPage(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get single record
     */
    public function apiShow(string $id)
    {
        try {
            $dataLapangan = DataLapangan::with('enumerator')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $dataLapangan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    /**
     * API: Create new record
     */
    public function apiStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_pu' => 'required|string|max:255',
            'nik' => 'required|string|max:16',
            // Add more validation rules
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $dataLapangan = DataLapangan::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil ditambahkan',
                'data' => $dataLapangan
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Update record
     */
    public function apiUpdate(Request $request, string $id)
    {
        try {
            $dataLapangan = DataLapangan::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nama_pu' => 'sometimes|required|string|max:255',
                'nik' => 'sometimes|required|string|max:16',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $dataLapangan->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diupdate',
                'data' => $dataLapangan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Delete record
     */
    public function apiDestroy(string $id)
    {
        try {
            $dataLapangan = DataLapangan::findOrFail($id);
            $dataLapangan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Bulk delete
     */
    public function apiBulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:data_lapangans,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid IDs provided',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DataLapangan::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' data berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Update status
     */
    public function apiUpdateStatus(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:PENDING,PROGRESS OSS,PROGRESS SIHALAL,TERBIT SH,DITOLAK'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $dataLapangan = DataLapangan::findOrFail($id);
            $dataLapangan->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diupdate',
                'data' => $dataLapangan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Update status payment
     */
    public function apiUpdateStatusPayment(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'status_pembayaran' => 'required|in:PENDING,PENGAJUAN,DIBAYAR'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $dataLapangan = DataLapangan::findOrFail($id);
            $dataLapangan->update(['status_pembayaran' => $request->status_pembayaran]);

            return response()->json([
                'success' => true,
                'message' => 'Status pembayaran berhasil diupdate',
                'data' => $dataLapangan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate status pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Update keterangan
     */
    public function apiUpdateKeterangan(Request $request, string $id)
    {
        try {
            $dataLapangan = DataLapangan::findOrFail($id);
            $dataLapangan->update(['keterangan' => $request->keterangan]);

            return response()->json([
                'success' => true,
                'message' => 'Keterangan berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate keterangan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Upload file
     */
    public function apiUploadFile(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $dataLapangan = DataLapangan::findOrFail($id);

            if ($request->hasFile('file')) {
                // Delete old file if exists
                if ($dataLapangan->file_path && Storage::exists($dataLapangan->file_path)) {
                    Storage::delete($dataLapangan->file_path);
                }

                $path = $request->file('file')->store('data-lapangan-files', 'public');
                $dataLapangan->update(['file_path' => $path]);

                return response()->json([
                    'success' => true,
                    'message' => 'File berhasil diupload',
                    'file_url' => Storage::url($path)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No file uploaded'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Delete file
     */
    public function apiDeleteFile(string $id)
    {
        try {
            $dataLapangan = DataLapangan::findOrFail($id);

            if ($dataLapangan->file_path && Storage::exists($dataLapangan->file_path)) {
                Storage::delete($dataLapangan->file_path);
                $dataLapangan->update(['file_path' => null]);

                return response()->json([
                    'success' => true,
                    'message' => 'File berhasil dihapus'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus file: ' . $e->getMessage()
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Get filtered and paginated data
     */
    private function getDataLapangans(Request $request)
    {
        $query = DataLapangan::with('enumerator');

        if ($request->filled('nama_pu')) {
            $query->where('nama_pu', 'like', '%' . $request->nama_pu . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $query->orderBy('created_at', 'desc');

        return $query->paginate($request->get('per_page', 10));
    }
}
