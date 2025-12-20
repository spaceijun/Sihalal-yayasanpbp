<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\CashflowsKoordinator;
use App\Models\DataLapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashflowKoordinatorController extends Controller
{
    public function index()
    {
        $koordinatorId = Auth::user()->koordinator->id;
        // Relasi: CashflowsKoordinator -> DataLapangan -> Enumerator -> Koordinator
        $cashflows = CashflowsKoordinator::with(['dataLapangan.enumerator.koordinator'])
            ->whereHas('dataLapangan.enumerator', function ($q) use ($koordinatorId) {
                $q->where('koordinator_id', $koordinatorId);
            })
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        // Hitung total pemasukan untuk koordinator yang login
        $totalPemasukan = CashflowsKoordinator::where('tipe', 'PEMASUKAN')
            ->whereHas('dataLapangan.enumerator', function ($q) use ($koordinatorId) {
                $q->where('koordinator_id', $koordinatorId);
            })
            ->sum('nominal');

        // Hitung total pengeluaran untuk koordinator yang login
        $totalPengeluaran = CashflowsKoordinator::where('tipe', 'PENGELUARAN')
            ->whereHas('dataLapangan.enumerator', function ($q) use ($koordinatorId) {
                $q->where('koordinator_id', $koordinatorId);
            })
            ->sum('nominal');

        // Hitung saldo
        $saldo = $totalPemasukan - $totalPengeluaran;

        // Hitung total data lapangan yang sudah dibayar
        $totalDibayar = DataLapangan::whereHas('enumerator', function ($q) use ($koordinatorId) {
            $q->where('koordinator_id', $koordinatorId);
        })
            ->where('status_pembayaran', 'DIBAYAR')
            ->count();

        return view('koordinator.cashflow.index', compact(
            'cashflows',
            'totalPemasukan',
            'totalPengeluaran',
            'saldo',
            'totalDibayar'
        ));
    }
}
