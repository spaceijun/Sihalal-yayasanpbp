<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Verifikator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\VerifikatorRequest;
use App\Models\Cashflow;
use App\Models\VerifikatorPayment;
use App\Traits\SendsWhatsAppNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class VerifikatorController extends Controller
{
    use SendsWhatsAppNotification;
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
                'dataLapangans as jumlah_belum_dibayar' => fn($q) => $q->whereNull('payment_id')
            ])
            ->paginate(10);

        $i = ($verifikators->currentPage() - 1) * $verifikators->perPage();

        return view('superadmin.verifikator.index', compact('verifikators', 'i'));
    }
    public function bayar(Verifikator $verifikator)
    {
        $payment = null;

        DB::transaction(function () use ($verifikator, &$payment) {
            $dataBelumDibayar = $verifikator->dataLapangans()->whereNull('payment_id');

            $stats = $dataBelumDibayar
                ->selectRaw('COUNT(*) as jumlah, MIN(created_at) as dari, MAX(created_at) as sampai')
                ->first();

            if (!$stats || $stats->jumlah === 0) return;

            $payment = VerifikatorPayment::create([
                'verifikator_id' => $verifikator->id,
                'jumlah_data'    => $stats->jumlah,
                'total_nominal'  => $stats->jumlah * $verifikator->rate_per_data,
                'periode_dari'   => $stats->dari,
                'periode_sampai' => $stats->sampai,
                'paid_at'        => now(),
            ]);

            $verifikator->dataLapangans()
                ->whereNull('payment_id')
                ->update(['payment_id' => $payment->id]);

            Cashflow::create([
                'data_lapangan_id' => null,
                'tipe'             => 'Pengeluaran',
                'jumlah'           => $payment->total_nominal,
                'keterangan'       => 'Pembayaran verifikator ' . $verifikator->nama_lengkap
                    . ' sebanyak ' . $stats->jumlah . ' data'
                    . ' @ Rp ' . number_format($verifikator->rate_per_data, 0, ',', '.'),
                'tanggal'          => now()->toDateString(),
            ]);
        });

        // Kirim notifikasi WA setelah transaksi berhasil
        if ($payment && $verifikator->telephone) {
            $this->sendPembayaranVerifikatorNotification(
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
    public function kalkulasi(Request $request, Verifikator $verifikator)
    {
        $filter = $request->get('filter', 'semua');

        // Query data lapangan
        $query = $verifikator->dataLapangans()->latest();
        if ($filter === 'pending') $query->whereNull('payment_id');
        if ($filter === 'lunas')   $query->whereNotNull('payment_id');

        $dataLapangans = $query->paginate(15);

        // Rekap per bulan
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

        $belumDibayar = $verifikator->dataLapangans()->whereNull('payment_id')->count();

        return response()->json([
            'summary' => [
                'rate_per_data' => $verifikator->rate_per_data,
                'total_data'    => $verifikator->dataLapangans()->count(),
                'belum_dibayar' => $belumDibayar,
                'total_nominal' => $belumDibayar * $verifikator->rate_per_data,
            ],
            'rekap'         => $rekap,
            'dataLapangans' => $dataLapangans,
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
    public function show($id): View
    {
        $verifikator = Verifikator::find($id);

        return view('superadmin.verifikator.show', compact('verifikator'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $verifikator = Verifikator::find($id);

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

    public function destroy($id): RedirectResponse
    {
        Verifikator::find($id)->delete();

        return Redirect::route('superadmin.verifikators.index')
            ->with('success', 'Verifikator deleted successfully');
    }
}
