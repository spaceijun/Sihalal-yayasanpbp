@extends('layouts.app')
@section('template_title')
    Cashflows
@endsection
@section('content')
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

    {{-- Chart dengan 2 Tab --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <ul class="nav nav-tabs card-header-tabs" id="chartTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-pemasukan" data-bs-toggle="tab"
                        data-bs-target="#panel-pemasukan" type="button" role="tab">
                        <i class="bx bx-trending-up me-1 text-success"></i> Pemasukan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-pengeluaran" data-bs-toggle="tab" data-bs-target="#panel-pengeluaran"
                        type="button" role="tab">
                        <i class="bx bx-trending-down me-1 text-danger"></i> Pengeluaran
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="chartTabContent">
                <div class="tab-pane fade show active" id="panel-pemasukan" role="tabpanel">
                    <canvas id="chartPemasukan" height="100"></canvas>
                </div>
                <div class="tab-pane fade" id="panel-pengeluaran" role="tabpanel">
                    <canvas id="chartPengeluaran" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel 10 Transaksi Terakhir --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <h5 class="mb-0">10 Transaksi Terakhir</h5>
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
        let chartPemasukanInstance = null;
        let chartPengeluaranInstance = null;

        const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
        ];

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

        function fetchData() {
            const url = '{{ route('superadmin.cashflows.data') }}';

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    processData(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('transactionTable').innerHTML =
                        '<tr><td colspan="4" class="text-center text-danger">Gagal memuat data</td></tr>';
                });
        }

        function processData(cashflows) {
            let totalPemasukan = 0;
            let totalPengeluaran = 0;
            let totalKas = 0;

            // Data per bulan untuk chart (indeks 0 = Januari, 11 = Desember)
            const pemasukanPerBulan = Array(12).fill(0);
            const pengeluaranPerBulan = Array(12).fill(0);

            const currentYear = new Date().getFullYear();

            cashflows.forEach(item => {
                const jumlah = parseFloat(item.jumlah);
                const date = new Date(item.created_at);
                const bulanIndex = date.getMonth(); // 0–11
                const tahun = date.getFullYear();

                if (item.tipe === 'Pemasukan') {
                    totalPemasukan += jumlah;
                    if (tahun === currentYear) pemasukanPerBulan[bulanIndex] += jumlah;
                } else if (item.tipe === 'Pengeluaran') {
                    totalPengeluaran += jumlah;
                    if (tahun === currentYear) pengeluaranPerBulan[bulanIndex] += jumlah;
                } else if (item.tipe === 'Kas') {
                    totalKas += jumlah;
                }
            });

            const netCashflow = totalPemasukan - totalPengeluaran;

            // Update summary cards
            document.getElementById('totalPemasukan').textContent = formatCurrency(totalPemasukan);
            document.getElementById('totalPengeluaran').textContent = formatCurrency(totalPengeluaran);
            document.getElementById('totalKas').textContent = formatCurrency(totalKas);
            document.getElementById('netCashflow').textContent = formatCurrency(netCashflow);

            // Update net cashflow icon & warna
            const netIcon = document.getElementById('netCashflowIcon');
            const netText = document.getElementById('netCashflow');
            if (netCashflow >= 0) {
                netIcon.innerHTML =
                    `<span class="avatar-title bg-success-subtle rounded fs-3"><i class="bx bx-trending-up text-success"></i></span>`;
                netText.classList.remove('text-danger');
                netText.classList.add('text-success');
            } else {
                netIcon.innerHTML =
                    `<span class="avatar-title bg-danger-subtle rounded fs-3"><i class="bx bx-trending-down text-danger"></i></span>`;
                netText.classList.remove('text-success');
                netText.classList.add('text-danger');
            }

            renderChartPemasukan(pemasukanPerBulan, currentYear);
            renderChartPengeluaran(pengeluaranPerBulan, currentYear);

            // Ambil 10 transaksi terakhir berdasarkan created_at
            const last10 = [...cashflows]
                .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
                .slice(0, 10);

            renderTable(last10);
        }

        function renderChartPemasukan(data, year) {
            const ctx = document.getElementById('chartPemasukan');
            if (!ctx) return;
            if (chartPemasukanInstance) chartPemasukanInstance.destroy();

            chartPemasukanInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: MONTHS,
                    datasets: [{
                        label: 'Pemasukan (Rp)',
                        data: data,
                        backgroundColor: 'rgba(25, 135, 84, 0.7)',
                        borderColor: 'rgba(25, 135, 84, 1)',
                        borderWidth: 2,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: `Pemasukan per Bulan — ${year}`,
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => formatCurrency(ctx.parsed.y)
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

        function renderChartPengeluaran(data, year) {
            const ctx = document.getElementById('chartPengeluaran');
            if (!ctx) return;
            if (chartPengeluaranInstance) chartPengeluaranInstance.destroy();

            chartPengeluaranInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: MONTHS,
                    datasets: [{
                        label: 'Pengeluaran (Rp)',
                        data: data,
                        backgroundColor: 'rgba(220, 53, 69, 0.7)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 2,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: `Pengeluaran per Bulan — ${year}`,
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => formatCurrency(ctx.parsed.y)
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
                    '<tr><td colspan="4" class="text-center text-muted">Tidak ada data transaksi</td></tr>';
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

        fetchData();
    </script>
@endsection
