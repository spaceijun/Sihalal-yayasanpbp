<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Cashflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CashflowRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class CashflowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $cashflows = Cashflow::paginate();

        return view('superadmin.arus-kas.index', compact('cashflows'))
            ->with('i', ($request->input('page', 1) - 1) * $cashflows->perPage());
    }

    public function getData()
    {
        $cashflows = Cashflow::orderBy('created_at', 'asc')->get();
        return response()->json($cashflows);
    }

    public function cashflows()
    {
        $cashflows = Cashflow::orderBy('created_at', 'asc')->get();
        return view('superadmin.arus-kas.cashflows', compact('cashflows'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $cashflow = new Cashflow();

        return view('superadmin.arus-kas.create', compact('cashflow'));
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
    public function show($id): View
    {
        $cashflow = Cashflow::find($id);

        return view('superadmin.arus-kas.show', compact('cashflow'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $cashflow = Cashflow::find($id);

        return view('superadmin.arus-kas.edit', compact('cashflow'));
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

    public function destroy($id): RedirectResponse
    {
        Cashflow::find($id)->delete();

        return Redirect::route('superadmin.arus-kas.index')
            ->with('success', 'Cashflow deleted successfully');
    }
}
