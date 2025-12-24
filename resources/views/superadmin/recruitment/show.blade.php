@extends('layouts.app')

@section('template_title')
    {{ $recruitment->nama_lengkap ?? __('Show') . ' ' . __('Recruitment') }}
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
                <!-- Card Data Informasi -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <span><i class="fas fa-user me-2"></i>Data Pelamar</span>
                    </div>
                    <div class="card-body">
                        @if ($recruitment->koordinator_id)
                            <div class="form-group mb-3">
                                <strong>Nama Koordinator</strong>
                                <p class="text-muted mb-0">{{ $recruitment->koordinator->nama_lengkap ?? '-' }}</p>
                            </div>
                            <hr>
                        @endif

                        <div class="form-group mb-3">
                            <strong>Nama Lengkap</strong>
                            <p class="text-muted mb-0">{{ $recruitment->nama_lengkap }}</p>
                        </div>

                        <hr>

                        <div class="form-group mb-3">
                            <strong>No. Telepon</strong>
                            <p class="text-muted mb-0">
                                <a href="tel:{{ $recruitment->telephone }}" class="text-decoration-none">
                                    <i class="fas fa-phone me-2"></i>{{ $recruitment->telephone }}
                                </a>
                            </p>
                        </div>

                        <hr>

                        <div class="form-group mb-3">
                            <strong>Alamat Lengkap</strong>
                            <p class="text-muted mb-0">{{ $recruitment->alamat_lengkap }}</p>
                        </div>

                        <hr>

                        <div class="form-group mb-3">
                            <strong>Pendidikan Terakhir</strong>
                            <p class="text-muted mb-0">
                                <span class="badge bg-info">{{ $recruitment->pendidikan_terakhir }}</span>
                            </p>
                        </div>

                        <hr>

                        <div class="form-group mb-3">
                            <strong>Pengalaman</strong>
                            <p class="text-muted mb-0">{{ $recruitment->pengalaman }}</p>
                        </div>

                        <hr>

                        <div class="form-group mb-3">
                            <strong>Rekomendasi</strong>
                            <p class="text-muted mb-0">
                                @if ($recruitment->rekomendasi)
                                    <span class="badge bg-success">{{ $recruitment->rekomendasi }}</span>
                                @else
                                    <span class="text-muted fst-italic">Tidak ada rekomendasi</span>
                                @endif
                            </p>
                        </div>

                        <hr>

                        <div class="form-group mb-0">
                            <strong>Status Lamaran</strong>
                            <p class="mb-0 mt-2">
                                @if ($recruitment->status == 'Melamar')
                                    <span class="badge bg-warning text-dark">{{ $recruitment->status }}</span>
                                @elseif($recruitment->status == 'Diterima')
                                    <span class="badge bg-success">{{ $recruitment->status }}</span>
                                @elseif($recruitment->status == 'Ditolak')
                                    <span class="badge bg-danger">{{ $recruitment->status }}</span>
                                @endif
                            </p>
                        </div>

                        <hr>

                        <div class="form-group mb-0">
                            <strong>Tanggal Melamar</strong>
                            <p class="text-muted mb-0">
                                <i class="fas fa-calendar me-2"></i>
                                {{ $recruitment->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Edit Status & Dokumentasi Foto (Kanan) -->
            <div class="col-md-6">
                <!-- Card Edit Status -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <span><i class="fas fa-edit me-2"></i>Edit Status Lamaran</span>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('superadmin.recruitments.update-status', $recruitment->id) }}"
                            method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="status-1" class="form-label"><strong>Status Lamaran</strong></label>
                                <div class="row align-items-end">
                                    <div class="col-md-8">
                                        <select name="status" id="status-1" class="form-select" required>
                                            <option value="">-- Pilih Status --</option>
                                            <option value="Melamar"
                                                {{ $recruitment->status == 'Melamar' ? 'selected' : '' }}>
                                                Melamar
                                            </option>
                                            <option value="Diterima"
                                                {{ $recruitment->status == 'Diterima' ? 'selected' : '' }}>
                                                Diterima
                                            </option>
                                            <option value="Ditolak"
                                                {{ $recruitment->status == 'Ditolak' ? 'selected' : '' }}>
                                                Ditolak
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-save me-2"></i>Update
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Dropdown Koordinator (Muncul jika status Diterima) -->
                            <div class="form-group mb-3" id="koordinatorWrapper"
                                style="display: {{ $recruitment->status == 'Diterima' ? 'block' : 'none' }};">
                                <label for="koordinator_id" class="form-label">
                                    <strong>Pilih Koordinator</strong>
                                </label>
                                <select name="koordinator_id" id="koordinator_id" class="form-select">
                                    <option value="">-- Pilih Koordinator --</option>
                                    @foreach ($koordinators as $koordinator)
                                        <option value="{{ $koordinator->id }}"
                                            {{ $recruitment->koordinator_id == $koordinator->id ? 'selected' : '' }}>
                                            {{ $koordinator->nama_lengkap }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Wajib diisi jika status diterima
                                </small>
                            </div>

                            <!-- Alasan Penolakan (Muncul jika status Ditolak) -->
                            <div class="form-group mb-0" id="alasanPenolakanWrapper"
                                style="display: {{ $recruitment->status == 'Ditolak' ? 'block' : 'none' }};">
                                <label for="alasan_penolakan" class="form-label">
                                    <strong>Alasan Penolakan</strong>
                                </label>
                                <textarea name="alasan_penolakan" id="alasan_penolakan" class="form-control" rows="3"
                                    placeholder="Masukkan alasan penolakan...">{{ old('alasan_penolakan', $recruitment->alasan_penolakan ?? '') }}</textarea>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Wajib diisi jika status ditolak
                                </small>
                            </div>
                        </form>

                        @if ($recruitment->alasan_penolakan && $recruitment->status == 'Ditolak')
                            <hr class="my-3">
                            <div class="alert alert-danger mb-0">
                                <strong><i class="fas fa-exclamation-triangle me-2"></i>Alasan Penolakan:</strong>
                                <p class="mb-0 mt-2">{{ $recruitment->alasan_penolakan }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Section Foto -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-images me-2"></i>Dokumentasi Foto</span>
                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                            data-bs-target="#modalKolaseFoto">
                            <i class="fas fa-th me-2"></i>Lihat Kolase
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Foto Diri -->
                        <div class="form-group mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Foto Diri (3x4)</strong>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalFotoDiri">
                                        <i class="fas fa-eye me-2"></i>Lihat Foto
                                    </button>
                                    <a href="{{ route('superadmin.recruitments.download-foto', [$recruitment->id, 'foto_diri']) }}"
                                        class="btn btn-success btn-sm">
                                        <i class="fas fa-download me-2"></i>Download
                                    </a>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Foto KTP -->
                        <div class="form-group mb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Foto KTP</strong>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalFotoKTP">
                                        <i class="fas fa-eye me-2"></i>Lihat Foto
                                    </button>
                                    <a href="{{ route('superadmin.recruitments.download-foto', [$recruitment->id, 'foto_ktp']) }}"
                                        class="btn btn-success btn-sm">
                                        <i class="fas fa-download me-2"></i>Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Kolase Foto -->
    <div class="modal fade" id="modalKolaseFoto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kolase Dokumentasi Foto - {{ $recruitment->nama_lengkap }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3" id="collageContent">
                    <div class="row g-3">
                        <!-- Foto Diri -->
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light py-2 px-3">
                                    <small class="fw-bold">Foto Diri (3x4)</small>
                                </div>
                                <img src="{{ asset('storage/' . $recruitment->foto_diri) }}" alt="Foto Diri"
                                    class="card-img-bottom collage-img"
                                    style="height: 400px; object-fit: cover; cursor: pointer;"
                                    onclick="viewFullImage('{{ asset('storage/' . $recruitment->foto_diri) }}', 'Foto Diri')">
                            </div>
                        </div>

                        <!-- Foto KTP -->
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light py-2 px-3">
                                    <small class="fw-bold">Foto KTP</small>
                                </div>
                                <img src="{{ asset('storage/' . $recruitment->foto_ktp) }}" alt="Foto KTP"
                                    class="card-img-bottom collage-img"
                                    style="height: 400px; object-fit: cover; cursor: pointer;"
                                    onclick="viewFullImage('{{ asset('storage/' . $recruitment->foto_ktp) }}', 'Foto KTP')">
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

    <!-- Modal Foto Diri -->
    <div class="modal fade" id="modalFotoDiri" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Foto Diri (3x4)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-3">
                    <img src="{{ asset('storage/' . $recruitment->foto_diri) }}" alt="Foto Diri"
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

    <!-- Modal Foto KTP -->
    <div class="modal fade" id="modalFotoKTP" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Foto KTP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-3">
                    <img src="{{ asset('storage/' . $recruitment->foto_ktp) }}" alt="Foto KTP" class="img-fluid rounded"
                        style="max-height: 500px;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Show/Hide Koordinator dan Alasan Penolakan based on Status
        document.getElementById('status-1').addEventListener('change', function() {
            const koordinatorWrapper = document.getElementById('koordinatorWrapper');
            const alasanWrapper = document.getElementById('alasanPenolakanWrapper');
            const koordinatorSelect = document.getElementById('koordinator_id');
            const alasanTextarea = document.getElementById('alasan_penolakan');

            if (this.value === 'Diterima') {
                koordinatorWrapper.style.display = 'block';
                koordinatorSelect.required = true;
                alasanWrapper.style.display = 'none';
                alasanTextarea.required = false;
            } else if (this.value === 'Ditolak') {
                alasanWrapper.style.display = 'block';
                alasanTextarea.required = true;
                koordinatorWrapper.style.display = 'none';
                koordinatorSelect.required = false;
            } else {
                koordinatorWrapper.style.display = 'none';
                koordinatorSelect.required = false;
                alasanWrapper.style.display = 'none';
                alasanTextarea.required = false;
            }
        });

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
            const namaLengkap = '{{ $recruitment->nama_lengkap }}';

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
                    a.download = 'Kolase_Foto_' + namaLengkap.replace(/\s+/g, '_') + '.jpg';
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
            printWindow.document.write('.collage-img { height: 400px !important; object-fit: cover; }');
            printWindow.document.write('.card { break-inside: avoid; }');
            printWindow.document.write(
                '@media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }');
            printWindow.document.write('</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write(
                '<h3 class="text-center mb-4">Dokumentasi Foto - {{ $recruitment->nama_lengkap }}</h3>');
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
