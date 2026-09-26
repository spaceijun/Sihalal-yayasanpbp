# Refaktor Alur Data Entry — Integrasi Pihak Ketiga (Urusin Secara Online)

> **Status dokumen:** **sudah diimplementasikan** (lihat Checklist di bagian bawah) — service HTTP
> client, verifikasi final, tracking submission, polling status, download dokumen, dan konfigurasi
> API key di Superadmin > Setting Website semuanya sudah ada di kode. Yang **belum** dilakukan:
> pengujian end-to-end dengan API key & base URL nyata dari Urusin Secara Online (belum ada
> kredensial sungguhan) — jadi beberapa detail response API (lihat catatan "Perubahan dari draf
> sebelumnya" di §3) masih perlu dikonfirmasi begitu integrasi benar-benar diuji ke server asli.

---

## 1. Kenapa alur ini berubah

Selama ini proses OSS (NIB) dan Sertifikat Halal dientry **manual** oleh role `data_entry` di sistem
ini sendiri (upload PDF hasil pengurusan, direview oleh superadmin, dst — lihat §2 "Alur Lama").
Sekarang entry-nya dialihkan ke pihak ketiga **urusinsecara.online**, yang akan mengurus
penerbitan NIB (OSS) dan Sertifikat Halal berdasarkan data yang kita kirim ke mereka lewat API,
lalu mengembalikan dokumennya begitu terbit.

**Alur baru (ringkas, sesuai instruksi):**

```
Data Lapangan masuk (Enumerator input, termasuk foto)
        ↓
Verifikasi Koordinator (Terverifikasi / Perlu Koreksi) — sudah diimplementasikan
        ↓ (jika Terverifikasi)
Verifikasi FINAL Admin Umum / Superadmin — belum ada implementasi khusus, lihat §5
        ↓ (jika disetujui)
Kirim data ke Urusin Secara Online (API submit NIB, lalu submit Halal)
        ↓
Pihak ketiga memproses pengurusan NIB (OSS) & Sertifikat Halal
        ↓ (setelah terbit)
Dokumen NIB & Sertifikat Halal dikirim balik ke sistem ini → bisa didownload
```

Ini **menggantikan** peran manual role `data_entry` (OSS/SIHALAL) dalam mengurus/upload dokumen —
lihat §5 untuk status modul lama tersebut.

### 1.1 Periode Migrasi — role & menu Data Entry lama TIDAK dihapus

**Dikonfirmasi:** role `data_entry`, login, menu, dan seluruh controller/view lamanya (§2) **tidak
diubah maupun dihapus** oleh refaktor ini. Alur baru (§1) hanya berlaku untuk **Data Lapangan yang
baru masuk setelah integrasi ini aktif**. Data yang saat ini sudah terlanjur berjalan lewat alur
manual (`TERVERIFIKASI` → diambil `data_entry` → upload `file_oss`/`file_sihalal` → review
superadmin) **tetap diselesaikan lewat jalur lama itu sampai tuntas** — bukan dipindahkan paksa
ke alur baru di tengah jalan.

Konsekuensi untuk implementasi nanti:
- Role, login, dan menu `data_entry` (OSS & SIHALAL) tetap ada dan berfungsi seperti sekarang,
  tanpa perubahan apa pun.
- Kedua alur (lama dan baru) **berjalan berdampingan** untuk sementara, bukan alur lama langsung
  dinonaktifkan saat alur baru diaktifkan.
- Perlu ada penanda yang jelas di level `data_lapangans` mana yang "ikut alur lama" vs "ikut alur
  baru", supaya data yang sudah diambil `data_entry` secara manual tidak ikut ke-submit otomatis
  ke Urusin Secara Online, dan sebaliknya. Cara paling sederhana: data yang statusnya sudah lewat
  dari `PENDING`/`TERVERIFIKASI` awal (sudah ada progress `data_entry` tercatat) tetap di jalur
  lama; hanya data yang verifikasi finalnya (§5.1) terjadi **setelah** integrasi aktif yang masuk
  ke jalur baru. Detail pastinya perlu didesain saat implementasi, bukan diputuskan di sini.
- Ini otomatis menjawab poin terbuka §5.6 di bawah: **tidak dihapus, dipertahankan penuh untuk
  data yang sudah/sedang berjalan manual**, alur baru murni untuk data baru ke depannya.

---

## 2. Alur Lama (existing, untuk konteks/perbandingan)

Kode existing yang relevan (tidak diubah oleh dokumen ini):

- `App\Models\DataLapangan` — kolom `status` (`PENDING`, `TERVERIFIKASI`, `PROGRESS OSS`,
  `PROGRESS SIHALAL`, `TERBIT SH`, `DITOLAK`, `REVISI`), `file_oss`, `file_sihalal`,
  `email_sihalal`, `pengajuan_lewat`.
- `App\Http\Controllers\Superadmin\DataLapanganController::updateEmail()` — superadmin
  memverifikasi data (assign `verifikator_id`, `tanggal_verifikasi`) → status jadi `TERVERIFIKASI`.
- `App\Http\Controllers\DataEntry\DataLapanganController` — role `data_entry` (sub-tipe `OSS`
  atau `SIHALAL`, lihat `App\Models\DataEntry::entry_type`) mengambil data berstatus
  `TERVERIFIKASI`/`PROGRESS OSS`, upload PDF hasil pengurusan manual via `uploadFile()`, tercatat
  di `App\Models\DataEntryProgress` dengan status `PENDING`.
- Superadmin mereview `DataEntryProgress` (`DITERIMA`/`REVISI`/`DITOLAK`) — saat `DITERIMA`,
  status `data_lapangans` baru benar-benar berubah (`PROGRESS OSS` → `PROGRESS SIHALAL` → dst).

Modul ini sepenuhnya manual: dokumen OSS/Sihalal didapat "di luar sistem" oleh manusia (role
`data_entry`), lalu di-upload sebagai file PDF.

---

## 3. Spesifikasi Teknis API Urusin Secara Online

Diberikan oleh pemilik produk dalam bentuk halaman dokumentasi API (Blade view milik pihak
ketiga sendiri — bukan bagian dari codebase ini). Kita akan menjadi **klien** dari
API ini: mendaftar untuk dapat API key, lalu memanggil endpoint di bawah dari sistem kita.

> **Catatan penamaan (update):** revisi dokumentasi terbaru yang diberikan menyebut produknya
> secara eksplisit sebagai **"REST API urusinsecara online"**, jadi istilah **Urusin Secara
> Online** yang dipakai di dokumen ini sudah sesuai — draf sebelumnya sempat menampilkan nama
> "UrusinAja" (`urusinaja.id`) di beberapa contoh URL, tapi itu tampaknya sisa dari versi
> dokumentasi yang belum konsisten dan sudah digantikan revisi ini. Domain/base URL persis untuk
> `.env` (`URUSIN_BASE_URL`) tetap perlu diminta langsung ke pemilik produk saat pendaftaran API
> key — dokumentasi hanya menampilkan `{{ url('/api/v1') }}` relatif terhadap situs mereka
> sendiri, bukan domain publiknya.

### 3.1 Overview

|             |                                                                                 |
| ----------- | ------------------------------------------------------------------------------- |
| Base URL    | `{URUSIN_BASE_URL}/api/v1` (lihat catatan penamaan di atas)                     |
| Format      | `application/json`                                                              |
| Autentikasi | Header `Authorization: Bearer {API_KEY}`                                        |
| Mode        | **Sandbox** (`ua_sandbox_...`) atau **Production** (`ua_live_...`) — lihat §3.3 |
| Rate limit  | tergantung paket API key (contoh dokumentasi: 60 req/menit)                     |

Header wajib di setiap request:

```
Authorization: Bearer ua_live_YOUR_API_KEY_HERE
Content-Type: application/json
Accept: application/json
```

### 3.2 Prefix API Key (Sandbox vs Production)

| Prefix               | Mode       | Keterangan                                                                |
| -------------------- | ---------- | ------------------------------------------------------------------------- |
| `ua_sandbox_xxxx...` | Sandbox    | Request disimulasikan, **tidak** menyimpan data nyata, **tidak** ditagih. |
| `ua_live_xxxx...`    | Production | Request memproses data nyata dan **ditagih** sesuai paket yang berlaku.   |

### 3.3 Mode Sandbox

Untuk pengujian integrasi tanpa membuat data nyata dan tanpa tagihan:

|                           | Sandbox       | Production                                                |
| ------------------------- | ------------- | --------------------------------------------------------- |
| Prefix key                | `ua_sandbox_` | `ua_live_`                                                |
| Simpan ke database mereka | Tidak         | Ya                                                        |
| Invoice/tagihan           | Tidak ada     | Dikirim tiap Senin                                        |
| Diblokir jika belum bayar | Tidak pernah  | Ya, API diblokir mulai Selasa jika invoice belum dilunasi |
| Response                  | Mock/simulasi | Nyata, diproses Data Entry mereka                         |
| Dokumen hasil             | — (simulasi)  | Bisa diunduh                                              |

Contoh response Sandbox (`POST /api/v1/nib/submit`):

```json
{
    "success": true,
    "message": "[SANDBOX] Pengajuan NIB berhasil disimulasikan.",
    "sandbox": true,
    "data": {
        "submission_id": "sandbox_AbCdEfGhIjKl",
        "order_id": "sandbox_MnOpQrStUvWx",
        "status": "pending",
        "package": "NIB Perorangan Dasar",
        "created_at": "2026-08-26T12:00:00.000000Z",
        "note": "Mode sandbox: tidak ada data nyata yang dibuat, tidak ada tagihan."
    }
}
```

Mode ditentukan oleh key yang dipakai (prefix-nya), bukan oleh parameter request — jadi
sistem kita perlu tahu di `.env`/config apakah key yang tersimpan itu sandbox atau production
(atau baca dari endpoint `/me`, lihat §3.4).

### 3.4 Endpoint `/me` — Info & Status API Key

**`GET /api/v1/me`** — cek info, mode aktif, paket, dan status billing dari API key yang dipakai.
Berguna untuk divalidasi sebelum melakukan request submission.

Response `200`:

```json
{
    "success": true,
    "data": {
        "label": "API Key - Nama Klien",
        "mode": "production",
        "is_sandbox": false,
        "scopes": ["nib", "halal"],
        "package_nib": "NIB Perorangan Dasar",
        "package_nib_price": "500000.00",
        "package_halal": "Halal UMK Reguler",
        "package_halal_price": "1000000.00",
        "rate_limit_per_minute": 60,
        "total_requests": 142,
        "billing_blocked": false,
        "last_used_at": "2026-08-26T10:30:00.000000Z",
        "note": null
    }
}
```

> Jika `billing_blocked: true`, semua endpoint submission mengembalikan error `403` sampai
> invoice dilunasi. Untuk mode Sandbox, `billing_blocked` selalu `false`.

Perhatikan bahwa `package_nib` dan `package_halal` sudah melekat pada API key itu sendiri
(satu key = satu paket NIB + satu paket Halal yang sudah ditentukan saat berlangganan) — ini
relevan untuk keputusan `package_id` di §5.3, karena kemungkinan `package_id` yang dikirim saat
submit **harus** cocok dengan paket yang terdaftar di key tersebut, bukan bebas dipilih per
pengajuan. Ini belum dikonfirmasi, masih dugaan berdasarkan bentuk response `/me`.

### 3.5 API NIB (Nomor Induk Berusaha)

**`POST /api/v1/nib/submit`** — buat pengajuan NIB baru.

> **Update PENTING (kemungkinan penyebab error 500 yang berulang saat test sandbox):** request
> body **berubah dari JSON menjadi `multipart/form-data`**, dan sekarang **wajib menyertakan file
> foto KTP** (`ktp_photo`, image jpg/png, maks 5MB) — field ini **tidak ada sama sekali** di draf
> dokumentasi sebelumnya, yang kita implementasikan sebagai JSON polos tanpa foto apa pun. Semua
> percobaan submit sebelumnya kemungkinan besar gagal (500 generik, bukan 400 rapi) justru karena
> field wajib ini hilang total, bukan cuma bernilai kosong.

Request body (`multipart/form-data`):

```
nama_lengkap    = "Budi Santoso"        // required | string
nik             = "3578012304850001"    // required | 16 digit
tempat_lahir    = "Surabaya"            // required | string
tanggal_lahir   = "1985-03-23"          // required | format Y-m-d
alamat_ktp      = "Jl. Merdeka No. 10"  // required | string
provinsi_kode   = "35"                  // required | kode provinsi
kabupaten_kode  = "3578"                // required | kode kabupaten
kecamatan_kode  = "357801"              // required | kode kecamatan
kelurahan_kode  = "3578010001"          // required | kode kelurahan
nama_usaha      = "Toko Maju Jaya"      // required | string
jenis_usaha     = "Perdagangan"         // required | string
modal_usaha     = 5000000               // required | integer (rupiah)
email           = "budi@toko.com"       // required | valid email
telepon         = "081234567890"        // required | string
ktp_photo       = [FILE]                // required | image (jpg/png) maks 5MB — SYARAT UTAMA
```

> `package_id` **tidak dikirim** di body — paket NIB sudah otomatis ditentukan dari konfigurasi
> API key yang dipakai (lihat `package_nib` di respons `/me`, §3.4). Ini menjawab pasti poin
> terbuka §5.3 di bawah.

Response sukses `201`:

```json
{
    "success": true,
    "message": "Pengajuan NIB berhasil dibuat.",
    "data": {
        "submission_id": "abc12345-def67890-ghijklmnop12",
        "order_id": "xyz-order-hashed-id",
        "status": "pending",
        "package": "NIB Perorangan Dasar",
        "ktp_photo": "uploaded",
        "created_at": "2026-08-26T10:00:00.000000Z"
    }
}
```

> Status awal contoh submit standalone ini masih `pending`, tapi contoh response Bundle API di
> §3.7 (submit gabungan) menunjukkan status awal `waiting_assignment` untuk NIB — kemungkinan
> `pending` di sini cuma belum disinkronkan ke istilah terbaru mereka. Field `payment_url` tetap
> tidak ada; billing tampaknya sepenuhnya ditangani lewat invoice mingguan (§3.3), bukan per
> pengajuan.

**`GET /api/v1/nib/{submission_id}`** — cek status pengajuan.

Response sukses `200` — saat status `completed`:

```json
{
    "success": true,
    "data": {
        "submission_id": "abc12345-...",
        "status": "completed",
        "status_label": "Selesai",
        "document_available": true,
        "document": {
            "name": "NIB_BudiSantoso.pdf",
            "mime_type": "application/pdf",
            "size": "245.3 KB",
            "download_url": "https://urusinsecara.online/api/v1/nib/{submission_id}/download"
        },
        "timeline": [
            { "status": "pending", "date": "2026-08-20T10:00:00Z" },
            { "status": "processing", "date": "2026-08-21T09:00:00Z" },
            { "status": "completed", "date": "2026-08-22T14:30:00Z" }
        ],
        "created_at": "2026-08-20T10:00:00Z",
        "completed_at": "2026-08-22T14:30:00Z"
    }
}
```

> **Perubahan dari draf sebelumnya:** versi dokumentasi ini tidak lagi menampilkan `nib_number`
> langsung di response status — hanya `document_available` + objek `document` (dengan
> `download_url`). Belum jelas apakah `nib_number` masih ada di response nyata (mungkin
> tersembunyi di contoh ini) atau memang sudah tidak dikirim terpisah dan harus dibaca dari
> dokumen PDF-nya sendiri. Perlu dicek langsung ke API mereka saat implementasi, jangan diasumsikan.

**`GET /api/v1/nib/{submission_id}/download`** — unduh file NIB (tersedia saat status selesai).

Endpoint ini mengembalikan **file PDF/dokumen NIB secara langsung** (binary stream, bukan JSON).
Simpan dengan ekstensi sesuai `mime_type` dari endpoint status. Pastikan
`document_available: true` sebelum memanggil endpoint ini.

### 3.6 API Sertifikat Halal

**`POST /api/v1/halal/submit`** — buat pengajuan sertifikat halal.

> **Update PENTING (sama seperti §3.5):** request body **berubah dari JSON menjadi
> `multipart/form-data`**, dan sekarang **wajib menyertakan foto tiap produk**
> (`products[i][foto_produk]`, image jpg/png, maks 5MB per file) — juga tidak ada sama sekali di
> draf sebelumnya. `package_id` sudah dihapus total dari draf sebelumnya (paket otomatis dari
> API key, `package_halal` di `/me`, §3.4) — itu tetap berlaku.

Request body (`multipart/form-data`, notasi array pakai kurung siku standar form):

```
nib_submission_id          = "abc12345-..."   // nullable | hashed_id NIB yang selesai (jika sudah ada)
nama_usaha                 = "Toko ABC"       // required | string
alamat_usaha               = "Jl. Raya No. 5" // required | string
email                      = "toko@abc.com"   // required | valid email
telepon                    = "081234567890"   // required | string
products[0][nama_produk]   = "Keripik Tempe"  // required | string
products[0][jenis_produk]  = "Makanan Ringan" // required | string
products[0][bahan_utama]   = "Tempe, Tepung"  // required | string
products[0][foto_produk]   = [FILE]           // required | image (jpg/png) maks 5MB — SYARAT UTAMA
products[1][nama_produk]   = "Sirup Jahe"     // produk ke-2 (jika lebih dari satu)
products[1][foto_produk]   = [FILE]           // required per produk
```

> `nib_submission_id` **nullable** (opsional) — submit Halal tidak wajib menunggu NIB selesai
> lebih dulu, bisa diproses independen. Body tetap wajib menyertakan `nama_usaha`, `alamat_usaha`,
> `email`, `telepon` langsung di level pengajuan Halal (lihat §4 untuk mapping ke
> `data_lapangans`).

Response sukses `201`:

```json
{
    "success": true,
    "message": "Pengajuan Sertifikat Halal berhasil dibuat.",
    "data": {
        "submission_id": "halal-abc12345-def67890",
        "order_id": "xyz-order-hashed-id",
        "status": "pending",
        "package": "Halal UMK Reguler",
        "total_products": 2,
        "created_at": "2026-08-26T10:00:00.000000Z"
    }
}
```

**`GET /api/v1/halal/{submission_id}`** — cek status sertifikasi halal.

Response sukses `200` — saat status `completed`:

```json
{
    "success": true,
    "data": {
        "submission_id": "halal-abc12345-...",
        "status": "completed",
        "status_label": "Selesai",
        "document_available": true,
        "document": {
            "name": "SertifikatHalal_TokoABC.pdf",
            "mime_type": "application/pdf",
            "size": "312.8 KB",
            "download_url": "https://urusinsecara.online/api/v1/halal/{submission_id}/download"
        },
        "products": [
            { "nama_produk": "Keripik Tempe", "jenis_produk": "Makanan Ringan" }
        ],
        "created_at": "2026-08-20T09:00:00Z",
        "completed_at": "2026-08-25T16:00:00Z"
    }
}
```

> **Perubahan dari draf sebelumnya:** `sertifikat_halal_number` dan `sertifikat_url` langsung di
> response sudah tidak ada lagi — diganti pola yang sama dengan NIB (`document_available` +
> objek `document` dengan `download_url`). Field `products[].status` (`approved` dst) di draf
> sebelumnya juga tidak muncul lagi di contoh ini. Sama seperti NIB, jangan asumsikan field mana
> yang benar-benar dikirim di response nyata — konfirmasi saat implementasi.

**`GET /api/v1/halal/{submission_id}/download`** — unduh sertifikat halal (tersedia saat status
selesai). Mengembalikan **file PDF secara langsung** (binary stream). Pastikan
`document_available: true` dari endpoint status sebelum memanggil endpoint ini.

### 3.7 Bundle API — Submit NIB + Halal Sekaligus

> **Update (dipakai mulai sekarang, menggantikan pola submit terpisah §3.5/§3.6):** karena sistem
> kita memang selalu mengirim NIB dan Halal bersamaan begitu verifikasi final disetujui (lihat
> keputusan di `UrusinSubmissionService`), pihak Urusin Secara Online menyediakan endpoint bundle
> khusus untuk ini — satu request, mereka yang menangani dependency NIB → Halal secara otomatis
> di sisi mereka (bukan kita yang menautkan lewat `nib_submission_id` seperti pola lama).

**`POST /api/v1/bundle/submit`** — submit NIB + Halal sekaligus dalam satu request.

> **⚠️ Inkonsistensi dokumentasi yang belum diklarifikasi pihak Urusin Secara Online:** revisi
> dokumentasi yang sama yang menambahkan syarat foto wajib di §3.5 (`ktp_photo`) dan §3.6
> (`products[i][foto_produk]`), serta mengubah kedua endpoint itu dari JSON ke
> `multipart/form-data`, **tidak mengubah contoh body Bundle sama sekali** — masih ditampilkan
> sebagai JSON polos tanpa field foto apa pun. Karena Bundle API secara konsep menggabungkan
> kedua submission itu, dan gejala error 500 generik (§3.8, bukan 400 rapi) yang berulang di log
> sandbox sangat cocok dengan pola "field wajib hilang total menyebabkan exception tak tertangani
> di sisi mereka" — sama seperti yang baru dikonfirmasi terjadi di NIB/Halal standalone —
> **keputusan implementasi kita: kirim `POST /bundle/submit` juga sebagai `multipart/form-data`,
> menyertakan `ktp_photo` dan `products[i][foto_produk]` seperti endpoint standalone**, bukan
> mengikuti literal contoh JSON di dokumentasi ini apa adanya. Ini murni dugaan berdasar pola,
> **belum dikonfirmasi resmi oleh Urusin Secara Online** — kalau ternyata Bundle memang tidak
> butuh/tidak menerima file, secara teknis tetap aman (Laravel di sisi mereka akan mengabaikan
> field yang tidak divalidasi), tapi perlu diperhatikan di log test berikutnya apakah 500-nya
> akhirnya hilang atau berubah bentuk.

Request body (kita kirim sebagai `multipart/form-data`, notasi array kurung siku seperti §3.6;
dokumentasi asli menampilkannya sebagai JSON — lihat catatan di atas):

```json
{
    "nama_lengkap": "Budi Santoso",
    "nik": "3578012304850001",
    "tempat_lahir": "Surabaya",
    "tanggal_lahir": "1985-03-23",
    "alamat_ktp": "Jl. Merdeka No. 10",
    "provinsi_kode": "35",
    "kabupaten_kode": "3578",
    "kecamatan_kode": "357801",
    "kelurahan_kode": "3578010001",
    "nama_usaha": "Toko Maju Jaya",
    "jenis_usaha": "Perdagangan",
    "modal_usaha": 5000000,
    "email": "budi@tokumajujaya.com",
    "telepon": "081234567890",
    "products": [
        {
            "nama_produk": "Keripik Tempe",
            "jenis_produk": "Makanan Ringan",
            "bahan_utama": "Tempe, Tepung"
        }
    ]
}
```

Plus, per keputusan di atas: `ktp_photo` = [FILE], `products[0][foto_produk]` = [FILE], dst.

> **Perhatikan (masih berlaku):** body ini adalah gabungan field NIB (§3.5) + `products[]` dari
> Halal (§3.6) — **tidak ada** `alamat_usaha` terpisah seperti di `POST /halal/submit`. Kemungkinan
> besar `alamat_ktp` dipakai juga sebagai alamat usaha di jalur bundle ini, tapi ini **belum
> dikonfirmasi** — tidak ada di contoh yang diberikan. `package_id` juga tidak ada di body ini,
> konsisten dengan §3.5/§3.6 (paket otomatis dari API key).

**Alur status otomatis setelah submit:**

```
POST /bundle/submit
        ↓
  NIB:   waiting_assignment   ← langsung masuk antrian penugasan, TIDAK menunggu pembayaran
                                 (bundle ditagih lewat invoice berkala, sama seperti §3.5/§3.6)
  Halal: waiting_nib          ← Halal menunggu, belum diproses
        ↓ (NIB selesai QC)
  Halal: waiting_assignment   ← Halal otomatis maju ke antrian penugasan,
                                 TANPA request tambahan dari client
```

Response sukses `201`:

```json
{
    "success": true,
    "message": "Bundle pengajuan NIB dan Sertifikat Halal berhasil dibuat.",
    "data": {
        "nib": {
            "submission_id": "abc12345-nib-...",
            "order_id": "nib-order-hashed",
            "status": "waiting_payment",
            "status_label": "Menunggu Pembayaran",
            "package": "NIB Perorangan Dasar",
            "created_at": "2026-08-26T10:00:00.000000Z"
        },
        "halal": {
            "submission_id": "halal-xyz789-...",
            "order_id": "halal-order-hashed",
            "status": "waiting_nib",
            "status_label": "Menunggu NIB Selesai",
            "package": "Halal UMK Reguler",
            "note": "Halal akan otomatis dilanjutkan ke antrian penugasan setelah NIB selesai.",
            "created_at": "2026-08-26T10:00:00.000000Z"
        }
    }
}
```

> **Dikonfirmasi:** response-nya nested per-jenis (`data.nib.*` dan `data.halal.*`), sesuai
> dugaan implementasi awal. `UrusinSubmissionService::extractBundleResult()` sudah menangani
> bentuk ini sebagai prioritas utama.

**`GET /api/v1/bundle/{nib_submission_id}/status`** — cek status NIB & Halal sekaligus dalam satu
request, dikunci oleh `submission_id` sisi NIB (bukan ID bundle terpisah).

Response sukses `200`:

```json
{
    "success": true,
    "data": {
        "nib": {
            "submission_id": "abc12345-nib-...",
            "status": "completed",
            "status_label": "Selesai",
            "document_available": true,
            "document": {
                "name": "NIB_BudiSantoso.pdf",
                "mime_type": "application/pdf",
                "size": "245.3 KB",
                "download_url": "https://urusinsecara.online/api/v1/nib/{submission_id}/download"
            },
            "completed_at": "2026-08-22T14:30:00.000000Z"
        },
        "halal": {
            "submission_id": "halal-xyz789-...",
            "status": "waiting_assignment",
            "status_label": "Menunggu Penugasan",
            "document_available": false,
            "document": null,
            "completed_at": null
        }
    }
}
```

Status `waiting_nib` berarti Halal masih menunggu NIB selesai; begitu NIB `completed`, Halal
otomatis maju ke `waiting_assignment` tanpa request tambahan. Command `urusin:sync`
(`UrusinSubmissionService::syncBundle()`) memakai endpoint ini sebagai jalur utama pengecekan
status — satu request untuk kedua jenis, menggantikan dua panggilan `GET /nib/{id}` +
`GET /halal/{id}` terpisah dari desain awal (§3.5/§3.6, tetap dipertahankan sebagai fallback
untuk record lama yang sempat disubmit terpisah sebelum Bundle API dipakai).

> **Masih belum dikonfirmasi:** apakah `alamat_ktp` benar-benar dipakai juga sebagai alamat usaha
> di jalur bundle (§4 di bawah), karena `alamat_usaha` terpisah tidak ada di body maupun response
> bundle ini.

### 3.8 Kode Status HTTP

| Kode | Status            | Keterangan                                |
| ---- | ----------------- | ----------------------------------------- |
| 200  | OK                | Request berhasil                          |
| 201  | Created           | Resource berhasil dibuat                  |
| 400  | Bad Request       | Parameter tidak valid/tidak lengkap       |
| 401  | Unauthorized      | API key tidak valid/tidak disertakan      |
| 403  | Forbidden         | API key tidak punya akses ke endpoint ini |
| 404  | Not Found         | Resource tidak ditemukan                  |
| 429  | Too Many Requests | Melampaui rate limit                      |
| 500  | Server Error      | Kesalahan internal di sisi mereka         |

### 3.9 Format Error

```json
{
    "success": false,
    "message": "Deskripsi error",
    "errors": {
        "nik": ["NIK harus 16 digit"],
        "nama_usaha": ["Nama usaha wajib diisi"]
    },
    "code": 401
}
```

---

## 4. Mapping data kita → payload API

| Field payload API                                                     | Sumber di `data_lapangans`             | Catatan                                                                                                                                                                                                                                                                                                                                          |
| --------------------------------------------------------------------- | -------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `nama_lengkap`                                                        | `nama_pu`                              |                                                                                                                                                                                                                                                                                                                                                  |
| `nik`                                                                 | `nik`                                  |                                                                                                                                                                                                                                                                                                                                                  |
| `tempat_lahir`                                                        | —                                      | **tidak ada kolomnya saat ini**                                                                                                                                                                                                                                                                                                                  |
| `tanggal_lahir`                                                       | `tanggal_lahir`                        | sudah ada (cast `date`)                                                                                                                                                                                                                                                                                                                          |
| `alamat_ktp`                                                          | `alamat` / `full_address` accessor     |                                                                                                                                                                                                                                                                                                                                                  |
| `provinsi_kode`, `kabupaten_kode`, `kecamatan_kode`, `kelurahan_kode` | —                                      | **gap** — `data_lapangans` hanya menyimpan `provinsi`/`kabupaten`/`kecamatan`/`kelurahan` sebagai teks nama, bukan kode wilayah. Perlu diputuskan: tambah kolom kode wilayah baru (isi manual/cascading dropdown seperti yang sudah dibangun untuk Koordinator di koordinator-refactor.md), atau lookup nama→kode via tabel wilayah saat submit. |
| `nama_usaha`                                                          | `nama_produk` (atau field usaha lain?) | perlu diperjelas — `data_lapangan` punya `nama_produk` s/d `nama_produk_5` (produk halal), belum tentu sama dengan "nama usaha"                                                                                                                                                                                                                  |
| `jenis_usaha`                                                         | —                                      | **tidak ada kolomnya saat ini**                                                                                                                                                                                                                                                                                                                  |
| `modal_usaha`                                                         | —                                      | **tidak ada kolomnya saat ini**                                                                                                                                                                                                                                                                                                                  |
| `email`                                                               | `email`                                |                                                                                                                                                                                                                                                                                                                                                  |
| `telepon`                                                             | `telephone`                            |                                                                                                                                                                                                                                                                                                                                                  |
| ~~`package_id`~~ (NIB & Halal)                                        | —                                      | **sudah tidak relevan** — field ini dihapus dari kedua endpoint submit di revisi dokumentasi terbaru; paket otomatis dari API key (lihat §3.5, §3.6).                                                                                                                                                                                            |
| `nama_usaha` (Halal)                                                  | `nama_produk` (atau field usaha lain?) | field yang sama juga dibutuhkan NIB — lihat baris `nama_usaha` di atas, pertanyaannya sama.                                                                                                                                                                                                                                                     |
| `alamat_usaha` (Halal)                                                | `alamat` / `full_address` accessor?    | belum jelas apakah ini harus sama persis dengan `alamat_ktp` yang dikirim ke NIB, atau alamat usaha yang berbeda dari alamat KTP — `data_lapangans` saat ini hanya punya satu alamat.                                                                                                                                                          |
| `email`, `telepon` (Halal)                                            | `email`, `telephone`                   | sama seperti field NIB — kini dikirim ulang di body Halal karena Halal bisa submit independen dari NIB (§3.6).                                                                                                                                                                                                                                  |
| `nib_submission_id` (Halal, nullable)                                 | —                                      | hasil dari submit NIB sebelumnya (§3.5) — perlu disimpan di skema tracking submission kita (lihat §5.4/checklist) supaya bisa dikirim ulang ke Halal kalau mau ditautkan.                                                                                                                                                                       |
| `products[].nama_produk` dst (Halal)                                  | `nama_produk`, `nama_produk_2..5`      | perlu `jenis_produk` & `bahan_utama` per produk — **tidak ada kolomnya saat ini**                                                                                                                                                                                                                                                                |
| `ktp_photo` (NIB/Bundle, file, §3.5)                                  | `foto_ktp`                             | **sudah ada** — kolom lama, diisi Enumerator saat entry. Dikirim sebagai lampiran multipart, bukan path/URL. Kalau file tidak ditemukan di disk `public`, submit diblokir lokal (mirip `missingFields()`) sebelum memanggil API.                                                                                                              |
| `products[i][foto_produk]` (Halal/Bundle, file, §3.6)                 | `foto_produk`, `foto_produk_2..5`      | **sudah ada** — dipasangkan per-index dengan `nama_produk_N`/`foto_produk_N` yang sama supaya urutannya selaras dengan `products[]` di payload teks. Sama seperti `ktp_photo`, submit diblokir lokal kalau foto produk manapun belum ada di disk.                                                                                             |

Sebagian besar baris bertanda "gap"/"tidak ada kolomnya saat ini" di atas **sudah diselesaikan**
oleh migrasi & implementasi nyata (lihat §5.4/checklist) — kolom `tempat_lahir`, `jenis_usaha`,
`modal_usaha`, kode wilayah (`provinsi_kode` dst), `nama_usaha`, `alamat_usaha`,
`jenis_produk_halal`, `bahan_utama_halal` semuanya sudah ditambahkan ke `data_lapangans`. Tabel
di atas dipertahankan apa adanya sebagai riwayat perencanaan awal — bukan cerminan skema
sekarang.

---

## 5. Hal yang masih perlu diputuskan sebelum implementasi

Dokumen ini **tidak** memutuskan hal-hal berikut secara sepihak — perlu konfirmasi eksplisit
sebelum ditulis jadi kode:

1. **Verifikasi final Admin Umum/Superadmin** — apakah ini pakai field baru yang mirror
   `verifikasi_koordinator` (mis. `verifikasi_final`, `catatan_final`, `verified_at_final`,
   `verified_by_final`), atau di-reuse dari mekanisme `TERVERIFIKASI` + `updateEmail()` yang
   sudah ada? Keduanya punya konsekuensi berbeda ke UI Superadmin/Admin Umum yang sudah ada.
2. **Field yang belum ada** di §4 (`tempat_lahir`, `jenis_usaha`, `modal_usaha`, kode wilayah,
   `alamat_usaha` (jika beda dari alamat KTP), `jenis_produk`/`bahan_utama` per produk) — perlu
   ditambahkan ke form input Enumerator/Data Lapangan, atau diisi manual oleh Admin
   Umum/Superadmin sebelum kirim?
3. ~~**`package_id`**~~ — **sudah terjawab.** Revisi dokumentasi terbaru menghapus `package_id`
   dari kedua endpoint submit (NIB & Halal) sepenuhnya — paket otomatis mengikuti apa yang
   terdaftar di API key (`package_nib`/`package_halal` dari `/me`, §3.4). Tidak perlu logika
   pemilihan paket di sisi kita.
4. **Cara terima balik status/dokumen** — dokumentasi yang diberikan hanya menyediakan endpoint
   `GET` untuk cek status + endpoint `GET .../download` untuk ambil filenya (tidak ada spek
   webhook masuk). Kemungkinan perlu **polling terjadwal** (scheduled command, mis. tiap 30–60
   menit, mengecek submission yang masih `pending`/`processing`, lalu download otomatis saat
   `document_available: true`) — kecuali nanti dikonfirmasi ada mekanisme webhook terpisah.
5. **Cara dapat API key** — perlu didaftarkan sebagai client di platform mereka dulu (`client.api-key.create`
   pada sistem mereka), lalu key-nya disimpan di `.env` sistem kita (`URUSIN_BASE_URL`, `URUSIN_API_KEY`).
   Perlu diputuskan juga: mulai integrasi dari **mode Sandbox** dulu untuk uji coba (§3.3), baru
   pindah ke Production setelah alur teruji — atau langsung Production?
6. ~~**Nasib modul Data Entry lama**~~ — **sudah dijawab, lihat §1.1.** Role/login/menu
   `data_entry` lama **tidak dihapus dan tidak diubah**; tetap dipakai untuk menyelesaikan data
   yang sudah berjalan di alur manual. Alur baru hanya untuk Data Lapangan baru ke depannya. Yang
   masih perlu didesain saat implementasi: mekanisme penanda per-record untuk membedakan data
   "jalur lama" vs "jalur baru" (lihat §1.1).
7. **Urutan submit NIB vs Halal** — ~~API mensyaratkan `nib_submission_id` yang statusnya sudah
   `completed` sebelum submit Halal~~ **sudah tidak berlaku** — revisi dokumentasi terbaru
   membuat `nib_submission_id` nullable di submit Halal (§3.6), jadi keduanya bisa diproses
   independen/paralel. Yang masih perlu diputuskan: apakah alur kita **tetap** mengirim submit
   Halal hanya setelah NIB `completed` (lebih sederhana, satu alur linear) dan menyertakan
   `nib_submission_id` untuk menautkan keduanya — atau submit keduanya sekaligus begitu verifikasi
   final disetujui (lebih cepat, tapi dua proses paralel yang perlu dilacak terpisah)?
8. ~~**Nama/domain resmi produk**~~ — **nama sudah terjawab** (lihat catatan di §3): dokumentasi
   terbaru menyebut diri "REST API urusinsecara online", konsisten dengan istilah yang dipakai di
   dokumen ini. Yang **masih** perlu didapat: domain/base URL publik yang sebenarnya untuk
   `URUSIN_BASE_URL` — dokumentasi hanya menampilkan `{{ url('/api/v1') }}` relatif terhadap situs
   mereka, bukan domain aslinya, jadi ini tetap perlu ditanyakan langsung saat pendaftaran API key.
9. **Penanganan `billing_blocked`** — di mode Production, jika invoice belum dibayar (setiap
   Senin ditagih, diblokir mulai Selasa jika belum lunas), semua endpoint submission akan
   mengembalikan `403`. Perlu keputusan: bagaimana sistem kita bereaksi saat ini terjadi di
   tengah antrian pengajuan (retry otomatis nanti, atau notifikasi manual ke admin untuk bayar
   invoice dulu)?

---

## 6. Referensi silang

- Verifikasi Koordinator (tahap 1, sudah diimplementasikan) — lihat `koordinator-dashboard.md` §3.
- Bagian "Data Entry via Pihak Ketiga (HOLD)" di `koordinator-dashboard.md` mengarah ke dokumen
  ini untuk detail teknis lengkap.

---

## Checklist implementasi

- [x] Tambah kolom yang dibutuhkan di `data_lapangans` (§4) — migrasi `2026_08_26_190100_add_urusin_integration_to_data_lapangans_table`
- [x] Desain skema tracking submission — kolom tambahan di `data_lapangans` (bukan tabel baru): `urusin_nib_submission_id/status/document_path`, `urusin_halal_submission_id/status/document_path`, `urusin_last_synced_at`
- [x] Buat service HTTP client — `App\Services\Integrasi\UrusinService` (base URL & API key dibaca dari DB `settingwebsites`, bukan `.env` — mengikuti pola Gemini/Anthropic API key & WA Gateway yang sudah ada)
- [x] Buat aksi "Verifikasi Final" untuk Admin Umum/Superadmin — `DataLapanganController::verifikasiFinal()`, field baru `verifikasi_final/catatan_final/verified_at_final/verified_by_final` (§5.1 dijawab: field terpisah, bukan reuse `TERVERIFIKASI` lama, supaya tidak bentrok dengan alur `data_entry` lama)
- [x] Buat trigger submit NIB & Halal setelah verifikasi final disetujui — `UrusinSubmissionService::submitBoth()`, dipanggil sinkron dari controller (§5.7 dijawab: NIB & Halal dikirim bersamaan, tidak menunggu NIB `completed`, karena `nib_submission_id` di Halal sudah nullable — lihat catatan keputusan di docblock `UrusinSubmissionService`)
- [x] Buat mekanisme polling status NIB & Halal (§5.4) — command `php artisan urusin:sync`, dijadwalkan tiap 30 menit di `routes/console.php` (pola `Schedule::call()+Artisan::call()`, konsisten dengan command terjadwal lain di app ini)
- [x] Download dokumen dari `.../download` saat `document_available: true`, simpan ke `storage/app/public/urusin/{nib|halal}/{hashed_id}.pdf`, tombol unduh di panel detail data lapangan
- [x] Superadmin > Setting Website > tab API Keys: form Base URL + API Key Urusin Secara Online, tombol "Test Koneksi" (AJAX ke `GET /api/v1/me`) menampilkan mode/paket/status billing
- [x] Form "Data Usaha untuk Pengajuan" di halaman detail Superadmin/Admin Umum — field yang belum ada di §4 (`nama_usaha`, `tempat_lahir`, `jenis_usaha`, `modal_usaha`, `alamat_usaha`, kode wilayah, `jenis_produk_halal`/`bahan_utama_halal`) diisi manual di sini sebelum verifikasi final bisa disetujui (§5.2 dijawab: diisi manual oleh Admin Umum/Superadmin, bukan ditambahkan ke form Enumerator) — kode wilayah pakai dropdown cascading yang reuse endpoint publik `/api/wilayah/*` yang sudah ada di app ini
- [x] Tangani `gagal_kirim` (baik network error maupun `billing_blocked`/error API lain) — status tersimpan per jenis (NIB/Halal), tombol "Kirim Ulang" manual di panel detail (§5.9 dijawab: tidak ada retry otomatis, admin yang memicu ulang setelah invoice dilunasi/masalah diperbaiki)
- [x] Penanda per-record jalur lama vs baru — kolom `jalur_data_entry` (default `'lama'` untuk semua data existing lewat migrasi; diset `'baru'` otomatis hanya saat `verifikasiFinal()` disetujui). Role/menu/login `data_entry` lama **tidak diubah sama sekali** (§1.1 tetap berlaku)
- [ ] Mulai integrasi dari API key **Sandbox** dulu (§3.3) sebelum pindah ke Production (§5.5) — ini kebijakan operasional saat mengisi API key di Setting Website, bukan kode; belum ada API key nyata yang diisi/diuji end-to-end ke server Urusin Secara Online yang sesungguhnya
- [x] Uji end-to-end dengan API key & base URL nyata (sandbox) — sudah dicoba oleh user dengan API key sandbox sungguhan (`ua_sandbox_...`, base URL `https://urusinsecara.online`). Ditemukan & diperbaiki 3 bug nyata dari `storage/logs/laravel.log`:
  1. Base URL yang menyertakan `/api/v1` menyebabkan endpoint dobel (`/api/v1/api/v1/nib/submit`, 404) — `UrusinService` sekarang menormalisasi base URL otomatis.
  2. Kode wilayah dari `WilayahService` (wilayah.id) berformat ber-titik (`36.01.17.2001`), sementara contoh dokumentasi Urusin tidak pakai titik (`3578010001`) — kemungkinan penyebab HTTP 500 "Server Error" generik dari mereka. `UrusinSubmissionService::buildNibPayload()` sekarang menghapus semua titik sebelum kirim.
  3. Tidak ada validasi field wajib di sisi kita sebelum kirim — data lama tanpa `email`/`tanggal_lahir` terkirim sebagai `null` dan mendapat 500 generik tanpa penjelasan. Sekarang dicek dulu secara lokal (`missingFields()`), gagal cepat dengan pesan jelas, tidak memanggil API kalau memang belum lengkap.
  4. Pesan kegagalan sebelumnya hanya ada di log server, admin tidak bisa lihat dari UI — kolom baru `urusin_gagal_pesan` (migrasi `2026_08_26_220000_...`) sekarang menyimpan & menampilkan alasan gagal terakhir langsung di panel.
  Belum ada laporan **sukses penuh** (submission_id benar-benar diterima) — sekali field wajib (khususnya `email`/`tanggal_lahir` di data lama) lengkap, perlu dicoba ulang untuk konfirmasi jalur sukses.
  Percobaan lanjutan (22:13) menunjukkan HTTP 500 masih terjadi walau field sudah lengkap — investigasi menemukan `tanggal_lahir` yang diisi user adalah **tanggal hari ini** (kemungkinan nilai placeholder saat mengisi form, bukan tanggal lahir asli), yang kemungkinan besar membuat validasi umur di sisi Urusin crash jadi 500 generik alih-alih menolak dengan 400 yang rapi. Ditambahkan guard baru: `UrusinSubmissionService::implausibleTanggalLahir()` menolak secara lokal jika `tanggal_lahir` hari ini/masa depan, usia tersirat >120 tahun, atau <17 tahun (syarat usia KTP/NIK) — sudah diverifikasi menangkap kasus record 166 ini dengan benar.
  Percobaan lanjutan lagi (22:17) tetap 500 walau field & tanggal sudah lolos guard — diisolasi
  dengan memanggil `GET /api/v1/me` langsung (endpoint tanpa payload sama sekali, bahkan lewat raw
  `Http::get()` di luar `UrusinService`) dan **tetap 500** dengan body `{"message":"Server Error"}`
  (bentuk respons default Laravel untuk exception tak tertangani saat `APP_DEBUG=false`) —
  **dikonfirmasi ini masalah di server Urusin Secara Online, bukan di kode/data kita.** Perlu
  dilaporkan ke tim mereka langsung (API key sandbox yang dipakai untuk tes: `ua_sandbox_iSGa...`).
- [x] Beralih ke **Bundle API** (`POST /bundle/submit`, §3.7) sebagai jalur submit utama,
  menggantikan `submitNib()`+`submitHalal()` terpisah — sesuai instruksi user, karena sistem kita
  memang selalu mengirim keduanya sekaligus. `submitNib()`/`submitHalal()` lama dipertahankan
  (tidak dipakai jalur utama) untuk kompatibilitas.
- [x] Bentuk response `POST /bundle/submit` & `GET /bundle/{nib_id}/status` **dikonfirmasi** (§3.7)
  — nested per-jenis `data.nib.*`/`data.halal.*`, sesuai dugaan awal `extractBundleResult()`.
  `syncBundle()` sekarang jadi jalur utama polling (satu request untuk NIB+Halal), dipakai oleh
  command `urusin:sync`; `syncNib()`/`syncHalal()` lama jadi fallback untuk record yang sempat
  disubmit terpisah sebelum Bundle API dipakai.
- [ ] Field mapping yang masih berupa asumsi, perlu dikonfirmasi saat uji nyata: apakah `alamat_ktp` benar dipakai juga sebagai alamat usaha di jalur bundle (§3.7 catatan — `alamat_usaha` terpisah tidak ada di Bundle API), apakah `jenis_produk`/`bahan_utama` benar boleh sama untuk semua produk sekaligus (keputusan implementasi, lihat docblock `UrusinSubmissionService::buildHalalPayload()`)
