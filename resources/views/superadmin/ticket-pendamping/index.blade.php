@extends('layouts.app')
@section('template_title') Ticket Pendamping @endsection

@push('styles')
    <style>
        /* Table responsive wrapper and custom card footer styling */
        .adm-card .table-responsive {
            margin: 0;
            border: none;
        }

        .adm-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding: 12px 20px;
            border-top: 1px solid var(--adm-border);
            background: var(--adm-bg-light);
        }

        .adm-footer-info .dataTables_info {
            font-size: 12.5px;
            color: var(--adm-text-muted);
            padding: 0 !important;
            margin: 0;
            line-height: 1;
        }

        /* Beautiful pagination styling matching admin-ui.css */
        .adm-pagination .pagination {
            margin: 0;
            padding: 0;
            display: flex;
            gap: 4px;
            align-items: center;
            list-style: none;
        }

        .adm-pagination .pagination .page-item .page-link {
            min-width: 30px;
            height: 30px;
            padding: 0 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px !important;
            border: 1px solid var(--adm-border-mid);
            background: #fff;
            color: var(--adm-text-mid) !important;
            font-size: 12.5px;
            font-weight: 500;
            box-shadow: none !important;
            transition: all 0.15s ease;
            text-decoration: none;
            line-height: 1;
        }

        .adm-pagination .pagination .page-item:hover:not(.active):not(.disabled) .page-link {
            background: var(--adm-bg-light);
            border-color: var(--adm-border-mid);
            color: var(--adm-text-dark) !important;
        }

        .adm-pagination .pagination .page-item.active .page-link {
            background: var(--adm-blue) !important;
            border-color: var(--adm-blue) !important;
            color: #fff !important;
            font-weight: 600;
        }

        .adm-pagination .pagination .page-item.disabled .page-link {
            opacity: 0.35;
            background: #fff;
            border-color: var(--adm-border-mid);
            color: var(--adm-text-muted) !important;
            cursor: not-allowed;
        }
    </style>
@endpush

@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        {{-- PAGE HEADER --}}
        <div class="adm-header">
            <div class="adm-header-left">
                <h1>
                    <svg viewBox="0 0 24 24"
                        style="display:inline-block;width:22px;height:22px;stroke:var(--adm-blue);fill:none;stroke-width:2;vertical-align:-4px;margin-right:6px;">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                    </svg>
                    Ticket Pendamping
                </h1>
                <p>Kelola tiket kendala dari enumerator/pendamping lapangan</p>
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
                <div class="adm-stat-value is-success" style="color: var(--adm-amber);">{{ $counts['open'] }}</div>
                <div class="adm-stat-sub">Menunggu tindakan</div>
            </div>
            <div class="adm-stat">
                <div class="adm-stat-label">Proses</div>
                <div class="adm-stat-value is-warn" style="color: var(--adm-blue);">{{ $counts['proses'] }}</div>
                <div class="adm-stat-sub">Sedang diproses</div>
            </div>
            <div class="adm-stat">
                <div class="adm-stat-label">Closed</div>
                <div class="adm-stat-value" style="color: var(--adm-green);">{{ $counts['closed'] }}</div>
                <div class="adm-stat-sub">Telah selesai</div>
            </div>
        </div>

        {{-- MAIN TABLE CARD --}}
        <div class="adm-card">
            {{-- FILTER BAR --}}
            <div class="adm-filter-bar">
                <div class="adm-filter-group">
                    <span class="adm-filter-label">Cari</span>
                    <div class="adm-search-shell">
                        <svg class="adm-search-icon" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                        <input type="text" class="adm-search-input" id="ticketSearch"
                            placeholder="Cari nomor, enumerator..." />
                    </div>
                </div>
                <div class="adm-filter-group">
                    <span class="adm-filter-label">Status</span>
                    <select class="adm-select" id="statusFilter" style="width:140px;">
                        <option value="">Semua Status</option>
                        <option value="Open">Open</option>
                        <option value="Proses">Proses</option>
                        <option value="Closed">Closed</option>
                    </select>
                </div>
                <div class="adm-filter-group" style="margin-left:auto;">
                    <span class="adm-filter-label">&nbsp;</span>
                    <button class="adm-reset-btn" id="resetFilter">
                        <svg viewBox="0 0 24 24">
                            <polyline points="1 4 1 10 7 10" />
                            <path d="M3.51 15a9 9 0 1 0 .49-3.5" />
                        </svg>
                        Reset
                    </button>
                </div>
            </div>

            <table id="ticketTable" class="adm-table w-100">
                <thead>
                    <tr>
                        <th style="width:44px">#</th>
                        <th style="width:140px">No. Tiket</th>
                        <th style="width:180px">Enumerator</th>
                        <th>Nama PU</th>
                        <th>Isi Kendala</th>
                        <th class="tc" style="width:100px">Status</th>
                        <th style="width:130px">Tanggal</th>
                        <th class="tc" style="width:90px">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    {{-- Hidden Delete Form --}}
    <form id="deleteForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- Delete Modal --}}
    <div id="deleteModal" class="modal fade adm-modal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <svg viewBox="0 0 24 24" style="width:18px;height:18px;stroke:var(--adm-red);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;margin-right:6px;vertical-align:-3px;">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                            <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                        Konfirmasi Hapus Tiket
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="text-align:center;padding:28px 24px 20px;">
                    <div style="width:64px;height:64px;border-radius:50%;background:var(--adm-red-lt);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                        <svg viewBox="0 0 24 24" style="width:28px;height:28px;stroke:var(--adm-red);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;">
                            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/>
                        </svg>
                    </div>
                    <h5 style="font-family:'Sora',sans-serif;font-weight:700;color:var(--adm-text-dark);margin-bottom:8px;">Yakin hapus tiket ini?</h5>
                    <p style="font-size:13px;color:var(--adm-text-muted);margin:0;">Tindakan ini tidak dapat dibatalkan. Tiket pendamping akan dihapus permanen dari sistem.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="adm-btn-primary" id="confirmDeleteBtn"
                        style="background:linear-gradient(135deg,var(--adm-red),#b91c1c);">
                        <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>
                        Hapus Tiket
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            'use strict';

            const CSRF = $('meta[name="csrf-token"]').attr('content');
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

            const dt = $('#ticketTable').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                dom: "<'table-responsive'tr><'adm-card-footer'<'adm-footer-info'i><'adm-pagination'p>>",
                ajax: {
                    url: '{{ route($routePrefix . '.ticket-pendampings.data') }}',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': CSRF
                    },
                    data: function(d) {
                        d.status = $('#statusFilter').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'tc' },
                    { data: 'no_tiket_cell', name: 'no_tiket', className: '' },
                    { data: 'enumerator_cell', name: 'user.name', className: '' },
                    { data: 'nama_pu_cell', name: 'dataLapangan.nama_pu', className: '' },
                    { data: 'isi_kendala_cell', name: 'isi_kendala', className: '' },
                    { data: 'status_badge', name: 'status', className: 'tc' },
                    { data: 'tanggal', name: 'created_at', className: '' },
                    { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'tc' }
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
                    emptyTable: 'Belum ada tiket pendamping',
                    processing: '<div class="spinner-border text-primary" role="status"></div>',
                },
                pageLength: 15,
                order: [
                    [6, 'desc']
                ],
                responsive: true,
                drawCallback: bindRowActions
            });

            function bindRowActions() {
                // Delete actions
                $(document).off('click', '.btn-delete').on('click', '.btn-delete', function() {
                    const deleteUrl = $(this).data('url');
                    $('#deleteForm').attr('action', deleteUrl);
                    deleteModal.show();
                });
            }

            // Confirm delete trigger
            $('#confirmDeleteBtn').on('click', function() {
                const btn = $(this);
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menghapus...');
                $('#deleteForm').submit();
            });

            // Filter status
            $('#statusFilter').on('change', function() {
                dt.ajax.reload();
            });

            // Custom search trigger
            $('#ticketSearch').on('keyup', function() {
                dt.search($(this).val()).draw();
            });

            // Reset filters
            $('#resetFilter').on('click', function() {
                $('#ticketSearch').val('');
                $('#statusFilter').val('');
                dt.search('').ajax.reload();
            });
        });
    </script>
@endpush
