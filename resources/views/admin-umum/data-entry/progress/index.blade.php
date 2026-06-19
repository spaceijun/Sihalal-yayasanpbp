@extends('layouts.app')
@section('template_title')
    Validasi Progress Data Entry
@endsection

@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        {{-- PAGE HEADER --}}
        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Validasi Progress Data Entry</h1>
                <p>Tinjau dan validasi submission data entry. SIHALAL menunggu konfirmasi Superadmin.</p>
            </div>
            <button type="button" id="btnBulkTerima" class="adm-btn-primary adm-btn-success" disabled
                onclick="submitBulkTerima()">
                <svg viewBox="0 0 24 24">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
                <span id="bulkBtnText">Validasi Semua Dipilih</span>
            </button>
        </div>

        {{-- STAT TABS --}}
        <div style="display:flex;gap:6px;margin-bottom:16px;flex-wrap:wrap;">
            <button type="button" class="adm-btn primary" id="tab-default" onclick="setTab('')">
                Butuh Review
                @if ($countPending + $countValidasiAdmin + $countRevisi > 0)
                    <span class="adm-count-badge" id="badge-default">{{ $countPending + $countValidasiAdmin + $countRevisi }}</span>
                @endif
            </button>
            <button type="button" class="adm-btn" id="tab-PENDING" onclick="setTab('PENDING')">
                Pending
                @if ($countPending > 0)
                    <span class="adm-count-badge" style="background:var(--adm-amber-lt);color:var(--adm-amber);"
                        id="badge-PENDING">{{ $countPending }}</span>
                @endif
            </button>
            <button type="button" class="adm-btn" id="tab-VALIDASI_ADMIN" onclick="setTab('VALIDASI_ADMIN')">
                Validasi Admin
                @if ($countValidasiAdmin > 0)
                    <span class="adm-count-badge" style="background:#e0f2fe;color:#0369a1;"
                        id="badge-VALIDASI_ADMIN">{{ $countValidasiAdmin }}</span>
                @endif
            </button>
            <button type="button" class="adm-btn" id="tab-REVISI" onclick="setTab('REVISI')">
                Revisi
                @if ($countRevisi > 0)
                    <span class="adm-count-badge" style="background:var(--adm-amber-lt);color:var(--adm-amber);"
                        id="badge-REVISI">{{ $countRevisi }}</span>
                @endif
            </button>
            <button type="button" class="adm-btn" id="tab-DITERIMA" onclick="setTab('DITERIMA')">
                Diterima
                @if ($countDiterima > 0)
                    <span class="adm-count-badge" style="background:var(--adm-green-lt);color:var(--adm-green);"
                        id="badge-DITERIMA">{{ $countDiterima }}</span>
                @endif
            </button>
            <button type="button" class="adm-btn" id="tab-DITOLAK" onclick="setTab('DITOLAK')">
                Ditolak
                @if ($countDitolak > 0)
                    <span class="adm-count-badge" style="background:var(--adm-red-lt);color:var(--adm-red);"
                        id="badge-DITOLAK">{{ $countDitolak }}</span>
                @endif
            </button>
        </div>

        {{-- MAIN CARD --}}
        <div class="adm-card">

            {{-- FILTER BAR --}}
            <div class="adm-filter-bar">
                <div class="adm-filter-group">
                    <label class="adm-filter-label">Cari</label>
                    <div class="adm-search-shell">
                        <svg class="adm-search-icon" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                        <input type="text" id="dtSearch" class="adm-search-input" style="width:260px;"
                            placeholder="Cari nama PU atau data entry...">
                    </div>
                </div>
                <div class="adm-filter-group">
                    <label class="adm-filter-label">Entry Type</label>
                    <select id="filterEntryType" class="adm-select">
                        <option value="">Semua Type</option>
                        <option value="OSS">OSS</option>
                        <option value="SIHALAL">SIHALAL</option>
                    </select>
                </div>
                <div style="display:flex;gap:6px;align-items:flex-end;">
                    <button type="button" class="adm-btn-primary" style="height:34px;" onclick="resetFilter()">
                        <svg viewBox="0 0 24 24">
                            <polyline points="1 4 1 10 7 10" />
                            <path d="M3.51 15a9 9 0 1 0 .49-3.5" />
                        </svg>
                        Reset
                    </button>
                </div>
            </div>

            {{-- TABLE --}}
            <form id="bulkForm" action="{{ route('admin-umum.data-entry-progress.bulk-validasi') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table id="progressTable" class="adm-table w-100">
                        <thead>
                            <tr>
                                <th style="width:40px"><input type="checkbox" id="checkAll" class="form-check-input"
                                        title="Pilih semua"></th>
                                <th style="width:44px">#</th>
                                <th>Tanggal</th>
                                <th>Data Entry</th>
                                <th class="tc">Type</th>
                                <th>Nama PU</th>
                                <th class="tc">Status</th>
                                <th class="tc">Keterangan</th>
                                <th class="tc" style="width:120px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL VALIDASI --}}
    <div id="modalValidasi" class="modal fade adm-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <svg viewBox="0 0 24 24">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                        <span id="modalValidasiTitle">Validasi Progress</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- Info SIHALAL --}}
                    <div id="infoSIHALAL" style="display:none;">
                        <div class="adm-alert adm-alert-info">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            <div><strong>SIHALAL:</strong> Validasi ini akan meneruskan data ke Superadmin untuk konfirmasi akhir. Status data_lapangans akan tetap.</div>
                        </div>
                    </div>
                    {{-- Info OSS --}}
                    <div id="infoOSS" style="display:none;">
                        <div class="adm-alert adm-alert-success">
                            <svg viewBox="0 0 24 24">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                            <div><strong>OSS:</strong> Validasi ini akan langsung menerima data dan mengubah status menjadi PROGRESS OSS.</div>
                        </div>
                        <div class="adm-field" style="margin-top:14px;">
                            <label class="adm-label">Verifikator <span class="req">*</span></label>
                            <select id="selectVerifikator" class="adm-field-select" required>
                                <option value="">-- Pilih Verifikator --</option>
                                @foreach ($verifikators as $v)
                                    <option value="{{ $v->id }}">{{ $v->nama_lengkap }} (Rp
                                        {{ number_format($v->rate_per_data, 0, ',', '.') }}/data)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="adm-field" style="margin-top:12px;">
                            <label class="adm-label">Tanggal Verifikasi <span class="req">*</span></label>
                            <input type="date" id="inputTanggalVerifikasi" class="adm-input"
                                value="{{ now()->toDateString() }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="adm-btn-primary adm-btn-success" id="btnKonfirmasiValidasi">
                        <svg viewBox="0 0 24 24">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                        Ya, Validasi
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL REVISI --}}
    <div id="modalRevisi" class="modal fade adm-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <svg viewBox="0 0 24 24">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                        </svg>
                        Minta Revisi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formRevisi" method="POST" action="">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="adm-alert adm-alert-warning">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            <div>Catatan ini akan ditampilkan ke data entry sebagai panduan perbaikan.</div>
                        </div>
                        <div class="adm-field" style="margin-top:14px;">
                            <label class="adm-label">Catatan Revisi <span class="req">*</span></label>
                            <textarea name="keterangan_revisi" class="adm-textarea" rows="4"
                                placeholder="Jelaskan apa yang perlu diperbaiki oleh data entry..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="adm-btn-primary"
                            style="background:linear-gradient(135deg,#B86800,#a05800);">
                            <svg viewBox="0 0 24 24">
                                <line x1="22" y1="2" x2="11" y2="13" />
                                <polygon points="22 2 15 22 11 13 2 9 22 2" />
                            </svg>
                            Kirim Revisi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL TOLAK --}}
    <div id="modalTolak" class="modal fade adm-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="15" y1="9" x2="9" y2="15" />
                            <line x1="9" y1="9" x2="15" y2="15" />
                        </svg>
                        Tolak Progress
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formTolak" method="POST" action="">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="adm-alert adm-alert-danger">
                            <svg viewBox="0 0 24 24">
                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                                <line x1="12" y1="9" x2="12" y2="13" />
                                <line x1="12" y1="17" x2="12.01" y2="17" />
                            </svg>
                            <div>Tindakan ini bersifat <strong>permanen</strong>. Data tidak dapat dikembalikan ke status PENDING.</div>
                        </div>
                        <div class="adm-field" style="margin-top:14px;">
                            <label class="adm-label">Alasan Penolakan <span class="req">*</span></label>
                            <textarea name="keterangan_revisi" class="adm-textarea" rows="4" placeholder="Jelaskan alasan penolakan..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="adm-btn-primary"
                            style="background:linear-gradient(135deg,var(--adm-red),#b91c1c);">
                            <svg viewBox="0 0 24 24">
                                <line x1="18" y1="6" x2="6" y2="18" />
                                <line x1="6" y1="6" x2="18" y2="18" />
                            </svg>
                            Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL KETERANGAN --}}
    <div id="modalKeterangan" class="modal fade adm-modal-plain" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"
                        style="font-family:'Sora',sans-serif;font-weight:700;color:var(--adm-text-dark);">Detail Keterangan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="display:flex;flex-direction:column;gap:16px;">
                    <div id="keteranganRevisiWrapper" style="display:none;">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--adm-red);margin-bottom:6px;">Catatan Revisi</div>
                        <div style="background:var(--adm-red-lt);border-left:3px solid var(--adm-red);border-radius:6px;padding:10px 14px;font-size:13px;color:var(--adm-text-mid);" id="keteranganRevisiText"></div>
                    </div>
                    <div id="keteranganUpdateWrapper" style="display:none;">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--adm-green);margin-bottom:6px;">Balasan Data Entry</div>
                        <div style="background:var(--adm-green-lt);border-left:3px solid var(--adm-green);border-radius:6px;padding:10px 14px;font-size:13px;color:var(--adm-text-mid);" id="keteranganUpdateText"></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Tutup</button></div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    var _activeStatus = '';
    $(document).ready(function () {
        var _searchTimer;

        var table = $('#progressTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: '{{ route('admin-umum.data-entry-progress.data') }}',
                type: 'GET',
                data: function (d) {
                    d.status     = window._activeStatus;
                    d.entry_type = $('#filterEntryType').val();
                }
            },
            columns: [
                { data: 'checkbox',         name: 'checkbox',         orderable: false, searchable: false },
                { data: 'DT_RowIndex',      name: 'DT_RowIndex',      orderable: false, searchable: false },
                { data: 'tanggal',           name: 'actioned_at' },
                { data: 'data_entry_cell',   name: 'data_entry_cell',  orderable: false },
                { data: 'type_badge',        name: 'type_badge',       orderable: false, searchable: false },
                { data: 'nama_pu_cell',      name: 'nama_pu_cell',     orderable: false },
                { data: 'status_badge',      name: 'status_badge',     orderable: false, searchable: false },
                { data: 'keterangan_cell',   name: 'keterangan_cell',  orderable: false, searchable: false },
                { data: 'aksi',              name: 'aksi',             orderable: false, searchable: false },
            ],
            dom: 'rt<"adm-card-footer d-flex justify-content-between align-items-center"ip>',
            language: {
                processing: '<div class="text-center py-4"><div class="spinner-border" style="color:var(--adm-blue);width:2rem;height:2rem;" role="status"></div></div>',
                emptyTable:  '<div class="adm-empty"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><p>Tidak ada data progress.</p></div>',
                zeroRecords: '<div class="adm-empty"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><p>Data tidak ditemukan.</p></div>',
                info:        'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty:   'Tidak ada data',
                paginate:    { previous: '‹', next: '›' },
            },
            pageLength: 20,
            order: [[2, 'desc']],
            drawCallback: function () {
                document.getElementById('checkAll').checked = false;
                document.querySelectorAll('.row-check').forEach(function (cb) {
                    cb.addEventListener('change', updateBulkButton);
                });
                updateBulkButton();
            }
        });

        $('#dtSearch').on('input', function () {
            clearTimeout(_searchTimer);
            var val = this.value;
            _searchTimer = setTimeout(function () { table.search(val).draw(); }, 400);
        });

        $('#filterEntryType').on('change', function () { table.ajax.reload(); });

        document.getElementById('checkAll').addEventListener('change', function () {
            document.querySelectorAll('.row-check').forEach(function (cb) { cb.checked = this.checked; }, this);
            updateBulkButton();
        });

        document.getElementById('btnKonfirmasiValidasi').addEventListener('click', function () {
            if (_validasiMode === 'single' && _validasiEntryType === 'OSS') {
                var verifikatorId = document.getElementById('selectVerifikator').value;
                var tanggalVerifikasi = document.getElementById('inputTanggalVerifikasi').value;
                if (!verifikatorId) {
                    alert('Verifikator wajib dipilih.');
                    return;
                }
                if (!tanggalVerifikasi) {
                    alert('Tanggal verifikasi wajib diisi.');
                    return;
                }
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin-umum/data-entry-progress/' + _validasiProgressId + '/validasi';
                form.innerHTML = '@csrf @method('PATCH')' +
                    '<input type="hidden" name="verifikator_id" value="' + verifikatorId + '">' +
                    '<input type="hidden" name="tanggal_verifikasi" value="' + tanggalVerifikasi + '">';
                document.body.appendChild(form);
                form.submit();
            } else {
                // Bulk atau SIHALAL single - submit form
                document.getElementById('bulkForm').submit();
            }
        });
    });

    function setTab(status) {
        window._activeStatus = status;
        ['default','PENDING','VALIDASI_ADMIN','REVISI','DITERIMA','DITOLAK'].forEach(function (k) {
            var btn = document.getElementById('tab-' + k);
            if (!btn) return;
            btn.className = 'adm-btn' + (k === (status || 'default') ? ' primary' : '');
        });
        $('#progressTable').DataTable().ajax.reload();
    }

    function resetFilter() {
        window._activeStatus = '';
        document.getElementById('dtSearch').value = '';
        document.getElementById('filterEntryType').value = '';
        setTab('');
        $('#progressTable').DataTable().search('').ajax.reload();
    }

    function updateBulkButton() {
        var checked = document.querySelectorAll('.row-check:checked').length;
        var btn = document.getElementById('btnBulkTerima');
        btn.disabled = checked === 0;
        document.getElementById('bulkBtnText').textContent =
            checked > 0 ? 'Validasi ' + checked + ' Yang Dipilih' : 'Validasi Semua Dipilih';
    }

    var _validasiMode = 'single', _validasiProgressId = null, _validasiEntryType = null;

    function submitTerima(hashedId, entryType) {
        _validasiMode = 'single';
        _validasiProgressId = hashedId;
        _validasiEntryType = entryType;
        document.getElementById('modalValidasiTitle').textContent = 'Validasi Progress';
        _resetModalValidasi(entryType);
        new bootstrap.Modal(document.getElementById('modalValidasi')).show();
    }

    function submitBulkTerima() {
        if (document.querySelectorAll('.row-check:checked').length === 0) return;
        _validasiMode = 'bulk';
        document.getElementById('modalValidasiTitle').textContent = 'Validasi Bulk Progress';
        _resetModalValidasi(null);
        new bootstrap.Modal(document.getElementById('modalValidasi')).show();
    }

    function _resetModalValidasi(entryType) {
        document.getElementById('infoSIHALAL').style.display = 'none';
        document.getElementById('infoOSS').style.display = 'none';
        document.getElementById('selectVerifikator').value = '';
        document.getElementById('inputTanggalVerifikasi').value = '{{ now()->toDateString() }}';
        if (entryType === 'OSS') {
            document.getElementById('infoOSS').style.display = 'block';
        } else if (entryType === 'SIHALAL') {
            document.getElementById('infoSIHALAL').style.display = 'block';
        } else {
            document.getElementById('infoSIHALAL').style.display = 'block';
            document.getElementById('infoOSS').style.display = 'block';
        }
    }

    function bukaModalRevisi(hashedId) {
        document.getElementById('formRevisi').action = '/admin-umum/data-entry-progress/' + hashedId + '/revisi';
        document.querySelector('#formRevisi textarea[name="keterangan_revisi"]').value = '';
        new bootstrap.Modal(document.getElementById('modalRevisi')).show();
    }

    function bukaModalTolak(hashedId) {
        document.getElementById('formTolak').action = '/admin-umum/data-entry-progress/' + hashedId + '/tolak';
        document.querySelector('#formTolak textarea[name="keterangan_revisi"]').value = '';
        new bootstrap.Modal(document.getElementById('modalTolak')).show();
    }

    function lihatKeterangan(keteranganRevisi, keteranganUpdate) {
        var revisiWrapper = document.getElementById('keteranganRevisiWrapper');
        var updateWrapper = document.getElementById('keteranganUpdateWrapper');
        if (keteranganRevisi) {
            document.getElementById('keteranganRevisiText').textContent = keteranganRevisi;
            revisiWrapper.style.display = 'block';
        } else { revisiWrapper.style.display = 'none'; }
        if (keteranganUpdate) {
            document.getElementById('keteranganUpdateText').textContent = keteranganUpdate;
            updateWrapper.style.display = 'block';
        } else { updateWrapper.style.display = 'none'; }
        new bootstrap.Modal(document.getElementById('modalKeterangan')).show();
    }
    </script>
    @endpush
@endsection
