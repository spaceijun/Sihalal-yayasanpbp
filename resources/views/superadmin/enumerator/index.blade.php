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

    {{-- Generate User Modal --}}
    <div id="generateUserModal" class="modal fade" tabindex="-1" aria-labelledby="generateUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="generateUserModalLabel">
                        <i class="las la-user-plus me-1"></i> Generate Akun User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Akun user akan dibuat dengan detail berikut:</p>
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th class="text-nowrap" width="35%">Nama</th>
                            <td id="generateUserNama">-</td>
                        </tr>
                        <tr>
                            <th class="text-nowrap">Email</th>
                            <td id="generateUserEmail">-</td>
                        </tr>
                        <tr>
                            <th class="text-nowrap">Password</th>
                            <td><code>enumkh123</code></td>
                        </tr>
                        <tr>
                            <th class="text-nowrap">Role</th>
                            <td><span class="badge bg-info">Enumerator</span></td>
                        </tr>
                    </table>
                    <div class="alert alert-warning mb-0 py-2">
                        <i class="las la-exclamation-triangle"></i>
                        Pastikan nomor telepon sudah benar karena digunakan sebagai email login.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="las la-times"></i> Batal
                    </button>
                    <button type="button" class="btn btn-warning" id="confirmGenerateUserBtn">
                        <i class="las la-user-plus"></i> Generate User
                    </button>
                </div>
            </div>
        </div>
    </div>

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

            // Generate User Modal
            const generateUserModal = new bootstrap.Modal(document.getElementById('generateUserModal'));
            const confirmGenerateUserBtn = document.getElementById('confirmGenerateUserBtn');
            let generateUserEnumeratorId = null;

            // API Base URL
            const API_BASE_URL = '/api/superadmin/enumerators';

            let searchTimeout;
            let isLoading = false;

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
                if (isLoading) {
                    console.log('Request already in progress, skipping...');
                    return;
                }

                isLoading = true;

                tableWrapper.style.opacity = '0.5';
                tableWrapper.style.pointerEvents = 'none';
                tableLoading.style.display = 'block';

                const formInputs = searchForm.querySelectorAll('input, select, button');
                formInputs.forEach(input => input.disabled = true);

                let fetchUrl = API_BASE_URL;
                const params = new URLSearchParams();

                if (searchInput.value.trim()) {
                    params.append('search', searchInput.value.trim());
                }

                if (statusSelect.value.trim()) {
                    params.append('status', statusSelect.value.trim());
                }

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

                            // Re-attach ALL event handlers ke elemen baru
                            attachDeleteHandlers();
                            attachPaginationHandlers();
                            attachGenerateUserHandlers();
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
                        tableWrapper.style.opacity = '1';
                        tableWrapper.style.pointerEvents = 'auto';
                        formInputs.forEach(input => input.disabled = false);
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
                }, 500);
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
                    const newButton = button.cloneNode(true);
                    button.parentNode.replaceChild(newButton, button);

                    newButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        deleteEnumeratorId = this.dataset.id;
                        deleteModal.show();
                    });
                });
            }

            /**
             * Handle confirm delete button click
             */
            confirmDeleteBtn.addEventListener('click', function() {
                if (!deleteEnumeratorId) return;

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
                            deleteModal.hide();
                            alert(data.message || 'Data berhasil dihapus');
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
             * Attach generate user handlers to all generate user buttons
             */
            function attachGenerateUserHandlers() {
                const generateButtons = document.querySelectorAll('.btn-generate-user');

                generateButtons.forEach(button => {
                    const newButton = button.cloneNode(true);
                    button.parentNode.replaceChild(newButton, button);

                    newButton.addEventListener('click', function() {
                        const enumeratorId = this.dataset.id;
                        const namaLengkap = this.dataset.nama;
                        const telephone = this.dataset.hp;

                        // Isi detail ke modal
                        generateUserEnumeratorId = enumeratorId;
                        document.getElementById('generateUserNama').textContent = namaLengkap;
                        document.getElementById('generateUserEmail').textContent = telephone +
                            '@kawulohalal.id';

                        // Tampilkan modal
                        generateUserModal.show();
                    });
                });
            }

            /**
             * Handle confirm generate user button click
             */
            confirmGenerateUserBtn.addEventListener('click', function() {
                if (!generateUserEnumeratorId) return;

                confirmGenerateUserBtn.disabled = true;
                confirmGenerateUserBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';

                fetch(`${API_BASE_URL}/${generateUserEnumeratorId}/generate-user`, {
                        method: 'POST',
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
                        generateUserModal.hide();
                        if (data.success) {
                            alert(data.message || 'User berhasil digenerate');
                            loadData();
                        } else {
                            alert(data.message || 'Gagal generate user');
                        }
                    })
                    .catch(error => {
                        console.error('Error generating user:', error);
                        alert('Terjadi kesalahan saat generate user');
                    })
                    .finally(() => {
                        confirmGenerateUserBtn.disabled = false;
                        confirmGenerateUserBtn.innerHTML =
                            '<i class="las la-user-plus"></i> Generate User';
                        generateUserEnumeratorId = null;
                    });
            });

            /**
             * Initial attachment of event handlers
             */
            attachDeleteHandlers();
            attachPaginationHandlers();
            attachGenerateUserHandlers();
        });
    </script>
@endsection
