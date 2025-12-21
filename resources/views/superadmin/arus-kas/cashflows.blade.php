@extends('layouts.app')
@section('template_title')
    Cashflows
@endsection
@section('content')
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

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <canvas id="cashflowChart" height="100"></canvas>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Transaksi Terakhir</h5>
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

    <!-- Tambahkan Chart.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fungsi format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }

        // Fungsi format tanggal
        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        // Ambil data dari API Laravel
        fetch('{{ route('superadmin.cashflows.data') }}')
            .then(response => response.json())
            .then(data => {
                console.log('Data received:', data);
                processData(data);
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                document.getElementById('transactionTable').innerHTML =
                    '<tr><td colspan="4" class="text-center text-danger">Gagal memuat data</td></tr>';
            });

        function processData(cashflows) {
            // Hitung total KESELURUHAN data untuk card dan chart
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

            // RUMUS BARU: Net Cashflow = Pemasukan - Pengeluaran + Kas
            const netCashflow = totalPemasukan - totalPengeluaran + totalKas;

            // Update card summary dengan total keseluruhan
            document.getElementById('totalPemasukan').textContent = formatCurrency(totalPemasukan);
            document.getElementById('totalPengeluaran').textContent = formatCurrency(totalPengeluaran);
            document.getElementById('totalKas').textContent = formatCurrency(totalKas);
            document.getElementById('netCashflow').textContent = formatCurrency(netCashflow);

            // Update net cashflow icon color
            const netIcon = document.getElementById('netCashflowIcon');
            const netText = document.getElementById('netCashflow');

            if (netCashflow >= 0) {
                netIcon.innerHTML = `
                    <span class="avatar-title bg-success-subtle rounded fs-3">
                        <i class="bx bx-trending-up text-success"></i>
                    </span>
                `;
                netText.classList.remove('text-danger');
                netText.classList.add('text-success');
            } else {
                netIcon.innerHTML = `
                    <span class="avatar-title bg-danger-subtle rounded fs-3">
                        <i class="bx bx-trending-down text-danger"></i>
                    </span>
                `;
                netText.classList.remove('text-success');
                netText.classList.add('text-danger');
            }

            // Render chart dengan total keseluruhan data
            renderChart(totalPemasukan, totalPengeluaran, totalKas);

            // Render table hanya 10 transaksi terakhir
            renderTable(cashflows);
        }

        function renderChart(pemasukan, pengeluaran, kas) {
            const ctx = document.getElementById('cashflowChart');
            if (!ctx) {
                console.error('Canvas element not found');
                return;
            }

            if (typeof Chart === 'undefined') {
                console.error('Chart.js not loaded');
                return;
            }

            new Chart(ctx, {
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
                            position: 'top',
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
                                label: function(context) {
                                    return 'Total: ' + formatCurrency(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return formatCurrency(value);
                                }
                            }
                        }
                    }
                }
            });
        }

        function renderTable(cashflows) {
            const tbody = document.getElementById('transactionTable');
            if (cashflows.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">Tidak ada data</td></tr>';
                return;
            }

            const recentTransactions = cashflows.slice(-10).reverse();

            tbody.innerHTML = recentTransactions.map(item => {
                let badgeClass = '';
                let textClass = '';
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
    </script>
@endsection
