@extends('layouts.app')

@section('template_title')
    {{ $dataLapangan->nama_pu ?? __('Show') . ' ' . __('Data Lapangan') }}
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
        $statusBadge = $statusBadgeMap[$dataLapangan->status] ?? 'dl-badge-ditolak';
        $hasPendingProgress = $latestProgress?->status === 'PENDING';

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

    <div class="dl-page" data-lock-id="{{ $dataLapangan->id }}">

        {{-- ── ALERTS ── --}}
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

        {{-- ── HEADER ── --}}
        <div class="dl-header">
            <div>
                <div class="dl-header-title">{{ $dataLapangan->nama_pu }}</div>
                <div class="dl-header-meta">
                    NIK {{ $dataLapangan->nik }}
                    &nbsp;·&nbsp;
                    Pendamping: <strong>{{ $dataLapangan->enumerator->nama_lengkap }}</strong>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                <span class="dl-badge {{ $statusBadge }}">
                    <span class="dl-badge-dot"></span>{{ $dataLapangan->status }}
                </span>

                @if (
                    ($dataLapangan->status == 'PROGRESS OSS' || $dataLapangan->status == 'DITOLAK') &&
                        !$hasPendingProgress &&
                        !$dataLapangan->email_sihalal)
                    <button type="button" class="dl-btn dl-btn-success" data-bs-toggle="modal"
                        data-bs-target="#modalUpdateStatusHalal">
                        <i class="las la-edit"></i> Update Status Halal
                    </button>
                @elseif ($hasPendingProgress || $dataLapangan->email_sihalal)
                    <span class="dl-badge dl-badge-pending">
                        <i class="las la-clock" style="font-size:12px;"></i>&nbsp;Menunggu Review Admin
                    </span>
                @endif

                <a href="{{ route('data-entry.data-lapangan.index') }}" class="dl-back">
                    <i class="las la-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        {{-- ── STEPPER ── --}}
        <div class="dl-stepper">
            <div class="dl-stepper-inner">
                @foreach ($steps as $i => $step)
                    @php $cls = $i < $currentIdx ? 'done' : ($i === $currentIdx ? 'active' : ''); @endphp
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

        {{-- ── MAIN GRID ── --}}
        <div class="dl-grid">

            {{-- ══════════ KOLOM KIRI — Info + File ══════════ --}}
            <div style="display:flex;flex-direction:column;gap:1.25rem;">

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
                                    ['Nama Produk', $dataLapangan->nama_produk ?? '—'],
                                    ['Alamat', $dataLapangan->alamat],
                                ];
                            @endphp
                            @foreach ($infoFields as $f)
                                <tr>
                                    <td class="dl-key" style="padding-left:1.25rem;">{{ $f[0] }}</td>
                                    <td class="dl-val" style="padding-right:1.25rem;">{{ $f[1] }}</td>
                                </tr>
                            @endforeach
                            {{-- Password --}}
                            <tr>
                                <td class="dl-key" style="padding-left:1.25rem;">Password</td>
                                <td class="dl-val" style="padding-right:1.25rem;">
                                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                        <code
                                            style="background:#F1F5F9;padding:3px 8px;border-radius:6px;font-size:13px;">Halal@123</code>
                                        <span class="dl-badge dl-badge-pending" style="font-size:10px;">Samakan semua</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="dl-key" style="padding-left:1.25rem;">Status</td>
                                <td class="dl-val" style="padding-right:1.25rem;">
                                    <span class="dl-badge {{ $statusBadge }}">{{ $dataLapangan->status }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Card: Dokumentasi File --}}
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
                        <div class="dl-divider">
                            <span class="dl-divider-label">File OSS / NIB</span>
                            <div class="dl-divider-line"></div>
                        </div>

                        @if ($dataLapangan->file_oss)
                            <div class="dl-file-row available" style="margin-bottom:10px;">
                                <div class="dl-file-icon">PDF</div>
                                <div class="dl-file-label">File NIB tersedia</div>
                                <a href="{{ asset('storage/' . $dataLapangan->file_oss) }}" target="_blank"
                                    class="dl-btn dl-btn-success dl-btn-sm">
                                    <i class="las la-external-link-alt"></i> Lihat File NIB
                                </a>
                            </div>
                        @else
                            <div class="dl-file-row missing" style="margin-bottom:10px;">
                                <div class="dl-file-icon">PDF</div>
                                <div class="dl-file-label">File OSS belum tersedia</div>
                            </div>
                        @endif

                        @if ($entryType === 'OSS' && !$dataLapangan->file_oss)
                            <form action="{{ route('data-entry.data-lapangan.upload-file', $dataLapangan->hashed_id) }}"
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
                        @endif
                    </div>
                </div>

            </div>

            {{-- ══════════ KOLOM KANAN — Produk + Foto ══════════ --}}
            <div style="display:flex;flex-direction:column;gap:1.25rem;">

                {{-- Card: Produk Terdaftar --}}
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
                                                onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                            <div class="dl-produk-card-img-placeholder" style="display:none;">
                                                <i class="las la-image"></i>
                                            </div>
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
                        @if ($entryType == 'SIHALAL')
                            <button type="button" class="dl-btn dl-btn-ghost dl-btn-sm" data-bs-toggle="modal"
                                data-bs-target="#modalKolaseFoto">
                                <i class="las la-th"></i> Kolase
                            </button>
                        @endif
                    </div>

                    @if ($entryType == 'OSS')
                        {{-- Foto Rumah --}}
                        <div class="dl-photo-row">
                            <div class="dl-photo-label">
                                <div class="dl-photo-thumb-placeholder"><i class="las la-home"></i></div>
                                Foto Rumah
                            </div>
                            <div class="dl-photo-actions">
                                <button type="button" class="dl-btn dl-btn-ghost dl-btn-icon-only"
                                    data-bs-toggle="modal" data-bs-target="#modalFotoRumah">
                                    <i class="las la-eye"></i>
                                </button>
                                <a href="{{ route('superadmin.datalapangan.download-foto-rumah-pdf', $dataLapangan->id) }}"
                                    class="dl-btn dl-btn-ghost dl-btn-sm">
                                    <i class="las la-download"></i> PDF
                                </a>
                            </div>
                        </div>
                        {{-- Foto KTP --}}
                        <div class="dl-photo-row">
                            <div class="dl-photo-label">
                                <div class="dl-photo-thumb-placeholder"><i class="las la-id-card"></i></div>
                                Foto KTP
                            </div>
                            <div class="dl-photo-actions">
                                <button type="button" class="dl-btn dl-btn-ghost dl-btn-icon-only"
                                    data-bs-toggle="modal" data-bs-target="#modalFotoKTP">
                                    <i class="las la-eye"></i>
                                </button>
                                <a href="{{ route('data-entry.datalapangan.download-foto-ktp', $dataLapangan->id) }}"
                                    class="dl-btn dl-btn-primary dl-btn-sm">
                                    <i class="las la-download"></i> Download KTP
                                </a>
                            </div>
                        </div>
                    @elseif ($entryType == 'SIHALAL')
                        {{-- Foto KTP --}}
                        <div class="dl-photo-row">
                            <div class="dl-photo-label">
                                @if (!empty($dataLapangan->foto_ktp))
                                    <img src="{{ asset('storage/' . $dataLapangan->foto_ktp) }}" class="dl-photo-thumb"
                                        alt="">
                                @else
                                    <div class="dl-photo-thumb-placeholder"><i class="las la-id-card"></i></div>
                                @endif
                                Foto KTP
                            </div>
                            <div class="dl-photo-actions">
                                <button type="button" class="dl-btn dl-btn-ghost dl-btn-icon-only"
                                    data-bs-toggle="modal" data-bs-target="#modalFotoKTP">
                                    <i class="las la-eye"></i>
                                </button>
                                <a href="{{ route('data-entry.datalapangan.download-foto-ktp', $dataLapangan->id) }}"
                                    class="dl-btn dl-btn-primary dl-btn-sm">
                                    <i class="las la-download"></i> Download KTP
                                </a>
                            </div>
                        </div>
                        {{-- Foto Produk — semua slot yang ada fotonya --}}
                        @foreach ($allProducts as $idx => $prod)
                            @if (!empty($prod['foto']))
                                <div class="dl-photo-row">
                                    <div class="dl-photo-label">
                                        <img src="{{ asset('storage/' . $prod['foto']) }}" class="dl-photo-thumb"
                                            alt="">
                                        Foto Produk {{ $idx }}
                                        <span style="font-size:11px;color:var(--dl-muted);font-weight:400;">—
                                            {{ $prod['nama'] }}</span>
                                    </div>
                                    <div class="dl-photo-actions">
                                        <button type="button" class="dl-btn dl-btn-ghost dl-btn-icon-only"
                                            onclick="viewFullImage('{{ asset('storage/' . $prod['foto']) }}', 'Foto Produk {{ $idx }}: {{ $prod['nama'] }}')">
                                            <i class="las la-eye"></i>
                                        </button>
                                        @if ($idx === 1)
                                            <a href="{{ route('data-entry.datalapangan.download-foto-produk', $dataLapangan->id) }}"
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
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════ --}}
    {{-- INCLUDE MODAL STATUS                  --}}
    {{-- ══════════════════════════════════════ --}}
    @include('data-entry.data-lapangan.partials.status-modal')


    {{-- ══════════════════════════════════════ --}}
    {{-- MODALS                                --}}
    {{-- ══════════════════════════════════════ --}}

    {{-- Modal Peraturan --}}
    <div class="modal fade dl-modal" id="modalPeraturan" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="las la-exclamation-triangle" style="color:var(--dl-amber);"></i>
                        Peraturan Sesi Pengerjaan
                    </h5>
                </div>
                <div class="modal-body">
                    <p style="font-size:13px;color:var(--dl-muted);margin-bottom:1rem;">
                        Harap baca dan pahami peraturan berikut sebelum memulai pengerjaan:
                    </p>
                    @php
                        $peraturan = [
                            [
                                'icon' => 'la-clock',
                                'color' => 'var(--dl-amber)',
                                'bg' => '#FFFBEB',
                                'border' => '#FDE68A',
                                'text' =>
                                    '<strong>Waktu pengerjaan adalah 50 menit.</strong> Timer berjalan otomatis dan tidak dapat dijeda.',
                            ],
                            [
                                'icon' => 'la-lock',
                                'color' => 'var(--dl-rose)',
                                'bg' => 'var(--dl-rose-lt)',
                                'border' => '#FECDD3',
                                'text' =>
                                    '<strong>Jangan keluar atau menutup halaman.</strong> Menutup tab atau refresh manual akan mengakhiri sesi secara otomatis.',
                            ],
                            [
                                'icon' => 'la-user-lock',
                                'color' => 'var(--dl-blue)',
                                'bg' => 'var(--dl-blue-lt)',
                                'border' => '#BFDBFE',
                                'text' =>
                                    '<strong>Data dikunci eksklusif untuk Anda.</strong> Pengguna lain tidak dapat mengakses data yang sedang dikerjakan.',
                            ],
                            [
                                'icon' => 'la-redo-alt',
                                'color' => 'var(--dl-green)',
                                'bg' => 'var(--dl-green-lt)',
                                'border' => '#A7F3D0',
                                'text' =>
                                    '<strong>Perpanjang sesi sebelum waktu habis.</strong> Gunakan tombol <em>"Perpanjang Sesi"</em> di pojok kanan bawah.',
                            ],
                            [
                                'icon' => 'la-exclamation-circle',
                                'color' => 'var(--dl-rose)',
                                'bg' => 'var(--dl-rose-lt)',
                                'border' => '#FECDD3',
                                'text' =>
                                    '<strong>Sesi yang habis tidak dapat dipulihkan.</strong> Data akan dilepas otomatis dan Anda diarahkan ke halaman daftar.',
                            ],
                        ];
                    @endphp
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        @foreach ($peraturan as $p)
                            <div
                                style="display:flex;align-items:flex-start;gap:12px;padding:12px 14px;background:{{ $p['bg'] }};border:1px solid {{ $p['border'] }};border-radius:10px;">
                                <span
                                    style="width:30px;height:30px;border-radius:50%;background:rgba(255,255,255,.7);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="las {{ $p['icon'] }}"
                                        style="font-size:15px;color:{{ $p['color'] }};"></i>
                                </span>
                                <div style="font-size:13px;line-height:1.5;">{!! $p['text'] !!}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer" style="justify-content:center;">
                    <button type="button" class="dl-btn dl-btn-amber" id="btnMengerti" style="padding:9px 28px;">
                        <i class="las la-check"></i> Saya Mengerti, Mulai Pengerjaan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Kolase Foto (SIHALAL only) --}}
    <div class="modal fade dl-modal" id="modalKolaseFoto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="las la-th" style="color:var(--dl-amber);"></i> Kolase Dokumentasi Foto
                    </h5>
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
                        {{-- Semua foto produk --}}
                        @foreach ($allProducts as $idx => $prod)
                            @if (!empty($prod['foto']))
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-light py-2 px-3 border-bottom"
                                            style="font-size:12px;font-weight:600;">Foto Produk {{ $idx }}:
                                            {{ $prod['nama'] }}</div>
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
                    <button type="button" class="dl-btn dl-btn-success" onclick="downloadCollage()">
                        <i class="las la-download"></i> Download Kolase
                    </button>
                    <button type="button" class="dl-btn dl-btn-primary" onclick="printCollage()">
                        <i class="las la-print"></i> Print
                    </button>
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
                    <button type="button" class="dl-btn dl-btn-success" onclick="downloadSingleImage()">
                        <i class="las la-download"></i> Download
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals Foto Individual --}}
    @foreach ([['id' => 'modalFotoKTP', 'title' => 'Foto KTP', 'src' => asset('storage/' . $dataLapangan->foto_ktp)], ['id' => 'modalFotoRumah', 'title' => 'Foto Rumah', 'src' => asset('storage/' . $dataLapangan->foto_rumah)], ['id' => 'modalFotoPendamping', 'title' => 'Foto Pendamping', 'src' => asset('storage/' . $dataLapangan->foto_pendamping)], ['id' => 'modalFotoProduk', 'title' => 'Foto Produk', 'src' => asset('storage/' . $dataLapangan->foto_produk)]] as $modal)
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


    {{-- ══════════════════════════════════════ --}}
    {{-- LOCK TIMER WIDGET                     --}}
    {{-- ══════════════════════════════════════ --}}
    <div id="lockTimerContainer" class="position-fixed bottom-0 end-0 m-3" style="z-index:9999;display:none;">
        <div class="dl-card"
            style="min-width:220px;border-radius:14px;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,.15);">
            <div
                style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#FFFBEB;border-bottom:1px solid #FDE68A;">
                <span
                    style="width:20px;height:20px;border-radius:50%;background:var(--dl-amber);display:flex;align-items:center;justify-content:center;">
                    <i class="las la-lock" style="font-size:11px;color:#fff;"></i>
                </span>
                <small style="font-size:12px;font-weight:700;color:var(--dl-amber);">Sesi Pengerjaan</small>
            </div>
            <div style="text-align:center;padding:14px 14px 4px;">
                <span id="lockTimerDisplay"
                    style="font-size:28px;font-weight:700;font-family:monospace;letter-spacing:2px;">50:00</span>
            </div>
            <div style="padding:4px 14px 10px;">
                <div style="height:4px;border-radius:4px;background:#F1F5F9;">
                    <div id="lockTimerProgress"
                        style="width:100%;height:100%;border-radius:4px;background:var(--dl-green);transition:width .8s linear,background .5s;">
                    </div>
                </div>
                <small style="display:block;text-align:center;margin-top:4px;font-size:11px;color:var(--dl-muted);">Data
                    sedang dikunci untuk Anda</small>
            </div>
            <div style="padding:0 14px 14px;">
                <button id="btnPerpanjang" class="dl-btn dl-btn-amber w-100" style="justify-content:center;">
                    <i class="las la-redo-alt"></i> Perpanjang Sesi
                </button>
            </div>
        </div>
    </div>


    {{-- ══════════════════════════════════════ --}}
    {{-- STYLES                                --}}
    {{-- ══════════════════════════════════════ --}}
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

        /* ── PAGE ── */
        .dl-page {
            padding: 1.5rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* ── HEADER ── */
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

        /* ── BADGES ── */
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

        /* ── STEPPER ── */
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

        /* ── GRID ── */
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

        /* ── CARD ── */
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

        /* ── INFO TABLE ── */
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

        /* ── DIVIDER ── */
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

        /* ── BUTTONS ── */
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
            text-decoration: none;
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
            color: #fff;
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
            color: var(--dl-green);
        }

        .dl-btn-ghost {
            background: #F1F5F9;
            color: var(--dl-slate);
            border-color: var(--dl-border);
        }

        .dl-btn-ghost:hover {
            background: #E2E8F0;
            color: var(--dl-text);
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

        .w-100 {
            width: 100%;
        }

        /* ── PHOTO ROW ── */
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

        /* ── FILE ROW ── */
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

        /* ── UPLOAD GROUP ── */
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

        /* ── ALERTS ── */
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

        /* ── BACK BUTTON ── */
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

        /* ── MODAL ── */
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

        /* ── COLLAGE ── */
        .dl-collage-img {
            height: 260px;
            object-fit: cover;
            width: 100%;
            cursor: pointer;
            border-radius: 0 0 10px 10px;
        }

        /* ── PRODUK GRID ── */
        .dl-produk-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
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

        /* ── EMPTY STATE ── */
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

        /* ── ANIMATIONS ── */
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
            animation-delay: .10s;
        }

        .dl-card:nth-child(3) {
            animation-delay: .15s;
        }
    </style>


    {{-- ══════════════════════════════════════ --}}
    {{-- SCRIPTS                               --}}
    {{-- ══════════════════════════════════════ --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        // ── AUTO DISMISS ALERTS ──
        setTimeout(() => {
            document.querySelectorAll('.dl-alert').forEach(el => {
                el.style.transition = 'opacity .4s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 400);
            });
        }, 5000);

        // ── IMAGE VIEWER ──
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

        // ── MODAL PERATURAN ──
        (function() {
            sessionStorage.removeItem('isReloading');
            const modalEl = document.getElementById('modalPeraturan');
            const modalPeraturan = new bootstrap.Modal(modalEl);
            modalPeraturan.show();
            document.getElementById('btnMengerti').addEventListener('click', function() {
                bootstrap.Modal.getInstance(modalEl).hide();
            });
        })();

        // ── LOCK TIMER — 50 menit ──
        (function() {
            const LOCK_URL = '/api/data-entry/data-lapangans';
            const LIST_URL = '{{ route('data-entry.data-lapangan.index') }}';
            const DURATION = 50 * 60;

            const sectionEl = document.querySelector('.dl-page[data-lock-id]');
            const PAGE_ID = sectionEl ? sectionEl.dataset.lockId : null;
            let LOCK_ID = sessionStorage.getItem('currentLockId');

            function getCsrfToken() {
                return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            }

            function showExpiredAlert() {
                document.getElementById('lockTimerContainer').style.display = 'none';
                const alertDiv = Object.assign(document.createElement('div'), {
                    className: 'dl-alert dl-alert-danger position-fixed top-0 start-0 end-0 m-3',
                    style: 'z-index:99999;',
                    innerHTML: `<i class="las la-exclamation-circle dl-alert-icon"></i>
                        <strong>Waktu Sesi Habis!</strong>&nbsp;Data telah dilepas. Anda akan diarahkan dalam
                        <strong id="redirectCountdown">5</strong> detik...`
                });
                document.body.prepend(alertDiv);
                let cd = 5;
                const iv = setInterval(() => {
                    cd--;
                    const el = document.getElementById('redirectCountdown');
                    if (el) el.textContent = cd;
                    if (cd <= 0) {
                        clearInterval(iv);
                        window.location.href = LIST_URL;
                    }
                }, 1000);
            }

            if (!LOCK_ID && PAGE_ID) {
                document.getElementById('lockTimerContainer').style.display = 'none';
                sessionStorage.setItem('isReloading', '1');
                sessionStorage.setItem('currentLockId', PAGE_ID);
                sessionStorage.setItem(`lockStart_${PAGE_ID}`, Date.now());
                fetch(`${LOCK_URL}/${PAGE_ID}/lock`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                }).then(r => r.json()).then(result => {
                    if (result.success) {
                        window.location.reload();
                    } else {
                        sessionStorage.removeItem('isReloading');
                        sessionStorage.removeItem('currentLockId');
                        sessionStorage.removeItem(`lockStart_${PAGE_ID}`);
                    }
                }).catch(err => {
                    console.error('Lock error:', err);
                    sessionStorage.removeItem('isReloading');
                    sessionStorage.removeItem('currentLockId');
                    sessionStorage.removeItem(`lockStart_${PAGE_ID}`);
                });
                return;
            }

            if (!LOCK_ID) {
                document.getElementById('lockTimerContainer').style.display = 'none';
                return;
            }

            const lockStartKey = `lockStart_${LOCK_ID}`;
            let lockStart = sessionStorage.getItem(lockStartKey);
            if (!lockStart) {
                lockStart = Date.now();
                sessionStorage.setItem(lockStartKey, lockStart);
            }

            const elapsed = Math.floor((Date.now() - parseInt(lockStart)) / 1000);
            let timeLeft = Math.max(DURATION - elapsed, 0);

            if (timeLeft <= 0) {
                document.getElementById('lockTimerContainer').style.display = 'none';
                sessionStorage.removeItem('currentLockId');
                sessionStorage.removeItem(lockStartKey);
                fetch(`${LOCK_URL}/${LOCK_ID}/lock`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                }).then(() => showExpiredAlert());
                return;
            }

            document.getElementById('lockTimerContainer').style.display = 'block';

            let timerInterval = null;
            let isExpired = false;

            async function releaseLock() {
                sessionStorage.removeItem('currentLockId');
                sessionStorage.removeItem(lockStartKey);
                await fetch(`${LOCK_URL}/${LOCK_ID}/lock`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
            }

            async function renewLock() {
                const res = await fetch(`${LOCK_URL}/${LOCK_ID}/lock`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                return await res.json();
            }

            function updateDisplay() {
                const m = Math.floor(timeLeft / 60).toString().padStart(2, '0');
                const s = (timeLeft % 60).toString().padStart(2, '0');
                const bar = document.getElementById('lockTimerProgress');
                const disp = document.getElementById('lockTimerDisplay');
                disp.textContent = `${m}:${s}`;
                bar.style.width = ((timeLeft / DURATION) * 100) + '%';
                if (timeLeft <= 60) {
                    bar.style.background = 'var(--dl-rose)';
                    disp.style.color = 'var(--dl-rose)';
                } else if (timeLeft <= 5 * 60) {
                    bar.style.background = 'var(--dl-amber)';
                    disp.style.color = 'var(--dl-amber)';
                } else {
                    bar.style.background = 'var(--dl-green)';
                    disp.style.color = 'var(--dl-text)';
                }
            }

            function startTimer() {
                timerInterval = setInterval(async function() {
                    timeLeft--;
                    updateDisplay();
                    if (timeLeft <= 0 && !isExpired) {
                        isExpired = true;
                        clearInterval(timerInterval);
                        await releaseLock();
                        showExpiredAlert();
                    }
                }, 1000);
            }

            document.getElementById('btnPerpanjang').addEventListener('click', async function() {
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memproses...';
                const result = await renewLock();
                if (result.success) {
                    sessionStorage.setItem(lockStartKey, Date.now());
                    timeLeft = DURATION;
                    isExpired = false;
                    updateDisplay();
                    this.innerHTML = '<i class="las la-redo-alt"></i> Perpanjang Sesi';
                    this.disabled = false;
                } else {
                    alert('Gagal memperpanjang sesi. Data telah dilepas.');
                    window.location.href = LIST_URL;
                }
            });

            window.addEventListener('beforeunload', function() {
                if (!isExpired) {
                    navigator.sendBeacon(`${LOCK_URL}/${LOCK_ID}/unlock-beacon`);
                    sessionStorage.removeItem('currentLockId');
                    sessionStorage.removeItem(lockStartKey);
                }
            });

            updateDisplay();
            startTimer();
        })();
    </script>
@endsection
