<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DataLapangan
 *
 * @property $id
 * @property $enumerator_id
 * @property $nama_pu
 * @property $nik
 * @property $rt
 * @property $rw
 * @property $alamat
 * @property $titik_koordinat
 * @property $foto_ktp
 * @property $foto_rumah
 * @property $foto_pendamping
 * @property $foto_proses
 * @property $foto_produk
 * @property $created_at
 * @property $updated_at
 *
 * @property Enumerator $enumerator
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class DataLapangan extends Model
{

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['enumerator_id', 'nama_pu', 'nik', 'rt', 'rw', 'alamat', 'titik_koordinat', 'foto_ktp', 'foto_rumah', 'foto_pendamping', 'foto_proses', 'foto_produk', 'status', 'status_pembayaran', 'file_oss', 'file_sihalal'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function enumerator()
    {
        return $this->belongsTo(\App\Models\Enumerator::class, 'enumerator_id', 'id');
    }

    /**
     * Get the associated CashflowsKoordinator model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function CashflowsKoordinator()
    {
        return $this->hasMany(CashflowsKoordinator::class);
    }

    /**
     * Booted method to create a new cashflow when the status_pembayaran of a data_lapangan is changed to DIBAYAR.
     *
     * This method is called when a data_lapangan is updated.
     * If there is no existing cashflow for the data_lapangan, a new one will be created.
     * The cashflow will have the type 'PEMASUKAN', nominal 70000, and a description of 'Pembayaran untuk {nama_pu} (NIK: {nik})'.
     * The date of the cashflow will be the current date and time.
     */
    protected static function booted()
    {
        /**
         * This method is called when a data_lapangan is updated.
         * It checks if the status_pembayaran of the data_lapangan is changed to DIBAYAR.
         * If it is, it checks if there is already a cashflow for the data_lapangan.
         * If there is no existing cashflow, a new one will be created.
         */
        static::updated(function ($dataLapangan) {
            // Cek apakah status_pembayaran berubah menjadi DIBAYAR
            if (
                $dataLapangan->isDirty('status_pembayaran') &&
                $dataLapangan->status_pembayaran === 'DIBAYAR'
            ) {

                // Cek apakah sudah ada cashflow pemasukan untuk data lapangan ini
                $existingCashflowPemasukan = CashflowsKoordinator::where('data_lapangan_id', $dataLapangan->id)
                    ->where('tipe', 'PEMASUKAN')
                    ->first();

                // Jika belum ada, buat cashflow pemasukan baru
                if (!$existingCashflowPemasukan) {
                    CashflowsKoordinator::create([
                        'data_lapangan_id' => $dataLapangan->id,
                        'tipe' => 'PEMASUKAN',
                        'nominal' => 70000,
                        'keterangan' => 'Pembayaran untuk ' . $dataLapangan->nama_pu . ' (NIK: ' . $dataLapangan->nik . ')',
                        'tanggal' => now()
                    ]);
                }

                // Cek apakah sudah ada cashflow pengeluaran untuk data lapangan ini
                $existingCashflowPengeluaran = Cashflow::where('data_lapangan_id', $dataLapangan->id)
                    ->where('tipe', 'Pengeluaran')
                    ->first();

                // Jika belum ada, buat cashflow pengeluaran baru
                if (!$existingCashflowPengeluaran) {
                    Cashflow::create([
                        'data_lapangan_id' => $dataLapangan->id,
                        'tipe' => 'Pengeluaran',
                        'jumlah' => 70000,
                        'keterangan' => 'Pembayaran untuk ' . $dataLapangan->enumerator->nama_lengkap . ' - ' . $dataLapangan->nama_pu . ' (NIK: ' . $dataLapangan->nik . ')',
                        'tanggal' => now()
                    ]);
                }
            }
        });
    }
}
