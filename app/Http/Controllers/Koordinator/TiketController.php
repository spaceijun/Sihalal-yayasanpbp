<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Http\Requests\KoordinatorTiketRequest;
use App\Models\DataLapangan;
use App\Models\Enumerator;
use App\Models\Superadmin\Koordinator;
use App\Models\Ticket;
use App\Services\Koordinator\TiketKoordinatorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class TiketController extends Controller
{
    public function __construct(private TiketKoordinatorService $service) {}

    /**
     * Display a listing of the resource (scoped to this koordinator's own tickets).
     */
    public function index(): View
    {
        return view('koordinator.tiket.index');
    }

    /**
     * Return DataTables JSON for ticket listing, scoped to the logged-in user.
     */
    public function data(Request $request)
    {
        $query = Ticket::where('user_id', Auth::id())->latest();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('no_ticket_cell', fn ($t) => '<span class="adm-mono" style="color:var(--adm-blue);font-weight:600;">'.e($t->no_ticket).'</span>')
            ->addColumn('kategori_badge', fn ($t) => '<span class="adm-badge adm-badge-indigo">'.e($t->kategori).'</span>')
            ->addColumn('subject_cell', fn ($t) => e($t->subject))
            ->addColumn('status_badge', fn ($t) => match ($t->status) {
                'open' => '<span class="adm-badge adm-badge-pending">Open</span>',
                'in_progress' => '<span class="adm-badge adm-badge-info">In Progress</span>',
                default => '<span class="adm-badge adm-badge-success">Closed</span>',
            })
            ->addColumn('tanggal_fmt', fn ($t) => $t->created_at?->format('d M Y, H:i') ?? '-')
            ->addColumn('aksi', function ($t) {
                $showUrl = route('koordinator.tiket.show', $t->hashed_id);

                return '<a href="'.$showUrl.'" class="adm-btn primary icon-only" title="Detail">
                    <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </a>';
            })
            ->rawColumns(['no_ticket_cell', 'kategori_badge', 'status_badge', 'aksi'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        /** @var Koordinator $koordinator */
        $koordinator = Auth::user()->koordinator;

        $enumerators = Enumerator::where('koordinator_id', $koordinator->id)->orderBy('nama_lengkap')->get();
        $enumeratorIds = $enumerators->pluck('id');
        $dataLapangans = DataLapangan::whereIn('enumerator_id', $enumeratorIds)
            ->latest()
            ->limit(300)
            ->get(['id', 'no_registrasi', 'nama_pu']);

        return view('koordinator.tiket.create', compact('enumerators', 'dataLapangans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KoordinatorTiketRequest $request): RedirectResponse
    {
        try {
            $ticket = $this->service->store(Auth::id(), $request->validated(), $request->file('file'));

            return Redirect::route('koordinator.tiket.show', $ticket->hashed_id)
                ->with('success', 'Tiket berhasil dibuat');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat tiket: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket): View
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        $ticket->loadMissing('dataLapangan', 'enumerator');

        return view('koordinator.tiket.show', compact('ticket'));
    }
}
