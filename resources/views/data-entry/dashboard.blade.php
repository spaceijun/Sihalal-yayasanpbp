@extends('layouts.app')

@section('template_title')
    Dashboard
@endsection

@section('content')

    {{-- ============================================================ --}}
    {{-- HERO BANNER: Welcome + Entry Type + Tarif                    --}}
    {{-- ============================================================ --}}
    <div class="db-hero mb-4">
        <div class="db-hero__left">
            <div class="db-hero__greeting">Selamat datang,</div>
            <div class="db-hero__name">{{ $dataEntry->user->name }}</div>
            <div class="db-hero__meta">
                <div class="db-type-pill db-type-pill--{{ strtolower($dataEntry->entry_type) }}">
                    <span class="db-type-pill__dot"></span>
                    DATA ENTRY - {{ $dataEntry->entry_type }}
                </div>
                <div class="db-hero__sep"></div>
                <span class="db-hero__tarif-label">Tarif per paket</span>
                <span class="db-hero__tarif-value">Rp {{ number_format($tarifPer15, 0, ',', '.') }}</span>
                <span class="db-hero__tarif-unit">/ {{ $kelipatanPer }} data</span>
            </div>
        </div>
        <div class="db-hero__right">
            <div class="db-hero__stat">
                <div class="db-hero__stat-num">{{ $kelipatan }}x</div>
                <div class="db-hero__stat-lbl">Paket terpenuhi</div>
            </div>
            <div class="db-hero__stat-divider"></div>
            <div class="db-hero__stat">
                <div class="db-hero__stat-num">Rp
                    {{ number_format($penagihans->where('status', 'Dibayar')->sum('nominal'), 0, ',', '.') }}</div>
                <div class="db-hero__stat-lbl">Total penghasilan</div>
            </div>
        </div>
    </div>

    {{-- Alert rekening --}}
    @if (empty($dataEntry->bank_id) || empty($dataEntry->no_rekening) || empty($dataEntry->nama_rekening))
        <div class="db-alert db-alert--warning mb-4" id="alertRekening">
            <div class="db-alert__icon">
                <i class="ri-error-warning-line"></i>
            </div>
            <div class="db-alert__body">
                <strong>Rekening belum lengkap</strong> — Harap lengkapi informasi bank dan rekening agar proses pembayaran
                dapat berjalan lancar.
                <a href="{{ route('data-entry.manajemen-akun.index') }}" class="db-alert__link">Lengkapi sekarang →</a>
            </div>
        </div>
    @endif



    {{-- ============================================================ --}}
    {{-- ROW 1: Summary Cards                                         --}}
    {{-- ============================================================ --}}
    <p class="text-uppercase fw-medium text-muted fs-11 letter-spacing-1 mb-2">Ringkasan</p>
    <div class="row g-3 mb-3">

        {{-- Total Dientry --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card card-animate border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted text-uppercase fw-medium fs-11 mb-2">Total Dientry</p>
                            <h4 class="fs-22 fw-semibold mb-1">
                                <span class="counter-value" data-target="{{ $totalDientry }}">0</span>
                            </h4>
                            <p class="text-muted fs-12 mb-0">Semua data tersubmit</p>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded-3 fs-20">
                                <i class="ri-database-2-line text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Data Diterima --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card card-animate border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted text-uppercase fw-medium fs-11 mb-2">Data Diterima</p>
                            <h4 class="fs-22 fw-semibold mb-1">
                                <span class="counter-value" data-target="{{ $totalDiterima }}">0</span>
                            </h4>
                            <p class="text-muted fs-12 mb-0">Basis penagihan</p>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded-3 fs-20">
                                <i class="ri-checkbox-circle-line text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Paket Terpenuhi --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card card-animate border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted text-uppercase fw-medium fs-11 mb-2">Paket Terpenuhi</p>
                            <h4 class="fs-22 fw-semibold mb-1">
                                <span class="counter-value" data-target="{{ $kelipatan }}">0</span>
                                <small class="fs-13 text-muted fw-normal">x Paket</small>
                            </h4>
                            <p class="text-muted fs-12 mb-0">@ {{ $kelipatanPer }} data / Rp
                                {{ number_format($tarifPer15, 0, ',', '.') }}</p>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-info-subtle rounded-3 fs-20">
                                <i class="ri-stack-line text-info"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Penghasilan --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card card-animate border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted text-uppercase fw-medium fs-11 mb-2">Total Penghasilan</p>
                            <h4 class="fs-18 fw-semibold mb-1">
                                Rp <span class="counter-value" data-target="{{ $totalPenghasilan }}">0</span>
                            </h4>
                            <p class="mb-0">
                                <span class="badge bg-success-subtle text-success fs-11">
                                    <i class="ri-arrow-up-line me-1"></i>{{ $kelipatan }}x Paket
                                </span>
                            </p>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle rounded-3 fs-20">
                                <i class="ri-money-dollar-circle-line text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- ROW 2: Status Review Cards                                   --}}
    {{-- ============================================================ --}}
    <p class="text-uppercase fw-medium text-muted fs-11 letter-spacing-1 mb-2">Status Review</p>
    <div class="row g-3 mb-3">

        {{-- Pending --}}
        <div class="col-xl-4 col-sm-6">
            <div class="card card-animate border-0 shadow-sm h-100"
                style="border-radius: 12px; border-left: 3px solid var(--vz-warning) !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted text-uppercase fw-medium fs-11 mb-1">Menunggu Review</p>
                            <h4 class="fs-20 fw-semibold mb-1 text-warning">
                                {{ $totalPending }}
                                <small class="fs-13 text-muted fw-normal">data</small>
                            </h4>
                            <p class="text-muted fs-12 mb-0">Sedang diperiksa Verifikator</p>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-warning-subtle rounded-3 fs-20">
                                <i class="ri-time-line text-warning"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Revisi --}}
        <div class="col-xl-4 col-sm-6">
            <div class="card card-animate border-0 shadow-sm h-100"
                style="border-radius: 12px; border-left: 3px solid var(--vz-danger) !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted text-uppercase fw-medium fs-11 mb-1">Perlu Direvisi</p>
                            <h4 class="fs-20 fw-semibold mb-1 text-danger">
                                {{ $totalRevisi }}
                                <small class="fs-13 text-muted fw-normal">data</small>
                            </h4>
                            <p class="text-muted fs-12 mb-0">Mohon segera perbaiki</p>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-danger-subtle rounded-3 fs-20">
                                <i class="ri-edit-line text-danger"></i>
                            </span>
                        </div>
                    </div>
                    @if ($totalRevisi > 0)
                        <div class="mt-2">
                            <a href="{{ route('data-entry.progress.index') }}" class="btn btn-danger btn-sm w-100"
                                style="border-radius: 8px;">
                                <i class="ri-external-link-line me-1"></i> Lihat &amp; Perbaiki
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Ditolak --}}
        <div class="col-xl-4 col-sm-6">
            <div class="card card-animate border-0 shadow-sm h-100"
                style="border-radius: 12px; border-left: 3px solid var(--vz-secondary) !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted text-uppercase fw-medium fs-11 mb-1">Ditolak</p>
                            <h4 class="fs-20 fw-semibold mb-1">
                                {{ $totalDitolak }}
                                <small class="fs-13 text-muted fw-normal">data</small>
                            </h4>
                            <p class="text-muted fs-12 mb-0">Tidak dapat diproses lebih lanjut</p>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-light rounded-3 fs-20">
                                <i class="ri-close-circle-line text-secondary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- ROW 3: Progress Bar                                          --}}
    {{-- ============================================================ --}}
    <p class="text-uppercase fw-medium text-muted fs-11 letter-spacing-1 mb-2">Progress Paket Berikutnya</p>
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ri-bar-chart-2-line text-primary fs-18"></i>
                            <span class="fw-medium fs-14">Menuju Paket ke-{{ $kelipatan + 1 }}</span>
                            <span
                                class="badge {{ $dataEntry->entry_type === 'SIHALAL' ? 'bg-primary-subtle text-primary' : 'bg-info-subtle text-info' }} fs-11">
                                {{ $dataEntry->entry_type }}
                            </span>
                        </div>
                        <span class="badge bg-primary-subtle text-primary fs-12">
                            {{ $sisaData }} / {{ $kelipatanPer }} data diterima
                        </span>
                    </div>

                    <div class="progress mb-2" style="height: 8px; border-radius: 99px; background: var(--vz-light);">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar"
                            style="width: {{ $kelipatanPer > 0 ? ($sisaData / $kelipatanPer) * 100 : 0 }}%; border-radius: 99px;">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <p class="text-muted fs-12 mb-0">
                            Tambahkan <strong>{{ $kelipatanPer - $sisaData }}</strong> data lagi untuk mendapat
                            <strong class="text-success">Rp {{ number_format($tarifPer15, 0, ',', '.') }}</strong>
                            berikutnya
                        </p>
                        @if ($totalPending > 0)
                            <p class="text-muted fs-12 mb-0">
                                <i class="ri-information-line text-warning me-1"></i>
                                <strong>{{ $totalPending }}</strong> data menunggu review
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- ROW 4: Riwayat Penagihan                                     --}}
    {{-- ============================================================ --}}
    <p class="text-uppercase fw-medium text-muted fs-11 letter-spacing-1 mb-2">Riwayat Penagihan</p>
    <div class="row g-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div
                    class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2 py-3 px-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-receipt-line text-primary fs-18"></i>
                        <h5 class="card-title mb-0 fs-14 fw-semibold">Tagihan</h5>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="badge bg-warning-subtle text-warning fs-11">
                            <i class="ri-time-line me-1"></i>Pending:
                            {{ $penagihans->where('status', 'Menunggu')->count() }}
                        </span>
                        <span class="badge bg-info-subtle text-info fs-11">
                            <i class="ri-loader-2-line me-1"></i>Diproses:
                            {{ $penagihans->where('status', 'Diproses')->count() }}
                        </span>
                        <span class="badge bg-success-subtle text-success fs-11">
                            <i class="ri-check-line me-1"></i>Dibayar:
                            {{ $penagihans->where('status', 'Dibayar')->count() }}
                        </span>
                        <span class="badge bg-danger-subtle text-danger fs-11">
                            <i class="ri-close-line me-1"></i>Ditolak:
                            {{ $penagihans->where('status', 'Ditolak')->count() }}
                        </span>
                    </div>
                </div>

                <div class="card-body p-3">
                    @if ($penagihans->isEmpty())
                        <div class="text-center py-5">
                            <div class="avatar-md mx-auto mb-3">
                                <span class="avatar-title bg-light rounded-circle fs-1">
                                    <i class="ri-receipt-line text-muted"></i>
                                </span>
                            </div>
                            <p class="text-muted fs-14 mb-0">
                                Belum ada tagihan. Kumpulkan <strong>{{ $kelipatanPer }}</strong> data yang diterima untuk
                                membuat tagihan pertama!
                            </p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-nowrap align-middle mb-0 fs-13">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-muted fw-medium" style="font-size:11px;">#</th>
                                        <th class="text-muted fw-medium" style="font-size:11px;">Tanggal Tagihan</th>
                                        <th class="text-muted fw-medium" style="font-size:11px;">Jumlah Data</th>
                                        <th class="text-muted fw-medium" style="font-size:11px;">Jumlah Paket</th>
                                        <th class="text-muted fw-medium" style="font-size:11px;">Nominal</th>
                                        <th class="text-muted fw-medium" style="font-size:11px;">Status</th>
                                        <th class="text-muted fw-medium" style="font-size:11px;">Tanggal Dibayar</th>
                                        <th class="text-muted fw-medium" style="font-size:11px;">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($penagihans as $index => $penagihan)
                                        <tr>
                                            <td class="text-muted">{{ $index + 1 }}</td>
                                            <td>
                                                <i class="ri-calendar-line text-muted me-1"></i>
                                                {{ $penagihan->tanggal_tagihan->format('d M Y') }}
                                            </td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary">
                                                    {{ $penagihan->jumlah_data }} Data
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info">
                                                    {{ $penagihan->jumlah_paket }}x Paket
                                                </span>
                                            </td>
                                            <td>
                                                <strong class="text-success">
                                                    Rp {{ number_format($penagihan->nominal, 0, ',', '.') }}
                                                </strong>
                                            </td>
                                            <td>
                                                @switch($penagihan->status)
                                                    @case('Menunggu')
                                                        <span class="badge bg-warning-subtle text-warning">
                                                            <i class="ri-time-line me-1"></i>Pending
                                                        </span>
                                                    @break

                                                    @case('Diproses')
                                                        <span class="badge bg-info-subtle text-info">
                                                            <i class="ri-loader-2-line me-1"></i>Diproses
                                                        </span>
                                                    @break

                                                    @case('Dibayar')
                                                        <span class="badge bg-success-subtle text-success">
                                                            <i class="ri-check-circle-line me-1"></i>Dibayar
                                                        </span>
                                                    @break

                                                    @case('Ditolak')
                                                        <span class="badge bg-danger-subtle text-danger">
                                                            <i class="ri-close-circle-line me-1"></i>Ditolak
                                                        </span>
                                                    @break
                                                @endswitch
                                            </td>
                                            <td>
                                                @if ($penagihan->tanggal_dibayar)
                                                    <i class="ri-calendar-check-line text-success me-1"></i>
                                                    {{ $penagihan->tanggal_dibayar->format('d M Y') }}
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($penagihan->catatan)
                                                    <span data-bs-toggle="tooltip" title="{{ $penagihan->catatan }}">
                                                        <i class="ri-information-line text-info fs-16"></i>
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <div class="px-3 py-2 bg-success-subtle rounded-2">
                                <p class="mb-0 text-success fw-semibold fs-13">
                                    <i class="ri-check-circle-line me-1"></i>
                                    Total Sudah Dibayar:
                                    <strong>
                                        Rp
                                        {{ number_format($penagihans->where('status', 'Dibayar')->sum('nominal'), 0, ',', '.') }}
                                    </strong>
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- Modal Pengumuman --}}
    @if ($showPengumuman && $pengumuman)
        <div class="modal fade" id="modalPengumuman" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content border-0"
                    style="border-radius: 20px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">

                    {{-- Accent Bar Top --}}
                    <div
                        style="height: 4px; background: {{ $pengumuman->jenis === 'SIHALAL' ? 'var(--vz-primary)' : 'var(--vz-info)' }};">
                    </div>

                    {{-- Body --}}
                    <div class="modal-body p-0">

                        {{-- Header Section --}}
                        <div class="px-4 pt-4 pb-3">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span
                                    class="badge {{ $pengumuman->jenis === 'SIHALAL' ? 'bg-primary-subtle text-primary' : 'bg-info-subtle text-info' }}"
                                    style="font-size: 11px; padding: 5px 10px; border-radius: 6px; font-weight: 500; letter-spacing: 0.3px;">
                                    <i class="ri-megaphone-line me-1"></i>Pengumuman {{ $pengumuman->jenis }}
                                </span>
                                <span class="text-muted" style="font-size: 11px;">No. {{ $pengumuman->nomor }}</span>
                            </div>

                            <h5 class="fw-semibold mb-1"
                                style="font-size: 15px; line-height: 1.4; color: var(--vz-heading-color);">
                                {{ $pengumuman->judul }}
                            </h5>
                        </div>

                        {{-- Divider --}}
                        <hr class="my-0 mx-4" style="border-color: var(--vz-border-color); opacity: 0.6;">

                        {{-- Deskripsi --}}
                        @if ($pengumuman->deskripsi)
                            <div class="px-4 py-3">
                                <p class="text-muted mb-2"
                                    style="font-size: 11px; font-weight: 500; letter-spacing: .05em; text-transform: uppercase;">
                                    <i class="la la-align-left me-1"></i>Deskripsi
                                </p>
                                <p class="mb-0"
                                    style="font-size: 13.5px; line-height: 1.75; color: var(--vz-body-color);">
                                    {!! $pengumuman->deskripsi !!}
                                </p>
                            </div>
                        @endif

                        {{-- PDF Attachment --}}
                        @if ($pengumuman->foto)
                            <div class="px-4 pb-3">
                                <a href="{{ asset('storage/' . $pengumuman->foto) }}" target="_blank"
                                    class="d-flex align-items-center gap-3 p-3 text-decoration-none"
                                    style="background: var(--vz-danger-bg-subtle); border: 1px solid var(--vz-danger-border-subtle); border-radius: 12px; transition: opacity .2s;"
                                    onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
                                    <div
                                        style="width: 38px; height: 38px; background: var(--vz-danger); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="ri-file-pdf-2-line text-white fs-18"></i>
                                    </div>
                                    <div class="flex-grow-1" style="min-width: 0;">
                                        <p class="mb-0 fw-medium text-danger" style="font-size: 13px;">Lihat Lampiran PDF
                                        </p>
                                        <p class="mb-0 text-muted" style="font-size: 11px;">Klik untuk membuka di tab baru
                                        </p>
                                    </div>
                                    <i class="ri-external-link-line text-danger fs-16"></i>
                                </a>
                            </div>
                        @endif

                    </div>

                    {{-- Footer --}}
                    <div class="px-4 pb-4 pt-2">
                        <button type="button" id="btnSudahDibaca"
                            class="btn w-100 {{ $pengumuman->jenis === 'SIHALAL' ? 'btn-primary' : 'btn-info' }}"
                            style="border-radius: 12px; padding: 11px; font-size: 14px; font-weight: 500;"
                            data-pengumuman-id="{{ $pengumuman->id }}">
                            <i class="ri-check-double-line me-1"></i> Sudah Dibaca
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif
    <style>
        /* ---- Hero Banner ---- */
        .db-hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            padding: 1.5rem 1.75rem;
            border-radius: 16px;
            background: linear-gradient(135deg, #1e3a5f 0%, #2a4a7f 50%, #1a3560 100%);
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .db-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }

        .db-hero__left {
            flex: 1;
            min-width: 0;
        }

        .db-hero__greeting {
            font-size: 12px;
            font-weight: 400;
            opacity: .7;
            letter-spacing: .04em;
            margin-bottom: 2px;
        }

        .db-hero__name {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -.3px;
            margin-bottom: 12px;
            line-height: 1.2;
        }

        .db-hero__meta {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .db-hero__sep {
            width: 1px;
            height: 14px;
            background: rgba(255, 255, 255, .25);
        }

        .db-hero__tarif-label {
            font-size: 12px;
            opacity: .65;
        }

        .db-hero__tarif-value {
            font-size: 14px;
            font-weight: 700;
        }

        .db-hero__tarif-unit {
            font-size: 12px;
            opacity: .65;
        }

        .db-hero__right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-shrink: 0;
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 12px;
            padding: 1rem 1.5rem;
        }

        .db-hero__stat-divider {
            width: 1px;
            height: 36px;
            background: rgba(255, 255, 255, .2);
        }

        .db-hero__stat-num {
            font-size: 18px;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 4px;
        }

        .db-hero__stat-lbl {
            font-size: 11px;
            opacity: .65;
        }

        /* ---- Type Pills ---- */
        .db-type-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .05em;
            padding: 4px 10px;
            border-radius: 999px;
        }

        .db-type-pill--oss {
            background: rgba(13, 202, 240, .15);
            color: #0dcaf0;
            border: 1px solid rgba(13, 202, 240, .3);
        }

        .db-type-pill--sihalal {
            background: rgba(99, 102, 241, .2);
            color: #818cf8;
            border: 1px solid rgba(99, 102, 241, .3);
        }

        .db-type-pill__dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: currentColor;
        }

        /* ---- Alert ---- */
        .db-alert {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 13px 16px;
            border-radius: 12px;
            font-size: 13px;
        }

        .db-alert--warning {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-left: 4px solid #f59e0b;
            color: #92400e;
        }

        .db-alert__icon {
            font-size: 18px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .db-alert__body {
            flex: 1;
            line-height: 1.5;
        }

        .db-alert__link {
            margin-left: 8px;
            font-weight: 600;
            color: inherit;
            text-decoration: underline;
        }

        .db-alert__close {
            background: none;
            border: none;
            cursor: pointer;
            color: inherit;
            opacity: .6;
            font-size: 16px;
            padding: 0;
            line-height: 1;
        }

        .db-alert__close:hover {
            opacity: 1;
        }

        /* ---- Responsive ---- */
        @media (max-width: 768px) {
            .db-hero {
                flex-direction: column;
                align-items: flex-start;
            }

            .db-hero__right {
                width: 100%;
            }
        }
    </style>
    @if ($showPengumuman && $pengumuman)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = new bootstrap.Modal(document.getElementById('modalPengumuman'), {
                    backdrop: 'static',
                    keyboard: false
                });
                modal.show();

                document.getElementById('btnSudahDibaca').addEventListener('click', function() {
                    const pengumumanId = this.dataset.pengumumanId;

                    fetch('{{ route('data-entry.markPengumumanRead') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                pengumuman_id: pengumumanId
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                modal.hide();
                            }
                        })
                        .catch(() => {
                            // Tetap tutup modal meski request gagal
                            modal.hide();
                        });
                });
            });
        </script>
    @endif
@endsection
