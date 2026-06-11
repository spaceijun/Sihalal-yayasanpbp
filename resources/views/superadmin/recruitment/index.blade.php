@extends('layouts.app')
@section('template_title')
    Recruitment
@endsection

@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Recruitment</h1>
                <p>Kelola data pelamar data entry dan pendamping lapangan</p>
            </div>
            <a href="{{ route('superadmin.recruitments.create') }}" class="adm-btn-primary">
                <svg viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Tambah Pelamar
            </a>
        </div>

        <div class="adm-card">
            <div class="table-responsive">
                <table id="recruitmentTable" class="adm-table w-100">
                    <thead>
                        <tr>
                            <th style="width:44px">#</th>
                            <th>Koordinator</th>
                            <th>Nama Lengkap</th>
                            <th>Telephone</th>
                            <th>Rekomendasi</th>
                            <th class="tc">Tipe</th>
                            <th class="tc">Status</th>
                            <th class="tc" style="width:90px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    @include('superadmin.recruitment.partials.delete-modal')
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            'use strict';

            const CSRF = $('meta[name="csrf-token"]').attr('content');

            const dt = $('#recruitmentTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('superadmin.recruitments.data') }}',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': CSRF
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
                        data: 'koordinator_name',
                        name: 'koordinator_name',
                        orderable: false
                    },
                    {
                        data: 'nama_cell',
                        name: 'nama_lengkap',
                        className: ''
                    },
                    {
                        data: 'telephone',
                        name: 'telephone',
                        className: 'adm-mono'
                    },
                    {
                        data: 'rekomendasi_badge',
                        name: 'rekomendasi',
                        orderable: false
                    },
                    {
                        data: 'recruit_type_badge',
                        name: 'recruit_type',
                        className: 'tc'
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
                    info: 'Menampilkan _START_ – _END_ dari _TOTAL_ pelamar',
                    infoEmpty: 'Tidak ada data',
                    paginate: {
                        previous: '‹',
                        next: '›'
                    },
                    zeroRecords: 'Tidak ada recruitment ditemukan',
                    emptyTable: 'Belum ada data recruitment',
                    processing: '<div class="spinner-border text-primary" role="status"></div>',
                },
                pageLength: 20,
                order: [
                    [2, 'asc']
                ],
                responsive: true,
            });
        });
    </script>
@endpush
