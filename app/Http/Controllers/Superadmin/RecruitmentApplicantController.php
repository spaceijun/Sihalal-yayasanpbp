<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Traits\HasRoutePrefix;
use App\Models\Recruitment;
use App\Models\RecruitmentPost;
use App\Models\Superadmin\Koordinator;
use App\Services\Superadmin\RecruitmentPostService;
use Illuminate\Http\Request;

class RecruitmentApplicantController extends Controller
{
    use HasRoutePrefix;

    public function __construct(private RecruitmentPostService $service) {}

    /**
     * Tampilkan form pendaftaran publik berdasarkan slug.
     */
    public function form(string $slug)
    {
        $post = RecruitmentPost::where('slug', $slug)->firstOrFail();

        if (! $post->isOpen()) {
            return view('publik.recruitment-closed', compact('post'));
        }

        $koordinators = $post->posisi === 'PENDAMPING' ? Koordinator::all() : collect();

        return view('publik.recruitment-form', compact('post', 'koordinators'));
    }

    /**
     * Proses submit form pendaftaran publik.
     */
    public function submit(Request $request, string $slug)
    {
        $post = RecruitmentPost::where('slug', $slug)->firstOrFail();

        if (! $post->isOpen()) {
            return redirect()->back()->with('error', 'Pendaftaran sudah ditutup.');
        }

        // Validasi dinamis berdasarkan requirements
        $rules   = [];
        $messages = [];

        foreach ($post->requirements ?? [] as $req) {
            $fieldKey = $req['field_key'];
            $label    = $req['label'];
            $rule     = [];

            if ($req['required'] ?? false) {
                $rule[] = $req['type'] === 'file' ? 'required|file' : 'required';
                $messages["{$fieldKey}.required"] = "{$label} wajib diisi.";
                $messages["{$fieldKey}.file"]     = "{$label} harus berupa file.";
            } else {
                $rule[] = 'nullable';
            }

            if ($req['type'] === 'file') {
                $rule[] = 'max:5120'; // 5MB
                $messages["{$fieldKey}.max"] = "{$label} tidak boleh lebih dari 5MB.";
            }

            $rules[$fieldKey] = implode('|', $rule);
        }

        $request->validate($rules, $messages);

        $recruitment = $this->service->submitApplication($post, $request);

        return redirect()->route('recruitment.confirm', $recruitment->hashed_id);
    }

    /**
     * Update status pelamar dari halaman show lowongan (superadmin).
     */
    public function updateStatus(Request $request, $recruitmentId)
    {
        $recruitment = Recruitment::findByHashedIdOrFail($recruitmentId);
        $recruitType = $recruitment->recruit_type;

        // Validasi dinamis berdasarkan tipe
        $validationRules = ['status' => 'required|in:Melamar,Diterima,Ditolak'];

        if ($recruitType === 'PENDAMPING') {
            $validationRules['koordinator_id'] = 'required_if:status,Diterima|nullable|exists:koordinators,id';
        }

        $validationRules['alasan_penolakan'] = 'required_if:status,Ditolak|nullable';

        $request->validate($validationRules, [
            'koordinator_id.required_if'    => 'Koordinator wajib dipilih jika status diterima.',
            'alasan_penolakan.required_if'  => 'Alasan penolakan wajib diisi jika status ditolak.',
        ]);

        $result = $this->service->updateStatus(
            $recruitment,
            $request->status,
            $request->filled('koordinator_id') ? (int) $request->koordinator_id : null,
            $request->alasan_penolakan
        );

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Tampilkan detail pelamar.
     */
    public function show(string $hashedId)
    {
        $recruitment = Recruitment::with(['koordinator', 'recruitmentPost'])->findOrFail(Recruitment::findByHashedIdOrFail($hashedId)->id);
        $koordinators = Koordinator::all();

        $routePrefix = $this->routePrefix();

        return view('superadmin.recruitment.show', compact('recruitment', 'koordinators', 'routePrefix'));
    }

    /**
     * Tampilkan halaman konfirmasi pendaftaran.
     */
    public function confirm(string $hashedId)
    {
        $recruitment = Recruitment::findByHashedIdOrFail($hashedId);

        return view('publik.confirm', compact('recruitment'));
    }
}
