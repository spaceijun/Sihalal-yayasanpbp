<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Surat Kawulo Halal - {{ $enumerator->nama_lengkap }}</title>
    <style>
        /* --- RESET & BASE --- */
        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #fafafa;
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.3;
            color: #000;
            -webkit-font-smoothing: antialiased;
        }

        /* --- KONFIGURASI KERTAS --- */
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 2cm;
            margin: 1cm auto;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        /* --- KOP SURAT --- */
        .header {
            position: relative;
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 4px double #000;
            padding-bottom: 10px;
            min-height: 110px;
        }

        .logo-left,
        .logo-right {
            position: absolute;
            top: 0;
            width: 80px;
            height: auto;
        }

        .logo-left {
            left: 0;
        }

        .logo-right {
            right: 0;
        }

        .header-text {
            margin: 0 90px;
        }

        .header h3 {
            margin: 0;
            font-size: 14pt;
            font-weight: normal;
            text-transform: uppercase;
            color: #1a459c;
        }

        .header h1 {
            margin: 5px 0;
            font-size: 22pt;
            font-weight: bold;
            letter-spacing: 1px;
            font-family: Arial, sans-serif;
            color: #1a459c;
        }

        .header p {
            margin: 2px 0;
            font-size: 10pt;
            color: #000;
        }

        .header .services {
            font-style: italic;
            font-weight: bold;
            font-size: 10pt;
        }

        /* --- KONTEN --- */
        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .meta-table td {
            vertical-align: top;
            padding-bottom: 2px;
        }

        .meta-table td:first-child {
            width: 90px;
        }

        .meta-table td:nth-child(2) {
            width: 15px;
            text-align: center;
        }

        .title-center {
            text-align: center;
            margin: 20px 0;
        }

        .title-center h2 {
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
            font-size: 14pt;
            font-weight: bold;
        }

        .content {
            text-align: justify;
            line-height: 1.5;
        }

        .content p {
            margin-bottom: 10px;
            margin-top: 0;
        }

        .bio-table {
            width: 100%;
            margin: 10px 0 15px 20px;
            border-collapse: collapse;
        }

        .bio-table td {
            vertical-align: top;
            padding: 3px 0;
        }

        .bio-table td:first-child {
            width: 140px;
        }

        .bio-table td:nth-child(2) {
            width: 20px;
            text-align: center;
        }

        /* --- TANDA TANGAN --- */
        .signature-wrapper {
            float: right;
            width: 300px;
            text-align: center;
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .signature-date {
            margin-bottom: 10px;
        }

        .signature-stack {
            position: relative;
            width: 100%;
            height: 110px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .img-stamp {
            position: absolute;
            width: 100px;
            opacity: 0.8;
            z-index: 1;
            transform: rotate(-5deg);
        }

        .img-sign {
            position: absolute;
            width: 130px;
            z-index: 2;
            margin-top: 10px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 5px;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        .mb-2 {
            margin-bottom: 20px;
        }

        /* --- SETTING KHUSUS CETAK (A4) --- */
        @page {
            size: A4;
            margin: 0;
        }

        @media print {

            html,
            body {
                width: 210mm;
                height: 297mm;
            }

            .page {
                margin: 0;
                border: initial;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
            }

            table,
            tr,
            td,
            .signature-wrapper {
                page-break-inside: avoid;
            }

            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <!-- Halaman 1: Surat Pemberitahuan -->
    <div class="page">
        <div class="header">
            <img src="https://sihalal.yayasanpermatabakti.com/assets/images/stample.png" alt="Logo Yayasan"
                class="logo-left" />
            <img src="https://sihalal.yayasanpermatabakti.com/assets/images/stample.png" alt="Logo Halal"
                class="logo-right" />
            <div class="header-text">
                <h3>Yayasan Permata Bakti Pertiwi</h3>
                <h1>"KAWULO HALAL"</h1>
                <p class="services">
                    Jasa Konsultasi Sertifikasi Produk Halal Untuk UMKM Low-Risk
                </p>
                <p>
                    Noble House, Kuningan, Jakarta Selatan 12950 | CS: 0857 7258 5049
                </p>
                <p>www.sihalal.yayasanpermatabakti.com</p>
            </div>
        </div>

        <table class="meta-table">
            <tr>
                <td>Nomor</td>
                <td>:</td>
                <td>{{ $enumerator->no_registrasi }}/KH-SP/{{ date('m/Y') }}</td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td>:</td>
                <td>1 Lembar</td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td>:</td>
                <td>
                    <strong>Pemberitahuan PPPH</strong>
                </td>
            </tr>
        </table>

        <div class="mb-2">
            Jakarta, {{ now()->locale('id')->translatedFormat('d F Y') }}<br />
            Kepada Yth.<br />
            <strong>Kepala Desa ..........</strong><br />
            Di Tempat
        </div>

        <div class="content">
            <p>Dengan hormat,</p>
            <p>
                Sehubungan dengan pelaksanaan Program Sertifikat Halal Gratis (SEHATI)
                yang diselenggarakan oleh Badan Penyelenggara Jaminan Produk Halal
                (BPJPH) bagi Pelaku Usaha Mikro, kami dari Kawulo Halal Yayasan
                Permata Bakti Pertiwi bermaksud memberikan pemberitahuan mengenai
                penugasan <b>Pendamping Proses Produk Halal (PPPH)</b> di wilayah Desa
                yang Bapak/Ibu pimpin. PPPH yang bersangkutan bertugas untuk melakukan
                pendampingan kepada Pelaku Usaha Mikro dalam rangka pengajuan
                sertifikasi halal melalui mekanisme <em>self declare</em>. Adapun
                identitas PPPH yang ditugaskan adalah sebagai berikut:
            </p>

            <table class="bio-table">
                <tr>
                    <td>Nama Lengkap</td>
                    <td>:</td>
                    <td>{{ $enumerator->nama_lengkap }}</td>
                </tr>
                <tr>
                    <td>No HP</td>
                    <td>:</td>
                    <td>{{ $enumerator->telephone }}</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ $enumerator->alamat }}</td>
                </tr>
                <tr>
                    <td>Jabatan Tugas</td>
                    <td>:</td>
                    <td>Pendamping Proses Produk Halal (PPPH)</td>
                </tr>
            </table>

            <p>
                Bersama surat ini, kami lampirkan Surat Tugas PPPH
                <b>Nomor: {{ $enumerator->no_registrasi }}/KH-ST/{{ date('m/Y') }}</b> sebagai kelengkapan
                administrasi.
                Kami mohon izin dan dukungan Bapak/Ibu Kepala Desa agar PPPH yang
                bersangkutan dapat melaksanakan tugas pendampingan dengan lancar dan
                sesuai dengan ketentuan yang berlaku.
            </p>
        </div>

        <div class="signature-wrapper clearfix">
            <div class="signature-date">Jakarta, {{ now()->locale('id')->translatedFormat('d F Y') }}</div>
            <div class="signature-stack">
                <img src="https://sihalal.yayasanpermatabakti.com/assets/images/stample.png" class="img-stamp"
                    alt="Stempel" />
                <img src="https://sihalal.yayasanpermatabakti.com/assets/images/signature.png" class="img-sign"
                    alt="Tanda Tangan" />
            </div>
            <div class="signature-name">Mohammad Faizun Aziz</div>
            <div class="signature-role">HR Departmen</div>
        </div>
    </div>

    <!-- Halaman 2: Surat Tugas -->
    <div class="page">
        <div class="header">
            <img src="https://sihalal.yayasanpermatabakti.com/assets/images/stample.png" alt="Logo Yayasan"
                class="logo-left" />
            <img src="https://sihalal.yayasanpermatabakti.com/assets/images/stample.png" alt="Logo Halal"
                class="logo-right" />
            <div class="header-text">
                <h3>Yayasan Permata Bakti Pertiwi</h3>
                <h1>"KAWULO HALAL"</h1>
                <p class="services">
                    Jasa Konsultasi Sertifikasi Produk Halal Untuk UMKM Low-Risk
                </p>
                <p>
                    Noble House, Kuningan, Jakarta Selatan 12950 | CS: 0857 7258 5049
                </p>
                <p>www.sihalal.yayasanpermatabakti.com</p>
            </div>
        </div>

        <div class="title-center">
            <h2>SURAT TUGAS</h2>
            <p style="margin-top: 5px">Nomor: {{ $enumerator->no_registrasi }}/KH-ST/{{ date('m/Y') }}</p>
        </div>

        <div class="content">
            <p>
                Sehubungan dengan pelaksanaan Program Sertifikat Halal Gratis (SEHATI)
                yang diselenggarakan oleh Badan Penyelenggara Jaminan Produk Halal
                (BPJPH) bagi Pelaku Usaha Mikro untuk melakukan Pendampingan Proses
                Produk Halal maka Bersama ini kami yang bertanda tangan di bawah ini:
            </p>

            <table class="bio-table">
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td><strong>Mohammad Faizun Aziz</strong></td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>:</td>
                    <td>HR Departmen</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>
                        Jl. Lewa No. 46 RT 006/010 Pekayon, Pasar Rebo, Jakarta Timur
                        13710
                    </td>
                </tr>
                <tr>
                    <td>Institusi</td>
                    <td>:</td>
                    <td>Kawulo Halal by Yayasan Permata Bakti Pertiwi</td>
                </tr>
            </table>

            <p>Menyatakan bahwa nama di bawah ini:</p>

            <table class="bio-table">
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td><strong>{{ $enumerator->nama_lengkap }}</strong></td>
                </tr>
                <tr>
                    <td>No. HP</td>
                    <td>:</td>
                    <td>{{ $enumerator->telephone }}</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ $enumerator->alamat }}</td>
                </tr>
                <tr>
                    <td>Koordinator</td>
                    <td>:</td>
                    <td>{{ $enumerator->koordinator->nama_lengkap ?? '-' }}</td>
                </tr>
            </table>

            <p>
                Adalah Pendamping Proses Produk Halal (PPPH) dari Kawulo Halal by
                Yayasan Permata Bakti Pertiwi Indonesia yang bertugas dan bertanggung
                jawab memberikan pelayanan SEHATI kepada pelaku usaha sesuai dengan
                peraturan dan ketentuan yang berlaku.
            </p>
            <p>
                Demikian surat tugas ini dibuat untuk dilaksanakan dengan penuh amanah
                dan profesional.
            </p>
        </div>

        <div class="signature-wrapper clearfix">
            <div class="signature-date">Jakarta, {{ now()->locale('id')->translatedFormat('d F Y') }}</div>
            <div class="signature-stack">
                <img src="https://sihalal.yayasanpermatabakti.com/assets/images/stample.png" class="img-stamp"
                    alt="Stempel" />
                <img src="https://sihalal.yayasanpermatabakti.com/assets/images/signature.png" class="img-sign"
                    alt="Tanda Tangan" />
            </div>
            <div class="signature-name">Mohammad Faizun Aziz</div>
            <div class="signature-role">HR Departmen</div>
        </div>
    </div>
</body>

</html>
