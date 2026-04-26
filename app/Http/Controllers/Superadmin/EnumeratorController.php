<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Enumerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\EnumeratorRequest;
use App\Models\DataBank;
use App\Models\Superadmin\Koordinator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EnumeratorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $enumerators = Enumerator::with('koordinator', 'bank')->paginate();

        return view('superadmin.enumerator.index', compact('enumerators'))
            ->with('i', ($request->input('page', 1) - 1) * $enumerators->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $enumerator = new Enumerator();
        $koordinators = Koordinator::all();

        return view('superadmin.enumerator.create', compact('enumerator', 'koordinators'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EnumeratorRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        if ($request->hasFile('foto_diri')) {
            $image     = $request->file('foto_diri');
            $extension = $image->getClientOriginalExtension();
            $imageName = time() . '_' . uniqid() . '.' . $extension;
            $image->storeAs('foto-diri', $imageName, 'public');
            $validatedData['foto_diri'] = 'foto-diri/' . $imageName;
        }

        DB::transaction(function () use ($validatedData) {
            $lastNo = Enumerator::lockForUpdate()
                ->orderBy('no_registrasi', 'desc')
                ->value('no_registrasi');

            $nextNo = $lastNo ? ((int) $lastNo + 1) : 1;

            if ($nextNo > 999) {
                throw new \Exception('No registrasi sudah penuh');
            }

            $noRegistrasi = str_pad($nextNo, 3, '0', STR_PAD_LEFT);

            Enumerator::create(array_merge(
                $validatedData,
                ['no_registrasi' => $noRegistrasi]
            ));
        });

        return Redirect::route('superadmin.enumerators.index')
            ->with('success', 'Enumerator created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($hashedId)
    {
        $enumerator = Enumerator::findByHashedIdOrFail($hashedId);
        $enumerator->load(['koordinator', 'bank']);

        return view('superadmin.enumerator.show', compact('enumerator'));
    }

    /**
     * Tampilkan galeri foto per enumerator (foto_pendamping & foto_produk)
     */
    public function gallery($hashedId): View
    {
        $enumerator = Enumerator::findByHashedIdOrFail($hashedId);
        $enumerator->load(['koordinator', 'dataLapangans']);

        return view('superadmin.enumerator.gallery', compact('enumerator'));
    }

    /**
     * Download foto dari data lapangan milik enumerator
     */
    public function downloadFoto($hashedId, $type): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $enumerator = Enumerator::findByHashedIdOrFail($hashedId);
        $enumerator->load('dataLapangans');

        $allowed = ['foto_pendamping', 'foto_produk'];

        if (!in_array($type, $allowed)) {
            abort(403, 'Tipe foto tidak diizinkan.');
        }

        $data = $enumerator->dataLapangans
            ->whereNotNull($type)
            ->first();

        if (!$data || !$data->$type) {
            abort(404, 'Foto tidak ditemukan.');
        }

        $path = storage_path('app/public/' . $data->$type);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan di storage.');
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $filename  = $type . '_' . $enumerator->no_registrasi . '_' . $data->id . '.' . $extension;

        return response()->streamDownload(function () use ($path) {
            readfile($path);
        }, $filename);
    }


    /**
     * Download foto per entri data lapangan
     * Route: /enumerators/{id}/download-foto/{dataId}/{type}
     */
    public function downloadFotoByEntry($hashedId, $dataId, $type): StreamedResponse
    {
        $enumerator = Enumerator::findByHashedIdOrFail($hashedId);

        $allowed = ['foto_pendamping', 'foto_produk'];

        if (!in_array($type, $allowed)) {
            abort(403, 'Tipe foto tidak diizinkan.');
        }

        $data = $enumerator->dataLapangans()->findOrFail($dataId);

        if (!$data->$type) {
            abort(404, 'Foto tidak ditemukan.');
        }

        $path = storage_path('app/public/' . $data->$type);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan di storage.');
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $filename  = $type . '_' . $enumerator->no_registrasi . '_' . $data->id . '.' . $extension;

        return response()->streamDownload(function () use ($path) {
            readfile($path);
        }, $filename);
    }

    /**
     * Generate and display Surat Tugas
     */
    public function suratTugas($hashedId)
    {
        $enumerator = Enumerator::findByHashedIdOrFail($hashedId);
        $enumerator->load('koordinator');

        return view('superadmin.enumerator.partials.surat', compact('enumerator'));
    }

    /**
     * Generate ID Card as HTML
     */
    public function idCard($hashedId)
    {
        $enumerator = Enumerator::findByHashedIdOrFail($hashedId);

        return view('superadmin.enumerator.partials.idcard', compact('enumerator'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($hashedId): View
    {
        $enumerator   = Enumerator::findByHashedIdOrFail($hashedId);
        $koordinators = Koordinator::all();
        $banks        = DataBank::orderBy('name')->get();
        return view('superadmin.enumerator.edit', compact('enumerator', 'koordinators', 'banks'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(EnumeratorRequest $request, Enumerator $enumerator): RedirectResponse
    {
        $enumerator->update($request->validated());

        return Redirect::route('superadmin.enumerators.index')
            ->with('success', 'Enumerator updated successfully');
    }

    /**
     * Aktifkan kembali enumerator yang berstatus Tidak Aktif.
     */
    public function aktivasi($hashedId): RedirectResponse
    {
        $enumerator = Enumerator::findByHashedIdOrFail($hashedId);

        if ($enumerator->status === 'Tidak Aktif') {
            $enumerator->update(['status' => 'Aktif']);

            return Redirect::back()
                ->with('success', "Enumerator {$enumerator->nama_lengkap} berhasil diaktifkan kembali.");
        }

        return Redirect::back()
            ->with('error', 'Enumerator sudah berstatus Aktif.');
    }

    /**
     * Delete the specified resource.
     */
    public function destroy($hashedId): RedirectResponse
    {
        Enumerator::findByHashedIdOrFail($hashedId)->delete();

        return Redirect::route('superadmin.enumerators.index')
            ->with('success', 'Enumerator deleted successfully');
    }
}
