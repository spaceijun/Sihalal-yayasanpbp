<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\VerifikatorPayment;
use App\Services\Superadmin\VerifikatorPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\VerifikatorPaymentRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class VerifikatorPaymentController extends Controller
{
    public function __construct(private VerifikatorPaymentService $service) {}

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

        return view('superadmin.verifikator-payment.index');
    }

    /**
     * Return DataTables JSON for verifikator payment listing.
     */
    public function data(Request $request): JsonResponse
    {
        $query = VerifikatorPayment::query();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($verifikatorPayment) {
                $editUrl = route('superadmin.verifikator-payments.edit', $verifikatorPayment->id);
                $label = 'Payment #'.$verifikatorPayment->id;

                return '<div class="adm-actions">
                    <a class="adm-btn primary icon-only" href="'.$editUrl.'" title="Edit">
                        <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                    <button type="button" class="adm-btn danger icon-only" title="Hapus"
                        onclick="confirmDeleteVerifikatorPayment('.$verifikatorPayment->id.', \''.e(addslashes($label)).'\')">
                        <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    </button>
                </div>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
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
        try {
            $this->service->store($request->validated());

            return Redirect::route('superadmin.verifikator-payments.index')
                ->with('success', 'VerifikatorPayment created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan verifikator payment: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(VerifikatorPayment $verifikatorPayment): View
    {
        return view('superadmin.verifikator-payment.show', compact('verifikatorPayment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VerifikatorPayment $verifikatorPayment): View
    {
        return view('superadmin.verifikator-payment.edit', compact('verifikatorPayment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VerifikatorPaymentRequest $request, VerifikatorPayment $verifikatorPayment): RedirectResponse
    {
        try {
            $this->service->update($verifikatorPayment, $request->validated());

            return Redirect::route('superadmin.verifikator-payments.index')
                ->with('success', 'VerifikatorPayment updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui verifikator payment: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VerifikatorPayment $verifikatorPayment): JsonResponse
    {
        try {
            $this->service->delete($verifikatorPayment);

            return response()->json(['message' => 'VerifikatorPayment deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
