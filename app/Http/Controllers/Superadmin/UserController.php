<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Traits\HasRoutePrefix;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    use HasRoutePrefix;

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $routePrefix = $this->routePrefix();

        return view('superadmin.user.index', compact('routePrefix'));
    }

    /**
     * Return DataTables JSON for user listing.
     */
    public function data(Request $request)
    {
        $query = User::query();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama_cell', function ($u) {
                $inisial = strtoupper(substr($u->name, 0, 2));

                return '<div class="adm-name-cell">
                    <div class="adm-avatar" style="background:var(--adm-blue-lt);color:var(--adm-blue);">'.$inisial.'</div>
                    <div style="font-weight:600;font-size:13px;">'.e($u->name).'</div>
                </div>';
            })
            ->addColumn('role_badge', function ($u) {
                $role = strtolower($u->role);
                if ($role === 'superadmin') {
                    return '<span class="adm-badge" style="background:#FFF1F2;color:#BE123C;border:1px solid #FECDD3;">superadmin</span>';
                } elseif ($role === 'admin') {
                    return '<span class="adm-badge adm-badge-info">admin</span>';
                }

                return '<span class="adm-badge" style="background:#F1F5F9;color:#475569;border:1px solid #CBD5E1;">'.e($u->role).'</span>';
            })
            ->addColumn('aksi', function ($u) {
                $showUrl = route('superadmin.users.show', $u->hashed_id);
                $editUrl = route('superadmin.users.edit', $u->hashed_id);
                $deleteUrl = route('superadmin.users.destroy', $u->hashed_id);

                return '<div class="adm-actions" style="justify-content:center;gap:4px;">
                    <a class="adm-btn primary icon-only" href="'.$showUrl.'" title="Lihat">
                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                    <a class="adm-btn warning icon-only" href="'.$editUrl.'" title="Edit">
                        <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                    <form action="'.$deleteUrl.'" method="POST" class="d-inline" onsubmit="return confirm(\'Yakin hapus user '.e($u->name).'?\')">
                        <input type="hidden" name="_token" value="'.csrf_token().'">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="adm-btn danger icon-only" title="Hapus">
                            <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                        </button>
                    </form>
                </div>';
            })
            ->rawColumns(['nama_cell', 'role_badge', 'aksi'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $user = new User;
        $routePrefix = $this->routePrefix();

        return view('superadmin.user.create', compact('user', 'routePrefix'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request): RedirectResponse
    {
        User::create($request->validated());

        return Redirect::route('superadmin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($hashedId): View
    {
        $user = User::findByHashedIdOrFail($hashedId);
        $routePrefix = $this->routePrefix();

        return view('superadmin.user.show', compact('user', 'routePrefix'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($hashedId): View
    {
        $user = User::findByHashedIdOrFail($hashedId);
        $routePrefix = $this->routePrefix();

        return view('superadmin.user.edit', compact('user', 'routePrefix'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $user->update($request->validated());

        return Redirect::route('superadmin.users.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy($hashedId): RedirectResponse
    {
        User::findByHashedIdOrFail($hashedId)->delete();

        return Redirect::route('superadmin.users.index')
            ->with('success', 'User deleted successfully');
    }
}
