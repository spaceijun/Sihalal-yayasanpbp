<?php

namespace App\Http\Controllers\DataEntry;

use App\Http\Controllers\Controller;
use App\Models\DataEntry;
use App\Models\DataBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingAkunController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role !== 'data_entry') {
            abort(403, 'Unauthorized');
        }
        $dataEntry = DataEntry::with(['user', 'bank'])->where('user_id', $user->id)->first();
        $banks = DataBank::orderBy('name')->get();
        $pendidikanOptions = [
            'Tamat SD',
            'Tamat SMP/MTS',
            'Tamat SMA/SMK/MA',
            'Tamat D1/D2',
            'Tamat D3/S1',
            'Tamat S2',
            'Tamat S3',
        ];
        return view('data-entry.manajemen-akun.index', compact('dataEntry', 'banks', 'pendidikanOptions'));
    }

    public function update(Request $request)
    {;
        $user = Auth::user();
        if ($user->role !== 'data_entry') {
            abort(403, 'Unauthorized');
        }
        $request->validate([
            'bank_id'            => 'nullable|exists:data_banks,id',
            'no_rekening'        => 'nullable|string|max:50',
            'nama_rekening'      => 'nullable|string|max:100',
        ]);
        $dataEntry = DataEntry::where('user_id', $user->id)->firstOrFail();
        $dataEntry->update([
            'bank_id'       => $request->bank_id,
            'no_rekening'   => $request->no_rekening,
            'nama_rekening' => $request->nama_rekening,
        ]);
        return redirect()->back()->with('success', 'Data rekening berhasil diperbarui.');
    }

    public function updateKtp(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'data_entry') {
            abort(403, 'Unauthorized');
        }
        $request->validate([
            'nik'               => 'required|string|digits:16',
            'nama_lengkap_ktp'  => 'required|string|max:255',
            'foto_ktp'          => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'pendidikan_terakhir' => 'required|in:Tamat SD,Tamat SMP/MTS,Tamat SMA/SMK/MA,Tamat D1/D2,Tamat D3/S1,Tamat S2,Tamat S3',
            'foto_ijasah'       => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $dataEntry = DataEntry::where('user_id', $user->id)->firstOrFail();

        $data = [
            'nik'                => $request->nik,
            'nama_lengkap_ktp'   => $request->nama_lengkap_ktp,
            'pendidikan_terakhir' => $request->pendidikan_terakhir,
        ];

        if ($request->hasFile('foto_ktp')) {
            // Hapus foto lama
            if ($dataEntry->foto_ktp) {
                Storage::disk('public')->delete($dataEntry->foto_ktp);
            }
            $data['foto_ktp'] = $request->file('foto_ktp')->store('data-entry/ktp', 'public');
        }

        if ($request->hasFile('foto_ijasah')) {
            // Hapus foto lama
            if ($dataEntry->foto_ijasah) {
                Storage::disk('public')->delete($dataEntry->foto_ijasah);
            }
            $data['foto_ijasah'] = $request->file('foto_ijasah')->store('data-entry/ijasah', 'public');
        }

        $dataEntry->update($data);

        return redirect()->back()->with('success', 'Data KTP & pendidikan berhasil diperbarui.');
    }
}
