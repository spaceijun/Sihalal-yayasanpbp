<?php

namespace App\Services\Superadmin;

use App\Models\Enumerator;
use App\Models\Recruitment;
use App\Models\RecruitmentPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RecruitmentPostService
{
    /**
     * Buat lowongan pekerjaan baru.
     */
    public function create(array $data): RecruitmentPost
    {
        $data['slug'] = $this->generateUniqueSlug($data['nama_loker']);

        if (isset($data['template_pakta_integritas']) && $data['template_pakta_integritas'] instanceof \Illuminate\Http\UploadedFile) {
            $file = $data['template_pakta_integritas'];
            $ext = $file->getClientOriginalExtension();
            $fileName = 'template_pakta_integritas_' . time() . '.' . $ext;
            $path = $file->storeAs('recruitment_templates', $fileName, 'public');
            $data['template_pakta_integritas'] = $path;
        }

        return RecruitmentPost::create($data);
    }

    /**
     * Update lowongan pekerjaan.
     */
    public function update(RecruitmentPost $post, array $data): RecruitmentPost
    {
        // Jika nama berubah, generate slug baru
        if (isset($data['nama_loker']) && $data['nama_loker'] !== $post->nama_loker) {
            $data['slug'] = $this->generateUniqueSlug($data['nama_loker'], $post->id);
        }

        if (isset($data['template_pakta_integritas'])) {
            if ($data['template_pakta_integritas'] instanceof \Illuminate\Http\UploadedFile) {
                // Hapus file lama jika ada
                if ($post->template_pakta_integritas) {
                    Storage::disk('public')->delete($post->template_pakta_integritas);
                }
                $file = $data['template_pakta_integritas'];
                $ext = $file->getClientOriginalExtension();
                $fileName = 'template_pakta_integritas_' . time() . '.' . $ext;
                $path = $file->storeAs('recruitment_templates', $fileName, 'public');
                $data['template_pakta_integritas'] = $path;
            } elseif ($data['template_pakta_integritas'] === null) {
                if ($post->template_pakta_integritas) {
                    Storage::disk('public')->delete($post->template_pakta_integritas);
                }
                $data['template_pakta_integritas'] = null;
            }
        }

        $post->update($data);

        return $post->fresh();
    }

    /**
     * Toggle status aktif lowongan.
     */
    public function toggleActive(RecruitmentPost $post): RecruitmentPost
    {
        $post->update(['is_active' => ! $post->is_active]);

        return $post->fresh();
    }

    /**
     * Hapus lowongan pekerjaan.
     */
    public function delete(RecruitmentPost $post): void
    {
        $post->delete();
    }

    /**
     * Proses pendaftaran dari form publik dinamis.
     * Upload file sesuai requirements yang dikonfigurasi.
     */
    public function submitApplication(RecruitmentPost $post, Request $request): Recruitment
    {
        $requirements = $post->requirements ?? [];
        $data = [
            'recruitment_post_id' => $post->id,
            'recruit_type'        => $post->posisi,
            'status'              => 'Melamar',
        ];

        // Proses setiap field sesuai requirements
        foreach ($requirements as $req) {
            $fieldKey  = $req['field_key'];
            $fieldType = $req['type'];

            if ($fieldType === 'file') {
                if ($request->hasFile($fieldKey)) {
                    $file      = $request->file($fieldKey);
                    $ext       = $file->getClientOriginalExtension();
                    $fileName  = time() . '_' . uniqid() . '.' . $ext;
                    $folder    = 'recruitment/' . $fieldKey;
                    $file->storeAs($folder, $fileName, 'public');
                    $data[$fieldKey] = $folder . '/' . $fileName;
                }
            } elseif ($fieldType === 'checkbox') {
                $data[$fieldKey] = $request->has($fieldKey) ? '1' : '0';
            } else {
                $data[$fieldKey] = $request->input($fieldKey);
            }
        }

        // Map core fields jika ada di requirements (untuk kompatibilitas model Recruitment)
        $coreFields = [
            'nama_lengkap', 'telephone', 'alamat_lengkap', 'pengalaman',
            'rekomendasi', 'pendidikan_terakhir', 'foto_diri', 'foto_ktp',
            'foto_ijasah', 'pakta_integritas', 'nik', 'jenis_kelamin', 'type_entry',
        ];

        // Pastikan field yang tidak ada di model disimpan sebagai answers JSON
        $modelData = [];
        $extraData = [];

        foreach ($data as $key => $val) {
            if (in_array($key, array_merge($coreFields, ['recruitment_post_id', 'recruit_type', 'status', 'koordinator_id']))) {
                $modelData[$key] = $val;
            } else {
                $extraData[$key] = $val;
            }
        }

        // Simpan extra fields sebagai JSON di kolom 'answers' jika ada
        if (! empty($extraData)) {
            $modelData['answers'] = json_encode($extraData);
        }

        // Uppercase nama_lengkap jika ada
        if (isset($modelData['nama_lengkap'])) {
            $modelData['nama_lengkap'] = strtoupper($modelData['nama_lengkap']);
        }

        // Pastikan field required ada, isi default jika kosong
        $defaults = [
            'nama_lengkap'       => $request->input('nama_lengkap', '-'),
            'telephone'          => $request->input('telephone', '-'),
            'alamat_lengkap'     => $request->input('alamat_lengkap', '-'),
            'pengalaman'         => $request->input('pengalaman', '-'),
            'pendidikan_terakhir'=> $request->input('pendidikan_terakhir', '-'),
            'foto_diri'          => null,
            'foto_ktp'           => null,
        ];

        foreach ($defaults as $key => $default) {
            if (! isset($modelData[$key])) {
                $modelData[$key] = $default;
            }
        }

        return Recruitment::create($modelData);
    }

    /**
     * Update status lamaran (termasuk logika koordinator untuk PENDAMPING).
     */
    public function updateStatus(Recruitment $recruitment, string $status, ?int $koordinatorId = null, ?string $alasanPenolakan = null): array
    {
        DB::beginTransaction();

        try {
            $recruitType = $recruitment->recruit_type;
            $message     = '';

            $recruitment->status = $status;

            if ($status === 'Diterima') {
                $recruitment->alasan_penolakan = null;

                if ($recruitType === 'PENDAMPING') {
                    if (! $koordinatorId) {
                        throw new \Exception('Koordinator wajib dipilih untuk posisi PENDAMPING.');
                    }

                    $recruitment->koordinator_id = $koordinatorId;

                    $existingEnumerator = Enumerator::where('telephone', $recruitment->telephone)->first();

                    if (! $existingEnumerator) {
                        $lastNo   = Enumerator::lockForUpdate()->orderBy('no_registrasi', 'desc')->value('no_registrasi');
                        $nextNo   = $lastNo ? ((int) $lastNo + 1) : 1;

                        if ($nextNo > 999) {
                            throw new \Exception('No registrasi sudah penuh.');
                        }

                        $noRegistrasi = str_pad($nextNo, 3, '0', STR_PAD_LEFT);

                        Enumerator::create([
                            'koordinator_id' => $koordinatorId,
                            'nama_lengkap'   => $recruitment->nama_lengkap,
                            'telephone'      => $recruitment->telephone,
                            'foto_diri'      => $recruitment->foto_diri,
                            'no_registrasi'  => $noRegistrasi,
                            'alamat'         => $recruitment->alamat_lengkap,
                            'status'         => 'Aktif',
                        ]);

                        $message = 'Status diterima. Data enumerator baru telah dibuat!';
                    } else {
                        $existingEnumerator->update([
                            'koordinator_id' => $koordinatorId,
                            'nama_lengkap'   => $recruitment->nama_lengkap,
                            'alamat'         => $recruitment->alamat_lengkap,
                            'status'         => 'Aktif',
                        ]);

                        $message = 'Status diterima. Data enumerator telah diperbarui!';
                    }
                } else {
                    $recruitment->koordinator_id = null;
                    $message = "Status lamaran {$recruitType} berhasil diperbarui menjadi Diterima!";
                }
            } elseif ($status === 'Ditolak') {
                $recruitment->alasan_penolakan = $alasanPenolakan;
                $recruitment->koordinator_id   = null;

                if ($recruitType === 'PENDAMPING') {
                    $enumerator = Enumerator::where('telephone', $recruitment->telephone)->first();

                    if ($enumerator) {
                        $enumerator->delete();
                        $message = 'Status ditolak. Data enumerator telah dihapus!';
                    } else {
                        $message = 'Status lamaran berhasil diperbarui menjadi Ditolak!';
                    }
                } else {
                    $message = "Status lamaran {$recruitType} berhasil diperbarui menjadi Ditolak!";
                }
            } else {
                // Melamar
                $recruitment->koordinator_id   = null;
                $recruitment->alasan_penolakan = null;

                if ($recruitType === 'PENDAMPING') {
                    $enumerator = Enumerator::where('telephone', $recruitment->telephone)->first();

                    if ($enumerator) {
                        $enumerator->delete();
                    }
                }

                $message = 'Status lamaran berhasil dikembalikan ke Melamar!';
            }

            $recruitment->save();
            DB::commit();

            return ['success' => true, 'message' => $message];
        } catch (\Exception $e) {
            DB::rollBack();

            return ['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }

    /**
     * Generate slug unik dari nama loker.
     */
    private function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 1;

        while (true) {
            $query = RecruitmentPost::where('slug', $slug);

            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            if (! $query->exists()) {
                break;
            }

            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    /**
     * Parse dan validasi requirements JSON dari form input.
     * Format input: array dari superadmin (label, type, field_key, required, options).
     */
    public function parseRequirements(array $rawRequirements): array
    {
        $parsed = [];

        foreach ($rawRequirements as $req) {
            if (empty($req['label']) || empty($req['type'])) {
                continue;
            }

            $item = [
                'label'     => trim($req['label']),
                'type'      => $req['type'],
                'field_key' => Str::snake(trim($req['field_key'] ?? $req['label'])),
                'required'  => (bool) ($req['required'] ?? false),
                'hint'      => $req['hint'] ?? null,
            ];

            // Untuk type select/radio, simpan options
            if (in_array($req['type'], ['select', 'radio', 'checkbox_group'])) {
                $options = array_filter(array_map('trim', explode("\n", $req['options'] ?? '')));
                $item['options'] = array_values($options);
            }

            // Untuk file, simpan accept type
            if ($req['type'] === 'file') {
                $item['accept'] = $req['accept'] ?? '*/*';
            }

            $parsed[] = $item;
        }

        return $parsed;
    }
}
