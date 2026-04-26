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
        <a href="{{ route('superadmin.enumerators.create') }}" class="adm-btn-primary">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Enumerator
        </a>
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
                        <svg class="adm-search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text" id="search" name="search" class="adm-search-input" style="width:280px;"
                            placeholder="Cari nama enumerator atau koordinator..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="adm-filter-group">
                    <label class="adm-filter-label">Status</label>
                    <select id="status-1" name="status" class="adm-select">
                        <option value="">Semua Status</option>
                        <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Tidak Aktif" {{ request('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div style="display:flex;gap:6px;align-items:flex-end;">
                    <button type="button" id="resetBtn" class="adm-reset-btn">
                        <svg viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/></svg>
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
                            <th>No Regis</th>
                            <th>Koordinator</th>
                            <th>Nama Lengkap</th>
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

{{-- Include Delete Modal --}}
@include('superadmin.enumerator.partials.delete-modal')

{{-- Generate User Modal --}}
<div id="generateUserModal" class="modal fade adm-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generateUserModalLabel">
                    <svg viewBox="0 0 24 24" style="width:18px;height:18px;stroke:var(--adm-blue);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;margin-right:6px;vertical-align:-3px;">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/>
                        <line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/>
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
                        <span class="adm-info-val"><code style="background:var(--adm-blue-lt);color:var(--adm-blue);padding:2px 8px;border-radius:4px;font-size:12px;">enumkh123</code></span>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search');
        const statusSelect = document.getElementById('status-1');
        const resetBtn = document.getElementById('resetBtn');
        const tableBody = document.getElementById('tableBody');
        const paginationWrapper = document.getElementById('paginationWrapper');
        const tableLoading = document.getElementById('tableLoading');
        const tableWrapper = document.getElementById('tableWrapper');

        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        let deleteEnumeratorId = null;

        const generateUserModal = new bootstrap.Modal(document.getElementById('generateUserModal'));
        const confirmGenerateUserBtn = document.getElementById('confirmGenerateUserBtn');
        let generateUserEnumeratorId = null;

        const API_BASE_URL = '/api/superadmin/enumerators';
        let searchTimeout, isLoading = false;

        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                document.querySelector('input[name="_token"]')?.value;
        }

        function loadData(url = null) {
            if (isLoading) return;
            isLoading = true;
            tableWrapper.style.opacity = '0.5';
            tableWrapper.style.pointerEvents = 'none';
            tableLoading.style.display = 'block';

            const params = new URLSearchParams();
            if (searchInput.value.trim()) params.append('search', searchInput.value.trim());
            if (statusSelect.value.trim()) params.append('status', statusSelect.value.trim());
            if (url) {
                const page = new URL(url, window.location.origin).searchParams.get('page');
                if (page) params.append('page', page);
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
                        attachGenerateUserHandlers();
                    } else { alert(data.message || 'Terjadi kesalahan saat memuat data'); }
                })
                .catch(err => alert('Terjadi kesalahan: ' + err.message))
                .finally(() => {
                    tableLoading.style.display = 'none';
                    tableWrapper.style.opacity = '1';
                    tableWrapper.style.pointerEvents = 'auto';
                    isLoading = false;
                });
        }

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => loadData(), 500);
        });
        statusSelect.addEventListener('change', () => loadData());
        resetBtn.addEventListener('click', function() {
            searchInput.value = ''; statusSelect.value = ''; loadData();
        });

        function attachDeleteHandlers() {
            document.querySelectorAll('.btn-delete').forEach(btn => {
                const nb = btn.cloneNode(true);
                btn.parentNode.replaceChild(nb, btn);
                nb.addEventListener('click', function(e) {
                    e.preventDefault();
                    deleteEnumeratorId = this.dataset.id;
                    deleteModal.show();
                });
            });
        }

        confirmDeleteBtn.addEventListener('click', function() {
            if (!deleteEnumeratorId) return;
            confirmDeleteBtn.disabled = true;
            confirmDeleteBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Menghapus...';
            fetch(`${API_BASE_URL}/${deleteEnumeratorId}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) { deleteModal.hide(); alert(data.message || 'Data berhasil dihapus'); loadData(); }
                    else { alert(data.message || 'Gagal menghapus data'); }
                })
                .catch(() => alert('Terjadi kesalahan saat menghapus data'))
                .finally(() => {
                    confirmDeleteBtn.disabled = false;
                    confirmDeleteBtn.innerHTML = '<svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2;"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg> Hapus Data';
                    deleteEnumeratorId = null;
                });
        });

        function attachPaginationHandlers() {
            paginationWrapper.querySelectorAll('a.page-link').forEach(link => {
                const nl = link.cloneNode(true);
                link.parentNode.replaceChild(nl, link);
                nl.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');
                    if (url && url !== '#') loadData(url);
                });
            });
        }

        function attachGenerateUserHandlers() {
            document.querySelectorAll('.btn-generate-user').forEach(btn => {
                const nb = btn.cloneNode(true);
                btn.parentNode.replaceChild(nb, btn);
                nb.addEventListener('click', function() {
                    generateUserEnumeratorId = this.dataset.id;
                    document.getElementById('generateUserNama').textContent = this.dataset.nama;
                    document.getElementById('generateUserEmail').textContent = this.dataset.hp + '@kawulohalal.id';
                    generateUserModal.show();
                });
            });
        }

        confirmGenerateUserBtn.addEventListener('click', function() {
            if (!generateUserEnumeratorId) return;
            confirmGenerateUserBtn.disabled = true;
            confirmGenerateUserBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Memproses...';
            fetch(`${API_BASE_URL}/${generateUserEnumeratorId}/generate-user`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                })
                .then(r => r.json())
                .then(data => {
                    generateUserModal.hide();
                    if (data.success) { alert(data.message || 'User berhasil digenerate'); loadData(); }
                    else { alert(data.message || 'Gagal generate user'); }
                })
                .catch(() => alert('Terjadi kesalahan saat generate user'))
                .finally(() => {
                    confirmGenerateUserBtn.disabled = false;
                    confirmGenerateUserBtn.innerHTML = 'Generate User';
                    generateUserEnumeratorId = null;
                });
        });

        attachDeleteHandlers();
        attachPaginationHandlers();
        attachGenerateUserHandlers();
    });
</script>
@endsection
