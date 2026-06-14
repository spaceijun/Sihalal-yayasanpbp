<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Traits\HasRoutePrefix;
use App\Models\TicketPendamping;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class TicketPendampingController extends Controller
{
    use HasRoutePrefix;

    public function index(Request $request): View
    {
        $counts = [
            'all' => TicketPendamping::count(),
            'open' => TicketPendamping::where('status', 'Open')->count(),
            'proses' => TicketPendamping::where('status', 'Proses')->count(),
            'closed' => TicketPendamping::where('status', 'Closed')->count(),
        ];

        $routePrefix = $this->routePrefix();

        return view('superadmin.ticket-pendamping.index', compact('counts', 'routePrefix'));
    }

    /**
     * Yajra DataTables JSON endpoint.
     */
    public function data(Request $request)
    {
        $query = TicketPendamping::with(['user.enumerator', 'dataLapangan:id,nama_pu'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn(
                'no_tiket_cell',
                fn ($t) => '<span class="adm-mono" style="font-size:12px;white-space:nowrap;">'.e($t->no_tiket).'</span>'
            )
            ->addColumn('enumerator_cell', function ($t) {
                $name = $t->user?->enumerator?->nama_lengkap ?? $t->user?->name ?? '-';
                $init = strtoupper(substr($name, 0, 2));

                return '<div class="adm-name-cell">
                    <div class="adm-avatar">'.e($init).'</div>
                    <strong>'.e($name).'</strong>
                </div>';
            })
            ->addColumn(
                'nama_pu_cell',
                fn ($t) => $t->dataLapangan
                    ? '<span style="font-size:12.5px;">'.e($t->dataLapangan->nama_pu).'</span>'
                    : '<span style="color:var(--adm-text-faint);">—</span>'
            )
            ->addColumn(
                'isi_kendala_cell',
                fn ($t) => '<span style="font-size:12.5px;">'.e(Str::limit($t->isi_kendala, 70)).'</span>'
            )
            ->addColumn('status_badge', fn ($t) => match ($t->status) {
                'Open' => '<span class="adm-badge adm-badge-pending"><span class="dot"></span>Open</span>',
                'Proses' => '<span class="adm-badge adm-badge-info"><span class="dot"></span>Proses</span>',
                'Closed' => '<span class="adm-badge adm-badge-terbit"><span class="dot"></span>Closed</span>',
                default => '<span class="adm-badge adm-badge-nonaktif">—</span>',
            })
            ->addColumn(
                'tanggal',
                fn ($t) => $t->created_at?->format('d/m/Y H:i') ?? '-'
            )
            ->addColumn('aksi', function ($t) {
                $showUrl = route($this->routePrefix() . '.ticket-pendampings.show', $t->hashed_id);
                $deleteUrl = route($this->routePrefix() . '.ticket-pendampings.destroy', $t->hashed_id);

                return '<div class="adm-actions" style="justify-content:center;gap:4px;">
                    <a href="'.$showUrl.'" class="adm-btn primary icon-only" title="Lihat Detail">
                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                    <button class="adm-btn danger icon-only btn-delete"
                        data-url="'.$deleteUrl.'" title="Hapus">
                        <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    </button>
                </div>';
            })
            ->rawColumns(['no_tiket_cell', 'enumerator_cell', 'nama_pu_cell', 'isi_kendala_cell', 'status_badge', 'aksi'])
            ->make(true);
    }

    public function show(string $hashedId): View
    {
        $ticket = TicketPendamping::findByHashedIdOrFail($hashedId);
        $ticket->load(['user.enumerator', 'dataLapangan:id,nama_pu,nik,status,alamat']);

        $routePrefix = $this->routePrefix();

        return view('superadmin.ticket-pendamping.show', compact('ticket', 'routePrefix'));
    }

    /**
     * PATCH superadmin/ticket-pendampings/{id}/status
     */
    public function updateStatus(Request $request, string $hashedId): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:Open,Proses,Closed',
        ]);

        $ticket = TicketPendamping::findByHashedId($hashedId);
        $ticket->update(['status' => $request->status]);

        return redirect()->back()
            ->with('success', 'Status tiket diperbarui menjadi '.$request->status.'.');
    }

    public function destroy(string $hashedId): RedirectResponse
    {
        TicketPendamping::findByHashedId($hashedId)->delete();

        return redirect()->route($this->routePrefix() . '.ticket-pendampings.index')
            ->with('success', 'Tiket pendamping berhasil dihapus.');
    }
}
