<?php

namespace App\Models;

use App\Models\Superadmin\Koordinator;
use App\Models\CashflowsKoordinator;
use App\Models\Cashflow;
use App\Models\User;
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
    protected $fillable = [
        'enumerator_id',
        'nama_pu',
        'nik',
        'email',
        'email_sihalal',
        'telephone',
        'nama_produk',
        'nama_produk_2',
        'nama_produk_3',
        'nama_produk_4',
        'nama_produk_5',
        'alamat',
        'foto_ktp',
        'foto_rumah',
        'foto_pendamping',
        'foto_produk',
        'foto_produk_2',
        'foto_produk_3',
        'foto_produk_4',
        'foto_produk_5',
        'status',
        'verifikator_id',
        'tanggal_verifikasi',
        'status_pembayaran',
        'file_oss',
        'has_nib',
        'file_sihalal',
        'keterangan_oss',
        'keterangan_sihalal',
        'is_being_edited',
        'edited_by',
        'edit_expires_at',
    ];


    protected $casts = [
        'edit_expires_at' => 'datetime',
        'is_being_edited' => 'boolean',
        'has_nib' => 'boolean',
    ];

    public function scopeAvailable($query)
    {
        return $query->where(function ($q) {
            $q->where('is_being_edited', false)
                ->orWhere('edit_expires_at', '<', now());
        });
    }

    public function editedBy()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
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
     * Get the associated Verifikator model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     * @see \App\Models\Verifikator
     */
    public function verifikator()
    {
        return $this->belongsTo(Verifikator::class);
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
        static::updated(function ($dataLapangan) {
            if (
                $dataLapangan->isDirty('status_pembayaran') &&
                $dataLapangan->status_pembayaran === 'DIBAYAR'
            ) {
                // Hitung fee berdasarkan tanggal data dibuat
                $fee = self::resolveFee($dataLapangan);

                $existingCashflowPemasukan = CashflowsKoordinator::where('data_lapangan_id', $dataLapangan->id)
                    ->where('tipe', 'PEMASUKAN')
                    ->first();

                if (!$existingCashflowPemasukan) {
                    CashflowsKoordinator::create([
                        'data_lapangan_id' => $dataLapangan->id,
                        'tipe'             => 'PEMASUKAN',
                        'nominal'          => $fee,
                        'keterangan'       => 'Pembayaran untuk ' . $dataLapangan->nama_pu . ' (NIK: ' . $dataLapangan->nik . ')',
                        'tanggal'          => now(),
                    ]);
                }

                $existingCashflowPengeluaran = Cashflow::where('data_lapangan_id', $dataLapangan->id)
                    ->where('tipe', 'Pengeluaran')
                    ->first();

                if (!$existingCashflowPengeluaran) {
                    Cashflow::create([
                        'data_lapangan_id' => $dataLapangan->id,
                        'tipe'             => 'Pengeluaran',
                        'jumlah'           => $fee,
                        'keterangan'       => 'Pembayaran untuk ' . $dataLapangan->enumerator->nama_lengkap . ' - ' . $dataLapangan->nama_pu . ' (NIK: ' . $dataLapangan->nik . ')',
                        'tanggal'          => now(),
                    ]);

                    $dataLapangan->load('enumerator');

                    if ($dataLapangan->enumerator && $dataLapangan->enumerator->telephone) {
                        $dataLapangan->sendPembayaranNotificationToEnumerator();
                    }
                }
            }
        });
    }

    /**
     * Hitung fee berdasarkan tanggal data dibuat.
     * Tambahkan entri baru di array $feeSchedule saat harga naik,
     * tanpa perlu ubah logic apapun.
     */
    private static function resolveFee(self $dataLapangan): int
    {
        $feeSchedule = [
            '2026-05-01' => 60000,
            // '2027-01-01' => 75000, // ← cukup tambah baris ini saat harga naik lagi
        ];

        // Urutkan dari tanggal terbaru ke terlama
        krsort($feeSchedule);

        $createdAt = $dataLapangan->created_at->toDateString();

        foreach ($feeSchedule as $date => $amount) {
            if ($createdAt >= $date) {
                return $amount;
            }
        }

        // Fallback ke fee_enum koordinator untuk data sebelum semua schedule
        return $dataLapangan->enumerator->koordinator->fee_enum;
    }
    /**
     * Get the associated data entry progress models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function dataEntryProgress()
    {
        return $this->hasMany(DataEntryProgress::class, 'data_lapangan_id')
            ->whereNotNull('data_entry_id');
    }

    // Relasi khusus verifikator (untuk VerifikatorController)
    public function verifikatorProgress()
    {
        return $this->hasMany(DataEntryProgress::class, 'data_lapangan_id')
            ->whereNotNull('verifikator_id');  // ✅ hanya milik verifikator
    }
    public function dataEntry()
    {
        return $this->hasOneThrough(
            DataEntry::class,
            DataEntryProgress::class,
            'data_lapangan_id', // FK di data_entry_progress
            'id',               // FK di data_entrys
            'id',               // PK di data_lapangans
            'data_entry_id'     // FK di data_entry_progress
        );
    }
}
