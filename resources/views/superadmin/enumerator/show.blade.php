@extends('layouts.app')

@section('template_title')
    {{ $enumerator->nama_lengkap ?? __('Show') . ' ' . __('Enumerator') }}
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
                        <span><i class="fas fa-user me-2"></i>Data Enumerator</span>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <strong>Nama Koordinator</strong>
                            <p class="text-muted mb-0">{{ $enumerator->koordinator->nama_lengkap ?? '-' }}</p>
                        </div>

                        <hr>

                        <div class="form-group mb-3">
                            <strong>Nama Lengkap</strong>
                            <p class="text-muted mb-0">{{ $enumerator->nama_lengkap }}</p>
                        </div>

                        <hr>

                        <div class="form-group mb-3">
                            <strong>No. Registrasi</strong>
                            <p class="text-muted mb-0">
                                <span class="badge bg-info">{{ $enumerator->no_registrasi }}</span>
                            </p>
                        </div>

                        <hr>

                        <div class="form-group mb-3">
                            <strong>No. Telepon</strong>
                            <p class="text-muted mb-0">
                                <a href="tel:{{ $enumerator->telephone }}" class="text-decoration-none">
                                    <i class="fas fa-phone me-2"></i>{{ $enumerator->telephone }}
                                </a>
                            </p>
                        </div>

                        <hr>

                        <div class="form-group mb-3">
                            <strong>Alamat Lengkap</strong>
                            <p class="text-muted mb-0">{{ $enumerator->alamat }}</p>
                        </div>

                        <hr>

                        <div class="form-group mb-3">
                            <strong>Status</strong>
                            <p class="mb-0 mt-2">
                                @if ($enumerator->status == 'Aktif')
                                    <span class="badge bg-success">{{ $enumerator->status }}</span>
                                @else
                                    <span class="badge bg-danger">{{ $enumerator->status }}</span>
                                @endif
                            </p>
                        </div>

                        <hr>

                        <div class="form-group mb-0">
                            <strong>Tanggal Terdaftar</strong>
                            <p class="text-muted mb-0">
                                <i class="fas fa-calendar me-2"></i>
                                {{ $enumerator->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Foto & Dokumen (Kanan) -->
            <div class="col-md-6">
                <!-- Card Foto -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <span><i class="fas fa-image me-2"></i>Foto Enumerator</span>
                    </div>
                    <div class="card-body text-center">
                        @if ($enumerator->foto_diri)
                            <img src="{{ asset('storage/' . $enumerator->foto_diri) }}"
                                alt="Foto {{ $enumerator->nama_lengkap }}" class="img-fluid rounded shadow-sm"
                                style="width: 200px; height: auto; cursor: pointer;" data-bs-toggle="modal"
                                data-bs-target="#modalFoto">
                        @else
                            <div class="text-muted">
                                <i class="fas fa-user-circle" style="font-size: 150px;"></i>
                                <p class="mt-2">Foto tidak tersedia</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Card Dokumen -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <span><i class="fas fa-file-alt me-2"></i>Dokumen</span>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                data-bs-target="#modalSuratTugas">
                                <i class="fas fa-file-pdf me-2"></i>Download Surat Tugas
                            </button>

                            <button type="button" class="btn btn-info text-white" onclick="downloadIdCard()">
                                <i class="fas fa-id-card me-2"></i>Download ID Card
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal View Foto -->
    <div class="modal fade" id="modalFoto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Foto {{ $enumerator->nama_lengkap }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-3">
                    @if ($enumerator->foto_diri)
                        <img src="{{ asset('storage/' . $enumerator->foto_diri) }}"
                            alt="Foto {{ $enumerator->nama_lengkap }}" class="img-fluid rounded"
                            style="max-height: 600px;">
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Preview Surat Tugas -->
    <div class="modal fade" id="modalSuratTugas" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-file-alt me-2"></i>Preview Surat Tugas - {{ $enumerator->nama_lengkap }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" style="max-height: 70vh; overflow-y: auto;">
                    <iframe id="suratTugasFrame" src="" style="width: 100%; height: 800px; border: none;"
                        onload="suratTugasLoaded()">
                    </iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Tutup
                    </button>
                    <button type="button" class="btn btn-primary" onclick="printSuratTugas()">
                        <i class="fas fa-print me-2"></i>Print
                    </button>
                    {{-- <button type="button" class="btn btn-success" onclick="downloadSuratTugasPDF()">
                        <i class="fas fa-download me-2"></i>Download PDF
                    </button> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden ID Card Container for Export -->
    <div id="idCardContainer" style="position: absolute; left: -9999px;">
        <div style="width: 590px; height: 1004px; background: white; position: relative; overflow: hidden;">
            <div style="padding: 50px 40px 0; display: flex; gap: 20px; align-items: flex-start;">
                <img src="https://sihalal.yayasanpermatabakti.com/assets/images/stample.png"
                    style="width: 100px; height: auto;">
                <div
                    style="color: #2e0d6e; font-family: Arial, sans-serif; font-weight: 700; font-size: 24px; line-height: 1.2; text-transform: uppercase; letter-spacing: 1px;">
                    LEMBAGA PENDAMPING<br>
                    PROSES PRODUK HALAL<br>
                    KAWULO HALAL
                </div>
            </div>

            <div style="margin-top: 80px; text-align: center;">
                <div
                    style="width: 320px; height: 340px; border: 6px solid #2e0d6e; border-radius: 50px; overflow: hidden; margin: 0 auto; background: #ddd;">
                    @if ($enumerator->foto_diri)
                        <img src="{{ asset('storage/' . $enumerator->foto_diri) }}"
                            style="width: 100%; height: 100%; object-fit: cover; object-position: center top;">
                    @endif
                </div>
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <div
                    style="font-size: 48px; font-weight: 900; text-transform: uppercase; color: black; margin-bottom: 5px; letter-spacing: 1px;">
                    {{ strtoupper($enumerator->nama_lengkap) }}
                </div>
                <div style="font-size: 28px; font-weight: 500; color: black; letter-spacing: 2px;">
                    No Registrasi<br>{{ $enumerator->no_registrasi }}
                </div>
            </div>

            <div
                style="position: absolute; width: 250px; height: 250px; border: 18px solid #dce4ff; transform: rotate(45deg); bottom: 180px; left: 50px;">
            </div>
            <div
                style="position: absolute; width: 250px; height: 250px; border: 18px solid #dce4ff; transform: rotate(45deg); bottom: 80px; left: 150px;">
            </div>

            <div style="position: absolute; bottom: 0; left: 0; width: 100%; height: 180px;">
                <svg viewBox="0 0 590 150" preserveAspectRatio="none" style="width: 100%; height: 100%; display: block;">
                    <path d="M0,100 C150,150 300,50 590,10 L590,150 L0,150 Z" fill="#2e0d6e"></path>
                </svg>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Load Surat Tugas when modal is shown
        const modalSuratTugas = document.getElementById('modalSuratTugas');
        modalSuratTugas.addEventListener('show.bs.modal', function() {
            const iframe = document.getElementById('suratTugasFrame');
            iframe.src = '{{ route('superadmin.enumerators.surat-tugas', $enumerator->id) }}';
        });

        // Function when iframe loaded
        function suratTugasLoaded() {
            console.log('Surat Tugas loaded successfully');
        }

        // Print Surat Tugas
        function printSuratTugas() {
            const iframe = document.getElementById('suratTugasFrame');
            iframe.contentWindow.print();
        }

        // Download Surat Tugas as PDF
        function downloadSuratTugasPDF() {
            const iframe = document.getElementById('suratTugasFrame');
            const namaLengkap = '{{ $enumerator->nama_lengkap }}';

            // Show loading
            const loadingDiv = document.createElement('div');
            loadingDiv.innerHTML =
                '<div class="text-center"><i class="fas fa-spinner fa-spin me-2"></i>Memproses PDF...</div>';
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

            const {
                jsPDF
            } = window.jspdf;

            // Get all pages from iframe
            const pages = iframe.contentDocument.querySelectorAll('.page');
            const pdf = new jsPDF('p', 'mm', 'a4');

            let processedPages = 0;
            const totalPages = pages.length;

            Array.from(pages).forEach((page, index) => {
                html2canvas(page, {
                    scale: 2,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff'
                }).then(canvas => {
                    const imgData = canvas.toDataURL('image/jpeg', 0.95);
                    const imgWidth = 210; // A4 width in mm
                    const imgHeight = (canvas.height * imgWidth) / canvas.width;

                    if (index > 0) {
                        pdf.addPage();
                    }
                    pdf.addImage(imgData, 'JPEG', 0, 0, imgWidth, imgHeight);

                    processedPages++;

                    if (processedPages === totalPages) {
                        pdf.save('Surat_Tugas_' + namaLengkap.replace(/\s+/g, '_') + '.pdf');
                        document.body.removeChild(loadingDiv);
                    }
                }).catch(err => {
                    console.error('Error creating PDF:', err);
                    alert('Gagal membuat PDF');
                    document.body.removeChild(loadingDiv);
                });
            });
        }

        // Download ID Card as Image
        function downloadIdCard() {
            const idCardElement = document.getElementById('idCardContainer').children[0];
            const namaLengkap = '{{ $enumerator->nama_lengkap }}';

            // Show loading indicator
            const loadingDiv = document.createElement('div');
            loadingDiv.innerHTML =
                '<div class="text-center"><i class="fas fa-spinner fa-spin me-2"></i>Memproses ID Card...</div>';
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

            html2canvas(idCardElement, {
                scale: 2,
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff',
                width: 590,
                height: 1004
            }).then(canvas => {
                canvas.toBlob(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'ID_Card_' + namaLengkap.replace(/\s+/g, '_') + '.jpg';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                    document.body.removeChild(loadingDiv);
                }, 'image/jpeg', 0.95);
            }).catch(err => {
                console.error('Error creating ID Card:', err);
                alert('Gagal membuat ID Card');
                document.body.removeChild(loadingDiv);
            });
        }
    </script>
@endsection
