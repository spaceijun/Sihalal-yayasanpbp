@extends('layouts.app')
@section('template_title') Detail Verifikator @endsection
@section('content')
<div class="adm-page">
    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Detail Verifikator</h1>
            <p>Informasi lengkap petugas verifikator</p>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route($routePrefix . '.verifikators.edit', $verifikator->hashed_id) }}" class="adm-btn-primary">
                <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit
            </a>
            <a href="{{ route($routePrefix . '.verifikators.index') }}" class="adm-btn-secondary">
                <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg> Kembali
            </a>
        </div>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Informasi Verifikator
            </div>
        </div>
        <div style="padding:0 20px;">
            <div class="adm-info-list">
                <div class="adm-info-row">
                    <span class="adm-info-key">Nama Lengkap</span>
                    <span class="adm-info-val">{{ $verifikator->nama_lengkap }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Telephone</span>
                    <span class="adm-info-val adm-mono">{{ $verifikator->telephone }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Alamat Lengkap</span>
                    <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $verifikator->alamat_lengkap ?: 'â€”' }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Rate Per Data</span>
                    <span class="adm-info-val adm-mono">Rp {{ number_format($verifikator->rate_per_data, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

