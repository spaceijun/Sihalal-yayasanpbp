@extends('layouts.guest')
@section('title', 'Form Halal')
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

                                            <!-- Enumerator dengan Search -->
                                            <div class="mb-3">
                                                <label for="enumerator_search" class="form-label">Nama Pendamping <span
                                                        class="text-danger">*</span></label>

                                                <div class="position-relative">
                                                    <input type="text" id="enumerator_search" class="form-control"
                                                        placeholder="🔍 Ketik untuk mencari pendamping..."
                                                        autocomplete="off">
                                                    <div id="search_results"
                                                        class="position-absolute w-100 bg-white border rounded shadow-sm mt-1"
                                                        style="display: none; max-height: 200px; overflow-y: auto; z-index: 1000;">
                                                    </div>
                                                </div>

                                                <!-- Hidden Select -->
                                                <select id="enumerator_id" name="enumerator_id"
                                                    class="form-control mt-2 @error('enumerator_id') is-invalid @enderror"
                                                    required style="display: none;">
                                                    <option value="">-- Pilih Pendamping --</option>
                                                    @foreach ($enumerators as $enumerator)
                                                        <option value="{{ $enumerator->id }}"
                                                            data-name="{{ $enumerator->nama_lengkap }}"
                                                            data-status="{{ $enumerator->status }}"
                                                            {{ old('enumerator_id') == $enumerator->id ? 'selected' : '' }}>
                                                            {{ $enumerator->nama_lengkap }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <!-- Display Selected -->
                                                <div id="selected_enumerator" class="mt-2" style="display: none;">
                                                    <div class="alert alert-info alert-dismissible fade show p-2 mb-0"
                                                        role="alert">
                                                        <i class="ri-user-line me-1"></i>
                                                        <strong>Terpilih:</strong> <span id="selected_name"></span>
                                                    </div>
                                                </div>

                                                <!-- Alert Enumerator Tidak Aktif -->
                                                <div id="alert_tidak_aktif" class="mt-2" style="display: none;">
                                                    <div class="alert alert-danger p-3 mb-0">
                                                        <div class="d-flex align-items-center gap-2 mb-1">
                                                            <i class="ri-forbid-2-line fs-5"></i>
                                                            <strong>Pendamping Tidak Aktif</strong>
                                                        </div>
                                                        <p class="mb-1">
                                                            Pendamping <strong id="nama_tidak_aktif"></strong>
                                                            sedang berstatus <span class="badge bg-danger">Tidak
                                                                Aktif</span>.
                                                        </p>
                                                        <small>
                                                            <i class="ri-information-line me-1"></i>
                                                            Pendamping dinonaktifkan karena tidak memenuhi target minimal
                                                            20 data lapangan dalam 30 hari terakhir.
                                                            Silakan pilih pendamping lain atau hubungi koordinator.
                                                        </small>
                                                    </div>
                                                </div>

                                                @error('enumerator_id')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- ===== FORM FIELDS (dikunci jika enumerator Tidak Aktif) ===== -->
                                            <div id="formFields">

                                                <!-- Nama PU -->
                                                <div class="mb-3">
                                                    <label for="nama_pu" class="form-label">Nama PU <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" id="nama_pu" name="nama_pu"
                                                        class="form-control @error('nama_pu') is-invalid @enderror"
                                                        value="{{ old('nama_pu') }}" required autofocus
                                                        placeholder="Masukkan nama PU">
                                                    <small class="text-muted">Nama akan otomatis diubah ke huruf
                                                        besar</small>
                                                    @error('nama_pu')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <!-- Nama Produk -->
                                                <div class="mb-3">
                                                    <label for="nama_produk" class="form-label">Nama Produk <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" id="nama_produk" name="nama_produk"
                                                        class="form-control @error('nama_produk') is-invalid @enderror"
                                                        value="{{ old('nama_produk') }}" required
                                                        placeholder="Masukkan nama produk">
                                                    @error('nama_produk')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <!-- Telephone -->
                                                <div class="mb-3">
                                                    <label for="telephone" class="form-label">Nomor Telepon <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" id="telephone" name="telephone"
                                                        class="form-control @error('telephone') is-invalid @enderror"
                                                        value="{{ old('telephone') }}" required
                                                        placeholder="Masukkan nomor telepon">
                                                    <small class="text-muted">Nomor telepon harus diisi</small>
                                                    @error('telephone')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <!-- NIK -->
                                                <div class="mb-3">
                                                    <label for="nik" class="form-label">NIK <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" id="nik" name="nik"
                                                        class="form-control @error('nik') is-invalid @enderror"
                                                        value="{{ old('nik') }}" required
                                                        placeholder="Masukkan NIK (16 digit)" maxlength="16"
                                                        pattern="[0-9]{16}" inputmode="numeric">
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

                                                <!-- Upload Foto Section -->
                                                <div class="mb-3">
                                                    <h6 class="text-muted mb-3">Upload Dokumentasi <span
                                                            class="text-danger">*</span></h6>

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

                                                    <div class="mb-3">
                                                        <label for="foto_pendamping" class="form-label">Foto Pendamping
                                                            <span class="text-danger">*</span></label>
                                                        <input type="file" id="foto_pendamping" name="foto_pendamping"
                                                            class="form-control @error('foto_pendamping') is-invalid @enderror"
                                                            accept="image/*" required>
                                                        <small class="text-muted">Format: JPG, PNG, JPEG. Max: 10MB</small>
                                                        @error('foto_pendamping')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

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

                                            </div>
                                            {{-- end #formFields --}}

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

    <script src="{{ asset('assets/js/form-halal.js') }}"></script>
    <script>
        // ── Data status enumerator dari server ──────────────────────────────────
        const enumeratorStatusMap = {
            @foreach ($enumerators as $enumerator)
                {{ $enumerator->id }}: "{{ $enumerator->status }}",
            @endforeach
        };

        // ── Elemen DOM ──────────────────────────────────────────────────────────
        const selectEl = document.getElementById('enumerator_id');
        const alertTidakAktif = document.getElementById('alert_tidak_aktif');
        const namaTidakAktif = document.getElementById('nama_tidak_aktif');
        const formFields = document.getElementById('formFields');
        const submitBtn = document.getElementById('submitBtn');

        // ── Fungsi kunci/buka form ──────────────────────────────────────────────
        function lockForm() {
            // Disable semua input di dalam formFields
            formFields.querySelectorAll('input, textarea, select, button[type="submit"]')
                .forEach(el => el.disabled = true);

            // Overlay visual
            formFields.style.opacity = '0.4';
            formFields.style.pointerEvents = 'none';
            formFields.style.userSelect = 'none';
        }

        function unlockForm() {
            formFields.querySelectorAll('input, textarea, select, button[type="submit"]')
                .forEach(el => el.disabled = false);

            formFields.style.opacity = '1';
            formFields.style.pointerEvents = '';
            formFields.style.userSelect = '';
        }

        // ── Elemen alert "Terpilih" ─────────────────────────────────────────────
        const selectedEnumeratorEl = document.getElementById('selected_enumerator');
        const selectedNameEl = document.getElementById('selected_name');

        // ── Cek status saat enumerator dipilih ──────────────────────────────────
        function checkEnumeratorStatus(enumeratorId, namaEnumerator) {
            if (!enumeratorId) {
                // Sembunyikan kedua alert, buka form
                selectedEnumeratorEl.style.display = 'none';
                alertTidakAktif.style.display = 'none';
                unlockForm();
                return;
            }

            // Selalu tampilkan alert "Terpilih"
            selectedNameEl.textContent = namaEnumerator;
            selectedEnumeratorEl.style.display = 'block';

            const status = enumeratorStatusMap[enumeratorId];

            if (status === 'Tidak Aktif') {
                // Tampilkan juga alert tidak aktif + kunci form
                namaTidakAktif.textContent = namaEnumerator;
                alertTidakAktif.style.display = 'block';
                lockForm();
            } else {
                // Sembunyikan alert tidak aktif, buka form
                alertTidakAktif.style.display = 'none';
                unlockForm();
            }
        }

        // ── Pantau perubahan pada hidden select ────────────────────────────────
        // form-halal.js mengubah value select ini saat user memilih dari dropdown search
        // Kita observe dengan MutationObserver + event listener
        selectEl.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const nama = selectedOption ? selectedOption.dataset.name : '';
            checkEnumeratorStatus(this.value, nama);
        });

        // ── Juga patch fungsi di form-halal.js yang set value select ───────────
        // Intercept setiap kali value select diubah secara programatik
        const originalDescriptor = Object.getOwnPropertyDescriptor(HTMLSelectElement.prototype, 'value');
        Object.defineProperty(selectEl, 'value', {
            set(newVal) {
                originalDescriptor.set.call(this, newVal);
                const opt = this.options[this.selectedIndex];
                const nama = opt ? opt.dataset.name : '';
                checkEnumeratorStatus(newVal, nama);
            },
            get() {
                return originalDescriptor.get.call(this);
            }
        });

        // ── Cek state awal (jika ada old value dari validasi server) ────────────
        document.addEventListener('DOMContentLoaded', function() {
            if (selectEl.value) {
                const opt = selectEl.options[selectEl.selectedIndex];
                const nama = opt ? opt.dataset.name : '';
                checkEnumeratorStatus(selectEl.value, nama);
            }

            // ── Cegah alert tidak aktif ikut auto-dismiss dari layout/global script ──
            // Override Bootstrap Alert close pada elemen ini
            const alertTidakAktifEl = document.getElementById('alert_tidak_aktif');
            if (alertTidakAktifEl) {
                alertTidakAktifEl.addEventListener('close.bs.alert', function(e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    return false;
                }, true);
            }

            // Jika layout punya setTimeout auto-dismiss, re-show alert ini setelahnya
            setTimeout(function() {
                const enumeratorId = selectEl.value;
                if (enumeratorId) {
                    const status = enumeratorStatusMap[enumeratorId];
                    if (status === 'Tidak Aktif') {
                        alertTidakAktif.style.display = 'block';
                    }
                }
            }, 6000); // 6 detik (setelah auto-dismiss 5 detik)
        });
    </script>
@endsection
