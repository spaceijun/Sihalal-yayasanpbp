<!DOCTYPE html>
<html lang="id" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<head>
    <meta charset="utf-8" />
    <title>{{ $profile->title ?? 'Tentang Kami - Kawulo Halal' }}</title>
    <meta name="description"
        content="{{ $profile->meta_description ?? 'Kenali lebih dekat tentang Kawulo Halal dan misi kami.' }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <!-- Open Graph -->
    <meta property="og:title" content="{{ $profile->title ?? 'Tentang Kami - Kawulo Halal' }}">
    <meta property="og:description"
        content="{{ $profile->meta_description ?? 'Kenali lebih dekat tentang Kawulo Halal dan misi kami.' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
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
    <!-- Kawulo Halal Modern Theme Css -->
    <link href="{{ asset('assets/css/compro-ui.css') }}" rel="stylesheet" type="text/css" />

    <style>
        /* ── Page-specific styles (about only) ── */
        .hero-title {
            font-size: clamp(2.25rem, 5vw, 3.5rem);
            font-weight: 800;
            line-height: 1.2;
            color: var(--kh-dark);
            letter-spacing: -1px;
        }

        .hero-title .highlight {
            background: var(--kh-gradient-text);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-float-badge-modern {
            position: absolute;
            bottom: -20px;
            right: -20px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(47, 143, 230, 0.15);
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 15px 35px rgba(47, 143, 230, 0.08);
            z-index: 2;
        }

        /* Value cards override inside About Page */
        .value-card-modern {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(47, 143, 230, 0.08);
            border-radius: 16px;
            padding: 1.5rem;
            transition: var(--kh-transition);
        }

        .value-card-modern:hover {
            transform: translateY(-5px);
            background: #fff;
            border-color: rgba(47, 143, 230, 0.2);
            box-shadow: 0 10px 25px rgba(47, 143, 230, 0.05);
        }

        .value-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.25rem;
            background: rgba(47, 143, 230, 0.08);
            color: var(--kh-primary);
        }

        .value-card-modern:hover .value-icon-wrapper {
            background: var(--kh-gradient);
            color: #fff;
        }

        /* Accordion Modernization */
        .accordion-modern .accordion-item {
            background: #fff;
            border: 1px solid rgba(47, 143, 230, 0.06) !important;
            border-radius: 16px !important;
            margin-bottom: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(47, 143, 230, 0.02);
            transition: var(--kh-transition);
        }

        .accordion-modern .accordion-item:hover {
            border-color: rgba(47, 143, 230, 0.15) !important;
            box-shadow: 0 10px 25px rgba(47, 143, 230, 0.05);
        }

        .accordion-modern .accordion-button {
            padding: 1.25rem 1.5rem;
            font-weight: 700;
            font-size: 1rem;
            color: var(--kh-dark) !important;
            background: #fff !important;
            border: none;
            box-shadow: none !important;
        }

        .accordion-modern .accordion-button:not(.collapsed) {
            color: var(--kh-primary) !important;
            border-bottom: 1px solid rgba(47, 143, 230, 0.06);
        }

        .accordion-modern .accordion-body {
            padding: 1.5rem;
            font-size: 0.925rem;
            line-height: 1.8;
            color: var(--kh-text-light);
            background: #FAFDFF;
        }

        .accordion-modern .accordion-button::after {
            background-size: 1rem;
            transition: var(--kh-transition);
        }
    </style>
</head>

<body>
    <div class="layout-wrapper landing">

        @include('publik.company-profile.partials.navbar')

        <!-- ========== HERO ========== -->
        <section class="section gradient-hero"
            style="padding-top: 9rem; padding-bottom: 5rem; position: relative; overflow: hidden;">
            <div class="kh-blob kh-blob-1"></div>
            <div class="kh-blob kh-blob-2"></div>
            <div class="kh-blob kh-blob-3 d-none d-lg-block"></div>

            <div class="container position-relative text-center" style="z-index: 1;">
                <div class="row justify-content-center">
                    <div class="col-lg-8" data-aos="fade-up" data-aos-duration="800">
                        <div class="modern-badge mb-4 animate-fade-in-up" style="margin: 0 auto 1.5rem;">
                            <i class="ri-building-line text-kh"></i>
                            <span>Tentang Kami</span>
                        </div>
                        <h1 class="hero-title mb-4 animate-fade-in-up delay-100">
                            Mewujudkan Sertifikasi Halal<br>
                            <span class="highlight">untuk Semua UMKM</span>
                        </h1>
                        <p class="hero-lead lead mb-0 animate-fade-in-up delay-200 text-muted"
                            style="max-width: 600px; margin: 0 auto; line-height: 1.8; font-size: 1.05rem;">
                            Kami berkomitmen mendampingi dan mempermudah pelaku usaha UMKM di Indonesia dalam meraih
                            sertifikasi halal dengan proses yang transparan dan profesional.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== TENTANG KAMI ========== -->
        <section class="section bg-white">
            <div class="container">
                <div class="row align-items-center g-5">
                    <!-- Image Side -->
                    <div class="col-lg-6" data-aos="fade-right" data-aos-duration="800">
                        <div class="position-relative">
                            <div
                                style="border-radius: 24px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.06); border: 8px solid rgba(255,255,255,0.85);">
                                <img src="{{ asset('assets/images/about.jpg') }}" alt="Tim Kawulo Halal"
                                    class="img-fluid w-100">
                            </div>
                            <!-- Float badge -->
                            <div class="hero-float-badge-modern d-none d-lg-block animate-float">
                                <div class="row g-3 text-center" style="min-width: 240px;">
                                    <div class="col-4 border-end border-light">
                                        <div class="fw-bold fs-5 text-kh">5+</div>
                                        <small class="text-muted" style="font-size: 0.75rem;">Tahun</small>
                                    </div>
                                    <div class="col-4 border-end border-light">
                                        <div class="fw-bold fs-5 text-emerald">5K+</div>
                                        <small class="text-muted" style="font-size: 0.75rem;">UMKM</small>
                                    </div>
                                    <div class="col-4">
                                        <div class="fw-bold fs-5 text-dark">98%</div>
                                        <small class="text-muted" style="font-size: 0.75rem;">Puas</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Side -->
                    <div class="col-lg-6" data-aos="fade-left" data-aos-duration="800" data-aos-delay="100">
                        <div class="modern-badge modern-badge-emerald mb-3">
                            <i class="ri-heart-line"></i>
                            <span>Siapa Kami</span>
                        </div>
                        <h2 class="section-title text-start mb-4" style="line-height: 1.3;">
                            Misi Utama Kami adalah<br>
                            Melayani dengan <span class="text-kh">Dedikasi Tinggi</span>
                        </h2>
                        <p class="text-muted mb-4" style="line-height: 1.8; font-size: 1.05rem;">
                            Kawulo Halal merupakan platform pendampingan sertifikasi halal yang diinisiasi oleh
                            <strong>Yayasan Permata Bakti Pertiwi</strong>. Kami hadir menjembatani pelaku UMKM di
                            seluruh Indonesia agar dapat mengakses sertifikasi halal secara cepat dan kredibel.
                        </p>
                        <p class="text-muted mb-5" style="line-height: 1.8; font-size: 1.05rem;">
                            Berbekal tim pendamping profesional yang berdedikasi tinggi di berbagai wilayah, kami
                            membantu menyederhanakan birokrasi pengurusan kehalalan produk Anda.
                        </p>

                        <!-- Value Cards -->
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="value-card-modern">
                                    <span class="value-icon-wrapper">
                                        <i class="ri-heart-line"></i>
                                    </span>
                                    <h6 class="fw-bold mb-1" style="color: var(--kh-dark);">Integritas</h6>
                                    <p class="text-muted small mb-0">Mengedepankan kejujuran & kepatuhan penuh syariah.
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="value-card-modern">
                                    <span class="value-icon-wrapper"
                                        style="color:var(--kh-secondary); background:rgba(25,180,160,0.08);">
                                        <i class="ri-medal-line"></i>
                                    </span>
                                    <h6 class="fw-bold mb-1" style="color: var(--kh-dark);">Profesional</h6>
                                    <p class="text-muted small mb-0">Didukung oleh tim pendamping bersertifikat resmi.
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="value-card-modern">
                                    <span class="value-icon-wrapper"
                                        style="color:var(--kh-dark); background:rgba(22,36,59,0.06);">
                                        <i class="ri-hand-heart-line"></i>
                                    </span>
                                    <h6 class="fw-bold mb-1" style="color: var(--kh-dark);">Pelayanan Prima</h6>
                                    <p class="text-muted small mb-0">Menjadikan kenyamanan klien sebagai prioritas
                                        utama.</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="value-card-modern">
                                    <span class="value-icon-wrapper">
                                        <i class="ri-speed-line"></i>
                                    </span>
                                    <h6 class="fw-bold mb-1" style="color: var(--kh-dark);">Efisiensi</h6>
                                    <p class="text-muted small mb-0">Menyediakan alur pendampingan yang ringkas.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== STATS ========== -->
        <section class="py-5" style="background: linear-gradient(180deg, #FFFFFF 0%, #F8FBFF 100%);">
            <div class="container">
                <div class="stats-glass-card" data-aos="fade-up" data-aos-duration="1000">
                    <div class="row g-4 text-center">
                        <div class="col-6 col-md-3">
                            <div class="stat-card-modern">
                                <div class="stat-number-modern" data-target="5000">0</div>
                                <div class="stat-label-modern">UMKM Didampingi</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-card-modern">
                                <div class="stat-number-modern" data-target="50">0</div>
                                <div class="stat-label-modern">Pendamping Halal</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-card-modern">
                                <div class="stat-number-modern" data-target="30">0</div>
                                <div class="stat-label-modern">Cakupan Wilayah</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-card-modern">
                                <div class="stat-number-modern" data-target="5">0</div>
                                <div class="stat-label-modern">Tahun Pengalaman</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== VISI & MISI ========== -->
        <section class="section bg-white">
            <div class="container">
                <div class="row justify-content-center mb-5" data-aos="fade-up">
                    <div class="col-lg-7 text-center">
                        <div class="modern-badge mb-3" style="margin:0 auto;">
                            <i class="ri-flag-line"></i>
                            <span>Visi & Misi</span>
                        </div>
                        <h2 class="section-title">Membangun Ekosistem <span class="text-kh">Halal</span></h2>
                        <p class="section-subtitle">Arah dan visi kami untuk mewujudkan ekosistem halal nasional yang
                            inklusif.</p>
                    </div>
                </div>
                <div class="row g-4">
                    <!-- Visi -->
                    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="0">
                        <div class="benefit-card-modern h-100" style="border-top: 4px solid var(--kh-secondary);">
                            <div class="benefit-icon-modern"
                                style="color: var(--kh-secondary); background: rgba(25, 180, 160, 0.08);">
                                <i class="ri-eye-line"></i>
                            </div>
                            <h4 class="fw-bold mb-3" style="color: var(--kh-dark);">Visi Kami</h4>
                            <p class="text-muted mb-0" style="line-height: 1.8; font-size: 0.95rem;">
                                Menjadi mitra pendampingan sertifikasi halal terpercaya dan terdepan di Indonesia,
                                mewujudkan ekosistem usaha halal yang inklusif dan berkelanjutan bagi seluruh lapisan
                                pelaku usaha nasional.
                            </p>
                        </div>
                    </div>
                    <!-- Misi -->
                    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="benefit-card-modern h-100" style="border-top: 4px solid var(--kh-primary);">
                            <div class="benefit-icon-modern">
                                <i class="ri-flag-line"></i>
                            </div>
                            <h4 class="fw-bold mb-3" style="color: var(--kh-dark);">Misi Kami</h4>
                            <ul class="list-unstyled vstack gap-3 mb-0">
                                <li class="d-flex align-items-start gap-2">
                                    <div class="avatar-xxs bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1"
                                        style="width:20px;height:20px;">
                                        <i class="ri-check-line text-success" style="font-size: 0.75rem;"></i>
                                    </div>
                                    <span class="text-muted small" style="line-height:1.5;">Menyelenggarakan edukasi
                                        dan literasi masif seputar pentingnya jaminan produk halal.</span>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <div class="avatar-xxs bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1"
                                        style="width:20px;height:20px;">
                                        <i class="ri-check-line text-success" style="font-size: 0.75rem;"></i>
                                    </div>
                                    <span class="text-muted small" style="line-height:1.5;">Menyediakan layanan
                                        konsultasi dan pendampingan sertifikasi halal yang ramah bagi pelaku usaha
                                        kecil.</span>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <div class="avatar-xxs bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1"
                                        style="width:20px;height:20px;">
                                        <i class="ri-check-line text-success" style="font-size: 0.75rem;"></i>
                                    </div>
                                    <span class="text-muted small" style="line-height:1.5;">Memperluas jejaring
                                        pendamping halal profesional dan berintegritas tinggi di seluruh
                                        Indonesia.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== CTA BANNER ========== -->
        <section class="py-5 position-relative" style="background: var(--kh-white);">
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
                                    <h3 class="text-white fw-bold mb-2">Siap Mendaftarkan Produk Anda?</h3>
                                    <p class="text-white mb-0" style="opacity:0.85;max-width:580px;">
                                        Segera hubungi kami dan mulailah perjalanan sertifikasi halal Anda bersama
                                        pendamping terbaik.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end text-center">
                            <a href="{{ route('publik.contact') }}"
                                class="btn btn-light btn-lg fw-bold px-4 py-3 shadow shine-hover">
                                <i class="ri-edit-line align-middle me-2"></i> Daftar Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== FAQ ========== -->
        <section class="section" style="background: #F8FBFF;">
            <div class="container">
                <div class="row justify-content-center mb-5" data-aos="fade-up">
                    <div class="col-lg-7 text-center">
                        <div class="modern-badge modern-badge-emerald mb-3" style="margin:0 auto;">
                            <i class="ri-question-line"></i>
                            <span>Tanya Jawab</span>
                        </div>
                        <h2 class="section-title">Pertanyaan yang Sering <span class="text-kh">Diajukan</span></h2>
                        <p class="section-subtitle">Temukan jawaban seputar pengurusan sertifikat halal.</p>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
                        <div class="accordion accordion-modern animate-fade-in-up" id="faqAccordion">

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq1">
                                        Berapa lama proses sertifikasi halal?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse show"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Proses pendampingan dan verifikasi dokumen halal low-risk umumnya memakan waktu
                                        30 hari kerja sejak dokumen terkirim lengkap kepada BPJPH.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faq2">
                                        Apa saja dokumen utama yang diperlukan?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Dokumen wajib meliputi: Nomor Induk Berusaha (NIB), list nama dan jenis produk,
                                        daftar bahan baku yang digunakan (disertai sertifikat halalnya), matriks bahan,
                                        serta surat penunjukan penyelia halal.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faq3">
                                        Bagaimana dengan biaya pengurusan?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Biaya ditentukan berdasarkan skala usaha Anda. Hubungi tim kami untuk konsultasi
                                        gratis guna mendapatkan penawaran alur dan biaya terbaik untuk usaha Anda.
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== CTA BOTTOM ========== -->
        <section class="py-5 position-relative"
            style="background: linear-gradient(135deg, #16243B 0%, #0F1A2D 100%); overflow: hidden;">
            <div class="container position-relative text-center py-4" style="z-index: 2;">
                <span
                    style="width:72px;height:72px;border-radius:50%;background:rgba(255,255,255,0.05);display:inline-flex;align-items:center;justify-content:center;margin-bottom:1.5rem;color:rgba(255,255,255,0.7);">
                    <i class="ri-chat-smile-3-line" style="font-size: 2.2rem;"></i>
                </span>
                <h3 class="text-white fw-bold mb-2">Masih Memiliki Pertanyaan Lain?</h3>
                <p class="text-white text-opacity-75 mb-4" style="max-width:480px; margin:0 auto;">Tim support
                    customer care kami siap membantu Anda 24 jam sehari, 7 hari seminggu.</p>
                <a href="{{ route('publik.contact') }}"
                    class="btn btn-light btn-lg fw-bold px-5 py-3 shadow shine-hover">
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
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- Kawulo Halal Modern Theme Js -->
    <script src="{{ asset('assets/js/compro.js') }}"></script>
</body>

</html>
