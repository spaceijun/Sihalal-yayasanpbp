@extends('layouts.app')
@section('template_title')
    Detail Data Entry
@endsection

@section('content')
@php
    $ktpLengkap = !empty($dataEntry->nik) && !empty($dataEntry->nama_lengkap_ktp) && !empty($dataEntry->pendidikan_terakhir);
    $rekeningLengkap = !empty($dataEntry->bank_id) && !empty($dataEntry->no_rekening) && !empty($dataEntry->nama_rekening);
    $inisial = strtoupper(substr($dataEntry->nama_lengkap, 0, 2));
@endphp

<div class="de-page">

    {{-- ===== Page Header ===== --}}
    <div class="de-topbar">
        <div class="de-topbar__left">
            <a href="{{ route($routePrefix . '.data-entries.index') }}" class="de-back-btn">
                <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                Kembali
            </a>
            <div class="de-topbar__divider"></div>
            <div>
                <h1 class="de-topbar__title">Detail Data Entry</h1>
                <p class="de-topbar__sub">ID: DE-{{ str_pad($dataEntry->id, 4, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>
        <a href="{{ route($routePrefix . '.data-entries.edit', $dataEntry->hashed_id) }}" class="de-edit-btn">
            <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit Data
        </a>
    </div>

    <div class="de-grid">

        {{-- ===== LEFT COLUMN ===== --}}
        <div class="de-col-left">

            {{-- Profile Hero Card --}}
            <div class="de-hero-card">
                <div class="de-hero-avatar">{{ $inisial }}</div>
                <div class="de-hero-info">
                    <h2 class="de-hero-name">{{ $dataEntry->nama_lengkap }}</h2>
                    <p class="de-hero-email">{{ $dataEntry->email }}</p>
                    @if($dataEntry->telephone)
                        <p class="de-hero-phone">
                            <svg viewBox="0 0 24 24" width="12" height="12"><path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.79 19.79 0 0 1 11.61 19a19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 3.09 4.18 2 2 0 0 1 5.08 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L9.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            {{ $dataEntry->telephone }}
                        </p>
                    @endif
                </div>
                <div class="de-hero-badges">
                    @if($dataEntry->status === 'Aktif')
                        <span class="de-badge de-badge--aktif">Aktif</span>
                    @else
                        <span class="de-badge de-badge--nonaktif">Tidak Aktif</span>
                    @endif
                    @if($dataEntry->entry_type)
                        <span class="de-badge de-badge--{{ strtolower($dataEntry->entry_type) }}">{{ $dataEntry->entry_type }}</span>
                    @endif
                </div>
            </div>

            {{-- Informasi Akun --}}
            <div class="de-card">
                <div class="de-card__header">
                    <div class="de-card__icon">
                        <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <span class="de-card__title">Informasi Akun</span>
                </div>
                <div class="de-card__body">
                    <div class="de-info-row">
                        <span class="de-info-label">Nama Lengkap</span>
                        <span class="de-info-value">{{ $dataEntry->nama_lengkap ?: '—' }}</span>
                    </div>
                    <div class="de-info-row">
                        <span class="de-info-label">Email</span>
                        <span class="de-info-value">{{ $dataEntry->email ?: '—' }}</span>
                    </div>
                    <div class="de-info-row">
                        <span class="de-info-label">Telephone</span>
                        <span class="de-info-value de-mono">{{ $dataEntry->telephone ?: '—' }}</span>
                    </div>
                    <div class="de-info-row">
                        <span class="de-info-label">Alamat</span>
                        <span class="de-info-value de-wrap">{{ $dataEntry->alamat ?: '—' }}</span>
                    </div>
                    <div class="de-info-row">
                        <span class="de-info-label">Entry Type</span>
                        <span class="de-info-value">
                            @if($dataEntry->entry_type === 'OSS')
                                <span class="de-badge de-badge--oss">OSS</span>
                            @elseif($dataEntry->entry_type === 'SIHALAL')
                                <span class="de-badge de-badge--sihalal">SIHALAL</span>
                            @else
                                —
                            @endif
                        </span>
                    </div>
                    <div class="de-info-row">
                        <span class="de-info-label">Status</span>
                        <span class="de-info-value">
                            @if($dataEntry->status === 'Aktif')
                                <span class="de-badge de-badge--aktif">Aktif</span>
                            @else
                                <span class="de-badge de-badge--nonaktif">Tidak Aktif</span>
                            @endif
                        </span>
                    </div>
                    @if($dataEntry->koordinators->isNotEmpty())
                    <div class="de-info-row">
                        <span class="de-info-label">Koordinator</span>
                        <span class="de-info-value">
                            <div class="de-tags">
                                @foreach($dataEntry->koordinators as $k)
                                    <span class="de-tag">{{ $k->nama_lengkap }}</span>
                                @endforeach
                            </div>
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Rekening --}}
            <div class="de-card">
                <div class="de-card__header">
                    <div class="de-card__icon">
                        <svg viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    </div>
                    <span class="de-card__title">Informasi Rekening</span>
                    @if($rekeningLengkap)
                        <span class="de-badge de-badge--aktif" style="margin-left:auto;">Lengkap</span>
                    @else
                        <span class="de-badge de-badge--warn" style="margin-left:auto;">Belum Lengkap</span>
                    @endif
                </div>
                <div class="de-card__body">
                    @if($rekeningLengkap)
                        <div class="de-rekening-box">
                            <div class="de-rekening-bank">{{ $dataEntry->bank->name }}</div>
                            <div class="de-rekening-no de-mono">{{ $dataEntry->no_rekening }}</div>
                            <div class="de-rekening-an">a.n. {{ $dataEntry->nama_rekening }}</div>
                        </div>
                    @else
                        <p class="de-empty">Data rekening belum diisi oleh pengguna.</p>
                    @endif
                </div>
            </div>

        </div>

        {{-- ===== RIGHT COLUMN ===== --}}
        <div class="de-col-right">

            {{-- KTP & Pendidikan --}}
            <div class="de-card">
                <div class="de-card__header">
                    <div class="de-card__icon">
                        <svg viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    </div>
                    <span class="de-card__title">Identitas KTP &amp; Pendidikan</span>
                    @if($ktpLengkap)
                        <span class="de-badge de-badge--aktif" style="margin-left:auto;">Lengkap</span>
                    @else
                        <span class="de-badge de-badge--danger" style="margin-left:auto;">Belum Lengkap</span>
                    @endif
                </div>
                <div class="de-card__body">
                    <div class="de-info-row">
                        <span class="de-info-label">NIK</span>
                        <span class="de-info-value de-mono">{{ $dataEntry->nik ?: '—' }}</span>
                    </div>
                    <div class="de-info-row">
                        <span class="de-info-label">Nama (KTP)</span>
                        <span class="de-info-value">{{ $dataEntry->nama_lengkap_ktp ?: '—' }}</span>
                    </div>
                    <div class="de-info-row">
                        <span class="de-info-label">Pendidikan Terakhir</span>
                        <span class="de-info-value">
                            @if($dataEntry->pendidikan_terakhir)
                                <span class="de-tag">{{ $dataEntry->pendidikan_terakhir }}</span>
                            @else
                                —
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            {{-- Foto KTP --}}
            <div class="de-card">
                <div class="de-card__header">
                    <div class="de-card__icon">
                        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    </div>
                    <span class="de-card__title">Foto KTP</span>
                </div>
                <div class="de-card__body">
                    @if($dataEntry->foto_ktp)
                        <a href="{{ asset('storage/' . $dataEntry->foto_ktp) }}" target="_blank" class="de-photo-wrap">
                            <img src="{{ asset('storage/' . $dataEntry->foto_ktp) }}" alt="Foto KTP" class="de-photo">
                            <div class="de-photo-overlay">
                                <svg viewBox="0 0 24 24" width="20" height="20"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                Buka Foto
                            </div>
                        </a>
                    @else
                        <div class="de-photo-placeholder">
                            <svg viewBox="0 0 24 24" width="32" height="32"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            <p>Foto KTP belum diunggah</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Foto Ijazah --}}
            <div class="de-card">
                <div class="de-card__header">
                    <div class="de-card__icon">
                        <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    <span class="de-card__title">Foto Ijazah</span>
                </div>
                <div class="de-card__body">
                    @if($dataEntry->foto_ijasah)
                        <a href="{{ asset('storage/' . $dataEntry->foto_ijasah) }}" target="_blank" class="de-photo-wrap">
                            <img src="{{ asset('storage/' . $dataEntry->foto_ijasah) }}" alt="Foto Ijazah" class="de-photo">
                            <div class="de-photo-overlay">
                                <svg viewBox="0 0 24 24" width="20" height="20"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                Buka Foto
                            </div>
                        </a>
                    @else
                        <div class="de-photo-placeholder">
                            <svg viewBox="0 0 24 24" width="32" height="32"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            <p>Foto Ijazah belum diunggah</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

<style>
/* ── Tokens ── */
:root {
    --de-bg: #f5f6fa;
    --de-card: #fff;
    --de-border: #e5e7eb;
    --de-radius: 14px;
    --de-shadow: 0 1px 4px rgba(0,0,0,.07);
    --de-text: #111827;
    --de-mid: #374151;
    --de-muted: #6b7280;
    --de-faint: #9ca3af;
    --de-primary: #5b21b6;
    --de-primary-lt: #ede9fe;
}

/* ── Layout ── */
.de-page { padding: 1.5rem 1.75rem; max-width: 1200px; }

.de-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 12px;
}
.de-topbar__left { display: flex; align-items: center; gap: 14px; }
.de-topbar__divider { width: 1px; height: 28px; background: var(--de-border); }
.de-topbar__title { font-size: 17px; font-weight: 700; color: var(--de-text); margin: 0; }
.de-topbar__sub { font-size: 12px; color: var(--de-muted); margin: 0; }

.de-back-btn {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 13px; font-weight: 500; color: var(--de-muted);
    text-decoration: none; padding: 6px 12px;
    border: 1px solid var(--de-border); border-radius: 8px;
    background: #fff; transition: all .15s;
}
.de-back-btn svg { width:14px; height:14px; fill:none; stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
.de-back-btn:hover { color: var(--de-primary); border-color: var(--de-primary); }

.de-edit-btn {
    display: inline-flex; align-items: center; gap: 7px;
    font-size: 13px; font-weight: 600;
    color: #fff; background: var(--de-primary);
    padding: 8px 18px; border-radius: 10px;
    text-decoration: none; transition: background .15s;
}
.de-edit-btn svg { width:14px; height:14px; fill:none; stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
.de-edit-btn:hover { background: #4c1d95; color:#fff; }

/* ── Grid ── */
.de-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.25rem;
    align-items: start;
}
.de-col-left, .de-col-right { display: flex; flex-direction: column; gap: 1.25rem; }

/* ── Hero Card ── */
.de-hero-card {
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 55%, #1e3a5f 100%);
    border-radius: var(--de-radius);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 16px;
    color: #fff;
    position: relative;
    overflow: hidden;
    flex-wrap: wrap;
}
.de-hero-card::before {
    content:''; position:absolute; inset:0;
    background:url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M20 20h20v20H20zM0 0h20v20H0z'/%3E%3C/g%3E%3C/svg%3E");
    pointer-events:none;
}
.de-hero-avatar {
    width: 56px; height: 56px; border-radius: 50%;
    background: rgba(255,255,255,.15); border: 2px solid rgba(255,255,255,.25);
    font-size: 20px; font-weight: 700; letter-spacing: .5px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.de-hero-info { flex: 1; min-width: 0; }
.de-hero-name { font-size: 17px; font-weight: 700; margin: 0 0 3px; }
.de-hero-email { font-size: 12.5px; opacity: .7; margin: 0 0 2px; }
.de-hero-phone { font-size: 12px; opacity: .6; margin: 0; display:flex; align-items:center; gap:4px; }
.de-hero-phone svg { fill:none; stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
.de-hero-badges { display:flex; gap:6px; flex-wrap:wrap; }

/* ── Card ── */
.de-card {
    background: var(--de-card); border: 1px solid var(--de-border);
    border-radius: var(--de-radius); box-shadow: var(--de-shadow); overflow: hidden;
}
.de-card__header {
    display: flex; align-items: center; gap: 10px;
    padding: 13px 18px; border-bottom: 1px solid #f3f4f6;
    background: #fafafa;
}
.de-card__icon {
    width: 28px; height: 28px; border-radius: 7px;
    background: var(--de-primary-lt); display: flex; align-items: center;
    justify-content: center; flex-shrink: 0;
}
.de-card__icon svg { width:13px; height:13px; fill:none; stroke:var(--de-primary); stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
.de-card__title { font-size: 11.5px; font-weight: 700; color: var(--de-muted); text-transform: uppercase; letter-spacing: .06em; }
.de-card__body { padding: 4px 18px 12px; }

/* ── Info Rows ── */
.de-info-row {
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 1rem; padding: 11px 0; border-bottom: 1px solid #f3f4f6;
}
.de-info-row:last-child { border-bottom: none; }
.de-info-label { font-size: 12.5px; color: var(--de-muted); white-space: nowrap; padding-top: 1px; flex-shrink: 0; }
.de-info-value { font-size: 13px; font-weight: 600; color: var(--de-text); text-align: right; }
.de-info-value.de-wrap { white-space: normal; max-width: 300px; line-height: 1.5; font-weight: 400; color: var(--de-mid); }
.de-mono { font-family: 'Courier New', monospace; letter-spacing: .03em; }

/* ── Badges ── */
.de-badge {
    display: inline-flex; align-items: center;
    font-size: 11px; font-weight: 600; padding: 3px 10px;
    border-radius: 999px; letter-spacing: .02em; white-space: nowrap;
}
.de-badge--aktif { background: #d1fae5; color: #065f46; }
.de-badge--nonaktif { background: #fee2e2; color: #991b1b; }
.de-badge--oss { background: #dbeafe; color: #1e40af; }
.de-badge--sihalal { background: #ede9fe; color: #5b21b6; }
.de-badge--warn { background: #fef3c7; color: #92400e; }
.de-badge--danger { background: #fee2e2; color: #991b1b; }

/* ── Tags ── */
.de-tags { display:flex; gap:5px; flex-wrap:wrap; justify-content:flex-end; }
.de-tag {
    font-size: 11.5px; padding: 3px 9px; border-radius: 6px;
    background: var(--de-primary-lt); color: var(--de-primary); font-weight: 500;
}

/* ── Rekening Box ── */
.de-rekening-box {
    background: #f9fafb; border: 1px solid var(--de-border);
    border-radius: 10px; padding: 14px 16px; margin-top: 8px;
}
.de-rekening-bank { font-size: 14px; font-weight: 700; color: var(--de-text); margin-bottom: 4px; }
.de-rekening-no { font-size: 18px; font-weight: 700; color: var(--de-primary); letter-spacing: .05em; margin-bottom: 4px; }
.de-rekening-an { font-size: 12px; color: var(--de-muted); }

/* ── Photo ── */
.de-photo-wrap {
    display: block; position: relative; border-radius: 10px;
    overflow: hidden; margin-top: 8px; cursor: pointer; text-decoration:none;
}
.de-photo {
    width: 100%; max-height: 220px; object-fit: cover;
    border-radius: 10px; display: block;
    border: 1px solid var(--de-border); transition: filter .2s;
}
.de-photo-overlay {
    position: absolute; inset: 0; background: rgba(91,33,182,.55);
    display: flex; align-items: center; justify-content: center; gap: 8px;
    color: #fff; font-size: 13px; font-weight: 600;
    opacity: 0; transition: opacity .2s; border-radius: 10px;
}
.de-photo-overlay svg { fill:none; stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
.de-photo-wrap:hover .de-photo-overlay { opacity: 1; }
.de-photo-wrap:hover .de-photo { filter: brightness(.7); }

.de-photo-placeholder {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: 10px; padding: 2.5rem 1rem; background: #f9fafb;
    border: 2px dashed #e5e7eb; border-radius: 10px; margin-top: 8px;
    color: var(--de-faint); text-align: center;
}
.de-photo-placeholder svg { fill:none; stroke:currentColor; stroke-width:1.5; stroke-linecap:round; stroke-linejoin:round; }
.de-photo-placeholder p { font-size: 12.5px; margin: 0; }

/* ── Empty ── */
.de-empty { font-size: 13px; color: var(--de-faint); padding: 12px 0; text-align:center; }

/* ── Responsive ── */
@media (max-width: 900px) {
    .de-grid { grid-template-columns: 1fr; }
    .de-page { padding: 1rem; }
}
</style>
@endsection
