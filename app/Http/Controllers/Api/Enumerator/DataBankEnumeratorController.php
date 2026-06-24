<?php

namespace App\Http\Controllers\Api\Enumerator;

use App\Http\Controllers\Controller;
use App\Models\DataBank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DataBankEnumeratorController extends Controller
{
    /**
     * Display a listing of all available banks.
     */
    public function index(Request $request): JsonResponse
    {
        $query = DataBank::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $banks = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'message' => 'Data bank berhasil diambil.',
            'data'    => $banks,
        ]);
    }

    /**
     * Display a specific bank by ID.
     */
    public function show(int $id): JsonResponse
    {
        $bank = DataBank::find($id);

        if (!$bank) {
            return response()->json([
                'success' => false,
                'message' => 'Data bank tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail data bank berhasil diambil.',
            'data'    => $bank,
        ]);
    }
}
