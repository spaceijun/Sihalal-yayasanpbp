@extends('layouts.app')

@section('template_title')
    Data Lapangan - {{ $enumerator->nama_lengkap }}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="card card-animate overflow-hidden">
                    <div class="position-absolute top-0 start-0 w-100 h-100"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); opacity: 0.9;"></div>
                    <div class="card-body position-relative" style="z-index: 1;">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-semibold text-white mb-0">Terbit SH</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4 text-white">
                                    <span class="counter-value" data-target="{{ $totalTerbitSh }}">0</span>
                                </h4>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-white bg-opacity-25 rounded fs-3">
                                    <i class="bx bx-check-double text-white"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="card card-animate overflow-hidden">
                    <div class="position-absolute top-0 start-0 w-100 h-100"
                        style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); opacity: 0.9;"></div>
                    <div class="card-body position-relative" style="z-index: 1;">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-semibold text-white mb-0">Dibayar</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4 text-white">
                                    <span class="counter-value" data-target="{{ $totalDibayar }}">0</span>
                                </h4>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-white bg-opacity-25 rounded fs-3">
                                    <i class="bx bx-wallet text-white"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                @include('layouts.messages')

                {{-- Tabel Data Lapangan --}}
                <div class="card">
                    <div class="card-header">
                        <span id="card_title">Perolehan Data Lapangan - {{ $enumerator->nama_lengkap }}</span>
                    </div>
                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama PU</th>
                                        <th>NIK</th>
                                        <th>Status</th>
                                        <th>Status Pembayaran</th>
                                        <th>Tanggal Input</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($dataLapangan as $data)
                                        <tr>
                                            <td>{{ $dataLapangan->firstItem() + $loop->index }}</td>
                                            <td>{{ $data->nama_pu }}</td>
                                            <td>{{ $data->nik }}</td>
                                            <td>
                                                @if ($data->status == 'PENDING')
                                                    <span class="badge bg-warning">{{ $data->status }}</span>
                                                @elseif($data->status == 'PROGRESS OSS')
                                                    <span class="badge bg-info">{{ $data->status }}</span>
                                                @elseif($data->status == 'PROGRESS SIHALAL')
                                                    <span class="badge bg-primary">{{ $data->status }}</span>
                                                @elseif($data->status == 'TERBIT SH')
                                                    <span class="badge bg-success">{{ $data->status }}</span>
                                                @elseif($data->status == 'DITOLAK')
                                                    <span class="badge bg-dark">{{ $data->status }}</span>
                                                @elseif($data->status == 'REVISI')
                                                    <span class="badge bg-danger">{{ $data->status }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($data->status_pembayaran === 'DIBAYAR')
                                                    <span class="badge bg-success">{{ $data->status_pembayaran }}</span>
                                                @elseif ($data->status_pembayaran === 'PENDING')
                                                    <span class="badge bg-warning">{{ $data->status_pembayaran }}</span>
                                                @elseif ($data->status_pembayaran === 'PENGAJUAN')
                                                    <span class="badge bg-info">{{ $data->status_pembayaran }}</span>
                                                @else
                                                    <span
                                                        class="badge badge-secondary">{{ $data->status_pembayaran ?? 'Belum Diproses' }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $data->created_at->format('d M Y') }}</td>
                                            {{-- <td>
                                                <a href="{{ route('koordinator.data-lapangan.show', $data->id) }}"
                                                    class="btn btn-sm btn-success">
                                                    <i class="las la-eye"></i> Detail
                                                </a>
                                            </td> --}}
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="las la-inbox la-3x mb-2"></i>
                                                    <p class="mb-0">Tidak ada data lapangan</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @include('layouts.pagination', ['paginator' => $dataLapangan])
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
