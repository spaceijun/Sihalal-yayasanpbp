@extends('layouts.app')
@section('template_title')
    Laporan Arus Kas
@endsection

@section('content')
<div class="adm-page">

    {{-- ── PAGE HEADER ── --}}
    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Laporan Arus Kas</h1>
            <p>Ringkasan pemasukan, pengeluaran, dan neraca kas</p>
        </div>
        <a href="{{ route('superadmin.arus-kas.index') }}" class="adm-btn-secondary">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            Kelola Transaksi
        </a>
    </div>

    {{-- ── STAT CARDS ── --}}
    <div class="adm-stats" style="margin-bottom:22px;">
        <div class="adm-stat is-accent">
            <div class="adm-stat-label">Total Pemasukan</div>
            <div class="adm-stat-value" id="totalPemasukan">Rp 0</div>
            <div class="adm-stat-sub">Semua waktu</div>
        </div>
        <div class="adm-stat">
            <div class="adm-stat-label">Total Pengeluaran</div>
            <div class="adm-stat-value is-danger" id="totalPengeluaran">Rp 0</div>
            <div class="adm-stat-sub">Semua waktu</div>
        </div>
        <div class="adm-stat">
            <div class="adm-stat-label">Total Kas</div>
            <div class="adm-stat-value is-warn" id="totalKas">Rp 0</div>
            <div class="adm-stat-sub">Semua waktu</div>
        </div>
        <div class="adm-stat">
            <div class="adm-stat-label">Net Cashflow</div>
            <div class="adm-stat-value is-success" id="netCashflow">Rp 0</div>
            <div class="adm-stat-sub">Pemasukan − Pengeluaran</div>
        </div>
    </div>

    {{-- ── CHARTS ── --}}
    <div class="adm-card" style="margin-bottom:22px;">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/></svg>
                Tren Bulanan
            </div>
            <div style="display:flex;gap:4px;">
                <button class="adm-btn primary" id="btnTabPemasukan" onclick="switchTab('pemasukan')">
                    Pemasukan
                </button>
                <button class="adm-btn" id="btnTabPengeluaran" onclick="switchTab('pengeluaran')">
                    Pengeluaran
                </button>
            </div>
        </div>
        <div style="padding:20px;">
            <div id="panelPemasukan"><canvas id="chartPemasukan" height="100"></canvas></div>
            <div id="panelPengeluaran" style="display:none;"><canvas id="chartPengeluaran" height="100"></canvas></div>
        </div>
    </div>

    {{-- ── RECENT TRANSACTIONS ── --}}
    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                10 Transaksi Terakhir
            </div>
        </div>
        <div class="table-responsive">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Keterangan</th>
                        <th class="tr">Jumlah</th>
                    </tr>
                </thead>
                <tbody id="transactionTable">
                    <tr>
                        <td colspan="4">
                            <div class="adm-loading">
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                Memuat data...
                            </div>
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
    const MONTHS = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', { style:'currency', currency:'IDR', minimumFractionDigits:0 }).format(amount);
    }
    function formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' });
    }

    function switchTab(tab) {
        document.getElementById('panelPemasukan').style.display  = tab === 'pemasukan'   ? 'block' : 'none';
        document.getElementById('panelPengeluaran').style.display = tab === 'pengeluaran' ? 'block' : 'none';
        document.getElementById('btnTabPemasukan').className   = tab === 'pemasukan'   ? 'adm-btn primary' : 'adm-btn';
        document.getElementById('btnTabPengeluaran').className = tab === 'pengeluaran' ? 'adm-btn primary' : 'adm-btn';
    }

    function fetchData() {
        fetch('{{ route('superadmin.cashflows.data') }}')
            .then(r => r.json())
            .then(data => processData(data))
            .catch(() => {
                document.getElementById('transactionTable').innerHTML =
                    '<tr><td colspan="4" style="text-align:center;color:var(--adm-red);padding:30px">Gagal memuat data</td></tr>';
            });
    }

    function processData(cashflows) {
        let totalPemasukan = 0, totalPengeluaran = 0, totalKas = 0;
        const pemasukanPerBulan = Array(12).fill(0);
        const pengeluaranPerBulan = Array(12).fill(0);
        const currentYear = new Date().getFullYear();

        cashflows.forEach(item => {
            const jumlah = parseFloat(item.jumlah);
            const date = new Date(item.created_at);
            const bulanIndex = date.getMonth();
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
        document.getElementById('totalPemasukan').textContent  = formatCurrency(totalPemasukan);
        document.getElementById('totalPengeluaran').textContent = formatCurrency(totalPengeluaran);
        document.getElementById('totalKas').textContent        = formatCurrency(totalKas);
        const netEl = document.getElementById('netCashflow');
        netEl.textContent = formatCurrency(netCashflow);
        netEl.className = netCashflow >= 0 ? 'adm-stat-value is-success' : 'adm-stat-value is-danger';

        renderChart('chartPemasukan', pemasukanPerBulan, `Pemasukan per Bulan — ${currentYear}`, 'rgba(15,110,86,.7)', 'rgba(15,110,86,1)', 'chartPemasukanInstance');
        renderChart('chartPengeluaran', pengeluaranPerBulan, `Pengeluaran per Bulan — ${currentYear}`, 'rgba(220,38,38,.7)', 'rgba(220,38,38,1)', 'chartPengeluaranInstance');

        const last10 = [...cashflows].sort((a,b) => new Date(b.created_at) - new Date(a.created_at)).slice(0,10);
        renderTable(last10);
    }

    function renderChart(canvasId, data, title, bg, border, instanceKey) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;
        if (window[instanceKey]) window[instanceKey].destroy();
        window[instanceKey] = new Chart(ctx, {
            type: 'bar',
            data: { labels: MONTHS, datasets: [{ data, backgroundColor: bg, borderColor: border, borderWidth: 2, borderRadius: 6 }] },
            options: {
                responsive: true,
                plugins: { legend:{ display:false }, title:{ display:true, text:title, font:{ size:14, weight:'bold' } },
                    tooltip:{ callbacks:{ label: c => formatCurrency(c.parsed.y) } } },
                scales: { y: { beginAtZero:true, ticks:{ callback: v => formatCurrency(v) } } }
            }
        });
    }

    function renderTable(cashflows) {
        const tbody = document.getElementById('transactionTable');
        if (!cashflows || cashflows.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;color:var(--adm-text-faint);padding:30px">Tidak ada data transaksi</td></tr>';
            return;
        }
        tbody.innerHTML = cashflows.map(item => {
            let badge = '';
            let amountClass = '';
            if (item.tipe === 'Pemasukan') {
                badge = '<span class="adm-badge adm-badge-success"><span class="dot"></span>Pemasukan</span>';
                amountClass = 'color:var(--adm-green);';
            } else if (item.tipe === 'Pengeluaran') {
                badge = '<span class="adm-badge adm-badge-danger"><span class="dot"></span>Pengeluaran</span>';
                amountClass = 'color:var(--adm-red);';
            } else {
                badge = `<span class="adm-badge adm-badge-pending"><span class="dot"></span>${item.tipe}</span>`;
                amountClass = 'color:var(--adm-amber);';
            }
            return `<tr>
                <td style="color:var(--adm-text-muted);font-size:12.5px;">${formatDate(item.created_at)}</td>
                <td>${badge}</td>
                <td style="font-size:12.5px;color:var(--adm-text-mid);">${item.keterangan}</td>
                <td class="tr adm-mono" style="font-weight:600;${amountClass}">${formatCurrency(item.jumlah)}</td>
            </tr>`;
        }).join('');
    }

    fetchData();
</script>
@endsection
