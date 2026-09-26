# Workflow: Refaktor Modul Data Lapangan

> Mengacu pada `pattern-sistem.md`, `koordinator-dashboard.md`, dan `data-entry-integrasi.md`.

> **⚠️ Catatan rekonsiliasi (setelah implementasi):** dokumen ini awalnya ditulis dengan asumsi
> `verifikasi_final`, `jalur_data_entry`, `UrusinService`, dan panel Urusin **belum ada sama
> sekali** di kode. Pada kenyataannya, semua itu **sudah diimplementasikan sepenuhnya** di sesi
> sebelumnya (lihat `data-entry-integrasi.md`) — dengan penamaan yang **berbeda** dari yang
> ditulis di dokumen ini:
> - Enum `verifikasi_final` yang benar-benar berjalan: `['Belum', 'Terverifikasi', 'Perlu Koreksi']`
>   (bukan `['Belum', 'Disetujui', 'Ditolak']` seperti draf di bawah).
> - `UrusinService` sudah ada dengan method `me()`, `submitNib()`, `statusNib()`, `downloadNib()`
>   (return array, bukan string path), dst — bukan `getMe()`/`getStatusNib()` seperti draf di bawah.
> - Orkestrasi submit/sync ada di `UrusinSubmissionService` (bukan `DataLapanganVerifikasiService`
>   terpisah), command polling-nya `urusin:sync` (bukan `SyncUrusinStatus`).
>
> **Keputusan (dikonfirmasi):** kode existing tetap dipakai apa adanya — dokumen di bawah ini
> **tidak** dijadikan acuan literal untuk migrasi/service/panel yang sudah berjalan. Yang benar-
> benar diimplementasikan dari dokumen ini hanya bagian yang genuinely baru: **filter & kolom
> badge di `index.blade.php`** (verifikasi koordinator, verifikasi final, jalur data entry).
>
> Bagian **§5b** (koordinator upload foto `foto_proses`/`foto_pendamping`/`foto_rumah`) **tidak
> diimplementasikan** — bertentangan dengan keputusan yang sudah dikonfirmasi di
> `koordinator-dashboard.md`: foto diisi Enumerator, koordinator hanya melihat (view-only), tidak
> pernah upload. Portal koordinator tetap seperti yang sudah dibangun sebelumnya.
>
> Sisa dokumen di bawah ini dibiarkan apa adanya sebagai draf historis — jangan diimplementasikan
> ulang tanpa mengecek dulu apakah sudah ada & apa penamaan yang benar-benar dipakai di kode.

---

## Konteks & Status Existing

| Komponen | Status |
|---|---|
| `show.blade.php` Superadmin | ✅ Sudah lengkap — foto KTP, Rumah, Pendamping, Proses, Produk sudah tampil |
| Panel Urusin Secara Online | ✅ Di-include via `partials/urusin-panel` |
| Verifikasi Koordinator | ✅ Kolom `verifikasi_koordinator` sudah ada di fillable |
| Verifikasi Final | ❌ Belum ada — `verifikasi_final`, `verified_by_final`, dll belum ada |
| `UrusinService` | ❌ Belum ada (checklist integrasi.md sudah centang tapi kode belum ada) |
| `jalur_data_entry` | ❌ Belum ada kolom/migrasi |
| Form `form.blade.php` | ⚠️ Legacy — foto masih pakai `<input type="text">` (path manual, bukan upload file) |
| `index.blade.php` | ⚠️ Perlu filter `verifikasi_koordinator` & `verifikasi_final` |

---

## Alur Verifikasi Lengkap (Final)

```
Enumerator input data (mobile app)
        ↓  status: PENDING, verifikasi_koordinator: Belum
Koordinator upload foto + verifikasi lapangan
        ↓  verifikasi_koordinator: Terverifikasi
Admin Umum / Superadmin review + isi data usaha untuk API
        ↓  verifikasi_final: Disetujui
Sistem kirim ke Urusin Secara Online (NIB + Halal)
        ↓  jalur_data_entry: 'baru', urusin_nib_status: pending
Urusin proses (polling tiap 30 menit)
        ↓  urusin_nib_status: completed, dokumen didownload
TERBIT SH → Enumerator ajukan pembayaran
```

---

## Langkah 1 — Migration Baru

**File:** `YYYY_MM_DD_add_refactor_fields_to_data_lapangans_table.php`

```php
Schema::table('data_lapangans', function (Blueprint $table) {

    // ── Verifikasi Final (Admin Umum/Superadmin) ──
    $table->enum('verifikasi_final', ['Belum', 'Disetujui', 'Ditolak'])
          ->default('Belum')->after('catatan_koordinator');
    $table->text('catatan_final')->nullable()->after('verifikasi_final');
    $table->timestamp('verified_at_final')->nullable()->after('catatan_final');
    $table->unsignedBigInteger('verified_by_final')->nullable()->after('verified_at_final');
    // FK ke users (bukan verifikators — admin umum dan superadmin pakai tabel users)
    $table->foreign('verified_by_final')->references('id')->on('users');

    // ── Data Usaha untuk API Urusin (diisi Admin sebelum verifikasi final) ──
    $table->string('nama_usaha')->nullable()->after('verified_by_final');
    $table->string('tempat_lahir')->nullable()->after('nama_usaha');
    $table->string('jenis_usaha')->nullable()->after('tempat_lahir');
    $table->unsignedBigInteger('modal_usaha')->nullable()->after('jenis_usaha');
    $table->text('alamat_usaha')->nullable()->after('modal_usaha');
    // Kode wilayah untuk API (teks nama sudah ada, kode belum)
    $table->string('provinsi_kode', 10)->nullable()->after('provinsi');
    $table->string('kabupaten_kode', 10)->nullable()->after('kabupaten');
    $table->string('kecamatan_kode', 10)->nullable()->after('kecamatan');
    $table->string('kelurahan_kode', 10)->nullable()->after('kelurahan');
    // Detail produk halal (jenis + bahan untuk API Halal)
    $table->string('jenis_produk_halal')->nullable()->after('nama_produk_5');
    $table->text('bahan_utama_halal')->nullable()->after('jenis_produk_halal');

    // ── Urusin Integration Tracking ──
    $table->string('jalur_data_entry')->default('lama')->after('pengajuan_lewat');
    // NIB
    $table->string('urusin_nib_submission_id')->nullable()->after('jalur_data_entry');
    $table->string('urusin_nib_status')->nullable()->after('urusin_nib_submission_id');
    $table->string('urusin_nib_document_path')->nullable()->after('urusin_nib_status');
    // Halal
    $table->string('urusin_halal_submission_id')->nullable()->after('urusin_nib_document_path');
    $table->string('urusin_halal_status')->nullable()->after('urusin_halal_submission_id');
    $table->string('urusin_halal_document_path')->nullable()->after('urusin_halal_status');
    // Sync
    $table->timestamp('urusin_last_synced_at')->nullable()->after('urusin_halal_document_path');
    $table->text('urusin_gagal_pesan')->nullable()->after('urusin_last_synced_at');
});
```

> Data existing → `jalur_data_entry` default `'lama'` otomatis via `default('lama')`.
> Data baru yang melewati `verifikasiFinal()` → di-set `'baru'` oleh service.

---

## Langkah 2 — Update Model `DataLapangan`

Tambahkan ke `$fillable`:
```php
// Verifikasi Final
'verifikasi_final', 'catatan_final', 'verified_at_final', 'verified_by_final',

// Data Usaha API
'nama_usaha', 'tempat_lahir', 'jenis_usaha', 'modal_usaha', 'alamat_usaha',
'provinsi_kode', 'kabupaten_kode', 'kecamatan_kode', 'kelurahan_kode',
'jenis_produk_halal', 'bahan_utama_halal',

// Urusin Tracking
'jalur_data_entry',
'urusin_nib_submission_id', 'urusin_nib_status', 'urusin_nib_document_path',
'urusin_halal_submission_id', 'urusin_halal_status', 'urusin_halal_document_path',
'urusin_last_synced_at', 'urusin_gagal_pesan',
```

Tambahkan ke `$casts`:
```php
'verified_at_final'      => 'datetime',
'urusin_last_synced_at'  => 'datetime',
'modal_usaha'            => 'integer',
```

Tambahkan scopes:
```php
// Data yang siap diproses admin umum/superadmin
public function scopeSiapVerifikasiFinal($query)
{
    return $query
        ->where('verifikasi_koordinator', 'Terverifikasi')
        ->where('verifikasi_final', 'Belum');
}

// Data jalur baru yang belum disubmit ke Urusin
public function scopeJalurBaruBelumSubmit($query)
{
    return $query
        ->where('jalur_data_entry', 'baru')
        ->where('verifikasi_final', 'Disetujui')
        ->whereNull('urusin_nib_submission_id');
}
```

---

## Langkah 3 — Service Layer

### 3a. `UrusinService` — HTTP Client

**File:** `app/Services/Integrasi/UrusinService.php`

```php
// Baca base_url & api_key dari tabel settingwebsites (pola yang sama dengan WA Gateway)
// Method:
// - getMe(): array                  → GET /api/v1/me
// - submitNib(array $payload): array → POST /api/v1/nib/submit
// - getStatusNib(string $id): array  → GET /api/v1/nib/{id}
// - downloadNib(string $id): string  → GET /api/v1/nib/{id}/download → return path simpan
// - submitHalal(array $payload): array → POST /api/v1/halal/submit
// - getStatusHalal(string $id): array → GET /api/v1/halal/{id}
// - downloadHalal(string $id): string → GET /api/v1/halal/{id}/download → return path simpan
// - buildNibPayload(DataLapangan): array
// - buildHalalPayload(DataLapangan): array
```

### 3b. `DataLapanganVerifikasiService`

**File:** `app/Services/Superadmin/DataLapanganVerifikasiService.php`

```php
// verifikasiFinal(DataLapangan $dl, User $admin, array $data): DataLapangan
//   - Validasi: verifikasi_koordinator harus 'Terverifikasi'
//   - Update: verifikasi_final, catatan_final, verified_at_final, verified_by_final
//   - Jika Disetujui: set jalur_data_entry='baru', trigger submitToUrusin()

// submitToUrusin(DataLapangan $dl): void
//   - Panggil UrusinService::submitNib() + submitHalal() paralel (tidak menunggu NIB selesai)
//   - Simpan submission_id & status awal ke kolom urusin_*
//   - Jika gagal: set urusin_gagal_pesan, JANGAN ubah status utama data lapangan

// kirimUlang(DataLapangan $dl, string $jenis): void — jenis: 'nib'|'halal'|'keduanya'
//   - Untuk tombol "Kirim Ulang" manual oleh admin
//   - Reset urusin_gagal_pesan, coba submit ulang

// syncStatus(DataLapangan $dl): void
//   - Panggil getStatusNib() dan/atau getStatusHalal()
//   - Jika completed: download dokumen, simpan path
//   - Jika keduanya completed: update status data lapangan ke 'TERBIT SH'
```

### 3c. Command Polling

**File:** `app/Console/Commands/SyncUrusinStatus.php`

```php
// php artisan urusin:sync
// Query: DataLapangan jalur_data_entry='baru'
//   + (urusin_nib_status NOT IN ['completed','gagal'] OR urusin_halal_status NOT IN ['completed','gagal'])
// Loop tiap record → DataLapanganVerifikasiService::syncStatus()
// Daftarkan di routes/console.php: Schedule::command('urusin:sync')->everyThirtyMinutes()
```

---

## Langkah 4 — Controller Updates

### 4a. `DataLapanganController` (Superadmin & Admin Umum)

Tambahkan method baru:

```php
// verifikasiFinal(Request $request, $hashedId): JsonResponse (AJAX)
//   - Validasi input: verifikasi_final, catatan_final, + data usaha
//   - Delegasi ke DataLapanganVerifikasiService::verifikasiFinal()
//   - Return JSON {success, message}

// kirimUlangUrusin(Request $request, $hashedId): JsonResponse (AJAX)
//   - Tombol manual kirim ulang
//   - Delegasi ke DataLapanganVerifikasiService::kirimUlang()

// downloadUrusin(Request $request, $hashedId, string $jenis): Response
//   - Serve file NIB/Halal yang sudah tersimpan di storage
```

Update method `data()` DataTables:
- Tambah kolom `verif_koordinator_badge` (Belum/Terverifikasi/Perlu Koreksi)
- Tambah kolom `verif_final_badge`
- Tambah kolom `jalur_badge` (Lama/Baru)
- Update filter: tambah `verifikasi_koordinator` & `verifikasi_final`

Update method `show()`:
- Pass `$verifikasiFinalDone = $dataLapangan->verifikasi_final !== 'Belum'`
- Pass `$siapUrusin = $dataLapangan->nama_usaha && $dataLapangan->tempat_lahir && ...`

---

## Langkah 5 — View Updates

### 5a. `show.blade.php` Superadmin/Admin Umum

**Foto sudah tampil dengan baik** — TIDAK perlu diubah strukturnya.
Yang perlu ditambah:

**1. Badge Verifikasi Koordinator** di header / stepper:
```blade
{{-- Di bawah stepper, sebelum urusin-panel --}}
@if($dataLapangan->verifikasi_koordinator !== 'Belum')
<div style="padding:10px 0; display:flex; gap:8px; align-items:center;">
    <span style="font-size:12px; color:var(--dl-muted);">Verifikasi Koordinator:</span>
    @if($dataLapangan->verifikasi_koordinator === 'Terverifikasi')
        <span class="dl-badge dl-badge-terbit">✅ Terverifikasi</span>
    @else
        <span class="dl-badge dl-badge-ditolak">⚠️ Perlu Koreksi</span>
    @endif
    @if($dataLapangan->catatan_koordinator)
        <span style="font-size:12px;color:var(--dl-muted);">— {{ $dataLapangan->catatan_koordinator }}</span>
    @endif
</div>
@endif
```

**2. `partials/urusin-panel.blade.php`** — Panel ini sudah di-include.
Update isinya dengan:
- Section "Data Usaha untuk Pengajuan" — form isi `nama_usaha`, `tempat_lahir`, `jenis_usaha`, `modal_usaha`, `alamat_usaha`, kode wilayah (cascade), `jenis_produk_halal`, `bahan_utama_halal`
- Section "Verifikasi Final" — tombol Setujui/Tolak (AJAX)
- Section "Status Integrasi" — status NIB & Halal badge, tombol download, tombol kirim ulang

```
[Panel Urusin Layout]

┌─────────────────────────────────────────────────────┐
│ 🔷 Data Usaha untuk Pengajuan API                    │
│  Diisi oleh Admin Umum/Superadmin sebelum verifikasi │
│  [nama_usaha] [tempat_lahir]                         │
│  [jenis_usaha] [modal_usaha]                         │
│  [alamat_usaha — textarea]                           │
│  [provinsi cascade] [kabupaten cascade]              │
│  [jenis_produk_halal] [bahan_utama_halal]            │
│  [Simpan Data Usaha — AJAX]                          │
├─────────────────────────────────────────────────────┤
│ ✅ Verifikasi Final                                   │
│  Hanya aktif jika: verifikasi_koordinator=Terverif   │
│  + data usaha sudah terisi lengkap                   │
│  [catatan_final — textarea]                          │
│  [Setujui & Kirim ke Urusin] [Tolak]                 │
├─────────────────────────────────────────────────────┤
│ 📡 Status Integrasi Urusin Secara Online             │
│  NIB: [badge status] [Download NIB] [Kirim Ulang]   │
│  Halal: [badge status] [Download SH] [Kirim Ulang]  │
│  Last sync: {{ $dl->urusin_last_synced_at }}         │
│  [Sinkronisasi Manual]                               │
└─────────────────────────────────────────────────────┘
```

**Badge status Urusin:**
- `null` → `adm-badge-muted` "Belum Disubmit"
- `pending` → `adm-badge-pending` "Menunggu Proses"
- `processing` → `adm-badge-indigo` "Sedang Diproses"
- `completed` → `adm-badge-success` "Selesai"
- `gagal` → `adm-badge-danger` "Gagal" + pesan error

### 5b. `show.blade.php` Koordinator (Portal Koordinator)

**Halaman ini yang perlu dibangun ulang** — saat ini belum ada portal koordinator.

Layout: **2 kolom**

**Kolom kiri — Data Lapangan (read-only):**
- Info dasar: No. Reg, Nama PU, NIK, Tanggal Lahir, Enumerator
- Info alamat: Provinsi, Kabupaten, Kecamatan, Desa, RT/RW, Alamat Lengkap
- Status current + badge verifikasi koordinator saat ini

**Kolom kanan — Galeri Foto + Form Verifikasi:**

```
┌─────────────────────────────────────────────────────┐
│ 📷 Foto Lapangan                                      │
│                                                       │
│  [Foto KTP]         [Foto Rumah/Lokasi]               │
│  thumbnail + lihat  thumbnail + lihat                 │
│                                                       │
│  [Foto Pendamping]  [Foto Proses]                     │
│  thumbnail + lihat  thumbnail + lihat                 │
│                                                       │
│  Produk:                                              │
│  [Foto Produk 1] [Foto Produk 2] ... (grid)           │
├─────────────────────────────────────────────────────┤
│ ✅ Upload Foto Lapangan & Verifikasi                   │
│  [foto_proses — upload, wajib jika kosong]            │
│  [foto_pendamping — upload, wajib jika kosong]        │
│  [foto_rumah — upload, opsional]                      │
│  [keputusan — select Terverifikasi/Perlu Koreksi]     │
│  [catatan — textarea]                                 │
│  [Simpan Verifikasi — POST form biasa]                │
└─────────────────────────────────────────────────────┘
```

**Penting:** Foto yang sudah ada ditampilkan sebagai preview + info "upload baru untuk mengganti".
Foto yang belum ada ditandai dengan placeholder merah + label "Wajib".

### 5c. `index.blade.php` — Filter Bar Tambahan

Tambah 2 filter dropdown:

```html
<!-- Filter Verifikasi Koordinator -->
<div class="adm-filter-group">
    <label class="adm-filter-label">Verif. Koordinator</label>
    <select id="filterVerifKoord" class="adm-select">
        <option value="">Semua</option>
        <option value="Belum">Belum</option>
        <option value="Terverifikasi">Terverifikasi</option>
        <option value="Perlu Koreksi">Perlu Koreksi</option>
    </select>
</div>

<!-- Filter Verifikasi Final -->
<div class="adm-filter-group">
    <label class="adm-filter-label">Verif. Final</label>
    <select id="filterVerifFinal" class="adm-select">
        <option value="">Semua</option>
        <option value="Belum">Belum</option>
        <option value="Disetujui">Disetujui</option>
        <option value="Ditolak">Ditolak</option>
    </select>
</div>

<!-- Filter Jalur -->
<div class="adm-filter-group">
    <label class="adm-filter-label">Jalur</label>
    <select id="filterJalur" class="adm-select">
        <option value="">Semua</option>
        <option value="lama">Jalur Lama</option>
        <option value="baru">Jalur Baru (Urusin)</option>
    </select>
</div>
```

> **Default untuk Admin Umum:** filter `filterVerifKoord` = `Terverifikasi` di-set otomatis via JS
> saat page load, sehingga antrian admin umum sudah bersih dari data yang belum diverifikasi koordinator.

---

## Langkah 6 — Setting Website (API Key Urusin)

**File:** `resources/views/superadmin/setting/partials/tab-api-keys.blade.php` (atau tambahkan ke tab yang sudah ada)

```
Section: Urusin Secara Online
  [Base URL — text input]
  [API Key — password input + toggle show/hide]
  [Simpan — POST AJAX]
  [Test Koneksi — GET /me AJAX → tampilkan mode, paket, billing_blocked]
```

**Controller method:** `SettingWebsiteController::testUrusin()` → AJAX, panggil `UrusinService::getMe()`.

**Simpan ke:** tabel `settingwebsites` — key `urusin_base_url` dan `urusin_api_key`.

---

## Langkah 7 — AJAX & JS Pattern

Semua aksi di panel Urusin menggunakan AJAX (sesuai pattern-sistem.md):

```javascript
// Simpan Data Usaha
async function simpanDataUsaha(hashedId) {
    const btn = document.getElementById('btnSimpanDataUsaha');
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Menyimpan...`;
    try {
        const res = await fetch(`/superadmin/data-lapangans/${hashedId}/data-usaha`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': getCsrf(), 'Accept': 'application/json',
                       'Content-Type': 'application/json' },
            body: JSON.stringify(collectDataUsahaForm()),
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message);
        Swal.fire({ toast: true, position: 'top-end', icon: 'success',
            title: 'Data usaha disimpan', showConfirmButton: false, timer: 2500 });
    } catch (err) {
        Swal.fire({ toast: true, position: 'top-end', icon: 'error',
            title: err.message, showConfirmButton: false, timer: 3500 });
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Simpan Data Usaha';
    }
}

// Verifikasi Final
async function verifikasiFinal(hashedId, keputusan) {
    const konfirmasi = await Swal.fire({
        title: keputusan === 'Disetujui' ? 'Setujui & Kirim ke Urusin?' : 'Tolak Data ini?',
        text: keputusan === 'Disetujui'
            ? 'Data akan dikirim ke Urusin Secara Online untuk diproses NIB & Sertifikat Halal.'
            : 'Data tidak akan dikirim ke Urusin.',
        icon: keputusan === 'Disetujui' ? 'question' : 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Lanjutkan',
        cancelButtonText: 'Batal',
    });
    if (!konfirmasi.isConfirmed) return;
    // ... AJAX ke /superadmin/data-lapangans/{id}/verifikasi-final
}
```

---

## Ringkasan Kolom Baru di `data_lapangans`

| Kelompok | Kolom Baru | Keterangan |
|---|---|---|
| Verifikasi Final | `verifikasi_final`, `catatan_final`, `verified_at_final`, `verified_by_final` | 4 kolom |
| Data Usaha API | `nama_usaha`, `tempat_lahir`, `jenis_usaha`, `modal_usaha`, `alamat_usaha` | 5 kolom |
| Kode Wilayah | `provinsi_kode`, `kabupaten_kode`, `kecamatan_kode`, `kelurahan_kode` | 4 kolom |
| Produk Halal | `jenis_produk_halal`, `bahan_utama_halal` | 2 kolom |
| Jalur | `jalur_data_entry` | 1 kolom |
| Urusin NIB | `urusin_nib_submission_id`, `urusin_nib_status`, `urusin_nib_document_path` | 3 kolom |
| Urusin Halal | `urusin_halal_submission_id`, `urusin_halal_status`, `urusin_halal_document_path` | 3 kolom |
| Sync | `urusin_last_synced_at`, `urusin_gagal_pesan` | 2 kolom |
| **Total** | | **24 kolom baru** |

---

## Checklist Implementasi

> Status sebenarnya di kode (bukan literal sesuai draf di atas — lihat catatan rekonsiliasi).

### Migration & Model
- [x] Kolom-kolom yang didaftarkan di §"Ringkasan Kolom Baru" **sudah ada semua** — dibuat lewat
      migrasi `2026_08_26_190100_add_urusin_integration_to_data_lapangans_table` (nama file &
      sebagian penamaan enum berbeda dari draf §1, lihat catatan rekonsiliasi di atas)
- [x] `DataLapangan::$fillable` dan `$casts` sudah update (termasuk `urusin_last_synced_at`;
      `modal_usaha` tidak di-cast integer eksplisit, cukup andalkan tipe kolom `unsignedBigInteger`)
- [ ] Scope `siapVerifikasiFinal()` dan `jalurBaruBelumSubmit()` — **belum ditambahkan**, tidak
      ada pemanggil yang membutuhkannya saat ini (query filter sudah cukup lewat controller)

### Service Layer
- [x] HTTP client ke Urusin Secara Online — `App\Services\Integrasi\UrusinService`
- [x] Orkestrasi verifikasi & submit — `App\Services\Integrasi\UrusinSubmissionService` +
      `DataLapanganService::verifikasiFinal()`/`updateDataUsaha()` (bukan file terpisah
      `DataLapanganVerifikasiService` seperti draf §3b)
- [x] Command polling — `php artisan urusin:sync` (`SyncUrusinSubmissions.php`), terdaftar di
      `routes/console.php` tiap 30 menit

### Controller
- [x] `verifikasiFinal()`, `updateDataUsaha()`, `retryUrusin()` — AJAX endpoint (superadmin & admin-umum)
- [ ] `downloadUrusin()` sebagai endpoint terpisah — **tidak dibuat**; dokumen sudah disimpan di
      `storage/app/public/urusin/...` dan diunduh langsung lewat `asset()` URL di panel, tidak
      perlu endpoint controller tambahan
- [ ] `syncManual()` (trigger sync 1 record dari UI) — **belum ada**, sync hanya lewat command terjadwal
- [x] `data()` — kolom `verif_koordinator_badge`, `verif_final_badge`, `jalur_badge` + filter
      `verif_koordinator_filter`, `verif_final_filter`, `jalur_filter` ditambahkan di sesi ini
- [x] Route terdaftar di `web.php` (superadmin & admin-umum)

### Views — Superadmin/Admin Umum
- [x] `partials/urusin-panel.blade.php` — 3 section (Data Usaha, Verifikasi Final, Status Integrasi) sudah ada
- [x] `index.blade.php` — 3 filter dropdown baru (verif koord, verif final, jalur) + kolom badge di tabel — **ditambahkan di sesi ini**
- [x] `show.blade.php` — badge verifikasi koordinator & final ada di dalam `urusin-panel` (bukan di bawah stepper terpisah seperti draf §5a, tapi informasi yang sama tersedia)

### Views — Portal Koordinator
- [x] `koordinator/data-lapangan/show.blade.php` — sudah dibangun sebelumnya: galeri foto
      (`foto_ktp`, `foto_rumah`, `foto_pendamping`, `foto_produk`–`foto_produk_5`) + form verifikasi
      (keputusan + catatan, AJAX)
- [ ] Galeri termasuk `foto_proses` — **belum**, hanya 4 jenis foto + produk yang tampil (lihat
      `koordinator-dashboard.md` §3 untuk daftar field resminya)
- [x] Foto **view-only**, TIDAK ada form upload — sesuai keputusan yang dikonfirmasi ulang di sesi
      ini, §5b draf di atas (upload foto oleh koordinator) sengaja **tidak** diimplementasikan

### Setting Website
- [x] Tab API Keys Urusin di Setting Website
- [x] Method test koneksi (`testUrusinConnection()`, bukan `testUrusin()`) di `SettingwebsiteController`
- [x] Simpan ke tabel `settingwebsites` key `urusin_base_url` & `urusin_api_key`

### Konsistensi Pattern
- [x] Semua notifikasi pakai SweetAlert2 Toast
- [x] Semua AJAX menggunakan loading state pada button
- [x] `hashed_id` digunakan di semua URL baru
- [x] Semua JS baru di `@push('scripts')`
- [x] Alur lama (`data_entry` manual) TIDAK diubah — `jalur_data_entry='lama'` tetap berjalan
      berdampingan (lihat §1.1 `data-entry-integrasi.md`)
