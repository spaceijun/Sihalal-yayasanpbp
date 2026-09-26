@extends('layouts.app')
@section('template_title') Spotcheck @endsection
@section('content')
<div class="adm-page">
    @include('layouts.messages')

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Manajemen Spotcheck</h1>
            <p>Kelola data kunjungan spotcheck pelaku usaha</p>
        </div>
        <a href="{{ route($routePrefix . '.spotchecks.create') }}" class="adm-btn-primary">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Spotcheck
        </a>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                Daftar Spotcheck
            </div>
        </div>

        <div class="table-responsive">
            <table id="spotcheckTable" class="adm-table w-100">
                <thead>
                    <tr>
                        <th style="width:44px">#</th>
                        <th>ID Data Lapangan</th>
                        <th>Nama Spotcheck</th>
                        <th>Tanggal</th>
                        <th>Foto PU</th>
                        <th>Hasil Spotcheck</th>
                        <th class="tc" style="width:110px">Aksi</th>
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
            window.dataTableInstance = $('#spotcheckTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route($routePrefix . '.spotchecks.data') }}',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'tc' },
                    { data: 'data_lapangan_id', name: 'data_lapangan_id', className: 'adm-mono' },
                    { data: 'nama_spotcheck', name: 'nama_spotcheck' },
                    { data: 'tanggal_cell', name: 'tanggal_spotcheck' },
                    { data: 'foto_pu_cell', name: 'foto_pu', orderable: false, searchable: false },
                    { data: 'hasil_cell', name: 'hasil_spotcheck', orderable: false },
                    { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'tc' },
                ],
                language: {
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ – _END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data',
                    infoFiltered: '(difilter dari _MAX_ total)',
                    paginate: { previous: '‹', next: '›' },
                    zeroRecords: 'Tidak ada spotcheck ditemukan',
                    emptyTable: 'Belum ada data spotcheck.',
                    processing: '<div class="spinner-border text-primary" role="status"></div>',
                },
                pageLength: 15,
                order: [[3, 'desc']],
                responsive: true,
            });
        });

        function confirmDeleteSpotcheck(hashedId, nama) {
            Swal.fire({
                title: 'Hapus Spotcheck?',
                html: `Data spotcheck <strong>${nama}</strong> akan dihapus permanen.`,
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
                    const response = await fetch(`{{ url($routePrefix . '/spotchecks') }}/${hashedId}`, {
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
                        title: data.message || 'Spotcheck berhasil dihapus!',
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
        .dataTables_wrapper, .dt-container { padding: 15px 0 0 0 !important; }
        .dataTables_wrapper .row:first-child, .dt-container .row:first-child {
            padding: 0 20px !important; margin-bottom: 16px !important;
            margin-left: 0 !important; margin-right: 0 !important; align-items: center !important;
        }
        .dataTables_wrapper .row:last-child, .dt-container .row:last-child {
            padding: 14px 20px !important; margin-top: 16px !important;
            margin-left: 0 !important; margin-right: 0 !important;
            background: var(--adm-bg-light) !important; border-top: 1px solid var(--adm-border) !important;
            align-items: center !important;
        }
        .dataTables_filter input, .dt-search input {
            height: 34px !important; width: 220px !important;
            background-color: var(--adm-bg-input) !important; border: 1px solid var(--adm-border-mid) !important;
            border-radius: var(--adm-radius-sm) !important; padding: 0 12px !important;
            font-size: 12.5px !important; color: var(--adm-text-dark) !important; outline: none !important;
            transition: border-color 0.18s, box-shadow 0.18s !important;
        }
        .dataTables_filter input:focus, .dt-search input:focus {
            border-color: var(--adm-blue) !important; background: #fff !important;
            box-shadow: 0 0 0 3px rgba(26, 95, 200, 0.08) !important;
        }
        .dataTables_length select, .dt-length select {
            height: 34px !important; background-color: var(--adm-bg-input) !important;
            border: 1px solid var(--adm-border-mid) !important; border-radius: var(--adm-radius-sm) !important;
            padding: 4px 28px 4px 10px !important; font-size: 12.5px !important;
            color: var(--adm-text-dark) !important; outline: none !important; cursor: pointer !important;
        }
        .dataTables_length select:focus, .dt-length select:focus {
            border-color: var(--adm-blue) !important; box-shadow: 0 0 0 3px rgba(26, 95, 200, 0.08) !important;
        }
        .adm-table thead th:first-child, .adm-table tbody td:first-child { padding-left: 20px !important; }
        .adm-table thead th:last-child, .adm-table tbody td:last-child { padding-right: 20px !important; }
        .adm-table thead th.sorting, .adm-table thead th.sorting_asc, .adm-table thead th.sorting_desc { padding-right: 28px !important; }
        .adm-mono { font-family: "Consolas", "Courier New", monospace !important; font-size: 12.5px !important; letter-spacing: 0.02em !important; }
        .adm-table tbody td { vertical-align: middle !important; }
    </style>
@endpush
