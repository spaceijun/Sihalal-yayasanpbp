@extends('layouts.app')
@section('template_title') Laporan Harian @endsection
@section('content')
<div class="adm-page">
    @include('layouts.messages')

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Laporan Data Lapangan</h1>
            <p>Rekap data masuk / diubah per koordinator & enumerator</p>
        </div>
    </div>

    {{-- ── FILTER BAR ── --}}
    <div class="adm-filter-bar no-print" style="flex-wrap:wrap;gap:10px;margin-bottom:18px;">

        {{-- Toggle Tipe Data --}}
        <div class="adm-filter-group">
            <label class="adm-filter-label">Tipe Data</label>
            <div style="display:flex;gap:0;border:1px solid var(--adm-border);border-radius:7px;overflow:hidden;">
                <input type="radio" name="tipeData" id="filterCreated" value="created" class="visually-hidden"
                    {{ $tipeData == 'created' ? 'checked' : '' }}>
                <label for="filterCreated" class="adm-toggle-btn" style="{{ $tipeData=='created' ? 'background:var(--adm-green);color:#fff;' : '' }}">
                    Data Masuk
                </label>
                <input type="radio" name="tipeData" id="filterUpdated" value="updated" class="visually-hidden"
                    {{ $tipeData == 'updated' ? 'checked' : '' }}>
                <label for="filterUpdated" class="adm-toggle-btn" style="{{ $tipeData=='updated' ? 'background:var(--adm-blue);color:#fff;' : '' }}">
                    Data Diubah
                </label>
            </div>
        </div>

        {{-- Toggle Tipe Filter --}}
        <div class="adm-filter-group">
            <label class="adm-filter-label">Periode</label>
            <div style="display:flex;gap:0;border:1px solid var(--adm-border);border-radius:7px;overflow:hidden;">
                <input type="radio" name="tipeFilter" id="filterHarian" value="harian" class="visually-hidden"
                    {{ $tipeFilter == 'harian' ? 'checked' : '' }}>
                <label for="filterHarian" class="adm-toggle-btn" style="{{ $tipeFilter=='harian' ? 'background:var(--adm-blue);color:#fff;' : '' }}">
                    Harian
                </label>
                <input type="radio" name="tipeFilter" id="filterBulanan" value="bulanan" class="visually-hidden"
                    {{ $tipeFilter == 'bulanan' ? 'checked' : '' }}>
                <label for="filterBulanan" class="adm-toggle-btn" style="{{ $tipeFilter=='bulanan' ? 'background:var(--adm-blue);color:#fff;' : '' }}">
                    Bulanan
                </label>
            </div>
        </div>

        {{-- Filter Koordinator --}}
        <div class="adm-filter-group">
            <label class="adm-filter-label">Koordinator</label>
            <select id="filterKoordinator" class="adm-select" style="min-width:200px;">
                <option value="">Semua Koordinator</option>
                @foreach ($koordinators as $koordinator)
                    <option value="{{ $koordinator->id }}" {{ $koordinatorId == $koordinator->id ? 'selected' : '' }}>
                        {{ $koordinator->nama_lengkap }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Filter Tanggal --}}
        <div class="adm-filter-group" id="wrapTanggal" style="{{ $tipeFilter=='bulanan' ? 'display:none;' : '' }}">
            <label class="adm-filter-label">Tanggal</label>
            <input type="date" id="filterTanggal" class="adm-select" value="{{ $tanggal }}">
        </div>

        {{-- Filter Bulan --}}
        <div class="adm-filter-group" id="wrapBulan" style="{{ $tipeFilter=='harian' ? 'display:none;' : '' }}">
            <label class="adm-filter-label">Bulan</label>
            <input type="month" id="filterBulan" class="adm-select" value="{{ $bulan }}">
        </div>
    </div>

    @if ($laporanPerKoordinator->isEmpty())
        <div class="adm-card" style="padding:40px 20px;">
            <div class="adm-empty">
                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                <p>Tidak ada data <strong>{{ $tipeData == 'created' ? 'masuk' : 'diubah' }}</strong> untuk
                    @if ($tipeFilter == 'harian')
                        tanggal {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}
                    @else
                        bulan {{ \Carbon\Carbon::parse($bulan)->format('F Y') }}
                    @endif
                    @if ($koordinatorId)
                        — {{ $koordinators->firstWhere('id', $koordinatorId)->nama_lengkap ?? '' }}
                    @endif
                </p>
            </div>
        </div>
    @else

        {{-- ── PERIODE INFO ── --}}
        <div class="adm-alert adm-alert-info" style="margin-bottom:16px;">
            <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <div style="font-size:13px;">
                <strong>Tipe Data:</strong> {{ $tipeData == 'created' ? 'Data Masuk' : 'Data Diubah' }}
                &nbsp;|&nbsp;
                <strong>Periode:</strong>
                @if ($tipeFilter == 'harian')
                    {{ \Carbon\Carbon::parse($tanggal)->isoFormat('dddd, D MMMM YYYY') }}
                @else
                    {{ \Carbon\Carbon::parse($bulan)->isoFormat('MMMM YYYY') }}
                @endif
                @if ($koordinatorId)
                    &nbsp;|&nbsp; <strong>Koordinator:</strong>
                    {{ $koordinators->firstWhere('id', $koordinatorId)->nama_lengkap ?? '' }}
                @endif
            </div>
        </div>

        {{-- ── STAT CARDS ── --}}
        <div class="adm-stats" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px;">
            <div class="adm-stat">
                <div class="adm-stat-label">Total Koordinator</div>
                <div class="adm-stat-value" style="color:var(--adm-blue);">{{ $laporanPerKoordinator->count() }}</div>
                <div class="adm-stat-sub">Aktif pada periode ini</div>
            </div>
            <div class="adm-stat">
                <div class="adm-stat-label">Total Data</div>
                <div class="adm-stat-value">{{ $laporanPerKoordinator->sum('total_data') }}</div>
                <div class="adm-stat-sub">Keseluruhan data</div>
            </div>
            <div class="adm-stat">
                <div class="adm-stat-label">Terbit SH</div>
                <div class="adm-stat-value is-success">{{ $laporanPerKoordinator->sum('total_terbit_sh') }}</div>
                <div class="adm-stat-sub">Sudah terbit sertifikat</div>
            </div>
            <div class="adm-stat is-accent">
                <div class="adm-stat-label">Dibayar</div>
                <div class="adm-stat-value">{{ $laporanPerKoordinator->sum('total_pembayaran_dibayar') }}</div>
                <div class="adm-stat-sub">Status pembayaran</div>
            </div>
        </div>

        {{-- ── GRAFIK BULANAN ── --}}
        @if ($tipeFilter == 'bulanan' && $grafikHarian)
            <div class="adm-card" style="margin-bottom:18px;padding:20px;">
                <div class="adm-card-header" style="margin:-20px -20px 16px;padding:14px 20px;">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                        Grafik Data Per Hari
                    </div>
                </div>
                <canvas id="chartHarian" height="80"></canvas>
            </div>
        @endif

        {{-- ── LAPORAN PER KOORDINATOR ── --}}
        @foreach ($laporanPerKoordinator as $koordinator)
            <div class="adm-card" id="koordinator-{{ $koordinator['koordinator_id'] }}" style="margin-bottom:18px;">
                <div class="adm-card-header" style="background:var(--adm-blue-lt);border-bottom:1px solid var(--adm-border);">
                    <div class="adm-card-title" style="color:var(--adm-blue);">
                        <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        {{ $koordinator['nama_koordinator'] }}
                        <span class="adm-count-badge">{{ $koordinator['total_data'] }} Data</span>
                    </div>
                    <button class="adm-btn primary no-print" style="font-size:12px;padding:5px 12px;"
                        onclick="downloadKoordinatorImage({{ $koordinator['koordinator_id'] }}, '{{ $koordinator['nama_koordinator'] }}')">
                        <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download Gambar
                    </button>
                </div>

                {{-- Summary 2-kolom --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;padding:16px 20px;">
                    <div style="border:1px solid var(--adm-border);border-radius:8px;padding:14px;">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--adm-text-muted);margin-bottom:10px;">Status Proses</div>
                        @foreach ([['Pending','total_pending','#B86800','#FFF9EC'],['Revisi','total_revisi','#DC2626','#FEF2F2'],['Progress OSS','total_progress_oss','#0369A1','#EFF8FF'],['Progress SiHalal','total_progress_sihalal','#1A5FC8','#EFF6FF'],['Terbit SH','total_terbit_sh','#15803D','#F0FDF4']] as [$label,$key,$color,$bg])
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                                <span style="font-size:12.5px;">{{ $label }}</span>
                                <span class="adm-badge" style="background:{{ $bg }};color:{{ $color }};border:1px solid {{ $color }}33;">{{ $koordinator[$key] }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div style="border:1px solid var(--adm-border);border-radius:8px;padding:14px;">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--adm-text-muted);margin-bottom:10px;">Status Pembayaran</div>
                        @foreach ([['Pending','total_pembayaran_pending','#64748B','#F1F5F9'],['Pengajuan','total_pembayaran_pengajuan','#B86800','#FFF9EC'],['Dibayar','total_pembayaran_dibayar','#15803D','#F0FDF4']] as [$label,$key,$color,$bg])
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                                <span style="font-size:12.5px;">{{ $label }}</span>
                                <span class="adm-badge" style="background:{{ $bg }};color:{{ $color }};border:1px solid {{ $color }}33;">{{ $koordinator[$key] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Tabel Enumerator --}}
                <div class="table-responsive" style="padding:0 20px 20px;">
                    <table class="adm-table" style="font-size:12.5px;">
                        <thead>
                            <tr>
                                <th rowspan="2" style="width:36px">#</th>
                                <th rowspan="2">Nama Enumerator</th>
                                <th rowspan="2" class="tc">Total</th>
                                <th colspan="5" class="tc" style="background:#EFF8FF;color:#0369A1;">Status Proses</th>
                                <th colspan="3" class="tc" style="background:#F0FDF4;color:#15803D;">Status Pembayaran</th>
                            </tr>
                            <tr>
                                <th class="tc" style="background:#FFF9EC;color:#B86800;font-size:11px;">Pending</th>
                                <th class="tc" style="background:#FEF2F2;color:#DC2626;font-size:11px;">Revisi</th>
                                <th class="tc" style="background:#EFF8FF;color:#0369A1;font-size:11px;">OSS</th>
                                <th class="tc" style="background:#EFF6FF;color:#2563EB;font-size:11px;">SiHalal</th>
                                <th class="tc" style="background:#F0FDF4;color:#15803D;font-size:11px;">Terbit</th>
                                <th class="tc" style="background:#F1F5F9;color:#64748B;font-size:11px;">Pending</th>
                                <th class="tc" style="background:#FFF9EC;color:#B86800;font-size:11px;">Pengajuan</th>
                                <th class="tc" style="background:#F0FDF4;color:#15803D;font-size:11px;">Dibayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($koordinator['enumerators'] as $index => $enum)
                                <tr>
                                    <td><span class="adm-rownum">{{ $index + 1 }}</span></td>
                                    <td style="font-weight:600;">{{ $enum['nama_enumerator'] }}</td>
                                    <td class="tc" style="font-weight:700;color:var(--adm-blue);">{{ $enum['total_data'] }}</td>
                                    @foreach (['pending','revisi','progress_oss','progress_sihalal','terbit_sh'] as $s)
                                        <td class="tc">{{ $enum['status'][$s] > 0 ? $enum['status'][$s] : '—' }}</td>
                                    @endforeach
                                    @foreach (['pending','pengajuan','dibayar'] as $p)
                                        <td class="tc">{{ $enum['status_pembayaran'][$p] > 0 ? $enum['status_pembayaran'][$p] : '—' }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="font-weight:700;background:var(--adm-blue-lt);">
                                <td colspan="2" class="tr" style="font-size:12px;color:var(--adm-text-muted);">Subtotal</td>
                                <td class="tc" style="color:var(--adm-blue);">{{ $koordinator['total_data'] }}</td>
                                <td class="tc">{{ $koordinator['total_pending'] }}</td>
                                <td class="tc">{{ $koordinator['total_revisi'] }}</td>
                                <td class="tc">{{ $koordinator['total_progress_oss'] }}</td>
                                <td class="tc">{{ $koordinator['total_progress_sihalal'] }}</td>
                                <td class="tc" style="color:var(--adm-green);">{{ $koordinator['total_terbit_sh'] }}</td>
                                <td class="tc">{{ $koordinator['total_pembayaran_pending'] }}</td>
                                <td class="tc">{{ $koordinator['total_pembayaran_pengajuan'] }}</td>
                                <td class="tc" style="color:var(--adm-green);">{{ $koordinator['total_pembayaran_dibayar'] }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endforeach

        {{-- ── GRAND TOTAL ── --}}
        <div class="adm-card" style="border-color:var(--adm-blue);background:var(--adm-blue-lt);">
            <div class="adm-card-header">
                <div class="adm-card-title" style="color:var(--adm-blue);">
                    <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    Grand Total
                </div>
            </div>
            <div class="table-responsive" style="padding:0 20px 20px;">
                <table class="adm-table" style="font-size:12.5px;">
                    <thead>
                        <tr>
                            <th>Total Data</th>
                            <th class="tc" style="color:#B86800;">Pending</th>
                            <th class="tc" style="color:#DC2626;">Revisi</th>
                            <th class="tc" style="color:#0369A1;">OSS</th>
                            <th class="tc" style="color:#2563EB;">SiHalal</th>
                            <th class="tc" style="color:var(--adm-green);">Terbit SH</th>
                            <th class="tc" style="color:#64748B;">Pmt Pending</th>
                            <th class="tc" style="color:#B86800;">Pmt Pengajuan</th>
                            <th class="tc" style="color:var(--adm-green);">Pmt Dibayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="font-weight:700;font-size:15px;">
                            <td style="color:var(--adm-blue);">{{ $laporanPerKoordinator->sum('total_data') }}</td>
                            <td class="tc">{{ $laporanPerKoordinator->sum('total_pending') }}</td>
                            <td class="tc">{{ $laporanPerKoordinator->sum('total_revisi') }}</td>
                            <td class="tc">{{ $laporanPerKoordinator->sum('total_progress_oss') }}</td>
                            <td class="tc">{{ $laporanPerKoordinator->sum('total_progress_sihalal') }}</td>
                            <td class="tc" style="color:var(--adm-green);">{{ $laporanPerKoordinator->sum('total_terbit_sh') }}</td>
                            <td class="tc">{{ $laporanPerKoordinator->sum('total_pembayaran_pending') }}</td>
                            <td class="tc">{{ $laporanPerKoordinator->sum('total_pembayaran_pengajuan') }}</td>
                            <td class="tc" style="color:var(--adm-green);">{{ $laporanPerKoordinator->sum('total_pembayaran_dibayar') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    @endif
</div>

{{-- ── JS Libraries (dipertahankan) ── --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if ($tipeFilter == 'bulanan' && $grafikHarian)
        const ctx = document.getElementById('chartHarian');
        const grafikData = @json($grafikHarian);
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: grafikData.map(item => { const d = new Date(item.tanggal); return d.getDate()+'/'+(d.getMonth()+1); }),
                datasets: [{ label: 'Total Data Per Hari', data: grafikData.map(item => item.total), borderColor:'rgb(26,95,200)', backgroundColor:'rgba(26,95,200,0.1)', tension:0.3, fill:true }]
            },
            options: { responsive:true, maintainAspectRatio:true, plugins:{legend:{display:true,position:'top'}}, scales:{y:{beginAtZero:true,ticks:{stepSize:1}}} }
        });
    @endif

    document.querySelectorAll('input[name="tipeFilter"]').forEach(r => {
        r.addEventListener('change', function() {
            document.getElementById('wrapTanggal').style.display = this.value === 'harian' ? '' : 'none';
            document.getElementById('wrapBulan').style.display = this.value === 'bulanan' ? '' : 'none';
            applyFilter();
        });
    });
    document.querySelectorAll('input[name="tipeData"]').forEach(r => r.addEventListener('change', applyFilter));

    function applyFilter() {
        const tipeFilter = document.querySelector('input[name="tipeFilter"]:checked').value;
        const tipeData = document.querySelector('input[name="tipeData"]:checked').value;
        const koordinatorId = document.getElementById('filterKoordinator').value;
        const tanggal = document.getElementById('filterTanggal').value;
        const bulan = document.getElementById('filterBulan').value;
        let url = "{{ route($routePrefix . '.laporan-harian.index') }}?tipe=" + tipeFilter + "&tipe_data=" + tipeData;
        url += tipeFilter === 'harian' ? "&tanggal=" + tanggal : "&bulan=" + bulan;
        if (koordinatorId) url += "&koordinator_id=" + koordinatorId;
        window.location.href = url;
    }
    document.getElementById('filterTanggal').addEventListener('change', applyFilter);
    document.getElementById('filterBulan').addEventListener('change', applyFilter);
    document.getElementById('filterKoordinator').addEventListener('change', applyFilter);

    function downloadKoordinatorImage(koordinatorId, namaKoordinator) {
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<svg viewBox="0 0 24 24" style="width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2;animation:spin 1s linear infinite;"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg> Memproses...';
        const noPrint = document.querySelectorAll('.no-print');
        noPrint.forEach(el => el.style.display = 'none');
        const element = document.getElementById('koordinator-' + koordinatorId);
        html2canvas(element, { scale:2, logging:false, useCORS:true, backgroundColor:'#ffffff' }).then(canvas => {
            noPrint.forEach(el => el.style.display = '');
            button.disabled = false; button.innerHTML = originalHTML;
            canvas.toBlob(function(blob) {
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                const tipeFilter = document.querySelector('input[name="tipeFilter"]:checked').value;
                const tipeData = document.querySelector('input[name="tipeData"]:checked').value;
                const tanggal = document.getElementById('filterTanggal').value;
                const bulan = document.getElementById('filterBulan').value;
                let filename = 'laporan-' + namaKoordinator.replace(/\s+/g,'-') + '-' + tipeData + '-' + tipeFilter + '-';
                filename += (tipeFilter === 'harian' ? tanggal : bulan) + '.png';
                link.download = filename; link.href = url; link.click();
                if (typeof Swal !== 'undefined') { Swal.fire({ icon:'success', title:'Berhasil!', text:'Laporan '+namaKoordinator+' berhasil didownload', timer:2000, showConfirmButton:false }); }
                else { alert('Laporan '+namaKoordinator+' berhasil didownload!'); }
            });
        }).catch(error => {
            noPrint.forEach(el => el.style.display = '');
            button.disabled = false; button.innerHTML = originalHTML;
            if (typeof Swal !== 'undefined') { Swal.fire({ icon:'error', title:'Gagal!', text:'Terjadi kesalahan saat menggenerate gambar' }); }
            else { alert('Gagal menggenerate gambar!'); }
            console.error('Error:', error);
        });
    }
</script>
<style>
    @keyframes spin { from { transform:rotate(0deg); } to { transform:rotate(360deg); } }
    .adm-toggle-btn { padding:6px 14px; font-size:12.5px; font-weight:600; cursor:pointer; background:#fff; color:var(--adm-text-muted); transition:all .15s; border:none; }
    .adm-toggle-btn:hover { background:var(--adm-blue-lt); color:var(--adm-blue); }
    @media print { .no-print { display:none !important; } }
</style>
@endsection
