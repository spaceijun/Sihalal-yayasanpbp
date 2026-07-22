# API Dokumentasi — Data Lapangan

## Overview

API Data Lapangan digunakan untuk mengelola data lapangan sertifikasi halal oleh Enumerator. Semua endpoint memerlukan autentikasi JWT dan role `enumerator`.

**Base URL:** `/api/enumerator`

**Autentikasi:** Bearer Token (JWT)

---

## Alur Kerja (Workflow)

```
┌─────────────────────────────────────────────────────────────────────┐
│                         ALUR INPUT DATA                              │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  1. SCAN KTP (Opsional)                                             │
│     POST /api/scan-ktp                                              │
│     • Upload foto KTP                                                │
│     • Dapat data hasil OCR dari Gemini Flash API                     │
│     • Data: nik, nama, alamat, ttl, dll                             │
│                                                                      │
│  2. PRE-FILL FORM                                                   │
│     • Tampilkan hasil OCR di form                                    │
│     • User bisa edit/perbaiki data                                  │
│                                                                      │
│  3. SUBMIT DATA                                                      │
│     POST /api/enumerator/data-lapangan                               │
│     • Kirim data lengkap + semua foto                                │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘
```

---

## Endpoints

### 0. Scan KTP (Gemini OCR)

Memindai foto KTP menggunakan Gemini Flash API untuk mengekstrak data secara otomatis.

```
POST /api/scan-ktp
```

> **Catatan:** Endpoint ini **tidak memerlukan autentikasi** dan bisa digunakan oleh publik. Terbatas 10 request per menit (rate limit).

**Headers:**
```
Content-Type: multipart/form-data
```

**Body Parameters:**

| Parameter | Tipe | Required | Deskripsi |
|-----------|------|----------|-----------|
| `foto_ktp` | file | Yes | Foto KTP (jpg/png, max 5MB) |

**Contoh Request:**
```bash
curl -X POST "https://api.example.com/api/scan-ktp" \
  -F "foto_ktp=@/path/to/foto-ktp.jpg"
```

**Contoh Response (200 OK):**
```json
{
  "success": true,
  "message": "KTP berhasil dipindai",
  "data": {
    "nik": "3578123456789012",
    "nama": "BUDI SANTOSO",
    "tempat_lahir": "SURABAYA",
    "tanggal_lahir": "01-01-1990",
    "jenis_kelamin": "LAKI-LAKI",
    "alamat": "JL. RAYA NO 10",
    "rt": "001",
    "rw": "002",
    "kelurahan": "SAWAHAN",
    "kecamatan": "SAWAHAN",
    "kabupaten": "KOTA SURABAYA",
    "provinsi": "JAWA TIMUR",
    "agama": "ISLAM",
    "status_perkawinan": "BELUM KAWIN",
    "pekerjaan": "PEDAGANG",
    "kewarganegaraan": "WNI",
    "berlaku_hingga": "16-02-2029"
  }
}
```

**Response Error (422):**
```json
{
  "success": false,
  "message": "Layanan OCR sementara tidak tersedia. Silakan coba lagi dalam beberapa menit."
}
```

**Catatan Penting:**
- Tidak memerlukan autentikasi (publik)
- Rate limit: 10 request per menit
- Kode pos mungkin tidak selalu terdeteksi, bisa di-auto-fill berdasarkan kelurahan
- Hasil OCR tetap perlu diverifikasi oleh user sebelum disubmit

**Contoh Penggunaan di Flutter:**
```dart
Future<Map<String, dynamic>?> scanKtp(File fotoKtp) async {
  final uri = Uri.parse('https://api.example.com/api/scan-ktp');
  final request = http.MultipartRequest('POST', uri)
    ..files.add(await http.MultipartFile.fromPath('foto_ktp', fotoKtp.path));

  final streamedResponse = await request.send();
  final response = await http.Response.fromStream(streamedResponse);
  final data = json.decode(response.body);

  if (data['success'] == true) {
    return data['data'];
  }
  throw Exception(data['message']);
}
```

---

### 1. List Data Lapangan

Mengambil daftar data lapangan milik enumerator yang login.

```
GET /api/enumerator/data-lapangan
```

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Query Parameters:**

| Parameter | Tipe | Required | Deskripsi |
|----------|------|----------|-----------|
| `search` | string | No | Filter berdasarkan nama PU |
| `status` | string | No | Filter berdasarkan status |
| `per_page` | integer | No | Jumlah item per halaman (default: 10, max: 100) |

**Status Options:**
- `PENDING`
- `REVISI`
- `TERVERIFIKASI`
- `PROGRESS OSS`
- `PROGRESS SIHALAL`
- `TERBIT SH`

**Contoh Request:**
```bash
curl -X GET "https://api.example.com/api/enumerator/data-lapangan?status=PENDING&per_page=20" \
  -H "Authorization: Bearer {token}"
```

**Contoh Response (200 OK):**
```json
{
  "status": true,
  "message": "Data lapangan berhasil diambil",
  "filters": {
    "search": null,
    "status": "PENDING",
    "per_page": 20
  },
  "status_options": [
    "PENDING",
    "REVISI",
    "TERVERIFIKASI",
    "PROGRESS OSS",
    "PROGRESS SIHALAL",
    "TERBIT SH"
  ],
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "no_registrasi": "KH2026-00001",
        "enumerator_id": 1,
        "nama_pu": "TOKO BUAH SEGAR",
        "nik": "3578123456789012",
        "email": "tokobuah@example.com",
        "telephone": "081234567890",
        "nama_produk": "Keripik Singkong",
        "nama_produk_2": null,
        "nama_produk_3": null,
        "nama_produk_4": null,
        "nama_produk_5": null,
        "alamat": "Jl. Raya No. 10",
        "provinsi": "JAWA TIMUR",
        "kabupaten": "KOTA SURABAYA",
        "kecamatan": "SAWAHAN",
        "kelurahan": "SAWAHAN",
        "rt": "001",
        "rw": "002",
        "kode_pos": "60251",
        "tanggal_lahir": "1990-01-15",
        "umur": 36,
        "full_address": "Jl. Raya No. 10, RT 001, RW 002, SAWAHAN, SAWAHAN, KOTA SURABAYA, JAWA TIMUR, PSTP 60251",
        "foto_ktp": "/storage/foto-ktp/abc123.jpg",
        "foto_rumah": "/storage/foto-rumah/def456.jpg",
        "foto_pendamping": "/storage/foto-pendamping/ghi789.jpg",
        "foto_proses": "/storage/foto-proses/jkl012.jpg",
        "foto_produk": "/storage/foto-produk/mno345.jpg",
        "foto_produk_2": null,
        "foto_produk_3": null,
        "foto_produk_4": null,
        "foto_produk_5": null,
        "file_oss": null,
        "has_nib": false,
        "status": "PENDING",
        "status_pembayaran": "TIDAK ADA PENGAJUAN",
        "verifikator": null,
        "tanggal_verifikasi": null,
        "keterangan": null,
        "file_sihalal": null,
        "created_at": "2026-07-22T10:30:00.000000Z",
        "updated_at": "2026-07-22T10:30:00.000000Z"
      }
    ],
    "first_page_url": "...",
    "from": 1,
    "last_page": 5,
    "last_page_url": "...",
    "next_page_url": "...",
    "path": "...",
    "per_page": 20,
    "prev_page_url": null,
    "to": 20,
    "total": 100
  }
}
```

---

### 2. Detail Data Lapangan

Mengambil detail satu data lapangan berdasarkan ID.

```
GET /api/enumerator/data-lapangan/{id}
```

**Headers:**
```
Authorization: Bearer {token}
```

**Contoh Request:**
```bash
curl -X GET "https://api.example.com/api/enumerator/data-lapangan/1" \
  -H "Authorization: Bearer {token}"
```

**Contoh Response (200 OK):**
```json
{
  "status": true,
  "message": "Detail data lapangan berhasil diambil",
  "data": {
    "id": 1,
    "no_registrasi": "KH2026-00001",
    "enumerator_id": 1,
    "nama_pu": "TOKO BUAH SEGAR",
    "nik": "3578123456789012",
    "email": "tokobuah@example.com",
    "telephone": "081234567890",
    "nama_produk": "Keripik Singkong",
    "alamat": "Jl. Raya No. 10",
    "provinsi": "JAWA TIMUR",
    "kabupaten": "KOTA SURABAYA",
    "kecamatan": "SAWAHAN",
    "kelurahan": "SAWAHAN",
    "rt": "001",
    "rw": "002",
    "kode_pos": "60251",
    "status": "PENDING",
    "status_pembayaran": "TIDAK ADA PENGAJUAN",
    "created_at": "2026-07-22T10:30:00.000000Z",
    "updated_at": "2026-07-22T10:30:00.000000Z"
  }
}
```

**Response Error (404):**
```json
{
  "status": false,
  "message": "Data tidak ditemukan"
}
```

---

### 3. Tambah Data Lapangan

Membuat data lapangan baru. **Hanya dapat dilakukan jika enumerator berstatus "Aktif".**

```
POST /api/enumerator/data-lapangan
```

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Body Parameters:**

| Parameter | Tipe | Required | Deskripsi |
|-----------|------|----------|-----------|
| `nama_pu` | string | Yes | Nama Pelaku Usaha |
| `nik` | string | Yes | NIK (16 digit) |
| `telephone` | string | Yes | Nomor telepon (10-15 digit) |
| `nama_produk` | string | Yes | Nama produk utama |
| `alamat` | string | Yes | Alamat lengkap |
| `foto-ktp` | file | Yes | Foto KTP (jpg/png, max 2MB) |
| `foto-rumah` | file | Yes | Foto rumah (jpg/png, max 2MB) |
| `foto-pendamping` | file | Yes | Foto pendamping (jpg/png, max 2MB) |
| `foto-proses` | file | Yes | Foto proses (jpg/png, max 2MB) |
| `foto-produk` | file | Yes | Foto produk (jpg/png, max 2MB) |
| `provinsi` | string | No | Nama provinsi |
| `kabupaten` | string | No | Nama kabupaten/kota |
| `kecamatan` | string | No | Nama kecamatan |
| `kelurahan` | string | No | Nama desa/kelurahan |
| `rt` | string | No | RT (3 digit, contoh: 001) |
| `rw` | string | No | RW (3 digit, contoh: 002) |
| `kode_pos` | string | No | Kode pos (5 digit) |
| `tanggal_lahir` | date | No | Tanggal lahir (YYYY-MM-DD) |
| `has_nib` | boolean | Yes | Apakah memiliki NIB (true/false) |
| `file_oss` | file | Conditional | File OSS (pdf, max 5MB) — wajib jika has_nib=true |
| `nama_produk_2` | string | No | Nama produk tambahan 2 |
| `nama_produk_3` | string | No | Nama produk tambahan 3 |
| `nama_produk_4` | string | No | Nama produk tambahan 4 |
| `nama_produk_5` | string | No | Nama produk tambahan 5 |
| `foto-produk-2` | file | Conditional | Foto produk 2 (wajib jika nama_produk_2 ada) |
| `foto-produk-3` | file | Conditional | Foto produk 3 (wajib jika nama_produk_3 ada) |
| `foto-produk-4` | file | Conditional | Foto produk 4 (wajib jika nama_produk_4 ada) |
| `foto-produk-5` | file | Conditional | Foto produk 5 (wajib jika nama_produk_5 ada) |

**Contoh Request:**
```bash
curl -X POST "https://api.example.com/api/enumerator/data-lapangan" \
  -H "Authorization: Bearer {token}" \
  -F "nama_pu=Toko Buah Segar" \
  -F "nik=3578123456789012" \
  -F "telephone=081234567890" \
  -F "nama_produk=Keripik Singkong" \
  -F "alamat=Jl. Raya No. 10" \
  -F "provinsi=JAWA TIMUR" \
  -F "kabupaten=KOTA SURABAYA" \
  -F "kecamatan=SAWAHAN" \
  -F "kelurahan=SAWAHAN" \
  -F "rt=001" \
  -F "rw=002" \
  -F "kode_pos=60251" \
  -F "has_nib=false" \
  -F "foto-ktp=@/path/to/foto-ktp.jpg" \
  -F "foto-rumah=@/path/to/foto-rumah.jpg" \
  -F "foto-pendamping=@/path/to/foto-pendamping.jpg" \
  -F "foto-proses=@/path/to/foto-proses.jpg" \
  -F "foto-produk=@/path/to/foto-produk.jpg"
```

**Contoh Response (201 Created):**
```json
{
  "status": true,
  "message": "Data lapangan berhasil disimpan",
  "data": {
    "id": 1,
    "no_registrasi": "KH2026-00001",
    "enumerator_id": 1,
    "nama_pu": "TOKO BUAH SEGAR",
    "nik": "3578123456789012",
    "telephone": "081234567890",
    "nama_produk": "Keripik Singkong",
    "alamat": "Jl. Raya No. 10",
    "provinsi": "JAWA TIMUR",
    "kabupaten": "KOTA SURABAYA",
    "kecamatan": "SAWAHAN",
    "kelurahan": "SAWAHAN",
    "rt": "001",
    "rw": "002",
    "kode_pos": "60251",
    "status": "PENDING",
    "status_pembayaran": "TIDAK ADA PENGAJUAN",
    "has_nib": false,
    "created_at": "2026-07-22T10:30:00.000000Z",
    "updated_at": "2026-07-22T10:30:00.000000Z"
  }
}
```

**Response Error (403 - Enumerator Tidak Aktif):**
```json
{
  "status": false,
  "message": "Anda tidak dapat mengajukan data lapangan karena akun enumerator Anda tidak aktif. Silakan hubungi koordinator.",
  "data": {
    "status_enumerator": "Tidak Aktif",
    "jumlah_data_30_hari": 15,
    "minimal_required": 20
  }
}
```

**Response Error (422 - Validasi Gagal):**
```json
{
  "status": false,
  "message": "Validasi gagal",
  "errors": {
    "nik": ["The nik field is required."],
    "foto-ktp": ["The foto-ktp field is required."]
  }
}
```

---

### 4. Update Data Lapangan

Memperbarui data lapangan. **Status otomatis direset ke PENDING setiap kali data diperbarui.**

```
PUT /api/enumerator/data-lapangan/{id}
PATCH /api/enumerator/data-lapangan/{id}
```

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Body Parameters:**

Semua parameter opsional — hanya kirim field yang ingin diupdate.

| Parameter | Tipe | Required | Deskripsi |
|-----------|------|----------|-----------|
| `nama_pu` | string | No | Nama Pelaku Usaha |
| `nik` | string | No | NIK (16 digit) |
| `telephone` | string | No | Nomor telepon |
| `nama_produk` | string | No | Nama produk utama |
| `alamat` | string | No | Alamat lengkap |
| `provinsi` | string | No | Nama provinsi |
| `kabupaten` | string | No | Nama kabupaten/kota |
| `kecamatan` | string | No | Nama kecamatan |
| `kelurahan` | string | No | Nama desa/kelurahan |
| `rt` | string | No | RT (3 digit) |
| `rw` | string | No | RW (3 digit) |
| `kode_pos` | string | No | Kode pos (5 digit) |
| `tanggal_lahir` | date | No | Tanggal lahir |
| `has_nib` | boolean | No | Apakah memiliki NIB |
| `file_oss` | file | No | File OSS (pdf, max 5MB) |
| `nama_produk_2` | string | No | Nama produk tambahan 2 |
| `nama_produk_3` | string | No | Nama produk tambahan 3 |
| `nama_produk_4` | string | No | Nama produk tambahan 4 |
| `nama_produk_5` | string | No | Nama produk tambahan 5 |
| `foto-ktp` | file | No | Foto KTP baru |
| `foto-rumah` | file | No | Foto rumah baru |
| `foto-pendamping` | file | No | Foto pendamping baru |
| `foto-proses` | file | No | Foto proses baru |
| `foto-produk` | file | No | Foto produk baru |
| `foto-produk-2` | file | No | Foto produk 2 baru |
| `foto-produk-3` | file | No | Foto produk 3 baru |
| `foto-produk-4` | file | No | Foto produk 4 baru |
| `foto-produk-5` | file | No | Foto produk 5 baru |

**Contoh Request:**
```bash
curl -X PUT "https://api.example.com/api/enumerator/data-lapangan/1" \
  -H "Authorization: Bearer {token}" \
  -F "nama_pu=Toko Buah Segar Updated" \
  -F "alamat=Jl. Baru No. 20"
```

**Contoh Response (200 OK):**
```json
{
  "status": true,
  "message": "Data lapangan berhasil diperbarui",
  "data": {
    "id": 1,
    "no_registrasi": "KH2026-00001",
    "nama_pu": "TOKO BUAH SEGAR UPDATED",
    "alamat": "Jl. Baru No. 20",
    "status": "PENDING",
    "updated_at": "2026-07-22T11:00:00.000000Z"
  }
}
```

**Catatan Penting:**
- Foto lama akan otomatis dihapus dan digantikan foto baru
- Jika `has_nib` diubah ke `false`, file OSS lama akan dihapus
- Setiap update akan mereset status ke `PENDING`

---

### 5. Hapus Data Lapangan

Menghapus data lapangan beserta semua foto terkait.

```
DELETE /api/enumerator/data-lapangan/{id}
```

**Headers:**
```
Authorization: Bearer {token}
```

**Contoh Request:**
```bash
curl -X DELETE "https://api.example.com/api/enumerator/data-lapangan/1" \
  -H "Authorization: Bearer {token}"
```

**Contoh Response (200 OK):**
```json
{
  "status": true,
  "message": "Data lapangan berhasil dihapus"
}
```

---

## Model Data

### DataLapangan Object

| Field | Tipe | Deskripsi |
|-------|------|-----------|
| `id` | integer | ID unik |
| `no_registrasi` | string | Nomor registrasi (format: KHYYYY-NNNNN) |
| `enumerator_id` | integer | ID enumerator |
| `nama_pu` | string | Nama Pelaku Usaha (UPPERCASE) |
| `nik` | string | NIK (16 digit) |
| `email` | string | Email |
| `email_sihalal` | string | Email SIHALAL |
| `telephone` | string | Nomor telepon |
| `nama_produk` | string | Nama produk utama |
| `nama_produk_2` | string | Nama produk 2 |
| `nama_produk_3` | string | Nama produk 3 |
| `nama_produk_4` | string | Nama produk 4 |
| `nama_produk_5` | string | Nama produk 5 |
| `alamat` | string | Alamat lengkap |
| `provinsi` | string | Provinsi |
| `kabupaten` | string | Kabupaten/Kota |
| `kecamatan` | string | Kecamatan |
| `kelurahan` | string | Desa/Kelurahan |
| `rt` | string | RT (3 digit) |
| `rw` | string | RW (3 digit) |
| `kode_pos` | string | Kode Pos (5 digit) |
| `tanggal_lahir` | date | Tanggal Lahir (YYYY-MM-DD) |
| `umur` | integer | Umur (dihitung otomatis) |
| `full_address` | string | Alamat lengkap formatted |
| `foto_ktp` | string | URL foto KTP |
| `foto_rumah` | string | URL foto rumah |
| `foto_pendamping` | string | URL foto pendamping |
| `foto_proses` | string | URL foto proses |
| `foto_produk` | string | URL foto produk 1 |
| `foto_produk_2` | string | URL foto produk 2 |
| `foto_produk_3` | string | URL foto produk 3 |
| `foto_produk_4` | string | URL foto produk 4 |
| `foto_produk_5` | string | URL foto produk 5 |
| `file_oss` | string | URL file OSS |
| `has_nib` | boolean | Apakah memiliki NIB |
| `status` | string | Status data |
| `status_pembayaran` | string | Status pembayaran |
| `verifikator` | object | Data verifikator |
| `tanggal_verifikasi` | datetime | Tanggal verifikasi |
| `keterangan` | string | Keterangan |
| `keterangan_oss` | string | Keterangan OSS |
| `keterangan_sihalal` | string | Keterangan SIHALAL |
| `file_sihalal` | string | URL file SIHALAL |
| `created_at` | datetime | Tanggal dibuat |
| `updated_at` | datetime | Tanggal diupdate |

### Status Values

| Status | Deskripsi |
|--------|-----------|
| `PENDING` | Menunggu verifikasi |
| `REVISI` | Perlu perbaikan |
| `TERVERIFIKASI` | Data terverifikasi |
| `PROGRESS OSS` | Dalam proses OSS |
| `PROGRESS SIHALAL` | Dalam proses SIHALAL |
| `TERBIT SH` | Sertifikat Halal terbit |
| `DITOLAK` | Ditolak |

### Status Pembayaran

| Status | Deskripsi |
|--------|-----------|
| `TIDAK ADA PENGAJUAN` | Belum mengajukan |
| `PENGAJUAN` | Sudah diajukan |
| `DIBAYAR` | Sudah dibayar |
| `DITOLAK` | Pengajuan ditolak |

---

## Error Codes

| HTTP Code | Message | Deskripsi |
|-----------|---------|-----------|
| 200 | Success | Request berhasil |
| 201 | Created | Resource berhasil dibuat |
| 400 | Bad Request | Request tidak valid |
| 401 | Unauthorized | Token tidak valid |
| 403 | Forbidden | Akses ditolak (enumerator tidak aktif) |
| 404 | Not Found | Data tidak ditemukan |
| 422 | Unprocessable Entity | Validasi gagal |
| 500 | Internal Server Error | Server error |

---

## Catatan Penting

1. **Autentikasi:** Semua endpoint memerlukan Bearer token JWT
2. **Enumerator Aktif:** Store hanya bisa dilakukan jika enumerator berstatus "Aktif"
3. **Otomatis UPPERCASE:** Field `nama_pu` akan otomatis dikonversi ke huruf besar
4. **Auto Reset Status:** Update data akan mereset status ke PENDING
5. **File Management:** Upload foto baru akan otomatis menghapus foto lama
6. **Max File Size:** Foto max 2MB, file OSS max 5MB
7. **Maksimal 5 Produk:** Maksimal 5 produk per data lapangan

---

## Contoh Lengkap (Flutter/Dart)

```dart
class DataLapanganApi {
  static const String baseUrl = 'https://api.example.com/api';
  static const String enumeratorUrl = '$baseUrl/enumerator';

  // ============================================================
  // OCR SCAN - Gemini Flash API
  // ============================================================

  /// Scan KTP menggunakan Gemini Flash API
  /// Returns null jika gagal
  Future<Map<String, dynamic>?> scanKtp(File fotoKtp) async {
    final uri = Uri.parse('$baseUrl/scan-ktp');

    final request = http.MultipartRequest('POST', uri)
      ..files.add(await http.MultipartFile.fromPath('foto_ktp', fotoKtp.path));

    final streamedResponse = await request.send();
    final response = await http.Response.fromStream(streamedResponse);
    final data = json.decode(response.body);

    if (data['success'] == true) {
      return data['data']; // Map dengan nik, nama, alamat, dll
    }
    return null; // Gagal scan, tangani di UI
  }

  // ============================================================
  // DATA LAPANGAN CRUD
  // ============================================================

  // GET List
  Future<List<Map<String, dynamic>>> getDataLapangan({
    String? status,
    String? search,
    int perPage = 10,
  }) async {
    final token = await getToken();
    final queryParams = {
      if (status != null) 'status': status,
      if (search != null) 'search': search,
      'per_page': perPage.toString(),
    };

    final response = await http.get(
      Uri.parse('$enumeratorUrl/data-lapangan').replace(queryParameters: queryParams),
      headers: {'Authorization': 'Bearer $token'},
    );

    final data = json.decode(response.body);
    if (data['status'] == true) {
      return List<Map<String, dynamic>>.from(data['data']['data']);
    }
    throw Exception(data['message']);
  }

  // POST Store
  Future<Map<String, dynamic>> createDataLapangan({
    required String namaPu,
    required String nik,
    required String telephone,
    required String namaProduk,
    required String alamat,
    required bool hasNib,
    required File fotoKtp,
    required File fotoRumah,
    required File fotoPendamping,
    required File fotoProses,
    required File fotoProduk,
    String? provinsi,
    String? kabupaten,
    String? kecamatan,
    String? kelurahan,
    String? rt,
    String? rw,
    String? kodePos,
  }) async {
    final token = await getToken();
    final uri = Uri.parse('$enumeratorUrl/data-lapangan');
    final request = http.MultipartRequest('POST', uri)
      ..headers['Authorization'] = 'Bearer $token'
      ..fields['nama_pu'] = namaPu
      ..fields['nik'] = nik
      ..fields['telephone'] = telephone
      ..fields['nama_produk'] = namaProduk
      ..fields['alamat'] = alamat
      ..fields['has_nib'] = hasNib.toString()
      ..fields['provinsi'] = provinsi ?? ''
      ..fields['kabupaten'] = kabupaten ?? ''
      ..fields['kecamatan'] = kecamatan ?? ''
      ..fields['kelurahan'] = kelurahan ?? ''
      ..fields['rt'] = rt ?? ''
      ..fields['rw'] = rw ?? ''
      ..fields['kode_pos'] = kodePos ?? ''
      ..files.add(await http.MultipartFile.fromPath('foto-ktp', fotoKtp.path))
      ..files.add(await http.MultipartFile.fromPath('foto-rumah', fotoRumah.path))
      ..files.add(await http.MultipartFile.fromPath('foto-pendamping', fotoPendamping.path))
      ..files.add(await http.MultipartFile.fromPath('foto-proses', fotoProses.path))
      ..files.add(await http.MultipartFile.fromPath('foto-produk', fotoProduk.path));

    final streamedResponse = await request.send();
    final response = await http.Response.fromStream(streamedResponse);
    final data = json.decode(response.body);

    if (data['status'] == true) {
      return data['data'];
    }
    throw Exception(data['message']);
  }

  // PUT Update
  Future<Map<String, dynamic>> updateDataLapangan(int id, Map<String, dynamic> updates) async {
    final token = await getToken();
    final uri = Uri.parse('$enumeratorUrl/data-lapangan/$id');
    final request = http.MultipartRequest('PUT', uri)
      ..headers['Authorization'] = 'Bearer $token';

    for (final entry in updates.entries) {
      if (entry.value is File) {
        request.files.add(await http.MultipartFile.fromPath(entry.key, entry.value.path));
      } else {
        request.fields[entry.key] = entry.value.toString();
      }
    }

    final streamedResponse = await request.send();
    final response = await http.Response.fromStream(streamedResponse);
    final data = json.decode(response.body);

    if (data['status'] == true) {
      return data['data'];
    }
    throw Exception(data['message']);
  }

  // DELETE
  Future<void> deleteDataLapangan(int id) async {
    final token = await getToken();
    final response = await http.delete(
      Uri.parse('$enumeratorUrl/data-lapangan/$id'),
      headers: {'Authorization': 'Bearer $token'},
    );

    final data = json.decode(response.body);
    if (data['status'] != true) {
      throw Exception(data['message']);
    }
  }
}
```
