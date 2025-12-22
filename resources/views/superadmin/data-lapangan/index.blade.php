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
                            <a href="{{ route('superadmin.data-lapangans.create') }}"
                                class="btn btn-primary btn-sm float-right" data-placement="left">
                                {{ __('Create New') }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Form Search -->
                <div class="card-body bg-white border-bottom">
                    <form id="searchForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="nama_pu" class="form-label">Nama PU</label>
                                <input type="text" class="form-control" id="nama_pu" name="nama_pu"
                                    placeholder="Cari berdasarkan nama PU..." value="{{ request('nama_pu') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="status-1" class="form-label">Status</label>
                                <select class="form-control" id="status-1" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>
                                        Pending</option>
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
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="las la-search"></i> Cari
                                </button>
                                <button type="button" class="btn btn-secondary" id="btnReset">
                                    <i class="las la-redo"></i> Reset
                                </button>
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
                                        <th>Tanggal Input</th>
                                        <th>Pendamping</th>
                                        <th>Nama PU</th>
                                        <th>NIK</th>
                                        <th>Status</th>
                                        <th>Status Pembayaran</th>
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
            // Element references
            const searchForm = document.getElementById('searchForm');
            const namaPuInput = document.getElementById('nama_pu');
            const statusSelect = document.getElementById('status-1');
            const tableBody = document.getElementById('tableBody');
            const paginationWrapper = document.getElementById('paginationWrapper');
            const tableLoading = document.getElementById('tableLoading');
            const tableWrapper = document.getElementById('tableWrapper');
            const btnReset = document.getElementById('btnReset');

            // API Base URL
            const API_BASE_URL = '/api/superadmin/data-lapangans';

            let searchTimeout;

            /**
             * Get CSRF token from meta tag or form input
             */
            function getCsrfToken() {
                return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                    document.querySelector('input[name="_token"]')?.value;
            }

            /**
             * Main function to load data via AJAX
             */
            function loadData(url = null) {
                // Show loading state - HIDE TABLE AND SHOW LOADING
                tableWrapper.style.display = 'none';
                tableLoading.style.display = 'block';

                // Disable form inputs during loading
                const formInputs = searchForm.querySelectorAll('input, select, button');
                formInputs.forEach(input => input.disabled = true);

                // Prepare fetch URL with parameters
                let fetchUrl = API_BASE_URL;
                const params = new URLSearchParams();

                // Add search parameters
                if (namaPuInput.value.trim()) {
                    params.append('nama_pu', namaPuInput.value.trim());
                }

                if (statusSelect.value.trim()) {
                    params.append('status', statusSelect.value.trim());
                }

                // Handle pagination
                if (url) {
                    // Extract page parameter from pagination URL
                    const urlObj = new URL(url, window.location.origin);
                    const page = urlObj.searchParams.get('page');
                    if (page) {
                        params.append('page', page);
                    }
                }

                // Build final URL
                const queryString = params.toString();
                if (queryString) {
                    fetchUrl += '?' + queryString;
                }

                // Fetch data from API
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
                            // Update table body with new HTML
                            tableBody.innerHTML = data.table;

                            // Update pagination with new HTML
                            paginationWrapper.innerHTML = data.pagination;

                            // Re-attach event handlers to new elements
                            attachDeleteHandlers();
                            attachPaginationHandlers();
                        } else {
                            alert(data.message || 'Terjadi kesalahan saat memuat data');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading data:', error);
                        alert('Terjadi kesalahan saat memuat data: ' + error.message);
                    })
                    .finally(() => {
                        // Hide loading state - SHOW TABLE AND HIDE LOADING
                        tableLoading.style.display = 'none';
                        tableWrapper.style.display = 'block';

                        // Enable form inputs
                        formInputs.forEach(input => input.disabled = false);
                    });
            }

            /**
             * Handle form submit for search
             */
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                loadData();
            });

            /**
             * Handle reset button click
             */
            btnReset.addEventListener('click', function() {
                namaPuInput.value = '';
                statusSelect.value = '';
                loadData();
            });

            /**
             * Instant search with debounce on nama_pu input
             */
            namaPuInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    loadData();
                }, 500); // Wait 500ms after user stops typing
            });

            /**
             * Instant search on status change
             */
            statusSelect.addEventListener('change', function() {
                loadData();
            });

            /**
             * Attach delete handlers to all delete forms
             */
            function attachDeleteHandlers() {
                const deleteForms = document.querySelectorAll('.delete-form');

                deleteForms.forEach(form => {
                    // Remove old event listeners by cloning
                    const newForm = form.cloneNode(true);
                    form.parentNode.replaceChild(newForm, form);

                    newForm.addEventListener('submit', function(e) {
                        e.preventDefault();

                        if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                            return false;
                        }

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
                                    // Show success message
                                    alert(data.message || 'Data berhasil dihapus');

                                    // Reload data after successful delete
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

            /**
             * Attach pagination handlers to all pagination links
             */
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

            /**
             * Initial attachment of event handlers
             */
            attachDeleteHandlers();
            attachPaginationHandlers();

            /**
             * Auto refresh when user navigates back to this page
             */
            window.addEventListener('pageshow', function(event) {
                if (event.persisted ||
                    (window.performance && window.performance.navigation.type === 2)) {
                    loadData();
                }
            });
        });
    </script>
@endsection
