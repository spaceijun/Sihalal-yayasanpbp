<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Traits\HasRoutePrefix;
use App\Models\Cashflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CashflowRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class CashflowController extends Controller
{
    use HasRoutePrefix;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $cashflows = Cashflow::paginate();
        $routePrefix = $this->routePrefix();

        return view('superadmin.arus-kas.index', compact('cashflows', 'routePrefix'))
            ->with('i', ($request->input('page', 1) - 1) * $cashflows->perPage());
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
        Cashflow::create($request->validated());

        return Redirect::route('superadmin.arus-kas.index')
            ->with('success', 'Cashflow created successfully.');
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
     */
    public function update(CashflowRequest $request, Cashflow $cashflow): RedirectResponse
    {
        $cashflow->update($request->validated());

        return Redirect::route('superadmin.arus-kas.index')
            ->with('success', 'Cashflow updated successfully');
    }

    public function destroy($hashedId): RedirectResponse
    {
        Cashflow::findByHashedIdOrFail($hashedId)->delete();

        return Redirect::route('superadmin.arus-kas.index')
            ->with('success', 'Cashflow deleted successfully');
    }
}
