@extends('layouts.app')
@section('content')
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="ri-admin-line"></i> <strong>Selamat datang, Superadmin!</strong>
        Semoga Hari Kalian Selalu "BEJO".
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <!-- Summary Cards -->
    <div class="row">
        <div class="col-xl-4">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Tim</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $totalDataKoordinator }}">0</span>
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
        <div class="col-xl-4">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Pendamping</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $totalDataEnumerator }}">0</span>
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
        <div class="col-xl-4">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Data Masuk</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $totalDataLapangan }}">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-danger-subtle rounded fs-3">
                                <i class="bx bx-trending-down text-danger"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 20 Data Terakhir -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">20 Data Terakhir Masuk</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Pendamping</th>
                                    <th>Nama PU</th>
                                    <th>Status</th>
                                    <th>Tanggal Input</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestData as $index => $data)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $data->enumerator->nama_lengkap ?? '-' }}</td>
                                        <td>{{ $data->nama_pu }}</td>
                                        <td>
                                            @if ($data->status == 'PENDING')
                                                <span class="badge bg-warning">{{ $data->status }}</span>
                                            @elseif($data->status == 'DITOLAK')
                                                <span class="badge bg-danger">{{ $data->status }}</span>
                                            @elseif($data->status == 'PROGRESS OSS')
                                                <span class="badge bg-info">{{ $data->status }}</span>
                                            @elseif($data->status == 'PROGRESS SIHALAL')
                                                <span class="badge bg-primary">{{ $data->status }}</span>
                                            @elseif($data->status == 'TERBIT SH')
                                                <span class="badge bg-success">{{ $data->status }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $data->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada data</td>
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
