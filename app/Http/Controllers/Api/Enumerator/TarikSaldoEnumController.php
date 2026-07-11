<?php

namespace App\Http\Controllers\Api\Enumerator;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * TarikSaldoEnumController
 *
 * Endpoint Flutter untuk enumerator mengajukan penarikan saldo (tarik saldo)
 * secara mandiri per data lapangan yang sudah berstatus TERBIT SH.
 *
 * Alur:
 *   1. Enumerator lihat daftar data lapangan TERBIT SH yang bisa ditarik (GET /tarik-saldo/eligible)
 *   2. Enumerator ajukan penarikan (POST /tarik-saldo/{id}) → status_pembayaran = PENGAJUAN
 *   3. Superadmin approve/tolak via web panel
 *
 * Batasan:
 *   - Enumerator harus berstatus AKTIF
 *   - Pengajuan hanya bisa dilakukan sekali dalam 7 hari (cooldown)
 */
class TarikSaldoEnumController extends Controller
{
    /**
     * GET /api/enumerator/tarik-saldo/eligible
     *
     * Daftar data lapangan milik enumerator yang:
     *   - Status = TERBIT SH
     *   - Status pembayaran = TIDAK ADA PENGAJUAN
     * (Artinya bisa diajukan untuk penarikan)
     *
     * Response juga menyertakan info cooldown agar Flutter bisa
     * menampilkan kapan enumerator bisa ajukan lagi.
     */
    public function eligible(Request $request): JsonResponse
    {
        try {
            $enumerator = Auth::user()->enumerator;

            if (! $enumerator) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data enumerator tidak ditemukan.',
                ], 404);
            }

            $cutoff = Carbon::create(2026, 5, 1);

            $data = DataLapangan::where('enumerator_id', $enumerator->id)
                ->where('status', 'TERBIT SH')
                ->where('status_pembayaran', 'TIDAK ADA PENGAJUAN')
                ->latest()
                ->get()
                ->map(fn ($dl) => $this->formatItem($dl, $cutoff));

            $cooldown = $this->buildCooldownInfo($enumerator);

            return response()->json([
                'status'   => true,
                'message'  => 'Daftar data lapangan yang bisa diajukan penarikan',
                'total'    => $data->count(),
                'cooldown' => $cooldown,
                'data'     => $data,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat mengambil data.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/enumerator/tarik-saldo/riwayat
     *
     * Riwayat pengajuan penarikan milik enumerator:
     *   - PENGAJUAN (menunggu superadmin)
     *   - DIBAYAR
     *   - DITOLAK
     */
    public function riwayat(Request $request): JsonResponse
    {
        try {
            $enumerator = Auth::user()->enumerator;

            if (! $enumerator) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data enumerator tidak ditemukan.',
                ], 404);
            }

            $cutoff = Carbon::create(2026, 5, 1);

            $data = DataLapangan::where('enumerator_id', $enumerator->id)
                ->where('status', 'TERBIT SH')
                ->whereIn('status_pembayaran', ['PENGAJUAN', 'DIBAYAR', 'DITOLAK'])
                ->latest('updated_at')
                ->get()
                ->map(fn ($dl) => $this->formatItem($dl, $cutoff));

            return response()->json([
                'status'  => true,
                'message' => 'Riwayat pengajuan penarikan saldo',
                'total'   => $data->count(),
                'data'    => $data,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat mengambil riwayat.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/enumerator/tarik-saldo/{id}
     *
     * Enumerator mengajukan tarik saldo untuk satu data lapangan.
     * Body (opsional):
     *   - catatan : string (max:500) — catatan dari enumerator
     *
     * Validasi:
     *   - Enumerator harus berstatus Aktif
     *   - Cooldown: hanya bisa ajukan 1x dalam 7 hari
     *   - Data lapangan harus milik enumerator yang login
     *   - Status harus TERBIT SH
     *   - status_pembayaran harus TIDAK ADA PENGAJUAN
     */
    public function ajukan(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'catatan' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $enumerator = Auth::user()->enumerator;

            if (! $enumerator) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data enumerator tidak ditemukan.',
                ], 404);
            }

            // ── Guard 1: Enumerator harus berstatus Aktif ──────────────────────────
            if ($enumerator->status !== 'Aktif') {
                return response()->json([
                    'status'  => false,
                    'message' => 'Akun Anda saat ini berstatus Tidak Aktif. Pengajuan penarikan saldo tidak dapat dilakukan. Hubungi admin untuk informasi lebih lanjut.',
                    'kode'    => 'ENUMERATOR_TIDAK_AKTIF',
                ], 403);
            }

            // ── Guard 2: Cooldown 7 hari sejak pengajuan terakhir ─────────────────
            if (! $enumerator->bisaAjukan()) {
                $nextDate = $enumerator->last_pengajuan_at->addDays(7);

                return response()->json([
                    'status'            => false,
                    'message'           => 'Pengajuan penarikan hanya bisa dilakukan sekali dalam 7 hari. Anda melakukan pengajuan terakhir pada '
                        . $enumerator->last_pengajuan_at->translatedFormat('d M Y')
                        . '. Pengajuan berikutnya dapat dilakukan mulai '
                        . $nextDate->translatedFormat('d M Y') . '.',
                    'kode'              => 'COOLDOWN_AKTIF',
                    'last_pengajuan_at' => $enumerator->last_pengajuan_at->toDateTimeString(),
                    'next_pengajuan_at' => $nextDate->toDateTimeString(),
                    'sisa_hari'         => $enumerator->sisaHariCooldown(),
                ], 429);
            }

            // ── Cari data lapangan ────────────────────────────────────────────────
            $dataLapangan = DataLapangan::where('id', $id)
                ->where('enumerator_id', $enumerator->id)
                ->first();

            if (! $dataLapangan) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data lapangan tidak ditemukan.',
                ], 404);
            }

            // Guard: harus sudah TERBIT SH
            if ($dataLapangan->status !== 'TERBIT SH') {
                return response()->json([
                    'status'  => false,
                    'message' => 'Pengajuan penarikan hanya bisa dilakukan untuk data yang sudah berstatus TERBIT SH.',
                    'data'    => [
                        'status_saat_ini' => $dataLapangan->status,
                    ],
                ], 422);
            }

            // Guard: hanya bisa diajukan jika TIDAK ADA PENGAJUAN
            if ($dataLapangan->status_pembayaran !== 'TIDAK ADA PENGAJUAN') {
                $pesanStatus = match ($dataLapangan->status_pembayaran) {
                    'PENGAJUAN' => 'Pengajuan penarikan sudah dikirim dan sedang menunggu persetujuan superadmin.',
                    'DIBAYAR'   => 'Pembayaran untuk data ini sudah selesai diproses.',
                    'DITOLAK'   => 'Pengajuan penarikan sebelumnya ditolak. Hubungi admin untuk informasi lebih lanjut.',
                    default     => 'Status pembayaran tidak valid untuk pengajuan baru.',
                };

                return response()->json([
                    'status'               => false,
                    'message'              => $pesanStatus,
                    'status_pembayaran'    => $dataLapangan->status_pembayaran,
                    'keterangan_penolakan' => $dataLapangan->keterangan_pembayaran,
                ], 422);
            }

            // ── Ajukan penarikan ──────────────────────────────────────────────────
            $dataLapangan->update([
                'status_pembayaran'     => 'PENGAJUAN',
                'keterangan_pembayaran' => $request->catatan ?? null,
            ]);

            // Catat waktu pengajuan untuk cooldown (1x per 7 hari)
            $enumerator->update(['last_pengajuan_at' => now()]);

            $cutoff  = Carbon::create(2026, 5, 1);
            $nominal = Carbon::parse($dataLapangan->created_at)->lt($cutoff) ? 50000 : 60000;

            return response()->json([
                'status'  => true,
                'message' => 'Pengajuan penarikan saldo berhasil dikirim. Menunggu persetujuan superadmin.',
                'data'    => [
                    'id'                 => $dataLapangan->id,
                    'no_registrasi'      => $dataLapangan->no_registrasi,
                    'nama_pu'            => $dataLapangan->nama_pu,
                    'status_pembayaran'  => $dataLapangan->status_pembayaran,
                    'nominal'            => $nominal,
                    'nominal_fmt'        => 'Rp ' . number_format($nominal, 0, ',', '.'),
                    'next_pengajuan_at'  => now()->addDays(7)->toDateTimeString(),
                    'sisa_hari_cooldown' => 7,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat mengajukan penarikan.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/enumerator/tarik-saldo/ajukan-semua
     *
     * Enumerator mengajukan SEMUA data lapangan yang eligible sekaligus.
     * Body (opsional):
     *   - catatan : string (max:500) — catatan umum dari enumerator
     *
     * Validasi sama dengan ajukan() tunggal:
     *   - Enumerator harus berstatus Aktif
     *   - Cooldown: hanya bisa ajukan 1x dalam 7 hari
     */
    public function ajukanSemua(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'catatan' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $enumerator = Auth::user()->enumerator;

            if (! $enumerator) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data enumerator tidak ditemukan.',
                ], 404);
            }

            // ── Guard 1: Enumerator harus berstatus Aktif ──────────────────────
            if ($enumerator->status !== 'Aktif') {
                return response()->json([
                    'status'  => false,
                    'message' => 'Akun Anda saat ini berstatus Tidak Aktif. Pengajuan penarikan saldo tidak dapat dilakukan. Hubungi admin untuk informasi lebih lanjut.',
                    'kode'    => 'ENUMERATOR_TIDAK_AKTIF',
                ], 403);
            }

            // ── Guard 2: Cooldown 7 hari ────────────────────────────────────────
            if (! $enumerator->bisaAjukan()) {
                $nextDate = $enumerator->last_pengajuan_at->addDays(7);

                return response()->json([
                    'status'            => false,
                    'message'           => 'Pengajuan penarikan hanya bisa dilakukan sekali dalam 7 hari. Anda melakukan pengajuan terakhir pada '
                        . $enumerator->last_pengajuan_at->translatedFormat('d M Y')
                        . '. Pengajuan berikutnya dapat dilakukan mulai '
                        . $nextDate->translatedFormat('d M Y') . '.',
                    'kode'              => 'COOLDOWN_AKTIF',
                    'last_pengajuan_at' => $enumerator->last_pengajuan_at->toDateTimeString(),
                    'next_pengajuan_at' => $nextDate->toDateTimeString(),
                    'sisa_hari'         => $enumerator->sisaHariCooldown(),
                ], 429);
            }

            // ── Ambil semua data yang eligible ─────────────────────────────────
            $cutoff   = Carbon::create(2026, 5, 1);
            $eligibles = DataLapangan::where('enumerator_id', $enumerator->id)
                ->where('status', 'TERBIT SH')
                ->where('status_pembayaran', 'TIDAK ADA PENGAJUAN')
                ->get();

            if ($eligibles->isEmpty()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Tidak ada data lapangan yang bisa diajukan saat ini. Semua data sudah diajukan atau belum berstatus TERBIT SH.',
                    'kode'    => 'TIDAK_ADA_ELIGIBLE',
                ], 422);
            }

            // ── Bulk update ke PENGAJUAN ────────────────────────────────────────
            $diajukan = [];
            $totalNominal = 0;

            foreach ($eligibles as $dl) {
                $dl->update([
                    'status_pembayaran'     => 'PENGAJUAN',
                    'keterangan_pembayaran' => $request->catatan ?? null,
                ]);

                $nominal       = Carbon::parse($dl->created_at)->lt($cutoff) ? 50000 : 60000;
                $totalNominal += $nominal;

                $diajukan[] = [
                    'id'            => $dl->id,
                    'no_registrasi' => $dl->no_registrasi,
                    'nama_pu'       => $dl->nama_pu,
                    'nominal'       => $nominal,
                    'nominal_fmt'   => 'Rp ' . number_format($nominal, 0, ',', '.'),
                ];
            }

            // ── Update cooldown setelah semua berhasil ──────────────────────────
            $enumerator->update(['last_pengajuan_at' => now()]);

            return response()->json([
                'status'             => true,
                'message'            => 'Berhasil mengajukan ' . count($diajukan) . ' data lapangan sekaligus. Menunggu persetujuan superadmin.',
                'total_diajukan'     => count($diajukan),
                'total_nominal'      => $totalNominal,
                'total_nominal_fmt'  => 'Rp ' . number_format($totalNominal, 0, ',', '.'),
                'next_pengajuan_at'  => now()->addDays(7)->toDateTimeString(),
                'sisa_hari_cooldown' => 7,
                'data'               => $diajukan,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat mengajukan penarikan.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/enumerator/tarik-saldo/summary
     *
     * Ringkasan saldo enumerator: jumlah data per status pembayaran + estimasi total.
     * Juga menyertakan info cooldown pengajuan.
     */
    public function summary(): JsonResponse

    {
        try {
            $enumerator = Auth::user()->enumerator;

            if (! $enumerator) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data enumerator tidak ditemukan.',
                ], 404);
            }

            $cutoff = Carbon::create(2026, 5, 1);

            $allTerbitSh = DataLapangan::where('enumerator_id', $enumerator->id)
                ->where('status', 'TERBIT SH')
                ->get();

            $summary = [
                'belum_diajukan' => ['count' => 0, 'total' => 0],
                'menunggu'       => ['count' => 0, 'total' => 0],
                'dibayar'        => ['count' => 0, 'total' => 0],
                'ditolak'        => ['count' => 0, 'total' => 0],
            ];

            foreach ($allTerbitSh as $dl) {
                $nominal = Carbon::parse($dl->created_at)->lt($cutoff) ? 50000 : 60000;

                match ($dl->status_pembayaran) {
                    'TIDAK ADA PENGAJUAN' => ($summary['belum_diajukan']['count']++ && $summary['belum_diajukan']['total'] += $nominal),
                    'PENGAJUAN'           => ($summary['menunggu']['count']++ && $summary['menunggu']['total'] += $nominal),
                    'DIBAYAR'             => ($summary['dibayar']['count']++ && $summary['dibayar']['total'] += $nominal),
                    'DITOLAK'             => ($summary['ditolak']['count']++ && $summary['ditolak']['total'] += $nominal),
                    default               => null,
                };
            }

            // Format nominal
            foreach ($summary as $key => $val) {
                $summary[$key]['total_fmt'] = 'Rp ' . number_format($val['total'], 0, ',', '.');
            }

            return response()->json([
                'status'   => true,
                'message'  => 'Ringkasan saldo enumerator',
                'cooldown' => $this->buildCooldownInfo($enumerator),
                'data'     => $summary,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // ─── Helpers ────────────────────────────────────────────────────────────────

    /**
     * Format satu item DataLapangan ke array response.
     */
    private function formatItem(DataLapangan $dl, Carbon $cutoff): array
    {
        $nominal = Carbon::parse($dl->created_at)->lt($cutoff) ? 50000 : 60000;

        return [
            'id'                   => $dl->id,
            'no_registrasi'        => $dl->no_registrasi,
            'nama_pu'              => $dl->nama_pu,
            'nik'                  => $dl->nik,
            'nama_produk'          => $dl->nama_produk,
            'status'               => $dl->status,
            'status_pembayaran'    => $dl->status_pembayaran,
            'keterangan_penolakan' => $dl->keterangan_pembayaran,
            'nominal'              => $nominal,
            'nominal_fmt'          => 'Rp ' . number_format($nominal, 0, ',', '.'),
            'created_at'           => $dl->created_at,
            'updated_at'           => $dl->updated_at,
        ];
    }

    /**
     * Buat info cooldown untuk response.
     */
    private function buildCooldownInfo($enumerator): array
    {
        return [
            'enumerator_aktif'  => $enumerator->status === 'Aktif',
            'bisa_ajukan'       => $enumerator->bisaAjukan(),
            'sisa_hari'         => $enumerator->sisaHariCooldown(),
            'last_pengajuan_at' => $enumerator->last_pengajuan_at?->toDateTimeString(),
            'next_pengajuan_at' => $enumerator->last_pengajuan_at
                ? $enumerator->last_pengajuan_at->addDays(7)->toDateTimeString()
                : null,
        ];
    }
}
