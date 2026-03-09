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
        $enumerator = Enumerator::with('koordinator')
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
        // Pastikan user belum memiliki data enumerator
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
            'foto-diri'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'no_registrasi'  => 'required|string|unique:enumerators,no_registrasi',
            'alamat'         => 'required|string',
            'status'         => 'required|in:Aktif,Tidak Aktif',
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

        // Handle upload foto-diri
        if ($request->hasFile('foto-diri')) {
            $data['foto-diri'] = $request->file('foto-diri')
                ->store('enumerators/foto', 'public');
        }

        $enumerator = Enumerator::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Data enumerator berhasil dibuat.',
            'data'    => $enumerator->load('koordinator'),
        ], 201);
    }

    /**
     * Display a specific enumerator — only if it belongs to the authenticated user.
     */
    public function show(int $id): JsonResponse
    {
        $enumerator = Enumerator::with('koordinator')
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
            'foto-diri'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'no_registrasi'  => 'sometimes|string|unique:enumerators,no_registrasi,' . $enumerator->id,
            'alamat'         => 'sometimes|string',
            'status'         => 'sometimes|in:Aktif,Tidak Aktif',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Handle upload foto-diri baru & hapus yang lama
        if ($request->hasFile('foto-diri')) {
            if ($enumerator->foto_diri) {
                Storage::disk('public')->delete($enumerator->foto_diri);
            }
            $data['foto-diri'] = $request->file('foto-diri')
                ->store('enumerators/foto', 'public');
        }

        $enumerator->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data enumerator berhasil diperbarui.',
            'data'    => $enumerator->fresh()->load('koordinator'),
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

        // Hapus foto jika ada
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
