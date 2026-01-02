@extends('layouts.app')
@section('template_title')
    Data Lapangans
@endsection
@section('content')
    <style>
        .search-highlight {
            background-color: #fff3cd;
            font-weight: 500;
        }

        .no-data {
            padding: 3rem;
            text-align: center;
            color: #6c757d;
        }
    </style>
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                @include('layouts.messages')

                <!-- Filter Section -->
                <div class="card mb-3">
                    <div class="card-header">
                        <span>{{ __('Filter Data') }}</span>
                    </div>
                    <div class="card-body bg-white">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="searchNamaPU" class="form-label">Nama PU</label>
                                <input type="text" class="form-control" id="searchNamaPU"
                                    placeholder="Cari berdasarkan nama PU...">
                            </div>
                            <div class="col-md-4">
                                <label for="filterStatus" class="form-label">Status</label>
                                <select class="form-control" id="filterStatus">
                                    <option value="">Semua Status</option>
                                    <option value="PENDING">Pending</option>
                                    <option value="PROGRESS OSS">Progress OSS</option>
                                    <option value="PROGRESS SIHALAL">Progress SIHALAL</option>
                                    <option value="TERBIT SH">Terbit SH</option>
                                    <option value="DITOLAK">Ditolak</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="filterStatusPembayaran" class="form-label">Status Pembayaran</label>
                                <select class="form-control" id="filterStatusPembayaran">
                                    <option value="">Semua Status</option>
                                    <option value="PENDING">Pending</option>
                                    <option value="PENGAJUAN">Pengajuan</option>
                                    <option value="DIBAYAR">Dibayar</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col">
                                <button type="button" class="btn btn-secondary" id="resetBtn">
                                    <i class="las la-redo"></i> Reset
                                </button>
                                {{-- <span class="ms-3 text-muted" id="resultCount"></span> --}}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">
                                {{ __('Data Lapangans') }}
                            </span>
                            <div class="float-right">
                                {{-- <a href="{{ route('koordinator.data-lapangans.create') }}"
                                    class="btn btn-primary btn-sm float-right" data-placement="left">
                                    {{ __('Create New') }}
                                </a> --}}
                            </div>
                        </div>
                    </div>
                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="dataTable">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>
                                        <th>Pendamping</th>
                                        <th>Nama PU</th>
                                        <th>NIK</th>
                                        <th>Status</th>
                                        <th>Status Pembayaran</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <!-- Data akan diisi oleh JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div id="paginationInfo"></div>
                            <nav>
                                <ul class="pagination mb-0" id="pagination"></ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Data dari Laravel (inject dari controller)
        const allData = @json($dataLapangans->items());

        let currentPage = 1;
        const itemsPerPage = 10;
        let filteredData = [...allData];

        // Fungsi untuk mendapatkan badge status
        function getStatusBadge(status) {
            const badges = {
                'PENDING': '<span class="badge bg-warning text-dark">PENDING</span>',
                'PROGRESS OSS': '<span class="badge bg-info">PROGRESS OSS</span>',
                'PROGRESS SIHALAL': '<span class="badge bg-primary">PROGRESS SIHALAL</span>',
                'TERBIT SH': '<span class="badge bg-success">TERBIT SH</span>',
                'DITOLAK': '<span class="badge bg-danger">DITOLAK</span>'
            };
            return badges[status] || status;
        }

        // Fungsi untuk mendapatkan badge status pembayaran
        function getStatusPembayaranBadge(status) {
            const badges = {
                'PENDING': '<span class="badge bg-warning text-dark">PENDING</span>',
                'PENGAJUAN': '<span class="badge bg-info">PENGAJUAN</span>',
                'DIBAYAR': '<span class="badge bg-success">DIBAYAR</span>'
            };
            return badges[status] || status;
        }

        // Fungsi untuk render table
        function renderTable() {
            const tbody = document.getElementById('tableBody');
            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const pageData = filteredData.slice(start, end);

            if (pageData.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-muted">
                                <i class="las la-inbox la-3x mb-2"></i>
                                <p class="mb-0">{{ __('No data available') }}</p>
                            </div>
                        </td>
                    </tr>
                `;
                document.getElementById('resultCount').textContent = '';
                document.getElementById('paginationInfo').textContent = '';
                return;
            }

            tbody.innerHTML = pageData.map((item, index) => {
                const showUrl = "{{ route('koordinator.data-lapangan.show', ':id') }}".replace(':id', item.id);
                return `
                <tr>
                    <td>${start + index + 1}</td>
                    <td>${item.enumerator.nama_lengkap}</td>
                    <td>${item.nama_pu}</td>
                    <td>${item.nik}</td>
                    <td>${getStatusBadge(item.status)}</td>
                    <td>${getStatusPembayaranBadge(item.status_pembayaran)}</td>
                    <td>
                        <a class="btn btn-sm btn-primary" href="${showUrl}">
                            <i class="las la-eye"></i> {{ __('Show') }}
                        </a>
                    </td>
                </tr>
            `;
            }).join('');

            document.getElementById('resultCount').textContent =
                `Menampilkan ${start + 1} - ${Math.min(end, filteredData.length)} dari ${filteredData.length} data`;

            document.getElementById('paginationInfo').innerHTML = `
                <div class="dataTables_info">
                    Showing ${start + 1} to ${Math.min(end, filteredData.length)} of ${filteredData.length} entries
                </div>
            `;

            renderPagination();
        }

        // Fungsi untuk render pagination
        function renderPagination() {
            const totalPages = Math.ceil(filteredData.length / itemsPerPage);
            const pagination = document.getElementById('pagination');

            if (totalPages <= 1) {
                pagination.innerHTML = '';
                return;
            }

            let html = '';

            // Previous button
            html += `
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
                </li>
            `;

            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                    html += `
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>
                    `;
                } else if (i === currentPage - 2 || i === currentPage + 2) {
                    html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
            }

            // Next button
            html += `
                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
                </li>
            `;

            pagination.innerHTML = html;
        }

        // Fungsi filter data
        function filterData() {
            const namaPU = document.getElementById('searchNamaPU').value.toLowerCase();
            const status = document.getElementById('filterStatus').value;
            const statusPembayaran = document.getElementById('filterStatusPembayaran').value;

            filteredData = allData.filter(item => {
                const matchNamaPU = item.nama_pu.toLowerCase().includes(namaPU);
                const matchStatus = !status || item.status === status;
                const matchStatusPembayaran = !statusPembayaran || item.status_pembayaran === statusPembayaran;

                return matchNamaPU && matchStatus && matchStatusPembayaran;
            });

            currentPage = 1;
            renderTable();
        }

        // Event listeners
        document.getElementById('searchNamaPU').addEventListener('input', filterData);
        document.getElementById('filterStatus').addEventListener('change', filterData);
        document.getElementById('filterStatusPembayaran').addEventListener('change', filterData);

        document.getElementById('resetBtn').addEventListener('click', () => {
            document.getElementById('searchNamaPU').value = '';
            document.getElementById('filterStatus').value = '';
            document.getElementById('filterStatusPembayaran').value = '';
            filterData();
        });

        document.getElementById('pagination').addEventListener('click', (e) => {
            e.preventDefault();
            if (e.target.tagName === 'A' && !e.target.parentElement.classList.contains('disabled')) {
                currentPage = parseInt(e.target.dataset.page);
                renderTable();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        });

        // Initial render
        renderTable();
    </script>
@endsection
