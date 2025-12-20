@extends('layouts.app')

@section('template_title')
    {{ $dataLapangan->name ?? __('Show') . ' ' . __('Data Lapangan') }}
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
                        <form action="{{ route('superadmin.data-lapangans.update-status', $dataLapangan->id) }}"
                            method="POST">
                            @csrf
                            <div class="row align-items-end">
                                <div class="col-md-8">
                                    {{-- <label for="" class="form-label fw-bold">Pilih Status</label> --}}
                                    <select name="status" id="" class="form-select" required>
                                        <option value="">-- Pilih Status --</option>
                                        <option value="PENDING" {{ $dataLapangan->status == 'PENDING' ? 'selected' : '' }}>
                                            PENDING
                                        </option>
                                        <option value="PROGRESS OSS"
                                            {{ $dataLapangan->status == 'PROGRESS OSS' ? 'selected' : '' }}>
                                            PROGRESS OSS
                                        </option>
                                        <option value="PROGRESS SIHALAL"
                                            {{ $dataLapangan->status == 'PROGRESS SIHALAL' ? 'selected' : '' }}>
                                            PROGRESS SIHALAL
                                        </option>
                                        <option value="TERBIT SH"
                                            {{ $dataLapangan->status == 'TERBIT SH' ? 'selected' : '' }}>
                                            TERBIT SH
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
                </div>

                <!-- Card Data Informasi -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <span>Data Informasi</span>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <strong>Nama Pendamping</strong>
                            <p class="text-muted mb-0">{{ $dataLapangan->enumerator_id }}</p>
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

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <strong>RT</strong>
                                    <p class="text-muted mb-0">{{ $dataLapangan->rt }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <strong>RW</strong>
                                    <p class="text-muted mb-0">{{ $dataLapangan->rw }}</p>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group mb-3">
                            <strong>Alamat</strong>
                            <p class="text-muted mb-0">{{ $dataLapangan->alamat }}</p>
                        </div>

                        <hr>

                        <div class="form-group mb-3">
                            <strong>Titik Koordinat</strong>
                            <p class="text-muted mb-0">{{ $dataLapangan->titik_koordinat }}</p>
                        </div>

                        <hr>

                        <div class="form-group mb-0">
                            <strong>Status</strong>
                            <p class="mb-0 mt-2">
                                @if ($dataLapangan->status == 'PENDING')
                                    <span class="badge bg-warning text-dark">{{ $dataLapangan->status }}</span>
                                @elseif($dataLapangan->status == 'PROGRESS OSS')
                                    <span class="badge bg-info">{{ $dataLapangan->status }}</span>
                                @elseif($dataLapangan->status == 'PROGRESS SIHALAL')
                                    <span class="badge bg-primary">{{ $dataLapangan->status }}</span>
                                @elseif($dataLapangan->status == 'TERBIT SH')
                                    <span class="badge bg-success">{{ $dataLapangan->status }}</span>
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
                    <div class="card-header bg-primary text-white">
                        <span>Dokumentasi Foto</span>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <strong>Foto KTP</strong>
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $dataLapangan->foto_ktp) }}" alt="Foto KTP"
                                    class="img-fluid rounded border"
                                    style="max-height: 180px; width: 100%; object-fit: cover; cursor: pointer;"
                                    onclick="openImageModal(this.src)">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <strong>Foto Rumah</strong>
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $dataLapangan->foto_rumah) }}" alt="Foto Rumah"
                                    class="img-fluid rounded border"
                                    style="max-height: 180px; width: 100%; object-fit: cover; cursor: pointer;"
                                    onclick="openImageModal(this.src)">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <strong>Foto Pendamping</strong>
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $dataLapangan->foto_pendamping) }}" alt="Foto Pendamping"
                                    class="img-fluid rounded border"
                                    style="max-height: 180px; width: 100%; object-fit: cover; cursor: pointer;"
                                    onclick="openImageModal(this.src)">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <strong>Foto Proses</strong>
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $dataLapangan->foto_proses) }}" alt="Foto Proses"
                                    class="img-fluid rounded border"
                                    style="max-height: 180px; width: 100%; object-fit: cover; cursor: pointer;"
                                    onclick="openImageModal(this.src)">
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <strong>Foto Produk</strong>
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $dataLapangan->foto_produk) }}" alt="Foto Produk"
                                    class="img-fluid rounded border"
                                    style="max-height: 180px; width: 100%; object-fit: cover; cursor: pointer;"
                                    onclick="openImageModal(this.src)">
                            </div>
                        </div>
                    </div>
                </div>

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
                                        <i class="fas fa-trash">Delete File</i>
                                    </button>
                                </div>
                            @else
                                <div class="alert alert-warning mt-2 mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>File OSS belum tersedia
                                </div>
                            @endif

                            <!-- Upload Form OSS -->
                            <div class="mt-2">
                                <form action="{{ route('superadmin.data-lapangans.upload-file', $dataLapangan->id) }}"
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
                                        <i class="fas fa-trash">Delete File</i>
                                    </button>
                                </div>
                            @else
                                <div class="alert alert-warning mt-2 mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>File SIHALAL belum tersedia
                                </div>
                            @endif

                            <!-- Upload Form SIHALAL -->
                            <div class="mt-2">
                                <form action="{{ route('superadmin.data-lapangans.upload-file', $dataLapangan->id) }}"
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

    <!-- Modal untuk melihat gambar lebih besar -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" id="modalImage" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function openImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            var imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
            imageModal.show();
        }

        function deleteFile(id, fileType) {
            if (confirm('Apakah Anda yakin ingin menghapus file ini?')) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('superadmin/data-lapangans') }}/${id}/delete-file`;

                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                // Add file type
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
                // Check file type
                if (file.type !== 'application/pdf') {
                    alert('File harus berformat PDF!');
                    input.value = '';
                    return false;
                }

                // Check file size (5MB = 5 * 1024 * 1024 bytes)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran file maksimal 5MB!');
                    input.value = '';
                    return false;
                }
            }
        }

        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
@endsection
