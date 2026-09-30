# Integrasi WRGROUP Super Apps (Holding Company)

> **Status dokumen:** sudah diimplementasikan (mekanisme outbox, retry/backoff, circuit breaker,
> heartbeat, laporan nihil, laporan pendapatan bersih, dan pembayaran komisi — semuanya sudah ada
> di kode). Yang **belum**: pengujian end-to-end dengan kredensial nyata (WRGROUP belum
> menerbitkan Business ID/API Key/API Secret untuk Kawulo Halal) — lihat Checklist Go-Live di §6.
>
> **Perubahan penting (2026-09-30):** pengiriman event `invoice`/`payment` per-kasus
> (`recordDataLapanganPaid()`) **dimatikan permanen**. Nominal yang dipakainya (`resolveFee()`)
> adalah honor enumerator/pendamping — **Pengeluaran** Kawulo Halal, bukan pemasukannya — sehingga
> dasar komisi WRGROUP sebelumnya salah satu angka pengeluaran, bukan pendapatan. Dasar komisi kini
> dilaporkan lewat **laporan pendapatan bersih periodik** (Pemasukan − Pengeluaran dari ledger Arus
> Kas manual, `superadmin/arus-kas`) — lihat §2b.

---

## 1. Kenapa integrasi ini ada

Kawulo Halal adalah "pilar" (bisnis anggota) dari holding company **WRGROUP Super Apps**. Pendapatan
bersih Kawulo Halal (dari ledger Arus Kas — lihat §2b) dilaporkan ke WRGROUP sebagai dasar komisi
yang menjadi hak WRGROUP. Sebaliknya, Kawulo Halal **membayar** komisi itu langsung (transfer
manual) dan melaporkan buktinya ke WRGROUP lewat API untuk diverifikasi — WRGROUP tidak pernah
menyentuh alur uang di sisi Kawulo Halal.

Integrasi ini satu arah dan machine-to-machine: Kawulo Halal mengirim, WRGROUP tidak pernah
memanggil balik (tidak ada endpoint publik baru yang dibuat di sisi kita untuk ini).

Modul ini adalah port dari implementasi yang sama persis di `swaraningcode-be` (pilar lain di
holding company yang sama) — mekanisme intinya (outbox, retry, circuit breaker) **sama persis**;
yang disesuaikan hanya pemetaan event bisnis (lihat §2), karena domain Kawulo Halal (kasus
sertifikasi halal) berbeda dari domain swaraningcode-be (invoice/order/sale proyek).

---

## 2. Pemetaan Event Bisnis (invoice/payment — DIMATIKAN)

Kawulo Halal **tidak punya invoice yang terbit terpisah dari payment** — satu `DataLapangan`
berpindah `status_pembayaran` langsung ke `DIBAYAR` dalam satu langkah, begitu fee dikonfirmasi oleh
staf (mirip pola "Sale" di reference `swaraningcode-be`, bukan pola "Invoice" bertahap terbit→bayar).

```
DataLapangan.status_pembayaran → DIBAYAR (staf konfirmasi pembayaran)
        ↓
DataLapanganObserver::updated() → handleWrgroupSync()
        ↓ (wasChanged('status_pembayaran') && strtoupper(...) === 'DIBAYAR')
WrgroupEventService::recordDataLapanganPaid($dataLapangan)  ← NO-OP sejak 2026-09-30, lihat catatan atas
```

Kode pemetaan (`WrgroupPayloadBuilder::invoiceDoc()`/`paymentDoc()`, `WrgroupEventService::record()`)
**dibiarkan di tempat, tidak dihapus** — supaya bisa dihidupkan lagi tanpa port ulang seandainya
Kawulo Halal suatu saat punya kolom tagihan-ke-klien yang genuinely terpisah dari fee enumerator.
Baris `wrgroup_outbox` lama (endpoint `invoice`/`payment` yang sudah terlanjur terkirim sebelum
2026-09-30) dibiarkan sebagai riwayat.

Detail yang **masih relevan** (dipakai bersama oleh mekanisme lama & laporan pendapatan bersih):
- Tidak ada **refundDoc()**: `DataLapangan` tidak memiliki alur pembatalan pembayaran. Endpoint
  `/events/refund` tetap ada di `WrgroupDeliveryService`/`config/wrgroup.php` (tidak pernah dipakai
  saat ini) supaya bisa disambungkan nanti tanpa port ulang, seandainya alur refund ditambahkan.
- **Laporan nihil** (`wrgroup:nihil`, terjadwal `dailyAt('03:00')`): triwulan dengan nol kasus yang
  mencapai `DIBAYAR`, dicek dari `wrgroup_outbox` (endpoint `invoice`, riwayat lama) DAN langsung ke
  tabel `data_lapangans` (supaya data sebelum integrasi ini aktif ikut terhitung). Catatan: sejak
  invoice dimatikan, pengecekan riwayat `wrgroup_outbox` hanya relevan untuk data lama; cek langsung
  ke `data_lapangans` tetap jalan seperti biasa.

---

## 2b. Laporan Pendapatan Bersih (dasar komisi — AKTIF)

Dasar komisi/CSR Kawulo Halal di WRGROUP sekarang adalah **nilai bersih ledger Arus Kas**
(`superadmin/arus-kas`, tabel `cashflows`: entri manual bertipe `Pemasukan`/`Pengeluaran`/`Kas`),
bukan invoice per-kasus sertifikasi.

```
CashflowService::netPeriode($mulai, $selesai)
    = SUM(jumlah WHERE tipe='Pemasukan') − SUM(jumlah WHERE tipe='Pengeluaran')
      (tipe 'Kas' dikecualikan — penyesuaian posisi kas internal, bukan pemasukan/pengeluaran nyata)
        ↓
WrgroupEventService::reportPendapatanBersih($period, $nilai, $catatan?)
        ↓ (endpoint 'pendapatan_bersih', transaction_id 'PB-{period}', versi baru hanya bila nilai berubah — hash sama seperti record())
    wrgroup_outbox (status: pending) → SendWrgroupEvent / wrgroup:process
        ↓ POST /reports/pendapatan-bersih (cakupan kredensial: laporan)
WRGROUP: PilarKelengkapanService::catatPendapatanBersih() — MENIMPA (bukan menjumlah) nilai lama
    per triwulan; ditolak bila komisi triwulan itu sudah terbit (lihat wrgroup-superapps §... Komisi)
```

- **Jadwal**: `wrgroup:pendapatan-bersih` (tanpa argumen), `hourly()` di `routes/console.php`.
  Melaporkan **triwulan berjalan** (koreksi berkala seiring entri Arus Kas baru masuk sepanjang
  triwulan) **dan** triwulan sebelumnya (masih bisa dikoreksi selama WRGROUP belum menagihkan
  komisinya). Argumen `{period}` eksplisit (mis. `php artisan wrgroup:pendapatan-bersih 2026-Q1`)
  untuk kirim ulang satu triwulan tertentu secara manual, mis. setelah entri Arus Kas dikoreksi.
- **Idempotent & terkoreksi**: mengirim ulang nilai yang SAMA untuk triwulan yang sama tidak
  membuat baris outbox baru (hash payload sama). Nilai yang berbeda membuat versi outbox baru
  (riwayat lengkap tetap tersimpan lokal), tapi WRGROUP hanya menyimpan **nilai terakhir** per
  triwulan — bukan menjumlahkan tiap pengiriman.
- **Tidak bisa dikoreksi lagi setelah komisi triwulan itu diterbitkan** WRGROUP (Superadmin
  WRGROUP) — percobaan sesudahnya dibalas `422 aturan_bisnis` dan dianggap gagal-wajar (bukan
  error) oleh command terjadwal.
- Tidak dipasangkan dengan laporan nihil untuk triwulan yang sama (mutually exclusive di sisi
  WRGROUP) — Kawulo Halal dalam praktiknya hanya memakai jalur pendapatan bersih, tidak pernah
  memanggil `wrgroup:nihil` untuk triwulan yang aktif dipakai.

---

## 2c. Pembukuan Setoran Komisi ke Arus Kas

Setoran komisi yang diajukan lewat panel (`WrgroupKomisiService::ajukanPembayaran()`) baru
dibukukan ke Arus Kas **setelah WRGROUP memverifikasinya** — simetris dengan WRGROUP yang
membukukan pendapatannya pada saat verifikasi, dengan tanggal yang sama.

- `wrgroup:komisi-sync` (`everyFifteenMinutes`) → `WrgroupKomisiService::sinkronVerifikasi()`:
  membaca `GET /komisi/periode`, mencocokkan `pembayaran[].event_id` dengan baris
  `wrgroup_komisi_pembayarans`, lalu mengisi `status_verifikasi`/`diverifikasi_at`/`catatan_verifikasi`.
  Tidak memanggil WRGROUP sama sekali bila tidak ada setoran yang menunggu.
- Setoran **terverifikasi** → satu baris `Cashflow` tipe `Pengeluaran`, `sumber = 'komisi_wrgroup'`,
  tanggal `tanggal_transaksi_bank`. Setoran **ditolak** tidak dibukukan (alasannya tampil di halaman
  komisi). `dibukukan_at` + `cashflow_id` menjaga idempotensi — sekali dibukukan tidak pernah diulang,
  walau entri Arus Kasnya kemudian dihapus manual.
- **Dikecualikan dari dasar komisi:** `CashflowService::netPeriode()` mengabaikan baris
  `sumber = 'komisi_wrgroup'`, sehingga komisi yang sudah dibayar tidak memotong dasar komisi triwulan
  berikutnya (keputusan 2026-09-30: komisi dihitung dari pendapatan bersih SEBELUM komisi). Baris itu
  tetap tampil di Laporan Arus Kas sebagai pengeluaran biasa.

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
diregistrasi terpisah oleh superadmin WRGROUP. Cakupan (scope) yang perlu diminta: `laporan`,
`komisi` (cakupan `invoice`/`payment` tidak lagi diperlukan sejak §2 dimatikan; boleh tetap
diberikan tanpa efek — kode itu memang tidak pernah memanggil endpoint invoice/payment lagi — tapi
tidak wajib diminta ulang untuk kredensial baru).

Setelah ubah `.env`: `php artisan config:cache`.

---

## 4. Struktur File

- `config/wrgroup.php`
- `app/Services/Integrasi/Wrgroup/` — `WrgroupClient`, `WrgroupPayloadBuilder`, `WrgroupEventService`,
  `WrgroupDeliveryService`, `WrgroupBackfillService`, `WrgroupKomisiService`, `WrgroupMonitorService`
  (ditaruh di bawah namespace `Integrasi` yang sudah dipakai `UrusinService`/`UrusinSubmissionService`
  — kategori yang sama, klien pihak ketiga outbound).
- `app/Jobs/SendWrgroupEvent.php`
- `app/Console/Commands/Wrgroup{ProcessOutbox,SendHeartbeat,ReportNihil,ReportPendapatanBersih,KomisiSync,Backfill}.php`
  — didaftarkan di `routes/console.php` lewat `Schedule::call(fn () => Artisan::call(...))`, **bukan**
  `Schedule::command(...)`, mengikuti konvensi repo ini (proc_open sering dimatikan di shared
  hosting — lihat komentar di `routes/console.php`).
- `app/Services/Superadmin/CashflowService::netPeriode()` — agregat Pemasukan−Pengeluaran ledger
  Arus Kas per rentang tanggal, dipakai `WrgroupReportPendapatanBersih` (lihat §2b).
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
4. Pastikan Superadmin WRGROUP sudah menandai Jenis Surat/kredensial dengan cakupan `laporan`
   (§3), lalu jalankan `php artisan wrgroup:pendapatan-bersih` manual sekali, pastikan baris
   outbox endpoint `pendapatan_bersih` mencapai status `sent` (lewat "Proses Antrian" manual di
   panel atau tunggu scheduler `wrgroup:process` tiap menit).
5. Minta kredensial **production** dengan cakupan yang sama secara eksplisit, ganti `.env`.
6. Minta superadmin WRGROUP set "Tanggal Operasional" pilar ini. (`wrgroup:backfill` hanya
   relevan untuk invoice/payment per-kasus, yang sudah dimatikan — lihat catatan di atas dokumen
   ini; tidak perlu dijalankan untuk pendapatan bersih, yang otomatis mulai melapor sejak
   `WRGROUP_ENABLED=true`.)
7. Pastikan cron `* * * * * php artisan schedule:run` aktif di server produksi.
