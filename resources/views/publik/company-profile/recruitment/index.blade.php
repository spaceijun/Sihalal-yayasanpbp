<!DOCTYPE html>
<html lang="id" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<head>
    <meta charset="utf-8" />
    <title>Lowongan Kerja - Kawulo Halal</title>
    <meta name="description"
        content="Bergabunglah dengan tim Kawulo Halal. Temukan lowongan kerja sebagai pendamping halal.">
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
    <!-- Kawulo Halal Modern Theme Css -->
    <link href="{{ asset('assets/css/compro-ui.css') }}" rel="stylesheet" type="text/css" />

    <style>
        /* Spesifik halaman recruitment */
        .job-card-kh {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border-radius: var(--kh-radius-lg);
            border: 1px solid rgba(47, 143, 230, 0.08);
            box-shadow: var(--kh-shadow-sm);
            transition: var(--kh-transition);
            height: 100%;
            overflow: hidden;
            position: relative;
        }

        .job-card-kh::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--kh-gradient);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s ease;
        }

        .job-card-kh:hover {
            transform: translateY(-8px);
            box-shadow: var(--kh-shadow-lg);
            border-color: rgba(47, 143, 230, 0.18);
        }

        .job-card-kh:hover::before {
            transform: scaleX(1);
        }

        .job-icon-wrapper {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--kh-sky);
            color: var(--kh-primary);
            font-size: 1.5rem;
            transition: var(--kh-transition);
        }

        .job-card-kh:hover .job-icon-wrapper {
            background: var(--kh-gradient);
            color: #fff;
            transform: rotate(6deg) scale(1.05);
        }

        .benefit-card-modern {
            padding: 1.25rem;
            background: #fff;
            border-radius: 20px;
            border: 1px solid rgba(47, 143, 230, 0.08);
            box-shadow: var(--kh-shadow-sm);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: var(--kh-transition);
            height: 100%;
        }

        .benefit-card-modern:hover {
            transform: translateY(-4px);
            box-shadow: var(--kh-shadow);
            border-color: rgba(47, 143, 230, 0.18);
        }

        .benefit-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: var(--kh-sky);
            color: var(--kh-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
            transition: var(--kh-transition);
        }

        .benefit-card-modern:hover .benefit-icon-wrapper {
            background: var(--kh-gradient);
            color: #fff;
        }

        .empty-state {
            border-radius: var(--kh-radius-lg);
            border: 2px dashed rgba(47, 143, 230, 0.2);
            padding: 4rem 2rem;
            background: var(--kh-sky-2);
        }
    </style>
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
                            <i class="ri-briefcase-line text-kh"></i>
                            <span class="small fw-semibold">Join Our Team</span>
                        </div>
                        <h1 class="hero-title mb-4 animate-fade-in-up delay-100">
                            Lowongan <span class="highlight">Kerja</span>
                        </h1>
                        <p class="hero-lead lead mb-0 animate-fade-in-up delay-200"
                            style="max-width: 520px; margin: 0 auto;">
                            Bergabunglah dengan tim kami untuk berkontribusi dalam ekosistem sertifikasi halal
                            Indonesia.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== BENEFIT STRIP ========== -->
        <section class="py-4 bg-kh-soft border-bottom" style="border-color: rgba(47,143,230,0.08) !important;">
            <div class="container">
                <div class="row g-3 justify-content-center">
                    <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="0">
                        <div class="benefit-card-modern">
                            <div class="benefit-icon-wrapper">
                                <i class="ri-money-dollar-circle-line"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Gaji Kompetitif</h6>
                                <p class="text-muted mb-0" style="font-size: 0.8rem;">Sesuai pengalaman</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="100">
                        <div class="benefit-card-modern">
                            <div class="benefit-icon-wrapper">
                                <i class="ri-time-line"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Fleksibel</h6>
                                <p class="text-muted mb-0" style="font-size: 0.8rem;">Waktu kerja fleksibel</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="200">
                        <div class="benefit-card-modern">
                            <div class="benefit-icon-wrapper">
                                <i class="ri-graduation-cap-line"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Pelatihan</h6>
                                <p class="text-muted mb-0" style="font-size: 0.8rem;">Sertifikasi gratis</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="300">
                        <div class="benefit-card-modern">
                            <div class="benefit-icon-wrapper">
                                <i class="ri-team-line"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Tim Solid</h6>
                                <p class="text-muted mb-0" style="font-size: 0.8rem;">Dukungan penuh</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== JOB LISTINGS ========== -->
        <section class="section">
            <div class="container">
                @if ($posts->isEmpty())
                    <div class="text-center" data-aos="fade-up">
                        <div class="empty-state d-inline-block">
                            <i class="ri-briefcase-line text-kh" style="font-size: 4rem; opacity: 0.3;"></i>
                            <h4 class="fw-bold mt-3 mb-2">Belum Ada Lowongan</h4>
                            <p class="text-muted mb-0">Lowongan baru akan segera diumumkan.<br>Stay tuned!</p>
                        </div>
                    </div>
                @else
                    <div class="row justify-content-center mb-5" data-aos="fade-up">
                        <div class="col-lg-6 text-center">
                            <span class="badge bg-kh-soft text-kh px-3 py-2 mb-3">
                                <i class="ri-briefcase-line me-1 align-bottom"></i> Karir
                            </span>
                            <h2 class="fw-bold mb-2">Posisi yang <span class="text-kh">Tersedia</span></h2>
                            <p class="text-muted">{{ $posts->count() }} posisi terbuka saat ini</p>
                        </div>
                    </div>

                    <div class="row g-4">
                        @foreach ($posts as $post)
                            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 2) * 100 }}">
                                <div class="job-card-kh">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-start justify-content-between mb-3">
                                            <div class="flex-grow-1">
                                                <div class="d-flex gap-2 mb-2 flex-wrap">
                                                    @if ($post->isOpen())
                                                        <span class="job-badge bg-success bg-opacity-10 text-success">
                                                            <i class="ri-checkbox-circle-line me-1"></i>Terbuka
                                                        </span>
                                                    @else
                                                        <span
                                                            class="job-badge bg-secondary bg-opacity-10 text-secondary">Ditutup</span>
                                                    @endif
                                                    @if ($post->posisi)
                                                        <span
                                                            class="job-badge bg-kh-soft text-kh">{{ $post->posisi }}</span>
                                                    @endif
                                                </div>
                                                <h5 class="fw-bold mb-2" style="color: var(--kh-dark);">
                                                    {{ $post->nama_loker }}</h5>
                                                <ul class="list-inline text-muted small mb-0">
                                                    @if ($post->tanggal_buka)
                                                        <li class="list-inline-item me-3">
                                                            <i
                                                                class="ri-calendar-line me-1 text-success align-bottom"></i>{{ $post->tanggal_buka->format('d M Y') }}
                                                        </li>
                                                    @endif
                                                    @if ($post->tanggal_tutup)
                                                        <li class="list-inline-item text-danger">
                                                            <i
                                                                class="ri-calendar-close-line me-1 align-bottom"></i>{{ $post->tanggal_tutup->format('d M Y') }}
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                            <div class="job-icon-wrapper ms-3">
                                                <i class="ri-briefcase-4-line"></i>
                                            </div>
                                        </div>
                                        @if ($post->deskripsi)
                                            <p class="text-muted small mb-3" style="line-height: 1.6;">
                                                {{ Str::limit($post->deskripsi, 130) }}</p>
                                        @endif
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('publik.recruitment.show', $post->slug) }}"
                                                class="btn {{ $post->isOpen() ? 'btn-kh' : 'btn-secondary' }} btn-sm flex-grow-1">
                                                <i class="ri-eye-line me-1 align-bottom"></i> Lihat Detail
                                            </a>
                                            @if ($post->isOpen())
                                                <a href="{{ route('recruitment.form', $post->slug) }}"
                                                    class="btn btn-kh-outline btn-sm">
                                                    <i class="ri-send-plane-line align-bottom"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if ($posts->hasPages())
                        <div class="mt-5 d-flex justify-content-center" data-aos="fade-up">
                            {{ $posts->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </section>

        <!-- ========== CTA BOTTOM ========== -->
        <section class="py-5 position-relative" style="background: linear-gradient(135deg, #16243B 0%, #0F1A2D 100%); overflow: hidden;">
            <div class="footer-section::after"></div>
            <div class="container position-relative text-center py-4" style="z-index: 2;">
                <span style="width:72px;height:72px;border-radius:50%;background:rgba(255,255,255,0.05);display:inline-flex;align-items:center;justify-content:center;margin-bottom:1.5rem;color:rgba(255,255,255,0.7);">
                    <i class="ri-user-add-line" style="font-size: 2.2rem;"></i>
                </span>
                <h3 class="text-white fw-bold mb-2">Tidak Menemukan Posisi yang Cocok?</h3>
                <p class="text-white text-opacity-75 mb-4" style="max-width:480px; margin:0 auto; font-size: 0.95rem;">Kirimkan CV Anda ke kami, kami akan menghubungi Anda saat ada posisi yang sesuai.</p>
                <a href="{{ route('publik.contact') }}" class="btn btn-light btn-lg fw-bold px-5 py-3 shadow shine-hover">
                    <i class="ri-mail-line align-middle me-2"></i> Hubungi Kami
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
    <script src="{{ asset('assets/js/compro.js') }}"></script>
</body>

</html>
