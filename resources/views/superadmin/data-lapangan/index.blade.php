@extends('layouts.app')

@section('template_title')
    Data Lapangans
@endsection

@section('content')
    <div class="row">
        <div class="col">
            @include('layouts.messages')
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span id="card_title">
                            {{ __('Data Lapangans') }}
                        </span>

                        <div class="float-right">
                            <button id="exportBtn" class="btn btn-success btn-sm me-2">
                                <i class="fa fa-file-excel"></i> Export Excel
                            </button>
                            <a href="{{ route('superadmin.data-lapangans.create') }}"
                                class="btn btn-primary btn-sm float-right" data-placement="left">
                                {{ __('Create New') }}
                            </a>
                            <a href="{{ route('superadmin.data-lapangans.data-revisi') }}"
                                class="btn btn-primary btn-sm float-right" data-placement="left">
                                {{ __('Data Revisi') }}
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Bulk Action Bar --}}
                <div id="bulkActionBar" class="px-3 py-2 border-bottom d-none align-items-center gap-3"
                    style="background-color: #f0f7ff;">
                    <span id="selectedCount" class="fw-bold text-primary">0 dipilih</span>
                    <button id="btnBulkDibayar" class="btn btn-success btn-sm">
                        <i class="las la-check-circle"></i> Tandai Dibayar
                    </button>
                    <button id="btnCancelSelect" class="btn btn-outline-secondary btn-sm">
                        Batal
                    </button>
                </div>

                <!-- Form Search -->
                <div class="card-body bg-white border-bottom">
                    <form id="searchForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Cari Nama PU / Pendamping</label>
                                <input type="text" class="form-control" id="search" name="search"
                                    placeholder="Cari berdasarkan nama PU atau pendamping..."
                                    value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="status-1" class="form-label">Status</label>
                                <select class="form-control" id="status-1" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>
                                        Pending</option>
                                    <option value="Terverifikasi"
                                        {{ request('status') == 'Terverifikasi' ? 'selected' : '' }}>Terverifikasi
                                    </option>
                                    <option value="Progress OSS"
                                        {{ request('status') == 'Progress OSS' ? 'selected' : '' }}>Progress OSS
                                    </option>
                                    <option value="Progress SIHALAL"
                                        {{ request('status') == 'Progress SIHALAL' ? 'selected' : '' }}>Progress SIHALAL
                                    </option>
                                    <option value="Terbit SH" {{ request('status') == 'Terbit SH' ? 'selected' : '' }}>
                                        Terbit SH</option>
                                    <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>
                                        Ditolak</option>
                                    <option value="Revisi" {{ request('status') == 'Revisi' ? 'selected' : '' }}>
                                        Revisi</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status_pembayaran" class="form-label">Status Pembayaran</label>
                                <select class="form-control" id="status_pembayaran" name="status_pembayaran">
                                    <option value="">Semua</option>
                                    <option value="PENDING"
                                        {{ request('status_pembayaran') == 'PENDING' ? 'selected' : '' }}>
                                        Pending</option>
                                    <option value="PENGAJUAN"
                                        {{ request('status_pembayaran') == 'PENGAJUAN' ? 'selected' : '' }}>
                                        Pengajuan</option>
                                    <option value="DIBAYAR"
                                        {{ request('status_pembayaran') == 'DIBAYAR' ? 'selected' : '' }}>
                                        Dibayar</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="tanggal_dari" class="form-label">Tanggal Dari</label>
                                <input type="date" class="form-control" id="tanggal_dari" name="tanggal_dari"
                                    value="{{ request('tanggal_dari') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="tanggal_sampai" class="form-label">Tanggal Sampai</label>
                                <input type="date" class="form-control" id="tanggal_sampai" name="tanggal_sampai"
                                    value="{{ request('tanggal_sampai') }}">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body bg-white">
                    <!-- Modal Konfirmasi Bulk Payment -->
                    <div id="modalBulkPayment" class="modal fade" tabindex="-1" aria-labelledby="modalBulkPaymentLabel"
                        aria-hidden="true" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalBulkPaymentLabel">
                                        <i class="las la-check-circle text-success me-1"></i>
                                        Konfirmasi Tandai Dibayar
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="text-muted">
                                        Anda akan menandai <strong id="modalSelectedCount">0</strong> data sebagai <span
                                            class="badge bg-success">DIBAYAR</span>.
                                    </p>
                                    <p class="text-muted mb-0">Tindakan ini tidak dapat dibatalkan. Pastikan data yang
                                        dipilih sudah benar.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                    <button type="button" id="btnConfirmBulkDibayar" class="btn btn-success">
                                        <i class="las la-check-circle"></i> Ya, Tandai Dibayar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Modal Konfirmasi Bulk Payment -->

                    <!-- Loading indicator -->
                    <div id="tableLoading" class="text-center py-5" style="display: none;">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted fw-bold">SABAR BOS...</p>
                    </div>

                    <!-- Table wrapper -->
                    <div id="tableWrapper">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th style="width: 40px;">
                                            <input type="checkbox" id="checkAll" title="Pilih semua">
                                        </th>
                                        <th>No</th>
                                        <th>Created</th>
                                        <th>Pendamping</th>
                                        <th>Nama PU</th>
                                        <th>NIK</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Spotcheck</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    {{-- Initial load dari server --}}
                                    @include('superadmin.data-lapangan.partials.table-body')
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination wrapper --}}
                        <div id="paginationWrapper">
                            @include('layouts.pagination', ['paginator' => $dataLapangans])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ─── Element references ────────────────────────────────────────
            const searchForm = document.getElementById('searchForm');
            const searchInput = document.getElementById('search');
            const statusSelect = document.getElementById('status-1');
            const statusPembayaranSelect = document.getElementById('status_pembayaran');
            const tanggalDariInput = document.getElementById('tanggal_dari');
            const tanggalSampaiInput = document.getElementById('tanggal_sampai');
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

            // API Base URL
            const API_BASE_URL = '/api/superadmin/data-lapangans';

            let searchTimeout;

            // ─── Helper: CSRF token ────────────────────────────────────────
            function getCsrfToken() {
                return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                    document.querySelector('input[name="_token"]')?.value;
            }

            // ─── Force Unlock ──────────────────────────────────────────────
            function attachForceUnlockHandlers() {
                document.querySelectorAll('.btn-force-unlock').forEach(btn => {
                    const newBtn = btn.cloneNode(true);
                    btn.parentNode.replaceChild(newBtn, btn);

                    newBtn.addEventListener('click', async function() {
                        if (!confirm('Yakin ingin membuka paksa kunci data ini?')) return;

                        const id = this.dataset.id;
                        this.disabled = true;
                        this.innerHTML =
                            '<span class="spinner-border spinner-border-sm"></span>';

                        try {
                            const res = await fetch(`${API_BASE_URL}/${id}/force-unlock`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': getCsrfToken(),
                                    'Accept': 'application/json',
                                },
                                credentials: 'same-origin'
                            });

                            const data = await res.json();

                            if (data.success) {
                                loadData();
                            } else {
                                alert('Gagal unlock: ' + data.message);
                                this.disabled = false;
                                this.innerHTML = '<i class="las la-lock-open"></i>';
                            }
                        } catch (error) {
                            console.error('Error force unlock:', error);
                            alert('Terjadi kesalahan saat unlock');
                            this.disabled = false;
                            this.innerHTML = '<i class="las la-lock-open"></i>';
                        }
                    });
                });
            }

            // ─── Export Excel ──────────────────────────────────────────────
            exportBtn.addEventListener('click', function() {
                const params = new URLSearchParams();

                if (searchInput.value.trim()) params.append('search', searchInput.value.trim());
                if (statusSelect.value.trim()) params.append('status', statusSelect.value.trim());
                if (statusPembayaranSelect.value.trim()) params.append('status_pembayaran',
                    statusPembayaranSelect.value.trim());
                if (tanggalDariInput.value.trim()) params.append('tanggal_dari', tanggalDariInput.value
                    .trim());
                if (tanggalSampaiInput.value.trim()) params.append('tanggal_sampai', tanggalSampaiInput
                    .value.trim());

                const exportUrl = '{{ route('superadmin.data-lapangans.export') }}' +
                    (params.toString() ? '?' + params.toString() : '');

                window.location.href = exportUrl;
            });

            // ─── Load Data (AJAX) ──────────────────────────────────────────
            function loadData(url = null) {
                tableWrapper.style.display = 'none';
                tableLoading.style.display = 'block';

                const formInputs = searchForm.querySelectorAll('input, select');
                formInputs.forEach(input => input.disabled = true);

                let fetchUrl = API_BASE_URL;
                const params = new URLSearchParams();

                if (searchInput.value.trim()) params.append('search', searchInput.value.trim());
                if (statusSelect.value.trim()) params.append('status', statusSelect.value.trim());
                if (statusPembayaranSelect.value.trim()) params.append('status_pembayaran', statusPembayaranSelect
                    .value.trim());
                if (tanggalDariInput.value.trim()) params.append('tanggal_dari', tanggalDariInput.value.trim());
                if (tanggalSampaiInput.value.trim()) params.append('tanggal_sampai', tanggalSampaiInput.value
                .trim());

                if (url) {
                    const urlObj = new URL(url, window.location.origin);
                    const page = urlObj.searchParams.get('page');
                    if (page) params.append('page', page);
                }

                if (params.toString()) fetchUrl += '?' + params.toString();

                fetch(fetchUrl, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            tableBody.innerHTML = data.table;
                            paginationWrapper.innerHTML = data.pagination;

                            // Re-attach semua handlers ke elemen baru di tbody
                            attachDeleteHandlers();
                            attachPaginationHandlers();
                            attachForceUnlockHandlers();
                            attachCheckboxHandlers(); // row checkboxes saja
                            updateBulkBar(); // reset bulk bar & sinkron checkAll
                        } else {
                            alert(data.message || 'Terjadi kesalahan saat memuat data');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading data:', error);
                        alert('Terjadi kesalahan saat memuat data: ' + error.message);
                    })
                    .finally(() => {
                        tableLoading.style.display = 'none';
                        tableWrapper.style.display = 'block';
                        formInputs.forEach(input => input.disabled = false);
                    });
            }

            // ─── Search & Filter listeners ─────────────────────────────────
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => loadData(), 500);
            });

            statusSelect.addEventListener('change', () => loadData());
            statusPembayaranSelect.addEventListener('change', () => loadData());
            tanggalDariInput.addEventListener('change', () => loadData());
            tanggalSampaiInput.addEventListener('change', () => loadData());

            // ─── Delete ────────────────────────────────────────────────────
            function attachDeleteHandlers() {
                document.querySelectorAll('.delete-form').forEach(form => {
                    const newForm = form.cloneNode(true);
                    form.parentNode.replaceChild(newForm, form);

                    newForm.addEventListener('submit', function(e) {
                        e.preventDefault();

                        if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) return false;

                        const dataId = this.dataset.id;

                        fetch(`${API_BASE_URL}/${dataId}`, {
                                method: 'DELETE',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': getCsrfToken(),
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                credentials: 'same-origin'
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert(data.message || 'Data berhasil dihapus');
                                    loadData();
                                } else {
                                    alert(data.message || 'Gagal menghapus data');
                                }
                            })
                            .catch(error => {
                                console.error('Error deleting data:', error);
                                alert('Terjadi kesalahan saat menghapus data');
                            });
                    });
                });
            }

            // ─── Pagination ────────────────────────────────────────────────
            function attachPaginationHandlers() {
                paginationWrapper.querySelectorAll('a.page-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const url = this.getAttribute('href');
                        if (url && url !== '#') loadData(url);
                    });
                });
            }

            // ─── Bulk Payment ──────────────────────────────────────────────

            /**
             * Update tampilan bulk bar & sinkronisasi checkAll.
             * Dipanggil: setiap kali checkbox row berubah, dan setelah loadData().
             */
            function updateBulkBar() {
                const checked = document.querySelectorAll('.row-checkbox:checked');
                const allCheckboxes = document.querySelectorAll('.row-checkbox');
                const checkAll = document.getElementById('checkAll');

                // Tampilkan/sembunyikan bulk bar
                if (checked.length > 0) {
                    bulkBar.classList.remove('d-none');
                    bulkBar.classList.add('d-flex');
                    selectedCount.textContent = `${checked.length} dipilih`;
                } else {
                    bulkBar.classList.add('d-none');
                    bulkBar.classList.remove('d-flex');
                }

                // Sinkronisasi state checkAll (di thead — tidak pernah di-replace)
                if (checkAll) {
                    checkAll.checked = allCheckboxes.length > 0 && checked.length === allCheckboxes.length;
                    checkAll.indeterminate = checked.length > 0 && checked.length < allCheckboxes.length;
                }
            }

            /**
             * Attach listener ke .row-checkbox di tbody.
             * Dipanggil setiap kali loadData() selesai karena tbody di-replace.
             * Tidak perlu clone — elemen ini baru dibuat oleh innerHTML.
             */
            function attachCheckboxHandlers() {
                document.querySelectorAll('.row-checkbox').forEach(cb => {
                    cb.addEventListener('change', updateBulkBar);
                });
            }

            /**
             * Attach listener ke #checkAll di thead.
             * Dipanggil SEKALI saat DOMContentLoaded karena thead tidak pernah di-replace.
             */
            function attachCheckAllHandler() {
                const checkAll = document.getElementById('checkAll');
                if (!checkAll) return;

                checkAll.addEventListener('change', function() {
                    document.querySelectorAll('.row-checkbox').forEach(cb => {
                        cb.checked = this.checked;
                    });
                    updateBulkBar();
                });
            }

            /**
             * Tombol "Tandai Dibayar" — buka modal konfirmasi
             */
            btnBulkDibayar.addEventListener('click', function() {
                const checked = document.querySelectorAll('.row-checkbox:checked');
                if (checked.length === 0) return;

                // Tampilkan jumlah item di dalam modal
                modalSelectedCount.textContent = checked.length;
                modalBulkPayment.show();
            });

            /**
             * Tombol konfirmasi di dalam modal — eksekusi bulk update
             */
            btnConfirmBulkDibayar.addEventListener('click', async function() {
                const checked = document.querySelectorAll('.row-checkbox:checked');
                if (checked.length === 0) return;

                const ids = Array.from(checked).map(cb => cb.value);

                // Tutup modal & tampilkan loading di tombol
                modalBulkPayment.hide();
                btnBulkDibayar.disabled = true;
                btnBulkDibayar.innerHTML =
                    '<span class="spinner-border spinner-border-sm"></span> Memproses...';

                try {
                    const res = await fetch('{{ route('superadmin.data-lapangans.bulk-payment') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken(),
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            ids
                        }),
                    });

                    const data = await res.json();

                    if (data.success) {
                        loadData(); // Tabel refresh → updateBulkBar() dipanggil di dalamnya
                    } else {
                        alert(data.message || 'Gagal memperbarui data');
                    }
                } catch (err) {
                    console.error('Bulk payment error:', err);
                    alert('Terjadi kesalahan saat memperbarui data');
                } finally {
                    btnBulkDibayar.disabled = false;
                    btnBulkDibayar.innerHTML = '<i class="las la-check-circle"></i> Tandai Dibayar';
                }
            });

            /**
             * Tombol "Batal" — uncheck semua & sembunyikan bulk bar
             */
            btnCancelSelect.addEventListener('click', function() {
                document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
                updateBulkBar();
            });

            // ─── Initial attachment ────────────────────────────────────────
            attachDeleteHandlers();
            attachPaginationHandlers();
            attachForceUnlockHandlers();
            attachCheckAllHandler();
            attachCheckboxHandlers();
            updateBulkBar();

            // ─── Auto refresh saat back/forward browser ────────────────────
            window.addEventListener('pageshow', function(event) {
                if (event.persisted ||
                    (window.performance && window.performance.navigation.type === 2)) {
                    loadData();
                }
            });
        });
    </script>
@endsection
