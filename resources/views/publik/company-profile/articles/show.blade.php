<!DOCTYPE html>
<html lang="id" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">
<head>
    <meta charset="utf-8" />
    <title>{{ $article->title }} - Kawulo Halal</title>
    <meta name="description" content="{{ $article->excerpt ?? Str::limit(strip_tags($article->content), 160) }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/public-pages.css') }}" rel="stylesheet" type="text/css" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --kh-primary: #1A5FC8;
            --kh-primary-dark: #1040A0;
            --kh-secondary: #1D9E75;
            --kh-accent: #7DD3C8;
            --kh-dark: #0f172a;
            --kh-light: #f8fafc;
            --kh-text: #334155;
            --kh-text-light: #64748b;
            --kh-gradient: linear-gradient(135deg, #1A5FC8 0%, #1D9E75 100%);
            --kh-gradient-dark: linear-gradient(135deg, #1040A0 0%, #167a5b 100%);
            --kh-shadow: 0 4px 20px rgba(26, 95, 200, 0.15);
            --kh-shadow-lg: 0 20px 40px rgba(26, 95, 200, 0.2);
            --kh-radius: 16px;
            --kh-radius-lg: 24px;
        }

        * { font-family: 'Plus Jakarta Sans', sans-serif; }

        .text-kh { color: var(--kh-primary) !important; }
        .bg-kh { background-color: var(--kh-primary) !important; }
        .bg-kh-gradient { background: var(--kh-gradient) !important; }

        .btn-kh {
            background: var(--kh-gradient);
            border: none;
            color: #fff;
            padding: 0.75rem 1.75rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: var(--kh-shadow);
        }
        .btn-kh:hover {
            background: var(--kh-gradient-dark);
            transform: translateY(-2px);
            box-shadow: var(--kh-shadow-lg);
            color: #fff;
        }
        .btn-kh-outline {
            background: transparent;
            border: 2px solid var(--kh-primary);
            color: var(--kh-primary);
            padding: 0.75rem 1.75rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-kh-outline:hover {
            background: var(--kh-primary);
            color: #fff;
            transform: translateY(-2px);
        }

        /* ========== NAVBAR ========== */
        .navbar-kh {
            backdrop-filter: blur(20px);
            background: rgba(15, 23, 42, 0.85);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .navbar-kh.scrolled {
            background: rgba(15, 23, 42, 0.98);
            box-shadow: 0 4px 30px rgba(0,0,0,0.2);
        }
        .navbar-kh .nav-link {
            color: rgba(255,255,255,0.75) !important;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .navbar-kh .nav-link:hover,
        .navbar-kh .nav-link.active {
            color: #fff !important;
            background: rgba(5, 150, 105, 0.15);
        }
        .navbar-kh .nav-link.active {
            color: var(--kh-primary) !important;
        }

        /* ========== ARTICLE CONTENT ========== */
        .article-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(5, 150, 105, 0.1);
            color: var(--kh-primary);
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .article-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--kh-dark);
            line-height: 1.2;
        }
        .article-excerpt {
            font-size: 1.125rem;
            color: var(--kh-text-light);
            line-height: 1.7;
        }
        .article-body {
            font-size: 1.05rem;
            line-height: 1.9;
            color: var(--kh-text);
        }
        .article-body h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 2.5rem;
            margin-bottom: 1rem;
            color: var(--kh-dark);
        }
        .article-body h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-top: 2rem;
            margin-bottom: 0.75rem;
            color: var(--kh-dark);
        }
        .article-body p {
            margin-bottom: 1.25rem;
        }
        .article-body ul, .article-body ol {
            margin-bottom: 1.25rem;
            padding-left: 1.5rem;
        }
        .article-body li {
            margin-bottom: 0.5rem;
        }
        .article-body img {
            border-radius: var(--kh-radius);
            margin: 1.5rem 0;
            max-width: 100%;
        }
        .article-featured-img {
            border-radius: var(--kh-radius-lg);
            overflow: hidden;
            box-shadow: var(--kh-shadow-lg);
        }

        /* ========== AUTHOR ========== */
        .author-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.25rem;
            background: rgba(5, 150, 105, 0.05);
            border-radius: var(--kh-radius);
            border: 1px solid rgba(5, 150, 105, 0.1);
        }
        .author-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.25rem;
            color: #fff;
        }

        /* ========== SHARE ========== */
        .share-card {
            background: #fff;
            border-radius: var(--kh-radius);
            padding: 1.5rem;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .share-btn {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        .share-btn:hover {
            transform: translateY(-3px);
        }

        /* ========== RELATED ARTICLES ========== */
        .related-card {
            background: #fff;
            border-radius: var(--kh-radius-lg);
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
            transition: all 0.4s ease;
            text-decoration: none;
            display: block;
            height: 100%;
        }
        .related-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--kh-shadow-lg);
        }
        .related-card .card-img {
            height: 160px;
            object-fit: cover;
            transition: transform 0.6s ease;
        }
        .related-card:hover .card-img {
            transform: scale(1.08);
        }

        /* ========== CTA ========== */
        .cta-section {
            background: var(--kh-gradient);
            position: relative;
            overflow: hidden;
        }
        .cta-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
        }

        /* ========== FOOTER ========== */
        .footer-section {
            background: var(--kh-dark);
        }
        .footer-brand {
            font-size: 1.5rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 1rem;
        }
        .footer-desc {
            color: rgba(255,255,255,0.6);
            line-height: 1.8;
            max-width: 320px;
        }
        .footer-title {
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 1.25rem;
        }
        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .footer-links li {
            margin-bottom: 0.75rem;
        }
        .footer-links a {
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .footer-links a:hover {
            color: var(--kh-primary);
            transform: translateX(5px);
        }
        .footer-social {
            display: flex;
            gap: 12px;
        }
        .footer-social a {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        .footer-social a:hover {
            transform: translateY(-3px);
        }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 1.5rem;
            margin-top: 3rem;
        }

        /* ========== BACK TO TOP ========== */
        .back-to-top {
            background: var(--kh-gradient);
            border: none;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--kh-shadow);
            transition: all 0.3s ease;
        }
        .back-to-top:hover {
            transform: translateY(-5px);
            box-shadow: var(--kh-shadow-lg);
        }

        /* ========== SCROLL REVEAL ========== */
        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 767px) {
            .article-title { font-size: 1.75rem; }
        }
    </style>
</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">
    <div class="layout-wrapper landing">

        @include('publik.company-profile.partials.navbar')

        <!-- ========== ARTICLE HEADER ========== -->
        <section class="section mt-5 pt-0">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <!-- Breadcrumb -->
                        <nav aria-label="breadcrumb" class="mb-4 reveal">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('publik.home') }}" class="text-decoration-none">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('publik.articles.index') }}" class="text-decoration-none">Artikel</a></li>
                                <li class="breadcrumb-item active text-muted" aria-current="page">{{ Str::limit($article->title, 30) }}</li>
                            </ol>
                        </nav>

                        <!-- Category & Meta -->
                        <div class="d-flex align-items-center gap-3 mb-4 flex-wrap reveal">
                            <span class="article-badge">{{ ucfirst($article->category) }}</span>
                            <span class="text-muted"><i class="ri-calendar-line me-1"></i>{{ $article->published_at?->format('d M Y') ?? $article->created_at->format('d M Y') }}</span>
                            <span class="text-muted"><i class="ri-time-line me-1"></i>{{ $article->reading_time ?? 3 }} min read</span>
                        </div>

                        <!-- Title -->
                        <h1 class="article-title mb-4 reveal">{{ $article->title }}</h1>

                        <!-- Excerpt -->
                        @if($article->excerpt)
                        <p class="article-excerpt mb-4 reveal">{{ $article->excerpt }}</p>
                        @endif

                        <!-- Author -->
                        @if($article->author)
                        <div class="author-card reveal">
                            <div class="author-avatar bg-kh-gradient">
                                {{ substr($article->author, 0, 1) }}
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold">{{ $article->author }}</h6>
                                <p class="text-muted mb-0 small">Penulis</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== FEATURED IMAGE ========== -->
        @if($article->image)
        <section class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="article-featured-img reveal">
                        <img src="{{ Storage::url($article->image) }}" alt="{{ $article->title }}" class="img-fluid w-100" style="max-height: 450px; object-fit: cover;">
                    </div>
                </div>
            </div>
        </section>
        @endif

        <!-- ========== CONTENT ========== -->
        <section class="section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="article-body reveal">
                            {!! nl2br(e($article->content)) !!}
                        </div>

                        <!-- Share -->
                        <div class="share-card mt-5 reveal">
                            <p class="fw-semibold mb-3"><i class="ri-share-line me-1 align-bottom"></i> Bagikan artikel ini:</p>
                            <div class="d-flex gap-2">
                                <a href="https://wa.me/?text={{ urlencode($article->title . ' - ' . url()->current()) }}" target="_blank" class="share-btn btn btn-soft-success">
                                    <i class="ri-whatsapp-fill fs-5"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?text={{ urlencode($article->title) }}&url={{ urlencode(url()->current()) }}" target="_blank" class="share-btn btn btn-soft-dark">
                                    <i class="ri-twitter-x-fill fs-5"></i>
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="share-btn btn btn-soft-primary">
                                    <i class="ri-facebook-fill fs-5"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== RELATED ARTICLES ========== -->
        @if($relatedArticles->isNotEmpty())
        <section class="section" style="background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <h4 class="fw-bold mb-4 reveal"><i class="ri-bookmark-line me-2 text-kh align-bottom"></i>Artikel Terkait</h4>
                        <div class="row g-4">
                            @foreach($relatedArticles as $related)
                            <div class="col-lg-4 col-md-6 reveal">
                                <a href="{{ route('publik.articles.show', $related->slug) }}" class="related-card">
                                    @if($related->image)
                                    <div class="overflow-hidden">
                                        <img src="{{ Storage::url($related->image) }}" alt="{{ $related->title }}" class="card-img w-100">
                                    </div>
                                    @else
                                    <div class="bg-kh-gradient d-flex align-items-center justify-content-center" style="height: 160px;">
                                        <i class="ri-article-line text-white opacity-25" style="font-size: 3rem;"></i>
                                    </div>
                                    @endif
                                    <div class="card-body p-3">
                                        <span class="article-badge mb-2">{{ ucfirst($related->category) }}</span>
                                        <h6 class="fw-bold mb-2 mt-2" style="color: var(--kh-dark);">{{ Str::limit($related->title, 55) }}</h6>
                                        <small class="text-muted">{{ $related->published_at?->format('d M Y') ?? $related->created_at->format('d M Y') }}</small>
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif

        <!-- ========== CTA ========== -->
        <section class="py-5 cta-section position-relative">
            <div class="container position-relative">
                <div class="row align-items-center gy-4">
                    <div class="col-sm-8">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-xl bg-white bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0">
                                <i class="ri-shield-check-fill text-white fs-4"></i>
                            </div>
                            <div>
                                <h4 class="text-white mb-1">Ingin Mendapatkan Sertifikat Halal?</h4>
                                <p class="text-white text-opacity-75 mb-0">Daftarkan produk Anda sekarang dan dapatkan sertifikat halal dari BPJPH.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 text-sm-end">
                        <a href="{{ route('publik.contact') }}" class="btn btn-light btn-lg fw-bold shadow">
                            <i class="ri-arrow-right-line align-bottom me-1"></i> Daftar Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== FOOTER ========== -->
        <footer class="footer-section py-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 mt-4">
                        <div class="footer-brand">
                            <img src="{{ asset('assets/images/logo-light.png') }}" alt="logo light" height="28" class="mb-3">
                        </div>
                        <p class="footer-desc">Layanan sertifikasi halal terpercaya untuk UMKM Indonesia.</p>
                        <div class="footer-social mt-4">
                            @foreach ($socialMedia ?? [] as $social)
                                <a href="{{ $social->url }}" target="_blank" class="btn"
                                    style="background: {{ $social->color }}22; color: {{ $social->color }};">
                                    <i class="{{ $social->icon }}"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-lg-7 ms-lg-auto">
                        <div class="row">
                            <div class="col-sm-4 mt-4">
                                <h5 class="footer-title">Tautan</h5>
                                <ul class="footer-links">
                                    <li><a href="{{ route('publik.home') }}"><i class="ri-arrow-right-s-line"></i>Home</a></li>
                                    <li><a href="{{ route('publik.about') }}"><i class="ri-arrow-right-s-line"></i>Tentang Kami</a></li>
                                    <li><a href="{{ route('publik.articles.index') }}"><i class="ri-arrow-right-s-line"></i>Artikel</a></li>
                                    <li><a href="{{ route('publik.recruitment.index') }}"><i class="ri-arrow-right-s-line"></i>Lowongan Pekerjaan</a></li>
                                    <li><a href="{{ route('publik.contact') }}"><i class="ri-arrow-right-s-line"></i>Kontak</a></li>
                                </ul>
                            </div>
                            <div class="col-sm-4 mt-4">
                                <h5 class="footer-title">Kontak</h5>
                                <ul class="footer-links">
                                    <li><a href="#"><i class="ri-map-pin-2-line me-2"></i>Jakarta, Indonesia</a></li>
                                    <li><a href="mailto:info@kawulohalal.id"><i class="ri-mail-line me-2"></i>info@kawulohalal.id</a></li>
                                    <li><a href="tel:+6281234567890"><i class="ri-phone-line me-2"></i>+62 812-3456-7890</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row footer-bottom">
                    <div class="col-12 text-center">
                        <p class="text-muted mb-0 small" style="color: rgba(255,255,255,0.5);">&copy; {{ date('Y') }} Kawulo Halal — Yayasan Permata Bakti Pertiwi.</p>
                    </div>
                </div>
            </div>
        </footer>

        <button onclick="topFunction()" class="btn btn-icon back-to-top landing-back-top" id="back-to-top">
            <i class="ri-arrow-up-line text-white"></i>
        </button>
    </div>

    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <script src="{{ asset('assets/js/plugins.js') }}"></script>
    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) navbar.classList.add('scrolled');
            else navbar.classList.remove('scrolled');
        });
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('visible'); });
        }, { threshold: 0.1 });
        document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));
    </script>
</body>
</html>
