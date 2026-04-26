@extends('layouts.app')
@section('template_title') Manajemen User @endsection
@section('content')
<div class="adm-page">
    @include('layouts.messages')

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Manajemen User</h1>
            <p>Kelola akun dan hak akses pengguna sistem</p>
        </div>
        <a href="{{ route('superadmin.users.create') }}" class="adm-btn-primary">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah User
        </a>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Daftar Pengguna
                <span class="adm-count-badge">{{ $users->total() }}</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th style="width:44px">#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th class="tc">Role</th>
                        <th class="tc" style="width:110px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td><span class="adm-rownum">{{ ++$i }}</span></td>
                            <td>
                                <div class="adm-name-cell">
                                    <div class="adm-avatar" style="background:var(--adm-blue-lt);color:var(--adm-blue);">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div style="font-weight:600;font-size:13px;">{{ $user->name }}</div>
                                </div>
                            </td>
                            <td style="font-size:12.5px;color:var(--adm-text-muted);">{{ $user->email }}</td>
                            <td class="adm-mono" style="font-size:12px;">{{ $user->telephone ?: 'â€”' }}</td>
                            <td class="tc">
                                @if (strtolower($user->role) === 'superadmin')
                                    <span class="adm-badge" style="background:#FFF1F2;color:#BE123C;border:1px solid #FECDD3;">superadmin</span>
                                @elseif (strtolower($user->role) === 'admin')
                                    <span class="adm-badge adm-badge-info">admin</span>
                                @else
                                    <span class="adm-badge" style="background:#F1F5F9;color:#475569;border:1px solid #CBD5E1;">{{ $user->role }}</span>
                                @endif
                            </td>
                            <td class="tc">
                                <div class="adm-actions" style="justify-content:center;gap:4px;">
                                    <a class="adm-btn primary icon-only"
                                        href="{{ route('superadmin.users.show', $user->hashed_id) }}" title="Lihat">
                                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <a class="adm-btn warning icon-only"
                                        href="{{ route('superadmin.users.edit', $user->hashed_id) }}" title="Edit">
                                        <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </a>
                                    <form action="{{ route('superadmin.users.destroy', $user->hashed_id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="adm-btn danger icon-only" title="Hapus"
                                            onclick="return confirm('Yakin hapus user {{ $user->name }}?')">
                                            <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="adm-card-footer">
            <span class="adm-footer-info">
                Menampilkan {{ $users->firstItem() ?? 0 }}â€“{{ $users->lastItem() ?? 0 }}
                dari {{ $users->total() }} pengguna
            </span>
            @include('layouts.pagination', ['paginator' => $users])
        </div>
    </div>
</div>
@endsection

