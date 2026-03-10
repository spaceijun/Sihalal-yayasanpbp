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
                                                            title="Terima" onclick="submitTerima({{ $progress->id }})">
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
                            {{ $progresses->links() }}
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
    </form>

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

            const checkAll = document.getElementById('checkAll');
            const btnBulkTerima = document.getElementById('btnBulkTerima');

            checkAll?.addEventListener('change', function() {
                document.querySelectorAll('.row-check').forEach(cb => {
                    cb.checked = this.checked;
                });
                updateBulkButton();
            });

            document.querySelectorAll('.row-check').forEach(cb => {
                cb.addEventListener('change', updateBulkButton);
            });

            function updateBulkButton() {
                const checked = document.querySelectorAll('.row-check:checked').length;
                btnBulkTerima.disabled = checked === 0;
                btnBulkTerima.textContent = checked > 0 ?
                    `Terima ${checked} Yang Dipilih` :
                    'Terima Semua Dipilih';

                const icon = document.createElement('i');
                icon.className = 'las la-check-double me-1';
                btnBulkTerima.prepend(icon);
            }
        });

        function submitTerima(progressId) {
            document.getElementById('confirmTitle').textContent = 'Konfirmasi Terima';
            document.getElementById('confirmMessage').textContent = 'Apakah Anda yakin ingin menerima progress ini?';
            document.getElementById('confirmBtn').className = 'btn btn-success';
            document.getElementById('confirmBtn').innerHTML = '<i class="las la-check me-1"></i>Ya, Terima';

            document.getElementById('confirmBtn').onclick = function() {
                const url = `/superadmin/data-entry-progress/${progressId}/terima`;
                document.getElementById('formTerima').action = url;
                document.getElementById('formTerima').submit();
            };

            new bootstrap.Modal(document.getElementById('modalKonfirmasi')).show();
        }

        function submitBulkTerima() {
            const checked = document.querySelectorAll('.row-check:checked').length;
            if (checked === 0) return;

            document.getElementById('confirmTitle').textContent = 'Konfirmasi Terima Massal';
            document.getElementById('confirmMessage').textContent =
                `Apakah Anda yakin ingin menerima ${checked} progress yang dipilih?`;
            document.getElementById('confirmBtn').className = 'btn btn-success';
            document.getElementById('confirmBtn').innerHTML =
                `<i class="las la-check-double me-1"></i>Ya, Terima ${checked}`;

            document.getElementById('confirmBtn').onclick = function() {
                document.getElementById('bulkForm').submit();
            };

            new bootstrap.Modal(document.getElementById('modalKonfirmasi')).show();
        }

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

        function bukaModalRevisi(progressId) {
            const url = `/superadmin/data-entry-progress/${progressId}/revisi`;
            document.getElementById('formRevisi').action = url;
            document.querySelector('#formRevisi textarea[name="keterangan_revisi"]').value = '';
            new bootstrap.Modal(document.getElementById('modalRevisi')).show();
        }

        function bukaModalTolak(progressId) {
            const url = `/superadmin/data-entry-progress/${progressId}/tolak`;
            document.getElementById('formTolak').action = url;
            document.querySelector('#formTolak textarea[name="keterangan_revisi"]').value = '';
            new bootstrap.Modal(document.getElementById('modalTolak')).show();
        }
    </script>
@endsection
