@extends('layouts.app')

@section('template_title')
    Data Lapangan
@endsection

@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        {{-- ── PAGE HEADER ── --}}
        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Data Lapangan</h1>
                <p>Kelola data lapangan pelaku usaha di lapangan secara real-time</p>
            </div>
            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                <button id="exportBtn" class="adm-btn success" style="gap:6px;">
                    <svg viewBox="0 0 24 24" style="stroke-width:2.2;">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="16" y1="13" x2="8" y2="13" />
                        <line x1="16" y1="17" x2="8" y2="17" />
                    </svg>
                    Export Excel
                </button>
                <a href="{{ route('superadmin.data-lapangans.data-revisi') }}" class="adm-btn-secondary" style="gap:6px;">
                    <svg viewBox="0 0 24 24">
                        <polyline points="1 4 1 10 7 10" />
                        <path d="M3.51 15a9 9 0 1 0 .49-3.5" />
                    </svg>
                    Data Revisi
                </a>
                <a href="{{ route('superadmin.data-lapangans.create') }}" class="adm-btn-primary" style="gap:6px;">
                    <svg viewBox="0 0 24 24">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Tambah
                </a>
            </div>
        </div>

        {{-- ── STATS / SUMMARY CARDS ── --}}
        <div class="adm-stats" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 20px;">
            {{-- PENDING --}}
            <div class="adm-stat" style="border-top: 4px solid var(--adm-amber); position: relative; overflow: hidden;">
                <div style="position: absolute; right: 12px; top: 12px; opacity: 0.08; color: var(--adm-amber);">
                    <svg viewBox="0 0 24 24"
                        style="width: 54px; height: 54px; fill: none; stroke: currentColor; stroke-width: 2.5;">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                </div>
                <div class="adm-stat-label" style="color: var(--adm-amber); font-weight: 700;">Pending</div>
                <div class="adm-stat-value is-warn" style="font-size: 28px;">{{ $paymentStats['pending_count'] }}</div>
                <div class="adm-stat-sub" style="font-weight: 600; color: var(--adm-text-mid);">
                    Rp {{ number_format($paymentStats['pending_total'], 0, ',', '.') }}
                </div>
            </div>

            {{-- PENGAJUAN --}}
            <div class="adm-stat" style="border-top: 4px solid var(--adm-blue); position: relative; overflow: hidden;">
                <div style="position: absolute; right: 12px; top: 12px; opacity: 0.08; color: var(--adm-blue);">
                    <svg viewBox="0 0 24 24"
                        style="width: 54px; height: 54px; fill: none; stroke: currentColor; stroke-width: 2.5;">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="16" y1="13" x2="8" y2="13" />
                        <line x1="16" y1="17" x2="8" y2="17" />
                    </svg>
                </div>
                <div class="adm-stat-label" style="color: var(--adm-blue); font-weight: 700;">Pengajuan</div>
                <div class="adm-stat-value" style="color: var(--adm-blue); font-size: 28px;">
                    {{ $paymentStats['pengajuan_count'] }}</div>
                <div class="adm-stat-sub" style="font-weight: 600; color: var(--adm-text-mid);">
                    Rp {{ number_format($paymentStats['pengajuan_total'], 0, ',', '.') }}
                </div>
            </div>

            {{-- DIBAYAR --}}
            <div class="adm-stat" style="border-top: 4px solid var(--adm-green); position: relative; overflow: hidden;">
                <div style="position: absolute; right: 12px; top: 12px; opacity: 0.08; color: var(--adm-green);">
                    <svg viewBox="0 0 24 24"
                        style="width: 54px; height: 54px; fill: none; stroke: currentColor; stroke-width: 2.5;">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                        <polyline points="22 4 12 14.01 9 11.01" />
                    </svg>
                </div>
                <div class="adm-stat-label" style="color: var(--adm-green); font-weight: 700;">Dibayar</div>
                <div class="adm-stat-value is-success" style="font-size: 28px;">{{ $paymentStats['dibayar_count'] }}</div>
                <div class="adm-stat-sub" style="font-weight: 600; color: var(--adm-text-mid);">
                    Rp {{ number_format($paymentStats['dibayar_total'], 0, ',', '.') }}
                </div>
            </div>
        </div>

        {{-- ── BULK ACTION BAR ── --}}
        <div id="bulkActionBar" class="d-none"
            style="display:none;align-items:center;gap:12px;padding:12px 18px;background:var(--adm-blue-lt);border:1px solid rgba(26,95,200,0.2);border-radius:var(--adm-radius);margin-bottom:16px;box-shadow:var(--adm-shadow-sm);">
            <div style="display:flex;align-items:center;gap:6px;">
                <span class="adm-count-badge" id="selectedCount"
                    style="min-width:24px;height:24px;border-radius:50%;font-size:12px;">0</span>
                <span style="font-weight:600;color:var(--adm-text-dark);font-size:13px;">data terpilih</span>
            </div>
            <div style="margin-left:auto;display:flex;gap:8px;">
                <button id="btnCancelSelect" class="adm-btn-secondary"
                    style="font-size:12px;padding:0 12px;height:32px;">Batal</button>
                <button id="btnBulkDibayar" class="adm-btn success"
                    style="font-size:12px;padding:0 14px;height:32px;background:linear-gradient(135deg,var(--adm-green),#127d62);border:none;box-shadow:none;">
                    <svg viewBox="0 0 24 24" style="width:14px;height:14px;">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                    Tandai Dibayar
                </button>
            </div>
        </div>

        {{-- ── CARD CONTAINER ── --}}
        <div class="adm-card">
            <div class="adm-card-header">
                <div class="adm-card-title">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                    </svg>
                    Data Lapangan Sertifikasi Halal
                </div>
            </div>

            {{-- ── FILTER BAR ── --}}
            <div class="adm-filter-bar">
                {{-- Text Search --}}
                <div class="adm-filter-group">
                    <label class="adm-filter-label">Cari</label>
                    <div class="adm-search-shell">
                        <svg class="adm-search-icon" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                        <input type="text" id="dtSearch" class="adm-search-input" style="width:220px;"
                            placeholder="Nama PU, NIK, no. registrasi...">
                    </div>
                </div>
                {{-- Status --}}
                <div class="adm-filter-group" style="min-width: 180px;">
                    <label class="adm-filter-label">Status Survei</label>
                    <select id="filterStatus" class="adm-select" style="width: 100%;">
                        <option value="">Semua Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Terverifikasi">Terverifikasi</option>
                        <option value="Progress OSS">Progress OSS</option>
                        <option value="Progress SIHALAL">Progress SIHALAL</option>
                        <option value="Terbit SH">Terbit SH</option>
                        <option value="Ditolak">Ditolak</option>
                        <option value="Revisi">Revisi</option>
                    </select>
                </div>
                {{-- Status Pembayaran --}}
                <div class="adm-filter-group" style="min-width: 150px;">
                    <label class="adm-filter-label">Status Pembayaran</label>
                    <select id="filterPayment" class="adm-select" style="width: 100%;">
                        <option value="">Semua</option>
                        <option value="PENDING">Pending</option>
                        <option value="PENGAJUAN">Pengajuan</option>
                        <option value="DIBAYAR">Dibayar</option>
                    </select>
                </div>
                {{-- Reset Button --}}
                <div style="display:flex;align-items:flex-end;">
                    <button id="resetFilters" class="adm-reset-btn" style="height: 34px;">
                        <svg viewBox="0 0 24 24">
                            <polyline points="1 4 1 10 7 10" />
                            <path d="M3.51 15a9 9 0 1 0 .49-3.5" />
                        </svg>
                        Reset Filter
                    </button>
                </div>
            </div>

            {{-- ── TABLE ── --}}
            <div class="table-responsive" style="padding: 0;">
                <table id="dataLapanganTable" class="adm-table w-100" style="margin: 0;">
                    <thead>
                        <tr>
                            <th style="width:40px;text-align:center;">
                                <input type="checkbox" id="checkAll" title="Pilih semua"
                                    style="cursor:pointer;transform:scale(1.15);">
                            </th>
                            <th style="width:44px" class="tc">#</th>
                            <th>Tanggal</th>
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

    {{-- ── MODAL BULK PAYMENT ── --}}
    <div id="modalBulkPayment" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
            <div class="modal-content"
                style="border:none;border-radius:var(--adm-radius);box-shadow:0 15px 30px rgba(0,0,0,0.15);overflow:hidden;">
                <div class="modal-header"
                    style="background:#fff;border-bottom:1px solid var(--adm-border);padding:16px 20px;">
                    <h5 class="modal-title"
                        style="font-family:'Sora',sans-serif;font-size:15px;font-weight:700;color:var(--adm-text-dark);display:flex;align-items:center;gap:8px;">
                        <svg viewBox="0 0 24 24"
                            style="width:20px;height:20px;stroke:var(--adm-green);fill:none;stroke-width:2.2;">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                        Konfirmasi Pembayaran
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="font-size:12px;opacity:0.6;"></button>
                </div>
                <div class="modal-body" style="padding:24px 20px;background:#fcfdfe;">
                    <div style="text-align:center;margin-bottom:16px;">
                        <div
                            style="width:48px;height:48px;border-radius:50%;background:var(--adm-green-lt);color:var(--adm-green);display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px;">
                            <svg viewBox="0 0 24 24"
                                style="width:24px;height:24px;fill:none;stroke:currentColor;stroke-width:2.2;">
                                <rect x="2" y="4" width="20" height="16" rx="2" />
                                <line x1="12" y1="10" x2="12" y2="14" />
                                <line x1="8" y1="12" x2="16" y2="12" />
                            </svg>
                        </div>
                        <p style="margin:0;font-size:14px;color:var(--adm-text-dark);font-weight:600;">Tandai Dibayar</p>
                        <p style="margin:4px 0 0;font-size:12.5px;color:var(--adm-text-muted);line-height:1.5;">
                            Anda akan menandai <strong id="modalSelectedCount"
                                style="color:var(--adm-green);font-size:13.5px;">0</strong> data pelaku usaha sebagai
                            <strong>DIBAYAR</strong>.
                        </p>
                    </div>
                    <div
                        style="background:#fff;border:1px dashed var(--adm-border-mid);border-radius:8px;padding:12px 14px;font-size:11.5px;color:var(--adm-text-muted);line-height:1.6;">
                        <span style="font-weight:700;color:var(--adm-text-dark);display:block;margin-bottom:2px;">Catatan
                            Penting:</span>
                        Proses ini akan mengaktifkan pembuatan cashflow pemasukan/pengeluaran otomatis dan mengirim
                        notifikasi WhatsApp kepada masing-masing Pendamping.
                    </div>
                </div>
                <div class="modal-footer"
                    style="background:#fff;border-top:1px solid var(--adm-border);padding:14px 20px;display:flex;justify-content:flex-end;gap:8px;">
                    <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal"
                        style="height:34px;font-size:12.5px;">Batal</button>
                    <button type="button" id="btnConfirmBulkDibayar" class="adm-btn-primary"
                        style="background:linear-gradient(135deg,var(--adm-green),#0f6e56);box-shadow:0 2px 8px rgba(15,110,86,0.2);height:34px;font-size:12.5px;border:none;">
                        Ya, Tandai Dibayar
                    </button>
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
                const CSRF_TOKEN = '{{ csrf_token() }}';

                function getCsrfToken() {
                    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                }

                const filterStatus = document.getElementById('filterStatus');
                const filterPayment = document.getElementById('filterPayment');

                // ── Init DataTable ──
                const table = $('#dataLapanganTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('superadmin.data-lapangans.data') }}',
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': getCsrfToken()
                        },
                        data: function(d) {
                            d.status_filter = filterStatus.value;
                            d.payment_filter = filterPayment.value;
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
                            data: 'pendamping_cell',
                            name: 'enumerator_nama',
                            orderable: true,
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
                    dom: 'rt<"adm-card-footer"ip>',
                    language: {
                        info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                        infoEmpty: 'Tidak ada data',
                        paginate: {
                            previous: '‹',
                            next: '›'
                        },
                        zeroRecords: 'Tidak ada data ditemukan',
                        emptyTable: '<div class="adm-empty"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><p>Belum ada data lapangan.</p></div>',
                        processing: '<div class="text-center py-3"><div class="spinner-border" style="color:var(--adm-blue);width:1.8rem;height:1.8rem;" role="status"></div></div>',
                    },
                    pageLength: 25,
                    order: [
                        [2, 'desc']
                    ],
                    responsive: true,
                    drawCallback: function() {
                        attachForceUnlockHandlers();
                        attachToggleUnlockHandlers();
                        attachCheckboxHandlers();
                        updateBulkBar();
                    },
                });

                // ── Search input — debounced ──
                let _searchTimer;
                document.getElementById('dtSearch').addEventListener('input', function() {
                    clearTimeout(_searchTimer);
                    const val = this.value;
                    _searchTimer = setTimeout(function() {
                        table.search(val).draw();
                    }, 400);
                });

                // ── Filter dropdowns → reload table ──
                filterStatus.addEventListener('change', () => table.ajax.reload(null, true));
                filterPayment.addEventListener('change', () => table.ajax.reload(null, true));

                document.getElementById('resetFilters').addEventListener('click', function() {
                    filterStatus.value = '';
                    filterPayment.value = '';
                    document.getElementById('dtSearch').value = '';
                    table.search('').ajax.reload(null, true);
                });

                // ── Export Excel ──
                document.getElementById('exportBtn').addEventListener('click', function() {
                    const params = new URLSearchParams();
                    const s = table.search();
                    if (s) params.append('search', s);
                    if (filterStatus.value) params.append('status', filterStatus.value);
                    if (filterPayment.value) params.append('status_pembayaran', filterPayment.value);
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

                // ── Toggle Unlock for Data Entry ──
                function attachToggleUnlockHandlers() {
                    document.querySelectorAll('.btn-toggle-unlock').forEach(btn => {
                        const nb = btn.cloneNode(true);
                        btn.parentNode.replaceChild(nb, btn);
                        nb.addEventListener('click', async function() {
                            const url = this.dataset.url;
                            this.disabled = true;
                            const origHtml = this.innerHTML;
                            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                            try {
                                const res = await fetch(url, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': CSRF_TOKEN,
                                        'Accept': 'application/json'
                                    },
                                    credentials: 'same-origin'
                                });
                                const data = await res.json();
                                if (data.success) {
                                    table.ajax.reload(null, false);
                                } else {
                                    alert('Gagal: ' + data.message);
                                    this.disabled = false;
                                    this.innerHTML = origHtml;
                                }
                            } catch {
                                alert('Terjadi kesalahan');
                                this.disabled = false;
                                this.innerHTML = origHtml;
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
                        selectedCountEl.textContent = `${checked.length}`;
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
