<!DOCTYPE html>
<html lang="id" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<head>
    <meta charset="utf-8" />
    <title>Konfirmasi Lamaran – {{ $recruitment->nama_lengkap }} - Kawulo Halal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Layout config Js -->
    <script src="{{ asset('assets/js/layout.js') }}"></script>

    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/public-pages.css') }}" rel="stylesheet" type="text/css" />
    <!-- Kawulo Halal Modern Theme -->
    <link href="{{ asset('assets/css/compro-ui.css') }}" rel="stylesheet" type="text/css" />

    <style>
        /* ── Hero — selaras hero-closed ── */
        .hero-confirm {
            background: linear-gradient(180deg, #EAF4FF 0%, #F4FAFF 50%, #FFFFFF 100%);
            padding-top: 9rem;
            padding-bottom: 4rem;
            position: relative;
            overflow: hidden;
        }

        .hero-confirm .kh-blob {
            opacity: 0.45;
        }

        .hero-confirm-title {
            font-size: clamp(1.75rem, 4vw, 3rem);
            font-weight: 800;
            line-height: 1.15;
            color: var(--kh-dark);
        }

        .hero-confirm-title .highlight {
            background: var(--kh-gradient-text);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ── Confirm card — selaras .closed-card ── */
        .confirm-card {
            background: #fff;
            border-radius: var(--kh-radius-lg);
            border: 1px solid #EAF1FB;
            padding: 3rem 2.5rem;
            box-shadow: var(--kh-shadow-lg);
            text-align: center;
            transition: var(--kh-transition);
            position: relative;
            overflow: hidden;
        }

        .confirm-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--kh-gradient-soft);
            opacity: 0;
            transition: opacity 0.4s ease;
            pointer-events: none;
        }

        .confirm-card:hover::before {
            opacity: 1;
        }

        .confirm-card>* {
            position: relative;
            z-index: 1;
        }

        /* ── Icon circle — selaras .closed-icon ── */
        .confirm-icon {
            width: 80px;
            height: 80px;
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            background: var(--kh-sky);
            transition: var(--kh-transition);
        }

        .confirm-icon i {
            font-size: 2.25rem;
            color: var(--kh-primary);
            transition: var(--kh-transition);
        }

        .confirm-card:hover .confirm-icon {
            background: var(--kh-gradient);
            transform: scale(1.1) rotate(6deg);
        }

        .confirm-card:hover .confirm-icon i {
            color: #fff !important;
        }

        /* ── Info list — selaras .closed-info-list ── */
        .confirm-info-list {
            list-style: none;
            padding: 0;
            margin: 0 0 1.75rem;
            text-align: left;
        }

        .confirm-info-list li {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid var(--kh-sky);
            font-size: 13.5px;
            color: var(--kh-text);
        }

        .confirm-info-list li:last-child {
            border-bottom: none;
        }

        .confirm-info-list li i {
            font-size: 1.05rem;
            color: var(--kh-primary);
            flex-shrink: 0;
            width: 20px;
        }

        .confirm-info-list li span.val {
            color: var(--kh-text-light);
            font-size: 12.5px;
            margin-left: auto;
        }

        /* ── Notice box ── */
        .confirm-notice {
            background: #FFFBEB;
            border: 1px solid #FEF3C7;
            border-radius: 14px;
            padding: 1rem 1.25rem;
            display: flex;
            gap: 10px;
            text-align: left;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            font-size: 13px;
            color: #B45309;
            line-height: 1.6;
        }

        .confirm-notice i {
            font-size: 1.1rem;
            color: #D97706;
            flex-shrink: 0;
            margin-top: 2px;
        }

        /* ── WhatsApp button ── */
        .confirm-wa-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: #25D366;
            color: #fff;
            text-decoration: none;
            padding: 1rem 1.75rem;
            border-radius: 14px;
            font-size: 14.5px;
            font-weight: 600;
            width: 100%;
            transition: all 0.25s ease;
            box-shadow: 0 10px 25px rgba(37, 211, 102, 0.25);
            margin-bottom: 0.75rem;
        }

        .confirm-wa-btn:hover {
            background: #20BA5A;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(37, 211, 102, 0.35);
        }

        .confirm-wa-btn svg {
            width: 18px;
            height: 18px;
            fill: currentColor;
            flex-shrink: 0;
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .hero-confirm {
                padding-top: 7rem;
                padding-bottom: 3rem;
            }

            .confirm-card {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="layout-wrapper landing">

        @include('publik.company-profile.partials.navbar')

        <!-- ========== HERO ========== -->
        <section class="hero-confirm" id="hero">
            <div class="kh-blob kh-blob-1"></div>
            <div class="kh-blob kh-blob-2"></div>
            <div class="container position-relative text-center" style="z-index: 1;">
                <div class="row justify-content-center">
                    <div class="col-lg-7">
                        <div class="hero-badge mb-4 animate-fade-in-up" style="margin: 0 auto 1.5rem;">
                            <i class="ri-mail-send-line text-kh"></i>
                            <span class="small fw-semibold">Rekrutmen</span>
                        </div>
                        <h1 class="hero-confirm-title mb-4 animate-fade-in-up delay-100">
                            Lamaran<br>
                            <span class="highlight">Berhasil Dikirim!</span>
                        </h1>
                        <p class="hero-lead lead mb-0 animate-fade-in-up delay-200"
                            style="max-width: 480px; margin: 0 auto;">
                            Data <strong>{{ $recruitment->nama_lengkap }}</strong> telah kami terima.
                            Lengkapi konfirmasi melalui WhatsApp untuk memproses lamaran Anda.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== BREADCRUMB ========== -->
        <div class="bg-kh-soft border-bottom" style="border-color: rgba(47,143,230,0.08) !important;">
            <div class="container py-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item">
                            <a href="{{ route('publik.home') }}" class="text-decoration-none text-kh">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('publik.recruitment.index') }}"
                                class="text-decoration-none text-kh">Lowongan</a>
                        </li>
                        <li class="breadcrumb-item active text-muted" aria-current="page">Konfirmasi Lamaran</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- ========== MAIN CONTENT ========== -->
        <section class="section" id="confirm-info">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-5 col-md-7" data-aos="fade-up">
                        <div class="confirm-card">

                            {{-- Icon --}}
                            <div class="confirm-icon">
                                <i class="ri-checkbox-circle-line"></i>
                            </div>

                            {{-- Badge posisi --}}
                            <span class="badge bg-kh-soft text-kh px-3 py-2 mb-3 fw-semibold"
                                style="border-radius: var(--kh-radius-pill); font-size: 12px;">
                                {{ $recruitment->recruit_type }}
                            </span>

                            {{-- Judul --}}
                            <h4 class="fw-bold mb-2" style="color: var(--kh-dark);">Lamaran Berhasil Dikirim!</h4>
                            <p class="text-muted mb-4" style="font-size: 14px; line-height: 1.7;">
                                Terima kasih <strong>{{ $recruitment->nama_lengkap }}</strong>,<br>
                                data Anda telah kami terima dengan baik.
                            </p>

                            {{-- Info list --}}
                            <ul class="confirm-info-list">
                                <li>
                                    <i class="ri-briefcase-4-line"></i>
                                    <strong>Posisi Dilamar</strong>
                                    <span class="val">{{ $recruitment->recruit_type }}</span>
                                </li>
                                <li>
                                    <i class="ri-phone-line"></i>
                                    <strong>No. Telepon</strong>
                                    <span class="val">{{ $recruitment->telephone }}</span>
                                </li>
                                <li>
                                    <i class="ri-graduation-cap-line"></i>
                                    <strong>Pendidikan Terakhir</strong>
                                    <span class="val">{{ $recruitment->pendidikan_terakhir }}</span>
                                </li>
                                <li>
                                    <i class="ri-user-line"></i>
                                    <strong>Jenis Kelamin</strong>
                                    <span class="val">{{ $recruitment->jenis_kelamin }}</span>
                                </li>
                                @if ($recruitment->rekomendasi)
                                    <li>
                                        <i class="ri-star-line"></i>
                                        <strong>Rekomendasi</strong>
                                        <span class="val">{{ $recruitment->rekomendasi }}</span>
                                    </li>
                                @endif
                                <li>
                                    <i class="ri-time-line"></i>
                                    <strong>Status</strong>
                                    <span class="val">
                                        <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold"
                                            style="font-size: 11.5px; border-radius: 20px; padding: 4px 11px;">
                                            <i class="ri-loader-4-line align-bottom me-1"></i>Melamar
                                        </span>
                                    </span>
                                </li>
                            </ul>



                            <a href="{{ route('publik.recruitment.index') }}"
                                class="btn btn-kh-outline w-100 py-3 fw-bold">
                                <i class="ri-arrow-left-line align-middle me-1"></i> Lihat Lowongan Lain
                            </a>

                        </div>

                        <p class="text-center text-muted small mt-4">
                            Ingin melihat posisi lain yang tersedia?
                            <a href="{{ route('publik.recruitment.index') }}"
                                class="text-kh fw-semibold text-decoration-none">Cek di sini</a>
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== CTA — selaras close.blade.php ========== -->
        <section class="py-5 gradient-kh cta-section position-relative">
            <div class="container position-relative">
                <div class="row align-items-center gy-4">
                    <div class="col-sm-8">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-lg flex-shrink-0">
                                <div class="avatar-title bg-white bg-opacity-25 rounded-3">
                                    <i class="ri-customer-service-2-fill text-white fs-2"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-white mb-1">Ada pertanyaan seputar rekrutmen?</h4>
                                <p class="text-white text-opacity-75 mb-0">Tim kami siap membantu Anda menemukan
                                    posisi yang tepat.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 text-sm-end">
                        <a href="{{ route('publik.contact') }}"
                            class="btn btn-light btn-lg fw-bold shadow shine-hover">
                            <i class="ri-message-3-line align-bottom me-1"></i> Hubungi Kami
                        </a>
                    </div>
                </div>
            </div>
        </section>

        @include('publik.company-profile.partials.footer')
    </div>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <script src="{{ asset('assets/js/plugins.js') }}"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="{{ asset('assets/js/compro.js') }}"></script>
</body>

</html>
