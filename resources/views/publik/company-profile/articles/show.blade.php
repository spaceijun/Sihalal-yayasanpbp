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
    <!-- Kawulo Halal Modern Theme Css -->
    <link href="{{ asset('assets/css/compro-ui.css') }}" rel="stylesheet" type="text/css" />

    <style>
        /* ── Page-specific styles (article detail) ── */
        .breadcrumb-item a {
            color: var(--kh-text-light);
            font-weight: 500;
            transition: var(--kh-transition);
        }

        .breadcrumb-item a:hover {
            color: var(--kh-primary);
        }

        .breadcrumb-item.active {
            color: var(--kh-text) !important;
            font-weight: 600;
        }

        .article-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(15, 44, 89, 0.08);
            color: var(--kh-primary);
            padding: 6px 16px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .article-title {
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 800;
            color: var(--kh-dark);
            line-height: 1.25;
            letter-spacing: -1px;
        }

        .article-excerpt {
            font-size: 1.15rem;
            color: var(--kh-text-light);
            line-height: 1.8;
            font-weight: 400;
            border-left: 3px solid var(--kh-primary);
            padding-left: 1.5rem;
        }

        .article-body {
            font-size: 1.1rem;
            line-height: 1.95;
            color: var(--kh-text);
        }

        .article-body h2 {
            font-size: 1.65rem;
            font-weight: 800;
            margin-top: 3rem;
            margin-bottom: 1.25rem;
            color: var(--kh-dark);
            letter-spacing: -0.5px;
        }

        .article-body h3 {
            font-size: 1.35rem;
            font-weight: 700;
            margin-top: 2.25rem;
            margin-bottom: 1rem;
            color: var(--kh-dark);
        }

        .article-body p {
            margin-bottom: 1.5rem;
        }

        .article-body ul, .article-body ol {
            margin-bottom: 1.5rem;
            padding-left: 1.5rem;
        }

        .article-body li {
            margin-bottom: 0.625rem;
        }

        .article-body blockquote {
            background: var(--kh-sky-2);
            border-left: 4px solid var(--kh-secondary);
            padding: 1.5rem;
            border-radius: 0 16px 16px 0;
            font-style: italic;
            margin: 2rem 0;
            color: var(--kh-dark);
        }

        .article-body img {
            border-radius: 20px;
            margin: 2rem 0;
            max-width: 100%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .article-featured-img {
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(15, 44, 89, 0.04);
            border: 1px solid rgba(15, 44, 89, 0.06);
        }

        /* ========== AUTHOR CARD ========== */
        .author-card {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            padding: 1.5rem;
            background: rgba(15, 44, 89, 0.02);
            border-radius: 20px;
            border: 1px solid rgba(15, 44, 89, 0.06);
            margin-top: 2rem;
        }

        .author-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.35rem;
            color: #fff;
            box-shadow: 0 5px 15px rgba(15, 44, 89, 0.15);
        }

        /* ========== SHARE CARD ========== */
        .share-card {
            background: #fff;
            border-radius: 20px;
            padding: 1.75rem;
            border: 1px solid rgba(15, 44, 89, 0.06);
            box-shadow: 0 10px 30px rgba(15, 44, 89, 0.01);
        }

        .share-btn {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--kh-transition);
            font-size: 1.25rem;
        }

        .share-btn:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }
    </style>
</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">
    <div class="layout-wrapper landing">

        @include('publik.company-profile.partials.navbar')

        <!-- ========== ARTICLE HEADER ========== -->
        <section class="section mt-5 pt-5 pb-4">
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
                            <div class="author-avatar gradient-kh">
                                {{ substr($article->author, 0, 1) }}
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold" style="color: var(--kh-dark);">{{ $article->author }}</h6>
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
        <section class="container mt-2">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="article-featured-img reveal">
                        <img src="{{ Storage::url($article->image) }}" alt="{{ $article->title }}" class="img-fluid w-100" style="max-height: 480px; object-fit: cover;">
                    </div>
                </div>
            </div>
        </section>
        @endif

        <!-- ========== CONTENT ========== -->
        <section class="section pt-5 pb-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="article-body reveal">
                            {!! nl2br(e($article->content)) !!}
                        </div>

                        <!-- Share -->
                        <div class="share-card mt-5 reveal">
                            <p class="fw-semibold mb-3" style="color: var(--kh-dark);"><i class="ri-share-line me-1 align-bottom text-kh"></i> Bagikan artikel ini:</p>
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
        <section class="section bg-light bg-opacity-40">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <h4 class="fw-bold mb-4 reveal" style="color: var(--kh-dark);"><i class="ri-bookmark-line me-2 text-kh align-bottom"></i>Artikel Terkait</h4>
                        <div class="row g-4">
                            @foreach($relatedArticles as $related)
                            <div class="col-lg-4 col-md-6 reveal">
                                <a href="{{ route('publik.articles.show', $related->slug) }}" class="text-decoration-none">
                                    <div class="article-card-modern">
                                        <div class="article-img-container" style="height: 180px;">
                                            @if($related->image)
                                                <img src="{{ Storage::url($related->image) }}" alt="{{ $related->title }}" class="card-img w-100">
                                            @else
                                                <div class="gradient-kh d-flex align-items-center justify-content-center h-100">
                                                    <i class="ri-article-line text-white opacity-25" style="font-size: 3rem;"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-body p-4">
                                            <span class="article-badge mb-2">{{ ucfirst($related->category) }}</span>
                                            <h6 class="fw-bold mb-2 mt-2 lh-base" style="color: var(--kh-dark); font-size: 1rem;">
                                                {{ Str::limit($related->title, 55) }}
                                            </h6>
                                            <small class="text-muted d-flex align-items-center gap-1">
                                                <i class="ri-calendar-line"></i> {{ $related->published_at?->format('d M Y') ?? $related->created_at->format('d M Y') }}
                                            </small>
                                        </div>
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

        <!-- ========== CTA BOTTOM ========== -->
        <section class="py-5 position-relative" style="background: var(--kh-gradient-dark); overflow: hidden;">
            <div class="container position-relative text-center py-4" style="z-index: 2;">
                <span style="width:72px;height:72px;border-radius:50%;background:rgba(255,255,255,0.05);display:inline-flex;align-items:center;justify-content:center;margin-bottom:1.5rem;color:rgba(255,255,255,0.7);">
                    <i class="ri-award-line" style="font-size: 2.2rem;"></i>
                </span>
                <h3 class="text-white fw-bold mb-2">Ingin Mendapatkan Sertifikat Halal?</h3>
                <p class="text-white text-opacity-75 mb-4" style="max-width:480px; margin:0 auto;">Daftarkan produk Anda sekarang secara mudah, transparan, dan profesional bersama Kawulo Halal.</p>
                <a href="{{ route('publik.contact') }}" class="btn btn-light btn-lg fw-bold px-5 py-3 shadow shine-hover">
                    <i class="ri-edit-line align-middle me-2"></i> Daftar Sekarang
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
    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (navbar) {
                if (window.scrollY > 50) navbar.classList.add('scrolled');
                else navbar.classList.remove('scrolled');
            }
        });
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('visible'); });
        }, { threshold: 0.1 });
        document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));
    </script>
</body>
</html>
