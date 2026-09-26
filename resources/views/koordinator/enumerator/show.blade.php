@extends('layouts.app')
@section('template_title') Detail Enumerator @endsection

@section('content')
<div class="adm-page">
    @include('layouts.messages')

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Detail Enumerator</h1>
            <p>Informasi &amp; performa enumerator</p>
        </div>
        <a href="{{ route('koordinator.enumerator.index') }}" class="adm-btn-secondary">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg> Kembali
        </a>
    </div>

    <div class="adm-stats">
        <div class="adm-stat is-accent">
            <div class="adm-stat-label">Data Bulan Ini</div>
            <div class="adm-stat-value">{{ $dataBulanIni }}</div>
            <div class="adm-stat-sub">Target: {{ $targetBulanan }} data ({{ $progressPercent }}%)</div>
        </div>
        <div class="adm-stat">
            <div class="adm-stat-label">Total Data</div>
            <div class="adm-stat-value">{{ $totalData }}</div>
            <div class="adm-stat-sub">Sepanjang waktu</div>
        </div>
        <div class="adm-stat">
            <div class="adm-stat-label">Terbit SH</div>
            <div class="adm-stat-value is-success">{{ $totalTerbitSh }}</div>
            <div class="adm-stat-sub">Sudah terbit sertifikat</div>
        </div>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Informasi Enumerator
            </div>
        </div>
        <div style="padding:0 20px;">
            <div class="adm-info-list">
                <div class="adm-info-row">
                    <span class="adm-info-key">Nama Lengkap</span>
                    <span class="adm-info-val">{{ $enumerator->nama_lengkap }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">No. Registrasi</span>
                    <span class="adm-info-val adm-mono">{{ $enumerator->no_registrasi ?: '—' }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Telepon</span>
                    <span class="adm-info-val adm-mono">{{ $enumerator->telephone ?: '—' }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Alamat</span>
                    <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $enumerator->alamat ?: '—' }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Status</span>
                    <span class="adm-info-val">
                        @if ($enumerator->status === 'Aktif')
                            <span class="adm-badge adm-badge-success">Aktif</span>
                        @else
                            <span class="adm-badge adm-badge-nonaktif">Tidak Aktif</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
