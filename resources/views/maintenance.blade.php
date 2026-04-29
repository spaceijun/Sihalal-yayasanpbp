<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kawulo Halal – Under Maintenance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&family=Noto+Serif:ital,wght@0,400;1,400&display=swap"
        rel="stylesheet" />
    <style>
        :root {
            --cyan-deep: #0a1628;
            --cyan-mid: #0d2240;
            --cyan-glow: #00c8e0;
            --cyan-soft: #5fe8f5;
            --cyan-pale: #b8f7ff;
            --cyan-accent: #00e5c5;
            --gold: #f0c040;
            --text-main: #e8f7fa;
            --text-muted: #7ab8c8;
            --card-bg: rgba(255, 255, 255, 0.04);
            --card-border: rgba(0, 200, 224, 0.18);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--cyan-deep);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Animated mesh background ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            z-index: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 10%, rgba(0, 200, 224, 0.13) 0%, transparent 60%),
                radial-gradient(ellipse 60% 70% at 80% 90%, rgba(0, 229, 197, 0.10) 0%, transparent 55%),
                radial-gradient(ellipse 50% 50% at 55% 45%, rgba(0, 100, 150, 0.12) 0%, transparent 50%);
            pointer-events: none;
        }

        /* ── Grid lines overlay ── */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            z-index: 0;
            background-image:
                linear-gradient(rgba(0, 200, 224, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 200, 224, 0.04) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
        }

        .page-wrap {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        /* ── Logo / Brand ── */
        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(0, 200, 224, 0.08);
            border: 1px solid rgba(0, 200, 224, 0.25);
            border-radius: 100px;
            padding: 8px 20px 8px 12px;
            margin-bottom: 2.2rem;
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--cyan-glow), var(--cyan-accent));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .brand-name {
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 0.06em;
            color: var(--cyan-pale);
            text-transform: uppercase;
        }

        /* ── Headline ── */
        .headline {
            font-size: clamp(2.4rem, 6vw, 4.2rem);
            font-weight: 800;
            line-height: 1.08;
            text-align: center;
            letter-spacing: -0.02em;
            margin-bottom: 0.5rem;
        }

        .headline .line-1 {
            color: var(--text-main);
        }

        .headline .line-2 {
            background: linear-gradient(90deg, var(--cyan-glow), var(--cyan-accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .subtitle {
            font-family: 'Noto Serif', serif;
            font-style: italic;
            font-size: 1.05rem;
            color: var(--text-muted);
            text-align: center;
            margin-bottom: 2.8rem;
            max-width: 400px;
        }

        /* ── Countdown ── */
        .countdown-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 2.8rem;
            width: 100%;
            max-width: 520px;
        }

        .cd-unit {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 1.4rem 0.5rem 1.1rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: border-color 0.3s;
        }

        .cd-unit::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--cyan-glow), transparent);
            opacity: 0.7;
        }

        .cd-unit:hover {
            border-color: rgba(0, 200, 224, 0.45);
        }

        .cd-number {
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 700;
            line-height: 1;
            color: var(--cyan-soft);
            display: block;
            font-variant-numeric: tabular-nums;
            letter-spacing: -0.02em;
        }

        .cd-label {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--text-muted);
            margin-top: 6px;
            display: block;
        }

        /* ── Progress bar ── */
        .progress-section {
            width: 100%;
            max-width: 520px;
            margin-bottom: 2.8rem;
        }

        .progress-meta {
            display: flex;
            justify-content: space-between;
            font-size: 0.78rem;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .progress {
            height: 6px;
            background: rgba(255, 255, 255, 0.07);
            border-radius: 100px;
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(90deg, var(--cyan-glow), var(--cyan-accent));
            border-radius: 100px;
            transition: width 0.8s ease;
            position: relative;
        }

        .progress-bar::after {
            content: '';
            position: absolute;
            right: -1px;
            top: 50%;
            transform: translateY(-50%);
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--cyan-soft);
            box-shadow: 0 0 10px var(--cyan-glow);
        }

        /* ── Info cards ── */
        .info-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
            width: 100%;
            max-width: 520px;
            margin-bottom: 2.8rem;
        }

        .info-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 1rem 1.1rem;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .info-card-icon {
            width: 32px;
            height: 32px;
            flex-shrink: 0;
            border-radius: 8px;
            background: rgba(0, 200, 224, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
        }

        .info-card-text {
            font-size: 0.82rem;
            line-height: 1.45;
        }

        .info-card-title {
            font-weight: 600;
            color: var(--cyan-pale);
            margin-bottom: 2px;
        }

        .info-card-desc {
            color: var(--text-muted);
        }

        /* ── Social / Footer ── */
        .social-row {
            display: flex;
            gap: 10px;
            margin-bottom: 2rem;
        }

        .social-dot {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 1px solid rgba(0, 200, 224, 0.2);
            background: rgba(255, 255, 255, 0.04);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
            text-decoration: none;
            color: var(--text-muted);
        }

        .social-dot:hover {
            border-color: var(--cyan-glow);
            background: rgba(0, 200, 224, 0.1);
            color: var(--cyan-soft);
        }

        .footer-note {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-align: center;
            letter-spacing: 0.04em;
        }

        .footer-note strong {
            color: var(--cyan-soft);
            font-weight: 600;
        }

        /* ── Floating particles ── */
        .particle {
            position: fixed;
            border-radius: 50%;
            background: var(--cyan-glow);
            opacity: 0;
            pointer-events: none;
            animation: float-up linear infinite;
        }

        @keyframes float-up {
            0% {
                transform: translateY(100vh) scale(0);
                opacity: 0;
            }

            10% {
                opacity: 0.35;
            }

            90% {
                opacity: 0.15;
            }

            100% {
                transform: translateY(-10vh) scale(1.2);
                opacity: 0;
            }
        }

        /* ── Top edge light ── */
        .edge-light {
            position: fixed;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--cyan-glow), transparent);
            opacity: 0.6;
        }

        @media (max-width: 480px) {
            .countdown-grid {
                gap: 8px;
            }

            .cd-unit {
                padding: 1rem 0.3rem 0.8rem;
                border-radius: 12px;
            }
        }
    </style>
</head>

<body>

    <div class="edge-light"></div>

    <!-- Particles -->
    <div id="particles"></div>

    <div class="page-wrap">

        <!-- Brand -->
        <div class="brand-badge">
            <div class="brand-icon">🌙</div>
            <span class="brand-name">Kawulo Halal</span>
        </div>

        <!-- Headline -->
        <h1 class="headline">
            <span class="line-1">Segera</span><br />
            <span class="line-2">Hadir Kembali</span>
        </h1>
        <p class="subtitle">Kami sedang mempersiapkan sesuatu yang lebih baik untuk Anda.</p>

        <!-- Countdown -->
        <div class="countdown-grid">
            <div class="cd-unit">
                <span class="cd-number" id="cd-days">03</span>
                <span class="cd-label">Hari</span>
            </div>
            <div class="cd-unit">
                <span class="cd-number" id="cd-hours">00</span>
                <span class="cd-label">Jam</span>
            </div>
            <div class="cd-unit">
                <span class="cd-number" id="cd-mins">00</span>
                <span class="cd-label">Menit</span>
            </div>
            <div class="cd-unit">
                <span class="cd-number" id="cd-secs">00</span>
                <span class="cd-label">Detik</span>
            </div>
        </div>

        <!-- Progress -->
        <div class="progress-section">
            <div class="progress-meta">
                <span>Progress pembaruan</span>
                <span id="pct-label">0%</span>
            </div>
            <div class="progress">
                <div class="progress-bar" id="prog-bar" style="width:0%"></div>
            </div>
        </div>

        <!-- Info cards -->
        <div class="info-cards">
            <div class="info-card">
                <div class="info-card-icon">🔧</div>
                <div class="info-card-text">
                    <div class="info-card-title">Pembaruan</div>
                    <div class="info-card-desc">Fitur baru & performa lebih cepat</div>
                </div>
            </div>
            <div class="info-card">
                <div class="info-card-icon">🔒</div>
                <div class="info-card-text">
                    <div class="info-card-title">Keamanan</div>
                    <div class="info-card-desc">Sistem lebih aman & terpercaya</div>
                </div>
            </div>
            <div class="info-card">
                <div class="info-card-icon">✨</div>
                <div class="info-card-text">
                    <div class="info-card-title">Tampilan</div>
                    <div class="info-card-desc">Desain modern & ramah pengguna</div>
                </div>
            </div>
        </div>

        <!-- Social -->
        <div class="social-row">
            <a href="#" class="social-dot" title="Instagram">📷</a>
            <a href="#" class="social-dot" title="WhatsApp">💬</a>
            <a href="#" class="social-dot" title="Email">✉️</a>
        </div>

        <p class="footer-note">
            &copy; 2025 <strong>Kawulo Halal</strong> &nbsp;·&nbsp; Hubungi kami:
            <strong>support@kawulohalal.id</strong>
        </p>

    </div>

    <script>
        // ── Countdown: 3 hari dari sekarang ──────────────────────────────────
        const targetDate = new Date(Date.now() + 3 * 24 * 60 * 60 * 1000);
        const totalDuration = 3 * 24 * 60 * 60 * 1000;

        function pad(n) {
            return String(n).padStart(2, '0');
        }

        function tick() {
            const diff = targetDate - Date.now();
            if (diff <= 0) {
                document.getElementById('cd-days').textContent = '00';
                document.getElementById('cd-hours').textContent = '00';
                document.getElementById('cd-mins').textContent = '00';
                document.getElementById('cd-secs').textContent = '00';
                document.getElementById('prog-bar').style.width = '100%';
                document.getElementById('pct-label').textContent = '100%';
                return;
            }
            const d = Math.floor(diff / 86400000);
            const h = Math.floor((diff % 86400000) / 3600000);
            const m = Math.floor((diff % 3600000) / 60000);
            const s = Math.floor((diff % 60000) / 1000);

            document.getElementById('cd-days').textContent = pad(d);
            document.getElementById('cd-hours').textContent = pad(h);
            document.getElementById('cd-mins').textContent = pad(m);
            document.getElementById('cd-secs').textContent = pad(s);

            const elapsed = totalDuration - diff;
            const pct = Math.min(100, Math.round((elapsed / totalDuration) * 100));
            document.getElementById('prog-bar').style.width = pct + '%';
            document.getElementById('pct-label').textContent = pct + '%';
        }

        tick();
        setInterval(tick, 1000);

        // ── Particles ───────────────────────────────────────────────────────
        const container = document.getElementById('particles');
        for (let i = 0; i < 18; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            const size = Math.random() * 4 + 2;
            p.style.cssText = `
      width:${size}px; height:${size}px;
      left:${Math.random() * 100}vw;
      animation-duration:${8 + Math.random() * 12}s;
      animation-delay:${Math.random() * 10}s;
    `;
            container.appendChild(p);
        }
    </script>

</body>

</html>
