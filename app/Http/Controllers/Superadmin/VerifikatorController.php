<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Verifikator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\VerifikatorRequest;
use App\Models\Cashflow;
use App\Models\DataEntryProgress;
use App\Models\VerifikatorPayment;
use App\Services\Superadmin\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class VerifikatorController extends Controller
{
    protected $notificationService;
    public function __construct()
    {
        $this->notificationService = app()->make(NotificationService::class);
    }
    /**
     * Menampilkan halaman verifikator.
     *
     * Halaman ini menampilkan data verifikator yang belum dibayar.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $verifikators = Verifikator::with('verifikatorPayments')
            ->withCount([
                'dataLapangans as jumlah_belum_dibayar' => fn($q) => $q->whereNull('payment_id'),
            ])
            ->paginate(10);

        // Tambahkan hitungan progress per verifikator
        $progressCounts = DataEntryProgress::where('action', 'created')
            ->where('status', 'DITERIMA')
            ->whereNull('payment_id')
            ->whereIn('verifikator_id', $verifikators->pluck('id'))
            ->selectRaw('verifikator_id, COUNT(*) as jumlah')
            ->groupBy('verifikator_id')
            ->pluck('jumlah', 'verifikator_id');

        // Inject ke masing-masing verifikator sebagai attribute tambahan
        $verifikators->each(function ($v) use ($progressCounts) {
            $v->jumlah_belum_dibayar_progress = $progressCounts->get($v->id, 0);
            $v->total_belum_dibayar = $v->jumlah_belum_dibayar + $v->jumlah_belum_dibayar_progress;
        });

        $i = ($verifikators->currentPage() - 1) * $verifikators->perPage();

        return view('superadmin.verifikator.index', compact('verifikators', 'i'));
    }
    public function bayar(Verifikator $verifikator)
    {
        $payment = null;

        DB::transaction(function () use ($verifikator, &$payment) {

            // Data Lapangan
            $dataBelumDibayar = $verifikator->dataLapangans()->whereNull('payment_id');

            $stats = $dataBelumDibayar
                ->selectRaw('COUNT(*) as jumlah, MIN(created_at) as dari, MAX(created_at) as sampai')
                ->first();

            // Progress Data entry
            $progressBelumDibayar = DataEntryProgress::where('verifikator_id', $verifikator->id)
                ->where('status', 'DITERIMA')
                ->whereNull('payment_id')
                ->where('action', 'created');

            $statsProgress = $progressBelumDibayar
                ->selectRaw('COUNT(*) as jumlah, MIN(actioned_at) as dari, MAX(actioned_at) as sampai')
                ->first();

            // Gabungkan jumlah dari keduanya
            $totalJumlah = ($stats->jumlah ?? 0) + ($statsProgress->jumlah ?? 0);

            if ($totalJumlah === 0) return;

            // Tentukan rentang periode (ambil min & max dari keduanya)
            $allDates = array_filter([
                $stats->dari         ?? null,
                $stats->sampai       ?? null,
                $statsProgress->dari ?? null,
                $statsProgress->sampai ?? null,
            ]);

            $periodeDari   = min($allDates);
            $periodeSampai = max($allDates);

            // satu verifikator, 2 data (data lapangan & data progress)
            $payment = VerifikatorPayment::create([
                'verifikator_id' => $verifikator->id,
                'jumlah_data'    => $totalJumlah,
                'total_nominal'  => $totalJumlah * $verifikator->rate_per_data,
                'periode_dari'   => $periodeDari,
                'periode_sampai' => $periodeSampai,
                'paid_at'        => now(),
            ]);

            // Penanda pembayaran data lapangan
            if (($stats->jumlah ?? 0) > 0) {
                $verifikator->dataLapangans()
                    ->whereNull('payment_id')
                    ->update(['payment_id' => $payment->id]);
            }

            // Penanda pembayaran dataentryprogress
            if (($statsProgress->jumlah ?? 0) > 0) {
                DataEntryProgress::where('verifikator_id', $verifikator->id)
                    ->where('status', 'DITERIMA')
                    ->whereNull('payment_id')
                    ->where('action', 'created')
                    ->update(['payment_id' => $payment->id]);
            }

            // Catat Cashflow
            $keteranganParts = [];

            if (($stats->jumlah ?? 0) > 0) {
                $keteranganParts[] = $stats->jumlah . ' data lapangan';
            }

            if (($statsProgress->jumlah ?? 0) > 0) {
                $keteranganParts[] = $statsProgress->jumlah . ' progress data entry';
            }

            Cashflow::create([
                'data_lapangan_id' => null,
                'tipe'             => 'Pengeluaran',
                'jumlah'           => $payment->total_nominal,
                'keterangan'       => 'Pembayaran verifikator ' . $verifikator->nama_lengkap
                    . ' (' . implode(' + ', $keteranganParts) . ')'
                    . ' @ Rp ' . number_format($verifikator->rate_per_data, 0, ',', '.'),
                'tanggal'          => now()->toDateString(),
            ]);
        });

        // Notifikasi Wa
        if ($payment && $verifikator->telephone) {
            $this->notificationService->sendPembayaranVerifikatorNotification(
                $verifikator->nama_lengkap,
                $verifikator->telephone,
                $payment->jumlah_data,
                $verifikator->rate_per_data,
                $payment->total_nominal
            );
        }

        return redirect()->route('superadmin.verifikators.index')
            ->with('success', 'Pembayaran berhasil diproses.');
    }
    /**
     * Kalkulasi Verifikator
     *
     * @param Request $request
     * @param Verifikator $verifikator
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @description Kalkulasi Verifikator berdasarkan filter (semua, pending, lunas)
     * dan mengembalikan response berupa JSON yang berisi tentang summary
     * dan detail data lapangan dan data entry progress.
     */
    public function kalkulasi(Request $request, Verifikator $verifikator)
    {
        $filter = $request->get('filter', 'semua');

        // â”€â”€ Data Lapangan â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $queryLapangan = $verifikator->dataLapangans()->latest();
        if ($filter === 'pending') $queryLapangan->whereNull('payment_id');
        if ($filter === 'lunas')   $queryLapangan->whereNotNull('payment_id');
        $dataLapangans = $queryLapangan->paginate(15, ['*'], 'page_lapangan');

        $rekap = $verifikator->dataLapangans()
            ->selectRaw("
            DATE_FORMAT(created_at, '%Y-%m') as bulan,
            DATE_FORMAT(created_at, '%M %Y') as bulan_label,
            COUNT(*) as total,
            SUM(CASE WHEN payment_id IS NOT NULL THEN 1 ELSE 0 END) as sudah_dibayar,
            SUM(CASE WHEN payment_id IS NULL THEN 1 ELSE 0 END) as belum_dibayar
        ")
            ->groupBy('bulan', 'bulan_label')
            ->orderByDesc('bulan')
            ->get()
            ->map(fn($r) => [
                ...$r->toArray(),
                'nominal_pending' => $r->belum_dibayar * $verifikator->rate_per_data,
            ]);

        // â”€â”€ Data Entry Progress â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $queryProgress = \App\Models\DataEntryProgress::with(['dataLapangan', 'dataEntry.user'])
            ->where('verifikator_id', $verifikator->id)
            ->where('action', 'created')
            ->where('status', 'DITERIMA')
            ->latest('tanggal_verifikasi');

        if ($filter === 'pending') $queryProgress->whereNull('payment_id');
        if ($filter === 'lunas')   $queryProgress->whereNotNull('payment_id');

        $dataProgress = $queryProgress->paginate(15, ['*'], 'page_progress');

        $rekapProgress = \App\Models\DataEntryProgress::where('verifikator_id', $verifikator->id)
            ->where('action', 'created')
            ->where('status', 'DITERIMA')
            ->selectRaw("
            DATE_FORMAT(tanggal_verifikasi, '%Y-%m') as bulan,
            DATE_FORMAT(tanggal_verifikasi, '%M %Y') as bulan_label,
            COUNT(*) as total,
            SUM(CASE WHEN payment_id IS NOT NULL THEN 1 ELSE 0 END) as sudah_dibayar,
            SUM(CASE WHEN payment_id IS NULL THEN 1 ELSE 0 END) as belum_dibayar
        ")
            ->groupBy('bulan', 'bulan_label')
            ->orderByDesc('bulan')
            ->get()
            ->map(fn($r) => [
                ...$r->toArray(),
                'nominal_pending' => $r->belum_dibayar * $verifikator->rate_per_data,
            ]);

        $belumDibayarLapangan = $verifikator->dataLapangans()->whereNull('payment_id')->count();
        $belumDibayarProgress = \App\Models\DataEntryProgress::where('verifikator_id', $verifikator->id)
            ->where('action', 'created')
            ->where('status', 'DITERIMA')
            ->whereNull('payment_id')
            ->count();

        return response()->json([
            'summary' => [
                'rate_per_data'          => $verifikator->rate_per_data,
                'total_data'             => $verifikator->dataLapangans()->count(),
                'belum_dibayar_lapangan' => $belumDibayarLapangan,
                'belum_dibayar_progress' => $belumDibayarProgress,
                'belum_dibayar'          => $belumDibayarLapangan + $belumDibayarProgress,
                'total_nominal'          => ($belumDibayarLapangan + $belumDibayarProgress) * $verifikator->rate_per_data,
            ],
            'rekap'         => $rekap,
            'dataLapangans' => $dataLapangans,
            'rekapProgress' => $rekapProgress,
            'dataProgress'  => $dataProgress,
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $verifikator = new Verifikator();

        return view('superadmin.verifikator.create', compact('verifikator'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VerifikatorRequest $request): RedirectResponse
    {
        Verifikator::create($request->validated());

        return Redirect::route('superadmin.verifikators.index')
            ->with('success', 'Verifikator created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($hashedId): View
    {
        $verifikator = Verifikator::findByHashedIdOrFail($hashedId);
        return view('superadmin.verifikator.show', compact('verifikator'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($hashedId): View
    {
        $verifikator = Verifikator::findByHashedIdOrFail($hashedId);
        return view('superadmin.verifikator.edit', compact('verifikator'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VerifikatorRequest $request, Verifikator $verifikator): RedirectResponse
    {
        $verifikator->update($request->validated());

        return Redirect::route('superadmin.verifikators.index')
            ->with('success', 'Verifikator updated successfully');
    }

    public function destroy($hashedId): RedirectResponse
    {
        Verifikator::findByHashedIdOrFail($hashedId)->delete();

        return Redirect::route('superadmin.verifikators.index')
            ->with('success', 'Verifikator deleted successfully');
    }
}
