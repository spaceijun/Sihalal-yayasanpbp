@extends('layouts.app')
@section('template_title') Detail User @endsection
@section('content')
<div class="adm-page">
    @include('layouts.messages')
    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Detail Pengguna</h1>
            <p>Informasi akun <strong>{{ $user->name }}</strong></p>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('superadmin.users.edit', $user->hashed_id) }}" class="adm-btn-primary">
                <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit
            </a>
            <a href="{{ route('superadmin.users.index') }}" class="adm-btn-secondary">
                <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg> Kembali
            </a>
        </div>
    </div>
    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Profil Pengguna
            </div>
        </div>
        <div style="padding:0 20px 20px;">
            <div class="adm-info-list">
                <div class="adm-info-row">
                    <span class="adm-info-key">Nama</span>
                    <span class="adm-info-val">{{ $user->name }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Email</span>
                    <span class="adm-info-val" style="color:var(--adm-blue);">{{ $user->email }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Telepon</span>
                    <span class="adm-info-val adm-mono" style="font-size:12.5px;">{{ $user->telephone ?: 'â€”' }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Role</span>
                    <span class="adm-info-val">
                        @if (strtolower($user->role) === 'superadmin')
                            <span class="adm-badge" style="background:#FFF1F2;color:#BE123C;border:1px solid #FECDD3;">superadmin</span>
                        @elseif (strtolower($user->role) === 'admin')
                            <span class="adm-badge adm-badge-info">admin</span>
                        @else
                            <span class="adm-badge" style="background:#F1F5F9;color:#475569;border:1px solid #CBD5E1;">{{ $user->role }}</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

