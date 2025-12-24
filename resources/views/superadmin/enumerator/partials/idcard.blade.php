<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ID Card Kawulo Halal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@600;700&family=Roboto:wght@400;500;700&display=swap");

        :root {
            /* Warna utama diambil dari sampel gambar (Ungu Gelap/Biru Tua) */
            --primary-color: #2e0d6e;
            /* Warna pola background (Biru Muda Pucat) */
            --pattern-color: #dce4ff;
        }

        body {
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: "Roboto", sans-serif;
        }

        /* 1. Main Container ID Card */
        .id-card {
            width: 590px;
            height: 1004px;
            background-color: white;
            position: relative;
            overflow: hidden;
            /* Agar elemen dekorasi tidak keluar dari kartu */
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* 2. Header Section */
        .header {
            width: 100%;
            padding-top: 50px;
            padding-left: 40px;
            padding-right: 40px;
            display: flex;
            align-items: flex-start;
            gap: 20px;
            z-index: 10;
        }

        .header-logo {
            width: 100px;
            height: auto;
        }

        .header-text {
            color: var(--primary-color);
            font-family: "Chakra Petch", sans-serif;
            /* Font kotak/teknis */
            font-weight: 700;
            font-size: 24px;
            line-height: 1.2;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* 3. Profile Image Section */
        .profile-container {
            margin-top: 80px;
            position: relative;
            z-index: 10;
        }

        .profile-img-box {
            width: 320px;
            height: 340px;
            border: 6px solid var(--primary-color);
            border-radius: 50px;
            /* Lengkungan kotak foto */
            overflow: hidden;
            background-color: #ddd;
            /* Placeholder color */
        }

        .profile-img-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center top;
        }

        /* 4. Text Info (Nama & ID) */
        .info-section {
            text-align: center;
            margin-top: 30px;
            z-index: 10;
        }

        .name-text {
            font-size: 48px;
            font-weight: 900;
            text-transform: uppercase;
            color: black;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .id-number {
            font-size: 28px;
            font-weight: 500;
            color: black;
            letter-spacing: 2px;
        }

        /* 5. Geometric Pattern (Background Diamonds) */
        .pattern-box {
            position: absolute;
            width: 250px;
            height: 250px;
            border: 18px solid var(--pattern-color);
            transform: rotate(45deg);
            z-index: 1;
            /* Di belakang foto/teks */
            background: transparent;
        }

        .pattern-1 {
            bottom: 180px;
            left: 50px;
        }

        .pattern-2 {
            bottom: 80px;
            left: 150px;
            border-color: #dce4ff;
            /* Sedikit variasi jika perlu */
        }

        /* Membuat efek potong/overlap pada pola */
        .pattern-overlay {
            /* Teknik CSS sederhana untuk meniru pola anyaman,
                untuk hasil sempurna biasanya butuh SVG,
                tapi ini pendekatan CSS murni */
            position: absolute;
        }

        /* 6. Footer Wave */
        .footer-wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 180px;
            /* Tinggi area gelombang */
            z-index: 5;
        }

        /* Mengisi warna gelombang dengan SVG agar lengkungan presisi */
        .footer-wave svg {
            display: block;
            width: 100%;
            height: 100%;
        }
    </style>
</head>

<body>
    <div class="id-card">
        <div class="header">
            <img src="{{ $settingWebsite->favicon }}" alt="Logo" class="header-logo" />

            <div class="header-text">
                LEMBAGA PENDAMPING<br />
                PROSES PRODUK HALAL<br />
                KAWULO HALAL
            </div>
        </div>

        <div class="profile-container">
            <div class="profile-img-box">
                <img src="{{ asset('storage/' . $enumerator->foto_diri) }}" alt="Foto Profil" />
            </div>
        </div>

        <div class="info-section">
            <div class="name-text">{{ $enumerator->nama_lengkap }}</div>
            <div class="id-number">No Registrasi <br />{{ $enumerator->no_registrasi }}/KH-YPBP/12/2025</div>
        </div>

        {{-- <div class="pattern-box pattern-1"></div>
        <div class="pattern-box pattern-2"></div> --}}

        <div class="footer-wave">
            <svg viewBox="0 0 590 150" preserveAspectRatio="none">
                <path d="M0,100 C150,150 300,50 590,10 L590,150 L0,150 Z" fill="#2e0d6e"></path>
            </svg>
        </div>
    </div>
</body>

</html>
