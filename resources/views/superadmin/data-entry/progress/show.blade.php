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
                        <form action="{{ route('superadmin.data-entry-progress.terima', $progress->hashed_id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success btn-sm fw-semibold">
                                <i class="las la-check me-1"></i>Terima
                            </button>
                        </form>
                        <button type="button" class="btn btn-warning btn-sm fw-semibold" data-bs-toggle="modal"
                            data-bs-target="#modalRevisi">
                            <i class="las la-edit me-1"></i>Minta Revisi
                        </button>
                        <button type="button" class="btn btn-danger btn-sm fw-semibold" data-bs-toggle="modal"
                            data-bs-target="#modalTolak">
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

            {{-- ========== KOLOM KANAN — Data Lapangan & File ========== --}}
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
                <form action="{{ route('superadmin.data-entry-progress.revisi', $progress->hashed_id) }}" method="POST">
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
                <form action="{{ route('superadmin.data-entry-progress.tolak', $progress->hashed_id) }}" method="POST">
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

    <script>
        setTimeout(function() {
            document.querySelectorAll('.alert-dismissible').forEach(function(el) {
                bootstrap.Alert.getOrCreateInstance(el)?.close();
            });
        }, 5000);
    </script>
@endsection
