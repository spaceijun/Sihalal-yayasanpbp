@extends('layouts.app')

@section('template_title')
    Progress Data Entry
@endsection

@section('content')
    <div class="row">
        <div class="col">
            @include('layouts.messages')
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span id="card_title">
                            {{ __('Progress Data Entry') }}
                        </span>
                    </div>
                </div>

                <!-- Form Search -->
                <div class="card-body bg-white border-bottom">
                    <form id="searchForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Cari Nama PU / NIK</label>
                                <input type="text" class="form-control" id="search" name="search"
                                    placeholder="Cari berdasarkan nama PU atau NIK...">
                            </div>
                            <div class="col-md-2">
                                <label for="action" class="form-label">Aksi</label>
                                <select class="form-select" id="action" name="action">
                                    <option value="">Semua Aksi</option>
                                    <option value="create">Create</option>
                                    <option value="update">Update</option>
                                    <option value="delete">Delete</option>
                                </select>
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
                                        <th>Waktu Aksi</th>
                                        <th>Nama PU</th>
                                        <th>NIK</th>
                                        <th>Aksi</th>
                                        <th>Status</th>
                                        <th>Detail</th>
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
        document.addEventListener('DOMContentLoaded', function() {
            const searchForm = document.getElementById('searchForm');
            const searchInput = document.getElementById('search');
            const actionInput = document.getElementById('action');
            const tanggalDariInput = document.getElementById('tanggal_dari');
            const tanggalSampaiInput = document.getElementById('tanggal_sampai');
            const tableBody = document.getElementById('tableBody');
            const paginationWrapper = document.getElementById('paginationWrapper');
            const tableLoading = document.getElementById('tableLoading');
            const tableWrapper = document.getElementById('tableWrapper');

            const API_BASE_URL = '/api/data-entry/progress';
            let searchTimeout;

            function getCsrfToken() {
                return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                    document.querySelector('input[name="_token"]')?.value;
            }

            function loadData(url = null) {
                tableWrapper.style.display = 'none';
                tableLoading.style.display = 'block';

                const formInputs = searchForm.querySelectorAll('input, select');
                formInputs.forEach(input => input.disabled = true);

                const params = new URLSearchParams();

                if (searchInput.value.trim()) params.append('search', searchInput.value.trim());
                if (actionInput.value.trim()) params.append('action', actionInput.value.trim());
                if (tanggalDariInput.value.trim()) params.append('tanggal_dari', tanggalDariInput.value.trim());
                if (tanggalSampaiInput.value.trim()) params.append('tanggal_sampai', tanggalSampaiInput.value
                    .trim());

                if (url) {
                    const urlObj = new URL(url, window.location.origin);
                    const page = urlObj.searchParams.get('page');
                    if (page) params.append('page', page);
                }

                let fetchUrl = API_BASE_URL;
                const queryString = params.toString();
                if (queryString) fetchUrl += '?' + queryString;

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
                            attachPaginationHandlers();
                        } else {
                            alert(data.message || 'Terjadi kesalahan saat memuat data');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading data:', error);
                        tableBody.innerHTML =
                            '<tr><td colspan="7" class="text-center text-danger">Terjadi kesalahan saat memuat data</td></tr>';
                    })
                    .finally(() => {
                        tableLoading.style.display = 'none';
                        tableWrapper.style.display = 'block';
                        formInputs.forEach(input => input.disabled = false);
                    });
            }

            // Event listeners
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => loadData(), 500);
            });

            actionInput.addEventListener('change', () => loadData());
            tanggalDariInput.addEventListener('change', () => loadData());
            tanggalSampaiInput.addEventListener('change', () => loadData());

            function attachPaginationHandlers() {
                paginationWrapper.querySelectorAll('a.page-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const url = this.getAttribute('href');
                        if (url && url !== '#') loadData(url);
                    });
                });
            }

            // Initial load
            loadData();

            window.addEventListener('pageshow', function(event) {
                if (event.persisted ||
                    (window.performance && window.performance.navigation.type === 2)) {
                    loadData();
                }
            });
        });
    </script>
@endsection
