<?php

namespace App\Services;

use App\Models\AnalisisProduct;
use Illuminate\Support\Facades\Log;

/**
 * PanduanHalalService
 *
 * Service untuk panduan halal produk UMKM Indonesia.
 * Alur:
 * 1. Cek database AnalisisProduct untuk hasil analisis sebelumnya
 * 2. Jika tidak ada, generate Google Search URL untuk riset manual
 * 3. Return template panduan untuk diisi manual
 *
 * @author SIHaLAL Yogyakarta
 * @version 2.0.1
 */
class PanduanHalalService
{
    /**
     * Get panduan halal for a product
     *
     * @param  string  $productName
     * @param  int|null  $userId
     * @param  int|null  $dataLapanganId
     * @return array
     */
    public function getPanduan(string $productName, ?int $userId = null, ?int $dataLapanganId = null): array
    {
        $productName = trim($productName);

        // Step 1: Check database for existing analysis
        $existing = $this->getFromDatabase($productName);
        if ($existing !== null) {
            return $this->formatDatabaseResponse($existing);
        }

        // Step 2: Generate Google Search URLs for manual research
        $searchUrls = $this->generateSearchUrls($productName);

        // Step 3: Return template + search URLs
        return $this->generateTemplateResponse($productName, $searchUrls, $userId, $dataLapanganId);
    }

    /**
     * Check database for existing analysis
     */
    private function getFromDatabase(string $productName): ?AnalisisProduct
    {
        return AnalisisProduct::whereRaw('LOWER(product_name) = ?', [strtolower($productName)])
            ->where('is_approved', true)
            ->first();
    }

    /**
     * Save analysis result to database
     */
    public function saveToDatabase(array $data, ?int $userId = null, ?int $dataLapanganId = null): AnalisisProduct
    {
        $analisis = AnalisisProduct::create([
            'product_name' => $data['product_name'] ?? 'Unknown',
            'kemasan' => $data['kemasan'] ?? null,
            'bahan' => is_array($data['bahan'] ?? null) ? $data['bahan'] : null,
            'proses' => is_array($data['proses'] ?? null) ? $data['proses'] : null,
            'catatan_halal' => $data['catatan_halal'] ?? null,
            'status_halal' => $data['status_halal'] ?? 'PERLU_VERIFIKASI',
            'sertifikasi' => $data['sertifikasi'] ?? null,
            'google_search_url' => $data['google_search_url'] ?? null,
            'user_id' => $userId,
            'data_lapangan_id' => $dataLapanganId,
        ]);

        Log::info('PanduanHalalService: Saved new analysis', [
            'product_name' => $data['product_name'] ?? 'Unknown',
            'id' => $analisis->id,
        ]);

        return $analisis;
    }

    /**
     * Update existing analysis
     */
    public function updateInDatabase(int $id, array $data): ?AnalisisProduct
    {
        $analisis = AnalisisProduct::find($id);
        if ($analisis === null) {
            return null;
        }

        $analisis->update($data);
        return $analisis->fresh();
    }

    /**
     * Generate Google Search URLs for product research
     */
    private function generateSearchUrls(string $productName): array
    {
        return [
            'utama' => 'https://www.google.com/search?q=' . urlencode('resep ' . $productName . ' bahan lengkap') . '&hl=id&gl=id',
            'sertifikasi' => 'https://www.google.com/search?q=' . urlencode($productName . ' sertifikasi halal MUI') . '&hl=id&gl=id',
            'kemasan' => 'https://www.google.com/search?q=' . urlencode($productName . ' kemasan label halal MUI') . '&hl=id&gl=id',
            'bpom' => 'https://www.google.com/search?q=' . urlencode($productName . ' BPOM halal') . '&hl=id&gl=id',
            'umum' => 'https://www.google.com/search?q=' . urlencode($productName . ' jajanan tradisional halal') . '&hl=id&gl=id',
        ];
    }

    /**
     * Generate template response with search URLs
     */
    private function generateTemplateResponse(string $productName, array $searchUrls, ?int $userId, ?int $dataLapanganId): array
    {
        $bahanTemplate = $this->generateBahanTemplate($productName);
        $prosesTemplate = $this->generateProsesTemplate($productName);

        return [
            'product_name' => $productName,
            'found_in_database' => false,
            'kemasan' => 'Silakan riset melalui Google Search untuk info kemasan',
            'bahan' => $bahanTemplate,
            'proses' => $prosesTemplate,
            'catatan_halal' => 'Silakan lakukan riset bahan dan proses melalui Google Search',
            'status_halal' => 'PERLU_VERIFIKASI',
            'sertifikasi' => 'Perlu sertifikasi MUI - riset melalui link di bawah',
            'google_search' => $searchUrls,
            'sumber' => 'google_search',
            'produk_matched' => null,
            'warning' => 'Produk belum ada di database. Gunakan Google Search untuk riset bahan dan proses.',
            'save_to_database' => true,
            'user_id' => $userId,
            'data_lapangan_id' => $dataLapanganId,
        ];
    }

    /**
     * Format database response
     */
    private function formatDatabaseResponse(AnalisisProduct $analisis): array
    {
        return [
            'product_name' => $analisis->product_name,
            'found_in_database' => true,
            'kemasan' => $analisis->kemasan ?? '',
            'bahan' => is_array($analisis->bahan) ? $analisis->bahan : [],
            'proses' => is_array($analisis->proses) ? $analisis->proses : [],
            'catatan_halal' => $analisis->catatan_halal ?? '',
            'status_halal' => $analisis->status_halal ?? 'PERLU_VERIFIKASI',
            'sertifikasi' => $analisis->sertifikasi ?? '',
            'google_search' => [
                'utama' => $analisis->google_search_url ?? '',
                'sertifikasi' => 'https://www.google.com/search?q=' . urlencode($analisis->product_name . ' sertifikasi halal MUI'),
            ],
            'sumber' => 'database',
            'produk_matched' => $analisis->product_name,
            'analisis_id' => $analisis->id,
            'created_at' => $analisis->created_at ? $analisis->created_at->toIso8601String() : date('c'),
        ];
    }

    /**
     * Generate bahan template based on product name keywords
     */
    private function generateBahanTemplate(string $productName): array
    {
        $lower = strtolower($productName);
        $templates = [];

        // Detect product type and add appropriate templates
        if (str_contains($lower, 'peyek') || str_contains($lower, 'rempeyek')) {
            $templates = [
                ['nama' => 'Tepung beras ketan', 'kategori' => 'UTAMA', 'status_halal' => 'AMAN', 'keterangan' => 'Bahan utama peyek'],
                ['nama' => 'Tepung tapioka/kanji', 'kategori' => 'UTAMA', 'status_halal' => 'AMAN', 'keterangan' => 'Memberikan tekstur renyah'],
                ['nama' => 'Kacang tanah (opsional)', 'kategori' => 'UTAMA', 'status_halal' => 'AMAN', 'keterangan' => 'Topping kacang'],
                ['nama' => 'Bawang putih', 'kategori' => 'PEMBANTU', 'status_halal' => 'AMAN', 'keterangan' => 'Bumbu dasar'],
                ['nama' => 'Daun jeruk purut', 'kategori' => 'PEMBANTU', 'status_halal' => 'AMAN', 'keterangan' => 'Aroma'],
                ['nama' => 'Garam', 'kategori' => 'PEMBANTU', 'status_halal' => 'AMAN', 'keterangan' => 'Penyedap'],
                ['nama' => 'Gula merah (opsional)', 'kategori' => 'PEMBANTU', 'status_halal' => 'AMAN', 'keterangan' => 'Pemanis alami'],
                ['nama' => 'Minyak goreng', 'kategori' => 'PEMBANTU', 'status_halal' => 'AMAN', 'keterangan' => 'Pilih minyak berlogo halal MUI'],
            ];
        } elseif (str_contains($lower, 'bakso')) {
            $templates = [
                ['nama' => 'Daging sapi/ayam giling', 'kategori' => 'UTAMA', 'status_halal' => 'PERLU_VERIFIKASI', 'keterangan' => 'WAJIB sertifikat halal MUI'],
                ['nama' => 'Tepung tapioka', 'kategori' => 'UTAMA', 'status_halal' => 'AMAN', 'keterangan' => 'Pengikat'],
                ['nama' => 'Bawang putih goreng', 'kategori' => 'PEMBANTU', 'status_halal' => 'AMAN', 'keterangan' => 'Bumbu'],
                ['nama' => 'Merica', 'kategori' => 'PEMBANTU', 'status_halal' => 'AMAN', 'keterangan' => 'Penyedap'],
                ['nama' => 'Es batu', 'kategori' => 'PEMBANTU', 'status_halal' => 'AMAN', 'keterangan' => 'Tekstur'],
            ];
        } elseif (str_contains($lower, 'nasi') || str_contains($lower, 'mie') || str_contains($lower, 'goreng')) {
            $templates = [
                ['nama' => 'Beras/nasi', 'kategori' => 'UTAMA', 'status_halal' => 'AMAN', 'keterangan' => 'Bahan utama'],
                ['nama' => 'Protein (ayam/sapi/udang)', 'kategori' => 'UTAMA', 'status_halal' => 'PERLU_VERIFIKASI', 'keterangan' => 'WAJIB sertifikat halal MUI'],
                ['nama' => 'Minyak goreng', 'kategori' => 'PEMBANTU', 'status_halal' => 'AMAN', 'keterangan' => 'Pilih minyak berlogo halal MUI'],
                ['nama' => 'Bumbu (bawang, cabai, dll)', 'kategori' => 'PEMBANTU', 'status_halal' => 'AMAN', 'keterangan' => 'Umumnya halal'],
                ['nama' => 'Kecap manis', 'kategori' => 'PEMBANTU', 'status_halal' => 'PERLU_VERIFIKASI', 'keterangan' => 'Pilih kecap berlogo halal MUI'],
            ];
        } elseif (str_contains($lower, 'soto') || str_contains($lower, 'soup')) {
            $templates = [
                ['nama' => 'Ayam/sapi', 'kategori' => 'UTAMA', 'status_halal' => 'PERLU_VERIFIKASI', 'keterangan' => 'WAJIB sertifikat halal MUI'],
                ['nama' => 'Santan', 'kategori' => 'UTAMA', 'status_halal' => 'PERLU_VERIFIKASI', 'keterangan' => 'Pilih santan berlogo halal MUI'],
                ['nama' => 'Bihun/nasi', 'kategori' => 'PEMBANTU', 'status_halal' => 'AMAN', 'keterangan' => 'Pelengkap'],
                ['nama' => 'Sayuran', 'kategori' => 'PEMBANTU', 'status_halal' => 'AMAN', 'keterangan' => 'Tauge, kol, seledri'],
            ];
        } elseif (str_contains($lower, 'sate')) {
            $templates = [
                ['nama' => 'Daging (ayam/sapi/kambing)', 'kategori' => 'UTAMA', 'status_halal' => 'PERLU_VERIFIKASI', 'keterangan' => 'WAJIB disembelih halal'],
                ['nama' => 'Kecap manis', 'kategori' => 'PEMBANTU', 'status_halal' => 'AMAN', 'keterangan' => 'Bumbu marinasi'],
                ['nama' => 'Kacang tanah', 'kategori' => 'UTAMA', 'status_halal' => 'AMAN', 'keterangan' => 'Bumbu kacang'],
                ['nama' => 'Arang', 'kategori' => 'PEMBANTU', 'status_halal' => 'AMAN', 'keterangan' => 'Bahan bakar'],
            ];
        } else {
            // Generic template
            $templates = [
                ['nama' => 'Bahan utama', 'kategori' => 'UTAMA', 'status_halal' => 'PERLU_VERIFIKASI', 'keterangan' => 'Silakan riset bahan utama produk ini'],
                ['nama' => 'Bahan tambahan', 'kategori' => 'PEMBANTU', 'status_halal' => 'PERLU_VERIFIKASI', 'keterangan' => 'Silakan riset bahan tambahan'],
                ['nama' => 'Bumbu', 'kategori' => 'PEMBANTU', 'status_halal' => 'AMAN', 'keterangan' => 'Umumnya halal'],
            ];
        }

        // Always add cleaning agent reminder
        $templates[] = ['nama' => 'Sabun cuci piring (Sunlight/Cuci piring)', 'kategori' => 'CLEANING_AGENT', 'status_halal' => 'PERLU_VERIFIKASI', 'keterangan' => 'WAJIB bersihkan residu sabun sebelum digunakan'];

        return $templates;
    }

    /**
     * Generate proses template based on product name keywords
     */
    private function generateProsesTemplate(string $productName): array
    {
        $lower = strtolower($productName);

        if (str_contains($lower, 'peyek') || str_contains($lower, 'rempeyek') || str_contains($lower, 'goreng')) {
            return [
                ['langkah' => 1, 'nama' => 'Persiapan adonan', 'deskripsi' => 'Campurkan tepung beras ketan, tepung tapioka, bawang putih halus, garam, dan gula merah. Tuang air sedikit demi sedikit hingga adonan kalis.', 'titik_kritis' => false],
                ['langkah' => 2, 'nama' => 'Persiapan topping', 'deskripsi' => 'Siapkan topping sesuai jenis (kacang tanah kupas/udang kecil). Pastikan bahan berkualitas.', 'titik_kritis' => false],
                ['langkah' => 3, 'nama' => 'Penggorengan', 'deskripsi' => 'Panaskan minyak goreng halal dalam jumlah banyak. Ambil adonan dengan sendok, goreng hingga renyah dan berwarna kuning keemasan.', 'titik_kritis' => true],
                ['langkah' => 4, 'nama' => 'Penirisan', 'deskripsi' => 'Tiriskan dari minyak dengan saringan bersih. Pastikan BUKAN saringan yang bekas Cuci cucian piring.', 'titik_kritis' => true],
                ['langkah' => 5, 'nama' => 'Pendinginan dan pengemasan', 'deskripsi' => 'Dinginkan, kemudian kemasi dalam kemasan bersih dengan label komposisi lengkap.', 'titik_kritis' => false],
            ];
        } elseif (str_contains($lower, 'bakso')) {
            return [
                ['langkah' => 1, 'nama' => 'Persiapan daging', 'deskripsi' => 'Pastikan daging giling BERSERTIFIKAT HALAL MUI. Simpan di freezer hingga dingin.', 'titik_kritis' => true],
                ['langkah' => 2, 'nama' => 'Campurkan adonan', 'deskripsi' => 'Campurkan daging, tepung tapioka, bawang putih goreng halus, merica, garam. Uleni hingga kalis.', 'titik_kritis' => false],
                ['langkah' => 3, 'nama' => 'Cetak bakso', 'deskripsi' => 'Cetak bulat-bulat dengan tangan yang bersih. Gunakan sarung tangan halal.', 'titik_kritis' => true],
                ['langkah' => 4, 'nama' => 'Perebusan', 'deskripsi' => 'Masukkan ke air mendidih. Angkat saat bakso mengapung.', 'titik_kritis' => true],
                ['langkah' => 5, 'nama' => 'Penyajian', 'deskripsi' => 'Sajikan dengan kuah dan pelengkap. Pastikan mangkuk PIRING BERSIH dari residu sabun.', 'titik_kritis' => true],
            ];
        } elseif (str_contains($lower, 'nasi') || str_contains($lower, 'mie')) {
            return [
                ['langkah' => 1, 'nama' => 'Persiapan bahan', 'deskripsi' => 'Siapkan nasi/mie dan bumbu. Pastikan semua bahan halal.', 'titik_kritis' => true],
                ['langkah' => 2, 'nama' => 'Pengolahan protein', 'deskripsi' => 'Masak daging/ayam hingga matang sempurna. Pastikan daging BERSERTIFIKAT HALAL MUI.', 'titik_kritis' => true],
                ['langkah' => 3, 'nama' => 'Tumis bumbu', 'deskripsi' => 'Tumis bumbu dengan MINYAK GORENG HALAL hingga harum.', 'titik_kritis' => true],
                ['langkah' => 4, 'nama' => 'Pencampuran', 'deskripsi' => 'Masukkan nasi/mie, aduk rata dengan bumbu. Tambahkan kecap halal.', 'titik_kritis' => false],
                ['langkah' => 5, 'nama' => 'Penyajian', 'deskripsi' => 'Sajikan di piring BERSIH. Pastikan bebas residu sabun.', 'titik_kritis' => true],
            ];
        } elseif (str_contains($lower, 'soto')) {
            return [
                ['langkah' => 1, 'nama' => 'Rebus protein', 'deskripsi' => 'Rebus ayam/sapi. Buang air pertama, ganti dengan air baru.', 'titik_kritis' => true],
                ['langkah' => 2, 'nama' => 'Buat bumbu', 'deskripsi' => 'Haluskan kunyit, jahe, bawang. Tumis dengan minyak halal.', 'titik_kritis' => false],
                ['langkah' => 3, 'nama' => 'Masak kuah', 'deskripsi' => 'Masukkan bumbu ke rebusan. Tambahkan SANTAN HALAL.', 'titik_kritis' => true],
                ['langkah' => 4, 'nama' => 'Saring dan seasoning', 'deskripsi' => 'Saring kuah. Koreksi rasa dengan garam halal.', 'titik_kritis' => false],
                ['langkah' => 5, 'nama' => 'Penyajian', 'deskripsi' => 'Susun nasi/bihun, sayuran, suwir ayam di mangkok BERSIH. Tuangkan kuah.', 'titik_kritis' => true],
            ];
        } elseif (str_contains($lower, 'sate')) {
            return [
                ['langkah' => 1, 'nama' => 'Persiapan daging', 'deskripsi' => 'Pastikan daging DISEMBELIH HALAL sesuai syariat Islam.', 'titik_kritis' => true],
                ['langkah' => 2, 'nama' => 'Marinasi', 'deskripsi' => 'Lumuri daging dengan kecap halal dan bumbu. Diamkan.', 'titik_kritis' => false],
                ['langkah' => 3, 'nama' => 'Tusuk sate', 'deskripsi' => 'Tusuk daging ke tusuk bambu BERSIH.', 'titik_kritis' => false],
                ['langkah' => 4, 'nama' => 'Panggang', 'deskripsi' => 'Bakar di atas arang halal. Bolak-balik hingga matang.', 'titik_kritis' => true],
                ['langkah' => 5, 'nama' => 'Sajikan', 'deskripsi' => 'Sajikan dengan bumbu kacang halal, bawang merah, jeruk nipis.', 'titik_kritis' => false],
            ];
        } else {
            // Generic template
            return [
                ['langkah' => 1, 'nama' => 'Persiapan bahan', 'deskripsi' => 'Siapkan semua bahan. Pastikan bahan utama bersertifikat halal MUI.', 'titik_kritis' => true],
                ['langkah' => 2, 'nama' => 'Pengolahan', 'deskripsi' => 'Masak/proses sesuai resep dengan PERALATAN BERSIH.', 'titik_kritis' => true],
                ['langkah' => 3, 'nama' => 'Pembersihan akhir', 'deskripsi' => 'Pastikan TIDAK ADA RESIDU SABUN pada peralatan.', 'titik_kritis' => true],
                ['langkah' => 4, 'nama' => 'Pengemasan', 'deskripsi' => 'Kemasi dalam kemasan bersih dengan label halal.', 'titik_kritis' => false],
            ];
        }
    }

    /**
     * Get all analyzed products
     */
    public function getAllAnalyzed(bool $approvedOnly = true): array
    {
        $query = AnalisisProduct::query();

        if ($approvedOnly) {
            $query->where('is_approved', true);
        }

        return $query->orderBy('created_at', 'desc')->get()->toArray();
    }

    /**
     * Search analyzed products
     */
    public function searchAnalyzed(string $keyword): array
    {
        return AnalisisProduct::where('product_name', 'LIKE', '%' . $keyword . '%')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->toArray();
    }

    /**
     * Get pending approvals
     */
    public function getPendingApprovals(): array
    {
        return AnalisisProduct::where('is_approved', false)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Approve an analysis
     */
    public function approve(int $id, int $approverId): bool
    {
        $analisis = AnalisisProduct::find($id);
        if ($analisis === null) {
            return false;
        }

        $analisis->is_approved = true;
        $analisis->approved_at = now();
        $analisis->approved_by = $approverId;

        return $analisis->save();
    }

    /**
     * Get statistics
     */
    public function getStats(): array
    {
        return [
            'total' => AnalisisProduct::count(),
            'approved' => AnalisisProduct::where('is_approved', true)->count(),
            'pending' => AnalisisProduct::where('is_approved', false)->count(),
            'aman' => AnalisisProduct::where('status_halal', 'AMAN')->count(),
            'perlu_verifikasi' => AnalisisProduct::where('status_halal', 'PERLU_VERIFIKASI')->count(),
            'berisiko' => AnalisisProduct::where('status_halal', 'BERISIKO')->count(),
        ];
    }
}
