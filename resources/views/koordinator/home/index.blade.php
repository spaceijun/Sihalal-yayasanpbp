@extends('layouts.app')
@section('content')
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="ri-admin-line"></i> <strong>Selamat datang, Koordinator!</strong>
        Semoga Hari Kalian Selalu "BEJO".
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <!-- Data Masuk -->
        <div class="col-xl-3">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Data Masuk</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $dataMasuk }}">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                <i class="bx bx-trending-up text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Pending -->
        <div class="col-xl-3">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Data Pending</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $pending }}">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-warning-subtle rounded fs-3">
                                <i class="bx bx-time text-warning"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Proses -->
        <div class="col-xl-3">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Data Proses</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $progress }}">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-info-subtle rounded fs-3">
                                <i class="bx bx-loader-circle text-info"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Terbit -->
        <div class="col-xl-3">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Data Terbit</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $terbitSH }}">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle rounded fs-3">
                                <i class="bx bx-check-circle text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel 20 Data Masuk Terbaru -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">20 Data Masuk Terbaru</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Input</th>
                                    <th>Nama Pendamping</th>
                                    <th>Nama PU</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dataLapangan as $index => $data)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $data->enumerator->nama_lengkap }}</td>
                                        <td>{{ $data->nama_pu }}</td>
                                        <td>{{ $data->status }}</td>
                                        <td>
                                            @if ($data->status == 'PENDING')
                                                <span class="badge bg-warning">{{ $data->status }}</span>
                                            @elseif(in_array($data->status, ['PROGRESS OSS', 'PROGRESS SIHALAL']))
                                                <span class="badge bg-info">{{ $data->status }}</span>
                                            @elseif($data->status == 'TERBIT SH')
                                                <span class="badge bg-success">{{ $data->status }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $data->status }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $data->created_at->format('d M Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="bx bx-info-circle fs-3"></i>
                                            <p class="mb-0 mt-2">Belum ada data masuk</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
