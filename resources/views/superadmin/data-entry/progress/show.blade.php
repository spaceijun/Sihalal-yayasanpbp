@extends('layouts.app')

@section('template_title')
    Detail Progress — {{ $progress->dataLapangan?->nama_pu ?? 'N/A' }}
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
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mt-3" role="alert">
                <i class="las la-exclamation-circle fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ===== TOP BAR ===== --}}
        @php
            $dl = $progress->dataLapangan;
            $entryType = $progress->dataEntry?->entry_type;

            // Kumpulkan semua produk (utama + tambahan yang tidak null)
            $allProducts = [];
            if ($dl) {
                $productFields = [
                    1 => ['nama' => $dl->nama_produk, 'foto' => $dl->foto_produk],
                    2 => ['nama' => $dl->nama_produk_2, 'foto' => $dl->foto_produk_2],
                    3 => ['nama' => $dl->nama_produk_3, 'foto' => $dl->foto_produk_3],
                    4 => ['nama' => $dl->nama_produk_4, 'foto' => $dl->foto_produk_4],
                    5 => ['nama' => $dl->nama_produk_5, 'foto' => $dl->foto_produk_5],
                ];
                foreach ($productFields as $idx => $p) {
                    if (!empty($p['nama'])) {
                        $allProducts[$idx] = $p;
                    }
                }
            }

            $progressStatusClass = match ($progress->status) {
                'PENDING' => ['bg' => 'warning', 'text' => 'text-dark', 'label' => 'PENDING'],
                'DITERIMA' => ['bg' => 'success', 'text' => 'text-white', 'label' => 'DITERIMA'],
                'REVISI' => ['bg' => 'warning', 'text' => 'text-dark', 'label' => 'REVISI'],
                'DITOLAK' => ['bg' => 'dark', 'text' => 'text-white', 'label' => 'DITOLAK'],
                default => ['bg' => 'secondary', 'text' => 'text-white', 'label' => $progress->status],
            };

            $dlStatusClass = match ($dl?->status) {
                'PROGRESS OSS' => 'bg-info',
                'PROGRESS SIHALAL' => 'bg-primary',
                'TERBIT SH' => 'bg-success',
                'DITOLAK' => 'bg-dark',
                default => 'bg-secondary',
            };
        @endphp

        <div class="d-flex align-items-start justify-content-between pt-3 pb-3 border-bottom mb-4">
            <div>
                <h5 class="fw-semibold mb-1">{{ $dl?->nama_pu ?? 'Detail Progress' }}</h5>
                <p class="text-muted mb-0 small">
                    NIK {{ $dl?->nik ?? '-' }}
                    &nbsp;·&nbsp;
                    Pendamping: <strong>{{ $dl?->enumerator?->nama_lengkap ?? '-' }}</strong>
                </p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                <span class="badge bg-{{ $progressStatusClass['bg'] }} {{ $progressStatusClass['text'] }} px-3 py-2 fs-6">
                    {{ $progressStatusClass['label'] }}
                </span>
                <a href="{{ route('superadmin.data-entry-progress.index') }}" class="btn btn-light btn-sm">
                    <i class="las la-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>

        {{-- ===== ACTION BAR (hanya muncul jika PENDING) ===== --}}
        @if ($progress->status === 'PENDING')
            <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                <div class="d-flex align-items-center gap-3 px-4 py-3"
                    style="background:rgba(var(--vz-warning-rgb),.07);border-left:4px solid var(--vz-warning);">
                    <span class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width:36px;height:36px;background:rgba(var(--vz-warning-rgb),.15);">
                        <i class="las la-hourglass-half" style="font-size:18px;color:var(--vz-warning);"></i>
                    </span>
                    <div class="flex-grow-1">
                        <div class="fw-semibold" style="font-size:14px;color:var(--vz-warning);">Menunggu Tindakan</div>
                        <div class="text-muted" style="font-size:13px;">Progress ini belum ditinjau. Silakan terima, minta
                            revisi, atau tolak.</div>
                    </div>
                    <div class="d-flex gap-2 flex-shrink-0">
                        <button type="button" class="btn btn-success btn-sm fw-semibold"
                            onclick="submitTerima('{{ $progress->hashed_id }}', '{{ $entryType }}')">
                            <i class="las la-check me-1"></i>Terima
                        </button>
                        <button type="button" class="btn btn-warning btn-sm fw-semibold"
                            onclick="bukaModalRevisi('{{ $progress->hashed_id }}')">
                            <i class="las la-edit me-1"></i>Minta Revisi
                        </button>
                        <button type="button" class="btn btn-danger btn-sm fw-semibold"
                            onclick="bukaModalTolak('{{ $progress->hashed_id }}')">
                            <i class="las la-times me-1"></i>Tolak
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- ===== MAIN GRID ===== --}}
        <div class="row g-4">

            {{-- ========== KOLOM KIRI — Info Progress ========== --}}
            <div class="col-lg-5">

                {{-- Card: Informasi Progress --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                        <span class="rounded d-flex align-items-center justify-content-center"
                            style="width:28px;height:28px;background:rgba(var(--vz-primary-rgb),.12);">
                            <i class="las la-info-circle" style="font-size:14px;color:var(--vz-primary);"></i>
                        </span>
                        <h6 class="mb-0 fw-semibold">Informasi Progress</h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-borderless mb-0" style="font-size:14px;">
                            <tbody>
                                <tr class="border-bottom">
                                    <td class="fw-semibold text-muted py-3 px-4"
                                        style="width:42%;font-size:12px;vertical-align:middle;">Data Entry</td>
                                    <td class="py-3 pe-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-semibold flex-shrink-0"
                                                style="width:28px;height:28px;font-size:11px;">
                                                {{ strtoupper(substr($progress->dataEntry?->user?->name ?? 'U', 0, 2)) }}
                                            </div>
                                            <span>{{ $progress->dataEntry?->user?->name ?? '-' }}</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="fw-semibold text-muted py-3 px-4" style="font-size:12px;">Entry Type</td>
                                    <td class="py-3 pe-4">
                                        @if ($entryType === 'OSS')
                                            <span class="badge bg-info-subtle text-info px-3 py-2">OSS</span>
                                        @elseif ($entryType === 'SIHALAL')
                                            <span class="badge bg-primary-subtle text-primary px-3 py-2">SIHALAL</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary px-3 py-2">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="fw-semibold text-muted py-3 px-4" style="font-size:12px;">Tanggal Aksi</td>
                                    <td class="py-3 pe-4" style="font-size:13px;">
                                        {{ $progress->actioned_at?->format('d M Y H:i') ?? '-' }}
                                    </td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="fw-semibold text-muted py-3 px-4" style="font-size:12px;">File Type</td>
                                    <td class="py-3 pe-4">
                                        <code class="bg-light px-2 py-1 rounded" style="font-size:12px;">
                                            {{ strtoupper($progress->new_data['file_type'] ?? '-') }}
                                        </code>
                                    </td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="fw-semibold text-muted py-3 px-4" style="font-size:12px;">Nama File</td>
                                    <td class="py-3 pe-4" style="font-size:13px;color:var(--vz-body-color);">
                                        @php $fileName = $progress->new_data['file_name'] ?? '-'; @endphp
                                        {{ $fileName !== 'N/A' ? $fileName : 'Update Status' }}
                                    </td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="fw-semibold text-muted py-3 px-4" style="font-size:12px;">Verifikator</td>
                                    <td class="py-3 pe-4" style="font-size:13px;">
                                        @if ($progress->verifikator)
                                            <div class="fw-semibold">{{ $progress->verifikator->nama_lengkap }}</div>
                                            <div class="text-muted" style="font-size:12px;">
                                                {{ $progress->tanggal_verifikasi?->format('d M Y') }}</div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted py-3 px-4" style="font-size:12px;">Status Progress
                                    </td>
                                    <td class="py-3 pe-4">
                                        <span
                                            class="badge bg-{{ $progressStatusClass['bg'] }} {{ $progressStatusClass['text'] }} px-3 py-2">
                                            {{ $progressStatusClass['label'] }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Card: Riwayat Keterangan --}}
                @if ($progress->keterangan_revisi || $progress->keterangan_update)
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                            <span class="rounded d-flex align-items-center justify-content-center"
                                style="width:28px;height:28px;background:rgba(var(--vz-info-rgb),.12);">
                                <i class="las la-comments" style="font-size:14px;color:var(--vz-info);"></i>
                            </span>
                            <h6 class="mb-0 fw-semibold">Riwayat Keterangan</h6>
                        </div>
                        <div class="card-body d-flex flex-column gap-3">
                            @if ($progress->keterangan_revisi)
                                <div class="d-flex gap-3">
                                    <span
                                        class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width:32px;height:32px;background:rgba(var(--vz-danger-rgb),.1);">
                                        <i class="las la-user-shield" style="font-size:14px;color:var(--vz-danger);"></i>
                                    </span>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold mb-1"
                                            style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:var(--vz-danger);">
                                            Catatan Superadmin
                                        </div>
                                        <div class="p-3 rounded-3"
                                            style="background:rgba(var(--vz-danger-rgb),.06);font-size:13px;border-left:3px solid var(--vz-danger);">
                                            {{ $progress->keterangan_revisi }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if ($progress->keterangan_update)
                                <div class="d-flex gap-3">
                                    <span
                                        class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width:32px;height:32px;background:rgba(var(--vz-success-rgb),.1);">
                                        <i class="las la-reply" style="font-size:14px;color:var(--vz-success);"></i>
                                    </span>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold mb-1"
                                            style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:var(--vz-success);">
                                            Balasan Data Entry
                                        </div>
                                        <div class="p-3 rounded-3"
                                            style="background:rgba(var(--vz-success-rgb),.06);font-size:13px;border-left:3px solid var(--vz-success);">
                                            {{ $progress->keterangan_update }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

            </div>

            {{-- ========== KOLOM KANAN — Data Lapangan, Foto & Produk ========== --}}
            <div class="col-lg-7">

                {{-- Card: Data Pelaku Usaha --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                        <span class="rounded d-flex align-items-center justify-content-center"
                            style="width:28px;height:28px;background:rgba(var(--vz-primary-rgb),.12);">
                            <i class="las la-user" style="font-size:14px;color:var(--vz-primary);"></i>
                        </span>
                        <h6 class="mb-0 fw-semibold">Data Pelaku Usaha</h6>
                        @if ($dl)
                            <span class="badge {{ $dlStatusClass }} ms-auto px-3 py-2">{{ $dl->status }}</span>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        @if ($dl)
                            <table class="table table-borderless mb-0" style="font-size:14px;">
                                <tbody>
                                    @php
                                        $puFields = [
                                            ['Nama PU', $dl->nama_pu, null],
                                            ['NIK', $dl->nik, 'mono'],
                                            ['Telepon', $dl->telephone, null],
                                            ['Email', $dl->email ?? '-', null],
                                            ['Email Sihalal', $dl->email_sihalal ?? '-', null],
                                            ['Nama Produk', $dl->nama_produk ?? '-', null],
                                            ['Alamat', $dl->alamat, null],
                                            ['Pendamping', $dl->enumerator?->nama_lengkap ?? '-', null],
                                        ];
                                    @endphp
                                    @foreach ($puFields as $idx => $f)
                                        <tr class="{{ $idx < count($puFields) - 1 ? 'border-bottom' : '' }}">
                                            <td class="fw-semibold text-muted py-3 px-4"
                                                style="width:32%;font-size:12px;vertical-align:top;padding-top:14px!important;">
                                                {{ $f[0] }}
                                            </td>
                                            <td class="py-3 pe-4"
                                                style="{{ $f[2] === 'mono' ? 'font-family:var(--vz-font-monospace);' : '' }}color:var(--vz-body-color);">
                                                {{ $f[1] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="px-4 py-3 text-muted" style="font-size:13px;">
                                Data lapangan tidak ditemukan.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Card: Dokumentasi Foto --}}
                @if ($dl)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                            <span class="rounded d-flex align-items-center justify-content-center"
                                style="width:28px;height:28px;background:rgba(var(--vz-warning-rgb),.12);">
                                <i class="las la-images" style="font-size:14px;color:var(--vz-warning);"></i>
                            </span>
                            <h6 class="mb-0 fw-semibold">Dokumentasi Foto</h6>
                        </div>
                        <div class="card-body p-0">

                            @php
                                $staticPhotos = [
                                    [
                                        'label' => 'Foto KTP',
                                        'foto' => $dl->foto_ktp,
                                        'dl_route' => route(
                                            'superadmin.datalapangan.download-foto-ktp',
                                            $dl->hashed_id,
                                        ),
                                        'dl_label' => 'KTP',
                                    ],
                                    [
                                        'label' => 'Foto Rumah',
                                        'foto' => $dl->foto_rumah,
                                        'dl_route' => route(
                                            'superadmin.datalapangan.download-foto-rumah-pdf',
                                            $dl->hashed_id,
                                        ),
                                        'dl_label' => 'PDF',
                                    ],
                                    [
                                        'label' => 'Foto Pendamping',
                                        'foto' => $dl->foto_pendamping,
                                        'dl_route' => route(
                                            'superadmin.datalapangan.download-foto-pendamping',
                                            $dl->hashed_id,
                                        ),
                                        'dl_label' => 'Download',
                                    ],
                                ];
                            @endphp

                            @foreach ($staticPhotos as $p)
                                <div class="d-flex align-items-center justify-content-between px-4 py-2 border-bottom"
                                    style="font-size:13px;">
                                    <div class="d-flex align-items-center gap-2">
                                        @if (!empty($p['foto']))
                                            <img src="{{ asset('storage/' . $p['foto']) }}"
                                                style="width:36px;height:36px;border-radius:7px;object-fit:cover;border:1px solid var(--vz-border-color);cursor:pointer;"
                                                onclick="viewProgressImage('{{ asset('storage/' . $p['foto']) }}', '{{ $p['label'] }}')"
                                                alt="{{ $p['label'] }}">
                                        @else
                                            <div class="rounded d-flex align-items-center justify-content-center bg-light"
                                                style="width:36px;height:36px;font-size:14px;color:#94A3B8;border:1px dashed var(--vz-border-color);">
                                                <i class="las la-image"></i>
                                            </div>
                                        @endif
                                        <span class="fw-semibold">{{ $p['label'] }}</span>
                                        @if (empty($p['foto']))
                                            <span class="badge bg-secondary-subtle text-secondary"
                                                style="font-size:10px;">Belum ada</span>
                                        @endif
                                    </div>
                                    <div class="d-flex gap-2">
                                        @if (!empty($p['foto']))
                                            <button type="button" class="btn btn-light btn-sm px-2"
                                                onclick="viewProgressImage('{{ asset('storage/' . $p['foto']) }}', '{{ $p['label'] }}')"
                                                title="Lihat foto">
                                                <i class="las la-eye"></i>
                                            </button>
                                        @endif
                                        <a href="{{ $p['dl_route'] }}" class="btn btn-success btn-sm">
                                            <i class="las la-download me-1"></i>{{ $p['dl_label'] }}
                                        </a>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Foto Produk --}}
                            @foreach ($allProducts as $idx => $prod)
                                @if (!empty($prod['foto']))
                                    <div class="d-flex align-items-center justify-content-between px-4 py-2 border-bottom"
                                        style="font-size:13px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ asset('storage/' . $prod['foto']) }}"
                                                style="width:36px;height:36px;border-radius:7px;object-fit:cover;border:1px solid var(--vz-border-color);cursor:pointer;"
                                                onclick="viewProgressImage('{{ asset('storage/' . $prod['foto']) }}', 'Foto Produk {{ $idx }}: {{ $prod['nama'] }}')"
                                                alt="Foto Produk {{ $idx }}">
                                            <span class="fw-semibold">Foto Produk {{ $idx }}</span>
                                            @if ($idx === 1)
                                                <span class="badge bg-secondary-subtle text-secondary"
                                                    style="font-size:10px;">Utama</span>
                                            @endif
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-light btn-sm px-2"
                                                onclick="viewProgressImage('{{ asset('storage/' . $prod['foto']) }}', 'Foto Produk {{ $idx }}: {{ $prod['nama'] }}')"
                                                title="Lihat foto">
                                                <i class="las la-eye"></i>
                                            </button>
                                            @if ($idx === 1)
                                                <a href="{{ route('superadmin.datalapangan.download-foto-produk', $dl->hashed_id) }}"
                                                    class="btn btn-success btn-sm">
                                                    <i class="las la-download me-1"></i>Download
                                                </a>
                                            @else
                                                <a href="{{ asset('storage/' . $prod['foto']) }}" download
                                                    class="btn btn-success btn-sm">
                                                    <i class="las la-download me-1"></i>Download
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                        </div>
                    </div>
                @endif

                {{-- Card: Produk Terdaftar --}}
                @if ($dl && count($allProducts) > 0)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                            <span class="rounded d-flex align-items-center justify-content-center"
                                style="width:28px;height:28px;background:rgba(var(--vz-warning-rgb),.12);">
                                <i class="las la-box-open" style="font-size:14px;color:var(--vz-warning);"></i>
                            </span>
                            <h6 class="mb-0 fw-semibold">Produk Terdaftar</h6>
                            <span class="badge bg-secondary-subtle text-secondary ms-auto" style="font-size:11px;">
                                {{ count($allProducts) }} produk
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach ($allProducts as $idx => $prod)
                                    <div class="col-4">
                                        <div class="border rounded-3 overflow-hidden"
                                            style="font-size:13px;background:#FAFBFF;transition:box-shadow .2s,transform .2s;"
                                            onmouseover="this.style.boxShadow='0 4px 16px rgba(26,95,200,.12)';this.style.transform='translateY(-2px)';"
                                            onmouseout="this.style.boxShadow='';this.style.transform='';">
                                            @if (!empty($prod['foto']))
                                                <img src="{{ asset('storage/' . $prod['foto']) }}"
                                                    style="width:100%;aspect-ratio:4/3;object-fit:cover;cursor:pointer;display:block;border-bottom:1px solid var(--vz-border-color);"
                                                    onclick="viewProgressImage('{{ asset('storage/' . $prod['foto']) }}', 'Foto Produk {{ $idx }}: {{ $prod['nama'] }}')"
                                                    alt="{{ $prod['nama'] }}">
                                            @else
                                                <div class="d-flex align-items-center justify-content-center bg-light"
                                                    style="aspect-ratio:4/3;font-size:28px;color:#CBD5E1;border-bottom:1px solid var(--vz-border-color);">
                                                    <i class="las la-image"></i>
                                                </div>
                                            @endif
                                            <div class="p-2">
                                                <div
                                                    style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--vz-primary);">
                                                    Produk {{ $idx }}
                                                </div>
                                                <div class="fw-semibold"
                                                    style="font-size:12px;line-height:1.4;color:var(--vz-body-color);">
                                                    {{ $prod['nama'] }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Card: File OSS --}}
                @if ($dl && $entryType === 'OSS' && $dl->file_oss)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                            <span class="rounded d-flex align-items-center justify-content-center"
                                style="width:28px;height:28px;background:rgba(var(--vz-danger-rgb),.12);">
                                <i class="las la-file-pdf" style="font-size:14px;color:var(--vz-danger);"></i>
                            </span>
                            <h6 class="mb-0 fw-semibold">File OSS</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-success-subtle">
                                <div class="rounded d-flex align-items-center justify-content-center bg-success text-white fw-bold"
                                    style="width:40px;height:40px;font-size:12px;flex-shrink:0;">PDF</div>
                                <div class="flex-grow-1" style="min-width:0;">
                                    <div class="fw-semibold" style="font-size:13px;">File OSS tersedia</div>
                                    <div class="text-muted" style="font-size:12px;">Klik tombol untuk membuka atau
                                        mengunduh</div>
                                </div>
                                <a href="{{ asset('storage/' . $dl->file_oss) }}" target="_blank"
                                    class="btn btn-success btn-sm fw-semibold flex-shrink-0">
                                    <i class="las la-external-link-alt me-1"></i>Buka File
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Card: File SIHALAL --}}
                @if ($dl && $entryType === 'SIHALAL' && $dl->file_sihalal)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                            <span class="rounded d-flex align-items-center justify-content-center"
                                style="width:28px;height:28px;background:rgba(var(--vz-primary-rgb),.12);">
                                <i class="las la-file-pdf" style="font-size:14px;color:var(--vz-primary);"></i>
                            </span>
                            <h6 class="mb-0 fw-semibold">File Sihalal</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-primary-subtle">
                                <div class="rounded d-flex align-items-center justify-content-center bg-primary text-white fw-bold"
                                    style="width:40px;height:40px;font-size:12px;flex-shrink:0;">PDF</div>
                                <div class="flex-grow-1" style="min-width:0;">
                                    <div class="fw-semibold" style="font-size:13px;">File Sihalal tersedia</div>
                                    <div class="text-muted" style="font-size:12px;">Klik tombol untuk membuka atau
                                        mengunduh</div>
                                </div>
                                <a href="{{ asset('storage/' . $dl->file_sihalal) }}" target="_blank"
                                    class="btn btn-primary btn-sm fw-semibold flex-shrink-0">
                                    <i class="las la-external-link-alt me-1"></i>Buka File
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </section>


    {{-- ========================================= --}}
    {{-- FORM TERIMA (hidden, di-submit via JS)    --}}
    {{-- ========================================= --}}
    <form id="formTerima" method="POST" action="">
        @csrf
        @method('PATCH')
        <input type="hidden" name="verifikator_id" id="terimaVerifikatorId">
        <input type="hidden" name="tanggal_verifikasi" id="terimaTanggalVerifikasi">
    </form>

    {{-- ========================================= --}}
    {{-- MODAL TERIMA (dengan step verifikasi)     --}}
    {{-- ========================================= --}}
    <div class="modal fade" id="modalTerima" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom" style="background:rgba(var(--vz-success-rgb),.08);">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        <span class="rounded d-flex align-items-center justify-content-center"
                            style="width:28px;height:28px;background:rgba(var(--vz-success-rgb),.2);">
                            <i class="las la-check" style="font-size:15px;color:var(--vz-success);"></i>
                        </span>
                        <span id="modalTerimaTitle">Terima Progress</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">

                    {{-- STEP 1: Pertanyaan --}}
                    <div id="stepPertanyaan">

                        {{-- OSS --}}
                        <div id="pertanyaanOSS" style="display:none;">
                            <div class="alert alert-warning d-flex gap-2 mb-3 py-2" style="font-size:13px;">
                                <i class="las la-exclamation-triangle mt-1 flex-shrink-0"></i>
                                <div><strong>Perhatian!</strong> Pastikan Anda telah memeriksa file sebelum melanjutkan.
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="fw-semibold" style="font-size:13px;">Apakah File OSS yang diajukan sudah
                                    benar?</label>
                                <div class="mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ossCheck" id="ossYa"
                                            value="ya">
                                        <label class="form-check-label" for="ossYa">Ya, saya sudah mengecek dan
                                            benar.</label>
                                    </div>
                                </div>
                                <div id="errorOSS" class="text-danger mt-1" style="display:none;font-size:12px;">
                                    Anda harus mengkonfirmasi file OSS sudah benar.
                                </div>
                            </div>
                        </div>

                        {{-- SIHALAL --}}
                        <div id="pertanyaanSIHALAL" style="display:none;">
                            <div class="alert alert-info d-flex gap-2 mb-3 py-2" style="font-size:13px;">
                                <i class="las la-info-circle mt-1 flex-shrink-0"></i>
                                <div><strong>Catatan:</strong> Kedua tahap wajib sudah selesai sebelum melanjutkan
                                    verifikasi.</div>
                            </div>
                            <div class="mb-3">
                                <label class="fw-semibold" style="font-size:13px;">1. Apakah data ini sudah dicek pada
                                    Website Sihalal?</label>
                                <div class="mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="siHalalCek"
                                            id="siHalalCekYa" value="ya" onchange="cekSiHalalValid()">
                                        <label class="form-check-label" for="siHalalCekYa">Ya, sudah saya cek.</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="siHalalCek"
                                            id="siHalalCekBelum" value="belum" onchange="cekSiHalalValid()">
                                        <label class="form-check-label text-danger" for="siHalalCekBelum">Belum
                                            dicek.</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="fw-semibold" style="font-size:13px;">2. Apakah data ini sudah diverifikasi
                                    dan di-Verval pada Website Sihalal?</label>
                                <div class="mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="siHalalVerval"
                                            id="siHalalVervalYa" value="ya" onchange="cekSiHalalValid()">
                                        <label class="form-check-label" for="siHalalVervalYa">Ya, sudah saya verif dan
                                            Verval.</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="siHalalVerval"
                                            id="siHalalVervalBelum" value="belum" onchange="cekSiHalalValid()">
                                        <label class="form-check-label text-danger" for="siHalalVervalBelum">Belum diverif
                                            dan Verval.</label>
                                    </div>
                                </div>
                            </div>
                            <div id="alertSiHalalBelum" class="alert alert-danger d-flex gap-2 py-2 mt-2"
                                style="display:none!important;font-size:13px;">
                                <i class="las la-times-circle mt-1 flex-shrink-0"></i>
                                <div><strong>Tidak dapat melanjutkan!</strong> Data harus sudah dicek dan di-Verval pada
                                    Website Sihalal.</div>
                            </div>
                            <div id="errorSIHALAL" class="text-danger mt-1" style="display:none;font-size:12px;">
                                Harap jawab kedua pertanyaan di atas.
                            </div>
                        </div>
                    </div>

                    {{-- STEP 2: Verifikator --}}
                    <div id="stepVerifikator" style="display:none;">
                        <div class="alert alert-success d-flex gap-2 mb-3 py-2" style="font-size:13px;">
                            <i class="las la-check-circle mt-1 flex-shrink-0"></i>
                            <div>Pemeriksaan selesai. Silahkan pilih verifikator dan tanggal verifikasi.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Verifikator <span class="text-danger">*</span>
                            </label>
                            <select id="selectVerifikator" class="form-select" required>
                                <option value="">-- Pilih Verifikator --</option>
                                @foreach ($verifikators as $v)
                                    <option value="{{ $v->id }}">
                                        {{ $v->nama_lengkap }} (Rp
                                        {{ number_format($v->rate_per_data, 0, ',', '.') }}/data)
                                    </option>
                                @endforeach
                            </select>
                            <div id="errorVerifikator" class="text-danger mt-1" style="display:none;font-size:12px;">
                                Verifikator wajib dipilih.
                            </div>
                        </div>
                        <div class="mb-1">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Tanggal Verifikasi <span class="text-danger">*</span>
                            </label>
                            <input type="date" id="inputTanggalVerifikasi" class="form-control"
                                value="{{ now()->toDateString() }}" required>
                            <div id="errorTanggal" class="text-danger mt-1" style="display:none;font-size:12px;">
                                Tanggal verifikasi wajib diisi.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary fw-semibold" id="btnLanjutVerifikasi">
                        <i class="las la-arrow-right me-1"></i>Lanjut ke Verifikasi
                    </button>
                    <button type="button" class="btn btn-success fw-semibold" id="btnKonfirmasiTerima"
                        style="display:none;">
                        <i class="las la-check me-1"></i>Ya, Terima
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- MODAL MINTA REVISI                        --}}
    {{-- ========================================= --}}
    <div class="modal fade" id="modalRevisi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom" style="background:rgba(var(--vz-warning-rgb),.1);">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        <span class="rounded d-flex align-items-center justify-content-center"
                            style="width:28px;height:28px;background:rgba(var(--vz-warning-rgb),.2);">
                            <i class="las la-edit" style="font-size:15px;color:var(--vz-warning);"></i>
                        </span>
                        Minta Revisi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formRevisi" method="POST" action="">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body p-4">
                        <div class="alert alert-warning d-flex gap-2 mb-4 py-2" style="font-size:13px;">
                            <i class="las la-info-circle mt-1 flex-shrink-0"></i>
                            <div>Catatan ini akan ditampilkan ke data entry sebagai panduan perbaikan.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Catatan Revisi <span class="text-danger">*</span>
                            </label>
                            <textarea name="keterangan_revisi" class="form-control" rows="4"
                                placeholder="Jelaskan apa yang perlu diperbaiki..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning fw-semibold">
                            <i class="las la-paper-plane me-1"></i>Kirim Revisi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- MODAL TOLAK                               --}}
    {{-- ========================================= --}}
    <div class="modal fade" id="modalTolak" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom" style="background:rgba(var(--vz-danger-rgb),.08);">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        <span class="rounded d-flex align-items-center justify-content-center"
                            style="width:28px;height:28px;background:rgba(var(--vz-danger-rgb),.15);">
                            <i class="las la-times-circle" style="font-size:15px;color:var(--vz-danger);"></i>
                        </span>
                        Tolak Progress
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formTolak" method="POST" action="">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body p-4">
                        <div class="d-flex gap-2 p-3 rounded-3 mb-4"
                            style="background:rgba(var(--vz-danger-rgb),.07);border-left:3px solid var(--vz-danger);font-size:13px;">
                            <i class="las la-exclamation-triangle mt-1 flex-shrink-0" style="color:var(--vz-danger);"></i>
                            <div>Tindakan ini bersifat <strong>permanen</strong>. Data akan ditolak dan tidak dapat
                                dikembalikan ke status PENDING.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Alasan Penolakan <span class="text-danger">*</span>
                            </label>
                            <textarea name="keterangan_revisi" class="form-control" rows="4" placeholder="Jelaskan alasan penolakan..."
                                required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger fw-semibold">
                            <i class="las la-times me-1"></i>Tolak Progress
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- MODAL FULL IMAGE (untuk foto)             --}}
    {{-- ========================================= --}}
    <div class="modal fade" id="modalProgressFullImage" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title" id="progressFullImageTitle">Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-3">
                    <img id="progressFullImageSrc" src="" alt="" class="img-fluid rounded"
                        style="max-height:580px;">
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-success fw-semibold" onclick="downloadProgressImage()">
                        <i class="las la-download me-1"></i>Download
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ── State ──────────────────────────────────────────────
        let _terimaProgressId = null;
        let _terimaEntryType = null;

        // ── Auto-dismiss alerts ────────────────────────────────
        setTimeout(function() {
            document.querySelectorAll('.alert-dismissible').forEach(function(el) {
                bootstrap.Alert.getOrCreateInstance(el)?.close();
            });
        }, 5000);

        // ── DOMContentLoaded ───────────────────────────────────
        document.addEventListener('DOMContentLoaded', function() {

            // Lanjut ke step verifikator
            document.getElementById('btnLanjutVerifikasi').addEventListener('click', function() {
                if (!_validasiPertanyaan()) return;
                document.getElementById('stepPertanyaan').style.display = 'none';
                document.getElementById('stepVerifikator').style.display = 'block';
                document.getElementById('btnLanjutVerifikasi').style.display = 'none';
                document.getElementById('btnKonfirmasiTerima').style.display = 'inline-flex';
            });

            // Konfirmasi terima — submit form
            document.getElementById('btnKonfirmasiTerima').addEventListener('click', function() {
                const verifikatorId = document.getElementById('selectVerifikator').value;
                const tanggalVerifikasi = document.getElementById('inputTanggalVerifikasi').value;
                let valid = true;

                if (!verifikatorId) {
                    document.getElementById('errorVerifikator').style.display = 'block';
                    valid = false;
                } else {
                    document.getElementById('errorVerifikator').style.display = 'none';
                }
                if (!tanggalVerifikasi) {
                    document.getElementById('errorTanggal').style.display = 'block';
                    valid = false;
                } else {
                    document.getElementById('errorTanggal').style.display = 'none';
                }
                if (!valid) return;

                document.getElementById('terimaVerifikatorId').value = verifikatorId;
                document.getElementById('terimaTanggalVerifikasi').value = tanggalVerifikasi;
                document.getElementById('formTerima').action =
                    `/superadmin/data-entry-progress/${_terimaProgressId}/terima`;
                document.getElementById('formTerima').submit();
            });
        });

        // ── submitTerima ───────────────────────────────────────
        function submitTerima(hashedId, entryType) {
            _terimaProgressId = hashedId;
            _terimaEntryType = entryType;
            document.getElementById('modalTerimaTitle').textContent = 'Terima Progress';
            _resetModalTerima(entryType);
            new bootstrap.Modal(document.getElementById('modalTerima')).show();
        }

        function _resetModalTerima(entryType) {
            document.getElementById('stepPertanyaan').style.display = 'block';
            document.getElementById('stepVerifikator').style.display = 'none';
            document.getElementById('btnLanjutVerifikasi').style.display = 'inline-flex';
            document.getElementById('btnKonfirmasiTerima').style.display = 'none';
            document.getElementById('selectVerifikator').value = '';
            document.getElementById('inputTanggalVerifikasi').value = '{{ now()->toDateString() }}';
            document.getElementById('errorVerifikator').style.display = 'none';
            document.getElementById('errorTanggal').style.display = 'none';
            document.getElementById('pertanyaanOSS').style.display = 'none';
            document.getElementById('pertanyaanSIHALAL').style.display = 'none';
            document.getElementById('alertSiHalalBelum').style.display = 'none';
            document.getElementById('errorOSS').style.display = 'none';
            document.getElementById('errorSIHALAL').style.display = 'none';

            document.querySelectorAll('input[name="ossCheck"]').forEach(r => r.checked = false);
            document.querySelectorAll('input[name="siHalalCek"]').forEach(r => r.checked = false);
            document.querySelectorAll('input[name="siHalalVerval"]').forEach(r => r.checked = false);

            if (entryType === 'OSS') {
                document.getElementById('pertanyaanOSS').style.display = 'block';
            } else if (entryType === 'SIHALAL') {
                document.getElementById('pertanyaanSIHALAL').style.display = 'block';
            } else {
                document.getElementById('pertanyaanOSS').style.display = 'block';
                document.getElementById('pertanyaanSIHALAL').style.display = 'block';
            }
        }

        function _validasiPertanyaan() {
            let valid = true;

            if (_terimaEntryType === 'OSS' || _terimaEntryType === null) {
                const ossCheck = document.querySelector('input[name="ossCheck"]:checked');
                if (!ossCheck) {
                    document.getElementById('errorOSS').style.display = 'block';
                    valid = false;
                } else {
                    document.getElementById('errorOSS').style.display = 'none';
                }
            }

            if (_terimaEntryType === 'SIHALAL' || _terimaEntryType === null) {
                const cek = document.querySelector('input[name="siHalalCek"]:checked');
                const verval = document.querySelector('input[name="siHalalVerval"]:checked');
                if (!cek || !verval) {
                    document.getElementById('errorSIHALAL').style.display = 'block';
                    valid = false;
                } else {
                    document.getElementById('errorSIHALAL').style.display = 'none';
                    if (cek.value === 'belum' && verval.value === 'belum') {
                        document.getElementById('alertSiHalalBelum').style.removeProperty('display');
                        valid = false;
                    } else {
                        document.getElementById('alertSiHalalBelum').style.display = 'none';
                    }
                }
            }
            return valid;
        }

        function cekSiHalalValid() {
            const cek = document.querySelector('input[name="siHalalCek"]:checked');
            const verval = document.querySelector('input[name="siHalalVerval"]:checked');
            if (cek && verval && cek.value === 'belum' && verval.value === 'belum') {
                document.getElementById('alertSiHalalBelum').style.removeProperty('display');
            } else {
                document.getElementById('alertSiHalalBelum').style.display = 'none';
            }
        }

        // ── bukaModalRevisi ────────────────────────────────────
        function bukaModalRevisi(hashedId) {
            document.getElementById('formRevisi').action = `/superadmin/data-entry-progress/${hashedId}/revisi`;
            document.querySelector('#formRevisi textarea[name="keterangan_revisi"]').value = '';
            new bootstrap.Modal(document.getElementById('modalRevisi')).show();
        }

        // ── bukaModalTolak ─────────────────────────────────────
        function bukaModalTolak(hashedId) {
            document.getElementById('formTolak').action = `/superadmin/data-entry-progress/${hashedId}/tolak`;
            document.querySelector('#formTolak textarea[name="keterangan_revisi"]').value = '';
            new bootstrap.Modal(document.getElementById('modalTolak')).show();
        }

        // ── viewProgressImage — modal full image ───────────────
        function viewProgressImage(src, title) {
            document.getElementById('progressFullImageSrc').src = src;
            document.getElementById('progressFullImageTitle').textContent = title;
            new bootstrap.Modal(document.getElementById('modalProgressFullImage')).show();
        }

        // ── downloadProgressImage ──────────────────────────────
        function downloadProgressImage() {
            const src = document.getElementById('progressFullImageSrc').src;
            const title = document.getElementById('progressFullImageTitle').textContent;
            fetch(src)
                .then(r => r.blob())
                .then(blob => {
                    const a = document.createElement('a');
                    a.href = URL.createObjectURL(blob);
                    a.download = title.replace(/\s+/g, '_') + '.jpg';
                    a.click();
                })
                .catch(() => alert('Gagal mendownload gambar.'));
        }
    </script>
@endsection
