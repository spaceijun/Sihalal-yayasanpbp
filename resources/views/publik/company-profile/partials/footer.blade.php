<footer class="footer-section">
    <div class="container position-relative" style="z-index: 1;">
        <div class="row">
            <div class="col-lg-4 mt-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <img src="{{ asset('storage/' . $settingWebsite->favicon) }}" alt="logo light" height="28" />
                    <span class="text-white fw-bold fs-16 tracking-wide">{{ $settingWebsite->title ?? 'Kawulo Halal' }}</span>
                </div>
                <p class="text-white text-opacity-60 mb-4" style="max-width: 320px; line-height: 1.8; font-size: 0.95rem;">
                    Layanan sertifikasi halal terpercaya untuk UMKM Indonesia. Membantu pelaku usaha mendapatkan
                    sertifikat halal dengan proses mudah dan cepat.
                </p>
                <div class="d-flex gap-2">
                    @foreach ($socialMedia ?? [] as $social)
                        <a href="{{ $social->url }}" target="_blank" class="social-btn-modern" title="{{ $social->name ?? 'Social Media' }}">
                            <i class="{{ $social->icon }}"></i>
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-7 ms-lg-auto">
                <div class="row">
                    <div class="col-sm-5 mt-4">
                        <h6 class="footer-title text-uppercase tracking-wider fs-12 text-white text-opacity-90">Tautan</h6>
                        <ul class="list-unstyled footer-list">
                            <li class="mb-2"><a href="{{ route('publik.home') }}" class="footer-link"><i
                                        class="ri-arrow-right-s-line me-1 align-bottom text-kh"></i>Home</a></li>
                            <li class="mb-2"><a href="{{ route('publik.about') }}" class="footer-link"><i
                                        class="ri-arrow-right-s-line me-1 align-bottom text-kh"></i>Tentang Kami</a></li>
                            <li class="mb-2"><a href="{{ route('publik.articles.index') }}" class="footer-link"><i
                                        class="ri-arrow-right-s-line me-1 align-bottom text-kh"></i>Artikel</a></li>
                            <li class="mb-2"><a href="{{ route('publik.recruitment.index') }}" class="footer-link"><i
                                        class="ri-arrow-right-s-line me-1 align-bottom text-kh"></i>Lowongan Pekerjaan</a></li>
                            <li class="mb-2"><a href="{{ route('publik.contact') }}" class="footer-link"><i
                                        class="ri-arrow-right-s-line me-1 align-bottom text-kh"></i>Kontak</a></li>
                        </ul>
                    </div>
                    <div class="col-sm-7 mt-4">
                        <h6 class="footer-title text-uppercase tracking-wider fs-12 text-white text-opacity-90">Kontak Kami</h6>
                        <ul class="list-unstyled footer-list">
                            <li class="mb-3">
                                <a href="https://maps.google.com" target="_blank" class="footer-link-contact">
                                    <i class="ri-map-pin-2-line text-kh"></i>
                                    <span>Jl. DR. Ide Anak Agung Gde Agung Blok Kav. E 4.2 No.2, RT.5/RW.2, Kuningan, Kuningan Tim., Kecamatan Setiabudi, Kota Jakarta Selatan, DKI Jakarta 12950</span>
                                </a>
                            </li>
                            <li class="mb-3">
                                <a href="mailto:info@kawulohalal.id" class="footer-link-contact">
                                    <i class="ri-mail-line text-kh"></i>
                                    <span>info@kawulohalal.id</span>
                                </a>
                            </li>
                            <li class="mb-3">
                                <a href="tel:+628976774482" class="footer-link-contact">
                                    <i class="ri-phone-line text-kh"></i>
                                    <span>+62 897-6774-482</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-5 pt-4 border-top border-white border-opacity-10">
            <div class="col-12 text-center">
                <p class="text-white text-opacity-50 mb-0 small">&copy; {{ date('Y') }} {{ $settingWebsite->title ?? 'Kawulo Halal' }} — Yayasan Permata Bakti Pertiwi.</p>
            </div>
        </div>
    </div>
</footer>

<button onclick="topFunction()" class="btn btn-kh btn-icon landing-back-top" id="back-to-top">
    <i class="ri-arrow-up-line"></i>
</button>

<style>
    .footer-section::after {
        content: '';
        position: absolute;
        bottom: -20%;
        left: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(25, 180, 160, 0.15) 0%, transparent 70%);
        pointer-events: none;
    }
    .social-btn-modern {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: rgba(255, 255, 255, 0.7) !important;
        transition: var(--kh-transition);
        font-size: 1.1rem;
    }
    .social-btn-modern:hover {
        background: var(--kh-primary) !important;
        color: #fff !important;
        border-color: var(--kh-primary) !important;
        transform: translateY(-4px);
        box-shadow: var(--kh-shadow-lg);
    }
    .footer-link-contact {
        display: flex;
        align-items: flex-start !important;
        gap: 0.75rem;
        color: rgba(255, 255, 255, 0.6);
        text-decoration: none;
        transition: var(--kh-transition);
        line-height: 1.6;
        font-size: 0.95rem;
    }
    .footer-link-contact i {
        margin-top: 3px;
        font-size: 1.1rem;
        transition: var(--kh-transition);
    }
    .footer-link-contact:hover {
        color: #fff;
    }
    .footer-link-contact:hover i {
        color: var(--kh-primary-light) !important;
    }
</style>
