<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\VerifikatorPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\VerifikatorPaymentRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class VerifikatorPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $verifikatorPayments = VerifikatorPayment::paginate();

        return view('superadmin.verifikator-payment.index', compact('verifikatorPayments'))
            ->with('i', ($request->input('page', 1) - 1) * $verifikatorPayments->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $verifikatorPayment = new VerifikatorPayment();

        return view('superadmin.verifikator-payment.create', compact('verifikatorPayment'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VerifikatorPaymentRequest $request): RedirectResponse
    {
        VerifikatorPayment::create($request->validated());

        return Redirect::route('superadmin.verifikator-payments.index')
            ->with('success', 'VerifikatorPayment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $verifikatorPayment = VerifikatorPayment::find($id);

        return view('superadmin.verifikator-payment.show', compact('verifikatorPayment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $verifikatorPayment = VerifikatorPayment::find($id);

        return view('superadmin.verifikator-payment.edit', compact('verifikatorPayment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VerifikatorPaymentRequest $request, VerifikatorPayment $verifikatorPayment): RedirectResponse
    {
        $verifikatorPayment->update($request->validated());

        return Redirect::route('superadmin.verifikator-payments.index')
            ->with('success', 'VerifikatorPayment updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        VerifikatorPayment::find($id)->delete();

        return Redirect::route('superadmin.verifikator-payments.index')
            ->with('success', 'VerifikatorPayment deleted successfully');
    }
}
