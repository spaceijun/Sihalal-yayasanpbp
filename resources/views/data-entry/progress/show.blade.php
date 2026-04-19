@extends('layouts.app')

@section('template_title')
    {{ $dataLapangan->nama_pu ?? __('Show') . ' ' . __('Data Lapangan') }}
@endsection

@section('content')
    <section class="content container-fluid">

        {{-- ===== ALERT MESSAGES ===== --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mt-3" role="alert">
                <i class="las la-check-circle fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2 mt-3" role="alert">
                <i class="las la-exclamation-circle fs-5"></i>
                <div>{{ session('warning') }}</div>
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

        {{-- ===== BANNER STATUS PROGRESS ===== --}}
        @if ($latestProgress)
            @php
                $progressConfig = match ($latestProgress->status) {
                    'PENDING' => [
                        'color' => 'warning',
                        'icon' => 'la-clock',
                        'title' => 'Menunggu Review Admin',
                        'msg' => 'Progress Anda sedang menunggu untuk direview oleh admin.',
                        'note' => null,
                    ],
                    'DITERIMA' => [
                        'color' => 'success',
                        'icon' => 'la-check-circle',
                        'title' => 'Data Diterima!',
                        'msg' => 'Data Anda telah diverifikasi dan diterima oleh admin.',
                        'note' => null,
                    ],
                    'REVISI' => [
                        'color' => 'danger',
                        'icon' => 'la-exclamation-triangle',
                        'title' => 'Perlu Revisi!',
                        'msg' => 'Data Anda perlu direvisi. Silakan perbaiki dan kirim ulang.',
                        'note' => $latestProgress->keterangan_revisi,
                    ],
                    'DITOLAK' => [
                        'color' => 'dark',
                        'icon' => 'la-times-circle',
                        'title' => 'Data Ditolak',
                        'msg' => 'Data Anda telah ditolak oleh admin.',
                        'note' => $latestProgress->keterangan_revisi,
                    ],
                    default => null,
                };
            @endphp
            @if ($progressConfig)
                <div class="card border-0 shadow-sm mt-3 mb-0 overflow-hidden">
                    <div class="d-flex align-items-stretch">
                        {{-- Color accent strip --}}
                        <div class="flex-shrink-0" style="width:5px;background:var(--vz-{{ $progressConfig['color'] }});">
                        </div>
                        <div class="d-flex align-items-start gap-3 p-3 flex-grow-1"
                            style="background:rgba(var(--vz-{{ $progressConfig['color'] }}-rgb),.06);">
                            <span class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1"
                                style="width:36px;height:36px;background:rgba(var(--vz-{{ $progressConfig['color'] }}-rgb),.15);">
                                <i class="las {{ $progressConfig['icon'] }}"
                                    style="font-size:18px;color:var(--vz-{{ $progressConfig['color'] }});"></i>
                            </span>
                            <div class="flex-grow-1">
                                <div class="fw-semibold mb-1"
                                    style="font-size:14px;color:var(--vz-{{ $progressConfig['color'] }});">
                                    {{ $progressConfig['title'] }}
                                </div>
                                <div class="text-muted" style="font-size:13px;">{{ $progressConfig['msg'] }}</div>
                                @if ($progressConfig['note'])
                                    <div class="mt-2 p-2 rounded-2"
                                        style="background:rgba(var(--vz-{{ $progressConfig['color'] }}-rgb),.08);font-size:13px;">
                                        <span class="fw-semibold">Catatan:</span> <em>{{ $progressConfig['note'] }}</em>
                                    </div>
                                @endif
                            </div>
                            {{-- Tombol Resubmit di banner jika REVISI --}}
                            @if ($latestProgress->status === 'REVISI')
                                <div class="flex-shrink-0 ms-2">
                                    @if ($entryType === 'OSS')
                                        <button type="button" class="btn btn-warning btn-sm fw-semibold"
                                            data-bs-toggle="modal" data-bs-target="#modalRevisiOSS">
                                            <i class="las la-paper-plane me-1"></i>Kirim Ulang
                                        </button>
                                    @elseif ($entryType === 'SIHALAL')
                                        <button type="button" class="btn btn-warning btn-sm fw-semibold"
                                            data-bs-toggle="modal" data-bs-target="#modalRevisiSIHALAL">
                                            <i class="las la-paper-plane me-1"></i>Kirim Ulang
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
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
            <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                @php
                    $statusClass = match ($dataLapangan->status) {
                        'PENDING' => 'bg-warning text-dark',
                        'TERVERIFIKASI' => 'bg-info',
                        'PROGRESS OSS' => 'bg-info',
                        'PROGRESS SIHALAL' => 'bg-primary',
                        'TERBIT SH' => 'bg-success',
                        'DITOLAK' => 'bg-dark',
                        'REVISI' => 'bg-danger',
                        default => 'bg-secondary',
                    };
                @endphp
                <span class="badge {{ $statusClass }} px-3 py-2 fs-6">{{ $dataLapangan->status }}</span>
                <a href="{{ route('data-entry.data-lapangan.index') }}" class="btn btn-light btn-sm">
                    <i class="las la-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>

        {{-- ===== MAIN GRID ===== --}}
        <div class="row g-4">

            {{-- ========== KOLOM KIRI — Informasi ========== --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                        <span class="rounded d-flex align-items-center justify-content-center"
                            style="width:28px;height:28px;background:rgba(var(--vz-primary-rgb),.12);">
                            <i class="las la-user" style="font-size:14px;color:var(--vz-primary);"></i>
                        </span>
                        <h6 class="mb-0 fw-semibold">Informasi Pelaku Usaha</h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-borderless mb-0" style="font-size:14px;">
                            <tbody>
                                @php
                                    $fields = [
                                        ['Nama Pendamping', $dataLapangan->enumerator->nama_lengkap, null],
                                        ['Nama Pelaku Usaha', $dataLapangan->nama_pu, null],
                                        ['NIK', $dataLapangan->nik, 'mono'],
                                        ['Telepon', $dataLapangan->telephone ?? 'Tidak ada data', null],
                                        ['Email', $dataLapangan->email ?? 'Email belum tersedia', null],
                                        [
                                            'Email Sihalal',
                                            $dataLapangan->email_sihalal ?? 'Email Sihalal belum tersedia',
                                            null,
                                        ],
                                        ['Nama Produk', $dataLapangan->nama_produk ?? 'Tidak ada data', null],
                                        ['Alamat', $dataLapangan->alamat, null],
                                    ];
                                @endphp
                                @foreach ($fields as $idx => $field)
                                    <tr class="{{ $idx < count($fields) - 1 ? 'border-bottom' : '' }}">
                                        <td class="fw-semibold text-muted py-3 px-4"
                                            style="width:38%;font-size:12px;vertical-align:top;padding-top:14px!important;">
                                            {{ $field[0] }}
                                        </td>
                                        <td class="py-3 pe-4"
                                            style="{{ $field[2] === 'mono' ? 'font-family:var(--vz-font-monospace);' : '' }}color:var(--vz-body-color)">
                                            {{ $field[1] }}
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- Password --}}
                                <tr class="border-bottom">
                                    <td class="fw-semibold text-muted py-3 px-4"
                                        style="font-size:12px;vertical-align:top;padding-top:14px!important;">Password</td>
                                    <td class="py-3 pe-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <code class="bg-light px-2 py-1 rounded"
                                                style="font-size:13px;">Halal@123</code>
                                            <span class="badge bg-warning text-dark" style="font-size:10px;">Samakan
                                                semua</span>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Status --}}
                                <tr>
                                    <td class="fw-semibold text-muted py-3 px-4" style="font-size:12px;">Status</td>
                                    <td class="py-3 pe-4">
                                        <span
                                            class="badge {{ $statusClass }} px-3 py-2">{{ $dataLapangan->status }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ========== KOLOM KANAN — Foto & File ========== --}}
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
                        @if ($entryType == 'SIHALAL')
                            <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                                data-bs-target="#modalKolaseFoto">
                                <i class="las la-th me-1"></i>Lihat Kolase
                            </button>
                        @endif
                    </div>
                    <div class="card-body p-0">

                        @if ($entryType == 'OSS')
                            {{-- Foto Rumah --}}
                            <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                                        style="width:32px;height:32px;flex-shrink:0;">
                                        <i class="las la-home text-muted" style="font-size:16px;"></i>
                                    </span>
                                    <span class="fw-semibold" style="font-size:14px;">Foto Rumah</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalFotoRumah">
                                        <i class="las la-eye"></i>
                                    </button>
                                    <a href="{{ route('superadmin.datalapangan.download-foto-rumah-pdf', $dataLapangan->id) }}"
                                        class="btn btn-outline-secondary btn-sm">
                                        <i class="las la-download me-1"></i>PDF
                                    </a>
                                </div>
                            </div>
                            {{-- Foto KTP --}}
                            <div class="d-flex align-items-center justify-content-between px-4 py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                                        style="width:32px;height:32px;flex-shrink:0;">
                                        <i class="las la-id-card text-muted" style="font-size:16px;"></i>
                                    </span>
                                    <span class="fw-semibold" style="font-size:14px;">Foto KTP</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalFotoKTP">
                                        <i class="las la-eye"></i>
                                    </button>
                                    <a href="{{ route('koordinator.datalapangan.download-foto-ktp', $dataLapangan->id) }}"
                                        class="btn btn-primary btn-sm">
                                        <i class="las la-download me-1"></i>Download KTP
                                    </a>
                                </div>
                            </div>
                        @elseif ($entryType == 'SIHALAL')
                            {{-- Foto KTP --}}
                            <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                                        style="width:32px;height:32px;flex-shrink:0;">
                                        <i class="las la-id-card text-muted" style="font-size:16px;"></i>
                                    </span>
                                    <span class="fw-semibold" style="font-size:14px;">Foto KTP</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalFotoKTP">
                                        <i class="las la-eye"></i>
                                    </button>
                                    <a href="{{ route('koordinator.datalapangan.download-foto-ktp', $dataLapangan->id) }}"
                                        class="btn btn-primary btn-sm">
                                        <i class="las la-download me-1"></i>Download KTP
                                    </a>
                                </div>
                            </div>
                            {{-- Foto Pendamping --}}
                            <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                                        style="width:32px;height:32px;flex-shrink:0;">
                                        <i class="las la-user text-muted" style="font-size:16px;"></i>
                                    </span>
                                    <span class="fw-semibold" style="font-size:14px;">Foto Pendamping</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalFotoPendamping">
                                        <i class="las la-eye"></i>
                                    </button>
                                    <a href="{{ route('koordinator.datalapangan.download-foto-produk', $dataLapangan->id) }}"
                                        class="btn btn-success btn-sm">
                                        <i class="las la-download me-1"></i>Download
                                    </a>
                                </div>
                            </div>
                            {{-- Foto Produk --}}
                            <div class="d-flex align-items-center justify-content-between px-4 py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                                        style="width:32px;height:32px;flex-shrink:0;">
                                        <i class="las la-box text-muted" style="font-size:16px;"></i>
                                    </span>
                                    <span class="fw-semibold" style="font-size:14px;">Foto Produk</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalFotoProduk">
                                        <i class="las la-eye"></i>
                                    </button>
                                    <a href="{{ route('koordinator.datalapangan.download-foto-produk', $dataLapangan->id) }}"
                                        class="btn btn-success btn-sm">
                                        <i class="las la-download me-1"></i>Download
                                    </a>
                                </div>
                            </div>
                        @endif

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
                        <p class="text-uppercase fw-semibold mb-2"
                            style="font-size:11px;letter-spacing:.05em;color:var(--vz-secondary-color)">File OSS</p>

                        @if ($dataLapangan->file_oss)
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-success-subtle mb-3">
                                <div class="rounded d-flex align-items-center justify-content-center bg-success text-white fw-bold"
                                    style="width:36px;height:36px;font-size:11px;flex-shrink:0;">PDF</div>
                                <div class="flex-grow-1" style="min-width:0">
                                    <div class="fw-semibold" style="font-size:13px;">File OSS tersedia</div>
                                    <div class="text-muted" style="font-size:12px;">Klik tombol di kanan untuk membuka
                                    </div>
                                </div>
                                <a href="{{ asset('storage/' . $dataLapangan->file_oss) }}" target="_blank"
                                    class="btn btn-success btn-sm">
                                    <i class="las la-external-link-alt me-1"></i>Lihat
                                </a>
                            </div>
                        @else
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-danger-subtle mb-3">
                                <div class="rounded d-flex align-items-center justify-content-center bg-danger text-white fw-bold"
                                    style="width:36px;height:36px;font-size:11px;flex-shrink:0;">PDF</div>
                                <div class="flex-grow-1" style="font-size:13px;color:var(--vz-danger);">
                                    File OSS belum tersedia
                                </div>
                            </div>
                        @endif

                        @if ($entryType === 'OSS')
                            <form action="{{ route('data-entry.data-lapangan.upload-file', $dataLapangan->hashed_id) }}"
                                method="POST" enctype="multipart/form-data" id="uploadOssForm">
                                @csrf
                                <input type="hidden" name="file_type" value="oss">
                                <div class="input-group input-group-sm">
                                    <input type="file" class="form-control" name="file" id="file_oss"
                                        accept=".pdf" required>
                                    <button class="btn btn-primary" type="submit">
                                        <i class="las la-upload me-1"></i>Upload
                                    </button>
                                </div>
                                <div class="form-text">Format PDF · Maks 5MB</div>
                            </form>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ===== INCLUDE MODAL STATUS ===== --}}
    @include('data-entry.data-lapangan.partials.status-modal')


    {{-- ========================================= --}}
    {{-- MODAL REVISI OSS                          --}}
    {{-- ========================================= --}}
    @if ($latestProgress && $latestProgress->status === 'REVISI' && $entryType === 'OSS')
        <div class="modal fade" id="modalRevisiOSS" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-bottom" style="background:rgba(var(--vz-warning-rgb),.1);">
                        <h5 class="modal-title d-flex align-items-center gap-2">
                            <i class="las la-edit" style="color:var(--vz-warning);font-size:18px;"></i>
                            Resubmit Revisi — OSS
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('data-entry.data-lapangan.resubmit', $dataLapangan->hashed_id) }}"
                        method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body p-4">
                            @if ($latestProgress->keterangan_revisi)
                                <div class="d-flex gap-2 p-3 rounded-3 mb-4"
                                    style="background:rgba(var(--vz-warning-rgb),.08);border-left:3px solid var(--vz-warning);">
                                    <i class="las la-sticky-note mt-1 flex-shrink-0"
                                        style="color:var(--vz-warning);font-size:16px;"></i>
                                    <div>
                                        <div class="fw-semibold mb-1" style="font-size:12px;color:var(--vz-warning);">
                                            Catatan Superadmin
                                        </div>
                                        <div style="font-size:13px;">{{ $latestProgress->keterangan_revisi }}</div>
                                    </div>
                                </div>
                            @endif
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Keterangan Perbaikan <span class="text-danger">*</span>
                                </label>
                                <textarea name="keterangan_update" class="form-control" rows="4"
                                    placeholder="Jelaskan perbaikan yang telah Anda lakukan..." required></textarea>
                                <div class="form-text">Jelaskan perubahan yang sudah dilakukan sesuai catatan revisi.</div>
                            </div>
                        </div>
                        <div class="modal-footer border-top">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning fw-semibold">
                                <i class="las la-paper-plane me-1"></i>Kirim Ulang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- ========================================= --}}
    {{-- MODAL REVISI SIHALAL                      --}}
    {{-- ========================================= --}}
    @if ($latestProgress && $latestProgress->status === 'REVISI' && $entryType === 'SIHALAL')
        <div class="modal fade" id="modalRevisiSIHALAL" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-bottom" style="background:rgba(var(--vz-warning-rgb),.1);">
                        <h5 class="modal-title d-flex align-items-center gap-2">
                            <i class="las la-edit" style="color:var(--vz-warning);font-size:18px;"></i>
                            Resubmit Revisi — Sihalal
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('data-entry.data-lapangan.resubmit', $dataLapangan->hashed_id) }}"
                        method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body p-4">
                            @if ($latestProgress->keterangan_revisi)
                                <div class="d-flex gap-2 p-3 rounded-3 mb-4"
                                    style="background:rgba(var(--vz-warning-rgb),.08);border-left:3px solid var(--vz-warning);">
                                    <i class="las la-sticky-note mt-1 flex-shrink-0"
                                        style="color:var(--vz-warning);font-size:16px;"></i>
                                    <div>
                                        <div class="fw-semibold mb-1" style="font-size:12px;color:var(--vz-warning);">
                                            Catatan Superadmin
                                        </div>
                                        <div style="font-size:13px;">{{ $latestProgress->keterangan_revisi }}</div>
                                    </div>
                                </div>
                            @endif
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Keterangan Perbaikan <span class="text-danger">*</span>
                                </label>
                                <textarea name="keterangan_update" class="form-control" rows="4"
                                    placeholder="Jelaskan perbaikan yang telah Anda lakukan..." required></textarea>
                                <div class="form-text">Jelaskan perubahan yang sudah dilakukan sesuai catatan revisi.</div>
                            </div>
                        </div>
                        <div class="modal-footer border-top">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning fw-semibold">
                                <i class="las la-paper-plane me-1"></i>Kirim Ulang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif


    {{-- ========================================= --}}
    {{-- MODAL KOLASE FOTO                         --}}
    {{-- ========================================= --}}
    <div class="modal fade" id="modalKolaseFoto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
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
                                    style="height:250px;object-fit:cover;cursor:pointer;"
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
                                    style="height:250px;object-fit:cover;cursor:pointer;"
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
                        <i class="las la-download me-1"></i>Download Foto
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


    {{-- ========================================= --}}
    {{-- SCRIPTS                                   --}}
    {{-- ========================================= --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        // Auto-dismiss standard alerts setelah 7 detik
        setTimeout(function() {
            document.querySelectorAll('section.content .alert.alert-dismissible').forEach(function(el) {
                bootstrap.Alert.getOrCreateInstance(el)?.close();
            });
        }, 7000);

        // ===== IMAGE VIEWER =====
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
            const overlay = document.createElement('div');
            overlay.style.cssText =
                'position:fixed;inset:0;background:rgba(0,0,0,.5);display:flex;align-items:center;justify-content:center;z-index:99999;';
            overlay.innerHTML =
                '<div class="card p-3 shadow" style="font-size:14px;"><i class="las la-spinner fa-spin me-2"></i>Memproses download...</div>';
            document.body.appendChild(overlay);
            html2canvas(el, {
                scale: 2,
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#fff'
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
                    document.body.removeChild(overlay);
                }, 'image/jpeg', 0.95);
            }).catch(() => {
                alert('Gagal membuat kolase');
                document.body.removeChild(overlay);
            });
        }

        function printCollage() {
            const content = document.getElementById('collageContent').innerHTML;
            const w = window.open('', '', 'height=600,width=800');
            w.document.write(`<html><head><title>Kolase Foto</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>.collage-img{height:250px!important;object-fit:cover}@media print{body{-webkit-print-color-adjust:exact}}</style>
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
