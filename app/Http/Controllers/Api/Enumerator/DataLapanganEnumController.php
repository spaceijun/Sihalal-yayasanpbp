<?php

namespace App\Http\Controllers\Api\Enumerator;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DataLapanganEnumController extends Controller
{
    const STATUS_LIST = [
        'PENDING',
        'REVISI',
        'TERVERIFIKASI',
        'PROGRESS OSS',
        'PROGRESS SIHALAL',
        'TERBIT SH',
    ];

    /**
     * GET /api/enumerator/data-lapangan
     * Query params:
     *   - search  : string (nama_pu)
     *   - status  : PENDING | TERVERIFIKASI | PROGRESS OSS | PROGRESS SIHALAL | TERBIT SH
     *   - per_page: int (default 10)
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'search'   => 'nullable|string|max:255',
            'status'   => 'nullable|string|in:' . implode(',', self::STATUS_LIST),
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $enumeratorId = Auth::user()->enumerator->id;

            $query = DataLapangan::where('enumerator_id', $enumeratorId);

            // Search by nama_pu
            if ($request->filled('search')) {
                $query->where('nama_pu', 'like', '%' . $request->search . '%');
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $data = $query->latest()->paginate($request->get('per_page', 10));

            $data->getCollection()->transform(fn($item) => $this->formatData($item));

            return response()->json([
                'status'  => true,
                'message' => 'Data lapangan berhasil diambil',
                'filters' => [
                    'search'   => $request->search,
                    'status'   => $request->status,
                    'per_page' => $request->get('per_page', 10),
                ],
                'status_options' => self::STATUS_LIST,
                'data'    => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat mengambil data',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/enumerator/data-lapangan/{id}
     * Tampilkan detail satu data
     */
    public function show(int $id): JsonResponse
    {
        try {
            $enumeratorId = Auth::user()->enumerator->id;

            $dataLapangan = DataLapangan::where('id', $id)
                ->where('enumerator_id', $enumeratorId)
                ->firstOrFail();

            return response()->json([
                'status'  => true,
                'message' => 'Detail data lapangan berhasil diambil',
                'data'    => $this->formatData($dataLapangan),
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/enumerator/data-lapangan
     * Simpan data baru
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nama_pu'         => 'required|string|max:255',
            'nik'             => 'required|string|size:16',
            'telephone'       => 'required|string|max:15',
            'nama_produk'     => 'required|string|max:255',
            'alamat'          => 'required|string',
            'foto-ktp'        => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'foto-rumah'      => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'foto-pendamping' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'foto-produk'     => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $enumeratorId = Auth::user()->enumerator->id;

            $foto_ktp_path        = $request->file('foto-ktp')->store('foto-ktp', 'public');
            $foto_rumah_path      = $request->file('foto-rumah')->store('foto-rumah', 'public');
            $foto_pendamping_path = $request->file('foto-pendamping')->store('foto-pendamping', 'public');
            $foto_produk_path     = $request->file('foto-produk')->store('foto-produk', 'public');

            $validatedData = [
                'enumerator_id'        => $enumeratorId,
                'nama_pu'              => $request->nama_pu,
                'nik'                  => $request->nik,
                'telephone'            => $request->telephone,
                'nama_produk'          => $request->nama_produk,
                'alamat'               => $request->alamat,
                'foto_ktp_path'        => $foto_ktp_path,
                'foto_rumah_path'      => $foto_rumah_path,
                'foto_pendamping_path' => $foto_pendamping_path,
                'foto_produk_path'     => $foto_produk_path,
            ];

            $dataLapangan = $this->createData($validatedData);

            return response()->json([
                'status'  => true,
                'message' => 'Data lapangan berhasil disimpan',
                'data'    => $this->formatData($dataLapangan),
            ], 201);
        } catch (\Exception $e) {
            if (isset($foto_ktp_path))        Storage::disk('public')->delete($foto_ktp_path);
            if (isset($foto_rumah_path))       Storage::disk('public')->delete($foto_rumah_path);
            if (isset($foto_pendamping_path))  Storage::disk('public')->delete($foto_pendamping_path);
            if (isset($foto_produk_path))      Storage::disk('public')->delete($foto_produk_path);

            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * PUT/PATCH /api/enumerator/data-lapangan/{id}
     * Update data (foto bersifat opsional)
     * Status otomatis direset ke PENDING setiap kali data diperbarui
     */
    public function update(Request $request, int $id): JsonResponse
    {
        // Validasi foto hanya jika field tersebut berupa file (bukan string URL)
        $fotoRules = [];
        foreach (['foto-ktp', 'foto-rumah', 'foto-pendamping', 'foto-produk'] as $field) {
            if ($request->hasFile($field)) {
                $fotoRules[$field] = 'image|mimes:jpg,jpeg,png|max:2048';
            }
        }

        $validator = Validator::make($request->all(), array_merge([
            'nama_pu'     => 'sometimes|required|string|max:255',
            'nik'         => 'sometimes|required|string|size:16',
            'telephone'   => 'sometimes|required|string|max:15',
            'nama_produk' => 'sometimes|required|string|max:255',
            'alamat'      => 'sometimes|required|string',
        ], $fotoRules));

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $enumeratorId = Auth::user()->enumerator->id;

            $dataLapangan = DataLapangan::where('id', $id)
                ->where('enumerator_id', $enumeratorId)
                ->firstOrFail();

            // Reset status ke PENDING setiap kali data diperbarui
            $dataToUpdate = ['status' => 'PENDING'];

            // Update field teks
            if ($request->has('nama_pu'))     $dataToUpdate['nama_pu']     = strtoupper($request->nama_pu);
            if ($request->has('nik'))         $dataToUpdate['nik']         = $request->nik;
            if ($request->has('telephone'))   $dataToUpdate['telephone']   = $request->telephone;
            if ($request->has('nama_produk')) $dataToUpdate['nama_produk'] = $request->nama_produk;
            if ($request->has('alamat'))      $dataToUpdate['alamat']      = $request->alamat;

            // Mapping: input field (dash) => kolom DB (underscore)
            $fotoFields = [
                'foto-ktp'        => 'foto_ktp',
                'foto-rumah'      => 'foto_rumah',
                'foto-pendamping' => 'foto_pendamping',
                'foto-produk'     => 'foto_produk',
            ];

            $newPaths = [];
            foreach ($fotoFields as $inputKey => $dbColumn) {
                if ($request->hasFile($inputKey)) {
                    $newPaths[$dbColumn] = $request->file($inputKey)->store($inputKey, 'public');
                    $dataToUpdate[$dbColumn] = $newPaths[$dbColumn];
                }
            }

            // Simpan path lama sebelum update
            $oldPaths = [];
            foreach ($newPaths as $dbColumn => $newPath) {
                $oldPaths[$dbColumn] = $dataLapangan->getOriginal($dbColumn);
            }

            $dataLapangan->update($dataToUpdate);

            // Hapus foto lama setelah berhasil update
            foreach ($oldPaths as $dbColumn => $oldPath) {
                if ($oldPath) Storage::disk('public')->delete($oldPath);
            }

            $dataLapangan->refresh();

            return response()->json([
                'status'  => true,
                'message' => 'Data lapangan berhasil diperbarui',
                'data'    => $this->formatData($dataLapangan),
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            foreach ($newPaths ?? [] as $path) {
                Storage::disk('public')->delete($path);
            }
            return response()->json([
                'status'  => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            foreach ($newPaths ?? [] as $path) {
                Storage::disk('public')->delete($path);
            }
            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat memperbarui data',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE /api/enumerator/data-lapangan/{id}
     * Hapus data beserta semua fotonya
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $enumeratorId = Auth::user()->enumerator->id;

            $dataLapangan = DataLapangan::where('id', $id)
                ->where('enumerator_id', $enumeratorId)
                ->firstOrFail();

            // Hapus semua foto dari storage
            $fotoColumns = ['foto_ktp', 'foto_rumah', 'foto_pendamping', 'foto_produk'];
            foreach ($fotoColumns as $column) {
                if ($dataLapangan->$column) {
                    Storage::disk('public')->delete($dataLapangan->$column);
                }
            }

            $dataLapangan->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Data lapangan berhasil dihapus',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat menghapus data',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // ─── Helpers ────────────────────────────────────────────────────────────────

    private function createData(array $validatedData): DataLapangan
    {
        if (isset($validatedData['nama_pu'])) {
            $validatedData['nama_pu'] = strtoupper($validatedData['nama_pu']);
        }

        return DataLapangan::create([
            'enumerator_id'   => $validatedData['enumerator_id'],
            'nama_pu'         => $validatedData['nama_pu'],
            'nik'             => $validatedData['nik'],
            'telephone'       => $validatedData['telephone'],
            'nama_produk'     => $validatedData['nama_produk'],
            'alamat'          => $validatedData['alamat'],
            'foto_ktp'        => $validatedData['foto_ktp_path'],
            'foto_rumah'      => $validatedData['foto_rumah_path'],
            'foto_pendamping' => $validatedData['foto_pendamping_path'],
            'foto_produk'     => $validatedData['foto_produk_path'],
        ]);
    }

    private function formatData(DataLapangan $item): array
    {
        return [
            'id'                  => $item->id,
            'enumerator_id'       => $item->enumerator_id,
            'nama_pu'             => $item->nama_pu,
            'nik'                 => $item->nik,
            'email'               => $item->email,
            'telephone'           => $item->telephone,
            'nama_produk'         => $item->nama_produk,
            'alamat'              => $item->alamat,
            'foto_ktp'            => $item->foto_ktp        ? Storage::url($item->foto_ktp)        : null,
            'foto_rumah'          => $item->foto_rumah      ? Storage::url($item->foto_rumah)      : null,
            'foto_pendamping'     => $item->foto_pendamping ? Storage::url($item->foto_pendamping) : null,
            'foto_produk'         => $item->foto_produk     ? Storage::url($item->foto_produk)     : null,
            'status'              => $item->status,
            'status_pembayaran'   => $item->status_pembayaran,
            'verifikator'         => $item->verifikator,
            'tanggal_verifikasi'  => $item->tanggal_verifikasi,
            'keterangan'          => $item->keterangan,
            'file_oss'            => $item->file_oss     ? Storage::url($item->file_oss)     : null,
            'file_sihalal'        => $item->file_sihalal ? Storage::url($item->file_sihalal) : null,
            'created_at'          => $item->created_at,
            'updated_at'          => $item->updated_at,
        ];
    }
}
