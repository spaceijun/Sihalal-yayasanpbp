@extends('layouts.app')
@section('template_title')
    Recruitment
@endsection
@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Recruitment</h1>
                <p>Kelola data pelamar data entry dan pendamping lapangan</p>
            </div>
            <a href="{{ route('superadmin.recruitments.create') }}" class="adm-btn-primary">
                <svg viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Tambah Pelamar
            </a>
        </div>

        <div class="adm-card">
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
                                style="width:280px;" placeholder="Cari nama atau telephone..."
                                value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="adm-filter-group">
                        <label class="adm-filter-label">Status</label>
                        <select id="status-1" name="status" class="adm-select">
                            <option value="">Semua Status</option>
                            <option value="Melamar" {{ request('status') == 'Melamar' ? 'selected' : '' }}>Melamar</option>
                            <option value="Diterima" {{ request('status') == 'Diterima' ? 'selected' : '' }}>Diterima
                            </option>
                            <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
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

            <div id="tableLoading" style="display:none;text-align:center;padding:40px;">
                <div class="spinner-border text-primary" role="status" style="width:2.5rem;height:2.5rem;"></div>
                <p style="margin-top:12px;font-weight:600;color:var(--adm-text-muted);">Memuat data...</p>
            </div>

            <div id="tableWrapper">
                <div class="table-responsive">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th style="width:44px">#</th>
                                <th>Koordinator</th>
                                <th>Nama Lengkap</th>
                                <th>Telephone</th>
                                <th>Rekomendasi</th>
                                <th class="tc">Status</th>
                                <th class="tc" style="width:90px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @include('superadmin.recruitment.partials.table-body')
                        </tbody>
                    </table>
                </div>
                <div class="adm-card-footer">
                    <span class="adm-footer-info">
                        Menampilkan {{ $recruitments->firstItem() ?? 0 }}–{{ $recruitments->lastItem() ?? 0 }}
                        dari {{ $recruitments->total() }} pelamar
                    </span>
                    <div id="paginationWrapper">
                        @include('layouts.pagination', ['paginator' => $recruitments])
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('superadmin.recruitment.partials.delete-modal')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');
            const statusSelect = document.getElementById('status-1');
            const resetBtn = document.getElementById('resetBtn');
            const tableBody = document.getElementById('tableBody');
            const paginationWrapper = document.getElementById('paginationWrapper');
            const tableLoading = document.getElementById('tableLoading');
            const tableWrapper = document.getElementById('tableWrapper');
            const deleteModalEl = document.getElementById('deleteModal');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            let deleteModal = deleteModalEl && typeof bootstrap !== 'undefined' ? new bootstrap.Modal(
                deleteModalEl) : null;
            let deleteRecruitmentId = null;
            const API_BASE_URL = '/api/superadmin/recruitments';
            let searchTimeout, isLoading = false;

            function getCsrfToken() {
                return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                    document.querySelector('input[name="_token"]')?.value;
            }

            function loadData(url = null) {
                if (isLoading) return;
                isLoading = true;
                tableWrapper.style.display = 'none';
                tableLoading.style.display = 'block';
                const params = new URLSearchParams();
                if (searchInput.value.trim()) params.append('search', searchInput.value.trim());
                if (statusSelect.value.trim()) params.append('status', statusSelect.value.trim());
                if (url) {
                    try {
                        const page = new URL(url, window.location.origin).searchParams.get('page');
                        if (page) params.append('page', page);
                    } catch (e) {}
                }
                let fetchUrl = API_BASE_URL + (params.toString() ? '?' + params.toString() : '');
                fetch(fetchUrl, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    })
                    .then(r => {
                        if (!r.ok) throw new Error('HTTP ' + r.status);
                        return r.json();
                    })
                    .then(data => {
                        if (data.success) {
                            tableBody.innerHTML = data.table;
                            paginationWrapper.innerHTML = data.pagination;
                            attachDeleteHandlers();
                            attachPaginationHandlers();
                        } else {
                            alert(data.message || 'Terjadi kesalahan');
                        }
                    })
                    .catch(err => alert('Error: ' + err.message))
                    .finally(() => {
                        tableLoading.style.display = 'none';
                        tableWrapper.style.display = 'block';
                        isLoading = false;
                    });
            }

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => loadData(), 500);
            });
            statusSelect.addEventListener('change', () => loadData());
            resetBtn.addEventListener('click', function() {
                searchInput.value = '';
                statusSelect.value = '';
                loadData();
            });

            function attachDeleteHandlers() {
                document.querySelectorAll('.delete-btn').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        deleteRecruitmentId = this.dataset.id;
                        if (deleteModal) deleteModal.show();
                    });
                });
            }

            if (confirmDeleteBtn) {
                confirmDeleteBtn.addEventListener('click', function() {
                    if (!deleteRecruitmentId) return;
                    confirmDeleteBtn.disabled = true;
                    confirmDeleteBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm"></span> Menghapus...';
                    fetch(`${API_BASE_URL}/${deleteRecruitmentId}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': getCsrfToken(),
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                if (deleteModal) deleteModal.hide();
                                alert(data.message || 'Data berhasil dihapus');
                                loadData();
                            } else {
                                alert(data.message || 'Gagal menghapus data');
                            }
                        })
                        .catch(() => alert('Terjadi kesalahan saat menghapus data'))
                        .finally(() => {
                            confirmDeleteBtn.disabled = false;
                            confirmDeleteBtn.innerHTML = 'Hapus Data';
                            deleteRecruitmentId = null;
                        });
                });
            }

            function attachPaginationHandlers() {
                paginationWrapper.querySelectorAll('a.page-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const url = this.getAttribute('href');
                        if (url && url !== '#' && !this.parentElement.classList.contains(
                            'disabled')) loadData(url);
                    });
                });
            }

            attachDeleteHandlers();
            attachPaginationHandlers();

            window.addEventListener('pageshow', function(event) {
                if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                    isLoading = false;
                    loadData();
                }
            });
        });
    </script>
@endsection
