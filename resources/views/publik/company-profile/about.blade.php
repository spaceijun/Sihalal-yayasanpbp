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
</head>

<body>
    <div class="layout-wrapper landing">

        @include('publik.company-profile.partials.navbar')

        <!-- ========== HERO ========== -->
        <section class="section gradient-hero"
            style="padding-top: 8rem; padding-bottom: 5rem; position: relative; overflow: hidden;">
            <div class="kh-blob kh-blob-1"></div>
            <div class="kh-blob kh-blob-2"></div>
            <div class="kh-blob kh-blob-3 d-none d-lg-block"></div>

            <div class="container position-relative text-center" style="z-index: 1;">
                <div class="row justify-content-center">
                    <div class="col-lg-7" data-aos="fade-up" data-aos-duration="800">
                        <div class="hero-badge mb-4 animate-fade-in-up" style="display: inline-flex;">
                            <i class="ri-building-line text-kh"></i>
                            <span class="small fw-semibold">Tentang Kami</span>
                        </div>
                        <h1 class="hero-title mb-4 animate-fade-in-up delay-100">
                            Mewujudkan Sertifikasi Halal<br>
                            <span class="highlight">untuk Semua UMKM</span>
                        </h1>
                        <p class="hero-lead lead mb-0 animate-fade-in-up delay-200"
                            style="max-width: 580px; margin: 0 auto;">
                            Kami adalah tim profesional yang berkomitmen membantu pelaku UMKM mendapatkan sertifikat
                            halal dengan mudah, cepat, dan terjangkau.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== TENTANG KAMI ========== -->
        <section class="section">
            <div class="container">
                <div class="row align-items-center g-5">
                    <!-- Image Side -->
                    <div class="col-lg-6" data-aos="fade-right" data-aos-duration="800">
                        <div class="position-relative">
                            <div
                                style="border-radius: var(--kh-radius-lg); overflow: hidden; box-shadow: var(--kh-shadow-lg);">
                                <img src="{{ asset('assets/images/about.jpg') }}" alt="Tim Kawulo Halal"
                                    class="img-fluid w-100">
                            </div>
                            <!-- Float badge -->
                            <div class="hero-float-badge d-none d-lg-block animate-float"
                                style="position: absolute; bottom: -20px; right: -20px; background: var(--kh-gradient); border: none; animation-delay: 1s;">
                                <div class="row g-3 text-center text-white" style="min-width: 240px;">
                                    <div class="col-4 border-end border-white border-opacity-25">
                                        <div class="fw-bold fs-5">5+</div>
                                        <small class="opacity-75" style="color: rgba(255,255,255,0.8);">Tahun</small>
                                    </div>
                                    <div class="col-4 border-end border-white border-opacity-25">
                                        <div class="fw-bold fs-5">5000+</div>
                                        <small class="opacity-75" style="color: rgba(255,255,255,0.8);">UMKM</small>
                                    </div>
                                    <div class="col-4">
                                        <div class="fw-bold fs-5">98%</div>
                                        <small class="opacity-75"
                                            style="color: rgba(255,255,255,0.8);">Kepuasan</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Side -->
                    <div class="col-lg-6" data-aos="fade-left" data-aos-duration="800" data-aos-delay="100">
                        <span class="badge bg-kh-soft text-kh px-3 py-2 mb-3">
                            <i class="ri-heart-line me-1 align-bottom"></i> Siapa Kami
                        </span>
                        <h2 class="fw-bold mb-4" style="font-size: 2rem; line-height: 1.3;">
                            Misi Kami adalah<br>
                            <span class="text-kh">Melayani dengan Sepenuh Hati</span>
                        </h2>
                        <p class="text-muted mb-4" style="line-height: 1.8; font-size: 1.05rem;">
                            Kawulo Halal adalah layanan sertifikasi halal yang diinisiasi oleh
                            <strong>Yayasan Permata Bakti Pertiwi</strong>. Kami hadir untuk membantu pelaku UMKM
                            di Indonesia mendapatkan sertifikat halal secara mudah, cepat, dan terjangkau.
                        </p>
                        <p class="text-muted mb-5" style="line-height: 1.8; font-size: 1.05rem;">
                            Dengan pengalaman lebih dari 5 tahun dan tim profesional yang tersebar di berbagai daerah,
                            kami telah berhasil membantu ribuan pelaku UMKM mendapatkan legitimasi halal untuk produk
                            mereka.
                        </p>

                        <!-- Value Cards menggunakan benefit-card style -->
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="benefit-card" style="padding: 1.25rem;">
                                    <div class="benefit-icon bg-kh-soft"
                                        style="margin-bottom: 0.75rem; width: 48px; height: 48px;">
                                        <i class="ri-heart-line text-kh"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">Integritas</h6>
                                    <p class="text-muted small mb-0">Jujur & transparan dalam setiap proses</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="benefit-card" style="padding: 1.25rem;">
                                    <div class="benefit-icon bg-kh-soft"
                                        style="margin-bottom: 0.75rem; width: 48px; height: 48px;">
                                        <i class="ri-medal-line text-kh"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">Profesional</h6>
                                    <p class="text-muted small mb-0">Tim bersertifikat & berpengalaman</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="benefit-card" style="padding: 1.25rem;">
                                    <div class="benefit-icon bg-kh-soft"
                                        style="margin-bottom: 0.75rem; width: 48px; height: 48px;">
                                        <i class="ri-hand-heart-line text-kh"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">Pelayanan</h6>
                                    <p class="text-muted small mb-0">Klien adalah prioritas utama kami</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="benefit-card" style="padding: 1.25rem;">
                                    <div class="benefit-icon bg-kh-soft"
                                        style="margin-bottom: 0.75rem; width: 48px; height: 48px;">
                                        <i class="ri-speed-line text-kh"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">Efisiensi</h6>
                                    <p class="text-muted small mb-0">Proses cepat dan mudah dipahami</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== STATS ========== -->
        <section class="stats-section py-5" id="stats">
            <div class="container">
                <div class="row g-0 text-center">
                    <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="0">
                        <div class="stat-card">
                            <div class="stat-number" data-target="5000">0</div>
                            <div class="stat-label">UMKM Tersertifikasi</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="100">
                        <div class="stat-card">
                            <div class="stat-number" data-target="50">0</div>
                            <div class="stat-label">Pendamping Halal</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="200">
                        <div class="stat-card">
                            <div class="stat-number" data-target="30">0</div>
                            <div class="stat-label">Provinsi Coverage</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="300">
                        <div class="stat-card">
                            <div class="stat-number" data-target="5">0</div>
                            <div class="stat-label">Tahun Pengalaman</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== VISI & MISI ========== -->
        <section class="section">
            <div class="container">
                <div class="row justify-content-center mb-5" data-aos="fade-up">
                    <div class="col-lg-7 text-center">
                        <span class="badge bg-kh-soft text-kh px-3 py-2 mb-3">
                            <i class="ri-flag-line me-1 align-bottom"></i> Visi & Misi
                        </span>
                        <h2 class="fw-bold mb-3">Membangun Ekosistem <span class="text-kh">Halal</span></h2>
                        <p class="text-muted">Komitmen kami untuk mewujudkan ekosistem halal yang inklusif dan
                            berkelanjutan.</p>
                    </div>
                </div>
                <div class="row g-4">
                    <!-- Visi -->
                    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="0">
                        <div class="benefit-card h-100" style="border-top: 4px solid var(--kh-secondary);">
                            <div class="benefit-icon bg-kh-soft"
                                style="background: rgba(25, 180, 160, 0.1) !important;">
                                <i class="ri-eye-line" style="color: var(--kh-secondary) !important;"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Visi</h4>
                            <p class="text-muted mb-0" style="line-height: 1.8;">
                                Menjadikan Kawulo Halal sebagai <strong>partner utama</strong> sertifikasi halal bagi
                                UMKM Indonesia, terwujudnya ekosistem halal yang inklusif dan berkelanjutan untuk
                                seluruh pelaku usaha.
                            </p>
                        </div>
                    </div>
                    <!-- Misi -->
                    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="benefit-card h-100" style="border-top: 4px solid var(--kh-primary);">
                            <div class="benefit-icon bg-kh-soft">
                                <i class="ri-flag-line text-kh"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Misi</h4>
                            <ul class="list-unstyled vstack gap-3 mb-0">
                                <li class="d-flex align-items-start gap-2">
                                    <div
                                        class="avatar-xxs bg-success bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1">
                                        <i class="ri-check-line text-success" style="font-size: 0.7rem;"></i>
                                    </div>
                                    <span class="text-muted small">Melakukan sosialisasi dan edukasi tentang pentingnya
                                        sertifikat halal</span>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <div
                                        class="avatar-xxs bg-success bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1">
                                        <i class="ri-check-line text-success" style="font-size: 0.7rem;"></i>
                                    </div>
                                    <span class="text-muted small">Memberikan layanan sertifikasi halal yang mudah dan
                                        terjangkau</span>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <div
                                        class="avatar-xxs bg-success bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1">
                                        <i class="ri-check-line text-success" style="font-size: 0.7rem;"></i>
                                    </div>
                                    <span class="text-muted small">Membangun jaringan pendamping halal di seluruh
                                        Indonesia</span>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <div
                                        class="avatar-xxs bg-success bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1">
                                        <i class="ri-check-line text-success" style="font-size: 0.7rem;"></i>
                                    </div>
                                    <span class="text-muted small">Mendukung pertumbuhan ekonomi halal Indonesia</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== CTA BANNER ========== -->
        <section class="py-5 gradient-kh cta-section position-relative">
            <div class="container position-relative">
                <div class="row align-items-center gy-4">
                    <div class="col-sm-8">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-lg flex-shrink-0">
                                <div class="avatar-title bg-white bg-opacity-25 rounded-3">
                                    <i class="ri-shield-check-fill text-white fs-2"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-white mb-1">Siap Bergabung dengan Kami?</h4>
                                <p class="text-white text-opacity-75 mb-0">Jadilah bagian dari ekosistem halal
                                    Indonesia.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 text-sm-end">
                        <a href="{{ route('publik.contact') }}"
                            class="btn btn-light btn-lg fw-bold shadow shine-hover">
                            <i class="ri-edit-line align-bottom me-1"></i> Daftar Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== FAQ ========== -->
        <section class="section bg-kh-soft">
            <div class="container">
                <div class="row justify-content-center mb-5" data-aos="fade-up">
                    <div class="col-lg-7 text-center">
                        <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 mb-3">
                            <i class="ri-question-line me-1 align-bottom"></i> FAQ
                        </span>
                        <h2 class="fw-bold mb-3">Pertanyaan yang Sering <span class="text-kh">Diajukan</span></h2>
                        <p class="text-muted">Temukan jawaban dari pertanyaan umum seputar sertifikasi halal.</p>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
                        <div class="accordion" id="faqAccordion">

                            <div class="accordion-item border-0 rounded-3 mb-3 shadow-sm">
                                <h2 class="accordion-header">
                                    <button class="accordion-button fw-semibold rounded-3" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faq1"
                                        style="color: var(--kh-dark);">
                                        Berapa lama proses sertifikasi halal?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse show"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted" style="line-height: 1.8;">
                                        Proses sertifikasi halal untuk produk low-risk umumnya memakan waktu sekitar 30
                                        hari kerja sejak dokumen lengkap diajukan. Dengan bantuan tim Pendamping Halal
                                        Kawulo Halal, proses ini bisa berjalan lebih cepat dan efisien.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-0 rounded-3 mb-3 shadow-sm">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-semibold rounded-3" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faq2"
                                        style="color: var(--kh-dark);">
                                        Apa saja dokumen yang diperlukan?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted" style="line-height: 1.8;">
                                        Dokumen yang diperlukan meliputi: NIB (Nomor Induk Berusaha), daftar produk,
                                        bahan baku, proses produksi, layout tempat produksi, serta sertifikat Penyelia
                                        Halal dari pelatihan yang kami sediakan.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-0 rounded-3 mb-3 shadow-sm">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-semibold rounded-3" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faq3"
                                        style="color: var(--kh-dark);">
                                        Berapa biaya sertifikasi halal?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted" style="line-height: 1.8;">
                                        Biaya bervariasi tergantung jenis produk dan kompleksitas. Untuk produk
                                        low-risk, kami menawarkan skema pembayaran fleksibel dan transparan. Hubungi
                                        tim kami untuk konsultasi gratis dan penawaran harga.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-0 rounded-3 mb-3 shadow-sm">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-semibold rounded-3" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faq4"
                                        style="color: var(--kh-dark);">
                                        Apakah ada pelatihan untuk Penyelia Halal?
                                    </button>
                                </h2>
                                <div id="faq4" class="accordion-collapse collapse"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted" style="line-height: 1.8;">
                                        Ya, kami menyediakan pelatihan Penyelia Halal yang diakui BPJPH. Pelatihan ini
                                        termasuk dalam paket layanan sertifikasi halal kami dan sertifikatnya berlaku
                                        untuk multiple produk.
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== CTA BOTTOM ========== -->
        <section class="py-5 gradient-kh-dark cta-section position-relative">
            <div class="container position-relative text-center">
                <i class="ri-hand-coin-line text-white opacity-25" style="font-size: 5rem;"></i>
                <h3 class="text-white fw-bold mt-3 mb-2">Punya Pertanyaan?</h3>
                <p class="text-white text-opacity-75 mb-4">Tim kami siap membantu Anda 24/7</p>
                <a href="{{ route('publik.contact') }}" class="btn btn-light btn-lg fw-bold px-5 shadow shine-hover">
                    <i class="ri-message-3-line align-bottom me-1"></i> Hubungi Kami
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
