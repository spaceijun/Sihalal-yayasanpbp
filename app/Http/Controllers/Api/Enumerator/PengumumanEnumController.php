<?php

namespace App\Http\Controllers\Api\Enumerator;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PengumumanEnumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $data = Pengumuman::where('jenis', 'PENDAMPING')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data pengumuman pendamping berhasil diambil',
            'data'    => $data,
        ]);
    }

    public function show($id): JsonResponse
    {
        $pengumuman = Pengumuman::where('jenis', 'PENDAMPING')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail pengumuman berhasil diambil',
            'data'    => $pengumuman,
        ]);
    }
}
