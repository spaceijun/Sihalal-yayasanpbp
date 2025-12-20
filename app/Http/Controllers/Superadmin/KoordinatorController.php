<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Superadmin\Koordinator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\KoordinatorRequest;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class KoordinatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $koordinators = Koordinator::paginate();

        return view('superadmin.koordinator.index', compact('koordinators'))
            ->with('i', ($request->input('page', 1) - 1) * $koordinators->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $koordinator = new Koordinator();

        return view('superadmin.koordinator.create', compact('koordinator'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KoordinatorRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->nama_lengkap,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'password' => bcrypt($request->password),
            'role' => 'koordinator',
        ]);

        $user->assignRole('koordinator');

        Koordinator::create([
            'user_id' => $user->id,
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'alamat' => $request->alamat,
            'status' => $request->status,
        ]);

        return Redirect::route('superadmin.koordinators.index')
            ->with('success', 'Koordinator created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $koordinator = Koordinator::find($id);

        return view('superadmin.koordinator.show', compact('koordinator'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $koordinator = Koordinator::find($id);

        return view('superadmin.koordinator.edit', compact('koordinator'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(KoordinatorRequest $request, Koordinator $koordinator): RedirectResponse
    {
        $koordinator->update($request->validated());

        return Redirect::route('superadmin.koordinators.index')
            ->with('success', 'Koordinator updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Koordinator::find($id)->delete();

        return Redirect::route('superadmin.koordinators.index')
            ->with('success', 'Koordinator deleted successfully');
    }
}
