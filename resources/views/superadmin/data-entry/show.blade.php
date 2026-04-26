@extends('layouts.app')
@section('template_title')
    Detail Data Entry
@endsection

@section('content')
<div class="adm-page">

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Detail Data Entry</h1>
            <p>Informasi lengkap akun data entry</p>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('superadmin.data-entries.edit', $dataEntry->hashed_id) }}" class="adm-btn-primary">
                <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit
            </a>
            <a href="{{ route('superadmin.data-entries.index') }}" class="adm-btn-secondary">
                <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                Kembali
            </a>
        </div>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                Informasi Akun
            </div>
        </div>
        <div style="padding:0 20px;">
            <div class="adm-info-list">
                <div class="adm-info-row">
                    <span class="adm-info-key">Nama Lengkap</span>
                    <span class="adm-info-val">{{ $dataEntry->nama_lengkap }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Email</span>
                    <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $dataEntry->email }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Telephone</span>
                    <span class="adm-info-val adm-mono">{{ $dataEntry->telephone }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Alamat</span>
                    <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $dataEntry->alamat ?: '-' }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Status</span>
                    <span class="adm-info-val">
                        @if ($dataEntry->status === 'Aktif')
                            <span class="adm-badge adm-badge-success">Aktif</span>
                        @else
                            <span class="adm-badge adm-badge-nonaktif">Tidak Aktif</span>
                        @endif
                    </span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Entry Type</span>
                    <span class="adm-info-val">
                        @if ($dataEntry->entry_type === 'OSS')
                            <span class="adm-badge adm-badge-oss">OSS</span>
                        @elseif ($dataEntry->entry_type === 'SIHALAL')
                            <span class="adm-badge adm-badge-sihalal">SIHALAL</span>
                        @else
                            <span class="adm-info-val" style="font-weight:400;">{{ $dataEntry->entry_type ?: '-' }}</span>
                        @endif
                    </span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Rekening</span>
                    <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">
                        @if ($dataEntry->bank && $dataEntry->no_rekening && $dataEntry->nama_rekening)
                            {{ $dataEntry->bank->name }}, {{ $dataEntry->no_rekening }} an. {{ $dataEntry->nama_rekening }}
                        @else
                            —
                        @endif
                    </span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">User ID</span>
                    <span class="adm-info-val adm-mono" style="font-size:12px;">{{ $dataEntry->user_id }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
