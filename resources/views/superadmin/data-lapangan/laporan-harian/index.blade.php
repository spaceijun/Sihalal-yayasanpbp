@extends('layouts.app')

@section('template_title')
    Laporan Data Lapangan
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="card-title mb-0">Laporan Data Lapangan</h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="d-flex justify-content-end gap-2 align-items-center flex-wrap">
                                <!-- Toggle Tipe Data -->
                                <div class="btn-group no-print" role="group">
                                    <input type="radio" class="btn-check btn-sm" name="tipeData" id="filterCreated"
                                        value="created" {{ $tipeData == 'created' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-success" for="filterCreated">
                                        <i class="ri-add-circle-line me-1"></i>Data Masuk
                                    </label>

                                    <input type="radio" class="btn-check btn-sm" name="tipeData" id="filterUpdated"
                                        value="updated" {{ $tipeData == 'updated' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-info" for="filterUpdated">
                                        <i class="ri-edit-circle-line me-1"></i>Data Diubah
                                    </label>
                                </div>

                                <!-- Toggle Tipe Filter -->
                                <div class="btn-group no-print" role="group">
                                    <input type="radio" class="btn-check btn-sm" name="tipeFilter" id="filterHarian"
                                        value="harian" {{ $tipeFilter == 'harian' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="filterHarian">
                                        <i class="ri-calendar-line me-1"></i>Harian
                                    </label>

                                    <input type="radio" class="btn-check btn-sm" name="tipeFilter" id="filterBulanan"
                                        value="bulanan" {{ $tipeFilter == 'bulanan' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="filterBulanan">
                                        <i class="ri-calendar-2-line me-1"></i>Bulanan
                                    </label>
                                </div>

                                <!-- Filter Koordinator -->
                                <select class="form-select no-print" id="filterKoordinator" style="max-width: 250px;">
                                    <option value="">Semua Koordinator</option>
                                    @foreach ($koordinators as $koordinator)
                                        <option value="{{ $koordinator->id }}"
                                            {{ $koordinatorId == $koordinator->id ? 'selected' : '' }}>
                                            {{ $koordinator->nama_lengkap }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Filter Tanggal (Harian) -->
                                <input type="date" class="form-control no-print" id="filterTanggal"
                                    value="{{ $tanggal }}"
                                    style="max-width: 200px; {{ $tipeFilter == 'bulanan' ? 'display:none;' : '' }}">

                                <!-- Filter Bulan (Bulanan) -->
                                <input type="month" class="form-control no-print" id="filterBulan"
                                    value="{{ $bulan }}"
                                    style="max-width: 200px; {{ $tipeFilter == 'harian' ? 'display:none;' : '' }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($laporanPerKoordinator->isEmpty())
                        <div class="alert alert-info text-center" role="alert">
                            <i class="ri-information-line fs-4"></i>
                            <p class="mb-0 mt-2">Tidak ada data
                                <strong>{{ $tipeData == 'created' ? 'masuk' : 'diubah' }}</strong> untuk
                                @if ($tipeFilter == 'harian')
                                    tanggal {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}
                                @else
                                    bulan {{ \Carbon\Carbon::parse($bulan)->format('F Y') }}
                                @endif
                                @if ($koordinatorId)
                                    dan koordinator
                                    {{ $koordinators->firstWhere('id', $koordinatorId)->nama_lengkap ?? '' }}
                                @endif
                            </p>
                        </div>
                    @else
                        <!-- Periode Info -->
                        <div class="alert alert-primary border-0 mb-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="ri-calendar-check-line fs-4 me-2"></i>
                                <div>
                                    <strong>Tipe Data:</strong>
                                    {{ $tipeData == 'created' ? 'Data Masuk (Created)' : 'Data Diubah (Updated)' }}
                                    <span class="mx-2">|</span>
                                    <strong>Periode:</strong>
                                    @if ($tipeFilter == 'harian')
                                        {{ \Carbon\Carbon::parse($tanggal)->isoFormat('dddd, D MMMM YYYY') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($bulan)->isoFormat('MMMM YYYY') }}
                                    @endif
                                    @if ($koordinatorId)
                                        <span class="mx-2">|</span>
                                        <strong>Koordinator:</strong>
                                        {{ $koordinators->firstWhere('id', $koordinatorId)->nama_lengkap ?? '' }}
                                    @endif
                                </div>
                            </div>
                        </div>

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

                        <!-- Grafik Data Harian (Hanya untuk Bulanan) -->
                        @if ($tipeFilter == 'bulanan' && $grafikHarian)
                            <div class="card border shadow-none mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0"><i class="ri-bar-chart-line me-2"></i>Grafik Data Per Hari
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="chartHarian" height="80"></canvas>
                                </div>
                            </div>
                        @endif

                        <!-- Laporan Per Koordinator -->
                        @foreach ($laporanPerKoordinator as $koordinator)
                            <div class="card border shadow-none mb-3"
                                id="koordinator-{{ $koordinator['koordinator_id'] }}">
                                <div class="card-header bg-primary-subtle">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h5 class="mb-0">
                                                <i
                                                    class="ri-user-star-fill me-2"></i>{{ $koordinator['nama_koordinator'] }}
                                            </h5>
                                        </div>
                                        <div class="col-auto">
                                            <button class="btn btn-sm btn-info no-print"
                                                onclick="downloadKoordinatorImage({{ $koordinator['koordinator_id'] }}, '{{ $koordinator['nama_koordinator'] }}')">
                                                <i class="ri-download-2-line me-1"></i> Download Gambar
                                            </button>
                                            <span class="badge bg-primary fs-6 ms-2">Total:
                                                {{ $koordinator['total_data'] }} Data</span>
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
                                                    <span><i class="ri-time-line text-danger me-1"></i> Revisi</span>
                                                    <span
                                                        class="badge bg-danger">{{ $koordinator['total_revisi'] }}</span>
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
                                                    <th class="text-center" colspan="5">Status Proses</th>
                                                    <th class="text-center" colspan="3">Status Pembayaran</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="3"></th>
                                                    <th class="text-center bg-warning-subtle">Pending</th>
                                                    <th class="text-center bg-danger-subtle">Revisi</th>
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
                                                        <td><i
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
                                                            @if ($enum['status']['revisi'] > 0)
                                                                <span
                                                                    class="badge bg-danger">{{ $enum['status']['revisi'] }}</span>
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
                                                    <td class="text-center">{{ $koordinator['total_revisi'] }}</td>
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
                                                <th class="text-center fw-bold" colspan="5">Status Proses</th>
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
                                                <td class="text-center fw-bold text-danger">
                                                    {{ $laporanPerKoordinator->sum('total_revisi') }}</td>
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

    <!-- Library untuk export image -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Initialize Chart for Bulanan
        @if ($tipeFilter == 'bulanan' && $grafikHarian)
            const ctx = document.getElementById('chartHarian');
            const grafikData = @json($grafikHarian);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: grafikData.map(item => {
                        const date = new Date(item.tanggal);
                        return date.getDate() + '/' + (date.getMonth() + 1);
                    }),
                    datasets: [{
                        label: 'Total Data Per Hari',
                        data: grafikData.map(item => item.total),
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        @endif

        // Toggle Filter Type
        document.querySelectorAll('input[name="tipeFilter"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const filterTanggal = document.getElementById('filterTanggal');
                const filterBulan = document.getElementById('filterBulan');

                if (this.value === 'harian') {
                    filterTanggal.style.display = 'block';
                    filterBulan.style.display = 'none';
                } else {
                    filterTanggal.style.display = 'none';
                    filterBulan.style.display = 'block';
                }

                applyFilter();
            });
        });

        // Toggle Tipe Data
        document.querySelectorAll('input[name="tipeData"]').forEach(radio => {
            radio.addEventListener('change', applyFilter);
        });

        // Apply Filter
        function applyFilter() {
            const tipeFilter = document.querySelector('input[name="tipeFilter"]:checked').value;
            const tipeData = document.querySelector('input[name="tipeData"]:checked').value;
            const koordinatorId = document.getElementById('filterKoordinator').value;
            const tanggal = document.getElementById('filterTanggal').value;
            const bulan = document.getElementById('filterBulan').value;

            let url = "{{ route('superadmin.laporan-harian.index') }}?tipe=" + tipeFilter + "&tipe_data=" + tipeData;

            if (tipeFilter === 'harian') {
                url += "&tanggal=" + tanggal;
            } else {
                url += "&bulan=" + bulan;
            }

            if (koordinatorId) {
                url += "&koordinator_id=" + koordinatorId;
            }

            window.location.href = url;
        }

        // Event listeners
        document.getElementById('filterTanggal').addEventListener('change', applyFilter);
        document.getElementById('filterBulan').addEventListener('change', applyFilter);
        document.getElementById('filterKoordinator').addEventListener('change', applyFilter);

        // Download Koordinator as Image
        function downloadKoordinatorImage(koordinatorId, namaKoordinator) {
            // Show loading indicator
            const button = event.target.closest('button');
            const originalHTML = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="ri-loader-4-line me-1"></i> Memproses...';

            // Hide all no-print elements
            const noPrintElements = document.querySelectorAll('.no-print');
            noPrintElements.forEach(el => el.style.display = 'none');

            const element = document.getElementById('koordinator-' + koordinatorId);

            html2canvas(element, {
                scale: 2,
                logging: false,
                useCORS: true,
                backgroundColor: '#ffffff'
            }).then(canvas => {
                // Show elements again
                noPrintElements.forEach(el => el.style.display = '');
                button.disabled = false;
                button.innerHTML = originalHTML;

                // Convert canvas to blob
                canvas.toBlob(function(blob) {
                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    const tipeFilter = document.querySelector('input[name="tipeFilter"]:checked').value;
                    const tipeData = document.querySelector('input[name="tipeData"]:checked').value;
                    const tanggal = document.getElementById('filterTanggal').value;
                    const bulan = document.getElementById('filterBulan').value;

                    let filename = 'laporan-' + namaKoordinator.replace(/\s+/g, '-') + '-' + tipeData +
                        '-' + tipeFilter + '-';
                    filename += (tipeFilter === 'harian' ? tanggal : bulan) + '.png';

                    link.download = filename;
                    link.href = url;
                    link.click();

                    // Show success message if Swal is available
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Laporan ' + namaKoordinator + ' berhasil didownload',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        alert('Laporan ' + namaKoordinator + ' berhasil didownload!');
                    }
                });
            }).catch(error => {
                noPrintElements.forEach(el => el.style.display = '');
                button.disabled = false;
                button.innerHTML = originalHTML;

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan saat menggenerate gambar'
                    });
                } else {
                    alert('Gagal menggenerate gambar!');
                }
                console.error('Error:', error);
            });
        }
    </script>
@endsection
