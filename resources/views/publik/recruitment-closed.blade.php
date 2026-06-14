<!DOCTYPE html>
<html lang="id" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<head>
    <meta charset="utf-8" />
    <title>{{ $post->nama_loker ?? 'Lowongan' }} – Pendaftaran Ditutup - Kawulo Halal</title>
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
        /* ── Page-specific styles (closed only) ── */

        /* Hero — selaras hero-contact & hero-rc */
        .hero-closed {
            background: linear-gradient(180deg, #EAF4FF 0%, #F4FAFF 50%, #FFFFFF 100%);
            padding-top: 9rem;
            padding-bottom: 4rem;
            position: relative;
            overflow: hidden;
        }

        .hero-closed .kh-blob {
            opacity: 0.45;
        }

        .hero-closed-title {
            font-size: clamp(1.75rem, 4vw, 3rem);
            font-weight: 800;
            line-height: 1.15;
            color: var(--kh-dark);
        }

        .hero-closed-title .highlight {
            background: var(--kh-gradient-text);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ── Closed card — selaras .form-card di contact ── */
        .closed-card {
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

        .closed-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--kh-gradient-soft);
            opacity: 0;
            transition: opacity 0.4s ease;
            pointer-events: none;
        }

        .closed-card:hover::before {
            opacity: 1;
        }

        .closed-card>* {
            position: relative;
            z-index: 1;
        }

        /* Icon circle — selaras .contact-icon */
        .closed-icon {
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

        .closed-icon i {
            font-size: 2.25rem;
            color: var(--kh-primary);
            transition: var(--kh-transition);
        }

        .closed-card:hover .closed-icon {
            background: var(--kh-gradient);
            transform: scale(1.1) rotate(6deg);
        }

        .closed-card:hover .closed-icon i {
            color: #fff !important;
        }

        /* Info items list */
        .closed-info-list {
            list-style: none;
            padding: 0;
            margin: 0 0 1.75rem;
            text-align: left;
        }

        .closed-info-list li {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid var(--kh-sky);
            font-size: 13.5px;
            color: var(--kh-text);
        }

        .closed-info-list li:last-child {
            border-bottom: none;
        }

        .closed-info-list li i {
            font-size: 1.05rem;
            color: var(--kh-primary);
            flex-shrink: 0;
            width: 20px;
        }

        .closed-info-list li span {
            color: var(--kh-text-light);
            font-size: 12.5px;
            margin-left: auto;
        }

        /* Other jobs section */
        .other-jobs-section {
            background: var(--kh-sky-2);
        }

        /* Job cards selaras .job-card di home */
        .job-card-sm {
            background: #fff;
            border-radius: var(--kh-radius);
            padding: 1.5rem;
            border: 1px solid #EAF1FB;
            transition: var(--kh-transition);
            height: 100%;
        }

        .job-card-sm:hover {
            transform: translateY(-6px);
            box-shadow: var(--kh-shadow-lg);
            border-color: var(--kh-primary-light);
        }

        .job-badge-sm {
            border-radius: var(--kh-radius-pill);
            font-size: 0.72rem;
            font-weight: 600;
            padding: 0.2rem 0.65rem;
        }

        @media (max-width: 768px) {
            .hero-closed {
                padding-top: 7rem;
                padding-bottom: 3rem;
            }

            .closed-card {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="layout-wrapper landing">

        @include('publik.company-profile.partials.navbar')

        <!-- ========== HERO — selaras hero-contact / hero-rc ========== -->
        <section class="hero-closed" id="hero">
            <div class="kh-blob kh-blob-1"></div>
            <div class="kh-blob kh-blob-2"></div>

            <div class="container position-relative text-center" style="z-index: 1;">
                <div class="row justify-content-center">
                    <div class="col-lg-7">
                        <div class="hero-badge mb-4 animate-fade-in-up" style="margin: 0 auto 1.5rem;">
                            <i class="ri-briefcase-line text-kh"></i>
                            <span class="small fw-semibold">Informasi Lowongan</span>
                        </div>
                        <h1 class="hero-closed-title mb-4 animate-fade-in-up delay-100">
                            Pendaftaran<br>
                            <span class="highlight">Telah Ditutup</span>
                        </h1>
                        <p class="hero-lead lead mb-0 animate-fade-in-up delay-200"
                            style="max-width: 480px; margin: 0 auto;">
                            Lowongan <strong>{{ $post->nama_loker }}</strong> sudah tidak menerima pendaftaran baru
                            saat ini.
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
                        <li class="breadcrumb-item">
                            <a href="{{ route('publik.recruitment.show', $post->slug) }}"
                                class="text-decoration-none text-kh">{{ $post->nama_loker }}</a>
                        </li>
                        <li class="breadcrumb-item active text-muted" aria-current="page">Pendaftaran Ditutup</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- ========== MAIN CONTENT ========== -->
        <section class="section" id="closed-info">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-5 col-md-7" data-aos="fade-up">
                        <div class="closed-card">

                            {{-- Icon --}}
                            <div class="closed-icon">
                                <i class="ri-lock-line"></i>
                            </div>

                            {{-- Badge posisi --}}
                            @if ($post->posisi)
                                <span class="badge bg-kh-soft text-kh px-3 py-2 mb-3 fw-semibold"
                                    style="border-radius: var(--kh-radius-pill); font-size: 12px;">
                                    {{ $post->posisi }}
                                </span>
                            @endif

                            {{-- Judul --}}
                            <h4 class="fw-bold mb-2" style="color: var(--kh-dark);">Pendaftaran Ditutup</h4>
                            <p class="text-muted mb-4" style="font-size: 14px; line-height: 1.7;">
                                Mohon maaf, lowongan <strong>{{ $post->nama_loker }}</strong> sudah tidak menerima
                                pendaftaran baru saat ini.
                            </p>

                            {{-- Info list --}}
                            <ul class="closed-info-list">
                                <li>
                                    <i class="ri-briefcase-4-line"></i>
                                    <strong>Posisi</strong>
                                    <span>{{ $post->posisi ?? '-' }}</span>
                                </li>
                                @if ($post->tanggal_tutup && $post->tanggal_tutup->isPast())
                                    <li>
                                        <i class="ri-calendar-close-line"></i>
                                        <strong>Ditutup pada</strong>
                                        <span>{{ $post->tanggal_tutup->format('d M Y') }}</span>
                                    </li>
                                @elseif (!$post->is_active)
                                    <li>
                                        <i class="ri-information-line"></i>
                                        <strong>Status</strong>
                                        <span>Lowongan tidak aktif</span>
                                    </li>
                                @endif
                                <li>
                                    <i class="ri-map-pin-line"></i>
                                    <strong>Lokasi</strong>
                                    <span>{{ $post->lokasi ?? 'Indonesia' }}</span>
                                </li>
                            </ul>

                            {{-- Actions --}}
                            <a href="{{ route('publik.recruitment.index') }}"
                                class="btn btn-kh w-100 py-3 fw-bold mb-3">
                                <i class="ri-arrow-left-line align-middle me-1"></i> Lihat Lowongan Lain
                            </a>
                            <a href="{{ route('publik.contact') }}" class="btn btn-kh-outline w-100 py-3 fw-bold">
                                <i class="ri-message-3-line align-middle me-1"></i> Hubungi Kami
                            </a>

                        </div>

                        <p class="text-center text-muted small mt-4">
                            Ingin tahu posisi lain yang tersedia?
                            <a href="{{ route('publik.recruitment.index') }}"
                                class="text-kh fw-semibold text-decoration-none">Cek di sini</a>
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== LOWONGAN LAIN (jika ada) ========== -->
        @if (!empty($otherPosts) && $otherPosts->isNotEmpty())
            <section class="section other-jobs-section" id="lowongan-lain">
                <div class="container">
                    <div class="row justify-content-center mb-5" data-aos="fade-up">
                        <div class="col-lg-7 text-center">
                            <span class="badge bg-white text-kh px-3 py-2 mb-3 shadow-sm">
                                <i class="ri-briefcase-line me-1 align-bottom"></i> Karir
                            </span>
                            <h2 class="fw-bold mb-2">Lowongan <span class="text-kh">Tersedia</span></h2>
                            <p class="text-muted">Masih ada kesempatan lain yang menunggu Anda.</p>
                        </div>
                    </div>
                    <div class="row g-4 justify-content-center">
                        @foreach ($otherPosts as $other)
                            <div class="col-lg-4 col-md-6" data-aos="fade-up"
                                data-aos-delay="{{ $loop->index * 100 }}">
                                <div class="job-card-sm h-100">
                                    <div class="d-flex align-items-start justify-content-between mb-3">
                                        <div>
                                            @if ($other->isOpen())
                                                <span
                                                    class="job-badge-sm bg-success bg-opacity-10 text-success mb-2 d-inline-block">
                                                    <i class="ri-checkbox-circle-line me-1"></i>Terbuka
                                                </span>
                                            @endif
                                            @if ($other->posisi)
                                                <span class="job-badge-sm bg-kh-soft text-kh mb-2 d-inline-block ms-1">
                                                    {{ $other->posisi }}
                                                </span>
                                            @endif
                                        </div>
                                        <div
                                            class="avatar-lg bg-kh-soft rounded-3 d-flex align-items-center justify-content-center">
                                            <i class="ri-briefcase-4-line text-kh fs-5"></i>
                                        </div>
                                    </div>
                                    <h5 class="fw-bold mb-2" style="color: var(--kh-dark);">{{ $other->nama_loker }}
                                    </h5>
                                    <p class="text-muted small mb-3">{{ Str::limit($other->deskripsi, 100) }}</p>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('publik.recruitment.show', $other->slug) }}"
                                            class="btn btn-kh btn-sm flex-grow-1">
                                            <i class="ri-eye-line me-1 align-bottom"></i> Lihat Detail
                                        </a>
                                        @if ($other->isOpen())
                                            <a href="{{ route('recruitment.form', $other->slug) }}"
                                                class="btn btn-kh-outline btn-sm">
                                                <i class="ri-send-plane-line align-bottom"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- ========== CTA — selaras contact & recruitment-form ========== -->
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
