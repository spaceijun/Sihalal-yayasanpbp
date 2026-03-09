<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CashflowsKoordinator;
use App\Models\Enumerator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CashflowEnumeratorController extends Controller
{
    /**
     * Ambil enumerator berdasarkan user yang sedang login.
     */
    private function getEnumerator(): Enumerator|JsonResponse
    {
        $enumerator = Enumerator::where('user_id', Auth::id())->first();

        if (!$enumerator) {
            return response()->json([
                'status'  => false,
                'message' => 'Anda tidak terdaftar sebagai enumerator.',
            ], 403);
        }

        return $enumerator;
    }

    /**
     * Base query: hanya PEMASUKAN milik enumerator yang login.
     */
    private function baseQuery(int $enumeratorId)
    {
        return CashflowsKoordinator::where('tipe', 'PEMASUKAN')
            ->whereHas('dataLapangan', function ($q) use ($enumeratorId) {
                $q->where('enumerator_id', $enumeratorId);
            });
    }

    // -------------------------------------------------------------------------

    /**
     * GET /enumerator/cashflow
     * List semua pemasukan milik enumerator yang sedang login.
     */
    public function index(Request $request): JsonResponse
    {
        $enumerator = $this->getEnumerator();
        if ($enumerator instanceof JsonResponse) return $enumerator;

        $query = $this->baseQuery($enumerator->id)->with('dataLapangan');

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_akhir);
        }
        if ($request->filled('data_lapangan_id')) {
            $query->where('data_lapangan_id', $request->data_lapangan_id);
        }

        $cashflows = $query->orderBy('tanggal', 'desc')->paginate($request->get('per_page', 15));

        return response()->json([
            'status'        => true,
            'message'       => 'Data pemasukan berhasil diambil.',
            'total_pemasukan' => (float) $this->baseQuery($enumerator->id)->sum('nominal'),
            'data'          => $cashflows,
        ]);
    }

    /**
     * GET /enumerator/cashflow/{id}
     * Detail satu pemasukan milik enumerator yang login.
     */
    public function show(int $id): JsonResponse
    {
        $enumerator = $this->getEnumerator();
        if ($enumerator instanceof JsonResponse) return $enumerator;

        $cashflow = $this->baseQuery($enumerator->id)->with('dataLapangan')->find($id);

        if (!$cashflow) {
            return response()->json([
                'status'  => false,
                'message' => 'Data pemasukan tidak ditemukan atau bukan milik Anda.',
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Detail pemasukan berhasil diambil.',
            'data'    => $cashflow,
        ]);
    }
}
