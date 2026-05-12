@extends('layouts.app')
@section('template_title')
    Enumerators
@endsection

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
            {{-- FILTER BAR --}}
            <div class="adm-filter-bar">
                <form id="searchForm" style="display:contents;">
                    @csrf
                    <div class="adm-filter-group">
                        <label class="adm-filter-label">Cari</label>
                        <div class="adm-search-shell">
                            <svg class="adm-search-icon" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            <input type="text" id="search" name="search" class="adm-search-input"
                                style="width:280px;" placeholder="Cari nama enumerator atau koordinator..."
                                value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="adm-filter-group">
                        <label class="adm-filter-label">Status</label>
                        <select id="status-1" name="status" class="adm-select">
                            <option value="">Semua Status</option>
                            <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Tidak Aktif" {{ request('status') == 'Tidak Aktif' ? 'selected' : '' }}>
                                Tidak Aktif</option>
                        </select>
                    </div>
                    <div style="display:flex;gap:6px;align-items:flex-end;">
                        <button type="button" id="resetBtn" class="adm-reset-btn">
                            <svg viewBox="0 0 24 24">
                                <polyline points="1 4 1 10 7 10" />
                                <path d="M3.51 15a9 9 0 1 0 .49-3.5" />
                            </svg>
                            Reset
                        </button>
                    </div>
                </form>
            </div>

            {{-- TABLE --}}
            <div id="tableLoading" style="display:none;text-align:center;padding:40px;">
                <div class="spinner-border text-primary" role="status" style="width:2.5rem;height:2.5rem;"></div>
                <p style="margin-top:12px;font-weight:600;color:var(--adm-text-muted);">Memuat data...</p>
            </div>
            <div id="tableWrapper">
                <div class="table-responsive">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th>No Registrasi</th>
                                <th>Nama Lengkap</th>
                                <th>Data Masuk (By Month)</th>
                                <th>Rekening</th>
                                <th class="tc">Status</th>
                                <th class="tc" style="width:200px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @include('superadmin.enumerator.partials.table-body')
                        </tbody>
                    </table>
                </div>
                <div class="adm-card-footer">
                    <span class="adm-footer-info">
                        Menampilkan {{ $enumerators->firstItem() ?? 0 }}–{{ $enumerators->lastItem() ?? 0 }}
                        dari {{ $enumerators->total() }} enumerator
                    </span>
                    <div id="paginationWrapper">
                        @include('layouts.pagination', ['paginator' => $enumerators])
                    </div>
                </div>
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
                        {{-- Pilih Bulan --}}
                        <div style="flex:1;">
                            <label for="exportBulan"
                                style="font-size:11px;font-weight:600;color:var(--adm-text-muted);
                                       display:block;margin-bottom:4px;">
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
                        {{-- Pilih Tahun --}}
                        <div style="flex:1;">
                            <label for="exportTahun"
                                style="font-size:11px;font-weight:600;color:var(--adm-text-muted);
                                       display:block;margin-bottom:4px;">
                                Tahun
                            </label>
                            <select id="exportTahun" class="adm-select" style="width:100%;">
                                @foreach (range(now()->year - 2, now()->year) as $y)
                                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Preview periode --}}
                    <div
                        style="margin-top:14px;padding:10px 14px;background:var(--adm-blue-lt);
                                border-radius:6px;font-size:12px;color:var(--adm-blue);font-weight:600;">
                        <svg viewBox="0 0 24 24"
                            style="width:14px;height:14px;stroke:var(--adm-blue);fill:none;stroke-width:2;
                                   stroke-linecap:round;stroke-linejoin:round;margin-right:4px;vertical-align:-2px;">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                        Periode:&nbsp;<span id="exportPreviewLabel"></span>
                    </div>

                    {{-- Info filter aktif --}}
                    <div id="exportFilterInfo" class="adm-alert adm-alert-warning" style="margin-top:12px;display:none;">
                        <svg viewBox="0 0 24 24">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3
                                                         L13.71 3.86a2 2 0 0 0-3.42 0z" />
                            <line x1="12" y1="9" x2="12" y2="13" />
                            <line x1="12" y1="17" x2="12.01" y2="17" />
                        </svg>
                        <div>Filter pencarian aktif akan ikut diterapkan pada laporan.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="exportPdfConfirmBtn" class="adm-btn-primary">
                        <svg viewBox="0 0 24 24"
                            style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2;
                   stroke-linecap:round;stroke-linejoin:round;">
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
                            style="width:18px;height:18px;stroke:var(--adm-blue);fill:none;stroke-width:2;
                                   stroke-linecap:round;stroke-linejoin:round;margin-right:6px;vertical-align:-3px;">
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
                    <p style="font-size:13px;color:var(--adm-text-muted);margin-bottom:14px;">
                        Akun user akan dibuat dengan detail berikut:
                    </p>
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
                                <code
                                    style="background:var(--adm-blue-lt);color:var(--adm-blue);
                                             padding:2px 8px;border-radius:4px;font-size:12px;">
                                    enumkh123
                                </code>
                            </span>
                        </div>
                        <div class="adm-info-row">
                            <span class="adm-info-key">Role</span>
                            <span class="adm-info-val">
                                <span class="adm-badge adm-badge-info">Enumerator</span>
                            </span>
                        </div>
                    </div>
                    <div class="adm-alert adm-alert-warning" style="margin-top:14px;">
                        <svg viewBox="0 0 24 24">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3
                                                         L13.71 3.86a2 2 0 0 0-3.42 0z" />
                            <line x1="12" y1="9" x2="12" y2="13" />
                            <line x1="12" y1="17" x2="12.01" y2="17" />
                        </svg>
                        <div>Pastikan nomor telepon sudah benar karena digunakan sebagai email login.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="adm-btn-primary" id="confirmGenerateUserBtn">
                        <svg viewBox="0 0 24 24">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="8.5" cy="7" r="4" />
                            <line x1="20" y1="8" x2="20" y2="14" />
                            <line x1="23" y1="11" x2="17" y2="11" />
                        </svg>
                        Generate User
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        /**
         * Enumerator Index Page Script
         * @version 1.2.0
         * @description Handles table search/filter, pagination, delete, generate user, export PDF modal
         */
        document.addEventListener('DOMContentLoaded', function() {
            'use strict';

            /* ── CONSTANTS ── */
            const API_BASE_URL = '/api/superadmin/enumerators';
            const EXPORT_URL = '{{ route('superadmin.enumerators.export-pdf') }}';
            const SEARCH_DELAY = 400; // ms debounce

            const NAMA_BULAN = [
                '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];

            /* ── DOM REFS ── */
            const searchInput = document.getElementById('search');
            const statusSelect = document.getElementById('status-1');
            const resetBtn = document.getElementById('resetBtn');
            const tableBody = document.getElementById('tableBody');
            const paginationWrapper = document.getElementById('paginationWrapper');
            const tableLoading = document.getElementById('tableLoading');
            const tableWrapper = document.getElementById('tableWrapper');

            // Export modal
            const exportBulan = document.getElementById('exportBulan');
            const exportTahun = document.getElementById('exportTahun');
            const exportPreview = document.getElementById('exportPreviewLabel');
            const exportConfirmBtn = document.getElementById('exportPdfConfirmBtn');
            const exportFilterInfo = document.getElementById('exportFilterInfo');

            // Delete modal
            const deleteModalEl = document.getElementById('deleteModal');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

            // Generate user modal
            const generateUserModalEl = document.getElementById('generateUserModal');
            const confirmGenerateUserBtn = document.getElementById('confirmGenerateUserBtn');

            /* ── GUARD: pastikan semua elemen ada ── */
            const requiredEls = {
                searchInput,
                statusSelect,
                resetBtn,
                tableBody,
                paginationWrapper,
                tableLoading,
                tableWrapper,
                exportBulan,
                exportTahun,
                exportPreview,
                exportConfirmBtn,
                exportFilterInfo,
                deleteModalEl,
                confirmDeleteBtn,
                generateUserModalEl,
                confirmGenerateUserBtn,
            };
            for (const [name, el] of Object.entries(requiredEls)) {
                if (!el) {
                    console.error(`[Enumerator] Element tidak ditemukan: #${name}`);
                }
            }

            /* ── BOOTSTRAP MODALS ── */
            const deleteModal = new bootstrap.Modal(deleteModalEl);
            const generateUserModal = new bootstrap.Modal(generateUserModalEl);

            /* ── STATE ── */
            let searchTimeout = null;
            let isLoading = false;
            let deleteEnumeratorId = null;
            let generateUserEnumeratorId = null;

            /* ────────────────────────────────────────
             * HELPERS
             * ──────────────────────────────────────── */
            function getCsrfToken() {
                return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ??
                    document.querySelector('input[name="_token"]')?.value ??
                    '';
            }

            function getFilterParams() {
                const p = new URLSearchParams();
                const s = searchInput?.value.trim();
                const st = statusSelect?.value.trim();
                if (s) p.append('search', s);
                if (st) p.append('status', st);
                return p;
            }

            function setTableLoading(state) {
                if (!tableWrapper || !tableLoading) return;
                tableLoading.style.display = state ? 'block' : 'none';
                tableWrapper.style.opacity = state ? '0.5' : '1';
                tableWrapper.style.pointerEvents = state ? 'none' : 'auto';
            }

            function showToast(message, type = 'info') {
                // Fallback ke alert jika tidak ada toast library
                alert(message);
            }

            /* ────────────────────────────────────────
             * TABLE: LOAD DATA
             * ──────────────────────────────────────── */
            function loadData(pageUrl = null) {
                if (isLoading) return;
                isLoading = true;
                setTableLoading(true);

                const params = getFilterParams();

                if (pageUrl) {
                    try {
                        const page = new URL(pageUrl, window.location.origin).searchParams.get('page');
                        if (page) params.append('page', page);
                    } catch (e) {
                        console.warn('[Enumerator] Invalid page URL:', pageUrl);
                    }
                }

                const fetchUrl = API_BASE_URL + (params.toString() ? '?' + params.toString() : '');

                fetch(fetchUrl, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    })
                    .then(res => {
                        if (!res.ok) throw new Error(`HTTP ${res.status}: ${res.statusText}`);
                        return res.json();
                    })
                    .then(data => {
                        if (!data.success) throw new Error(data.message || 'Gagal memuat data');
                        if (tableBody) tableBody.innerHTML = data.table ?? '';
                        if (paginationWrapper) paginationWrapper.innerHTML = data.pagination ?? '';
                        attachDeleteHandlers();
                        attachPaginationHandlers();
                        attachGenerateUserHandlers();
                    })
                    .catch(err => {
                        console.error('[Enumerator] loadData error:', err);
                        showToast('Terjadi kesalahan saat memuat data: ' + err.message, 'error');
                    })
                    .finally(() => {
                        setTableLoading(false);
                        isLoading = false;
                    });
            }

            /* ────────────────────────────────────────
             * FILTER & SEARCH
             * ──────────────────────────────────────── */
            searchInput?.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    loadData();
                    updateExportUrl();
                }, SEARCH_DELAY);
            });

            statusSelect?.addEventListener('change', function() {
                loadData();
                updateExportUrl();
            });

            resetBtn?.addEventListener('click', function() {
                if (searchInput) searchInput.value = '';
                if (statusSelect) statusSelect.value = '';
                loadData();
                setTimeout(updateExportUrl, 50);
            });

            /* ────────────────────────────────────────
             * EXPORT PDF MODAL
             * ──────────────────────────────────────── */
            let currentExportUrl = '';

            function updateExportUrl() {
                if (!exportBulan || !exportTahun || !exportPreview || !exportConfirmBtn) return;

                const bulan = exportBulan.value;
                const tahun = exportTahun.value;

                // Update preview label
                exportPreview.textContent = (NAMA_BULAN[parseInt(bulan)] ?? '—') + ' ' + tahun;

                // Build URL
                const params = new URLSearchParams();
                params.append('bulan', bulan);
                params.append('tahun', tahun);
                getFilterParams().forEach((val, key) => params.append(key, val));

                currentExportUrl = EXPORT_URL + '?' + params.toString();

                // Tampilkan warning filter aktif
                const hasFilter = searchInput?.value.trim() || statusSelect?.value.trim();
                if (exportFilterInfo) {
                    exportFilterInfo.style.display = hasFilter ? 'flex' : 'none';
                }
            }

            exportBulan?.addEventListener('change', updateExportUrl);
            exportTahun?.addEventListener('change', updateExportUrl);

            // Handler tombol Download PDF
            exportConfirmBtn?.addEventListener('click', function() {
                if (!currentExportUrl) {
                    showToast('URL export tidak valid, coba lagi.', 'error');
                    return;
                }

                // Tutup modal dulu, lalu buka tab baru
                const exportModalInstance = bootstrap.Modal.getInstance(
                    document.getElementById('exportPdfModal')
                );

                if (exportModalInstance) {
                    // Tunggu modal selesai menutup, baru buka tab
                    const modalEl = document.getElementById('exportPdfModal');
                    const onHidden = function() {
                        modalEl.removeEventListener('hidden.bs.modal', onHidden);
                        window.open(currentExportUrl, '_blank', 'noopener,noreferrer');
                    };
                    modalEl.addEventListener('hidden.bs.modal', onHidden);
                    exportModalInstance.hide();
                } else {
                    // Fallback jika modal instance tidak ditemukan
                    window.open(currentExportUrl, '_blank', 'noopener,noreferrer');
                }
            });

            // Init saat load
            updateExportUrl();
            /* ────────────────────────────────────────
             * DELETE HANDLER
             * ──────────────────────────────────────── */
            function attachDeleteHandlers() {
                document.querySelectorAll('.btn-delete').forEach(btn => {
                    const clone = btn.cloneNode(true);
                    btn.parentNode.replaceChild(clone, btn);
                    clone.addEventListener('click', function(e) {
                        e.preventDefault();
                        deleteEnumeratorId = this.dataset.id ?? null;
                        if (deleteEnumeratorId) deleteModal.show();
                    });
                });
            }

            confirmDeleteBtn?.addEventListener('click', function() {
                if (!deleteEnumeratorId) return;

                const originalHTML = confirmDeleteBtn.innerHTML;
                confirmDeleteBtn.disabled = true;
                confirmDeleteBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menghapus...';

                fetch(`${API_BASE_URL}/${deleteEnumeratorId}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    })
                    .then(res => {
                        if (!res.ok) throw new Error(`HTTP ${res.status}`);
                        return res.json();
                    })
                    .then(data => {
                        if (!data.success) throw new Error(data.message || 'Gagal menghapus data');
                        deleteModal.hide();
                        showToast(data.message || 'Data berhasil dihapus', 'success');
                        loadData();
                    })
                    .catch(err => {
                        console.error('[Enumerator] delete error:', err);
                        showToast('Terjadi kesalahan saat menghapus: ' + err.message, 'error');
                    })
                    .finally(() => {
                        confirmDeleteBtn.disabled = false;
                        confirmDeleteBtn.innerHTML = originalHTML;
                        deleteEnumeratorId = null;
                    });
            });

            /* ────────────────────────────────────────
             * PAGINATION HANDLER
             * ──────────────────────────────────────── */
            function attachPaginationHandlers() {
                paginationWrapper?.querySelectorAll('a.page-link').forEach(link => {
                    const clone = link.cloneNode(true);
                    link.parentNode.replaceChild(clone, link);
                    clone.addEventListener('click', function(e) {
                        e.preventDefault();
                        const url = this.getAttribute('href');
                        if (url && url !== '#') loadData(url);
                    });
                });
            }

            /* ────────────────────────────────────────
             * GENERATE USER HANDLER
             * ──────────────────────────────────────── */
            function attachGenerateUserHandlers() {
                document.querySelectorAll('.btn-generate-user').forEach(btn => {
                    const clone = btn.cloneNode(true);
                    btn.parentNode.replaceChild(clone, btn);
                    clone.addEventListener('click', function() {
                        generateUserEnumeratorId = this.dataset.id ?? null;
                        const nama = this.dataset.nama ?? '—';
                        const hp = this.dataset.hp ?? '';

                        const namaEl = document.getElementById('generateUserNama');
                        const emailEl = document.getElementById('generateUserEmail');
                        if (namaEl) namaEl.textContent = nama;
                        if (emailEl) emailEl.textContent = hp ? `${hp}@kawulohalal.id` : '—';

                        if (generateUserEnumeratorId) generateUserModal.show();
                    });
                });
            }

            confirmGenerateUserBtn?.addEventListener('click', function() {
                if (!generateUserEnumeratorId) return;

                const originalHTML = confirmGenerateUserBtn.innerHTML;
                confirmGenerateUserBtn.disabled = true;
                confirmGenerateUserBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';

                fetch(`${API_BASE_URL}/${generateUserEnumeratorId}/generate-user`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    })
                    .then(res => {
                        if (!res.ok) throw new Error(`HTTP ${res.status}`);
                        return res.json();
                    })
                    .then(data => {
                        generateUserModal.hide();
                        if (!data.success) throw new Error(data.message || 'Gagal generate user');
                        showToast(data.message || 'User berhasil digenerate', 'success');
                        loadData();
                    })
                    .catch(err => {
                        console.error('[Enumerator] generateUser error:', err);
                        generateUserModal.hide();
                        showToast('Terjadi kesalahan: ' + err.message, 'error');
                    })
                    .finally(() => {
                        confirmGenerateUserBtn.disabled = false;
                        confirmGenerateUserBtn.innerHTML = originalHTML;
                        generateUserEnumeratorId = null;
                    });
            });

            /* ────────────────────────────────────────
             * INIT
             * ──────────────────────────────────────── */
            attachDeleteHandlers();
            attachPaginationHandlers();
            attachGenerateUserHandlers();

            console.info('[Enumerator] Page script v1.2.0 loaded.');
        });
    </script>
@endsection
