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
    public function index(Request $request): View
    {
        $recruitments = Recruitment::with('koordinator')->paginate();

        return view('superadmin.recruitment.index', compact('recruitments'))
            ->with('i', ($request->input('page', 1) - 1) * $recruitments->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $recruitment = new Recruitment();

        return view('publik.form-recruitment', compact('recruitment'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RecruitmentRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
        ]);
        // Handle foto_ktp
        if ($request->hasFile('foto_ktp')) {
            $image = $request->file('foto_ktp');
            $extension = $image->getClientOriginalExtension();
            // Sanitasi nama file - hapus karakter spesial
            $imageName = time() . '_' . uniqid() . '.' . $extension;
            $image->storeAs('recruitment/foto-ktp', $imageName, 'public');
            $validatedData['foto_ktp'] = 'recruitment/foto-ktp/' . $imageName;
        }

        // Handle foto_diri
        if ($request->hasFile('foto_diri')) {
            $image = $request->file('foto_diri');
            $extension = $image->getClientOriginalExtension();
            $imageName = time() . '_' . uniqid() . '.' . $extension;
            $image->storeAs('recruitment/foto-diri', $imageName, 'public');
            $validatedData['foto_diri'] = 'recruitment/foto-diri/' . $imageName;
        }

        $validated['nama_lengkap'] = strtoupper($validated['nama_lengkap']);

        Recruitment::create($validatedData);

        return Redirect::route('recruitment.formulir')
            ->with('success', 'Lamaran anda berhasil dikirim.');
    }


    /**
     * Update status recruitment dan create enumerator jika diterima
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Melamar,Diterima,Ditolak',
            'koordinator_id' => 'required_if:status,Diterima|nullable|exists:koordinators,id',
            'alasan_penolakan' => 'required_if:status,Ditolak|nullable',
        ], [
            'koordinator_id.required_if' => 'Koordinator wajib dipilih jika status diterima',
            'koordinator_id.exists' => 'Koordinator yang dipilih tidak valid',
            'alasan_penolakan.required_if' => 'Alasan penolakan wajib diisi jika status ditolak',
        ]);

        DB::beginTransaction();

        try {
            $recruitment = Recruitment::findOrFail($id);
            $previousStatus = $recruitment->status;

            $recruitment->status = $request->status;

            if ($request->status == 'Diterima') {
                $recruitment->koordinator_id = $request->koordinator_id;
                $recruitment->alasan_penolakan = null;

                // Cek apakah recruitment ini sudah pernah membuat enumerator
                $existingEnumerator = Enumerator::where('telephone', $recruitment->telephone)->first();

                if (!$existingEnumerator) {

                    DB::transaction(function () use ($request, $recruitment) {

                        // Ambil no_registrasi terakhir (lock supaya tidak bentrok)
                        $lastNo = Enumerator::lockForUpdate()
                            ->orderBy('no_registrasi', 'desc')
                            ->value('no_registrasi');

                        // Tentukan nomor berikutnya
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
                    // Jika sudah ada, update data enumerator
                    $existingEnumerator->update([
                        'koordinator_id' => $request->koordinator_id,
                        'nama_lengkap' => $recruitment->nama_lengkap,
                        'alamat' => $recruitment->alamat_lengkap,
                        'status' => 'Aktif',
                    ]);

                    $message = 'Status lamaran berhasil diperbarui dan data enumerator telah diupdate!';
                }
            } elseif ($request->status == 'Ditolak') {
                $recruitment->alasan_penolakan = $request->alasan_penolakan;
                $recruitment->koordinator_id = null;

                // Jika ada enumerator dengan telephone yang sama, hapus data enumerator
                $enumerator = Enumerator::where('telephone', $recruitment->telephone)->first();

                if ($enumerator) {
                    // Hapus data enumerator karena lamaran ditolak
                    $enumerator->delete();
                    $message = 'Status lamaran berhasil diperbarui menjadi ditolak dan data enumerator telah dihapus!';
                } else {
                    // Jika tidak ada enumerator (belum pernah diterima)
                    $message = 'Status lamaran berhasil diperbarui menjadi ditolak!';
                }
            } else {
                // Status Melamar
                $recruitment->koordinator_id = null;
                $recruitment->alasan_penolakan = null;

                // Jika status kembali ke Melamar dan sebelumnya ada enumerator, hapus data enumerator
                $enumerator = Enumerator::where('telephone', $recruitment->telephone)->first();
                if ($enumerator) {
                    $enumerator->delete();
                    $message = 'Status lamaran berhasil diperbarui dan data enumerator telah dihapus!';
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

    public function downloadFoto($id, $type)
    {
        $recruitment = Recruitment::findOrFail($id);
        $filePath = storage_path('app/public/' . $recruitment->$type);

        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        return redirect()->back()->with('error', 'File tidak ditemukan!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $recruitment = Recruitment::with('koordinator')->findOrFail($id);
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
}
