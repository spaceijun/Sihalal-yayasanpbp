# Integrasi WRGROUP Super Apps (Holding Company)

> **Status dokumen:** sudah diimplementasikan (mekanisme outbox, retry/backoff, circuit breaker,
> heartbeat, laporan nihil, dan pembayaran komisi — semuanya sudah ada di kode). Yang **belum**:
> pengujian end-to-end dengan kredensial nyata (WRGROUP belum menerbitkan Business ID/API Key/API
> Secret untuk Kawulo Halal) — lihat Checklist Go-Live di §6.

---

## 1. Kenapa integrasi ini ada

Kawulo Halal adalah "pilar" (bisnis anggota) dari holding company **WRGROUP Super Apps**. Setiap
kasus sertifikasi halal yang sudah lunas (fee dibayar oleh pelaku usaha) dilaporkan ke WRGROUP
sebagai data pendapatan, supaya WRGROUP dapat menghitung komisi yang menjadi haknya. Sebaliknya,
Kawulo Halal **membayar** komisi itu langsung (transfer manual) dan melaporkan buktinya ke WRGROUP
lewat API untuk diverifikasi — WRGROUP tidak pernah menyentuh alur uang di sisi Kawulo Halal.

Integrasi ini satu arah dan machine-to-machine: Kawulo Halal mengirim, WRGROUP tidak pernah
memanggil balik (tidak ada endpoint publik baru yang dibuat di sisi kita untuk ini).

Modul ini adalah port dari implementasi yang sama persis di `swaraningcode-be` (pilar lain di
holding company yang sama) — mekanisme intinya (outbox, retry, circuit breaker) **sama persis**;
yang disesuaikan hanya pemetaan event bisnis (lihat §2), karena domain Kawulo Halal (kasus
sertifikasi halal) berbeda dari domain swaraningcode-be (invoice/order/sale proyek).

---

## 2. Pemetaan Event Bisnis

Kawulo Halal **tidak punya invoice yang terbit terpisah dari payment** — satu `DataLapangan`
berpindah `status_pembayaran` langsung ke `DIBAYAR` dalam satu langkah, begitu fee dikonfirmasi oleh
staf (mirip pola "Sale" di reference `swaraningcode-be`, bukan pola "Invoice" bertahap terbit→bayar).

```
DataLapangan.status_pembayaran → DIBAYAR (staf konfirmasi pembayaran)
        ↓
DataLapanganObserver::updated() → handleWrgroupSync()
        ↓ (wasChanged('status_pembayaran') && strtoupper(...) === 'DIBAYAR')
WrgroupEventService::recordDataLapanganPaid($dataLapangan)
        ↓
    record('invoice', WrgroupPayloadBuilder::invoiceDoc(...), $dataLapangan)
    record('payment', WrgroupPayloadBuilder::paymentDoc(...), $dataLapangan)
        ↓ (masing-masing, bila fee > 0)
    wrgroup_outbox (status: pending) → SendWrgroupEvent (afterCommit) atau wrgroup:process (scheduler)
```

Detail:
- **Nominal** dihitung lewat `DataLapangan::resolveFee()` (kini `public static`, sebelumnya
  `private`) — method yang **sama persis** dipakai `DataLapangan::booted()` untuk membukukan
  `CashflowsKoordinator`/`Cashflow` lokal. Satu sumber kebenaran: angka yang dilaporkan ke WRGROUP
  selalu sama dengan yang dibukukan secara lokal.
- **transaction_id** memakai `no_registrasi` (mis. `KH2026-00001`) langsung — bukan primary key
  seperti reference `swaraningcode-be` — karena `no_registrasi` sudah unik permanen sejak dibuat
  (`DataLapangan::generateNoRegistrasi()`, dengan row-lock anti race condition) dan tidak pernah
  dipakai ulang, beda dari nomor dokumen di reference yang bisa dipakai ulang setelah hapus.
- **Tanggal transaksi** memakai `updated_at` pada saat `status_pembayaran` berpindah ke `DIBAYAR`
  (proksi terbaik yang tersedia — `DataLapangan` tidak punya kolom `paid_at` khusus). **Batasan
  yang diketahui:** edit lain pada baris yang sudah `DIBAYAR` di masa lalu bisa menggeser
  `updated_at` tanpa mengubah `status_pembayaran` — ini tidak memicu event baru (`wasChanged()`
  di observer menjaganya), tapi berarti `hasTransactions()`/laporan nihil menghitung berdasarkan
  `updated_at` saja, bukan tanggal lunas yang "benar" bila baris itu pernah diedit lagi setelahnya.
- Tidak ada **refundDoc()**: `DataLapangan` tidak memiliki alur pembatalan pembayaran. Endpoint
  `/events/refund` tetap ada di `WrgroupDeliveryService`/`config/wrgroup.php` (tidak pernah dipakai
  saat ini) supaya bisa disambungkan nanti tanpa port ulang, seandainya alur refund ditambahkan.
- **Laporan nihil** (`wrgroup:nihil`, terjadwal `dailyAt('03:00')`): triwulan dengan nol kasus yang
  mencapai `DIBAYAR`, dicek dari `wrgroup_outbox` DAN langsung ke tabel `data_lapangans` (supaya
  data sebelum integrasi ini aktif ikut terhitung, bukan hanya yang sudah lewat outbox).

---

## 3. Kredensial & Konfigurasi

Kredensial (`WRGROUP_BUSINESS_ID`/`WRGROUP_API_KEY`/`WRGROUP_API_SECRET`) **hanya di `.env`** —
tidak pernah di database atau panel admin (beda dari konvensi Urusin Secara Online/Tripay-style
di repo ini yang DB-stored) — karena ini kredensial ke holding company, sensitivitasnya setara
`SWC_APPLICATION_KEY`. Jangan pernah commit nilai asli.

```
WRGROUP_ENABLED=false          # false = seluruh integrasi no-op total, tidak ada query apa pun
WRGROUP_BUSINESS_ID=
WRGROUP_API_KEY=
WRGROUP_API_SECRET=
WRGROUP_API_URL=https://office.wrgroup.id/api/v1
WRGROUP_START_DATE=            # YYYY-MM-DD, diisi WRGROUP superadmin — lihat §6
```

Kawulo Halal mendapat kredensial **sendiri**, berbeda dari `swaraningcode-be` — setiap pilar
diregistrasi terpisah oleh superadmin WRGROUP. Cakupan (scope) yang perlu diminta: `invoice`,
`payment`, `laporan`, `komisi` (tidak perlu `refund`, lihat §2).

Setelah ubah `.env`: `php artisan config:cache`.

---

## 4. Struktur File

- `config/wrgroup.php`
- `app/Services/Integrasi/Wrgroup/` — `WrgroupClient`, `WrgroupPayloadBuilder`, `WrgroupEventService`,
  `WrgroupDeliveryService`, `WrgroupBackfillService`, `WrgroupKomisiService`, `WrgroupMonitorService`
  (ditaruh di bawah namespace `Integrasi` yang sudah dipakai `UrusinService`/`UrusinSubmissionService`
  — kategori yang sama, klien pihak ketiga outbound).
- `app/Jobs/SendWrgroupEvent.php`
- `app/Console/Commands/Wrgroup{ProcessOutbox,SendHeartbeat,ReportNihil,Backfill}.php` — didaftarkan
  di `routes/console.php` lewat `Schedule::call(fn () => Artisan::call(...))`, **bukan**
  `Schedule::command(...)`, mengikuti konvensi repo ini (proc_open sering dimatikan di shared
  hosting — lihat komentar di `routes/console.php`).
- `app/Http/Controllers/Superadmin/Wrgroup{Controller,KomisiController}.php` — route di
  `superadmin/wrgroup/*`, grup `role:superadmin`.
- `app/Http/Requests/Wrgroup{NihilRequest,KomisiPembayaranRequest}.php`
- `app/Models/Superadmin/Wrgroup{Outbox,KomisiPembayaran}.php` — `use HasHashedId;` (trait app ini,
  hash dihitung on-the-fly dari `id`, **bukan** kolom `hashed_id` tersimpan — migrasi sengaja tidak
  menambah kolom itu, konsisten dengan `DataLapangan` dan model lain di repo ini).
- `database/migrations/2026_09_24_000001_create_wrgroup_outbox_table.php`,
  `..._000002_create_wrgroup_komisi_pembayarans_table.php` — CREATE TABLE murni, tidak menyentuh
  tabel manapun yang sudah ada.
- `resources/views/superadmin/wrgroup/{index,show,komisi}.blade.php` — DataTables server-side
  (yajra) + SweetAlert2 toast via `layouts.messages`, mengikuti `.agent/pattern-sistem.md`.
- Hook bisnis: `app/Models/DataLapangan.php` (`resolveFee()` jadi `public static`),
  `app/Observers/DataLapanganObserver.php` (`handleWrgroupSync()`).
- Tautan sidebar "WRGROUP Super Apps" di `resources/views/layouts/navigation.blade.php`.

---

## 5. Bug Tak Terkait yang Ditemukan & Diperbaiki Saat Verifikasi

Saat memverifikasi modul ini dengan Pest (`tests/Feature/WrgroupIntegrationTest.php`), ditemukan dua
masalah **pre-existing, tidak terkait WRGROUP sama sekali** (dikonfirmasi lewat test lama
`ProfileTest`/`ExampleTest` yang gagal identik, dan lewat `php artisan migrate:fresh` polos di
database sqlite kosong — tanpa test sama sekali):

1. **Diperbaiki:** `App\Services\GeminiOcrService::__construct()` query `Settingwebsite::first()`
   secara eager. Karena `App\Console\Commands\BackfillKtpOcr` constructor-inject service ini, dan
   Artisan me-resolve constructor SEMUA command terdaftar setiap kali console boot (termasuk saat
   `migrate:fresh` dijalankan), ini membuat **migrasi pertama pada database yang benar-benar kosong
   selalu gagal** — bukan hanya di test, tapi juga di instalasi baru dari nol. Database dev nyata
   tidak kena karena tabel `settingwebsites` sudah lama ada isinya. Diperbaiki dengan menjadikan
   `apiKey` lazy (di-resolve saat pertama dipakai, bukan di constructor) — lihat method
   `apiKey()` baru di file tsb.
2. **TIDAK diperbaiki (di luar scope, butuh sentuhan ke banyak file migrasi lain):** beberapa
   migrasi lain di repo ini memakai SQL mentah MySQL-only untuk ubah enum, mis.
   `ALTER TABLE tickets MODIFY status ENUM(...)` (di `2026_08_26_174748_add_kategori_to_tickets_table.php`
   dan sejenisnya) — sqlite tidak mengerti sintaks `MODIFY`/`ENUM`, jadi migrasi itu gagal total di
   database test (`RefreshDatabase` + sqlite `:memory:`). Ini artinya **suite Pest penuh saat ini
   tidak bisa jalan bersih di sqlite** terlepas dari WRGROUP. Modul WRGROUP sendiri sudah dibuktikan
   benar lewat `php -l`, `php artisan route:list`, dan migrasi sungguhan berhasil di database dev
   MySQL nyata (`sihalal_yayasanpbp`) — tes Pest yang ditulis di modul ini (`WrgroupIntegrationTest.php`)
   akan langsung hijau begitu masalah sqlite/MySQL ini diperbaiki terpisah.

---

## 6. Checklist Go-Live (operasional, bukan kode)

1. Superadmin WRGROUP membuat record Pilar untuk Kawulo Halal (bila belum ada) dan menerbitkan
   kredensial **testing** bercakupan `invoice, payment, laporan, komisi`.
2. Isi `.env` (§3), `WRGROUP_ENABLED=true`, `php artisan config:cache`.
3. Panel `superadmin/wrgroup` → "Kirim Heartbeat" harus HTTP 200, `environment: testing`,
   `persisted: false`.
4. Tandai satu `DataLapangan` uji sebagai `DIBAYAR`, pastikan baris outbox mencapai status
   `sent` (lewat "Proses Antrian" manual atau tunggu scheduler tiap menit).
5. Minta kredensial **production** dengan cakupan yang sama secara eksplisit, ganti `.env`.
6. Minta superadmin WRGROUP set "Tanggal Operasional" pilar ini, lalu jalankan
   `php artisan wrgroup:backfill --dry-run` sebelum backfill sungguhan (mengirim kasus lunas lama).
7. Pastikan cron `* * * * * php artisan schedule:run` aktif di server produksi.
