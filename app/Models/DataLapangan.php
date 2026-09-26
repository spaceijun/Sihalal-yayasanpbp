<?php

namespace App\Models;

use App\Models\Superadmin\Koordinator;
use App\Observers\DataLapanganObserver;
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
 * @property $foto_ktp
 * @property $foto_rumah
 * @property $foto_pendamping
 * @property $foto_proses
 * @property $foto_produk
 * @property $created_at
 * @property $updated_at
 * @property Enumerator $enumerator
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class DataLapangan extends Model
{
    use HasHashedId, SendsWhatsAppNotification;

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'enumerator_id',
        'no_registrasi',
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
        'provinsi',
        'kabupaten',
        'kecamatan',
        'kelurahan',
        'rt',
        'rw',
        'kode_pos',
        'latitude',
        'longitude',
        'akurasi_meter',
        'geotag_captured_at',
        'tanggal_lahir',
        'foto_proses',
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
        'keterangan_pembayaran',
        'file_oss',
        'has_nib',
        'file_sihalal',
        'keterangan',
        'keterangan_oss',
        'keterangan_sihalal',
        'is_being_edited',
        'edited_by',
        'edit_expires_at',
        'is_unlocked_for_data_entry',
        'old_email_sihalal',
        'pengajuan_lewat',
        'verifikasi_koordinator',
        'catatan_koordinator',
        'verified_at_koordinator',
        'verified_by_koordinator',
        'verifikasi_final',
        'catatan_final',
        'verified_at_final',
        'verified_by_final',
        'jalur_data_entry',
        'nama_usaha',
        'tempat_lahir',
        'jenis_usaha',
        'modal_usaha',
        'alamat_usaha',
        'provinsi_kode',
        'kabupaten_kode',
        'kecamatan_kode',
        'kelurahan_kode',
        'jenis_produk_halal',
        'bahan_utama_halal',
        'urusin_nib_submission_id',
        'urusin_nib_status',
        'urusin_nib_document_path',
        'urusin_halal_submission_id',
        'urusin_halal_status',
        'urusin_halal_document_path',
        'urusin_last_synced_at',
        'urusin_gagal_pesan',
    ];

    protected $attributes = [
        'is_unlocked_for_data_entry' => true,
        'status_pembayaran' => 'TIDAK ADA PENGAJUAN',
    ];

    protected $casts = [
        'edit_expires_at' => 'datetime',
        'is_being_edited' => 'boolean',
        'has_nib' => 'boolean',
        'is_unlocked_for_data_entry' => 'boolean',
        'tanggal_lahir' => 'date',
        'verified_at_koordinator' => 'datetime',
        'verified_at_final' => 'datetime',
        'urusin_last_synced_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'akurasi_meter' => 'decimal:2',
        'geotag_captured_at' => 'datetime',
    ];

    /**
     * URL Google Maps ke titik geotag hasil submit Enumerator (§3 data-lapangan-enumerator-api.md)
     * — null kalau data belum punya koordinat (mis. data lama sebelum fitur ini ada).
     */
    public function getGoogleMapsUrlAttribute(): ?string
    {
        if ($this->latitude === null || $this->longitude === null) {
            return null;
        }

        return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
    }

    /**
     * Get full formatted address
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->alamat,
            $this->rt ? 'RT ' . $this->rt : null,
            $this->rw ? 'RW ' . $this->rw : null,
            $this->kelurahan,
            $this->kecamatan,
            $this->kabupaten,
            $this->provinsi,
            $this->kode_pos ? 'PSTP ' . $this->kode_pos : null,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Calculate age from tanggal_lahir
     */
    public function getUmurAttribute(): ?int
    {
        if (!$this->tanggal_lahir) {
            return null;
        }

        return $this->tanggal_lahir->age;
    }

    /**
     * Get formatted tanggal lahir
     */
    public function getFormattedTanggalLahirAttribute(): ?string
    {
        if (!$this->tanggal_lahir) {
            return null;
        }

        return $this->tanggal_lahir->format('d/m/Y');
    }

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
     * Koordinator yang melakukan verifikasi lapangan (bukan verifikator dokumen).
     */
    public function verifiedByKoordinator()
    {
        return $this->belongsTo(\App\Models\Superadmin\Koordinator::class, 'verified_by_koordinator');
    }

    /**
     * User (Superadmin/Admin Umum) yang melakukan verifikasi final (tahap 2), sebelum
     * data dikirim ke Urusin Secara Online. Lihat .agent/workflows/data-entry-integrasi.md.
     */
    public function verifiedByFinal()
    {
        return $this->belongsTo(User::class, 'verified_by_final');
    }

    /**
     * Apakah data usaha yang dibutuhkan payload NIB & Halal Urusin Secara Online sudah lengkap.
     */
    public function getUrusinDataUsahaLengkapAttribute(): bool
    {
        return (bool) ($this->nama_usaha && $this->tempat_lahir && $this->jenis_usaha
            && $this->modal_usaha && $this->alamat_usaha
            && $this->provinsi_kode && $this->kabupaten_kode
            && $this->kecamatan_kode && $this->kelurahan_kode
            && $this->jenis_produk_halal && $this->bahan_utama_halal);
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
        // Observer (progress log + FCM notification)
        static::observe(DataLapanganObserver::class);
        // Auto-generate no_registrasi
        static::creating(function ($dataLapangan) {
            if (empty($dataLapangan->no_registrasi)) {
                $dataLapangan->no_registrasi = static::generateNoRegistrasi();
            }
        });

        // Logika Pembayaran
        static::updated(function ($dataLapangan) {
            if (
                $dataLapangan->isDirty('status_pembayaran') &&
                $dataLapangan->status_pembayaran === 'DIBAYAR'
            ) {
                $fee = self::resolveFee($dataLapangan);

                $existingCashflowPemasukan = CashflowsKoordinator::where('data_lapangan_id', $dataLapangan->id)
                    ->where('tipe', 'PEMASUKAN')
                    ->first();

                if (! $existingCashflowPemasukan) {
                    CashflowsKoordinator::create([
                        'data_lapangan_id' => $dataLapangan->id,
                        'tipe' => 'PEMASUKAN',
                        'nominal' => $fee,
                        'keterangan' => 'Pembayaran untuk '.$dataLapangan->nama_pu.' (NIK: '.$dataLapangan->nik.')',
                        'tanggal' => now(),
                    ]);
                }

                $existingCashflowPengeluaran = Cashflow::where('data_lapangan_id', $dataLapangan->id)
                    ->where('tipe', 'Pengeluaran')
                    ->first();

                if (! $existingCashflowPengeluaran) {
                    Cashflow::create([
                        'data_lapangan_id' => $dataLapangan->id,
                        'tipe' => 'Pengeluaran',
                        'jumlah' => $fee,
                        'keterangan' => 'Pembayaran untuk '.$dataLapangan->enumerator->nama_lengkap.' - '.$dataLapangan->nama_pu.' (NIK: '.$dataLapangan->nik.')',
                        'tanggal' => now(),
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
     *
     * Public: dipakai juga oleh WrgroupPayloadBuilder (app/Services/Integrasi/Wrgroup/) agar
     * fee yang dilaporkan ke WRGROUP persis sama dengan yang dibukukan ke cashflow lokal di
     * booted() di bawah — satu sumber kebenaran, bukan duplikasi jadwal fee.
     */
    public static function resolveFee(self $dataLapangan): int
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

        // Fallback ke Fee Enumerator (Global/Provinsi) untuk data sebelum semua schedule
        $koordinator = $dataLapangan->enumerator->koordinator;
        $fee = app(\App\Services\Superadmin\FeeEnumeratorService::class)->resolveForKoordinator($koordinator);

        return $fee?->nominal_fee ?? 0;
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

    private static function generateNoRegistrasi(): string
    {
        $tahun = date('Y');
        $prefix = 'KH'.$tahun.'-';

        // Ambil nomor urut terbesar yang sudah ada, bukan count
        $last = static::where('no_registrasi', 'like', $prefix.'%')
            ->lockForUpdate()  // ← lock row agar tidak race condition
            ->max('no_registrasi');

        if ($last) {
            $lastUrutan = (int) substr($last, strlen($prefix));
            $urutan = $lastUrutan + 1;
        } else {
            $urutan = 1;
        }

        return $prefix.str_pad($urutan, 5, '0', STR_PAD_LEFT);
    }
}
