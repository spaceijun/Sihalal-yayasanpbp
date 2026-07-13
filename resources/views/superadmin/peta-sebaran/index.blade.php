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
            <p class="ps-header-sub">Visualisasi lokasi Pelaku Usaha per kecamatan (dari kode NIK)</p>
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
    </div>
</div>

{{-- ─── STATS ROW ─── --}}
<div class="ps-stats-row">
    <div class="ps-stat-chip ps-chip-total">
        <i data-feather="layers"></i>
        <div><span id="statTotal">0</span><small>Total PU</small></div>
    </div>
    <div class="ps-stat-chip ps-chip-kec">
        <i data-feather="map"></i>
        <div><span id="statKec">0</span><small>Kecamatan</small></div>
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
        <span class="ps-progress-label"><i data-feather="zap"></i> Geocoding kecamatan baru…</span>
        <span id="progressCounter" class="ps-progress-counter">0 / 0</span>
    </div>
    <div class="ps-progress-track"><div class="ps-progress-fill" id="progressFill" style="width:0%"></div></div>
    <div class="ps-progress-note">Marker akan muncul secara bertahap. Kecamatan yang sudah di-cache tampil instan.</div>
</div>

{{-- ─── MAP CARD ─── --}}
<div class="ps-map-card">
    <div id="petaSebaranMap"
         data-url="{{ route($routePrefix . '.peta-sebaran.data') }}"
         data-geocode-url="{{ route($routePrefix . '.peta-sebaran.geocode') }}">
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
        <div class="ps-legend-note">Semakin besar = semakin banyak PU di kecamatan tersebut</div>
    </div>
</div>

{{-- ─── NOTE ─── --}}
<div class="ps-note">
    <i data-feather="info"></i>
    <span>Lokasi ditentukan dari <strong>6 digit pertama NIK</strong> (kode provinsi + kabupaten + kecamatan).
    Tidak memerlukan kolom koordinat — data langsung dikelompokkan per kecamatan.
    Cache bersifat permanen; kecamatan baru otomatis di-geocode dan disimpan.</span>
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

/* STATS */
.ps-stats-row{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:16px}
.ps-stat-chip{display:flex;align-items:center;gap:10px;background:var(--ps-card);border:1px solid var(--ps-border);border-radius:12px;padding:10px 18px;box-shadow:var(--ps-shadow);flex:1 1 130px;min-width:110px}
.ps-stat-chip svg{width:18px;height:18px;flex-shrink:0}
.ps-stat-chip div{display:flex;flex-direction:column;gap:2px}
.ps-stat-chip span{font-size:22px;font-weight:800;color:var(--ps-text);line-height:1}
.ps-stat-chip small{font-size:11px;color:var(--ps-text-muted);font-weight:600;text-transform:uppercase;letter-spacing:.8px}
.ps-chip-total svg{stroke:#6366f1} .ps-chip-kec svg{stroke:#0F2C59}
.ps-chip-terbit svg{stroke:#10b981} .ps-chip-pending svg{stroke:#f59e0b}
.ps-chip-progress svg{stroke:#06b6d4} .ps-chip-ditolak svg{stroke:#ef4444}

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

    // ── Status colours ──
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

    // ── Map init ──
    const map = L.map('petaSebaranMap', { zoomControl:true }).setView([-7.25, 109.22], 8);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution:'&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom:18,
    }).addTo(map);

    const markerLayer = L.layerGroup().addTo(map);
    let allBounds = [];

    // ── Cluster circle icon ──
    function clusterIcon(cluster) {
        const totalItems = cluster.count;
        // Dominant status
        const statusCounts = {};
        (cluster.items || []).forEach(i => { statusCounts[i.status] = (statusCounts[i.status]||0)+1; });
        const dominant = Object.entries(statusCounts).sort((a,b)=>b[1]-a[1])[0]?.[0] ?? 'PENDING';
        const { color } = scfg(dominant);

        // Size: log scale, min 32, max 64
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

    // ── Popup ──
    function buildPopup(cluster) {
        const kecLabel = cluster.nama_kecamatan
            ? `Kec. ${escH(cluster.nama_kecamatan)}, ${escH(cluster.nama_kabupaten)}`
            : `Kode Wilayah: ${escH(cluster.kode)}`;

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
            <div class="ps-popup-title">${kecLabel}</div>
            <div class="ps-popup-sub">${cluster.count} Pelaku Usaha terdaftar</div>
            ${itemsHtml}${more}
        </div>`;
    }

    function addClusterMarker(cluster) {
        const lat = parseFloat(cluster.lat), lng = parseFloat(cluster.lng);
        if (isNaN(lat)||isNaN(lng)) return;
        const m = L.marker([lat, lng], { icon: clusterIcon(cluster), title: cluster.kode });
        m.bindPopup(buildPopup(cluster), { maxWidth:300, maxHeight:340 });
        markerLayer.addLayer(m);
        allBounds.push([lat,lng]);
    }

    function fitMap() {
        if (allBounds.length > 0) map.fitBounds(allBounds, { padding:[50,50], maxZoom:12 });
    }

    // ── Stats ──
    function updateStats(clusters) {
        const allItems = clusters.flatMap(c => c.items || []);
        document.getElementById('statTotal').textContent    = allItems.length;
        document.getElementById('statKec').textContent      = clusters.length;
        document.getElementById('statTerbit').textContent   = allItems.filter(d=>d.status==='TERBIT SH').length;
        document.getElementById('statPending').textContent  = allItems.filter(d=>d.status==='PENDING').length;
        document.getElementById('statProgress').textContent = allItems.filter(d=>d.status==='PROGRESS OSS'||d.status==='PROGRESS SIHALAL').length;
        document.getElementById('statDitolak').textContent  = allItems.filter(d=>d.status==='DITOLAK').length;
    }

    // ── Progress bar ──
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

    // ── Phase 2: geocode uncached kecamatans sequentially ──
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
                    body: JSON.stringify({ kode: cluster.kode }),
                });
                const json = await res.json();

                if (json.success) {
                    cluster.lat            = json.lat;
                    cluster.lng            = json.lng;
                    cluster.nama_kecamatan = json.nama_kecamatan;
                    cluster.nama_kabupaten = json.nama_kabupaten;
                    cluster.nama_provinsi  = json.nama_provinsi;
                    addClusterMarker(cluster);
                    // Refit every 10 new markers
                    if (done % 10 === 0) fitMap();
                }
            } catch (_) { /* skip */ }

            done++;
            showProgress(done, total);

            if (done < total) {
                await new Promise(r => setTimeout(r, 1150)); // ~1 req/sec for Nominatim
            }
        }
        fitMap();
    }

    // ── Main load ──
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
                updateStats(json.data);

                // Phase 2 — geocode the rest in background
                if (uncached.length > 0) {
                    geocodeQueue(uncached);
                }
            })
            .catch(err => console.error('Peta load error:', err));
    }

    document.getElementById('filterStatus').addEventListener('change', function () {
        loadData(this.value);
    });
    document.getElementById('btnRefresh').addEventListener('click', function () {
        loadData(document.getElementById('filterStatus').value);
    });

    loadData();
    if (typeof feather !== 'undefined') feather.replace();
})();
</script>
@endpush
