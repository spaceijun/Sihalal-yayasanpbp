<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Traits\HasRoutePrefix;
use App\Models\RecruitmentPost;
use App\Models\Superadmin\Koordinator;
use App\Services\Superadmin\RecruitmentPostService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RecruitmentPostController extends Controller
{
    use HasRoutePrefix;

    public function __construct(private RecruitmentPostService $service) {}

    /**
     * Daftar semua lowongan pekerjaan (index + datatable).
     */
    public function index()
    {
        $routePrefix = $this->routePrefix();
        return view('superadmin.recruitment-posts.index', compact('routePrefix'));
    }

    /**
     * DataTables JSON endpoint.
     */
    public function data(Request $request)
    {
        $query = RecruitmentPost::withCount('recruitments');

        // Admin Umum hanya melihat lowongan yang dia buat
        if (auth()->user()->role === 'admin_umum') {
            $query->where('created_by', auth()->id());
        }

        if ($request->filled('posisi')) {
            $query->where('posisi', $request->posisi);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif' ? 1 : 0);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama_cell', function ($p) {
                $showUrl  = route($this->routePrefix() . '.recruitment-posts.show', $p->hashed_id);

                return '<div class="adm-name-cell">
                    <div>
                        <a href="' . $showUrl . '" style="font-weight:600;font-size:13px;color:var(--adm-text-dark);text-decoration:none;">' . e($p->nama_loker) . '</a>
                        <span style="font-size:11px;color:var(--adm-text-faint);display:block;">' . e($p->slug) . '</span>
                    </div>
                </div>';
            })
            ->addColumn('posisi_badge', function ($p) {
                $color = match ($p->posisi) {
                    'PENDAMPING'  => 'adm-badge-info',
                    'DATA ENTRY'  => 'adm-badge-pending',
                    'ADMIN UMUM'  => 'adm-badge-success',
                    default       => 'adm-badge-info',
                };

                return '<span class="adm-badge ' . $color . '">' . e($p->posisi) . '</span>';
            })
            ->addColumn('status_badge', function ($p) {
                return $p->is_active
                    ? '<span class="adm-badge adm-badge-success"><span class="dot"></span>Aktif</span>'
                    : '<span class="adm-badge adm-badge-nonaktif"><span class="dot"></span>Nonaktif</span>';
            })
            ->addColumn('link_publik', function ($p) {
                if ($p->is_active) {
                    $url = $p->public_url;

                    return '<a href="' . $url . '" target="_blank" class="adm-link-copy" style="font-size:11px;word-break:break-all;">'
                        . e($url)
                        . '</a>';
                }

                return '<span style="color:var(--adm-text-faint);font-size:12px;">— (nonaktif)</span>';
            })
            ->addColumn('jumlah_pelamar', function ($p) {
                $count = $p->recruitments_count ?? 0;

                return '<span class="adm-badge" style="background:var(--adm-bg-muted);color:var(--adm-text-dark);font-weight:700;">' . $count . '</span>';
            })
            ->addColumn('aksi', function ($p) {
                $editUrl   = route($this->routePrefix() . '.recruitment-posts.edit', $p->hashed_id);
                $showUrl   = route($this->routePrefix() . '.recruitment-posts.show', $p->hashed_id);
                $toggleUrl = route($this->routePrefix() . '.recruitment-posts.toggle', $p->hashed_id);
                $deleteUrl = route($this->routePrefix() . '.recruitment-posts.destroy', $p->hashed_id);
                $toggleLabel = $p->is_active ? 'Nonaktifkan' : 'Aktifkan';
                $toggleIcon  = $p->is_active
                    ? '<svg viewBox="0 0 24 24"><rect x="1" y="5" width="22" height="14" rx="7" ry="7"/><circle cx="16" cy="12" r="3" fill="currentColor"/></svg>'
                    : '<svg viewBox="0 0 24 24"><rect x="1" y="5" width="22" height="14" rx="7" ry="7"/><circle cx="8" cy="12" r="3" fill="currentColor"/></svg>';

                return '<div class="adm-actions">
                    <a class="adm-btn primary icon-only" href="' . $showUrl . '" title="Pelamar">
                        <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </a>
                    <a class="adm-btn success icon-only" href="' . $editUrl . '" title="Edit">
                        <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                    <form action="' . $toggleUrl . '" method="POST" class="d-inline">
                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                        <input type="hidden" name="_method" value="PATCH">
                        <button type="submit" class="adm-btn ' . ($p->is_active ? 'warning' : '') . ' icon-only" title="' . $toggleLabel . '">
                            ' . $toggleIcon . '
                        </button>
                    </form>
                    <form action="' . $deleteUrl . '" method="POST" class="d-inline form-delete">
                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="adm-btn danger icon-only" title="Hapus">
                            <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                        </button>
                    </form>
                </div>';
            })
            ->rawColumns(['nama_cell', 'posisi_badge', 'status_badge', 'link_publik', 'jumlah_pelamar', 'aksi'])
            ->make(true);
    }

    /**
     * Form create lowongan.
     */
    public function create()
    {
        $routePrefix = $this->routePrefix();
        return view('superadmin.recruitment-posts.create', compact('routePrefix'));
    }

    /**
     * Simpan lowongan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_loker'                 => 'required|string|max:255',
            'posisi'                     => 'required|in:PENDAMPING,DATA ENTRY,ADMIN UMUM',
            'deskripsi'                  => 'nullable|string',
            'jobdesk'                    => 'nullable|string',
            'template_pakta_integritas'  => 'nullable|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:10240',
            'is_active'                  => 'nullable|boolean',
            'tanggal_buka'               => 'nullable|date',
            'tanggal_tutup'              => 'nullable|date|after_or_equal:tanggal_buka',
            'requirements'              => 'nullable|array',
        ]);

        $data = $request->only(['nama_loker', 'posisi', 'deskripsi', 'jobdesk', 'tanggal_buka', 'tanggal_tutup']);
        $data['is_active'] = $request->boolean('is_active');
        $data['created_by'] = auth()->id();

        if ($request->hasFile('template_pakta_integritas')) {
            $data['template_pakta_integritas'] = $request->file('template_pakta_integritas');
        }

        if ($request->filled('requirements')) {
            $data['requirements'] = $this->service->parseRequirements($request->input('requirements'));
        } else {
            $data['requirements'] = [];
        }

        $post = $this->service->create($data);

        return redirect()
            ->route($this->routePrefix() . '.recruitment-posts.index')
            ->with('success', "Lowongan \"{$post->nama_loker}\" berhasil dibuat!");
    }

    /**
     * Detail lowongan + daftar pelamar.
     */
    public function show($hashedId)
    {
        $post        = RecruitmentPost::findByHashedIdOrFail($hashedId);
        $koordinators = Koordinator::all();

        $routePrefix = $this->routePrefix();

        return view('superadmin.recruitment-posts.show', compact('post', 'koordinators', 'routePrefix'));
    }

    /**
     * Form edit lowongan.
     */
    public function edit($hashedId)
    {
        $post = RecruitmentPost::findByHashedIdOrFail($hashedId);

        $routePrefix = $this->routePrefix();

        return view('superadmin.recruitment-posts.edit', compact('post', 'routePrefix'));
    }

    /**
     * Update lowongan.
     */
    public function update(Request $request, $hashedId)
    {
        $request->validate([
            'nama_loker'                 => 'required|string|max:255',
            'posisi'                     => 'required|in:PENDAMPING,DATA ENTRY,ADMIN UMUM',
            'deskripsi'                  => 'nullable|string',
            'jobdesk'                    => 'nullable|string',
            'template_pakta_integritas'  => 'nullable|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:10240',
            'is_active'                  => 'nullable|boolean',
            'tanggal_buka'               => 'nullable|date',
            'tanggal_tutup'              => 'nullable|date|after_or_equal:tanggal_buka',
            'requirements'              => 'nullable|array',
        ]);

        $post = RecruitmentPost::findByHashedIdOrFail($hashedId);

        $data = $request->only(['nama_loker', 'posisi', 'deskripsi', 'jobdesk', 'tanggal_buka', 'tanggal_tutup']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('template_pakta_integritas')) {
            $data['template_pakta_integritas'] = $request->file('template_pakta_integritas');
        } elseif ($request->boolean('delete_template')) {
            $data['template_pakta_integritas'] = null;
        }

        if ($request->filled('requirements')) {
            $data['requirements'] = $this->service->parseRequirements($request->input('requirements'));
        } else {
            $data['requirements'] = [];
        }

        $this->service->update($post, $data);

        return redirect()
            ->route($this->routePrefix() . '.recruitment-posts.index')
            ->with('success', "Lowongan \"{$post->nama_loker}\" berhasil diperbarui!");
    }

    /**
     * Toggle aktif / nonaktif lowongan.
     */
    public function toggle($hashedId)
    {
        $post = RecruitmentPost::findByHashedIdOrFail($hashedId);
        $this->service->toggleActive($post);

        $label = $post->is_active ? 'dinonaktifkan' : 'diaktifkan';

        return redirect()->back()->with('success', "Lowongan \"{$post->nama_loker}\" berhasil {$label}!");
    }

    /**
     * Hapus lowongan.
     */
    public function destroy($hashedId)
    {
        $post = RecruitmentPost::findByHashedIdOrFail($hashedId);
        $nama = $post->nama_loker;
        $this->service->delete($post);

        return redirect()
            ->route($this->routePrefix() . '.recruitment-posts.index')
            ->with('success', "Lowongan \"{$nama}\" berhasil dihapus!");
    }
}
