@extends('layouts.app')
@section('template_title') Enumerators @endsection

@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        {{-- PAGE HEADER --}}
        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Enumerator</h1>
                <p>Kelola pendamping lapangan dan akses akun mereka</p>
            </div>
            <div style="display:flex;gap:8px;align-items:center;">
                {{-- Tombol Export PDF --}}
                <button type="button" class="adm-btn-secondary" data-bs-toggle="modal" data-bs-target="#exportPdfModal"
                    title="Export ke PDF">
                    <svg viewBox="0 0 24 24"
                        style="width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="16" y1="13" x2="8" y2="13" />
                        <line x1="16" y1="17" x2="8" y2="17" />
                        <polyline points="10 9 9 9 8 9" />
                    </svg>
                    Export PDF
                </button>
                <a href="{{ route('superadmin.enumerators.create') }}" class="adm-btn-primary">
                    <svg viewBox="0 0 24 24">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Tambah Enumerator
                </a>
            </div>
        </div>

        {{-- MAIN TABLE CARD --}}
        <div class="adm-card">
            <div class="table-responsive">
                <table id="enumeratorTable" class="adm-table w-100">
                    <thead>
                        <tr>
                            <th style="width:44px">#</th>
                            <th>No Registrasi</th>
                            <th>Nama Lengkap</th>
                            <th class="tc">Data Masuk (By Month)</th>
                            <th>Rekening</th>
                            <th class="tc">Status</th>
                            <th class="tc" style="width:200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── EXPORT PDF MODAL ── --}}
    <div id="exportPdfModal" class="modal fade adm-modal" tabindex="-1" aria-labelledby="exportPdfModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportPdfModalLabel">
                        <svg viewBox="0 0 24 24"
                            style="width:18px;height:18px;stroke:var(--adm-blue);fill:none;stroke-width:2;
                                   stroke-linecap:round;stroke-linejoin:round;margin-right:6px;vertical-align:-3px;">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                        </svg>
                        Export Laporan PDF
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body" style="padding:20px 24px;">
                    <p style="font-size:13px;color:var(--adm-text-muted);margin-bottom:16px;">
                        Pilih periode bulan dan tahun untuk laporan yang akan diekspor.
                    </p>

                    <div style="display:flex;gap:12px;">
                        <div style="flex:1;">
                            <label for="exportBulan"
                                style="font-size:11px;font-weight:600;color:var(--adm-text-muted);display:block;margin-bottom:4px;">
                                Bulan
                            </label>
                            <select id="exportBulan" class="adm-select" style="width:100%;">
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(null, $m, 1)->locale('id')->isoFormat('MMMM') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div style="flex:1;">
                            <label for="exportTahun"
                                style="font-size:11px;font-weight:600;color:var(--adm-text-muted);display:block;margin-bottom:4px;">
                                Tahun
                            </label>
                            <select id="exportTahun" class="adm-select" style="width:100%;">
                                @foreach (range(now()->year - 2, now()->year) as $y)
                                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="margin-top:14px;padding:10px 14px;background:var(--adm-blue-lt);border-radius:6px;font-size:12px;color:var(--adm-blue);font-weight:600;">
                        <svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:var(--adm-blue);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;margin-right:4px;vertical-align:-2px;">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                        Periode:&nbsp;<span id="exportPreviewLabel"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="exportPdfConfirmBtn" class="adm-btn-primary">
                        <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <polyline points="7 10 12 15 17 10" />
                            <line x1="12" y1="15" x2="12" y2="3" />
                        </svg>
                        Download PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── DELETE MODAL ── --}}
    @include('superadmin.enumerator.partials.delete-modal')

    {{-- ── GENERATE USER MODAL ── --}}
    <div id="generateUserModal" class="modal fade adm-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="generateUserModalLabel">
                        <svg viewBox="0 0 24 24"
                            style="width:18px;height:18px;stroke:var(--adm-blue);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;margin-right:6px;vertical-align:-3px;">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="8.5" cy="7" r="4" />
                            <line x1="20" y1="8" x2="20" y2="14" />
                            <line x1="23" y1="11" x2="17" y2="11" />
                        </svg>
                        Generate Akun User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:20px 24px;">
                    <p style="font-size:13px;color:var(--adm-text-muted);margin-bottom:14px;">Akun user akan dibuat dengan detail berikut:</p>
                    <div class="adm-info-list" style="border:none;">
                        <div class="adm-info-row">
                            <span class="adm-info-key">Nama</span>
                            <span class="adm-info-val" id="generateUserNama">—</span>
                        </div>
                        <div class="adm-info-row">
                            <span class="adm-info-key">Email</span>
                            <span class="adm-info-val adm-mono" id="generateUserEmail" style="font-size:12px;">—</span>
                        </div>
                        <div class="adm-info-row">
                            <span class="adm-info-key">Password</span>
                            <span class="adm-info-val">
                                <code style="background:var(--adm-blue-lt);color:var(--adm-blue);padding:2px 8px;border-radius:4px;font-size:12px;">enumkh123</code>
                            </span>
                        </div>
                        <div class="adm-info-row">
                            <span class="adm-info-key">Role</span>
                            <span class="adm-info-val"><span class="adm-badge adm-badge-info">Enumerator</span></span>
                        </div>
                    </div>
                    <div class="adm-alert adm-alert-warning" style="margin-top:14px;">
                        <svg viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <div>Pastikan nomor telepon sudah benar karena digunakan sebagai email login.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="adm-btn-primary" id="confirmGenerateUserBtn">
                        <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                        Generate User
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const GENERATE_URL = '{{ url('superadmin/enumerators') }}/{id}/generate-user';
    const DELETE_URL   = '{{ url('superadmin/enumerators') }}/{id}';
    const EXPORT_URL   = '{{ route('superadmin.enumerators.export-pdf') }}';
    const CSRF         = $('meta[name="csrf-token"]').attr('content');

    const NAMA_BULAN = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

    /* ── Bootstrap Modals ── */
    const deleteModal       = new bootstrap.Modal(document.getElementById('deleteModal'));
    const generateUserModal = new bootstrap.Modal(document.getElementById('generateUserModal'));

    let deleteEnumeratorId      = null;
    let generateUserEnumId      = null;

    /* ── DataTable ── */
    const dt = $('#enumeratorTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('superadmin.enumerators.data') }}',
            type: 'GET',
            headers: { 'X-CSRF-TOKEN': CSRF }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'tc' },
            { data: 'no_reg',      name: 'no_registrasi', className: '' },
            { data: 'nama_cell',   name: 'nama_lengkap',  className: '' },
            { data: 'data_bulan',  name: 'data_bulan_ini', className: 'tc', orderable: false, searchable: false },
            { data: 'rekening',    name: 'rekening',       orderable: false, searchable: false },
            { data: 'status_badge',name: 'status',         className: 'tc' },
            { data: 'aksi',        name: 'aksi',           orderable: false, searchable: false, className: 'tc' },
        ],
        language: {
            search:      'Cari:',
            lengthMenu:  'Tampilkan _MENU_ data',
            info:        'Menampilkan _START_ – _END_ dari _TOTAL_ enumerator',
            infoEmpty:   'Tidak ada data',
            paginate: { previous: '‹', next: '›' },
            zeroRecords: 'Tidak ada enumerator ditemukan',
            emptyTable:  'Belum ada data enumerator',
            processing:  '<div class="spinner-border text-primary" role="status"></div>',
        },
        pageLength: 15,
        order: [[2, 'asc']],
        responsive: true,
        drawCallback: bindRowActions,
    });

    /* ── Bind row actions setiap setelah draw ── */
    function bindRowActions() {
        // Delete buttons
        $(document).off('click', '.btn-delete').on('click', '.btn-delete', function () {
            deleteEnumeratorId = $(this).data('id');
            if (deleteEnumeratorId) deleteModal.show();
        });

        // Generate user buttons
        $(document).off('click', '.btn-generate-user').on('click', '.btn-generate-user', function () {
            generateUserEnumId = $(this).data('id');
            const nama = $(this).data('nama') || '—';
            const hp   = $(this).data('hp')   || '';
            $('#generateUserNama').text(nama);
            $('#generateUserEmail').text(hp ? hp + '@kawulohalal.id' : '—');
            if (generateUserEnumId) generateUserModal.show();
        });
    }

    /* ── Confirm Delete ── */
    $('#confirmDeleteBtn').on('click', function () {
        if (!deleteEnumeratorId) return;
        const btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menghapus...');

        $.ajax({
            url: DELETE_URL.replace('{id}', deleteEnumeratorId),
            type: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        })
        .done(function (res) {
            deleteModal.hide();
            if (res.success) {
                dt.ajax.reload(null, false);
            } else {
                alert(res.message || 'Gagal menghapus data');
            }
        })
        .fail(function () { alert('Terjadi kesalahan saat menghapus data'); })
        .always(function () {
            btn.prop('disabled', false).text('Hapus Data');
            deleteEnumeratorId = null;
        });
    });

    /* ── Confirm Generate User ── */
    $('#confirmGenerateUserBtn').on('click', function () {
        if (!generateUserEnumId) return;
        const btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Memproses...');

        $.ajax({
            url: GENERATE_URL.replace('{id}', generateUserEnumId),
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        })
        .done(function (res) {
            generateUserModal.hide();
            if (res.success) {
                alert(res.message || 'User berhasil digenerate');
                dt.ajax.reload(null, false);
            } else {
                alert(res.message || 'Gagal generate user');
            }
        })
        .fail(function () { alert('Terjadi kesalahan'); })
        .always(function () {
            btn.prop('disabled', false).html(
                '<svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2;margin-right:4px;"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg> Generate User'
            );
            generateUserEnumId = null;
        });
    });

    /* ── Export PDF ── */
    let exportUrl = '';
    function updateExportUrl() {
        const bulan = $('#exportBulan').val();
        const tahun = $('#exportTahun').val();
        $('#exportPreviewLabel').text((NAMA_BULAN[parseInt(bulan)] || '—') + ' ' + tahun);
        exportUrl = EXPORT_URL + '?bulan=' + bulan + '&tahun=' + tahun;
    }
    $('#exportBulan, #exportTahun').on('change', updateExportUrl);
    updateExportUrl();

    $('#exportPdfConfirmBtn').on('click', function () {
        if (!exportUrl) return;
        const exportModalInst = bootstrap.Modal.getInstance(document.getElementById('exportPdfModal'));
        const el = document.getElementById('exportPdfModal');
        const onHidden = function () {
            el.removeEventListener('hidden.bs.modal', onHidden);
            window.open(exportUrl, '_blank', 'noopener,noreferrer');
        };
        el.addEventListener('hidden.bs.modal', onHidden);
        if (exportModalInst) exportModalInst.hide();
        else window.open(exportUrl, '_blank', 'noopener,noreferrer');
    });
});
</script>
@endpush
