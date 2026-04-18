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

                                {{-- ============================================================ --}}
                                {{-- LEFT SIDE - Info Section                                      --}}
                                {{-- ============================================================ --}}
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
                                                            <p class="fs-15 fst-italic">" Bergabunglah dengan tim kami
                                                                untuk membuat perubahan. "</p>
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
                                                            data-bs-slide-to="0" class="active" aria-current="true"
                                                            aria-label="Slide 1"></button>
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators"
                                                            data-bs-slide-to="1" aria-label="Slide 2"></button>
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators"
                                                            data-bs-slide-to="2" aria-label="Slide 3"></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ============================================================ --}}
                                {{-- RIGHT SIDE - Form Section                                     --}}
                                {{-- ============================================================ --}}
                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4">

                                        {{-- SUCCESS MESSAGE --}}
                                        @if (session('success'))
                                            <div class="alert alert-success alert-dismissible alert-label-icon label-arrow fade show"
                                                role="alert">
                                                <i class="ri-check-double-line label-icon"></i>
                                                <strong>Berhasil!</strong> {{ session('success') }}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                    aria-label="Close"></button>
                                            </div>
                                        @endif

                                        {{-- ERROR MESSAGE --}}
                                        @if (session('error'))
                                            <div class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show"
                                                role="alert">
                                                <i class="ri-error-warning-line label-icon"></i>
                                                <strong>Gagal!</strong> {{ session('error') }}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                    aria-label="Close"></button>
                                            </div>
                                        @endif

                                        <div>
                                            <h5 class="text-primary">Form Recruitment</h5>
                                            <p class="text-muted">Lengkapi data diri Anda dengan detail.</p>
                                        </div>

                                        <form method="POST" action="{{ route('recruitment.store') }}"
                                            enctype="multipart/form-data" class="mt-4" id="formRecruitment" novalidate>
                                            @csrf

                                            {{-- ================================================ --}}
                                            {{-- POSISI DILAMAR                                   --}}
                                            {{-- ================================================ --}}
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    Posisi Dilamar <span class="text-danger">*</span>
                                                </label>
                                                <div class="d-flex gap-4 mt-1">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="recruit_type"
                                                            id="type_pendamping" value="PENDAMPING" required>
                                                        <label class="form-check-label"
                                                            for="type_pendamping">Pendamping</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="recruit_type"
                                                            id="type_data_entry" value="DATA ENTRY">
                                                        <label class="form-check-label" for="type_data_entry">Data
                                                            Entry</label>
                                                    </div>
                                                </div>
                                                @error('recruit_type')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- ================================================ --}}
                                            {{-- TYPE ENTRY — hanya muncul jika DATA ENTRY        --}}
                                            {{-- ================================================ --}}
                                            <div class="mb-3 d-none" id="typeEntryWrapper">
                                                <label for="type_entry" class="form-label">
                                                    Tipe Entry <span class="text-danger">*</span>
                                                </label>
                                                <select id="type_entry" name="type_entry"
                                                    class="form-control @error('type_entry') is-invalid @enderror">
                                                    <option value="">-- Pilih Tipe Entry --</option>
                                                    <option value="OSS">OSS</option>
                                                    <option value="SIHALAL">SIHALAL</option>
                                                </select>
                                                @error('type_entry')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror

                                                {{-- Alert Fee OSS --}}
                                                <div id="alertOSS"
                                                    class="alert alert-info d-flex align-items-start gap-2 mt-2 mb-0 py-2 px-3 d-none">
                                                    <i class="ri-money-dollar-circle-line fs-16 mt-1 flex-shrink-0"></i>
                                                    <div>
                                                        <strong>Fee OSS: Rp100.000</strong><br>
                                                        <small>Sebagai Data Entry OSS, Anda akan mendapatkan fee
                                                            sebesar <strong>Rp100.000</strong> per 15 Data yang
                                                            berhasil diproses. Pastikan Anda memahami alur kerja
                                                            sistem OSS sebelum melanjutkan pendaftaran.</small>
                                                    </div>
                                                </div>

                                                {{-- Alert Fee SIHALAL --}}
                                                <div id="alertSIHALAL"
                                                    class="alert alert-warning d-flex align-items-start gap-2 mt-2 mb-0 py-2 px-3 d-none">
                                                    <i class="ri-money-dollar-circle-line fs-16 mt-1 flex-shrink-0"></i>
                                                    <div>
                                                        <strong>Fee SIHALAL: Rp150.000</strong><br>
                                                        <small>Sebagai Data Entry SIHALAL, Anda akan mendapatkan fee
                                                            sebesar <strong>Rp150.000</strong> per 15 Data yang
                                                            berhasil diproses. Pastikan Anda memahami alur kerja
                                                            sistem SIHALAL sebelum melanjutkan pendaftaran.</small>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- ================================================ --}}
                                            {{-- NAMA LENGKAP                                     --}}
                                            {{-- ================================================ --}}
                                            <div class="mb-3">
                                                <label for="nama_lengkap" class="form-label">
                                                    Nama Lengkap <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" id="nama_lengkap" name="nama_lengkap"
                                                    class="form-control text-uppercase @error('nama_lengkap') is-invalid @enderror"
                                                    required autofocus placeholder="Masukkan nama lengkap"
                                                    autocomplete="name" maxlength="255"
                                                    style="text-transform: uppercase;">
                                                <small class="text-muted">Nama akan otomatis diubah ke huruf
                                                    besar</small>
                                                @error('nama_lengkap')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- ================================================ --}}
                                            {{-- NIK                                               --}}
                                            {{-- ================================================ --}}
                                            <div class="mb-3">
                                                <label for="nik" class="form-label">
                                                    NIK (Nomor Induk Kependudukan)
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" id="nik" name="nik"
                                                    class="form-control @error('nik') is-invalid @enderror" required
                                                    placeholder="Masukkan 16 digit NIK" maxlength="16" minlength="16"
                                                    inputmode="numeric" pattern="\d{16}" autocomplete="off">
                                                <small class="text-muted">Sesuai KTP, 16 digit angka</small>
                                                @error('nik')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- ================================================ --}}
                                            {{-- JENIS KELAMIN                                    --}}
                                            {{-- ================================================ --}}
                                            <div class="mb-3">
                                                <label for="jenis_kelamin" class="form-label">
                                                    Jenis Kelamin <span class="text-danger">*</span>
                                                </label>
                                                <select id="jenis_kelamin" name="jenis_kelamin"
                                                    class="form-control @error('jenis_kelamin') is-invalid @enderror"
                                                    required>
                                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                                    <option value="Laki-laki">Laki-laki</option>
                                                    <option value="Perempuan">Perempuan</option>
                                                </select>
                                                @error('jenis_kelamin')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- ================================================ --}}
                                            {{-- NO. TELEPON                                      --}}
                                            {{-- ================================================ --}}
                                            <div class="mb-3">
                                                <label for="telephone" class="form-label">
                                                    No. Telepon <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" id="telephone" name="telephone"
                                                    class="form-control @error('telephone') is-invalid @enderror" required
                                                    placeholder="Contoh: 081234567890" maxlength="15" inputmode="numeric"
                                                    autocomplete="tel">
                                                <small class="text-muted">Masukkan nomor telepon yang aktif (10–15
                                                    digit)</small>
                                                @error('telephone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- ================================================ --}}
                                            {{-- ALAMAT LENGKAP                                   --}}
                                            {{-- ================================================ --}}
                                            <div class="mb-3">
                                                <label for="alamat_lengkap" class="form-label">
                                                    Alamat Lengkap <span class="text-danger">*</span>
                                                </label>
                                                <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3"
                                                    class="form-control @error('alamat_lengkap') is-invalid @enderror" required placeholder="Masukkan alamat lengkap"
                                                    maxlength="500"></textarea>
                                                @error('alamat_lengkap')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- ================================================ --}}
                                            {{-- PENDIDIKAN TERAKHIR                              --}}
                                            {{-- ================================================ --}}
                                            <div class="mb-3">
                                                <label for="pendidikan_terakhir" class="form-label">
                                                    Pendidikan Terakhir <span class="text-danger">*</span>
                                                </label>
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
                                                        <option value="{{ $pendidikan }}">{{ $pendidikan }}</option>
                                                    @endforeach
                                                </select>
                                                @error('pendidikan_terakhir')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- ================================================ --}}
                                            {{-- PENGALAMAN                                       --}}
                                            {{-- ================================================ --}}
                                            <div class="mb-3">
                                                <label for="pengalaman" class="form-label">
                                                    Pengalaman <span class="text-danger">*</span>
                                                </label>
                                                <textarea id="pengalaman" name="pengalaman" rows="3"
                                                    class="form-control @error('pengalaman') is-invalid @enderror" required
                                                    placeholder="Jelaskan pengalaman kerja Anda" maxlength="1000"></textarea>
                                                <small class="text-muted">Tuliskan pengalaman kerja yang
                                                    relevan</small>
                                                @error('pengalaman')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- ================================================ --}}
                                            {{-- REKOMENDASI                                      --}}
                                            {{-- ================================================ --}}
                                            <div class="mb-3">
                                                <label for="rekomendasi" class="form-label">Rekomendasi</label>
                                                <select id="rekomendasi" name="rekomendasi"
                                                    class="form-control @error('rekomendasi') is-invalid @enderror">
                                                    <option value="">-- Pilih Rekomendasi (Opsional) --</option>
                                                    @if (isset($daftarRekomendasi) && $daftarRekomendasi->count())
                                                        @foreach ($daftarRekomendasi as $rekomendasi)
                                                            <option value="{{ $rekomendasi->nama_lengkap }}">
                                                                {{ $rekomendasi->nama_lengkap }}
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        <option value="" disabled>Tidak ada data Rekomendasi</option>
                                                    @endif
                                                </select>
                                                <small class="text-muted">Jika tidak ada, kosongkan saja</small>
                                                @error('rekomendasi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- ================================================ --}}
                                            {{-- UPLOAD DOKUMENTASI                               --}}
                                            {{-- ================================================ --}}
                                            <div class="mb-3">
                                                <h6 class="text-muted mb-3">
                                                    Upload Dokumentasi <span class="text-danger">*</span>
                                                </h6>

                                                {{-- Foto Diri --}}
                                                <div class="mb-3">
                                                    <label for="foto_diri" class="form-label">
                                                        Foto Diri (3x4) <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="file" id="foto_diri" name="foto_diri"
                                                        class="form-control @error('foto_diri') is-invalid @enderror"
                                                        accept="image/jpeg,image/jpg,image/png" required>
                                                    <small class="text-muted">Format: JPG, PNG, JPEG. Maks: 10MB</small>
                                                    @error('foto_diri')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Foto KTP --}}
                                                <div class="mb-3">
                                                    <label for="foto_ktp" class="form-label">
                                                        Foto KTP <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="file" id="foto_ktp" name="foto_ktp"
                                                        class="form-control @error('foto_ktp') is-invalid @enderror"
                                                        accept="image/jpeg,image/jpg,image/png" required>
                                                    <small class="text-muted">Format: JPG, PNG, JPEG. Maks: 10MB</small>
                                                    @error('foto_ktp')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Foto Ijazah --}}
                                            <div class="mb-3">
                                                <label for="foto_ijasah" class="form-label">
                                                    Foto Ijazah <span class="text-danger">*</span>
                                                </label>
                                                <input type="file" id="foto_ijasah" name="foto_ijasah"
                                                    class="form-control @error('foto_ijasah') is-invalid @enderror"
                                                    accept="image/jpeg,image/jpg,image/png,application/pdf" required>
                                                <small class="text-muted">Format: JPG, PNG, JPEG, PDF. Maks:
                                                    10MB</small>
                                                @error('foto_ijasah')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- ================================================ --}}
                                            {{-- DOWNLOAD PAKTA INTEGRITAS                        --}}
                                            {{-- ================================================ --}}
                                            <div class="mb-3">
                                                <label class="form-label">Template Pakta Integritas</label>
                                                <div class="d-flex gap-2 flex-wrap">
                                                    <a href="{{ asset('assets/files/pakta-integritas-pendamping.docx') }}"
                                                        download class="btn btn-outline-info btn-sm"
                                                        id="btnDownloadPendamping">
                                                        <i class="ri-download-2-line me-1"></i> Pakta Integritas
                                                        Pendamping
                                                    </a>
                                                    <a href="{{ asset('assets/files/pakta-integritas-data-entry.docx') }}"
                                                        download class="btn btn-outline-secondary btn-sm"
                                                        id="btnDownloadDataEntry">
                                                        <i class="ri-download-2-line me-1"></i> Pakta Integritas
                                                        Data Entry
                                                    </a>
                                                </div>
                                                <small class="text-muted d-block mt-1">Unduh sesuai tipe rekrutmen
                                                    Anda, tanda tangani, lalu upload di field di bawah.</small>
                                            </div>

                                            {{-- ================================================ --}}
                                            {{-- PAKTA INTEGRITAS UPLOAD                          --}}
                                            {{-- ================================================ --}}
                                            <div class="mb-3">
                                                <label for="pakta_integritas" class="form-label">
                                                    Pakta Integritas (sudah ditandatangani)
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="file" id="pakta_integritas" name="pakta_integritas"
                                                    class="form-control @error('pakta_integritas') is-invalid @enderror"
                                                    accept="image/jpeg,image/jpg,image/png,application/pdf" required>
                                                <small class="text-muted">Format: JPG, PNG, JPEG, PDF. Maks:
                                                    10MB</small>
                                                @error('pakta_integritas')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Hidden Status --}}
                                            <input type="hidden" name="status" value="Melamar">

                                            {{-- Submit --}}
                                            <div class="mt-4">
                                                <button class="btn btn-success w-100" type="submit" id="submitBtn">
                                                    <i class="ri-send-plane-line me-1"></i> Kirim Lamaran
                                                </button>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                                {{-- END Right Side --}}

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            'use strict';

            const MAX_FILE_SIZE_BYTES = 10 * 1024 * 1024;
            const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/jpg', 'image/png'];
            const ALLOWED_MIXED_TYPES = [...ALLOWED_IMAGE_TYPES, 'application/pdf'];
            const ALERT_AUTO_CLOSE_MS = 5000;
            const TOAST_DURATION_MS = 3500;

            const el = {};

            function getEl(id) {
                const node = document.getElementById(id);
                if (!node) console.warn(`[RecruitmentForm] Element not found: #${id}`);
                return node;
            }

            function initElements() {
                el.form = getEl('formRecruitment');
                el.submitBtn = getEl('submitBtn');
                el.namaLengkap = getEl('nama_lengkap');
                el.nik = getEl('nik');
                el.telephone = getEl('telephone');
                el.typeEntryWrapper = getEl('typeEntryWrapper');
                el.typeEntrySelect = getEl('type_entry');
                el.alertOSS = getEl('alertOSS');
                el.alertSIHALAL = getEl('alertSIHALAL');
                el.btnDownloadPendamping = getEl('btnDownloadPendamping');
                el.btnDownloadDataEntry = getEl('btnDownloadDataEntry');
                el.recruitTypeInputs = document.querySelectorAll('input[name="recruit_type"]');

                const criticalIds = ['form', 'submitBtn', 'namaLengkap', 'telephone',
                    'typeEntryWrapper', 'typeEntrySelect'
                ];
                return criticalIds.every(key => {
                    if (!el[key]) {
                        console.error(`[RecruitmentForm] Critical element missing: ${key}`);
                        return false;
                    }
                    return true;
                });
            }

            function show(node) {
                if (node) {
                    node.classList.remove('d-none');
                    if (node.classList.contains('alert')) node.classList.add('d-flex');
                }
            }

            function hide(node) {
                if (node) {
                    node.classList.add('d-none');
                    node.classList.remove('d-flex');
                }
            }

            function showToast(type, message) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: type,
                        title: type === 'success' ? 'Berhasil!' : type === 'warning' ? 'Peringatan!' : 'Gagal!',
                        text: message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: TOAST_DURATION_MS,
                        timerProgressBar: true,
                    });
                } else {
                    alert(`[${type.toUpperCase()}] ${message}`);
                }
            }

            function focusInvalid(node) {
                if (!node) return;
                node.classList.add('is-invalid');
                node.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                node.focus();
            }

            function initAutoCloseAlerts() {
                document.querySelectorAll('.alert[role="alert"]').forEach(alertEl => {
                    setTimeout(() => {
                        try {
                            const bsAlert = bootstrap.Alert.getOrCreateInstance(alertEl);
                            bsAlert.close();
                        } catch (e) {
                            alertEl.style.display = 'none';
                        }
                    }, ALERT_AUTO_CLOSE_MS);
                });
            }

            function updateDownloadButtons() {
                const selected = document.querySelector('input[name="recruit_type"]:checked');
                if (!el.btnDownloadPendamping || !el.btnDownloadDataEntry) return;

                if (!selected) {
                    el.btnDownloadPendamping.className = 'btn btn-outline-info btn-sm';
                    el.btnDownloadDataEntry.className = 'btn btn-outline-secondary btn-sm';
                    return;
                }

                if (selected.value === 'PENDAMPING') {
                    el.btnDownloadPendamping.className = 'btn btn-info btn-sm text-white';
                    el.btnDownloadDataEntry.className = 'btn btn-outline-secondary btn-sm';
                } else {
                    el.btnDownloadPendamping.className = 'btn btn-outline-info btn-sm';
                    el.btnDownloadDataEntry.className = 'btn btn-secondary btn-sm text-white';
                }
            }

            function updateTypeEntryVisibility() {
                const selected = document.querySelector('input[name="recruit_type"]:checked');
                const isDataEntry = selected && selected.value === 'DATA ENTRY';

                if (isDataEntry) {
                    show(el.typeEntryWrapper);
                    el.typeEntrySelect.setAttribute('required', 'required');
                } else {
                    hide(el.typeEntryWrapper);
                    el.typeEntrySelect.removeAttribute('required');
                    el.typeEntrySelect.value = '';
                    el.typeEntrySelect.classList.remove('is-invalid');
                    hide(el.alertOSS);
                    hide(el.alertSIHALAL);
                }
            }

            function updateFeeAlert() {
                const val = el.typeEntrySelect ? el.typeEntrySelect.value : '';

                if (val === 'OSS') {
                    show(el.alertOSS);
                    hide(el.alertSIHALAL);
                } else if (val === 'SIHALAL') {
                    hide(el.alertOSS);
                    show(el.alertSIHALAL);
                } else {
                    hide(el.alertOSS);
                    hide(el.alertSIHALAL);
                }
            }

            function onRecruitTypeChange() {
                updateDownloadButtons();
                updateTypeEntryVisibility();
                updateFeeAlert();
            }

            function initNamaLengkap() {
                const input = el.namaLengkap;
                if (!input) return;

                function toUpper() {
                    const pos = input.selectionStart;
                    input.value = input.value.toUpperCase();
                    try {
                        input.setSelectionRange(pos, pos);
                    } catch (_) {}
                }

                input.addEventListener('input', toUpper);
                input.addEventListener('paste', () => setTimeout(toUpper, 0));
            }

            function initNIK() {
                const input = el.nik;
                if (!input) return;

                function sanitize() {
                    input.value = input.value.replace(/\D/g, '').slice(0, 16);
                }

                input.addEventListener('input', sanitize);
                input.addEventListener('keypress', e => {
                    if (!/\d/.test(e.key)) e.preventDefault();
                });
                input.addEventListener('paste', e => {
                    e.preventDefault();
                    const pasted = (e.clipboardData || window.clipboardData).getData('text');
                    input.value = pasted.replace(/\D/g, '').slice(0, 16);
                });
            }

            function initTelephone() {
                const input = el.telephone;
                if (!input) return;

                function sanitize() {
                    const cleaned = input.value.replace(/\D/g, '').slice(0, 15);
                    input.value = cleaned;
                    if (cleaned.length >= 10) input.classList.remove('is-invalid');
                }

                input.addEventListener('input', sanitize);
                input.addEventListener('keypress', e => {
                    if (!/\d/.test(e.key)) e.preventDefault();
                });
                input.addEventListener('paste', e => {
                    e.preventDefault();
                    const pasted = (e.clipboardData || window.clipboardData).getData('text');
                    input.value = pasted.replace(/\D/g, '').slice(0, 15);
                    input.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                });
            }

            function validateFile(file, allowedTypes, fieldLabel) {
                if (!file) return true;

                if (file.size > MAX_FILE_SIZE_BYTES) {
                    showToast('error', `Ukuran file "${fieldLabel}" maksimal 10MB!`);
                    return false;
                }

                if (!allowedTypes.includes(file.type)) {
                    const exts = allowedTypes.map(t => t.split('/')[1].toUpperCase()).join(', ');
                    showToast('error', `Format file "${fieldLabel}" harus: ${exts}!`);
                    return false;
                }

                return true;
            }

            function bindFileInput(inputId, allowedTypes, label) {
                const input = getEl(inputId);
                if (!input) return;

                input.addEventListener('change', function() {
                    const file = this.files[0];
                    if (!validateFile(file, allowedTypes, label)) {
                        this.value = '';
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                    }
                });
            }

            function initFileInputs() {
                bindFileInput('foto_diri', ALLOWED_IMAGE_TYPES, 'Foto Diri');
                bindFileInput('foto_ktp', ALLOWED_IMAGE_TYPES, 'Foto KTP');
                bindFileInput('foto_ijasah', ALLOWED_MIXED_TYPES, 'Foto Ijazah');
                bindFileInput('pakta_integritas', ALLOWED_MIXED_TYPES, 'Pakta Integritas');
            }

            function initFormSubmission() {
                if (!el.form) return;

                el.form.addEventListener('submit', function(e) {
                    let valid = true;

                    const selectedRecruit = document.querySelector('input[name="recruit_type"]:checked');
                    if (!selectedRecruit) {
                        showToast('error', 'Silakan pilih Posisi Dilamar terlebih dahulu!');
                        valid = false;
                    }

                    if (valid && selectedRecruit?.value === 'DATA ENTRY' && !el.typeEntrySelect.value) {
                        focusInvalid(el.typeEntrySelect);
                        showToast('error', 'Silakan pilih Tipe Entry terlebih dahulu!');
                        valid = false;
                    }

                    if (valid && el.nik) {
                        const nikVal = el.nik.value.trim();
                        if (!/^\d{16}$/.test(nikVal)) {
                            focusInvalid(el.nik);
                            showToast('error', 'NIK harus tepat 16 digit angka!');
                            valid = false;
                        }
                    }

                    if (valid) {
                        const tel = el.telephone.value;
                        if (tel.length < 10 || tel.length > 15) {
                            focusInvalid(el.telephone);
                            showToast('error', 'Nomor telepon harus antara 10–15 digit!');
                            valid = false;
                        }
                    }

                    if (!valid) {
                        e.preventDefault();
                        return false;
                    }

                    el.submitBtn.disabled = true;
                    el.submitBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Mengirim...';
                });
            }

            function initLiveValidationReset() {
                el.form?.querySelectorAll('.form-control, .form-check-input').forEach(input => {
                    input.addEventListener('input', function() {
                        if (this.classList.contains('is-invalid') && this.value)
                            this.classList.remove('is-invalid');
                    });
                    input.addEventListener('change', function() {
                        if (this.classList.contains('is-invalid') && this.value)
                            this.classList.remove('is-invalid');
                    });
                });
            }

            function initPageShowReset() {
                window.addEventListener('pageshow', function(e) {
                    if (e.persisted && el.submitBtn) {
                        el.submitBtn.disabled = false;
                        el.submitBtn.innerHTML = '<i class="ri-send-plane-line me-1"></i> Kirim Lamaran';
                    }
                });
            }

            function init() {
                if (!initElements()) return;

                el.recruitTypeInputs.forEach(input => {
                    input.addEventListener('change', onRecruitTypeChange);
                });

                el.typeEntrySelect.addEventListener('change', updateFeeAlert);

                // Inisialisasi awal — tidak ada nilai default karena ini form create
                updateDownloadButtons();
                updateTypeEntryVisibility();
                updateFeeAlert();

                initNamaLengkap();
                initNIK();
                initTelephone();
                initFileInputs();
                initFormSubmission();
                initLiveValidationReset();
                initPageShowReset();
                initAutoCloseAlerts();
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }

        })();
    </script>
@endsection
