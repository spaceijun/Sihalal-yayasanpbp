@extends('layouts.app')

@section('template_title')
    {{ $dataLapangan->nama_pu ?? __('Show') . ' ' . __('Data Lapangan') }}
@endsection

@section('content')
    <section class="content container-fluid">
        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mt-3" role="alert">
                <i class="las la-check-circle fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mt-3" role="alert">
                <i class="las la-exclamation-circle fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ===== TOP BAR ===== --}}
        <div class="d-flex align-items-start justify-content-between pt-3 pb-3 border-bottom mb-4">
            <div>
                <h5 class="fw-semibold mb-1">{{ $dataLapangan->nama_pu }}</h5>
                <p class="text-muted mb-0 small">
                    NIK {{ $dataLapangan->nik }}
                    &nbsp;·&nbsp;
                    Pendamping: <strong>{{ $dataLapangan->enumerator->nama_lengkap }}</strong>
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                @php
                    $statusClass = match ($dataLapangan->status) {
                        'PENDING' => 'bg-warning text-dark',
                        'TERVERIFIKASI' => 'bg-secondary',
                        'PROGRESS OSS' => 'bg-info',
                        'PROGRESS SIHALAL' => 'bg-primary',
                        'TERBIT SH' => 'bg-success',
                        'DITOLAK' => 'bg-dark',
                        'REVISI' => 'bg-danger',
                        default => 'bg-secondary',
                    };
                @endphp
                <span class="badge {{ $statusClass }} px-3 py-2 fs-6">{{ $dataLapangan->status }}</span>
                <a href="{{ route('superadmin.data-lapangans.index') }}" class="btn btn-light btn-sm">
                    <i class="las la-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        {{-- ===== PROGRESS STEPPER ===== --}}
        @php
            $steps = ['PENDING', 'TERVERIFIKASI', 'PROGRESS OSS', 'PROGRESS SIHALAL', 'TERBIT SH'];
            $currentIdx = array_search($dataLapangan->status, $steps);
            if ($currentIdx === false) {
                $currentIdx = -1;
            }
        @endphp
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div class="d-flex align-items-center position-relative" style="padding: 0 24px;">
                    {{-- connector line --}}
                    <div class="position-absolute top-50 translate-middle-y"
                        style="left:44px;right:44px;height:2px;background:var(--vz-border-color);z-index:0;"></div>
                    @foreach ($steps as $i => $step)
                        @php
                            $isDone = $i < $currentIdx;
                            $isActive = $i === $currentIdx;
                            $dotClass = $isDone
                                ? 'bg-success text-white'
                                : ($isActive
                                    ? 'bg-primary text-white'
                                    : 'bg-light text-muted border');
                        @endphp
                        <div class="flex-fill d-flex flex-column align-items-center position-relative" style="z-index:1;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-semibold {{ $dotClass }}"
                                style="width:32px;height:32px;font-size:12px;border:2px solid transparent;
                                    {{ $isActive ? 'box-shadow:0 0 0 4px rgba(var(--vz-primary-rgb),.15)' : '' }}">
                                @if ($isDone)
                                    <i class="las la-check" style="font-size:14px;"></i>
                                @else
                                    {{ $i + 1 }}
                                @endif
                            </div>
                            <span class="mt-2 text-center"
                                style="font-size:11px;
                              color:{{ $isActive ? 'var(--vz-primary)' : ($isDone ? 'var(--vz-success)' : 'var(--vz-secondary-color)') }};
                              font-weight:{{ $isActive ? '600' : '400' }}">
                                {{ $step }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ===== MAIN GRID ===== --}}
        <div class="row g-4">

            {{-- ========== KOLOM KIRI ========== --}}
            <div class="col-lg-6">

                {{-- Card: Status & Aksi --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                        <span
                            class="avatar-xs rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                            style="width:28px;height:28px;">
                            <i class="las la-bolt" style="font-size:14px;"></i>
                        </span>
                        <h6 class="mb-0 fw-semibold">Status &amp; Aksi</h6>
                    </div>
                    <div class="card-body">

                        {{-- Tombol Aksi --}}
                        @if ($dataLapangan->status == 'PENDING')
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <button type="button" class="btn btn-success w-100 btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalUpdateEmail">
                                        <i class="las la-check-circle me-1"></i>Update Email &amp; Verifikasi
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-danger w-100 btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalRevisi">
                                        <i class="las la-redo me-1"></i>Update Revisi
                                    </button>
                                </div>
                            </div>
                        @endif

                        {{-- Verifikator --}}
                        @if ($dataLapangan->verifikator)
                            <div class="p-3 rounded-3 bg-light mb-3">
                                <p class="text-uppercase fw-semibold mb-2"
                                    style="font-size:11px;letter-spacing:.05em;color:var(--vz-secondary-color)">Verifikator
                                </p>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-semibold"
                                        style="width:36px;height:36px;font-size:13px;flex-shrink:0;">
                                        {{ strtoupper(substr($dataLapangan->verifikator->nama_lengkap ?? 'V', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold" style="font-size:14px;">
                                            {{ $dataLapangan->verifikator->nama_lengkap ?? '-' }}</div>
                                        <div class="text-muted" style="font-size:12px;">
                                            {{ $dataLapangan->tanggal_verifikasi
                                                ? \Carbon\Carbon::parse($dataLapangan->tanggal_verifikasi)->translatedFormat('d M Y')
                                                : 'Tanggal tidak tersedia' }}
                                        </div>
                                    </div>
                                    <span class="badge bg-success ms-auto">Terverifikasi</span>
                                </div>
                            </div>
                        @elseif ($dataLapangan->status == 'REVISI')
                            <div class="alert alert-warning d-flex gap-2 py-2 mb-3">
                                <i class="las la-exclamation-triangle mt-1" style="font-size:16px;flex-shrink:0;"></i>
                                <div>
                                    <div class="fw-semibold" style="font-size:12px;">Keterangan Revisi</div>
                                    <div style="font-size:13px;">{{ $dataLapangan->keterangan ?? 'Tidak ada keterangan.' }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <hr class="my-3">

                        {{-- Data Entry --}}
                        <p class="text-uppercase fw-semibold mb-2"
                            style="font-size:11px;letter-spacing:.05em;color:var(--vz-secondary-color)">Data Entry</p>

                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light mb-2">
                            <div>
                                <div class="fw-semibold" style="font-size:13px;">Entry OSS</div>
                                <div class="text-muted" style="font-size:12px;">
                                    {{ $dataEntryOSS?->dataEntry?->nama_lengkap ?? 'Tidak ada data' }}
                                    @if ($dataEntryOSS?->actioned_at)
                                        &nbsp;·&nbsp;{{ \Carbon\Carbon::parse($dataEntryOSS->actioned_at)->translatedFormat('d M Y') }}
                                    @endif
                                </div>
                            </div>
                            <span class="badge bg-info-subtle text-info">OSS</span>
                        </div>

                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light">
                            <div>
                                <div class="fw-semibold" style="font-size:13px;">Entry Sihalal</div>
                                <div class="text-muted" style="font-size:12px;">
                                    {{ $dataEntrySihalal?->dataEntry?->nama_lengkap ?? 'Tidak ada data' }}
                                    @if ($dataEntrySihalal?->actioned_at)
                                        &nbsp;·&nbsp;{{ \Carbon\Carbon::parse($dataEntrySihalal->actioned_at)->translatedFormat('d M Y') }}
                                    @endif
                                </div>
                            </div>
                            <span class="badge bg-primary-subtle text-primary">Sihalal</span>
                        </div>

                        <hr class="my-3">

                        {{-- Email Sihalal --}}
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <p class="text-uppercase fw-semibold mb-0"
                                style="font-size:11px;letter-spacing:.05em;color:var(--vz-secondary-color)">Email Sihalal
                            </p>
                            @if (!$dataLapangan->email_sihalal && $dataLapangan->status == 'PROGRESS SIHALAL')
                                <button type="button" class="btn btn-primary btn-sm py-1 px-2" style="font-size:11px;"
                                    data-bs-toggle="modal" data-bs-target="#modalEditEmailSihalal">
                                    <i class="las la-plus me-1"></i>Tambah
                                </button>
                            @endif
                        </div>
                        <p class="mb-0"
                            style="font-size:13px;color:{{ $dataLapangan->email_sihalal ? 'var(--vz-body-color)' : 'var(--vz-secondary-color)' }}">
                            {{ $dataLapangan->email_sihalal ?? 'Data tidak tersedia' }}
                        </p>

                    </div>
                </div>

                {{-- Card: Informasi Pelaku Usaha --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                        <span class="rounded d-flex align-items-center justify-content-center bg-purple-subtle text-purple"
                            style="width:28px;height:28px;background:rgba(var(--vz-purple-rgb),.15);">
                            <i class="las la-user" style="font-size:14px;color:var(--vz-purple);"></i>
                        </span>
                        <h6 class="mb-0 fw-semibold">Informasi Pelaku Usaha</h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-borderless mb-0" style="font-size:14px;">
                            <tbody>
                                @php
                                    $fields = [
                                        ['Nama Pendamping', $dataLapangan->enumerator->nama_lengkap],
                                        ['Nama Pelaku Usaha', $dataLapangan->nama_pu],
                                        ['NIK', $dataLapangan->nik],
                                        ['No Telepon', $dataLapangan->telephone ?? 'Tidak ada data'],
                                        ['Email', $dataLapangan->email ?? 'Tidak ada data'],
                                        ['Nama Produk', $dataLapangan->nama_produk ?? 'Tidak ada data'],
                                        ['Alamat', $dataLapangan->alamat],
                                    ];
                                @endphp
                                @foreach ($fields as $idx => $field)
                                    <tr class="{{ $idx < count($fields) - 1 ? 'border-bottom' : '' }}">
                                        <td class="fw-semibold text-muted py-3 px-4"
                                            style="width:40%;font-size:12px;vertical-align:top;padding-top:14px!important;">
                                            {{ $field[0] }}
                                        </td>
                                        <td class="py-3 pe-4" style="color:var(--vz-body-color)">{{ $field[1] }}</td>
                                    </tr>
                                @endforeach
                                <tr class="border-bottom">
                                    <td class="fw-semibold text-muted py-3 px-4" style="font-size:12px;">Status</td>
                                    <td class="py-3 pe-4">
                                        <span
                                            class="badge {{ $statusClass }} px-3 py-2">{{ $dataLapangan->status }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted py-3 px-4" style="font-size:12px;">Status Pembayaran
                                    </td>
                                    <td class="py-3 pe-4">
                                        @php
                                            $bayarClass = match ($dataLapangan->status_pembayaran) {
                                                'PENDING' => 'bg-warning text-dark',
                                                'PENGAJUAN' => 'bg-info',
                                                'DIBAYAR' => 'bg-success',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <span
                                            class="badge {{ $bayarClass }} px-3 py-2">{{ $dataLapangan->status_pembayaran }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            {{-- ========== KOLOM KANAN ========== --}}
            <div class="col-lg-6">

                {{-- Card: Dokumentasi Foto --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div
                        class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="rounded d-flex align-items-center justify-content-center"
                                style="width:28px;height:28px;background:rgba(var(--vz-warning-rgb),.15);">
                                <i class="las la-images" style="font-size:14px;color:var(--vz-warning);"></i>
                            </span>
                            <h6 class="mb-0 fw-semibold">Dokumentasi Foto</h6>
                        </div>
                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                            data-bs-target="#modalKolaseFoto">
                            <i class="las la-th me-1"></i>Lihat Kolase
                        </button>
                    </div>
                    <div class="card-body p-0">
                        @php
                            $photos = [
                                [
                                    'label' => 'Foto KTP',
                                    'view' => 'modalFotoKTP',
                                    'download_route' => route(
                                        'superadmin.datalapangan.download-foto-ktp',
                                        $dataLapangan->id,
                                    ),
                                    'download_label' => 'Download KTP',
                                    'dl_class' => 'btn-primary',
                                ],
                                [
                                    'label' => 'Foto Rumah',
                                    'view' => 'modalFotoRumah',
                                    'download_route' => route(
                                        'superadmin.datalapangan.download-foto-rumah-pdf',
                                        $dataLapangan->id,
                                    ),
                                    'download_label' => 'Download PDF',
                                    'dl_class' => 'btn-outline-secondary',
                                ],
                                [
                                    'label' => 'Foto Pendamping',
                                    'view' => 'modalFotoPendamping',
                                    'download_route' => route(
                                        'superadmin.datalapangan.download-foto-pendamping',
                                        $dataLapangan->id,
                                    ),
                                    'download_label' => 'Download',
                                    'dl_class' => 'btn-success',
                                ],
                                [
                                    'label' => 'Foto Produk',
                                    'view' => 'modalFotoProduk',
                                    'download_route' => route(
                                        'superadmin.datalapangan.download-foto-produk',
                                        $dataLapangan->id,
                                    ),
                                    'download_label' => 'Download',
                                    'dl_class' => 'btn-success',
                                ],
                            ];
                        @endphp

                        @foreach ($photos as $photo)
                            <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                                        style="width:32px;height:32px;flex-shrink:0;">
                                        <i class="las la-image text-muted" style="font-size:16px;"></i>
                                    </span>
                                    <span class="fw-semibold" style="font-size:14px;">{{ $photo['label'] }}</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#{{ $photo['view'] }}">
                                        <i class="las la-eye"></i>
                                    </button>
                                    <a href="{{ $photo['download_route'] }}"
                                        class="btn {{ $photo['dl_class'] }} btn-sm">
                                        <i class="las la-download me-1"></i>{{ $photo['download_label'] }}
                                    </a>
                                </div>
                            </div>
                        @endforeach

                        {{-- Foto Spotcheck --}}
                        <div class="px-4 py-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                                    style="width:32px;height:32px;flex-shrink:0;">
                                    <i class="las la-map-marker text-muted" style="font-size:16px;"></i>
                                </span>
                                <span class="fw-semibold" style="font-size:14px;">Foto Spotcheck</span>
                            </div>
                            @if ($dataLapangan->spotchecks && $dataLapangan->spotchecks->count() > 0)
                                @foreach ($dataLapangan->spotchecks as $index => $spotcheck)
                                    @if ($spotcheck->foto_pu)
                                        <div
                                            class="d-flex align-items-center justify-content-between py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                            <span class="text-muted" style="font-size:13px;">Spotcheck
                                                {{ $index + 1 }}</span>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalFotoSpotcheck{{ $spotcheck->id }}">
                                                    <i class="las la-eye"></i>
                                                </button>
                                                <a href="{{ asset('storage/' . $spotcheck->foto_pu) }}"
                                                    download="Spotcheck_{{ $spotcheck->nama_spotcheck ?? $index + 1 }}.jpg"
                                                    class="btn btn-success btn-sm">
                                                    <i class="las la-download me-1"></i>Download
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <div class="alert alert-info mb-0 py-2" style="font-size:13px;">
                                    <i class="las la-info-circle me-1"></i>Belum ada foto spotcheck
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Card: Form Keterangan Revisi --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                        <span class="rounded d-flex align-items-center justify-content-center"
                            style="width:28px;height:28px;background:rgba(var(--vz-danger-rgb),.12);">
                            <i class="las la-comment-alt" style="font-size:14px;color:var(--vz-danger);"></i>
                        </span>
                        <h6 class="mb-0 fw-semibold">Form Keterangan Revisi</h6>
                    </div>
                    <div class="card-body">
                        @if ($dataLapangan->keterangan)
                            <div class="alert alert-warning d-flex gap-2 mb-3 py-2">
                                <i class="las la-sticky-note mt-1" style="font-size:16px;flex-shrink:0;"></i>
                                <div>
                                    <div class="fw-semibold mb-1" style="font-size:12px;">Catatan Tersimpan &mdash;
                                        {{ $dataLapangan->updated_at ? $dataLapangan->updated_at->format('d M Y, H:i') : '-' }}
                                    </div>
                                    <div style="font-size:13px;">{{ $dataLapangan->keterangan }}</div>
                                </div>
                            </div>
                        @endif
                        <form action="{{ route('superadmin.data-lapangans.update-keterangan', $dataLapangan->id) }}"
                            method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="keterangan_form" class="form-label fw-semibold"
                                    style="font-size:13px;">Keterangan / Catatan</label>
                                <textarea name="keterangan" id="keterangan_form" class="form-control" rows="4"
                                    placeholder="Masukkan keterangan atau catatan tambahan...">{{ old('keterangan', $dataLapangan->keterangan ?? '') }}</textarea>
                                <div class="form-text"><i class="las la-info-circle me-1"></i>Tambahkan catatan penting
                                    terkait data lapangan ini</div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="las la-save me-1"></i>Simpan Keterangan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Card: Dokumentasi File --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                        <span class="rounded d-flex align-items-center justify-content-center"
                            style="width:28px;height:28px;background:rgba(var(--vz-success-rgb),.12);">
                            <i class="las la-file-alt" style="font-size:14px;color:var(--vz-success);"></i>
                        </span>
                        <h6 class="mb-0 fw-semibold">Dokumentasi File</h6>
                    </div>
                    <div class="card-body">

                        {{-- File OSS --}}
                        <p class="text-uppercase fw-semibold mb-2"
                            style="font-size:11px;letter-spacing:.05em;color:var(--vz-secondary-color)">File OSS</p>
                        @if ($dataLapangan->file_oss)
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-success-subtle mb-2">
                                <div class="rounded d-flex align-items-center justify-content-center bg-success text-white fw-bold"
                                    style="width:36px;height:36px;font-size:11px;flex-shrink:0;">PDF</div>
                                <div class="flex-grow-1" style="min-width:0">
                                    <div class="fw-semibold text-truncate" style="font-size:13px;">File OSS tersedia</div>
                                </div>
                                <a href="{{ asset('storage/' . $dataLapangan->file_oss) }}" target="_blank"
                                    class="btn btn-success btn-sm">
                                    <i class="las la-download me-1"></i>Unduh
                                </a>
                                <button type="button" class="btn btn-danger btn-sm"
                                    onclick="deleteFile('{{ $dataLapangan->id }}', 'oss')">
                                    <i class="las la-trash"></i>
                                </button>
                            </div>
                        @else
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-danger-subtle mb-2">
                                <div class="rounded d-flex align-items-center justify-content-center bg-danger text-white fw-bold"
                                    style="width:36px;height:36px;font-size:11px;flex-shrink:0;">PDF</div>
                                <div class="flex-grow-1" style="font-size:13px;color:var(--vz-danger);">File OSS belum
                                    tersedia</div>
                            </div>
                        @endif
                        <form action="{{ route('superadmin.data-lapangans.upload-file', $dataLapangan->hashed_id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="file_type" value="oss">
                            <div class="input-group input-group-sm">
                                <input type="file" class="form-control" name="file" id="file_oss" accept=".pdf"
                                    required>
                                <button class="btn btn-primary" type="submit"><i
                                        class="las la-upload me-1"></i>Upload</button>
                            </div>
                            <div class="form-text">Format PDF · Maks 5MB</div>
                        </form>

                        <hr class="my-4">

                        {{-- File Sihalal --}}
                        <p class="text-uppercase fw-semibold mb-2"
                            style="font-size:11px;letter-spacing:.05em;color:var(--vz-secondary-color)">File Sihalal</p>
                        @if ($dataLapangan->file_sihalal)
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-success-subtle mb-2">
                                <div class="rounded d-flex align-items-center justify-content-center bg-success text-white fw-bold"
                                    style="width:36px;height:36px;font-size:11px;flex-shrink:0;">PDF</div>
                                <div class="flex-grow-1" style="min-width:0">
                                    <div class="fw-semibold text-truncate" style="font-size:13px;">File Sihalal tersedia
                                    </div>
                                </div>
                                <a href="{{ asset('storage/' . $dataLapangan->file_sihalal) }}" target="_blank"
                                    class="btn btn-success btn-sm">
                                    <i class="las la-download me-1"></i>Unduh
                                </a>
                                <button type="button" class="btn btn-danger btn-sm"
                                    onclick="deleteFile('{{ $dataLapangan->id }}', 'sihalal')">
                                    <i class="las la-trash"></i>
                                </button>
                            </div>
                        @else
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-danger-subtle mb-2">
                                <div class="rounded d-flex align-items-center justify-content-center bg-danger text-white fw-bold"
                                    style="width:36px;height:36px;font-size:11px;flex-shrink:0;">PDF</div>
                                <div class="flex-grow-1" style="font-size:13px;color:var(--vz-danger);">File Sihalal belum
                                    tersedia</div>
                            </div>
                        @endif
                        <form action="{{ route('superadmin.data-lapangans.upload-file', $dataLapangan->hashed_id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="file_type" value="sihalal">
                            <div class="input-group input-group-sm">
                                <input type="file" class="form-control" name="file" id="file_sihalal"
                                    accept=".pdf" required>
                                <button class="btn btn-primary" type="submit"><i
                                        class="las la-upload me-1"></i>Upload</button>
                            </div>
                            <div class="form-text">Format PDF · Maks 5MB</div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </section>


    {{-- ========================================= --}}
    {{-- MODALS                                     --}}
    {{-- ========================================= --}}

    {{-- Modal Update Email & Verifikasi --}}
    <div class="modal fade" id="modalUpdateEmail" tabindex="-1" aria-labelledby="modalUpdateEmailLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="modalUpdateEmailLabel">
                        <i class="las la-envelope text-primary fs-5"></i>
                        Update Email &amp; Verifikasi Data
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">

                    {{-- STEP 1: Checklist --}}
                    <div id="stepChecklist">
                        <div class="alert alert-warning d-flex gap-2 mb-4">
                            <i class="las la-exclamation-triangle fs-5 mt-1" style="flex-shrink:0;"></i>
                            <div><strong>PERHATIAN</strong> — Jawab semua pertanyaan sebelum melanjutkan verifikasi.</div>
                        </div>

                        @php
                            $checklist = [
                                [
                                    'id' => 'foto',
                                    'name' => 'q_foto',
                                    'label' => 'Apakah Foto sudah dicek dan dalam kondisi benar serta sesuai?',
                                    'yes_val' => 'ya',
                                    'yes_txt' => 'Ya',
                                    'no_txt' => 'Tidak',
                                    'warn_id' => 'warn_foto',
                                    'warn_msg' => 'Foto harus dicek terlebih dahulu.',
                                    'required_val' => 'ya',
                                ],
                                [
                                    'id' => 'nik',
                                    'name' => 'q_nik',
                                    'label' =>
                                        'Apakah NIK sudah dicek melalui <a href="https://oss.go.id" target="_blank" class="fw-semibold">oss.go.id</a>?',
                                    'yes_val' => 'sudah',
                                    'yes_txt' => 'Sudah',
                                    'no_txt' => 'Belum',
                                    'warn_id' => 'warn_nik',
                                    'warn_msg' => 'NIK harus dicek melalui oss.go.id terlebih dahulu.',
                                    'required_val' => 'sudah',
                                ],
                                [
                                    'id' => 'email',
                                    'name' => 'q_email',
                                    'label' => 'Apakah Email sudah dibuat?',
                                    'yes_val' => 'ya',
                                    'yes_txt' => 'Ya',
                                    'no_txt' => 'Tidak',
                                    'warn_id' => 'warn_email_q',
                                    'warn_msg' => 'Email harus sudah dibuat.',
                                    'required_val' => 'ya',
                                ],
                            ];
                        @endphp

                        @foreach ($checklist as $i => $q)
                            <div class="card border mb-3 rounded-3" id="card_{{ $q['id'] }}">
                                <div class="card-body py-3">
                                    <p class="fw-semibold mb-3" style="font-size:14px;">
                                        <span class="badge bg-primary-subtle text-primary me-2">{{ $i + 1 }}</span>
                                        {!! $q['label'] !!}
                                    </p>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input check-answer" type="radio"
                                                name="{{ $q['name'] }}" id="{{ $q['id'] }}_ya"
                                                value="{{ $q['yes_val'] }}">
                                            <label class="form-check-label" for="{{ $q['id'] }}_ya">
                                                <span class="badge bg-success px-3 py-2">{{ $q['yes_txt'] }}</span>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input check-answer" type="radio"
                                                name="{{ $q['name'] }}" id="{{ $q['id'] }}_tidak"
                                                value="{{ str_replace('ya', 'tidak', str_replace('sudah', 'belum', $q['yes_val'])) }}">
                                            <label class="form-check-label" for="{{ $q['id'] }}_tidak">
                                                <span class="badge bg-danger px-3 py-2">{{ $q['no_txt'] }}</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mt-2 d-none alert alert-danger py-2 mb-0" id="{{ $q['warn_id'] }}">
                                        <i class="las la-times-circle me-1"></i>{{ $q['warn_msg'] }}
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Q4 --}}
                        <div class="card border mb-3 rounded-3" id="cardQ4">
                            <div class="card-body py-3">
                                <p class="fw-semibold mb-3" style="font-size:14px;">
                                    <span class="badge bg-primary-subtle text-primary me-2">4</span>
                                    Apakah ada komentar pada Form Keterangan Revisi?
                                </p>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input check-answer" type="radio" name="q_keterangan"
                                            id="q_ket_ya" value="ya">
                                        <label class="form-check-label" for="q_ket_ya">
                                            <span class="badge bg-warning text-dark px-3 py-2">Ya, sudah saya hapus</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input check-answer" type="radio" name="q_keterangan"
                                            id="q_ket_tidak" value="tidak">
                                        <label class="form-check-label" for="q_ket_tidak">
                                            <span class="badge bg-success px-3 py-2">Tidak</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-primary" onclick="validateChecklist()">
                                Lanjutkan <i class="las la-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 2: Form --}}
                    <div id="stepForm" class="d-none">
                        <div class="alert alert-success d-flex gap-2 mb-4">
                            <i class="las la-check-circle fs-5 mt-1" style="flex-shrink:0;"></i>
                            <div><strong>Semua pengecekan terpenuhi.</strong> Silakan isi data verifikasi di bawah ini.
                            </div>
                        </div>

                        <form action="{{ route('superadmin.data-lapangans.update-email', $dataLapangan->id) }}"
                            method="POST" id="formVerifikasi">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror" id="email"
                                    value="{{ old('email', $dataLapangan->email) }}" placeholder="Masukkan email"
                                    required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="verifikator_id" class="form-label fw-semibold">Verifikator</label>
                                <select name="verifikator_id"
                                    class="form-select @error('verifikator_id') is-invalid @enderror" id="verifikator_id"
                                    required>
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
                            <div class="mb-3">
                                <label for="tanggal_verifikasi" class="form-label fw-semibold">Tanggal Verifikasi</label>
                                <input type="date" name="tanggal_verifikasi"
                                    class="form-control @error('tanggal_verifikasi') is-invalid @enderror"
                                    id="tanggal_verifikasi"
                                    value="{{ old('tanggal_verifikasi', optional($dataLapangan->tanggal_verifikasi)->format('Y-m-d')) }}">
                                @error('tanggal_verifikasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-flex justify-content-between mt-3">
                                <button type="button" class="btn btn-light" onclick="backToChecklist()">
                                    <i class="las la-arrow-left me-1"></i>Kembali
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="las la-save me-1"></i>Simpan
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Modal Revisi --}}
    <div class="modal fade" id="modalRevisi" tabindex="-1" aria-labelledby="modalRevisiLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="modalRevisiLabel">
                        <i class="las la-redo text-danger fs-5"></i> Revisi Data
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('superadmin.data-lapangans.update-keterangan', $dataLapangan->id) }}"
                    method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="alert alert-warning d-flex gap-2 mb-3">
                            <i class="las la-exclamation-triangle fs-5 mt-1" style="flex-shrink:0;"></i>
                            <div><strong>PERHATIAN</strong> — Pastikan data sudah divalidasi dengan baik!</div>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan_modal" class="form-label fw-semibold">Keterangan Revisi</label>
                            <textarea name="keterangan" id="keterangan_modal" rows="4"
                                class="form-control @error('keterangan') is-invalid @enderror" placeholder="Masukkan keterangan...">{{ old('keterangan', $dataLapangan->keterangan) }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="las la-save me-1"></i>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit Email Sihalal --}}
    <div class="modal fade" id="modalEditEmailSihalal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        <i class="las la-envelope text-primary fs-5"></i> Edit Email Sihalal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('superadmin.data-lapangans.update-email-sihalal', $dataLapangan->id) }}"
                    method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="email_sihalal" class="form-label fw-semibold">Email Sihalal</label>
                            <input type="email" name="email_sihalal" id="email_sihalal"
                                class="form-control @error('email_sihalal') is-invalid @enderror"
                                value="{{ old('email_sihalal', $dataLapangan->email_sihalal) }}"
                                placeholder="Masukkan email sihalal" required>
                            @error('email_sihalal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="las la-save me-1"></i>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Kolase Foto --}}
    <div class="modal fade" id="modalKolaseFoto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title">Kolase Dokumentasi Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3" id="collageContent">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light py-2 px-3 border-bottom">
                                    <small class="fw-semibold">Foto Pendamping</small>
                                </div>
                                <img src="{{ asset('storage/' . $dataLapangan->foto_pendamping) }}" alt="Foto Pendamping"
                                    class="card-img-bottom collage-img rounded-bottom"
                                    style="height:280px;object-fit:cover;cursor:pointer;"
                                    onclick="viewFullImage('{{ asset('storage/' . $dataLapangan->foto_pendamping) }}', 'Foto Pendamping')">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light py-2 px-3 border-bottom">
                                    <small class="fw-semibold">Foto Produk</small>
                                </div>
                                <img src="{{ asset('storage/' . $dataLapangan->foto_produk) }}" alt="Foto Produk"
                                    class="card-img-bottom collage-img rounded-bottom"
                                    style="height:280px;object-fit:cover;cursor:pointer;"
                                    onclick="viewFullImage('{{ asset('storage/' . $dataLapangan->foto_produk) }}', 'Foto Produk')">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-success btn-sm" onclick="downloadCollage()">
                        <i class="las la-download me-1"></i>Download Kolase
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="printCollage()">
                        <i class="las la-print me-1"></i>Print Kolase
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Full Image --}}
    <div class="modal fade" id="modalFullImage" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title" id="fullImageTitle">Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-3">
                    <img id="fullImageSrc" src="" alt="Full Image" class="img-fluid rounded"
                        style="max-height:600px;">
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-success btn-sm" onclick="downloadSingleImage()">
                        <i class="las la-download me-1"></i>Download
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals Foto Individual --}}
    @foreach ([['id' => 'modalFotoKTP', 'title' => 'Foto KTP', 'src' => asset('storage/' . $dataLapangan->foto_ktp)], ['id' => 'modalFotoRumah', 'title' => 'Foto Rumah', 'src' => asset('storage/' . $dataLapangan->foto_rumah)], ['id' => 'modalFotoPendamping', 'title' => 'Foto Pendamping', 'src' => asset('storage/' . $dataLapangan->foto_pendamping)], ['id' => 'modalFotoProduk', 'title' => 'Foto Produk', 'src' => asset('storage/' . $dataLapangan->foto_produk)]] as $modal)
        <div class="modal fade" id="{{ $modal['id'] }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title">{{ $modal['title'] }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center p-3">
                        <img src="{{ $modal['src'] }}" alt="{{ $modal['title'] }}" class="img-fluid rounded"
                            style="max-height:500px;">
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Modals Foto Spotcheck --}}
    @if ($dataLapangan->spotchecks && $dataLapangan->spotchecks->count() > 0)
        @foreach ($dataLapangan->spotchecks as $spotcheck)
            @if ($spotcheck->foto_pu)
                <div class="modal fade" id="modalFotoSpotcheck{{ $spotcheck->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header border-bottom">
                                <h5 class="modal-title">Foto Spotcheck @if ($spotcheck->nama_spotcheck)
                                        — {{ $spotcheck->nama_spotcheck }}
                                    @endif
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center p-3">
                                <img src="{{ asset('storage/' . $spotcheck->foto_pu) }}" alt="Foto Spotcheck"
                                    class="img-fluid rounded" style="max-height:500px;">
                            </div>
                            <div class="modal-footer border-top">
                                <button type="button" class="btn btn-light btn-sm"
                                    data-bs-dismiss="modal">Tutup</button>
                                <a href="{{ asset('storage/' . $spotcheck->foto_pu) }}"
                                    download="Spotcheck_{{ $spotcheck->nama_spotcheck ?? $spotcheck->id }}.jpg"
                                    class="btn btn-success btn-sm">
                                    <i class="las la-download me-1"></i>Download
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endif


    {{-- ===== AUTO-OPEN MODALS ON VALIDATION ERROR ===== --}}
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


    {{-- ===== SCRIPTS ===== --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        // ===== CHECKLIST VALIDATION =====
        function validateChecklist() {
            const qFoto = document.querySelector('input[name="q_foto"]:checked');
            const qNik = document.querySelector('input[name="q_nik"]:checked');
            const qEmail = document.querySelector('input[name="q_email"]:checked');
            const qKeterangan = document.querySelector('input[name="q_keterangan"]:checked');
            let valid = true;

            document.getElementById('warn_foto').classList.add('d-none');
            document.getElementById('warn_nik').classList.add('d-none');
            document.getElementById('warn_email_q').classList.add('d-none');

            if (!qFoto || qFoto.value !== 'ya') {
                document.getElementById('warn_foto').classList.remove('d-none');
                valid = false;
            }
            if (!qNik || qNik.value !== 'sudah') {
                document.getElementById('warn_nik').classList.remove('d-none');
                valid = false;
            }
            if (!qEmail || qEmail.value !== 'ya') {
                document.getElementById('warn_email_q').classList.remove('d-none');
                valid = false;
            }

            if (!qKeterangan) {
                valid = false;
                const card = document.getElementById('cardQ4');
                card.classList.add('border-danger');
                setTimeout(() => card.classList.remove('border-danger'), 2000);
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
            document.getElementById('warn_foto').classList.add('d-none');
            document.getElementById('warn_nik').classList.add('d-none');
            document.getElementById('warn_email_q').classList.add('d-none');
            document.getElementById('stepChecklist').classList.remove('d-none');
            document.getElementById('stepForm').classList.add('d-none');
        });

        // ===== FILE DELETE =====
        function deleteFile(id, fileType) {
            if (confirm('Apakah Anda yakin ingin menghapus file ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('superadmin/data-lapangans') }}/${id}/delete-file`;
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                const ft = document.createElement('input');
                ft.type = 'hidden';
                ft.name = 'file_type';
                ft.value = fileType;
                form.appendChild(csrf);
                form.appendChild(ft);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // ===== FILE VALIDATION =====
        ['file_oss', 'file_sihalal'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.addEventListener('change', function() {
                validatePdfFile(this);
            });
        });

        function validatePdfFile(input) {
            const file = input.files[0];
            if (!file) return;
            if (file.type !== 'application/pdf') {
                alert('File harus berformat PDF!');
                input.value = '';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file maksimal 5MB!');
                input.value = '';
                return;
            }
        }

        // ===== PHOTO VIEWER =====
        function viewFullImage(src, title) {
            document.getElementById('fullImageSrc').src = src;
            document.getElementById('fullImageTitle').textContent = title;
            new bootstrap.Modal(document.getElementById('modalFullImage')).show();
        }

        function downloadSingleImage() {
            const src = document.getElementById('fullImageSrc').src;
            const title = document.getElementById('fullImageTitle').textContent;
            fetch(src).then(r => r.blob()).then(blob => {
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = title.replace(/\s+/g, '_') + '.jpg';
                document.body.appendChild(a);
                a.click();
                URL.revokeObjectURL(url);
                document.body.removeChild(a);
            }).catch(() => alert('Gagal mendownload gambar'));
        }

        // ===== COLLAGE =====
        function downloadCollage() {
            const el = document.getElementById('collageContent');
            const namaPU = '{{ $dataLapangan->nama_pu }}';
            const loadingDiv = document.createElement('div');
            loadingDiv.style.cssText =
                'position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:rgba(0,0,0,.8);color:#fff;padding:20px 28px;border-radius:12px;z-index:9999;font-size:14px;';
            loadingDiv.textContent = 'Memproses download...';
            document.body.appendChild(loadingDiv);
            html2canvas(el, {
                scale: 2,
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff'
            }).then(canvas => {
                canvas.toBlob(blob => {
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'Kolase_Foto_' + namaPU.replace(/\s+/g, '_') + '.jpg';
                    document.body.appendChild(a);
                    a.click();
                    URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                    document.body.removeChild(loadingDiv);
                }, 'image/jpeg', .95);
            }).catch(() => {
                alert('Gagal membuat kolase');
                document.body.removeChild(loadingDiv);
            });
        }

        function printCollage() {
            const content = document.getElementById('collageContent').innerHTML;
            const w = window.open('', '', 'height=600,width=800');
            w.document.write(`<html><head><title>Kolase Foto</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>.collage-img{height:280px!important;object-fit:cover}@media print{body{-webkit-print-color-adjust:exact}}</style>
            </head><body>
            <h4 class="text-center my-4">Dokumentasi Foto — {{ $dataLapangan->nama_pu }}</h4>
            ${content}</body></html>`);
            w.document.close();
            w.focus();
            setTimeout(() => {
                w.print();
                w.close();
            }, 250);
        }
    </script>
@endsection
