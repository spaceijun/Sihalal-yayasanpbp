<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Traits\HasRoutePrefix;
use App\Models\Cashflow;
use App\Services\Superadmin\CashflowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CashflowRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class CashflowController extends Controller
{
    use HasRoutePrefix;

    public function __construct(private CashflowService $service) {}

    /**
     * Display a listing of the resource.
     *
     * Also serves as the DataTables server-side endpoint (when requested via
     * AJAX) so no extra route needs to be registered for the resource index.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->data($request);
        }

        $routePrefix = $this->routePrefix();
        $totalCashflows = Cashflow::count();

        return view('superadmin.arus-kas.index', compact('routePrefix', 'totalCashflows'));
    }

    /**
     * Return DataTables JSON for cashflow listing.
     */
    public function data(Request $request): JsonResponse
    {
        $query = Cashflow::query();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tipe_badge', function ($cashflow) {
                return match ($cashflow->tipe) {
                    'Pemasukan' => '<span class="adm-badge adm-badge-success"><span class="dot"></span>Pemasukan</span>',
                    'Pengeluaran' => '<span class="adm-badge adm-badge-danger"><span class="dot"></span>Pengeluaran</span>',
                    'Kas' => '<span class="adm-badge adm-badge-pending"><span class="dot"></span>Kas</span>',
                    default => '<span class="adm-badge adm-badge-info">'.e($cashflow->tipe).'</span>',
                };
            })
            ->addColumn('jumlah_fmt', fn ($cashflow) => 'Rp '.number_format($cashflow->jumlah, 0, ',', '.'))
            ->addColumn('tanggal_fmt', fn ($cashflow) => \Carbon\Carbon::parse($cashflow->tanggal)->format('d M Y'))
            ->addColumn('keterangan_fmt', fn ($cashflow) => $cashflow->keterangan ?: '-')
            ->addColumn('aksi', function ($cashflow) {
                $editUrl = route($this->routePrefix() . '.arus-kas.edit', $cashflow->hashed_id);
                $label = $cashflow->tipe.' - '.\Carbon\Carbon::parse($cashflow->tanggal)->format('d M Y');

                return '<div class="adm-actions">
                    <a class="adm-btn primary icon-only" href="'.$editUrl.'" title="Edit">
                        <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                    <button type="button" class="adm-btn danger icon-only" title="Hapus"
                        onclick="confirmDeleteCashflow(\''.$cashflow->hashed_id.'\', \''.e(addslashes($label)).'\')">
                        <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    </button>
                </div>';
            })
            ->rawColumns(['tipe_badge', 'keterangan_fmt', 'aksi'])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $query = Cashflow::orderBy('created_at', 'asc');

        if ($request->filled('bulan')) {
            $query->whereMonth('created_at', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }

        $cashflows = $query->get();

        return response()->json($cashflows);
    }

    public function cashflows()
    {
        $cashflows = Cashflow::orderBy('created_at', 'asc')->get();
        $routePrefix = $this->routePrefix();
        return view('superadmin.arus-kas.cashflows', compact('cashflows', 'routePrefix'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $cashflow = new Cashflow();
        $routePrefix = $this->routePrefix();

        return view('superadmin.arus-kas.create', compact('cashflow', 'routePrefix'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CashflowRequest $request): RedirectResponse
    {
        try {
            $this->service->store($request->validated());

            return Redirect::route('superadmin.arus-kas.index')
                ->with('success', 'Cashflow created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan cashflow: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($hashedId): View
    {
        $cashflow = Cashflow::findByHashedIdOrFail($hashedId);
        $routePrefix = $this->routePrefix();

        return view('superadmin.arus-kas.show', compact('cashflow', 'routePrefix'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($hashedId): View
    {
        $cashflow = Cashflow::findByHashedIdOrFail($hashedId);
        $routePrefix = $this->routePrefix();

        return view('superadmin.arus-kas.edit', compact('cashflow', 'routePrefix'));
    }

    /**
     * Update the specified resource in storage.
     *
     * NOTE: uses $hashedId (not implicit model binding) because the
     * "arus-kas" resource route's auto-generated wildcard is {arus_ka}
     * (Laravel singularizes "arus_kas" oddly), which never matches a
     * $cashflow-named parameter — implicit binding would silently fail and
     * resolve an empty, non-persisted model, making every update a no-op.
     */
    public function update(CashflowRequest $request, $hashedId): RedirectResponse
    {
        try {
            $cashflow = Cashflow::findByHashedIdOrFail($hashedId);

            $this->service->update($cashflow, $request->validated());

            return Redirect::route('superadmin.arus-kas.index')
                ->with('success', 'Cashflow updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui cashflow: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($hashedId): JsonResponse
    {
        try {
            $cashflow = Cashflow::findByHashedIdOrFail($hashedId);

            $this->service->delete($cashflow);

            return response()->json(['message' => 'Cashflow deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
