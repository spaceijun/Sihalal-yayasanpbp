@extends('layouts.app')

@section('template_title')
    {{ $enumerator->nama_lengkap ?? __('Show') . ' ' . __('Enumerator') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <!-- Alert Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="las la-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="las la-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="las la-exclamation-triangle me-2"></i>
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
                        <span><i class="las la-user me-2"></i>Data Enumerator</span>
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
                            <strong>No Rekening</strong>
                            <p class="text-muted mb-0">{{ $enumerator->no_rekening ?? 'Belum Tersedia' }}</p>
                        </div>

                        <hr>

                        <div class="form-group mb-3">
                            <strong>Nama Rekening</strong>
                            <p class="text-muted mb-0">{{ $enumerator->nama_rekening ?? 'Belum Tersedia' }}</p>
                        </div>

                        <hr>

                        <div class="form-group mb-3">
                            <strong>Nama Bank (Kode Bank)</strong>
                            <p class="text-muted mb-0">{{ $enumerator->bank->name ?? 'Belum Tersedia' }}
                                ({{ $enumerator->bank->code ?? 'Belum Tersedia' }})</p>
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
                                    <i class="las la-phone me-2"></i>{{ $enumerator->telephone }}
                                </a>
                            </p>
                        </div>

                        <hr>

                        <div class="form-group mb-3">
                            <strong>Alamat Lengkap</strong>
                            <p class="text-muted mb-0">{{ $enumerator->alamat }}</p>
                        </div>

                        <hr>

                        {{-- STATUS + BUTTON AKTIVASI --}}
                        <div class="form-group mb-3">
                            <strong>Status</strong>
                            <div class="d-flex align-items-center gap-3 mt-2">
                                @if ($enumerator->status == 'Aktif')
                                    <span class="badge bg-success fs-6">{{ $enumerator->status }}</span>
                                @else
                                    <span class="badge bg-danger fs-6">{{ $enumerator->status }}</span>

                                    {{-- Tombol Aktifkan Kembali --}}
                                    <form action="{{ route('superadmin.enumerators.aktivasi', $enumerator->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Aktifkan kembali enumerator {{ $enumerator->nama_lengkap }}?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="las la-user-check me-1"></i>Aktifkan Kembali
                                        </button>
                                    </form>
                                @endif
                            </div>

                            {{-- Info jika Tidak Aktif --}}
                            @if ($enumerator->status == 'Tidak Aktif')
                                <small class="text-danger mt-2 d-block">
                                    <i class="las la-info-circle me-1"></i>
                                    Enumerator ini dinonaktifkan karena tidak memenuhi target minimal
                                    20 data lapangan dalam 30 hari terakhir.
                                </small>
                            @endif
                        </div>

                        <hr>

                        <div class="form-group mb-0">
                            <strong>Tanggal Terdaftar</strong>
                            <p class="text-muted mb-0">
                                <i class="las la-calendar me-2"></i>
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
                        <span><i class="las la-image me-2"></i>Foto Enumerator</span>
                    </div>
                    <div class="card-body text-center">
                        @if ($enumerator->foto_diri)
                            <img src="{{ asset('storage/' . $enumerator->foto_diri) }}"
                                alt="Foto {{ $enumerator->nama_lengkap }}" class="img-fluid rounded shadow-sm"
                                style="width: 200px; height: auto; cursor: pointer;" data-bs-toggle="modal"
                                data-bs-target="#modalFoto">
                        @else
                            <div class="text-muted">
                                <i class="las la-user-circle" style="font-size: 150px;"></i>
                                <p class="mt-2">Foto tidak tersedia</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Card Dokumen -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <span><i class="las la-file-alt me-2"></i>Dokumen</span>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                data-bs-target="#modalSuratTugas">
                                <i class="las la-file-pdf me-2"></i>Download Surat Tugas
                            </button>

                            <button type="button" class="btn btn-info text-white" onclick="downloadIdCard()">
                                <i class="las la-id-card me-2"></i>Download ID Card
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
                        <i class="las la-times me-2"></i>Tutup
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
                        <i class="las la-file-alt me-2"></i>Preview Surat Tugas - {{ $enumerator->nama_lengkap }}
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
                        <i class="las la-times me-2"></i>Tutup
                    </button>
                    <button type="button" class="btn btn-primary" onclick="printSuratTugas()">
                        <i class="las la-print me-2"></i>Print
                    </button>
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

            <div style="text-align: center; margin-top: 30px; position: relative; z-index: 20;">
                <div
                    style="font-size: 48px; font-weight: 900; text-transform: uppercase; color: black; margin-bottom: 5px; letter-spacing: 1px;">
                    {{ strtoupper($enumerator->nama_lengkap) }}
                </div>
                <div style="font-size: 28px; font-weight: 500; color: black; letter-spacing: 2px;">
                    No Registrasi<br>{{ $enumerator->no_registrasi }}/KH-YPBP/12/2025
                </div>
            </div>

            <div style="position: absolute; bottom: 0; left: 0; width: 100%; height: 180px; z-index: 5;">
                <svg viewBox="0 0 590 150" preserveAspectRatio="none" style="width: 100%; height: 100%; display: block;">
                    <path d="M0,100 C150,150 300,50 590,10 L590,150 L0,150 Z" fill="#2e0d6e"></path>
                </svg>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        const modalSuratTugas = document.getElementById('modalSuratTugas');
        modalSuratTugas.addEventListener('show.bs.modal', function() {
            const iframe = document.getElementById('suratTugasFrame');
            iframe.src = '{{ route('superadmin.enumerators.surat-tugas', $enumerator->id) }}';
        });

        function suratTugasLoaded() {
            console.log('Surat Tugas loaded successfully');
        }

        function printSuratTugas() {
            const iframe = document.getElementById('suratTugasFrame');
            iframe.contentWindow.print();
        }

        function downloadIdCard() {
            const idCardElement = document.getElementById('idCardContainer').children[0];
            const namaLengkap = '{{ $enumerator->nama_lengkap }}';

            const loadingDiv = document.createElement('div');
            loadingDiv.innerHTML =
                '<div class="text-center"><i class="las la-spinner fa-spin me-2"></i>Memproses ID Card...</div>';
            loadingDiv.style.cssText =
                'position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:rgba(0,0,0,0.8);color:white;padding:20px;border-radius:10px;z-index:9999;';
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
