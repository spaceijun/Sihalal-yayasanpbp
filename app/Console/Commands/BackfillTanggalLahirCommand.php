<?php

namespace App\Console\Commands;

use App\Models\DataLapangan;
use Illuminate\Console\Command;

/**
 * BackfillTanggalLahirCommand
 *
 * Perintah Artisan untuk mengekstrak dan mengisi (backfill) tanggal_lahir
 * pada tabel `data_lapangans` berdasarkan 6 digit tengah Nomor Induk Kependudukan (NIK).
 *
 * Rumus Struktur NIK Indonesia:
 * - 6 Digit Pertama : Kode Wilayah (Provinsi, Kab/Kota, Kecamatan)
 * - 6 Digit Tengah  : Tanggal Lahir (DDMMYY)
 *   - Laki-laki     : Tanggal 01 - 31
 *   - Perempuan     : Tanggal 41 - 71 (Tanggal Lahir asli + 40)
 * - 4 Digit Terakhir: Nomor Urut
 *
 * Cara penggunaan:
 *   php artisan app:backfill-tanggal-lahir               — proses semua data yang tanggal_lahir-nya masih kosong
 *   php artisan app:backfill-tanggal-lahir --dry-run      — simulasi tanpa menyimpan perubahan ke DB
 *   php artisan app:backfill-tanggal-lahir --limit=100    — batasi jumlah record yang diproses
 *   php artisan app:backfill-tanggal-lahir --id=123       — proses satu record spesifik berdasarkan ID
 *   php artisan app:backfill-tanggal-lahir --force        — overwrite tanggal_lahir yang sudah terisi
 */
class BackfillTanggalLahirCommand extends Command
{
    /**
     * Nama dan deskripsi signature command.
     *
     * @var string
     */
    protected $signature = 'app:backfill-tanggal-lahir
                            {--dry-run : Simulasi — tampilkan hasil tanpa menyimpan ke DB}
                            {--force   : Overwrite tanggal_lahir yang sudah terisi nilainya}
                            {--limit=0 : Batasi jumlah record yang diproses (0 = semua)}
                            {--id=     : Proses hanya satu record berdasarkan ID}';

    /**
     * Deskripsi command console.
     *
     * @var string
     */
    protected $description = 'Backfill tanggal_lahir pada tabel data_lapangans berdasarkan rumus 6 digit tengah NIK';

    /**
     * Eksekusi perintah console.
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $force    = $this->option('force');
        $limit    = (int) $this->option('limit');
        $singleId = $this->option('id');

        $this->info('');
        $this->info('╔══════════════════════════════════════════════════════════╗');
        $this->info('║    BACKFILL TANGGAL LAHIR DARI NIK - DATA LAPANGANS      ║');
        $this->info('╚══════════════════════════════════════════════════════════╝');

        if ($isDryRun) {
            $this->warn('  ⚠️  Mode: DRY RUN — Perubahan tidak akan disimpan ke Database');
        }
        if ($force) {
            $this->warn('  ⚠️  Mode: FORCE — Tanggal lahir yang sudah terisi akan di-overwrite');
        }

        // Query data_lapangans yang memiliki NIK
        $query = DataLapangan::query()->whereNotNull('nik')->where('nik', '!=', '');

        if ($singleId) {
            $query->where('id', $singleId);
        } elseif (! $force) {
            // Hanya ambil yang tanggal_lahir-nya masih NULL / kosong
            $query->whereNull('tanggal_lahir');
        }

        if ($limit > 0) {
            $query->limit($limit);
        }

        $records = $query->get();
        $total   = $records->count();

        if ($total === 0) {
            $this->info('Tidak ada record yang perlu diproses.');
            return Command::SUCCESS;
        }

        $this->info("Ditemukan {$total} record untuk diproses.\n");

        $bar           = $this->output->createProgressBar($total);
        $bar->start();

        $updatedCount  = 0;
        $skippedCount  = 0;
        $invalidNikCount = 0;

        foreach ($records as $record) {
            $parsedDate = static::parseTanggalLahirFromNik($record->nik);

            if (! $parsedDate) {
                $invalidNikCount++;
                $bar->advance();
                continue;
            }

            // Jika tanggal lahir sudah sama, skip
            if ($record->tanggal_lahir && $record->tanggal_lahir->format('Y-m-d') === $parsedDate) {
                $skippedCount++;
                $bar->advance();
                continue;
            }

            if (! $isDryRun) {
                // Gunakan quiet save / update langsung agar tidak memicu event observer yang tidak perlu jika ada
                $record->tanggal_lahir = $parsedDate;
                $record->saveQuietly();
            }

            $updatedCount++;
            $bar->advance();
        }

        $bar->finish();
        $this->info("\n");

        // Tampilkan Summary Results
        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Total Data Diproses', $total],
                ['Berhasil Updated ' . ($isDryRun ? '(Simulasi)' : ''), $updatedCount],
                ['Diskip (Data Sama / Sudah Sesuai)', $skippedCount],
                ['Gagal / NIK Tidak Valid', $invalidNikCount],
            ]
        );

        if ($isDryRun) {
            $this->warn('DRY RUN Selesai. Tidak ada perubahan yang disimpan.');
        } else {
            $this->info('Proses backfill tanggal lahir selesai dengan sukses!');
        }

        return Command::SUCCESS;
    }

    /**
     * Ekstrak tanggal lahir dari NIK 16 digit sesuai rumus resmi KTP Indonesia.
     *
     * Rumus:
     * - Digit 7-8  : Hari lahir (Laki-laki: 01-31, Perempuan: 41-71 [Hari + 40])
     * - Digit 9-10 : Bulan lahir (01-12)
     * - Digit 11-12: Tahun lahir (YY)
     *
     * @param string|null $nik
     * @return string|null Format 'Y-m-d' atau NULL jika NIK tidak valid
     */
    public static function parseTanggalLahirFromNik(?string $nik): ?string
    {
        if (empty($nik)) {
            return null;
        }

        // Hapus karakter non-digit (seperti spasi, strip, dll.)
        $cleanNik = preg_replace('/[^0-9]/', '', trim($nik));

        // NIK Indonesia harus tepat 16 digit
        if (strlen($cleanNik) !== 16) {
            return null;
        }

        $dayRaw  = (int) substr($cleanNik, 6, 2);
        $month   = (int) substr($cleanNik, 8, 2);
        $yearYY  = (int) substr($cleanNik, 10, 2);

        // Jika dayRaw > 40, berarti Perempuan -> kurangi 40 untuk dapatkan tanggal lahir asli
        $day = $dayRaw > 40 ? ($dayRaw - 40) : $dayRaw;

        // Tentukan tahun 4 digit (YYYY)
        // Jika YY > 2 digit tahun saat ini (misal 26 untuk 2026), maka abad 20 (19YY).
        // Jika YY <= 2 digit tahun saat ini, maka abad 21 (20YY).
        $currentYearYY = (int) date('y');
        $year = ($yearYY > $currentYearYY) ? (1900 + $yearYY) : (2000 + $yearYY);

        // Validasi keabsahan tanggal kalender Masehi (misal menghidari 31 Februari)
        if (! checkdate($month, $day, $year)) {
            return null;
        }

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }
}
