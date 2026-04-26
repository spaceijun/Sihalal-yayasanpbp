@extends('layouts.app')
@section('template_title') Data Lapangan @endsection
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
                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Export Excel
            </button>
            <a href="{{ route('superadmin.data-lapangans.data-revisi') }}" class="adm-btn-secondary">
                <svg viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/></svg>
                Data Revisi
            </a>
            <a href="{{ route('superadmin.data-lapangans.create') }}" class="adm-btn-primary">
                <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah
            </a>
        </div>
    </div>

    {{-- ── BULK ACTION BAR ── --}}
    <div id="bulkActionBar" class="d-none"
        style="display:none;align-items:center;gap:12px;padding:10px 16px;background:var(--adm-blue-lt);border:1px solid var(--adm-blue);border-radius:var(--adm-radius);margin-bottom:14px;">
        <span id="selectedCount" style="font-weight:700;color:var(--adm-blue);font-size:13px;">0 dipilih</span>
        <button id="btnBulkDibayar" class="adm-btn success" style="font-size:12px;padding:5px 14px;">
            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Tandai Dibayar
        </button>
        <button id="btnCancelSelect" class="adm-btn-secondary" style="font-size:12px;padding:5px 12px;">Batal</button>
    </div>

    <div class="adm-card">
        {{-- ── FILTER BAR ── --}}
        <div class="adm-filter-bar" style="flex-wrap:wrap;gap:10px;">
            <form id="searchForm" style="display:contents;">
                @csrf
                <div class="adm-filter-group">
                    <label class="adm-filter-label">Cari</label>
                    <div class="adm-search-shell">
                        <svg class="adm-search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text" id="search" name="search" class="adm-search-input" style="width:220px;"
                            placeholder="Nama PU atau pendamping..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="adm-filter-group">
                    <label class="adm-filter-label">Status</label>
                    <select id="status-1" name="status" class="adm-select">
                        <option value="">Semua Status</option>
                        @foreach(['Pending','Terverifikasi','Progress OSS','Progress SIHALAL','Terbit SH','Ditolak','Revisi'] as $s)
                            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="adm-filter-group">
                    <label class="adm-filter-label">Pembayaran</label>
                    <select id="status_pembayaran" name="status_pembayaran" class="adm-select">
                        <option value="">Semua</option>
                        <option value="PENDING" {{ request('status_pembayaran') == 'PENDING' ? 'selected' : '' }}>Pending</option>
                        <option value="PENGAJUAN" {{ request('status_pembayaran') == 'PENGAJUAN' ? 'selected' : '' }}>Pengajuan</option>
                        <option value="DIBAYAR" {{ request('status_pembayaran') == 'DIBAYAR' ? 'selected' : '' }}>Dibayar</option>
                    </select>
                </div>
                <div class="adm-filter-group">
                    <label class="adm-filter-label">Dari Tanggal</label>
                    <input type="date" id="tanggal_dari" name="tanggal_dari" class="adm-select"
                        value="{{ request('tanggal_dari') }}">
                </div>
                <div class="adm-filter-group">
                    <label class="adm-filter-label">Sampai</label>
                    <input type="date" id="tanggal_sampai" name="tanggal_sampai" class="adm-select"
                        value="{{ request('tanggal_sampai') }}">
                </div>
                <div style="display:flex;align-items:flex-end;">
                    <button type="button" id="resetBtn" class="adm-reset-btn">
                        <svg viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/></svg>
                        Reset
                    </button>
                </div>
            </form>
        </div>

        {{-- ── MODAL BULK PAYMENT ── --}}
        <div id="modalBulkPayment" class="modal fade" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <svg viewBox="0 0 24 24" style="width:18px;height:18px;stroke:var(--adm-green);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;margin-right:6px;vertical-align:-3px;">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            Konfirmasi Tandai Dibayar
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" style="padding:20px 24px;">
                        <div class="adm-alert adm-alert-success" style="margin-bottom:0;">
                            <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            <div>
                                <p style="margin:0;font-size:13px;">Anda akan menandai <strong id="modalSelectedCount">0</strong> data sebagai <strong>DIBAYAR</strong>.</p>
                                <p style="margin:4px 0 0;font-size:12.5px;color:var(--adm-text-muted);">Tindakan ini tidak dapat dibatalkan. Pastikan data sudah benar.</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" id="btnConfirmBulkDibayar" class="adm-btn-primary"
                            style="background:linear-gradient(135deg,var(--adm-green),#15803d);">
                            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            Ya, Tandai Dibayar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Loading --}}
        <div id="tableLoading" style="display:none;text-align:center;padding:40px;">
            <div class="spinner-border text-primary" role="status" style="width:2.5rem;height:2.5rem;"></div>
            <p style="margin-top:12px;font-weight:600;color:var(--adm-text-muted);">Memuat data...</p>
        </div>

        {{-- Table wrapper --}}
        <div id="tableWrapper">
            <div class="table-responsive">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th style="width:40px;text-align:center;">
                                <input type="checkbox" id="checkAll" title="Pilih semua" style="cursor:pointer;">
                            </th>
                            <th style="width:44px">#</th>
                            <th>Waktu</th>
                            <th>Pendamping</th>
                            <th>Nama PU</th>
                            <th>NIK</th>
                            <th class="tc">Status</th>
                            <th class="tc">Payment</th>
                            <th class="tc">Spotcheck</th>
                            <th class="tc" style="width:110px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @include('superadmin.data-lapangan.partials.table-body')
                    </tbody>
                </table>
            </div>

            <div class="adm-card-footer">
                <span class="adm-footer-info">
                    Menampilkan {{ $dataLapangans->firstItem() ?? 0 }}–{{ $dataLapangans->lastItem() ?? 0 }}
                    dari {{ $dataLapangans->total() }} data
                </span>
                <div id="paginationWrapper">
                    @include('layouts.pagination', ['paginator' => $dataLapangans])
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchForm = document.getElementById('searchForm');
        const searchInput = document.getElementById('search');
        const statusSelect = document.getElementById('status-1');
        const statusPembayaranSelect = document.getElementById('status_pembayaran');
        const tanggalDariInput = document.getElementById('tanggal_dari');
        const tanggalSampaiInput = document.getElementById('tanggal_sampai');
        const resetBtn = document.getElementById('resetBtn');
        const tableBody = document.getElementById('tableBody');
        const paginationWrapper = document.getElementById('paginationWrapper');
        const tableLoading = document.getElementById('tableLoading');
        const tableWrapper = document.getElementById('tableWrapper');
        const exportBtn = document.getElementById('exportBtn');
        const bulkBar = document.getElementById('bulkActionBar');
        const selectedCount = document.getElementById('selectedCount');
        const btnBulkDibayar = document.getElementById('btnBulkDibayar');
        const btnCancelSelect = document.getElementById('btnCancelSelect');
        const modalBulkPayment = new bootstrap.Modal(document.getElementById('modalBulkPayment'));
        const modalSelectedCount = document.getElementById('modalSelectedCount');
        const btnConfirmBulkDibayar = document.getElementById('btnConfirmBulkDibayar');
        const API_BASE_URL = '/api/superadmin/data-lapangans';
        let searchTimeout;

        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                document.querySelector('input[name="_token"]')?.value;
        }

        // ── Force Unlock ──
        function attachForceUnlockHandlers() {
            document.querySelectorAll('.btn-force-unlock').forEach(btn => {
                const newBtn = btn.cloneNode(true);
                btn.parentNode.replaceChild(newBtn, btn);
                newBtn.addEventListener('click', async function() {
                    if (!confirm('Yakin ingin membuka paksa kunci data ini?')) return;
                    const id = this.dataset.id;
                    this.disabled = true;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                    try {
                        const res = await fetch(`${API_BASE_URL}/${id}/force-unlock`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                            credentials: 'same-origin'
                        });
                        const data = await res.json();
                        if (data.success) loadData();
                        else { alert('Gagal unlock: ' + data.message); this.disabled = false; }
                    } catch (err) {
                        alert('Terjadi kesalahan saat unlock');
                        this.disabled = false;
                    }
                });
            });
        }

        // ── Export Excel ──
        exportBtn.addEventListener('click', function() {
            const params = new URLSearchParams();
            if (searchInput.value.trim()) params.append('search', searchInput.value.trim());
            if (statusSelect.value.trim()) params.append('status', statusSelect.value.trim());
            if (statusPembayaranSelect.value.trim()) params.append('status_pembayaran', statusPembayaranSelect.value.trim());
            if (tanggalDariInput.value.trim()) params.append('tanggal_dari', tanggalDariInput.value.trim());
            if (tanggalSampaiInput.value.trim()) params.append('tanggal_sampai', tanggalSampaiInput.value.trim());
            window.location.href = '{{ route('superadmin.data-lapangans.export') }}' + (params.toString() ? '?' + params.toString() : '');
        });

        // ── Load Data (AJAX) ──
        function loadData(url = null) {
            tableWrapper.style.display = 'none';
            tableLoading.style.display = 'block';
            const formInputs = searchForm.querySelectorAll('input, select');
            formInputs.forEach(i => i.disabled = true);
            const params = new URLSearchParams();
            if (searchInput.value.trim()) params.append('search', searchInput.value.trim());
            if (statusSelect.value.trim()) params.append('status', statusSelect.value.trim());
            if (statusPembayaranSelect.value.trim()) params.append('status_pembayaran', statusPembayaranSelect.value.trim());
            if (tanggalDariInput.value.trim()) params.append('tanggal_dari', tanggalDariInput.value.trim());
            if (tanggalSampaiInput.value.trim()) params.append('tanggal_sampai', tanggalSampaiInput.value.trim());
            if (url) {
                try { const page = new URL(url, window.location.origin).searchParams.get('page'); if (page) params.append('page', page); } catch(e) {}
            }
            let fetchUrl = API_BASE_URL + (params.toString() ? '?' + params.toString() : '');
            fetch(fetchUrl, {
                method: 'GET',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            })
            .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .then(data => {
                if (data.success) {
                    tableBody.innerHTML = data.table;
                    paginationWrapper.innerHTML = data.pagination;
                    attachDeleteHandlers();
                    attachPaginationHandlers();
                    attachForceUnlockHandlers();
                    attachCheckboxHandlers();
                    updateBulkBar();
                } else alert(data.message || 'Terjadi kesalahan');
            })
            .catch(err => alert('Error: ' + err.message))
            .finally(() => {
                tableLoading.style.display = 'none';
                tableWrapper.style.display = 'block';
                formInputs.forEach(i => i.disabled = false);
            });
        }

        searchInput.addEventListener('input', () => { clearTimeout(searchTimeout); searchTimeout = setTimeout(() => loadData(), 500); });
        statusSelect.addEventListener('change', () => loadData());
        statusPembayaranSelect.addEventListener('change', () => loadData());
        tanggalDariInput.addEventListener('change', () => loadData());
        tanggalSampaiInput.addEventListener('change', () => loadData());
        resetBtn.addEventListener('click', () => {
            searchInput.value = ''; statusSelect.value = '';
            statusPembayaranSelect.value = ''; tanggalDariInput.value = ''; tanggalSampaiInput.value = '';
            loadData();
        });

        // ── Delete ──
        function attachDeleteHandlers() {
            document.querySelectorAll('.delete-form').forEach(form => {
                const nf = form.cloneNode(true);
                form.parentNode.replaceChild(nf, form);
                nf.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) return;
                    fetch(`${API_BASE_URL}/${this.dataset.id}`, {
                        method: 'DELETE',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin'
                    }).then(r => r.json()).then(data => {
                        if (data.success) { alert(data.message || 'Berhasil dihapus'); loadData(); }
                        else alert(data.message || 'Gagal menghapus');
                    }).catch(() => alert('Terjadi kesalahan'));
                });
            });
        }

        // ── Pagination ──
        function attachPaginationHandlers() {
            paginationWrapper.querySelectorAll('a.page-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');
                    if (url && url !== '#') loadData(url);
                });
            });
        }

        // ── Bulk Payment ──
        function updateBulkBar() {
            const checked = document.querySelectorAll('.row-checkbox:checked');
            const allCbs = document.querySelectorAll('.row-checkbox');
            const checkAll = document.getElementById('checkAll');
            if (checked.length > 0) {
                bulkBar.classList.remove('d-none'); bulkBar.style.display = 'flex';
                selectedCount.textContent = `${checked.length} dipilih`;
            } else {
                bulkBar.classList.add('d-none'); bulkBar.style.display = 'none';
            }
            if (checkAll) {
                checkAll.checked = allCbs.length > 0 && checked.length === allCbs.length;
                checkAll.indeterminate = checked.length > 0 && checked.length < allCbs.length;
            }
        }

        function attachCheckboxHandlers() {
            document.querySelectorAll('.row-checkbox').forEach(cb => cb.addEventListener('change', updateBulkBar));
        }

        function attachCheckAllHandler() {
            const checkAll = document.getElementById('checkAll');
            if (!checkAll) return;
            checkAll.addEventListener('change', function() {
                document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
                updateBulkBar();
            });
        }

        btnBulkDibayar.addEventListener('click', function() {
            const checked = document.querySelectorAll('.row-checkbox:checked');
            if (!checked.length) return;
            modalSelectedCount.textContent = checked.length;
            modalBulkPayment.show();
        });

        btnConfirmBulkDibayar.addEventListener('click', async function() {
            const checked = document.querySelectorAll('.row-checkbox:checked');
            if (!checked.length) return;
            const ids = Array.from(checked).map(cb => cb.value);
            modalBulkPayment.hide();
            btnBulkDibayar.disabled = true;
            btnBulkDibayar.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memproses...';
            try {
                const res = await fetch('{{ route('superadmin.data-lapangans.bulk-payment') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                    credentials: 'same-origin',
                    body: JSON.stringify({ ids })
                });
                const data = await res.json();
                if (data.success) loadData();
                else alert(data.message || 'Gagal memperbarui data');
            } catch(err) { alert('Terjadi kesalahan'); }
            finally { btnBulkDibayar.disabled = false; btnBulkDibayar.innerHTML = '<svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2;"><polyline points="20 6 9 17 4 12"/></svg> Tandai Dibayar'; }
        });

        btnCancelSelect.addEventListener('click', function() {
            document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
            updateBulkBar();
        });

        attachDeleteHandlers();
        attachPaginationHandlers();
        attachForceUnlockHandlers();
        attachCheckAllHandler();
        attachCheckboxHandlers();
        updateBulkBar();

        window.addEventListener('pageshow', function(event) {
            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) loadData();
        });
    });
</script>

@endsection
