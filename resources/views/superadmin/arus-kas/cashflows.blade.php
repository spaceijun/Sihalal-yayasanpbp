@extends('layouts.app')
@section('template_title')
    Cashflows
@endsection
@section('content')
    {{-- Filter --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bx bx-filter-alt me-2"></i>Filter Cashflow</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Bulan</label>
                            <select id="filterBulan" class="form-select">
                                <option value="">-- Semua Bulan --</option>
                                <option value="1">Januari</option>
                                <option value="2">Februari</option>
                                <option value="3">Maret</option>
                                <option value="4">April</option>
                                <option value="5">Mei</option>
                                <option value="6">Juni</option>
                                <option value="7">Juli</option>
                                <option value="8">Agustus</option>
                                <option value="9">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Tahun</label>
                            <select id="filterTahun" class="form-select">
                                <option value="">-- Semua Tahun --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary w-100" onclick="applyFilter()">
                                    <i class="bx bx-filter-alt me-1"></i> Terapkan
                                </button>
                                <button class="btn btn-outline-secondary w-100" onclick="resetFilter()">
                                    <i class="bx bx-reset me-1"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Summary Cards --}}
    <div class="row">
        <div class="col-xl-3">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Pemasukan</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span id="totalPemasukan">Rp 0</span>
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
        <div class="col-xl-3">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Pengeluaran</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span id="totalPengeluaran">Rp 0</span>
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
        <div class="col-xl-3">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Kas</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span id="totalKas">Rp 0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle rounded fs-3">
                                <i class="bx bx-wallet text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Net Cashflow</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span id="netCashflow">Rp 0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0" id="netCashflowIcon">
                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                <i class="bx bx-line-chart text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <canvas id="cashflowChart" height="100"></canvas>
        </div>
    </div>

    {{-- Tabel Transaksi --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Transaksi</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Keterangan</th>
                            <th class="text-end">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody id="transactionTable">
                        <tr>
                            <td colspan="4" class="text-center">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <span class="ms-2">Memuat data...</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <script>
        let chartInstance = null;
        let tahunSudahDiisi = false;

        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }

        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        function populateYearOptions(cashflows) {
            if (tahunSudahDiisi) return;

            const years = [...new Set(
                cashflows.map(item => new Date(item.created_at).getFullYear())
            )].sort((a, b) => b - a);

            const select = document.getElementById('filterTahun');
            years.forEach(year => {
                const opt = document.createElement('option');
                opt.value = year;
                opt.textContent = year;
                select.appendChild(opt);
            });

            // Set default ke tahun sekarang jika ada
            const currentYear = new Date().getFullYear();
            if (years.includes(currentYear)) {
                select.value = currentYear;
            }

            tahunSudahDiisi = true;
        }

        function fetchData() {
            const bulan = document.getElementById('filterBulan').value;
            const tahun = document.getElementById('filterTahun').value;

            const params = new URLSearchParams();
            if (bulan) params.append('bulan', bulan);
            if (tahun) params.append('tahun', tahun);

            const url = '{{ route('superadmin.cashflows.data') }}' +
                (params.toString() ? '?' + params.toString() : '');

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    // Isi dropdown tahun hanya sekali dari data pertama (tanpa filter)
                    if (!tahunSudahDiisi) {
                        populateYearOptions(data);
                        // Setelah dropdown tahun terisi dan default tahun di-set,
                        // fetch ulang dengan filter tahun default
                        const tahunDefault = document.getElementById('filterTahun').value;
                        if (tahunDefault) {
                            fetchData();
                            return;
                        }
                    }

                    processData(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('transactionTable').innerHTML =
                        '<tr><td colspan="4" class="text-center text-danger">Gagal memuat data</td></tr>';
                });
        }

        function applyFilter() {
            fetchData();
        }

        function resetFilter() {
            document.getElementById('filterBulan').value = '';
            document.getElementById('filterTahun').value = '';
            fetchData();
        }

        function processData(cashflows) {
            let totalPemasukan = 0;
            let totalPengeluaran = 0;
            let totalKas = 0;

            cashflows.forEach(item => {
                const jumlah = parseFloat(item.jumlah);
                if (item.tipe === 'Pemasukan') {
                    totalPemasukan += jumlah;
                } else if (item.tipe === 'Pengeluaran') {
                    totalPengeluaran += jumlah;
                } else if (item.tipe === 'Kas') {
                    totalKas += jumlah;
                }
            });

            const netCashflow = totalPemasukan - totalPengeluaran;

            // Update cards
            document.getElementById('totalPemasukan').textContent = formatCurrency(totalPemasukan);
            document.getElementById('totalPengeluaran').textContent = formatCurrency(totalPengeluaran);
            document.getElementById('totalKas').textContent = formatCurrency(totalKas);
            document.getElementById('netCashflow').textContent = formatCurrency(netCashflow);

            // Update net cashflow icon & warna
            const netIcon = document.getElementById('netCashflowIcon');
            const netText = document.getElementById('netCashflow');
            if (netCashflow >= 0) {
                netIcon.innerHTML = `
                    <span class="avatar-title bg-success-subtle rounded fs-3">
                        <i class="bx bx-trending-up text-success"></i>
                    </span>`;
                netText.classList.remove('text-danger');
                netText.classList.add('text-success');
            } else {
                netIcon.innerHTML = `
                    <span class="avatar-title bg-danger-subtle rounded fs-3">
                        <i class="bx bx-trending-down text-danger"></i>
                    </span>`;
                netText.classList.remove('text-success');
                netText.classList.add('text-danger');
            }

            renderChart(totalPemasukan, totalPengeluaran, totalKas);
            renderTable(cashflows);
        }

        function renderChart(pemasukan, pengeluaran, kas) {
            const ctx = document.getElementById('cashflowChart');
            if (!ctx) return;

            if (chartInstance) chartInstance.destroy();

            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Pemasukan', 'Pengeluaran', 'Kas'],
                    datasets: [{
                        label: 'Total (Rp)',
                        data: [pemasukan, pengeluaran, kas],
                        backgroundColor: [
                            'rgba(25, 135, 84, 0.8)',
                            'rgba(220, 53, 69, 0.8)',
                            'rgba(13, 110, 253, 0.8)'
                        ],
                        borderColor: [
                            'rgba(25, 135, 84, 1)',
                            'rgba(220, 53, 69, 1)',
                            'rgba(13, 110, 253, 1)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'Laporan Cashflow',
                            font: {
                                size: 18,
                                weight: 'bold'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => 'Total: ' + formatCurrency(ctx.parsed.y)
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: value => formatCurrency(value)
                            }
                        }
                    }
                }
            });
        }

        function renderTable(cashflows) {
            const tbody = document.getElementById('transactionTable');

            if (!cashflows || cashflows.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="4" class="text-center text-muted">Tidak ada data untuk filter ini</td></tr>';
                return;
            }

            tbody.innerHTML = cashflows.map(item => {
                let badgeClass = '',
                    textClass = '';
                if (item.tipe === 'Pemasukan') {
                    badgeClass = 'bg-success';
                    textClass = 'text-success';
                } else if (item.tipe === 'Pengeluaran') {
                    badgeClass = 'bg-danger';
                    textClass = 'text-danger';
                } else {
                    badgeClass = 'bg-primary';
                    textClass = 'text-primary';
                }

                return `
                    <tr>
                        <td>${formatDate(item.created_at)}</td>
                        <td><span class="badge ${badgeClass}">${item.tipe}</span></td>
                        <td>${item.keterangan}</td>
                        <td class="text-end fw-bold ${textClass}">${formatCurrency(item.jumlah)}</td>
                    </tr>
                `;
            }).join('');
        }

        // Load pertama tanpa filter (untuk isi dropdown tahun)
        fetchData();
    </script>
@endsection
