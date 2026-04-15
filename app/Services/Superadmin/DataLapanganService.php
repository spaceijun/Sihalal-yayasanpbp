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

        // Map path fields to database fields
        $dataToSave = [
            'enumerator_id' => $validatedData['enumerator_id'],
            'nama_pu' => $validatedData['nama_pu'],
            'nik' => $validatedData['nik'],
            'telephone' => $validatedData['telephone'],
            'nama_produk' => $validatedData['nama_produk'],
            'alamat' => $validatedData['alamat'],
            'foto_ktp' => $validatedData['foto_ktp_path'],
            'foto_rumah' => $validatedData['foto_rumah_path'],
            'foto_pendamping' => $validatedData['foto_pendamping_path'],
            'foto_produk' => $validatedData['foto_produk_path'],
        ];

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
        $id,
        string $email,
        ?int $verifikatorId,
        ?string $tanggalVerifikasi
    ): void {
        DataLapangan::where('id', $id)->update([
            'email'              => $email,
            'verifikator_id'     => $verifikatorId,
            'tanggal_verifikasi' => $tanggalVerifikasi,
            'status'             => 'TERVERIFIKASI',
        ]);
    }

    /**
     * Update email sihalal of a data lapangan.
     *
     * @param int $id
     * @param string $emailSihalal
     * @return DataLapangan
     */
    public function updateEmailSihalal(int $id, string $emailSihalal): DataLapangan
    {
        $dataLapangan = DataLapangan::findOrFail($id);
        $dataLapangan->update(['email_sihalal' => $emailSihalal]);

        return $dataLapangan;
    }
}
