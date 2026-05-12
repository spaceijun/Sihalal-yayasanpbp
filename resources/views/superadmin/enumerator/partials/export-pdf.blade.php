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
            padding: 5px 10px;
            text-align: center;
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
        $logoKiriSrc = 'https://yourdomain.com/assets/images/logo-pbp.png';
        $logoKananSrc = 'https://yourdomain.com/assets/images/logo_kawulo_halal.png';

        function fetchIconBase64(string $url): string
        {
            try {
                $data = @file_get_contents($url);
                if (!$data) {
                    return '';
                }
                return 'data:image/png;base64,' . base64_encode($data);
            } catch (\Exception $e) {
                return '';
            }
        }

        $iconKantor = fetchIconBase64('https://cdn-icons-png.flaticon.com/16/1378/1378884.png');
        $iconTelp = fetchIconBase64('https://cdn-icons-png.flaticon.com/16/597/597177.png');
        $iconWeb = fetchIconBase64('https://cdn-icons-png.flaticon.com/16/364/364089.png');

        $periodeCarbon = \Carbon\Carbon::create($tahun, $bulan, 1)->locale('id');
        $totalData = $enumerators->count();
        $target_val = $target;
        $memenuhinya = $enumerators->filter(fn($e) => $e->total_data_bulan >= $target_val)->count();
        $belumMemenuhi = $totalData - $memenuhinya;
    @endphp

    <table style="width:100%; border-collapse:collapse; padding-bottom:6px; margin-bottom:4px;">
        <tr>
            {{-- Logo Kiri --}}
            <td style="width:70px; text-align:center; vertical-align:middle;">
                <img src="{{ $logoKiriSrc }}" alt="Logo Yayasan" style="width:62px; height:62px; object-fit:contain;">
            </td>

            {{-- Teks Tengah --}}
            <td style="text-align:center; vertical-align:middle; padding:0 12px;">
                <div
                    style="font-size:14px; font-weight:800; color:#1e3a8a; letter-spacing:1px; line-height:1.3; text-transform:uppercase;">
                    Yayasan Permata Bakti Pertiwi
                </div>
                <div style="font-size:13px; font-weight:800; color:#1e3a8a; letter-spacing:0.8px; line-height:1.3;">
                    "KAWULO HALAL"
                </div>
                <div style="font-size:8px; color:#475569; margin-top:4px;">
                    Menyelenggarakan Pelayanan :
                </div>
                <div style="font-size:8px; color:#334155; font-style:italic;">
                    Jasa Konsultasi Sertifikasi Produk Halal Untuk UMKM <em>Low-Risk</em>
                </div>
            </td>

            {{-- Logo Kanan --}}
            <td style="width:85px; text-align:center; vertical-align:middle;">
                <img src="{{ $logoKananSrc }}" alt="Logo Kawulo Halal"
                    style="width:56px; height:56px; object-fit:contain;">
            </td>
        </tr>

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
                    <table style="width:100%; border-collapse:collapse;">
                        <tr>
                            <td style="text-align:center; vertical-align:middle; white-space:nowrap;">
                                @if ($iconKantor)
                                    <img src="{{ $iconKantor }}"
                                        style="width:11px;height:11px;vertical-align:middle;margin-right:3px;">
                                @else
                                    &#9679;&nbsp;
                                @endif
                                <span style="vertical-align:middle;">
                                    Nouble House 18<sup style="font-size:6px;">th</sup> Floor, Kuningan, Jakarta Selatan
                                    12950
                                </span>
                            </td>
                            <td
                                style="text-align:center; vertical-align:middle; padding:0 8px; color:#ffff; font-weight:800; white-space:nowrap;">
                                |</td>
                            <td style="text-align:center; vertical-align:middle; white-space:nowrap;">
                                @if ($iconTelp)
                                    <img src="{{ $iconTelp }}"
                                        style="width:11px;height:11px;vertical-align:middle;margin-right:3px;">
                                @else
                                    &#9679;&nbsp;
                                @endif
                                <span style="vertical-align:middle;">Telp : 0897 6774 482</span>
                            </td>
                            <td
                                style="text-align:center; vertical-align:middle; padding:0 8px; color:#ffff; font-weight:800; white-space:nowrap;">
                                |</td>
                            <td style="text-align:center; vertical-align:middle; white-space:nowrap;">
                                @if ($iconWeb)
                                    <img src="{{ $iconWeb }}"
                                        style="width:11px;height:11px;vertical-align:middle;margin-right:3px;">
                                @else
                                    &#9679;&nbsp;
                                @endif
                                <span style="vertical-align:middle;">www.kawulohalal.id</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <div style="margin-bottom: 10px;"></div>

    {{-- ── JUDUL LAPORAN ── --}}
    <div class="report-title-wrap">
        <div class="report-title">Laporan Pendamping Kawulo Halal</div>
        <div class="report-subtitle">Periode: {{ $periodeLabel }}</div>
    </div>
    <hr class="report-divider">

    {{-- ── INFO LAPORAN ── --}}
    <table class="info-table">
        <tr>
            <td class="key">Nomor Laporan</td>
            <td class="sep">:</td>
            <td>{{ str_pad($totalData, 3, '0', STR_PAD_LEFT) }}/YPBP-KH/{{ $tahun }}</td>
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
            <td>{{ $periodeLabel }}</td>
        </tr>
        <tr>
            <td class="key">Target Data</td>
            <td class="sep">:</td>
            <td>{{ $target }} data / pendamping</td>
            <td class="gap"></td>
            <td class="key">Batas Nonaktif</td>
            <td class="sep">:</td>
            <td>25 {{ $periodeLabel }}</td>
        </tr>
    </table>

    {{-- ── RINGKASAN ── --}}
    <table class="ringkasan-table">
        <tr class="ring-head">
            <td style="width:33.33%; background:#1e3a8a; padding:5px 10px; text-align:center;">
                <span class="ring-lbl">Total Pendamping</span>
            </td>
            <td style="width:33.33%; background:#15803d; padding:5px 10px; text-align:center;">
                <span class="ring-lbl">Memenuhi Target</span>
            </td>
            <td style="width:33.33%; background:#b91c1c; padding:5px 10px; text-align:center;">
                <span class="ring-lbl">Belum Memenuhi</span>
            </td>
        </tr>
        <tr class="ring-val-row">
            <td style="width:33.33%;">
                <span class="ring-val" style="color:#1e3a8a;">{{ $totalData }}</span>
                <span class="ring-desc">Pendamping periode ini</span>
            </td>
            <td style="width:33.33%;">
                <span class="ring-val" style="color:#15803d;">{{ $memenuhinya }}</span>
                <span class="ring-desc">≥ {{ $target }} data bulan ini</span>
            </td>
            <td style="width:33.33%;">
                <span class="ring-val" style="color:#b91c1c;">{{ $belumMemenuhi }}</span>
                <span class="ring-desc">Akan dinonaktifkan tgl 25</span>
            </td>
        </tr>
    </table>

    {{-- ── TABEL DATA ── --}}
    <table class="main-table">
        <thead>
            <tr>
                <th class="tc" style="width:72px;">No Registrasi</th>
                <th>Nama Lengkap</th>
                <th class="tc" style="width:52px;">Target<br>Data</th>
                <th class="tc" style="width:62px;">Data Masuk<br>({{ $periodeCarbon->isoFormat('MMM YYYY') }})</th>
                <th class="tc" style="width:40px;">Kurang</th>
                <th style="width:150px;">Keterangan</th>
                <th class="tc" style="width:55px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($enumerators as $i => $enumerator)
                @php
                    $dataMasuk = $enumerator->total_data_bulan;
                    $kurang = max(0, $target - $dataMasuk);
                    $sudahCukup = $dataMasuk >= $target;
                    $namaBulan = $periodeCarbon->isoFormat('MMMM');
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
                            <span style="color:#b91c1c; font-weight:700;">{{ $kurang }} Data</span>
                        @else
                            <span style="color:#15803d; font-weight:700;">—</span>
                        @endif
                    </td>
                    <td>
                        @if ($sudahCukup)
                            <span class="badge badge-aman">
                                Tidak dinonaktifkan tgl 25 {{ $namaBulan }}
                            </span>
                        @else
                            <span class="badge badge-warning">
                                Dinonaktifkan tgl 25 {{ $namaBulan }}
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
    <table style="width:100%; margin-top:20px;">
        <tr>
            <td></td>
            <td style="text-align:right; font-size:9px; color:#334155; vertical-align:top;">
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
