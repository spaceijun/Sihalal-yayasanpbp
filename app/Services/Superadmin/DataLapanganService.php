<?php

namespace App\Services\Superadmin;

use App\Models\DataLapangan;

class DataLapanganService
{
    /**
     * Get filtered and paginated data lapangan
     */
    public function getFilteredData(array $filters, int $perPage = 20)
    {
        $query = DataLapangan::with('enumerator');

        if (!empty($filters['nama_pu'])) {
            $query->where('nama_pu', 'like', '%' . $filters['nama_pu'] . '%');
        }

        if (!empty($filters['enumerator_id'])) {
            $query->where('enumerator_id', $filters['enumerator_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['status_pembayaran'])) {
            $query->where('status_pembayaran', $filters['status_pembayaran']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get data revisi with pagination
     */
    public function getDataRevisi(int $perPage = 15)
    {
        return DataLapangan::with('enumerator')
            ->whereNotNull('keterangan')
            ->paginate($perPage);
    }

    /**
     * Create new data lapangan
     */
    public function create(array $validatedData): DataLapangan
    {
        // FORCE UPPERCASE untuk nama_pu
        if (isset($validatedData['nama_pu'])) {
            $validatedData['nama_pu'] = strtoupper($validatedData['nama_pu']);
        }

        // Kolom wajib
        $dataToSave = [
            'enumerator_id'    => $validatedData['enumerator_id'],
            'nama_pu'          => $validatedData['nama_pu'],
            'nik'              => $validatedData['nik'],
            'telephone'        => $validatedData['telephone'],
            'nama_produk'      => $validatedData['nama_produk'],
            'alamat'           => $validatedData['alamat'] ?? null,
            'provinsi'         => $validatedData['provinsi'] ?? null,
            'kabupaten'        => $validatedData['kabupaten'] ?? null,
            'kecamatan'        => $validatedData['kecamatan'] ?? null,
            'kelurahan'        => $validatedData['kelurahan'] ?? null,
            'rt'               => $validatedData['rt'] ?? null,
            'rw'               => $validatedData['rw'] ?? null,
            'kode_pos'         => $validatedData['kode_pos'] ?? null,
            'foto_ktp'         => $validatedData['foto_ktp_path'],
            'foto_rumah'       => $validatedData['foto_rumah_path'],
            'foto_pendamping'  => $validatedData['foto_pendamping_path'],
            'foto_proses'      => $validatedData['foto_proses_path'] ?? null,
            'foto_produk'      => $validatedData['foto_produk_path'],
        ];

        // Kolom produk tambahan (nullable)
        foreach ([2, 3, 4, 5] as $i) {
            $dataToSave["nama_produk_{$i}"] = $validatedData["nama_produk_{$i}"] ?? null;
            $dataToSave["foto_produk_{$i}"] = $validatedData["foto_produk_{$i}_path"] ?? null;
        }

        return DataLapangan::create($dataToSave);
    }

    /**
     * Update data lapangan
     */
    public function update(DataLapangan $dataLapangan, array $validatedData): bool
    {
        return $dataLapangan->update($validatedData);
    }

    /**
     * Delete data lapangan
     */
    public function delete(int $id): bool
    {
        $dataLapangan = DataLapangan::find($id);
        return $dataLapangan ? $dataLapangan->delete() : false;
    }

    /**
     * Update keterangan
     */
    public function updateKeterangan(int $id, ?string $keterangan): DataLapangan
    {
        $dataLapangan = DataLapangan::findOrFail($id);
        $dataLapangan->keterangan = $keterangan;

        // Update status menjadi REVISI jika keterangan diisi
        if (!empty($keterangan)) {
            $dataLapangan->status = 'REVISI';
        }

        $dataLapangan->save();
        return $dataLapangan;
    }

    /**
     * Update email, verifikator, dan tanggal_verifikasi
     */
    public function updateEmail(
        int $id,
        string $email,
        ?int $verifikatorId,
        ?string $tanggalVerifikasi
    ): void {
        $dataLapangan = DataLapangan::findOrFail($id);

        if ($dataLapangan->email_sihalal !== null) {
            $status = 'PROGRESS SIHALAL';
        } elseif ($dataLapangan->file_oss !== null) {
            $status = 'PROGRESS OSS';
        } else {
            $status = 'TERVERIFIKASI';
        }

        $dataLapangan->update([
            'email'              => $email,
            'verifikator_id'     => $verifikatorId,
            'tanggal_verifikasi' => $tanggalVerifikasi,
            'status'             => $status,
        ]);
    }

    /**
     * Update email sihalal of a data lapangan.
     */
    public function updateEmailSihalal(int $id, string $emailSihalal): DataLapangan
    {
        $dataLapangan = DataLapangan::findOrFail($id);
        $dataLapangan->update(['email_sihalal' => $emailSihalal]);
        return $dataLapangan;
    }

    public function getDataRevisiAll()
    {
        return DataLapangan::with('enumerator')
            ->where('status', 'Revisi')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Format data lapangan for API response (Flutter)
     */
    public function formatForApi(DataLapangan $data): array
    {
        return [
            'id' => $data->id,
            'no_registrasi' => $data->no_registrasi,
            'enumerator_id' => $data->enumerator_id,
            'enumerator' => $data->enumerator ? [
                'id' => $data->enumerator->id,
                'nama' => $data->enumerator->nama_lengkap,
            ] : null,
            'nama_pu' => $data->nama_pu,
            'nik' => $data->nik,
            'alamat' => $data->alamat,
            'provinsi' => $data->provinsi,
            'kabupaten' => $data->kabupaten,
            'kecamatan' => $data->kecamatan,
            'kelurahan' => $data->kelurahan,
            'rt' => $data->rt,
            'rw' => $data->rw,
            'kode_pos' => $data->kode_pos,
            'full_address' => $data->full_address,
            'telephone' => $data->telephone,
            'email' => $data->email,
            'email_sihalal' => $data->email_sihalal,
            'products' => array_filter([
                $data->nama_produk,
                $data->nama_produk_2,
                $data->nama_produk_3,
                $data->nama_produk_4,
                $data->nama_produk_5,
            ]),
            'photos' => [
                'ktp' => $data->foto_ktp ? asset($data->foto_ktp) : null,
                'rumah' => $data->foto_rumah ? asset($data->foto_rumah) : null,
                'pendamping' => $data->foto_pendamping ? asset($data->foto_pendamping) : null,
                'proses' => $data->foto_proses ? asset($data->foto_proses) : null,
                'produk_1' => $data->foto_produk ? asset($data->foto_produk) : null,
                'produk_2' => $data->foto_produk_2 ? asset($data->foto_produk_2) : null,
                'produk_3' => $data->foto_produk_3 ? asset($data->foto_produk_3) : null,
                'produk_4' => $data->foto_produk_4 ? asset($data->foto_produk_4) : null,
                'produk_5' => $data->foto_produk_5 ? asset($data->foto_produk_5) : null,
            ],
            'status' => $data->status,
            'has_nib' => $data->has_nib,
            'pengajuan_lewat' => $data->pengajuan_lewat,
            'created_at' => $data->created_at?->toIso8601String(),
            'updated_at' => $data->updated_at?->toIso8601String(),
        ];
    }
}
