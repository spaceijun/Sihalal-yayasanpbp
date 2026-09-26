<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Services\Superadmin\TicketService;
use App\Traits\HasRoutePrefix;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class TicketController extends Controller
{
    use HasRoutePrefix;

    public function __construct(private TicketService $service) {}

    public function index(): View
    {
        $counts = [
            'all' => Ticket::count(),
            'open' => Ticket::where('status', 'open')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'closed' => Ticket::where('status', 'closed')->count(),
        ];

        $routePrefix = $this->routePrefix();

        return view('superadmin.ticket.index', compact('counts', 'routePrefix'));
    }

    /**
     * Yajra DataTables JSON endpoint untuk listing tiket.
     */
    public function data(Request $request)
    {
        $query = Ticket::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn(
                'no_ticket_cell',
                fn ($t) => '<span class="adm-mono" style="color:var(--adm-blue);font-weight:600;">'.e($t->no_ticket).'</span>'
            )
            ->addColumn('user_cell', function ($t) {
                $name = $t->user->name ?? '-';
                $init = strtoupper(substr($name, 0, 1));

                return '<div class="adm-name-cell">
                    <div class="adm-avatar" style="background:var(--adm-blue-lt);color:var(--adm-blue);">'.e($init).'</div>
                    <span style="font-size:13px;font-weight:600;color:var(--adm-text-dark);">'.e($name).'</span>
                </div>';
            })
            ->addColumn(
                'subject_cell',
                fn ($t) => '<span style="font-weight:600;color:var(--adm-text-dark);font-size:13px;">'.e($t->subject).'</span>'
            )
            ->addColumn('status_badge', fn ($t) => match ($t->status) {
                'open' => '<span class="adm-badge adm-badge-success"><span class="dot"></span> Open</span>',
                'in_progress' => '<span class="adm-badge adm-badge-pending"><span class="dot"></span> In Progress</span>',
                default => '<span class="adm-badge adm-badge-nonaktif"><span class="dot"></span> Closed</span>',
            })
            ->addColumn('aksi', function ($t) {
                $showUrl = route($this->routePrefix() . '.tickets.show', $t->hashed_id);

                return '<div class="adm-actions">
                    <a href="'.$showUrl.'" class="adm-btn primary icon-only" title="Lihat Detail">
                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                    <button type="button" class="adm-btn danger icon-only" title="Hapus"
                        onclick="confirmDeleteTicket(\''.$t->hashed_id.'\', \''.e(addslashes($t->no_ticket)).'\')">
                        <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    </button>
                </div>';
            })
            ->rawColumns(['no_ticket_cell', 'user_cell', 'subject_cell', 'status_badge', 'aksi'])
            ->make(true);
    }

    public function show($hashedId): View
    {
        $ticket = Ticket::findByHashedId($hashedId);
        $ticket?->load('user');

        $routePrefix = $this->routePrefix();

        return view('superadmin.ticket.show', compact('ticket', 'routePrefix'));
    }

    public function destroy($hashedId): JsonResponse
    {
        $ticket = Ticket::findByHashedId($hashedId);

        if (! $ticket) {
            return response()->json(['message' => 'Tiket tidak ditemukan'], 404);
        }

        try {
            $this->service->delete($ticket);

            return response()->json(['message' => 'Tiket berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function close($hashedId): RedirectResponse
    {
        $ticket = Ticket::findByHashedId($hashedId);

        if (! $ticket) {
            return redirect()->back()->with('error', 'Tiket tidak ditemukan.');
        }

        try {
            $this->service->close($ticket);

            return redirect()->back()->with('success', 'Tiket berhasil ditutup.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
