@extends('layouts.app')

@section('template_title')
    Peta Sebaran Data Lapangan
@endsection

@section('content')

{{-- ─── HEADER ─── --}}
<div class="ps-page-header">
    <div class="ps-header-content">
        <div class="ps-header-icon"><i data-feather="map"></i></div>
        <div>
            <h1 class="ps-header-title">Peta Sebaran Data Lapangan</h1>
            <p class="ps-header-sub">Visualisasi lokasi Pelaku Usaha per Desa/Kelurahan</p>
        </div>
    </div>
    <div class="ps-header-actions">
        <div class="ps-filter-wrap">
            <label class="ps-filter-label">Filter Status</label>
            <select id="filterStatus" class="ps-select">
                <option value="">Semua Status</option>
                <option value="PENDING">Pending</option>
                <option value="REVISI">Revisi</option>
                <option value="PROGRESS OSS">Progress OSS</option>
                <option value="PROGRESS SIHALAL">Progress Sihalal</option>
                <option value="TERBIT SH">Terbit SH</option>
                <option value="DITOLAK">Ditolak</option>
            </select>
        </div>
        <button id="btnRefresh" class="ps-btn ps-btn-primary">
            <i data-feather="refresh-cw"></i><span>Muat Ulang</span>
        </button>
        <button id="btnDetailStatistik" class="ps-btn ps-btn-detail">
            <i data-feather="bar-chart-2"></i><span>Detail Statistik</span>
        </button>
    </div>
</div>

{{-- ─── STATS ROW ─── --}}
<div class="ps-stats-row">
    <div class="ps-stat-chip ps-chip-total">
        <i data-feather="layers"></i>
        <div><span id="statTotal">0</span><small>Total PU</small></div>
    </div>
    <div class="ps-stat-chip ps-chip-prov">
        <i data-feather="globe"></i>
        <div><span id="statProv">0</span><small>Provinsi</small></div>
    </div>
    <div class="ps-stat-chip ps-chip-kab">
        <i data-feather="navigation"></i>
        <div><span id="statKab">0</span><small>Kabupaten/Kota</small></div>
    </div>
    <div class="ps-stat-chip ps-chip-kec">
        <i data-feather="map"></i>
        <div><span id="statKec">0</span><small>Kecamatan</small></div>
    </div>
    <div class="ps-stat-chip ps-chip-desa">
        <i data-feather="map-pin"></i>
        <div><span id="statDesa">0</span><small>Desa/Kelurahan</small></div>
    </div>
    <div class="ps-stat-chip ps-chip-terbit">
        <i data-feather="check-circle"></i>
        <div><span id="statTerbit">0</span><small>Terbit SH</small></div>
    </div>
    <div class="ps-stat-chip ps-chip-pending">
        <i data-feather="clock"></i>
        <div><span id="statPending">0</span><small>Pending</small></div>
    </div>
    <div class="ps-stat-chip ps-chip-progress">
        <i data-feather="loader"></i>
        <div><span id="statProgress">0</span><small>Progress</small></div>
    </div>
    <div class="ps-stat-chip ps-chip-ditolak">
        <i data-feather="x-circle"></i>
        <div><span id="statDitolak">0</span><small>Ditolak</small></div>
    </div>
</div>

{{-- ─── GEOCODING PROGRESS BAR ─── --}}
<div class="ps-progress-wrap" id="progressWrap" style="display:none">
    <div class="ps-progress-header">
        <span class="ps-progress-label"><i data-feather="zap"></i> Geocoding Desa/Kelurahan…</span>
        <span id="progressCounter" class="ps-progress-counter">0 / 0</span>
    </div>
    <div class="ps-progress-track"><div class="ps-progress-fill" id="progressFill" style="width:0%"></div></div>
    <div class="ps-progress-note">Marker akan muncul secara bertahap per desa. Data yang sudah di-cache tampil instan.</div>
</div>

{{-- ─── MAP CARD ─── --}}
<div class="ps-map-card">
    <div id="petaSebaranMap"
         data-url="{{ route($routePrefix . '.peta-sebaran.data') }}"
         data-geocode-url="{{ route($routePrefix . '.peta-sebaran.geocode') }}"
         data-statistik-url="{{ route($routePrefix . '.peta-sebaran.statistik-detail') }}">
    </div>
    {{-- Legend --}}
    <div class="ps-legend">
        <div class="ps-legend-title">Keterangan</div>
        <div class="ps-legend-items">
            <div class="ps-legend-item"><span class="ps-dot" style="background:#10b981"></span> Terbit SH</div>
            <div class="ps-legend-item"><span class="ps-dot" style="background:#f59e0b"></span> Pending</div>
            <div class="ps-legend-item"><span class="ps-dot" style="background:#8b5cf6"></span> Progress OSS</div>
            <div class="ps-legend-item"><span class="ps-dot" style="background:#06b6d4"></span> Progress Sihalal</div>
            <div class="ps-legend-item"><span class="ps-dot" style="background:#ef4444"></span> Ditolak</div>
            <div class="ps-legend-item"><span class="ps-dot" style="background:#f97316"></span> Revisi</div>
        </div>
        <div class="ps-legend-title" style="margin-top:10px">Ukuran Lingkaran</div>
        <div class="ps-legend-note">Semakin besar = semakin banyak PU di desa/kelurahan tersebut</div>
    </div>
</div>

{{-- ─── NOTE ─── --}}
<div class="ps-note">
    <i data-feather="info"></i>
    <span>Pengelompokan lokasi dilakukan per <strong>Desa/Kelurahan</strong> menggunakan <strong>WilayahService (kodepos.vercel.app)</strong>.
    Cache bersifat permanen; desa/kelurahan baru otomatis di-geocode dan disimpan.</span>
</div>

{{-- ─── MODAL DETAIL STATISTIK ─── --}}
<div id="modalDetailStatistik" class="ps-modal-backdrop" style="display:none">
    <div class="ps-modal-dialog">
        <div class="ps-modal-header">
            <div class="ps-modal-title-box">
                <div class="ps-modal-title-icon"><i data-feather="bar-chart-2"></i></div>
                <div>
                    <h3 class="ps-modal-title">Detail Statistik Sebaran Wilayah</h3>
                    <p class="ps-modal-sub">Rincian sebaran Pelaku Usaha & status sertifikasi halal per level wilayah</p>
                </div>
            </div>
            <button id="btnCloseModal" class="ps-modal-close-btn">&times;</button>
        </div>

        <div class="ps-modal-body">
            {{-- Toolbar: Tabs + Search --}}
            <div class="ps-modal-toolbar">
                <div class="ps-modal-tabs">
                    <button class="ps-tab-btn active" data-level="provinsi">
                        <i data-feather="globe"></i><span>Provinsi</span> <span class="ps-tab-count" id="countTabProv">0</span>
                    </button>
                    <button class="ps-tab-btn" data-level="kabupaten">
                        <i data-feather="navigation"></i><span>Kabupaten/Kota</span> <span class="ps-tab-count" id="countTabKab">0</span>
                    </button>
                    <button class="ps-tab-btn" data-level="kecamatan">
                        <i data-feather="map"></i><span>Kecamatan</span> <span class="ps-tab-count" id="countTabKec">0</span>
                    </button>
                    <button class="ps-tab-btn" data-level="desa">
                        <i data-feather="map-pin"></i><span>Desa/Kelurahan</span> <span class="ps-tab-count" id="countTabDesa">0</span>
                    </button>
                </div>
                <div class="ps-modal-search">
                    <i data-feather="search"></i>
                    <input type="text" id="searchStatistikInput" placeholder="Cari nama wilayah...">
                </div>
            </div>

            {{-- Table --}}
            <div class="ps-modal-table-wrap">
                <table class="ps-modal-table">
                    <thead>
                        <tr>
                            <th width="45">No</th>
                            <th>Nama Wilayah</th>
                            <th class="text-center" width="90">Total PU</th>
                            <th class="text-center" width="90">Terbit SH</th>
                            <th class="text-center" width="85">Pending</th>
                            <th class="text-center" width="85">Progress</th>
                            <th class="text-center" width="85">Ditolak</th>
                            <th class="text-right" width="130">% Terbit SH</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyStatistikDetail">
                        <tr><td colspan="8" class="text-center py-4 text-muted">Memuat data statistik detail...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ═══ STYLES ═══ --}}
<link rel="stylesheet" href="{{ asset('assets/libs/leaflet/leaflet.css') }}">
<style>
:root {
    --ps-bg:#f3f5fb; --ps-card:#fff; --ps-border:#e8ecf4; --ps-border-bright:#d0d7eb;
    --ps-text:#1a2040; --ps-text-sec:#5a6380; --ps-text-muted:#9aa0b8;
    --ps-shadow:0 2px 12px rgba(80,100,160,.08),0 1px 3px rgba(80,100,160,.06);
    --ps-shadow-hover:0 8px 28px rgba(80,100,160,.14);
    --ps-radius:16px; --ps-accent:#0F2C59; --ps-accent2:#8E9AAF;
}
body,.page-content,.main-content{background:var(--ps-bg)!important}

/* HEADER */
.ps-page-header{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;margin-bottom:20px}
.ps-header-content{display:flex;align-items:center;gap:14px}
.ps-header-icon{width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,var(--ps-accent),var(--ps-accent2));display:flex;align-items:center;justify-content:center;color:#fff;box-shadow:0 4px 14px rgba(15,44,89,.25);flex-shrink:0}
.ps-header-icon svg{width:22px;height:22px;stroke:#fff}
.ps-header-title{font-size:20px;font-weight:800;color:var(--ps-text);margin:0;line-height:1.2}
.ps-header-sub{font-size:13px;color:var(--ps-text-muted);margin:2px 0 0}
.ps-header-actions{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.ps-filter-wrap{display:flex;flex-direction:column;gap:3px}
.ps-filter-label{font-size:11px;font-weight:600;color:var(--ps-text-muted);text-transform:uppercase;letter-spacing:1px}
.ps-select{border:1px solid var(--ps-border-bright);border-radius:8px;padding:8px 12px;font-size:13px;color:var(--ps-text);background:var(--ps-card);outline:none;min-width:160px;cursor:pointer}
.ps-select:focus{border-color:var(--ps-accent)}
.ps-btn{display:inline-flex;align-items:center;gap:7px;border:none;border-radius:10px;padding:9px 18px;font-size:13px;font-weight:600;cursor:pointer;transition:all .2s}
.ps-btn svg{width:15px;height:15px}
.ps-btn-primary{background:linear-gradient(135deg,var(--ps-accent),#244B84);color:#fff;box-shadow:0 4px 12px rgba(15,44,89,.2)}
.ps-btn-primary:hover{transform:translateY(-1px);box-shadow:0 6px 18px rgba(15,44,89,.28)}
.ps-btn-detail{background:linear-gradient(135deg,#3730a3,#4f46e5);color:#fff;box-shadow:0 4px 12px rgba(79,70,229,.25)}
.ps-btn-detail:hover{transform:translateY(-1px);box-shadow:0 6px 18px rgba(79,70,229,.35)}

/* STATS */
.ps-stats-row{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:16px}
.ps-stat-chip{display:flex;align-items:center;gap:10px;background:var(--ps-card);border:1px solid var(--ps-border);border-radius:12px;padding:10px 18px;box-shadow:var(--ps-shadow);flex:1 1 130px;min-width:110px}
.ps-stat-chip svg{width:18px;height:18px;flex-shrink:0}
.ps-stat-chip div{display:flex;flex-direction:column;gap:2px}
.ps-stat-chip span{font-size:22px;font-weight:800;color:var(--ps-text);line-height:1}
.ps-stat-chip small{font-size:11px;color:var(--ps-text-muted);font-weight:600;text-transform:uppercase;letter-spacing:.8px}
.ps-chip-total svg{stroke:#6366f1}
.ps-chip-prov svg{stroke:#3b82f6}
.ps-chip-kab svg{stroke:#8b5cf6}
.ps-chip-kec svg{stroke:#0F2C59}
.ps-chip-desa svg{stroke:#ec4899}
.ps-chip-terbit svg{stroke:#10b981}
.ps-chip-pending svg{stroke:#f59e0b}
.ps-chip-progress svg{stroke:#06b6d4}
.ps-chip-ditolak svg{stroke:#ef4444}

/* PROGRESS BAR */
.ps-progress-wrap{background:var(--ps-card);border:1px solid var(--ps-border);border-radius:12px;padding:14px 18px;margin-bottom:14px;box-shadow:var(--ps-shadow)}
.ps-progress-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}
.ps-progress-label{font-size:13px;font-weight:600;color:var(--ps-text);display:flex;align-items:center;gap:7px}
.ps-progress-label svg{width:14px;height:14px;stroke:#f59e0b}
.ps-progress-counter{font-size:12px;color:var(--ps-text-muted);font-weight:600;font-variant-numeric:tabular-nums}
.ps-progress-track{height:6px;background:#f1f4fb;border-radius:3px;overflow:hidden}
.ps-progress-fill{height:100%;background:linear-gradient(90deg,#0F2C59,#5A84BA);border-radius:3px;transition:width .4s ease}
.ps-progress-note{font-size:11px;color:var(--ps-text-muted);margin-top:6px}

/* MAP */
.ps-map-card{position:relative;background:var(--ps-card);border:1px solid var(--ps-border);border-radius:var(--ps-radius);box-shadow:var(--ps-shadow);overflow:hidden;margin-bottom:14px}
#petaSebaranMap{height:580px;width:100%}

/* LEGEND */
.ps-legend{position:absolute;bottom:24px;left:16px;background:rgba(255,255,255,.95);border:1px solid var(--ps-border);border-radius:12px;padding:12px 16px;z-index:1000;box-shadow:var(--ps-shadow);backdrop-filter:blur(6px)}
.ps-legend-title{font-size:11px;font-weight:700;color:var(--ps-text-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:6px}
.ps-legend-items{display:flex;flex-direction:column;gap:5px}
.ps-legend-item{display:flex;align-items:center;gap:7px;font-size:12px;color:var(--ps-text-sec)}
.ps-legend-note{font-size:11px;color:var(--ps-text-muted);margin-top:4px;max-width:160px;line-height:1.4}
.ps-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0}

/* NOTE */
.ps-note{display:flex;align-items:flex-start;gap:10px;background:rgba(142,154,175,.08);border:1px solid rgba(142,154,175,.2);border-radius:10px;padding:12px 16px;font-size:13px;color:var(--ps-text-sec);margin-bottom:8px}
.ps-note svg{width:16px;height:16px;flex-shrink:0;margin-top:1px;stroke:var(--ps-accent2)}

/* CLUSTER MARKER */
.ps-cluster{display:flex;align-items:center;justify-content:center;border-radius:50%;border:3px solid rgba(255,255,255,.85);box-shadow:0 2px 8px rgba(0,0,0,.25);font-weight:800;color:#fff;font-family:'Plus Jakarta Sans',sans-serif;line-height:1;transition:transform .15s}
.ps-cluster:hover{transform:scale(1.08)}

/* POPUP */
.ps-popup{min-width:240px;font-family:'Plus Jakarta Sans',sans-serif;max-height:280px;overflow-y:auto}
.ps-popup-title{font-size:14px;font-weight:700;color:var(--ps-text);margin-bottom:4px}
.ps-popup-sub{font-size:11px;color:var(--ps-text-muted);margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid #f1f4fb}
.ps-popup-item{display:flex;flex-direction:column;gap:2px;padding:6px 0;border-bottom:1px solid #f7f9fd}
.ps-popup-item:last-child{border-bottom:none}
.ps-popup-pu{font-size:13px;font-weight:600;color:var(--ps-text)}
.ps-popup-detail{font-size:11px;color:var(--ps-text-sec)}
.ps-popup-badge{display:inline-block;padding:1px 7px;border-radius:20px;font-size:10px;font-weight:700;margin-top:3px}

/* MODAL STATISTIK */
.ps-modal-backdrop{position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(15,23,42,.6);backdrop-filter:blur(6px);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px;animation:fadeIn .2s ease}
.ps-modal-dialog{background:#fff;border-radius:20px;box-shadow:0 20px 50px rgba(0,0,0,.2);width:100%;max-width:1020px;max-height:88vh;display:flex;flex-direction:column;overflow:hidden;animation:slideUp .25s ease}
.ps-modal-header{display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #e2e8f0;background:#f8fafc}
.ps-modal-title-box{display:flex;align-items:center;gap:14px}
.ps-modal-title-icon{width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#4f46e5,#6366f1);display:flex;align-items:center;justify-content:center;color:#fff;box-shadow:0 4px 12px rgba(79,70,229,.25)}
.ps-modal-title-icon svg{width:22px;height:22px}
.ps-modal-title{font-size:18px;font-weight:800;color:#0f172a;margin:0}
.ps-modal-sub{font-size:12px;color:#64748b;margin:2px 0 0}
.ps-modal-close-btn{background:none;border:none;font-size:24px;line-height:1;color:#94a3b8;cursor:pointer;padding:4px 8px;border-radius:8px;transition:all .15s}
.ps-modal-close-btn:hover{color:#0f172a;background:#e2e8f0}

.ps-modal-body{padding:20px 24px;display:flex;flex-direction:column;gap:16px;overflow:hidden;flex:1}
.ps-modal-toolbar{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
.ps-modal-tabs{display:flex;align-items:center;gap:6px;background:#f1f5f9;padding:4px;border-radius:12px}
.ps-tab-btn{display:inline-flex;align-items:center;gap:6px;border:none;background:none;padding:7px 14px;border-radius:8px;font-size:12px;font-weight:600;color:#64748b;cursor:pointer;transition:all .15s}
.ps-tab-btn svg{width:14px;height:14px}
.ps-tab-btn.active{background:#fff;color:#4f46e5;box-shadow:0 2px 8px rgba(0,0,0,.06)}
.ps-tab-count{display:inline-block;padding:1px 6px;border-radius:10px;font-size:10px;background:#e2e8f0;color:#475569}
.ps-tab-btn.active .ps-tab-count{background:#e0e7ff;color:#4338ca}

.ps-modal-search{position:relative;display:flex;align-items:center}
.ps-modal-search svg{position:absolute;left:12px;width:15px;height:15px;stroke:#94a3b8}
.ps-modal-search input{padding:8px 12px 8px 34px;border:1px solid #cbd5e1;border-radius:10px;font-size:12px;width:220px;outline:none;transition:all .15s}
.ps-modal-search input:focus{border-color:#4f46e5;box-shadow:0 0 0 3px rgba(79,70,229,.1)}

.ps-modal-table-wrap{flex:1;overflow-y:auto;border:1px solid #e2e8f0;border-radius:12px;max-height:52vh}
.ps-modal-table{width:100%;border-collapse:collapse;font-size:12.5px;text-align:left}
.ps-modal-table th{position:sticky;top:0;background:#f8fafc;color:#475569;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.5px;padding:12px 14px;border-bottom:1px solid #e2e8f0;z-index:2}
.ps-modal-table td{padding:10px 14px;border-bottom:1px solid #f1f5f9;color:#334155;vertical-align:middle}
.ps-modal-table tr:hover td{background:#f8fafc}
.ps-parent-sub{font-size:11px;color:#94a3b8;margin-top:2px}

.ps-badge-pct{display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;padding:2px 8px;border-radius:12px}
.ps-pct-bar{width:45px;height:5px;background:#e2e8f0;border-radius:3px;overflow:hidden;display:inline-block}
.ps-pct-fill{height:100%;background:#10b981;border-radius:3px}

@keyframes fadeIn{from{opacity:0}to{opacity:1}}
@keyframes slideUp{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}
</style>
@endsection

@push('scripts')
<script src="{{ asset('assets/libs/leaflet/leaflet.js') }}"></script>
<script>
(function () {
    'use strict';

    const CSRF        = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const mapEl       = document.getElementById('petaSebaranMap');
    const DATA_URL    = mapEl.dataset.url;
    const GEOCODE_URL = mapEl.dataset.geocodeUrl;

    const SC = {
        'PENDING':          { color:'#f59e0b', bg:'#FEF3C7' },
        'REVISI':           { color:'#f97316', bg:'#FFF7ED' },
        'PROGRESS OSS':     { color:'#8b5cf6', bg:'#EDE9FE' },
        'PROGRESS SIHALAL': { color:'#06b6d4', bg:'#CFFAFE' },
        'TERBIT SH':        { color:'#10b981', bg:'#D1FAE5' },
        'DITOLAK':          { color:'#ef4444', bg:'#FEE2E2' },
    };
    const scfg = s => SC[s?.toUpperCase()] ?? { color:'#6b7280', bg:'#F3F4F6' };

    function escH(s) {
        if (!s) return '-';
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    const map = L.map('petaSebaranMap', { zoomControl:true }).setView([-7.25, 109.22], 8);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution:'&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom:18,
    }).addTo(map);

    const markerLayer = L.layerGroup().addTo(map);
    let allBounds = [];

    function clusterIcon(cluster) {
        const totalItems = cluster.count;
        const statusCounts = {};
        (cluster.items || []).forEach(i => { statusCounts[i.status] = (statusCounts[i.status]||0)+1; });
        const dominant = Object.entries(statusCounts).sort((a,b)=>b[1]-a[1])[0]?.[0] ?? 'PENDING';
        const { color } = scfg(dominant);

        const size = Math.min(64, Math.max(32, 32 + Math.log2(totalItems + 1) * 6));
        const fs   = size > 48 ? 14 : 12;

        return L.divIcon({
            className: '',
            html: `<div class="ps-cluster" style="width:${size}px;height:${size}px;background:${color};font-size:${fs}px">${totalItems}</div>`,
            iconSize: [size, size],
            iconAnchor: [size/2, size/2],
            popupAnchor: [0, -(size/2 + 4)],
        });
    }

    function buildPopup(cluster) {
        let desaLabel = `Desa ${escH(cluster.nama_desa || 'Utama')}`;
        if (cluster.nama_kecamatan) {
            desaLabel += `, Kec. ${escH(cluster.nama_kecamatan)}`;
        }
        if (cluster.nama_kabupaten) {
            desaLabel += `, ${escH(cluster.nama_kabupaten)}`;
        }

        const itemsHtml = (cluster.items || []).slice(0, 30).map(item => {
            const { color, bg } = scfg(item.status);
            return `<div class="ps-popup-item">
                <div class="ps-popup-pu">${escH(item.nama_pu)}</div>
                <div class="ps-popup-detail">${escH(item.pendamping)} · ${escH(item.tanggal)}</div>
                <span class="ps-popup-badge" style="background:${bg};color:${color};border:1px solid ${color}33">${escH(item.status)}</span>
            </div>`;
        }).join('');

        const more = cluster.count > 30 ? `<div style="font-size:11px;color:var(--ps-text-muted);padding-top:6px">...dan ${cluster.count - 30} lainnya</div>` : '';

        return `<div class="ps-popup">
            <div class="ps-popup-title">${desaLabel}</div>
            <div class="ps-popup-sub">${cluster.count} Pelaku Usaha terdaftar</div>
            ${itemsHtml}${more}
        </div>`;
    }

    function addClusterMarker(cluster) {
        const lat = parseFloat(cluster.lat), lng = parseFloat(cluster.lng);
        if (isNaN(lat)||isNaN(lng)) return;
        const m = L.marker([lat, lng], { icon: clusterIcon(cluster), title: cluster.nama_desa || cluster.key });
        m.bindPopup(buildPopup(cluster), { maxWidth:300, maxHeight:340 });
        markerLayer.addLayer(m);
        allBounds.push([lat,lng]);
    }

    function fitMap() {
        if (allBounds.length > 0) map.fitBounds(allBounds, { padding:[50,50], maxZoom:14 });
    }

    function updateStats(json) {
        const clusters = json.data || (Array.isArray(json) ? json : []);
        const allItems = clusters.flatMap(c => c.items || []);

        document.getElementById('statTotal').textContent    = json.total_records ?? allItems.length;
        document.getElementById('statProv').textContent     = json.total_provinsi ?? (new Set(clusters.map(c=>c.kode_prov)).size);
        document.getElementById('statKab').textContent      = json.total_kabupaten ?? (new Set(clusters.map(c=>c.kode_kab)).size);
        document.getElementById('statKec').textContent      = json.total_kecamatan ?? (new Set(clusters.map(c=>c.kode_kec)).size);
        document.getElementById('statDesa').textContent     = json.total_desa ?? clusters.length;

        document.getElementById('statTerbit').textContent   = allItems.filter(d=>d.status==='TERBIT SH').length;
        document.getElementById('statPending').textContent  = allItems.filter(d=>d.status==='PENDING').length;
        document.getElementById('statProgress').textContent = allItems.filter(d=>d.status==='PROGRESS OSS'||d.status==='PROGRESS SIHALAL').length;
        document.getElementById('statDitolak').textContent  = allItems.filter(d=>d.status==='DITOLAK').length;
    }

    const progressWrap    = document.getElementById('progressWrap');
    const progressCounter = document.getElementById('progressCounter');
    const progressFill    = document.getElementById('progressFill');

    function showProgress(done, total) {
        progressWrap.style.display = 'block';
        progressCounter.textContent = `${done} / ${total}`;
        progressFill.style.width = (total > 0 ? (done/total*100) : 0) + '%';
        if (done >= total) {
            setTimeout(() => { progressWrap.style.display = 'none'; }, 2500);
        }
    }

    async function geocodeQueue(clusters) {
        const total = clusters.length;
        let done = 0;
        showProgress(0, total);

        for (const cluster of clusters) {
            try {
                const res = await fetch(GEOCODE_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept':       'application/json',
                        'X-CSRF-TOKEN': CSRF,
                    },
                    body: JSON.stringify({
                        key: cluster.key || cluster.kode,
                        kode_kec: cluster.kode_kec,
                        nama_desa: cluster.nama_desa
                    }),
                });
                const json = await res.json();

                if (json.success) {
                    cluster.lat            = json.lat;
                    cluster.lng            = json.lng;
                    cluster.nama_desa      = json.nama_desa || cluster.nama_desa;
                    cluster.nama_kecamatan = json.nama_kecamatan;
                    cluster.nama_kabupaten = json.nama_kabupaten;
                    cluster.nama_provinsi  = json.nama_provinsi;
                    addClusterMarker(cluster);
                    if (done % 10 === 0) fitMap();
                }
            } catch (_) { /* skip */ }

            done++;
            showProgress(done, total);

            if (done < total) {
                await new Promise(r => setTimeout(r, 400));
            }
        }
        fitMap();
    }

    function loadData(status = '') {
        markerLayer.clearLayers();
        allBounds = [];
        progressWrap.style.display = 'none';

        const url = DATA_URL + (status ? '?status=' + encodeURIComponent(status) : '');

        fetch(url)
            .then(r => r.json())
            .then(json => {
                if (!json.success) throw new Error('Gagal memuat data');

                const cached   = json.data.filter(c => c.lat !== null);
                const uncached = json.data.filter(c => c.needs_geocode === true);

                // Phase 1 — render cached clusters immediately
                cached.forEach(addClusterMarker);
                fitMap();
                updateStats(json);

                // Phase 2 — geocode the rest in background
                if (uncached.length > 0) {
                    geocodeQueue(uncached);
                }
            })
            .catch(err => console.error('Peta load error:', err));
    }

    document.getElementById('filterStatus').addEventListener('change', function () {
        statistikData = null;
        loadData(this.value);
        if (modalDetail.style.display === 'flex') {
            loadStatistikDetail();
        }
    });
    document.getElementById('btnRefresh').addEventListener('click', function () {
        statistikData = null;
        loadData(document.getElementById('filterStatus').value);
        if (modalDetail.style.display === 'flex') {
            loadStatistikDetail();
        }
    });

    // ── Detail Statistik Modal Logic ──
    const STATISTIK_URL    = mapEl.dataset.statistikUrl;
    const modalDetail      = document.getElementById('modalDetailStatistik');
    const btnDetail        = document.getElementById('btnDetailStatistik');
    const btnCloseModal    = document.getElementById('btnCloseModal');
    const inputSearchModal = document.getElementById('searchStatistikInput');
    const tbodyDetail      = document.getElementById('tbodyStatistikDetail');

    let statistikData = null;
    let currentTab = 'provinsi';

    btnDetail.addEventListener('click', function () {
        modalDetail.style.display = 'flex';
        if (!statistikData) {
            loadStatistikDetail();
        } else {
            renderStatistikTable();
        }
    });

    btnCloseModal.addEventListener('click', function () {
        modalDetail.style.display = 'none';
    });

    modalDetail.addEventListener('click', function (e) {
        if (e.target === modalDetail) modalDetail.style.display = 'none';
    });

    document.querySelectorAll('.ps-tab-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.ps-tab-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentTab = this.dataset.level;
            renderStatistikTable();
        });
    });

    inputSearchModal.addEventListener('input', function () {
        renderStatistikTable();
    });

    function loadStatistikDetail() {
        tbodyDetail.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">Memuat data statistik detail...</td></tr>';

        const status = document.getElementById('filterStatus').value;
        const url    = STATISTIK_URL + (status ? '?status=' + encodeURIComponent(status) : '');

        fetch(url)
            .then(r => r.json())
            .then(json => {
                if (!json.success) throw new Error('Gagal memuat statistik detail');
                statistikData = json;

                document.getElementById('countTabProv').textContent = json.provinsi?.length || 0;
                document.getElementById('countTabKab').textContent  = json.kabupaten?.length || 0;
                document.getElementById('countTabKec').textContent  = json.kecamatan?.length || 0;
                document.getElementById('countTabDesa').textContent = json.desa?.length || 0;

                renderStatistikTable();
            })
            .catch(err => {
                tbodyDetail.innerHTML = `<tr><td colspan="8" class="text-center text-danger py-4">${escH(err.message)}</td></tr>`;
            });
    }

    function renderStatistikTable() {
        if (!statistikData || !statistikData[currentTab]) return;

        const query = (inputSearchModal.value || '').trim().toLowerCase();
        let items = statistikData[currentTab];

        if (query) {
            items = items.filter(item =>
                (item.name && item.name.toLowerCase().includes(query)) ||
                (item.parent && item.parent.toLowerCase().includes(query))
            );
        }

        if (items.length === 0) {
            tbodyDetail.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada data wilayah yang cocok</td></tr>';
            return;
        }

        let html = '';
        items.forEach((item, idx) => {
            const pct = item.total > 0 ? Math.round((item.terbit / item.total) * 100) : 0;
            const parentLabel = item.parent ? `<div class="ps-parent-sub">${escH(item.parent)}</div>` : '';

            html += `<tr>
                <td class="text-muted">${idx + 1}</td>
                <td>
                    <strong style="color:#0f172a">${escH(item.name)}</strong>
                    ${parentLabel}
                </td>
                <td class="text-center"><strong>${item.total}</strong></td>
                <td class="text-center"><span class="badge" style="background:#d1fae5;color:#065f46">${item.terbit}</span></td>
                <td class="text-center"><span class="badge" style="background:#fef3c7;color:#92400e">${item.pending}</span></td>
                <td class="text-center"><span class="badge" style="background:#cffafe;color:#155e75">${item.progress}</span></td>
                <td class="text-center"><span class="badge" style="background:#fee2e2;color:#991b1b">${item.ditolak + item.revisi}</span></td>
                <td class="text-right">
                    <span class="ps-badge-pct">
                        <span class="ps-pct-bar"><span class="ps-pct-fill" style="width:${pct}%"></span></span>
                        <span>${pct}%</span>
                    </span>
                </td>
            </tr>`;
        });

        tbodyDetail.innerHTML = html;
    }

    loadData();
    if (typeof feather !== 'undefined') feather.replace();
})();
</script>
@endpush
