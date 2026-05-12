<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Daftar Enumerator</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #111;
            background: #fff;
            line-height: 1.4;
            padding: 20px 28px;
            /* ← margin kiri kanan diatur di sini */
        }

        /* ── KOP SURAT ── */
        .kop-table {
            width: 100%;
            border-bottom: 3px solid #1e3a8a;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .kop-table td {
            vertical-align: middle;
        }

        .kop-logo {
            width: 66px;
        }

        .kop-logo img {
            width: 56px;
            height: 56px;
        }

        .kop-fallback {
            width: 52px;
            height: 52px;
            background: #1e3a8a;
            border-radius: 7px;
            text-align: center;
            padding-top: 11px;
        }

        .kop-fallback span {
            color: #fff;
            font-size: 17px;
            font-weight: 800;
        }

        .kop-org {
            font-size: 15px;
            font-weight: 800;
            color: #1e3a8a;
            letter-spacing: 0.3px;
            line-height: 1.2;
        }

        .kop-sub {
            font-size: 8.5px;
            color: #475569;
            margin-top: 2px;
        }

        .kop-sub2 {
            font-size: 8px;
            color: #64748b;
            margin-top: 1px;
        }

        /* ── JUDUL ── */
        .report-title-wrap {
            text-align: center;
            margin: 10px 0 3px;
        }

        .report-title {
            font-size: 12px;
            font-weight: 700;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .report-subtitle {
            font-size: 9px;
            color: #475569;
            margin-top: 2px;
        }

        .report-divider {
            border: none;
            border-top: 1px solid #cbd5e1;
            margin: 6px 0 10px;
        }

        /* ── INFO LAPORAN ── */
        .info-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }

        .info-table td {
            font-size: 9px;
            padding: 2px 0;
            vertical-align: top;
            color: #334155;
        }

        .info-table td.key {
            width: 110px;
            font-weight: 600;
            color: #1e293b;
        }

        .info-table td.sep {
            width: 8px;
        }

        .info-table td.gap {
            width: 24px;
        }

        /* ── RINGKASAN ── */
        .ringkasan-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .ring-head td {
            width: 33.33%;
            padding: 5px 10px;
            text-align: center;
        }

        .ring-head td.h-total {
            background: #1e3a8a;
        }

        .ring-head td.h-aktif {
            background: #15803d;
        }

        .ring-head td.h-nonaktif {
            background: #b91c1c;
        }

        .ring-lbl {
            font-size: 8px;
            font-weight: 700;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .ring-val-row td {
            text-align: center;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
        }

        .ring-val {
            font-size: 22px;
            font-weight: 800;
            line-height: 1;
            display: block;
        }

        .ring-val.blue {
            color: #1e3a8a;
        }

        .ring-val.green {
            color: #15803d;
        }

        .ring-val.red {
            color: #b91c1c;
        }

        .ring-desc {
            font-size: 7.5px;
            color: #64748b;
            display: block;
            margin-top: 2px;
        }

        /* ── MAIN TABLE ── */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .main-table thead tr {
            background: #1e3a8a;
            color: #fff;
        }

        .main-table thead th {
            padding: 6px 8px;
            font-size: 9px;
            font-weight: 600;
            text-align: left;
            border: 1px solid #1e3a8a;
            white-space: nowrap;
        }

        .main-table thead th.tc {
            text-align: center;
        }

        .main-table tbody td {
            padding: 5px 8px;
            font-size: 9px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
            color: #1e293b;
        }

        .main-table tbody tr.even td {
            background: #f8fafc;
        }

        .main-table tbody td.tc {
            text-align: center;
        }

        .main-table tbody td.mono {
            font-family: 'Courier New', monospace;
            font-size: 9px;
            text-align: center;
        }

        /* ── BADGE ── */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: 700;
            white-space: nowrap;
        }

        .badge-aktif {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }

        .badge-nonaktif {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        /* ── DATA BULAN ── */
        .bulan-chip {
            display: inline-block;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            border-radius: 3px;
            padding: 1px 7px;
            font-size: 8.5px;
            font-weight: 600;
            white-space: nowrap;
        }

        .bulan-empty {
            color: #94a3b8;
            font-size: 9px;
        }

        /* ── NO DATA ── */
        .no-data {
            text-align: center;
            padding: 20px;
            color: #94a3b8;
            font-size: 9px;
            border: 1px solid #e2e8f0;
        }

        /* ── TANDA TANGAN ── */
        .ttd-table {
            width: 100%;
            margin-top: 20px;
        }

        .ttd-table td {
            font-size: 9px;
            color: #334155;
            vertical-align: top;
        }

        .ttd-right {
            text-align: right;
        }

        .ttd-space {
            height: 48px;
        }

        .ttd-name {
            font-weight: 700;
            font-size: 9.5px;
            color: #1e293b;
            border-top: 1px solid #334155;
            padding-top: 4px;
            display: inline-block;
            min-width: 140px;
            text-align: center;
        }

        .ttd-role {
            font-size: 8px;
            color: #64748b;
        }

        /* ── FOOTER ── */
        .footer-wrap {
            border-top: 1px solid #cbd5e1;
            padding-top: 5px;
            margin-top: 10px;
        }

        .footer-table {
            width: 100%;
        }

        .footer-table td {
            font-size: 7.5px;
            color: #94a3b8;
        }

        .footer-table td.right {
            text-align: right;
        }

        @page {
            size: A4 portrait;
            margin: 0;
            /* biar padding di body yang control */
        }
    </style>
</head>

<body>

    {{-- ── KOP SURAT ── --}}
    @php
        $logoPath = public_path('assets/images/logo_kawulo_halal.png');
        $logoSrc = '';
        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoMime = mime_content_type($logoPath);
            $logoSrc = 'data:' . $logoMime . ';base64,' . $logoData;
        }
    @endphp
    <table class="kop-table">
        <tr>
            <td class="kop-logo">
                @if ($logoSrc)
                    <img src="{{ $logoSrc }}" alt="Logo">
                @else
                    <div class="kop-fallback"><span>KH</span></div>
                @endif
            </td>
            <td style="padding-left:12px;">
                <div class="kop-org">KAWULO HALAL</div>
                <div class="kop-sub2">Nouble House 18th Floor, Kuningan, Jakarta Selatan 12950 | www.kawulohalal.id | +62
                    897-6774-482</div>
            </td>
        </tr>
    </table>

    {{-- ── JUDUL LAPORAN ── --}}
    <div class="report-title-wrap">
        <div class="report-title">Laporan Pendamping Kawulo Halal</div>
        <div class="report-subtitle">Periode: {{ now()->locale('id')->isoFormat('MMMM YYYY') }}</div>
    </div>
    <hr class="report-divider">

    {{-- ── INFO LAPORAN ── --}}
    @php
        $total = $enumerators->count();
        $aktif = $enumerators->where('status', 'Aktif')->count();
        $nonAktif = $enumerators->where('status', 'Tidak Aktif')->count();
    @endphp
    <table class="info-table">
        <tr>
            <td class="key">Nomor Laporan</td>
            <td class="sep">:</td>
            <td>{{ str_pad($total, 3, '0', STR_PAD_LEFT) }}/YPBP-KH/{{ now()->year }}</td>
            </td>
            <td class="gap"></td>
            <td class="key">Tanggal Cetak</td>
            <td class="sep">:</td>
            <td>{{ $exportedAt }}</td>
        </tr>
        <tr>
            <td class="key">Jenis Laporan</td>
            <td class="sep">:</td>
            <td>Rekap Data Pendamping Lapangan</td>
            <td class="gap"></td>
            <td class="key">Periode Data</td>
            <td class="sep">:</td>
            <td>{{ now()->locale('id')->isoFormat('MMMM YYYY') }}</td>
        </tr>
    </table>

    {{-- ── RINGKASAN ── --}}
    @php
        $total = $enumerators->count();
    @endphp
    <table class="ringkasan-table">
        <tr class="ring-head">
            <td class="h-total" style="width:50%;"><span class="ring-lbl">Total Enumerator Aktif</span></td>
            <td class="h-aktif" style="width:50%;"><span class="ring-lbl">Periode Data</span></td>
        </tr>
        <tr class="ring-val-row">
            <td style="width:50%;">
                <span class="ring-val blue">{{ $total }}</span>
                <span class="ring-desc">Enumerator aktif bertugas</span>
            </td>
            <td style="width:50%;">
                <span class="ring-val" style="font-size:14px;color:#15803d;">
                    {{ now()->locale('id')->isoFormat('MMMM YYYY') }}
                </span>
                <span class="ring-desc">Bulan laporan</span>
            </td>
        </tr>
    </table>

    {{-- ── TABEL DATA ── --}}
    <table class="main-table">
        <thead>
            <tr>
                <th class="tc" style="width:26px;">No</th>
                <th class="tc" style="width:72px;">No Registrasi</th>
                <th>Nama Lengkap</th>
                <th class="tc" style="width:95px;">Data Masuk<br>({{ now()->locale('id')->isoFormat('MMM YYYY') }})
                </th>
                <th class="tc" style="width:65px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($enumerators as $i => $enumerator)
                <tr class="{{ $i % 2 === 1 ? 'even' : '' }}">
                    <td class="tc">{{ $i + 1 }}</td>
                    <td class="mono">{{ $enumerator->no_registrasi }}</td>
                    <td>{{ $enumerator->nama_lengkap }}</td>
                    <td class="tc">
                        @if ($enumerator->data_per_bulan->isNotEmpty())
                            @foreach ($enumerator->data_per_bulan as $dp)
                                <span class="bulan-chip">{{ $dp->total }} data</span>
                            @endforeach
                        @else
                            <span class="bulan-empty">—</span>
                        @endif
                    </td>
                    <td class="tc">
                        @if ($enumerator->status === 'Aktif')
                            <span class="badge badge-aktif">Aktif</span>
                        @else
                            <span class="badge badge-nonaktif">Tidak Aktif</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="no-data">Tidak ada data enumerator yang ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ── TANDA TANGAN ── --}}
    <table class="ttd-table">
        <tr>
            <td></td>
            <td class="ttd-right">
                <div>Dibuat di: Jakarta</div>
                <div>Pada tanggal: {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}</div>
            </td>
        </tr>
    </table>

    {{-- ── FOOTER ── --}}
    <div class="footer-wrap">
        <table class="footer-table">
            <tr>
                <td>Dokumen ini digenerate secara otomatis oleh sistem informasi Kawulo Halal.</td>
                <td class="right">&copy; {{ date('Y') }} Kawulo Halal. Semua hak dilindungi.</td>
            </tr>
        </table>
    </div>

</body>

</html>
