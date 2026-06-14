<footer class="footer-section">
    <div class="container position-relative" style="z-index: 1;">
        <div class="row">
            <div class="col-lg-4 mt-4">
                <img src="{{ asset('storage/' . $settingWebsite->favicon) }}" alt="logo light" height="26"
                    class="mb-3" />
                <p class="text-white text-opacity-60 mb-4" style="max-width: 320px; line-height: 1.8;">
                    Layanan sertifikasi halal terpercaya untuk UMKM Indonesia. Membantu pelaku usaha mendapatkan
                    sertifikat halal dengan proses mudah dan cepat.
                </p>
                <div class="d-flex gap-2">
                    @foreach ($socialMedia ?? [] as $social)
                        <a href="{{ $social->url }}" target="_blank" class="social-btn btn"
                            style="background: {{ $social->color }}; color: #fff;">
                            <i class="{{ $social->icon }}"></i>
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-7 ms-lg-auto">
                <div class="row">
                    <div class="col-sm-4 mt-4">
                        <h6 class="footer-title">Tautan</h6>
                        <ul class="list-unstyled footer-list">
                            <li class="mb-2"><a href="{{ route('publik.home') }}" class="footer-link"><i
                                        class="ri-arrow-right-s-line me-1 align-bottom"></i>Home</a></li>
                            <li class="mb-2"><a href="{{ route('publik.about') }}" class="footer-link"><i
                                        class="ri-arrow-right-s-line me-1 align-bottom"></i>Tentang Kami</a></li>
                            <li class="mb-2"><a href="{{ route('publik.articles.index') }}" class="footer-link"><i
                                        class="ri-arrow-right-s-line me-1 align-bottom"></i>Artikel</a></li>
                            <li class="mb-2"><a href="{{ route('publik.recruitment.index') }}" class="footer-link"><i
                                        class="ri-arrow-right-s-line me-1 align-bottom"></i>Lowongan Pekerjaan</a></li>
                            <li class="mb-2"><a href="{{ route('publik.contact') }}" class="footer-link"><i
                                        class="ri-arrow-right-s-line me-1 align-bottom"></i>Kontak</a></li>
                        </ul>
                    </div>
                    <div class="col-sm-4 mt-4">
                        <h6 class="footer-title">Kontak</h6>
                        <ul class="list-unstyled footer-list">
                            <li class="mb-2"><a href="#" class="footer-link"><i
                                        class="ri-map-pin-2-line me-1 align-bottom"></i>Jl. DR. Ide Anak Agung Gde Agung
                                    Blok Kav. E 4.2 No.2, RT.5/RW.2, Kuningan, Kuningan Tim., Kecamatan Setiabudi, Kota
                                    Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12950, Indonesia</a></li>
                            <li class="mb-2"><a href="mailto:info@kawulohalal.id" class="footer-link"><i
                                        class="ri-mail-line me-1 align-bottom"></i>info@kawulohalal.id</a></li>
                            <li class="mb-2"><a href="tel:+6281234567890" class="footer-link"><i
                                        class="ri-phone-line me-1 align-bottom"></i>+62 897-6774-482</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-5 pt-4 border-top border-white border-opacity-10">
            <div class="col-12 text-center">
                <p class="text-white text-opacity-50 mb-0 small">&copy; {{ date('Y') }} Kawulo Halal — Yayasan
                    Permata Bakti Pertiwi.</p>
            </div>
        </div>
    </div>
</footer>
<button onclick="topFunction()" class="btn btn-kh btn-icon landing-back-top" id="back-to-top">
    <i class="ri-arrow-up-line"></i>
</button>
