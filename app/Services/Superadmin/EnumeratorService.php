<?php

namespace App\Services\Superadmin;

use App\Models\Enumerator;
use App\Models\EnumeratorAktivasiLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EnumeratorService
{
    /**
     * Aktifkan kembali enumerator yang berstatus Tidak Aktif.
     * Menyimpan log beserta surat pernyataan yang diupload.
     *
     * @throws \InvalidArgumentException jika status sudah Aktif
     */
    public function aktivasi(Enumerator $enumerator, UploadedFile $suratPernyataan, ?string $catatan = null): EnumeratorAktivasiLog
    {
        if ($enumerator->status === 'Aktif') {
            throw new \InvalidArgumentException('Enumerator sudah berstatus Aktif.');
        }

        // Simpan surat pernyataan
        $extension = $suratPernyataan->getClientOriginalExtension();
        $fileName  = 'surat-aktivasi_' . $enumerator->no_registrasi . '_' . time() . '.' . $extension;
        $path      = $suratPernyataan->storeAs('surat-aktivasi', $fileName, 'public');

        // Update status enumerator
        $enumerator->update(['status' => 'Aktif']);

        // Simpan log
        $log = EnumeratorAktivasiLog::create([
            'enumerator_id'    => $enumerator->id,
            'diaktifkan_oleh'  => Auth::user()?->name ?? 'System',
            'surat_pernyataan' => $path,
            'catatan'          => $catatan,
            'tanggal_aktivasi' => now(),
        ]);

        return $log;
    }

    /**
     * Hapus file surat pernyataan dari storage (opsional / maintenance).
     */
    public function deleteSuratPernyataan(EnumeratorAktivasiLog $log): void
    {
        if ($log->surat_pernyataan && Storage::disk('public')->exists($log->surat_pernyataan)) {
            Storage::disk('public')->delete($log->surat_pernyataan);
        }
    }
}
