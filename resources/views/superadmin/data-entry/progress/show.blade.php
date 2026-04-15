@extends('layouts.app')

@section('template_title')
    Detail Progress — {{ $progress->dataLapangan?->nama_pu ?? 'N/A' }}
@endsection

@section('content')
    <section class="content container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="las la-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="las la-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="mb-3">
            <a href="{{ route('superadmin.data-entry-progress.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="las la-arrow-left me-1"></i>Kembali ke Daftar
            </a>
        </div>

        <div class="row">
            {{-- Kolom kiri: Info Progress --}}
            <div class="col-md-5">

                {{-- Card Status Progress --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <i class="las la-info-circle me-2"></i>Informasi Progress
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-5">Data Entry</dt>
                            <dd class="col-sm-7">{{ $progress->dataEntry?->user?->name ?? '-' }}</dd>

                            <dt class="col-sm-5">Entry Type</dt>
                            <dd class="col-sm-7">
                                @if ($progress->dataEntry?->entry_type === 'OSS')
                                    <span class="badge bg-info">OSS</span>
                                @elseif ($progress->dataEntry?->entry_type === 'SIHALAL')
                                    <span class="badge bg-primary">SIHALAL</span>
                                @endif
                            </dd>

                            <dt class="col-sm-5">Tanggal Aksi</dt>
                            <dd class="col-sm-7">{{ $progress->actioned_at?->format('d M Y H:i') ?? '-' }}</dd>

                            <dt class="col-sm-5">File Type</dt>
                            <dd class="col-sm-7">
                                <span class="badge bg-light text-dark border">
                                    {{ strtoupper($progress->new_data['file_type'] ?? '-') }}
                                </span>
                            </dd>

                            <dt class="col-sm-5">Nama File</dt>
                            <dd class="col-sm-7">
                                <small>{{ $progress->new_data['file_name'] !== 'N/A' ? $progress->new_data['file_name'] ?? '-' : 'Update Status' }}</small>
                            </dd>

                            <dt class="col-sm-5">Status</dt>
                            <dd class="col-sm-7">
                                @if ($progress->status === 'PENDING')
                                    <span class="badge bg-warning text-dark">PENDING</span>
                                @elseif ($progress->status === 'DITERIMA')
                                    <span class="badge bg-success">DITERIMA</span>
                                @elseif ($progress->status === 'REVISI')
                                    <span class="badge bg-warning text-dark">REVISI</span>
                                @elseif ($progress->status === 'DITOLAK')
                                    <span class="badge bg-dark">DITOLAK</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>

                {{-- Catatan Revisi / Keterangan --}}
                @if ($progress->keterangan_revisi || $progress->keterangan_update)
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <i class="las la-comments me-2"></i>Riwayat Keterangan
                        </div>
                        <div class="card-body">
                            @if ($progress->keterangan_revisi)
                                <div class="mb-2">
                                    <small class="text-muted fw-bold">Catatan Superadmin:</small>
                                    <p class="mb-0 text-danger">{{ $progress->keterangan_revisi }}</p>
                                </div>
                            @endif
                            @if ($progress->keterangan_update)
                                <hr class="my-2">
                                <div>
                                    <small class="text-muted fw-bold">Balasan Data Entry:</small>
                                    <p class="mb-0 text-success">{{ $progress->keterangan_update }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- Kolom kanan: Data Lapangan --}}
            <div class="col-md-7">
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <i class="las la-user me-2"></i>Data Pelaku Usaha
                    </div>
                    <div class="card-body">
                        @php $dl = $progress->dataLapangan; @endphp
                        @if ($dl)
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Nama PU</dt>
                                <dd class="col-sm-8">{{ $dl->nama_pu }}</dd>

                                <dt class="col-sm-4">NIK</dt>
                                <dd class="col-sm-8">{{ $dl->nik }}</dd>

                                <dt class="col-sm-4">Telephone</dt>
                                <dd class="col-sm-8">{{ $dl->telephone }}</dd>

                                <dt class="col-sm-4">Email</dt>
                                <dd class="col-sm-8">{{ $dl->email ?? '-' }}</dd>

                                <dt class="col-sm-4">Nama Produk</dt>
                                <dd class="col-sm-8">{{ $dl->nama_produk ?? '-' }}</dd>

                                <dt class="col-sm-4">Alamat</dt>
                                <dd class="col-sm-8">{{ $dl->alamat }}</dd>

                                <dt class="col-sm-4">Pendamping</dt>
                                <dd class="col-sm-8">{{ $dl->enumerator?->nama_lengkap ?? '-' }}</dd>

                                <dt class="col-sm-4">Status Data</dt>
                                <dd class="col-sm-8">
                                    @php $status = $dl->status; @endphp
                                    @if ($status === 'PROGRESS OSS')
                                        <span class="badge bg-info">{{ $status }}</span>
                                    @elseif ($status === 'PROGRESS SIHALAL')
                                        <span class="badge bg-primary">{{ $status }}</span>
                                    @elseif ($status === 'TERBIT SH')
                                        <span class="badge bg-success">{{ $status }}</span>
                                    @elseif ($status === 'DITOLAK')
                                        <span class="badge bg-dark">{{ $status }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $status }}</span>
                                    @endif
                                </dd>
                            </dl>
                        @else
                            <p class="text-muted mb-0">Data lapangan tidak ditemukan.</p>
                        @endif
                    </div>
                </div>

                {{-- Preview / Download File --}}
                @if ($dl && $progress->dataEntry?->entry_type === 'OSS' && $dl->file_oss)
                    <div class="card">
                        <div class="card-header bg-light">
                            <i class="las la-file-pdf me-2 text-danger"></i>File OSS
                        </div>
                        <div class="card-body">
                            <a href="{{ asset('storage/' . $dl->file_oss) }}" target="_blank"
                                class="btn btn-outline-danger w-100">
                                <i class="las la-external-link-alt me-2"></i>Buka / Download File OSS
                            </a>
                        </div>
                    </div>
                @endif

                @if ($dl && $progress->dataEntry?->entry_type === 'SIHALAL' && $dl->file_sihalal)
                    <div class="card">
                        <div class="card-header bg-light">
                            <i class="las la-file-pdf me-2 text-danger"></i>File SIHALAL
                        </div>
                        <div class="card-body">
                            <a href="{{ asset('storage/' . $dl->file_sihalal) }}" target="_blank"
                                class="btn btn-outline-primary w-100">
                                <i class="las la-external-link-alt me-2"></i>Buka / Download File SIHALAL
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Modal Revisi --}}
    <div class="modal fade" id="modalRevisi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="las la-edit me-2"></i>Minta Revisi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('superadmin.data-entry-progress.revisi', $progress->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <label class="form-label fw-bold">Catatan Revisi <span class="text-danger">*</span></label>
                        <textarea name="keterangan_revisi" class="form-control" rows="4"
                            placeholder="Jelaskan apa yang perlu diperbaiki..." required></textarea>
                        <small class="text-muted">Catatan ini akan ditampilkan ke data entry.</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning btn-sm">
                            <i class="las la-paper-plane me-2"></i>Kirim Revisi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Tolak --}}
    <div class="modal fade" id="modalTolak" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="las la-times-circle me-2"></i>Tolak Progress</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('superadmin.data-entry-progress.tolak', $progress->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="alert alert-danger py-2">
                            <small><i class="las la-exclamation-triangle me-1"></i>Data ini akan ditolak permanen.</small>
                        </div>
                        <label class="form-label fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="keterangan_revisi" class="form-control" rows="4" placeholder="Jelaskan alasan penolakan..."
                            required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="las la-times me-2"></i>Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        setTimeout(function() {
            document.querySelectorAll('.alert-dismissible').forEach(function(el) {
                new bootstrap.Alert(el).close();
            });
        }, 5000);
    </script>
@endsection
