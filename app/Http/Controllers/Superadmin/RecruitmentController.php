<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RecruitmentRequest;
use App\Models\Enumerator;
use App\Models\Recruitment;
use App\Models\Superadmin\Koordinator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class RecruitmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('superadmin.recruitment.index');
    }

    /**
     * Return DataTables JSON for recruitment listing.
     */
    public function data(Request $request)
    {
        $query = Recruitment::with('koordinator');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('koordinator_name', fn($r) => $r->koordinator->nama_lengkap ?? '—')
            ->addColumn('nama_cell', function ($r) {
                $inisial = strtoupper(substr($r->nama_lengkap, 0, 2));
                $showUrl = route('superadmin.recruitments.show', $r->hashed_id);
                return '<div class="adm-name-cell">
                    <div class="adm-avatar" style="background:var(--adm-blue-lt);color:var(--adm-blue);">' . $inisial . '</div>
                    <a href="' . $showUrl . '" style="font-weight:600;font-size:13px;color:var(--adm-text-dark);text-decoration:none;">' . e($r->nama_lengkap) . '</a>
                </div>';
            })
            ->addColumn('rekomendasi_badge', function ($r) {
                if ($r->rekomendasi) {
                    return '<span class="adm-badge adm-badge-success">' . e($r->rekomendasi) . '</span>';
                }
                return '<span style="color:var(--adm-text-faint);">—</span>';
            })
            ->addColumn('status_badge', function ($r) {
                if ($r->status === 'Diterima') return '<span class="adm-badge adm-badge-success"><span class="dot"></span>Diterima</span>';
                if ($r->status === 'Ditolak')  return '<span class="adm-badge adm-badge-danger"><span class="dot"></span>Ditolak</span>';
                return '<span class="adm-badge adm-badge-pending"><span class="dot"></span>Melamar</span>';
            })
            ->addColumn('recruit_type_badge', function ($r) {
                $type = $r->recruit_type ?? '—';
                return '<span class="adm-badge adm-badge-info">' . e($type) . '</span>';
            })
            ->addColumn('aksi', function ($r) {
                $showUrl   = route('superadmin.recruitments.show', $r->hashed_id);
                $deleteUrl = route('superadmin.recruitments.destroy', $r->id);
                return '<div class="adm-actions">
                    <a class="adm-btn primary icon-only" href="' . $showUrl . '" title="Detail">
                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                    <form action="' . $deleteUrl . '" method="POST" class="d-inline" onsubmit="return confirm(\'Yakin hapus data ini?\')">
                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="adm-btn danger icon-only" title="Hapus">
                            <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                        </button>
                    </form>
                </div>';
            })
            ->rawColumns(['nama_cell', 'rekomendasi_badge', 'status_badge', 'recruit_type_badge', 'aksi'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $recruitment = new Recruitment;
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
            $imageName = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
            $image->storeAs('recruitment/foto-ktp', $imageName, 'public');
            $validatedData['foto_ktp'] = 'recruitment/foto-ktp/'.$imageName;
        }

        // Handle foto_diri
        if ($request->hasFile('foto_diri')) {
            $image = $request->file('foto_diri');
            $imageName = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
            $image->storeAs('recruitment/foto-diri', $imageName, 'public');
            $validatedData['foto_diri'] = 'recruitment/foto-diri/'.$imageName;
        }

        // Handle foto_ijasah
        if ($request->hasFile('foto_ijasah')) {
            $file = $request->file('foto_ijasah');
            $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->storeAs('recruitment/foto-ijasah', $fileName, 'public');
            $validatedData['foto_ijasah'] = 'recruitment/foto-ijasah/'.$fileName;
        }

        // Handle pakta_integritas
        if ($request->hasFile('pakta_integritas')) {
            $file = $request->file('pakta_integritas');
            $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->storeAs('recruitment/pakta-integritas', $fileName, 'public');
            $validatedData['pakta_integritas'] = 'recruitment/pakta-integritas/'.$fileName;
        }

        $recruitment = Recruitment::create($validatedData);

        return Redirect::route('recruitment.confirm', $recruitment->hashed_id);
    }

    public function updateStatus(Request $request, $hashedId)
    {
        $recruitment = Recruitment::findByHashedIdOrFail($hashedId);
        $recruitType = $recruitment->recruit_type; // PENDAMPING, DATA ENTRY, atau ADMIN UMUM

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
            // DATA ENTRY / ADMIN UMUM – hanya bisa Diterima/Ditolak/Melamar, tanpa koordinator
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

                    if (! $existingEnumerator) {
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
                                'nama_lengkap' => $recruitment->nama_lengkap,
                                'telephone' => $recruitment->telephone,
                                'foto_diri' => $recruitment->foto_diri,
                                'no_registrasi' => $noRegistrasi,
                                'alamat' => $recruitment->alamat_lengkap,
                                'status' => 'Aktif',
                            ]);
                        });

                        $message = 'Status lamaran berhasil diperbarui dan data enumerator telah dibuat!';
                    } else {
                        $existingEnumerator->update([
                            'koordinator_id' => $request->koordinator_id,
                            'nama_lengkap' => $recruitment->nama_lengkap,
                            'alamat' => $recruitment->alamat_lengkap,
                            'status' => 'Aktif',
                        ]);

                        $message = 'Status lamaran berhasil diperbarui dan data enumerator telah diupdate!';
                    }
                } else {
                    // DATA ENTRY / ADMIN UMUM: tidak butuh koordinator, tidak buat Enumerator
                    $recruitment->koordinator_id = null;
                    $message = 'Status lamaran ' . $recruitType . ' berhasil diperbarui menjadi diterima!';
                }
            } elseif ($request->status == 'Ditolak') {

                $recruitment->alasan_penolakan = $request->alasan_penolakan;
                $recruitment->koordinator_id = null;

                if ($recruitType == 'PENDAMPING') {
                    $enumerator = Enumerator::where('telephone', $recruitment->telephone)->first();

                    if ($enumerator) {
                        $enumerator->delete();
                        $message = 'Status lamaran berhasil diperbarui menjadi ditolak dan data enumerator telah dihapus!';
                    } else {
                        $message = 'Status lamaran berhasil diperbarui menjadi ditolak!';
                    }
                } else {
                    // DATA ENTRY / ADMIN UMUM tidak punya enumerator
                    $message = 'Status lamaran ' . $recruitType . ' berhasil diperbarui menjadi ditolak!';
                }
            } else {
                // Status: Melamar
                $recruitment->koordinator_id = null;
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

            return redirect()->back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function downloadFoto($hashedId, $type)
    {
        $recruitment = Recruitment::findByHashedIdOrFail($hashedId);
        $filePath = storage_path('app/public/'.$recruitment->$type);

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
