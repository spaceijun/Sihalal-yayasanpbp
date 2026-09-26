<?php

namespace App\Http\Controllers\AdminUmum;

use App\Http\Controllers\Controller;
use App\Models\DataEntryProgress;
use App\Services\AdminUmum\DataEntryProgressService;
use App\Traits\HasRoutePrefix;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class DataEntryProgressController extends Controller
{
    use HasRoutePrefix;

    public function __construct(private DataEntryProgressService $service) {}

    protected function routePrefix(): string
    {
        return 'admin-umum';
    }

    /**
     * Yajra DataTables JSON endpoint untuk tabel progress di halaman index.
     * Admin Umum hanya melihat data dengan status PENDING dan REVISI
     */
    public function data(Request $request)
    {
        $query = $this->service->getListingQuery($request);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('checkbox', function ($p) {
                if (! in_array($p->status, ['PENDING', 'REVISI'])) {
                    return '';
                }

                return '<input type="checkbox" name="progress_ids[]" value="'.$p->hashed_id.'" class="form-check-input row-check">';
            })
            ->addColumn('tanggal', fn ($p) => $p->actioned_at?->format('d/m/Y H:i') ?? '-')
            ->addColumn('data_entry_cell', function ($p) {
                $init = strtoupper(substr($p->dataEntry?->user?->name ?? 'U', 0, 2));
                $name = e($p->dataEntry?->user?->name ?? '-');

                return '<div class="adm-name-cell">
                    <div class="adm-avatar" style="background:var(--adm-blue-lt);color:var(--adm-blue);font-size:11px;">'.$init.'</div>
                    <strong style="font-size:13px;">'.$name.'</strong>
                </div>';
            })
            ->addColumn('type_badge', function ($p) {
                return match ($p->dataEntry?->entry_type) {
                    'OSS' => '<span class="adm-badge adm-badge-oss">OSS</span>',
                    'SIHALAL' => '<span class="adm-badge adm-badge-sihalal">SIHALAL</span>',
                    default => '<span class="adm-badge adm-badge-nonaktif">—</span>',
                };
            })
            ->addColumn('nama_pu_cell', function ($p) {
                $url = route('admin-umum.data-entry-progress.show', $p->hashed_id);
                $nama = e($p->dataLapangan?->nama_pu ?? '-');
                $nik = e($p->dataLapangan?->nik ?? '');

                return '<a href="'.$url.'" style="font-weight:600;font-size:13px;color:var(--adm-blue);text-decoration:none;">'.$nama.'</a>'
                    .'<div style="font-size:11px;color:var(--adm-text-muted);">'.$nik.'</div>';
            })
            ->addColumn('status_badge', function ($p) {
                return match ($p->status) {
                    'PENDING' => '<span class="adm-badge adm-badge-pending"><span class="dot"></span>PENDING</span>',
                    'VALIDASI_ADMIN' => '<span class="adm-badge adm-badge-info"><span class="dot"></span>VALIDASI ADMIN</span>',
                    'DITERIMA' => '<span class="adm-badge adm-badge-success"><span class="dot"></span>DITERIMA</span>',
                    'REVISI' => '<span class="adm-badge adm-badge-revisi"><span class="dot"></span>REVISI</span>',
                    'DITOLAK' => '<span class="adm-badge adm-badge-danger"><span class="dot"></span>DITOLAK</span>',
                    default => '',
                };
            })
            ->addColumn('verifikator_cell', function ($p) {
                if ($p->verifikator) {
                    return '<div style="font-weight:600;font-size:13px;">'.e($p->verifikator->nama_lengkap).'</div>'
                        .'<div style="font-size:11px;color:var(--adm-text-muted);">'.($p->tanggal_verifikasi?->format('d/m/Y') ?? '').'</div>';
                }

                return '<span style="color:var(--adm-text-faint);">—</span>';
            })
            ->addColumn('keterangan_cell', function ($p) {
                if (! $p->keterangan_revisi && ! $p->keterangan_update) {
                    return '<span style="color:var(--adm-text-faint);">—</span>';
                }
                $cls = $p->keterangan_update ? 'success' : 'danger';
                $label = $p->keterangan_update ? 'Sudah Direvisi' : 'Perlu Revisi';
                $kr = $p->keterangan_revisi ? "'".addslashes(e($p->keterangan_revisi))."'" : 'null';
                $ku = $p->keterangan_update ? "'".addslashes(e($p->keterangan_update))."'" : 'null';

                return '<button type="button" class="adm-btn '.$cls.'" onclick="lihatKeterangan('.$kr.','.$ku.')">'.$label.'</button>';
            })
            ->addColumn('aksi', function ($p) {
                if (in_array($p->status, ['PENDING', 'REVISI'])) {
                    $type = $p->dataEntry?->entry_type;

                    return '<div class="adm-actions">
                        <button type="button" class="adm-btn success icon-only" title="Validasi" onclick="submitTerima(\''.$p->hashed_id.'\',\''.$type.'\')">
                            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </button>
                        <button type="button" class="adm-btn warning icon-only" title="Minta Revisi" onclick="bukaModalRevisi(\''.$p->hashed_id.'\')">
                            <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </button>
                        <button type="button" class="adm-btn danger icon-only" title="Tolak" onclick="bukaModalTolak(\''.$p->hashed_id.'\')">
                            <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>';
                }
                $url = route('admin-umum.data-entry-progress.show', $p->hashed_id);

                return '<div class="adm-actions">
                    <a href="'.$url.'" class="adm-btn primary icon-only" title="Lihat Detail">
                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                </div>';
            })
            ->filterColumn(
                'nama_pu_cell',
                fn ($q, $k) => $q->whereHas('dataLapangan', fn ($q2) => $q2->where('nama_pu', 'like', "%{$k}%"))
            )
            ->filterColumn(
                'data_entry_cell',
                fn ($q, $k) => $q->whereHas('dataEntry.user', fn ($q2) => $q2->where('name', 'like', "%{$k}%"))
            )
            ->rawColumns(['checkbox', 'data_entry_cell', 'type_badge', 'nama_pu_cell', 'status_badge', 'verifikator_cell', 'keterangan_cell', 'aksi'])
            ->make(true);
    }

    public function index(Request $request): View
    {
        $data = $this->service->getIndexData($request);

        // Use superadmin view layout with admin-umum routePrefix
        return view('superadmin.data-entry.progress.index', array_merge(
            $data,
            ['routePrefix' => 'admin-umum']
        ));
    }

    public function show(DataEntryProgress $progress): View
    {
        $data = $this->service->getShowData($progress);

        // Use superadmin view layout with admin-umum routePrefix
        return view('superadmin.data-entry.progress.show', array_merge(
            $data,
            ['routePrefix' => 'admin-umum']
        ));
    }

    /**
     * Validasi progress oleh Admin Umum.
     * Admin Umum hanya bisa memvalidasi progress dengan status PENDING dan REVISI.
     */
    public function validasi(Request $request, DataEntryProgress $progress): RedirectResponse
    {
        $result = $this->service->validasi($request, $progress);

        return $result['success']
            ? redirect()->back()->with('success', $result['message'])
            : redirect()->back()->with('error', $result['message']);
    }

    /**
     * Bulk validasi progress oleh Admin Umum.
     * Admin Umum hanya bisa bulk validasi progress dengan status PENDING dan REVISI.
     */
    public function bulkValidasi(Request $request): RedirectResponse
    {
        $result = $this->service->bulkValidasi($request);

        return $result['success']
            ? redirect()->back()->with('success', $result['message'])
            : redirect()->back()->with('error', $result['message']);
    }

    /**
     * Minta revisi progress.
     * Admin Umum hanya bisa minta revisi untuk progress dengan status PENDING dan REVISI.
     */
    public function revisi(Request $request, DataEntryProgress $progress): RedirectResponse
    {
        $result = $this->service->revisi($request, $progress);

        return $result['success']
            ? redirect()->back()->with('success', $result['message'])
            : redirect()->back()->with('error', $result['message']);
    }

    /**
     * Tolak progress.
     * Admin Umum hanya bisa menolak progress dengan status PENDING dan REVISI.
     */
    public function tolak(Request $request, DataEntryProgress $progress): RedirectResponse
    {
        $result = $this->service->tolak($request, $progress);

        return $result['success']
            ? redirect()->back()->with('success', $result['message'])
            : redirect()->back()->with('error', $result['message']);
    }
}
