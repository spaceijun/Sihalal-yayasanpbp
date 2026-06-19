<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Recruitment;
use App\Models\RecruitmentPost;
use App\Models\Superadmin\Koordinator;
use App\Services\Superadmin\RecruitmentPostService;
use App\Traits\HasRoutePrefix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        $rules = [];
        $messages = [];

        foreach ($post->requirements ?? [] as $req) {
            // Guard: skip malformed entries that lack required keys
            if (empty($req['field_key']) || empty($req['type'])) {
                continue;
            }

            $fieldKey = $req['field_key'];
            $label = $req['label'] ?? $fieldKey;
            $rule = [];

            if ($req['required'] ?? false) {
                $rule[] = $req['type'] === 'file' ? 'required' : 'required';
                $messages["{$fieldKey}.required"] = "{$label} wajib diisi.";
            } else {
                $rule[] = 'nullable';
            }

            if ($req['type'] === 'file') {
                $rule[] = 'file';
                $rule[] = 'max:5120'; // 5MB

                // Add MIME validation based on accept attribute
                $accept = $req['accept'] ?? null;
                if ($accept && $accept !== '*/*') {
                    $mimes = array_filter(array_map(function ($m) {
                        $m = trim($m);
                        // Convert MIME type to extension for Laravel's mimes rule
                        $map = [
                            'image/jpeg' => 'jpg,jpeg',
                            'image/jpg' => 'jpg,jpeg',
                            'image/png' => 'png',
                            'image/gif' => 'gif',
                            'application/pdf' => 'pdf',
                            'application/msword' => 'doc',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                        ];

                        return $map[$m] ?? null;
                    }, explode(',', $accept)));

                    if (! empty($mimes)) {
                        $extList = implode(',', array_unique(explode(',', implode(',', $mimes))));
                        $rule[] = 'mimes:'.$extList;
                        $messages["{$fieldKey}.mimes"] = "{$label} harus berformat ".strtoupper(str_replace(',', '/', $extList)).'.';
                    }

                    $messages["{$fieldKey}.file"] = "{$label} harus berupa file.";
                    $messages["{$fieldKey}.max"] = "{$label} tidak boleh lebih dari 5MB.";
                }
            }

            $rules[$fieldKey] = implode('|', $rule);
        }

        $request->validate($rules, $messages);

        try {
            $recruitment = $this->service->submitApplication($post, $request);
        } catch (\Throwable $e) {
            Log::error('[RecruitmentApplicantController@submit] Gagal menyimpan lamaran', [
                'slug'      => $slug,
                'error'     => $e->getMessage(),
                'file'      => $e->getFile() . ':' . $e->getLine(),
                'input'     => $request->except(['_token']),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan lamaran. Silakan coba lagi.');
        }

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
            'koordinator_id.required_if' => 'Koordinator wajib dipilih jika status diterima.',
            'alasan_penolakan.required_if' => 'Alasan penolakan wajib diisi jika status ditolak.',
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
