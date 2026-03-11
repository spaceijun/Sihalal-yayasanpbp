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
                    </div>
                </div>

                <!-- Form Search -->
                <div class="card-body bg-white border-bottom">
                    <form id="searchForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="search" class="form-label">Cari Nama PU / Pendamping</label>
                                <input type="text" class="form-control" id="search" name="search"
                                    placeholder="Cari berdasarkan nama PU atau pendamping...">
                            </div>
                            <div class="col-md-3">
                                <label for="tanggal_dari" class="form-label">Tanggal Dari</label>
                                <input type="date" class="form-control" id="tanggal_dari" name="tanggal_dari">
                            </div>
                            <div class="col-md-3">
                                <label for="tanggal_sampai" class="form-label">Tanggal Sampai</label>
                                <input type="date" class="form-control" id="tanggal_sampai" name="tanggal_sampai">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body bg-white">
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
                                        <th>No</th>
                                        <th>Created</th>
                                        <th>Pendamping</th>
                                        <th>Nama PU</th>
                                        <th>NIK</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    {{-- Data will be loaded via AJAX --}}
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination wrapper --}}
                        <div id="paginationWrapper">
                            {{-- Pagination will be loaded via AJAX --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ================================
        // LOCK MECHANISM
        // ================================
        let currentLockId = null;
        let lockRenewer = null;
        const LOCK_URL = '/api/data-entry/data-lapangans';

        async function acquireLock(id) {
            const res = await fetch(`${LOCK_URL}/${id}/lock`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            });
            return await res.json();
        }

        async function releaseLock(id) {
            if (!id) return;
            await fetch(`${LOCK_URL}/${id}/lock`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            });
            currentLockId = null;
            clearInterval(lockRenewer);
        }

        // Intercept klik tombol Show
        document.addEventListener('click', async function(e) {
            const btn = e.target.closest('.btn-show-data');
            if (!btn) return;

            e.preventDefault();
            const id = btn.dataset.id;
            const href = btn.getAttribute('href');

            // Disable tombol sementara
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            const result = await acquireLock(id);

            if (!result.success) {
                btn.disabled = false;
                btn.innerHTML = '<i class="las la-eye"></i> Show';
                alert('Data tidak tersedia saat ini.');
                return;
            }

            currentLockId = id;

            // Auto-renew lock setiap 10 menit
            lockRenewer = setInterval(() => acquireLock(id), 10 * 60 * 1000);

            // Navigasi ke halaman show
            window.location.href = href;
        });

        // Release lock saat user kembali ke halaman ini (back button)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted || performance.navigation?.type === 2) {
                // User balik via back button — release lock jika ada
                if (currentLockId) {
                    releaseLock(currentLockId);
                }
                loadData(); // Refresh tabel
            }
        });

        // Release lock saat tab ditutup / refresh
        window.addEventListener('beforeunload', function() {
            if (currentLockId) {
                navigator.sendBeacon(`${LOCK_URL}/${currentLockId}/unlock-beacon`);
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            // Element references
            const searchForm = document.getElementById('searchForm');
            const searchInput = document.getElementById('search');
            const tanggalDariInput = document.getElementById('tanggal_dari');
            const tanggalSampaiInput = document.getElementById('tanggal_sampai');
            const tableBody = document.getElementById('tableBody');
            const paginationWrapper = document.getElementById('paginationWrapper');
            const tableLoading = document.getElementById('tableLoading');
            const tableWrapper = document.getElementById('tableWrapper');

            const API_BASE_URL = '/api/data-entry/data-lapangans';
            let searchTimeout;

            function getCsrfToken() {
                return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                    document.querySelector('input[name="_token"]')?.value;
            }

            function loadData(url = null) {
                // Show loading
                tableWrapper.style.display = 'none';
                tableLoading.style.display = 'block';

                // Disable form inputs
                const formInputs = searchForm.querySelectorAll('input, select');
                formInputs.forEach(input => input.disabled = true);

                // Build URL with filters
                let fetchUrl = API_BASE_URL;
                const params = new URLSearchParams();

                // Add filters
                if (searchInput.value.trim()) {
                    params.append('search', searchInput.value.trim());
                }
                if (tanggalDariInput.value.trim()) {
                    params.append('tanggal_dari', tanggalDariInput.value.trim());
                }
                if (tanggalSampaiInput.value.trim()) {
                    params.append('tanggal_sampai', tanggalSampaiInput.value.trim());
                }

                // Handle pagination
                if (url) {
                    const urlObj = new URL(url, window.location.origin);
                    const page = urlObj.searchParams.get('page');
                    if (page) {
                        params.append('page', page);
                    }
                }

                const queryString = params.toString();
                if (queryString) {
                    fetchUrl += '?' + queryString;
                }

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
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            tableBody.innerHTML = data.table;
                            paginationWrapper.innerHTML = data.pagination;
                            attachPaginationHandlers();
                        } else {
                            alert(data.message || 'Terjadi kesalahan saat memuat data');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading data:', error);
                        tableBody.innerHTML =
                            '<tr><td colspan="8" class="text-center text-danger">Terjadi kesalahan saat memuat data</td></tr>';
                    })
                    .finally(() => {
                        tableLoading.style.display = 'none';
                        tableWrapper.style.display = 'block';
                        formInputs.forEach(input => input.disabled = false);
                    });
            }

            // Instant search with debounce
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    loadData();
                }, 500);
            });

            tanggalDariInput.addEventListener('change', function() {
                loadData();
            });

            tanggalSampaiInput.addEventListener('change', function() {
                loadData();
            });

            function attachPaginationHandlers() {
                const paginationLinks = paginationWrapper.querySelectorAll('a.page-link');
                paginationLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const url = this.getAttribute('href');
                        if (url && url !== '#') {
                            loadData(url);
                        }
                    });
                });
            }

            // Initial load
            loadData();

            // Auto refresh
            window.addEventListener('pageshow', function(event) {
                if (event.persisted ||
                    (window.performance && window.performance.navigation.type === 2)) {
                    loadData();
                }
            });
        });
    </script>
@endsection
