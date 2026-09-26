<?php

/*
|--------------------------------------------------------------------------
| Integrasi WRGROUP Super Apps (holding company)
|--------------------------------------------------------------------------
|
| Satu arah, mesin-ke-mesin: Kawulo Halal (pilar) MENGIRIM event invoice/payment
| (fee sertifikasi halal yang sudah dibayar), laporan nihil triwulan, dan
| heartbeat ke WRGROUP. WRGROUP tidak memanggil balik, jadi tidak ada endpoint
| publik yang dibuat di sisi kita.
|
| Kredensial HANYA dari .env (jangan di-commit, jangan dikirim ke front-end).
| Dokumentasi modul: .agent/workflows/wrgroup-integrasi.md
|
*/

return [

    // Saklar utama. false = seluruh integrasi no-op (tidak ada event yang dicatat).
    'enabled' => (bool) env('WRGROUP_ENABLED', false),

    'business_id' => env('WRGROUP_BUSINESS_ID'),
    'api_key' => env('WRGROUP_API_KEY'),
    'api_secret' => env('WRGROUP_API_SECRET'),

    // Base URL sampai /api/v1 (tanpa slash akhir). Kredensial testing dan production
    // memakai URL yang sama — bedanya hanya di respons (testing: persisted=false).
    // URL bukan rahasia; tanpa kredensial di bawah, tidak ada request yang dikirim.
    'api_url' => env('WRGROUP_API_URL', 'https://office.wrgroup.id/api/v1'),

    'http_timeout' => (int) env('WRGROUP_HTTP_TIMEOUT', 15),
    'http_verify' => (bool) env('WRGROUP_HTTP_VERIFY', true),

    // Format data mengikuti ketentuan pilar: IDR + Asia/Jakarta.
    'currency' => 'IDR',
    'timezone' => 'Asia/Jakarta',

    // Tanggal Kawulo Halal mulai aktif di WRGROUP (YYYY-MM-DD). Laporan nihil otomatis
    // hanya dibuat untuk triwulan yang dimulai pada/setelah triwulan tanggal ini.
    'start_date' => env('WRGROUP_START_DATE'),

    // Perlakuan pajak (PPN) pada dasar komisi masih menunggu keputusan final WRGROUP.
    // true = kirim pajak apa adanya; false = selalu kirim pajak: 0. Kawulo Halal tidak
    // memiliki kolom pajak terpisah pada DataLapangan (fee sertifikasi tidak dipecah
    // PPN-nya) sehingga payload selalu mengirim pajak: 0 apa pun nilai opsi ini — kunci
    // ini dipertahankan hanya untuk menjaga bentuk config identik dgn modul WRGROUP lain.
    'report_tax' => (bool) env('WRGROUP_REPORT_TAX', true),

    // Maks. event diproses per menit oleh wrgroup:process (batas WRGROUP 300/menit/IP).
    'batch_size' => (int) env('WRGROUP_BATCH_SIZE', 100),

    // Jeda antar percobaan (detik) untuk timeout / 5xx / 422 retry:true.
    // Jumlah elemen = maksimum percobaan; setelahnya status menjadi "failed".
    'backoff' => [60, 300, 900, 3600, 10800, 21600, 43200, 86400, 86400, 86400],

    'heartbeat_status' => 'online',
];
