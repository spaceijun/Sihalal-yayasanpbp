@extends('layouts.app')

@section('template_title')
    Tickets
@endsection

@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        {{-- PAGE HEADER --}}
        <div class="adm-header">
            <div class="adm-header-left">
                <h1>
                    <svg viewBox="0 0 24 24"
                        style="display:inline-block;width:20px;height:20px;stroke:var(--adm-blue);fill:none;stroke-width:2;vertical-align:-3px;margin-right:6px;">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" />
                        <line x1="7" y1="7" x2="7.01" y2="7" />
                    </svg>
                    Manajemen Tiket
                </h1>
                <p>Kelola semua tiket dukungan pengguna</p>
            </div>
        </div>

        {{-- STAT CARDS --}}
        <div class="adm-stats" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px;">
            <div class="adm-stat is-accent">
                <div class="adm-stat-label">Total Tiket</div>
                <div class="adm-stat-value">{{ $counts['all'] }}</div>
                <div class="adm-stat-sub">Semua tiket masuk</div>
            </div>
            <div class="adm-stat">
                <div class="adm-stat-label">Open</div>
                <div class="adm-stat-value is-success">{{ $counts['open'] }}</div>
                <div class="adm-stat-sub">Menunggu tindakan</div>
            </div>
            <div class="adm-stat">
                <div class="adm-stat-label">In Progress</div>
                <div class="adm-stat-value is-warn">{{ $counts['in_progress'] }}</div>
                <div class="adm-stat-sub">Sedang diproses</div>
            </div>
            <div class="adm-stat">
                <div class="adm-stat-label">Solved</div>
                <div class="adm-stat-value" style="color:var(--adm-text-muted);">{{ $counts['closed'] }}</div>
                <div class="adm-stat-sub">Telah diselesaikan</div>
            </div>
        </div>

        {{-- MAIN TABLE CARD --}}
        <div class="adm-card">
            <div class="adm-card-header">
                <div class="adm-card-title">
                    <svg viewBox="0 0 24 24">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" />
                        <line x1="7" y1="7" x2="7.01" y2="7" />
                    </svg>
                    Data Tiket
                </div>
            </div>

            {{-- FILTER BAR --}}
            <div class="adm-filter-bar">
                <div class="adm-filter-group">
                    <span class="adm-filter-label">Status</span>
                    <select class="adm-select" id="statusFilter" style="width:160px;">
                        <option value="">Semua Status</option>
                        <option value="open">Open</option>
                        <option value="in_progress">In Progress</option>
                        <option value="closed">Solved</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table id="ticketsTable" class="adm-table w-100">
                    <thead>
                        <tr>
                            <th style="width:44px">#</th>
                            <th style="width:160px;">No. Tiket</th>
                            <th style="width:180px;">User</th>
                            <th>Subjek</th>
                            <th class="tc" style="width:120px;">Status</th>
                            <th class="tc" style="width:100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.dataTableInstance = $('#ticketsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route($routePrefix . '.tickets.data') }}',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(d) {
                        d.status = $('#statusFilter').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'tc'
                    },
                    {
                        data: 'no_ticket_cell',
                        name: 'no_ticket'
                    },
                    {
                        data: 'user_cell',
                        name: 'user.name'
                    },
                    {
                        data: 'subject_cell',
                        name: 'subject'
                    },
                    {
                        data: 'status_badge',
                        name: 'status',
                        className: 'tc'
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false,
                        className: 'tc'
                    },
                ],
                language: {
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ – _END_ dari _TOTAL_ tiket',
                    infoEmpty: 'Tidak ada data',
                    infoFiltered: '(difilter dari _MAX_ total)',
                    paginate: {
                        previous: '‹',
                        next: '›'
                    },
                    zeroRecords: 'Tidak ada tiket ditemukan',
                    emptyTable: 'Belum ada tiket masuk',
                    processing: '<div class="spinner-border text-primary" role="status"></div>',
                },
                pageLength: 15,
                order: [
                    [1, 'asc']
                ],
                responsive: true,
            });

            $('#statusFilter').on('change', function() {
                window.dataTableInstance.ajax.reload();
            });
        });

        function confirmDeleteTicket(hashedId, noTicket) {
            Swal.fire({
                title: 'Hapus Tiket?',
                html: `Tiket <strong>${noTicket}</strong> akan dihapus permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then(async (result) => {
                if (!result.isConfirmed) return;

                Swal.fire({
                    title: 'Menghapus...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading(),
                });

                try {
                    const response = await fetch(`{{ url($routePrefix . '/tickets') }}/${hashedId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    });

                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Gagal menghapus');

                    Swal.fire({
                        toast: true, position: 'top-end', icon: 'success',
                        title: data.message || 'Tiket berhasil dihapus!',
                        showConfirmButton: false, timer: 2500, timerProgressBar: true,
                    });

                    if (window.dataTableInstance) window.dataTableInstance.ajax.reload(null, false);
                } catch (err) {
                    Swal.fire('Gagal!', err.message, 'error');
                }
            });
        }
    </script>
@endpush

@push('styles')
    <style>
        /* Spacing & Padding for DataTable Controls */
        .dataTables_wrapper,
        .dt-container {
            padding: 15px 0 0 0 !important;
        }

        /* Top Controls: Length & Search padding */
        .dataTables_wrapper .row:first-child,
        .dt-container .row:first-child {
            padding: 0 20px !important;
            margin-bottom: 16px !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            align-items: center !important;
        }

        /* Bottom Controls: Info & Paginate styling and background */
        .dataTables_wrapper .row:last-child,
        .dt-container .row:last-child {
            padding: 14px 20px !important;
            margin-top: 16px !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            background: var(--adm-bg-light) !important;
            border-top: 1px solid var(--adm-border) !important;
            align-items: center !important;
        }

        /* Search input premium styling */
        .dataTables_filter input,
        .dt-search input {
            height: 34px !important;
            width: 220px !important;
            background-color: var(--adm-bg-input) !important;
            border: 1px solid var(--adm-border-mid) !important;
            border-radius: var(--adm-radius-sm) !important;
            padding: 0 12px !important;
            font-size: 12.5px !important;
            color: var(--adm-text-dark) !important;
            outline: none !important;
            transition: border-color 0.18s, box-shadow 0.18s !important;
        }

        .dataTables_filter input:focus,
        .dt-search input:focus {
            border-color: var(--adm-blue) !important;
            background: #fff !important;
            box-shadow: 0 0 0 3px rgba(26, 95, 200, 0.08) !important;
        }

        /* Length dropdown premium styling */
        .dataTables_length select,
        .dt-length select {
            height: 34px !important;
            background-color: var(--adm-bg-input) !important;
            border: 1px solid var(--adm-border-mid) !important;
            border-radius: var(--adm-radius-sm) !important;
            padding: 4px 28px 4px 10px !important;
            font-size: 12.5px !important;
            color: var(--adm-text-dark) !important;
            outline: none !important;
            cursor: pointer !important;
        }

        .dataTables_length select:focus,
        .dt-length select:focus {
            border-color: var(--adm-blue) !important;
            box-shadow: 0 0 0 3px rgba(26, 95, 200, 0.08) !important;
        }

        /* Sidebar table padding alignment */
        .adm-table thead th:first-child,
        .adm-table tbody td:first-child {
            padding-left: 20px !important;
        }

        .adm-table thead th:last-child,
        .adm-table tbody td:last-child {
            padding-right: 20px !important;
        }

        /* Prevent sorting icons from overlapping header text */
        .adm-table thead th.sorting,
        .adm-table thead th.sorting_asc,
        .adm-table thead th.sorting_desc {
            padding-right: 28px !important;
        }

        /* Ensure line height inside td is vertically balanced */
        .adm-table tbody td {
            vertical-align: middle !important;
        }
    </style>
@endpush
