<?php

namespace App\Http\Controllers\DataEntry;

use App\Http\Controllers\Controller;
use App\Models\DataEntry;
use App\Models\DataBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        return view('data-entry.manajemen-akun.index', compact('dataEntry', 'banks'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'data_entry') {
            abort(403, 'Unauthorized');
        }
        $request->validate([
            'bank_id'       => 'nullable|exists:data_banks,id',
            'no_rekening'   => 'nullable|string|max:50',
            'nama_rekening' => 'nullable|string|max:100',
        ]);
        $dataEntry = DataEntry::where('user_id', $user->id)->firstOrFail();
        $dataEntry->update([
            'bank_id'       => $request->bank_id,
            'no_rekening'   => $request->no_rekening,
            'nama_rekening' => $request->nama_rekening,
        ]);
        return redirect()->back()->with('success', 'Data rekening berhasil diperbarui.');
    }
}
