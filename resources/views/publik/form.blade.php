@extends('layouts.guest')
@section('content')
    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card overflow-hidden m-0 card-bg-fill galaxy-border-none">
                            <div class="row justify-content-center g-0">
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
                                                            <p class="fs-15 fst-italic">" Data yang akurat adalah kunci
                                                                keberhasilan program. "</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">" Dokumentasi lapangan yang lengkap
                                                                dan terstruktur."</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">" Kemudahan dalam pengelolaan data
                                                                pendamping. "</p>
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
                                <!-- Form -->
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
                                            <h5 class="text-primary">Form Data Lapangan</h5>
                                            <p class="text-muted">Lengkapi data lapangan dengan detail.</p>
                                        </div>
                                        <form method="POST" action="{{ route('formulir.halal.store') }}"
                                            enctype="multipart/form-data" class="mt-4" id="formDataLapangan">
                                            @csrf

                                            <!-- Enumerator -->
                                            <div class="mb-3">
                                                <label for="enumerator_id" class="form-label">Nama Pendamping <span
                                                        class="text-danger">*</span></label>
                                                <select id="enumerator_id" name="enumerator_id"
                                                    class="form-control @error('enumerator_id') is-invalid @enderror"
                                                    required>
                                                    <option value="">-- Pilih Pendamping --</option>
                                                    @foreach ($enumerators as $enumerator)
                                                        <option value="{{ $enumerator->id }}"
                                                            {{ old('enumerator_id') == $enumerator->id ? 'selected' : '' }}>
                                                            {{ $enumerator->nama_lengkap }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('enumerator_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Nama PU -->
                                            <div class="mb-3">
                                                <label for="nama_pu" class="form-label">Nama PU <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" id="nama_pu" name="nama_pu"
                                                    class="form-control text-uppercase @error('nama_pu') is-invalid @enderror"
                                                    value="{{ old('nama_pu') }}" required autofocus
                                                    placeholder="Masukkan nama PU" style="text-transform: uppercase;">
                                                <small class="text-muted">Nama akan otomatis diubah ke huruf besar</small>
                                                @error('nama_pu')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- NIK with Validation -->
                                            <div class="mb-3">
                                                <label for="nik" class="form-label">NIK <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" id="nik" name="nik"
                                                    class="form-control @error('nik') is-invalid @enderror"
                                                    value="{{ old('nik') }}" required
                                                    placeholder="Masukkan NIK (16 digit)" maxlength="16" pattern="[0-9]{16}"
                                                    inputmode="numeric">
                                                <div class="d-flex justify-content-between align-items-center mt-1">
                                                    <small class="text-muted" id="nikCounter">
                                                        <i class="ri-information-line"></i> 0/16 digit
                                                    </small>
                                                    <small class="text-muted" id="nikStatus">Belum lengkap</small>
                                                </div>
                                                @error('nik')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <div class="invalid-feedback" id="nikError">
                                                    NIK harus tepat 16 digit angka
                                                </div>
                                            </div>

                                            <!-- RT & RW -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="rt" class="form-label">RT <span
                                                                class="text-danger">*</span></label>
                                                        <input type="number" id="rt" name="rt"
                                                            class="form-control @error('rt') is-invalid @enderror"
                                                            value="{{ old('rt') }}" required placeholder="RT">
                                                        @error('rt')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="rw" class="form-label">RW <span
                                                                class="text-danger">*</span></label>
                                                        <input type="number" id="rw" name="rw"
                                                            class="form-control @error('rw') is-invalid @enderror"
                                                            value="{{ old('rw') }}" required placeholder="RW">
                                                        @error('rw')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Alamat -->
                                            <div class="mb-3">
                                                <label for="alamat" class="form-label">Alamat <span
                                                        class="text-danger">*</span></label>
                                                <textarea id="alamat" name="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror"
                                                    required placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
                                                @error('alamat')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Titik Koordinat -->
                                            <div class="mb-3">
                                                <label for="titik_koordinat" class="form-label">Titik Koordinat <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" id="titik_koordinat" name="titik_koordinat"
                                                    class="form-control @error('titik_koordinat') is-invalid @enderror"
                                                    value="{{ old('titik_koordinat') }}" required
                                                    placeholder="Contoh: -7.123456, 110.123456">
                                                @error('titik_koordinat')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Upload Foto Section -->
                                            <div class="mb-3">
                                                <h6 class="text-muted mb-3">Upload Dokumentasi <span
                                                        class="text-danger">*</span></h6>

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

                                                <!-- Foto Rumah -->
                                                <div class="mb-3">
                                                    <label for="foto_rumah" class="form-label">Foto Rumah <span
                                                            class="text-danger">*</span></label>
                                                    <input type="file" id="foto_rumah" name="foto_rumah"
                                                        class="form-control @error('foto_rumah') is-invalid @enderror"
                                                        accept="image/*" required>
                                                    <small class="text-muted">Format: JPG, PNG, JPEG. Max: 10MB</small>
                                                    @error('foto_rumah')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <!-- Foto Pendamping -->
                                                <div class="mb-3">
                                                    <label for="foto_pendamping" class="form-label">Foto Pendamping <span
                                                            class="text-danger">*</span></label>
                                                    <input type="file" id="foto_pendamping" name="foto_pendamping"
                                                        class="form-control @error('foto_pendamping') is-invalid @enderror"
                                                        accept="image/*" required>
                                                    <small class="text-muted">Format: JPG, PNG, JPEG. Max: 10MB</small>
                                                    @error('foto_pendamping')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <!-- Foto Proses -->
                                                <div class="mb-3">
                                                    <label for="foto_proses" class="form-label">Foto Proses <span
                                                            class="text-danger">*</span></label>
                                                    <input type="file" id="foto_proses" name="foto_proses"
                                                        class="form-control @error('foto_proses') is-invalid @enderror"
                                                        accept="image/*" required>
                                                    <small class="text-muted">Format: JPG, PNG, JPEG. Max: 10MB</small>
                                                    @error('foto_proses')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <!-- Foto Produk -->
                                                <div class="mb-3">
                                                    <label for="foto_produk" class="form-label">Foto Produk <span
                                                            class="text-danger">*</span></label>
                                                    <input type="file" id="foto_produk" name="foto_produk"
                                                        class="form-control @error('foto_produk') is-invalid @enderror"
                                                        accept="image/*" required>
                                                    <small class="text-muted">Format: JPG, PNG, JPEG. Max: 10MB</small>
                                                    @error('foto_produk')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Submit Button -->
                                            <div class="mt-4">
                                                <button class="btn btn-success w-100" type="submit" id="submitBtn">
                                                    <i class="ri-save-line me-1"></i> Simpan Data
                                                </button>
                                            </div>
                                        </form>
                                        <div class="mt-4 text-center">
                                            <p class="mb-0">
                                                <a href="{{ route('superadmin.data-lapangans.index') }}"
                                                    class="fw-semibold text-primary text-decoration-underline">
                                                    <i class="ri-arrow-left-line me-1"></i> Kembali ke List
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Form -->
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
            const nikInput = document.getElementById('nik');
            const nikCounter = document.getElementById('nikCounter');
            const nikError = document.getElementById('nikError');
            const form = document.getElementById('formDataLapangan');
            const submitBtn = document.getElementById('submitBtn');
            const namaPuInput = document.getElementById('nama_pu');

            // Check if all elements exist before proceeding
            if (!nikInput || !nikCounter || !form || !submitBtn || !namaPuInput) {
                console.error('Required form elements not found');
                return;
            }

            let nikCheckTimeout;
            let isNikValid = false;

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
            // NAMA PU - AUTO UPPERCASE
            // ============================================
            namaPuInput.addEventListener('input', function(e) {
                this.value = this.value.toUpperCase();
            });

            namaPuInput.addEventListener('paste', function(e) {
                setTimeout(() => {
                    this.value = this.value.toUpperCase();
                }, 10);
            });

            // ============================================
            // NIK VALIDATION & COUNTER (FIXED)
            // ============================================
            function updateNikCounter(length) {
                if (!nikCounter) return;

                const nikStatus = document.getElementById('nikStatus');
                if (!nikStatus) return;

                // Update counter text dengan icon
                nikCounter.innerHTML = `<i class="ri-information-line"></i> ${length}/16 digit`;

                if (length === 16) {
                    // Valid: 16 digit
                    nikCounter.classList.remove('text-muted', 'text-danger', 'text-warning');
                    nikCounter.classList.add('text-success');
                    nikCounter.innerHTML = `<i class="ri-checkbox-circle-line"></i> ${length}/16 digit`;
                    nikStatus.textContent = '✓ Lengkap';
                    nikStatus.classList.remove('text-muted', 'text-danger', 'text-warning');
                    nikStatus.classList.add('text-success');
                } else if (length > 16) {
                    // Error: Lebih dari 16 digit (tidak akan terjadi karena maxlength, tapi sebagai fallback)
                    nikCounter.classList.remove('text-muted', 'text-success', 'text-warning');
                    nikCounter.classList.add('text-danger');
                    nikCounter.innerHTML = `<i class="ri-error-warning-line"></i> ${length}/16 digit`;
                    nikStatus.textContent = '✗ Terlalu panjang!';
                    nikStatus.classList.remove('text-muted', 'text-success', 'text-warning');
                    nikStatus.classList.add('text-danger');
                } else if (length > 0) {
                    // Warning: Kurang dari 16 digit
                    nikCounter.classList.remove('text-muted', 'text-success', 'text-danger');
                    nikCounter.classList.add('text-warning');
                    nikCounter.innerHTML = `<i class="ri-error-warning-line"></i> ${length}/16 digit`;
                    nikStatus.textContent = `Kurang ${16 - length} digit`;
                    nikStatus.classList.remove('text-muted', 'text-success', 'text-danger');
                    nikStatus.classList.add('text-warning');
                } else {
                    // Empty
                    nikCounter.classList.remove('text-success', 'text-danger', 'text-warning');
                    nikCounter.classList.add('text-muted');
                    nikCounter.innerHTML = `<i class="ri-information-line"></i> ${length}/16 digit`;
                    nikStatus.textContent = 'Belum lengkap';
                    nikStatus.classList.remove('text-success', 'text-danger', 'text-warning');
                    nikStatus.classList.add('text-muted');
                }
            }

            function checkNikExists(nik) {
                if (!nikInput) return;

                if (nik.length !== 16) {
                    isNikValid = false;
                    return;
                }

                // Remove existing warning
                const existingWarning = document.getElementById('nikExistsWarning');
                if (existingWarning) {
                    existingWarning.remove();
                }

                // Create loading indicator
                const loadingDiv = document.createElement('div');
                loadingDiv.id = 'nikExistsWarning';
                loadingDiv.className = 'mt-2';
                loadingDiv.innerHTML =
                    '<small class="text-info"><i class="ri-loader-4-line"></i> Memeriksa NIK...</small>';
                nikInput.parentElement.appendChild(loadingDiv);

                // Make AJAX request to check NIK
                fetch('/check-nik', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ||
                                document.querySelector('input[name="_token"]')?.value
                        },
                        body: JSON.stringify({
                            nik: nik
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        const warningDiv = document.getElementById('nikExistsWarning');

                        if (data.exists) {
                            isNikValid = false;
                            nikInput.classList.remove('is-valid');
                            nikInput.classList.add('is-invalid');

                            if (warningDiv) {
                                warningDiv.innerHTML = `
                                <div class="alert alert-warning alert-dismissible fade show p-2 mb-0" role="alert">
                                    <i class="ri-error-warning-line me-1"></i>
                                    <small><strong>NIK sudah terdaftar!</strong> NIK ini sudah digunakan oleh: <strong>${data.nama_pu || 'Pengguna lain'}</strong></small>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.7rem; padding: 0.25rem;"></button>
                                </div>
                            `;
                            }

                            showToast('warning', 'NIK sudah terdaftar di database!');
                        } else {
                            isNikValid = true;
                            nikInput.classList.remove('is-invalid');
                            nikInput.classList.add('is-valid');

                            if (warningDiv) {
                                warningDiv.innerHTML =
                                    '<small class="text-success"><i class="ri-checkbox-circle-line me-1"></i>NIK tersedia</small>';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error checking NIK:', error);
                        const warningDiv = document.getElementById('nikExistsWarning');
                        if (warningDiv) {
                            warningDiv.remove();
                        }
                        isNikValid = true; // Allow submission if check fails
                    });
            }

            nikInput.addEventListener('input', function(e) {
                // Only allow numbers and limit to 16 digits
                let value = this.value.replace(/[^0-9]/g, '');

                // Enforce maximum 16 digits
                if (value.length > 16) {
                    value = value.slice(0, 16);
                }

                this.value = value;
                const length = value.length;
                updateNikCounter(length);

                // Clear previous validation states
                nikInput.classList.remove('is-valid', 'is-invalid');
                const existingWarning = document.getElementById('nikExistsWarning');
                if (existingWarning) {
                    existingWarning.remove();
                }

                // Clear previous timeout
                clearTimeout(nikCheckTimeout);

                // Check NIK after user stops typing (debounce)
                if (length === 16) {
                    nikCheckTimeout = setTimeout(() => {
                        checkNikExists(this.value);
                    }, 500);
                } else {
                    isNikValid = false;
                }
            });

            nikInput.addEventListener('keypress', function(e) {
                if (e.key < '0' || e.key > '9') {
                    e.preventDefault();
                }
            });

            nikInput.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = (e.clipboardData || window.clipboardData).getData('text');
                const numericData = pastedData.replace(/[^0-9]/g, '').slice(0, 16);
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
                if (!nikInput) return;

                const nikValue = nikInput.value;

                if (nikValue.length !== 16) {
                    e.preventDefault();
                    nikInput.classList.add('is-invalid');
                    if (nikError) nikError.style.display = 'block';

                    nikInput.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    nikInput.focus();

                    showToast('error', 'NIK harus tepat 16 digit!');
                    return false;
                }

                if (!isNikValid) {
                    e.preventDefault();
                    nikInput.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    nikInput.focus();

                    showToast('error', 'NIK sudah terdaftar di database!');
                    return false;
                }

                // Disable submit button to prevent double submission
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
                }
            });

            // ============================================
            // IMAGE FILE VALIDATION
            // ============================================
            const imageInputs = ['foto_ktp', 'foto_rumah', 'foto_pendamping', 'foto_proses', 'foto_produk'];

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

            // Trigger counter on page load if NIK already has value
            if (nikInput.value) {
                updateNikCounter(nikInput.value.length);
            }
        });
    </script>
@endsection
