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
                            <i class="fas fa-tasks me-2"></i>Review Progress Data Entry
                        </span>
                        <div class="d-flex gap-2">
                            {{-- Bulk Terima --}}
                            <button type="button" id="btnBulkTerima" class="btn btn-success btn-sm" disabled
                                onclick="submitBulkTerima()">
                                <i class="fas fa-check-double me-1"></i>Terima Semua Dipilih
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Tab Status --}}
                <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                    <ul class="nav nav-tabs card-header-tabs" id="statusTabs">
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
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <a href="{{ route('superadmin.data-entry-progress.index') }}"
                                    class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-redo me-1"></i>Reset
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
                                        <th>File / Aksi</th>
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
                                                <small class="text-muted">
                                                    {{ $progress->dataLapangan?->nik ?? '' }}
                                                </small>
                                            </td>
                                            <td>
                                                @php $newData = $progress->new_data; @endphp
                                                @if (is_array($newData))
                                                    <span
                                                        class="badge bg-light text-dark border">{{ strtoupper($newData['file_type'] ?? '-') }}</span>
                                                    <br>
                                                    <small
                                                        class="text-muted">{{ $newData['file_name'] !== 'N/A' ? $newData['file_name'] : 'Update Status' }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($progress->status === 'PENDING')
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-hourglass-half me-1"></i>PENDING
                                                    </span>
                                                @elseif ($progress->status === 'DITERIMA')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>DITERIMA
                                                    </span>
                                                @elseif ($progress->status === 'REVISI')
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-edit me-1"></i>REVISI
                                                    </span>
                                                @elseif ($progress->status === 'DITOLAK')
                                                    <span class="badge bg-dark">
                                                        <i class="fas fa-times me-1"></i>DITOLAK
                                                    </span>
                                                @endif
                                            </td>
                                            <td style="max-width: 200px;">
                                                @if ($progress->keterangan_revisi)
                                                    <small class="text-danger">
                                                        <i
                                                            class="fas fa-comment me-1"></i>{{ Str::limit($progress->keterangan_revisi, 60) }}
                                                    </small>
                                                @endif
                                                @if ($progress->keterangan_update)
                                                    <small class="text-success d-block">
                                                        <i
                                                            class="fas fa-reply me-1"></i>{{ Str::limit($progress->keterangan_update, 60) }}
                                                    </small>
                                                @endif
                                                @if (!$progress->keterangan_revisi && !$progress->keterangan_update)
                                                    <small class="text-muted">-</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($progress->status === 'PENDING')
                                                    <div class="d-flex gap-1 flex-wrap">
                                                        {{-- Tombol Terima --}}
                                                        <form
                                                            action="{{ route('superadmin.data-entry-progress.terima', $progress->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-success btn-sm"
                                                                onclick="return confirm('Terima progress ini?')"
                                                                title="Terima">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>

                                                        {{-- Tombol Revisi (buka modal) --}}
                                                        <button type="button" class="btn btn-warning btn-sm"
                                                            title="Minta Revisi"
                                                            onclick="bukaModalRevisi({{ $progress->id }})">
                                                            <i class="fas fa-edit"></i>
                                                        </button>

                                                        {{-- Tombol Tolak (buka modal) --}}
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            title="Tolak" onclick="bukaModalTolak({{ $progress->id }})">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                @elseif ($progress->status === 'REVISI')
                                                    {{-- Bisa terima atau tolak setelah resubmit --}}
                                                    <div class="d-flex gap-1 flex-wrap">
                                                        <a href="{{ route('superadmin.data-entry-progress.show', $progress->id) }}"
                                                            class="btn btn-outline-primary btn-sm" title="Lihat Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    <a href="{{ route('superadmin.data-entry-progress.show', $progress->id) }}"
                                                        class="btn btn-outline-secondary btn-sm" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-5 text-muted">
                                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
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
    {{-- MODAL REVISI                                                  --}}
    {{-- ============================================================ --}}
    <div class="modal fade" id="modalRevisi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Minta Revisi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formRevisi" method="POST" action="">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Catatan Revisi untuk Data Entry <span
                                    class="text-danger">*</span></label>
                            <textarea name="keterangan_revisi" class="form-control" rows="4"
                                placeholder="Jelaskan apa yang perlu diperbaiki oleh data entry..." required></textarea>
                            <small class="text-muted">Catatan ini akan ditampilkan ke data entry agar mereka tahu apa yang
                                harus diperbaiki.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning btn-sm">
                            <i class="fas fa-paper-plane me-2"></i>Kirim Revisi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL TOLAK                                                   --}}
    {{-- ============================================================ --}}
    <div class="modal fade" id="modalTolak" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-times-circle me-2"></i>Tolak Progress</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formTolak" method="POST" action="">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="alert alert-danger py-2">
                            <small><i class="fas fa-exclamation-triangle me-1"></i>Data ini akan ditolak dan tidak dapat
                                masuk ke penagihan.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea name="keterangan_revisi" class="form-control" rows="4" placeholder="Jelaskan alasan penolakan..."
                                required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-times me-2"></i>Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Check all checkbox
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

                // Tambahkan icon kembali
                const icon = document.createElement('i');
                icon.className = 'fas fa-check-double me-1';
                btnBulkTerima.prepend(icon);
            }
        });

        function submitBulkTerima() {
            const checked = document.querySelectorAll('.row-check:checked').length;
            if (checked === 0) return;
            if (!confirm(`Terima ${checked} progress yang dipilih?`)) return;
            document.getElementById('bulkForm').submit();
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
