<?php

namespace App\Http\Controllers\Api\Enumerator;

use App\Http\Controllers\Controller;
use App\Models\Enumerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EnumeratorController extends Controller
{
    /**
     * Display the enumerator profile of the currently authenticated user.
     */
    public function index(): JsonResponse
    {
        $enumerator = Enumerator::with(['koordinator', 'bank'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$enumerator) {
            return response()->json([
                'success' => false,
                'message' => 'Data enumerator tidak ditemukan untuk user ini.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data enumerator berhasil diambil.',
            'data'    => $enumerator,
        ]);
    }

    /**
     * Store a new enumerator linked to the authenticated user.
     */
    public function store(Request $request): JsonResponse
    {
        $existing = Enumerator::where('user_id', Auth::id())->first();
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'User ini sudah memiliki data enumerator.',
            ], 409);
        }

        $validator = Validator::make($request->all(), [
            'koordinator_id' => 'required|exists:koordinators,id',
            'nama_lengkap'   => 'required|string|max:255',
            'telephone'      => 'required|string|max:20',
            'foto_diri'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'no_registrasi'  => 'required|string|unique:enumerators,no_registrasi',
            'alamat'         => 'required|string',
            'status'         => 'required|in:Aktif,Tidak Aktif',
            'bank_id'        => 'nullable|exists:data_banks,id',
            'no_rekening'    => 'nullable|string|max:50',
            'nama_rekening'  => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = Auth::id();

        // Handle upload foto_diri
        if ($request->hasFile('foto_diri')) {
            $data['foto_diri'] = $request->file('foto_diri')
                ->store('foto-diri', 'public');
        }

        $enumerator = Enumerator::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Data enumerator berhasil dibuat.',
            'data'    => $enumerator->load(['koordinator', 'bank']),
        ], 201);
    }

    /**
     * Display a specific enumerator — only if it belongs to the authenticated user.
     */
    public function show(int $id): JsonResponse
    {
        $enumerator = Enumerator::with(['koordinator', 'bank'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$enumerator) {
            return response()->json([
                'success' => false,
                'message' => 'Data enumerator tidak ditemukan atau akses ditolak.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail enumerator berhasil diambil.',
            'data'    => $enumerator,
        ]);
    }

    /**
     * Update the enumerator of the authenticated user.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $enumerator = Enumerator::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$enumerator) {
            return response()->json([
                'success' => false,
                'message' => 'Data enumerator tidak ditemukan atau akses ditolak.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'koordinator_id' => 'sometimes|exists:koordinators,id',
            'nama_lengkap'   => 'sometimes|string|max:255',
            'telephone'      => 'sometimes|string|max:20',
            'foto_diri'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'no_registrasi'  => 'sometimes|string|unique:enumerators,no_registrasi,' . $enumerator->id,
            'alamat'         => 'sometimes|string',
            'status'         => 'sometimes|in:Aktif,Tidak Aktif',
            'bank_id'        => 'nullable|exists:data_banks,id',
            'no_rekening'    => 'nullable|string|max:50',
            'nama_rekening'  => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Handle upload foto_diri baru & hapus yang lama
        if ($request->hasFile('foto_diri')) {
            if ($enumerator->foto_diri) {
                Storage::disk('public')->delete($enumerator->foto_diri);
            }
            $data['foto_diri'] = $request->file('foto_diri')
                ->store('foto-diri', 'public');
        }

        $enumerator->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data enumerator berhasil diperbarui.',
            'data'    => $enumerator->fresh()->load(['koordinator', 'bank']),
        ]);
    }

    /**
     * Delete the enumerator of the authenticated user.
     */
    public function destroy(int $id): JsonResponse
    {
        $enumerator = Enumerator::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$enumerator) {
            return response()->json([
                'success' => false,
                'message' => 'Data enumerator tidak ditemukan atau akses ditolak.',
            ], 404);
        }

        if ($enumerator->foto_diri) {
            Storage::disk('public')->delete($enumerator->foto_diri);
        }

        $enumerator->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data enumerator berhasil dihapus.',
        ]);
    }
}
