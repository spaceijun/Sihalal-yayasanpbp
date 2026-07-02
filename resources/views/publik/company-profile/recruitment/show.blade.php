<!DOCTYPE html>
<html lang="id" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<head>
    <meta charset="utf-8" />
    <title>{{ $post->nama_loker ?? 'Lowongan' }} - Kawulo Halal</title>
    <meta name="description"
        content="{{ Str::limit($post->deskripsi ?? ($post->nama_loker ?? 'Lowongan Kerja'), 160) }}">
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
        /* Spesifik halaman detail lowongan */
        .job-header-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border-radius: var(--kh-radius-lg);
            border: 1px solid rgba(15, 44, 89, 0.06);
            padding: 2rem;
            box-shadow: var(--kh-shadow);
            margin-bottom: 1.5rem;
            position: relative;
            overflow: hidden;
            transition: var(--kh-transition);
        }

        .job-header-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--kh-gradient);
        }

        .job-header-card:hover {
            border-color: rgba(15, 44, 89, 0.12);
            box-shadow: var(--kh-shadow-lg);
        }

        .job-icon-wrapper-lg {
            width: 72px;
            height: 72px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--kh-sky);
            color: var(--kh-primary);
            font-size: 2.2rem;
            transition: var(--kh-transition);
        }

        .job-header-card:hover .job-icon-wrapper-lg {
            background: var(--kh-primary);
            color: #fff;
            transform: rotate(6deg) scale(1.05);
        }

        .content-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(10px);
            border-radius: var(--kh-radius-lg);
            border: 1px solid rgba(15, 44, 89, 0.06);
            padding: 2rem;
            box-shadow: var(--kh-shadow-sm);
            margin-bottom: 1.5rem;
            transition: var(--kh-transition);
        }

        .content-card:hover {
            box-shadow: var(--kh-shadow);
            border-color: rgba(15, 44, 89, 0.12);
        }

        .content-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--kh-dark);
            margin-bottom: 1.25rem;
        }

        .content-icon {
            width: 44px;
            height: 44px;
            border-radius: var(--kh-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: var(--kh-sky);
            color: var(--kh-primary);
            font-size: 1.2rem;
        }

        .req-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.875rem 1.25rem;
            border-radius: var(--kh-radius);
            border: 1px solid rgba(15, 44, 89, 0.04);
            background: rgba(15, 44, 89, 0.02);
            margin-bottom: 0.75rem;
            transition: var(--kh-transition);
        }

        .req-item:hover {
            background: var(--kh-sky);
            border-color: rgba(15, 44, 89, 0.1);
            transform: translateX(5px);
        }

        .req-check {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(15, 44, 89, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .cta-box {
            background: var(--kh-gradient);
            border-radius: var(--kh-radius-lg);
            padding: 2.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: var(--kh-shadow-lg);
        }

        .cta-box::before {
            content: '';
            position: absolute;
            top: -40%;
            right: -20%;
            width: 60%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 60%);
        }

        .cta-box>* {
            position: relative;
            z-index: 1;
        }

        @media (max-width: 767.98px) {

            .content-card,
            .job-header-card {
                padding: 1.5rem;
            }

            .text-muted.lh-lg {
                padding-left: 0 !important;
                margin-top: 1rem;
            }

            .job-details-wrapper {
                padding-left: 0 !important;
                margin-top: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="layout-wrapper landing">

        @include('publik.company-profile.partials.navbar')

        <!-- ========== BREADCRUMB ========== -->
        <div class="bg-kh-soft border-bottom" style="border-color: rgba(15,44,89,0.06) !important; margin-top: 70px;">
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
                        <li class="breadcrumb-item active text-muted" aria-current="page">
                            {{ Str::limit($post->nama_loker ?? 'Lowongan', 40) }}
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- ========== CONTENT ========== -->
        <section class="section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-9">

                        {{-- Job Header Card --}}
                        <div class="job-header-card" data-aos="fade-up">
                            <div class="row align-items-center gy-3">
                                <div class="col-lg-8">
                                    <div class="d-flex gap-2 mb-3 flex-wrap">
                                        @if ($post->is_active)
                                            <span class="job-badge bg-success bg-opacity-10 text-success">
                                                <i class="ri-checkbox-circle-line me-1"></i>Lowongan Terbuka
                                            </span>
                                        @else
                                            <span
                                                class="job-badge bg-secondary bg-opacity-10 text-secondary">Ditutup</span>
                                        @endif
                                        @if ($post->posisi)
                                            <span class="job-badge bg-kh-soft text-kh">{{ $post->posisi }}</span>
                                        @endif
                                    </div>
                                    <h1 class="fw-bold mb-3"
                                        style="font-size: 1.85rem; color: var(--kh-dark); line-height: 1.25;">
                                        {{ $post->nama_loker }}
                                    </h1>
                                    <ul class="list-inline text-muted small mb-0">
                                        @if ($post->tanggal_buka)
                                            <li class="list-inline-item me-3">
                                                <i class="ri-calendar-line me-1 text-success align-bottom"></i>
                                                Buka: {{ $post->tanggal_buka->format('d M Y') }}
                                            </li>
                                        @endif
                                        @if ($post->tanggal_tutup)
                                            <li class="list-inline-item text-danger">
                                                <i class="ri-calendar-close-line me-1 align-bottom"></i>
                                                Tutup: {{ $post->tanggal_tutup->format('d M Y') }}
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                                <div class="col-lg-4 text-lg-end">
                                    <div class="job-icon-wrapper-lg ms-lg-auto">
                                        <i class="ri-briefcase-4-line"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        @if ($post->deskripsi)
                            <div class="content-card" data-aos="fade-up" data-aos-delay="50">
                                <h3 class="content-title mb-3">
                                    <div class="content-icon">
                                        <i class="ri-file-text-line"></i>
                                    </div>
                                    Deskripsi Pekerjaan
                                </h3>
                                <div class="text-muted lh-lg job-details-wrapper"
                                    style="padding-left: 56px; font-size: 0.95rem;">
                                    {!! nl2br(e($post->deskripsi)) !!}
                                </div>
                            </div>
                        @endif

                        {{-- Job Desk --}}
                        @if ($post->jobdesk)
                            <div class="content-card" data-aos="fade-up" data-aos-delay="100">
                                <h3 class="content-title mb-3">
                                    <div class="content-icon">
                                        <i class="ri-briefcase-line"></i>
                                    </div>
                                    Job Desk
                                </h3>
                                <div class="text-muted lh-lg job-details-wrapper"
                                    style="padding-left: 56px; font-size: 0.95rem;">
                                    {!! nl2br(e($post->jobdesk)) !!}
                                </div>
                            </div>
                        @endif

                        {{-- Persyaratan --}}
                        @if ($post->requirements && is_array($post->requirements) && count($post->requirements))
                            <div class="content-card" data-aos="fade-up" data-aos-delay="150">
                                <h3 class="content-title mb-3">
                                    <div class="content-icon">
                                        <i class="ri-checkbox-circle-line"></i>
                                    </div>
                                    Persyaratan
                                </h3>
                                <div class="job-details-wrapper" style="padding-left: 56px;">
                                    @foreach ($post->requirements as $req)
                                        <div class="req-item">
                                            <div class="req-check">
                                                <i class="ri-check-line text-kh" style="font-size: 0.8rem;"></i>
                                            </div>
                                            <span
                                                class="text-muted small">{{ $req['label'] ?? ($req['field_key'] ?? '') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Pakta Integritas --}}
                        @if ($post->template_pakta_integritas)
                            <div class="content-card" data-aos="fade-up" data-aos-delay="200">
                                <h3 class="content-title mb-3">
                                    <div class="content-icon">
                                        <i class="ri-file-paper-2-line"></i>
                                    </div>
                                    Pakta Integritas
                                </h3>
                                <div class="job-details-wrapper" style="padding-left: 56px;">
                                    <a href="{{ Storage::url($post->template_pakta_integritas) }}" target="_blank"
                                        class="btn btn-kh-outline px-4 py-2">
                                        <i class="ri-file-download-line me-1 align-bottom"></i>
                                        Download Pakta Integritas
                                    </a>
                                </div>
                            </div>
                        @endif

                        {{-- CTA Lamar --}}
                        @if ($post->isOpen())
                            <div class="cta-box" data-aos="fade-up" data-aos-delay="250">
                                <i class="ri-user-add-line text-white opacity-50 mb-3 d-block"
                                    style="font-size: 3rem;"></i>
                                <h4 class="text-white fw-bold mb-2">Tertarik dengan posisi ini?</h4>
                                <p class="text-white text-opacity-75 mb-4">Klik tombol di bawah untuk melamar
                                    langsung. Pastikan data yang Anda isi sudah lengkap dan benar.</p>
                                <a href="{{ route('recruitment.form', $post->slug) }}"
                                    class="btn btn-light btn-lg fw-bold px-5 shadow shine-hover">
                                    <i class="ri-send-plane-line me-1 align-bottom"></i> Lamar Sekarang
                                </a>
                            </div>
                        @endif

                        {{-- Back link --}}
                        <div class="text-center mt-4" data-aos="fade-up">
                            <a href="{{ route('publik.recruitment.index') }}" class="btn btn-kh-outline">
                                <i class="ri-arrow-left-line me-1 align-bottom"></i> Lihat Semua Lowongan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== CTA BOTTOM ========== -->
        <section class="py-5 position-relative" style="background: var(--kh-gradient-dark); overflow: hidden;">
            <div class="container position-relative text-center py-4" style="z-index: 2;">
                <span
                    style="width:72px;height:72px;border-radius:50%;background:rgba(255,255,255,0.05);display:inline-flex;align-items:center;justify-content:center;margin-bottom:1.5rem;color:rgba(255,255,255,0.7);">
                    <i class="ri-chat-3-line" style="font-size: 2.2rem;"></i>
                </span>
                <h3 class="text-white fw-bold mb-2">Punya Pertanyaan Lain?</h3>
                <p class="text-white text-opacity-75 mb-4"
                    style="max-width:480px; margin:0 auto; font-size: 0.95rem;">Tim kami siap membantu dan memberikan
                    informasi yang Anda butuhkan.</p>
                <a href="{{ route('publik.contact') }}"
                    class="btn btn-light btn-lg fw-bold px-5 py-3 shadow shine-hover">
                    <i class="ri-message-2-line align-middle me-2"></i> Hubungi Kami
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
