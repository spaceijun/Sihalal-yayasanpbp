@extends('layouts.app')

@section('template_title')
    Enumerators
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
                                {{ __('Enumerators') }}
                            </span>

                            <div class="float-right">
                                <a href="{{ route('superadmin.enumerators.create') }}"
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
                                <div class="col-md-5">
                                    <label for="search" class="form-label">Cari Nama Enumerator / Koordinator</label>
                                    <input type="text" class="form-control" id="search" name="search"
                                        placeholder="Cari berdasarkan nama enumerator atau koordinator..."
                                        value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="status-1" class="form-label">Status</label>
                                    <select class="form-control" id="status-1" name="status">
                                        <option value="">Semua Status</option>
                                        <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>
                                            Aktif</option>
                                        <option value="Tidak Aktif"
                                            {{ request('status') == 'Tidak Aktif' ? 'selected' : '' }}>
                                            Tidak Aktif</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
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
                                            <th>No Reg</th>
                                            <th>Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        {{-- Initial load dari server --}}
                                        @include('superadmin.enumerator.partials.table-body')
                                    </tbody>
                                </table>
                            </div>

                            {{-- Pagination wrapper --}}
                            <div id="paginationWrapper">
                                @include('layouts.pagination', ['paginator' => $enumerators])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Include Delete Modal --}}
    @include('superadmin.enumerator.partials.delete-modal')

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

            // Modal elements
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            let deleteEnumeratorId = null;

            // API Base URL
            const API_BASE_URL = '/api/superadmin/enumerators';

            let searchTimeout;
            let isLoading = false; // Flag to prevent multiple simultaneous requests

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
                    console.log('Request already in progress, skipping...');
                    return;
                }

                isLoading = true;

                // Show loading state - HIDE table content and SHOW loading
                tableWrapper.style.opacity = '0.5';
                tableWrapper.style.pointerEvents = 'none';
                tableLoading.style.display = 'block';

                // Disable form inputs during loading
                const formInputs = searchForm.querySelectorAll('input, select, button');
                formInputs.forEach(input => input.disabled = true);

                // Prepare fetch URL with parameters
                let fetchUrl = API_BASE_URL;
                const params = new URLSearchParams();

                // Add search parameters
                if (searchInput.value.trim()) {
                    params.append('search', searchInput.value.trim());
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

                console.log('Fetching URL:', fetchUrl);

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
                            alert(data.message || 'Terjadi kesalahan saat memuat data');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading data:', error);
                        alert('Terjadi kesalahan saat memuat data: ' + error.message);
                    })
                    .finally(() => {
                        // Hide loading state - SHOW table and HIDE loading
                        tableLoading.style.display = 'none';
                        tableWrapper.style.opacity = '1';
                        tableWrapper.style.pointerEvents = 'auto';

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
             * Reset filter button
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
                const deleteButtons = document.querySelectorAll('.btn-delete');

                deleteButtons.forEach(button => {
                    // Remove old event listeners by cloning
                    const newButton = button.cloneNode(true);
                    button.parentNode.replaceChild(newButton, button);

                    newButton.addEventListener('click', function(e) {
                        e.preventDefault();

                        // Store the enumerator ID
                        deleteEnumeratorId = this.dataset.id;

                        // Show the modal
                        deleteModal.show();
                    });
                });
            }

            /**
             * Handle confirm delete button click
             */
            confirmDeleteBtn.addEventListener('click', function() {
                if (!deleteEnumeratorId) return;

                // Disable button during deletion
                confirmDeleteBtn.disabled = true;
                confirmDeleteBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menghapus...';

                fetch(`${API_BASE_URL}/${deleteEnumeratorId}`, {
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
                            deleteModal.hide();

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
                        deleteEnumeratorId = null;
                    });
            });

            /**
             * Attach pagination handlers to all pagination links
             */
            function attachPaginationHandlers() {
                const paginationLinks = paginationWrapper.querySelectorAll('a.page-link');

                paginationLinks.forEach(link => {
                    // Remove old event listeners by cloning
                    const newLink = link.cloneNode(true);
                    link.parentNode.replaceChild(newLink, link);

                    newLink.addEventListener('click', function(e) {
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
                    // Reset loading flag in case it was stuck
                    isLoading = false;
                    loadData();
                }
            });
        });
    </script>
@endsection
