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
            background: linear-gradient(180deg, var(--kh-sky) 0%, var(--kh-sky-2) 50%, var(--kh-white) 100%);
            padding-top: 9rem;
            padding-bottom: 5rem;
            position: relative;
            overflow: hidden;
        }

        .hero-contact .kh-blob {
            opacity: 0.35;
        }

        .hero-contact-title {
            font-size: clamp(2.25rem, 5vw, 3.5rem);
            font-weight: 800;
            line-height: 1.2;
            color: var(--kh-dark);
            letter-spacing: -1px;
        }

        .hero-contact-title .highlight {
            background: var(--kh-gradient-text);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Contact info cards */
        .contact-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: var(--kh-radius-lg);
            border: 1px solid rgba(15, 44, 89, 0.08);
            padding: 2.5rem 2rem;
            text-align: center;
            transition: var(--kh-transition);
            height: 100%;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(15, 44, 89, 0.02);
        }

        .contact-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(15, 44, 89, 0.02) 0%, rgba(142, 154, 175, 0.02) 100%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .contact-card>* {
            position: relative;
            z-index: 1;
        }

        .contact-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(15, 44, 89, 0.06);
            border-color: rgba(15, 44, 89, 0.15);
        }

        .contact-card:hover::before {
            opacity: 1;
        }

        .contact-icon {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            transition: var(--kh-transition);
            background: rgba(15, 44, 89, 0.08);
            color: var(--kh-primary);
        }

        .contact-card:hover .contact-icon {
            transform: scale(1.1) rotate(4deg);
            background: var(--kh-gradient);
            color: #fff;
        }

        .contact-card:hover .contact-icon i {
            color: #fff !important;
        }

        .contact-title {
            font-size: 1.15rem;
            font-weight: 750;
            color: var(--kh-dark);
            margin-bottom: 0.75rem;
        }

        .contact-value {
            color: var(--kh-text);
            font-weight: 600;
            font-size: 1.05rem;
            margin-bottom: 0.5rem;
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
            border: 1px solid rgba(15, 44, 89, 0.06);
            padding: 3rem;
            box-shadow: 0 15px 45px rgba(15, 44, 89, 0.03);
        }

        .form-kh .form-control,
        .form-kh .form-select {
            border-radius: 12px;
            padding: 0.875rem 1.15rem;
            border: 2px solid #EAF1FB;
            transition: var(--kh-transition);
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #FDFEFF;
            font-size: 0.95rem;
        }

        .form-kh .form-control:focus,
        .form-kh .form-select:focus {
            border-color: var(--kh-primary);
            box-shadow: 0 0 0 4px rgba(15, 44, 89, 0.08);
            background-color: #fff;
        }

        .form-kh label {
            font-weight: 600;
            color: var(--kh-dark);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-kh textarea {
            resize: vertical;
            min-height: 150px;
        }

        /* FAQ */
        .faq-section {
            background: linear-gradient(180deg, #FFFFFF 0%, var(--kh-sky-2) 100%);
        }

        .faq-card {
            background: #fff;
            border-radius: 18px;
            padding: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 1.25rem;
            border: 1px solid rgba(15, 44, 89, 0.06);
            transition: var(--kh-transition);
            margin-bottom: 1.25rem;
            box-shadow: 0 5px 15px rgba(15, 44, 89, 0.01);
        }

        .faq-card:last-child {
            margin-bottom: 0;
        }

        .faq-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 35px rgba(15, 44, 89, 0.04);
            border-color: rgba(15, 44, 89, 0.12);
        }

        .faq-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: rgba(15, 44, 89, 0.08);
            color: var(--kh-primary);
            transition: var(--kh-transition);
        }

        .faq-card:hover .faq-icon {
            background: var(--kh-gradient);
            color: #fff;
        }

        .faq-card:hover .faq-icon i {
            color: #fff !important;
        }

        .faq-question {
            font-weight: 700;
            color: var(--kh-dark);
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }

        .faq-answer {
            color: var(--kh-text-light);
            font-size: 0.88rem;
            line-height: 1.6;
            margin: 0;
        }

        @media (max-width: 768px) {
            .hero-contact {
                padding-top: 8rem;
                padding-bottom: 4rem;
            }

            .form-card {
                padding: 1.75rem;
            }
        }
    </style>
</head>

<body>
    <div class="layout-wrapper landing">

        @include('publik.company-profile.partials.navbar')

        <!-- ========== HERO ========== -->
        <section class="hero-contact" id="hero">
            <!-- Decorative blobs -->
            <div class="kh-blob kh-blob-1"></div>
            <div class="kh-blob kh-blob-2"></div>

            <div class="container position-relative text-center" style="z-index: 1;">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="modern-badge mb-4 animate-fade-in-up" style="margin: 0 auto 1.5rem;">
                            <i class="ri-customer-service-2-line"></i>
                            <span>Hubungi Kami</span>
                        </div>
                        <h1 class="hero-contact-title mb-4 animate-fade-in-up delay-100">
                            Ada Pertanyaan?<br>
                            <span class="highlight">Kami Siap Membantu</span>
                        </h1>
                        <p class="hero-lead lead mb-0 animate-fade-in-up delay-200 text-muted"
                            style="max-width: 540px; margin: 0 auto; line-height: 1.7; font-size: 1.05rem;">
                            Jangan ragu untuk menghubungi kami. Tim ahli pendamping halal kami siap melayani dan
                            mendampingi bisnis Anda.
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
                            <span
                                style="width:64px;height:64px;border-radius:18px;background:rgba(15,44,89,0.08);display:inline-flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;color:var(--kh-primary);transition:var(--kh-transition);"
                                class="contact-icon-wrapper">
                                <i class="ri-phone-line" style="font-size:1.6rem;"></i>
                            </span>
                            <h5 class="contact-title">Telepon / WhatsApp</h5>
                            <p class="contact-value" style="color:var(--kh-primary);">+62 897-6774-482</p>
                            <p class="contact-note">Senin – Jumat, 08.00 – 17.00</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="contact-card">
                            <span
                                style="width:64px;height:64px;border-radius:18px;background:rgba(142,154,175,0.08);display:inline-flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;color:var(--kh-secondary);transition:var(--kh-transition);"
                                class="contact-icon-wrapper">
                                <i class="ri-mail-line" style="font-size:1.6rem;"></i>
                            </span>
                            <h5 class="contact-title">Email</h5>
                            <p class="contact-value" style="color:var(--kh-secondary);">info@kawulohalal.id</p>
                            <p class="contact-note">Respon dalam 24 jam</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="contact-card">
                            <span
                                style="width:64px;height:64px;border-radius:18px;background:rgba(15,44,89,0.06);display:inline-flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;color:var(--kh-dark);transition:var(--kh-transition);"
                                class="contact-icon-wrapper">
                                <i class="ri-map-pin-line" style="font-size:1.6rem;"></i>
                            </span>
                            <h5 class="contact-title">Alamat</h5>
                            <p class="contact-value" style="color:var(--kh-dark);">Virtual Office On Jakarta Selatan
                            </p>
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
                            <div class="modern-badge modern-badge-emerald mb-3" style="margin: 0 auto;">
                                <i class="ri-mail-send-line"></i>
                                <span>Kirim Pesan</span>
                            </div>
                            <h2 class="section-title mb-2">Hubungi Kami Secara <span class="text-kh">Langsung</span>
                            </h2>
                            <p class="section-subtitle">Isi formulir di bawah ini dan tim kami akan segera menghubungi
                                Anda kembali.</p>
                        </div>

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert"
                                style="border-radius: 12px;">
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
                                        <button type="submit" class="btn btn-kh btn-lg w-100 shadow py-3">
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
                        <div class="modern-badge mb-3">
                            <i class="ri-question-line"></i>
                            <span>Pertanyaan Umum</span>
                        </div>
                        <h2 class="section-title mb-3 lh-base">
                            Pertanyaan yang<br><span class="text-kh">Sering Diajukan</span>
                        </h2>
                        <p class="section-subtitle text-start mb-4" style="max-width:100%;">
                            Temukan jawaban cepat untuk pertanyaan yang paling sering diajukan mengenai layanan
                            sertifikasi halal kami.
                        </p>
                        <a href="{{ route('publik.about') }}" class="btn btn-kh-outline btn-lg px-4 py-3">
                            <i class="ri-arrow-right-line align-bottom me-1"></i> Pelajari Lebih Lanjut
                        </a>
                    </div>
                    <div class="col-lg-7" data-aos="fade-left" data-aos-delay="100">
                        <div class="faq-card">
                            <div class="faq-icon">
                                <i class="ri-time-line" style="font-size: 1.25rem;"></i>
                            </div>
                            <div>
                                <h6 class="faq-question">Berapa lama proses sertifikasi halal?</h6>
                                <p class="faq-answer">Umumnya membutuhkan waktu hingga 30 hari kerja sejak seluruh
                                    dokumen persyaratan dinyatakan lengkap.</p>
                            </div>
                        </div>
                        <div class="faq-card">
                            <div class="faq-icon">
                                <i class="ri-file-list-3-line" style="font-size: 1.25rem;"></i>
                            </div>
                            <div>
                                <h6 class="faq-question">Apa saja dokumen yang diperlukan?</h6>
                                <p class="faq-answer">Dokumen utama meliputi NIB, daftar nama produk, daftar bahan baku
                                    beserta sertifikat halalnya, dan diagram alir proses produksi.</p>
                            </div>
                        </div>
                        <div class="faq-card">
                            <div class="faq-icon">
                                <i class="ri-money-dollar-circle-line" style="font-size: 1.25rem;"></i>
                            </div>
                            <div>
                                <h6 class="faq-question">Berapa biaya sertifikasi halal?</h6>
                                <p class="faq-answer">Biaya bergantung pada jenis layanan dan kapasitas bisnis Anda.
                                    Hubungi tim kami untuk konsultasi biaya secara gratis.</p>
                            </div>
                        </div>
                        <div class="faq-card">
                            <div class="faq-icon">
                                <i class="ri-graduation-cap-line" style="font-size: 1.25rem;"></i>
                            </div>
                            <div>
                                <h6 class="faq-question">Apakah ada pelatihan Penyelia Halal?</h6>
                                <p class="faq-answer">Ya, kami menyediakan paket pendampingan lengkap yang mencakup
                                    pembekalan dan penunjukan Penyelia Halal bersertifikat.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== CTA ========== -->
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
                                    <h3 class="text-white fw-bold mb-2">Ingin Mendaftarkan Produk Anda?</h3>
                                    <p class="text-white mb-0" style="opacity:0.85;max-width:580px;">
                                        Jangan tunda lagi. Daftarkan produk Anda sekarang untuk meraih kepercayaan
                                        konsumen lebih cepat.
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
