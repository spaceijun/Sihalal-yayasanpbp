@extends('layouts.app')
@section('template_title')
    Dashboard
@endsection

@section('content')
    {{-- WELCOME BANNER --}}
    <div class="welcome-banner" id="welcomeBanner">
        <div class="icon-wrap"><i class="ri-shield-user-line"></i></div>
        <div class="text-wrap">
            <strong>Selamat datang, <span class="badge-bejo">Admin Umum</span>!</strong>
            <span>Semoga Hari Kalian Selalu "BEJO" — Bekerja Keras, Jujur, dan Optimis.</span>
        </div>
        <button class="close-btn" onclick="document.getElementById('welcomeBanner').style.display='none'">&times;</button>
    </div>

    {{-- ─── SECTION 1: TIM & DATA ─── --}}
    <div class="section-label">Ringkasan Umum</div>
    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="stat-card c-blue">
                <div class="glow"></div>
                <div class="card-icon"><i class="bx bx-group"></i></div>
                <div class="card-label">Total Tim</div>
                <div class="card-value">
                    <span class="counter-value" data-target="{{ $totalDataKoordinator }}">0</span>
                </div>
                <div class="card-divider"></div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="stat-card c-cyan">
                <div class="glow"></div>
                <div class="card-icon"><i class="bx bx-user-check"></i></div>
                <div class="card-label">Total Pendamping</div>
                <div class="card-value">
                    <span class="counter-value" data-target="{{ $totalDataEnumerator }}">0</span>
                </div>
                <div class="card-divider"></div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="stat-card c-green">
                <div class="glow"></div>
                <div class="card-icon"><i class="bx bx-data"></i></div>
                <div class="card-label">Total Data Masuk</div>
                <div class="card-value">
                    <span class="counter-value" data-target="{{ $totalDataLapangan }}">0</span>
                </div>
                <div class="card-divider"></div>
            </div>
        </div>
    </div>

    {{-- ─── SECTION 2: STATUS DATA ─── --}}
    <div class="section-label">Status Data Lapangan</div>
    @php $total = $totalDataLapangan > 0 ? $totalDataLapangan : 1; @endphp
    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="mini-card c-amber">
                <div class="mc-glow"></div>
                <div class="mc-top">
                    <div class="mc-icon"><i class="bx bx-time-five"></i></div>
                    <div class="mc-pct">{{ number_format(($totalDataPending / $total) * 100, 1) }}%</div>
                </div>
                <div class="mc-label">Data Pending</div>
                <div class="mc-value">
                    <span class="counter-value" data-target="{{ $totalDataPending }}">0</span>
                </div>
                <div class="mc-bar-track">
                    <div class="mc-bar-fill" data-width="{{ ($totalDataPending / $total) * 100 }}"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="mini-card c-violet">
                <div class="mc-glow"></div>
                <div class="mc-top">
                    <div class="mc-icon"><i class="las la-tasks"></i></div>
                    <div class="mc-pct">{{ number_format(($totalDataTerverifikasi / $total) * 100, 1) }}%</div>
                </div>
                <div class="mc-label">Data Terverifikasi</div>
                <div class="mc-value">
                    <span class="counter-value" data-target="{{ $totalDataTerverifikasi }}">0</span>
                </div>
                <div class="mc-bar-track">
                    <div class="mc-bar-fill" data-width="{{ ($totalDataTerverifikasi / $total) * 100 }}"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="mini-card c-rose">
                <div class="mc-glow"></div>
                <div class="mc-top">
                    <div class="mc-icon"><i class="bx bx-edit"></i></div>
                    <div class="mc-pct">{{ number_format((($totalDataRevisi ?? 0) / $total) * 100, 1) }}%</div>
                </div>
                <div class="mc-label">Data Revisi</div>
                <div class="mc-value">
                    <span class="counter-value" data-target="{{ $totalDataRevisi ?? 0 }}">0</span>
                </div>
                <div class="mc-bar-track">
                    <div class="mc-bar-fill" data-width="{{ (($totalDataRevisi ?? 0) / $total) * 100 }}"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── SECTION 3: PROGRESS ─── --}}
    <div class="section-label">Progress Sertifikasi</div>
    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="mini-card c-cyan">
                <div class="mc-glow"></div>
                <div class="mc-top">
                    <div class="mc-icon"><i class="bx bx-loader-circle"></i></div>
                    <div class="mc-pct">{{ number_format(($totalDataProgressOSS / $total) * 100, 1) }}%</div>
                </div>
                <div class="mc-label">Progress OSS</div>
                <div class="mc-value">
                    <span class="counter-value" data-target="{{ $totalDataProgressOSS }}">0</span>
                </div>
                <div class="mc-bar-track">
                    <div class="mc-bar-fill" data-width="{{ ($totalDataProgressOSS / $total) * 100 }}"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="mini-card c-blue">
                <div class="mc-glow"></div>
                <div class="mc-top">
                    <div class="mc-icon"><i class="bx bx-sync"></i></div>
                    <div class="mc-pct">{{ number_format(($totalDataProgressSihalal / $total) * 100, 1) }}%</div>
                </div>
                <div class="mc-label">Progress Sihalal</div>
                <div class="mc-value">
                    <span class="counter-value" data-target="{{ $totalDataProgressSihalal }}">0</span>
                </div>
                <div class="mc-bar-track">
                    <div class="mc-bar-fill" data-width="{{ ($totalDataProgressSihalal / $total) * 100 }}"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="mini-card c-green">
                <div class="mc-glow"></div>
                <div class="mc-top">
                    <div class="mc-icon"><i class="bx bx-check-circle"></i></div>
                    <div class="mc-pct">{{ number_format(($totalDataTerbitSH / $total) * 100, 1) }}%</div>
                </div>
                <div class="mc-label">Data Terbit SH</div>
                <div class="mc-value">
                    <span class="counter-value" data-target="{{ $totalDataTerbitSH }}">0</span>
                </div>
                <div class="mc-bar-track">
                    <div class="mc-bar-fill" data-width="{{ ($totalDataTerbitSH / $total) * 100 }}"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── SECTION 4: PEMBAYARAN ─── --}}
    <div class="section-label">Status Pembayaran</div>
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="pay-card pay-pending">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="pay-label">Pembayaran Pending</div>
                        <div class="pay-value">
                            <span class="counter-value" data-target="{{ $totalPembayaranPending ?? 0 }}">0</span>
                        </div>
                    </div>
                    <div class="pay-icon"><i class="bx bx-wallet"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4"> {{-- ← BARU --}}
            <div class="pay-card pay-pengajuan"> {{-- ← BARU --}}
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="pay-label">Pembayaran Pengajuan</div>
                        <div class="pay-value">
                            <span class="counter-value" data-target="{{ $totalPembayaranPengajuan ?? 0 }}">0</span>
                        </div>
                    </div>
                    <div class="pay-icon"><i class="bx bx-send"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="pay-card pay-paid">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="pay-label">Sudah Dibayar</div>
                        <div class="pay-value">
                            <span class="counter-value" data-target="{{ $totalDibayar ?? 0 }}">0</span>
                        </div>
                    </div>
                    <div class="pay-icon"><i class="bx bx-check-double"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── SECTION 5: CHARTS ─── --}}
    <div class="section-label">Tren Data</div>
    <div class="row g-4 mb-4">
        {{-- Chart: Data Masuk Per Bulan --}}
        <div class="col-xl-6">
            <div class="chart-card">
                <div class="chart-header">
                    <div>
                        <div class="chart-title">Tren Data Masuk</div>
                        <div class="chart-subtitle">Jumlah seluruh data masuk per bulan</div>
                    </div>
                    <div class="chart-legend">
                        <div class="legend-item">
                            <div class="legend-dot" style="background:#4f8ef7"></div> Data Masuk
                        </div>
                    </div>
                </div>
                <div class="chart-body">
                    <canvas id="chartDataMasuk" height="220"></canvas>
                </div>
            </div>
        </div>
        {{-- Chart: Data Terbit SH Per Bulan --}}
        <div class="col-xl-6">
            <div class="chart-card">
                <div class="chart-header">
                    <div>
                        <div class="chart-title">Tren Data Terbit SH</div>
                        <div class="chart-subtitle">Jumlah seluruh data terbit SH per bulan</div>
                    </div>
                    <div class="chart-legend">
                        <div class="legend-item">
                            <div class="legend-dot" style="background:#10d98a"></div> Terbit SH
                        </div>
                    </div>
                </div>
                <div class="chart-body">
                    <canvas id="chartTerbitSH" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── SECTION 6: TABLES ─── --}}
    <div class="row g-4">
        {{-- Table: 20 Data Terakhir Masuk --}}
        <div class="col-12">
            <div class="data-table-wrap">
                <div class="dt-header">
                    <h5><i class="bx bx-import me-2" style="color:var(--accent-indigo)"></i>20 Data Terakhir Masuk</h5>
                    <span class="dt-badge">Realtime</span>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Pendamping</th>
                                <th>Nama PU</th>
                                <th>Status</th>
                                <th>Tanggal Input</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestDataToday as $index => $data)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $data->enumerator->nama_lengkap ?? '-' }}</td>
                                    <td>{{ $data->nama_pu }}</td>
                                    <td>
                                        @if ($data->status == 'PENDING')
                                            <span class="status-badge badge-pending">PENDING</span>
                                        @elseif($data->status == 'TERVERIFIKASI')
                                            <span class="status-badge badge-verified">TERVERIFIKASI</span>
                                        @elseif($data->status == 'DITOLAK')
                                            <span class="status-badge badge-ditolak">DITOLAK</span>
                                        @elseif($data->status == 'PROGRESS OSS')
                                            <span class="status-badge badge-oss">PROGRESS OSS</span>
                                        @elseif($data->status == 'PROGRESS SIHALAL')
                                            <span class="status-badge badge-sihalal">PROGRESS SIHALAL</span>
                                        @elseif($data->status == 'TERBIT SH')
                                            <span class="status-badge badge-terbit">TERBIT SH</span>
                                        @elseif($data->status == 'REVISI')
                                            <span class="status-badge badge-revisi">REVISI</span>
                                        @endif
                                    </td>
                                    <td>{{ $data->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align:center;color:var(--text-muted);padding:40px">
                                        <i class="bx bx-data" style="font-size:28px;display:block;margin-bottom:8px"></i>
                                        Belum ada data
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Table: 20 Data Terbit SH Terbaru --}}
        <div class="col-12">
            <div class="data-table-wrap">
                <div class="dt-header">
                    <h5><i class="bx bx-check-shield me-2" style="color:var(--accent-emerald)"></i>20 Data Terbit SH
                        Terbaru</h5>
                    <span class="dt-badge"
                        style="background:rgba(16,217,138,0.1);color:var(--accent-emerald);border-color:rgba(16,217,138,0.25)">Sertifikat
                        Halal</span>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Pendamping</th>
                                <th>Nama PU</th>
                                <th>Status</th>
                                <th>Tanggal Pengajuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestDataUpdate as $index => $data)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $data->enumerator->nama_lengkap ?? '-' }}</td>
                                    <td>{{ $data->nama_pu }}</td>
                                    <td>
                                        @if ($data->status == 'PENDING')
                                            <span class="status-badge badge-pending">PENDING</span>
                                        @elseif($data->status == 'DITOLAK')
                                            <span class="status-badge badge-ditolak">DITOLAK</span>
                                        @elseif($data->status == 'PROGRESS OSS')
                                            <span class="status-badge badge-oss">PROGRESS OSS</span>
                                        @elseif($data->status == 'PROGRESS SIHALAL')
                                            <span class="status-badge badge-sihalal">PROGRESS SIHALAL</span>
                                        @elseif($data->status == 'TERBIT SH')
                                            <span class="status-badge badge-terbit">TERBIT SH</span>
                                        @endif
                                    </td>
                                    <td>{{ $data->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align:center;color:var(--text-muted);padding:40px">
                                        <i class="bx bx-check-circle"
                                            style="font-size:28px;display:block;margin-bottom:8px"></i>
                                        Belum ada data
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --bg-base: #f3f5fb;
            --bg-card: #ffffff;
            --bg-card-hover: #fafbff;
            --border: #e8ecf4;
            --border-bright: #d0d7eb;
            --text-primary: #1a2040;
            --text-secondary: #5a6380;
            --text-muted: #9aa0b8;
            --accent-blue: #3b7cf4;
            --accent-cyan: #06b6d4;
            --accent-violet: #7c3aed;
            --accent-emerald: #059669;
            --accent-amber: #d97706;
            --accent-rose: #e11d48;
            --accent-pink: #db2777;
            --accent-indigo: #4f46e5;
            --shadow-card: 0 2px 12px rgba(80, 100, 160, 0.08), 0 1px 3px rgba(80, 100, 160, 0.06);
            --shadow-hover: 0 8px 28px rgba(80, 100, 160, 0.14);
            --radius: 16px;
            --radius-sm: 10px;
        }

        body,
        .page-content,
        .main-content {
            background: var(--bg-base) !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }

        /* ─── WELCOME BANNER ─── */
        .welcome-banner {
            background: linear-gradient(135deg, #eef2ff 0%, #f0f9ff 60%, #f5f3ff 100%);
            border: 1px solid #dde3f8;
            border-radius: var(--radius);
            padding: 20px 28px;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            gap: 14px;
            position: relative;
            overflow: hidden;
        }

        .welcome-banner::before {
            content: '';
            position: absolute;
            top: -50px;
            left: -50px;
            width: 180px;
            height: 180px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .welcome-banner::after {
            content: '';
            position: absolute;
            bottom: -40px;
            right: 80px;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        .welcome-banner .icon-wrap {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--accent-indigo), var(--accent-violet));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
        }

        .welcome-banner .text-wrap strong {
            display: block;
            color: var(--text-primary);
            font-size: 15px;
            font-weight: 700;
        }

        .welcome-banner .text-wrap span {
            color: var(--text-secondary);
            font-size: 13px;
        }

        .welcome-banner .badge-bejo {
            background: linear-gradient(90deg, var(--accent-indigo), var(--accent-cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }

        .welcome-banner .close-btn {
            margin-left: auto;
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 20px;
            line-height: 1;
            transition: color .2s;
        }

        .welcome-banner .close-btn:hover {
            color: var(--text-secondary);
        }

        /* ─── SECTION LABEL ─── */
        .section-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 14px;
            margin-top: 10px;
            padding-left: 2px;
        }

        /* ─── STAT CARDS ─── */
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 22px 24px;
            position: relative;
            overflow: hidden;
            transition: transform .25s, box-shadow .25s, border-color .25s;
            box-shadow: var(--shadow-card);
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            border-color: var(--border-bright);
            box-shadow: var(--shadow-hover);
        }

        .stat-card .glow {
            position: absolute;
            top: -20px;
            right: -20px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            opacity: 0.08;
            pointer-events: none;
        }

        .stat-card .card-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 18px;
        }

        .stat-card .card-label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .stat-card .card-value {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 32px;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1;
            letter-spacing: -1px;
        }

        .stat-card .card-divider {
            height: 3px;
            border-radius: 3px;
            margin-top: 18px;
            opacity: 0.35;
        }

        /* Color variants */
        .c-blue {
            --cc: var(--accent-blue);
        }

        .c-cyan {
            --cc: var(--accent-cyan);
        }

        .c-green {
            --cc: var(--accent-emerald);
        }

        .c-amber {
            --cc: var(--accent-amber);
        }

        .c-violet {
            --cc: var(--accent-violet);
        }

        .c-rose {
            --cc: var(--accent-rose);
        }

        .c-indigo {
            --cc: var(--accent-indigo);
        }

        .c-pink {
            --cc: var(--accent-pink);
        }

        .stat-card .card-icon {
            background: color-mix(in srgb, var(--cc) 12%, transparent);
            color: var(--cc);
        }

        .stat-card .glow {
            background: var(--cc);
        }

        .stat-card .card-divider {
            background: linear-gradient(90deg, var(--cc), transparent);
        }

        /* ─── PAYMENT CARDS ─── */
        .pay-card {
            border-radius: var(--radius);
            padding: 24px;
            position: relative;
            overflow: hidden;
            height: 100%;
            border: 1px solid transparent;
            box-shadow: var(--shadow-card);
            transition: transform .25s, box-shadow .25s;
        }

        .pay-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .pay-card.pay-pending {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-color: rgba(79, 70, 229, 0.2);
        }

        .pay-card.pay-paid {
            background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%);
            border-color: rgba(2, 132, 199, 0.2);
        }

        .pay-card .pay-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #fff;
        }

        .pay-card .pay-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 6px;
        }

        .pay-card .pay-value {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 34px;
            font-weight: 700;
            color: #fff;
            letter-spacing: -1px;
        }

        .pay-card.pay-pengajuan {
            background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
            border-color: rgba(217, 119, 6, 0.2);
        }

        /* ─── CHART CARD ─── */
        .chart-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-card);
            overflow: hidden;
        }

        .chart-card .chart-header {
            padding: 20px 24px 0;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .chart-card .chart-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }

        .chart-card .chart-subtitle {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .chart-card .chart-legend {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .chart-card .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--text-secondary);
        }

        .chart-card .legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .chart-card .chart-body {
            padding: 16px 24px 24px;
        }

        .chart-card canvas {
            width: 100% !important;
        }

        /* ─── TABLE ─── */
        .data-table-wrap {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-card);
            overflow: hidden;
        }

        .data-table-wrap .dt-header {
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border);
            gap: 12px;
        }

        .data-table-wrap .dt-header h5 {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }

        .data-table-wrap .dt-badge {
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 20px;
            font-weight: 600;
            background: rgba(79, 70, 229, 0.08);
            color: var(--accent-indigo);
            border: 1px solid rgba(79, 70, 229, 0.18);
        }

        .data-table-wrap .table-responsive {
            padding: 0 0 8px;
        }

        .data-table-wrap table {
            width: 100%;
            border-collapse: collapse;
            color: var(--text-primary);
            font-size: 13px;
        }

        .data-table-wrap thead th {
            background: #f7f9fd;
            color: var(--text-muted);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 10px 24px;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        .data-table-wrap tbody tr {
            border-bottom: 1px solid #f1f4fb;
            transition: background .15s;
        }

        .data-table-wrap tbody tr:hover {
            background: #f7f9fd;
        }

        .data-table-wrap tbody tr:last-child {
            border-bottom: none;
        }

        .data-table-wrap tbody td {
            padding: 11px 24px;
            vertical-align: middle;
            color: var(--text-secondary);
        }

        .data-table-wrap tbody td:first-child {
            color: var(--text-muted);
            font-size: 12px;
        }

        .data-table-wrap tbody td:nth-child(3) {
            color: var(--text-primary);
            font-weight: 500;
        }

        /* ─── BADGES ─── */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.5px;
            padding: 4px 10px;
            border-radius: 20px;
        }

        .status-badge::before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: currentColor;
        }

        .badge-pending {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .badge-verified {
            background: #eef2ff;
            color: #4338ca;
            border: 1px solid #c7d2fe;
        }

        .badge-rejected {
            background: #f9fafb;
            color: #6b7280;
            border: 1px solid #e5e7eb;
        }

        .badge-oss {
            background: #ecfeff;
            color: #0e7490;
            border: 1px solid #a5f3fc;
        }

        .badge-sihalal {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }

        .badge-terbit {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .badge-revisi {
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #fecdd3;
        }

        .badge-ditolak {
            background: #f9fafb;
            color: #6b7280;
            border: 1px solid #e5e7eb;
        }

        /* ─── COUNTER ANIMATION ─── */
        @keyframes countUp {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-value {
            animation: countUp .6s ease forwards;
        }

        /* ─── COUNTER NUMBERS ─── */
        .counter-value {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 32px;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1;
            letter-spacing: -1px;
        }

        .pay-value .counter-value {
            font-size: 34px;
            color: #fff;
        }

        /* ─── SCROLLBAR ─── */
        ::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f4fb;
        }

        ::-webkit-scrollbar-thumb {
            background: #d0d7eb;
            border-radius: 4px;
        }

        /* ─── ROW GAP FIX ─── */
        .row {
            --bs-gutter-x: 20px;
            --bs-gutter-y: 20px;
        }

        /* ─── OVERRIDE Bootstrap card ─── */
        .card {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }

        /* ─── MINI STAT CARD with percentage ─── */
        .mini-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 14px 18px;
            position: relative;
            overflow: hidden;
            transition: transform .22s, box-shadow .22s, border-color .22s;
            box-shadow: var(--shadow-card);
            height: 100%;
        }

        .mini-card:hover {
            transform: translateY(-2px);
            border-color: var(--border-bright);
            box-shadow: var(--shadow-hover);
        }

        .mini-card .mc-glow {
            position: absolute;
            top: -16px;
            right: -16px;
            width: 72px;
            height: 72px;
            border-radius: 50%;
            opacity: 0.09;
            pointer-events: none;
        }

        .mini-card .mc-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .mini-card .mc-icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            flex-shrink: 0;
        }

        .mini-card .mc-pct {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 20px;
            letter-spacing: 0.3px;
        }

        .mini-card .mc-label {
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .mini-card .mc-value {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1;
            letter-spacing: -0.5px;
        }

        .mini-card .mc-bar-track {
            height: 4px;
            border-radius: 4px;
            background: var(--border);
            margin-top: 10px;
            overflow: hidden;
        }

        .mini-card .mc-bar-fill {
            height: 100%;
            border-radius: 4px;
            background: var(--cc);
            transition: width 1.2s cubic-bezier(.22, 1, .36, 1);
            width: 0%;
        }

        .mini-card .mc-icon {
            background: color-mix(in srgb, var(--cc) 12%, transparent);
            color: var(--cc);
        }

        .mini-card .mc-glow {
            background: var(--cc);
        }

        .mini-card .mc-pct {
            background: color-mix(in srgb, var(--cc) 10%, transparent);
            color: var(--cc);
            border: 1px solid color-mix(in srgb, var(--cc) 25%, transparent);
        }
    </style>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ──────────────────────────────────────────
            // Counter animation
            // ──────────────────────────────────────────
            const easeOut = t => 1 - Math.pow(1 - t, 3);
            document.querySelectorAll('.counter-value[data-target]').forEach(el => {
                const target = parseInt(el.getAttribute('data-target')) || 0;
                const duration = 1400;
                const start = performance.now();

                function tick(now) {
                    const elapsed = Math.min((now - start) / duration, 1);
                    el.textContent = Math.floor(easeOut(elapsed) * target);
                    if (elapsed < 1) requestAnimationFrame(tick);
                }
                requestAnimationFrame(tick);
            });

            // ──────────────────────────────────────────
            // Progress bar animation
            // ──────────────────────────────────────────
            document.querySelectorAll('.mc-bar-fill[data-width]').forEach(bar => {
                const target = parseFloat(bar.getAttribute('data-width')) || 0;
                setTimeout(() => {
                    bar.style.width = Math.min(target, 100) + '%';
                }, 200);
            });

            Chart.defaults.color = '#9aa0b8';
            Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
            Chart.defaults.font.size = 11;

            const gridColor = 'rgba(80,100,160,0.07)';

            // ──────────────────────────────────────────
            // Helper: format label bulan dari "YYYY-MM"
            // ──────────────────────────────────────────
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];

            function formatLabel(ym) {
                const [y, m] = ym.split('-');
                return monthNames[parseInt(m) - 1] + ' ' + y;
            }

            // ──────────────────────────────────────────
            // Data dari Laravel — sudah diagregat per bulan di controller
            // $dataMasukPerBulan   : koleksi {bulan: "YYYY-MM", total: N}
            // $dataTerbitSHPerBulan: koleksi {bulan: "YYYY-MM", total: N}
            // ──────────────────────────────────────────
            const rawMasuk = @json($dataMasukPerBulan);
            const rawTerbit = @json($dataTerbitSHPerBulan);

            const labelsMasuk = rawMasuk.map(d => formatLabel(d.bulan));
            const valuesMasuk = rawMasuk.map(d => d.total);
            const labelsTerbit = rawTerbit.map(d => formatLabel(d.bulan));
            const valuesTerbit = rawTerbit.map(d => d.total);

            // ──────────────────────────────────────────
            // Chart: Data Masuk Per Bulan
            // ──────────────────────────────────────────
            const ctxMasuk = document.getElementById('chartDataMasuk').getContext('2d');
            const gradMasuk = ctxMasuk.createLinearGradient(0, 0, 0, 260);
            gradMasuk.addColorStop(0, 'rgba(79,142,247,0.3)');
            gradMasuk.addColorStop(1, 'rgba(79,142,247,0)');

            new Chart(ctxMasuk, {
                type: 'line',
                data: {
                    labels: labelsMasuk.length ? labelsMasuk : ['—'],
                    datasets: [{
                        label: 'Data Masuk',
                        data: valuesMasuk.length ? valuesMasuk : [0],
                        borderColor: '#4f8ef7',
                        borderWidth: 2.5,
                        backgroundColor: gradMasuk,
                        pointBackgroundColor: '#4f8ef7',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        tension: 0.42,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#ffffff',
                            borderColor: '#e8ecf4',
                            borderWidth: 1,
                            titleColor: '#9aa0b8',
                            bodyColor: '#1a2040',
                            padding: 12,
                            callbacks: {
                                title: items => items[0].label,
                                label: item => ` Jumlah: ${item.raw} data`,
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: gridColor,
                                drawBorder: false
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 30,
                                maxTicksLimit: 12
                            },
                        },
                        y: {
                            grid: {
                                color: gridColor,
                                drawBorder: false
                            },
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            },
                        }
                    }
                }
            });

            // ──────────────────────────────────────────
            // Chart: Terbit SH Per Bulan
            // ──────────────────────────────────────────
            const ctxSH = document.getElementById('chartTerbitSH').getContext('2d');
            const gradSH = ctxSH.createLinearGradient(0, 0, 0, 260);
            gradSH.addColorStop(0, 'rgba(16,217,138,0.3)');
            gradSH.addColorStop(1, 'rgba(16,217,138,0)');

            new Chart(ctxSH, {
                type: 'line',
                data: {
                    labels: labelsTerbit.length ? labelsTerbit : ['—'],
                    datasets: [{
                        label: 'Terbit SH',
                        data: valuesTerbit.length ? valuesTerbit : [0],
                        borderColor: '#10d98a',
                        borderWidth: 2.5,
                        backgroundColor: gradSH,
                        pointBackgroundColor: '#10d98a',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        tension: 0.42,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#ffffff',
                            borderColor: '#e8ecf4',
                            borderWidth: 1,
                            titleColor: '#9aa0b8',
                            bodyColor: '#1a2040',
                            padding: 12,
                            callbacks: {
                                title: items => items[0].label,
                                label: item => ` Jumlah: ${item.raw} terbit`,
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: gridColor,
                                drawBorder: false
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 30,
                                maxTicksLimit: 12
                            },
                        },
                        y: {
                            grid: {
                                color: gridColor,
                                drawBorder: false
                            },
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            },
                        }
                    }
                }
            });

        });
    </script>
@endsection
