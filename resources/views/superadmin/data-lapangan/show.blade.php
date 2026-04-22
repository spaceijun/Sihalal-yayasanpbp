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
                    NIK <strong>{{ $dataLapangan->nik }}</strong>
                    &nbsp;·&nbsp;
                    Pendamping: <strong>{{ $dataLapangan->enumerator->nama_lengkap }}</strong>
                    &nbsp;·&nbsp;
                    <strong>{{ count($allProducts) }}</strong> produk terdaftar
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                <span class="dl-badge {{ $statusBadge }}">
                    <span class="dl-badge-dot"></span>{{ $dataLapangan->status }}
                </span>
                <a href="{{ route('superadmin.data-lapangans.index') }}" class="dl-back">
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
                        @if ($dataLapangan->status == 'PENDING')
                            <div class="dl-actions-group" style="margin-bottom:1rem;">
                                <button type="button" class="dl-btn dl-btn-success" data-bs-toggle="modal"
                                    data-bs-target="#modalUpdateEmail">
                                    <i class="las la-check-circle"></i> Update Email &amp; Verifikasi
                                </button>
                                <button type="button" class="dl-btn dl-btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#modalRevisi">
                                    <i class="las la-redo"></i> Update Revisi
                                </button>
                            </div>
                        @endif

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
                        <form action="{{ route('superadmin.data-lapangans.update-keterangan', $dataLapangan->id) }}"
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

                    {{-- Foto utama --}}
                    @php
                        $staticPhotos = [
                            [
                                'label' => 'Foto KTP',
                                'modal' => 'modalFotoKTP',
                                'foto' => $dataLapangan->foto_ktp,
                                'dl_route' => route('superadmin.datalapangan.download-foto-ktp', $dataLapangan->id),
                                'dl_label' => 'KTP',
                                'dl_class' => 'dl-btn-primary',
                            ],
                            [
                                'label' => 'Foto Rumah',
                                'modal' => 'modalFotoRumah',
                                'foto' => $dataLapangan->foto_rumah,
                                'dl_route' => route(
                                    'superadmin.datalapangan.download-foto-rumah-pdf',
                                    $dataLapangan->id,
                                ),
                                'dl_label' => 'PDF',
                                'dl_class' => 'dl-btn-ghost',
                            ],
                            [
                                'label' => 'Foto Pendamping',
                                'modal' => 'modalFotoPendamping',
                                'foto' => $dataLapangan->foto_pendamping,
                                'dl_route' => route(
                                    'superadmin.datalapangan.download-foto-pendamping',
                                    $dataLapangan->id,
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
                                        <a href="{{ route('superadmin.datalapangan.download-foto-produk', $dataLapangan->id) }}"
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
                                <button type="button" class="dl-btn dl-btn-danger dl-btn-sm dl-btn-icon-only"
                                    onclick="deleteFile('{{ $dataLapangan->id }}', 'oss')">
                                    <i class="las la-trash"></i>
                                </button>
                            @endif
                        </div>
                        <form action="{{ route('superadmin.data-lapangans.upload-file', $dataLapangan->hashed_id) }}"
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
                                <button type="button" class="dl-btn dl-btn-danger dl-btn-sm dl-btn-icon-only"
                                    onclick="deleteFile('{{ $dataLapangan->id }}', 'sihalal')">
                                    <i class="las la-trash"></i>
                                </button>
                            @endif
                        </div>
                        <form action="{{ route('superadmin.data-lapangans.upload-file', $dataLapangan->hashed_id) }}"
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
                    <div id="stepForm" class="d-none">
                        <div
                            style="display:flex;gap:10px;padding:12px 14px;background:var(--dl-green-lt);border:1px solid #A7F3D0;border-radius:10px;margin-bottom:1.25rem;">
                            <i class="las la-check-circle"
                                style="color:var(--dl-green);font-size:16px;flex-shrink:0;margin-top:2px;"></i>
                            <div style="font-size:13px;color:#065F46;"><strong>Semua pengecekan terpenuhi.</strong> Isi
                                data verifikasi di bawah ini.</div>
                        </div>

                        <form action="{{ route('superadmin.data-lapangans.update-email', $dataLapangan->id) }}"
                            method="POST" id="formVerifikasi">
                            @csrf
                            <div style="margin-bottom:.85rem;">
                                <label class="form-label">Email</label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $dataLapangan->email) }}" placeholder="Masukkan email"
                                    required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                <button type="submit" class="dl-btn dl-btn-primary">
                                    <i class="las la-save"></i> Simpan
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
                <form action="{{ route('superadmin.data-lapangans.update-keterangan', $dataLapangan->id) }}"
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
                <form action="{{ route('superadmin.data-lapangans.update-email-sihalal', $dataLapangan->id) }}"
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

        // ── FILE DELETE ──
        function deleteFile(id, fileType) {
            if (!confirm('Apakah Anda yakin ingin menghapus file ini?')) return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('superadmin/data-lapangans') }}/${id}/delete-file`;
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
    </script>
@endsection
