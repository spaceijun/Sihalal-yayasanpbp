<?php

namespace App\Console\Commands;

use App\Models\DataLapangan;
use App\Services\GeminiOcrService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * BackfillKtpOcr
 *
 * Perintah Artisan untuk mengisi field kosong (NIK, alamat, provinsi, dll.)
 * pada data lapangan produksi yang sudah ada, menggunakan foto KTP yang tersimpan
 * dan Gemini OCR API.
 *
 * Cara penggunaan:
 *   php artisan app:backfill-ktp-ocr               — proses semua data yang punya field kosong
 *   php artisan app:backfill-ktp-ocr --dry-run      — simulasi tanpa menyimpan ke DB
 *   php artisan app:backfill-ktp-ocr --limit=50     — batasi 50 record per run
 *   php artisan app:backfill-ktp-ocr --delay=3      — delay antar request (detik, default: 2)
 *   php artisan app:backfill-ktp-ocr --id=123       — proses satu record saja (by ID)
 *   php artisan app:backfill-ktp-ocr --force        — overwrite field yang sudah ada juga
 */
class BackfillKtpOcr extends Command
{
    protected $signature = 'app:backfill-ktp-ocr
                            {--dry-run    : Simulasi — tampilkan hasil tapi jangan simpan ke DB}
                            {--limit=0   : Batasi jumlah record yang diproses (0 = semua)}
                            {--delay=2   : Jeda antar API request dalam detik}
                            {--id=       : Proses hanya satu record berdasarkan ID}
                            {--force     : Overwrite field yang sudah ada nilainya}';

    protected $description = 'Isi field kosong (NIK, alamat, wilayah, dll.) pada data lapangan menggunakan OCR foto KTP via Gemini AI';

    // Field yang akan diisi dari hasil OCR
    private const OCR_FIELDS = [
        'nik'       => 'NIK',
        'nama_pu'   => 'Nama PU',
        'alamat'    => 'Alamat',
        'rt'        => 'RT',
        'rw'        => 'RW',
        'provinsi'  => 'Provinsi',
        'kabupaten' => 'Kabupaten',
        'kecamatan' => 'Kecamatan',
        'kelurahan' => 'Kelurahan',
        'kode_pos'  => 'Kode Pos',
    ];

    public function __construct(private GeminiOcrService $ocrService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $limit    = (int) $this->option('limit');
        $delay    = (int) $this->option('delay');
        $singleId = $this->option('id');
        $force    = $this->option('force');

        $this->info('');
        $this->info('╔══════════════════════════════════════════════════╗');
        $this->info('║       Backfill KTP OCR — KAWULOHALAL             ║');
        $this->info('╚══════════════════════════════════════════════════╝');

        if ($isDryRun) {
            $this->warn('  Mode: DRY RUN — tidak ada data yang akan disimpan');
        }
        if ($force) {
            $this->warn('  Mode: FORCE — field yang sudah ada pun akan di-overwrite');
        }

        // Build query
        $query = DataLapangan::query()->whereNotNull('foto_ktp');

        if ($singleId) {
            $query->where('id', $singleId);
        } elseif (! $force) {
            // Hanya record yang punya minimal satu field kosong
            $query->where(function ($q) {
                $q->whereNull('nik')
                  ->orWhere('nik', '')
                  ->orWhereNull('alamat')
                  ->orWhere('alamat', '')
                  ->orWhereNull('provinsi')
                  ->orWhere('provinsi', '');
            });
        }

        if ($limit > 0) {
            $query->limit($limit);
        }

        $records = $query->get();
        $total   = $records->count();

        if ($total === 0) {
            $this->info('  Tidak ada data yang perlu diproses.');
            return self::SUCCESS;
        }

        $this->info("  Total record yang akan diproses: {$total}");
        $this->info("  Delay antar request: {$delay} detik");
        $this->info('');

        $updated   = 0;
        $skipped   = 0;
        $failed    = 0;
        $retryable = [];

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($records as $index => $record) {
            $bar->advance();

            $result = $this->processRecord($record, $force, $isDryRun);

            if ($result === 'updated') {
                $updated++;
            } elseif ($result === 'skipped') {
                $skipped++;
            } elseif ($result === 'rate_limited') {
                $retryable[] = $record->id;
                $failed++;
                $this->newLine();
                $this->warn("  ⏸  Rate limited pada record #{$record->id}. Akan dicatat untuk retry.");
                // Tunggu lebih lama jika rate limited
                sleep(max(10, $delay * 5));
                continue;
            } else {
                $failed++;
            }

            // Delay antar request (kecuali record terakhir)
            if ($index < $total - 1 && $delay > 0) {
                sleep($delay);
            }
        }

        $bar->finish();
        $this->newLine(2);

        // Laporan akhir
        $this->info('──────────────────────────────────────────────────');
        $this->info("  ✅  Berhasil diperbarui : {$updated}");
        $this->info("  ⏭  Dilewati (tidak ada field baru) : {$skipped}");
        $this->error("  ❌  Gagal              : {$failed}");

        if (! empty($retryable)) {
            $this->warn('  Record yang perlu di-retry: '.implode(', ', $retryable));
            $this->warn('  Jalankan: php artisan app:backfill-ktp-ocr --id=<ID>');
        }

        Log::info('BackfillKtpOcr selesai', compact('updated', 'skipped', 'failed', 'total'));

        return self::SUCCESS;
    }

    private function processRecord(DataLapangan $record, bool $force, bool $isDryRun): string
    {
        // Validasi: foto_ktp harus ada di storage
        $fotoPath = $record->foto_ktp;

        // Support path publik maupun storage
        $absolutePath = null;
        if (Storage::disk('public')->exists($fotoPath)) {
            $absolutePath = Storage::disk('public')->path($fotoPath);
        } elseif (file_exists(public_path($fotoPath))) {
            $absolutePath = public_path($fotoPath);
        } elseif (file_exists(storage_path('app/public/'.$fotoPath))) {
            $absolutePath = storage_path('app/public/'.$fotoPath);
        }

        if (! $absolutePath || ! file_exists($absolutePath)) {
            $this->newLine();
            $this->error("  ✗ Record #{$record->id} ({$record->nama_pu}): File foto KTP tidak ditemukan → {$fotoPath}");
            Log::warning('BackfillKtpOcr: file tidak ditemukan', ['id' => $record->id, 'path' => $fotoPath]);
            return 'failed';
        }

        // Baca & encode gambar ke base64
        $base64 = base64_encode(file_get_contents($absolutePath));

        // Panggil Gemini OCR
        $ocrResult = $this->ocrService->scanKtp($base64);

        if (! $ocrResult['success']) {
            $errorMsg = $ocrResult['error'] ?? 'Unknown error';

            // Deteksi rate limiting
            if (str_contains($errorMsg, '429') || str_contains(strtolower($errorMsg), 'rate')) {
                return 'rate_limited';
            }

            $this->newLine();
            $this->error("  ✗ Record #{$record->id}: OCR gagal — {$errorMsg}");
            Log::error('BackfillKtpOcr: OCR gagal', ['id' => $record->id, 'error' => $errorMsg]);
            return 'failed';
        }

        $ocrData = $ocrResult['data'] ?? [];

        // Tentukan field mana yang perlu diperbarui
        $toUpdate = [];

        foreach (self::OCR_FIELDS as $field => $label) {
            $ocrValue = trim($ocrData[$field] ?? '');
            if (empty($ocrValue)) continue;

            $currentValue = trim($record->$field ?? '');

            // Hanya update jika kosong (atau force mode)
            if ($force || empty($currentValue)) {
                // Normalisasi nama_pu ke uppercase
                if ($field === 'nama_pu') {
                    $ocrValue = strtoupper($ocrValue);
                }
                // Validasi NIK harus 16 digit
                if ($field === 'nik' && strlen(preg_replace('/\D/', '', $ocrValue)) !== 16) {
                    continue;
                }
                $toUpdate[$field] = $ocrValue;
            }
        }

        if (empty($toUpdate)) {
            return 'skipped';
        }

        // Tampilkan detail perubahan
        $this->newLine();
        $this->line("  ✓ Record #{$record->id} — {$record->nama_pu}:");
        foreach ($toUpdate as $field => $value) {
            $this->line("    {$field}: <comment>{$value}</comment>");
        }

        if (! $isDryRun) {
            try {
                $record->update($toUpdate);
                Log::info('BackfillKtpOcr: record diperbarui', [
                    'id'      => $record->id,
                    'updated' => array_keys($toUpdate),
                ]);
            } catch (\Exception $e) {
                $this->error("  ✗ Gagal menyimpan record #{$record->id}: {$e->getMessage()}");
                Log::error('BackfillKtpOcr: gagal simpan', ['id' => $record->id, 'error' => $e->getMessage()]);
                return 'failed';
            }
        }

        return 'updated';
    }
}
