@extends('layouts.guest')
@section('content')
    <!-- auth-page wrapper -->
    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>
        <!-- auth-page content -->
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card overflow-hidden card-bg-fill galaxy-border-none">
                            <div class="row g-0">
                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4 auth-one-bg h-100">
                                        <div class="bg-overlay"></div>
                                        <div class="position-relative h-100 d-flex flex-column">
                                            <div class="mb-4">
                                                <a href="/" class="d-block">
                                                    <img src="{{ asset('storage/sangkala.png') }}" alt=""
                                                        height="50">
                                                </a>
                                            </div>
                                            <div class="mt-auto">
                                                <div class="mb-3">
                                                    <i class="ri-double-quotes-l display-4 text-success"></i>
                                                </div>

                                                <div id="qoutescarouselIndicators" class="carousel slide"
                                                    data-bs-ride="carousel">
                                                    <div class="carousel-indicators">
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators"
                                                            data-bs-slide-to="0" class="active" aria-current="true"
                                                            aria-label="Slide 1"></button>
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators"
                                                            data-bs-slide-to="1" aria-label="Slide 2"></button>
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators"
                                                            data-bs-slide-to="2" aria-label="Slide 3"></button>
                                                    </div>
                                                    <div class="carousel-inner text-center text-white-50 pb-5">
                                                        <div class="carousel-item active">
                                                            <p class="fs-15 fst-italic">" Great! Clean code, clean design,
                                                                easy for customization. Thanks very much! "</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">" The theme is really great with an
                                                                amazing customer support."</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">" Great! Clean code, clean design,
                                                                easy for customization. Thanks very much! "</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- end carousel -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- end col -->

                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4">
                                        <div>
                                            <h5 class="text-primary">Yayasan Permata Bakti Pertiwi</h5>
                                            <p class="text-muted">Silakan login untuk melanjutkan ke akun Anda.</p>
                                        </div>

                                        <!-- Session Status -->
                                        <x-auth-session-status class="mb-4" :status="session('status')" />

                                        <!-- Alert untuk kesalahan login -->
                                        @if ($errors->any())
                                            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                                <div>
                                                    <strong>Login Gagal!</strong>
                                                    @if ($errors->has('email'))
                                                        Email atau password yang Anda masukkan salah.
                                                    @elseif($errors->has('password'))
                                                        Password yang Anda masukkan salah.
                                                    @elseif($errors->has('throttle'))
                                                        Terlalu banyak percobaan login. Silakan coba lagi nanti.
                                                    @else
                                                        Terjadi kesalahan saat login. Silakan coba lagi.
                                                    @endif
                                                </div>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                    aria-label="Close"></button>
                                            </div>
                                        @endif

                                        <div class="mt-4">
                                            <form method="POST" action="{{ route('login') }}">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">{{ __('Email') }}</label>
                                                    <input type="email"
                                                        class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                                        id="email" name="email" value="{{ old('email') }}"
                                                        placeholder="Masukkan email Anda" required autofocus
                                                        autocomplete="username">
                                                </div>

                                                <div class="mb-3">
                                                    <div class="float-end">
                                                        @if (Route::has('password.request'))
                                                            <a href="{{ route('password.request') }}"
                                                                class="text-muted">Lupa password?</a>
                                                        @endif
                                                    </div>
                                                    <label class="form-label" for="password">{{ __('Password') }}</label>
                                                    <div class="position-relative auth-pass-inputgroup mb-3">
                                                        <input type="password"
                                                            class="form-control pe-5 password-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                                            placeholder="Masukkan password Anda" id="password"
                                                            name="password" required autocomplete="current-password">
                                                        <button
                                                            class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon"
                                                            type="button" id="password-addon"><i
                                                                class="ri-eye-fill align-middle"></i></button>
                                                    </div>
                                                </div>

                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="remember_me"
                                                        name="remember">
                                                    <label class="form-check-label" for="remember_me">Ingat saya</label>
                                                </div>

                                                <div class="mt-4">
                                                    <button class="btn btn-success w-100" type="submit">Masuk</button>
                                                </div>

                                                {{-- <div class="mt-4 text-center">
                                                    <div class="signin-other-title">
                                                        <h5 class="fs-13 mb-4 title">Masuk dengan</h5>
                                                    </div>

                                                    <div>
                                                        <button type="button"
                                                            class="btn btn-primary btn-icon waves-effect waves-light"><i
                                                                class="ri-facebook-fill fs-16"></i></button>
                                                        <button type="button"
                                                            class="btn btn-danger btn-icon waves-effect waves-light"><i
                                                                class="ri-google-fill fs-16"></i></button>
                                                        <button type="button"
                                                            class="btn btn-dark btn-icon waves-effect waves-light"><i
                                                                class="ri-github-fill fs-16"></i></button>
                                                        <button type="button"
                                                            class="btn btn-info btn-icon waves-effect waves-light"><i
                                                                class="ri-twitter-fill fs-16"></i></button>
                                                    </div>
                                                </div> --}}
                                            </form>
                                        </div>

                                        {{-- <div class="mt-5 text-center">
                                            <p class="mb-0">Belum memiliki akun? <a href="{{ route('register') }}"
                                                    class="fw-semibold text-primary text-decoration-underline">Daftar</a>
                                            </p>
                                        </div> --}}
                                    </div>
                                </div>
                                <!-- end col -->
                            </div>
                            <!-- end row -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->

        <!-- footer -->
        <footer class="footer galaxy-border-none">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0">&copy;
                                <script>
                                    document.write(new Date().getFullYear())
                                </script> Yayasan Permata Bakti Pertiwi. All Rights Reserved.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->
    </div>
    <!-- end auth-page-wrapper -->

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Error handler for missing dependencies -->
    <script>
        // Create a safety wrapper for JavaScript libraries
        function safeExecute(fn) {
            try {
                fn();
            } catch (e) {
                console.warn('Error suppressed:', e.message);
            }
        }

        // Safe initializers for various components
        document.addEventListener('DOMContentLoaded', function() {
            // Password visibility toggle
            var passwordAddon = document.getElementById('password-addon');
            if (passwordAddon) {
                passwordAddon.addEventListener('click', function() {
                    var passwordInput = document.getElementById('password');
                    if (passwordInput.type === "password") {
                        passwordInput.type = "text";
                        this.querySelector('i').classList.remove('ri-eye-fill');
                        this.querySelector('i').classList.add('ri-eye-off-fill');
                    } else {
                        passwordInput.type = "password";
                        this.querySelector('i').classList.remove('ri-eye-off-fill');
                        this.querySelector('i').classList.add('ri-eye-fill');
                    }
                });
            }

            // Handle Bootstrap components safely
            if (typeof bootstrap !== 'undefined') {
                // Initialize alerts if they exist
                var alertList = document.querySelectorAll('.alert')
                if (alertList.length > 0) {
                    alertList.forEach(function(alert) {
                        new bootstrap.Alert(alert);
                    });
                }

                // Initialize carousel if it exists
                var carouselElement = document.getElementById('qoutescarouselIndicators');
                if (carouselElement) {
                    var carousel = new bootstrap.Carousel(carouselElement, {
                        interval: 3000,
                        wrap: true
                    });
                }
            }

            // Patch any missing functions from the template
            window.SimpleBar = window.SimpleBar || function() {
                return {
                    recalculate: function() {}
                };
            };
        });

        // Error handling for missing elements
        window.addEventListener('error', function(e) {
            // Prevent errors from missing elements in app.js and simplebar.min.js
            if (e.message && (e.message.includes('querySelector(...) is null') ||
                    e.message.includes('this.el is null'))) {
                console.warn('Ignoring non-critical error:', e.message);
                e.preventDefault();
                e.stopPropagation();
                return true;
            }
        }, true);

        // Prevent global errors
        window.onerror = function(msg, url, line, col, error) {
            // Check if error is related to missing elements
            if (msg.includes('querySelector') || msg.includes('is null') ||
                url.includes('simplebar.min.js') || url.includes('dashboard-projects.init.js')) {
                console.warn('Ignoring JS error:', msg);
                return true; // prevents the firing of the default event handler
            }
        };

        // Define fallbacks for common JavaScript objects used in the template
        window.SimpleBar = window.SimpleBar || function() {};
        window.Waves = window.Waves || {
            attach: function() {},
            init: function() {}
        };
        window.List = window.List || function() {};
        window.Swiper = window.Swiper || function() {};
    </script>
@endsection
