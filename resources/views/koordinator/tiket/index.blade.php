@extends('layouts.app')
@section('template_title') Tiket @endsection

@section('content')
<div class="adm-page">
    @include('layouts.messages')

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Tiket Aduan</h1>
            <p>Buat &amp; pantau aduan Anda kepada Admin</p>
        </div>
        <a href="{{ route('koordinator.tiket.create') }}" class="adm-btn-primary">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Tiket
        </a>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                Riwayat Tiket
            </div>
        </div>
        <div class="table-responsive">
            <table id="tiketTable" class="adm-table w-100">
                <thead>
                    <tr>
                        <th style="width:44px">#</th>
                        <th>No. Tiket</th>
                        <th>Kategori</th>
                        <th>Subjek</th>
                        <th class="tc">Status</th>
                        <th>Tanggal</th>
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
        window.dataTableInstance = $('#tiketTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('koordinator.tiket.data') }}',
                type: 'GET',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'tc' },
                { data: 'no_ticket_cell', name: 'no_ticket' },
                { data: 'kategori_badge', name: 'kategori' },
                { data: 'subject_cell', name: 'subject' },
                { data: 'status_badge', name: 'status', className: 'tc' },
                { data: 'tanggal_fmt', name: 'created_at' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'tc' },
            ],
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ – _END_ dari _TOTAL_ tiket',
                infoEmpty: 'Tidak ada data',
                paginate: { previous: '‹', next: '›' },
                zeroRecords: 'Tidak ada tiket ditemukan',
                emptyTable: 'Belum ada tiket dibuat',
                processing: '<div class="spinner-border text-primary" role="status"></div>',
            },
            pageLength: 15,
            order: [[5, 'desc']],
            responsive: true,
        });
    });
</script>
@endpush
