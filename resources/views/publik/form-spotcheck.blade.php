@extends('layouts.guest')
@section('title', 'Form Spotcheck')

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
                                                            <p class="fs-15 fst-italic">" Pastikan data lapangan
                                                                terverifikasi dengan akurat. "</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">" Spotcheck untuk kualitas data yang
                                                                lebih baik."</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">" Dokumentasi adalah kunci validasi
                                                                data lapangan. "</p>
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
                                            <h5 class="text-primary">Form Spotcheck</h5>
                                            <p class="text-muted">Lengkapi data spotcheck dengan detail.</p>
                                        </div>

                                        <form method="POST" action="{{ route('spotcheck.store') }}"
                                            enctype="multipart/form-data" class="mt-4" id="formSpotcheck">
                                            @csrf

                                            <!-- Enumerator with Search -->
                                            <div class="mb-3">
                                                <label for="enumerator_search" class="form-label">Enumerator <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" id="enumerator_search"
                                                    class="form-control @error('enumerator_id') is-invalid @enderror"
                                                    placeholder="Ketik untuk mencari enumerator..." autocomplete="off"
                                                    required>
                                                <input type="hidden" id="enumerator_id" name="enumerator_id"
                                                    value="{{ old('enumerator_id', $spotcheck?->enumerator_id) }}">

                                                <!-- Dropdown Results -->
                                                <div id="enumerator_results" class="search-results" style="display: none;">
                                                </div>

                                                <small class="text-muted">Ketik minimal 1 karakter untuk mencari</small>
                                                @error('enumerator_id')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Data Lapangan with Search (Auto-filtered by Enumerator) -->
                                            <div class="mb-3">
                                                <label for="data_lapangan_search" class="form-label">Data Lapangan <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" id="data_lapangan_search"
                                                    class="form-control @error('data_lapangan_id') is-invalid @enderror"
                                                    placeholder="Pilih enumerator terlebih dahulu..." autocomplete="off"
                                                    disabled required>
                                                <input type="hidden" id="data_lapangan_id" name="data_lapangan_id"
                                                    value="{{ old('data_lapangan_id', $spotcheck?->data_lapangan_id) }}">

                                                <!-- Dropdown Results -->
                                                <div id="lapangan_results" class="search-results" style="display: none;">
                                                </div>

                                                <small class="text-muted">Ketik untuk mencari data lapangan</small>
                                                @error('data_lapangan_id')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Nama Spotchecker -->
                                            <div class="mb-3">
                                                <label for="nama_spotcheck" class="form-label">Nama Petugas Spotcheck
                                                    <span class="text-danger">*</span></label>
                                                <input type="text" id="nama_spotcheck" name="nama_spotcheck"
                                                    class="form-control text-uppercase @error('nama_spotcheck') is-invalid @enderror"
                                                    value="{{ old('nama_spotcheck', $spotcheck?->nama_spotcheck) }}"
                                                    required autofocus placeholder="Masukkan nama petugas"
                                                    style="text-transform: uppercase;">
                                                <small class="text-muted">Nama akan otomatis diubah ke huruf besar</small>
                                                @error('nama_spotcheck')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Tanggal Spotcheck -->
                                            <div class="mb-3">
                                                <label for="tanggal_spotcheck" class="form-label">Tanggal Spotcheck <span
                                                        class="text-danger">*</span></label>
                                                <input type="date" id="tanggal_spotcheck" name="tanggal_spotcheck"
                                                    class="form-control @error('tanggal_spotcheck') is-invalid @enderror"
                                                    value="{{ old('tanggal_spotcheck', $spotcheck?->tanggal_spotcheck) }}"
                                                    required>
                                                <small class="text-muted">Pilih tanggal dilakukannya spotcheck</small>
                                                @error('tanggal_spotcheck')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Foto PU (Photo Upload) -->
                                            <div class="mb-3">
                                                <label for="foto_pu" class="form-label">Foto PU (Photo Upload) <span
                                                        class="text-danger">*</span></label>
                                                <input type="file" id="foto_pu" name="foto_pu"
                                                    class="form-control @error('foto_pu') is-invalid @enderror"
                                                    accept="image/*" required>
                                                <small class="text-muted">Format: JPG, PNG, JPEG. Max: 10MB</small>
                                                @error('foto_pu')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Hasil Spotcheck -->
                                            <div class="mb-3">
                                                <label for="hasil_spotcheck" class="form-label">Hasil Spotcheck <span
                                                        class="text-danger">*</span></label>
                                                <textarea id="hasil_spotcheck" name="hasil_spotcheck" rows="4"
                                                    class="form-control @error('hasil_spotcheck') is-invalid @enderror" required
                                                    placeholder="Jelaskan hasil spotcheck secara detail">{{ old('hasil_spotcheck', $spotcheck?->hasil_spotcheck) }}</textarea>
                                                <small class="text-muted">Tuliskan hasil verifikasi dan temuan
                                                    lapangan</small>
                                                @error('hasil_spotcheck')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Submit Button -->
                                            <div class="mt-4">
                                                <button class="btn btn-success w-100" type="submit" id="submitBtn">
                                                    <i class="ri-send-plane-line me-1"></i> Simpan Data Spotcheck
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .search-results {
            position: absolute;
            z-index: 1000;
            width: 100%;
            max-height: 250px;
            overflow-y: auto;
            background: #fff;
            border: 1px solid #ced4da;
            border-top: none;
            border-radius: 0 0 0.25rem 0.25rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .search-result-item {
            padding: 10px 15px;
            cursor: pointer;
            transition: background-color 0.2s;
            border-bottom: 1px solid #f0f0f0;
        }

        .search-result-item:hover {
            background-color: #f8f9fa;
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        .search-result-item.active {
            background-color: #e9ecef;
        }

        .no-results {
            padding: 15px;
            text-align: center;
            color: #6c757d;
        }

        .search-loading {
            padding: 15px;
            text-align: center;
            color: #6c757d;
        }

        /* Highlight matched text */
        .highlight {
            background-color: #fff3cd;
            font-weight: 600;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formSpotcheck');
            const submitBtn = document.getElementById('submitBtn');
            const namaSpotcheckInput = document.getElementById('nama_spotcheck');

            // Enumerator Search Elements
            const enumeratorSearchInput = document.getElementById('enumerator_search');
            const enumeratorIdInput = document.getElementById('enumerator_id');
            const enumeratorResults = document.getElementById('enumerator_results');

            // Data Lapangan Search Elements
            const dataLapanganSearchInput = document.getElementById('data_lapangan_search');
            const dataLapanganIdInput = document.getElementById('data_lapangan_id');
            const lapanganResults = document.getElementById('lapangan_results');

            // All enumerators data
            const enumerators = @json($enumerators ?? []);
            let dataLapanganList = [];

            // Check if all elements exist before proceeding
            if (!form || !submitBtn || !namaSpotcheckInput) {
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
            // NAMA SPOTCHECK - AUTO UPPERCASE
            // ============================================
            namaSpotcheckInput.addEventListener('input', function(e) {
                this.value = this.value.toUpperCase();
            });

            namaSpotcheckInput.addEventListener('paste', function(e) {
                setTimeout(() => {
                    this.value = this.value.toUpperCase();
                }, 10);
            });

            // ============================================
            // ENUMERATOR SEARCH FUNCTIONALITY
            // ============================================
            enumeratorSearchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();

                if (searchTerm.length === 0) {
                    enumeratorResults.style.display = 'none';
                    enumeratorIdInput.value = '';
                    return;
                }

                const filtered = enumerators.filter(enum_item =>
                    enum_item.nama_lengkap.toLowerCase().includes(searchTerm)
                );

                displayEnumeratorResults(filtered, searchTerm);
            });

            enumeratorSearchInput.addEventListener('focus', function() {
                if (this.value.trim().length > 0) {
                    const searchTerm = this.value.toLowerCase().trim();
                    const filtered = enumerators.filter(enum_item =>
                        enum_item.nama_lengkap.toLowerCase().includes(searchTerm)
                    );
                    displayEnumeratorResults(filtered, searchTerm);
                }
            });

            function displayEnumeratorResults(results, searchTerm) {
                if (results.length === 0) {
                    enumeratorResults.innerHTML = '<div class="no-results">Tidak ada enumerator ditemukan</div>';
                    enumeratorResults.style.display = 'block';
                    return;
                }

                let html = '';
                results.forEach(enum_item => {
                    const highlighted = highlightText(enum_item.nama_lengkap, searchTerm);
                    html += `<div class="search-result-item" data-id="${enum_item.id}" data-name="${enum_item.nama_lengkap}">
                        ${highlighted}
                    </div>`;
                });

                enumeratorResults.innerHTML = html;
                enumeratorResults.style.display = 'block';

                // Add click event to results
                enumeratorResults.querySelectorAll('.search-result-item').forEach(item => {
                    item.addEventListener('click', function() {
                        selectEnumerator(this.dataset.id, this.dataset.name);
                    });
                });
            }

            function selectEnumerator(id, name) {
                enumeratorIdInput.value = id;
                enumeratorSearchInput.value = name;
                enumeratorResults.style.display = 'none';

                // Load data lapangan for selected enumerator
                loadDataLapangan(id);
            }

            // ============================================
            // DATA LAPANGAN SEARCH FUNCTIONALITY
            // ============================================
            function loadDataLapangan(enumeratorId) {
                dataLapanganSearchInput.value = '';
                dataLapanganIdInput.value = '';
                dataLapanganSearchInput.placeholder = 'Memuat data...';
                dataLapanganSearchInput.disabled = true;
                lapanganResults.innerHTML = '<div class="search-loading">Memuat data lapangan...</div>';
                lapanganResults.style.display = 'block';

                fetch(`/api/data-lapangan/by-enumerator/${enumeratorId}`)
                    .then(response => response.json())
                    .then(data => {
                        dataLapanganList = data;
                        dataLapanganSearchInput.placeholder = 'Ketik untuk mencari data lapangan...';
                        dataLapanganSearchInput.disabled = false;
                        lapanganResults.style.display = 'none';

                        if (data.length === 0) {
                            dataLapanganSearchInput.placeholder = 'Tidak ada data lapangan';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching data lapangan:', error);
                        dataLapanganSearchInput.placeholder = 'Gagal memuat data';
                        lapanganResults.innerHTML =
                            '<div class="no-results text-danger">Gagal memuat data lapangan</div>';
                        showToast('error', 'Gagal memuat data lapangan. Silakan coba lagi.');
                    });
            }

            dataLapanganSearchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();

                if (searchTerm.length === 0) {
                    lapanganResults.style.display = 'none';
                    dataLapanganIdInput.value = '';
                    return;
                }

                const filtered = dataLapanganList.filter(lapangan =>
                    lapangan.nama_pu.toLowerCase().includes(searchTerm)
                );

                displayLapanganResults(filtered, searchTerm);
            });

            dataLapanganSearchInput.addEventListener('focus', function() {
                if (this.value.trim().length > 0 && dataLapanganList.length > 0) {
                    const searchTerm = this.value.toLowerCase().trim();
                    const filtered = dataLapanganList.filter(lapangan =>
                        lapangan.nama_pu.toLowerCase().includes(searchTerm)
                    );
                    displayLapanganResults(filtered, searchTerm);
                }
            });

            function displayLapanganResults(results, searchTerm) {
                if (results.length === 0) {
                    lapanganResults.innerHTML = '<div class="no-results">Tidak ada data lapangan ditemukan</div>';
                    lapanganResults.style.display = 'block';
                    return;
                }

                let html = '';
                results.forEach(lapangan => {
                    const highlighted = highlightText(lapangan.nama_pu, searchTerm);
                    html += `<div class="search-result-item" data-id="${lapangan.id}" data-name="${lapangan.nama_pu}">
                        ${highlighted}
                    </div>`;
                });

                lapanganResults.innerHTML = html;
                lapanganResults.style.display = 'block';

                // Add click event to results
                lapanganResults.querySelectorAll('.search-result-item').forEach(item => {
                    item.addEventListener('click', function() {
                        selectDataLapangan(this.dataset.id, this.dataset.name);
                    });
                });
            }

            function selectDataLapangan(id, name) {
                dataLapanganIdInput.value = id;
                dataLapanganSearchInput.value = name;
                lapanganResults.style.display = 'none';
            }

            // ============================================
            // HELPER FUNCTIONS
            // ============================================
            function highlightText(text, search) {
                if (!search) return text;
                const regex = new RegExp(`(${search})`, 'gi');
                return text.replace(regex, '<span class="highlight">$1</span>');
            }

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                if (!enumeratorSearchInput.contains(e.target) && !enumeratorResults.contains(e.target)) {
                    enumeratorResults.style.display = 'none';
                }
                if (!dataLapanganSearchInput.contains(e.target) && !lapanganResults.contains(e.target)) {
                    lapanganResults.style.display = 'none';
                }
            });

            // ============================================
            // FORM SUBMISSION
            // ============================================
            form.addEventListener('submit', function(e) {
                // Validate enumerator selection
                if (!enumeratorIdInput.value) {
                    e.preventDefault();
                    showToast('error', 'Silakan pilih enumerator terlebih dahulu!');
                    enumeratorSearchInput.focus();
                    return;
                }

                // Validate data lapangan selection
                if (!dataLapanganIdInput.value) {
                    e.preventDefault();
                    showToast('error', 'Silakan pilih data lapangan terlebih dahulu!');
                    dataLapanganSearchInput.focus();
                    return;
                }

                // Disable submit button to prevent double submission
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
            });

            // ============================================
            // IMAGE FILE VALIDATION
            // ============================================
            const fotoPuInput = document.getElementById('foto_pu');

            if (fotoPuInput) {
                fotoPuInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        // Check file size (max 10MB)
                        if (file.size > 10485760) {
                            showToast('error', 'Ukuran file foto PU maksimal 10MB!');
                            this.value = '';
                            return;
                        }

                        // Check file type
                        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                        if (!allowedTypes.includes(file.type)) {
                            showToast('error', 'Format file foto PU harus JPG, JPEG, atau PNG!');
                            this.value = '';
                            return;
                        }
                    }
                });
            }

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
