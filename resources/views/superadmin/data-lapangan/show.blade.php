@extends('layouts.app')

@section('template_title')
    {{ $dataLapangan->nama_pu ?? __('Show') . ' ' . __('Data Lapangan') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <!-- Alert Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row mt-3">
            <!-- Card 1: Data Informasi (Kiri) -->
            <div class="col-md-6">
                <!-- Card Edit Status -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <span><i class="fas fa-edit me-2"></i>Edit Status</span>
                    </div>
                    <div class="card-body">
                        {{-- Button Trigger Modal Update Email & Verifikasi --}}
                        @if ($dataLapangan->status == 'PENDING')
                            <div class="mt-1 d-flex gap-2">
                                <button type="button" class="btn btn-success btn-sm w-50" data-bs-toggle="modal"
                                    data-bs-target="#modalUpdateEmail">
                                    Update Email & Verifikasi Data
                                </button>
                                <button type="button" class="btn btn-danger btn-sm w-50" data-bs-toggle="modal"
                                    data-bs-target="#modalRevisi">
                                    Update Data Revisi
                                </button>
                            </div>
                        @endif
                        {{-- Status Data
                        <label for="">Status Data</label>
                        <form action="{{ route('superadmin.data-lapangans.update-status', $dataLapangan->hashed_id) }}"
                            method="POST">
                            @csrf
                            <div class="row align-items-end mb-3">
                                <div class="col-md-8">
                                    <select name="status" class="form-select" required>
                                        <option value="">-- Pilih Status --</option>
                                        <option value="PENDING" {{ $dataLapangan->status == 'PENDING' ? 'selected' : '' }}>
                                            PENDING</option>
                                        <option value="PROGRESS OSS"
                                            {{ $dataLapangan->status == 'PROGRESS OSS' ? 'selected' : '' }}>PROGRESS OSS
                                        </option>
                                        <option value="PROGRESS SIHALAL"
                                            {{ $dataLapangan->status == 'PROGRESS SIHALAL' ? 'selected' : '' }}>PROGRESS
                                            SIHALAL</option>
                                        <option value="TERBIT SH"
                                            {{ $dataLapangan->status == 'TERBIT SH' ? 'selected' : '' }}>TERBIT SH</option>
                                        <option value="DITOLAK" {{ $dataLapangan->status == 'DITOLAK' ? 'selected' : '' }}>
                                            DITOLAK</option>
                                        <option value="REVISI" {{ $dataLapangan->status == 'REVISI' ? 'selected' : '' }}>
                                            REVISI</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-save me-2"></i>Update
                                    </button>
                                </div>
                            </div>
                        </form> --}}

                        {{-- Status Pembayaran --}}
                        <div class="mt-2">
                            <label for="">Status Pembayaran</label>
                            <form
                                action="{{ route('superadmin.data-lapangans.update-status-payment', $dataLapangan->hashed_id) }}"
                                method="POST">
                                @csrf
                                <div class="row align-items-end mb-3">
                                    <div class="col-md-8">
                                        <select name="status_pembayaran" class="form-select" required>
                                            <option value="">-- Pilih Status --</option>
                                            <option value="PENDING"
                                                {{ $dataLapangan->status_pembayaran == 'PENDING' ? 'selected' : '' }}>
                                                PENDING
                                            </option>
                                            <option value="PENGAJUAN"
                                                {{ $dataLapangan->status_pembayaran == 'PENGAJUAN' ? 'selected' : '' }}>
                                                PENGAJUAN</option>
                                            <option value="DIBAYAR"
                                                {{ $dataLapangan->status_pembayaran == 'DIBAYAR' ? 'selected' : '' }}>
                                                DIBAYAR
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-save me-2"></i>Update
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @if ($dataLapangan->verifikator)
                            <hr>
                            <div class="form-group mb-3">
                                <strong>Nama Verifikator</strong>
                                <p class="text-muted mb-0">{{ $dataLapangan->verifikator ?? 'Verifikator Kosong' }}</p>
                            </div>
                            <hr>
                            <div class="form-group mb-3">
                                <strong>Tanggal Verifikasi</strong>
                                <p class="text-muted mb-0">
                                    {{ $dataLapangan->tanggal_verifikasi ?? 'Tidak ada Tanggal Verif.' }}
                                </p>
                            </div>
                        @elseif ($dataLapangan->status == 'REVISI')
                            <hr>
                            <div class="form-group mb-3">
                                <strong>Keterangan Revisi</strong>
                                <p class="text-muted mb-0">
                                    {{ $dataLapangan->keterangan ?? 'Tidak ada Keterangan.' }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Modal Update Email & Verifikasi --}}
                <div class="modal fade" id="modalUpdateEmail" tabindex="-1" aria-labelledby="modalUpdateEmailLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalUpdateEmailLabel">
                                    <i class="fas fa-envelope me-2"></i>Update Email & Verifikasi Data
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form action="{{ route('superadmin.data-lapangans.update-email', $dataLapangan->id) }}"
                                method="POST">
                                @csrf
                                <div class="modal-body">
                                    <!-- Warning Alert -->
                                    <div class="alert alert-warning alert-dismissible bg-warning text-white alert-label-icon fade show material-shadow"
                                        role="alert">
                                        <i class="ri-alert-line label-icon"></i><strong>PEHATIAN</strong> - Pastikan data
                                        sudah divalidasi dengan baik serta sudah membuat email untuk PU!
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" name="email"
                                            class="form-control @error('email') is-invalid @enderror" id="email"
                                            value="{{ old('email', $dataLapangan->email) }}" placeholder="Masukkan email"
                                            required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="verifikator" class="form-label">Verifikator</label>
                                        <input type="text" name="verifikator"
                                            class="form-control @error('verifikator') is-invalid @enderror"
                                            id="verifikator" value="{{ old('verifikator', $dataLapangan->verifikator) }}"
                                            placeholder="Nama verifikator">
                                        @error('verifikator')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="tanggal_verifikasi" class="form-label">Tanggal Verifikasi</label>
                                        <input type="date" name="tanggal_verifikasi"
                                            class="form-control @error('tanggal_verifikasi') is-invalid @enderror"
                                            id="tanggal_verifikasi"
                                            value="{{ old('tanggal_verifikasi', optional($dataLapangan->tanggal_verifikasi)->format('Y-m-d')) }}">
                                        @error('tanggal_verifikasi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="fas fa-times me-2"></i>Batal
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Modal Update Email & Verifikasi --}}
                <div class="modal fade" id="modalRevisi" tabindex="-1" aria-labelledby="modalRevisiLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalRevisiLabel">
                                    <i class="fas fa-envelope me-2"></i>Revisi Data
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form action="{{ route('superadmin.data-lapangans.update-keterangan', $dataLapangan->id) }}"
                                method="POST">
                                @csrf
                                <div class="modal-body">
                                    <!-- Warning Alert -->
                                    <div class="alert alert-warning alert-dismissible bg-warning text-white alert-label-icon fade show material-shadow"
                                        role="alert">
                                        <i class="ri-alert-line label-icon"></i><strong>PEHATIAN</strong> - Pastikan data
                                        sudah divalidasi dengan baik!
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>

                                    <div class="mb-3">
                                        <label for="keterangan" class="form-label">Keterangan Revisi</label>
                                        <textarea name="keterangan" id="keterangan" rows="3"
                                            class="form-control @error('keterangan') is-invalid @enderror" placeholder="Masukkan keterangan">{{ old('keterangan', $dataLapangan->keterangan) }}</textarea>
                                        @error('keterangan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="fas fa-times me-2"></i>Batal
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @if ($errors->hasAny(['email', 'verifikator', 'tanggal_verifikasi']))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            new bootstrap.Modal(document.getElementById('modalUpdateEmail')).show();
                        });
                    </script>
                @endif

                @if ($errors->hasAny(['keterangan']))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            new bootstrap.Modal(document.getElementById('modalRevisi')).show();
                        });
                    </script>
                @endif

                <!-- Card Data Informasi -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <span>Data Informasi</span>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <strong>Nama Pendamping</strong>
                            <p class="text-muted mb-0">{{ $dataLapangan->enumerator->nama_lengkap }}</p>
                        </div>

                        <hr>

                        <div class="form-group mb-3">
                            <strong>Nama Pelaku Usaha</strong>
                            <p class="text-muted mb-0">{{ $dataLapangan->nama_pu }}</p>
                        </div>

                        <hr>

                        <div class="form-group mb-3">
                            <strong>NIK</strong>
                            <p class="text-muted mb-0">{{ $dataLapangan->nik }}</p>
                        </div>
                        <hr>
                        <div class="form-group mb-3">
                            <strong>No Telephone</strong>
                            <p class="text-muted mb-0">{{ $dataLapangan->telephone ?? 'Tidak Ada Data' }}</p>
                        </div>

                        <hr>
                        <div class="form-group mb-3">
                            <strong>Email</strong>
                            <p class="text-muted mb-0">{{ $dataLapangan->email ?? 'Tidak Ada Data' }}</p>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <strong>Nama Produk</strong>
                                    <p class="text-muted mb-0">{{ $dataLapangan->nama_produk ?? 'Tidak Ada Data' }}</p>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group mb-3">
                            <strong>Alamat</strong>
                            <p class="text-muted mb-0">{{ $dataLapangan->alamat }}</p>
                        </div>

                        <hr>

                        <div class="form-group mb-0">
                            <strong>Status</strong>
                            <p class="mb-0 mt-2">
                                @if ($dataLapangan->status == 'PENDING')
                                    <span class="badge bg-warning">{{ $dataLapangan->status }}</span>
                                @elseif($dataLapangan->status == 'TERVERIFIKASI')
                                    <span class="badge bg-secondary">{{ $dataLapangan->status }}</span>
                                @elseif($dataLapangan->status == 'PROGRESS OSS')
                                    <span class="badge bg-info">{{ $dataLapangan->status }}</span>
                                @elseif($dataLapangan->status == 'PROGRESS SIHALAL')
                                    <span class="badge bg-primary">{{ $dataLapangan->status }}</span>
                                @elseif($dataLapangan->status == 'TERBIT SH')
                                    <span class="badge bg-success">{{ $dataLapangan->status }}</span>
                                @elseif($dataLapangan->status == 'DITOLAK')
                                    <span class="badge bg-dark">{{ $dataLapangan->status }}</span>
                                @elseif($dataLapangan->status == 'REVISI')
                                    <span class="badge bg-danger">{{ $dataLapangan->status }}</span>
                                @endif
                            </p>
                        </div>

                        <hr>

                        <div class="form-group mb-0">
                            <strong>Status Pembayaran</strong>
                            <p class="mb-0 mt-2">
                                @if ($dataLapangan->status_pembayaran == 'PENDING')
                                    <span class="badge bg-warning">{{ $dataLapangan->status_pembayaran }}</span>
                                @elseif($dataLapangan->status_pembayaran == 'PENGAJUAN')
                                    <span class="badge bg-info">{{ $dataLapangan->status_pembayaran }}</span>
                                @elseif($dataLapangan->status_pembayaran == 'DIBAYAR')
                                    <span class="badge bg-success">{{ $dataLapangan->status_pembayaran }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Foto & File (Kanan) -->
            <div class="col-md-6">
                <!-- Section Foto -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span>Dokumentasi Foto</span>
                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                            data-bs-target="#modalKolaseFoto">
                            <i class="fas fa-th me-2"></i>Lihat Kolase
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Foto KTP -->
                        <div class="form-group mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Foto KTP</strong>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalFotoKTP">
                                        <i class="fas fa-eye me-2"></i>Lihat Foto
                                    </button>
                                    <a href="{{ route('superadmin.datalapangan.download-foto-ktp', $dataLapangan->id) }}"
                                        class="btn btn-primary btn-sm">
                                        <i class="fas fa-download me-2"></i>Download KTP
                                    </a>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <!-- Foto Rumah -->
                        <div class="form-group mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Foto Rumah</strong>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalFotoRumah">
                                        <i class="fas fa-eye me-2"></i>Lihat Foto
                                    </button>
                                    <a href="{{ route('superadmin.datalapangan.download-foto-rumah-pdf', $dataLapangan->id) }}"
                                        class="btn btn-primary btn-sm">Download PDF</a>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Foto Pendamping -->
                        <div class="form-group mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Foto Pendamping</strong>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalFotoPendamping">
                                        <i class="fas fa-eye me-2"></i>Lihat Foto
                                    </button>
                                    <a href="{{ route('superadmin.datalapangan.download-foto-pendamping', $dataLapangan->id) }}"
                                        class="btn btn-success btn-sm">
                                        <i class="fas fa-download me-2"></i>Download
                                    </a>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Foto Produk -->
                        <div class="form-group mb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Foto Produk</strong>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalFotoProduk">
                                        <i class="fas fa-eye me-2"></i>Lihat Foto
                                    </button>
                                    <a href="{{ route('superadmin.datalapangan.download-foto-produk', $dataLapangan->id) }}"
                                        class="btn btn-success btn-sm">
                                        <i class="fas fa-download me-2"></i>Download
                                    </a>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Foto Spotcheck - LAYOUT DIPERBAIKI -->
                        <div class="form-group mb-0">
                            <strong>Foto Spotcheck</strong>
                            @if ($dataLapangan->spotchecks && $dataLapangan->spotchecks->count() > 0)
                                <div class="mt-2">
                                    @foreach ($dataLapangan->spotchecks as $index => $spotcheck)
                                        @if ($spotcheck->foto_pu)
                                            <div class="mb-2 pb-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-muted">Spotcheck {{ $index + 1 }}</span>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-primary btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalFotoSpotcheck{{ $spotcheck->id }}">
                                                            <i class="fas fa-eye me-2"></i>Lihat
                                                        </button>
                                                        <a href="{{ asset('storage/' . $spotcheck->foto_pu) }}"
                                                            download="Spotcheck_{{ $spotcheck->nama_spotcheck ?? $index + 1 }}.jpg"
                                                            class="btn btn-success btn-sm">
                                                            <i class="fas fa-download me-2"></i>Download
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-info mt-2 mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Belum ada foto spotcheck
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- <!-- Card Form Keterangan (Tambahkan setelah card Dokumentasi Foto) -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <span><i class="fas fa-comment-alt me-2"></i>Form Keterangan Revisi</span>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('superadmin.data-lapangans.update-keterangan', $dataLapangan->id) }}"
                            method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="keterangan" class="form-label">
                                    <strong>Keterangan / Catatan Revisi</strong>
                                </label>
                                <textarea name="keterangan" id="keterangan" class="form-control" rows="5"
                                    placeholder="Masukkan keterangan atau catatan tambahan...">{{ old('keterangan', $dataLapangan->keterangan ?? '') }}</textarea>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Tambahkan catatan penting terkait data lapangan Revisi ini
                                </small>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>Simpan Keterangan
                                </button>
                            </div>
                        </form>

                        @if ($dataLapangan->keterangan)
                            <hr class="my-3">
                            <div class="alert alert-info mb-0">
                                <strong><i class="fas fa-sticky-note me-2"></i>Keterangan Tersimpan:</strong>
                                <p class="mb-0 mt-2">{{ $dataLapangan->keterangan }}</p>
                                <small class="text-muted">
                                    Terakhir diperbarui:
                                    {{ $dataLapangan->updated_at ? $dataLapangan->updated_at->format('d M Y, H:i') : '-' }}
                                </small>
                            </div>
                        @endif
                    </div>
                </div> --}}
                <!-- Section File -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <span>Dokumentasi File</span>
                    </div>
                    <div class="card-body">
                        <!-- File OSS Section -->
                        <div class="form-group mb-3">
                            <strong>File OSS:</strong>
                            @if ($dataLapangan->file_oss)
                                <div class="mt-2 d-flex gap-2">
                                    <a href="{{ asset('storage/' . $dataLapangan->file_oss) }}" target="_blank"
                                        class="btn btn-outline-success btn-sm flex-grow-1">
                                        <i class="fas fa-download me-2"></i> Download File OSS
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                        onclick="deleteFile('{{ $dataLapangan->id }}', 'oss')">
                                        <i class="fas fa-trash">Delete</i>
                                    </button>
                                </div>
                            @else
                                <div class="alert alert-warning mt-2 mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>File OSS belum tersedia
                                </div>
                            @endif

                            <!-- Upload Form OSS -->
                            <div class="mt-2">
                                <form
                                    action="{{ route('superadmin.data-lapangans.upload-file', $dataLapangan->hashed_id) }}"
                                    method="POST" enctype="multipart/form-data" id="uploadOssForm">
                                    @csrf
                                    <input type="hidden" name="file_type" value="oss">
                                    <div class="input-group">
                                        <input type="file" class="form-control" name="file" id="file_oss"
                                            accept=".pdf" required>
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-upload me-2"></i>Upload
                                        </button>
                                    </div>
                                    <small class="text-muted">Format: PDF (Max: 5MB)</small>
                                </form>
                            </div>
                        </div>

                        <hr>

                        <!-- File SIHALAL Section -->
                        <div class="form-group mb-0">
                            <strong>File SIHALAL:</strong>
                            @if ($dataLapangan->file_sihalal)
                                <div class="mt-2 d-flex gap-2">
                                    <a href="{{ asset('storage/' . $dataLapangan->file_sihalal) }}" target="_blank"
                                        class="btn btn-outline-success btn-sm flex-grow-1">
                                        <i class="fas fa-download me-2"></i> Download File SIHALAL
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                        onclick="deleteFile('{{ $dataLapangan->id }}', 'sihalal')">
                                        <i class="fas fa-trash">Delete</i>
                                    </button>
                                </div>
                            @else
                                <div class="alert alert-warning mt-2 mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>File SIHALAL belum tersedia
                                </div>
                            @endif

                            <!-- Upload Form SIHALAL -->
                            <div class="mt-2">
                                <form
                                    action="{{ route('superadmin.data-lapangans.upload-file', $dataLapangan->hashed_id) }}"
                                    method="POST" enctype="multipart/form-data" id="uploadSihalalForm">
                                    @csrf
                                    <input type="hidden" name="file_type" value="sihalal">
                                    <div class="input-group">
                                        <input type="file" class="form-control" name="file" id="file_sihalal"
                                            accept=".pdf" required>
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-upload me-2"></i>Upload
                                        </button>
                                    </div>
                                    <small class="text-muted">Format: PDF (Max: 5MB)</small>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Kolase Foto - UPDATED: Only 2 Photos -->
    <div class="modal fade" id="modalKolaseFoto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kolase Dokumentasi Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3" id="collageContent">
                    <div class="row g-3">
                        <!-- Grid Layout: 2 Photos in a Row -->
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light py-2 px-3">
                                    <small class="fw-bold">Foto Pendamping</small>
                                </div>
                                <img src="{{ asset('storage/' . $dataLapangan->foto_pendamping) }}" alt="Foto Pendamping"
                                    class="card-img-bottom collage-img"
                                    style="height: 300px; object-fit: cover; cursor: pointer;"
                                    onclick="viewFullImage('{{ asset('storage/' . $dataLapangan->foto_pendamping) }}', 'Foto Pendamping')">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light py-2 px-3">
                                    <small class="fw-bold">Foto Produk</small>
                                </div>
                                <img src="{{ asset('storage/' . $dataLapangan->foto_produk) }}" alt="Foto Produk"
                                    class="card-img-bottom collage-img"
                                    style="height: 300px; object-fit: cover; cursor: pointer;"
                                    onclick="viewFullImage('{{ asset('storage/' . $dataLapangan->foto_produk) }}', 'Foto Produk')">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Tutup
                    </button>
                    <button type="button" class="btn btn-success btn-sm" onclick="downloadCollage()">
                        <i class="fas fa-download me-2"></i>Download Kolase
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="printCollage()">
                        <i class="fas fa-print me-2"></i>Print Kolase
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal View Full Image -->
    <div class="modal fade" id="modalFullImage" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fullImageTitle">Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-3">
                    <img id="fullImageSrc" src="" alt="Full Image" class="img-fluid rounded"
                        style="max-height: 600px;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Tutup
                    </button>
                    <button type="button" class="btn btn-success btn-sm" onclick="downloadSingleImage()">
                        <i class="fas fa-download me-2"></i>Download Foto
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Foto KTP -->
    <div class="modal fade" id="modalFotoKTP" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Foto KTP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-3">
                    <img src="{{ asset('storage/' . $dataLapangan->foto_ktp) }}" alt="Foto KTP"
                        class="img-fluid rounded" style="max-height: 500px;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Foto Rumah -->
    <div class="modal fade" id="modalFotoRumah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Foto Rumah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-3">
                    <img src="{{ asset('storage/' . $dataLapangan->foto_rumah) }}" alt="Foto Rumah"
                        class="img-fluid rounded" style="max-height: 500px;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Foto Pendamping -->
    <div class="modal fade" id="modalFotoPendamping" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Foto Pendamping</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-3">
                    <img src="{{ asset('storage/' . $dataLapangan->foto_pendamping) }}" alt="Foto Pendamping"
                        class="img-fluid rounded" style="max-height: 500px;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Foto Produk -->
    <div class="modal fade" id="modalFotoProduk" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Foto Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-3">
                    <img src="{{ asset('storage/' . $dataLapangan->foto_produk) }}" alt="Foto Produk"
                        class="img-fluid rounded" style="max-height: 500px;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Foto Spotcheck -->
    @if ($dataLapangan->spotchecks && $dataLapangan->spotchecks->count() > 0)
        @foreach ($dataLapangan->spotchecks as $spotcheck)
            @if ($spotcheck->foto_pu)
                <div class="modal fade" id="modalFotoSpotcheck{{ $spotcheck->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    Foto Spotcheck
                                    @if ($spotcheck->nama_spotcheck)
                                        - {{ $spotcheck->nama_spotcheck }}
                                    @endif
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center p-3">
                                <img src="{{ asset('storage/' . $spotcheck->foto_pu) }}" alt="Foto Spotcheck"
                                    class="img-fluid rounded" style="max-height: 500px;">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-2"></i>Tutup
                                </button>
                                <a href="{{ asset('storage/' . $spotcheck->foto_pu) }}"
                                    download="Spotcheck_{{ $spotcheck->nama_spotcheck ?? $spotcheck->id }}.jpg"
                                    class="btn btn-success btn-sm">
                                    <i class="fas fa-download me-2"></i>Download
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endif

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        function deleteFile(id, fileType) {
            if (confirm('Apakah Anda yakin ingin menghapus file ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('superadmin/data-lapangans') }}/${id}/delete-file`;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                const fileTypeInput = document.createElement('input');
                fileTypeInput.type = 'hidden';
                fileTypeInput.name = 'file_type';
                fileTypeInput.value = fileType;
                form.appendChild(fileTypeInput);

                document.body.appendChild(form);
                form.submit();
            }
        }

        // File validation
        document.getElementById('file_oss').addEventListener('change', function(e) {
            validatePdfFile(e.target);
        });

        document.getElementById('file_sihalal').addEventListener('change', function(e) {
            validatePdfFile(e.target);
        });

        function validatePdfFile(input) {
            const file = input.files[0];
            if (file) {
                if (file.type !== 'application/pdf') {
                    alert('File harus berformat PDF!');
                    input.value = '';
                    return false;
                }

                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran file maksimal 5MB!');
                    input.value = '';
                    return false;
                }
            }
        }

        // // Auto hide alerts after 5 seconds
        // setTimeout(function() {
        //     var alerts = document.querySelectorAll('.alert');
        //     alerts.forEach(function(alert) {
        //         var bsAlert = new bootstrap.Alert(alert);
        //         bsAlert.close();
        //     });
        // }, 5000);

        // View full image function
        function viewFullImage(src, title) {
            document.getElementById('fullImageSrc').src = src;
            document.getElementById('fullImageTitle').textContent = title;
            const modal = new bootstrap.Modal(document.getElementById('modalFullImage'));
            modal.show();
        }

        // Download single image
        function downloadSingleImage() {
            const imgSrc = document.getElementById('fullImageSrc').src;
            const imgTitle = document.getElementById('fullImageTitle').textContent;
            const fileName = imgTitle.replace(/\s+/g, '_') + '.jpg';

            fetch(imgSrc)
                .then(response => response.blob())
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = fileName;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                })
                .catch(err => {
                    console.error('Error downloading image:', err);
                    alert('Gagal mendownload gambar');
                });
        }

        // Download collage as image
        function downloadCollage() {
            const collageContent = document.getElementById('collageContent');
            const namaPU = '{{ $dataLapangan->nama_pu }}';

            // Show loading indicator
            const loadingDiv = document.createElement('div');
            loadingDiv.innerHTML =
                '<div class="text-center"><i class="fas fa-spinner fa-spin me-2"></i>Memproses download...</div>';
            loadingDiv.style.position = 'fixed';
            loadingDiv.style.top = '50%';
            loadingDiv.style.left = '50%';
            loadingDiv.style.transform = 'translate(-50%, -50%)';
            loadingDiv.style.backgroundColor = 'rgba(0,0,0,0.8)';
            loadingDiv.style.color = 'white';
            loadingDiv.style.padding = '20px';
            loadingDiv.style.borderRadius = '10px';
            loadingDiv.style.zIndex = '9999';
            document.body.appendChild(loadingDiv);

            html2canvas(collageContent, {
                scale: 2,
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff'
            }).then(canvas => {
                canvas.toBlob(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'Kolase_Foto_' + namaPU.replace(/\s+/g, '_') + '.jpg';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                    document.body.removeChild(loadingDiv);
                }, 'image/jpeg', 0.95);
            }).catch(err => {
                console.error('Error creating collage:', err);
                alert('Gagal membuat kolase gambar');
                document.body.removeChild(loadingDiv);
            });
        }

        // Print collage function
        function printCollage() {
            const printContent = document.getElementById('collageContent').innerHTML;
            const printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write('<html><head><title>Kolase Foto</title>');
            printWindow.document.write(
                '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">'
            );
            printWindow.document.write('<style>');
            printWindow.document.write('.collage-img { height: 300px !important; object-fit: cover; }');
            printWindow.document.write('.card { break-inside: avoid; }');
            printWindow.document.write(
                '@media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }');
            printWindow.document.write('</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write(
                '<h3 class="text-center mb-4">Dokumentasi Foto - {{ $dataLapangan->nama_pu }}</h3>');
            printWindow.document.write(printContent);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 250);
        }
    </script>
@endsection
