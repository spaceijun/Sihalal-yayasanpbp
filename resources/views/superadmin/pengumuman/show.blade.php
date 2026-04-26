@extends('layouts.app')
@section('template_title') {{ $pengumuman->judul ?? 'Detail Pengumuman' }} @endsection
@section('content')
<div class="adm-page">

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Detail Pengumuman</h1>
            <p>Informasi lengkap pengumuman untuk data entry</p>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('superadmin.pengumumen.edit', $pengumuman->hashed_id) }}" class="adm-btn-primary">
                <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit
            </a>
            <a href="{{ route('superadmin.pengumumen.index') }}" class="adm-btn-secondary">
                <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                Kembali
            </a>
        </div>
    </div>

    {{-- META INFO ── --}}
    <div class="adm-card" style="margin-bottom:16px;">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Informasi Pengumuman
            </div>
        </div>
        <div style="padding:0 20px;">
            <div class="adm-info-list">
                <div class="adm-info-row">
                    <span class="adm-info-key">Nomor</span>
                    <span class="adm-info-val adm-mono" style="font-size:12.5px;background:var(--adm-blue-lt);color:var(--adm-blue);padding:3px 10px;border-radius:6px;">
                        {{ $pengumuman->nomor }}
                    </span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Jenis</span>
                    <span class="adm-info-val">
                        @if ($pengumuman->jenis === 'OSS')
                            <span class="adm-badge adm-badge-oss">OSS</span>
                        @elseif ($pengumuman->jenis === 'SIHALAL')
                            <span class="adm-badge adm-badge-sihalal">SIHALAL</span>
                        @else
                            <span class="adm-badge adm-badge-info">{{ $pengumuman->jenis }}</span>
                        @endif
                    </span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Judul</span>
                    <span class="adm-info-val" style="font-size:15px;">{{ $pengumuman->judul }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Lampiran</span>
                    <span class="adm-info-val">
                        @if ($pengumuman->foto)
                            <a href="{{ asset('storage/' . $pengumuman->foto) }}" target="_blank"
                                class="adm-btn primary" style="padding:5px 14px;font-size:12px;">
                                <svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                Lihat PDF / Dokumen
                            </a>
                        @else
                            <span style="color:var(--adm-text-faint);font-size:13px;">Tidak ada lampiran</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ISI PENGUMUMAN ── --}}
    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><line x1="21" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="21" y1="18" x2="3" y2="18"/></svg>
                Isi Pengumuman
            </div>
        </div>
        <div style="padding:20px 24px;font-size:14px;line-height:1.8;color:var(--adm-text-mid);">
            {!! $pengumuman->deskripsi !!}
        </div>
    </div>

</div>
@endsection
