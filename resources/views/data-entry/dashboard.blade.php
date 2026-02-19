@extends('layouts.app')
@section('template_title')
    Dashboard
@endsection
@section('content')
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="ri-admin-line"></i> <strong>Selamat datang, Data Entry!</strong>
        Semoga Hari Kalian Selalu "BEJO".
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <!-- Total Data Dientry -->
        <div class="col-xl-3" data-aos="fade-up">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Data Dientry</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $totalDientry }}">0</span>
                            </h4>
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

        <!-- Paket Terpenuhi -->
        <div class="col-xl-3" data-aos="fade-up">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Paket Terpenuhi</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
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

        <!-- Sisa Data -->
        <div class="col-xl-3" data-aos="fade-up">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Sisa Data</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $sisaData }}">0</span>
                                <small class="fs-13 text-muted">/ {{ $kelipatanPer }}</small>
                            </h4>
                            <p class="text-muted mb-0 fs-12">
                                Butuh <strong>{{ $kelipatanPer - $sisaData }}</strong> data lagi
                            </p>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-warning-subtle rounded fs-3">
                                <i class="bx bx-time-five text-warning"></i>
                            </span>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 6px;">
                        <div class="progress-bar bg-warning" style="width: {{ ($sisaData / $kelipatanPer) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Penghasilan -->
        <div class="col-xl-3" data-aos="fade-up">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Penghasilan</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
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

    <!-- Progress Bar Menuju Paket Berikutnya -->
    <div class="row mt-2">
        <div class="col-12" data-aos="fade-up">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-semibold mb-0">
                            <i class="bx bx-bar-chart-alt-2 text-primary me-1"></i>
                            Progress Menuju Paket Berikutnya
                        </h6>
                        <span class="badge bg-primary-subtle text-primary fs-12">
                            {{ $sisaData }} / {{ $kelipatanPer }} Data
                        </span>
                    </div>
                    <div class="progress" style="height: 10px; border-radius: 8px;">
                        <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated"
                            style="width: {{ ($sisaData / $kelipatanPer) * 100 }}%; border-radius: 8px;"></div>
                    </div>
                    <p class="text-muted mt-2 mb-0 fs-12">
                        Tambahkan <strong>{{ $kelipatanPer - $sisaData }}</strong> data lagi untuk mendapatkan
                        <strong class="text-success">Rp {{ number_format($tarifPer15, 0, ',', '.') }}</strong> berikutnya
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Penagihan -->
    <div class="row mt-2">
        <div class="col-12" data-aos="fade-up">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-receipt text-primary me-1"></i> Riwayat Penagihan
                    </h5>
                    <!-- Ringkasan Status -->
                    <div class="d-flex gap-2">
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
                            <p class="text-muted mb-0">Belum ada tagihan. Kumpulkan 15 data untuk membuat tagihan pertama!
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

                        <!-- Total Sudah Dibayar -->
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
