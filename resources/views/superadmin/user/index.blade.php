@extends('layouts.app')
@section('template_title') Manajemen User @endsection

@section('content')
<div class="adm-page">
    @include('layouts.messages')

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Manajemen User</h1>
            <p>Kelola akun dan hak akses pengguna sistem</p>
        </div>
        <a href="{{ route($routePrefix . '.users.create') }}" class="adm-btn-primary">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah User
        </a>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Daftar Pengguna
            </div>
        </div>

        <div class="table-responsive">
            <table id="userTable" class="adm-table w-100">
                <thead>
                    <tr>
                        <th style="width:44px">#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th class="tc">Role</th>
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
document.addEventListener('DOMContentLoaded', function () {
    $('#userTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route($routePrefix . '.users.data') }}',
            type: 'GET',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'tc' },
            { data: 'nama_cell',   name: 'name',         className: '' },
            { data: 'email',       name: 'email',        className: '' },
            { data: 'telephone',   name: 'telephone',    className: 'adm-mono' },
            { data: 'role_badge',  name: 'role',         className: 'tc' },
            { data: 'aksi',        name: 'aksi',         orderable: false, searchable: false, className: 'tc' },
        ],
        language: {
            search:      'Cari:',
            lengthMenu:  'Tampilkan _MENU_ data',
            info:        'Menampilkan _START_ – _END_ dari _TOTAL_ pengguna',
            infoEmpty:   'Tidak ada data',
            paginate: { previous: '‹', next: '›' },
            zeroRecords: 'Tidak ada user ditemukan',
            emptyTable:  'Belum ada user terdaftar',
            processing:  '<div class="spinner-border text-primary" role="status"></div>',
        },
        pageLength: 15,
        order: [[1, 'asc']],
        responsive: true,
    });
});
</script>
@endpush
