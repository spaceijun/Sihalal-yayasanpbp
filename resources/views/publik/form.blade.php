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
                                                    <img src="{{ asset('assets/images/logo-light.png') }}" alt="Logo"
                                                        height="18">
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
                                        <div>
                                            <h5 class="text-primary">Form Data Lapangan</h5>
                                            <p class="text-muted">Lengkapi data lapangan dengan detail.</p>
                                        </div>
                                        <form method="POST" action="{{ route('formulir.halal.store') }}"
                                            enctype="multipart/form-data" class="mt-4">
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
                                                    class="form-control @error('nama_pu') is-invalid @enderror"
                                                    value="{{ old('nama_pu') }}" required autofocus
                                                    placeholder="Masukkan nama PU">
                                                @error('nama_pu')
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
                                                    placeholder="Masukkan NIK (16 digit)" maxlength="16">
                                                @error('nik')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- RT & RW -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="rt" class="form-label">RT <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" id="rt" name="rt"
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
                                                        <input type="text" id="rw" name="rw"
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
                                                    <small class="text-muted">Format: JPG, PNG, JPEG. Max: 2MB</small>
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
                                                    <small class="text-muted">Format: JPG, PNG, JPEG. Max: 2MB</small>
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
                                                    <small class="text-muted">Format: JPG, PNG, JPEG. Max: 2MB</small>
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
                                                    <small class="text-muted">Format: JPG, PNG, JPEG. Max: 2MB</small>
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
                                                    <small class="text-muted">Format: JPG, PNG, JPEG. Max: 2MB</small>
                                                    @error('foto_produk')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Submit Button -->
                                            <div class="mt-4">
                                                <button class="btn btn-success w-100" type="submit">
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
