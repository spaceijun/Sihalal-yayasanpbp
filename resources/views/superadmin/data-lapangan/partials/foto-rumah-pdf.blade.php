<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Foto Rumah - {{ $dataLapangan->nama_pu }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        .header p {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }

        .info-section {
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .info-label {
            display: table-cell;
            width: 150px;
            font-weight: bold;
            color: #333;
        }

        .info-value {
            display: table-cell;
            color: #555;
        }

        .foto-container {
            text-align: center;
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .foto-container img {
            max-width: 100%;
            max-height: 500px;
            border: 2px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .foto-label {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
            color: #333;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>DOKUMENTASI FOTO RUMAH</h1>
        <p>Data Lapangan Usaha Produktif</p>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Nama PU:</div>
            <div class="info-value">{{ $dataLapangan->nama_pu }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">NIK:</div>
            <div class="info-value">{{ $dataLapangan->nik }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Alamat:</div>
            <div class="info-value">{{ $dataLapangan->alamat }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">RT / RW:</div>
            <div class="info-value">{{ $dataLapangan->rt }} / {{ $dataLapangan->rw }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Koordinat:</div>
            <div class="info-value">{{ $dataLapangan->titik_koordinat }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">{{ $dataLapangan->status }}</div>
        </div>
    </div>

    <div class="foto-container">
        <div class="foto-label">FOTO RUMAH</div>
        <img src="{{ $imageSrc }}" alt="Foto Rumah {{ $dataLapangan->nama_pu }}">
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ $tanggal_cetak }}</p>
        <p>Dokumen ini digenerate secara otomatis oleh sistem</p>
    </div>
</body>

</html>
