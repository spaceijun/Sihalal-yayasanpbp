@extends('layouts.app')

@section('template_title')
    Recruitments
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                @include('layouts.messages')
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">
                                {{ __('Recruitments') }}
                            </span>

                            <div class="float-right">
                                <a href="{{ route('superadmin.recruitments.create') }}"
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
                                    <label for="search" class="form-label">Cari Nama / Telephone</label>
                                    <input type="text" class="form-control" id="search" name="search"
                                        placeholder="Cari berdasarkan nama atau telephone..."
                                        value="{{ request('search') }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="status-1" class="form-label">Status</label>
                                    <select class="form-control" id="status-1" name="status">
                                        <option value="">Semua Status</option>
                                        <option value="Melamar" {{ request('status') == 'Melamar' ? 'selected' : '' }}>
                                            Melamar</option>
                                        <option value="Diterima" {{ request('status') == 'Diterima' ? 'selected' : '' }}>
                                            Diterima</option>
                                        <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>
                                            Ditolak</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" id="resetBtn" class="btn btn-secondary w-100">
                                        <i class="las la-redo-alt"></i> Reset Filter
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
                                            <th>Koordinator</th>
                                            <th>Nama Lengkap</th>
                                            <th>Telephone</th>
                                            <th>Rekomendasi</th>
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        {{-- Initial load dari server --}}
                                        @include('superadmin.recruitment.partials.table-body')
                                    </tbody>
                                </table>
                            </div>

                            {{-- Pagination wrapper --}}
                            <div id="paginationWrapper">
                                @include('layouts.pagination', ['paginator' => $recruitments])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Include Delete Modal --}}
    @include('superadmin.recruitment.partials.delete-modal')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Element references
            const searchForm = document.getElementById('searchForm');
            const searchInput = document.getElementById('search');
            const statusSelect = document.getElementById('status-1');
            const resetBtn = document.getElementById('resetBtn');
            const tableBody = document.getElementById('tableBody');
            const paginationWrapper = document.getElementById('paginationWrapper');
            const tableLoading = document.getElementById('tableLoading');
            const tableWrapper = document.getElementById('tableWrapper');

            // Check if all elements exist
            if (!searchInput || !statusSelect || !tableBody || !paginationWrapper) {
                console.error('Required elements not found!');
                return;
            }

            // Modal elements
            const deleteModalEl = document.getElementById('deleteModal');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            let deleteModal = null;
            let deleteRecruitmentId = null;

            // Initialize modal only if element exists
            if (deleteModalEl && typeof bootstrap !== 'undefined') {
                deleteModal = new bootstrap.Modal(deleteModalEl);
            }

            // API Base URL
            const API_BASE_URL = '/api/superadmin/recruitments';

            let searchTimeout;
            let isLoading = false; // Prevent multiple simultaneous requests

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
                // Prevent multiple simultaneous requests
                if (isLoading) {
                    console.log('Already loading, skipping...');
                    return;
                }

                isLoading = true;

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
                const searchValue = searchInput.value.trim();
                if (searchValue) {
                    params.append('search', searchValue);
                }

                const statusValue = statusSelect.value.trim();
                if (statusValue) {
                    params.append('status', statusValue);
                }

                // Handle pagination
                if (url) {
                    try {
                        // Extract page parameter from pagination URL
                        const urlObj = new URL(url, window.location.origin);
                        const page = urlObj.searchParams.get('page');
                        if (page) {
                            params.append('page', page);
                        }
                    } catch (e) {
                        console.error('Error parsing URL:', e);
                    }
                }

                // Build final URL
                const queryString = params.toString();
                if (queryString) {
                    fetchUrl += '?' + queryString;
                }

                console.log('Fetching:', fetchUrl);

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
                        console.log('Data received:', data);

                        if (data.success) {
                            // Update table body with new HTML
                            tableBody.innerHTML = data.table;

                            // Update pagination with new HTML
                            paginationWrapper.innerHTML = data.pagination;

                            // Re-attach event handlers to new elements
                            attachDeleteHandlers();
                            attachPaginationHandlers();
                        } else {
                            console.error('API returned error:', data.message);
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

                        // Reset loading flag
                        isLoading = false;
                    });
            }

            /**
             * Instant search with debounce on search input
             */
            searchInput.addEventListener('input', function() {
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
             * Reset filters
             */
            resetBtn.addEventListener('click', function() {
                searchInput.value = '';
                statusSelect.value = '';
                loadData();
            });

            /**
             * Attach delete handlers to all delete buttons
             */
            function attachDeleteHandlers() {
                const deleteButtons = document.querySelectorAll('.delete-btn');

                deleteButtons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();

                        // Store the recruitment ID
                        deleteRecruitmentId = this.dataset.id;

                        // Show the modal
                        if (deleteModal) {
                            deleteModal.show();
                        }
                    });
                });
            }

            /**
             * Handle confirm delete button click
             */
            if (confirmDeleteBtn) {
                confirmDeleteBtn.addEventListener('click', function() {
                    if (!deleteRecruitmentId) return;

                    // Disable button during deletion
                    confirmDeleteBtn.disabled = true;
                    confirmDeleteBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menghapus...';

                    fetch(`${API_BASE_URL}/${deleteRecruitmentId}`, {
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
                                // Hide modal
                                if (deleteModal) {
                                    deleteModal.hide();
                                }

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
                        })
                        .finally(() => {
                            // Reset button state
                            confirmDeleteBtn.disabled = false;
                            confirmDeleteBtn.innerHTML = '<i class="las la-trash"></i> Hapus';
                            deleteRecruitmentId = null;
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
                        if (url && url !== '#' && !this.parentElement.classList.contains(
                            'disabled')) {
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
                    // Reset loading flag in case it was stuck
                    isLoading = false;
                    loadData();
                }
            });
        });
    </script>
@endsection
