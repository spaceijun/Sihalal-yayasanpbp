@extends('layouts.app')
@section('template_title')
    Manajemen Lowongan Pekerjaan
@endsection

@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Lowongan Pekerjaan</h1>
                <p>Kelola lowongan, syarat pendaftaran, dan generate link form otomatis</p>
            </div>
            <a href="{{ route($routePrefix . '.recruitment-posts.create') }}" class="adm-btn-primary">
                <svg viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Buat Lowongan
            </a>
        </div>

        <div class="adm-card">
            <div class="adm-filter-bar">
                <div class="adm-filter-group">
                    <label class="adm-filter-label" for="filterPosisi">Posisi</label>
                    <select id="filterPosisi" class="adm-select" style="min-width:150px;height:34px;">
                        <option value="">Semua Posisi</option>
                        <option value="PENDAMPING">Pendamping</option>
                        <option value="DATA ENTRY">Data Entry</option>
                        <option value="ADMIN UMUM">Admin Umum</option>
                    </select>
                </div>
                <div class="adm-filter-group">
                    <label class="adm-filter-label" for="filterStatus">Status</label>
                    <select id="filterStatus" class="adm-select" style="min-width:130px;height:34px;">
                        <option value="">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
                <div class="adm-filter-group">
                    <label class="adm-filter-label" for="customLength">Tampilkan</label>
                    <select id="customLength" class="adm-select" style="min-width:80px;height:34px;">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <div style="flex-grow:1;"></div>
                <div class="adm-filter-group">
                    <label class="adm-filter-label" for="customSearch">Pencarian</label>
                    <div style="position:relative;">
                        <input type="text" id="customSearch" class="adm-input" placeholder="Cari lowongan..."
                            style="padding-left:34px;height:34px;width:240px;">
                        <svg viewBox="0 0 24 24"
                            style="position:absolute;left:11px;top:10px;width:14px;height:14px;fill:none;stroke:var(--adm-text-muted);stroke-width:2.5;pointer-events:none;">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="recruitmentPostTable" class="adm-table w-100">
                    <thead>
                        <tr>
                            <th style="width:44px">#</th>
                            <th>Nama Lowongan</th>
                            <th class="tc">Posisi</th>
                            <th class="tc">Status</th>
                            <th>Link Pendaftaran</th>
                            <th class="tc">Pelamar</th>
                            <th class="tc" style="width:160px">Aksi</th>
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
            'use strict';
            const CSRF = $('meta[name="csrf-token"]').attr('content');

            const dt = $('#recruitmentPostTable').DataTable({
                processing: true,
                serverSide: true,
                dom: "<'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                ajax: {
                    url: '{{ route($routePrefix . '.recruitment-posts.data') }}',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': CSRF
                    },
                    data: function(d) {
                        d.posisi = $('#filterPosisi').val();
                        d.status = $('#filterStatus').val();
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
                        name: 'nama_loker'
                    },
                    {
                        data: 'posisi_badge',
                        name: 'posisi',
                        className: 'tc'
                    },
                    {
                        data: 'status_badge',
                        name: 'is_active',
                        className: 'tc'
                    },
                    {
                        data: 'link_publik',
                        name: 'slug',
                        orderable: false
                    },
                    {
                        data: 'jumlah_pelamar',
                        name: 'recruitments_count',
                        className: 'tc',
                        orderable: false
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
                    info: 'Menampilkan _START_ – _END_ dari _TOTAL_ lowongan',
                    infoEmpty: 'Tidak ada data',
                    paginate: {
                        previous: '‹',
                        next: '›'
                    },
                    zeroRecords: 'Tidak ada lowongan ditemukan',
                    emptyTable: 'Belum ada lowongan pekerjaan',
                    processing: '<div class="spinner-border text-primary" role="status"></div>',
                },
                pageLength: 20,
                order: [
                    [1, 'asc']
                ],
                responsive: true,
            });

            $('#filterPosisi, #filterStatus').on('change', function() {
                dt.draw();
            });
            $('#customLength').on('change', function() {
                dt.page.len(parseInt($(this).val())).draw();
            });
            $('#customSearch').on('keyup', function() {
                dt.search($(this).val()).draw();
            });

            // Konfirmasi hapus
            $(document).on('submit', '.form-delete', function(e) {
                e.preventDefault();
                const form = this;
                Swal.fire({
                    title: 'Hapus Lowongan?',
                    text: 'Data lowongan dan semua pelamar yang terhubung akan terputus!',
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
                    if (result.isConfirmed) form.submit();
                });
            });

            // Copy link
            $(document).on('click', '.adm-link-copy', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                navigator.clipboard.writeText(url).then(() => {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4500,
                        timerProgressBar: true,
                        didOpen: function (toast) {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    });
                    Toast.fire({
                        icon: 'success',
                        title: 'Link pendaftaran berhasil disalin!'
                    });
                });
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .dataTables_wrapper,
        .dt-container {
            padding: 15px 0 0 0 !important;
        }

        .dataTables_wrapper .row:first-child,
        .dt-container .row:first-child {
            padding: 0 20px !important;
            margin-bottom: 16px !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            align-items: center !important;
        }

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

        .adm-table thead th:first-child,
        .adm-table tbody td:first-child {
            padding-left: 20px !important;
        }

        .adm-table thead th:last-child,
        .adm-table tbody td:last-child {
            padding-right: 20px !important;
        }

        .adm-table thead th.sorting,
        .adm-table thead th.sorting_asc,
        .adm-table thead th.sorting_desc {
            padding-right: 28px !important;
        }

        .adm-table tbody td {
            vertical-align: middle !important;
        }

        .adm-link-copy {
            color: var(--adm-blue) !important;
            text-decoration: underline !important;
            cursor: pointer;
        }

        .adm-link-copy:hover {
            color: var(--adm-blue-dark, #1a4fb8) !important;
        }
    </style>
@endpush
