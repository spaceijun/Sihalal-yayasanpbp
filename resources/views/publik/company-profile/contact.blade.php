<!DOCTYPE html>
<html lang="id" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<head>
    <meta charset="utf-8" />
    <title>{{ $profile->title ?? 'Kontak - Kawulo Halal' }}</title>
    <meta name="description"
        content="{{ $profile->meta_description ?? 'Hubungi Kawulo Halal untuk informasi tentang sertifikasi halal.' }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <!-- Open Graph -->
    <meta property="og:title" content="{{ $profile->title ?? 'Kontak - Kawulo Halal' }}">
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
    <!-- Kawulo Halal Modern Theme -->
    <link href="{{ asset('assets/css/compro-ui.css') }}" rel="stylesheet" type="text/css" />

    <style>
        /* ── Page-specific styles (contact only) ── */

        /* Hero — light blue gradient selaras dengan home */
        .hero-contact {
            background: linear-gradient(180deg, #EAF4FF 0%, #F4FAFF 50%, #FFFFFF 100%);
            padding-top: 9rem;
            padding-bottom: 5rem;
            position: relative;
            overflow: hidden;
        }

        .hero-contact .kh-blob {
            opacity: 0.5;
        }

        .hero-contact-title {
            font-size: clamp(2rem, 4vw, 3.25rem);
            font-weight: 800;
            line-height: 1.15;
            color: var(--kh-dark);
        }

        .hero-contact-title .highlight {
            background: var(--kh-gradient-text);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Contact info cards */
        .contact-card {
            background: #fff;
            border-radius: var(--kh-radius-lg);
            border: 1px solid #EAF1FB;
            padding: 2rem;
            text-align: center;
            transition: var(--kh-transition);
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .contact-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--kh-gradient-soft);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .contact-card>* {
            position: relative;
            z-index: 1;
        }

        .contact-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--kh-shadow-lg);
            border-color: var(--kh-primary-light);
        }

        .contact-card:hover::before {
            opacity: 1;
        }

        .contact-icon {
            width: 72px;
            height: 72px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            transition: var(--kh-transition);
            background: var(--kh-sky);
        }

        .contact-card:hover .contact-icon {
            transform: scale(1.12) rotate(6deg);
            background: var(--kh-gradient);
        }

        .contact-card:hover .contact-icon i {
            color: #fff !important;
        }

        .contact-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--kh-dark);
            margin-bottom: 0.5rem;
        }

        .contact-value {
            color: var(--kh-text);
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .contact-note {
            color: var(--kh-text-light);
            font-size: 0.85rem;
            margin: 0;
        }

        /* Form card */
        .form-card {
            background: #fff;
            border-radius: var(--kh-radius-lg);
            border: 1px solid #EAF1FB;
            padding: 2.5rem;
            box-shadow: var(--kh-shadow);
        }

        .form-kh .form-control,
        .form-kh .form-select {
            border-radius: 12px;
            padding: 0.875rem 1rem;
            border: 2px solid #EAF1FB;
            transition: var(--kh-transition);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .form-kh .form-control:focus,
        .form-kh .form-select:focus {
            border-color: var(--kh-primary);
            box-shadow: 0 0 0 4px rgba(47, 143, 230, 0.12);
        }

        .form-kh label {
            font-weight: 600;
            color: var(--kh-dark);
            margin-bottom: 0.5rem;
        }

        .form-kh textarea {
            resize: vertical;
            min-height: 140px;
        }

        /* FAQ */
        .faq-section {
            background: var(--kh-sky-2);
        }

        .faq-card {
            background: #fff;
            border-radius: var(--kh-radius);
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            border: 1px solid #EAF1FB;
            transition: var(--kh-transition);
            margin-bottom: 1rem;
        }

        .faq-card:last-child {
            margin-bottom: 0;
        }

        .faq-card:hover {
            transform: translateX(6px);
            box-shadow: var(--kh-shadow);
            border-color: var(--kh-primary-light);
        }

        .faq-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: var(--kh-sky);
            transition: var(--kh-transition);
        }

        .faq-card:hover .faq-icon {
            background: var(--kh-gradient);
        }

        .faq-card:hover .faq-icon i {
            color: #fff !important;
        }

        .faq-question {
            font-weight: 700;
            color: var(--kh-dark);
            margin-bottom: 0.25rem;
            font-size: 0.95rem;
        }

        .faq-answer {
            color: var(--kh-text-light);
            font-size: 0.875rem;
            margin: 0;
        }

        @media (max-width: 768px) {
            .hero-contact {
                padding-top: 7rem;
                padding-bottom: 3rem;
            }

            .form-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="layout-wrapper landing">

        @include('publik.company-profile.partials.navbar')

        <!-- ========== HERO ========== -->
        <section class="hero-contact" id="hero">
            <!-- Decorative blobs sama seperti home -->
            <div class="kh-blob kh-blob-1"></div>
            <div class="kh-blob kh-blob-2"></div>

            <div class="container position-relative text-center" style="z-index: 1;">
                <div class="row justify-content-center">
                    <div class="col-lg-7">
                        <div class="hero-badge mb-4 animate-fade-in-up" style="margin: 0 auto 1.5rem;">
                            <i class="ri-customer-service-2-line text-kh"></i>
                            <span class="small fw-semibold">Hubungi Kami</span>
                        </div>
                        <h1 class="hero-contact-title mb-4 animate-fade-in-up delay-100">
                            Ada Pertanyaan?<br>
                            <span class="highlight">Kami Siap Membantu</span>
                        </h1>
                        <p class="hero-lead lead mb-0 animate-fade-in-up delay-200"
                            style="max-width: 520px; margin: 0 auto;">
                            Jangan ragu untuk menghubungi kami. Tim kami siap membantu Anda 24/7.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== CONTACT CARDS ========== -->
        <section class="section pt-5" id="kontak-info">
            <div class="container">
                <div class="row g-4">
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="0">
                        <div class="contact-card">
                            <div class="contact-icon">
                                <i class="ri-phone-line text-kh fs-3"></i>
                            </div>
                            <h5 class="contact-title">Telepon / WhatsApp</h5>
                            <p class="contact-value">+62 897-6774-482</p>
                            <p class="contact-note">Senin – Jumat, 08.00 – 17.00</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="contact-card">
                            <div class="contact-icon">
                                <i class="ri-mail-line text-kh fs-3"></i>
                            </div>
                            <h5 class="contact-title">Email</h5>
                            <p class="contact-value">info@kawulohalal.id</p>
                            <p class="contact-note">Respon dalam 24 jam</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="contact-card">
                            <div class="contact-icon">
                                <i class="ri-map-pin-line text-kh fs-3"></i>
                            </div>
                            <h5 class="contact-title">Alamat</h5>
                            <p class="contact-value">Virtual Office On Jakarta Selatan, DKI Jakarta</p>
                            <p class="contact-note">Layanan seluruh Indonesia</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== FORM ========== -->
        <section class="section" id="form-kontak">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center mb-5" data-aos="fade-up">
                            <span class="badge bg-kh-soft text-kh px-3 py-2 mb-3">
                                <i class="ri-mail-send-line me-1 align-bottom"></i> Kirim Pesan
                            </span>
                            <h2 class="fw-bold mb-2">Kirim <span class="text-kh">Pesan</span></h2>
                            <p class="text-muted">Isi formulir di bawah ini dan kami akan segera menghubungi Anda.</p>
                        </div>

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                                <strong><i class="ri-checkbox-circle-line me-1"></i> Berhasil!</strong>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="form-card" data-aos="fade-up" data-aos-delay="100">
                            <form action="{{ route('publik.contact.submit') }}" method="POST" class="form-kh">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">
                                            Nama Lengkap <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" placeholder="Masukkan nama Anda"
                                            value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">
                                            Email <span class="text-danger">*</span>
                                        </label>
                                        <input type="email"
                                            class="form-control @error('email') is-invalid @enderror" id="email"
                                            name="email" placeholder="nama@email.com" value="{{ old('email') }}"
                                            required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Nomor Telepon</label>
                                        <input type="tel" class="form-control" id="phone" name="phone"
                                            placeholder="08xxxxxxxxxx" value="{{ old('phone') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="subject" class="form-label">Subjek</label>
                                        <input type="text" class="form-control" id="subject" name="subject"
                                            placeholder="Subjek pesan" value="{{ old('subject') }}">
                                    </div>
                                    <div class="col-12">
                                        <label for="message" class="form-label">
                                            Pesan <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5"
                                            placeholder="Tulis pesan Anda di sini..." required>{{ old('message') }}</textarea>
                                        @error('message')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-kh btn-lg w-100">
                                            <i class="ri-send-plane-line me-1 align-bottom"></i> Kirim Pesan
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== FAQ ========== -->
        <section class="section faq-section" id="faq">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-5" data-aos="fade-right">
                        <span class="badge bg-kh-soft text-kh px-3 py-2 mb-3">
                            <i class="ri-question-line me-1 align-bottom"></i> FAQ
                        </span>
                        <h2 class="fw-bold mb-3 lh-base">
                            Pertanyaan yang<br><span class="text-kh">Sering Diajukan</span>
                        </h2>
                        <p class="text-muted mb-4">
                            Temukan jawaban untuk pertanyaan yang paling sering diajukan tentang sertifikasi halal.
                        </p>
                        <a href="{{ route('publik.about') }}" class="btn btn-kh">
                            <i class="ri-arrow-right-line align-bottom me-1"></i> Pelajari Lebih
                        </a>
                    </div>
                    <div class="col-lg-7" data-aos="fade-left" data-aos-delay="100">
                        <div class="faq-card">
                            <div class="faq-icon">
                                <i class="ri-time-line text-kh fs-5"></i>
                            </div>
                            <div>
                                <h6 class="faq-question">Berapa lama proses sertifikasi halal?</h6>
                                <p class="faq-answer">Umumnya 30 hari kerja sejak dokumen lengkap diterima.</p>
                            </div>
                        </div>
                        <div class="faq-card">
                            <div class="faq-icon">
                                <i class="ri-file-list-3-line text-kh fs-5"></i>
                            </div>
                            <div>
                                <h6 class="faq-question">Apa saja dokumen yang diperlukan?</h6>
                                <p class="faq-answer">NIB, daftar produk, informasi bahan baku, dan layout proses
                                    produksi.</p>
                            </div>
                        </div>
                        <div class="faq-card">
                            <div class="faq-icon">
                                <i class="ri-money-dollar-circle-line text-kh fs-5"></i>
                            </div>
                            <div>
                                <h6 class="faq-question">Berapa biaya sertifikasi halal?</h6>
                                <p class="faq-answer">Biaya tergantung jenis dan jumlah produk. Konsultasi gratis
                                    tersedia untuk Anda.</p>
                            </div>
                        </div>
                        <div class="faq-card">
                            <div class="faq-icon">
                                <i class="ri-graduation-cap-line text-kh fs-5"></i>
                            </div>
                            <div>
                                <h6 class="faq-question">Apakah ada pelatihan Penyelia Halal?</h6>
                                <p class="faq-answer">Ya, pelatihan Penyelia Halal tersedia dalam paket layanan
                                    sertifikasi kami.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== CTA ========== -->
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
                                <h4 class="text-white mb-1">Ingin Mendaftarkan Produk Anda?</h4>
                                <p class="text-white text-opacity-75 mb-0">Jangan tunda lagi. Daftarkan produk Anda
                                    sekarang dan raih kepercayaan konsumen.</p>
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

        @include('publik.company-profile.partials.footer')
    </div>

    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <script src="{{ asset('assets/js/plugins.js') }}"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- Kawulo Halal Modern Theme Js (handles navbar scroll, counter, ripple, back-to-top) -->
    <script src="{{ asset('assets/js/compro.js') }}"></script>
</body>

</html>
