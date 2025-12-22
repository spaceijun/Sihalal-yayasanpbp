@extends('layouts.app')

@section('template_title')
    Data Lapangans
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="card-title mb-0">Laporan Harian Data Lapangan</h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="d-flex justify-content-end gap-2 align-items-center">
                                <!-- Filter Koordinator -->
                                <select class="form-select" id="filterKoordinator" style="max-width: 250px;">
                                    <option value="">Semua Koordinator</option>
                                    @foreach ($koordinators as $koordinator)
                                        <option value="{{ $koordinator->id }}"
                                            {{ $koordinatorId == $koordinator->id ? 'selected' : '' }}>
                                            {{ $koordinator->nama_lengkap }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Filter Tanggal -->
                                <input type="date" class="form-control" id="filterTanggal" value="{{ $tanggal }}"
                                    style="max-width: 200px;">

                                {{-- <!-- Export Excel -->
                                <button class="btn btn-success" onclick="exportLaporan()">
                                    <i class="ri-file-excel-2-line align-middle"></i> Export Excel
                                </button> --}}

                                {{-- <!-- Print -->
                                <button class="btn btn-danger" onclick="printLaporan()">
                                    <i class="ri-printer-line align-middle"></i> Print
                                </button> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($laporanPerKoordinator->isEmpty())
                        <div class="alert alert-info text-center" role="alert">
                            <i class="ri-information-line fs-4"></i>
                            <p class="mb-0 mt-2">Tidak ada data untuk tanggal
                                {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}
                                @if ($koordinatorId)
                                    dan koordinator
                                    {{ $koordinators->firstWhere('id', $koordinatorId)->nama_lengkap ?? '' }}
                                @endif
                            </p>
                        </div>
                    @else
                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-xl-3 col-md-6">
                                <div class="card card-animate">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <p class="text-uppercase fw-medium text-muted mb-0">Total Koordinator</p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-end justify-content-between mt-2">
                                            <div>
                                                <h4 class="fs-22 fw-semibold ff-secondary mb-0">
                                                    {{ $laporanPerKoordinator->count() }}</h4>
                                            </div>
                                            <div class="avatar-sm flex-shrink-0">
                                                <span class="avatar-title bg-primary-subtle rounded fs-3">
                                                    <i class="ri-user-star-line text-primary"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="card card-animate">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <p class="text-uppercase fw-medium text-muted mb-0">Total Data</p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-end justify-content-between mt-2">
                                            <div>
                                                <h4 class="fs-22 fw-semibold ff-secondary mb-0">
                                                    {{ $laporanPerKoordinator->sum('total_data') }}</h4>
                                            </div>
                                            <div class="avatar-sm flex-shrink-0">
                                                <span class="avatar-title bg-success-subtle rounded fs-3">
                                                    <i class="ri-file-list-3-line text-success"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="card card-animate">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <p class="text-uppercase fw-medium text-muted mb-0">Terbit SH</p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-end justify-content-between mt-2">
                                            <div>
                                                <h4 class="fs-22 fw-semibold ff-secondary mb-0">
                                                    {{ $laporanPerKoordinator->sum('total_terbit_sh') }}</h4>
                                            </div>
                                            <div class="avatar-sm flex-shrink-0">
                                                <span class="avatar-title bg-info-subtle rounded fs-3">
                                                    <i class="ri-check-double-line text-info"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="card card-animate">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <p class="text-uppercase fw-medium text-muted mb-0">Dibayar</p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-end justify-content-between mt-2">
                                            <div>
                                                <h4 class="fs-22 fw-semibold ff-secondary mb-0">
                                                    {{ $laporanPerKoordinator->sum('total_pembayaran_dibayar') }}</h4>
                                            </div>
                                            <div class="avatar-sm flex-shrink-0">
                                                <span class="avatar-title bg-warning-subtle rounded fs-3">
                                                    <i class="ri-money-dollar-circle-line text-warning"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Laporan Per Koordinator -->
                        @foreach ($laporanPerKoordinator as $koordinator)
                            <div class="card border shadow-none mb-3">
                                <div class="card-header bg-primary-subtle">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h5 class="mb-0">
                                                <i
                                                    class="ri-user-star-fill me-2"></i>{{ $koordinator['nama_koordinator'] }}
                                            </h5>
                                        </div>
                                        <div class="col-auto">
                                            <span class="badge bg-primary fs-6">Total: {{ $koordinator['total_data'] }}
                                                Data</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Summary Koordinator -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <div class="border rounded p-3">
                                                <h6 class="text-muted mb-3">Status Proses</h6>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span><i class="ri-time-line text-warning me-1"></i> Pending</span>
                                                    <span
                                                        class="badge bg-warning">{{ $koordinator['total_pending'] }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span><i class="ri-file-text-line text-primary me-1"></i> Progress
                                                        OSS</span>
                                                    <span
                                                        class="badge bg-primary">{{ $koordinator['total_progress_oss'] }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span><i class="ri-file-shield-line text-info me-1"></i> Progress
                                                        SIHALAL</span>
                                                    <span
                                                        class="badge bg-info">{{ $koordinator['total_progress_sihalal'] }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span><i class="ri-check-double-line text-success me-1"></i> Terbit
                                                        SH</span>
                                                    <span
                                                        class="badge bg-success">{{ $koordinator['total_terbit_sh'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="border rounded p-3">
                                                <h6 class="text-muted mb-3">Status Pembayaran</h6>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span><i class="ri-time-line text-secondary me-1"></i> Pending</span>
                                                    <span
                                                        class="badge bg-secondary">{{ $koordinator['total_pembayaran_pending'] }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span><i class="ri-file-paper-line text-warning me-1"></i>
                                                        Pengajuan</span>
                                                    <span
                                                        class="badge bg-warning">{{ $koordinator['total_pembayaran_pengajuan'] }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span><i class="ri-check-line text-success me-1"></i> Dibayar</span>
                                                    <span
                                                        class="badge bg-success">{{ $koordinator['total_pembayaran_dibayar'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Table Enumerator -->
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Enumerator</th>
                                                    <th class="text-center">Total</th>
                                                    <th class="text-center" colspan="4">Status Proses</th>
                                                    <th class="text-center" colspan="3">Status Pembayaran</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="3"></th>
                                                    <th class="text-center bg-warning-subtle">Pending</th>
                                                    <th class="text-center bg-primary-subtle">OSS</th>
                                                    <th class="text-center bg-info-subtle">SIHALAL</th>
                                                    <th class="text-center bg-success-subtle">Terbit</th>
                                                    <th class="text-center bg-secondary-subtle">Pending</th>
                                                    <th class="text-center bg-warning-subtle">Pengajuan</th>
                                                    <th class="text-center bg-success-subtle">Dibayar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($koordinator['enumerators'] as $index => $enum)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>
                                                            <i
                                                                class="ri-user-3-line me-1"></i>{{ $enum['nama_enumerator'] }}
                                                        </td>
                                                        <td class="text-center fw-semibold">{{ $enum['total_data'] }}</td>
                                                        <td class="text-center">
                                                            @if ($enum['status']['pending'] > 0)
                                                                <span
                                                                    class="badge bg-warning">{{ $enum['status']['pending'] }}</span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($enum['status']['progress_oss'] > 0)
                                                                <span
                                                                    class="badge bg-primary">{{ $enum['status']['progress_oss'] }}</span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($enum['status']['progress_sihalal'] > 0)
                                                                <span
                                                                    class="badge bg-info">{{ $enum['status']['progress_sihalal'] }}</span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($enum['status']['terbit_sh'] > 0)
                                                                <span
                                                                    class="badge bg-success">{{ $enum['status']['terbit_sh'] }}</span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($enum['status_pembayaran']['pending'] > 0)
                                                                <span
                                                                    class="badge bg-secondary">{{ $enum['status_pembayaran']['pending'] }}</span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($enum['status_pembayaran']['pengajuan'] > 0)
                                                                <span
                                                                    class="badge bg-warning">{{ $enum['status_pembayaran']['pengajuan'] }}</span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($enum['status_pembayaran']['dibayar'] > 0)
                                                                <span
                                                                    class="badge bg-success">{{ $enum['status_pembayaran']['dibayar'] }}</span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="table-light">
                                                <tr class="fw-bold">
                                                    <td colspan="2" class="text-end">Subtotal:</td>
                                                    <td class="text-center">{{ $koordinator['total_data'] }}</td>
                                                    <td class="text-center">{{ $koordinator['total_pending'] }}</td>
                                                    <td class="text-center">{{ $koordinator['total_progress_oss'] }}</td>
                                                    <td class="text-center">{{ $koordinator['total_progress_sihalal'] }}
                                                    </td>
                                                    <td class="text-center">{{ $koordinator['total_terbit_sh'] }}</td>
                                                    <td class="text-center">{{ $koordinator['total_pembayaran_pending'] }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $koordinator['total_pembayaran_pengajuan'] }}</td>
                                                    <td class="text-center">{{ $koordinator['total_pembayaran_dibayar'] }}
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Grand Total -->
                        <div class="card border-primary">
                            <div class="card-body bg-primary-subtle">
                                <div class="table-responsive">
                                    <table class="table table-borderless mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-center fw-bold">GRAND TOTAL</th>
                                                <th class="text-center fw-bold">Total Data</th>
                                                <th class="text-center fw-bold" colspan="4">Status Proses</th>
                                                <th class="text-center fw-bold" colspan="3">Status Pembayaran</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="fs-5">
                                                <td></td>
                                                <td class="text-center fw-bold text-primary">
                                                    {{ $laporanPerKoordinator->sum('total_data') }}</td>
                                                <td class="text-center fw-bold text-warning">
                                                    {{ $laporanPerKoordinator->sum('total_pending') }}</td>
                                                <td class="text-center fw-bold text-primary">
                                                    {{ $laporanPerKoordinator->sum('total_progress_oss') }}</td>
                                                <td class="text-center fw-bold text-info">
                                                    {{ $laporanPerKoordinator->sum('total_progress_sihalal') }}</td>
                                                <td class="text-center fw-bold text-success">
                                                    {{ $laporanPerKoordinator->sum('total_terbit_sh') }}</td>
                                                <td class="text-center fw-bold text-secondary">
                                                    {{ $laporanPerKoordinator->sum('total_pembayaran_pending') }}</td>
                                                <td class="text-center fw-bold text-warning">
                                                    {{ $laporanPerKoordinator->sum('total_pembayaran_pengajuan') }}</td>
                                                <td class="text-center fw-bold text-success">
                                                    {{ $laporanPerKoordinator->sum('total_pembayaran_dibayar') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        // Fungsi untuk menerapkan filter
        function applyFilter() {
            const tanggal = document.getElementById('filterTanggal').value;
            const koordinatorId = document.getElementById('filterKoordinator').value;

            let url = "{{ route('superadmin.laporan-harian.index') }}?tanggal=" + tanggal;

            if (koordinatorId) {
                url += "&koordinator_id=" + koordinatorId;
            }

            window.location.href = url;
        }

        // Event listener untuk filter tanggal
        document.getElementById('filterTanggal').addEventListener('change', applyFilter);

        // Event listener untuk filter koordinator
        document.getElementById('filterKoordinator').addEventListener('change', applyFilter);


        // Print
        function printLaporan() {
            window.print();
        }

        // Style untuk Print
        const style = document.createElement('style');
        style.textContent = `
            @media print {
                .card-header .btn, 
                .breadcrumb,
                .page-title-box,
                #filterTanggal,
                #filterKoordinator {
                    display: none !important;
                }
                .card {
                    border: 1px solid #000 !important;
                    page-break-inside: avoid;
                }
                .table {
                    font-size: 10px;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
@endsection
