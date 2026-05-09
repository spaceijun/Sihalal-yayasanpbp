@extends('layouts.app')

@section('template_title')
    Server Info
@endsection

@section('content')
    {{-- ─── HEADER BANNER ─── --}}
    <div class="si-hero">
        <div class="si-hero-glow"></div>
        <div class="si-hero-inner">
            <div class="si-hero-icon">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="2" width="20" height="8" rx="2" />
                    <rect x="2" y="14" width="20" height="8" rx="2" />
                    <line x1="6" y1="6" x2="6.01" y2="6" />
                    <line x1="6" y1="18" x2="6.01" y2="18" />
                </svg>
            </div>
            <div>
                <h1 class="si-hero-title">Server Information</h1>
                <p class="si-hero-sub">Real-time system metrics &amp; resource monitoring</p>
            </div>
        </div>
        <div class="si-hero-badge">
            <span class="live-dot"></span>
            <span id="lastUpdate">--:--:--</span>
        </div>
    </div>

    {{-- ─── ROW 1: CPU + MEMORY + DISK ─── --}}
    <div class="section-label">System Resources</div>
    <div class="row g-4 mb-4">

        {{-- CPU Gauge --}}
        <div class="col-xl-4 col-md-6">
            <div class="res-card">
                <div class="res-card-header">
                    <div class="res-icon rc-blue">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <rect x="4" y="4" width="16" height="16" rx="2" />
                            <rect x="9" y="9" width="6" height="6" />
                            <line x1="9" y1="1" x2="9" y2="4" />
                            <line x1="15" y1="1" x2="15" y2="4" />
                            <line x1="9" y1="20" x2="9" y2="23" />
                            <line x1="15" y1="20" x2="15" y2="23" />
                            <line x1="20" y1="9" x2="23" y2="9" />
                            <line x1="20" y1="14" x2="23" y2="14" />
                            <line x1="1" y1="9" x2="4" y2="9" />
                            <line x1="1" y1="14" x2="4" y2="14" />
                        </svg>
                    </div>
                    <div>
                        <div class="res-label">CPU Usage</div>
                        <div class="res-sublabel" id="cpuModel">Loading…</div>
                    </div>
                    <div class="res-pct rc-blue-text" id="cpuPct">—</div>
                </div>
                <div class="gauge-wrap">
                    <svg class="gauge-svg" viewBox="0 0 200 120">
                        <path class="gauge-bg" d="M20,100 A80,80 0 0,1 180,100" />
                        <path class="gauge-fill gauge-blue" id="cpuArc" d="M20,100 A80,80 0 0,1 180,100"
                            stroke-dasharray="0 251.2" />
                        <text class="gauge-num" id="cpuNum" x="100" y="95" text-anchor="middle">0%</text>
                        <text class="gauge-unit" x="100" y="115" text-anchor="middle">Processor Load</text>
                    </svg>
                </div>
                <div class="res-meta-row">
                    <div class="res-meta-item">
                        <span class="rm-label">Cores</span>
                        <span class="rm-val" id="cpuCores">—</span>
                    </div>
                    <div class="res-meta-item">
                        <span class="rm-label">Freq</span>
                        <span class="rm-val" id="cpuFreq">—</span>
                    </div>
                    <div class="res-meta-item">
                        <span class="rm-label">Temp</span>
                        <span class="rm-val" id="cpuTemp">—</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Memory --}}
        <div class="col-xl-4 col-md-6">
            <div class="res-card">
                <div class="res-card-header">
                    <div class="res-icon rc-violet">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M6 19v-3" />
                            <path d="M10 19v-3" />
                            <path d="M14 19v-3" />
                            <path d="M18 19v-3" />
                            <path d="M8 11V9" />
                            <path d="M16 11V9" />
                            <path d="M12 11V9" />
                            <path d="M2 15h20" />
                            <path
                                d="M2 7a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v1.1a2 2 0 0 0 0 3.837V17a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-5.1a2 2 0 0 0 0-3.837z" />
                        </svg>
                    </div>
                    <div>
                        <div class="res-label">Memory (RAM)</div>
                        <div class="res-sublabel" id="memUsedFree">Loading…</div>
                    </div>
                    <div class="res-pct rc-violet-text" id="memPct">—</div>
                </div>
                <div class="gauge-wrap">
                    <svg class="gauge-svg" viewBox="0 0 200 120">
                        <path class="gauge-bg" d="M20,100 A80,80 0 0,1 180,100" />
                        <path class="gauge-fill gauge-violet" id="memArc" d="M20,100 A80,80 0 0,1 180,100"
                            stroke-dasharray="0 251.2" />
                        <text class="gauge-num" id="memNum" x="100" y="95" text-anchor="middle">0%</text>
                        <text class="gauge-unit" x="100" y="115" text-anchor="middle">Memory Usage</text>
                    </svg>
                </div>
                <div class="res-meta-row">
                    <div class="res-meta-item">
                        <span class="rm-label">Total</span>
                        <span class="rm-val" id="memTotal">—</span>
                    </div>
                    <div class="res-meta-item">
                        <span class="rm-label">Used</span>
                        <span class="rm-val" id="memUsed">—</span>
                    </div>
                    <div class="res-meta-item">
                        <span class="rm-label">Available</span>
                        <span class="rm-val" id="memAvail">—</span>
                    </div>
                </div>
                <div class="swap-row">
                    <span class="swap-label">Swap</span>
                    <div class="swap-bar-track">
                        <div class="swap-bar-fill" id="swapBar"></div>
                    </div>
                    <span class="swap-pct" id="swapPct">0%</span>
                </div>
            </div>
        </div>

        {{-- Disk --}}
        <div class="col-xl-4 col-md-6">
            <div class="res-card">
                <div class="res-card-header">
                    <div class="res-icon rc-emerald">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <ellipse cx="12" cy="5" rx="9" ry="3" />
                            <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3" />
                            <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5" />
                        </svg>
                    </div>
                    <div>
                        <div class="res-label">Disk Storage</div>
                        <div class="res-sublabel" id="diskUsedFree">Loading…</div>
                    </div>
                    <div class="res-pct rc-emerald-text" id="diskPct">—</div>
                </div>
                <div class="gauge-wrap">
                    <svg class="gauge-svg" viewBox="0 0 200 120">
                        <path class="gauge-bg" d="M20,100 A80,80 0 0,1 180,100" />
                        <path class="gauge-fill gauge-emerald" id="diskArc" d="M20,100 A80,80 0 0,1 180,100"
                            stroke-dasharray="0 251.2" />
                        <text class="gauge-num" id="diskNum" x="100" y="95" text-anchor="middle">0%</text>
                        <text class="gauge-unit" x="100" y="115" text-anchor="middle">Disk Usage</text>
                    </svg>
                </div>
                <div class="res-meta-row">
                    <div class="res-meta-item">
                        <span class="rm-label">Total</span>
                        <span class="rm-val" id="diskTotal">—</span>
                    </div>
                    <div class="res-meta-item">
                        <span class="rm-label">Used</span>
                        <span class="rm-val" id="diskUsed">—</span>
                    </div>
                    <div class="res-meta-item">
                        <span class="rm-label">Free</span>
                        <span class="rm-val" id="diskFree">—</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── ROW 2: CPU REAL-TIME CHART + PER-CORE ─── --}}
    <div class="section-label">CPU Usage — Real-time Processor Load</div>
    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="chart-card si-chart-card">
                <div class="chart-header">
                    <div>
                        <div class="chart-title">CPU Load — Live</div>
                        <div class="chart-subtitle">Sampling setiap 2 detik, 60 titik terakhir</div>
                    </div>
                    <div class="d-flex gap-3 align-items-center">
                        <div class="legend-chip lc-blue">CPU Total</div>
                        <div id="cpuSparkVal" class="spark-val">0%</div>
                    </div>
                </div>
                <div class="chart-body" style="padding:12px 20px 20px">
                    <canvas id="cpuRealtimeChart" height="180"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="chart-card si-chart-card h-100">
                <div class="chart-header">
                    <div>
                        <div class="chart-title">Per-Core Load</div>
                        <div class="chart-subtitle">Snapshot saat ini</div>
                    </div>
                </div>
                <div class="chart-body" id="perCoreWrap" style="padding:16px 20px 20px">
                    <div class="core-loading">Memuat data core…</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── ROW 3: LOAD AVG + NETWORK ─── --}}
    <div class="row g-4 mb-4">
        {{-- Load Average --}}
        <div class="col-xl-4 col-md-6">
            <div class="info-card">
                <div class="ic-header">
                    <div class="res-icon rc-amber">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                        </svg>
                    </div>
                    <span class="ic-title">System Load Average</span>
                </div>
                <div class="load-grid">
                    <div class="load-item">
                        <div class="load-period">1 min</div>
                        <div class="load-val" id="load1">—</div>
                    </div>
                    <div class="load-item">
                        <div class="load-period">5 min</div>
                        <div class="load-val" id="load5">—</div>
                    </div>
                    <div class="load-item">
                        <div class="load-period">15 min</div>
                        <div class="load-val" id="load15">—</div>
                    </div>
                </div>
                <div class="uptime-row">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                    Uptime: <strong id="uptime">—</strong>
                </div>
            </div>
        </div>

        {{-- Network --}}
        <div class="col-xl-4 col-md-6">
            <div class="info-card">
                <div class="ic-header">
                    <div class="res-icon rc-cyan">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M5 12.55a11 11 0 0 1 14.08 0" />
                            <path d="M1.42 9a16 16 0 0 1 21.16 0" />
                            <path d="M8.53 16.11a6 6 0 0 1 6.95 0" />
                            <line x1="12" y1="20" x2="12.01" y2="20" />
                        </svg>
                    </div>
                    <span class="ic-title">Network Transfer (Total)</span>
                </div>
                <div class="net-grid">
                    <div class="net-item">
                        <div class="net-icon net-rx">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <polyline points="19 12 12 19 5 12" />
                            </svg>
                        </div>
                        <div>
                            <div class="net-label">Received (RX)</div>
                            <div class="net-val" id="netRx">—</div>
                        </div>
                    </div>
                    <div class="net-item">
                        <div class="net-icon net-tx">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <line x1="12" y1="19" x2="12" y2="5" />
                                <polyline points="5 12 12 5 19 12" />
                            </svg>
                        </div>
                        <div>
                            <div class="net-label">Transmitted (TX)</div>
                            <div class="net-val" id="netTx">—</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── ROW 4: SERVER INFO + PHP INFO ─── --}}
    <div class="section-label">Server Info</div>
    <div class="row g-4 mb-4">
        <div class="col-xl-6">
            <div class="info-card">
                <div class="ic-header">
                    <div class="res-icon rc-indigo">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <rect x="2" y="2" width="20" height="8" rx="2" />
                            <rect x="2" y="14" width="20" height="8" rx="2" />
                            <line x1="6" y1="6" x2="6.01" y2="6" />
                            <line x1="6" y1="18" x2="6.01" y2="18" />
                        </svg>
                    </div>
                    <span class="ic-title">Server &amp; OS</span>
                </div>
                <div class="kv-table" id="serverTable">
                    <div class="kv-row"><span class="kv-k">Operating System</span><span class="kv-v"
                            id="srvOs">—</span></div>
                    <div class="kv-row"><span class="kv-k">Kernel</span><span class="kv-v" id="srvKernel">—</span>
                    </div>
                    <div class="kv-row"><span class="kv-k">Architecture</span><span class="kv-v"
                            id="srvArch">—</span></div>
                    <div class="kv-row"><span class="kv-k">Hostname</span><span class="kv-v" id="srvHost">—</span>
                    </div>
                    <div class="kv-row"><span class="kv-k">Domain</span><span class="kv-v" id="srvDomain">—</span>
                    </div>
                    <div class="kv-row"><span class="kv-k">Server IP</span><span class="kv-v"
                            id="srvIp">—</span></div>
                    <div class="kv-row"><span class="kv-k">Web Server</span><span class="kv-v"
                            id="srvWeb">—</span></div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="info-card">
                <div class="ic-header">
                    <div class="res-icon rc-rose">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z" />
                            <polyline points="13 2 13 9 20 9" />
                        </svg>
                    </div>
                    <span class="ic-title">PHP &amp; Runtime</span>
                </div>
                <div class="kv-table">
                    <div class="kv-row"><span class="kv-k">PHP Version</span><span class="kv-v kv-highlight"
                            id="phpVer">—</span></div>
                    <div class="kv-row"><span class="kv-k">Zend Engine</span><span class="kv-v"
                            id="phpZend">—</span></div>
                    <div class="kv-row"><span class="kv-k">SAPI</span><span class="kv-v" id="phpSapi">—</span>
                    </div>
                    <div class="kv-row"><span class="kv-k">Memory Limit</span><span class="kv-v"
                            id="phpMem">—</span></div>
                    <div class="kv-row"><span class="kv-k">Max Execution</span><span class="kv-v"
                            id="phpExec">—</span></div>
                    <div class="kv-row"><span class="kv-k">Upload Max</span><span class="kv-v"
                            id="phpUpload">—</span></div>
                    <div class="kv-row"><span class="kv-k">Extensions</span><span class="kv-v"
                            id="phpExt">—</span></div>
                </div>
            </div>
        </div>
    </div>


    {{-- ─── STACK VERSIONS ─── --}}
    <div class="section-label">Stack Versions</div>
    <div class="row g-4 mb-4" id="stackVersionsRow">
        {{-- Cards will be rendered by JS --}}
        <div class="col-xl-3 col-md-6">
            <div class="stack-card" id="stack-php">
                <div class="stack-icon-wrap" style="background:rgba(119,123,180,0.12)">
                    <svg width="28" height="28" viewBox="0 0 50 50" fill="#777BB4">
                        <path
                            d="M25 3C12.85 3 3 13.3 3 26s9.85 23 22 23 22-10.3 22-23S37.15 3 25 3zm-4.4 30.3H18l.9-5h-3.5l-.9 5H12l2.4-13.6h2.5l-.9 5h3.5l.9-5h2.5L20.6 33.3zm9.6 0h-2.3l.2-1.2c-.7.9-1.6 1.4-2.8 1.4-1.9 0-3-1.4-2.5-4l.9-5.1h2.5l-.9 4.8c-.2 1.3.2 2 1.1 2s1.7-.7 2-2l.9-4.8h2.5l-1.6 8.9zm7.9-6.8h-1.6l-.9 5h-2.5l.9-5h-1l.4-2h1l.3-1.6c.4-2 1.6-2.9 3.5-2.9.5 0 1 .1 1.4.2l-.5 2.1c-.2-.1-.5-.1-.8-.1-.6 0-1 .3-1.1 1l-.2 1.3h1.6l-.5 2z" />
                    </svg>
                </div>
                <div class="stack-label" id="stack-php-label">PHP</div>
                <div class="stack-version" id="stack-php-version">
                    <span class="sv-loading"></span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stack-card" id="stack-laravel">
                <div class="stack-icon-wrap" style="background:rgba(255,45,32,0.1)">
                    <svg width="28" height="28" viewBox="0 0 50 50" fill="#FF2D20">
                        <path
                            d="M49.626 11.564a.809.809 0 0 1 .028.209v10.972a.8.8 0 0 1-.402.694l-9.209 5.302-.032.018v10.509a.8.8 0 0 1-.402.694L20.42 49.811a.752.752 0 0 1-.119.048.688.688 0 0 1-.128.02.8.8 0 0 1-.2-.02.725.725 0 0 1-.118-.048L.402 39.963A.8.8 0 0 1 0 39.27V7.558a.809.809 0 0 1 .028-.209.786.786 0 0 1 .056-.16.769.769 0 0 1 .086-.134.8.8 0 0 1 .114-.115 1 1 0 0 1 .1-.06L9.572.101a.8.8 0 0 1 .8 0l9.188 5.298a.998.998 0 0 1 .1.06.8.8 0 0 1 .113.115.76.76 0 0 1 .087.134.786.786 0 0 1 .056.16.809.809 0 0 1 .028.209v20.7l8.008-4.614V11.772a.809.809 0 0 1 .028-.208.786.786 0 0 1 .056-.161.755.755 0 0 1 .087-.133.8.8 0 0 1 .114-.115 1 1 0 0 1 .1-.06l9.188-5.298a.8.8 0 0 1 .8 0l9.191 5.298a.999.999 0 0 1 .1.06.8.8 0 0 1 .114.115.755.755 0 0 1 .087.133.786.786 0 0 1 .056.161zm-1.591 10.753v-9.457l-3.364 1.936-4.645 2.677v9.457l8.009-4.613zm-9.61 16.533v-9.458l-4.571 2.625-13.059 7.483v9.535zm-36.84-31.15v31.011l17.618 10.157v-9.534l-9.208-5.229-.02-.013-.019-.015a.802.802 0 0 1-.114-.115.755.755 0 0 1-.087-.134.786.786 0 0 1-.056-.16.809.809 0 0 1-.028-.209V17.383l-4.643-2.677-3.443-1.976zm8.81-5.994L2.395 7.719l8.007 4.614 8.005-4.614-8.005-4.614zm4.164 28.764l4.645-2.678V7.719l-3.364 1.936-4.645 2.678v20.688l3.364-1.936zm24.667-23.325l-8.006 4.614 8.006 4.613 8.005-4.613-8.005-4.614zm-.801 10.605l-4.646-2.678-3.363-1.936v9.457l4.645 2.678 3.364 1.936v-9.457zm-18.011 3.269l13.058-7.512-6.53-3.764-13.056 7.512 6.528 3.764z" />
                    </svg>
                </div>
                <div class="stack-label" id="stack-laravel-label">Laravel</div>
                <div class="stack-version" id="stack-laravel-version">
                    <span class="sv-loading"></span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stack-card" id="stack-mysql">
                <div class="stack-icon-wrap" style="background:rgba(0,117,143,0.1)">
                    <svg width="28" height="28" viewBox="0 0 50 50">
                        <path fill="#00758F" d="M25 3C12.85 3 3 13.3 3 26s9.85 23 22 23 22-10.3 22-23S37.15 3 25 3z" />
                        <path fill="#fff" d="M14 19h4v12h-4zm6 0h4v5l3-5h4.5l-3.5 5.5L32 31h-4.5l-3-5v5H18V19h2z" />
                    </svg>
                </div>
                <div class="stack-label" id="stack-mysql-label">MySQL</div>
                <div class="stack-version" id="stack-mysql-version">
                    <span class="sv-loading"></span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stack-card" id="stack-server">
                <div class="stack-icon-wrap" style="background:rgba(210,33,40,0.1)">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D22128"
                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="2" width="20" height="8" rx="2" />
                        <rect x="2" y="14" width="20" height="8" rx="2" />
                        <line x1="6" y1="6" x2="6.01" y2="6" />
                        <line x1="6" y1="18" x2="6.01" y2="18" />
                    </svg>
                </div>
                <div class="stack-label" id="stack-server-label">Web Server</div>
                <div class="stack-version" id="stack-server-version">
                    <span class="sv-loading"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- ─────────────────────── STYLES ─────────────────────── --}}
    <style>
        :root {
            --bg-base: #f3f5fb;
            --bg-card: #ffffff;
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
            --accent-indigo: #4f46e5;
            --shadow-card: 0 2px 12px rgba(80, 100, 160, .08), 0 1px 3px rgba(80, 100, 160, .06);
            --shadow-hover: 0 8px 28px rgba(80, 100, 160, .14);
            --radius: 16px;
        }

        body,
        .page-content,
        .main-content {
            background: var(--bg-base) !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }

        /* ── HERO ─────────────────────────── */
        .si-hero {
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

        .si-hero-glow {
            position: absolute;
            top: -40px;
            right: -40px;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(79, 70, 229, .07) 0%, transparent 70%);
            pointer-events: none;
        }

        .si-hero-inner {
            display: flex;
            align-items: center;
            gap: 14px;
            flex: 1;
        }

        .si-hero-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--accent-indigo), var(--accent-cyan));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            box-shadow: 0 4px 14px rgba(99, 102, 241, .35);
            flex-shrink: 0;
        }

        .si-hero-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-primary);
            margin: 0;
        }

        .si-hero-sub {
            font-size: 13px;
            color: var(--text-muted);
            margin: 0;
        }

        .si-hero-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, .75);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            white-space: nowrap;
        }

        .live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, .4);
            animation: livePulse 1.6s infinite;
        }

        @keyframes livePulse {
            0% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, .4);
            }

            70% {
                box-shadow: 0 0 0 7px rgba(16, 185, 129, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        /* ── SECTION LABEL ─────────────────── */
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

        /* ── RESOURCE CARD (with gauge) ───── */
        .res-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow-card);
            transition: transform .25s, box-shadow .25s, border-color .25s;
            height: 100%;
        }

        .res-card:hover {
            transform: translateY(-3px);
            border-color: var(--border-bright);
            box-shadow: var(--shadow-hover);
        }

        .res-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 4px;
        }

        .res-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .rc-blue {
            background: rgba(59, 124, 244, .1);
            color: var(--accent-blue);
        }

        .rc-violet {
            background: rgba(124, 58, 237, .1);
            color: var(--accent-violet);
        }

        .rc-emerald {
            background: rgba(5, 150, 105, .1);
            color: var(--accent-emerald);
        }

        .rc-amber {
            background: rgba(217, 119, 6, .1);
            color: var(--accent-amber);
        }

        .rc-cyan {
            background: rgba(6, 182, 212, .1);
            color: var(--accent-cyan);
        }

        .rc-indigo {
            background: rgba(79, 70, 229, .1);
            color: var(--accent-indigo);
        }

        .rc-rose {
            background: rgba(225, 29, 72, .1);
            color: var(--accent-rose);
        }

        .res-label {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .res-sublabel {
            font-size: 11px;
            color: var(--text-muted);
            max-width: 160px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .res-pct {
            margin-left: auto;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 22px;
            font-weight: 700;
        }

        .rc-blue-text {
            color: var(--accent-blue);
        }

        .rc-violet-text {
            color: var(--accent-violet);
        }

        .rc-emerald-text {
            color: var(--accent-emerald);
        }

        /* ── GAUGE SVG ─────────────────────── */
        .gauge-wrap {
            padding: 8px 0 4px;
        }

        .gauge-svg {
            width: 100%;
            max-width: 200px;
            display: block;
            margin: 0 auto;
        }

        .gauge-bg {
            fill: none;
            stroke: #eef0f8;
            stroke-width: 12;
            stroke-linecap: round;
        }

        .gauge-fill {
            fill: none;
            stroke-width: 12;
            stroke-linecap: round;
            stroke-dasharray: 0 251.2;
            transition: stroke-dasharray 0.8s cubic-bezier(.22, 1, .36, 1);
        }

        .gauge-blue {
            stroke: var(--accent-blue);
        }

        .gauge-violet {
            stroke: var(--accent-violet);
        }

        .gauge-emerald {
            stroke: var(--accent-emerald);
        }

        .gauge-num {
            fill: var(--text-primary);
            font-family: 'Space Grotesk', sans-serif;
            font-size: 22px;
            font-weight: 700;
        }

        .gauge-unit {
            fill: var(--text-muted);
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        /* ── RES META ROW ──────────────────── */
        .res-meta-row {
            display: flex;
            gap: 4px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid var(--border);
        }

        .res-meta-item {
            flex: 1;
            text-align: center;
        }

        .rm-label {
            display: block;
            font-size: 10px;
            color: var(--text-muted);
            font-weight: 600;
            letter-spacing: .8px;
            text-transform: uppercase;
        }

        .rm-val {
            display: block;
            font-size: 12px;
            color: var(--text-primary);
            font-weight: 700;
            margin-top: 2px;
        }

        /* ── SWAP BAR ──────────────────────── */
        .swap-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid var(--border);
        }

        .swap-label {
            font-size: 10px;
            font-weight: 700;
            color: var(--text-muted);
            letter-spacing: 1px;
            text-transform: uppercase;
            width: 36px;
        }

        .swap-bar-track {
            flex: 1;
            height: 6px;
            border-radius: 6px;
            background: var(--border);
            overflow: hidden;
        }

        .swap-bar-fill {
            height: 100%;
            border-radius: 6px;
            background: var(--accent-violet);
            transition: width 1s ease;
            width: 0%;
        }

        .swap-pct {
            font-size: 11px;
            font-weight: 700;
            color: var(--accent-violet);
            width: 34px;
            text-align: right;
        }

        /* ── CHART CARD ────────────────────── */
        .chart-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-card);
            overflow: hidden;
        }

        .si-chart-card {
            display: flex;
            flex-direction: column;
        }

        .chart-header {
            padding: 18px 20px 0;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .chart-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .chart-subtitle {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .chart-body {
            flex: 1;
        }

        .legend-chip {
            font-size: 11px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            border: 1px solid;
        }

        .lc-blue {
            background: rgba(59, 124, 244, .08);
            color: var(--accent-blue);
            border-color: rgba(59, 124, 244, .2);
        }

        .spark-val {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--accent-blue);
        }

        /* ── PER-CORE ───────────────────────── */
        .core-item {
            margin-bottom: 10px;
        }

        .core-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }

        .core-lbl {
            font-size: 10px;
            font-weight: 700;
            color: var(--text-muted);
            width: 48px;
        }

        .core-pct {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-primary);
            margin-left: auto;
            min-width: 34px;
            text-align: right;
        }

        .core-track {
            flex: 1;
            height: 6px;
            border-radius: 6px;
            background: var(--border);
            overflow: hidden;
        }

        .core-fill {
            height: 100%;
            border-radius: 6px;
            background: linear-gradient(90deg, var(--accent-blue), var(--accent-cyan));
            transition: width .7s cubic-bezier(.22, 1, .36, 1);
            width: 0%;
        }

        .core-loading {
            color: var(--text-muted);
            font-size: 12px;
            text-align: center;
            padding: 40px 0;
        }

        /* ── INFO CARD ─────────────────────── */
        .info-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow-card);
            height: 100%;
            transition: transform .25s, box-shadow .25s;
        }

        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .ic-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--border);
        }

        .ic-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-primary);
        }

        /* ── LOAD GRID ─────────────────────── */
        .load-grid {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
        }

        .load-item {
            flex: 1;
            background: #f7f9fd;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 8px;
            text-align: center;
        }

        .load-period {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            color: var(--text-muted);
            text-transform: uppercase;
        }

        .load-val {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
            margin-top: 4px;
        }

        .uptime-row {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--text-muted);
            padding-top: 14px;
            border-top: 1px solid var(--border);
        }

        .uptime-row strong {
            color: var(--text-secondary);
        }

        /* ── NETWORK ───────────────────────── */
        .net-grid {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .net-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .net-icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .net-rx {
            background: rgba(5, 150, 105, .1);
            color: var(--accent-emerald);
        }

        .net-tx {
            background: rgba(59, 124, 244, .1);
            color: var(--accent-blue);
        }

        .net-label {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .net-val {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
        }

        /* ── KV TABLE ──────────────────────── */
        .kv-table {
            display: flex;
            flex-direction: column;
        }

        .kv-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 9px 0;
            border-bottom: 1px solid #f3f5fb;
            gap: 12px;
        }

        .kv-row:last-child {
            border-bottom: none;
        }

        .kv-k {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 600;
            flex-shrink: 0;
        }

        .kv-v {
            font-size: 12px;
            color: var(--text-primary);
            font-weight: 600;
            text-align: right;
            word-break: break-all;
        }

        .kv-highlight {
            background: linear-gradient(90deg, var(--accent-indigo), var(--accent-cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 14px;
            font-weight: 800;
        }


        /* ── STACK VERSION CARDS ─────────────────────────────────── */
        .stack-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px 20px;
            box-shadow: var(--shadow-card);
            text-align: center;
            transition: transform .25s, box-shadow .25s, border-color .25s;
            height: 100%;
        }

        .stack-card:hover {
            transform: translateY(-3px);
            border-color: var(--border-bright);
            box-shadow: var(--shadow-hover);
        }

        .stack-icon-wrap {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .stack-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .stack-version {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.5px;
            min-height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sv-loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid var(--border);
            border-top-color: var(--accent-blue);
            border-radius: 50%;
            animation: svSpin .8s linear infinite;
        }

        @keyframes svSpin {
            to {
                transform: rotate(360deg);
            }
        }

        .stack-version-badge {
            display: inline-block;
            padding: 2px 10px 3px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
        }

        /* ── SCROLLBAR ─────────────────────── */
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

        .card {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }
    </style>

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

    <script>
        (function() {
            'use strict';

            // ── CONFIG ─────────────────────────────────────────────
            const API_URL = '{{ route('superadmin.server-info.realtime') }}';
            const INTERVAL_MS = 2000; // polling setiap 2 detik
            const HISTORY_LEN = 60; // titik pada grafik
            const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

            // ── GAUGE MATH ──────────────────────────────────────────
            // Arc path: M20,100 A80,80 0 0,1 180,100 → circumference ≈ 251.2 (setengah lingkaran)
            const ARC_LEN = 251.2;

            function setGauge(arcId, pct) {
                const el = document.getElementById(arcId);
                if (!el) return;
                const filled = Math.min(Math.max(pct, 0), 100) / 100 * ARC_LEN;
                el.style.strokeDasharray = `${filled} ${ARC_LEN}`;
            }

            // ── CPU REALTIME CHART ───────────────────────────────────
            const cpuHistory = Array(HISTORY_LEN).fill(0);
            const cpuLabels = Array(HISTORY_LEN).fill('');
            Chart.defaults.color = '#9aa0b8';
            Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
            Chart.defaults.font.size = 11;

            const ctxCpu = document.getElementById('cpuRealtimeChart')?.getContext('2d');
            let cpuChart = null;

            if (ctxCpu) {
                const grad = ctxCpu.createLinearGradient(0, 0, 0, 180);
                grad.addColorStop(0, 'rgba(59,124,244,.25)');
                grad.addColorStop(1, 'rgba(59,124,244,0)');

                cpuChart = new Chart(ctxCpu, {
                    type: 'line',
                    data: {
                        labels: cpuLabels,
                        datasets: [{
                            label: 'CPU %',
                            data: cpuHistory,
                            borderColor: '#3b7cf4',
                            borderWidth: 2,
                            backgroundColor: grad,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                            tension: 0.4,
                            fill: true,
                        }]
                    },
                    options: {
                        responsive: true,
                        animation: {
                            duration: 300
                        },
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#fff',
                                borderColor: '#e8ecf4',
                                borderWidth: 1,
                                titleColor: '#9aa0b8',
                                bodyColor: '#1a2040',
                                padding: 10,
                                callbacks: {
                                    label: item => ` CPU: ${item.raw}%`
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    color: 'rgba(80,100,160,.06)'
                                },
                                ticks: {
                                    maxTicksLimit: 8,
                                    maxRotation: 0
                                }
                            },
                            y: {
                                min: 0,
                                max: 100,
                                grid: {
                                    color: 'rgba(80,100,160,.06)'
                                },
                                ticks: {
                                    callback: v => v + '%',
                                    stepSize: 25
                                }
                            }
                        }
                    }
                });
            }

            // ── PER-CORE RENDERER ────────────────────────────────────
            function renderCores(perCore) {
                const wrap = document.getElementById('perCoreWrap');
                if (!wrap || !perCore) return;

                const ids = Object.keys(perCore);
                if (!ids.length) return;

                wrap.innerHTML = ids.map(id => {
                    const pct = perCore[id] ?? 0;
                    return `
                <div class="core-item">
                    <div class="core-row">
                        <span class="core-lbl">Core ${parseInt(id)}</span>
                        <div class="core-track"><div class="core-fill" style="width:${pct}%"></div></div>
                        <span class="core-pct">${pct}%</span>
                    </div>
                </div>`;
                }).join('');
            }

            // ── DOM HELPERS ──────────────────────────────────────────
            function txt(id, val) {
                const el = document.getElementById(id);
                if (el) el.textContent = val ?? '—';
            }

            function pct(id, val) {
                txt(id, val + '%');
            }

            // ── FETCH & RENDER ───────────────────────────────────────
            async function fetchAndRender() {
                try {
                    const res = await fetch(API_URL, {
                        headers: {
                            'X-CSRF-TOKEN': CSRF,
                            'Accept': 'application/json'
                        }
                    });
                    if (!res.ok) return;
                    const d = await res.json();

                    // ─ Timestamp
                    txt('lastUpdate', d.timestamp);

                    // ─ CPU
                    const cpu = d.cpu ?? {};
                    pct('cpuPct', cpu.percent ?? 0);
                    txt('cpuNum', (cpu.percent ?? 0) + '%');
                    txt('cpuModel', cpu.model ?? '—');
                    txt('cpuCores', cpu.cores ?? '—');
                    txt('cpuFreq', cpu.freq ?? '—');
                    txt('cpuTemp', cpu.temp ? cpu.temp + '°C' : 'N/A');
                    setGauge('cpuArc', cpu.percent ?? 0);

                    // CPU history chart
                    const now = new Date();
                    const label = now.getHours().toString().padStart(2, '0') + ':' +
                        now.getMinutes().toString().padStart(2, '0') + ':' +
                        now.getSeconds().toString().padStart(2, '0');
                    cpuHistory.push(cpu.percent ?? 0);
                    cpuHistory.shift();
                    cpuLabels.push(label);
                    cpuLabels.shift();
                    if (cpuChart) cpuChart.update('none');
                    txt('cpuSparkVal', (cpu.percent ?? 0) + '%');

                    // Per-core
                    renderCores(cpu.per_core ?? {});

                    // ─ Memory
                    const mem = d.memory ?? {};
                    pct('memPct', mem.percent ?? 0);
                    txt('memNum', (mem.percent ?? 0) + '%');
                    txt('memUsedFree', `${mem.used ?? '—'} / ${mem.total ?? '—'}`);
                    txt('memTotal', mem.total ?? '—');
                    txt('memUsed', mem.used ?? '—');
                    txt('memAvail', mem.available ?? '—');
                    setGauge('memArc', mem.percent ?? 0);
                    const swapBar = document.getElementById('swapBar');
                    if (swapBar) swapBar.style.width = (mem.swap_pct ?? 0) + '%';
                    txt('swapPct', (mem.swap_pct ?? 0) + '%');

                    // ─ Disk
                    const disk = d.disk ?? {};
                    pct('diskPct', disk.percent ?? 0);
                    txt('diskNum', (disk.percent ?? 0) + '%');
                    txt('diskUsedFree', `${disk.used ?? '—'} / ${disk.total ?? '—'}`);
                    txt('diskTotal', disk.total ?? '—');
                    txt('diskUsed', disk.used ?? '—');
                    txt('diskFree', disk.free ?? '—');
                    setGauge('diskArc', disk.percent ?? 0);

                    // ─ Load
                    const load = d.load ?? {};
                    txt('load1', load['1'] ?? '—');
                    txt('load5', load['5'] ?? '—');
                    txt('load15', load['15'] ?? '—');
                    txt('uptime', d.uptime ?? '—');

                    // ─ Network
                    const net = d.network ?? {};
                    txt('netRx', net.rx ?? '—');
                    txt('netTx', net.tx ?? '—');

                    // ─ Server
                    const srv = d.server ?? {};
                    txt('srvOs', srv.os ?? '—');
                    txt('srvKernel', srv.kernel ?? '—');
                    txt('srvArch', srv.arch ?? '—');
                    txt('srvHost', srv.hostname ?? '—');
                    txt('srvDomain', srv.domain ?? '—');
                    txt('srvIp', srv.ip ?? '—');
                    txt('srvWeb', srv.web ?? '—');

                    // ─ PHP
                    const php = d.php ?? {};
                    txt('phpVer', php.version ?? '—');
                    txt('phpZend', php.zend ?? '—');
                    txt('phpSapi', php.sapi ?? '—');
                    txt('phpMem', php.memory_limit ?? '—');
                    txt('phpExec', php.max_exec ?? '—');
                    txt('phpUpload', php.upload_max ?? '—');
                    txt('phpExt', php.extensions ?? '—');


                    // ─ Stack Versions
                    const stack = d.stack ?? {};
                    const stackMap = [{
                            key: 'php',
                            labelId: 'stack-php-label',
                            verId: 'stack-php-version'
                        },
                        {
                            key: 'laravel',
                            labelId: 'stack-laravel-label',
                            verId: 'stack-laravel-version'
                        },
                        {
                            key: 'mysql',
                            labelId: 'stack-mysql-label',
                            verId: 'stack-mysql-version'
                        },
                        {
                            key: 'server',
                            labelId: 'stack-server-label',
                            verId: 'stack-server-version'
                        },
                    ];
                    stackMap.forEach(({
                        key,
                        labelId,
                        verId
                    }) => {
                        const item = stack[key] ?? {};
                        txt(labelId, item.label ?? key.toUpperCase());
                        const verEl = document.getElementById(verId);
                        if (verEl && item.version) {
                            verEl.innerHTML =
                                `<span class="stack-version-badge" style="background:${item.bg ?? 'transparent'};color:${item.color ?? 'inherit'}">${item.version}</span>`;
                        }
                        // Update icon wrap color
                        const card = document.getElementById('stack-' + key);
                        if (card && item.bg) {
                            const iconWrap = card.querySelector('.stack-icon-wrap');
                            if (iconWrap) iconWrap.style.background = item.bg;
                        }
                    });

                } catch (err) {
                    console.warn('[ServerInfo] fetch error:', err);
                }
            }

            // ── BOOT ─────────────────────────────────────────────────
            fetchAndRender();
            setInterval(fetchAndRender, INTERVAL_MS);

        })();
    </script>
@endsection
