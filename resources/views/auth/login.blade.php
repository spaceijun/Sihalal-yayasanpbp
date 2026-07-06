@extends('layouts.guest')
@section('title', 'Login')
@section('content')

    <div class="kh-login-root" id="loginRoot">

        {{-- ═══════════════════════════════════════════
         SPLIT PANELS (the two halves that open)
         ═══════════════════════════════════════════ --}}
        <div class="kh-split kh-split--left" id="splitLeft">
            <div class="kh-split-inner">

                {{-- Ambient particles --}}
                <canvas class="kh-particles" id="particleCanvas"></canvas>

                {{-- Grid overlay --}}
                <div class="kh-grid-overlay"></div>

                {{-- Content --}}
                <div class="kh-left-content">

                    {{-- Logo --}}
                    <div class="kh-logo">
                        <div class="kh-logo-mark">
                            <svg viewBox="0 0 32 32" fill="none">
                                <path d="M16 3L3 9.5l13 6.5 13-6.5L16 3z" stroke="rgba(255,255,255,0.9)" stroke-width="1.6"
                                    stroke-linejoin="round" />
                                <path d="M3 22.5l13 6.5 13-6.5" stroke="rgba(255,255,255,0.55)" stroke-width="1.6"
                                    stroke-linejoin="round" />
                                <path d="M3 16l13 6.5 13-6.5" stroke="rgba(255,255,255,0.75)" stroke-width="1.6"
                                    stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div class="kh-logo-text">
                            <span class="kh-logo-name">Kawulo Halal</span>
                            <span class="kh-logo-sub">Sertifikasi Produk Halal UMKM</span>
                        </div>
                    </div>

                    {{-- Tagline --}}
                    <div class="kh-tagline">
                        <p class="kh-tagline-eyebrow">Sistem Manajemen Terpadu</p>
                        <h1 class="kh-tagline-headline">
                            Platform<br>
                            <em class="kh-accent">Sertifikasi</em><br>
                            Digital
                        </h1>
                        <p class="kh-tagline-body">
                            Kelola data pendamping, pengajuan, dan sertifikasi halal UMKM secara digital—aman dan efisien.
                        </p>
                    </div>

                    {{-- Stats row --}}
                    <div class="kh-stats">
                        <div class="kh-stat">
                            <span class="kh-stat-num">1.2K+</span>
                            <span class="kh-stat-label">UMKM Terdaftar</span>
                        </div>
                        <div class="kh-stat-divider"></div>
                        <div class="kh-stat">
                            <span class="kh-stat-num">340+</span>
                            <span class="kh-stat-label">Pendamping Aktif</span>
                        </div>
                        <div class="kh-stat-divider"></div>
                        <div class="kh-stat">
                            <span class="kh-stat-num">98%</span>
                            <span class="kh-stat-label">Tingkat Keberhasilan</span>
                        </div>
                    </div>

                    {{-- Quote --}}
                    <div class="kh-quote">
                        <div class="kh-quote-line"></div>
                        <p class="kh-quote-text">"Jualan boleh sederhana, tapi jaminan halal harus luar biasa."</p>
                        <p class="kh-quote-author">— Agil Praditya Bapake Baskara</p>
                    </div>

                </div>
            </div>
        </div>

        <div class="kh-split kh-split--right" id="splitRight">
            <div class="kh-split-inner">
                <div class="kh-right-content">

                    {{-- Form header --}}
                    <div class="kh-form-header">
                        <div class="kh-form-badge">
                            <svg viewBox="0 0 16 16" fill="none" width="12" height="12">
                                <circle cx="8" cy="8" r="3" fill="#00D4AA" />
                            </svg>
                            Kawulo Halal v1.10
                        </div>
                        <h2 class="kh-form-title">Selamat datang<br>kembali</h2>
                        <p class="kh-form-sub">Masuk untuk melanjutkan ke dashboard</p>
                    </div>

                    {{-- Session Status --}}
                    @if (session('status'))
                        <div class="kh-alert kh-alert--success" id="loginAlert">
                            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                <polyline points="22 4 12 14.01 9 11.01" />
                            </svg>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    {{-- Maintenance Alert --}}
                    @if (session('maintenance_message'))
                        <div class="kh-alert kh-alert--maintenance" id="maintenanceAlert">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path
                                    d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                <line x1="12" y1="9" x2="12" y2="13" />
                                <line x1="12" y1="17" x2="12.01" y2="17" />
                            </svg>
                            <span>{{ session('maintenance_message') }}</span>
                        </div>
                    @endif

                    {{-- Error Alert --}}
                    @if ($errors->any())
                        <div class="kh-alert kh-alert--error" id="loginAlert">
                            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            <span>
                                @if ($errors->has('email'))
                                    Email atau password yang Anda masukkan salah.
                                @elseif ($errors->has('throttle'))
                                    Terlalu banyak percobaan. Silakan coba lagi nanti.
                                @else
                                    Terjadi kesalahan. Silakan coba lagi.
                                @endif
                            </span>
                            <button class="kh-alert-close" onclick="this.closest('.kh-alert').remove()"
                                aria-label="Tutup">×</button>
                        </div>
                    @endif

                    {{-- Login Form --}}
                    <form method="POST" action="{{ route('login') }}" novalidate id="loginForm">
                        @csrf

                        {{-- Email --}}
                        <div class="kh-field" id="fieldEmail">
                            <label class="kh-label" for="email">Alamat Email</label>
                            <div class="kh-input-wrap">
                                <svg class="kh-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                    <polyline points="22,6 12,13 2,6" />
                                </svg>
                                <input class="kh-input {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email"
                                    id="email" name="email" value="{{ old('email') }}"
                                    placeholder="nama@domain.com" required autofocus autocomplete="username">
                            </div>
                        </div>

                        {{-- Password --}}
                        <div class="kh-field" id="fieldPassword">
                            <label class="kh-label" for="password">Password</label>
                            <div class="kh-input-wrap">
                                <svg class="kh-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                                <input class="kh-input {{ $errors->has('password') ? 'is-invalid' : '' }}" type="password"
                                    id="password" name="password" placeholder="Masukkan password Anda" required
                                    autocomplete="current-password">
                                <button class="kh-eye-btn" type="button" id="eyeToggle"
                                    aria-label="Tampilkan password">
                                    <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Options --}}
                        <div class="kh-options">
                            <label class="kh-check">
                                <input type="checkbox" name="remember" id="remember_me">
                                <span class="kh-check-box">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg>
                                </span>
                                <span class="kh-check-label">Ingat saya</span>
                            </label>
                        </div>

                        {{-- Submit --}}
                        <button class="kh-btn-submit" type="submit" id="submitBtn">
                            <span class="kh-btn-text">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    width="17" height="17">
                                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                                    <polyline points="10 17 15 12 10 7" />
                                    <line x1="15" y1="12" x2="3" y2="12" />
                                </svg>
                                Masuk ke Sistem
                            </span>
                            <span class="kh-btn-loading" aria-hidden="true">
                                <span class="kh-spinner"></span>
                                Memverifikasi...
                            </span>
                        </button>
                    </form>

                    <div class="kh-footer">
                        <p>©
                            <script>
                                document.write(new Date().getFullYear())
                            </script> Kawulo Halal - Yayasan Permata Bakti Pertiwi
                        </p>
                    </div>

                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════
         SUCCESS CURTAIN — belah tengah reveal
         ═══════════════════════════════════════════ --}}
        <div class="kh-curtain" id="successCurtain" aria-hidden="true">
            {{-- Curtain kiri --}}
            <div class="kh-curtain-half kh-curtain-half--left" id="curtainLeft"></div>
            {{-- Curtain kanan --}}
            <div class="kh-curtain-half kh-curtain-half--right" id="curtainRight"></div>

            {{-- Logo di tengah layar --}}
            <div class="kh-curtain-logo" id="curtainLogo">
                <div class="kh-curtain-logo-wrap">
                    <img src="{{ asset('storage/' . ($settingWebsite->logo ?? '')) }}" alt="Logo" class="kh-cl-img"
                        id="curtainLogoImg" />
                    <div class="kh-cl-ring" id="curtainRing"></div>
                    <div class="kh-cl-pulse" id="curtainPulse"></div>
                </div>
                <p class="kh-cl-text" id="curtainText">Login berhasil</p>
                <p class="kh-cl-sub" id="curtainSub">Membuka dashboard<span class="kh-cl-dots"></span></p>
            </div>
        </div>

    </div>

    <style>
        /* ═══════════════════════════════════════════════
                                   KH LOGIN — FULL PAGE MODERN
                                   ═══════════════════════════════════════════════ */
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;1,400&family=Sora:wght@300;400;600;700&display=swap');

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        .kh-login-root {
            position: fixed;
            inset: 0;
            display: flex;
            overflow: hidden;
            background: #010f3d;
            z-index: 9999;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* ── SPLIT PANELS ── */
        .kh-split {
            position: relative;
            height: 100%;
            overflow: hidden;
            transition: transform 0.85s cubic-bezier(0.76, 0, 0.24, 1);
            will-change: transform;
        }

        .kh-split--left {
            width: 48%;
            flex-shrink: 0;
        }

        .kh-split--right {
            flex: 1;
        }

        /* Split open animation */
        .kh-login-root.is-splitting .kh-split--left {
            transform: translateX(-100%);
        }

        .kh-login-root.is-splitting .kh-split--right {
            transform: translateX(100%);
        }

        .kh-split-inner {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ── LEFT PANEL ── */
        /* ★ UPDATED: gradient menggunakan palet dari foto (#03ace9 → #012996 → #010f3d) */
        .kh-split--left .kh-split-inner {
            background: linear-gradient(150deg, #03ace9 0%, #012996 40%, #010f3d 100%);
        }

        .kh-particles {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        /* ★ UPDATED: grid overlay pakai warna biru cerah dari foto */
        .kh-grid-overlay {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(3, 172, 233, 0.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(3, 172, 233, 0.08) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
        }

        /* Gradient mask on grid */
        .kh-grid-overlay::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 70% 70% at 50% 50%, transparent 30%, #010f3d 100%);
        }

        /* ★ UPDATED: Decorative glow blobs pakai warna biru foto & teal */
        .kh-split--left .kh-split-inner::before {
            content: '';
            position: absolute;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(3, 172, 233, 0.22) 0%, transparent 70%);
            top: -100px;
            right: -100px;
            pointer-events: none;
        }

        .kh-split--left .kh-split-inner::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 212, 170, 0.15) 0%, transparent 70%);
            bottom: -80px;
            left: -80px;
            pointer-events: none;
        }

        /* ── RIGHT PANEL ── */
        .kh-split--right .kh-split-inner {
            background: #FAFBFD;
        }

        /* ── LEFT CONTENT ── */
        .kh-left-content {
            position: relative;
            z-index: 1;
            padding: 3rem 3rem 3rem 3.5rem;
            max-width: 520px;
            width: 100%;
        }

        /* Logo */
        .kh-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 3.5rem;
            animation: khFadeUp 0.6s cubic-bezier(0.16, 1, .3, 1) 0.1s both;
        }

        /* ★ UPDATED: logo mark pakai warna biru foto */
        .kh-logo-mark {
            width: 44px;
            height: 44px;
            background: rgba(3, 172, 233, 0.18);
            border: 1px solid rgba(3, 172, 233, 0.45);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .kh-logo-mark svg {
            width: 24px;
            height: 24px;
        }

        .kh-logo-text {
            line-height: 1.3;
        }

        .kh-logo-name {
            display: block;
            font-family: 'Sora', sans-serif;
            font-size: 15px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.95);
        }

        .kh-logo-sub {
            display: block;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.35);
            font-weight: 400;
        }

        /* Tagline */
        .kh-tagline {
            margin-bottom: 2.5rem;
            animation: khFadeUp 0.6s cubic-bezier(0.16, 1, .3, 1) 0.2s both;
        }

        /* ★ UPDATED: eyebrow pakai biru cerah foto */
        .kh-tagline-eyebrow {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            color: #03ace9;
            margin-bottom: 0.75rem;
        }

        .kh-tagline-headline {
            font-family: 'Sora', sans-serif;
            font-size: clamp(2rem, 3.5vw, 2.75rem);
            font-weight: 700;
            color: rgba(255, 255, 255, 0.95);
            line-height: 1.15;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
        }

        .kh-accent {
            font-style: normal;
            color: #00D4AA;
            position: relative;
        }

        .kh-accent::after {
            content: '';
            position: absolute;
            bottom: 2px;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #00D4AA, transparent);
            opacity: 0.5;
            border-radius: 2px;
        }

        .kh-tagline-body {
            font-size: 13.5px;
            line-height: 1.75;
            color: rgba(255, 255, 255, 0.42);
            max-width: 340px;
        }

        /* Stats */
        /* ★ UPDATED: stats card pakai border & bg warna biru foto */
        .kh-stats {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2.5rem;
            padding: 1.25rem 1.5rem;
            background: rgba(3, 172, 233, 0.08);
            border: 1px solid rgba(3, 172, 233, 0.2);
            border-radius: 14px;
            animation: khFadeUp 0.6s cubic-bezier(0.16, 1, .3, 1) 0.3s both;
        }

        .kh-stat {
            text-align: center;
        }

        .kh-stat-num {
            display: block;
            font-family: 'Sora', sans-serif;
            font-size: 20px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.2;
        }

        /* ★ UPDATED: label stat pakai biru foto */
        .kh-stat-label {
            display: block;
            font-size: 10.5px;
            color: rgba(3, 172, 233, 0.6);
            margin-top: 2px;
            white-space: nowrap;
        }

        /* ★ UPDATED: divider pakai biru foto */
        .kh-stat-divider {
            width: 1px;
            height: 32px;
            background: rgba(3, 172, 233, 0.15);
            flex-shrink: 0;
        }

        /* Quote */
        .kh-quote {
            animation: khFadeUp 0.6s cubic-bezier(0.16, 1, .3, 1) 0.4s both;
        }

        /* ★ UPDATED: quote line gradient dua warna signature teal → biru foto */
        .kh-quote-line {
            width: 28px;
            height: 2px;
            background: linear-gradient(90deg, #00D4AA, #03ace9);
            border-radius: 2px;
            margin-bottom: 0.75rem;
        }

        .kh-quote-text {
            font-size: 13px;
            font-style: italic;
            color: rgba(255, 255, 255, 0.42);
            line-height: 1.7;
            margin-bottom: 0.4rem;
        }

        /* ★ UPDATED: author pakai biru foto */
        .kh-quote-author {
            font-size: 11px;
            color: rgba(3, 172, 233, 0.45);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        /* ── RIGHT CONTENT ── */
        .kh-right-content {
            padding: 3rem;
            width: 100%;
            max-width: 420px;
            animation: khFadeUp 0.6s cubic-bezier(0.16, 1, .3, 1) 0.15s both;
        }

        /* Form Header */
        .kh-form-header {
            margin-bottom: 2rem;
        }

        .kh-form-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 600;
            color: #007A60;
            background: #E0FBF4;
            border: 1px solid #A5EDD8;
            border-radius: 20px;
            padding: 4px 12px;
            margin-bottom: 1.25rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .kh-form-title {
            font-family: 'Sora', sans-serif;
            font-size: clamp(1.6rem, 2.5vw, 2rem);
            font-weight: 700;
            color: #0A1628;
            line-height: 1.2;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .kh-form-sub {
            font-size: 14px;
            color: #7A8AA8;
        }

        /* Alerts */
        .kh-alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 1.25rem;
            font-size: 13px;
            line-height: 1.5;
            animation: khSlideDown 0.3s ease;
        }

        .kh-alert--success {
            background: #EBF9F5;
            border: 1px solid #A7DDD0;
            color: #0A5240;
        }

        .kh-alert--error {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #B91C1C;
        }

        .kh-alert--maintenance {
            background: #FFFBEB;
            border: 1px solid #FDE68A;
            color: #92400E;
        }

        .kh-alert svg {
            flex-shrink: 0;
            margin-top: 1px;
        }

        .kh-alert span {
            flex: 1;
        }

        .kh-alert-close {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            line-height: 1;
            color: inherit;
            opacity: 0.4;
            padding: 0;
            flex-shrink: 0;
        }

        .kh-alert-close:hover {
            opacity: 0.8;
        }

        /* Fields */
        .kh-field {
            margin-bottom: 1.1rem;
        }

        .kh-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.09em;
            color: #6B7A99;
            margin-bottom: 7px;
        }

        .kh-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .kh-input-icon {
            position: absolute;
            left: 14px;
            width: 15px;
            height: 15px;
            color: #B0BCCE;
            pointer-events: none;
            z-index: 1;
            flex-shrink: 0;
        }

        .kh-input {
            width: 100%;
            height: 48px;
            padding: 0 44px;
            background: #F4F6FB;
            border: 1.5px solid #E4EAF4;
            border-radius: 12px;
            font-size: 14px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #0A1628;
            outline: none;
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
        }

        .kh-input::placeholder {
            color: #B8C4D8;
        }

        /* ★ UPDATED: focus ring pakai biru foto */
        .kh-input:focus {
            border-color: #012996;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(3, 172, 233, 0.12);
        }

        .kh-input.is-invalid {
            border-color: #FCA5A5;
            background: #FEF2F2;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.08);
        }

        /* ★ UPDATED: label focus state pakai biru foto */
        .kh-field:focus-within .kh-label {
            color: #012996;
        }

        .kh-eye-btn {
            position: absolute;
            right: 13px;
            background: none;
            border: none;
            cursor: pointer;
            color: #B0BCCE;
            padding: 5px;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }

        .kh-eye-btn:hover {
            color: #5A6A88;
        }

        .kh-eye-btn svg {
            width: 16px;
            height: 16px;
        }

        /* Options row */
        .kh-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.75rem;
        }

        .kh-check {
            display: flex;
            align-items: center;
            gap: 9px;
            cursor: pointer;
            user-select: none;
        }

        .kh-check input[type="checkbox"] {
            display: none;
        }

        .kh-check-box {
            width: 18px;
            height: 18px;
            border: 1.5px solid #C8D3E8;
            border-radius: 5px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.18s;
            flex-shrink: 0;
        }

        /* ★ UPDATED: checkbox checked pakai biru foto */
        .kh-check input:checked+.kh-check-box {
            background: #012996;
            border-color: #012996;
        }

        .kh-check-box svg {
            width: 10px;
            height: 10px;
            opacity: 0;
            transition: opacity 0.15s;
            color: #fff;
        }

        .kh-check input:checked+.kh-check-box svg {
            opacity: 1;
        }

        .kh-check-label {
            font-size: 13px;
            color: #7A8AA8;
        }

        /* Submit button */
        /* ★ UPDATED: button gradient pakai biru foto */
        .kh-btn-submit {
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, #03ace9 0%, #012996 100%);
            border: none;
            border-radius: 13px;
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Sora', sans-serif;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 20px rgba(1, 41, 150, 0.35);
        }

        .kh-btn-submit::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, transparent 60%);
            opacity: 0;
            transition: opacity 0.2s;
        }

        /* ★ UPDATED: hover shadow pakai biru foto */
        .kh-btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(3, 172, 233, 0.4);
        }

        .kh-btn-submit:hover::before {
            opacity: 1;
        }

        .kh-btn-submit:active {
            transform: translateY(0);
        }

        .kh-btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* Ripple */
        .kh-btn-submit::after {
            content: '';
            position: absolute;
            inset: 50% 50%;
            background: rgba(255, 255, 255, 0.25);
            border-radius: 50%;
            transform: scale(0);
            opacity: 0;
            transition: inset 0.4s ease, transform 0.4s ease, opacity 0.4s;
        }

        .kh-btn-submit:active::after {
            inset: -50%;
            transform: scale(1);
            opacity: 1;
            transition: 0s;
        }

        .kh-btn-text,
        .kh-btn-loading {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            transition: opacity 0.25s, transform 0.25s;
        }

        .kh-btn-loading {
            opacity: 0;
            transform: translateY(6px);
        }

        .kh-btn-submit.is-loading .kh-btn-text {
            opacity: 0;
            transform: translateY(-6px);
        }

        .kh-btn-submit.is-loading .kh-btn-loading {
            opacity: 1;
            transform: translateY(0);
        }

        /* Spinner */
        .kh-spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: khSpin 0.7s linear infinite;
            flex-shrink: 0;
        }

        /* Footer */
        .kh-footer {
            text-align: center;
            margin-top: 2rem;
            font-size: 12px;
            color: #B0BCCE;
        }

        /* ── SUCCESS CURTAIN ── */
        .kh-curtain {
            position: fixed;
            inset: 0;
            z-index: 10000;
            pointer-events: none;
            display: flex;
        }

        .kh-curtain-half {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 50%;
            background: linear-gradient(150deg, #03ace9 0%, #012996 40%, #010f3d 100%);
            transition: transform 1s cubic-bezier(0.76, 0, 0.24, 1);
            will-change: transform;
        }

        .kh-curtain-half--left {
            left: 0;
            transform: translateX(-100%);
        }

        .kh-curtain-half--right {
            right: 0;
            transform: translateX(100%);
        }

        /* Curtain masuk (menutup) */
        .kh-curtain.is-closing .kh-curtain-half--left {
            transform: translateX(0);
        }

        .kh-curtain.is-closing .kh-curtain-half--right {
            transform: translateX(0);
        }

        /* Curtain membuka ke samping */
        .kh-curtain.is-opening .kh-curtain-half--left {
            transform: translateX(-100%);
            transition: transform 0.9s cubic-bezier(0.76, 0, 0.24, 1) 0.1s;
        }

        .kh-curtain.is-opening .kh-curtain-half--right {
            transform: translateX(100%);
            transition: transform 0.9s cubic-bezier(0.76, 0, 0.24, 1) 0.1s;
        }

        /* Logo di tengah curtain */
        .kh-curtain-logo {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10001;
            opacity: 0;
            transform: scale(0.7) translateY(20px);
            transition: opacity 0.5s ease, transform 0.5s cubic-bezier(0.16, 1, .3, 1);
            pointer-events: none;
        }

        .kh-curtain.is-logo-visible .kh-curtain-logo {
            opacity: 1;
            transform: scale(1) translateY(0);
        }

        /* Logo keluar saat curtain membuka */
        .kh-curtain.is-opening .kh-curtain-logo {
            opacity: 0;
            transform: scale(1.15) translateY(-10px);
            transition: opacity 0.35s ease, transform 0.35s ease;
        }

        .kh-curtain-logo-wrap {
            position: relative;
            width: 120px;
            height: 120px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .kh-cl-img {
            width: 90px;
            height: 90px;
            object-fit: contain;
            border-radius: 20px;
            filter: drop-shadow(0 8px 32px rgba(3, 172, 233, 0.6));
            animation: khLogoFloat 3s ease-in-out infinite;
            position: relative;
            z-index: 2;
        }

        .kh-cl-ring {
            position: absolute;
            inset: -4px;
            border-radius: 26px;
            border: 2px solid transparent;
            background: linear-gradient(135deg, #03ace9, #00D4AA, #012996) border-box;
            -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: destination-out;
            mask-composite: exclude;
            animation: khRingRotate 3s linear infinite;
        }

        .kh-cl-pulse {
            position: absolute;
            inset: -14px;
            border-radius: 36px;
            border: 1.5px solid rgba(3, 172, 233, 0.3);
            animation: khPulseRing 2s ease-out infinite;
        }

        .kh-cl-text {
            font-family: 'Sora', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.02em;
            text-align: center;
        }

        .kh-cl-sub {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 6px;
            text-align: center;
        }

        .kh-cl-dots::after {
            content: '';
            animation: khDots 1.4s steps(4, end) infinite;
        }

        @keyframes khLogoFloat {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        @keyframes khRingRotate {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes khPulseRing {
            0% {
                transform: scale(1);
                opacity: 0.6;
            }

            100% {
                transform: scale(1.4);
                opacity: 0;
            }
        }

        @keyframes khDots {
            0% {
                content: '';
            }

            25% {
                content: '.';
            }

            50% {
                content: '..';
            }

            75% {
                content: '...';
            }

            100% {
                content: '';
            }
        }

        /* ── KEYFRAMES ── */
        @keyframes khFadeUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes khSlideDown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes khSpin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes khShake {

            0%,
            100% {
                transform: translateX(0);
            }

            15% {
                transform: translateX(-7px);
            }

            30% {
                transform: translateX(7px);
            }

            45% {
                transform: translateX(-5px);
            }

            60% {
                transform: translateX(5px);
            }

            75% {
                transform: translateX(-3px);
            }

            90% {
                transform: translateX(3px);
            }
        }

        #loginForm.kh-shake {
            animation: khShake 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .kh-split--left {
                display: none;
            }

            .kh-split--right {
                width: 100%;
            }

            .kh-right-content {
                max-width: 460px;
                padding: 2.5rem 2rem;
                margin: auto;
            }
        }

        @media (max-width: 480px) {
            .kh-right-content {
                padding: 2rem 1.5rem;
            }

            .kh-form-title {
                font-size: 1.6rem;
            }
        }
    </style>

    <script>
        (function() {

            /* ── SPIRAL PARTICLE CANVAS ── */
            var canvas = document.getElementById('particleCanvas');
            if (canvas) {
                var ctx = canvas.getContext('2d');
                var spirals = [];
                var W, H;
                var globalTime = 0;

                function resize() {
                    W = canvas.width = canvas.offsetWidth;
                    H = canvas.height = canvas.offsetHeight;
                }

                function createSpiral() {
                    var cx = Math.random() * W;
                    var cy = Math.random() * H;
                    return {
                        cx: cx,
                        cy: cy,
                        /* pusat spiral */
                        angle: Math.random() * Math.PI * 2,
                        radius: 0,
                        maxRadius: Math.random() * 90 + 40,
                        growSpeed: Math.random() * 0.4 + 0.25,
                        rotSpeed: Math.random() * 0.04 + 0.018,
                        r: Math.random() * 1.5 + 0.5,
                        alpha: Math.random() * 0.55 + 0.15,
                        color: Math.random() > 0.5 ? '0,212,170' : '3,172,233',
                        trail: [],
                        /* titik-titik jejak spiral */
                        trailLen: Math.floor(Math.random() * 18 + 10),
                        phase: Math.random() * Math.PI * 2
                    };
                }

                function init() {
                    resize();
                    spirals = [];
                    for (var i = 0; i < 22; i++) spirals.push(createSpiral());
                }

                function tick() {
                    globalTime += 0.016;
                    /* Fade trail: semi-transparent clear */
                    ctx.fillStyle = 'rgba(1,15,61,0.18)';
                    ctx.fillRect(0, 0, W, H);

                    spirals.forEach(function(s) {
                        /* Kembangkan radius spiral */
                        s.radius += s.growSpeed;
                        s.angle += s.rotSpeed;

                        /* Posisi kepala spiral */
                        var x = s.cx + Math.cos(s.angle) * s.radius;
                        var y = s.cy + Math.sin(s.angle) * s.radius;

                        /* Rekam jejak */
                        s.trail.push({
                            x: x,
                            y: y
                        });
                        if (s.trail.length > s.trailLen) s.trail.shift();

                        /* Gambar jejak spiral sebagai garis bertahap memudar */
                        for (var t = 1; t < s.trail.length; t++) {
                            var prog = t / s.trail.length;
                            ctx.beginPath();
                            ctx.moveTo(s.trail[t - 1].x, s.trail[t - 1].y);
                            ctx.lineTo(s.trail[t].x, s.trail[t].y);
                            ctx.strokeStyle = 'rgba(' + s.color + ',' + (s.alpha * prog) + ')';
                            ctx.lineWidth = s.r * prog;
                            ctx.lineCap = 'round';
                            ctx.stroke();
                        }

                        /* Titik kepala bersinar */
                        ctx.beginPath();
                        ctx.arc(x, y, s.r * 1.6, 0, Math.PI * 2);
                        ctx.fillStyle = 'rgba(' + s.color + ',' + s.alpha + ')';
                        ctx.fill();

                        /* Reset jika spiral sudah cukup besar */
                        if (s.radius > s.maxRadius) {
                            /* lahir kembali di posisi baru */
                            s.cx = Math.random() * W;
                            s.cy = Math.random() * H;
                            s.radius = 0;
                            s.angle = Math.random() * Math.PI * 2;
                            s.maxRadius = Math.random() * 90 + 40;
                            s.growSpeed = Math.random() * 0.4 + 0.25;
                            s.rotSpeed = Math.random() * 0.04 + 0.018;
                            s.trail = [];
                            s.color = Math.random() > 0.5 ? '0,212,170' : '3,172,233';
                        }
                    });

                    requestAnimationFrame(tick);
                }

                window.addEventListener('resize', resize);
                init();
                tick();
            }

            /* ── PASSWORD TOGGLE ── */
            var eyeToggle = document.getElementById('eyeToggle');
            var pwInput = document.getElementById('password');
            var eyeIcon = document.getElementById('eyeIcon');
            var pwVisible = false;

            if (eyeToggle && pwInput) {
                eyeToggle.addEventListener('click', function() {
                    pwVisible = !pwVisible;
                    pwInput.type = pwVisible ? 'text' : 'password';
                    eyeIcon.innerHTML = pwVisible ?
                        '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>' :
                        '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
                });
            }

            /* ── CURTAIN SUCCESS ANIMATION ── */
            var loginRoot = document.getElementById('loginRoot');
            var curtain = document.getElementById('successCurtain');
            var curtainLogo = document.getElementById('curtainLogo');

            function triggerSuccessAnimation(redirectUrl) {
                /* Step 1 — login split panels geser dulu */
                loginRoot.classList.add('is-splitting');

                /* Step 2 — curtain menutup dari kiri & kanan ke tengah */
                setTimeout(function() {
                    curtain.removeAttribute('aria-hidden');
                    curtain.classList.add('is-closing');
                }, 180);

                /* Step 3 — logo muncul di tengah curtain */
                setTimeout(function() {
                    curtain.classList.add('is-logo-visible');
                }, 700);

                /* Step 4 — curtain membuka (belah tengah) ke kiri & kanan */
                setTimeout(function() {
                    curtain.classList.remove('is-closing');
                    curtain.classList.add('is-opening');
                }, 2100);

                /* Step 5 — redirect setelah curtain terbuka */
                setTimeout(function() {
                    window.location.href = redirectUrl;
                }, 3000);
            }

            window.khTriggerSuccess = triggerSuccessAnimation;

            /* ── FORM SUBMIT — AJAX + ANIMASI ── */
            var loginForm = document.getElementById('loginForm');
            var submitBtn = document.getElementById('submitBtn');
            var alertBox = document.getElementById('loginAlert');

            function showError(message) {
                /* Hapus alert lama jika ada */
                var old = document.getElementById('loginAlert');
                if (old) old.remove();

                var el = document.createElement('div');
                el.id = 'loginAlert';
                el.className = 'kh-alert kh-alert--error';
                el.innerHTML =
                    '<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2">' +
                    '<circle cx="12" cy="12" r="10"/>' +
                    '<line x1="12" y1="8" x2="12" y2="12"/>' +
                    '<line x1="12" y1="16" x2="12.01" y2="16"/>' +
                    '</svg>' +
                    '<span>' + message + '</span>' +
                    '<button class="kh-alert-close" onclick="this.closest(\'.kh-alert\').remove()" aria-label="Tutup">&times;</button>';

                /* Sisipkan sebelum form */
                loginForm.parentNode.insertBefore(el, loginForm);

                /* Shake animation pada form */
                loginForm.classList.add('kh-shake');
                setTimeout(function() {
                    loginForm.classList.remove('kh-shake');
                }, 500);
            }

            function resetBtn() {
                submitBtn.disabled = false;
                submitBtn.classList.remove('is-loading');
            }

            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    /* Loading state */
                    submitBtn.disabled = true;
                    submitBtn.classList.add('is-loading');

                    /* Kumpulkan form data */
                    var formData = new FormData(loginForm);

                    fetch(loginForm.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            redirect: 'manual' /* jangan ikuti redirect secara otomatis */
                        })
                        .then(function(res) {

                            /* ── Login BERHASIL: server kembalikan JSON {redirect: '/dashboard'} ── */
                            if (res.ok || res.status === 200) {
                                return res.json().then(function(data) {
                                    if (data.redirect) {
                                        triggerSuccessAnimation(data.redirect);
                                    } else {
                                        /* Fallback: redirect ke root */
                                        triggerSuccessAnimation('/');
                                    }
                                });
                            }

                            /* ── Login GAGAL: 422 Unprocessable (validasi) ── */
                            if (res.status === 422) {
                                return res.json().then(function(data) {
                                    resetBtn();
                                    var msg = 'Email atau password yang Anda masukkan salah.';

                                    if (data.errors) {
                                        if (data.errors.email) {
                                            msg = data.errors.email[0];
                                        } else if (data.errors.password) {
                                            msg = data.errors.password[0];
                                        }
                                    } else if (data.message) {
                                        msg = data.message;
                                    }

                                    showError(msg);

                                    /* Highlight field yang error */
                                    document.getElementById('password').value = '';
                                    document.getElementById('password').classList.add('is-invalid');
                                    document.getElementById('email').classList.add('is-invalid');
                                });
                            }

                            /* ── Error lain (500, dsb.) ── */
                            resetBtn();
                            showError('Terjadi kesalahan pada server. Silakan coba lagi.');
                        })
                        .catch(function(err) {
                            resetBtn();
                            showError('Koneksi gagal. Periksa koneksi internet Anda.');
                            console.error('Login fetch error:', err);
                        });
                });
            }

            /* Reset is-invalid saat user mengetik ulang */
            document.querySelectorAll('.kh-input').forEach(function(inp) {
                inp.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                });
            });

            /* ── FIELD FOCUS ENHANCEMENT ── */
            document.querySelectorAll('.kh-input').forEach(function(input) {
                input.addEventListener('focus', function() {
                    this.closest('.kh-field').classList.add('is-focused');
                });
                input.addEventListener('blur', function() {
                    this.closest('.kh-field').classList.remove('is-focused');
                });
            });

            /* ── SUPPRESS 3RD PARTY SCRIPT ERRORS ── */
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
                if (msg && (msg.includes('querySelector') || msg.includes('is null'))) return true;
                if (url && (url.includes('simplebar') || url.includes('dashboard'))) return true;
            };

        })();
    </script>

@endsection
