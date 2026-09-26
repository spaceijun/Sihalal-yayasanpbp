<?php

namespace App\Services\Integrasi;

use App\Models\DataLapangan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * UrusinSubmissionService
 *
 * Orkestrasi pengiriman Data Lapangan yang sudah lolos verifikasi final ke Urusin Secara
 * Online, serta sinkronisasi status/dokumen hasil (dipanggil oleh command urusin:sync — lihat
 * app/Console/Commands/SyncUrusinSubmissions.php).
 *
 * Keputusan implementasi (didokumentasikan di sini karena tidak eksplisit di workflow doc):
 *   - Sejak §3.7 data-entry-integrasi.md ditambahkan, NIB & Halal dikirim lewat **Bundle API**
 *     (`POST /bundle/submit`, satu request) — bukan dua request terpisah (`submitNib()` +
 *     `submitHalal()`) seperti desain awal. Pihak Urusin yang menangani dependency NIB → Halal
 *     otomatis di sisi mereka. Method `submitNib()`/`submitHalal()`/`buildHalalPayload()` lama
 *     tetap dipertahankan (tidak dipakai jalur utama) untuk kompatibilitas kalau suatu saat perlu
 *     submit satu jenis saja.
 *   - Bentuk response `POST /bundle/submit` dan `GET /bundle/{nib_id}/status` sudah dikonfirmasi
 *     (§3.7 data-entry-integrasi.md): nested per-jenis `{"nib": {...}, "halal": {...}}`.
 *     `extractBundleResult()` mem-parsing bentuk ini sebagai prioritas utama, dengan beberapa
 *     fallback lain tetap dipertahankan untuk jaga-jaga jika responsnya sedikit berbeda saat
 *     benar-benar sukses (sejauh ini semua percobaan masih gagal di level server mereka).
 *   - Status gabungan NIB+Halal disinkronkan lewat SATU panggilan `GET /bundle/{nib_id}/status`
 *     (`syncBundle()`), bukan dua panggilan terpisah `GET /nib/{id}` + `GET /halal/{id}` seperti
 *     desain awal — method syncNib()/syncHalal() lama dipertahankan untuk kompatibilitas tapi
 *     tidak dipakai jalur utama lagi.
 *   - Kegagalan kirim (network error / API menolak) tidak membatalkan verifikasi final yang
 *     sudah tersimpan — status disimpan sebagai 'gagal_kirim' agar bisa di-retry manual lewat
 *     tombol "Kirim Ulang" di halaman detail. Pesan kegagalannya disimpan di `urusin_gagal_pesan`
 *     supaya terlihat di panel, tidak hanya di storage/logs/laravel.log.
 *   - Sebelum memanggil API, field wajib & kewajaran `tanggal_lahir` dicek dulu di sisi kita
 *     (lihat missingFields()/implausibleTanggalLahir()) — ditemukan langsung dari test sandbox
 *     nyata: data lama tanpa `email`/`tanggal_lahir`, dan sekali `tanggal_lahir` diisi asal
 *     (mis. hari ini, atau tanggal yang tersirat usianya cuma hitungan hari) memicu HTTP 500
 *     generik dari mereka alih-alih ditolak rapi.
 *   - Update terbaru dokumentasi mereka mengubah `nib/submit` & `halal/submit` dari JSON ke
 *     `multipart/form-data` dengan lampiran foto WAJIB (`ktp_photo`, `products[i][foto_produk]`)
 *     yang sebelumnya tidak terdokumentasi sama sekali — kemungkinan besar inilah penyebab 500
 *     generik berulang di log sandbox (field wajib hilang total, bukan cuma kosong). Foto ini
 *     sudah tersimpan di `data_lapangans` (`foto_ktp`, `foto_produk`..`foto_produk_5`) dari alur
 *     entry Enumerator — tidak perlu field baru, hanya perlu dilampirkan saat submit. Sama
 *     seperti `tanggal_lahir`/`email`, kelengkapan foto dicek dulu di sisi kita sebelum memanggil
 *     API (lihat missingPhotoFields()) — kalau file path ada di DB tapi filenya tidak ada di
 *     disk, submit tetap diblokir lokal (bukan dikirim rusak lalu gagal generik di sisi mereka).
 *     Dokumentasi Bundle API (§3.7) sendiri belum diperbarui untuk mencantumkan foto — kita tetap
 *     melampirkannya di jalur Bundle sebagai keputusan implementasi (lihat catatan di §3.7
 *     data-entry-integrasi.md), bukan sesuatu yang resmi dikonfirmasi.
 */
class UrusinSubmissionService
{
    public function __construct(private UrusinService $urusinService) {}

    /**
     * Submit NIB + Halal untuk satu Data Lapangan yang sudah verifikasi final = Terverifikasi,
     * lewat Bundle API (§3.7). Idempotent secara longgar: dilewati kalau kedua jenis sudah
     * punya submission_id dan tidak sedang gagal_kirim.
     */
    public function submitBoth(DataLapangan $dataLapangan): array
    {
        $sudahSukses = $dataLapangan->urusin_nib_submission_id
            && $dataLapangan->urusin_nib_status !== 'gagal_kirim'
            && $dataLapangan->urusin_halal_submission_id
            && $dataLapangan->urusin_halal_status !== 'gagal_kirim';

        if ($sudahSukses) {
            return ['bundle' => null];
        }

        $result = $this->submitBundle($dataLapangan);

        $dataLapangan->forceFill(['urusin_last_synced_at' => now()])->save();

        return ['bundle' => $result];
    }

    /**
     * POST /bundle/submit (§3.7) — submit NIB + Halal sekaligus.
     */
    public function submitBundle(DataLapangan $dataLapangan): array
    {
        $payload = $this->buildBundlePayload($dataLapangan);

        $missing = $this->missingFields($payload, [
            'nama_lengkap', 'nik', 'tempat_lahir', 'tanggal_lahir', 'alamat_ktp',
            'provinsi_kode', 'kabupaten_kode', 'kecamatan_kode', 'kelurahan_kode',
            'nama_usaha', 'jenis_usaha', 'modal_usaha', 'email', 'telepon',
        ]);
        if (empty($payload['products'])) {
            $missing[] = 'products (minimal 1 produk dengan nama_produk terisi)';
        }

        $missing = array_merge($missing, $this->missingPhotoFields($dataLapangan, requireKtp: true, requireProductPhotos: true));

        if ($missing) {
            return $this->gagalkanBundle($dataLapangan, 'Field wajib API Bundle belum lengkap: '.implode(', ', $missing).'.', [
                'missing' => $missing,
            ]);
        }

        if ($tanggalLahirIssue = $this->implausibleTanggalLahir($payload['tanggal_lahir'])) {
            return $this->gagalkanBundle(
                $dataLapangan,
                "tanggal_lahir tidak valid: {$tanggalLahirIssue} (nilai saat ini: {$payload['tanggal_lahir']}). Periksa kembali di data lapangan sebelum kirim ulang.",
                ['tanggal_lahir' => $payload['tanggal_lahir'], 'issue' => $tanggalLahirIssue]
            );
        }

        $files = array_merge($this->buildKtpFile($dataLapangan), $this->buildProductFiles($dataLapangan));

        $result = $this->urusinService->submitBundle($payload, $files);

        if ($result['status']) {
            Log::info('UrusinSubmissionService: submit Bundle berhasil — response mentah (untuk penyesuaian parsing)', [
                'data_lapangan_id' => $dataLapangan->id,
                'response' => $result['data'],
            ]);

            [$nibId, $nibStatus, $halalId, $halalStatus] = $this->extractBundleResult($result['data'] ?? []);

            $dataLapangan->forceFill([
                'urusin_nib_submission_id' => $nibId,
                'urusin_nib_status' => $nibStatus ?? 'waiting_assignment',
                'urusin_halal_submission_id' => $halalId,
                'urusin_halal_status' => $halalStatus ?? 'waiting_nib',
                'urusin_gagal_pesan' => null,
            ])->save();

            return $result;
        }

        Log::error('UrusinSubmissionService: submit Bundle gagal', [
            'data_lapangan_id' => $dataLapangan->id,
            'error' => $result['error'],
        ]);

        return $this->gagalkanBundle($dataLapangan, 'Bundle: '.$result['error'], [], $result);
    }

    private function gagalkanBundle(DataLapangan $dataLapangan, string $message, array $logContext = [], ?array $returnResult = null): array
    {
        Log::warning('UrusinSubmissionService: submit Bundle dilewati/gagal', array_merge([
            'data_lapangan_id' => $dataLapangan->id,
        ], $logContext));

        $dataLapangan->forceFill([
            'urusin_nib_status' => 'gagal_kirim',
            'urusin_halal_status' => 'gagal_kirim',
            'urusin_gagal_pesan' => $message,
        ])->save();

        return $returnResult ?? ['status' => false, 'data' => null, 'error' => $message];
    }

    /**
     * Parsing response POST /bundle/submit — bentuk utama (dikonfirmasi §3.7 data-entry-integrasi.md)
     * adalah nested per-jenis: {"nib": {"submission_id":..., "status":...}, "halal": {...}}.
     * Fallback lain tetap dipertahankan untuk jaga-jaga:
     *   2. Flat berprefiks: {"nib_submission_id":..., "nib_status":..., "halal_submission_id":..., "halal_status":...}
     *   3. Satu submission_id untuk seluruh bundle: {"submission_id":..., "status":...} —
     *      dipakai untuk NIB saja, Halal dibiarkan null.
     *
     * @return array{0: ?string, 1: ?string, 2: ?string, 3: ?string} [nibId, nibStatus, halalId, halalStatus]
     */
    private function extractBundleResult(array $data): array
    {
        if (isset($data['nib']) && is_array($data['nib'])) {
            $nibId = $data['nib']['submission_id'] ?? null;
            $nibStatus = $data['nib']['status'] ?? null;
        } else {
            $nibId = $data['nib_submission_id'] ?? null;
            $nibStatus = $data['nib_status'] ?? null;
        }

        if (isset($data['halal']) && is_array($data['halal'])) {
            $halalId = $data['halal']['submission_id'] ?? null;
            $halalStatus = $data['halal']['status'] ?? null;
        } else {
            $halalId = $data['halal_submission_id'] ?? null;
            $halalStatus = $data['halal_status'] ?? null;
        }

        // Fallback: satu submission_id flat untuk keseluruhan bundle — anggap itu ID sisi NIB.
        if (! $nibId && ! $halalId && ! empty($data['submission_id'])) {
            $nibId = $data['submission_id'];
            $nibStatus = $data['status'] ?? $nibStatus;
        }

        return [$nibId, $nibStatus, $halalId, $halalStatus];
    }

    /**
     * @deprecated Jalur utama sekarang submitBundle(). Dipertahankan untuk kompatibilitas.
     */
    public function submitNib(DataLapangan $dataLapangan): array
    {
        $payload = $this->buildNibPayload($dataLapangan);
        $missing = $this->missingFields($payload, [
            'nama_lengkap', 'nik', 'tempat_lahir', 'tanggal_lahir', 'alamat_ktp',
            'provinsi_kode', 'kabupaten_kode', 'kecamatan_kode', 'kelurahan_kode',
            'nama_usaha', 'jenis_usaha', 'modal_usaha', 'email', 'telepon',
        ]);

        $missing = array_merge($missing, $this->missingPhotoFields($dataLapangan, requireKtp: true, requireProductPhotos: false));

        if ($missing) {
            $message = 'Field wajib API NIB belum lengkap: '.implode(', ', $missing).'.';
            Log::warning('UrusinSubmissionService: submit NIB dilewati (field kosong)', [
                'data_lapangan_id' => $dataLapangan->id,
                'missing' => $missing,
            ]);
            $dataLapangan->forceFill(['urusin_nib_status' => 'gagal_kirim', 'urusin_gagal_pesan' => $message])->save();

            return ['status' => false, 'data' => null, 'error' => $message];
        }

        if ($tanggalLahirIssue = $this->implausibleTanggalLahir($payload['tanggal_lahir'])) {
            $message = "tanggal_lahir tidak valid: {$tanggalLahirIssue} (nilai saat ini: {$payload['tanggal_lahir']}). Periksa kembali di data lapangan sebelum kirim ulang.";
            Log::warning('UrusinSubmissionService: submit NIB dilewati (tanggal_lahir tidak masuk akal)', [
                'data_lapangan_id' => $dataLapangan->id,
                'tanggal_lahir' => $payload['tanggal_lahir'],
                'issue' => $tanggalLahirIssue,
            ]);
            $dataLapangan->forceFill(['urusin_nib_status' => 'gagal_kirim', 'urusin_gagal_pesan' => $message])->save();

            return ['status' => false, 'data' => null, 'error' => $message];
        }

        $result = $this->urusinService->submitNib($payload, $this->buildKtpFile($dataLapangan));

        if ($result['status']) {
            $dataLapangan->forceFill([
                'urusin_nib_submission_id' => $result['data']['submission_id'] ?? null,
                'urusin_nib_status' => $result['data']['status'] ?? 'pending',
                'urusin_gagal_pesan' => null,
            ])->save();
        } else {
            Log::error('UrusinSubmissionService: submit NIB gagal', [
                'data_lapangan_id' => $dataLapangan->id,
                'error' => $result['error'],
            ]);
            $dataLapangan->forceFill([
                'urusin_nib_status' => 'gagal_kirim',
                'urusin_gagal_pesan' => 'NIB: '.$result['error'],
            ])->save();
        }

        return $result;
    }

    /**
     * @deprecated Jalur utama sekarang submitBundle(). Dipertahankan untuk kompatibilitas.
     */
    public function submitHalal(DataLapangan $dataLapangan): array
    {
        $payload = $this->buildHalalPayload($dataLapangan);
        $missing = $this->missingFields($payload, ['nama_usaha', 'alamat_usaha', 'email', 'telepon']);

        if (empty($payload['products'])) {
            $missing[] = 'products (minimal 1 produk dengan nama_produk terisi)';
        }

        $missing = array_merge($missing, $this->missingPhotoFields($dataLapangan, requireKtp: false, requireProductPhotos: true));

        if ($missing) {
            $message = 'Field wajib API Halal belum lengkap: '.implode(', ', $missing).'.';
            Log::warning('UrusinSubmissionService: submit Halal dilewati (field kosong)', [
                'data_lapangan_id' => $dataLapangan->id,
                'missing' => $missing,
            ]);
            $dataLapangan->forceFill(['urusin_halal_status' => 'gagal_kirim', 'urusin_gagal_pesan' => $message])->save();

            return ['status' => false, 'data' => null, 'error' => $message];
        }

        $result = $this->urusinService->submitHalal($payload, $this->buildProductFiles($dataLapangan));

        if ($result['status']) {
            $dataLapangan->forceFill([
                'urusin_halal_submission_id' => $result['data']['submission_id'] ?? null,
                'urusin_halal_status' => $result['data']['status'] ?? 'pending',
                'urusin_gagal_pesan' => null,
            ])->save();
        } else {
            Log::error('UrusinSubmissionService: submit Halal gagal', [
                'data_lapangan_id' => $dataLapangan->id,
                'error' => $result['error'],
            ]);
            $dataLapangan->forceFill([
                'urusin_halal_status' => 'gagal_kirim',
                'urusin_gagal_pesan' => 'Halal: '.$result['error'],
            ])->save();
        }

        return $result;
    }

    /**
     * Cek status NIB & Halal sekaligus lewat GET /bundle/{nib_submission_id}/status (§3.7) —
     * satu request untuk keduanya, jalur utama yang dipakai command urusin:sync. Butuh
     * urusin_nib_submission_id (didapat dari submitBundle()) sebagai kunci; kalau belum ada
     * (mis. data lama yang sempat disubmit terpisah sebelum Bundle API dipakai), fallback ke
     * syncNib()+syncHalal() lama.
     */
    public function syncBundle(DataLapangan $dataLapangan): void
    {
        if (empty($dataLapangan->urusin_nib_submission_id)) {
            return;
        }

        $result = $this->urusinService->statusBundle($dataLapangan->urusin_nib_submission_id);

        if (! $result['status']) {
            return;
        }

        $data = $result['data'] ?? [];
        $nib = $data['nib'] ?? [];
        $halal = $data['halal'] ?? [];

        if (! empty($nib['status'])) {
            $dataLapangan->urusin_nib_status = $nib['status'];
        }
        if (($nib['document_available'] ?? false) && empty($dataLapangan->urusin_nib_document_path)) {
            $this->downloadAndStore($dataLapangan, 'nib', $dataLapangan->urusin_nib_submission_id);
        }

        if (! empty($halal['status'])) {
            $dataLapangan->urusin_halal_status = $halal['status'];
        }
        if (($halal['document_available'] ?? false) && ! empty($dataLapangan->urusin_halal_submission_id) && empty($dataLapangan->urusin_halal_document_path)) {
            $this->downloadAndStore($dataLapangan, 'halal', $dataLapangan->urusin_halal_submission_id);
        }

        $dataLapangan->urusin_last_synced_at = now();
        $dataLapangan->save();
    }

    /**
     * @deprecated Jalur utama sekarang syncBundle(). Dipertahankan untuk kompatibilitas data
     * lama yang sempat disubmit terpisah sebelum Bundle API dipakai.
     *
     * Cek status NIB ke API & simpan dokumen jika sudah tersedia. Dipanggil oleh command sync.
     */
    public function syncNib(DataLapangan $dataLapangan): void
    {
        if (empty($dataLapangan->urusin_nib_submission_id)) {
            return;
        }

        $result = $this->urusinService->statusNib($dataLapangan->urusin_nib_submission_id);

        if (! $result['status']) {
            return;
        }

        $data = $result['data'] ?? [];
        $dataLapangan->urusin_nib_status = $data['status'] ?? $dataLapangan->urusin_nib_status;

        if (($data['document_available'] ?? false) && empty($dataLapangan->urusin_nib_document_path)) {
            $this->downloadAndStore($dataLapangan, 'nib', $dataLapangan->urusin_nib_submission_id);
        }

        $dataLapangan->urusin_last_synced_at = now();
        $dataLapangan->save();
    }

    /**
     * @deprecated Jalur utama sekarang syncBundle(). Dipertahankan untuk kompatibilitas.
     *
     * Cek status Halal ke API & simpan dokumen jika sudah tersedia. Dipanggil oleh command sync.
     */
    public function syncHalal(DataLapangan $dataLapangan): void
    {
        if (empty($dataLapangan->urusin_halal_submission_id)) {
            return;
        }

        $result = $this->urusinService->statusHalal($dataLapangan->urusin_halal_submission_id);

        if (! $result['status']) {
            return;
        }

        $data = $result['data'] ?? [];
        $dataLapangan->urusin_halal_status = $data['status'] ?? $dataLapangan->urusin_halal_status;

        if (($data['document_available'] ?? false) && empty($dataLapangan->urusin_halal_document_path)) {
            $this->downloadAndStore($dataLapangan, 'halal', $dataLapangan->urusin_halal_submission_id);
        }

        $dataLapangan->urusin_last_synced_at = now();
        $dataLapangan->save();
    }

    private function downloadAndStore(DataLapangan $dataLapangan, string $jenis, string $submissionId): void
    {
        $download = $jenis === 'nib'
            ? $this->urusinService->downloadNib($submissionId)
            : $this->urusinService->downloadHalal($submissionId);

        if (! $download['status']) {
            Log::warning("UrusinSubmissionService: gagal download dokumen {$jenis}", [
                'data_lapangan_id' => $dataLapangan->id,
                'submission_id' => $submissionId,
                'error' => $download['error'] ?? null,
            ]);

            return;
        }

        $path = "urusin/{$jenis}/{$dataLapangan->hashed_id}.pdf";
        Storage::disk('public')->put($path, $download['data']);

        if ($jenis === 'nib') {
            $dataLapangan->urusin_nib_document_path = $path;
        } else {
            $dataLapangan->urusin_halal_document_path = $path;
        }
    }

    /**
     * Bangun payload POST /bundle/submit (§3.7 data-entry-integrasi.md) — gabungan field NIB
     * + `products[]` Halal dalam satu request. Tidak menyertakan `alamat_usaha` (tidak ada di
     * contoh body Bundle API — lihat catatan di §3.7 dokumen).
     */
    public function buildBundlePayload(DataLapangan $dataLapangan): array
    {
        $payload = $this->buildNibPayload($dataLapangan);
        $payload['products'] = $this->extractProducts($dataLapangan);

        return $payload;
    }

    /**
     * Bangun payload POST /nib/submit dari Data Lapangan (§3.5 & §4 data-entry-integrasi.md).
     */
    public function buildNibPayload(DataLapangan $dataLapangan): array
    {
        return [
            'nama_lengkap' => $dataLapangan->nama_pu,
            'nik' => $dataLapangan->nik,
            'tempat_lahir' => $dataLapangan->tempat_lahir,
            'tanggal_lahir' => optional($dataLapangan->tanggal_lahir)->format('Y-m-d'),
            'alamat_ktp' => $dataLapangan->full_address ?: $dataLapangan->alamat,
            'provinsi_kode' => $this->normalizeKodeWilayah($dataLapangan->provinsi_kode),
            'kabupaten_kode' => $this->normalizeKodeWilayah($dataLapangan->kabupaten_kode),
            'kecamatan_kode' => $this->normalizeKodeWilayah($dataLapangan->kecamatan_kode),
            'kelurahan_kode' => $this->normalizeKodeWilayah($dataLapangan->kelurahan_kode),
            'nama_usaha' => $dataLapangan->nama_usaha,
            'jenis_usaha' => $dataLapangan->jenis_usaha,
            'modal_usaha' => $dataLapangan->modal_usaha,
            'email' => $dataLapangan->email,
            'telepon' => $dataLapangan->telephone,
        ];
    }

    /**
     * Bangun payload POST /halal/submit dari Data Lapangan (§3.6 & §4 data-entry-integrasi.md).
     */
    public function buildHalalPayload(DataLapangan $dataLapangan): array
    {
        return [
            'nib_submission_id' => $dataLapangan->urusin_nib_submission_id,
            'nama_usaha' => $dataLapangan->nama_usaha,
            'alamat_usaha' => $dataLapangan->alamat_usaha,
            'email' => $dataLapangan->email,
            'telepon' => $dataLapangan->telephone,
            'products' => $this->extractProducts($dataLapangan),
        ];
    }

    /**
     * `jenis_produk`/`bahan_utama` sama untuk semua produk (lihat catatan di §4 dokumen —
     * skema data_lapangans tidak punya kolom per-produk untuk field ini).
     */
    private function extractProducts(DataLapangan $dataLapangan): array
    {
        return collect($this->collectProductSlots($dataLapangan))
            ->map(fn ($slot) => [
                'nama_produk' => $slot['nama'],
                'jenis_produk' => $dataLapangan->jenis_produk_halal,
                'bahan_utama' => $dataLapangan->bahan_utama_halal,
            ])
            ->values()
            ->all();
    }

    /**
     * Pasangan nama produk + path foto per slot (`nama_produk`/`foto_produk` s.d. `_5`),
     * difilter hanya pada slot yang nama produknya terisi. Dipakai bersama oleh
     * extractProducts() (field teks `products[]`) dan buildProductFiles() (lampiran foto)
     * supaya urutan index-nya SELALU selaras satu sama lain — produk ke-N di payload harus
     * berpasangan dengan foto produk ke-N yang sama, bukan foto dari slot lain.
     *
     * @return array<int, array{nama: ?string, foto: ?string}>
     */
    private function collectProductSlots(DataLapangan $dataLapangan): array
    {
        $slots = [
            ['nama' => $dataLapangan->nama_produk, 'foto' => $dataLapangan->foto_produk],
            ['nama' => $dataLapangan->nama_produk_2, 'foto' => $dataLapangan->foto_produk_2],
            ['nama' => $dataLapangan->nama_produk_3, 'foto' => $dataLapangan->foto_produk_3],
            ['nama' => $dataLapangan->nama_produk_4, 'foto' => $dataLapangan->foto_produk_4],
            ['nama' => $dataLapangan->nama_produk_5, 'foto' => $dataLapangan->foto_produk_5],
        ];

        return collect($slots)->filter(fn ($slot) => filled($slot['nama']))->values()->all();
    }

    /**
     * Path absolut foto KTP (`foto_ktp`) sebagai lampiran multipart `ktp_photo`
     * (§3.5/§3.7 data-entry-integrasi.md — update wajib foto). Kosong kalau kolomnya kosong
     * atau filenya tidak ditemukan di disk `public` (path rusak/file terhapus).
     *
     * @return array<int, array{name: string, path: string, filename: string}>
     */
    private function buildKtpFile(DataLapangan $dataLapangan): array
    {
        if (! $path = $this->resolvePhotoPath($dataLapangan->foto_ktp)) {
            return [];
        }

        return [['name' => 'ktp_photo', 'path' => $path, 'filename' => basename($path)]];
    }

    /**
     * Path absolut foto tiap produk sebagai lampiran multipart `products[i][foto_produk]`
     * (§3.6/§3.7 data-entry-integrasi.md — update wajib foto), index selaras dengan
     * extractProducts() lewat collectProductSlots() yang sama.
     *
     * @return array<int, array{name: string, path: string, filename: string}>
     */
    private function buildProductFiles(DataLapangan $dataLapangan): array
    {
        $files = [];

        foreach ($this->collectProductSlots($dataLapangan) as $index => $slot) {
            if ($path = $this->resolvePhotoPath($slot['foto'])) {
                $files[] = ['name' => "products[{$index}][foto_produk]", 'path' => $path, 'filename' => basename($path)];
            }
        }

        return $files;
    }

    /**
     * Path absolut sebuah foto di disk `public` kalau benar-benar ada filenya — mengembalikan
     * null kalau kolom DB kosong ATAU filenya tidak ditemukan (bukan cuma cek kolom terisi),
     * supaya submit tidak dikirim dengan lampiran yang sebenarnya rusak/hilang.
     */
    private function resolvePhotoPath(?string $relativePath): ?string
    {
        if (! $relativePath) {
            return null;
        }

        $absolute = Storage::disk('public')->path($relativePath);

        return is_file($absolute) ? $absolute : null;
    }

    /**
     * Cek kelengkapan foto wajib sebelum submit (§3.5/§3.6/§3.7 data-entry-integrasi.md —
     * update dokumentasi terbaru menambahkan syarat foto KTP untuk NIB dan foto tiap produk
     * untuk Halal, yang tidak ada di draf sebelumnya). Sama seperti missingFields(): gagal
     * lokal dengan pesan jelas, bukan dikirim dan gagal generik di sisi mereka.
     *
     * @return array<int, string>
     */
    private function missingPhotoFields(DataLapangan $dataLapangan, bool $requireKtp, bool $requireProductPhotos): array
    {
        $missing = [];

        if ($requireKtp && ! $this->resolvePhotoPath($dataLapangan->foto_ktp)) {
            $missing[] = 'foto_ktp (foto KTP belum ada / file tidak ditemukan)';
        }

        if ($requireProductPhotos) {
            foreach ($this->collectProductSlots($dataLapangan) as $index => $slot) {
                if (! $this->resolvePhotoPath($slot['foto'])) {
                    $nomor = $index + 1;
                    $missing[] = "foto_produk untuk produk ke-{$nomor} ({$slot['nama']}) belum ada / file tidak ditemukan";
                }
            }
        }

        return $missing;
    }

    /**
     * Kode wilayah kita bersumber dari WilayahService (wilayah.id), yang memakai format
     * ber-titik (mis. "36.01.17.2001"). Contoh dokumentasi Urusin Secara Online (§3.5
     * data-entry-integrasi.md) menampilkan kode TANPA titik (mis. "3578010001"). Ditemukan
     * sebagai kemungkinan penyebab HTTP 500 saat test sandbox pertama — dinormalisasi di sini
     * (hapus semua titik) supaya formatnya cocok dengan yang mereka harapkan.
     */
    private function normalizeKodeWilayah(?string $kode): ?string
    {
        return $kode ? str_replace('.', '', $kode) : $kode;
    }

    /**
     * Cek field wajib mana saja yang masih kosong dari sebuah payload.
     *
     * @return array<int, string>
     */
    private function missingFields(array $payload, array $requiredKeys): array
    {
        return collect($requiredKeys)
            ->filter(fn ($key) => $payload[$key] === null || $payload[$key] === '')
            ->values()
            ->all();
    }

    /**
     * Sanity check tanggal_lahir sebelum dikirim ke API. Ditambahkan setelah ditemukan record
     * nyata (id 166) yang terkirim dengan tanggal_lahir = hari ini, lalu tanggal yang usianya
     * cuma hitungan hari (keduanya kemungkinan nilai placeholder saat mengisi form, bukan
     * tanggal lahir sungguhan) dan mendapat HTTP 500 generik dari Urusin Secara Online —
     * kemungkinan besar tanggal yang mustahil ini yang membuat validasi di sisi mereka crash
     * alih-alih menolak dengan error 400 yang rapi. Batas bawah 17 tahun mengikuti syarat usia
     * KTP/NIK di Indonesia — data usaha/NIB secara realistis tidak mungkin milik anak-anak.
     *
     * @return string|null  Pesan alasan jika tidak masuk akal, null jika terlihat wajar.
     */
    private function implausibleTanggalLahir(?string $tanggalLahirYmd): ?string
    {
        if (! $tanggalLahirYmd) {
            return null; // sudah ditangkap oleh missingFields()
        }

        $date = \DateTime::createFromFormat('Y-m-d', $tanggalLahirYmd);
        if (! $date) {
            return 'format tanggal tidak valid';
        }

        $today = new \DateTime('today');
        if ($date >= $today) {
            return 'tidak boleh hari ini atau di masa depan';
        }

        $age = $today->diff($date)->y;
        if ($age > 120) {
            return 'usia tersirat lebih dari 120 tahun';
        }
        if ($age < 17) {
            return 'usia tersirat kurang dari 17 tahun (di bawah syarat usia KTP/NIK)';
        }

        return null;
    }
}
