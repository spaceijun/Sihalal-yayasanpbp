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
    public function show($id)
    {
        $enumerator = Enumerator::with(['koordinator', 'bank'])->find($id);

        return view('superadmin.enumerator.show', compact('enumerator'));
    }

    /**
     * Generate and display Surat Tugas
     */
    public function suratTugas($id)
    {
        $enumerator = Enumerator::with('koordinator')->findOrFail($id);

        return view('superadmin.enumerator.partials.surat', compact('enumerator'));
    }

    /**
     * Generate ID Card as HTML
     */
    public function idCard($id)
    {
        $enumerator = Enumerator::find($id);

        return view('superadmin.enumerator.partials.idcard', compact('enumerator'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $enumerator   = Enumerator::find($id);
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
    public function aktivasi($id): RedirectResponse
    {
        $enumerator = Enumerator::findOrFail($id);

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
    public function destroy($id): RedirectResponse
    {
        Enumerator::find($id)->delete();

        return Redirect::route('superadmin.enumerators.index')
            ->with('success', 'Enumerator deleted successfully');
    }
}
