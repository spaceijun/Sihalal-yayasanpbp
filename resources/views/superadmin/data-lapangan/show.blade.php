@extends('layouts.app')

@section('template_title')
    {{ $dataLapangan->nama_pu ?? 'Detail Data Lapangan' }}
@endsection

@section('content')
    @php
        $statusBadgeMap = [
            'PENDING' => 'dl-badge-pending',
            'TERVERIFIKASI' => 'dl-badge-verif',
            'PROGRESS OSS' => 'dl-badge-oss',
            'PROGRESS SIHALAL' => 'dl-badge-sihalal',
            'TERBIT SH' => 'dl-badge-terbit',
            'DITOLAK' => 'dl-badge-ditolak',
            'REVISI' => 'dl-badge-revisi',
        ];
        $bayarBadgeMap = [
            'PENDING' => 'dl-badge-pending',
            'PENGAJUAN' => 'dl-badge-pengajuan',
            'DIBAYAR' => 'dl-badge-dibayar',
        ];
        $statusBadge = $statusBadgeMap[$dataLapangan->status] ?? 'dl-badge-ditolak';
        $bayarBadge = $bayarBadgeMap[$dataLapangan->status_pembayaran] ?? 'dl-badge-pending';

        // Kumpulkan semua produk (utama + tambahan yang tidak null)
        $allProducts = [];
        $productFields = [
            1 => ['nama' => $dataLapangan->nama_produk, 'foto' => $dataLapangan->foto_produk],
            2 => ['nama' => $dataLapangan->nama_produk_2, 'foto' => $dataLapangan->foto_produk_2],
            3 => ['nama' => $dataLapangan->nama_produk_3, 'foto' => $dataLapangan->foto_produk_3],
            4 => ['nama' => $dataLapangan->nama_produk_4, 'foto' => $dataLapangan->foto_produk_4],
            5 => ['nama' => $dataLapangan->nama_produk_5, 'foto' => $dataLapangan->foto_produk_5],
        ];
        foreach ($productFields as $idx => $p) {
            if (!empty($p['nama'])) {
                $allProducts[$idx] = $p;
            }
        }

        $steps = ['PENDING', 'TERVERIFIKASI', 'PROGRESS OSS', 'PROGRESS SIHALAL', 'TERBIT SH'];
        $currentIdx = array_search($dataLapangan->status, $steps);
        if ($currentIdx === false) {
            $currentIdx = -1;
        }
    @endphp

    <div class="dl-page">

        {{-- ALERTS --}}
        @if (session('success'))
            <div class="dl-alert dl-alert-success">
                <i class="las la-check-circle dl-alert-icon"></i>
                <div>{{ session('success') }}</div>
                <button type="button"
                    style="background:none;border:none;margin-left:auto;cursor:pointer;font-size:18px;line-height:1;color:inherit;opacity:.5;"
                    onclick="this.closest('.dl-alert').remove()">&times;</button>
            </div>
        @endif
        @if (session('error'))
            <div class="dl-alert dl-alert-danger">
                <i class="las la-exclamation-circle dl-alert-icon"></i>
                <div>{{ session('error') }}</div>
                <button type="button"
                    style="background:none;border:none;margin-left:auto;cursor:pointer;font-size:18px;line-height:1;color:inherit;opacity:.5;"
                    onclick="this.closest('.dl-alert').remove()">&times;</button>
            </div>
        @endif
        @if ($errors->any())
            <div class="dl-alert dl-alert-danger">
                <i class="las la-exclamation-circle dl-alert-icon"></i>
                <ul style="margin:0;padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li style="font-size:13px;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- HEADER --}}
        <div class="dl-header">
            <div>
                <div class="dl-header-title">{{ $dataLapangan->nama_pu }}</div>
                <div class="dl-header-meta">
                    No Registrasi : <strong>{{ $dataLapangan->no_registrasi }}</strong>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                <span class="dl-badge {{ $statusBadge }}">
                    <span class="dl-badge-dot"></span>{{ $dataLapangan->status }}
                </span>
                <a href="{{ route($routePrefix . '.data-lapangans.index') }}" class="dl-back">
                    <i class="las la-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        {{-- STEPPER --}}
        <div class="dl-stepper">
            <div class="dl-stepper-inner">
                @foreach ($steps as $i => $step)
                    @php
                        $cls = $i < $currentIdx ? 'done' : ($i === $currentIdx ? 'active' : '');
                    @endphp
                    <div class="dl-step {{ $cls }}">
                        <div class="dl-step-dot">
                            @if ($i < $currentIdx)
                                <i class="las la-check" style="font-size:13px;"></i>
                            @else
                                {{ $i + 1 }}
                            @endif
                        </div>
                        <div class="dl-step-label">{{ $step }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- MAIN GRID --}}
        <div class="dl-grid">

            {{-- ══════════ KOLOM KIRI ══════════ --}}
            <div style="display:flex;flex-direction:column;gap:1.25rem;">

                {{-- Card: Status & Aksi --}}
                <div class="dl-card">
                    <div class="dl-card-head">
                        <div class="dl-card-head-left">
                            <div class="dl-card-icon" style="background:#EEF4FF;">
                                <i class="las la-bolt" style="color:var(--dl-blue);"></i>
                            </div>
                            <span class="dl-card-title">Status &amp; Aksi</span>
                        </div>
                    </div>
                    <div class="dl-card-body">

                        {{-- Tombol aksi --}}
                        @if (in_array($dataLapangan->status, ['PENDING', 'REVISI']))
                            <div class="dl-actions-group" style="margin-bottom:1rem;">
                                <button type="button" class="dl-btn dl-btn-success" data-bs-toggle="modal"
                                    data-bs-target="#modalUpdateEmail">
                                    <i class="las la-check-circle"></i> Update Email &amp; Verifikasi
                                </button>
                            </div>
                        @endif

                        <button type="button" class="dl-btn dl-btn-danger" data-bs-toggle="modal"
                            data-bs-target="#modalRevisi">
                            <i class="las la-redo"></i> Update Revisi
                        </button>

                        {{-- Verifikator --}}
                        @if ($dataLapangan->verifikator)
                            <div class="dl-divider"><span class="dl-divider-label">Verifikator</span>
                                <div class="dl-divider-line"></div>
                            </div>
                            <div class="dl-verif-chip" style="margin-bottom:.75rem;">
                                <div class="dl-verif-avatar">
                                    {{ strtoupper(substr($dataLapangan->verifikator->nama_lengkap ?? 'V', 0, 2)) }}
                                </div>
                                <div>
                                    <div class="dl-verif-name">{{ $dataLapangan->verifikator->nama_lengkap }}</div>
                                    <div class="dl-verif-date">
                                        {{ $dataLapangan->tanggal_verifikasi
                                            ? \Carbon\Carbon::parse($dataLapangan->tanggal_verifikasi)->translatedFormat('d M Y')
                                            : 'Tanggal tidak tersedia' }}
                                    </div>
                                </div>
                                <span class="dl-badge dl-badge-terbit" style="margin-left:auto;">Terverifikasi</span>
                            </div>
                        @elseif ($dataLapangan->status == 'REVISI')
                            <div class="dl-revisi-alert">
                                <i class="las la-exclamation-triangle dl-revisi-icon"></i>
                                <div>
                                    <div class="dl-revisi-title">Keterangan Revisi</div>
                                    <div class="dl-revisi-text">{{ $dataLapangan->keterangan ?? 'Tidak ada keterangan.' }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Data Entry --}}
                        <div class="dl-divider"><span class="dl-divider-label">Data Entry</span>
                            <div class="dl-divider-line"></div>
                        </div>
                        <div class="dl-entry-row">
                            <div>
                                <div class="dl-entry-name">Entry OSS</div>
                                <div class="dl-entry-meta">
                                    {{ $dataEntryOSS?->dataEntry?->nama_lengkap ?? 'Tidak ada data' }}
                                    @if ($dataEntryOSS?->actioned_at)
                                        ·
                                        {{ \Carbon\Carbon::parse($dataEntryOSS->actioned_at)->translatedFormat('d M Y') }}
                                    @endif
                                </div>
                            </div>
                            <span class="dl-badge dl-badge-oss">OSS</span>
                        </div>
                        <div class="dl-entry-row">
                            <div>
                                <div class="dl-entry-name">Entry Sihalal</div>
                                <div class="dl-entry-meta">
                                    {{ $dataEntrySihalal?->dataEntry?->nama_lengkap ?? 'Tidak ada data' }}
                                    @if ($dataEntrySihalal?->actioned_at)
                                        ·
                                        {{ \Carbon\Carbon::parse($dataEntrySihalal->actioned_at)->translatedFormat('d M Y') }}
                                    @endif
                                </div>
                            </div>
                            <span class="dl-badge dl-badge-sihalal">Sihalal</span>
                        </div>

                        {{-- Email Sihalal --}}
                        <div class="dl-divider" style="margin-top:1rem;">
                            <span class="dl-divider-label">Email Sihalal</span>
                            <div class="dl-divider-line"></div>
                            @if (!$dataLapangan->email_sihalal && $dataLapangan->status == 'PROGRESS SIHALAL')
                                <button type="button" class="dl-btn dl-btn-primary dl-btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#modalEditEmailSihalal">
                                    <i class="las la-plus"></i> Tambah
                                </button>
                            @endif
                        </div>
                        <p
                            style="font-size:13.5px;color:{{ $dataLapangan->email_sihalal ? 'var(--dl-text)' : 'var(--dl-muted)' }};margin:0;">
                            {{ $dataLapangan->email_sihalal ?? 'Data tidak tersedia' }}
                        </p>

                        @if ($dataLapangan->old_email_sihalal)
                            <div style="margin-top:6px;padding:6px 10px;background:#FEF2F2;border:1px solid #FECACA;border-radius:6px;display:flex;align-items:center;gap:6px;">
                                <i class="las la-history" style="color:#DC2626;font-size:14px;"></i>
                                <span style="font-size:12px;color:#B91C1C;">
                                    Email lama: <strong>{{ $dataLapangan->old_email_sihalal }}</strong>
                                </span>
                            </div>
                        @endif

                    </div>
                </div>

                {{-- Card: Informasi Pelaku Usaha --}}
                <div class="dl-card">
                    <div class="dl-card-head">
                        <div class="dl-card-head-left">
                            <div class="dl-card-icon" style="background:#F5F0FF;">
                                <i class="las la-user" style="color:#7C3AED;"></i>
                            </div>
                            <span class="dl-card-title">Informasi Pelaku Usaha</span>
                        </div>
                    </div>
                    <div class="dl-card-body" style="padding:0;">
                        <table class="dl-info-table" style="padding:0 4px;">
                            @php
                                $infoFields = [
                                    ['Pendamping', $dataLapangan->enumerator->nama_lengkap],
                                    ['Nama PU', $dataLapangan->nama_pu],
                                    ['NIK', $dataLapangan->nik],
                                    ['No. Telepon', $dataLapangan->telephone ?? '—'],
                                    ['Email', $dataLapangan->email ?? '—'],
                                    ['Alamat', $dataLapangan->alamat],
                                ];
                            @endphp
                            @foreach ($infoFields as $f)
                                <tr>
                                    <td class="dl-key" style="padding-left:1.25rem;">{{ $f[0] }}</td>
                                    <td class="dl-val" style="padding-right:1.25rem;">{{ $f[1] }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td class="dl-key" style="padding-left:1.25rem;">Status</td>
                                <td class="dl-val" style="padding-right:1.25rem;">
                                    <span class="dl-badge {{ $statusBadge }}">{{ $dataLapangan->status }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="dl-key" style="padding-left:1.25rem;">Pembayaran</td>
                                <td class="dl-val" style="padding-right:1.25rem;">
                                    <span
                                        class="dl-badge {{ $bayarBadge }}">{{ $dataLapangan->status_pembayaran }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Card: Keterangan Revisi --}}
                <div class="dl-card">
                    <div class="dl-card-head">
                        <div class="dl-card-head-left">
                            <div class="dl-card-icon" style="background:#FFF1F2;">
                                <i class="las la-comment-alt" style="color:var(--dl-rose);"></i>
                            </div>
                            <span class="dl-card-title">Form Keterangan Revisi</span>
                        </div>
                    </div>
                    <div class="dl-card-body">
                        @if ($dataLapangan->keterangan)
                            <div class="dl-revisi-alert" style="margin-bottom:.75rem;">
                                <i class="las la-sticky-note dl-revisi-icon"></i>
                                <div>
                                    <div class="dl-revisi-title">
                                        Catatan — {{ $dataLapangan->updated_at?->format('d M Y, H:i') ?? '-' }}
                                    </div>
                                    <div class="dl-revisi-text">{{ $dataLapangan->keterangan }}</div>
                                </div>
                            </div>
                        @endif
                        <form
                            action="{{ route($routePrefix . '.data-lapangans.update-keterangan', $dataLapangan->hashed_id) }}"
                            method="POST">
                            @csrf
                            <label
                                style="font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dl-muted);display:block;margin-bottom:6px;">
                                Keterangan / Catatan
                            </label>
                            <textarea name="keterangan" class="dl-textarea" placeholder="Masukkan keterangan atau catatan tambahan...">{{ old('keterangan', $dataLapangan->keterangan ?? '') }}</textarea>
                            <div style="display:flex;justify-content:flex-end;margin-top:10px;">
                                <button type="submit" class="dl-btn dl-btn-success">
                                    <i class="las la-save"></i> Simpan Keterangan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

            {{-- ══════════ KOLOM KANAN ══════════ --}}
            <div style="display:flex;flex-direction:column;gap:1.25rem;">

                {{-- Card: Produk --}}
                <div class="dl-card">
                    <div class="dl-card-head">
                        <div class="dl-card-head-left">
                            <div class="dl-card-icon" style="background:#FFFBEB;">
                                <i class="las la-box-open" style="color:var(--dl-amber);"></i>
                            </div>
                            <span class="dl-card-title">Produk Terdaftar</span>
                        </div>
                        <span class="dl-badge dl-badge-pending" style="font-size:11px;">
                            {{ count($allProducts) }} produk
                        </span>
                    </div>
                    <div class="dl-card-body">
                        @if (count($allProducts) > 0)
                            <div class="dl-produk-grid">
                                @foreach ($allProducts as $idx => $prod)
                                    <div class="dl-produk-card">
                                        @if (!empty($prod['foto']))
                                            <img src="{{ asset('storage/' . $prod['foto']) }}" alt="{{ $prod['nama'] }}"
                                                class="dl-produk-card-img"
                                                onclick="viewFullImage('{{ asset('storage/' . $prod['foto']) }}', 'Foto Produk {{ $idx }}: {{ $prod['nama'] }}')"
                                                onerror="this.parentElement.innerHTML='<div class=dl-produk-card-img-placeholder><i class=\'las la-image\'></i></div>'+this.parentElement.innerHTML.replace(/<img[^>]*>/, '')">
                                        @else
                                            <div class="dl-produk-card-img-placeholder">
                                                <i class="las la-image"></i>
                                            </div>
                                        @endif
                                        <div class="dl-produk-card-body">
                                            <div class="dl-produk-num">Produk {{ $idx }}</div>
                                            <div class="dl-produk-name">{{ $prod['nama'] }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="dl-empty">
                                <i class="las la-box-open"></i>
                                Tidak ada produk terdaftar
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Card: Dokumentasi Foto --}}
                <div class="dl-card">
                    <div class="dl-card-head">
                        <div class="dl-card-head-left">
                            <div class="dl-card-icon" style="background:#FFFBEB;">
                                <i class="las la-images" style="color:var(--dl-amber);"></i>
                            </div>
                            <span class="dl-card-title">Dokumentasi Foto</span>
                        </div>
                        <button type="button" class="dl-btn dl-btn-ghost dl-btn-sm" data-bs-toggle="modal"
                            data-bs-target="#modalKolaseFoto">
                            <i class="las la-th"></i> Kolase
                        </button>
                    </div>

                    {{-- Foto utama — semua pakai hashed_id --}}
                    @php
                        $staticPhotos = [
                            [
                                'label' => 'Foto KTP',
                                'modal' => 'modalFotoKTP',
                                'foto' => $dataLapangan->foto_ktp,
                                'dl_route' => route(
                                    'superadmin.datalapangan.download-foto-ktp',
                                    $dataLapangan->hashed_id,
                                ),
                                'dl_label' => 'KTP',
                                'dl_class' => 'dl-btn-primary',
                            ],
                            [
                                'label' => 'Foto Rumah',
                                'modal' => 'modalFotoRumah',
                                'foto' => $dataLapangan->foto_rumah,
                                // KOREKSI: pakai hashed_id bukan id
                                'dl_route' => route(
                                    'superadmin.datalapangan.download-foto-rumah-pdf',
                                    $dataLapangan->hashed_id,
                                ),
                                'dl_label' => 'PDF',
                                'dl_class' => 'dl-btn-ghost',
                            ],
                            [
                                'label' => 'Foto Pendamping',
                                'modal' => 'modalFotoPendamping',
                                'foto' => $dataLapangan->foto_pendamping,
                                // KOREKSI: pakai hashed_id bukan id
                                'dl_route' => route(
                                    'superadmin.datalapangan.download-foto-pendamping',
                                    $dataLapangan->hashed_id,
                                ),
                                'dl_label' => 'Download',
                                'dl_class' => 'dl-btn-success',
                            ],
                        ];
                    @endphp

                    @foreach ($staticPhotos as $p)
                        <div class="dl-photo-row">
                            <div class="dl-photo-label">
                                @if (!empty($p['foto']))
                                    <img src="{{ asset('storage/' . $p['foto']) }}" class="dl-photo-thumb"
                                        alt="">
                                @else
                                    <div class="dl-photo-thumb-placeholder"><i class="las la-image"></i></div>
                                @endif
                                {{ $p['label'] }}
                            </div>
                            <div class="dl-photo-actions">
                                <button type="button" class="dl-btn dl-btn-ghost dl-btn-icon-only"
                                    data-bs-toggle="modal" data-bs-target="#{{ $p['modal'] }}">
                                    <i class="las la-eye"></i>
                                </button>
                                <a href="{{ $p['dl_route'] }}" class="dl-btn {{ $p['dl_class'] }} dl-btn-sm">
                                    <i class="las la-download"></i> {{ $p['dl_label'] }}
                                </a>
                            </div>
                        </div>
                    @endforeach

                    {{-- Foto Produk (semua slot yang ada fotonya) --}}
                    @foreach ($allProducts as $idx => $prod)
                        @if (!empty($prod['foto']))
                            <div class="dl-photo-row">
                                <div class="dl-photo-label">
                                    <img src="{{ asset('storage/' . $prod['foto']) }}" class="dl-photo-thumb"
                                        alt="">
                                    Foto Produk {{ $idx }}
                                    @if ($idx === 1)
                                        <span class="dl-badge dl-badge-pending"
                                            style="font-size:10px;padding:2px 8px;">Utama</span>
                                    @endif
                                </div>
                                <div class="dl-photo-actions">
                                    <button type="button" class="dl-btn dl-btn-ghost dl-btn-icon-only"
                                        onclick="viewFullImage('{{ asset('storage/' . $prod['foto']) }}', 'Foto Produk {{ $idx }}: {{ $prod['nama'] }}')">
                                        <i class="las la-eye"></i>
                                    </button>
                                    @if ($idx === 1)
                                        <a href="{{ route($routePrefix . '.datalapangan.download-foto-produk', $dataLapangan->hashed_id) }}"
                                            class="dl-btn dl-btn-success dl-btn-sm">
                                            <i class="las la-download"></i> Download
                                        </a>
                                    @else
                                        <a href="{{ asset('storage/' . $prod['foto']) }}" download
                                            class="dl-btn dl-btn-success dl-btn-sm">
                                            <i class="las la-download"></i> Download
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach

                    {{-- Foto Spotcheck --}}
                    <div style="padding:12px 14px;">
                        <div class="dl-divider"><span class="dl-divider-label">Foto Spotcheck</span>
                            <div class="dl-divider-line"></div>
                        </div>
                        @if ($dataLapangan->spotchecks && $dataLapangan->spotchecks->count() > 0)
                            @foreach ($dataLapangan->spotchecks as $index => $spotcheck)
                                @if ($spotcheck->foto_pu)
                                    <div class="dl-spotcheck-item">
                                        <div style="display:flex;align-items:center;gap:8px;">
                                            <img src="{{ asset('storage/' . $spotcheck->foto_pu) }}"
                                                class="dl-photo-thumb" alt="">
                                            <span style="font-size:13px;font-weight:500;">Spotcheck {{ $index + 1 }}
                                                @if ($spotcheck->nama_spotcheck)
                                                    <span style="color:var(--dl-muted);font-weight:400;">—
                                                        {{ $spotcheck->nama_spotcheck }}</span>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="dl-photo-actions">
                                            <button type="button" class="dl-btn dl-btn-ghost dl-btn-icon-only"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalFotoSpotcheck{{ $spotcheck->id }}">
                                                <i class="las la-eye"></i>
                                            </button>
                                            <a href="{{ asset('storage/' . $spotcheck->foto_pu) }}" download
                                                class="dl-btn dl-btn-success dl-btn-sm">
                                                <i class="las la-download"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div class="dl-empty" style="padding:.75rem 0;">
                                <i class="las la-map-marker"></i>
                                Belum ada foto spotcheck
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Card: Dokumentasi File (OSS & Sihalal) --}}
                <div class="dl-card">
                    <div class="dl-card-head">
                        <div class="dl-card-head-left">
                            <div class="dl-card-icon" style="background:var(--dl-green-lt);">
                                <i class="las la-file-alt" style="color:var(--dl-green);"></i>
                            </div>
                            <span class="dl-card-title">Dokumentasi File</span>
                        </div>
                    </div>
                    <div class="dl-card-body">

                        {{-- OSS --}}
                        <div class="dl-divider"><span class="dl-divider-label">File OSS</span>
                            <div class="dl-divider-line"></div>
                        </div>
                        <div class="dl-file-row {{ $dataLapangan->file_oss ? 'available' : 'missing' }}"
                            style="margin-bottom:10px;">
                            <div class="dl-file-icon">PDF</div>
                            <div class="dl-file-label">
                                {{ $dataLapangan->file_oss ? 'File OSS tersedia' : 'File OSS belum tersedia' }}
                            </div>
                            @if ($dataLapangan->file_oss)
                                <a href="{{ asset('storage/' . $dataLapangan->file_oss) }}" target="_blank"
                                    class="dl-btn dl-btn-success dl-btn-sm">
                                    <i class="las la-download"></i> Unduh
                                </a>
                                {{-- KOREKSI: deleteFile pakai hashed_id --}}
                                <button type="button" class="dl-btn dl-btn-danger dl-btn-sm dl-btn-icon-only"
                                    onclick="deleteFile('{{ $dataLapangan->hashed_id }}', 'oss')">
                                    <i class="las la-trash"></i>
                                </button>
                            @endif
                        </div>
                        <form action="{{ route($routePrefix . '.data-lapangans.upload-file', $dataLapangan->hashed_id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="file_type" value="oss">
                            <div class="dl-upload-group">
                                <input type="file" name="file" id="file_oss" accept=".pdf" required>
                                <button type="submit" class="dl-btn dl-btn-primary dl-btn-sm">
                                    <i class="las la-upload"></i> Upload
                                </button>
                            </div>
                            <p style="font-size:11.5px;color:var(--dl-muted);margin:4px 0 0;">Format PDF · Maks 5MB</p>
                        </form>

                        <div style="height:1px;background:var(--dl-border);margin:1.25rem 0;"></div>

                        {{-- Sihalal --}}
                        <div class="dl-divider"><span class="dl-divider-label">File Sihalal</span>
                            <div class="dl-divider-line"></div>
                        </div>
                        <div class="dl-file-row {{ $dataLapangan->file_sihalal ? 'available' : 'missing' }}"
                            style="margin-bottom:10px;">
                            <div class="dl-file-icon">PDF</div>
                            <div class="dl-file-label">
                                {{ $dataLapangan->file_sihalal ? 'File Sihalal tersedia' : 'File Sihalal belum tersedia' }}
                            </div>
                            @if ($dataLapangan->file_sihalal)
                                <a href="{{ asset('storage/' . $dataLapangan->file_sihalal) }}" target="_blank"
                                    class="dl-btn dl-btn-success dl-btn-sm">
                                    <i class="las la-download"></i> Unduh
                                </a>
                                {{-- KOREKSI: deleteFile pakai hashed_id --}}
                                <button type="button" class="dl-btn dl-btn-danger dl-btn-sm dl-btn-icon-only"
                                    onclick="deleteFile('{{ $dataLapangan->hashed_id }}', 'sihalal')">
                                    <i class="las la-trash"></i>
                                </button>
                            @endif
                        </div>
                        <form action="{{ route($routePrefix . '.data-lapangans.upload-file', $dataLapangan->hashed_id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="file_type" value="sihalal">
                            <div class="dl-upload-group">
                                <input type="file" name="file" id="file_sihalal" accept=".pdf" required>
                                <button type="submit" class="dl-btn dl-btn-primary dl-btn-sm">
                                    <i class="las la-upload"></i> Upload
                                </button>
                            </div>
                            <p style="font-size:11.5px;color:var(--dl-muted);margin:4px 0 0;">Format PDF · Maks 5MB</p>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>


    {{-- ══════════════════════════════════════ --}}
    {{-- MODALS                                --}}
    {{-- ══════════════════════════════════════ --}}

    {{-- Modal Update Email & Verifikasi --}}
    <div class="modal fade dl-modal" id="modalUpdateEmail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="las la-envelope" style="color:var(--dl-blue);"></i>
                        Update Email &amp; Verifikasi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    {{-- Step 1: Checklist --}}
                    <div id="stepChecklist">
                        <div
                            style="display:flex;gap:10px;padding:12px 14px;background:#FFFBEB;border:1px solid #FDE68A;border-radius:10px;margin-bottom:1rem;">
                            <i class="las la-exclamation-triangle"
                                style="color:var(--dl-amber);font-size:16px;flex-shrink:0;margin-top:2px;"></i>
                            <div style="font-size:13px;color:#78350F;"><strong>PERHATIAN</strong> — Jawab semua pertanyaan
                                sebelum melanjutkan.</div>
                        </div>

                        @php
                            $checklist = [
                                [
                                    'id' => 'foto',
                                    'name' => 'q_foto',
                                    'label' =>
                                        'Apakah <strong>Foto</strong> sudah dicek dan dalam kondisi benar serta sesuai?',
                                    'yes_val' => 'ya',
                                    'yes_txt' => 'Ya',
                                    'no_txt' => 'Tidak',
                                    'warn_id' => 'warn_foto',
                                    'warn_msg' => 'Foto harus dicek terlebih dahulu.',
                                ],
                                [
                                    'id' => 'nik',
                                    'name' => 'q_nik',
                                    'label' =>
                                        'Apakah <strong>NIK</strong> sudah dicek melalui <a href="https://oss.go.id" target="_blank" class="fw-semibold">oss.go.id</a>?',
                                    'yes_val' => 'sudah',
                                    'yes_txt' => 'Sudah',
                                    'no_txt' => 'Belum',
                                    'warn_id' => 'warn_nik',
                                    'warn_msg' => 'NIK harus dicek melalui oss.go.id.',
                                ],
                                [
                                    'id' => 'email',
                                    'name' => 'q_email',
                                    'label' => 'Apakah <strong>Email</strong> sudah dibuat?',
                                    'yes_val' => 'ya',
                                    'yes_txt' => 'Ya',
                                    'no_txt' => 'Belum',
                                    'warn_id' => 'warn_email_q',
                                    'warn_msg' => 'Email harus sudah dibuat.',
                                ],
                            ];
                        @endphp

                        @foreach ($checklist as $i => $q)
                            <div class="dl-check-card" id="card_{{ $q['id'] }}">
                                <div class="dl-check-q">
                                    <span class="dl-check-num">{{ $i + 1 }}</span>
                                    {!! $q['label'] !!}
                                </div>
                                <div class="dl-radio-group">
                                    <label class="dl-radio-label">
                                        <input type="radio" name="{{ $q['name'] }}" value="{{ $q['yes_val'] }}"
                                            class="check-answer">
                                        <i class="las la-check" style="color:var(--dl-green);font-size:13px;"></i>
                                        {{ $q['yes_txt'] }}
                                    </label>
                                    <label class="dl-radio-label">
                                        <input type="radio" name="{{ $q['name'] }}" value="tidak"
                                            class="check-answer">
                                        <i class="las la-times" style="color:var(--dl-rose);font-size:13px;"></i>
                                        {{ $q['no_txt'] }}
                                    </label>
                                </div>
                                <div id="{{ $q['warn_id'] }}"
                                    style="display:none;margin-top:8px;font-size:12px;color:var(--dl-rose);padding:6px 10px;background:var(--dl-rose-lt);border-radius:6px;">
                                    <i class="las la-times-circle me-1"></i>{{ $q['warn_msg'] }}
                                </div>
                            </div>
                        @endforeach

                        {{-- Q4 --}}
                        <div class="dl-check-card" id="cardQ4">
                            <div class="dl-check-q">
                                <span class="dl-check-num">4</span>
                                Apakah ada komentar pada <strong>Form Keterangan Revisi</strong>?
                            </div>
                            <div class="dl-radio-group">
                                <label class="dl-radio-label">
                                    <input type="radio" name="q_keterangan" value="ya" class="check-answer">
                                    <i class="las la-check" style="color:var(--dl-amber);font-size:13px;"></i> Ya, sudah
                                    dihapus
                                </label>
                                <label class="dl-radio-label">
                                    <input type="radio" name="q_keterangan" value="tidak" class="check-answer">
                                    <i class="las la-check" style="color:var(--dl-green);font-size:13px;"></i> Tidak ada
                                </label>
                            </div>
                        </div>

                        <div style="display:flex;justify-content:flex-end;margin-top:1rem;">
                            <button type="button" class="dl-btn dl-btn-primary" onclick="validateChecklist()">
                                Lanjutkan <i class="las la-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Step 2: Form --}}
                    {{-- Step 2: Form --}}
                    <div id="stepForm" class="d-none">
                        <div
                            style="display:flex;gap:10px;padding:12px 14px;background:var(--dl-green-lt);border:1px solid #A7F3D0;border-radius:10px;margin-bottom:1.25rem;">
                            <i class="las la-check-circle"
                                style="color:var(--dl-green);font-size:16px;flex-shrink:0;margin-top:2px;"></i>
                            <div style="font-size:13px;color:#065F46;"><strong>Semua pengecekan terpenuhi.</strong> Isi
                                data verifikasi di bawah ini.</div>
                        </div>

                        <form action="{{ route($routePrefix . '.data-lapangans.update-email', $dataLapangan->hashed_id) }}"
                            method="POST" id="formVerifikasi">
                            @csrf

                            {{-- EMAIL PREFIX + domain tetap --}}
                            <div style="margin-bottom:.85rem;">
                                <label class="form-label">Email</label>
                                <div style="display:flex;align-items:center;gap:0;">
                                    <input type="text" name="email_prefix" id="emailPrefixInput"
                                        class="form-control @error('email_prefix') is-invalid @enderror"
                                        style="border-radius:var(--dl-radius-sm) 0 0 var(--dl-radius-sm);border-right:none;"
                                        value="{{ old('email_prefix', $dataLapangan->email ? explode('@', $dataLapangan->email)[0] : '') }}"
                                        placeholder="namauser" required autocomplete="off">
                                    <span
                                        style="padding:8px 12px;background:#F1F5F9;border:1.5px solid var(--dl-border);border-left:none;border-radius:0 var(--dl-radius-sm) var(--dl-radius-sm) 0;font-size:13px;color:var(--dl-muted);white-space:nowrap;font-weight:600;">
                                        @kawulohalal.id
                                    </span>
                                </div>
                                @error('email_prefix')
                                    <div style="font-size:12px;color:var(--dl-rose);margin-top:4px;">{{ $message }}</div>
                                @enderror

                                {{-- Status cek email --}}
                                <div id="emailCheckStatus"
                                    style="margin-top:6px;font-size:12px;display:none;padding:6px 10px;border-radius:6px;">
                                </div>

                                <button type="button" onclick="checkEmailExists()" class="dl-btn dl-btn-ghost dl-btn-sm"
                                    style="margin-top:6px;">
                                    <i class="las la-search"></i> Cek Ketersediaan Email
                                </button>
                            </div>

                            <div style="margin-bottom:.85rem;">
                                <label class="form-label">Verifikator</label>
                                <select name="verifikator_id"
                                    class="form-select @error('verifikator_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Verifikator --</option>
                                    @foreach ($verifikators as $v)
                                        <option value="{{ $v->id }}"
                                            {{ old('verifikator_id', $dataLapangan->verifikator_id) == $v->id ? 'selected' : '' }}>
                                            {{ $v->nama_lengkap }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('verifikator_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div style="margin-bottom:.85rem;">
                                <label class="form-label">Tanggal Verifikasi</label>
                                <input type="date" name="tanggal_verifikasi"
                                    class="form-control @error('tanggal_verifikasi') is-invalid @enderror"
                                    value="{{ old('tanggal_verifikasi', optional($dataLapangan->tanggal_verifikasi)->format('Y-m-d')) }}">
                                @error('tanggal_verifikasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div style="display:flex;justify-content:space-between;">
                                <button type="button" class="dl-btn dl-btn-ghost" onclick="backToChecklist()">
                                    <i class="las la-arrow-left"></i> Kembali
                                </button>
                                <button type="submit" class="dl-btn dl-btn-primary" id="btnSubmitEmail">
                                    <i class="las la-save"></i> Simpan &amp; Verifikasi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Revisi --}}
    <div class="modal fade dl-modal" id="modalRevisi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="las la-redo" style="color:var(--dl-rose);"></i> Revisi Data
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route($routePrefix . '.data-lapangans.update-keterangan', $dataLapangan->hashed_id) }}"
                    method="POST">
                    @csrf
                    <div class="modal-body">
                        <div
                            style="display:flex;gap:10px;padding:12px 14px;background:#FFFBEB;border:1px solid #FDE68A;border-radius:10px;margin-bottom:1rem;">
                            <i class="las la-exclamation-triangle"
                                style="color:var(--dl-amber);font-size:16px;flex-shrink:0;margin-top:2px;"></i>
                            <div style="font-size:13px;color:#78350F;"><strong>PERHATIAN</strong> — Pastikan data sudah
                                divalidasi!</div>
                        </div>
                        <label
                            style="font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dl-muted);display:block;margin-bottom:6px;">
                            Keterangan Revisi
                        </label>
                        <textarea name="keterangan" rows="4" class="dl-textarea @error('keterangan') is-invalid @enderror"
                            placeholder="Masukkan keterangan...">{{ old('keterangan', $dataLapangan->keterangan) }}</textarea>
                        @error('keterangan')
                            <div style="font-size:12px;color:var(--dl-rose);margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="dl-btn dl-btn-ghost" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="dl-btn dl-btn-primary"><i class="las la-save"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit Email Sihalal --}}
    <div class="modal fade dl-modal" id="modalEditEmailSihalal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="las la-envelope" style="color:var(--dl-blue);"></i> Edit Email Sihalal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route($routePrefix . '.data-lapangans.update-email-sihalal', $dataLapangan->hashed_id) }}"
                    method="POST">
                    @csrf @method('PATCH')
                    <div class="modal-body">
                        <label class="form-label">Email Sihalal</label>
                        <input type="email" name="email_sihalal"
                            class="form-control @error('email_sihalal') is-invalid @enderror"
                            value="{{ old('email_sihalal', $dataLapangan->email_sihalal) }}"
                            placeholder="Masukkan email sihalal" required>
                        @error('email_sihalal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="dl-btn dl-btn-ghost" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="dl-btn dl-btn-primary"><i class="las la-save"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Kolase Foto --}}
    <div class="modal fade dl-modal" id="modalKolaseFoto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="las la-th" style="color:var(--dl-amber);"></i> Kolase Dokumentasi
                        Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3" id="collageContent">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light py-2 px-3 border-bottom"
                                    style="font-size:12px;font-weight:600;">Foto Pendamping</div>
                                <img src="{{ asset('storage/' . $dataLapangan->foto_pendamping) }}" alt="Foto Pendamping"
                                    class="dl-collage-img"
                                    onclick="viewFullImage('{{ asset('storage/' . $dataLapangan->foto_pendamping) }}', 'Foto Pendamping')">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light py-2 px-3 border-bottom"
                                    style="font-size:12px;font-weight:600;">Foto Produk Utama</div>
                                <img src="{{ asset('storage/' . $dataLapangan->foto_produk) }}" alt="Foto Produk"
                                    class="dl-collage-img"
                                    onclick="viewFullImage('{{ asset('storage/' . $dataLapangan->foto_produk) }}', 'Foto Produk')">
                            </div>
                        </div>
                        {{-- Produk tambahan --}}
                        @foreach ($allProducts as $idx => $prod)
                            @if ($idx > 1 && !empty($prod['foto']))
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-light py-2 px-3 border-bottom"
                                            style="font-size:12px;font-weight:600;">Foto Produk {{ $idx }}</div>
                                        <img src="{{ asset('storage/' . $prod['foto']) }}"
                                            alt="Foto Produk {{ $idx }}" class="dl-collage-img"
                                            onclick="viewFullImage('{{ asset('storage/' . $prod['foto']) }}', 'Foto Produk {{ $idx }}: {{ $prod['nama'] }}')">
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="dl-btn dl-btn-ghost" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="dl-btn dl-btn-success" onclick="downloadCollage()"><i
                            class="las la-download"></i> Download Kolase</button>
                    <button type="button" class="dl-btn dl-btn-primary" onclick="printCollage()"><i
                            class="las la-print"></i> Print</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Full Image --}}
    <div class="modal fade dl-modal" id="modalFullImage" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fullImageTitle">Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-3">
                    <img id="fullImageSrc" src="" alt="" class="img-fluid rounded"
                        style="max-height:580px;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="dl-btn dl-btn-ghost" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="dl-btn dl-btn-success" onclick="downloadSingleImage()"><i
                            class="las la-download"></i> Download</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals Foto Statis --}}
    @foreach ([['id' => 'modalFotoKTP', 'title' => 'Foto KTP', 'src' => asset('storage/' . $dataLapangan->foto_ktp)], ['id' => 'modalFotoRumah', 'title' => 'Foto Rumah', 'src' => asset('storage/' . $dataLapangan->foto_rumah)], ['id' => 'modalFotoPendamping', 'title' => 'Foto Pendamping', 'src' => asset('storage/' . $dataLapangan->foto_pendamping)]] as $modal)
        <div class="modal fade dl-modal" id="{{ $modal['id'] }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $modal['title'] }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center p-3">
                        <img src="{{ $modal['src'] }}" alt="{{ $modal['title'] }}" class="img-fluid rounded"
                            style="max-height:520px;">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="dl-btn dl-btn-ghost" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Modals Foto Spotcheck --}}
    @if ($dataLapangan->spotchecks && $dataLapangan->spotchecks->count() > 0)
        @foreach ($dataLapangan->spotchecks as $spotcheck)
            @if ($spotcheck->foto_pu)
                <div class="modal fade dl-modal" id="modalFotoSpotcheck{{ $spotcheck->id }}" tabindex="-1"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Foto Spotcheck @if ($spotcheck->nama_spotcheck)
                                        — {{ $spotcheck->nama_spotcheck }}
                                    @endif
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center p-3">
                                <img src="{{ asset('storage/' . $spotcheck->foto_pu) }}" class="img-fluid rounded"
                                    style="max-height:520px;">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="dl-btn dl-btn-ghost" data-bs-dismiss="modal">Tutup</button>
                                <a href="{{ asset('storage/' . $spotcheck->foto_pu) }}" download
                                    class="dl-btn dl-btn-success">
                                    <i class="las la-download"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endif

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Sora:wght@400;600;700&display=swap');

        :root {
            --dl-blue: #1A5FC8;
            --dl-blue-dk: #0F3A8A;
            --dl-blue-lt: #EEF4FF;
            --dl-teal: #0D9488;
            --dl-teal-lt: #F0FDFA;
            --dl-amber: #D97706;
            --dl-amber-lt: #FFFBEB;
            --dl-rose: #E11D48;
            --dl-rose-lt: #FFF1F2;
            --dl-green: #059669;
            --dl-green-lt: #ECFDF5;
            --dl-slate: #475569;
            --dl-border: #E2E8F0;
            --dl-bg: #F8FAFC;
            --dl-card: #FFFFFF;
            --dl-text: #0F172A;
            --dl-muted: #64748B;
            --dl-radius: 14px;
            --dl-radius-sm: 8px;
            --dl-shadow: 0 1px 3px rgba(0, 0, 0, .06), 0 4px 16px rgba(0, 0, 0, .06);
            --dl-shadow-md: 0 4px 6px rgba(0, 0, 0, .05), 0 10px 30px rgba(0, 0, 0, .1);
        }

        body {
            background: var(--dl-bg);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--dl-text);
        }

        .dl-page {
            padding: 1.5rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .dl-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.25rem 1.5rem;
            background: var(--dl-card);
            border-radius: var(--dl-radius);
            box-shadow: var(--dl-shadow);
            margin-bottom: 1.25rem;
            border-left: 4px solid var(--dl-blue);
        }

        .dl-header-title {
            font-family: 'Sora', sans-serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--dl-text);
            margin-bottom: 3px;
        }

        .dl-header-meta {
            font-size: 12.5px;
            color: var(--dl-muted);
        }

        .dl-header-meta strong {
            color: var(--dl-text);
            font-weight: 600;
        }

        .dl-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 99px;
            font-size: 11.5px;
            font-weight: 600;
            letter-spacing: .03em;
        }

        .dl-badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
            opacity: .7;
        }

        .dl-badge-pending {
            background: #FEF3C7;
            color: #92400E;
        }

        .dl-badge-verif {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .dl-badge-oss {
            background: #E0F2FE;
            color: #0369A1;
        }

        .dl-badge-sihalal {
            background: #EDE9FE;
            color: #6D28D9;
        }

        .dl-badge-terbit {
            background: #DCFCE7;
            color: #166534;
        }

        .dl-badge-ditolak {
            background: #F1F5F9;
            color: #475569;
        }

        .dl-badge-revisi {
            background: #FFE4E6;
            color: #9F1239;
        }

        .dl-badge-dibayar {
            background: #DCFCE7;
            color: #166534;
        }

        .dl-badge-pengajuan {
            background: #E0F2FE;
            color: #0369A1;
        }

        .dl-stepper {
            background: var(--dl-card);
            border-radius: var(--dl-radius);
            box-shadow: var(--dl-shadow);
            padding: 1rem 2rem;
            margin-bottom: 1.25rem;
            overflow-x: auto;
        }

        .dl-stepper-inner {
            display: flex;
            align-items: center;
            min-width: 520px;
        }

        .dl-step {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .dl-step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 16px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: var(--dl-border);
            z-index: 0;
            transition: background .4s;
        }

        .dl-step.done::after {
            background: var(--dl-green);
        }

        .dl-step.active::after {
            background: linear-gradient(90deg, var(--dl-blue), var(--dl-border));
        }

        .dl-step-dot {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            z-index: 1;
            border: 2px solid var(--dl-border);
            background: var(--dl-bg);
            color: var(--dl-muted);
            transition: all .3s;
        }

        .dl-step.done .dl-step-dot {
            background: var(--dl-green);
            color: #fff;
            border-color: var(--dl-green);
        }

        .dl-step.active .dl-step-dot {
            background: var(--dl-blue);
            color: #fff;
            border-color: var(--dl-blue);
            box-shadow: 0 0 0 5px rgba(26, 95, 200, .15);
        }

        .dl-step-label {
            margin-top: 8px;
            font-size: 10.5px;
            text-align: center;
            white-space: nowrap;
            color: var(--dl-muted);
            font-weight: 500;
        }

        .dl-step.done .dl-step-label {
            color: var(--dl-green);
            font-weight: 600;
        }

        .dl-step.active .dl-step-label {
            color: var(--dl-blue);
            font-weight: 700;
        }

        .dl-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
        }

        @media (max-width: 992px) {
            .dl-grid {
                grid-template-columns: 1fr;
            }
        }

        .dl-card {
            background: var(--dl-card);
            border-radius: var(--dl-radius);
            box-shadow: var(--dl-shadow);
            overflow: hidden;
            transition: box-shadow .2s;
        }

        .dl-card:hover {
            box-shadow: var(--dl-shadow-md);
        }

        .dl-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .85rem 1.25rem;
            border-bottom: 1px solid var(--dl-border);
            background: linear-gradient(135deg, #FAFBFF 0%, #F5F8FF 100%);
        }

        .dl-card-head-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dl-card-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .dl-card-title {
            font-family: 'Sora', sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: var(--dl-text);
        }

        .dl-card-body {
            padding: 1.25rem;
        }

        .dl-info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .dl-info-table tr {
            border-bottom: 1px solid #F1F5F9;
        }

        .dl-info-table tr:last-child {
            border-bottom: none;
        }

        .dl-info-table td {
            padding: 10px 4px;
            vertical-align: top;
        }

        .dl-info-table .dl-key {
            font-size: 11.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--dl-muted);
            width: 42%;
            padding-right: 12px;
            white-space: nowrap;
        }

        .dl-info-table .dl-val {
            font-size: 13.5px;
            color: var(--dl-text);
        }

        .dl-divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 1rem 0 .75rem;
        }

        .dl-divider-label {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--dl-muted);
            white-space: nowrap;
        }

        .dl-divider-line {
            flex: 1;
            height: 1px;
            background: var(--dl-border);
        }

        .dl-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: var(--dl-radius-sm);
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            border: 1.5px solid transparent;
            transition: all .2s;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .dl-btn:hover {
            transform: translateY(-1px);
        }

        .dl-btn-primary {
            background: var(--dl-blue);
            color: #fff;
            border-color: var(--dl-blue);
        }

        .dl-btn-primary:hover {
            background: var(--dl-blue-dk);
        }

        .dl-btn-danger {
            background: var(--dl-rose-lt);
            color: var(--dl-rose);
            border-color: #FECDD3;
        }

        .dl-btn-danger:hover {
            background: #FFE4E6;
        }

        .dl-btn-success {
            background: var(--dl-green-lt);
            color: var(--dl-green);
            border-color: #A7F3D0;
        }

        .dl-btn-success:hover {
            background: #D1FAE5;
        }

        .dl-btn-ghost {
            background: #F1F5F9;
            color: var(--dl-slate);
            border-color: var(--dl-border);
        }

        .dl-btn-ghost:hover {
            background: #E2E8F0;
        }

        .dl-btn-amber {
            background: var(--dl-amber-lt);
            color: var(--dl-amber);
            border-color: #FDE68A;
        }

        .dl-btn-amber:hover {
            background: #FEF3C7;
        }

        .dl-btn-sm {
            padding: 5px 10px;
            font-size: 11.5px;
        }

        .dl-btn-icon-only {
            width: 32px;
            height: 32px;
            padding: 0;
            justify-content: center;
            border-radius: var(--dl-radius-sm);
        }

        .dl-verif-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            background: var(--dl-green-lt);
            border: 1px solid #A7F3D0;
            border-radius: 10px;
        }

        .dl-verif-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--dl-green);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .dl-verif-name {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--dl-text);
        }

        .dl-verif-date {
            font-size: 11.5px;
            color: var(--dl-muted);
        }

        .dl-entry-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            border-radius: 10px;
            background: #F8FAFC;
            border: 1px solid var(--dl-border);
            margin-bottom: 8px;
        }

        .dl-entry-row:last-child {
            margin-bottom: 0;
        }

        .dl-entry-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--dl-text);
        }

        .dl-entry-meta {
            font-size: 11.5px;
            color: var(--dl-muted);
        }

        .dl-photo-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 11px 14px;
            border-bottom: 1px solid #F1F5F9;
            transition: background .15s;
        }

        .dl-photo-row:last-child {
            border-bottom: none;
        }

        .dl-photo-row:hover {
            background: #FAFBFF;
        }

        .dl-photo-label {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--dl-text);
        }

        .dl-photo-thumb {
            width: 36px;
            height: 36px;
            border-radius: 7px;
            object-fit: cover;
            border: 1.5px solid var(--dl-border);
            flex-shrink: 0;
        }

        .dl-photo-thumb-placeholder {
            width: 36px;
            height: 36px;
            border-radius: 7px;
            background: #F1F5F9;
            border: 1.5px dashed var(--dl-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: var(--dl-muted);
            flex-shrink: 0;
        }

        .dl-photo-actions {
            display: flex;
            gap: 6px;
        }

        .dl-produk-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 12px;
            margin-top: 4px;
        }

        .dl-produk-card {
            border: 1.5px solid var(--dl-border);
            border-radius: 12px;
            overflow: hidden;
            background: #FAFBFF;
            transition: box-shadow .2s, transform .2s;
        }

        .dl-produk-card:hover {
            box-shadow: 0 4px 16px rgba(26, 95, 200, .12);
            transform: translateY(-2px);
        }

        .dl-produk-card-img {
            width: 100%;
            aspect-ratio: 4/3;
            object-fit: cover;
            display: block;
            cursor: pointer;
            border-bottom: 1px solid var(--dl-border);
        }

        .dl-produk-card-img-placeholder {
            width: 100%;
            aspect-ratio: 4/3;
            background: #F1F5F9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: #CBD5E1;
            border-bottom: 1px solid var(--dl-border);
        }

        .dl-produk-card-body {
            padding: 10px 12px;
        }

        .dl-produk-num {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--dl-blue);
            margin-bottom: 3px;
        }

        .dl-produk-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--dl-text);
            line-height: 1.4;
        }

        .dl-file-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 8px;
        }

        .dl-file-row.available {
            background: var(--dl-green-lt);
            border: 1px solid #A7F3D0;
        }

        .dl-file-row.missing {
            background: #FFF1F2;
            border: 1px solid #FECDD3;
        }

        .dl-file-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .available .dl-file-icon {
            background: var(--dl-green);
            color: #fff;
        }

        .missing .dl-file-icon {
            background: var(--dl-rose);
            color: #fff;
        }

        .dl-file-label {
            flex: 1;
            font-size: 13px;
            font-weight: 500;
        }

        .available .dl-file-label {
            color: var(--dl-green);
        }

        .missing .dl-file-label {
            color: var(--dl-rose);
        }

        .dl-upload-group {
            display: flex;
            gap: 8px;
            align-items: stretch;
            margin-top: 6px;
        }

        .dl-upload-group input[type="file"] {
            flex: 1;
            font-size: 12.5px;
            padding: 6px 10px;
            border: 1.5px solid var(--dl-border);
            border-radius: var(--dl-radius-sm);
            background: #F8FAFC;
            color: var(--dl-text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-width: 0;
        }

        .dl-upload-group input[type="file"]:focus {
            outline: none;
            border-color: var(--dl-blue);
        }

        .dl-revisi-alert {
            display: flex;
            gap: 10px;
            padding: 12px 14px;
            background: #FFFBEB;
            border: 1px solid #FDE68A;
            border-radius: 10px;
            margin-bottom: .75rem;
        }

        .dl-revisi-icon {
            font-size: 16px;
            color: var(--dl-amber);
            flex-shrink: 0;
            margin-top: 1px;
        }

        .dl-revisi-title {
            font-size: 11.5px;
            font-weight: 700;
            color: var(--dl-amber);
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 4px;
        }

        .dl-revisi-text {
            font-size: 13px;
            color: #78350F;
        }

        .dl-spotcheck-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 9px 0;
            border-bottom: 1px solid #F1F5F9;
        }

        .dl-spotcheck-item:last-child {
            border-bottom: none;
        }

        .dl-textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid var(--dl-border);
            border-radius: var(--dl-radius-sm);
            font-size: 13.5px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--dl-text);
            resize: vertical;
            min-height: 90px;
            transition: border-color .2s;
        }

        .dl-textarea:focus {
            outline: none;
            border-color: var(--dl-blue);
            box-shadow: 0 0 0 3px rgba(26, 95, 200, .08);
        }

        .dl-alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .dl-alert-success {
            background: var(--dl-green-lt);
            border: 1px solid #A7F3D0;
            color: #065F46;
        }

        .dl-alert-danger {
            background: var(--dl-rose-lt);
            border: 1px solid #FECDD3;
            color: #9F1239;
        }

        .dl-alert-icon {
            font-size: 16px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .dl-check-card {
            border: 1.5px solid var(--dl-border);
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 10px;
            transition: border-color .2s;
        }

        .dl-check-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--dl-blue-lt);
            color: var(--dl-blue);
            font-size: 11px;
            font-weight: 700;
            margin-right: 8px;
            flex-shrink: 0;
        }

        .dl-check-q {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--dl-text);
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .dl-radio-group {
            display: flex;
            gap: 10px;
        }

        .dl-radio-label {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            padding: 5px 12px;
            border-radius: 7px;
            border: 1.5px solid var(--dl-border);
            font-size: 12.5px;
            font-weight: 600;
            transition: all .15s;
        }

        .dl-radio-label:has(input:checked) {
            border-color: var(--dl-blue);
            background: var(--dl-blue-lt);
            color: var(--dl-blue);
        }

        .dl-radio-label input {
            display: none;
        }

        .dl-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            color: var(--dl-slate);
            text-decoration: none;
            padding: 6px 12px;
            border: 1.5px solid var(--dl-border);
            border-radius: var(--dl-radius-sm);
            background: #fff;
            transition: all .2s;
        }

        .dl-back:hover {
            background: #F1F5F9;
            color: var(--dl-text);
            transform: translateX(-2px);
        }

        .dl-modal .modal-content {
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .15);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .dl-modal .modal-header {
            background: linear-gradient(135deg, #FAFBFF, #F0F4FF);
            border-bottom: 1px solid var(--dl-border);
            border-radius: 16px 16px 0 0;
            padding: 1.1rem 1.5rem;
        }

        .dl-modal .modal-title {
            font-family: 'Sora', sans-serif;
            font-size: 15px;
            font-weight: 700;
            color: var(--dl-text);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dl-modal .modal-body {
            padding: 1.5rem;
        }

        .dl-modal .modal-footer {
            border-top: 1px solid var(--dl-border);
            padding: 1rem 1.5rem;
        }

        .dl-modal .form-label {
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--dl-muted);
            margin-bottom: 6px;
        }

        .dl-modal .form-control,
        .dl-modal .form-select {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13.5px;
            border: 1.5px solid var(--dl-border);
            border-radius: var(--dl-radius-sm);
            padding: 8px 12px;
            transition: border-color .2s;
        }

        .dl-modal .form-control:focus,
        .dl-modal .form-select:focus {
            border-color: var(--dl-blue);
            box-shadow: 0 0 0 3px rgba(26, 95, 200, .1);
        }

        .dl-collage-img {
            height: 260px;
            object-fit: cover;
            width: 100%;
            cursor: pointer;
            border-radius: 0 0 10px 10px;
        }

        .dl-empty {
            text-align: center;
            padding: 1.5rem;
            color: var(--dl-muted);
            font-size: 13px;
        }

        .dl-empty i {
            font-size: 28px;
            display: block;
            margin-bottom: 8px;
            opacity: .4;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dl-card {
            animation: fadeUp .35s ease both;
        }

        .dl-card:nth-child(1) {
            animation-delay: .05s;
        }

        .dl-card:nth-child(2) {
            animation-delay: .1s;
        }

        .dl-card:nth-child(3) {
            animation-delay: .15s;
        }

        .dl-card:nth-child(4) {
            animation-delay: .2s;
        }

        .dl-card:nth-child(5) {
            animation-delay: .25s;
        }

        .dl-actions-group {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }
    </style>

    {{-- Auto-open modal on validation error --}}
    @if ($errors->hasAny(['email', 'verifikator_id', 'tanggal_verifikasi']))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = new bootstrap.Modal(document.getElementById('modalUpdateEmail'));
                modal.show();
                document.getElementById('stepChecklist').classList.add('d-none');
                document.getElementById('stepForm').classList.remove('d-none');
            });
        </script>
    @endif
    @if ($errors->hasAny(['keterangan']))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                new bootstrap.Modal(document.getElementById('modalRevisi')).show();
            });
        </script>
    @endif

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        // ── CHECKLIST ──
        function validateChecklist() {
            const checks = [{
                    name: 'q_foto',
                    val: 'ya',
                    warn: 'warn_foto'
                },
                {
                    name: 'q_nik',
                    val: 'sudah',
                    warn: 'warn_nik'
                },
                {
                    name: 'q_email',
                    val: 'ya',
                    warn: 'warn_email_q'
                },
            ];
            let valid = true;
            checks.forEach(c => {
                const el = document.querySelector(`input[name="${c.name}"]:checked`);
                const warnEl = document.getElementById(c.warn);
                if (!el || el.value !== c.val) {
                    warnEl.style.display = 'block';
                    valid = false;
                } else {
                    warnEl.style.display = 'none';
                }
            });
            const qKet = document.querySelector('input[name="q_keterangan"]:checked');
            if (!qKet) {
                valid = false;
                const card = document.getElementById('cardQ4');
                card.style.borderColor = 'var(--dl-rose)';
                setTimeout(() => card.style.borderColor = 'var(--dl-border)', 2000);
            }
            if (valid) {
                document.getElementById('stepChecklist').classList.add('d-none');
                document.getElementById('stepForm').classList.remove('d-none');
            }
        }

        function backToChecklist() {
            document.getElementById('stepForm').classList.add('d-none');
            document.getElementById('stepChecklist').classList.remove('d-none');
        }

        document.getElementById('modalUpdateEmail').addEventListener('hidden.bs.modal', function() {
            document.querySelectorAll('#stepChecklist input[type="radio"]').forEach(r => r.checked = false);
            ['warn_foto', 'warn_nik', 'warn_email_q'].forEach(id => document.getElementById(id).style.display =
                'none');
            document.getElementById('stepChecklist').classList.remove('d-none');
            document.getElementById('stepForm').classList.add('d-none');
        });

        // ── FILE DELETE — pakai hashed_id bukan raw id ──
        function deleteFile(hashedId, fileType) {
            if (!confirm('Apakah Anda yakin ingin menghapus file ini?')) return;
            const form = document.createElement('form');
            form.method = 'POST';
            // hashedId di-resolve oleh resolveRouteBinding di trait
            form.action = `{{ url('superadmin/data-lapangans') }}/${hashedId}/delete-file`;
            form.innerHTML = `
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="file_type" value="${fileType}">`;
            document.body.appendChild(form);
            form.submit();
        }

        // ── FILE VALIDATION ──
        ['file_oss', 'file_sihalal'].forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            el.addEventListener('change', function() {
                const f = this.files[0];
                if (!f) return;
                if (f.type !== 'application/pdf') {
                    alert('File harus berformat PDF!');
                    this.value = '';
                    return;
                }
                if (f.size > 5 * 1024 * 1024) {
                    alert('Ukuran file maksimal 5MB!');
                    this.value = '';
                    return;
                }
            });
        });

        // ── PHOTO VIEWER ──
        function viewFullImage(src, title) {
            document.getElementById('fullImageSrc').src = src;
            document.getElementById('fullImageTitle').textContent = title;
            new bootstrap.Modal(document.getElementById('modalFullImage')).show();
        }

        function downloadSingleImage() {
            const src = document.getElementById('fullImageSrc').src;
            const title = document.getElementById('fullImageTitle').textContent;
            fetch(src).then(r => r.blob()).then(blob => {
                const a = document.createElement('a');
                a.href = URL.createObjectURL(blob);
                a.download = title.replace(/\s+/g, '_') + '.jpg';
                a.click();
            }).catch(() => alert('Gagal mendownload gambar'));
        }

        // ── COLLAGE ──
        function downloadCollage() {
            const el = document.getElementById('collageContent');
            const loading = Object.assign(document.createElement('div'), {
                style: 'position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:rgba(0,0,0,.8);color:#fff;padding:18px 26px;border-radius:12px;z-index:9999;font-size:14px;',
                textContent: 'Memproses download...'
            });
            document.body.appendChild(loading);
            html2canvas(el, {
                scale: 2,
                useCORS: true,
                backgroundColor: '#fff'
            }).then(canvas => {
                canvas.toBlob(blob => {
                    const a = document.createElement('a');
                    a.href = URL.createObjectURL(blob);
                    a.download = 'Kolase_Foto_{{ $dataLapangan->nama_pu }}'.replace(/\s+/g, '_') + '.jpg';
                    a.click();
                    document.body.removeChild(loading);
                }, 'image/jpeg', .95);
            }).catch(() => {
                alert('Gagal membuat kolase');
                document.body.removeChild(loading);
            });
        }

        function printCollage() {
            const w = window.open('', '', 'height=700,width=900');
            w.document.write(`<html><head><title>Kolase Foto</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>.dl-collage-img{height:260px;object-fit:cover;width:100%}@media print{body{-webkit-print-color-adjust:exact}}</style>
            </head><body><h5 class="text-center my-4">Dokumentasi Foto — {{ $dataLapangan->nama_pu }}</h5>
            ${document.getElementById('collageContent').innerHTML}</body></html>`);
            w.document.close();
            setTimeout(() => {
                w.focus();
                w.print();
                w.close();
            }, 300);
        }

        // ── AUTO DISMISS ALERTS ──
        setTimeout(() => {
            document.querySelectorAll('.dl-alert').forEach(el => {
                el.style.transition = 'opacity .4s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 400);
            });
        }, 5000);

        // ── PASSWORD TOGGLE ──
        function togglePassword() {
            const input = document.getElementById('emailPasswordInput');
            const icon = document.getElementById('toggleIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'las la-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'las la-eye';
            }
        }

        // ── CEK EMAIL VIA AJAX ──
        function checkEmailExists() {
            const prefix = document.getElementById('emailPrefixInput').value.trim();
            const statusEl = document.getElementById('emailCheckStatus');

            if (!prefix) {
                statusEl.style.display = 'block';
                statusEl.style.background = '#FFF1F2';
                statusEl.style.border = '1px solid #FECDD3';
                statusEl.style.color = 'var(--dl-rose)';
                statusEl.innerHTML = '<i class="las la-times-circle"></i> Masukkan nama email terlebih dahulu.';
                return;
            }

            // Loading state
            statusEl.style.display = 'block';
            statusEl.style.background = '#F1F5F9';
            statusEl.style.border = '1px solid var(--dl-border)';
            statusEl.style.color = 'var(--dl-muted)';
            statusEl.innerHTML = '<i class="las la-spinner la-spin"></i> Mengecek ketersediaan email...';

            fetch(`{{ route($routePrefix . '.data-lapangans.check-email') }}?prefix=${encodeURIComponent(prefix)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.exists) {
                        statusEl.style.background = '#FFF1F2';
                        statusEl.style.border = '1px solid #FECDD3';
                        statusEl.style.color = 'var(--dl-rose)';
                        statusEl.innerHTML = '<i class="las la-times-circle"></i> Email <strong>' + prefix +
                            '@kawulohalal.id</strong> sudah digunakan.';
                    } else {
                        statusEl.style.background = 'var(--dl-green-lt)';
                        statusEl.style.border = '1px solid #A7F3D0';
                        statusEl.style.color = 'var(--dl-green)';
                        statusEl.innerHTML = '<i class="las la-check-circle"></i> Email <strong>' + prefix +
                            '@kawulohalal.id</strong> tersedia.';
                    }
                })
                .catch(() => {
                    statusEl.style.background = '#FFF1F2';
                    statusEl.style.border = '1px solid #FECDD3';
                    statusEl.style.color = 'var(--dl-rose)';
                    statusEl.innerHTML = '<i class="las la-exclamation-circle"></i> Gagal mengecek email. Coba lagi.';
                });
        }

        // Reset status cek saat user mengetik ulang prefix
        document.getElementById('emailPrefixInput')?.addEventListener('input', function() {
            const statusEl = document.getElementById('emailCheckStatus');
            statusEl.style.display = 'none';
        });
    </script>
@endsection
