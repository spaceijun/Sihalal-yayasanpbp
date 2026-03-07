<?php

namespace App\Http\Controllers\Api\Enumerator;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DataLapanganEnumController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nama_pu'           => 'required|string|max:255',
            'nik'               => 'required|string|size:16',
            'telephone'         => 'required|string|max:15',
            'nama_produk'       => 'required|string|max:255',
            'alamat'            => 'required|string',
            'foto_ktp'          => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'foto_rumah'        => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'foto_pendamping'   => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'foto_produk'       => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $enumeratorId = Auth::user()->enumerator->id;

            // Upload foto
            $foto_ktp_path        = $request->file('foto_ktp')->store('foto_ktp', 'public');
            $foto_rumah_path       = $request->file('foto_rumah')->store('foto_rumah', 'public');
            $foto_pendamping_path  = $request->file('foto_pendamping')->store('foto_pendamping', 'public');
            $foto_produk_path      = $request->file('foto_produk')->store('foto_produk', 'public');

            $validatedData = array_merge($validator->validated(), [
                'enumerator_id'       => $enumeratorId,
                'foto_ktp_path'       => $foto_ktp_path,
                'foto_rumah_path'     => $foto_rumah_path,
                'foto_pendamping_path' => $foto_pendamping_path,
                'foto_produk_path'    => $foto_produk_path,
            ]);

            $dataLapangan = $this->create($validatedData);

            return response()->json([
                'status'  => true,
                'message' => 'Data lapangan berhasil disimpan',
                'data'    => [
                    'id'              => $dataLapangan->id,
                    'enumerator_id'   => $dataLapangan->enumerator_id,
                    'nama_pu'         => $dataLapangan->nama_pu,
                    'nik'             => $dataLapangan->nik,
                    'telephone'       => $dataLapangan->telephone,
                    'nama_produk'     => $dataLapangan->nama_produk,
                    'alamat'          => $dataLapangan->alamat,
                    'foto_ktp'        => Storage::url($dataLapangan->foto_ktp),
                    'foto_rumah'      => Storage::url($dataLapangan->foto_rumah),
                    'foto_pendamping' => Storage::url($dataLapangan->foto_pendamping),
                    'foto_produk'     => Storage::url($dataLapangan->foto_produk),
                    'status'          => $dataLapangan->status,
                    'created_at'      => $dataLapangan->created_at,
                ],
            ], 201);
        } catch (\Exception $e) {
            // Rollback foto jika gagal simpan ke DB
            if (isset($foto_ktp_path))        Storage::disk('public')->delete($foto_ktp_path);
            if (isset($foto_rumah_path))       Storage::disk('public')->delete($foto_rumah_path);
            if (isset($foto_pendamping_path))  Storage::disk('public')->delete($foto_pendamping_path);
            if (isset($foto_produk_path))      Storage::disk('public')->delete($foto_produk_path);

            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    private function create(array $validatedData): DataLapangan
    {
        if (isset($validatedData['nama_pu'])) {
            $validatedData['nama_pu'] = strtoupper($validatedData['nama_pu']);
        }

        $dataToSave = [
            'enumerator_id'   => $validatedData['enumerator_id'],
            'nama_pu'         => $validatedData['nama_pu'],
            'nik'             => $validatedData['nik'],
            'telephone'       => $validatedData['telephone'],
            'nama_produk'     => $validatedData['nama_produk'],
            'alamat'          => $validatedData['alamat'],
            'foto_ktp'        => $validatedData['foto_ktp_path'],
            'foto_rumah'      => $validatedData['foto_rumah_path'],
            'foto_pendamping' => $validatedData['foto_pendamping_path'],
            'foto_produk'     => $validatedData['foto_produk_path'],
        ];

        return DataLapangan::create($dataToSave);
    }
}
