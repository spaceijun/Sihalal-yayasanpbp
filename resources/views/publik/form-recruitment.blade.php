@extends('layouts.guest')
@section('title', 'Form Recruitment')
@section('content')
    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card overflow-hidden m-0 card-bg-fill galaxy-border-none">
                            <div class="row justify-content-center g-0">
                                <!-- Left Side - Info Section -->
                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4 auth-one-bg h-100">
                                        <div class="bg-overlay"></div>
                                        <div class="position-relative h-100 d-flex flex-column">
                                            <div class="mb-4">
                                                <a href="/" class="d-block">
                                                    <img src="{{ asset('assets/images/logo-pbp.png') }}" alt="Logo"
                                                        height="100">
                                                </a>
                                            </div>
                                            <div class="mt-auto">
                                                <div class="mb-3">
                                                    <i class="ri-double-quotes-l display-4 text-success"></i>
                                                </div>
                                                <div id="qoutescarouselIndicators" class="carousel slide"
                                                    data-bs-ride="carousel">
                                                    <div class="carousel-inner text-center text-white-50 pb-5">
                                                        <div class="carousel-item active">
                                                            <p class="fs-15 fst-italic">" Bergabunglah dengan tim kami untuk
                                                                membuat perubahan. "</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">" Kesempatan berkarir bersama
                                                                program yang berdampak."</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">" Pengalaman dan dedikasi adalah
                                                                kunci kesuksesan. "</p>
                                                        </div>
                                                    </div>
                                                    <div class="carousel-indicators">
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators"
                                                            data-bs-slide-to="0" class="active"></button>
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators"
                                                            data-bs-slide-to="1"></button>
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators"
                                                            data-bs-slide-to="2"></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Side - Form Section -->
                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4">
                                        <!-- SUCCESS MESSAGE -->
                                        @if (session('success'))
                                            <div class="alert alert-success alert-dismissible alert-label-icon label-arrow fade show"
                                                role="alert">
                                                <i class="ri-check-double-line label-icon"></i><strong>Berhasil!</strong>
                                                {{ session('success') }}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                    aria-label="Close"></button>
                                            </div>
                                        @endif

                                        <!-- ERROR MESSAGE -->
                                        @if (session('error'))
                                            <div class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show"
                                                role="alert">
                                                <i class="ri-error-warning-line label-icon"></i><strong>Gagal!</strong>
                                                {{ session('error') }}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                    aria-label="Close"></button>
                                            </div>
                                        @endif

                                        <div>
                                            <h5 class="text-primary">Form Recruitment</h5>
                                            <p class="text-muted">Lengkapi data diri Anda dengan detail.</p>
                                        </div>

                                        <form method="POST" action="{{ route('recruitment.store') }}"
                                            enctype="multipart/form-data" class="mt-4" id="formRecruitment">
                                            @csrf

                                            <!-- Nama Lengkap -->
                                            <div class="mb-3">
                                                <label for="nama_lengkap" class="form-label">Nama Lengkap <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" id="nama_lengkap" name="nama_lengkap"
                                                    class="form-control text-uppercase @error('nama_lengkap') is-invalid @enderror"
                                                    value="{{ old('nama_lengkap', $recruitment?->nama_lengkap) }}" required
                                                    autofocus placeholder="Masukkan nama lengkap"
                                                    style="text-transform: uppercase;">
                                                <small class="text-muted">Nama akan otomatis diubah ke huruf besar</small>
                                                @error('nama_lengkap')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Telephone -->
                                            <div class="mb-3">
                                                <label for="telephone" class="form-label">No. Telepon <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" id="telephone" name="telephone"
                                                    class="form-control @error('telephone') is-invalid @enderror"
                                                    value="{{ old('telephone', $recruitment?->telephone) }}" required
                                                    placeholder="Contoh: 081234567890" maxlength="15" pattern="[0-9]{10,15}"
                                                    inputmode="numeric">
                                                <small class="text-muted">Masukkan nomor telepon yang aktif</small>
                                                @error('telephone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Alamat Lengkap -->
                                            <div class="mb-3">
                                                <label for="alamat_lengkap" class="form-label">Alamat Lengkap <span
                                                        class="text-danger">*</span></label>
                                                <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3"
                                                    class="form-control @error('alamat_lengkap') is-invalid @enderror" required placeholder="Masukkan alamat lengkap">{{ old('alamat_lengkap', $recruitment?->alamat_lengkap) }}</textarea>
                                                @error('alamat_lengkap')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Pendidikan Terakhir -->
                                            <div class="mb-3">
                                                <label for="pendidikan_terakhir" class="form-label">Pendidikan Terakhir
                                                    <span class="text-danger">*</span></label>
                                                <select id="pendidikan_terakhir" name="pendidikan_terakhir"
                                                    class="form-control @error('pendidikan_terakhir') is-invalid @enderror"
                                                    required>
                                                    <option value="">-- Pilih Pendidikan Terakhir --</option>
                                                    @php
                                                        $pendidikanList = [
                                                            'SD / Paket A / Sederajat',
                                                            'SMP / Paket B / Sederajat',
                                                            'SMA / SMK / Paket C / Sederajat',
                                                            'D1',
                                                            'D2',
                                                            'D3',
                                                            'S1',
                                                            'S2',
                                                            'S3',
                                                        ];
                                                    @endphp
                                                    @foreach ($pendidikanList as $pendidikan)
                                                        <option value="{{ $pendidikan }}"
                                                            {{ old('pendidikan_terakhir', $recruitment?->pendidikan_terakhir) == $pendidikan ? 'selected' : '' }}>
                                                            {{ $pendidikan }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('pendidikan_terakhir')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Pengalaman -->
                                            <div class="mb-3">
                                                <label for="pengalaman" class="form-label">Pengalaman <span
                                                        class="text-danger">*</span></label>
                                                <textarea id="pengalaman" name="pengalaman" rows="3"
                                                    class="form-control @error('pengalaman') is-invalid @enderror" required
                                                    placeholder="Jelaskan pengalaman kerja Anda">{{ old('pengalaman', $recruitment?->pengalaman) }}</textarea>
                                                <small class="text-muted">Tuliskan pengalaman kerja yang relevan</small>
                                                @error('pengalaman')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Rekomendasi -->
                                            <div class="mb-3">
                                                <label for="rekomendasi" class="form-label">Rekomendasi</label>
                                                <select id="rekomendasi" name="rekomendasi"
                                                    class="form-control @error('rekomendasi') is-invalid @enderror">
                                                    <option value="">-- Pilih Rekomendasi (Opsional) --</option>
                                                    @php
                                                        $daftarRekomendasi = [
                                                            'Adi Tarman',
                                                            'M. Faizun Aziz',
                                                            'Ade Sofyan',
                                                            'Agil Praditya Putu Yazier',
                                                            'Ahmad Nurohim',
                                                            'Zaenal Arifin',
                                                        ];
                                                    @endphp
                                                    @foreach ($daftarRekomendasi as $nama)
                                                        <option value="{{ $nama }}"
                                                            {{ old('rekomendasi', $recruitment?->rekomendasi) == $nama ? 'selected' : '' }}>
                                                            {{ $nama }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">Jika tidak ada, kosongkan saja</small>
                                                @error('rekomendasi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Upload Dokumentasi Section -->
                                            <div class="mb-3">
                                                <h6 class="text-muted mb-3">Upload Dokumentasi <span
                                                        class="text-danger">*</span></h6>

                                                <!-- Foto Diri -->
                                                <div class="mb-3">
                                                    <label for="foto_diri" class="form-label">Foto Diri (3x4) <span
                                                            class="text-danger">*</span></label>
                                                    <input type="file" id="foto_diri" name="foto_diri"
                                                        class="form-control @error('foto_diri') is-invalid @enderror"
                                                        accept="image/*" required>
                                                    <small class="text-muted">Format: JPG, PNG, JPEG. Max: 10MB</small>
                                                    @error('foto_diri')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <!-- Foto KTP -->
                                                <div class="mb-3">
                                                    <label for="foto_ktp" class="form-label">Foto KTP <span
                                                            class="text-danger">*</span></label>
                                                    <input type="file" id="foto_ktp" name="foto_ktp"
                                                        class="form-control @error('foto_ktp') is-invalid @enderror"
                                                        accept="image/*" required>
                                                    <small class="text-muted">Format: JPG, PNG, JPEG. Max: 10MB</small>
                                                    @error('foto_ktp')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Hidden Status Field -->
                                            <input type="hidden" name="status" value="Melamar">

                                            <!-- Submit Button -->
                                            <div class="mt-4">
                                                <button class="btn btn-success w-100" type="submit" id="submitBtn">
                                                    <i class="ri-send-plane-line me-1"></i> Kirim Lamaran
                                                </button>
                                            </div>
                                        </form>

                                        {{-- <div class="mt-4 text-center">
                                            <p class="mb-0">
                                                <a href="{{ route('home') }}"
                                                    class="fw-semibold text-primary text-decoration-underline">
                                                    <i class="ri-arrow-left-line me-1"></i> Kembali ke Beranda
                                                </a>
                                            </p>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formRecruitment');
            const submitBtn = document.getElementById('submitBtn');
            const namaLengkapInput = document.getElementById('nama_lengkap');
            const telephoneInput = document.getElementById('telephone');

            // Check if all elements exist before proceeding
            if (!form || !submitBtn || !namaLengkapInput || !telephoneInput) {
                console.error('Required form elements not found');
                return;
            }

            // ============================================
            // AUTO HIDE SUCCESS/ERROR ALERTS
            // ============================================
            const alerts = document.querySelectorAll('.alert');
            if (alerts.length > 0) {
                alerts.forEach(alert => {
                    setTimeout(() => {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }, 5000);
                });
            }

            // ============================================
            // NAMA LENGKAP - AUTO UPPERCASE
            // ============================================
            namaLengkapInput.addEventListener('input', function(e) {
                this.value = this.value.toUpperCase();
            });

            namaLengkapInput.addEventListener('paste', function(e) {
                setTimeout(() => {
                    this.value = this.value.toUpperCase();
                }, 10);
            });

            // ============================================
            // TELEPHONE VALIDATION
            // ============================================
            telephoneInput.addEventListener('input', function(e) {
                // Only allow numbers
                let value = this.value.replace(/[^0-9]/g, '');

                // Enforce maximum 15 digits
                if (value.length > 15) {
                    value = value.slice(0, 15);
                }

                this.value = value;
            });

            telephoneInput.addEventListener('keypress', function(e) {
                if (e.key < '0' || e.key > '9') {
                    e.preventDefault();
                }
            });

            telephoneInput.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = (e.clipboardData || window.clipboardData).getData('text');
                const numericData = pastedData.replace(/[^0-9]/g, '').slice(0, 15);
                this.value = numericData;

                const event = new Event('input', {
                    bubbles: true
                });
                this.dispatchEvent(event);
            });

            // ============================================
            // FORM SUBMISSION
            // ============================================
            form.addEventListener('submit', function(e) {
                // Validate telephone
                const telephoneValue = telephoneInput.value;

                if (telephoneValue.length < 10 || telephoneValue.length > 15) {
                    e.preventDefault();
                    telephoneInput.classList.add('is-invalid');

                    telephoneInput.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    telephoneInput.focus();

                    showToast('error', 'Nomor telepon harus antara 10-15 digit!');
                    return false;
                }

                // Disable submit button to prevent double submission
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
            });

            // ============================================
            // IMAGE FILE VALIDATION
            // ============================================
            const imageInputs = ['foto_diri', 'foto_ktp'];

            imageInputs.forEach(inputId => {
                const input = document.getElementById(inputId);
                if (input) {
                    input.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            if (file.size > 10485760) {
                                showToast('error',
                                    `Ukuran file ${inputId.replace(/_/g, ' ')} maksimal 10MB!`);
                                this.value = '';
                                return;
                            }

                            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                            if (!allowedTypes.includes(file.type)) {
                                showToast('error',
                                    `Format file ${inputId.replace(/_/g, ' ')} harus JPG, JPEG, atau PNG!`
                                );
                                this.value = '';
                                return;
                            }
                        }
                    });
                }
            });

            // ============================================
            // TOAST NOTIFICATION FUNCTION
            // ============================================
            function showToast(type, message) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: type,
                        title: type === 'success' ? 'Berhasil!' : (type === 'warning' ? 'Peringatan!' :
                            'Gagal!'),
                        text: message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                } else {
                    alert(message);
                }
            }
        });
    </script>
@endsection
