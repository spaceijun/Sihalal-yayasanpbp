@extends('layouts.app')
@section('template_title') Data Entries @endsection

@section('content')
<div class="adm-page">
    @include('layouts.messages')

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Data Entry</h1>
            <p>Kelola akun petugas data entry beserta koordinator dan rekening</p>
        </div>
        <a href="{{ route('superadmin.data-entries.create') }}" class="adm-btn-primary">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Data Entry
        </a>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
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
document.addEventListener('DOMContentLoaded', function () {
    $('#dataEntryTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('superadmin.data-entries.data') }}',
            type: 'GET',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        },
        columns: [
            { data: 'DT_RowIndex',       name: 'DT_RowIndex',   orderable: false, searchable: false, className: 'tc' },
            { data: 'nama_cell',         name: 'nama_lengkap',  className: '' },
            { data: 'email',             name: 'email',         className: '' },
            { data: 'telephone',         name: 'telephone',     className: 'adm-mono' },
            { data: 'status_badge',      name: 'status',        className: 'tc' },
            { data: 'entry_type_badge',  name: 'entry_type',    className: 'tc' },
            { data: 'rekening',          name: 'rekening',      orderable: false, searchable: false },
            { data: 'aksi',              name: 'aksi',          orderable: false, searchable: false, className: 'tc' },
        ],
        language: {
            search:      'Cari:',
            lengthMenu:  'Tampilkan _MENU_ data',
            info:        'Menampilkan _START_ – _END_ dari _TOTAL_ data entry',
            infoEmpty:   'Tidak ada data',
            paginate: { previous: '‹', next: '›' },
            zeroRecords: 'Tidak ada data entry ditemukan',
            emptyTable:  'Belum ada data entry terdaftar',
            processing:  '<div class="spinner-border text-primary" role="status"></div>',
        },
        pageLength: 15,
        order: [[1, 'asc']],
        responsive: true,
    });
});
</script>
@endpush
