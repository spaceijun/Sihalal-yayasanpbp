<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Recruitment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\RecruitmentRequest;
use App\Models\Enumerator;
use App\Models\Superadmin\Koordinator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class RecruitmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Recruitment::with('koordinator');

        // Apply search filter
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama_lengkap', 'like', '%' . $searchTerm . '%')
                    ->orWhere('telephone', 'like', '%' . $searchTerm . '%');
            });
        }

        // Apply status filter
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Paginate - akan otomatis pakai $perPage = 20 dari model
        $recruitments = $query->paginate();

        // Append query parameters ke pagination links
        $recruitments->appends($request->only(['search', 'status']));

        // Jika AJAX request, return JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'table' => view('superadmin.recruitment.partials.table-body', compact('recruitments'))->render(),
                'pagination' => view('layouts.pagination', ['paginator' => $recruitments])->render()
            ]);
        }

        // Jika normal request, return view
        return view('superadmin.recruitment.index', compact('recruitments'))
            ->with('i', ($request->input('page', 1) - 1) * $recruitments->perPage());
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $recruitment = new Recruitment();
        $daftarRekomendasi = Koordinator::all();

        return view('publik.form-recruitment', compact('recruitment', 'daftarRekomendasi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RecruitmentRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        // Auto uppercase nama_lengkap
        $validatedData['nama_lengkap'] = strtoupper($validatedData['nama_lengkap']);

        // Handle foto_ktp
        if ($request->hasFile('foto_ktp')) {
            $image = $request->file('foto_ktp');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('recruitment/foto-ktp', $imageName, 'public');
            $validatedData['foto_ktp'] = 'recruitment/foto-ktp/' . $imageName;
        }

        // Handle foto_diri
        if ($request->hasFile('foto_diri')) {
            $image = $request->file('foto_diri');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('recruitment/foto-diri', $imageName, 'public');
            $validatedData['foto_diri'] = 'recruitment/foto-diri/' . $imageName;
        }

        // Handle foto_ijasah
        if ($request->hasFile('foto_ijasah')) {
            $file = $request->file('foto_ijasah');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('recruitment/foto-ijasah', $fileName, 'public');
            $validatedData['foto_ijasah'] = 'recruitment/foto-ijasah/' . $fileName;
        }

        // Handle pakta_integritas
        if ($request->hasFile('pakta_integritas')) {
            $file = $request->file('pakta_integritas');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('recruitment/pakta-integritas', $fileName, 'public');
            $validatedData['pakta_integritas'] = 'recruitment/pakta-integritas/' . $fileName;
        }

        $recruitment = Recruitment::create($validatedData);

        return Redirect::route('recruitment.confirm', $recruitment->hashed_id);
    }

    public function updateStatus(Request $request, $hashedId)
    {
        $recruitment = Recruitment::findByHashedIdOrFail($hashedId);
        $recruitType = $recruitment->recruit_type; // PENDAMPING atau DATA ENTRY

        // Validasi dinamis berdasarkan recruit_type
        if ($recruitType == 'PENDAMPING') {
            $request->validate([
                'status' => 'required|in:Melamar,Diterima,Ditolak',
                'koordinator_id' => 'required_if:status,Diterima|nullable|exists:koordinators,id',
                'alasan_penolakan' => 'required_if:status,Ditolak|nullable',
            ], [
                'koordinator_id.required_if' => 'Koordinator wajib dipilih jika status diterima',
                'koordinator_id.exists' => 'Koordinator yang dipilih tidak valid',
                'alasan_penolakan.required_if' => 'Alasan penolakan wajib diisi jika status ditolak',
            ]);
        } else {
            // DATA ENTRY â€” hanya bisa Diterima/Ditolak/Melamar, tanpa koordinator
            $request->validate([
                'status' => 'required|in:Melamar,Diterima,Ditolak',
                'alasan_penolakan' => 'required_if:status,Ditolak|nullable',
            ], [
                'alasan_penolakan.required_if' => 'Alasan penolakan wajib diisi jika status ditolak',
            ]);
        }

        DB::beginTransaction();

        try {
            $previousStatus = $recruitment->status;
            $recruitment->status = $request->status;

            if ($request->status == 'Diterima') {

                $recruitment->alasan_penolakan = null;

                if ($recruitType == 'PENDAMPING') {
                    // â”€â”€ PENDAMPING: butuh koordinator, buat/update Enumerator â”€â”€
                    $recruitment->koordinator_id = $request->koordinator_id;

                    $existingEnumerator = Enumerator::where('telephone', $recruitment->telephone)->first();

                    if (!$existingEnumerator) {
                        DB::transaction(function () use ($request, $recruitment) {
                            $lastNo = Enumerator::lockForUpdate()
                                ->orderBy('no_registrasi', 'desc')
                                ->value('no_registrasi');

                            $nextNo = $lastNo ? ((int) $lastNo + 1) : 1;

                            if ($nextNo > 999) {
                                throw new \Exception('No registrasi sudah penuh');
                            }

                            $noRegistrasi = str_pad($nextNo, 3, '0', STR_PAD_LEFT);

                            Enumerator::create([
                                'koordinator_id' => $request->koordinator_id,
                                'nama_lengkap'   => $recruitment->nama_lengkap,
                                'telephone'      => $recruitment->telephone,
                                'foto_diri'      => $recruitment->foto_diri,
                                'no_registrasi'  => $noRegistrasi,
                                'alamat'         => $recruitment->alamat_lengkap,
                                'status'         => 'Aktif',
                            ]);
                        });

                        $message = 'Status lamaran berhasil diperbarui dan data enumerator telah dibuat!';
                    } else {
                        $existingEnumerator->update([
                            'koordinator_id' => $request->koordinator_id,
                            'nama_lengkap'   => $recruitment->nama_lengkap,
                            'alamat'         => $recruitment->alamat_lengkap,
                            'status'         => 'Aktif',
                        ]);

                        $message = 'Status lamaran berhasil diperbarui dan data enumerator telah diupdate!';
                    }
                } else {
                    // â”€â”€ DATA ENTRY: tidak butuh koordinator, tidak buat Enumerator â”€â”€
                    $recruitment->koordinator_id = null;
                    $message = 'Status lamaran DATA ENTRY berhasil diperbarui menjadi diterima!';
                }
            } elseif ($request->status == 'Ditolak') {

                $recruitment->alasan_penolakan = $request->alasan_penolakan;
                $recruitment->koordinator_id   = null;

                if ($recruitType == 'PENDAMPING') {
                    $enumerator = Enumerator::where('telephone', $recruitment->telephone)->first();

                    if ($enumerator) {
                        $enumerator->delete();
                        $message = 'Status lamaran berhasil diperbarui menjadi ditolak dan data enumerator telah dihapus!';
                    } else {
                        $message = 'Status lamaran berhasil diperbarui menjadi ditolak!';
                    }
                } else {
                    // DATA ENTRY tidak punya enumerator
                    $message = 'Status lamaran DATA ENTRY berhasil diperbarui menjadi ditolak!';
                }
            } else {
                // Status: Melamar
                $recruitment->koordinator_id   = null;
                $recruitment->alasan_penolakan = null;

                if ($recruitType == 'PENDAMPING') {
                    $enumerator = Enumerator::where('telephone', $recruitment->telephone)->first();

                    if ($enumerator) {
                        $enumerator->delete();
                        $message = 'Status lamaran berhasil diperbarui dan data enumerator telah dihapus!';
                    } else {
                        $message = 'Status lamaran berhasil diperbarui!';
                    }
                } else {
                    $message = 'Status lamaran berhasil diperbarui!';
                }
            }

            $recruitment->save();
            DB::commit();

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function downloadFoto($hashedId, $type)
    {
        $recruitment = Recruitment::findByHashedIdOrFail($hashedId);
        $filePath = storage_path('app/public/' . $recruitment->$type);

        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        return redirect()->back()->with('error', 'File tidak ditemukan!');
    }

    /**
     * Display the specified resource.
     */
    public function show($hashedId): View
    {
        $recruitment = Recruitment::with('koordinator')->findOrFail(Recruitment::findByHashedIdOrFail($hashedId)->id);
        $koordinators = Koordinator::all();

        return view('superadmin.recruitment.show', compact('recruitment', 'koordinators'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $recruitment = Recruitment::find($id);

        return view('superadmin.recruitment.edit', compact('recruitment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RecruitmentRequest $request, Recruitment $recruitment): RedirectResponse
    {
        $recruitment->update($request->validated());

        return Redirect::route('superadmin.recruitments.index')
            ->with('success', 'Recruitment updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Recruitment::find($id)->delete();

        return Redirect::route('superadmin.recruitments.index')
            ->with('success', 'Recruitment deleted successfully');
    }

    public function confirm($hashedId)
    {
        $recruitment = Recruitment::findByHashedIdOrFail($hashedId);
        return view('publik.confirm', compact('recruitment'));
    }
}



