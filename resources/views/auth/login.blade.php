@extends('layouts.guest')
@section('title', 'Login')
@section('content')

    {{-- Semua style login sudah dipindahkan ke public-pages.css --}}

    <div class="pub-bg" style="display:flex; align-items:center; justify-content:center; padding:2rem 1rem; overflow:hidden;">
        <div class="pub-orb pub-orb-1"></div>
        <div class="pub-orb pub-orb-2"></div>
        <div class="pub-orb pub-orb-3"></div>

        {{-- Auth Card: dua panel side-by-side --}}
        <div
            style="
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 900px;
            display: flex;
            border-radius: 22px;
            overflow: hidden;
            box-shadow:
                0 0 0 1px rgba(100,140,210,.15),
                0 40px 80px rgba(60,100,180,.12),
                0 8px 24px rgba(60,100,180,.08);
            animation: pubCardIn .6s cubic-bezier(.16,1,.3,1) both;
        ">

            {{-- ── LEFT PANEL ── --}}
            <div class="pub-panel-left"
                style="width:42%; flex-shrink:0; display:flex; flex-direction:column; justify-content:space-between; border-radius:0;">

                <div>
                    {{-- Brand --}}
                    <div class="pub-brand">
                        <div class="pub-brand-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.9)" stroke-width="1.8">
                                <path d="M12 2L2 7l10 5 10-5-10-5z" />
                                <path d="M2 17l10 5 10-5" />
                                <path d="M2 12l10 5 10-5" />
                            </svg>
                        </div>
                        <div class="pub-brand-name">
                            Kawulo Halal
                            <small>Sertifikasi Produk Halal Untuk UMKM Low-Risk</small>
                        </div>
                    </div>

                    <p class="pub-left-title">Sistem Manajemen<br><em>Terpadu</em></p>
                    <p class="pub-left-desc">Platform pengelolaan data dan administrasi secara digital, aman, dan efisien.
                    </p>
                </div>

                <div>
                    <div class="pub-quote">
                        <div class="pub-quote-mark">"</div>
                        <p class="pub-quote-text">Jualan boleh sederhana, tapi jaminan halal harus luar biasa.</p>
                        <p class="pub-quote-author">— Agil Praditya Bapake Baskara</p>
                    </div>

                    {{-- Decorative dots --}}
                    <div
                        style="display:flex; gap:5px; align-items:center; margin-top:1.25rem; position:relative; z-index:1;">
                        <div style="height:5px; width:18px; border-radius:3px; background:#7DD3C8;"></div>
                        <div style="height:5px; width:5px; border-radius:50%; background:rgba(255,255,255,.2);"></div>
                        <div style="height:5px; width:5px; border-radius:50%; background:rgba(255,255,255,.2);"></div>
                    </div>
                </div>
            </div>

            {{-- ── RIGHT PANEL ── --}}
            <div class="pub-panel-right"
                style="flex:1; border-radius:0; display:flex; flex-direction:column; justify-content:center;">

                <div class="pub-form-header">
                    <h2>Selamat datang kembali</h2>
                    <p>Masuk ke akun Anda untuk melanjutkan</p>
                </div>
                <div class="pub-divider"></div>

                {{-- Session Status --}}
                @if (session('status'))
                    <div class="alert-success-modern" style="margin-bottom:1.25rem;">
                        <svg viewBox="0 0 24 24">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                        <div class="alt-text">{{ session('status') }}</div>
                    </div>
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
                    <div class="pub-field">
                        <label class="pub-label" for="email">Alamat Email</label>
                        <div class="pub-input-shell">
                            <svg class="pub-input-icon" viewBox="0 0 24 24">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                <polyline points="22,6 12,13 2,6" />
                            </svg>
                            <input class="pub-input {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email"
                                id="email" name="email" value="{{ old('email') }}" placeholder="nama@domain.com"
                                required autofocus autocomplete="username">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="pub-field">
                        <label class="pub-label" for="password">Password</label>
                        <div class="pub-input-shell">
                            <svg class="pub-input-icon" viewBox="0 0 24 24">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            <input class="pub-input {{ $errors->has('password') ? 'is-invalid' : '' }}" type="password"
                                id="password" name="password" placeholder="Masukkan password Anda" required
                                autocomplete="current-password">
                            <button class="pub-eye-toggle" type="button" id="eyeToggle" aria-label="Tampilkan password">
                                <svg id="eyeIconSvg" viewBox="0 0 24 24">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Options row --}}
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem;">
                        <label class="pub-check">
                            <input type="checkbox" name="remember" id="remember_me">
                            <span class="pub-check-visual">
                                <svg viewBox="0 0 24 24">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            </span>
                            <span class="pub-check-text">Ingat saya</span>
                        </label>
                    </div>

                    {{-- Submit --}}
                    <button class="pub-btn-submit" type="submit">
                        <svg viewBox="0 0 24 24">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                            <polyline points="10 17 15 12 10 7" />
                            <line x1="15" y1="12" x2="3" y2="12" />
                        </svg>
                        Masuk ke Sistem
                    </button>
                </form>

                <div class="pub-footer">
                    &copy;
                    <script>
                        document.write(new Date().getFullYear())
                    </script>
                    Yayasan Permata Bakti Pertiwi. All rights reserved.
                </div>
            </div>

        </div>
    </div>

    {{-- Responsive: sembunyikan left panel di mobile --}}
    <style>
        @media (max-width: 640px) {
            .pub-panel-left {
                display: none !important;
            }

            .pub-panel-right {
                border-radius: 22px !important;
            }
        }
    </style>

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
