<!DOCTYPE html>
<html lang="id" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<head>
    <meta charset="utf-8" />
    <title>Artikel - Kawulo Halal</title>
    <meta name="description"
        content="Baca artikel terbaru tentang sertifikasi halal, tips bisnis, dan informasi bermanfaat lainnya.">
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
        /* ── Page-specific styles (articles index only) ── */
        .hero-title {
            font-size: clamp(2.5rem, 6vw, 4rem);
            font-weight: 800;
            line-height: 1.2;
            color: var(--kh-dark);
            letter-spacing: -1.5px;
        }

        .hero-title .highlight {
            background: var(--kh-gradient-text);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .filter-bar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(47, 143, 230, 0.08);
            position: sticky;
            top: 70px;
            z-index: 100;
            padding: 1.25rem 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
            transition: var(--kh-transition);
        }

        .search-wrapper {
            position: relative;
        }

        .search-wrapper i {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--kh-text-light);
            font-size: 1.1rem;
            pointer-events: none;
            transition: var(--kh-transition);
        }

        .search-input {
            border-radius: 30px;
            padding: 0.75rem 1rem 0.75rem 3rem;
            border: 1.5px solid rgba(47, 143, 230, 0.12);
            background-color: #fff;
            transition: var(--kh-transition);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.95rem;
            color: var(--kh-dark);
        }

        .search-input:focus {
            border-color: var(--kh-primary);
            box-shadow: 0 0 0 4px rgba(47, 143, 230, 0.08);
            background-color: #fff;
            outline: none;
        }

        .search-input:focus+i {
            color: var(--kh-primary);
        }

        .category-chip {
            display: inline-flex;
            align-items: center;
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 0.875rem;
            font-weight: 600;
            background: #fff;
            border: 1.5px solid rgba(47, 143, 230, 0.1);
            color: var(--kh-text-light);
            transition: var(--kh-transition);
            text-decoration: none;
            white-space: nowrap;
        }

        .category-chip:hover {
            border-color: var(--kh-primary);
            color: var(--kh-primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(47, 143, 230, 0.05);
        }

        .category-chip.active {
            background: var(--kh-gradient);
            border-color: transparent;
            color: #fff;
            box-shadow: 0 5px 15px rgba(47, 143, 230, 0.2);
            transform: translateY(-2px);
        }

        /* Featured article card */
        .featured-article-card {
            background: #fff;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid rgba(47, 143, 230, 0.06);
            box-shadow: 0 10px 30px rgba(47, 143, 230, 0.02);
            transition: var(--kh-transition);
            text-decoration: none;
            display: block;
        }

        .featured-article-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(47, 143, 230, 0.06);
            border-color: rgba(47, 143, 230, 0.15);
        }

        .featured-article-card .featured-img {
            height: 100%;
            min-height: 340px;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            width: 100%;
        }

        .featured-article-card:hover .featured-img {
            transform: scale(1.03);
        }

        /* Pagination custom style */
        .pagination .page-item .page-link {
            border-radius: 50%;
            margin: 0 4px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            border-color: rgba(47, 143, 230, 0.1);
            color: var(--kh-text-light);
            transition: var(--kh-transition);
        }

        .pagination .page-item.active .page-link,
        .pagination .page-item .page-link:hover {
            background: var(--kh-gradient);
            border-color: transparent;
            color: #fff;
            box-shadow: 0 4px 10px rgba(47, 143, 230, 0.15);
        }

        /* Empty state */
        .empty-state {
            border-radius: 20px;
            border: 2px dashed rgba(47, 143, 230, 0.2);
            padding: 4.5rem 2rem;
            background: rgba(47, 143, 230, 0.02);
            max-width: 480px;
            margin: 0 auto;
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
                            <i class="ri-book-open-line text-kh"></i>
                            <span>Blog & Artikel</span>
                        </div>
                        <h1 class="hero-title mb-4 animate-fade-in-up delay-100">
                            Artikel & <span class="highlight">Edukasi Halal</span>
                        </h1>
                        <p class="hero-lead lead mb-0 animate-fade-in-up delay-200 text-muted"
                            style="max-width: 600px; margin: 0 auto; line-height: 1.8; font-size: 1.05rem;">
                            Temukan panduan lengkap, berita terbaru, dan insight penting seputar sertifikasi halal untuk
                            mendukung perkembangan produk bisnis Anda.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== FILTER BAR ========== -->
        <section class="filter-bar">
            <div class="container">
                <form method="GET" class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <div class="search-wrapper">
                            <i class="ri-search-line"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari artikel..." class="form-control search-input">
                        </div>
                    </div>
                    @if ($categories->isNotEmpty())
                        <div class="col-md-6">
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('publik.articles.index') }}"
                                    class="category-chip {{ !request('category') ? 'active' : '' }}">
                                    Semua
                                </a>
                                @foreach ($categories as $cat)
                                    <a href="{{ route('publik.articles.index', ['category' => $cat->name]) }}"
                                        class="category-chip {{ request('category') == $cat->name ? 'active' : '' }}">
                                        {{ $cat->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if (request('search') || request('category'))
                        <div class="col-md-auto">
                            <a href="{{ route('publik.articles.index') }}" class="btn btn-ghost-danger btn-sm">
                                <i class="ri-close-line me-1 align-bottom"></i> Reset
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </section>

        <!-- ========== ARTICLES GRID ========== -->
        <section class="section bg-light bg-opacity-40">
            <div class="container">
                @if ($articles->isEmpty())
                    <div class="text-center" data-aos="fade-up">
                        <div class="empty-state d-inline-block">
                            <i class="ri-article-line text-kh" style="font-size: 4rem; opacity: 0.3;"></i>
                            <h4 class="fw-bold mt-3 mb-2" style="color: var(--kh-dark);">Belum Ada Artikel</h4>
                            <p class="text-muted mb-0">Artikel akan segera ditambahkan. Stay tuned!</p>
                        </div>
                    </div>
                @else
                    <div class="row g-4">

                        {{-- Featured article (first item on page 1) --}}
                        @if ($articles->currentPage() == 1)
                            @php $first = $articles->first(); @endphp
                            <div class="col-12" data-aos="fade-up">
                                <a href="{{ route('publik.articles.show', $first->slug) }}"
                                    class="featured-article-card shine-hover">
                                    <div class="row g-0">
                                        <div class="col-lg-5 overflow-hidden">
                                            @if ($first->image)
                                                <img src="{{ Storage::url($first->image) }}"
                                                    alt="{{ $first->title }}" class="featured-img">
                                            @else
                                                <div class="gradient-kh d-flex align-items-center justify-content-center"
                                                    style="min-height: 340px; height: 100%;">
                                                    <i class="ri-article-line text-white opacity-25"
                                                        style="font-size: 5rem;"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-lg-7">
                                            <div
                                                class="card-body p-4 p-lg-5 d-flex flex-column justify-content-center h-100">
                                                <div class="d-flex align-items-center gap-2 mb-3">
                                                    <span class="article-badge">{{ ucfirst($first->category) }}</span>
                                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1"
                                                        style="font-size: 0.75rem; font-weight: 600;">
                                                        <i class="ri-star-fill me-1"></i> Pilihan
                                                    </span>
                                                    <span class="text-muted small d-flex align-items-center gap-1">
                                                        <i class="ri-time-line"></i> {{ $first->reading_time ?? 3 }}
                                                        min
                                                    </span>
                                                </div>
                                                <h3 class="fw-bold mb-3"
                                                    style="font-size: 1.65rem; color: var(--kh-dark); line-height: 1.35;">
                                                    {{ $first->title }}
                                                </h3>
                                                <p class="text-muted mb-4"
                                                    style="line-height: 1.7; font-size: 0.95rem;">
                                                    {{ Str::limit($first->excerpt_or_truncated, 180) }}
                                                </p>
                                                <div
                                                    class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-2">
                                                    <small class="text-muted d-flex align-items-center gap-1">
                                                        <i class="ri-calendar-line"></i>
                                                        {{ $first->published_at?->format('d M Y') ?? $first->created_at->format('d M Y') }}
                                                    </small>
                                                    <span class="btn btn-kh btn-sm px-4">
                                                        Baca Selengkapnya <i
                                                            class="ri-arrow-right-line align-middle ms-1"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endif

                        {{-- Grid articles --}}
                        @foreach ($articles->skip($articles->currentPage() == 1 ? 1 : 0) as $loop_index => $article)
                            <div class="col-lg-4 col-md-6" data-aos="fade-up"
                                data-aos-delay="{{ ($loop_index % 3) * 100 }}">
                                <a href="{{ route('publik.articles.show', $article->slug) }}"
                                    class="text-decoration-none">
                                    <div class="article-card-modern">
                                        <div class="article-img-container">
                                            @if ($article->image)
                                                <img src="{{ Storage::url($article->image) }}"
                                                    alt="{{ $article->title }}" class="card-img w-100">
                                            @else
                                                <div
                                                    class="gradient-kh d-flex align-items-center justify-content-center h-100">
                                                    <i class="ri-article-line text-white opacity-25"
                                                        style="font-size: 4rem;"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center gap-2 mb-3">
                                                <span
                                                    class="article-badge">{{ ucfirst($article->category ?? 'Artikel') }}</span>
                                                <span class="text-muted small d-flex align-items-center gap-1">
                                                    <i class="ri-time-line"></i> {{ $article->reading_time ?? 3 }} min
                                                </span>
                                            </div>
                                            <h6 class="fw-bold mb-2 lh-base"
                                                style="font-size: 1.05rem; color: var(--kh-dark);">
                                                {{ Str::limit($article->title, 60) }}
                                            </h6>
                                            <p class="text-muted small mb-4" style="line-height: 1.6;">
                                                {{ Str::limit($article->excerpt_or_truncated, 95) }}
                                            </p>
                                            <div
                                                class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                <small class="text-muted d-flex align-items-center gap-1">
                                                    <i class="ri-calendar-line"></i>
                                                    {{ $article->published_at?->format('d M Y') ?? $article->created_at->format('d M Y') }}
                                                </small>
                                                <span class="btn btn-sm btn-kh-outline px-3 fw-semibold">
                                                    Baca <i class="ri-arrow-right-line align-middle ms-1"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if ($articles->hasPages())
                        <div class="mt-5 d-flex justify-content-center" data-aos="fade-up">
                            {{ $articles->withQueryString()->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </section>

        <!-- ========== CTA BOTTOM ========== -->
        <section class="py-5 position-relative"
            style="background: linear-gradient(135deg, #16243B 0%, #0F1A2D 100%); overflow: hidden;">
            <div class="container position-relative text-center py-4" style="z-index: 2;">
                <span
                    style="width:72px;height:72px;border-radius:50%;background:rgba(255,255,255,0.05);display:inline-flex;align-items:center;justify-content:center;margin-bottom:1.5rem;color:rgba(255,255,255,0.7);">
                    <i class="ri-award-line" style="font-size: 2.2rem;"></i>
                </span>
                <h3 class="text-white fw-bold mb-2">Ingin Mendapatkan Sertifikat Halal?</h3>
                <p class="text-white text-opacity-75 mb-4" style="max-width:480px; margin:0 auto;">Daftarkan produk
                    Anda sekarang secara mudah, transparan, dan profesional bersama Kawulo Halal.</p>
                <a href="{{ route('publik.contact') }}"
                    class="btn btn-light btn-lg fw-bold px-5 py-3 shadow shine-hover">
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
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- Kawulo Halal Modern Theme Js -->
    <script src="{{ asset('assets/js/compro.js') }}"></script>
</body>

</html>
