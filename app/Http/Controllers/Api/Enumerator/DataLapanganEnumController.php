<?php

namespace App\Http\Controllers\Api\Enumerator;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use Carbon\Carbon;
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

    // Mapping input field (dengan dash) → kolom database (dengan underscore)
    const FOTO_FIELDS = [
        'foto-ktp'        => 'foto_ktp',
        'foto-rumah'      => 'foto_rumah',
        'foto-pendamping' => 'foto_pendamping',
        'foto-produk'     => 'foto_produk',
        'foto-produk-2'   => 'foto_produk_2',
        'foto-produk-3'   => 'foto_produk_3',
        'foto-produk-4'   => 'foto_produk_4',
        'foto-produk-5'   => 'foto_produk_5',
    ];

    /**
     * GET /api/enumerator/data-lapangan
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

            if ($request->filled('search')) {
                $query->where('nama_pu', 'like', '%' . $request->search . '%');
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $data = $query->latest()->paginate($request->get('per_page', 10));
            $data->getCollection()->transform(fn($item) => $this->formatData($item));

            return response()->json([
                'status'         => true,
                'message'        => 'Data lapangan berhasil diambil',
                'filters'        => [
                    'search'   => $request->search,
                    'status'   => $request->status,
                    'per_page' => $request->get('per_page', 10),
                ],
                'status_options' => self::STATUS_LIST,
                'data'           => $data,
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
     * Enumerator hanya bisa store jika statusnya 'Aktif'.
     */
    public function store(Request $request): JsonResponse
    {
        // ── Guard: cek status enumerator ─────────────────────────────────────────
        $enumerator = Auth::user()->enumerator;

        if (! $enumerator || $enumerator->status === 'Tidak Aktif') {
            $jumlah30Hari = $enumerator
                ? $enumerator->dataLapangans()
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->count()
                : 0;

            return response()->json([
                'status'  => false,
                'message' => 'Anda tidak dapat mengajukan data lapangan karena akun enumerator Anda tidak aktif. Silakan hubungi koordinator.',
                'data'    => [
                    'status_enumerator'   => $enumerator?->status ?? 'Tidak Ditemukan',
                    'jumlah_data_30_hari' => $jumlah30Hari,
                    'minimal_required'    => 20,
                ],
            ], 403);
        }
        // ─────────────────────────────────────────────────────────────────────────

        $validator = Validator::make($request->all(), [
            // Data wajib
            'nama_pu'         => 'required|string|max:255',
            'nik'             => 'required|string|size:16',
            'telephone'       => 'required|string|max:15',
            'nama_produk'     => 'required|string|max:255',
            'alamat'          => 'required|string',
            'foto-ktp'        => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'foto-rumah'      => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'foto-pendamping' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'foto-produk'     => 'required|image|mimes:jpg,jpeg,png|max:2048',

            // NIB
            'has_nib'         => 'required|in:true,false,1,0',
            'file_oss'        => 'nullable|file|mimes:pdf|max:5120|required_if:has_nib,true,has_nib,1',

            // Produk tambahan (opsional)
            'nama_produk_2'   => 'nullable|string|max:255',
            'nama_produk_3'   => 'nullable|string|max:255',
            'nama_produk_4'   => 'nullable|string|max:255',
            'nama_produk_5'   => 'nullable|string|max:255',

            // Foto produk tambahan — wajib jika nama produk yang bersangkutan diisi
            'foto-produk-2'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048|required_with:nama_produk_2',
            'foto-produk-3'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048|required_with:nama_produk_3',
            'foto-produk-4'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048|required_with:nama_produk_4',
            'foto-produk-5'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048|required_with:nama_produk_5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $uploadedPaths = [];

        try {
            // Parse has_nib lebih awal agar bisa dipakai di createData
            $hasNib = filter_var($request->has_nib, FILTER_VALIDATE_BOOLEAN);

            // Upload foto wajib
            $uploadedPaths['foto_ktp']       = $request->file('foto-ktp')->store('foto-ktp', 'public');
            $uploadedPaths['foto_rumah']      = $request->file('foto-rumah')->store('foto-rumah', 'public');
            $uploadedPaths['foto_pendamping'] = $request->file('foto-pendamping')->store('foto-pendamping', 'public');
            $uploadedPaths['foto_produk']     = $request->file('foto-produk')->store('foto-produk', 'public');

            // Upload foto produk tambahan jika ada
            foreach (
                [
                    'foto-produk-2' => 'foto_produk_2',
                    'foto-produk-3' => 'foto_produk_3',
                    'foto-produk-4' => 'foto_produk_4',
                    'foto-produk-5' => 'foto_produk_5',
                ] as $inputKey => $dbColumn
            ) {
                if ($request->hasFile($inputKey)) {
                    $uploadedPaths[$dbColumn] = $request->file($inputKey)->store('foto-produk', 'public');
                }
            }

            // Upload file OSS hanya jika has_nib = true
            if ($hasNib && $request->hasFile('file_oss')) {
                $uploadedPaths['file_oss'] = $request->file('file_oss')->store('files/oss', 'public');
            }

            $dataLapangan = $this->createData(array_merge([
                'enumerator_id' => $enumerator->id,
                'nama_pu'       => $request->nama_pu,
                'nik'           => $request->nik,
                'telephone'     => $request->telephone,
                'nama_produk'   => $request->nama_produk,
                'alamat'        => $request->alamat,
                'has_nib'       => $hasNib,  // ← simpan nilai asli pilihan user
                'nama_produk_2' => $request->nama_produk_2,
                'nama_produk_3' => $request->nama_produk_3,
                'nama_produk_4' => $request->nama_produk_4,
                'nama_produk_5' => $request->nama_produk_5,
            ], $uploadedPaths));

            return response()->json([
                'status'  => true,
                'message' => 'Data lapangan berhasil disimpan',
                'data'    => $this->formatData($dataLapangan),
            ], 201);
        } catch (\Exception $e) {
            // Rollback semua file yang sudah terupload
            foreach ($uploadedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * PUT/PATCH /api/enumerator/data-lapangan/{id}
     * Status otomatis direset ke PENDING setiap kali data diperbarui.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        // Bangun rules foto secara dinamis hanya untuk file yang dikirim
        $fotoRules = [];
        foreach (self::FOTO_FIELDS as $inputKey => $dbColumn) {
            if ($request->hasFile($inputKey)) {
                $fotoRules[$inputKey] = 'image|mimes:jpg,jpeg,png|max:2048';
            }
        }

        // Rule file_oss jika dikirim
        if ($request->hasFile('file_oss')) {
            $fotoRules['file_oss'] = 'file|mimes:pdf|max:5120';
        }

        $validator = Validator::make($request->all(), array_merge([
            'nama_pu'       => 'sometimes|required|string|max:255',
            'nik'           => 'sometimes|required|string|size:16',
            'telephone'     => 'sometimes|required|string|max:15',
            'nama_produk'   => 'sometimes|required|string|max:255',
            'alamat'        => 'sometimes|required|string',
            'has_nib'       => 'sometimes|in:true,false,1,0',
            'nama_produk_2' => 'nullable|string|max:255',
            'nama_produk_3' => 'nullable|string|max:255',
            'nama_produk_4' => 'nullable|string|max:255',
            'nama_produk_5' => 'nullable|string|max:255',
        ], $fotoRules));

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $newPaths = [];

        try {
            $enumeratorId = Auth::user()->enumerator->id;

            $dataLapangan = DataLapangan::where('id', $id)
                ->where('enumerator_id', $enumeratorId)
                ->firstOrFail();

            // Reset status ke PENDING setiap kali data diperbarui
            $dataToUpdate = ['status' => 'PENDING'];

            // Field teks
            if ($request->has('nama_pu'))      $dataToUpdate['nama_pu']       = strtoupper($request->nama_pu);
            if ($request->has('nik'))           $dataToUpdate['nik']           = $request->nik;
            if ($request->has('telephone'))     $dataToUpdate['telephone']     = $request->telephone;
            if ($request->has('nama_produk'))   $dataToUpdate['nama_produk']   = $request->nama_produk;
            if ($request->has('alamat'))        $dataToUpdate['alamat']        = $request->alamat;
            if ($request->has('nama_produk_2')) $dataToUpdate['nama_produk_2'] = $request->nama_produk_2;
            if ($request->has('nama_produk_3')) $dataToUpdate['nama_produk_3'] = $request->nama_produk_3;
            if ($request->has('nama_produk_4')) $dataToUpdate['nama_produk_4'] = $request->nama_produk_4;
            if ($request->has('nama_produk_5')) $dataToUpdate['nama_produk_5'] = $request->nama_produk_5;

            // Update has_nib — simpan nilai asli pilihan user ke DB
            // Jika false, hapus file_oss yang sudah ada
            if ($request->has('has_nib')) {
                $hasNib = filter_var($request->has_nib, FILTER_VALIDATE_BOOLEAN);
                $dataToUpdate['has_nib'] = $hasNib; // ← simpan ke DB
                if (! $hasNib && $dataLapangan->file_oss) {
                    Storage::disk('public')->delete($dataLapangan->file_oss);
                    $dataToUpdate['file_oss'] = null;
                }
            }

            // Upload foto baru & kumpulkan path lama untuk dihapus setelah update
            $oldPaths = [];
            foreach (self::FOTO_FIELDS as $inputKey => $dbColumn) {
                if ($request->hasFile($inputKey)) {
                    $folder = str_starts_with($inputKey, 'foto-produk')
                        ? 'foto-produk'
                        : $inputKey;

                    $newPaths[$dbColumn]     = $request->file($inputKey)->store($folder, 'public');
                    $dataToUpdate[$dbColumn] = $newPaths[$dbColumn];
                    $oldPaths[$dbColumn]     = $dataLapangan->getOriginal($dbColumn);
                }
            }

            // Upload file OSS baru jika ada
            if ($request->hasFile('file_oss')) {
                $newPaths['file_oss']     = $request->file('file_oss')->store('files/oss', 'public');
                $dataToUpdate['file_oss'] = $newPaths['file_oss'];
                $oldPaths['file_oss']     = $dataLapangan->getOriginal('file_oss');
            }

            $dataLapangan->update($dataToUpdate);

            // Hapus file lama setelah update berhasil
            foreach ($oldPaths as $oldPath) {
                if ($oldPath) Storage::disk('public')->delete($oldPath);
            }

            $dataLapangan->refresh();

            return response()->json([
                'status'  => true,
                'message' => 'Data lapangan berhasil diperbarui',
                'data'    => $this->formatData($dataLapangan),
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            foreach ($newPaths as $path) Storage::disk('public')->delete($path);
            return response()->json([
                'status'  => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            foreach ($newPaths as $path) Storage::disk('public')->delete($path);
            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat memperbarui data',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE /api/enumerator/data-lapangan/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $enumeratorId = Auth::user()->enumerator->id;

            $dataLapangan = DataLapangan::where('id', $id)
                ->where('enumerator_id', $enumeratorId)
                ->firstOrFail();

            // Hapus semua file foto termasuk produk tambahan
            $fotoColumns = array_values(self::FOTO_FIELDS);
            foreach ($fotoColumns as $column) {
                if ($dataLapangan->$column) {
                    Storage::disk('public')->delete($dataLapangan->$column);
                }
            }

            // Hapus file OSS jika ada
            if ($dataLapangan->file_oss) {
                Storage::disk('public')->delete($dataLapangan->file_oss);
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

    private function createData(array $data): DataLapangan
    {
        if (isset($data['nama_pu'])) {
            $data['nama_pu'] = strtoupper($data['nama_pu']);
        }

        return DataLapangan::create([
            'enumerator_id'   => $data['enumerator_id'],
            'nama_pu'         => $data['nama_pu'],
            'nik'             => $data['nik'],
            'telephone'       => $data['telephone'],
            'nama_produk'     => $data['nama_produk'],
            'nama_produk_2'   => $data['nama_produk_2'] ?? null,
            'nama_produk_3'   => $data['nama_produk_3'] ?? null,
            'nama_produk_4'   => $data['nama_produk_4'] ?? null,
            'nama_produk_5'   => $data['nama_produk_5'] ?? null,
            'alamat'          => $data['alamat'],
            'foto_ktp'        => $data['foto_ktp'],
            'foto_rumah'      => $data['foto_rumah'],
            'foto_pendamping' => $data['foto_pendamping'],
            'foto_produk'     => $data['foto_produk'],
            'foto_produk_2'   => $data['foto_produk_2'] ?? null,
            'foto_produk_3'   => $data['foto_produk_3'] ?? null,
            'foto_produk_4'   => $data['foto_produk_4'] ?? null,
            'foto_produk_5'   => $data['foto_produk_5'] ?? null,
            'file_oss'        => $data['file_oss'] ?? null,
            'has_nib'         => $data['has_nib'] ?? false, // ← simpan pilihan user
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
            'nama_produk_2'       => $item->nama_produk_2,
            'nama_produk_3'       => $item->nama_produk_3,
            'nama_produk_4'       => $item->nama_produk_4,
            'nama_produk_5'       => $item->nama_produk_5,
            'alamat'              => $item->alamat,
            'foto_ktp'            => $item->foto_ktp        ? Storage::url($item->foto_ktp)        : null,
            'foto_rumah'          => $item->foto_rumah      ? Storage::url($item->foto_rumah)      : null,
            'foto_pendamping'     => $item->foto_pendamping ? Storage::url($item->foto_pendamping) : null,
            'foto_produk'         => $item->foto_produk     ? Storage::url($item->foto_produk)     : null,
            'foto_produk_2'       => $item->foto_produk_2   ? Storage::url($item->foto_produk_2)   : null,
            'foto_produk_3'       => $item->foto_produk_3   ? Storage::url($item->foto_produk_3)   : null,
            'foto_produk_4'       => $item->foto_produk_4   ? Storage::url($item->foto_produk_4)   : null,
            'foto_produk_5'       => $item->foto_produk_5   ? Storage::url($item->foto_produk_5)   : null,
            'file_oss'            => $item->file_oss        ? Storage::url($item->file_oss)        : null,
            // Baca dari kolom has_nib di DB; fallback ke inferensi file_oss
            // untuk data lama yang belum punya kolom has_nib
            'has_nib'             => isset($item->has_nib)
                ? (bool) $item->has_nib
                : (bool) $item->file_oss,
            'status'              => $item->status,
            'status_pembayaran'   => $item->status_pembayaran,
            'verifikator'         => $item->verifikator,
            'tanggal_verifikasi'  => $item->tanggal_verifikasi,
            'keterangan'          => $item->keterangan,
            'file_sihalal'        => $item->file_sihalal ? Storage::url($item->file_sihalal) : null,
            'created_at'          => $item->created_at,
            'updated_at'          => $item->updated_at,
        ];
    }
}
