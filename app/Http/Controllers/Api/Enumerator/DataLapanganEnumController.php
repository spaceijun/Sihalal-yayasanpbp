<?php

namespace App\Http\Controllers\Api\Enumerator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Enumerator\DataLapanganEnumStoreRequest;
use App\Http\Requests\Enumerator\DataLapanganEnumUpdateRequest;
use App\Services\Enumerator\DataLapanganEnumService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * DataLapanganEnumController
 *
 * Endpoint API Data Lapangan untuk aplikasi Flutter Enumerator. Controller ini sengaja dibuat
 * tipis — seluruh business logic (upload/hapus file, format response) ada di
 * App\Services\Enumerator\DataLapanganEnumService, validasi ada di
 * App\Http\Requests\Enumerator\DataLapanganEnum{Store,Update}Request — lihat
 * .agent/workflows/data-lapangan-enumerator-api.md §2 untuk latar belakang refaktor ini.
 *
 * Kontrak response (key `status`/`message`/`data`/`errors`, kode HTTP) dipertahankan PERSIS sama
 * dengan sebelum refaktor — dikonsumsi app Flutter yang sudah live, lihat catatan di §2 dokumen
 * yang sama soal kenapa endpoint ini TIDAK dipindah ke trait ApiResponses (`success` vs `status`).
 */
class DataLapanganEnumController extends Controller
{
    public function __construct(private DataLapanganEnumService $service) {}

    /**
     * GET /api/enumerator/data-lapangan
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:'.implode(',', DataLapanganEnumService::STATUS_LIST),
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $enumerator = Auth::user()->enumerator;
            $perPage = $request->get('per_page', 10);

            $data = $this->service->paginate($enumerator, [
                'search' => $request->search,
                'status' => $request->status,
            ], $perPage);

            return response()->json([
                'status' => true,
                'message' => 'Data lapangan berhasil diambil',
                'filters' => [
                    'search' => $request->search,
                    'status' => $request->status,
                    'per_page' => $perPage,
                ],
                'status_options' => DataLapanganEnumService::STATUS_LIST,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengambil data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/enumerator/data-lapangan/{id}
     */
    public function show(int $id): JsonResponse
    {
        try {
            $dataLapangan = $this->service->findOwned($id, Auth::user()->enumerator);

            return response()->json([
                'status' => true,
                'message' => 'Detail data lapangan berhasil diambil',
                'data' => $this->service->format($dataLapangan),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/enumerator/data-lapangan
     * Enumerator hanya bisa store jika statusnya 'Aktif'.
     */
    public function store(DataLapanganEnumStoreRequest $request): JsonResponse
    {
        $enumerator = Auth::user()->enumerator;

        if ($guardPayload = $this->service->assertEnumeratorAktif($enumerator)) {
            return response()->json([
                'status' => false,
                'message' => 'Anda tidak dapat mengajukan data lapangan karena akun enumerator Anda tidak aktif. Silakan hubungi koordinator.',
                'data' => $guardPayload,
            ], 403);
        }

        try {
            $dataLapangan = $this->service->create($enumerator, $request->validated());

            return response()->json([
                'status' => true,
                'message' => 'Data lapangan berhasil disimpan',
                'data' => $this->service->format($dataLapangan),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * PUT/PATCH /api/enumerator/data-lapangan/{id}
     * Status otomatis direset ke PENDING setiap kali data diperbarui.
     */
    public function update(DataLapanganEnumUpdateRequest $request, int $id): JsonResponse
    {
        try {
            $dataLapangan = $this->service->findOwned($id, Auth::user()->enumerator);
            $dataLapangan = $this->service->update($dataLapangan, $request->validated());

            return response()->json([
                'status' => true,
                'message' => 'Data lapangan berhasil diperbarui',
                'data' => $this->service->format($dataLapangan),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE /api/enumerator/data-lapangan/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $dataLapangan = $this->service->findOwned($id, Auth::user()->enumerator);
            $this->service->delete($dataLapangan);

            return response()->json([
                'status' => true,
                'message' => 'Data lapangan berhasil dihapus',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menghapus data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
