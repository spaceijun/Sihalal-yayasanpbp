@extends('layouts.app')
@section('template_title') Data Enumerator @endsection

@section('content')
<div class="adm-page">
    @include('layouts.messages')

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Data Enumerator</h1>
            <p>Daftar enumerator di bawah koordinasi Anda</p>
        </div>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                Daftar Enumerator
            </div>
        </div>
        <div class="table-responsive">
            <table id="enumeratorTable" class="adm-table w-100">
                <thead>
                    <tr>
                        <th style="width:44px">#</th>
                        <th>Nama</th>
                        <th>No. Registrasi</th>
                        <th>Telephone</th>
                        <th class="tc">Total Data</th>
                        <th class="tc">Data Bulan Ini</th>
                        <th class="tc">Status</th>
                        <th class="tc" style="width:70px">Aksi</th>
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
        window.dataTableInstance = $('#enumeratorTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('koordinator.enumerator.data') }}',
                type: 'GET',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'tc' },
                { data: 'nama_cell', name: 'nama_lengkap' },
                { data: 'no_registrasi', name: 'no_registrasi' },
                { data: 'telephone', name: 'telephone', className: 'adm-mono' },
                { data: 'data_lapangans_count', name: 'data_lapangans_count', className: 'tc' },
                { data: 'data_bulan_ini', name: 'data_bulan_ini', className: 'tc' },
                { data: 'status_badge', name: 'status', className: 'tc' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'tc' },
            ],
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ – _END_ dari _TOTAL_ enumerator',
                infoEmpty: 'Tidak ada data',
                paginate: { previous: '‹', next: '›' },
                zeroRecords: 'Tidak ada enumerator ditemukan',
                emptyTable: 'Belum ada enumerator di bawah koordinasi Anda',
                processing: '<div class="spinner-border text-primary" role="status"></div>',
            },
            pageLength: 15,
            order: [[1, 'asc']],
            responsive: true,
        });
    });
</script>
@endpush
