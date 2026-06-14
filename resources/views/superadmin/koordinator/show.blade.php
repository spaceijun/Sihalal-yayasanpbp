@extends('layouts.app')
@section('template_title') Detail Koordinator @endsection
@section('content')
<div class="adm-page">
    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Detail Koordinator</h1>
            <p>Informasi lengkap akun koordinator</p>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route($routePrefix . '.koordinators.edit', $koordinator->hashed_id) }}" class="adm-btn-primary">
                <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit
            </a>
            <a href="{{ route($routePrefix . '.koordinators.index') }}" class="adm-btn-secondary">
                <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg> Kembali
            </a>
        </div>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                Informasi Koordinator
            </div>
        </div>
        <div style="padding:0 20px;">
            <div class="adm-info-list">
                <div class="adm-info-row">
                    <span class="adm-info-key">Nama Lengkap</span>
                    <span class="adm-info-val">{{ $koordinator->nama_lengkap }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Email</span>
                    <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $koordinator->email }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Telephone</span>
                    <span class="adm-info-val adm-mono">{{ $koordinator->telephone }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Alamat</span>
                    <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $koordinator->alamat ?: 'â€”' }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Fee Enum</span>
                    <span class="adm-info-val adm-mono">Rp {{ number_format($koordinator->fee_enum, 0, ',', '.') }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Status</span>
                    <span class="adm-info-val">
                        @if ($koordinator->status === 'Aktif')
                            <span class="adm-badge adm-badge-success">Aktif</span>
                        @else
                            <span class="adm-badge adm-badge-nonaktif">Tidak Aktif</span>
                        @endif
                    </span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">User ID</span>
                    <span class="adm-info-val adm-mono" style="font-size:12px;">{{ $koordinator->user_id }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

