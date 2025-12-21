<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Enumerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\EnumeratorRequest;
use App\Models\Superadmin\Koordinator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class EnumeratorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $enumerators = Enumerator::with('koordinator')->paginate();

        return view('superadmin.enumerator.index', compact('enumerators'))
            ->with('i', ($request->input('page', 1) - 1) * $enumerators->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $enumerator = new Enumerator();
        $koordinators = Koordinator::all();

        return view('superadmin.enumerator.create', compact('enumerator', 'koordinators'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EnumeratorRequest $request): RedirectResponse
    {
        Enumerator::create($request->validated());

        return Redirect::route('superadmin.enumerators.index')
            ->with('success', 'Enumerator created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $enumerator = Enumerator::find($id);

        return view('superadmin.enumerator.show', compact('enumerator'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $enumerator = Enumerator::find($id);
        $koordinators = Koordinator::all();

        return view('superadmin.enumerator.edit', compact('enumerator', 'koordinators'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EnumeratorRequest $request, Enumerator $enumerator): RedirectResponse
    {
        $enumerator->update($request->validated());

        return Redirect::route('superadmin.enumerators.index')
            ->with('success', 'Enumerator updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Enumerator::find($id)->delete();

        return Redirect::route('superadmin.enumerators.index')
            ->with('success', 'Enumerator deleted successfully');
    }
}
