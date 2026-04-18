@extends('layouts.guest')
@section('title', 'Login')
@section('content')

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600&family=Sora:wght@400;600&display=swap');

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        .auth-root {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #EEF3FA;
            background-image:
                radial-gradient(ellipse 70% 60% at 15% 10%, rgba(180, 210, 255, 0.55) 0%, transparent 60%),
                radial-gradient(ellipse 50% 50% at 85% 85%, rgba(160, 220, 200, 0.3) 0%, transparent 55%),
                radial-gradient(ellipse 40% 40% at 70% 15%, rgba(200, 225, 255, 0.4) 0%, transparent 50%);
            padding: 2rem 1rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            position: relative;
            overflow: hidden;
        }

        /* Subtle dot pattern */
        .auth-root::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: radial-gradient(circle, rgba(100, 140, 200, 0.12) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
            z-index: 0;
        }

        /* Floating orbs */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(70px);
            pointer-events: none;
            z-index: 0;
            animation: floatOrb 14s ease-in-out infinite;
        }

        .orb-1 {
            width: 380px;
            height: 380px;
            background: rgba(130, 180, 255, 0.22);
            top: -100px;
            left: -80px;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 280px;
            height: 280px;
            background: rgba(100, 210, 180, 0.18);
            bottom: -60px;
            right: -60px;
            animation-delay: -5s;
        }

        .orb-3 {
            width: 200px;
            height: 200px;
            background: rgba(170, 200, 255, 0.2);
            top: 45%;
            right: 8%;
            animation-delay: -9s;
        }

        @keyframes floatOrb {

            0%,
            100% {
                transform: translate(0, 0);
            }

            33% {
                transform: translate(18px, -18px);
            }

            66% {
                transform: translate(-10px, 12px);
            }
        }

        /* Card */
        .auth-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 900px;
            display: flex;
            border-radius: 22px;
            overflow: hidden;
            box-shadow:
                0 0 0 1px rgba(100, 140, 210, 0.15),
                0 40px 80px rgba(60, 100, 180, 0.12),
                0 8px 24px rgba(60, 100, 180, 0.08);
            animation: cardEntrance 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes cardEntrance {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* ── LEFT PANEL ── */
        .panel-left {
            width: 42%;
            flex-shrink: 0;
            background: linear-gradient(145deg, #1A5FC8 0%, #1040A0 55%, #0C2E78 100%);
            padding: 2.5rem 2rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .panel-left::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.06);
            top: -80px;
            right: -90px;
        }

        .panel-left::after {
            content: '';
            position: absolute;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.04);
            bottom: 30px;
            left: -50px;
        }

        .pl-top {
            position: relative;
            z-index: 1;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 2.25rem;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-icon svg {
            width: 22px;
            height: 22px;
        }

        .brand-name {
            font-family: 'Sora', sans-serif;
            font-size: 13px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.95);
            line-height: 1.4;
        }

        .brand-name small {
            display: block;
            font-size: 11px;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.45);
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin-top: 1px;
        }

        .pl-tagline {
            font-size: 22px;
            font-family: 'Sora', sans-serif;
            font-weight: 600;
            color: #fff;
            line-height: 1.4;
            margin-bottom: 0.85rem;
        }

        .pl-tagline em {
            font-style: normal;
            color: #7DD3C8;
        }

        .pl-desc {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.5);
            line-height: 1.7;
            max-width: 240px;
        }

        .pl-bottom {
            position: relative;
            z-index: 1;
        }

        .quote-block {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 14px;
            padding: 1.25rem;
            margin-bottom: 1.25rem;
        }

        .quote-mark {
            font-size: 30px;
            line-height: 1;
            color: #7DD3C8;
            font-family: Georgia, serif;
            margin-bottom: 8px;
        }

        .quote-text {
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.65);
            line-height: 1.7;
            font-style: italic;
        }

        .quote-author {
            margin-top: 10px;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.3);
            font-weight: 500;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .pl-dots {
            display: flex;
            gap: 5px;
            align-items: center;
        }

        .pl-dot {
            height: 5px;
            border-radius: 3px;
            background: rgba(255, 255, 255, 0.2);
        }

        .pl-dot.active {
            width: 18px;
            background: #7DD3C8;
        }

        .pl-dot:not(.active) {
            width: 5px;
            border-radius: 50%;
        }

        /* ── RIGHT PANEL ── */
        .panel-right {
            flex: 1;
            background: #ffffff;
            padding: 2.75rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-headline h2 {
            font-family: 'Sora', sans-serif;
            font-size: 22px;
            font-weight: 600;
            color: #0F1F40;
            line-height: 1.3;
            margin-bottom: 4px;
        }

        .form-sub {
            font-size: 13.5px;
            color: #8A99B3;
            margin-bottom: 1.75rem;
        }

        .form-divider {
            height: 1px;
            background: #EDF0F7;
            margin-bottom: 1.75rem;
        }

        /* Session status */
        .session-status {
            background: #EBF9F5;
            border: 1px solid #A7DDD0;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13px;
            color: #0F6E56;
            margin-bottom: 1.25rem;
        }

        /* Alert */
        .alert-modern {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 1.25rem;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-modern svg {
            width: 15px;
            height: 15px;
            stroke: #EF4444;
            fill: none;
            stroke-width: 2;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .alert-modern-text {
            font-size: 13px;
            color: #B91C1C;
            line-height: 1.5;
            flex: 1;
        }

        .alert-modern-close {
            background: none;
            border: none;
            cursor: pointer;
            color: #FCA5A5;
            padding: 0;
            line-height: 1;
            font-size: 18px;
        }

        .alert-modern-close:hover {
            color: #EF4444;
        }

        /* Fields */
        .field-group {
            margin-bottom: 1.1rem;
        }

        .field-label {
            display: block;
            font-size: 11.5px;
            font-weight: 600;
            color: #6B7A99;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            margin-bottom: 7px;
        }

        .input-shell {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 13px;
            width: 15px;
            height: 15px;
            stroke: #B0BCCE;
            fill: none;
            stroke-width: 2;
            pointer-events: none;
            z-index: 1;
        }

        .input-shell input {
            width: 100%;
            height: 44px;
            background: #F5F7FB;
            border: 1px solid #E0E7F0;
            border-radius: 10px;
            padding: 0 44px 0 40px;
            font-size: 14px;
            color: #0F1F40;
            font-family: 'Plus Jakarta Sans', sans-serif;
            outline: none;
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
        }

        .input-shell input::placeholder {
            color: #B0BCCE;
        }

        .input-shell input:focus {
            border-color: #1A5FC8;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(26, 95, 200, 0.1);
        }

        .input-shell input.is-invalid {
            border-color: #FCA5A5;
            background: #FEF2F2;
        }

        .eye-toggle {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            cursor: pointer;
            color: #B0BCCE;
            padding: 4px;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }

        .eye-toggle:hover {
            color: #6B7A99;
        }

        .eye-toggle svg {
            width: 15px;
            height: 15px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
        }

        /* Options row */
        .options-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .custom-check {
            display: flex;
            align-items: center;
            gap: 9px;
            cursor: pointer;
            user-select: none;
        }

        .custom-check input[type="checkbox"] {
            display: none;
        }

        .check-visual {
            width: 17px;
            height: 17px;
            border: 1.5px solid #C8D3E8;
            border-radius: 5px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .custom-check input:checked+.check-visual {
            background: #1A5FC8;
            border-color: #1A5FC8;
        }

        .check-visual svg {
            width: 10px;
            height: 10px;
            stroke: #fff;
            fill: none;
            stroke-width: 3;
            opacity: 0;
            transition: opacity 0.15s;
        }

        .custom-check input:checked+.check-visual svg {
            opacity: 1;
        }

        .check-text {
            font-size: 13px;
            color: #7A8AA8;
        }

        /* Submit */
        .btn-submit {
            width: 100%;
            height: 46px;
            background: linear-gradient(135deg, #1A5FC8 0%, #1040A0 100%);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 14.5px;
            font-weight: 600;
            font-family: 'Sora', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            letter-spacing: 0.01em;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(26, 95, 200, 0.3);
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.12) 0%, transparent 100%);
            opacity: 0;
            transition: opacity 0.2s;
        }

        .btn-submit:hover::before {
            opacity: 1;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(26, 95, 200, 0.38);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit svg {
            width: 16px;
            height: 16px;
            stroke: rgba(255, 255, 255, 0.8);
            fill: none;
            stroke-width: 2;
        }

        /* Footer */
        .auth-footer {
            margin-top: 1.75rem;
            text-align: center;
            font-size: 11.5px;
            color: #B0BCCE;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .panel-left {
                display: none;
            }

            .panel-right {
                padding: 2rem 1.5rem;
                border-radius: 22px;
            }
        }
    </style>

    <div class="auth-root">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>

        <div class="auth-card">

            {{-- ── LEFT PANEL ── --}}
            <div class="panel-left">
                <div class="pl-top">
                    <div class="brand-logo">
                        <div class="brand-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.9)" stroke-width="1.8">
                                <path d="M12 2L2 7l10 5 10-5-10-5z" />
                                <path d="M2 17l10 5 10-5" />
                                <path d="M2 12l10 5 10-5" />
                            </svg>
                        </div>
                        <div class="brand-name">
                            Kawulo Halal
                            <small>Sertifikasi Produk Halal Untuk UMKM Low-Risk</small>
                        </div>
                    </div>

                    <p class="pl-tagline">Sistem Manajemen<br><em>Terpadu</em></p>
                    <p class="pl-desc">Platform pengelolaan data dan administrasi secara digital, aman, dan efisien.
                    </p>
                </div>

                <div class="pl-bottom">
                    <div class="quote-block">
                        <div class="quote-mark">"</div>
                        <p class="quote-text">Jualan boleh sederhana, tapi jaminan halal harus luar biasa.</p>
                        <p class="quote-author">— Agil Praditya Bapake Baskara</p>
                    </div>
                    <div class="pl-dots">
                        <div class="pl-dot active"></div>
                        <div class="pl-dot"></div>
                        <div class="pl-dot"></div>
                    </div>
                </div>
            </div>

            {{-- ── RIGHT PANEL ── --}}
            <div class="panel-right">

                <div class="form-headline">
                    <h2>Selamat datang kembali</h2>
                </div>
                <p class="form-sub">Masuk ke akun Anda untuk melanjutkan</p>
                <div class="form-divider"></div>

                {{-- Session Status --}}
                @if (session('status'))
                    <div class="session-status">{{ session('status') }}</div>
                @endif

                {{-- Error Alert --}}
                @if ($errors->any())
                    <div class="alert-modern" id="loginAlert">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        <div class="alert-modern-text">
                            <strong>Login gagal.</strong>
                            @if ($errors->has('email'))
                                Email atau password yang Anda masukkan salah.
                            @elseif ($errors->has('throttle'))
                                Terlalu banyak percobaan. Silakan coba lagi nanti.
                            @else
                                Terjadi kesalahan. Silakan coba lagi.
                            @endif
                        </div>
                        <button class="alert-modern-close"
                            onclick="document.getElementById('loginAlert').remove()">&times;</button>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" novalidate>
                    @csrf

                    {{-- Email --}}
                    <div class="field-group">
                        <label class="field-label" for="email">Alamat Email</label>
                        <div class="input-shell">
                            <svg class="input-icon" viewBox="0 0 24 24">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                <polyline points="22,6 12,13 2,6" />
                            </svg>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                placeholder="nama@domain.com" required autofocus autocomplete="username"
                                class="{{ $errors->has('email') ? 'is-invalid' : '' }}">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="field-group">
                        <label class="field-label" for="password">Password</label>
                        <div class="input-shell">
                            <svg class="input-icon" viewBox="0 0 24 24">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            <input type="password" id="password" name="password" placeholder="Masukkan password Anda"
                                required autocomplete="current-password"
                                class="{{ $errors->has('password') ? 'is-invalid' : '' }}">
                            <button class="eye-toggle" type="button" id="eyeToggle" aria-label="Tampilkan password">
                                <svg id="eyeIconSvg" viewBox="0 0 24 24">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Options --}}
                    <div class="options-row">
                        <label class="custom-check">
                            <input type="checkbox" name="remember" id="remember_me">
                            <span class="check-visual">
                                <svg viewBox="0 0 24 24">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            </span>
                            <span class="check-text">Ingat saya</span>
                        </label>
                    </div>

                    {{-- Submit --}}
                    <button class="btn-submit" type="submit">
                        <svg viewBox="0 0 24 24">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                            <polyline points="10 17 15 12 10 7" />
                            <line x1="15" y1="12" x2="3" y2="12" />
                        </svg>
                        Masuk ke Sistem
                    </button>
                </form>

                <div class="auth-footer">
                    &copy;
                    <script>
                        document.write(new Date().getFullYear())
                    </script> Yayasan Permata Bakti Pertiwi. All rights reserved.
                </div>
            </div>

        </div>
    </div>

    <script>
        (function() {
            var eyeToggle = document.getElementById('eyeToggle');
            var passwordInput = document.getElementById('password');
            var eyeIcon = document.getElementById('eyeIconSvg');
            var showPassword = false;

            if (eyeToggle && passwordInput) {
                eyeToggle.addEventListener('click', function() {
                    showPassword = !showPassword;
                    passwordInput.type = showPassword ? 'text' : 'password';
                    eyeIcon.innerHTML = showPassword ?
                        '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>' :
                        '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
                });
            }

            window.SimpleBar = window.SimpleBar || function() {
                return {
                    recalculate: function() {}
                };
            };
            window.Waves = window.Waves || {
                attach: function() {},
                init: function() {}
            };

            window.onerror = function(msg, url) {
                if (msg.includes('querySelector') || msg.includes('is null') ||
                    (url && (url.includes('simplebar') || url.includes('dashboard')))) {
                    return true;
                }
            };
        })();
    </script>

@endsection
