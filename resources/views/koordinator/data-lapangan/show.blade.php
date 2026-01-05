@extends('layouts.app')

@section('template_title')
    {{ $dataLapangan->name ?? __('Show') . ' ' . __('Data Lapangan') }}
@endsection

@section('content')
    <section class="content container-fluid">
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
            <!-- Card Data Informasi -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span>Data Informasi</span>

                        <!-- Button Update Status - Hanya muncul jika status PROGRESS OSS -->
                        @if ($dataLapangan->status == 'PROGRESS OSS' || $dataLapangan->status == 'DITOLAK')
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                data-bs-target="#modalUpdateStatusHalal">
                                <i class="fas fa-edit me-2"></i>Update Status Halal
                            </button>
                        @endif
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
                                @elseif($dataLapangan->status == 'DITOLAK')
                                    <span class="badge bg-danger">{{ $dataLapangan->status }}</span>
                                @endif
                            </p>
                        </div>

                        <hr>

                        <div class="form-group mb-0">
                            <strong>Status Pembayaran</strong>
                            <p class="mb-0 mt-2">
                                @if ($dataLapangan->status_pembayaran == 'PENDING')
                                    <span class="badge bg-warning text-dark">{{ $dataLapangan->status_pembayaran }}</span>
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

            <!-- Card Dokumentasi Foto & File -->
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span>Dokumentasi Foto</span>
                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                            data-bs-target="#modalKolaseFoto">
                            <i class="fas fa-th me-2"></i>Lihat Kolase
                        </button>
                    </div>
                    <div class="card-body">
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
                        <div class="form-group mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Foto Pendamping</strong>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#modalFotoPendamping">
                                    <i class="fas fa-eye me-2"></i>Lihat Foto
                                </button>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Foto Proses</strong>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#modalFotoProses">
                                    <i class="fas fa-eye me-2"></i>Lihat Foto
                                </button>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group mb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Foto Produk</strong>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#modalFotoProduk">
                                    <i class="fas fa-eye me-2"></i>Lihat Foto
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <span>Dokumentasi File</span>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <strong>File OSS</strong>
                            @if ($dataLapangan->file_oss)
                                <div class="mt-2 d-flex gap-2">
                                    <a href="{{ asset('storage/' . $dataLapangan->file_oss) }}" target="_blank"
                                        class="btn btn-outline-success btn-sm flex-grow-1">
                                        <i class="fas fa-download me-2"></i> Download File OSS
                                    </a>
                                </div>
                            @else
                                <div class="alert alert-warning mt-2 mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>File OSS belum tersedia
                                </div>
                            @endif
                        </div>

                        <hr>

                        <div class="form-group mb-0">
                            <strong>File SIHALAL</strong>
                            @if ($dataLapangan->file_sihalal)
                                <div class="mt-2 d-flex gap-2">
                                    <a href="{{ asset('storage/' . $dataLapangan->file_sihalal) }}" target="_blank"
                                        class="btn btn-outline-success btn-sm flex-grow-1">
                                        <i class="fas fa-download me-2"></i> Download File SIHALAL
                                    </a>
                                </div>
                            @else
                                <div class="alert alert-warning mt-2 mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>File SIHALAL belum tersedia
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Include Modal Update Status -->
    @include('koordinator.data-lapangan.partials.status-modal')

    <!-- Modal Kolase Foto -->
    <div class="modal fade" id="modalKolaseFoto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kolase Dokumentasi Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3" id="collageContent">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light py-2 px-3">
                                    <small class="fw-bold">Foto Pendamping</small>
                                </div>
                                <img src="{{ asset('storage/' . $dataLapangan->foto_pendamping) }}" alt="Foto Pendamping"
                                    class="card-img-bottom collage-img"
                                    style="height: 250px; object-fit: cover; cursor: pointer;"
                                    onclick="viewFullImage('{{ asset('storage/' . $dataLapangan->foto_pendamping) }}', 'Foto Pendamping')">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light py-2 px-3">
                                    <small class="fw-bold">Foto Proses</small>
                                </div>
                                <img src="{{ asset('storage/' . $dataLapangan->foto_proses) }}" alt="Foto Proses"
                                    class="card-img-bottom collage-img"
                                    style="height: 250px; object-fit: cover; cursor: pointer;"
                                    onclick="viewFullImage('{{ asset('storage/' . $dataLapangan->foto_proses) }}', 'Foto Proses')">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light py-2 px-3">
                                    <small class="fw-bold">Foto Produk</small>
                                </div>
                                <img src="{{ asset('storage/' . $dataLapangan->foto_produk) }}" alt="Foto Produk"
                                    class="card-img-bottom collage-img"
                                    style="height: 250px; object-fit: cover; cursor: pointer;"
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

    <!-- Modal Foto Proses -->
    <div class="modal fade" id="modalFotoProses" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Foto Proses</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-3">
                    <img src="{{ asset('storage/' . $dataLapangan->foto_proses) }}" alt="Foto Proses"
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        // Auto hide alerts after 5 seconds - KECUALI alert di dalam modal
        setTimeout(function() {
            // Hanya ambil alert yang ada di luar modal
            var alerts = document.querySelectorAll('.content.container-fluid > .alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

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
            printWindow.document.write('.collage-img { height: 250px !important; object-fit: cover; }');
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
