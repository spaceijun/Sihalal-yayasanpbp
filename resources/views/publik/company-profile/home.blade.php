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
</head>

<body>
    <div class="layout-wrapper landing">
        @include('publik.company-profile.partials.navbar')

        <!-- ========== HERO SECTION ========== -->
        <section class="section hero-section gradient-hero" id="hero">
            <!-- Decorative animated blobs -->
            <div class="kh-blob kh-blob-1"></div>
            <div class="kh-blob kh-blob-2"></div>
            <div class="kh-blob kh-blob-3 d-none d-lg-block"></div>

            <div class="hero-float-badge d-none d-lg-block" style="top: 18%; right: 8%; animation-delay: 0s;">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar-xs">
                        <div class="avatar-title bg-success rounded-circle"><i
                                class="ri-shield-check-fill text-white"></i></div>
                    </div>
                    <div>
                        <small class="fw-semibold d-block">Kawulo Halal</small>
                        <small class="text-muted-soft">Sertifikat Halal UMKM Low Risk</small>
                    </div>
                </div>
            </div>
            <div class="hero-float-badge d-none d-lg-block" style="bottom: 22%; left: 4%; animation-delay: 2s;">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar-xs">
                        <div class="avatar-title bg-warning rounded-circle"><i class="ri-star-fill text-white"></i>
                        </div>
                    </div>
                    <div>
                        <small class="fw-semibold d-block">98% Kepuasan</small>
                        <small class="text-muted-soft">Client Puas</small>
                    </div>
                </div>
            </div>

            <div class="container position-relative" style="z-index: 1;">
                <div class="row align-items-center">
                    <div class="col-lg-6" data-aos="fade-right" data-aos-duration="800">
                        <div class="hero-badge mb-4 animate-fade-in-up">
                            <i class="ri-verified-badge-fill text-success"></i>
                            <span class="small fw-semibold">Kawulo Halal</span>
                        </div>
                        <h1 class="hero-title mb-4 animate-fade-in-up delay-100">
                            Sertifikasi Halal<br>
                            <span class="highlight">Semakin Mudah</span><br>
                            untuk UMKM Indonesia
                        </h1>
                        <p class="hero-lead lead mb-5 animate-fade-in-up delay-200" style="max-width: 500px;">
                            Kawulo Halal membantu pelaku UMKM mendapatkan sertifikat halal dengan proses cepat, mudah,
                            dan biaya terjangkau.
                        </p>
                        <div class="d-flex flex-wrap gap-3 animate-fade-in-up delay-300">
                            <a href="{{ route('publik.contact') }}" class="btn btn-kh btn-lg px-4">
                                <i class="ri-arrow-right-line align-bottom me-1"></i> Mulai Sekarang
                            </a>
                            <a href="{{ route('publik.about') }}" class="btn btn-kh-outline btn-lg px-4">
                                <i class="ri-play-circle-line align-bottom me-1"></i> Pelajari Lebih
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-5 offset-lg-1 d-none d-lg-block" data-aos="fade-left" data-aos-duration="800"
                        data-aos-delay="200">
                        <div class="hero-card">
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <div class="avatar-lg">
                                    <div class="avatar-title bg-success rounded-3"><i
                                            class="ri-shield-check-fill text-white fs-4"></i></div>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold">Sertifikat Halal</h5>
                                    <small class="text-muted-soft">Low-Risk Product</small>
                                </div>
                            </div>
                            <div class="vstack gap-2 mb-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div
                                        class="avatar-xxs bg-success bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="ri-check-line text-success" style="font-size: 0.7rem;"></i>
                                    </div>
                                    <small class="text-muted-soft">Proses Mudah & Cepat</small>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div
                                        class="avatar-xxs bg-success bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="ri-check-line text-success" style="font-size: 0.7rem;"></i>
                                    </div>
                                    <small class="text-muted-soft">Biaya Terjangkau</small>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div
                                        class="avatar-xxs bg-success bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="ri-check-line text-success" style="font-size: 0.7rem;"></i>
                                    </div>
                                    <small class="text-muted-soft">Bantuan Pendampingan</small>
                                </div>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: 100%;"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <small class="text-muted-soft">Progress</small>
                                <small class="text-success fw-bold">100%</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== STATS COUNTER ========== -->
        <section class="stats-section py-5" id="stats">
            <div class="container">
                <div class="row g-0 text-center">
                    @foreach ($stats ?? [] as $stat)
                        <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 100 }}">
                            <div class="stat-card">
                                <div class="stat-number" data-target="{{ (int) $stat->value }}">0</div>
                                <div class="stat-label">{{ $stat->title }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- ========== LAYANAN / BENEFITS ========== -->
        <section class="section" id="layanan">
            <div class="container">
                <div class="row justify-content-center mb-5" data-aos="fade-up">
                    <div class="col-lg-7 text-center">
                        <span class="badge bg-kh-soft text-kh px-3 py-2 mb-3">
                            <i class="ri-service-line me-1 align-bottom"></i> Layanan Kami
                        </span>
                        <h2 class="fw-bold mb-3">Kenapa Memilih <span class="text-kh">Kawulo Halal?</span></h2>
                        <p class="text-muted">Kami menyediakan layanan sertifikasi halal yang komprehensif untuk
                            membantu UMKM Anda.</p>
                    </div>
                </div>
                <div class="row g-4">
                    @foreach ($benefits ?? [] as $benefit)
                        <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                            <div class="benefit-card">
                                <div class="benefit-icon bg-kh-soft">
                                    <i class="{{ $benefit->icon ?? 'ri-check-line' }} text-kh"></i>
                                </div>
                                <h5 class="fw-bold mb-2">{{ $benefit->title }}</h5>
                                <p class="text-muted mb-0 small">{{ $benefit->description ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
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
                                <h4 class="text-white mb-1">Siap Mendapatkan Sertifikat Halal?</h4>
                                <p class="text-white text-opacity-75 mb-0">Bergabung dengan ribuan pelaku UMKM yang
                                    telah berhasil.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 text-sm-end">
                        <a href="{{ route('publik.contact') }}"
                            class="btn btn-light btn-lg fw-bold shadow shine-hover">
                            <i class="ri-arrow-right-line align-bottom me-1"></i> Daftar Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== ARTIKEL ========== -->
        @if ($featuredArticles->isNotEmpty())
            <section class="section bg-kh-soft" id="artikel">
                <div class="container">
                    <div class="row justify-content-center mb-5" data-aos="fade-up">
                        <div class="col-lg-7 text-center">
                            <span class="badge bg-white text-kh px-3 py-2 mb-3 shadow-sm">
                                <i class="ri-book-open-line me-1 align-bottom"></i> Blog & Artikel
                            </span>
                            <h2 class="fw-bold mb-3">Artikel <span class="text-kh">Terbaru</span></h2>
                            <p class="text-muted">Baca artikel terbaru tentang sertifikasi halal dan tips untuk UMKM.
                            </p>
                        </div>
                    </div>
                    <div class="row g-4">
                        @foreach ($featuredArticles as $article)
                            <div class="col-lg-4 col-md-6" data-aos="fade-up"
                                data-aos-delay="{{ $loop->index * 100 }}">
                                <a href="{{ route('publik.articles.show', $article->slug) }}"
                                    class="text-decoration-none">
                                    <div class="article-card">
                                        @if ($article->image)
                                            <div class="overflow-hidden">
                                                <img src="{{ Storage::url($article->image) }}"
                                                    alt="{{ $article->title }}" class="card-img w-100">
                                            </div>
                                        @else
                                            <div class="bg-kh d-flex align-items-center justify-content-center"
                                                style="height: 200px;">
                                                <i class="ri-article-line text-white opacity-50"
                                                    style="font-size: 4rem;"></i>
                                            </div>
                                        @endif
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center gap-2 mb-3">
                                                <span
                                                    class="article-badge">{{ ucfirst($article->category ?? 'Artikel') }}</span>
                                                <span class="text-muted small"><i
                                                        class="ri-time-line me-1"></i>{{ $article->reading_time ?? 3 }}
                                                    min</span>
                                            </div>
                                            <h6 class="fw-bold mb-2 lh-base" style="font-size: 1rem;">
                                                {{ Str::limit($article->title, 55) }}</h6>
                                            <p class="text-muted small mb-3">
                                                {{ Str::limit($article->excerpt_or_truncated, 90) }}</p>
                                            <span class="btn btn-sm btn-kh-outline fw-semibold">
                                                Baca Selengkapnya <i class="ri-arrow-right-line align-bottom ms-1"></i>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-5" data-aos="fade-up">
                        <a href="{{ route('publik.articles.index') }}" class="btn btn-lg btn-kh">
                            <i class="ri-arrow-right-line align-bottom me-1"></i> Lihat Semua Artikel
                        </a>
                    </div>
                </div>
            </section>
        @endif

        <!-- ========== TESTIMONIALS ========== -->
        @if ($testimonials->isNotEmpty())
            <section class="section" id="testimonial">
                <div class="container">
                    <div class="row justify-content-center mb-5" data-aos="fade-up">
                        <div class="col-lg-7 text-center">
                            <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 mb-3">
                                <i class="ri-star-fill me-1 align-bottom"></i> Testimoni
                            </span>
                            <h2 class="fw-bold mb-3">Apa Kata <span class="text-kh">Mereka</span></h2>
                            <p class="text-muted">Cerita dari pelaku UMKM yang telah berhasil mendapatkan sertifikat
                                halal.</p>
                        </div>
                    </div>
                    <div class="row g-4">
                        @foreach ($testimonials as $testimonial)
                            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                                <div class="testimonial-card h-100">
                                    <div class="testimonial-quote">"</div>
                                    <p class="text-muted mb-4 lh-lg">{{ Str::limit($testimonial->testimonial, 150) }}
                                    </p>
                                    <div class="d-flex align-items-center gap-3">
                                        @if ($testimonial->photo)
                                            <img src="{{ Storage::url($testimonial->photo) }}"
                                                alt="{{ $testimonial->name }}" class="testimonial-avatar"
                                                style="object-fit: cover;">
                                        @else
                                            <div class="testimonial-avatar bg-kh">
                                                {{ substr($testimonial->name, 0, 1) }} </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0 fw-bold small">{{ $testimonial->name }}</h6>
                                            <small class="text-muted">{{ $testimonial->position ?? '' }}</small>
                                        </div>
                                    </div>
                                    <div class="text-warning mt-3">
                                        @for ($i = 0; $i < 5; $i++)
                                            <i class="ri-star{{ $i < $testimonial->rating ? '-fill' : '' }}"></i>
                                        @endfor
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
            <section class="section bg-kh-soft" id="karir">
                <div class="container">
                    <div class="row justify-content-center mb-5" data-aos="fade-up">
                        <div class="col-lg-7 text-center">
                            <span class="badge bg-white text-kh px-3 py-2 mb-3 shadow-sm">
                                <i class="ri-briefcase-line me-1 align-bottom"></i> Karir
                            </span>
                            <h2 class="fw-bold mb-3">Lowongan <span class="text-kh">Kerja</span></h2>
                            <p class="text-muted">Bergabunglah dengan tim kami untuk berkontribusi dalam ekosistem
                                sertifikasi halal.</p>
                        </div>
                    </div>
                    <div class="row g-4">
                        @foreach ($recruitmentPosts as $post)
                            <div class="col-lg-4 col-md-6" data-aos="fade-up"
                                data-aos-delay="{{ $loop->index * 100 }}">
                                <div class="job-card h-100">
                                    <div class="d-flex align-items-start justify-content-between mb-3">
                                        <div>
                                            @if ($post->isOpen())
                                                <span
                                                    class="job-badge bg-success bg-opacity-10 text-success mb-2 d-inline-block">
                                                    <i class="ri-checkbox-circle-line me-1"></i>Terbuka
                                                </span>
                                            @else
                                                <span
                                                    class="job-badge bg-secondary bg-opacity-10 text-secondary mb-2 d-inline-block">Ditutup</span>
                                            @endif
                                            @if ($post->posisi)
                                                <span
                                                    class="job-badge bg-kh-soft text-kh mb-2 d-inline-block ms-1">{{ $post->posisi }}</span>
                                            @endif
                                        </div>
                                        <div
                                            class="avatar-lg bg-kh-soft rounded-3 d-flex align-items-center justify-content-center">
                                            <i class="ri-briefcase-4-line text-kh fs-5"></i>
                                        </div>
                                    </div>
                                    <h5 class="fw-bold mb-2">{{ $post->nama_loker }}</h5>
                                    <p class="text-muted small mb-3">{{ Str::limit($post->deskripsi, 100) }}</p>
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
                        @endforeach
                    </div>
                    <div class="text-center mt-5" data-aos="fade-up">
                        <a href="{{ route('publik.recruitment.index') }}" class="btn btn-lg btn-kh">
                            <i class="ri-arrow-right-line align-bottom me-1"></i> Lihat Semua Lowongan
                        </a>
                    </div>
                </div>
            </section>
        @endif

        <!-- ========== CTA BOTTOM ========== -->
        <section class="py-5 gradient-kh-dark cta-section position-relative" id="kontak">
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
    <script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- Kawulo Halal Modern Theme Js -->
    <script src="{{ asset('assets/js/compro.js') }}"></script>
</body>

</html>
