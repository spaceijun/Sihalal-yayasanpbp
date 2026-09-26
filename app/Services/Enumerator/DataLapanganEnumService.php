<?php

namespace App\Services\Enumerator;

use App\Models\DataLapangan;
use App\Models\Enumerator;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * DataLapanganEnumService
 *
 * Seluruh business logic modul Data Lapangan untuk API Enumerator (aplikasi Flutter), dipindah
 * dari App\Http\Controllers\Api\Enumerator\DataLapanganEnumController supaya controller-nya
 * tetap tipis — lihat .agent/workflows/data-lapangan-enumerator-api.md §2 untuk alasan refaktor
 * ini. Validasi input ada di App\Http\Requests\Enumerator\DataLapanganEnum{Store,Update}Request,
 * bukan di sini — service ini hanya menerima data yang sudah tervalidasi.
 */
class DataLapanganEnumService
{
    public const STATUS_LIST = [
        'PENDING',
        'REVISI',
        'TERVERIFIKASI',
        'PROGRESS OSS',
        'PROGRESS SIHALAL',
        'TERBIT SH',
    ];

    /** Mapping input field (dengan dash, dari form-data Flutter) → kolom database. */
    public const FOTO_FIELDS = [
        'foto-ktp' => 'foto_ktp',
        'foto-rumah' => 'foto_rumah',
        'foto-pendamping' => 'foto_pendamping',
        'foto-proses' => 'foto_proses',
        'foto-produk' => 'foto_produk',
        'foto-produk-2' => 'foto_produk_2',
        'foto-produk-3' => 'foto_produk_3',
        'foto-produk-4' => 'foto_produk_4',
        'foto-produk-5' => 'foto_produk_5',
    ];

    /**
     * Minimal jumlah pengajuan 30 hari terakhir supaya enumerator dianggap aktif — dipakai hanya
     * untuk payload informatif di assertEnumeratorAktif(), BUKAN sumber kebenaran status aktif
     * (itu murni kolom `enumerators.status`, ditegakkan oleh middleware EnsureEnumeratorIsActive).
     */
    private const MINIMAL_PENGAJUAN_30_HARI = 20;

    public function paginate(Enumerator $enumerator, array $filters, int $perPage): LengthAwarePaginator
    {
        $query = DataLapangan::where('enumerator_id', $enumerator->id);

        if (! empty($filters['search'])) {
            $query->where('nama_pu', 'like', '%'.$filters['search'].'%');
        }
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $paginator = $query->latest()->paginate($perPage);
        $paginator->getCollection()->transform(fn (DataLapangan $item) => $this->format($item));

        return $paginator;
    }

    /**
     * @throws ModelNotFoundException kalau data tidak ada / bukan milik enumerator ini.
     */
    public function findOwned(int $id, Enumerator $enumerator): DataLapangan
    {
        return DataLapangan::where('id', $id)
            ->where('enumerator_id', $enumerator->id)
            ->firstOrFail();
    }

    /**
     * Guard "enumerator tidak aktif" — dipertahankan dari controller lama apa adanya (lihat
     * catatan dead-code di §2 data-lapangan-enumerator-api.md: middleware `enumerator.active`
     * sudah menangkap kasus yang sama lebih dulu di production, jadi return non-null di sini
     * secara praktis tidak pernah tercapai — tapi tidak dihapus supaya perilaku tidak berubah
     * tanpa diminta).
     *
     * @return array|null Payload alasan penolakan (untuk response 403), atau null kalau boleh submit.
     */
    public function assertEnumeratorAktif(?Enumerator $enumerator): ?array
    {
        if ($enumerator && $enumerator->status !== 'Tidak Aktif') {
            return null;
        }

        $jumlah30Hari = $enumerator
            ? $enumerator->dataLapangans()
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->count()
            : 0;

        return [
            'status_enumerator' => $enumerator?->status ?? 'Tidak Ditemukan',
            'jumlah_data_30_hari' => $jumlah30Hari,
            'minimal_required' => self::MINIMAL_PENGAJUAN_30_HARI,
        ];
    }

    /**
     * Simpan Data Lapangan baru — upload semua foto wajib/opsional & simpan geotag (§3
     * data-lapangan-enumerator-api.md). Kalau create DB gagal, semua file yang sudah terupload
     * dihapus (rollback) sebelum exception dilempar ulang ke controller.
     *
     * @param  array  $validated  Hasil StoreRequest::validated() — berisi field teks + UploadedFile.
     */
    public function create(Enumerator $enumerator, array $validated): DataLapangan
    {
        $uploadedPaths = [];

        try {
            $hasNib = filter_var($validated['has_nib'], FILTER_VALIDATE_BOOLEAN);

            foreach (self::FOTO_FIELDS as $inputKey => $dbColumn) {
                if (! empty($validated[$inputKey])) {
                    $folder = str_starts_with($inputKey, 'foto-produk') ? 'foto-produk' : $inputKey;
                    $uploadedPaths[$dbColumn] = $validated[$inputKey]->store($folder, 'public');
                }
            }

            if ($hasNib && ! empty($validated['file_oss'])) {
                $uploadedPaths['file_oss'] = $validated['file_oss']->store('files/oss', 'public');
            }

            return DB::transaction(function () use ($validated, $enumerator, $hasNib, $uploadedPaths) {
                return DataLapangan::create([
                    'enumerator_id' => $enumerator->id,
                    'nama_pu' => strtoupper($validated['nama_pu']),
                    'nik' => $validated['nik'],
                    'telephone' => $validated['telephone'],
                    'nama_produk' => $validated['nama_produk'],
                    'nama_produk_2' => $validated['nama_produk_2'] ?? null,
                    'nama_produk_3' => $validated['nama_produk_3'] ?? null,
                    'nama_produk_4' => $validated['nama_produk_4'] ?? null,
                    'nama_produk_5' => $validated['nama_produk_5'] ?? null,
                    'alamat' => $validated['alamat'],
                    'provinsi' => $validated['provinsi'] ?? null,
                    'kabupaten' => $validated['kabupaten'] ?? null,
                    'kecamatan' => $validated['kecamatan'] ?? null,
                    'kelurahan' => $validated['kelurahan'] ?? null,
                    'rt' => $validated['rt'] ?? null,
                    'rw' => $validated['rw'] ?? null,
                    'kode_pos' => $validated['kode_pos'] ?? null,
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'akurasi_meter' => $validated['akurasi_meter'] ?? null,
                    'geotag_captured_at' => $validated['geotag_captured_at'] ?? null,
                    'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                    'foto_ktp' => $uploadedPaths['foto_ktp'],
                    'foto_rumah' => $uploadedPaths['foto_rumah'],
                    'foto_pendamping' => $uploadedPaths['foto_pendamping'],
                    'foto_proses' => $uploadedPaths['foto_proses'],
                    'foto_produk' => $uploadedPaths['foto_produk'],
                    'foto_produk_2' => $uploadedPaths['foto_produk_2'] ?? null,
                    'foto_produk_3' => $uploadedPaths['foto_produk_3'] ?? null,
                    'foto_produk_4' => $uploadedPaths['foto_produk_4'] ?? null,
                    'foto_produk_5' => $uploadedPaths['foto_produk_5'] ?? null,
                    'file_oss' => $uploadedPaths['file_oss'] ?? null,
                    'has_nib' => $hasNib,
                ]);
            });
        } catch (\Throwable $e) {
            foreach ($uploadedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            throw $e;
        }
    }

    /**
     * Perbarui Data Lapangan milik enumerator. Status selalu direset ke PENDING (perilaku lama
     * dipertahankan). File baru menggantikan yang lama; file lama dihapus SETELAH update berhasil.
     *
     * @param  array  $validated  Hasil UpdateRequest::validated().
     */
    public function update(DataLapangan $dataLapangan, array $validated): DataLapangan
    {
        $newPaths = [];
        $oldPaths = [];

        try {
            $dataToUpdate = ['status' => 'PENDING'];

            $textFields = [
                'nik', 'telephone', 'nama_produk', 'alamat',
                'provinsi', 'kabupaten', 'kecamatan', 'kelurahan', 'rt', 'rw', 'kode_pos',
                'nama_produk_2', 'nama_produk_3', 'nama_produk_4', 'nama_produk_5',
                'latitude', 'longitude', 'akurasi_meter', 'geotag_captured_at',
            ];
            foreach ($textFields as $field) {
                if (array_key_exists($field, $validated)) {
                    $dataToUpdate[$field] = $validated[$field];
                }
            }
            if (array_key_exists('nama_pu', $validated)) {
                $dataToUpdate['nama_pu'] = strtoupper($validated['nama_pu']);
            }

            if (array_key_exists('has_nib', $validated)) {
                $hasNib = filter_var($validated['has_nib'], FILTER_VALIDATE_BOOLEAN);
                $dataToUpdate['has_nib'] = $hasNib;
                if (! $hasNib && $dataLapangan->file_oss) {
                    $oldPaths['file_oss'] = $dataLapangan->file_oss;
                    $dataToUpdate['file_oss'] = null;
                }
            }

            foreach (self::FOTO_FIELDS as $inputKey => $dbColumn) {
                if (! empty($validated[$inputKey])) {
                    $folder = str_starts_with($inputKey, 'foto-produk') ? 'foto-produk' : $inputKey;
                    $newPaths[$dbColumn] = $validated[$inputKey]->store($folder, 'public');
                    $dataToUpdate[$dbColumn] = $newPaths[$dbColumn];
                    $oldPaths[$dbColumn] = $dataLapangan->getOriginal($dbColumn);
                }
            }

            if (! empty($validated['file_oss'])) {
                $newPaths['file_oss'] = $validated['file_oss']->store('files/oss', 'public');
                $dataToUpdate['file_oss'] = $newPaths['file_oss'];
                $oldPaths['file_oss'] = $dataLapangan->getOriginal('file_oss');
            }

            $dataLapangan->update($dataToUpdate);

            foreach ($oldPaths as $oldPath) {
                if ($oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            return $dataLapangan->refresh();
        } catch (\Throwable $e) {
            foreach ($newPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            throw $e;
        }
    }

    public function delete(DataLapangan $dataLapangan): void
    {
        foreach (array_values(self::FOTO_FIELDS) as $column) {
            if ($dataLapangan->$column) {
                Storage::disk('public')->delete($dataLapangan->$column);
            }
        }

        if ($dataLapangan->file_oss) {
            Storage::disk('public')->delete($dataLapangan->file_oss);
        }

        $dataLapangan->delete();
    }

    public function format(DataLapangan $item): array
    {
        return [
            'id' => $item->id,
            'no_registrasi' => $item->no_registrasi,
            'enumerator_id' => $item->enumerator_id,
            'nama_pu' => $item->nama_pu,
            'nik' => $item->nik,
            'email' => $item->email,
            'telephone' => $item->telephone,
            'nama_produk' => $item->nama_produk,
            'nama_produk_2' => $item->nama_produk_2,
            'nama_produk_3' => $item->nama_produk_3,
            'nama_produk_4' => $item->nama_produk_4,
            'nama_produk_5' => $item->nama_produk_5,
            'alamat' => $item->alamat,
            'provinsi' => $item->provinsi,
            'kabupaten' => $item->kabupaten,
            'kecamatan' => $item->kecamatan,
            'kelurahan' => $item->kelurahan,
            'rt' => $item->rt,
            'rw' => $item->rw,
            'kode_pos' => $item->kode_pos,
            'latitude' => $item->latitude !== null ? (float) $item->latitude : null,
            'longitude' => $item->longitude !== null ? (float) $item->longitude : null,
            'akurasi_meter' => $item->akurasi_meter !== null ? (float) $item->akurasi_meter : null,
            'geotag_captured_at' => $item->geotag_captured_at?->toIso8601String(),
            'google_maps_url' => $item->google_maps_url,
            'tanggal_lahir' => $item->tanggal_lahir?->format('Y-m-d'),
            'umur' => $item->umur,
            'full_address' => $item->full_address,
            'foto_ktp' => $item->foto_ktp ? Storage::url($item->foto_ktp) : null,
            'foto_rumah' => $item->foto_rumah ? Storage::url($item->foto_rumah) : null,
            'foto_pendamping' => $item->foto_pendamping ? Storage::url($item->foto_pendamping) : null,
            'foto_proses' => $item->foto_proses ? Storage::url($item->foto_proses) : null,
            'foto_produk' => $item->foto_produk ? Storage::url($item->foto_produk) : null,
            'foto_produk_2' => $item->foto_produk_2 ? Storage::url($item->foto_produk_2) : null,
            'foto_produk_3' => $item->foto_produk_3 ? Storage::url($item->foto_produk_3) : null,
            'foto_produk_4' => $item->foto_produk_4 ? Storage::url($item->foto_produk_4) : null,
            'foto_produk_5' => $item->foto_produk_5 ? Storage::url($item->foto_produk_5) : null,
            'file_oss' => $item->file_oss ? Storage::url($item->file_oss) : null,
            'has_nib' => $item->has_nib !== null
                ? (bool) $item->has_nib
                : (bool) $item->file_oss,
            'status' => $item->status,
            'status_pembayaran' => $item->status_pembayaran,
            'verifikator' => $item->verifikator,
            'tanggal_verifikasi' => $item->tanggal_verifikasi,
            'keterangan' => $item->keterangan,
            'file_sihalal' => $item->file_sihalal ? Storage::url($item->file_sihalal) : null,
            'created_at' => $item->created_at,
            'updated_at' => $item->updated_at,
        ];
    }
}
