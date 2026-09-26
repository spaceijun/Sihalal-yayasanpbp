@extends('layouts.app')
@section('template_title') Detail Data Lapangan @endsection

@section('content')
<div class="adm-page">
    @include('layouts.messages')

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Detail Data Lapangan</h1>
            <p>Informasi lengkap (read-only) — data hanya dapat diubah oleh Enumerator/Data Entry</p>
        </div>
        <a href="{{ route('koordinator.data-lapangan.index') }}" class="adm-btn-secondary">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg> Kembali
        </a>
    </div>

    <div class="adm-card" style="margin-bottom:16px;">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="9" x2="15" y2="15"/><line x1="15" y1="9" x2="9" y2="15"/></svg>
                Informasi Pelaku Usaha
            </div>
        </div>
        <div style="padding:0 20px;">
            <div class="adm-info-list">
                <div class="adm-info-row">
                    <span class="adm-info-key">No. Registrasi</span>
                    <span class="adm-info-val adm-mono">{{ $dataLapangan->no_registrasi }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Nama PU</span>
                    <span class="adm-info-val">{{ $dataLapangan->nama_pu }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">NIK</span>
                    <span class="adm-info-val adm-mono">{{ $dataLapangan->nik }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Enumerator</span>
                    <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $dataLapangan->enumerator?->nama_lengkap ?? '—' }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Alamat</span>
                    <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $dataLapangan->full_address ?: '—' }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Lokasi GPS (Geotag)</span>
                    <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">
                        @if ($dataLapangan->latitude !== null && $dataLapangan->longitude !== null)
                            {{ number_format((float) $dataLapangan->latitude, 6) }}, {{ number_format((float) $dataLapangan->longitude, 6) }}
                            @if ($dataLapangan->akurasi_meter !== null)
                                (±{{ number_format((float) $dataLapangan->akurasi_meter, 1) }} m)
                            @endif
                            — <a href="{{ $dataLapangan->google_maps_url }}" target="_blank" rel="noopener">Buka di Google Maps</a>
                        @else
                            — (data lama sebelum fitur geotag aktif)
                        @endif
                    </span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Status Proses</span>
                    <span class="adm-info-val"><span class="adm-badge adm-badge-info">{{ $dataLapangan->status }}</span></span>
                </div>
            </div>
        </div>
    </div>

    <div class="adm-card" style="margin-bottom:16px;">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                Galeri Foto Lapangan
            </div>
        </div>
        <div style="padding:16px 20px;">
            <p style="font-size:12px;color:var(--adm-text-muted);margin:0 0 14px;">Foto diisi oleh Enumerator saat input data. Klik foto untuk memperbesar.</p>
            <div class="ko-foto-grid">
                @php
                    $fotoFields = [
                        'foto_ktp' => 'Foto KTP',
                        'foto_rumah' => 'Foto Lokasi/Rumah Usaha',
                        'foto_pendamping' => 'Foto Bersama Pendamping',
                        'foto_produk' => 'Foto Produk',
                        'foto_produk_2' => 'Foto Produk 2',
                        'foto_produk_3' => 'Foto Produk 3',
                        'foto_produk_4' => 'Foto Produk 4',
                        'foto_produk_5' => 'Foto Produk 5',
                    ];
                @endphp
                @foreach ($fotoFields as $field => $label)
                    <div class="ko-foto-item">
                        @if ($dataLapangan->$field)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($dataLapangan->$field) }}"
                                alt="{{ $label }}" class="ko-foto-thumb"
                                onclick="bukaLightboxFoto('{{ \Illuminate\Support\Facades\Storage::url($dataLapangan->$field) }}', '{{ $label }}')">
                        @else
                            <div class="ko-foto-empty">
                                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                <span>Belum ada foto</span>
                            </div>
                        @endif
                        <div class="ko-foto-label">{{ $label }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Status Verifikasi Koordinator
            </div>
        </div>
        <div style="padding:0 20px;">
            <div class="adm-info-list">
                <div class="adm-info-row">
                    <span class="adm-info-key">Verifikasi</span>
                    <span class="adm-info-val">
                        @switch($dataLapangan->verifikasi_koordinator)
                            @case('Terverifikasi')
                                <span class="adm-badge adm-badge-success">Terverifikasi</span>
                                @break
                            @case('Perlu Koreksi')
                                <span class="adm-badge adm-badge-danger">Perlu Koreksi</span>
                                @break
                            @default
                                <span class="adm-badge adm-badge-pending">Belum</span>
                        @endswitch
                    </span>
                </div>
                @if ($dataLapangan->catatan_koordinator)
                    <div class="adm-info-row">
                        <span class="adm-info-key">Catatan</span>
                        <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $dataLapangan->catatan_koordinator }}</span>
                    </div>
                @endif
                @if ($dataLapangan->verified_at_koordinator)
                    <div class="adm-info-row">
                        <span class="adm-info-key">Diverifikasi Pada</span>
                        <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $dataLapangan->verified_at_koordinator->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="adm-info-row">
                        <span class="adm-info-key">Diverifikasi Oleh</span>
                        <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $dataLapangan->verifiedByKoordinator?->nama_lengkap ?? '—' }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ══ LIGHTBOX FOTO ══ --}}
<div class="modal fade adm-modal-plain" id="modalLightboxFoto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lightboxFotoTitle">Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center" style="padding:20px;">
                <img id="lightboxFotoSrc" src="" alt="" style="max-width:100%;max-height:600px;border-radius:8px;">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function bukaLightboxFoto(src, title) {
        document.getElementById('lightboxFotoSrc').src = src;
        document.getElementById('lightboxFotoTitle').textContent = title;
        new bootstrap.Modal(document.getElementById('modalLightboxFoto')).show();
    }
</script>
@endpush

@push('styles')
<style>
    .ko-foto-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 14px;
    }
    .ko-foto-item { text-align: center; }
    .ko-foto-thumb {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: var(--adm-radius-sm);
        border: 1px solid var(--adm-border);
        cursor: pointer;
        transition: box-shadow .15s, transform .15s;
        display: block;
    }
    .ko-foto-thumb:hover {
        box-shadow: 0 4px 14px rgba(0,0,0,.12);
        transform: translateY(-1px);
    }
    .ko-foto-empty {
        width: 100%;
        height: 120px;
        border-radius: var(--adm-radius-sm);
        border: 1px dashed var(--adm-border);
        background: var(--adm-bg-light);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        color: var(--adm-text-faint);
    }
    .ko-foto-empty svg {
        width: 24px;
        height: 24px;
        stroke: var(--adm-text-faint);
        fill: none;
        stroke-width: 1.5;
    }
    .ko-foto-empty span { font-size: 11px; }
    .ko-foto-label {
        margin-top: 6px;
        font-size: 11.5px;
        font-weight: 600;
        color: var(--adm-text-mid);
    }
</style>
@endpush
