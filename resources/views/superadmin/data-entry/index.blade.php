@extends('layouts.app')
@section('template_title')
    Data Entries
@endsection

@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Data Entry</h1>
                <p>Kelola akun petugas data entry beserta koordinator dan rekening</p>
            </div>
            <a href="{{ route('superadmin.data-entries.create') }}" class="adm-btn-primary">
                <svg viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Tambah Data Entry
            </a>
        </div>

        <div class="adm-card">
            <div class="adm-card-header">
                <div class="adm-card-title">
                    <svg viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                    Daftar Data Entry
                </div>
            </div>

            <div class="table-responsive">
                <table id="dataEntryTable" class="adm-table w-100">
                    <thead>
                        <tr>
                            <th style="width:44px">#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Telephone</th>
                            <th class="tc">Status</th>
                            <th class="tc">Entry Type</th>
                            <th>Rekening</th>
                            <th class="tc" style="width:100px">Aksi</th>
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
            $('#dataEntryTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('superadmin.data-entries.data') }}',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                        data: 'nama_cell',
                        name: 'nama_lengkap',
                        className: ''
                    },
                    {
                        data: 'email',
                        name: 'email',
                        className: ''
                    },
                    {
                        data: 'telephone',
                        name: 'telephone',
                        className: 'adm-mono'
                    },
                    {
                        data: 'status_badge',
                        name: 'status',
                        className: 'tc'
                    },
                    {
                        data: 'entry_type_badge',
                        name: 'entry_type',
                        className: 'tc'
                    },
                    {
                        data: 'rekening',
                        name: 'rekening',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false,
                        className: 'tc'
                    },
                ],
                columnDefs: [{
                        targets: [0, 1, 4, 5, 6, 7],
                        render: null
                    }, // raw HTML columns
                ],
                language: {
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ – _END_ dari _TOTAL_ data entry',
                    infoEmpty: 'Tidak ada data',
                    paginate: {
                        previous: '‹',
                        next: '›'
                    },
                    zeroRecords: 'Tidak ada data entry ditemukan',
                    emptyTable: 'Belum ada data entry terdaftar',
                    processing: '<div class="spinner-border text-primary" role="status"></div>',
                },
                pageLength: 15,
                order: [
                    [1, 'asc']
                ],
                responsive: true,
            });

            // Intercept form deletion with SweetAlert2
            $(document).on('submit', '.form-delete', function(e) {
                e.preventDefault();
                var form = this;
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data entry ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#74788d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-danger w-xs me-2',
                        cancelButton: 'btn btn-light w-xs'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
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

        /* Fallback for monospace font families */
        .adm-mono {
            font-family: "Consolas", "Courier New", monospace !important;
            font-size: 12.5px !important;
            letter-spacing: 0.02em !important;
        }

        /* Ensure line height inside td is vertically balanced */
        .adm-table tbody td {
            vertical-align: middle !important;
        }
    </style>
@endpush
