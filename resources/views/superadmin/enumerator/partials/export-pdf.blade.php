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
        }

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

        .kop-sub2 {
            font-size: 8px;
            color: #64748b;
            margin-top: 1px;
        }

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

        .ring-desc {
            font-size: 7.5px;
            color: #64748b;
            display: block;
            margin-top: 2px;
        }

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

        .badge-aman {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }

        .badge-warning {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

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

        .no-data {
            text-align: center;
            padding: 20px;
            color: #94a3b8;
            font-size: 9px;
            border: 1px solid #e2e8f0;
        }

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
        }
    </style>
</head>

<body>

    {{-- ── KOP SURAT ── --}}
    @php
        $logoKiriSrc = 'https://kawulohalal.id/assets/images/logo-pbp.png';
        $logoKananSrc = 'https://kawulohalal.id/assets/images/logo_kawulo_halal.png';
    @endphp

    <table style="width:100%; border-collapse:collapse; padding-bottom:6px; margin-bottom:4px;">
        <tr>
            {{-- Logo Kiri --}}
            <td style="width:70px; text-align:center; vertical-align:middle;">
                @if ($logoKiriSrc)
                    <img src="{{ $logoKiriSrc }}" alt="Logo Yayasan" style="width:62px; height:62px; object-fit:contain;">
                @else
                    <div
                        style="width:52px;height:52px;background:#1e3a8a;border-radius:7px;text-align:center;padding-top:11px;">
                        <span style="color:#fff;font-size:17px;font-weight:800;">YP</span>
                    </div>
                @endif
            </td>

            {{-- Teks Tengah --}}
            <td style="text-align:center; vertical-align:middle; padding:0 12px;">
                <div
                    style="font-size:16px; font-weight:800; color:#1e3a8a; letter-spacing:1px; line-height:1.3; text-transform:uppercase;">
                    Yayasan Permata Bakti Pertiwi
                </div>
                <div style="font-size:16px; font-weight:800; color:#1e3a8a; letter-spacing:0.8px; line-height:1.3;">
                    "KAWULO HALAL"
                </div>
                <div style="font-size:11px; color:#475569; margin-top:4px;">
                    Menyelenggarakan Pelayanan :
                </div>
                <div style="font-size:11px; color:#334155; font-style:italic;">
                    Jasa Konsultasi Sertifikasi Produk Halal Untuk UMKM <em>Low-Risk</em>
                </div>
            </td>

            {{-- Logo Kanan --}}
            <td style="width:85px; text-align:center; vertical-align:middle;">
                @if ($logoKananSrc)
                    <img src="{{ $logoKananSrc }}" alt="Logo Kawulo Halal"
                        style="width:65px; height:65px; object-fit:contain;">
                @else
                    <div
                        style="width:52px;height:52px;background:#1e3a8a;border-radius:7px;text-align:center;padding-top:11px;margin:0 auto;">
                        <span style="color:#fff;font-size:17px;font-weight:800;">KH</span>
                    </div>
                @endif
            </td>
        </tr>

        @php
            // Fetch icon dari internet -> base64 (agar DomPDF bisa render)
            function fetchIconBase64(string $url): string
            {
                try {
                    $data = @file_get_contents($url);
                    if (!$data) {
                        return '';
                    }
                    $mime = 'image/png';
                    return 'data:' . $mime . ';base64,' . base64_encode($data);
                } catch (\Exception $e) {
                    return '';
                }
            }

            // Icon dari flaticon / icons8 CDN (PNG transparan, ukuran kecil)
            $iconLokasi = fetchIconBase64('https://cdn-icons-png.flaticon.com/16/684/684908.png');
            $iconTelp = fetchIconBase64('https://cdn-icons-png.flaticon.com/16/597/597177.png');
            $iconWeb = fetchIconBase64('https://cdn-icons-png.flaticon.com/16/364/364089.png');
        @endphp

        {{-- Strip info alamat --}}
        <tr>
            <td colspan="3" style="padding-top:7px;">
                <div
                    style="
            background: #1e3a8a;
            border-radius: 4px;
            padding: 5px 14px;
            text-align: center;
            font-size: 8px;
            color: #ffffff;
            font-weight: 600;
        ">
                    @if ($iconLokasi)
                        <img src="{{ $iconLokasi }}"
                            style="width:10px;height:10px;vertical-align:middle;margin-right:2px;">
                    @else
                        &#9679;
                    @endif
                    Nouble House 18<sup style="font-size:6px;">th</sup> Floor, Kuningan, Jakarta Selatan 12950

                    &nbsp;&nbsp;<span style="color:#ffffff;font-weight:800;">|</span>&nbsp;&nbsp;

                    @if ($iconTelp)
                        <img src="{{ $iconTelp }}"
                            style="width:10px;height:10px;vertical-align:middle;margin-right:2px;">
                    @else
                        &#9679;
                    @endif
                    Telp : 0897 6774 482

                    &nbsp;&nbsp;<span style="color:#ffffff;font-weight:800;">|</span>&nbsp;&nbsp;

                    @if ($iconWeb)
                        <img src="{{ $iconWeb }}"
                            style="width:10px;height:10px;vertical-align:middle;margin-right:2px;">
                    @else
                        &#9679;
                    @endif
                    www.kawulohalal.id
                </div>
            </td>
        </tr>
    </table>
    <div style="margin-bottom: 10px;"></div>
    {{-- ── JUDUL LAPORAN ── --}}
    <div class="report-title-wrap">
        <div class="report-title">Laporan Pendamping Kawulo Halal</div>
        <div class="report-subtitle">Periode: {{ now()->locale('id')->isoFormat('MMMM YYYY') }}</div>
    </div>
    <hr class="report-divider">

    {{-- ── INFO LAPORAN ── --}}
    @php
        $total = $enumerators->count();
        $target = 20;
        $cutoffTgl = '25 ' . now()->locale('id')->isoFormat('MMMM YYYY');
    @endphp
    <table class="info-table">
        <tr>
            <td class="key">Nomor Laporan</td>
            <td class="sep">:</td>
            <td>{{ str_pad($total, 3, '0', STR_PAD_LEFT) }}/YPBP-KH/{{ now()->year }}</td>
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
        <tr>
            <td class="key">Target Data</td>
            <td class="sep">:</td>
            <td>{{ $target }} data / pendamping</td>
            <td class="gap"></td>
            <td class="key">Batas Nonaktif</td>
            <td class="sep">:</td>
            <td>{{ $cutoffTgl }}</td>
        </tr>
    </table>

    {{-- ── RINGKASAN ── --}}
    @php
        $memenuhi = $enumerators->filter(fn($e) => $e->total_data_bulan >= $target)->count();
        $belumMemenuhi = $total - $memenuhi;
    @endphp
    <table class="ringkasan-table">
        <tr class="ring-head">
            <td class="h-total" style="width:33.33%;"><span class="ring-lbl">Total Pendamping</span></td>
            <td class="h-aktif" style="width:33.33%;"><span class="ring-lbl">Memenuhi Target</span></td>
            <td style="background:#b91c1c;width:33.33%;"><span class="ring-lbl">Belum Memenuhi</span></td>
        </tr>
        <tr class="ring-val-row">
            <td style="width:33.33%;">
                <span class="ring-val blue">{{ $total }}</span>
                <span class="ring-desc">Pendamping aktif bertugas</span>
            </td>
            <td style="width:33.33%;">
                <span class="ring-val" style="color:#15803d;">{{ $memenuhi }}</span>
                <span class="ring-desc">≥ {{ $target }} data bulan ini</span>
            </td>
            <td style="width:33.33%;">
                <span class="ring-val" style="color:#b91c1c;">{{ $belumMemenuhi }}</span>
                <span class="ring-desc">Akan dinonaktifkan tanggal 25</span>
            </td>
        </tr>
    </table>

    {{-- ── TABEL DATA ── --}}
    <table class="main-table">
        <thead>
            <tr>
                <th class="tc" style="width:72px;">No Registrasi</th>
                <th>Nama Lengkap</th>
                <th class="tc" style="width:58px;">Target<br>Data</th>
                <th class="tc" style="width:58px;">Data<br>Masuk</th>
                <th class="tc" style="width:42px;">Kurang</th>
                <th style="width:145px;">Keterangan</th>
                <th class="tc" style="width:55px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($enumerators as $i => $enumerator)
                @php
                    $dataMasuk = $enumerator->total_data_bulan;
                    $kurang = max(0, $target - $dataMasuk);
                    $memenuhi = $dataMasuk >= $target;
                @endphp
                <tr class="{{ $i % 2 === 1 ? 'even' : '' }}">
                    <td class="mono">KH-{{ $enumerator->no_registrasi }}</td>
                    <td>{{ $enumerator->nama_lengkap }}</td>
                    <td class="tc">{{ $target }}</td>
                    <td class="tc">
                        @if ($dataMasuk > 0)
                            <span class="bulan-chip">{{ $dataMasuk }} Data</span>
                        @else
                            <span class="bulan-empty">—</span>
                        @endif
                    </td>
                    <td class="tc">
                        @if ($kurang > 0)
                            <span style="color:#b91c1c;font-weight:700;">{{ $kurang }} Data</span>
                        @else
                            <span style="color:#15803d;font-weight:700;">—</span>
                        @endif
                    </td>
                    <td>
                        @if ($memenuhi)
                            <span class="badge badge-aman">
                                Tidak dinonaktifkan tanggal 25 {{ now()->locale('id')->isoFormat('MMMM') }}
                            </span>
                        @else
                            <span class="badge badge-warning">
                                Dinonaktifkan tanggal 25 {{ now()->locale('id')->isoFormat('MMMM') }}
                            </span>
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
                    <td colspan="8" class="no-data">Tidak ada data enumerator yang ditemukan.</td>
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
                <td>Dokumen ini digenerate secara otomatis oleh Kawulo Halal Super Apps.</td>
                <td class="right">&copy; {{ date('Y') }} Kawulo Halal.</td>
            </tr>
        </table>
    </div>

</body>

</html>
