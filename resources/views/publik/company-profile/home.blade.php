<!DOCTYPE html>
<html lang="id" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<head>
    <meta charset="utf-8" />
    <title>{{ $settingWebsite->title ?? 'Kawulo Halal - Jasa Sertifikasi Halal Low Risk' }}</title>
    <meta name="description"
        content="{{ $profile->meta_description ?? 'Kawulo Halal memberikan layanan sertifikasi halal untuk UMKM dengan proses mudah dan cepat.' }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('storage/' . $settingWebsite->favicon) }}">
    <!-- Open Graph -->
    <meta property="og:title" content="{{ $profile->title ?? 'Kawulo Halal' }}">
    <meta property="og:description"
        content="{{ $profile->meta_description ?? 'Layanan sertifikasi halal terpercaya untuk UMKM Indonesia' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- Swiper slider css -->
    <link href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />
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
    <!-- Kawulo Halal Modern Theme Css -->
    <link href="{{ asset('assets/css/compro-ui.css') }}" rel="stylesheet" type="text/css" />
    <style>
        /* ── Modern Hero Section Styles ── */
        .badge-pulse {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--kh-primary);
            margin-right: 8px;
            position: relative;
            animation: pulse-ring 1.5s cubic-bezier(0.215, 0.610, 0.355, 1) infinite;
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(15, 44, 89, 0.4);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 8px rgba(15, 44, 89, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(15, 44, 89, 0);
            }
        }

        .highlight-wrapper {
            position: relative;
            display: inline-block;
        }

        .typewriter-cursor {
            color: var(--kh-primary);
            animation: blink-cursor 0.75s step-end infinite;
            font-weight: 300;
            margin-left: 2px;
            display: inline-block;
            vertical-align: bottom;
        }

        @keyframes blink-cursor {

            from,
            to {
                color: transparent
            }

            50% {
                color: var(--kh-primary)
            }
        }

        /* Visualizer Card */
        .interactive-visualizer-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: var(--kh-radius-lg);
            border: 1px solid rgba(15, 44, 89, 0.08);
            box-shadow: var(--kh-shadow-lg);
            transition: var(--kh-transition);
            position: relative;
            overflow: hidden;
            z-index: 10;
        }

        .interactive-visualizer-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px rgba(15, 44, 89, 0.2);
            border-color: rgba(15, 44, 89, 0.15);
        }

        .dot-active-pulsing {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #198754;
            position: relative;
            animation: pulse-ring-green 1.5s infinite;
        }

        @keyframes pulse-ring-green {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.4);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 6px rgba(25, 135, 84, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(25, 135, 84, 0);
            }
        }

        .visualizer-tab-btn {
            background: #fff;
            border: 1.5px solid rgba(15, 44, 89, 0.06);
            border-radius: 16px;
            padding: 1rem 0.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: var(--kh-transition);
            cursor: pointer;
            color: var(--kh-text-light);
        }

        .visualizer-tab-btn i {
            font-size: 1.4rem;
            transition: var(--kh-transition);
        }

        .visualizer-tab-btn span {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .visualizer-tab-btn:hover {
            border-color: var(--kh-primary);
            color: var(--kh-primary);
            background: rgba(15, 44, 89, 0.02);
            transform: translateY(-2px);
        }

        .visualizer-tab-btn.active {
            background: var(--kh-gradient);
            border-color: transparent;
            color: #fff;
            box-shadow: 0 8px 20px rgba(15, 44, 89, 0.15);
        }

        .visualizer-tab-btn.active i {
            transform: scale(1.15);
            color: #fff;
        }

        .visualizer-content-panel {
            background: rgba(15, 44, 89, 0.02);
            border: 1px solid rgba(15, 44, 89, 0.04);
            border-radius: 20px;
            min-height: 160px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .step-content {
            animation: fadeIn 0.4s ease forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(4px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-silver {
            background: rgba(142, 154, 175, 0.08);
            border: 1px solid rgba(142, 154, 175, 0.12);
        }

        .bg-kh {
            background: var(--kh-gradient);
        }

        .progress-modern {
            border-radius: 10px;
            background-color: rgba(15, 44, 89, 0.05);
            overflow: hidden;
        }

        /* Prevent fixed navbar overlap */
        .hero-section {
            padding-top: 110px !important;
            padding-bottom: 60px !important;
        }

        @media (min-width: 992px) {
            .hero-section {
                padding-top: 130px !important;
                padding-bottom: 80px !important;
                min-height: 100vh;
                display: flex;
                align-items: center;
            }
        }
    </style>
</head>

<body>
    <div class="layout-wrapper landing">
        @include('publik.company-profile.partials.navbar')

        <!-- ========== HERO SECTION ========== -->
        <section class="section hero-section gradient-hero py-5" id="hero">
            <!-- Decorative animated blobs -->
            <div class="kh-blob kh-blob-1"></div>
            <div class="kh-blob kh-blob-2"></div>
            <div class="kh-blob kh-blob-3 d-none d-lg-block"></div>



            <div class="container position-relative" style="z-index: 2;">
                <div class="row align-items-center min-vh-100 py-5">
                    <div class="col-lg-6" data-aos="fade-right" data-aos-duration="1000">
                        <div class="modern-badge mb-4 animate-fade-in-up">
                            <span class="badge-pulse"></span>
                            <i class="ri-verified-badge-fill text-kh"></i>
                            <span>Layanan Sertifikasi Halal Resmi</span>
                        </div>
                        <h1 class="hero-title mb-4 animate-fade-in-up delay-100"
                            style="letter-spacing: -2px; font-weight: 800; line-height: 1.15;">
                            Sertifikasi Halal<br>
                            <span class="highlight-wrapper">
                                <span class="highlight" id="typewriter-text">Semakin Mudah</span><span
                                    class="typewriter-cursor">|</span>
                            </span><br>
                            untuk UMKM Indonesia
                        </h1>
                        <p class="hero-lead lead mb-5 animate-fade-in-up delay-200 text-muted"
                            style="max-width: 520px; font-size: 1.05rem; line-height: 1.8;">
                            Kawulo Halal mendampingi pelaku UMKM mendapatkan sertifikat halal resmi dengan alur
                            terintegrasi, kelengkapan berkas praktis, dan biaya terjangkau.
                        </p>
                        <div class="d-flex flex-wrap gap-3 animate-fade-in-up delay-300">
                            <a href="{{ route('publik.contact') }}" class="btn btn-kh btn-lg px-4 py-3 shadow">
                                <i class="ri-arrow-right-line align-middle me-2"></i> Mulai Sekarang
                            </a>
                            <a href="{{ route('publik.about') }}" class="btn btn-kh-outline btn-lg px-4 py-3">
                                <i class="ri-play-circle-line align-middle me-2"></i> Pelajari Lebih
                            </a>
                        </div>

                        <!-- Partner Logos Strip -->
                        <div class="partner-logos-strip mt-5 animate-fade-in-up delay-400">
                            <span
                                class="partner-label text-uppercase text-muted d-block mb-3 fs-11 tracking-wider">Mitra
                                &amp; Pendamping Resmi</span>
                            <div class="d-flex align-items-center gap-4 opacity-50">
                                <span class="fw-semibold text-dark fs-14"><i class="ri-government-line me-1"></i>
                                    BPJPH</span>
                                <span class="fw-semibold text-dark fs-14"><i class="ri-bank-line me-1"></i>
                                    KEMENAG</span>
                                <span class="fw-semibold text-dark fs-14"><i class="ri-shield-user-line me-1"></i>
                                    MUI</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5 offset-lg-1 d-none d-lg-block position-relative" data-aos="fade-left"
                        data-aos-duration="1000" data-aos-delay="200">
                        <!-- Floating Badge 1 (Top Left of Card) -->
                        <div class="hero-float-badge-modern" style="top: -20px; left: -45px; z-index: 20;">
                            <div class="d-flex align-items-center gap-3">
                                <span
                                    style="width:36px;height:36px;min-width:36px;border-radius:50%;background:rgba(25,135,84,0.1);display:inline-flex;align-items:center;justify-content:center;">
                                    <i class="ri-shield-check-fill" style="font-size:1rem;color:#198754;"></i>
                                </span>
                                <div>
                                    <span class="fw-bold text-dark d-block" style="font-size:0.82rem;">Kawulo
                                        Halal</span>
                                    <span style="font-size:0.72rem;color:var(--kh-text-light);">Sertifikasi Halal
                                        UMKM</span>
                                </div>
                            </div>
                        </div>

                        <!-- Floating Badge 2 (Bottom Right of Card) -->
                        <div class="hero-float-badge-modern"
                            style="bottom: -20px; right: -35px; z-index: 20; animation-delay: 2.5s;">
                            <div class="d-flex align-items-center gap-3">
                                <span
                                    style="width:36px;height:36px;min-width:36px;border-radius:50%;background:rgba(247,144,9,0.1);display:inline-flex;align-items:center;justify-content:center;">
                                    <i class="ri-star-fill" style="font-size:1rem;color:#f79009;"></i>
                                </span>
                                <div>
                                    <span class="fw-bold text-dark d-block" style="font-size:0.82rem;">98%
                                        Kepuasan</span>
                                    <span style="font-size:0.72rem;color:var(--kh-text-light);">Proses Cepat &amp;
                                        Sah</span>
                                </div>
                            </div>
                        </div>

                        <!-- Interactive visualizer card -->
                        <div class="interactive-visualizer-card p-4 p-md-5">
                            <div class="visualizer-header d-flex justify-content-between align-items-center mb-4">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="dot-active-pulsing"></span>
                                    <span class="text-uppercase tracking-wider fs-10 fw-bold text-kh">Proses
                                        Simulator</span>
                                </div>
                                <span class="badge bg-kh-soft text-kh fs-11" id="visualizer-status">Langkah 1:
                                    Pengisian</span>
                            </div>

                            <!-- The Tabs / Clicking items -->
                            <div class="row g-2 mb-4">
                                <div class="col-4">
                                    <button class="visualizer-tab-btn active w-100" data-step="1">
                                        <i class="ri-draft-line"></i>
                                        <span>1. Berkas</span>
                                    </button>
                                </div>
                                <div class="col-4">
                                    <button class="visualizer-tab-btn w-100" data-step="2">
                                        <i class="ri-shield-flash-line"></i>
                                        <span>2. Verifikasi</span>
                                    </button>
                                </div>
                                <div class="col-4">
                                    <button class="visualizer-tab-btn w-100" data-step="3">
                                        <i class="ri-award-line"></i>
                                        <span>3. Sertifikat</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Active Details Card -->
                            <div class="visualizer-content-panel p-4 mb-4">
                                <div id="step-content-1" class="step-content">
                                    <h6 class="fw-bold text-dark mb-2">Pendaftaran &amp; Unggah Berkas</h6>
                                    <p class="text-muted small mb-3">Pelaku usaha mengisi NIB, data produk, serta
                                        daftar bahan baku yang digunakan. Tim pendamping kami siap membimbing pengisian
                                        agar bebas kesalahan.</p>
                                    <div
                                        class="alert alert-silver d-flex align-items-center gap-2 mb-0 py-2 px-3 border-0">
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        <span class="small text-dark-soft">Butuh waktu kurang dari 1 hari</span>
                                    </div>
                                </div>

                                <div id="step-content-2" class="step-content d-none">
                                    <h6 class="fw-bold text-dark mb-2">Verifikasi Dokumen &amp; Sidang Fatwa</h6>
                                    <p class="text-muted small mb-3">Pendamping Halal memverifikasi kecocokan bahan
                                        baku dan alur proses produksi halal secara digital (Self-Declare), dilanjutkan
                                        dengan Sidang Fatwa MUI.</p>
                                    <div
                                        class="alert alert-silver d-flex align-items-center gap-2 mb-0 py-2 px-3 border-0">
                                        <i class="ri-time-fill text-kh"></i>
                                        <span class="small text-dark-soft">Proses verifikasi digital ~7-14 hari</span>
                                    </div>
                                </div>

                                <div id="step-content-3" class="step-content d-none">
                                    <h6 class="fw-bold text-dark mb-2">Sertifikat Halal Resmi Terbit</h6>
                                    <p class="text-muted small mb-3">BPJPH menerbitkan sertifikat halal resmi ber-QR
                                        Code yang berlaku selamanya. Produk Anda kini siap bersaing secara sah di pasar
                                        nasional!</p>
                                    <div
                                        class="alert alert-silver d-flex align-items-center gap-2 mb-0 py-2 px-3 border-0">
                                        <i class="ri-award-fill text-warning"></i>
                                        <span class="small text-dark-soft">Sertifikat terbit resmi BPJPH</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Indicator -->
                            <div class="visualizer-progress-wrapper">
                                <div class="d-flex justify-content-between mb-2">
                                    <small class="text-muted text-uppercase tracking-wider fs-10">Estimasi
                                        Kemajuan</small>
                                    <small class="fw-bold text-kh fs-11" id="visualizer-percentage">35%
                                        Selesai</small>
                                </div>
                                <div class="progress progress-modern" style="height: 8px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-kh"
                                        id="visualizer-progress-bar" style="width: 35%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== STATS COUNTER ========== -->
        <div class="stats-floating-container" id="stats">
            <div class="container">
                <div class="stats-glass-card" data-aos="fade-up" data-aos-duration="1000">
                    <div class="row g-0 text-center">
                        @foreach ($stats ?? [] as $stat)
                            <div class="col-6 col-md-3 stat-card-modern" data-aos="fade-up"
                                data-aos-delay="{{ $loop->index * 100 }}">
                                <div class="stat-number-modern" data-target="{{ (int) $stat->value }}">0</div>
                                <div class="stat-label-modern">{{ $stat->title }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== LAYANAN / BENEFITS ========== -->
        <section class="section py-5 py-md-6" style="padding-top: 100px; padding-bottom: 80px;" id="layanan">
            <div class="container">
                <div class="row justify-content-center mb-5" data-aos="fade-up">
                    <div class="col-lg-7 text-center">
                        <div class="modern-badge modern-badge-emerald mb-3">
                            <i class="ri-service-line"></i>
                            <span>Layanan Utama</span>
                        </div>
                        <h2 class="section-title mb-3">Kenapa Memilih <span class="text-kh">Kawulo Halal?</span></h2>
                        <p class="section-subtitle">Kami menyediakan kemudahan dan bimbingan terintegrasi untuk
                            membantu UMKM Anda mencapai standarisasi halal.</p>
                    </div>
                </div>
                <div class="row g-4">
                    @foreach ($benefits ?? [] as $benefit)
                        <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                            <div class="benefit-card-modern">
                                <div class="benefit-icon-modern">
                                    <i class="{{ $benefit->icon ?? 'ri-check-line' }}"></i>
                                </div>
                                <h5 class="fw-bold mb-2 text-dark">{{ $benefit->title }}</h5>
                                <p class="text-muted mb-0 small" style="line-height: 1.6;">
                                    {{ $benefit->description ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- ========== CTA BANNER ========== -->
        <section class="py-4 position-relative">
            <div class="container">
                <div class="cta-box-modern" data-aos="zoom-in" data-aos-duration="800">
                    <div class="cta-bg-blob"></div>
                    <div class="row align-items-center gy-4 position-relative" style="z-index: 2;">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center gap-4 flex-wrap flex-sm-nowrap">
                                <span
                                    style="width:64px;height:64px;min-width:64px;border-radius:16px;background:rgba(255,255,255,0.15);backdrop-filter:blur(8px);display:inline-flex;align-items:center;justify-content:center;"
                                    class="d-none d-sm-inline-flex">
                                    <i class="ri-shield-check-fill text-white" style="font-size:1.8rem;"></i>
                                </span>
                                <div>
                                    <h3 class="text-white fw-bold mb-2">Siap Mendapatkan Sertifikat Halal?</h3>
                                    <p class="text-white mb-0" style="opacity:0.85;max-width:580px;">
                                        Bergabunglah bersama ribuan pelaku UMKM Indonesia yang telah sukses meningkatkan
                                        omset dengan sertifikasi halal resmi.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end text-center">
                            <a href="{{ route('publik.contact') }}"
                                class="btn btn-light btn-lg fw-bold px-4 py-3 shadow shine-hover">
                                <i class="ri-arrow-right-line align-middle me-2"></i> Daftar Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== ARTIKEL ========== -->
        @if ($featuredArticles->isNotEmpty())
            <section class="section py-5 py-md-6" style="padding-top: 80px; padding-bottom: 80px;" id="artikel">
                <div class="container">
                    <div class="row justify-content-center mb-5" data-aos="fade-up">
                        <div class="col-lg-7 text-center">
                            <div class="modern-badge mb-3">
                                <i class="ri-book-open-line"></i>
                                <span>Informasi & Edukasi</span>
                            </div>
                            <h2 class="section-title mb-3">Artikel <span class="text-kh">Terbaru</span></h2>
                            <p class="section-subtitle">Temukan tips bisnis, regulasi halal terbaru, dan panduan
                                praktis untuk mendongkrak omset UMKM Anda.</p>
                        </div>
                    </div>
                    <div class="row g-4">
                        @foreach ($featuredArticles as $article)
                            <div class="col-lg-4 col-md-6" data-aos="fade-up"
                                data-aos-delay="{{ $loop->index * 100 }}">
                                <a href="{{ route('publik.articles.show', $article->slug) }}"
                                    class="text-decoration-none">
                                    <div class="article-card-modern">
                                        <div class="article-img-container">
                                            @if ($article->image)
                                                <img src="{{ Storage::url($article->image) }}"
                                                    alt="{{ $article->title }}" class="card-img w-100">
                                            @else
                                                <div
                                                    class="bg-kh d-flex align-items-center justify-content-center h-100">
                                                    <i class="ri-article-line text-white opacity-40"
                                                        style="font-size: 3.5rem;"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center gap-3 mb-3">
                                                <span
                                                    class="article-badge">{{ ucfirst($article->category ?? 'Artikel') }}</span>
                                                <span class="text-muted small d-flex align-items-center gap-1">
                                                    <i class="ri-time-line"></i> {{ $article->reading_time ?? 3 }} min
                                                </span>
                                            </div>
                                            <h5 class="fw-bold mb-3 lh-base text-dark"
                                                style="font-size: 1.1rem; transition: color 0.3s ease;">
                                                {{ Str::limit($article->title, 55) }}
                                            </h5>
                                            <p class="text-muted small mb-4" style="line-height: 1.6;">
                                                {{ Str::limit($article->excerpt_or_truncated, 90) }}
                                            </p>
                                            <span class="text-kh fw-bold small d-flex align-items-center gap-1">
                                                Baca Selengkapnya <i class="ri-arrow-right-line align-middle"></i>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-5" data-aos="fade-up">
                        <a href="{{ route('publik.articles.index') }}" class="btn btn-kh btn-lg px-4 py-3">
                            <i class="ri-arrow-right-line align-middle me-2"></i> Lihat Semua Artikel
                        </a>
                    </div>
                </div>
            </section>
        @endif

        <!-- ========== TESTIMONIALS ========== -->
        @if ($testimonials->isNotEmpty())
            <section class="section py-5 py-md-6" style="padding-top: 80px; padding-bottom: 80px;" id="testimonial">
                <div class="container">
                    <div class="row justify-content-center mb-5" data-aos="fade-up">
                        <div class="col-lg-7 text-center">
                            <div class="modern-badge modern-badge-orange mb-3">
                                <i class="ri-star-fill"></i>
                                <span>Kisah Sukses UMKM</span>
                            </div>
                            <h2 class="section-title mb-3">Apa Kata <span class="text-kh">Mereka?</span></h2>
                            <p class="section-subtitle">Dengarkan pengalaman langsung para pelaku usaha yang telah
                                dibantu dalam proses sertifikasi halal.</p>
                        </div>
                    </div>
                    <div class="row g-4">
                        @foreach ($testimonials as $testimonial)
                            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                                <div class="testimonial-card-modern d-flex flex-column h-100">
                                    <div class="testimonial-quote-modern">“</div>
                                    <div class="text-warning mb-3">
                                        @for ($i = 0; $i < 5; $i++)
                                            <i class="ri-star{{ $i < $testimonial->rating ? '-fill' : '' }}"></i>
                                        @endfor
                                    </div>
                                    <p class="testimonial-text flex-grow-1">
                                        "{{ Str::limit($testimonial->testimonial, 160) }}"
                                    </p>
                                    <div class="d-flex align-items-center gap-3 pt-3 border-top border-light mt-auto">
                                        @if ($testimonial->photo)
                                            <img src="{{ Storage::url($testimonial->photo) }}"
                                                alt="{{ $testimonial->name }}" class="testimonial-avatar-modern">
                                        @else
                                            <div class="testimonial-avatar-placeholder bg-kh">
                                                {{ strtoupper(substr($testimonial->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark fs-14">{{ $testimonial->name }}</h6>
                                            <small
                                                class="text-muted-soft fs-12">{{ $testimonial->position ?? 'Pemilik UMKM' }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- ========== LOWONGAN KERJA ========== -->
        @if ($recruitmentPosts->isNotEmpty())
            <section class="section py-5 py-md-6" style="padding-top: 80px; padding-bottom: 80px;" id="karir">
                <div class="container">
                    <div class="row justify-content-center mb-5" data-aos="fade-up">
                        <div class="col-lg-7 text-center">
                            <div class="modern-badge mb-3">
                                <i class="ri-briefcase-line"></i>
                                <span>Bergabung Bersama Kami</span>
                            </div>
                            <h2 class="section-title mb-3">Peluang <span class="text-kh">Karir</span></h2>
                            <p class="section-subtitle">Mari berkontribusi membangun ekosistem halal Indonesia dengan
                                bergabung bersama tim profesional Kawulo Halal.</p>
                        </div>
                    </div>
                    <div class="row g-4">
                        @foreach ($recruitmentPosts as $post)
                            <div class="col-lg-4 col-md-6" data-aos="fade-up"
                                data-aos-delay="{{ $loop->index * 100 }}">
                                <div class="job-card-modern d-flex flex-column h-100">
                                    <div class="d-flex align-items-start justify-content-between mb-4">
                                        <div class="d-flex flex-wrap gap-2">
                                            @if ($post->isOpen())
                                                <span
                                                    class="modern-badge modern-badge-emerald px-2 py-1 fs-10 text-uppercase">
                                                    <i class="ri-checkbox-circle-line"></i> Terbuka
                                                </span>
                                            @else
                                                <span
                                                    class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1 fs-10 text-uppercase">Ditutup</span>
                                            @endif
                                            @if ($post->posisi)
                                                <span
                                                    class="modern-badge px-2 py-1 fs-10 text-uppercase">{{ $post->posisi }}</span>
                                            @endif
                                        </div>
                                        <div class="avatar-md bg-kh-soft rounded-3 d-flex align-items-center justify-content-center"
                                            style="width: 42px; height: 42px; flex-shrink: 0;">
                                            <i class="ri-briefcase-4-line text-kh fs-20"></i>
                                        </div>
                                    </div>
                                    <h5 class="fw-bold mb-2 text-dark fs-16">{{ $post->nama_loker }}</h5>
                                    <p class="text-muted small flex-grow-1 mb-4" style="line-height: 1.6;">
                                        {{ Str::limit($post->deskripsi, 120) }}</p>
                                    <div class="d-flex gap-2 mt-auto">
                                        <a href="{{ route('publik.recruitment.show', $post->slug) }}"
                                            class="btn {{ $post->isOpen() ? 'btn-kh' : 'btn-secondary' }} btn-sm flex-grow-1 py-2">
                                            <i class="ri-eye-line align-middle me-1"></i> Detail
                                        </a>
                                        @if ($post->isOpen())
                                            <a href="{{ route('recruitment.form', $post->slug) }}"
                                                class="btn btn-kh-outline btn-sm px-3">
                                                <i class="ri-send-plane-line align-middle"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-5" data-aos="fade-up">
                        <a href="{{ route('publik.recruitment.index') }}" class="btn btn-kh btn-lg px-4 py-3">
                            <i class="ri-arrow-right-line align-middle me-2"></i> Lihat Semua Lowongan
                        </a>
                    </div>
                </div>
            </section>
        @endif

        <!-- ========== CTA BOTTOM ========== -->
        <section class="py-5 position-relative"
            style="background: linear-gradient(180deg, var(--kh-white) 0%, var(--kh-sky) 100%);" id="kontak">
            <div class="container position-relative text-center py-5">
                <div class="kh-blob kh-blob-2 d-none d-md-block" style="opacity: 0.18; top: -10%; left: 25%;"></div>
                <div class="kh-blob kh-blob-1 d-none d-md-block"
                    style="opacity: 0.12; bottom: -20%; right: 20%; width: 300px; height: 300px;"></div>
                <span
                    style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,rgba(15,44,89,0.12),rgba(142,154,175,0.08));display:inline-flex;align-items:center;justify-content:center;animation:float-badge-anim 8s ease-in-out infinite;margin-bottom:1.5rem;">
                    <i class="ri-message-3-line" style="font-size:2rem;color:var(--kh-primary);"></i>
                </span>
                <h2 class="section-title fw-bold mb-3">Punya Pertanyaan?</h2>
                <p class="text-muted mb-5"
                    style="max-width: 500px; margin-left: auto; margin-right: auto; font-size: 1.05rem; line-height: 1.7;">
                    Tim ahli pendamping halal kami siap melayani dan memberikan konsultasi gratis untuk bisnis Anda.</p>
                <a href="{{ route('publik.contact') }}" class="btn btn-kh btn-lg px-5 py-3 shadow-lg shine-hover">
                    <i class="ri-message-3-line align-middle me-2"></i> Hubungi Kami Sekarang
                </a>
            </div>
        </section>

        @include('publik.company-profile.partials.footer')
    </div>

    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <script src="{{ asset('assets/js/plugins.js') }}"></script>
    <script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- Kawulo Halal Modern Theme Js -->
    <script src="{{ asset('assets/js/compro.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Typewriter Animation
            const textElement = document.getElementById("typewriter-text");
            if (textElement) {
                const words = ["Semakin Mudah", "Lebih Cepat", "Sangat Praktis", "Terpercaya"];
                let wordIndex = 0;
                let charIndex = 0;
                let isDeleting = false;
                let typingSpeed = 100;

                function type() {
                    const currentWord = words[wordIndex];
                    if (isDeleting) {
                        charIndex--;
                        typingSpeed = 50;
                    } else {
                        charIndex++;
                        typingSpeed = 150;
                    }

                    textElement.textContent = currentWord.substring(0, charIndex);

                    if (!isDeleting && charIndex === currentWord.length) {
                        typingSpeed = 2000;
                        isDeleting = true;
                    } else if (isDeleting && charIndex === 0) {
                        isDeleting = false;
                        wordIndex = (wordIndex + 1) % words.length;
                        typingSpeed = 500;
                    }

                    setTimeout(type, typingSpeed);
                }
                setTimeout(type, 1000);
            }

            // Interactive Process Simulator
            const tabs = document.querySelectorAll(".visualizer-tab-btn");
            const contents = document.querySelectorAll(".step-content");
            const statusBar = document.getElementById("visualizer-status");
            const progressVal = document.getElementById("visualizer-percentage");
            const progressBar = document.getElementById("visualizer-progress-bar");

            tabs.forEach(tab => {
                tab.addEventListener("click", function() {
                    const step = this.dataset.step;

                    tabs.forEach(t => t.classList.remove("active"));
                    this.classList.add("active");

                    contents.forEach(c => {
                        c.classList.add("d-none");
                        c.classList.remove("active");
                    });
                    const activeContent = document.getElementById("step-content-" + step);
                    if (activeContent) {
                        activeContent.classList.remove("d-none");
                        activeContent.classList.add("active");
                    }

                    if (step === "1") {
                        statusBar.textContent = "Langkah 1: Pengisian";
                        progressVal.textContent = "35% Selesai";
                        progressBar.style.width = "35%";
                    } else if (step === "2") {
                        statusBar.textContent = "Langkah 2: Verifikasi";
                        progressVal.textContent = "70% Selesai";
                        progressBar.style.width = "70%";
                    } else if (step === "3") {
                        statusBar.textContent = "Langkah 3: Penerbitan";
                        progressVal.textContent = "100% Selesai";
                        progressBar.style.width = "100%";
                    }
                });
            });
        });
    </script>
</body>

</html>
