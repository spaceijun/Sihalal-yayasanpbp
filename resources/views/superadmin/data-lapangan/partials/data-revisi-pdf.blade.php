<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Data Revisi Per Pendamping</title>
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
            width: 130px;
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
            margin-bottom: 14px;
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

        /* ── GROUP HEADER ── */
        .group-header {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 3px;
            padding: 6px 10px;
            margin-bottom: 4px;
            margin-top: 14px;
        }

        .group-header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .group-nama {
            font-size: 10px;
            font-weight: 700;
            color: #1e3a8a;
        }

        .group-meta {
            font-size: 8px;
            color: #475569;
            margin-top: 2px;
        }

        .group-badge-count {
            display: inline-block;
            background: #1e3a8a;
            color: #fff;
            border-radius: 10px;
            padding: 2px 8px;
            font-size: 8px;
            font-weight: 700;
        }

        /* ── TABEL UTAMA ── */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        .main-table thead tr {
            background: #1e3a8a;
            color: #fff;
        }

        .main-table thead th {
            padding: 5px 8px;
            font-size: 8.5px;
            font-weight: 600;
            text-align: left;
            border: 1px solid #1e3a8a;
            white-space: nowrap;
        }

        .main-table thead th.tc {
            text-align: center;
        }

        .main-table tbody td {
            padding: 4px 8px;
            font-size: 8.5px;
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

        /* ── SUBTOTAL ROW ── */
        .subtotal-row td {
            background: #f1f5f9;
            font-weight: 700;
            font-size: 8.5px;
            color: #1e293b;
            padding: 4px 8px;
            border: 1px solid #cbd5e1;
        }

        .subtotal-row td.tc {
            text-align: center;
        }

        /* ── BADGE ── */
        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: 700;
            white-space: nowrap;
        }

        .badge-revisi {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        .badge-selesai {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }

        /* ── GRAND TOTAL ── */
        .grand-total-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
            border: 1.5px solid #1e3a8a;
        }

        .grand-total-table td {
            padding: 6px 10px;
            font-size: 9px;
            font-weight: 700;
            color: #1e293b;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
        }

        .grand-total-table td.tc {
            text-align: center;
        }

        .grand-total-table .gt-head {
            background: #1e3a8a;
            color: #fff;
            font-size: 9px;
            font-weight: 700;
            text-align: center;
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
            margin-top: 14px;
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
        function logoToBase64(string $path): string
        {
            try {
                $full = public_path($path);
                if (!file_exists($full) || !is_readable($full)) {
                    return '';
                }
                return 'data:' . mime_content_type($full) . ';base64,' . base64_encode(file_get_contents($full));
            } catch (\Exception $e) {
                return '';
            }
        }

        $logoKiriSrc = 'https://kawulohalal.id/assets/images/logo-pbp.png';
        $logoKananSrc = 'https://kawulohalal.id/assets/images/logo_kawulo_halal.png';

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
        $iconUser = fetchIconBase64('https://cdn-icons-png.flaticon.com/16/1077/1077114.png');

        // ── Variabel ringkasan ──────────────────────────────────────────────────
        $totalData = $dataLapangans->count();
        $totalEnumerator = $grouped->count();
        $totalRevisi = $dataLapangans->where('status', 'Revisi')->count();
        $bulan = now()->locale('id')->isoFormat('MM');
        $tahun = now()->year;
    @endphp

    {{-- ── HEADER / KOP ── --}}
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
                    style="background:#1e3a8a; border-radius:4px; padding:5px 14px;
                           text-align:center; font-size:8px; color:#ffffff; font-weight:600;">
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
                                style="text-align:center; vertical-align:middle; padding:0 8px; font-weight:800; white-space:nowrap;">
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
                                style="text-align:center; vertical-align:middle; padding:0 8px; font-weight:800; white-space:nowrap;">
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

    <div style="margin-bottom:10px;"></div>

    {{-- ── JUDUL LAPORAN ── --}}
    <div class="report-title-wrap">
        <div class="report-title">Laporan Data Revisi Per Pendamping</div>
        <div class="report-subtitle">Dicetak pada: {{ $exportedAt }}</div>
    </div>
    <hr class="report-divider">

    {{-- ── INFO LAPORAN ── --}}
    <table class="info-table">
        <tr>
            <td class="key">Nomor Laporan</td>
            <td class="sep">:</td>
            <td>{{ str_pad($totalData, 3, '0', STR_PAD_LEFT) }}/YPBP-KH/REV/{{ $bulan }}/{{ $tahun }}</td>
            <td class="gap"></td>
            <td class="key">Tanggal Cetak</td>
            <td class="sep">:</td>
            <td>{{ $exportedAt }}</td>
        </tr>
        <tr>
            <td class="key">Jenis Laporan</td>
            <td class="sep">:</td>
            <td>Rekap Data Revisi Per Pendamping</td>
            <td class="gap"></td>
            <td class="key">Total Pendamping</td>
            <td class="sep">:</td>
            <td>{{ $totalEnumerator }} pendamping</td>
        </tr>
        <tr>
            <td class="key">Total Data Revisi</td>
            <td class="sep">:</td>
            <td>{{ $totalData }} data</td>
            <td class="gap"></td>
            <td class="key">Rata-rata per Pendamping</td>
            <td class="sep">:</td>
            <td>{{ $totalEnumerator > 0 ? number_format($totalData / $totalEnumerator, 1) : 0 }} data</td>
        </tr>
    </table>

    {{-- ── RINGKASAN ── --}}
    <table class="ringkasan-table">
        <tr class="ring-head">
            <td style="width:33.33%; background:#1e3a8a; padding:5px 10px; text-align:center;">
                <span class="ring-lbl">Total Pendamping</span>
            </td>
            <td style="width:33.33%; background:#b91c1c; padding:5px 10px; text-align:center;">
                <span class="ring-lbl">Total Data Revisi</span>
            </td>
            <td style="width:33.33%; background:#d97706; padding:5px 10px; text-align:center;">
                <span class="ring-lbl">Rata-rata per Pendamping</span>
            </td>
        </tr>
        <tr class="ring-val-row">
            <td style="width:33.33%;">
                <span class="ring-val" style="color:#1e3a8a;">{{ $totalEnumerator }}</span>
                <span class="ring-desc">Pendamping memiliki data revisi</span>
            </td>
            <td style="width:33.33%;">
                <span class="ring-val" style="color:#b91c1c;">{{ $totalData }}</span>
                <span class="ring-desc">Total seluruh data revisi</span>
            </td>
            <td style="width:33.33%;">
                <span class="ring-val" style="color:#d97706;">
                    {{ $totalEnumerator > 0 ? number_format($totalData / $totalEnumerator, 1) : 0 }}
                </span>
                <span class="ring-desc">Data revisi per pendamping</span>
            </td>
        </tr>
    </table>

    {{-- ── DATA PER ENUMERATOR ── --}}
    @php $noGlobal = 1; @endphp

    @forelse($grouped as $namaEnumerator => $items)
        @php
            $jumlahData = $items->count();
            $jumlahRevisi = $items->where('status', 'Revisi')->count();
            $enumerator = $items->first()->enumerator;
        @endphp

        {{-- Group Header --}}
        <div class="group-header">
            <table class="group-header-table">
                <tr>
                    <td>
                        <div class="group-nama">
                            @if ($iconUser)
                                <img src="{{ $iconUser }}"
                                    style="width:11px;height:11px;vertical-align:middle;margin-right:4px;">
                            @endif
                            <span style="vertical-align:middle;">{{ $namaEnumerator }}</span>
                        </div>
                        <div class="group-meta">
                            No. Registrasi: KH-{{ $enumerator->no_registrasi ?? '-' }}
                            &nbsp;&nbsp;|&nbsp;&nbsp;
                            Wilayah: Jawa Tengah SEHATI 2026
                        </div>
                    </td>
                    <td style="text-align:right; vertical-align:middle; white-space:nowrap;">
                        <span class="group-badge-count">{{ $jumlahData }} Data Revisi</span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Tabel data per enumerator --}}
        <table class="main-table">
            <thead>
                <tr>
                    <th class="tc" style="width:28px;">No</th>
                    <th style="width:75px;">Tanggal</th>
                    <th>Nama PU</th>
                    <th class="tc" style="width:55px;">Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $j => $item)
                    <tr class="{{ $j % 2 === 1 ? 'even' : '' }}">
                        <td class="tc">{{ $noGlobal++ }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}</td>
                        <td>{{ $item->nama_pu }}</td>
                        <td class="tc">
                            <span class="badge {{ $item->status === 'Revisi' ? 'badge-revisi' : 'badge-selesai' }}">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                    </tr>
                @endforeach

                {{-- Subtotal per enumerator --}}
                <tr class="subtotal-row">
                    <td colspan="3" style="text-align:right;">
                        Subtotal &mdash; {{ $namaEnumerator }}
                    </td>
                    <td class="tc">{{ $jumlahRevisi }} Revisi</td>
                    <td>{{ $jumlahData }} data total</td>
                </tr>
            </tbody>
        </table>

    @empty
        <p class="no-data">Tidak ada data revisi.</p>
    @endforelse

    {{-- ── GRAND TOTAL ── --}}
    <table class="grand-total-table">
        <tr>
            <td class="gt-head" colspan="4">REKAPITULASI KESELURUHAN</td>
        </tr>
        <tr>
            <td style="width:40%;">Total Pendamping dengan Data Revisi</td>
            <td class="tc" style="width:20%;">{{ $totalEnumerator }} pendamping</td>
            <td style="width:25%;">Total Seluruh Data Revisi</td>
            <td class="tc" style="width:15%;">{{ $totalData }} data</td>
        </tr>
    </table>

    {{-- ── TANDA TANGAN ── --}}
    <table style="width:100%; margin-top:20px;">
        <tr>
            <td></td>
            <td style="text-align:right; font-size:9px; color:#334155; vertical-align:top;">
                <div>Best Regards,</div>
                <div>Kawulo Halal Management</div>
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
