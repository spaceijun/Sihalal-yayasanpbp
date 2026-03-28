@extends('layouts.app')

@section('template_title')
    Review Progress Data Entry
@endsection

@section('content')
    <div class="row">
        <div class="col">
            @include('layouts.messages')

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span id="card_title">
                            <i class="las la-tasks me-2"></i>Review Progress Data Entry
                        </span>
                        <div class="d-flex gap-2">
                            {{-- Bulk Terima --}}
                            <button type="button" id="btnBulkTerima" class="btn btn-success btn-sm" disabled
                                onclick="submitBulkTerima()">
                                <i class="las la-check-double me-1"></i>Terima Semua Dipilih
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Tab Status --}}
                <div class="card-header bg-white p-0 border-bottom" style="overflow: visible;">
                    <ul class="nav nav-tabs card-header-tabs px-3 pt-2 mb-0" id="statusTabs"
                        style="margin-bottom: -1px !important;">
                        <li class="nav-item">
                            <a class="nav-link {{ !request('status') || in_array(request('status'), ['PENDING', 'REVISI']) ? 'active' : '' }}"
                                href="{{ request()->fullUrlWithQuery(['status' => '']) }}">
                                Butuh Review
                                @if ($countPending + $countRevisi > 0)
                                    <span class="badge bg-danger ms-1">{{ $countPending + $countRevisi }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('status') === 'PENDING' ? 'active' : '' }}"
                                href="{{ request()->fullUrlWithQuery(['status' => 'PENDING']) }}">
                                Pending
                                @if ($countPending > 0)
                                    <span class="badge bg-warning text-dark ms-1">{{ $countPending }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('status') === 'REVISI' ? 'active' : '' }}"
                                href="{{ request()->fullUrlWithQuery(['status' => 'REVISI']) }}">
                                Revisi
                                @if ($countRevisi > 0)
                                    <span class="badge bg-warning text-dark ms-1">{{ $countRevisi }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('status') === 'DITERIMA' ? 'active' : '' }}"
                                href="{{ request()->fullUrlWithQuery(['status' => 'DITERIMA']) }}">
                                Diterima
                                @if ($countDiterima > 0)
                                    <span class="badge bg-success ms-1">{{ $countDiterima }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('status') === 'DITOLAK' ? 'active' : '' }}"
                                href="{{ request()->fullUrlWithQuery(['status' => 'DITOLAK']) }}">
                                Ditolak
                                @if ($countDitolak > 0)
                                    <span class="badge bg-dark ms-1">{{ $countDitolak }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Filter --}}
                <div class="card-body bg-white border-bottom">
                    <form method="GET" action="{{ route('superadmin.data-entry-progress.index') }}" id="filterForm">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label">Cari Nama PU / Data Entry</label>
                                <input type="text" class="form-control" name="search"
                                    placeholder="Cari nama PU atau nama data entry..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Entry Type</label>
                                <select class="form-control" name="entry_type">
                                    <option value="">Semua Type</option>
                                    <option value="OSS" {{ request('entry_type') === 'OSS' ? 'selected' : '' }}>OSS
                                    </option>
                                    <option value="SIHALAL" {{ request('entry_type') === 'SIHALAL' ? 'selected' : '' }}>
                                        SIHALAL</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="las la-search me-1"></i>Filter
                                </button>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <a href="{{ route('superadmin.data-entry-progress.index') }}"
                                    class="btn btn-outline-secondary w-100">
                                    <i class="las la-redo me-1"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body bg-white">
                    <form id="bulkForm" action="{{ route('superadmin.data-entry-progress.bulk-terima') }}" method="POST">
                        @csrf
                        <input type="hidden" name="verifikator_id" id="bulkVerifikatorId">
                        <input type="hidden" name="tanggal_verifikasi" id="bulkTanggalVerifikasi">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="thead">
                                    <tr>
                                        <th width="40">
                                            <input type="checkbox" id="checkAll" class="form-check-input"
                                                title="Pilih semua">
                                        </th>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Data Entry</th>
                                        <th>Type</th>
                                        <th>Nama PU</th>
                                        <th>Status</th>
                                        <th>Verifikator</th>
                                        <th>Keterangan</th>
                                        <th width="160">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($progresses as $i => $progress)
                                        <tr>
                                            <td>
                                                @if ($progress->status === 'PENDING')
                                                    <input type="checkbox" name="progress_ids[]"
                                                        value="{{ $progress->id }}" class="form-check-input row-check">
                                                @endif
                                            </td>
                                            <td>{{ $progresses->firstItem() + $i }}</td>
                                            <td>
                                                <small
                                                    class="text-muted">{{ $progress->actioned_at?->format('d/m/Y H:i') }}</small>
                                            </td>
                                            <td>
                                                <span
                                                    class="fw-semibold">{{ $progress->dataEntry?->user?->name ?? '-' }}</span>
                                            </td>
                                            <td>
                                                @if ($progress->dataEntry?->entry_type === 'OSS')
                                                    <span class="badge bg-info">OSS</span>
                                                @elseif ($progress->dataEntry?->entry_type === 'SIHALAL')
                                                    <span class="badge bg-primary">SIHALAL</span>
                                                @else
                                                    <span class="badge bg-secondary">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('superadmin.data-entry-progress.show', $progress->id) }}"
                                                    class="text-decoration-none fw-semibold">
                                                    {{ $progress->dataLapangan?->nama_pu ?? '-' }}
                                                </a>
                                                <br>
                                                <small class="text-muted">{{ $progress->dataLapangan?->nik ?? '' }}</small>
                                            </td>

                                            <td>
                                                @if ($progress->status === 'PENDING')
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="las la-hourglass-half me-1"></i>PENDING
                                                    </span>
                                                @elseif ($progress->status === 'DITERIMA')
                                                    <span class="badge bg-success">
                                                        <i class="las la-check me-1"></i>DITERIMA
                                                    </span>
                                                @elseif ($progress->status === 'REVISI')
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="las la-edit me-1"></i>REVISI
                                                    </span>
                                                @elseif ($progress->status === 'DITOLAK')
                                                    <span class="badge bg-dark">
                                                        <i class="las la-times me-1"></i>DITOLAK
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($progress->verifikator)
                                                    <span
                                                        class="fw-semibold">{{ $progress->verifikator->nama_lengkap }}</span>
                                                    <br>
                                                    <small
                                                        class="text-muted">{{ $progress->tanggal_verifikasi?->format('d/m/Y') }}</small>
                                                @else
                                                    <small class="text-muted">-</small>
                                                @endif
                                            </td>

                                            {{-- Kolom Keterangan --}}
                                            <td style="max-width: 200px;">
                                                @if ($progress->keterangan_revisi || $progress->keterangan_update)
                                                    <button type="button"
                                                        class="btn btn-sm {{ $progress->keterangan_update ? 'btn-success' : 'btn-danger' }}"
                                                        onclick="lihatKeterangan(
                                                            {{ $progress->keterangan_revisi ? '\'' . addslashes(e($progress->keterangan_revisi)) . '\'' : 'null' }},
                                                            {{ $progress->keterangan_update ? '\'' . addslashes(e($progress->keterangan_update)) . '\'' : 'null' }}
                                                        )">
                                                        <i
                                                            class="las {{ $progress->keterangan_update ? 'la-check-circle' : 'la-exclamation-circle' }} me-1"></i>
                                                        {{ $progress->keterangan_update ? 'Sudah Direvisi' : 'Perlu Revisi' }}
                                                    </button>
                                                @else
                                                    <small class="text-muted">-</small>
                                                @endif
                                            </td>

                                            <td>
                                                @if ($progress->status === 'PENDING')
                                                    <div class="d-flex gap-1 flex-wrap">
                                                        {{-- Tombol Terima --}}
                                                        <button type="button" class="btn btn-success btn-sm"
                                                            title="Terima"
                                                            onclick="submitTerima({{ $progress->id }}, '{{ $progress->dataEntry?->entry_type }}')">
                                                            <i class="las la-check"></i>
                                                        </button>

                                                        {{-- Tombol Revisi (buka modal) --}}
                                                        <button type="button" class="btn btn-warning btn-sm"
                                                            title="Minta Revisi"
                                                            onclick="bukaModalRevisi({{ $progress->id }})">
                                                            <i class="las la-edit"></i>
                                                        </button>

                                                        {{-- Tombol Tolak (buka modal) --}}
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            title="Tolak" onclick="bukaModalTolak({{ $progress->id }})">
                                                            <i class="las la-times"></i>
                                                        </button>
                                                    </div>
                                                @elseif ($progress->status === 'REVISI')
                                                    <div class="d-flex gap-1 flex-wrap">
                                                        <a href="{{ route('superadmin.data-entry-progress.show', $progress->id) }}"
                                                            class="btn btn-outline-primary btn-sm" title="Lihat Detail">
                                                            <i class="las la-eye"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    <a href="{{ route('superadmin.data-entry-progress.show', $progress->id) }}"
                                                        class="btn btn-outline-secondary btn-sm" title="Lihat Detail">
                                                        <i class="las la-eye"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-5 text-muted">
                                                <i class="las la-inbox la-2x mb-2 d-block"></i>
                                                Tidak ada data progress
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-3">
                            @include('layouts.pagination', ['paginator' => $progresses])
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- FORM TERIMA (di luar bulkForm untuk hindari nested form)      --}}
    {{-- ============================================================ --}}
    <form id="formTerima" method="POST" action="">
        @csrf
        @method('PATCH')
        <input type="hidden" name="verifikator_id" id="terimaVerifikatorId">
        <input type="hidden" name="tanggal_verifikasi" id="terimaTanggalVerifikasi">
    </form>

    {{-- ============================================================ --}}
    {{-- MODAL TERIMA — Step 1: Pertanyaan, Step 2: Pilih Verifikator  --}}
    {{-- ============================================================ --}}
    <div id="modalTerima" class="modal fade" tabindex="-1" aria-labelledby="modalTerimaLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTerimaLabel">
                        <i class="las la-check-circle me-2"></i>
                        <span id="modalTerimaTitle">Terima Progress</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    {{-- ── STEP 1: Pertanyaan ── --}}
                    <div id="stepPertanyaan">
                        <p class="text-muted mb-3" id="modalTerimaDesc">
                            Sebelum melanjutkan verifikasi, harap jawab pertanyaan berikut.
                        </p>

                        {{-- OSS --}}
                        <div id="pertanyaanOSS" style="display:none;">
                            <div class="alert alert-warning">
                                <i class="las la-exclamation-triangle me-1"></i>
                                <strong>Perhatian!</strong> Pastikan Anda telah memeriksa file sebelum melanjutkan.
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    Apakah File OSS yang diajukan sudah benar?
                                    <span class="text-muted fw-normal d-block small mt-1">
                                        Jika Anda ragu, silahkan dicek terlebih dahulu sebelum melanjutkan verifikasi.
                                    </span>
                                </label>
                                <div class="d-flex flex-column gap-2 mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ossCheck" id="ossYa"
                                            value="ya">
                                        <label class="form-check-label" for="ossYa">
                                            <i class="las la-check-circle text-success me-1"></i>
                                            Ya, saya sudah mengecek dan benar.
                                        </label>
                                    </div>
                                </div>
                                <div id="errorOSS" class="text-danger small mt-2" style="display:none;">
                                    Anda harus mengkonfirmasi file OSS sudah benar untuk melanjutkan.
                                </div>
                            </div>
                        </div>

                        {{-- SIHALAL --}}
                        <div id="pertanyaanSIHALAL" style="display:none;">
                            <div class="alert alert-info">
                                <i class="las la-info-circle me-1"></i>
                                <strong>Catatan:</strong> Kedua tahap wajib sudah selesai sebelum melanjutkan verifikasi.
                            </div>

                            {{-- Pertanyaan 1 --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    1. Apakah data ini sudah dicek pada Website Sihalal?
                                </label>
                                <div class="d-flex flex-column gap-2 mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="siHalalCek"
                                            id="siHalalCekYa" value="ya" onchange="cekSiHalalValid()">
                                        <label class="form-check-label" for="siHalalCekYa">
                                            <i class="las la-check-circle text-success me-1"></i>
                                            Ya, sudah saya cek.
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="siHalalCek"
                                            id="siHalalCekBelum" value="belum" onchange="cekSiHalalValid()">
                                        <label class="form-check-label text-danger" for="siHalalCekBelum">
                                            <i class="las la-times-circle me-1"></i>
                                            Belum dicek.
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- Pertanyaan 2 --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    2. Apakah data ini sudah diverifikasi dan di-Verval pada Website Sihalal?
                                </label>
                                <div class="d-flex flex-column gap-2 mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="siHalalVerval"
                                            id="siHalalVervalYa" value="ya" onchange="cekSiHalalValid()">
                                        <label class="form-check-label" for="siHalalVervalYa">
                                            <i class="las la-check-circle text-success me-1"></i>
                                            Ya, sudah saya verif dan Verval.
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="siHalalVerval"
                                            id="siHalalVervalBelum" value="belum" onchange="cekSiHalalValid()">
                                        <label class="form-check-label text-danger" for="siHalalVervalBelum">
                                            <i class="las la-times-circle me-1"></i>
                                            Belum diverif dan Verval.
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- Peringatan jika belum keduanya --}}
                            <div id="alertSiHalalBelum" class="alert alert-danger" style="display:none;">
                                <i class="las la-ban me-1"></i>
                                <strong>Tidak dapat melanjutkan!</strong> Data harus sudah dicek dan di-Verval
                                pada Website Sihalal sebelum dapat diverifikasi.
                            </div>
                            <div id="errorSIHALAL" class="text-danger small mt-1" style="display:none;">
                                Harap jawab kedua pertanyaan di atas.
                            </div>
                        </div>
                    </div>

                    {{-- ── STEP 2: Form Verifikator ── --}}
                    <div id="stepVerifikator" style="display:none;">
                        <div class="alert alert-success py-2">
                            <i class="las la-check-circle me-1"></i>
                            Pemeriksaan selesai. Silahkan pilih verifikator dan tanggal verifikasi.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                Verifikator <span class="text-danger">*</span>
                            </label>
                            <select id="selectVerifikator" class="form-select" required>
                                <option value="">-- Pilih Verifikator --</option>
                                @foreach ($verifikators as $v)
                                    <option value="{{ $v->id }}">
                                        {{ $v->nama_lengkap }}
                                        (Rp {{ number_format($v->rate_per_data, 0, ',', '.') }}/data)
                                    </option>
                                @endforeach
                            </select>
                            <div id="errorVerifikator" class="text-danger small mt-1" style="display:none;">
                                Verifikator wajib dipilih.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                Tanggal Verifikasi <span class="text-danger">*</span>
                            </label>
                            <input type="date" id="inputTanggalVerifikasi" class="form-control"
                                value="{{ now()->toDateString() }}" required>
                            <div id="errorTanggal" class="text-danger small mt-1" style="display:none;">
                                Tanggal verifikasi wajib diisi.
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>

                    {{-- Tombol Lanjut (step 1 → step 2) --}}
                    <button type="button" class="btn btn-primary" id="btnLanjutVerifikasi">
                        <i class="las la-arrow-right me-1"></i>Lanjut ke Verifikasi
                    </button>

                    {{-- Tombol Simpan (step 2) --}}
                    <button type="button" class="btn btn-success" id="btnKonfirmasiTerima" style="display:none;">
                        <i class="las la-check me-1"></i>Ya, Terima
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{-- ============================================================ --}}
    {{-- MODAL KONFIRMASI                                              --}}
    {{-- ============================================================ --}}
    <div id="modalKonfirmasi" class="modal fade" tabindex="-1" aria-labelledby="modalKonfirmasiLabel"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalKonfirmasiLabel">
                        <i class="las la-question-circle me-2"></i><span id="confirmTitle">Konfirmasi</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5 class="fs-15">Apakah Anda yakin?</h5>
                    <p class="text-muted" id="confirmMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="confirmBtn">
                        <i class="las la-check me-1"></i>Ya, Lanjutkan
                    </button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    {{-- ============================================================ --}}
    {{-- MODAL KETERANGAN                                              --}}
    {{-- ============================================================ --}}
    <div id="modalKeterangan" class="modal fade" tabindex="-1" aria-labelledby="modalKeteranganLabel"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalKeteranganLabel">
                        <i class="las la-comment me-2"></i>Detail Keterangan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="keteranganRevisiWrapper" style="display: none;">
                        <h5 class="fs-15">Catatan Revisi</h5>
                        <p class="text-muted" id="keteranganRevisiText"></p>
                    </div>
                    <div id="keteranganUpdateWrapper" style="display: none;">
                        <h5 class="fs-15">Balasan Data Entry</h5>
                        <p class="text-muted" id="keteranganUpdateText"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    {{-- ============================================================ --}}
    {{-- MODAL REVISI                                                  --}}
    {{-- ============================================================ --}}
    <div id="modalRevisi" class="modal fade" tabindex="-1" aria-labelledby="modalRevisiLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRevisiLabel">
                        <i class="las la-edit me-2"></i>Minta Revisi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formRevisi" method="POST" action="">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <h5 class="fs-15">Catatan Revisi untuk Data Entry</h5>
                        <p class="text-muted">Jelaskan apa yang perlu diperbaiki agar data entry mengetahui langkah
                            selanjutnya.</p>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Catatan Revisi <span class="text-danger">*</span></label>
                            <textarea name="keterangan_revisi" class="form-control" rows="4"
                                placeholder="Jelaskan apa yang perlu diperbaiki oleh data entry..." required></textarea>
                            <small class="text-muted">Catatan ini akan ditampilkan ke data entry.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="las la-paper-plane me-1"></i>Kirim Revisi
                        </button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    {{-- ============================================================ --}}
    {{-- MODAL TOLAK                                                   --}}
    {{-- ============================================================ --}}
    <div id="modalTolak" class="modal fade" tabindex="-1" aria-labelledby="modalTolakLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTolakLabel">
                        <i class="las la-times-circle me-2"></i>Tolak Progress
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formTolak" method="POST" action="">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <h5 class="fs-15">Konfirmasi Penolakan</h5>
                        <p class="text-muted">Data ini akan ditolak dan <strong>tidak dapat masuk ke penagihan</strong>.
                            Pastikan alasan penolakan sudah jelas sebelum melanjutkan.</p>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea name="keterangan_revisi" class="form-control" rows="4" placeholder="Jelaskan alasan penolakan..."
                                required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="las la-times me-1"></i>Tolak
                        </button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ── Checkbox bulk ─────────────────────────────────────────────────────
            const checkAll = document.getElementById('checkAll');
            const btnBulkTerima = document.getElementById('btnBulkTerima');

            checkAll?.addEventListener('change', function() {
                document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
                updateBulkButton();
            });

            document.querySelectorAll('.row-check').forEach(cb => {
                cb.addEventListener('change', updateBulkButton);
            });

            function updateBulkButton() {
                const checked = document.querySelectorAll('.row-check:checked').length;
                btnBulkTerima.disabled = checked === 0;
                btnBulkTerima.innerHTML = checked > 0 ?
                    `<i class="las la-check-double me-1"></i>Terima ${checked} Yang Dipilih` :
                    `<i class="las la-check-double me-1"></i>Terima Semua Dipilih`;
            }

            // ── Tombol Lanjut ke Step 2 ───────────────────────────────────────────
            document.getElementById('btnLanjutVerifikasi').addEventListener('click', function() {
                if (!_validasiPertanyaan()) return;

                // Tampilkan step 2
                document.getElementById('stepPertanyaan').style.display = 'none';
                document.getElementById('stepVerifikator').style.display = 'block';
                document.getElementById('btnLanjutVerifikasi').style.display = 'none';
                document.getElementById('btnKonfirmasiTerima').style.display = 'inline-block';
            });

            // ── Tombol Konfirmasi Terima (step 2) ─────────────────────────────────
            document.getElementById('btnKonfirmasiTerima').addEventListener('click', function() {
                const verifikatorId = document.getElementById('selectVerifikator').value;
                const tanggalVerifikasi = document.getElementById('inputTanggalVerifikasi').value;

                let valid = true;
                if (!verifikatorId) {
                    document.getElementById('errorVerifikator').style.display = 'block';
                    valid = false;
                } else {
                    document.getElementById('errorVerifikator').style.display = 'none';
                }
                if (!tanggalVerifikasi) {
                    document.getElementById('errorTanggal').style.display = 'block';
                    valid = false;
                } else {
                    document.getElementById('errorTanggal').style.display = 'none';
                }
                if (!valid) return;

                if (_terimaMode === 'single') {
                    document.getElementById('terimaVerifikatorId').value = verifikatorId;
                    document.getElementById('terimaTanggalVerifikasi').value = tanggalVerifikasi;
                    const url = `/superadmin/data-entry-progress/${_terimaProgressId}/terima`;
                    document.getElementById('formTerima').action = url;
                    document.getElementById('formTerima').submit();
                } else {
                    document.getElementById('bulkVerifikatorId').value = verifikatorId;
                    document.getElementById('bulkTanggalVerifikasi').value = tanggalVerifikasi;
                    document.getElementById('bulkForm').submit();
                }
            });
        });

        // ── State ─────────────────────────────────────────────────────────────────
        let _terimaMode = 'single';
        let _terimaProgressId = null;
        let _terimaEntryType = null;

        // ── Buka modal single terima ──────────────────────────────────────────────
        function submitTerima(progressId, entryType) {
            _terimaMode = 'single';
            _terimaProgressId = progressId;
            _terimaEntryType = entryType;

            document.getElementById('modalTerimaTitle').textContent = 'Terima Progress';
            _resetModalTerima(entryType);
            new bootstrap.Modal(document.getElementById('modalTerima')).show();
        }

        // ── Buka modal bulk terima ────────────────────────────────────────────────
        function submitBulkTerima() {
            const checked = document.querySelectorAll('.row-check:checked').length;
            if (checked === 0) return;

            _terimaMode = 'bulk';
            _terimaEntryType = null; // bulk bisa campur type, tampilkan pertanyaan umum

            document.getElementById('modalTerimaTitle').textContent = `Terima ${checked} Progress`;
            _resetModalTerima(null);
            new bootstrap.Modal(document.getElementById('modalTerima')).show();
        }

        // ── Reset modal ke step 1 ─────────────────────────────────────────────────
        function _resetModalTerima(entryType) {
            // Kembali ke step 1
            document.getElementById('stepPertanyaan').style.display = 'block';
            document.getElementById('stepVerifikator').style.display = 'none';
            document.getElementById('btnLanjutVerifikasi').style.display = 'inline-block';
            document.getElementById('btnKonfirmasiTerima').style.display = 'none';

            // Reset form step 2
            document.getElementById('selectVerifikator').value = '';
            document.getElementById('inputTanggalVerifikasi').value = '{{ now()->toDateString() }}';
            document.getElementById('errorVerifikator').style.display = 'none';
            document.getElementById('errorTanggal').style.display = 'none';

            // Sembunyikan semua blok pertanyaan
            document.getElementById('pertanyaanOSS').style.display = 'none';
            document.getElementById('pertanyaanSIHALAL').style.display = 'none';
            document.getElementById('alertSiHalalBelum').style.display = 'none';
            document.getElementById('errorOSS').style.display = 'none';
            document.getElementById('errorSIHALAL').style.display = 'none';

            // Reset radio buttons
            document.querySelectorAll('input[name="ossCheck"]').forEach(r => r.checked = false);
            document.querySelectorAll('input[name="siHalalCek"]').forEach(r => r.checked = false);
            document.querySelectorAll('input[name="siHalalVerval"]').forEach(r => r.checked = false);

            // Tampilkan blok pertanyaan sesuai type
            if (entryType === 'OSS') {
                document.getElementById('pertanyaanOSS').style.display = 'block';
            } else if (entryType === 'SIHALAL') {
                document.getElementById('pertanyaanSIHALAL').style.display = 'block';
            } else {
                // Bulk / tidak diketahui — tampilkan keduanya
                document.getElementById('pertanyaanOSS').style.display = 'block';
                document.getElementById('pertanyaanSIHALAL').style.display = 'block';
            }
        }

        // ── Validasi pertanyaan step 1 ────────────────────────────────────────────
        function _validasiPertanyaan() {
            let valid = true;

            if (_terimaEntryType === 'OSS' || _terimaEntryType === null) {
                const ossCheck = document.querySelector('input[name="ossCheck"]:checked');
                if (!ossCheck) {
                    document.getElementById('errorOSS').style.display = 'block';
                    valid = false;
                } else {
                    document.getElementById('errorOSS').style.display = 'none';
                }
            }

            if (_terimaEntryType === 'SIHALAL' || _terimaEntryType === null) {
                const cek = document.querySelector('input[name="siHalalCek"]:checked');
                const verval = document.querySelector('input[name="siHalalVerval"]:checked');

                if (!cek || !verval) {
                    document.getElementById('errorSIHALAL').style.display = 'block';
                    valid = false;
                } else {
                    document.getElementById('errorSIHALAL').style.display = 'none';

                    // Jika keduanya "belum" → blokir
                    if (cek.value === 'belum' && verval.value === 'belum') {
                        document.getElementById('alertSiHalalBelum').style.display = 'block';
                        valid = false;
                    } else {
                        document.getElementById('alertSiHalalBelum').style.display = 'none';
                    }
                }
            }

            return valid;
        }

        // ── Update alert sihalal saat radio berubah ───────────────────────────────
        function cekSiHalalValid() {
            const cek = document.querySelector('input[name="siHalalCek"]:checked');
            const verval = document.querySelector('input[name="siHalalVerval"]:checked');

            if (cek && verval && cek.value === 'belum' && verval.value === 'belum') {
                document.getElementById('alertSiHalalBelum').style.display = 'block';
            } else {
                document.getElementById('alertSiHalalBelum').style.display = 'none';
            }
        }

        // ── Modal Revisi ──────────────────────────────────────────────────────────
        function bukaModalRevisi(progressId) {
            const url = `/superadmin/data-entry-progress/${progressId}/revisi`;
            document.getElementById('formRevisi').action = url;
            document.querySelector('#formRevisi textarea[name="keterangan_revisi"]').value = '';
            new bootstrap.Modal(document.getElementById('modalRevisi')).show();
        }

        // ── Modal Tolak ───────────────────────────────────────────────────────────
        function bukaModalTolak(progressId) {
            const url = `/superadmin/data-entry-progress/${progressId}/tolak`;
            document.getElementById('formTolak').action = url;
            document.querySelector('#formTolak textarea[name="keterangan_revisi"]').value = '';
            new bootstrap.Modal(document.getElementById('modalTolak')).show();
        }

        // ── Modal Keterangan ──────────────────────────────────────────────────────
        function lihatKeterangan(keteranganRevisi, keteranganUpdate) {
            const revisiWrapper = document.getElementById('keteranganRevisiWrapper');
            const updateWrapper = document.getElementById('keteranganUpdateWrapper');

            if (keteranganRevisi) {
                document.getElementById('keteranganRevisiText').textContent = keteranganRevisi;
                revisiWrapper.style.display = 'block';
            } else {
                revisiWrapper.style.display = 'none';
            }
            if (keteranganUpdate) {
                document.getElementById('keteranganUpdateText').textContent = keteranganUpdate;
                updateWrapper.style.display = 'block';
            } else {
                updateWrapper.style.display = 'none';
            }

            new bootstrap.Modal(document.getElementById('modalKeterangan')).show();
        }
    </script>
@endsection
