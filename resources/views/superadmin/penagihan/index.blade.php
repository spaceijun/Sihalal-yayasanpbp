@extends('layouts.app')

@section('template_title')
    Manajemen Penagihan Data Entry
@endsection

@section('content')

    {{-- Alert --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bx bx-error me-1"></i> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bx bx-info-circle me-1"></i> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="row">
        <div class="col-xl-3" data-aos="fade-up">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Pending</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $totalMenunggu }}">0</span>
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
        <div class="col-xl-3" data-aos="fade-up">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Sedang Diproses</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $totalDiproses }}">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-info-subtle rounded fs-3">
                                <i class="bx bx-loader-alt text-info"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3" data-aos="fade-up">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Ditolak</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $totalDitolak }}">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-danger-subtle rounded fs-3">
                                <i class="bx bx-x-circle text-danger"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3" data-aos="fade-up">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Sudah Dibayar</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                Rp <span class="counter-value" data-target="{{ $totalDibayar }}">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                <i class="bx bx-money text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Penagihan --}}
    <div class="row mt-2">
        <div class="col-12" data-aos="fade-up">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-receipt text-primary me-1"></i> Daftar Tagihan Data Entry
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-nowrap align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nama Data Entry</th>
                                    <th>Tanggal Tagihan</th>
                                    <th>Jumlah Data</th>
                                    <th>Jumlah Paket</th>
                                    <th>Nominal</th>
                                    <th>Status</th>
                                    <th>Tanggal Dibayar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($penagihans as $index => $penagihan)
                                    <tr>
                                        <td>{{ $penagihans->firstItem() + $index }}</td>
                                        <td>
                                            <p class="mb-0 fw-medium">{{ $penagihan->dataEntry->nama_lengkap }}</p>
                                            <small class="text-muted">{{ $penagihan->dataEntry->email }}</small>
                                        </td>
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
                                                    <span class="badge bg-warning-subtle text-warning">
                                                        <i class="bx bx-time me-1"></i> Menunggu
                                                    </span>
                                                @break

                                                @case('Diproses')
                                                    <span class="badge bg-info-subtle text-info">
                                                        <i class="bx bx-loader-alt me-1"></i> Diproses
                                                    </span>
                                                @break

                                                @case('Dibayar')
                                                    <span class="badge bg-success-subtle text-success">
                                                        <i class="bx bx-check-circle me-1"></i> Dibayar
                                                    </span>
                                                @break

                                                @case('Ditolak')
                                                    <span class="badge bg-danger-subtle text-danger">
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
                                            @if (in_array($penagihan->status, ['Menunggu', 'Diproses']))
                                                <div class="d-flex gap-1">
                                                    {{-- Tombol Approve --}}
                                                    <button type="button" class="btn btn-sm btn-success"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalApprove{{ $penagihan->id }}">
                                                        <i class="bx bx-check"></i> Approve
                                                    </button>

                                                    {{-- Tombol Tolak --}}
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalTolak{{ $penagihan->id }}">
                                                        <i class="bx bx-x"></i> Tolak
                                                    </button>
                                                </div>
                                            @else
                                                @if ($penagihan->catatan)
                                                    <span data-bs-toggle="tooltip" title="{{ $penagihan->catatan }}">
                                                        <i class="bx bx-info-circle text-info fs-5"></i>
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <div class="avatar-md mx-auto mb-3">
                                                    <span class="avatar-title bg-light rounded-circle fs-1">
                                                        <i class="bx bx-receipt text-muted"></i>
                                                    </span>
                                                </div>
                                                <p class="text-muted mb-0">Belum ada tagihan masuk.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-end mt-3">
                            @include('layouts.pagination', ['paginator' => $penagihans])
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODALS --}}
        @foreach ($penagihans as $penagihan)
            @if (in_array($penagihan->status, ['Menunggu', 'Diproses']))
                {{-- Modal Approve --}}
                <div class="modal fade" id="modalApprove{{ $penagihan->id }}" tabindex="-1"
                    aria-labelledby="modalApproveLabel{{ $penagihan->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form action="{{ route('superadmin.penagihan.approve', $penagihan) }}" method="POST">
                                @csrf
                                <div class="modal-header border-bottom-0 pb-0">
                                    <h5 class="modal-title" id="modalApproveLabel{{ $penagihan->id }}">
                                        <i class="bx bx-check-circle text-success me-1"></i> Approve Tagihan
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-success border-0 bg-success-subtle">
                                        <p class="mb-1">
                                            <strong>Data Entry:</strong> {{ $penagihan->dataEntry->nama_lengkap }}
                                        </p>
                                        <p class="mb-1">
                                            <strong>Jumlah Data:</strong>
                                            {{ $penagihan->jumlah_data }} data ({{ $penagihan->jumlah_paket }} paket)
                                        </p>
                                        <p class="mb-0">
                                            <strong>Nominal:</strong>
                                            Rp {{ number_format($penagihan->nominal, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Catatan <span class="text-muted">(opsional)</span></label>
                                        <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-top-0 pt-0">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bx bx-check me-1"></i> Konfirmasi Pembayaran
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Modal Tolak --}}
                <div class="modal fade" id="modalTolak{{ $penagihan->id }}" tabindex="-1"
                    aria-labelledby="modalTolakLabel{{ $penagihan->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form action="{{ route('superadmin.penagihan.tolak', $penagihan) }}" method="POST">
                                @csrf
                                <div class="modal-header border-bottom-0 pb-0">
                                    <h5 class="modal-title" id="modalTolakLabel{{ $penagihan->id }}">
                                        <i class="bx bx-x-circle text-danger me-1"></i> Tolak Tagihan
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-danger border-0 bg-danger-subtle">
                                        <p class="mb-1">
                                            <strong>Data Entry:</strong> {{ $penagihan->dataEntry->nama_lengkap }}
                                        </p>
                                        <p class="mb-0">
                                            <strong>Nominal:</strong>
                                            Rp {{ number_format($penagihan->nominal, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Alasan Penolakan <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="catatan" class="form-control" rows="3" placeholder="Masukkan alasan penolakan..." required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-top-0 pt-0">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bx bx-x me-1"></i> Tolak Tagihan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

    @endsection
