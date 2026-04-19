@extends('layouts.app')

@section('template_title')
    {{ $dataLapangan->nama_pu ?? __('Show') . ' ' . __('Data Lapangan') }}
@endsection

@section('content')
    <section class="content container-fluid" data-lock-id="{{ $dataLapangan->id }}">

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
                    $hasPendingProgress = $latestProgress?->status === 'PENDING';
                @endphp
                <span class="badge {{ $statusClass }} px-3 py-2 fs-6">{{ $dataLapangan->status }}</span>

                @if (($dataLapangan->status == 'PROGRESS OSS' || $dataLapangan->status == 'DITOLAK') && !$hasPendingProgress)
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                        data-bs-target="#modalUpdateStatusHalal">
                        <i class="las la-edit me-1"></i>Update Status Halal
                    </button>
                @elseif ($hasPendingProgress)
                    <span class="badge bg-warning text-dark px-3 py-2">
                        <i class="las la-clock me-1"></i>Menunggu Review Superadmin
                    </span>
                @endif

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
                                        style="font-size:12px;vertical-align:top;padding-top:14px!important;">
                                        Password
                                    </td>
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
                                    <a href="{{ route('data-entry.datalapangan.download-foto-ktp', $dataLapangan->id) }}"
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
                                    <a href="{{ route('data-entry.datalapangan.download-foto-ktp', $dataLapangan->id) }}"
                                        class="btn btn-primary btn-sm">
                                        <i class="las la-download me-1"></i>Download KTP
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
                                    <a href="{{ route('data-entry.datalapangan.download-foto-produk', $dataLapangan->id) }}"
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
                            style="font-size:11px;letter-spacing:.05em;color:var(--vz-secondary-color)">File OSS / NIB</p>

                        @if ($dataLapangan->file_oss)
                            {{-- File sudah ada --}}
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-success-subtle mb-3">
                                <div class="rounded d-flex align-items-center justify-content-center bg-success text-white fw-bold"
                                    style="width:36px;height:36px;font-size:11px;flex-shrink:0;">PDF</div>
                                <div class="flex-grow-1" style="min-width:0">
                                    <div class="fw-semibold" style="font-size:13px;">File NIB tersedia</div>
                                    <div class="text-muted" style="font-size:12px;">Klik tombol di kanan untuk membuka
                                    </div>
                                </div>
                                <a href="{{ asset('storage/' . $dataLapangan->file_oss) }}" target="_blank"
                                    class="btn btn-success btn-sm">
                                    <i class="las la-external-link-alt me-1"></i>Lihat File NIB
                                </a>
                            </div>
                        @else
                            {{-- File belum ada —- tampilkan form upload --}}
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-danger-subtle mb-3">
                                <div class="rounded d-flex align-items-center justify-content-center bg-danger text-white fw-bold"
                                    style="width:36px;height:36px;font-size:11px;flex-shrink:0;">PDF</div>
                                <div class="flex-grow-1" style="font-size:13px;color:var(--vz-danger);">
                                    File OSS belum tersedia
                                </div>
                            </div>
                        @endif

                        @if ($entryType === 'OSS' && !$dataLapangan->file_oss)
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
    {{-- MODALS                                    --}}
    {{-- ========================================= --}}

    {{-- Modal Peraturan --}}
    <div class="modal fade" id="modalPeraturan" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom" style="background:rgba(var(--vz-warning-rgb),.12);">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        <span class="rounded d-flex align-items-center justify-content-center"
                            style="width:28px;height:28px;background:rgba(var(--vz-warning-rgb),.2);">
                            <i class="las la-exclamation-triangle" style="color:var(--vz-warning);font-size:15px;"></i>
                        </span>
                        Peraturan Sesi Pengerjaan
                    </h5>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted mb-4" style="font-size:13px;">
                        Harap baca dan pahami peraturan berikut sebelum memulai pengerjaan:
                    </p>
                    @php
                        $peraturan = [
                            [
                                'icon' => 'la-clock',
                                'color' => 'var(--vz-warning)',
                                'bg' => 'rgba(var(--vz-warning-rgb),.1)',
                                'text' =>
                                    '<strong>Waktu pengerjaan adalah 50 menit.</strong> Timer berjalan otomatis dan tidak dapat dijeda.',
                            ],
                            [
                                'icon' => 'la-lock',
                                'color' => 'var(--vz-danger)',
                                'bg' => 'rgba(var(--vz-danger-rgb),.08)',
                                'text' =>
                                    '<strong>Jangan keluar atau menutup halaman.</strong> Menutup tab atau refresh manual akan mengakhiri sesi secara otomatis.',
                            ],
                            [
                                'icon' => 'la-user-lock',
                                'color' => 'var(--vz-primary)',
                                'bg' => 'rgba(var(--vz-primary-rgb),.08)',
                                'text' =>
                                    '<strong>Data dikunci eksklusif untuk Anda.</strong> Pengguna lain tidak dapat mengakses data yang sedang dikerjakan.',
                            ],
                            [
                                'icon' => 'la-redo-alt',
                                'color' => 'var(--vz-success)',
                                'bg' => 'rgba(var(--vz-success-rgb),.08)',
                                'text' =>
                                    '<strong>Perpanjang sesi sebelum waktu habis.</strong> Gunakan tombol <em>"Perpanjang Sesi"</em> di pojok kanan bawah.',
                            ],
                            [
                                'icon' => 'la-exclamation-circle',
                                'color' => 'var(--vz-danger)',
                                'bg' => 'rgba(var(--vz-danger-rgb),.08)',
                                'text' =>
                                    '<strong>Sesi yang habis tidak dapat dipulihkan.</strong> Data akan dilepas otomatis dan Anda diarahkan ke halaman daftar.',
                            ],
                        ];
                    @endphp
                    <div class="d-flex flex-column gap-3">
                        @foreach ($peraturan as $p)
                            <div class="d-flex align-items-start gap-3 p-3 rounded-3"
                                style="background:{{ $p['bg'] }}">
                                <span class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width:32px;height:32px;background:rgba(255,255,255,.6);">
                                    <i class="las {{ $p['icon'] }}"
                                        style="font-size:15px;color:{{ $p['color'] }};"></i>
                                </span>
                                <div style="font-size:13px;line-height:1.5;">{!! $p['text'] !!}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer border-top justify-content-center">
                    <button type="button" class="btn btn-warning px-5 fw-semibold" id="btnMengerti">
                        <i class="las la-check me-2"></i>Saya Mengerti, Mulai Pengerjaan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Kolase Foto --}}
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


    {{-- ===== LOCK TIMER WIDGET ===== --}}
    <div id="lockTimerContainer" class="position-fixed bottom-0 end-0 m-3" style="z-index:9999;display:none;">
        <div class="card border-0 shadow" style="min-width:220px;border-radius:14px;overflow:hidden;">
            <div class="card-body p-0">
                {{-- Header strip --}}
                <div class="d-flex align-items-center gap-2 px-3 py-2"
                    style="background:rgba(var(--vz-warning-rgb),.15);border-bottom:0.5px solid rgba(var(--vz-warning-rgb),.25);">
                    <span class="rounded-circle bg-warning d-flex align-items-center justify-content-center"
                        style="width:20px;height:20px;">
                        <i class="las la-lock" style="font-size:11px;color:#fff;"></i>
                    </span>
                    <small class="fw-semibold" style="font-size:12px;color:var(--vz-warning);">Sesi Pengerjaan</small>
                </div>
                {{-- Timer display --}}
                <div class="text-center px-3 pt-3 pb-1">
                    <span id="lockTimerDisplay" class="fw-bold"
                        style="font-size:28px;font-family:var(--vz-font-monospace,monospace);letter-spacing:2px;">50:00</span>
                </div>
                {{-- Progress bar --}}
                <div class="px-3 pb-2">
                    <div class="progress"
                        style="height:4px;border-radius:4px;background:rgba(var(--vz-body-color-rgb),.08);">
                        <div id="lockTimerProgress" class="progress-bar bg-success"
                            style="width:100%;transition:width .8s linear,background-color .5s;border-radius:4px;"></div>
                    </div>
                    <small class="text-muted d-block text-center mt-1" style="font-size:11px;">Data sedang dikunci untuk
                        Anda</small>
                </div>
                {{-- Perpanjang button --}}
                <div class="px-3 pb-3">
                    <button id="btnPerpanjang" class="btn btn-warning btn-sm w-100 fw-semibold">
                        <i class="las la-redo-alt me-1"></i>Perpanjang Sesi
                    </button>
                </div>
            </div>
        </div>
    </div>


    {{-- ========================================= --}}
    {{-- SCRIPTS                                   --}}
    {{-- ========================================= --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        // Auto-dismiss alerts after 5s
        setTimeout(function() {
            document.querySelectorAll('section.content .alert').forEach(function(el) {
                bootstrap.Alert.getOrCreateInstance(el).close();
            });
        }, 5000);

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

        // ===== MODAL PERATURAN =====
        (function() {
            sessionStorage.removeItem('isReloading');
            const modalEl = document.getElementById('modalPeraturan');
            const modalPeraturan = new bootstrap.Modal(modalEl);
            modalPeraturan.show();
            document.getElementById('btnMengerti').addEventListener('click', function() {
                bootstrap.Modal.getInstance(modalEl).hide();
            });
        })();

        // ===== LOCK TIMER — 50 menit =====
        (function() {
            const LOCK_URL = '/api/data-entry/data-lapangans';
            const LIST_URL = '{{ route('data-entry.data-lapangan.index') }}';
            const DURATION = 50 * 60;

            const sectionEl = document.querySelector('section.content[data-lock-id]');
            const PAGE_ID = sectionEl ? sectionEl.dataset.lockId : null;
            let LOCK_ID = sessionStorage.getItem('currentLockId');

            function getCsrfToken() {
                return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            }

            function showExpiredAlert() {
                document.getElementById('lockTimerContainer').style.display = 'none';
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger fade show position-fixed top-0 start-0 end-0 m-3';
                alertDiv.style.zIndex = '99999';
                alertDiv.innerHTML = `<i class="las la-exclamation-circle me-2"></i>
                <strong>Waktu Sesi Habis!</strong> Data telah dilepas. Anda akan diarahkan dalam
                <strong id="redirectCountdown">5</strong> detik...`;
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
                    bar.className = 'progress-bar bg-danger';
                    disp.style.color = 'var(--vz-danger)';
                } else if (timeLeft <= 5 * 60) {
                    bar.className = 'progress-bar bg-warning';
                    disp.style.color = 'var(--vz-warning)';
                } else {
                    bar.className = 'progress-bar bg-success';
                    disp.style.color = 'var(--vz-body-color)';
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
                    this.innerHTML = '<i class="las la-redo-alt me-1"></i>Perpanjang Sesi';
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
