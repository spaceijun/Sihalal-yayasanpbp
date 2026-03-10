@extends('layouts.app')

@section('template_title')
    Dashboard
@endsection

@section('content')
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="ri-admin-line"></i>
        <strong>Selamat datang, {{ $dataEntry->user->name }}!</strong>
        Entry Type:
        <span class="badge {{ $dataEntry->entry_type === 'SIHALAL' ? 'bg-primary' : 'bg-info' }} ms-1">
            {{ $dataEntry->entry_type }}
        </span>
        — Tarif: <strong>Rp {{ number_format($tarifPer15, 0, ',', '.') }}</strong> per {{ $kelipatanPer }} data.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    {{-- ============================================================ --}}
    {{-- ROW 1: Summary Cards                                         --}}
    {{-- ============================================================ --}}
    <div class="row">

        {{-- Total Dientry --}}
        <div class="col-xl-3 col-md-6" data-aos="fade-up">
            <div class="card card-animate">
                <div class="card-body">
                    <p class="text-uppercase fw-medium text-muted mb-0">Total Dientry</p>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-1">
                                <span class="counter-value" data-target="{{ $totalDientry }}">0</span>
                            </h4>
                            <p class="text-muted mb-0 fs-12">Semua data yang pernah disubmit</p>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                <i class="bx bx-data text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Data Diterima --}}
        <div class="col-xl-3 col-md-6" data-aos="fade-up">
            <div class="card card-animate">
                <div class="card-body">
                    <p class="text-uppercase fw-medium text-muted mb-0">Data Diterima</p>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-1">
                                <span class="counter-value" data-target="{{ $totalDiterima }}">0</span>
                            </h4>
                            <p class="text-muted mb-0 fs-12">Disetujui superadmin — basis penagihan</p>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                <i class="bx bx-check-circle text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Paket Terpenuhi --}}
        <div class="col-xl-3 col-md-6" data-aos="fade-up">
            <div class="card card-animate">
                <div class="card-body">
                    <p class="text-uppercase fw-medium text-muted mb-0">Paket Terpenuhi</p>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-1">
                                <span class="counter-value" data-target="{{ $kelipatan }}">0</span>
                                <small class="fs-13 text-muted">x Paket</small>
                            </h4>
                            <p class="text-muted mb-0 fs-12">
                                @ {{ $kelipatanPer }} data / Rp {{ number_format($tarifPer15, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-info-subtle rounded fs-3">
                                <i class="bx bx-package text-info"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Penghasilan --}}
        <div class="col-xl-3 col-md-6" data-aos="fade-up">
            <div class="card card-animate">
                <div class="card-body">
                    <p class="text-uppercase fw-medium text-muted mb-0">Total Penghasilan</p>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-1">
                                Rp <span class="counter-value" data-target="{{ $totalPenghasilan }}">0</span>
                            </h4>
                            <p class="text-muted mb-0 fs-12">
                                <span class="badge bg-success-subtle text-success">
                                    <i class="bx bx-trending-up"></i> {{ $kelipatan }}x Paket
                                </span>
                            </p>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle rounded fs-3">
                                <i class="bx bx-money text-primary"></i>
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
    <div class="row mt-2">

        {{-- Pending --}}
        <div class="col-xl-4 col-md-6" data-aos="fade-up">
            <div class="card card-animate border-start border-warning border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-uppercase fw-medium text-muted mb-1 fs-12">Menunggu Review</p>
                            <h4 class="fs-20 fw-semibold mb-0 text-warning">
                                {{ $totalPending }}
                                <small class="fs-13 text-muted fw-normal">data</small>
                            </h4>
                            <p class="text-muted mb-0 fs-12 mt-1">Sedang diperiksa superadmin</p>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-warning-subtle rounded fs-3">
                                <i class="bx bx-hourglass text-warning"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Revisi --}}
        <div class="col-xl-4 col-md-6" data-aos="fade-up">
            <div class="card card-animate border-start border-danger border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-uppercase fw-medium text-muted mb-1 fs-12">Perlu Direvisi</p>
                            <h4 class="fs-20 fw-semibold mb-0 text-danger">
                                {{ $totalRevisi }}
                                <small class="fs-13 text-muted fw-normal">data</small>
                            </h4>
                            <p class="text-muted mb-0 fs-12 mt-1">Mohon segera perbaiki</p>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-danger-subtle rounded fs-3">
                                <i class="bx bx-edit text-danger"></i>
                            </span>
                        </div>
                    </div>
                    @if ($totalRevisi > 0)
                        <div class="mt-2">
                            <a href="{{ route('data-entry.progress.index') }}" class="btn btn-danger btn-sm w-100">
                                <i class="bx bx-link-external me-1"></i>Lihat & Perbaiki
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Ditolak --}}
        <div class="col-xl-4 col-md-6" data-aos="fade-up">
            <div class="card card-animate border-start border-dark border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-uppercase fw-medium text-muted mb-1 fs-12">Ditolak</p>
                            <h4 class="fs-20 fw-semibold mb-0">
                                {{ $totalDitolak }}
                                <small class="fs-13 text-muted fw-normal">data</small>
                            </h4>
                            <p class="text-muted mb-0 fs-12 mt-1">Tidak dapat diproses lebih lanjut</p>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-light rounded fs-3">
                                <i class="bx bx-x-circle text-dark"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- ROW 3: Progress Bar Menuju Paket Berikutnya                  --}}
    {{-- ============================================================ --}}
    <div class="row mt-2">
        <div class="col-12" data-aos="fade-up">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-semibold mb-0">
                            <i class="bx bx-bar-chart-alt-2 text-primary me-1"></i>
                            Progress Menuju Paket Berikutnya
                            <span
                                class="badge {{ $dataEntry->entry_type === 'SIHALAL' ? 'bg-primary' : 'bg-info' }} ms-1 fs-11">
                                {{ $dataEntry->entry_type }}
                            </span>
                        </h6>
                        <span class="badge bg-primary-subtle text-primary fs-12">
                            {{ $sisaData }} / {{ $kelipatanPer }} Data Diterima
                        </span>
                    </div>
                    <div class="progress" style="height: 10px; border-radius: 8px;">
                        <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated"
                            style="width: {{ $kelipatanPer > 0 ? ($sisaData / $kelipatanPer) * 100 : 0 }}%; border-radius: 8px;">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <p class="text-muted mb-0 fs-12">
                            Tambahkan <strong>{{ $kelipatanPer - $sisaData }}</strong> data diterima lagi untuk
                            mendapatkan
                            <strong class="text-success">Rp {{ number_format($tarifPer15, 0, ',', '.') }}</strong>
                            berikutnya
                        </p>
                        @if ($totalPending > 0)
                            <p class="text-muted mb-0 fs-12">
                                <i class="bx bx-info-circle text-warning me-1"></i>
                                <strong>{{ $totalPending }}</strong> data masih menunggu review superadmin
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
    <div class="row mt-2">
        <div class="col-12" data-aos="fade-up">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-receipt text-primary me-1"></i> Riwayat Penagihan
                    </h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="badge bg-warning fs-12">
                            <i class="bx bx-time"></i>
                            Pending: {{ $penagihans->where('status', 'Menunggu')->count() }}
                        </span>
                        <span class="badge bg-info fs-12">
                            <i class="bx bx-loader"></i>
                            Diproses: {{ $penagihans->where('status', 'Diproses')->count() }}
                        </span>
                        <span class="badge bg-success fs-12">
                            <i class="bx bx-check"></i>
                            Dibayar: {{ $penagihans->where('status', 'Dibayar')->count() }}
                        </span>
                        <span class="badge bg-danger fs-12">
                            <i class="bx bx-x"></i>
                            Ditolak: {{ $penagihans->where('status', 'Ditolak')->count() }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    @if ($penagihans->isEmpty())
                        <div class="text-center py-4">
                            <div class="avatar-md mx-auto mb-3">
                                <span class="avatar-title bg-light rounded-circle fs-1">
                                    <i class="bx bx-receipt text-muted"></i>
                                </span>
                            </div>
                            <p class="text-muted mb-0">
                                Belum ada tagihan. Kumpulkan {{ $kelipatanPer }} data yang diterima untuk membuat tagihan
                                pertama!
                            </p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-nowrap align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Tanggal Tagihan</th>
                                        <th>Jumlah Data</th>
                                        <th>Jumlah Paket</th>
                                        <th>Nominal</th>
                                        <th>Status</th>
                                        <th>Tanggal Dibayar</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($penagihans as $index => $penagihan)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <i class="bx bx-calendar text-muted me-1"></i>
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
                                                        <span class="badge bg-warning">
                                                            <i class="bx bx-time me-1"></i> Pending
                                                        </span>
                                                    @break

                                                    @case('Diproses')
                                                        <span class="badge bg-info">
                                                            <i class="bx bx-loader-alt me-1"></i> Diproses
                                                        </span>
                                                    @break

                                                    @case('Dibayar')
                                                        <span class="badge bg-success">
                                                            <i class="bx bx-check-circle me-1"></i> Dibayar
                                                        </span>
                                                    @break

                                                    @case('Ditolak')
                                                        <span class="badge bg-danger">
                                                            <i class="bx bx-x-circle me-1"></i> Ditolak
                                                        </span>
                                                    @break
                                                @endswitch
                                            </td>
                                            <td>
                                                @if ($penagihan->tanggal_dibayar)
                                                    <i class="bx bx-calendar-check text-success me-1"></i>
                                                    {{ $penagihan->tanggal_dibayar->format('d M Y') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($penagihan->catatan)
                                                    <span data-bs-toggle="tooltip" title="{{ $penagihan->catatan }}">
                                                        <i class="bx bx-info-circle text-info fs-5"></i>
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <div class="p-3 bg-success-subtle rounded">
                                <p class="mb-0 text-success fw-semibold">
                                    <i class="bx bx-check-circle me-1"></i>
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
@endsection
