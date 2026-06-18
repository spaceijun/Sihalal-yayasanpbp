<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Receipt Pembayaran #{{ str_pad($penagihan->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #1a1a2e;
            background: #ffffff;
            padding: 30px 40px;
        }

        /* ── HEADER ── */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 24px;
            padding-bottom: 18px;
            border-bottom: 3px solid #16213e;
        }

        .header-logo {
            display: table-cell;
            vertical-align: middle;
            width: 55%;
        }

        .header-meta {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #0f3460;
            letter-spacing: 1px;
        }

        .company-sub {
            font-size: 10px;
            color: #555;
            margin-top: 2px;
        }

        .receipt-no {
            font-size: 18px;
            font-weight: bold;
            color: #0f3460;
        }

        .receipt-label {
            font-size: 9px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .receipt-date {
            font-size: 10px;
            color: #555;
            margin-top: 3px;
        }

        /* ── STATUS BADGE ── */
        .status-badge {
            display: inline-block;
            background: #16a34a;
            color: #fff;
            font-size: 10px;
            font-weight: bold;
            padding: 3px 10px;
            border-radius: 20px;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }

        /* ── INFO ROW (2-column table) ── */
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            gap: 16px;
        }

        .info-box {
            display: table-cell;
            width: 48%;
            background: #f0f4f8;
            border-radius: 6px;
            padding: 14px 16px;
            vertical-align: top;
        }

        .info-box+.info-box {
            padding-left: 16px;
        }

        .info-box-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0f3460;
            margin-bottom: 8px;
            border-bottom: 1px solid #c5d3e0;
            padding-bottom: 5px;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .info-label {
            display: table-cell;
            width: 45%;
            color: #666;
            font-size: 10.5px;
        }

        .info-value {
            display: table-cell;
            font-weight: bold;
            font-size: 10.5px;
            color: #222;
        }

        /* ── DETAIL TABLE ── */
        .detail-title {
            font-size: 11px;
            font-weight: bold;
            color: #0f3460;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .detail-table thead tr {
            background: #0f3460;
            color: #fff;
        }

        .detail-table thead th {
            padding: 8px 12px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 0.3px;
        }

        .detail-table thead th.tr {
            text-align: right;
        }

        .detail-table tbody tr {
            border-bottom: 1px solid #e8ecf0;
        }

        .detail-table tbody tr:last-child {
            border-bottom: none;
        }

        .detail-table tbody td {
            padding: 9px 12px;
            font-size: 11px;
            vertical-align: middle;
        }

        .detail-table tbody td.tr {
            text-align: right;
        }

        .detail-table tfoot tr {
            background: #e8f5e9;
            border-top: 2px solid #16a34a;
        }

        .detail-table tfoot td {
            padding: 10px 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .detail-table tfoot td.tr {
            text-align: right;
            color: #16a34a;
        }

        /* ── TOTAL HIGHLIGHT ── */
        .total-section {
            background: linear-gradient(135deg, #0f3460 0%, #16213e 100%);
            color: #fff;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 20px;
            display: table;
            width: 100%;
        }

        .total-left {
            display: table-cell;
            vertical-align: middle;
        }

        .total-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
        }

        .total-label-text {
            font-size: 11px;
            color: #a8c4e0;
            margin-bottom: 2px;
        }

        .total-amount {
            font-size: 22px;
            font-weight: bold;
            color: #fff;
            letter-spacing: 0.5px;
        }

        .total-words {
            font-size: 9px;
            color: #a8c4e0;
            margin-top: 4px;
            font-style: italic;
        }

        /* ── CATATAN ── */
        .catatan-box {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 10.5px;
            color: #78350f;
            margin-bottom: 20px;
        }

        .catatan-box strong {
            color: #92400e;
        }

        /* ── FOOTER ── */
        .footer {
            border-top: 2px solid #e2e8f0;
            padding-top: 14px;
            display: table;
            width: 100%;
        }

        .footer-left {
            display: table-cell;
            vertical-align: bottom;
        }

        .footer-right {
            display: table-cell;
            text-align: right;
            vertical-align: bottom;
        }

        .footer-text {
            font-size: 9px;
            color: #999;
            line-height: 1.5;
        }

        .signature-area {
            text-align: center;
            font-size: 10px;
            color: #555;
        }

        .signature-line {
            width: 140px;
            border-bottom: 1px solid #333;
            margin: 30px auto 4px;
        }

        /* ── WATERMARK / STAMP ── */
        .stamp {
            display: inline-block;
            border: 3px solid #16a34a;
            color: #16a34a;
            font-size: 14px;
            font-weight: bold;
            padding: 5px 14px;
            border-radius: 4px;
            letter-spacing: 2px;
            transform: rotate(-8deg);
            margin-bottom: 6px;
        }
    </style>
</head>

<body>

    {{-- ── HEADER ── --}}
    <div class="header">
        <div class="header-logo">
            <div class="company-name">KAWULO HALAL</div>
            <div class="company-sub">Yayasan Permata Bakti Pertiwi</div>
            <div class="company-sub" style="margin-top:3px;">
                +62 897-6774-482 &nbsp;|&nbsp; {{ config('app.url') }}
            </div>
        </div>
        <div class="header-meta">
            <div class="receipt-label">Nomor Receipt</div>
            <div class="receipt-no">RCP-{{ str_pad($penagihan->id, 6, '0', STR_PAD_LEFT) }}</div>
            <div class="receipt-date">Tanggal: {{ $penagihan->tanggal_dibayar->format('d F Y, H:i') }} WIB</div>
            <div><span class="status-badge">✓ LUNAS</span></div>
        </div>
    </div>

    {{-- ── PIHAK INFO ── --}}
    <div class="info-section">
        <div class="info-box">
            <div class="info-box-title">Data Penerima Pembayaran</div>
            <div class="info-row">
                <div class="info-label">Nama Lengkap</div>
                <div class="info-value">{{ $dataEntry->nama_lengkap }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $dataEntry->email }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Telepon</div>
                <div class="info-value">{{ $dataEntry->telephone ?? '—' }}</div>
            </div>
            @if ($dataEntry->bank && $dataEntry->no_rekening)
                <div class="info-row">
                    <div class="info-label">Bank</div>
                    <div class="info-value">{{ $dataEntry->bank->nama_bank ?? '—' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">No. Rekening</div>
                    <div class="info-value">{{ $dataEntry->no_rekening }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Nama Rekening</div>
                    <div class="info-value">{{ $dataEntry->nama_rekening ?? '—' }}</div>
                </div>
            @endif
        </div>
        <div class="info-box" style="padding-left:16px;">
            <div class="info-box-title">Detail Tagihan</div>
            <div class="info-row">
                <div class="info-label">No. Tagihan</div>
                <div class="info-value">TAG-{{ str_pad($penagihan->id, 6, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tgl. Tagihan</div>
                <div class="info-value">{{ $penagihan->tanggal_tagihan->format('d M Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tgl. Dibayar</div>
                <div class="info-value">{{ $penagihan->tanggal_dibayar->format('d M Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status</div>
                <div class="info-value" style="color:#16a34a;">DIBAYAR</div>
            </div>
        </div>
    </div>

    {{-- ── DETAIL TABEL ── --}}
    <div class="detail-title">Rincian Pembayaran</div>
    <table class="detail-table">
        <thead>
            <tr>
                <th style="width:40px">#</th>
                <th>Deskripsi</th>
                <th class="tr" style="width:90px">Qty</th>
                <th class="tr" style="width:110px">Tarif/Paket</th>
                <th class="tr" style="width:120px">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>
                    Jasa Input Data Entry<br>
                    <span style="font-size:10px;color:#888;">
                        Entri data sebanyak {{ $penagihan->jumlah_data }} data
                        ({{ $penagihan->jumlah_paket }} paket ×
                        {{ $penagihan->jumlah_data / $penagihan->jumlah_paket }} data/paket)
                    </span>
                </td>
                <td class="tr">{{ $penagihan->jumlah_paket }}x</td>
                <td class="tr">Rp {{ number_format($tarifPerPaket, 0, ',', '.') }}</td>
                <td class="tr">Rp {{ number_format($penagihan->nominal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="tr">TOTAL PEMBAYARAN</td>
                <td class="tr" style="font-size:13px;">Rp {{ number_format($penagihan->nominal, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    {{-- ── TOTAL HIGHLIGHT ── --}}
    <div class="total-section">
        <div class="total-left">
            <div class="total-label-text">Total yang diterima:</div>
            <div class="total-amount">Rp {{ number_format($penagihan->nominal, 0, ',', '.') }}</div>
            <div class="total-words">{{ $nominalTerbilang }}</div>
        </div>
        <div class="total-right">
            <div class="stamp">LUNAS</div>
            <div style="font-size:9px;color:#a8c4e0;margin-top:2px;">
                Dicetak: {{ $tanggalCetak }}
            </div>
        </div>
    </div>

    {{-- ── CATATAN ── --}}
    @if ($penagihan->catatan)
        <div class="catatan-box">
            <strong>Catatan:</strong> {{ $penagihan->catatan }}
        </div>
    @endif

    {{-- ── FOOTER ── --}}
    <div class="footer">
        <div class="footer-left">
            <div class="footer-text">
                Dokumen ini digenerate otomatis oleh Sistem Informasi Kawulo Halal<br>
                Dikirim melalui WhatsApp oleh Kawulo Halal<br>
                Untuk verifikasi hubungi: +62 897-6774-482
            </div>
        </div>
        <div class="footer-right">
            <div class="signature-area">
                <div class="signature-line"></div>
                <div>( Bagian Keuangan, )</div>
                <div style="font-size:9px;margin-top:2px;color:#888;">Tanda Tangan</div>
            </div>
        </div>
    </div>

</body>

</html>
