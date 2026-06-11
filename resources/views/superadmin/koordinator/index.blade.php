@extends('layouts.app')
@section('template_title') Koordinator @endsection

@section('content')
<div class="adm-page">
    @include('layouts.messages')

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Koordinator</h1>
            <p>Kelola data koordinator lapangan beserta statistik data mereka</p>
        </div>
        <a href="{{ route('superadmin.koordinators.create') }}" class="adm-btn-primary">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Koordinator
        </a>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Data Koordinator
            </div>
        </div>

        <div class="table-responsive">
            <table id="koordinatorTable" class="adm-table w-100">
                <thead>
                    <tr>
                        <th style="width:44px">#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telephone</th>
                        <th class="tr">Fee Enum</th>
                        <th class="tc">Total Data</th>
                        <th class="tc">Terbit SH</th>
                        <th class="tc">Status</th>
                        <th class="tc" style="width:90px">Aksi</th>
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
document.addEventListener('DOMContentLoaded', function () {
    $('#koordinatorTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('superadmin.koordinators.data') }}',
            type: 'GET',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'tc' },
            { data: 'nama_cell',   name: 'nama_lengkap', render: $.fn.dataTable.render.text(), className: '' },
            { data: 'email',       name: 'email',        className: '' },
            { data: 'telephone',   name: 'telephone',    className: 'adm-mono' },
            { data: 'fee_fmt',     name: 'fee_enum',     className: 'tr', orderable: true },
            { data: 'data_lapangans_count', name: 'data_lapangans_count', className: 'tc' },
            { data: 'terbit_sh_count',      name: 'terbit_sh_count',      className: 'tc' },
            { data: 'status_badge', name: 'status', className: 'tc', orderable: true },
            { data: 'aksi',        name: 'aksi',         orderable: false, searchable: false, className: 'tc' },
        ],
        columnDefs: [
            { targets: [0,1,4,7,8], render: null },  // raw HTML columns
        ],
        createdRow: function(row, data) {
            $(row).find('td:eq(1)').html(data.nama_cell);
            $(row).find('td:eq(7)').html(data.status_badge);
            $(row).find('td:eq(8)').html(data.aksi);
            $(row).find('td:eq(4)').html(data.fee_fmt);
        },
        language: {
            search:          'Cari:',
            lengthMenu:      'Tampilkan _MENU_ data',
            info:            'Menampilkan _START_ – _END_ dari _TOTAL_ koordinator',
            infoEmpty:       'Tidak ada data',
            infoFiltered:    '(difilter dari _MAX_ total)',
            paginate: { previous: '‹', next: '›' },
            zeroRecords:     'Tidak ada koordinator ditemukan',
            emptyTable:      'Belum ada data koordinator',
            processing:      '<div class="spinner-border text-primary" role="status"></div>',
        },
        drawCallback: function () {
            // Bersihkan event agar tidak double-bind pada setiap draw
        },
        pageLength: 15,
        order: [[1, 'asc']],
        responsive: true,
    });
});
</script>
@endpush
