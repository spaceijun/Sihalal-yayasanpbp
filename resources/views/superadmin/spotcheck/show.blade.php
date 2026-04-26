@extends('layouts.app')
@section('template_title') Detail Spotcheck @endsection
@section('content')
<div class="adm-page">
    @include('layouts.messages')
    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Detail Spotcheck</h1>
            <p>Informasi lengkap hasil kunjungan spotcheck</p>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('superadmin.spotchecks.edit', $spotcheck->hashed_id) }}" class="adm-btn-primary">
                <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit
            </a>
            <a href="{{ route('superadmin.spotchecks.index') }}" class="adm-btn-secondary">
                <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg> Kembali
            </a>
        </div>
    </div>
    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                Informasi Spotcheck
            </div>
        </div>
        <div style="padding:0 20px 20px;">
            <div class="adm-info-list">
                <div class="adm-info-row">
                    <span class="adm-info-key">ID Data Lapangan</span>
                    <span class="adm-info-val adm-mono" style="font-size:12.5px;">{{ $spotcheck->data_lapangan_id }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Nama Spotcheck</span>
                    <span class="adm-info-val">{{ $spotcheck->nama_spotcheck }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Tanggal Spotcheck</span>
                    <span class="adm-info-val adm-mono" style="font-size:12.5px;">{{ $spotcheck->tanggal_spotcheck }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Foto PU</span>
                    <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $spotcheck->foto_pu ?: 'â€”' }}</span>
                </div>
                <div class="adm-info-row" style="align-items:flex-start;">
                    <span class="adm-info-key" style="padding-top:2px;">Hasil Spotcheck</span>
                    <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);white-space:pre-line;">{{ $spotcheck->hasil_spotcheck ?: 'â€”' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

