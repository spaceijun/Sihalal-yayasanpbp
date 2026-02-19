<?php

namespace App\Models;

use App\Models\Superadmin\Koordinator;
use App\Traits\HasHashedId;
use App\Traits\SendsWhatsAppNotification;
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
    use SendsWhatsAppNotification, HasHashedId;
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['enumerator_id', 'nama_pu', 'nik', 'email', 'telephone', 'nama_produk', 'alamat', 'foto_ktp', 'foto_rumah', 'foto_pendamping', 'foto_produk', 'status', 'verifikator', 'tanggal_verifikasi', 'status_pembayaran', 'file_oss', 'file_sihalal', 'keterangan_oss', 'keterangan_sihalal'];


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
     * Get the associated spotchecks model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function spotchecks()
    {
        return $this->hasMany(Spotcheck::class, 'data_lapangan_id');
    }

    /**
     * Get the associated Koordinator model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function koordinator()
    {
        return $this->belongsTo(Koordinator::class, 'koordinator_id');
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
                        'nominal' => $dataLapangan->enumerator->koordinator->fee_enum,
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
                        'jumlah' => $dataLapangan->enumerator->koordinator->fee_enum,
                        'keterangan' => 'Pembayaran untuk ' . $dataLapangan->enumerator->nama_lengkap . ' - ' . $dataLapangan->nama_pu . ' (NIK: ' . $dataLapangan->nik . ')',
                        'tanggal' => now()
                    ]);

                    // ===== KIRIM NOTIFIKASI WHATSAPP =====
                    // Load relasi enumerator jika belum di-load
                    $dataLapangan->load('enumerator');

                    // Kirim notifikasi ke enumerator
                    if ($dataLapangan->enumerator && $dataLapangan->enumerator->telephone) {
                        $dataLapangan->sendPembayaranNotificationToEnumerator();
                    }
                }
            }
        });
    }
}
