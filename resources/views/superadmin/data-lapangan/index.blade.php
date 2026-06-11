@extends('layouts.app')
@section('template_title')
    Data Lapangan
@endsection
@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Data Lapangan</h1>
                <p>Kelola data survei pelaku usaha di lapangan</p>
            </div>
            <div style="display:flex;gap:8px;align-items:center;">
                <button id="exportBtn" class="adm-btn success" style="gap:6px;">
                    <svg viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="16" y1="13" x2="8" y2="13" />
                        <line x1="16" y1="17" x2="8" y2="17" />
                    </svg>
                    Export Excel
                </button>
                <a href="{{ route('superadmin.data-lapangans.data-revisi') }}" class="adm-btn-secondary">
                    <svg viewBox="0 0 24 24">
                        <polyline points="1 4 1 10 7 10" />
                        <path d="M3.51 15a9 9 0 1 0 .49-3.5" />
                    </svg>
                    Data Revisi
                </a>
                <a href="{{ route('superadmin.data-lapangans.create') }}" class="adm-btn-primary">
                    <svg viewBox="0 0 24 24">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Tambah
                </a>
            </div>
        </div>

        {{-- ── PAYMENT SUMMARY CARDS ── --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:18px;">

            {{-- PENDING --}}
            <div
                style="background:#fff;border:1px solid #FDE68A;border-radius:10px;padding:16px 20px;display:flex;align-items:center;gap:14px;">
                <div
                    style="width:44px;height:44px;border-radius:10px;background:#FFFBEB;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg viewBox="0 0 24 24"
                        style="width:22px;height:22px;fill:none;stroke:#D97706;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                </div>
                <div>
                    <p
                        style="margin:0;font-size:11px;font-weight:600;color:#92400E;text-transform:uppercase;letter-spacing:.5px;">
                        Pending</p>
                    <p id="card-pending-count" style="margin:2px 0 0;font-size:20px;font-weight:800;color:#B45309;">
                        {{ $paymentStats['pending_count'] }}</p>
                    <p id="card-pending-total" style="margin:2px 0 0;font-size:12px;color:#D97706;">Rp
                        {{ number_format($paymentStats['pending_total'], 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- PENGAJUAN --}}
            <div
                style="background:#fff;border:1px solid #BAE6FD;border-radius:10px;padding:16px 20px;display:flex;align-items:center;gap:14px;">
                <div
                    style="width:44px;height:44px;border-radius:10px;background:#F0F9FF;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg viewBox="0 0 24 24"
                        style="width:22px;height:22px;fill:none;stroke:#0284C7;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="16" y1="13" x2="8" y2="13" />
                        <line x1="16" y1="17" x2="8" y2="17" />
                    </svg>
                </div>
                <div>
                    <p
                        style="margin:0;font-size:11px;font-weight:600;color:#0C4A6E;text-transform:uppercase;letter-spacing:.5px;">
                        Pengajuan</p>
                    <p id="card-pengajuan-count" style="margin:2px 0 0;font-size:20px;font-weight:800;color:#0369A1;">
                        {{ $paymentStats['pengajuan_count'] }}</p>
                    <p id="card-pengajuan-total" style="margin:2px 0 0;font-size:12px;color:#0284C7;">Rp
                        {{ number_format($paymentStats['pengajuan_total'], 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- DIBAYAR --}}
            <div
                style="background:#fff;border:1px solid #BBF7D0;border-radius:10px;padding:16px 20px;display:flex;align-items:center;gap:14px;">
                <div
                    style="width:44px;height:44px;border-radius:10px;background:#F0FDF4;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg viewBox="0 0 24 24"
                        style="width:22px;height:22px;fill:none;stroke:#16A34A;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                        <polyline points="22 4 12 14.01 9 11.01" />
                    </svg>
                </div>
                <div>
                    <p
                        style="margin:0;font-size:11px;font-weight:600;color:#14532D;text-transform:uppercase;letter-spacing:.5px;">
                        Dibayar</p>
                    <p id="card-dibayar-count" style="margin:2px 0 0;font-size:20px;font-weight:800;color:#15803D;">
                        {{ $paymentStats['dibayar_count'] }}</p>
                    <p id="card-dibayar-total" style="margin:2px 0 0;font-size:12px;color:#16A34A;">Rp
                        {{ number_format($paymentStats['dibayar_total'], 0, ',', '.') }}</p>
                </div>
            </div>

        </div>

        {{-- ── BULK ACTION BAR ── --}}
        <div id="bulkActionBar" class="d-none"
            style="display:none;align-items:center;gap:12px;padding:10px 16px;background:var(--adm-blue-lt);border:1px solid var(--adm-blue);border-radius:var(--adm-radius);margin-bottom:14px;">
            <span id="selectedCount" style="font-weight:700;color:var(--adm-blue);font-size:13px;">0 dipilih</span>
            <button id="btnBulkDibayar" class="adm-btn success" style="font-size:12px;padding:5px 14px;">
                <svg viewBox="0 0 24 24">
                    <polyline points="20 6 9 17 4 12" />
                </svg> Tandai Dibayar
            </button>
            <button id="btnCancelSelect" class="adm-btn-secondary" style="font-size:12px;padding:5px 12px;">Batal</button>
        </div>

        <div class="adm-card">
            {{-- ── MODAL BULK PAYMENT ── --}}
            <div id="modalBulkPayment" class="modal fade" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <svg viewBox="0 0 24 24"
                                    style="width:18px;height:18px;stroke:var(--adm-green);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;margin-right:6px;vertical-align:-3px;">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                    <polyline points="22 4 12 14.01 9 11.01" />
                                </svg>
                                Konfirmasi Tandai Dibayar
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" style="padding:20px 24px;">
                            <div class="adm-alert adm-alert-success" style="margin-bottom:0;">
                                <svg viewBox="0 0 24 24">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                    <polyline points="22 4 12 14.01 9 11.01" />
                                </svg>
                                <div>
                                    <p style="margin:0;font-size:13px;">Anda akan menandai <strong
                                            id="modalSelectedCount">0</strong> data sebagai <strong>DIBAYAR</strong>.</p>
                                    <p style="margin:4px 0 0;font-size:12.5px;color:var(--adm-text-muted);">Tindakan ini
                                        tidak dapat dibatalkan. Pastikan data sudah benar.</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" id="btnConfirmBulkDibayar" class="adm-btn-primary"
                                style="background:linear-gradient(135deg,var(--adm-green),#15803d);">
                                <svg viewBox="0 0 24 24">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                                Ya, Tandai Dibayar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table wrapper --}}
            <div id="tableWrapper">
                <div class="table-responsive">
                    <table id="dataLapanganTable" class="adm-table w-100">
                        <thead>
                            <tr>
                                <th style="width:40px;text-align:center;">
                                    <input type="checkbox" id="checkAll" title="Pilih semua" style="cursor:pointer;">
                                </th>
                                <th style="width:44px">#</th>
                                <th>Tanggal</th>
                                <th>No Registrasi</th>
                                <th>Pendamping</th>
                                <th>Nama PU</th>
                                <th>NIK</th>
                                <th class="tc">Status</th>
                                <th class="tc">Payment</th>
                                <th class="tc">Tagihan</th>
                                <th class="tc" style="width:110px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const FORCE_UNLOCK_URL = '{{ url('superadmin/data-lapangans') }}';
                const BULK_PAYMENT_URL = '{{ route('superadmin.data-lapangans.bulk-payment') }}';
                const EXPORT_URL = '{{ route('superadmin.data-lapangans.export') }}';

                function getCsrfToken() {
                    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                }

                // ── Init DataTable ──
                const table = $('#dataLapanganTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('superadmin.data-lapangans.data') }}',
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': getCsrfToken()
                        }
                    },
                    columns: [{
                            data: 'checkbox',
                            name: 'checkbox',
                            orderable: false,
                            searchable: false,
                            className: 'tc'
                        },
                        {
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            className: 'tc'
                        },
                        {
                            data: 'tanggal',
                            name: 'created_at',
                            className: ''
                        },
                        {
                            data: 'no_registrasi',
                            name: 'no_registrasi',
                            className: 'adm-mono'
                        },
                        {
                            data: 'pendamping_cell',
                            name: 'pendamping_cell',
                            orderable: false,
                            className: ''
                        },
                        {
                            data: 'nama_pu',
                            name: 'nama_pu',
                            className: ''
                        },
                        {
                            data: 'nik',
                            name: 'nik',
                            className: 'adm-mono'
                        },
                        {
                            data: 'status_badge',
                            name: 'status',
                            className: 'tc'
                        },
                        {
                            data: 'payment_badge',
                            name: 'status_pembayaran',
                            className: 'tc'
                        },
                        {
                            data: 'tagihan_cell',
                            name: 'tagihan_cell',
                            orderable: false,
                            searchable: false,
                            className: 'tc'
                        },
                        {
                            data: 'aksi',
                            name: 'aksi',
                            orderable: false,
                            searchable: false,
                            className: 'tc'
                        },
                    ],
                    language: {
                        search: 'Cari:',
                        lengthMenu: 'Tampilkan _MENU_ data',
                        info: 'Menampilkan _START_ – _END_ dari _TOTAL_ data',
                        infoEmpty: 'Tidak ada data',
                        paginate: {
                            previous: '‹',
                            next: '›'
                        },
                        zeroRecords: 'Tidak ada data ditemukan',
                        emptyTable: 'Belum ada data lapangan',
                        processing: '<div class="spinner-border text-primary" role="status"></div>',
                    },
                    pageLength: 25,
                    order: [
                        [2, 'desc']
                    ],
                    responsive: true,
                    drawCallback: function() {
                        attachForceUnlockHandlers();
                        attachCheckboxHandlers();
                        updateBulkBar();
                    },
                });

                // ── Export Excel ──
                document.getElementById('exportBtn').addEventListener('click', function() {
                    const params = new URLSearchParams();
                    const search = table.search();
                    if (search) params.append('search', search);
                    window.location.href = EXPORT_URL + (params.toString() ? '?' + params.toString() : '');
                });

                // ── Force Unlock ──
                function attachForceUnlockHandlers() {
                    document.querySelectorAll('.btn-force-unlock').forEach(btn => {
                        const nb = btn.cloneNode(true);
                        btn.parentNode.replaceChild(nb, btn);
                        nb.addEventListener('click', async function() {
                            if (!confirm('Yakin ingin membuka paksa kunci data ini?')) return;
                            const id = this.dataset.id;
                            this.disabled = true;
                            this.innerHTML =
                                '<span class="spinner-border spinner-border-sm"></span>';
                            try {
                                const res = await fetch(`${FORCE_UNLOCK_URL}/${id}/force-unlock`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': getCsrfToken(),
                                        'Accept': 'application/json'
                                    },
                                    credentials: 'same-origin'
                                });
                                const data = await res.json();
                                if (data.success) table.ajax.reload(null, false);
                                else {
                                    alert('Gagal unlock: ' + data.message);
                                    this.disabled = false;
                                }
                            } catch {
                                alert('Terjadi kesalahan saat unlock');
                                this.disabled = false;
                            }
                        });
                    });
                }

                // ── Bulk Payment ──
                const bulkBar = document.getElementById('bulkActionBar');
                const selectedCountEl = document.getElementById('selectedCount');
                const btnBulkDibayar = document.getElementById('btnBulkDibayar');
                const btnCancelSelect = document.getElementById('btnCancelSelect');
                const modalBulkPayment = new bootstrap.Modal(document.getElementById('modalBulkPayment'));
                const modalSelectedCount = document.getElementById('modalSelectedCount');
                const btnConfirmBulk = document.getElementById('btnConfirmBulkDibayar');

                function updateBulkBar() {
                    const checked = document.querySelectorAll('.row-checkbox:checked');
                    const all = document.querySelectorAll('.row-checkbox');
                    const ca = document.getElementById('checkAll');
                    if (checked.length > 0) {
                        bulkBar.classList.remove('d-none');
                        bulkBar.style.display = 'flex';
                        selectedCountEl.textContent = `${checked.length} dipilih`;
                    } else {
                        bulkBar.classList.add('d-none');
                        bulkBar.style.display = 'none';
                    }
                    if (ca) {
                        ca.checked = all.length > 0 && checked.length === all.length;
                        ca.indeterminate = checked.length > 0 && checked.length < all.length;
                    }
                }

                function attachCheckboxHandlers() {
                    document.querySelectorAll('.row-checkbox').forEach(cb =>
                        cb.addEventListener('change', updateBulkBar));
                }

                document.getElementById('checkAll').addEventListener('change', function() {
                    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
                    updateBulkBar();
                });

                btnBulkDibayar.addEventListener('click', function() {
                    const checked = document.querySelectorAll('.row-checkbox:checked');
                    if (!checked.length) return;
                    modalSelectedCount.textContent = checked.length;
                    modalBulkPayment.show();
                });

                btnConfirmBulk.addEventListener('click', async function() {
                    const checked = document.querySelectorAll('.row-checkbox:checked');
                    if (!checked.length) return;
                    const ids = Array.from(checked).map(cb => cb.value);
                    modalBulkPayment.hide();
                    btnBulkDibayar.disabled = true;
                    btnBulkDibayar.innerHTML =
                        '<span class="spinner-border spinner-border-sm"></span> Memproses...';
                    try {
                        const res = await fetch(BULK_PAYMENT_URL, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': getCsrfToken()
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify({
                                ids
                            })
                        });
                        const data = await res.json();
                        if (data.success) table.ajax.reload(null, false);
                        else alert(data.message || 'Gagal memperbarui data');
                    } catch {
                        alert('Terjadi kesalahan');
                    } finally {
                        btnBulkDibayar.disabled = false;
                        btnBulkDibayar.innerHTML =
                            '<svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2;"><polyline points="20 6 9 17 4 12"/></svg> Tandai Dibayar';
                    }
                });

                btnCancelSelect.addEventListener('click', function() {
                    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
                    updateBulkBar();
                });
            });
        </script>
    @endpush
@endsection
