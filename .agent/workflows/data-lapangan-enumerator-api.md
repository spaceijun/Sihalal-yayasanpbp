# Refaktor API Enumerator (Data Lapangan) + Geotagging Submit

> **Status dokumen:** sudah diimplementasikan (lihat Checklist di bagian bawah).

## 1. Latar belakang & ruang lingkup

Permintaan: *"update pada modul data lapangan: (1) refaktor infrastruktur API yang skalabel untuk
enumerator (fungsi untuk mobile Flutter), (2) tambahkan geotagging ketika enumerator submit data
lapangan"*.

Ruang lingkup dibatasi ke **modul Data Lapangan** di sisi API Enumerator
(`App\Http\Controllers\Api\Enumerator\DataLapanganEnumController`) — bukan seluruh 11 controller
di `Api/Enumerator/*`. Ini controller terbesar/paling padat logic (591 baris, semua CRUD + upload
foto + format response ditulis langsung di controller tanpa lapisan Service/FormRequest), dan di
sinilah geotagging perlu ditambahkan — jadi masuk akal dikerjakan sekaligus.

## 2. Refaktor arsitektur (poin 1)

**Sebelum:** satu controller monolitik menangani validasi inline (`Validator::make` di setiap
method), business logic (upload/hapus file, transform data), dan format response, semuanya di
tempat yang sama. Constant `FOTO_FIELDS`/`STATUS_LIST` dan daftar field dipakai berulang di 4
method berbeda (index/store/update/destroy) dengan risiko drift kalau salah satu lupa diupdate.

**Sesudah** — mengikuti pola layering yang sudah ada di codebase ini untuk modul Data Lapangan
lain (`App\Services\Superadmin\DataLapanganService`, `App\Services\Koordinator\DataLapanganService`):

- **`App\Services\Enumerator\DataLapanganEnumService`** — satu-satunya tempat business logic:
  query+filter (`paginate()`), ambil-milik-sendiri-atau-gagal (`findOwned()`), create/update
  (termasuk upload & rollback file saat gagal), delete (termasuk hapus semua file terkait), dan
  format response (`format()`). `FOTO_FIELDS`/`STATUS_LIST` sekarang cuma didefinisikan sekali di
  sini.
- **`App\Http\Requests\Enumerator\DataLapanganEnumStoreRequest`** &
  **`...\DataLapanganEnumUpdateRequest`** — validasi dipindah ke FormRequest, mengikuti pola
  `DataLapanganRequest`/`EnumeratorRequest` yang sudah ada di `App\Http\Requests`. Subnamespace
  `Enumerator\` baru diperkenalkan di sini (belum ada sebelumnya) supaya FormRequest
  endpoint-enumerator lain ke depannya (Cashflow, Tiket, dst — saat ini masih pakai
  `Validator::make` inline) bisa ikut pola yang sama tanpa bentrok nama file dengan Request area
  lain.
- **Controller** jadi tipis: inject service, panggil method yang sesuai, translate ke response
  JSON. **Kontrak response (`status`/`message`/`data`/`errors`, kode HTTP) dipertahankan PERSIS
  sama** dengan sebelumnya — ini dikonsumsi aplikasi Flutter yang sudah live, mengubah bentuk
  envelope-nya (mis. ke `success` seperti trait `ApiResponses` yang dipakai grup API lain) akan
  memutus app yang sudah terpasang tanpa koordinasi rilis. Endpoint Enumerator sengaja TIDAK
  dipindah ke `ApiResponses` trait karena alasan itu — inkonsistensi `status` vs `success` di
  seluruh permukaan API ini sudah ada sebelumnya, bukan sesuatu yang diperbaiki dokumen ini.

**Catatan efisiensi tambahan:** validator kecil di `index()` (3 field filter: `search`, `status`,
`per_page`) sengaja dibiarkan `Validator::make` inline di controller, tidak dijadikan FormRequest
terpisah — terlalu kecil untuk butuh class sendiri.

**Temuan sampingan (tidak diperbaiki, di luar scope):** guard "enumerator tidak aktif" di dalam
`store()` (menghitung `jumlah_data_30_hari`, payload lebih detail) sebenarnya **sudah tidak pernah
tercapai** di production — route `store` sudah dilindungi middleware `enumerator.active`
(`EnsureEnumeratorIsActive`) yang mengembalikan 403 dengan payload lebih sederhana untuk kasus
yang sama LEBIH DULU, sebelum request sampai ke controller. Guard di controller jadi dead code.
Logic-nya tetap dipertahankan apa adanya (cuma dipindah ke service sebagai
`assertEnumeratorAktif()`) supaya tidak mengubah perilaku yang tidak diminta — tapi ini layak
dibersihkan/diselaraskan di kesempatan lain.

## 3. Geotagging (poin 2)

### 3.1 Kolom baru — `data_lapangans`

| Kolom                | Tipe             | Keterangan                                                                 |
| --------------------- | ---------------- | --------------------------------------------------------------------------------------------- |
| `latitude`             | `decimal(10,7)` nullable | Lintang, hasil GPS device saat submit                                     |
| `longitude`            | `decimal(10,7)` nullable | Bujur, hasil GPS device saat submit                                       |
| `akurasi_meter`        | `decimal(8,2)` nullable  | Radius akurasi GPS (mis. `Position.accuracy` di Flutter `geolocator`)     |
| `geotag_captured_at`   | `timestamp` nullable     | Waktu titik GPS diambil DI DEVICE (bisa beda dari `created_at` server jika ada jeda upload)     |

Kolom **nullable di level DB** (migrasi aman untuk data lama yang sudah ada), tapi **wajib di
level validasi API** untuk `store()` (lihat §3.2) — mengikuti pola yang sama seperti kolom
`urusin_*` sebelumnya: penambahan kolom tidak pernah mengunci skema dengan `NOT NULL` tanpa
default, keharusan diatur di validasi aplikasi.

### 3.2 Validasi & kontrak API

- **`POST /api/enumerator/data-lapangan` (store):** `latitude` & `longitude` **wajib**
  (`required|numeric|between:-90,90` / `between:-180,180`). `akurasi_meter` &
  `geotag_captured_at` opsional.
- **`PUT/PATCH /api/enumerator/data-lapangan/{id}` (update):** ketiganya **opsional**
  (`nullable`/`sometimes`) — update tidak selalu berarti enumerator sedang di lokasi (mis. cuma
  perbaiki nama produk), jadi tidak dipaksa re-geotag. Kalau dikirim, tetap divalidasi sama
  ketatnya.
- Response `formatData()`/`format()` menambahkan `latitude`, `longitude`, `akurasi_meter`,
  `geotag_captured_at`, dan `google_maps_url` (null kalau lat/lng kosong) — supaya app bisa
  langsung render tanpa hitung sendiri.

> **⚠️ Breaking change untuk versi app lama:** karena `latitude`/`longitude` **wajib** di
> `store()`, build Flutter yang belum update untuk mengirim GPS akan mulai menerima `422
> Validasi gagal` saat submit data baru begitu perubahan ini deploy ke production. Ini konsekuensi
> yang disengaja dari "geotagging saat submit" (kalau opsional, datanya tidak akan reliable
> terisi) — **perlu dikoordinasikan dengan rilis app Flutter** yang mengirim field ini, bukan
> sesuatu yang bisa di-deploy sisi backend sendirian tanpa app-nya siap.

### 3.3 Field request yang dikirim Flutter

```
latitude            = -6.973436          // required | numeric, -90..90
longitude           = 107.630936         // required | numeric, -180..180
akurasi_meter        = 8.5                // nullable | numeric, >= 0 (Position.accuracy)
geotag_captured_at   = "2026-08-27T09:15:00Z"  // nullable | date (waktu fix GPS di device)
```

### 3.4 Tampilan hasil geotag (sisi review)

Ditambahkan baris "Lokasi Pengambilan Data (GPS)" + tautan "Buka di Google Maps" di halaman detail
Data Lapangan:
- `resources/views/superadmin/data-lapangan/show.blade.php` (dan `admin-umum` — view yang sama).
- `resources/views/koordinator/data-lapangan/show.blade.php`.

Tidak ditambahkan ke `index.blade.php`/DataTables (kolom tabel sudah padat) atau ke view
`data-entry`/`enumerator` web (bukan konsumen natural data ini) — di luar scope permintaan.

## 4. Checklist implementasi

- [x] Migrasi `latitude`/`longitude`/`akurasi_meter`/`geotag_captured_at` di `data_lapangans`.
- [x] `DataLapangan` model: `$fillable`, `$casts` (`decimal:7`/`decimal:2`/`datetime`), accessor
      `getGoogleMapsUrlAttribute()`.
- [x] `App\Services\Enumerator\DataLapanganEnumService` — business logic dipindah dari controller.
- [x] `App\Http\Requests\Enumerator\DataLapanganEnumStoreRequest` /
      `DataLapanganEnumUpdateRequest` — validasi dipindah dari controller, geotag ditambahkan.
- [x] `DataLapanganEnumController` dirapikan jadi tipis, kontrak response tidak berubah.
- [x] View detail Superadmin & Koordinator menampilkan lokasi GPS.
- [x] Verifikasi: `php -l` semua file baru/diubah, `php artisan migrate`, `route:list` bersih,
      test fungsional (buat + baca + format data lewat service secara langsung, cek kolom geotag
      tersimpan & `google_maps_url` benar).

## 5. Hal yang masih terbuka (di luar kendali backend)

- Build Flutter perlu diupdate untuk mengirim `latitude`/`longitude` (pakai plugin `geolocator`
  atau sejenis) — lihat catatan breaking-change di §3.2. Backend sudah siap menerima, tapi tidak
  bisa memaksa app mengirimnya.
- Dead code guard "enumerator tidak aktif" di §2 dibiarkan (di luar scope), didokumentasikan
  supaya tidak terulang jadi kebingungan saat baca kode nanti.
