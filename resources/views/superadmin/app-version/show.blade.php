@extends('layouts.app')
@section('template_title')
    Detail App Version
@endsection

@section('content')
<div class="adm-page">

    {{-- ── PAGE HEADER ── --}}
    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Detail Versi Aplikasi</h1>
            <p>Informasi lengkap versi <strong>{{ $appVersion->version }}</strong></p>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route($routePrefix . '.app-versions.edit', $appVersion->hashed_id) }}" class="adm-btn-primary">
                <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit
            </a>
            <a href="{{ route($routePrefix . '.app-versions.index') }}" class="adm-btn-secondary">
                <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                Kembali
            </a>
        </div>
    </div>

    {{-- ── DETAIL CARD ── --}}
    <div class="adm-card">
        <div class="adm-card-header">
                <div class="adm-card-title">
                    <svg viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                    Informasi Versi
                </div>
            </div>
            <div style="padding:0 20px;">
                <div class="adm-info-list">
                    <div class="adm-info-row">
                        <span class="adm-info-key">Version</span>
                        <span class="adm-info-val">
                            <span class="adm-badge adm-badge-info">v{{ $appVersion->version }}</span>
                        </span>
                    </div>
                    <div class="adm-info-row">
                        <span class="adm-info-key">Build Number</span>
                        <span class="adm-info-val adm-mono">{{ $appVersion->build_number }}</span>
                    </div>
                    <div class="adm-info-row">
                        <span class="adm-info-key">Force Update</span>
                        <span class="adm-info-val">
                            @if ($appVersion->force_update)
                                <span class="adm-badge adm-badge-danger"><span class="dot"></span>Wajib</span>
                            @else
                                <span class="adm-badge adm-badge-success">Opsional</span>
                            @endif
                        </span>
                    </div>
                    <div class="adm-info-row">
                        <span class="adm-info-key">Changelog</span>
                        <span class="adm-info-val" style="max-width:300px; text-align:right; font-weight:400; color:var(--adm-text-mid);">
                            {{ $appVersion->changelog ?: '-' }}
                        </span>
                    </div>
                    <div class="adm-info-row">
                        <span class="adm-info-key">Download URL</span>
                        <span class="adm-info-val">
                            <a href="{{ $appVersion->download_url }}" target="_blank" class="adm-btn primary icon-only" title="{{ $appVersion->download_url }}">
                                <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            </a>
                        </span>
                    </div>
                    <div class="adm-info-row">
                        <span class="adm-info-key">Dibuat</span>
                        <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-muted);">
                            {{ $appVersion->created_at->format('d M Y, H:i') }}
                        </span>
                    </div>
                    <div class="adm-info-row">
                        <span class="adm-info-key">Terakhir diupdate</span>
                        <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-muted);">
                            {{ $appVersion->updated_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
