# Workflow: Dashboard & Portal Koordinator

> Portal login terpisah untuk role `koordinator`.
> Mengacu pada `.agent/pattern-sistem.md`.
> **Status: sudah diimplementasikan** (dashboard, data enumerator, data lapangan + verifikasi, pengumuman, tiket) dan sudah diverifikasi lewat browser. Item yang masih perlu dikerjakan ditandai eksplisit di tiap bagian (lihat khususnya §3 — galeri foto pada halaman detail data lapangan).

---

## Ringkasan

Koordinator mendapat portal login sendiri dengan 5 menu utama:

| Menu | URL | Keterangan |
|---|---|---|
| **Dashboard** | `/koordinator/dashboard` | Analytics KPI enumerator + stats |
| **Data Enumerator** | `/koordinator/enumerator` | Daftar enumerator di bawah koordinator ini |
| **Data Lapangan** | `/koordinator/data-lapangan` | Data lapangan enumerator + verifikasi |
| **Pengumuman** | `/koordinator/pengumuman` | Baca pengumuman dari Admin Umum |
| **Tiket** | `/koordinator/tiket` | Buat & kelola aduan koordinator |

---

## Arsitektur Umum

### Middleware & Auth
Koordinator login via sistem auth yang sudah ada (role `koordinator`).
Semua route koordinator dibungkus middleware `auth` + `role:koordinator`.

### Resolve Koordinator dari Auth
```php
// Di setiap controller koordinator:
$koordinator = auth()->user()->koordinator; // via relasi User->hasOne(Koordinator)
// Pastikan relasi ini ada di User model
```

### Namespace Controllers
`App\Http\Controllers\Koordinator\`

### Views
`resources/views/koordinator/`

---

## 1. Dashboard Analytics

### Route
`GET /koordinator/dashboard` → `Koordinator\DashboardController@index`

### Data yang ditampilkan

**Stat Cards (baris atas):**

| Card | Query |
|---|---|
| Total Enumerator | `Enumerator::where('koordinator_id', $koordinator->id)->count()` |
| Enumerator Aktif | `...->where('status', 'Aktif')->count()` |
| Total Data Bulan Ini | `DataLapangan` via enumerator, filter `created_at` bulan ini |
| Total Terbit SH | `DataLapangan` via enumerator, filter `status = TERBIT SH` |

**KPI Table per Enumerator:**

Tabel listing enumerator + progress KPI mereka:

| Kolom | Source |
|---|---|
| Nama Enumerator | `enumerator.nama_lengkap` |
| Status | `enumerator.status` |
| Data Bulan Ini | `COUNT(data_lapangans)` bulan ini |
| Target Bulanan | **20 data** (hardcoded minimum, atau dari config) |
| Progress % | `(data_bulan_ini / target) * 100` |
| Progress Bar | Visual `<div>` bar sesuai % |
| KPI Status | ≥ 100% → `adm-badge-success` "Tercapai" / < 100% → `adm-badge-pending` "Belum" |
| Terbit SH | `COUNT` status TERBIT SH bulan ini |

> **Query efisien** — gunakan `withCount` + `whereHas` agar tidak N+1:

```php
$enumerators = Enumerator::where('koordinator_id', $koordinator->id)
    ->withCount([
        'dataLapangans as data_bulan_ini' => fn($q) => $q
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year),
        'dataLapangans as terbit_sh_bulan_ini' => fn($q) => $q
            ->where('status', 'TERBIT SH')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year),
    ])
    ->get();
```

**Grafik (opsional, fase 2):** Chart.js bar chart — data per enumerator vs target.

### Controller `DashboardController`
```php
public function index()
{
    $koordinator = auth()->user()->koordinator;

    $totalEnumerator  = Enumerator::where('koordinator_id', $koordinator->id)->count();
    $enumeratorAktif  = Enumerator::where('koordinator_id', $koordinator->id)->where('status', 'Aktif')->count();

    // Stat data lapangan bulan ini via subquery
    $enumeratorIds = Enumerator::where('koordinator_id', $koordinator->id)->pluck('id');
    $dataBulanIni  = DataLapangan::whereIn('enumerator_id', $enumeratorIds)
        ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
    $dataTerbitSH  = DataLapangan::whereIn('enumerator_id', $enumeratorIds)
        ->where('status', 'TERBIT SH')->count();

    // KPI per enumerator
    $enumerators = Enumerator::where('koordinator_id', $koordinator->id)
        ->withCount([...]) // seperti di atas
        ->get();

    $targetBulanan = 20; // konstanta KPI minimum

    return view('koordinator.dashboard', compact(
        'koordinator', 'totalEnumerator', 'enumeratorAktif',
        'dataBulanIni', 'dataTerbitSH', 'enumerators', 'targetBulanan'
    ));
}
```

---

## 2. Data Enumerator

### Routes
```
GET  /koordinator/enumerator          → index (DataTables)
GET  /koordinator/enumerator/data     → data (JSON)
GET  /koordinator/enumerator/{enum}   → show (detail)
```

### Scope Data
Hanya enumerator dengan `koordinator_id = $koordinator->id`.
**Tidak boleh** melihat enumerator koordinator lain.

### Kolom DataTables

| # | Nama | No. Registrasi | Telepon | Status | Data Bulan Ini | Total Data | Aksi |
|---|---|---|---|---|---|---|---|

### `data()` endpoint
```php
$query = Enumerator::where('koordinator_id', $koordinator->id)
    ->withCount([
        'dataLapangans',
        'dataLapangans as data_bulan_ini_count' => fn($q) => $q
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year),
    ]);
```

### View `show` — Detail Enumerator
- Info dasar (nama, telepon, status, no rekening)
- Statistik KPI (data bulan ini vs target, % progress)
- Tabel data lapangan milik enumerator ini (read-only di sini)

### Catatan
Koordinator **tidak bisa edit/hapus** enumerator — hanya bisa lihat.
Pengelolaan enumerator tetap di Superadmin.

---

## 3. Data Lapangan + Verifikasi Koordinator

### Alur Verifikasi Berjenjang (Overview)

Data lapangan yang diinput Enumerator (termasuk foto: `foto_ktp`, `foto_rumah`, `foto_pendamping`, `foto_produk`, dst) melewati **3 tahap** sebelum masuk ke Data Entry:

```
Enumerator input data + foto (foto_ktp, foto_rumah, foto_pendamping, foto_produk, ...)
        ↓
1. Koordinator verifikasi (modul ini)
   → lihat foto & data yang sudah diisi Enumerator, putuskan:
     Terverifikasi / Perlu Koreksi
        ↓ (jika Terverifikasi)
2. Superadmin / Admin Umum verifikasi
   → tahap approval kedua/final sebelum data dikirim ke Data Entry
   → mekanisme detail ada di modul Superadmin/Admin Umum masing-masing,
     di luar cakupan dokumen koordinator ini
        ↓ (jika disetujui)
3. Data Entry
   → sekarang menggunakan pihak ketiga: Urusin Secara Online
   → ⏸️ HOLD — integrasi pihak ketiga ini BELUM diimplementasikan.
     Spesifikasi teknis (API, format data, autentikasi) sudah didokumentasikan
     di `.agent/workflows/data-entry-integrasi.md`, tapi implementasi kodenya
     belum dimulai — masih ada beberapa keputusan terbuka di dokumen tersebut
     (§5) yang perlu dikonfirmasi dulu. Jangan bangun otomatisasi/integrasi ini
     sampai ada instruksi eksplisit untuk mengimplementasikannya.
```

> Koordinator (tahap 1) **tidak** memutuskan kelulusan akhir data — verifikasi koordinator adalah saringan awal berbasis foto & kelengkapan kunjungan lapangan. Keputusan final tetap di tangan Superadmin/Admin Umum (tahap 2).

### Konsep Verifikasi Koordinator (Minim Manipulasi Data Aktual)

Foto lapangan — `foto_ktp`, `foto_rumah`, `foto_pendamping`, `foto_produk` (dan `foto_produk_2` s/d `foto_produk_5` jika diisi) — **sudah diupload oleh Enumerator** saat input data awal. **Koordinator TIDAK mengunggah foto apa pun** — perannya murni melihat & menilai apa yang sudah ada.

Koordinator **tidak mengubah data isian enumerator** (nama PU, NIK, produk, alamat, foto, dll). Yang boleh dilakukan koordinator hanyalah:
1. **Melihat foto lapangan** yang sudah diisi Enumerator, untuk menilai kelengkapan & validitas kunjungan.
2. **Tentukan status verifikasi** — Terverifikasi atau Perlu Koreksi.
3. **Tulis catatan** — opsional, khususnya jika Perlu Koreksi.

### Wajib: Tampilkan Galeri Foto di Halaman Detail

Halaman detail (`koordinator/data-lapangan/{dl}` → `show.blade.php`) **wajib menampilkan foto-foto berikut** (jika terisi), agar koordinator bisa memverifikasi secara visual sebelum mengambil keputusan:

| Field | Keterangan |
|---|---|
| `foto_ktp` | Foto KTP pelaku usaha |
| `foto_rumah` | Foto lokasi/rumah usaha |
| `foto_pendamping` | Foto bersama pendamping/enumerator saat kunjungan |
| `foto_produk` | Foto produk utama |
| `foto_produk_2` s/d `foto_produk_5` | Foto produk tambahan (tampilkan hanya yang terisi) |

Setiap foto ditampilkan sebagai thumbnail yang bisa diperbesar (klik untuk lightbox/zoom), berlabel nama field-nya. Jika sebuah field foto kosong, tampilkan placeholder "Belum ada foto" — jangan sembunyikan barisnya, supaya koordinator sadar ada foto yang belum diisi Enumerator (indikasi data belum lengkap, relevan untuk keputusan Perlu Koreksi).

> **Status implementasi:** sudah diimplementasikan di `show.blade.php` koordinator.

### Alur Verifikasi (Chain)

```
Enumerator input data + foto
        ↓
  status: PENDING
  verifikasi_koordinator: Belum
        ↓
Koordinator review foto & data di halaman detail, lalu verifikasi
        ↓
  verifikasi_koordinator: Terverifikasi / Perlu Koreksi   ← titik ini
        ↓ (jika Terverifikasi)
Superadmin / Admin Umum review (tahap 2 — final approval)
        ↓ (jika disetujui)
Data Entry via pihak ketiga Urusin Secara Online — ⏸️ HOLD
  (spesifikasi teknis: lihat data-entry-integrasi.md)
```

> Data dengan `verifikasi_koordinator = Belum` atau `Perlu Koreksi` belum lolos tahap 1 — idealnya hanya data `Terverifikasi` yang masuk antrian review Superadmin/Admin Umum (tahap 2). Implementasi filter/antrian ini ada di modul Superadmin/Admin Umum masing-masing.

### Migration

```php
Schema::table('data_lapangans', function (Blueprint $table) {
    $table->enum('verifikasi_koordinator', ['Belum', 'Terverifikasi', 'Perlu Koreksi'])
          ->default('Belum')
          ->after('status');
    $table->text('catatan_koordinator')->nullable()->after('verifikasi_koordinator');
    $table->timestamp('verified_at_koordinator')->nullable()->after('catatan_koordinator');
    $table->unsignedBigInteger('verified_by_koordinator')->nullable()->after('verified_at_koordinator');
    $table->foreign('verified_by_koordinator')->references('id')->on('koordinators');
});
```

**Nilai enum `verifikasi_koordinator`:**
- `Belum` → default, belum direview koordinator
- `Terverifikasi` → foto & data sudah dicek koordinator, valid secara lapangan
- `Perlu Koreksi` → ada masalah, enumerator perlu perbaiki data/foto

### Routes (sudah diimplementasikan)
```
GET  /koordinator/data-lapangan                 → index (DataTables)
GET  /koordinator/data-lapangan/data            → data (JSON)
GET  /koordinator/data-lapangan/{dl}            → show (detail read-only + galeri foto)
POST /koordinator/data-lapangan/{dl}/verifikasi → verifikasi (AJAX JSON, tanpa upload file)
```

### Scope Data
```php
$enumeratorIds = Enumerator::where('koordinator_id', $koordinator->id)->pluck('id');
$query = DataLapangan::whereIn('enumerator_id', $enumeratorIds);
```

### Kolom DataTables Index (sudah diimplementasikan)

| # | No. Reg | Nama PU | Enumerator | Status | Foto | Verifikasi | Tgl Input | Aksi |
|---|---|---|---|---|---|---|---|---|

**Kolom `Foto`** — indikator kelengkapan foto yang diisi Enumerator (bukan status kerja koordinator, koordinator tidak upload apa pun):
- Ikon ✅ hijau jika `foto_ktp`, `foto_rumah`, `foto_pendamping`, `foto_produk` semuanya terisi
- Ikon ⚠️ amber jika ada salah satu yang masih kosong

**Badge `verifikasi_koordinator`:**
- `Belum` → `adm-badge-pending`
- `Terverifikasi` → `adm-badge-success`
- `Perlu Koreksi` → `adm-badge-danger`

**Aksi per baris (sudah diimplementasikan):**
- Tombol "Detail" → buka halaman `show` (lihat data + galeri foto lengkap)
- Tombol "Verifikasi" → buka **modal** verifikasi (AJAX JSON, sudah diimplementasikan)

> Karena koordinator tidak mengunggah foto apa pun, verifikasi tidak butuh `multipart/form-data` — cukup modal AJAX ringan (JSON), sudah diimplementasikan persis seperti ini di `resources/views/koordinator/data-lapangan/index.blade.php` (`bukaModalVerifikasi()` + `btnSimpanVerifikasi`). Halaman `show` tetap dipertahankan khusus untuk melihat detail + galeri foto secara penuh, bukan untuk aksi verifikasi.

### Halaman `show.blade.php` — Detail + Galeri Foto (read-only)

Layout:
- Info dasar: No. Reg, Nama PU, NIK, Enumerator, Alamat, Status proses *(sudah ada)*
- Status verifikasi koordinator saat ini (badge) + catatan + siapa & kapan diverifikasi *(sudah ada)*
- **Galeri Foto** — grid thumbnail untuk `foto_ktp`, `foto_rumah`, `foto_pendamping`, `foto_produk`, `foto_produk_2`–`foto_produk_5`, klik untuk lightbox, placeholder untuk field kosong (lihat "Wajib: Tampilkan Galeri Foto" di atas)

Aksi verifikasi tetap lewat modal yang sama seperti di index (dipicu dari tombol di halaman ini juga), bukan form terpisah di halaman `show`.

### Modal Verifikasi (AJAX JSON — sudah diimplementasikan)

Modal berisi: info ringkas (No. Reg, Nama PU), select Keputusan (Terverifikasi / Perlu Koreksi), textarea Catatan (opsional). Submit via `fetch()` POST JSON ke endpoint verifikasi, lalu `window.dataTableInstance.ajax.reload(null, false)`. Sudah diimplementasikan di `resources/views/koordinator/data-lapangan/index.blade.php`.

### Controller `Koordinator\DataLapanganController` (sudah diimplementasikan)
```php
// index() + data() → scoped by koordinator_id
// show()           → read-only detail + galeri foto (foto_* diakses langsung dari $dataLapangan di view)
// verifikasi()     → AJAX JSON (bukan multipart), hanya update:
//                      verifikasi_koordinator, catatan_koordinator,
//                      verified_at_koordinator, verified_by_koordinator
//                    Field yang TIDAK BOLEH diupdate:
//                      nama_pu, nik, alamat, produk, foto_*, email, telephone, dll
```

### Service `KoordinatorDataLapanganService` (sudah diimplementasikan)
```php
public function verifikasi(DataLapangan $dataLapangan, Koordinator $koordinator, array $data): DataLapangan
{
    // Guard: pastikan data milik enumerator koordinator ini
    if ($dataLapangan->enumerator->koordinator_id !== $koordinator->id) {
        throw new \Exception('Data lapangan ini bukan di bawah koordinasi Anda.');
    }

    return DB::transaction(function () use ($dataLapangan, $koordinator, $data) {
        $dataLapangan->update([
            'verifikasi_koordinator'  => $data['verifikasi_koordinator'],
            'catatan_koordinator'     => $data['catatan_koordinator'] ?? null,
            'verified_at_koordinator' => now(),
            'verified_by_koordinator' => $koordinator->id,
        ]);

        return $dataLapangan->fresh();
    });
}
```

### Dampak ke Dashboard Admin Umum / Superadmin

Update filter di `index.blade.php` superadmin & admin_umum — tambahkan filter:
```html
<!-- Filter tambahan di adm-filter-bar -->
<div class="adm-filter-group">
    <label class="adm-filter-label">Verifikasi Koordinator</label>
    <select id="filterVerifKoord" class="adm-select">
        <option value="">Semua</option>
        <option value="Belum">Belum Diverifikasi</option>
        <option value="Terverifikasi" selected>Sudah Terverifikasi</option>
        <option value="Perlu Koreksi">Perlu Koreksi</option>
    </select>
</div>
```

> **Default filter di Admin Umum/Superadmin = `Terverifikasi`** agar antrian mereka (tahap 2)
> hanya menampilkan data yang sudah lolos verifikasi koordinator (tahap 1).
> Superadmin tetap bisa switch ke `Semua` untuk melihat keseluruhan data.
> Implementasi filter ini serta alur approval tahap 2 secara umum ada di modul
> Superadmin/Admin Umum masing-masing — di luar cakupan dokumen koordinator ini.

### Data Entry via Pihak Ketiga (⏸️ HOLD)

Setelah lolos tahap 2 (disetujui Superadmin/Admin Umum), data seharusnya diteruskan ke Data Entry via layanan pihak ketiga **Urusin Secara Online**, yang akan mengurus penerbitan NIB (OSS) dan Sertifikat Halal, lalu mengirim dokumennya kembali ke sistem ini untuk didownload begitu terbit.

> **Status: HOLD.** Integrasi ini belum diimplementasikan. Spesifikasi teknis lengkap (endpoint API, format request/response, autentikasi, mapping data, serta daftar keputusan yang masih perlu dikonfirmasi) sudah ditulis di `.agent/workflows/data-entry-integrasi.md`. Jangan membangun otomatisasi/integrasi ini sampai ada instruksi eksplisit untuk mengimplementasikannya.

---

## 4. Pengumuman

### Konsep
Koordinator **hanya membaca** pengumuman yang dibuat oleh Admin Umum.
Tidak ada aksi CRUD di sini.

### Route
```
GET /koordinator/pengumuman       → index (listing semua pengumuman)
GET /koordinator/pengumuman/{p}   → show (detail + baca konten)
```

### Scope Data
```php
// Ambil semua pengumuman — tidak ada filter role (semua role bisa baca)
Pengumuman::latest()->paginate(10)
```

### View Index
- Grid card pengumuman (bukan tabel) — lebih sesuai konten editorial
- Tiap card: thumbnail foto, judul, nomor, tanggal, tombol "Baca Selengkapnya"

### View Show
- Header: nomor + judul pengumuman
- Foto banner (jika ada)
- Konten `deskripsi` (render sebagai HTML jika CKEditor, atau plain text)
- Tombol kembali ke listing

---

## 5. Tiket

### Konsep
Koordinator bisa membuat aduan/laporan kepada Superadmin/Admin.
Kategori aduan:
- **Data Lapangan** — masalah pada data lapangan spesifik
- **Enumerator** — masalah terkait enumerator
- **Teknis Lapangan** — kendala operasional lainnya

### Tabel `tickets` (existing)
```
id, user_id, no_ticket, subject, description, file, status
```

Tambahkan kolom `kategori` via migration baru:
```php
Schema::table('tickets', function (Blueprint $table) {
    $table->enum('kategori', ['Data Lapangan', 'Enumerator', 'Teknis Lapangan', 'Lainnya'])
          ->default('Lainnya')
          ->after('subject');
    $table->unsignedBigInteger('ref_data_lapangan_id')->nullable()->after('kategori');
    $table->foreign('ref_data_lapangan_id')->references('id')->on('data_lapangans');
    $table->unsignedBigInteger('ref_enumerator_id')->nullable()->after('ref_data_lapangan_id');
    $table->foreign('ref_enumerator_id')->references('id')->on('enumerators');
});
```

### Routes
```
GET  /koordinator/tiket          → index (DataTables, tiket milik koordinator)
GET  /koordinator/tiket/create   → form buat tiket
POST /koordinator/tiket          → store
GET  /koordinator/tiket/{t}      → show (detail + status)
```

### Scope Data
```php
// Hanya tiket milik user yang login
Ticket::where('user_id', auth()->id())
```

### View `index`
Kolom: No. Tiket | Kategori | Subject | Status | Tanggal | Aksi

**Badge status:**
- `Open` → `adm-badge-pending`
- `Proses` → `adm-badge-info`
- `Closed` → `adm-badge-success`

### View `create` — Form Tiket

```
Section: Detail Aduan
  adm-form-grid cols-2:
    [kategori — select]   [ref_data_lapangan — select, conditional jika kategori=Data Lapangan]
    [ref_enumerator — select, conditional jika kategori=Enumerator]

  adm-form-grid cols-1:
    [subject — text]
    [description — textarea]
    [file — file upload, optional, max 2MB]
```

**JS Conditional:**
- Jika `kategori = Data Lapangan` → tampilkan select data lapangan (AJAX load dari `/koordinator/data-lapangan/list`)
- Jika `kategori = Enumerator` → tampilkan select enumerator (AJAX load dari enumerator milik koordinator)

### `no_ticket` auto-generate di service:
```php
'TKT-' . date('Ymd') . '-' . str_pad(Ticket::count() + 1, 4, '0', STR_PAD_LEFT)
```

---

## Struktur File

```
app/
  Http/Controllers/Koordinator/
    DashboardController.php
    EnumeratorController.php
    DataLapanganController.php
    PengumumanController.php
    TiketController.php

  Services/Koordinator/
    DashboardService.php          (query KPI, stats)
    KoordinatorDataLapanganService.php  (verifikasi)
    TiketKoordinatorService.php   (store tiket)

resources/views/koordinator/
  dashboard.blade.php
  enumerator/
    index.blade.php
    show.blade.php
  data-lapangan/
    index.blade.php
    show.blade.php
  pengumuman/
    index.blade.php
    show.blade.php
  tiket/
    index.blade.php
    create.blade.php
    show.blade.php
```

---

## Update Navigation

**File:** `resources/views/layouts/navigation-koordinator.blade.php`

Ganti seluruh isi dengan menu baru:

```html
<li class="nav-item">
    <a href="{{ url('koordinator/dashboard') }}"
       class="nav-link {{ request()->is('koordinator/dashboard') ? 'active' : '' }}">
        <i data-feather="home"></i> Dashboard
    </a>
</li>

<li class="menu-title"><span>Menu Utama</span></li>

<li class="nav-item">
    <a href="{{ url('koordinator/enumerator') }}"
       class="nav-link {{ request()->is('koordinator/enumerator*') ? 'active' : '' }}">
        <i data-feather="users"></i> Data Enumerator
    </a>
</li>

<li class="nav-item">
    <a href="{{ url('koordinator/data-lapangan') }}"
       class="nav-link {{ request()->is('koordinator/data-lapangan*') ? 'active' : '' }}">
        <i data-feather="map"></i> Data Lapangan
    </a>
</li>

<li class="nav-item">
    <a href="{{ url('koordinator/pengumuman') }}"
       class="nav-link {{ request()->is('koordinator/pengumuman*') ? 'active' : '' }}">
        <i data-feather="bell"></i> Pengumuman
    </a>
</li>

<li class="nav-item">
    <a href="{{ url('koordinator/tiket') }}"
       class="nav-link {{ request()->is('koordinator/tiket*') ? 'active' : '' }}">
        <i data-feather="message-square"></i> Tiket
    </a>
</li>
```

---

## Migration Summary

| Migration | Perubahan |
|---|---|
| `add_verifikasi_to_data_lapangans` | +4 kolom: verifikasi_koordinator, catatan_koordinator, verified_at_koordinator, verified_by_koordinator |
| `add_kategori_to_tickets` | +3 kolom: kategori, ref_data_lapangan_id, ref_enumerator_id |

---

## Checklist Implementasi

### Persiapan
- [ ] Migration `add_verifikasi_to_data_lapangans` dibuat & dijalankan
- [ ] Migration `add_kategori_to_tickets` dibuat & dijalankan
- [ ] Model `DataLapangan` — tambah `verifikasi_koordinator`, `catatan_koordinator` ke `$fillable` & `$casts`
- [ ] Model `Ticket` — tambah kolom baru ke `$fillable`
- [ ] Relasi `User::koordinator()` → `hasOne(Koordinator::class, 'user_id')` dipastikan ada
- [ ] Routes koordinator didaftarkan dengan middleware `auth` + `role:koordinator`
- [ ] Navigation-koordinator.blade.php diupdate

### Dashboard
- [ ] `DashboardController` dibuat
- [ ] `DashboardService` dibuat (query KPI dengan withCount)
- [ ] `dashboard.blade.php` — stat cards + KPI table enumerator

### Data Enumerator
- [ ] `EnumeratorController` (index, data, show)
- [ ] DataTables server-side, scoped by koordinator_id
- [ ] `show.blade.php` — detail + statistik KPI enumerator

### Data Lapangan
- [x] `DataLapanganController` (index, data, show, verifikasi)
- [x] `KoordinatorDataLapanganService::verifikasi()` dengan validasi ownership
- [x] DataTables server-side + kolom verifikasi_koordinator badge
- [x] Modal verifikasi — AJAX JSON, hanya update 4 kolom (verifikasi_koordinator, catatan_koordinator, verified_at_koordinator, verified_by_koordinator) — tanpa upload foto
- [x] `show.blade.php` — read-only detail data lapangan
- [x] `show.blade.php` — galeri foto (`foto_ktp`, `foto_rumah`, `foto_pendamping`, `foto_produk`, `foto_produk_2`–`5`), thumbnail + lightbox modal, placeholder "Belum ada foto" untuk field kosong
- [x] Kolom `Foto` di DataTables index — indikator kelengkapan foto Enumerator (✅/⚠️, berdasarkan foto_ktp+foto_rumah+foto_pendamping+foto_produk)

### Pengumuman
- [ ] `PengumumanController` (index, show)
- [ ] Grid card listing pengumuman
- [ ] `show.blade.php` — render konten pengumuman

### Tiket
- [ ] `TiketController` (index, create, store, show)
- [ ] `TiketKoordinatorService::store()` dengan auto no_ticket
- [ ] DataTables scoped by user_id koordinator
- [ ] Form create dengan conditional field (Data Lapangan / Enumerator)
- [ ] JS toggle ref_data_lapangan / ref_enumerator berdasarkan kategori
- [ ] SweetAlert2 Toast untuk semua notifikasi
- [ ] Semua JS di `@push('scripts')`
- [ ] `hashed_id` di semua URL
