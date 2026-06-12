<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->nama_loker }} – Pendaftaran Ditutup</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1a3f7a 0%, #1a5fc8 100%);
            margin: 0;
            padding: 24px;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            padding: 48px 40px;
            max-width: 480px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .2);
        }

        .icon-wrap {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #fff0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }

        .icon-wrap svg {
            width: 36px;
            height: 36px;
            fill: none;
            stroke: #e03131;
            stroke-width: 2;
        }

        h1 {
            font-size: 22px;
            color: #1a202c;
            margin: 0 0 10px;
        }

        p {
            font-size: 14px;
            color: #4a5568;
            line-height: 1.7;
            margin: 0 0 20px;
        }

        .badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 20px;
            background: #eff6ff;
            color: #1a5fc8;
            font-size: 13px;
            font-weight: 700;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="icon-wrap">
            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="8" x2="12" y2="12" />
                <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
        </div>
        <span class="badge">{{ $post->posisi }}</span>
        <h1 style="margin-top:14px;">Pendaftaran Ditutup</h1>
        <p>Mohon maaf, lowongan <strong>{{ $post->nama_loker }}</strong> sudah tidak menerima pendaftaran baru saat ini.
        </p>
        @if ($post->tanggal_tutup && $post->tanggal_tutup->isPast())
            <p style="font-size:13px;color:#a0aec0;">Pendaftaran ditutup pada
                {{ $post->tanggal_tutup->format('d M Y') }}.</p>
        @elseif(!$post->is_active)
            <p style="font-size:13px;color:#a0aec0;">Lowongan ini sedang tidak aktif.</p>
        @endif
        <p style="font-size:12px;color:#a0aec0;margin-top:24px;">Kawulo Halal by Yayasan Permata Bakti Pertiwi &copy;
            {{ date('Y') }}</p>
    </div>
</body>

</html>
