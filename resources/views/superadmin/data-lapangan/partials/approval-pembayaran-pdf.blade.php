<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Approval Pembayaran</title>
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
            width: 120px;
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

        .main-table tbody tr.held-pending td {
            background: #fffbeb;
        }

        .main-table tbody tr.held-inaktif td {
            background: #fff5f5;
        }

        .main-table tbody td.tc {
            text-align: center;
        }

        .main-table tbody td.mono {
            font-family: 'Courier New', monospace;
            font-size: 8.5px;
            text-align: center;
        }

        .main-table tfoot td {
            padding: 6px 8px;
            font-size: 9px;
            font-weight: 700;
            border: 1px solid #cbd5e1;
            background: #f1f5f9;
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

        .badge-pending {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fcd34d;
        }

        .badge-pengajuan {
            background: #dbeafe;
            color: #1d4ed8;
            border: 1px solid #93c5fd;
        }

        .badge-hold {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
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

    @php
        function approvalLogoBase64(string $url): string
        {
            try {
                $data = @file_get_contents($url);
                return $data ? 'data:image/png;base64,' . base64_encode($data) : '';
            } catch (\Exception $e) {
                return '';
            }
        }

        $logoKiriSrc = 'https://kawulohalal.id/assets/images/logo-pbp.png';
        $logoKananSrc = 'https://kawulohalal.id/assets/images/logo_kawulo_halal.png';
        $iconKantor = approvalLogoBase64('https://cdn-icons-png.flaticon.com/16/1378/1378884.png');
        $iconTelp = approvalLogoBase64('https://cdn-icons-png.flaticon.com/16/597/597177.png');
        $iconWeb = approvalLogoBase64('https://cdn-icons-png.flaticon.com/16/364/364089.png');

        $totalData   = $items->count();

        // Bisa dibayar: status PENGAJUAN dan pendamping Aktif
        $bisa_dibayar   = $items->filter(fn($d) => $d['status_pembayaran'] === 'PENGAJUAN' && $d['enumerator_status'] === 'Aktif')->count();

        // Hold karena masih PENDING (belum diajukan ke keuangan)
        $ditahan_pending = $items->where('status_pembayaran', 'PENDING')->count();

        // Hold karena pendamping Tidak Aktif (walaupun sudah PENGAJUAN)
        $ditahan_inaktif = $items->filter(fn($d) => $d['enumerator_status'] === 'Tidak Aktif')->count();

        $totalDitahan   = $ditahan_pending + $ditahan_inaktif;
        $totalNominal   = $items->filter(fn($d) => $d['status_pembayaran'] === 'PENGAJUAN' && $d['enumerator_status'] === 'Aktif')->sum('nominal');

        // Group per pendamping
        $grouped = $items->groupBy('pendamping');
    @endphp

    {{-- ── KOP SURAT ── --}}
    <table style="width:100%; border-collapse:collapse; padding-bottom:6px; margin-bottom:4px;">
        <tr>
            <td style="width:70px; text-align:center; vertical-align:middle;">
                <img src="{{ $logoKiriSrc }}" alt="Logo Yayasan" style="width:62px; height:62px; object-fit:contain;">
            </td>
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
            <td style="width:85px; text-align:center; vertical-align:middle;">
                <img src="{{ $logoKananSrc }}" alt="Logo Kawulo Halal"
                    style="width:56px; height:56px; object-fit:contain;">
            </td>
        </tr>
        <tr>
            <td colspan="3" style="padding-top:7px;">
                <div
                    style="background:#1e3a8a; border-radius:4px; padding:5px 14px; text-align:center; font-size:8px; color:#ffffff; font-weight:600;">
                    <table style="width:100%; border-collapse:collapse;">
                        <tr>
                            <td style="text-align:center; vertical-align:middle; white-space:nowrap;">
                                @if ($iconKantor)
                                    <img src="{{ $iconKantor }}"
                                    style="width:11px;height:11px;vertical-align:middle;margin-right:3px;">@else&#9679;&nbsp;
                                @endif
                                <span style="vertical-align:middle;">Nouble House 18<sup style="font-size:6px;">th</sup>
                                    Floor, Kuningan, Jakarta Selatan 12950</span>
                            </td>
                            <td
                                style="text-align:center; vertical-align:middle; padding:0 8px; font-weight:800; white-space:nowrap;">
                                |</td>
                            <td style="text-align:center; vertical-align:middle; white-space:nowrap;">
                                @if ($iconTelp)
                                    <img src="{{ $iconTelp }}"
                                    style="width:11px;height:11px;vertical-align:middle;margin-right:3px;">@else&#9679;&nbsp;
                                @endif
                                <span style="vertical-align:middle;">Telp : 0897 6774 482</span>
                            </td>
                            <td
                                style="text-align:center; vertical-align:middle; padding:0 8px; font-weight:800; white-space:nowrap;">
                                |</td>
                            <td style="text-align:center; vertical-align:middle; white-space:nowrap;">
                                @if ($iconWeb)
                                    <img src="{{ $iconWeb }}"
                                    style="width:11px;height:11px;vertical-align:middle;margin-right:3px;">@else&#9679;&nbsp;
                                @endif
                                <span style="vertical-align:middle;">www.kawulohalal.id</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <div style="margin-bottom:10px;"></div>

    {{-- ── JUDUL LAPORAN ── --}}
    <div class="report-title-wrap">
        <div class="report-title">Laporan Approval Pembayaran — Data Siap Disetujui</div>
        <div class="report-subtitle">Data PENGAJUAN yang siap dibayar &amp; data PENDING yang masih di-hold</div>
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
            <td>Rekap Approval Pembayaran Pendamping</td>
            <td class="gap"></td>
            <td class="key">Total Data</td>
            <td class="sep">:</td>
            <td>{{ $totalData }} record</td>
        </tr>
        <tr>
            <td class="key">Siap Dibayar</td>
            <td class="sep">:</td>
            <td>{{ $bisa_dibayar }} data (PENGAJUAN + Pendamping Aktif)</td>
            <td class="gap"></td>
            <td class="key">Di-Hold</td>
            <td class="sep">:</td>
            <td>{{ $totalDitahan }} data ({{ $ditahan_pending }} PENDING, {{ $ditahan_inaktif }} Pend. Tdk Aktif)</td>
        </tr>
    </table>

    {{-- ── RINGKASAN ── --}}
    <table class="ringkasan-table">
        <tr class="ring-head">
            <td style="width:33.33%; background:#1e3a8a; padding:5px 10px; text-align:center;">
                <span class="ring-lbl">Siap Dibayar (PENGAJUAN)</span>
            </td>
            <td style="width:33.33%; background:#b45309; padding:5px 10px; text-align:center;">
                <span class="ring-lbl">Hold — Masih PENDING</span>
            </td>
            <td style="width:33.33%; background:#b91c1c; padding:5px 10px; text-align:center;">
                <span class="ring-lbl">Hold — Pend. Tidak Aktif</span>
            </td>
        </tr>
        <tr class="ring-val-row">
            <td style="width:33.33%;">
                <span class="ring-val" style="color:#1e3a8a;">{{ $bisa_dibayar }}</span>
                <span class="ring-desc">Rp {{ number_format($totalNominal, 0, ',', '.') }}</span>
            </td>
            <td style="width:33.33%;">
                <span class="ring-val" style="color:#b45309;">{{ $ditahan_pending }}</span>
                <span class="ring-desc">Belum diajukan ke keuangan</span>
            </td>
            <td style="width:33.33%;">
                <span class="ring-val" style="color:#b91c1c;">{{ $ditahan_inaktif }}</span>
                <span class="ring-desc">Pembayaran ditahan otomatis</span>
            </td>
        </tr>
    </table>

    {{-- ── TABEL DATA ── --}}
    <table class="main-table">
        <thead>
            <tr>
                <th class="tc" style="width:22px;">#</th>
                <th style="width:72px;">No. Reg</th>
                <th>Nama PU</th>
                <th class="mono tc" style="width:110px;">NIK</th>
                <th>Pendamping</th>
                <th class="tc" style="width:55px;">Status Pend.</th>
                <th class="tc" style="width:65px;">Status Bayar</th>
                <th class="tc" style="width:70px;">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $i => $d)
                @php
                    $isPending   = $d['status_pembayaran'] === 'PENDING';
                    $isInaktif   = $d['enumerator_status'] === 'Tidak Aktif';
                    $isHold      = $isPending || $isInaktif;
                    $rowClass    = $isPending  ? 'held-pending'
                                 : ($isInaktif ? 'held-inaktif'
                                 : ($i % 2 === 1 ? 'even' : ''));
                @endphp
                <tr class="{{ $rowClass }}">
                    <td class="tc">{{ $i + 1 }}</td>
                    <td class="mono">{{ $d['no_registrasi'] ?? '-' }}</td>
                    <td>{{ $d['nama_pu'] }}</td>
                    <td class="mono">{{ $d['nik'] }}</td>
                    <td>{{ $d['pendamping'] }}</td>
                    <td class="tc">
                        @if ($isInaktif)
                            <span class="badge badge-nonaktif">Tidak Aktif</span>
                        @else
                            <span class="badge badge-aktif">Aktif</span>
                        @endif
                    </td>
                    <td class="tc">
                        @if ($isPending)
                            <span class="badge badge-pending">Pending</span>
                        @else
                            <span class="badge badge-pengajuan">Pengajuan</span>
                        @endif
                    </td>
                    <td class="tc">
                        @if ($isPending)
                            <span class="badge badge-pending">&#9646; Belum Diajukan</span>
                        @elseif ($isInaktif)
                            <span class="badge badge-hold">&#9646; Ditahan</span>
                        @else
                            <span style="font-weight:700; color:#15803d;">{{ $d['nominal_fmt'] }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="no-data">Tidak ada data pembayaran yang ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
        @if ($items->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="7" style="text-align:right;">Total Nominal Dapat Dibayar</td>
                    <td class="tc" style="color:#15803d;">Rp {{ number_format($totalNominal, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        @endif
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
