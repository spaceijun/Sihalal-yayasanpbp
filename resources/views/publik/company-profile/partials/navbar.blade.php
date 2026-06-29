<nav class="navbar navbar-expand-lg navbar-landing fixed-top navbar-kh" id="navbar">
    <div class="container">
        <a class="navbar-brand" href="{{ route('publik.home') }}">
            <img src="{{ asset('assets/images/logo-dark.png') }}" class="card-logo card-logo-light" alt="Kawulo Halal"
                height="28">
        </a>
        <button class="navbar-toggler p-2 border-0" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="ri-menu-3-line fs-24"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mx-auto mt-2 mt-lg-0">
                <li class="nav-item">
                    <a class="nav-link nav-link-kh {{ Route::is('publik.home') ? 'active' : '' }}"
                        href="{{ route('publik.home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-kh {{ Route::is('publik.about') ? 'active' : '' }}"
                        href="{{ route('publik.about') }}">Tentang Kami</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-kh {{ Route::is('publik.articles.*') ? 'active' : '' }}"
                        href="{{ route('publik.articles.index') }}">Artikel</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-kh {{ Route::is('publik.recruitment.*') || Route::is('recruitment.*') ? 'active' : '' }}"
                        href="{{ route('publik.recruitment.index') }}">Lowongan Pekerjaan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-kh {{ Route::is('publik.contact') ? 'active' : '' }}"
                        href="{{ route('publik.contact') }}">Kontak</a>
                </li>
            </ul>
            <div class="d-flex gap-2 flex-wrap justify-content-center mt-3 mt-lg-0">
                <a href="{{ route('publik.contact') }}" class="btn btn-kh px-4 py-2 fs-13">
                    <i class="ri-edit-line align-bottom me-1"></i> Daftar Sekarang
                </a>
                @auth
                    @php
                        $dashboardUrl = match (auth()->user()->role) {
                            'superadmin' => '/superadmin',
                            'koordinator' => '/koordinator',
                            'data_entry' => '/data-entry',
                            'enumerator' => '/enumerator',
                            'admin_umum' => '/admin-umum',
                            default => '/dashboard',
                        };
                    @endphp
                    <a href="{{ url($dashboardUrl) }}" class="btn btn-kh-outline px-4 py-2 fs-13">
                        <i class="ri-dashboard-line align-bottom me-1"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-kh-outline px-4 py-2 fs-13">
                        <i class="ri-user-line align-bottom me-1"></i> Login
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
<div class="vertical-overlay" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent.show"></div>

<style>
    @media (max-width: 991.98px) {
        .navbar-collapse {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(24px);
            border-radius: 20px;
            padding: 1.5rem;
            margin-top: 1rem;
            box-shadow: 0 15px 40px rgba(47, 143, 230, 0.12);
            border: 1px solid rgba(47, 143, 230, 0.08);
        }
        .nav-link-kh {
            padding: 0.75rem 1rem !important;
        }
        .nav-link-kh::after {
            bottom: 4px;
            left: 1rem;
            transform: none;
        }
        .nav-link-kh:hover::after,
        .nav-link-kh.active::after {
            width: 30px;
        }
    }
</style>
