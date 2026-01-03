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
                            @include('layouts.pagination', ['paginator' => $dataLapangans])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Data dari Laravel (inject dari controller)
        const allData = @json($dataLapangans->items());
        const paginationData = {
            total: {{ $dataLapangans->total() }},
            perPage: {{ $dataLapangans->perPage() }},
            currentPage: {{ $dataLapangans->currentPage() }},
            lastPage: {{ $dataLapangans->lastPage() }}
        };

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

            if (allData.length === 0) {
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
                document.getElementById('paginationInfo').textContent = '';
                return;
            }

            const startNumber = (paginationData.currentPage - 1) * paginationData.perPage;

            tbody.innerHTML = allData.map((item, index) => {
                const showUrl = "{{ url('koordinator/data-lapangan') }}/" + item.id;
                return `
            <tr>
                <td>${startNumber + index + 1}</td>
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

            const start = startNumber + 1;
            const end = Math.min(startNumber + allData.length, paginationData.total);

            document.getElementById('paginationInfo').innerHTML = `
            <div class="dataTables_info">
                Showing ${start} to ${end} of ${paginationData.total} entries
            </div>
        `;
        }

        // Fungsi filter data - redirect dengan query parameters
        function filterData() {
            const namaPU = document.getElementById('searchNamaPU').value;
            const status = document.getElementById('filterStatus').value;
            const statusPembayaran = document.getElementById('filterStatusPembayaran').value;

            const params = new URLSearchParams(window.location.search);

            if (namaPU) {
                params.set('nama_pu', namaPU);
            } else {
                params.delete('nama_pu');
            }

            if (status) {
                params.set('status', status);
            } else {
                params.delete('status');
            }

            if (statusPembayaran) {
                params.set('status_pembayaran', statusPembayaran);
            } else {
                params.delete('status_pembayaran');
            }

            // Reset ke halaman 1 saat filter
            params.delete('page');

            const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
            window.location.href = newUrl;
        }

        // Set nilai filter dari URL saat halaman load
        function setFilterFromUrl() {
            const params = new URLSearchParams(window.location.search);

            const namaPU = params.get('nama_pu');
            const status = params.get('status');
            const statusPembayaran = params.get('status_pembayaran');

            if (namaPU) document.getElementById('searchNamaPU').value = namaPU;
            if (status) document.getElementById('filterStatus').value = status;
            if (statusPembayaran) document.getElementById('filterStatusPembayaran').value = statusPembayaran;
        }

        // Event listeners
        document.getElementById('searchNamaPU').addEventListener('input', debounce(filterData, 500));
        document.getElementById('filterStatus').addEventListener('change', filterData);
        document.getElementById('filterStatusPembayaran').addEventListener('change', filterData);

        document.getElementById('resetBtn').addEventListener('click', () => {
            window.location.href = window.location.pathname;
        });

        // Debounce function untuk search
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Initial render
        setFilterFromUrl();
        renderTable();
    </script>
@endsection
