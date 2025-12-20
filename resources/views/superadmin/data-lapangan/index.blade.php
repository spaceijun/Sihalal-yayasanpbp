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
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span>Dokumentasi File</span>
                        <small class="text-white-50">Upload untuk update status</small>
                    </div>
                    <div class="card-body">
                        <!-- File OSS Section -->
                        <div class="form-group mb-3 pb-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong class="text-primary">
                                    <i class="fas fa-file-pdf me-2"></i>File OSS
                                </strong>
                                @if ($dataLapangan->file_oss)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Tersedia
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Belum Upload</span>
                                @endif
                            </div>

                            @if ($dataLapangan->file_oss)
                                <div class="card bg-light mb-2">
                                    <div class="card-body p-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="flex-grow-1">
                                                <small class="text-muted d-block">File Terupload:</small>
                                                <span
                                                    class="text-truncate d-block">{{ basename($dataLapangan->file_oss) }}</span>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <a href="{{ asset('storage/' . $dataLapangan->file_oss) }}"
                                                    target="_blank" class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ asset('storage/' . $dataLapangan->file_oss) }}" download
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="confirmDelete('{{ $dataLapangan->id }}', 'oss', 'OSS')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Upload Form OSS -->
                            <div class="mt-2">
                                <form action="{{ route('superadmin.data-lapangans.upload-file', $dataLapangan->id) }}"
                                    method="POST" enctype="multipart/form-data" id="uploadOssForm">
                                    @csrf
                                    <input type="hidden" name="file_type" value="oss">
                                    <div class="input-group">
                                        <input type="file" class="form-control form-control-sm" name="file"
                                            id="file_oss" accept=".pdf" required
                                            onchange="showFileName(this, 'ossFileName')">
                                        <button class="btn btn-primary btn-sm" type="submit">
                                            <i class="fas fa-upload me-1"></i>Upload
                                        </button>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-info-circle me-1"></i>Format: PDF | Max: 5MB | Status akan berubah
                                        ke <strong>PROGRESS OSS</strong>
                                    </small>
                                    <small id="ossFileName" class="text-success d-none mt-1"></small>
                                </form>
                            </div>
                        </div>

                        <!-- File SIHALAL Section -->
                        <div class="form-group mb-0">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong class="text-primary">
                                    <i class="fas fa-file-pdf me-2"></i>File SIHALAL
                                </strong>
                                @if ($dataLapangan->file_sihalal)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Tersedia
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Belum Upload</span>
                                @endif
                            </div>

                            @if ($dataLapangan->file_sihalal)
                                <div class="card bg-light mb-2">
                                    <div class="card-body p-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="flex-grow-1">
                                                <small class="text-muted d-block">File Terupload:</small>
                                                <span
                                                    class="text-truncate d-block">{{ basename($dataLapangan->file_sihalal) }}</span>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <a href="{{ asset('storage/' . $dataLapangan->file_sihalal) }}"
                                                    target="_blank" class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ asset('storage/' . $dataLapangan->file_sihalal) }}" download
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="confirmDelete('{{ $dataLapangan->id }}', 'sihalal', 'SIHALAL')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Upload Form SIHALAL -->
                            <div class="mt-2">
                                <form action="{{ route('superadmin.data-lapangans.upload-file', $dataLapangan->id) }}"
                                    method="POST" enctype="multipart/form-data" id="uploadSihalalForm">
                                    @csrf
                                    <input type="hidden" name="file_type" value="sihalal">
                                    <div class="input-group">
                                        <input type="file" class="form-control form-control-sm" name="file"
                                            id="file_sihalal" accept=".pdf" required
                                            onchange="showFileName(this, 'sihalalFileName')">
                                        <button class="btn btn-primary btn-sm" type="submit">
                                            <i class="fas fa-upload me-1"></i>Upload
                                        </button>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-info-circle me-1"></i>Format: PDF | Max: 5MB | Status akan berubah
                                        ke <strong>TERBIT SH</strong>
                                    </small>
                                    <small id="sihalalFileName" class="text-success d-none mt-1"></small>
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

    <!-- Modal Konfirmasi Delete -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">Apakah Anda yakin ingin menghapus <strong id="fileTypeName"></strong>?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>Status akan otomatis berubah setelah file dihapus.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" onclick="deleteFile()">
                        <i class="fas fa-trash me-2"></i>Hapus File
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let deleteId = null;
        let deleteType = null;

        function openImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            var imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
            imageModal.show();
        }

        function confirmDelete(id, fileType, fileTypeName) {
            deleteId = id;
            deleteType = fileType;
            document.getElementById('fileTypeName').textContent = 'File ' + fileTypeName;

            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        function deleteFile() {
            if (!deleteId || !deleteType) return;

            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('superadmin/data-lapangan') }}/${deleteId}/delete-file`;

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
            fileTypeInput.value = deleteType;
            form.appendChild(fileTypeInput);

            document.body.appendChild(form);
            form.submit();
        }

        function showFileName(input, elementId) {
            const file = input.files[0];
            const element = document.getElementById(elementId);

            if (file) {
                element.textContent = `File dipilih: ${file.name}`;
                element.classList.remove('d-none');
                element.classList.add('d-block');
            } else {
                element.classList.add('d-none');
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
                    alert('⚠️ File harus berformat PDF!');
                    input.value = '';
                    return false;
                }

                // Check file size (5MB = 5 * 1024 * 1024 bytes)
                if (file.size > 5 * 1024 * 1024) {
                    alert('⚠️ Ukuran file maksimal 5MB!');
                    input.value = '';
                    return false;
                }

                // Show success message
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                console.log(`✓ File valid: ${file.name} (${fileSize} MB)`);
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
