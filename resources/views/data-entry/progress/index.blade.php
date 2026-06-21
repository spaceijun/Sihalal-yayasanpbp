@extends('layouts.app')

@section('template_title')
    Progress Data Entry
@endsection

@section('content')
    @php
        $dtProgressUrl =
            auth()->user()->role === 'data_entry'
                ? route('data-entry.progress.data')
                : route('enumerator.progress.data');
    @endphp

    <div class="row">
        <div class="col">
            @include('layouts.messages')
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span id="card_title">
                            {{ __('Progress Data Entry') }}
                        </span>
                    </div>
                </div>

                <!-- Form Search -->
                <div class="card-body bg-white border-bottom">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Cari Nama PU / NIK</label>
                            <input type="text" class="form-control" id="search" name="search"
                                placeholder="Cari berdasarkan nama PU atau NIK...">
                        </div>
                        <div class="col-md-2">
                            <label for="action" class="form-label">Aksi</label>
                            <select class="form-select" id="action" name="action">
                                <option value="">Semua Aksi</option>
                                <option value="create">Create</option>
                                <option value="update">Update</option>
                                <option value="delete">Delete</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="tanggal_dari" class="form-label">Tanggal Dari</label>
                            <input type="date" class="form-control" id="tanggal_dari" name="tanggal_dari">
                        </div>
                        <div class="col-md-3">
                            <label for="tanggal_sampai" class="form-label">Tanggal Sampai</label>
                            <input type="date" class="form-control" id="tanggal_sampai" name="tanggal_sampai">
                        </div>
                    </div>
                </div>

                <div class="card-body bg-white">
                    <div class="table-responsive">
                        <table id="progressTable" class="table table-striped table-hover w-100">
                            <thead class="thead">
                                <tr>
                                    <th>No</th>
                                    <th>Waktu Aksi</th>
                                    <th>Nama PU</th>
                                    <th>NIK</th>
                                    <th>Aksi</th>
                                    <th>Status</th>
                                    <th>Verifikator</th>
                                    <th>Detail</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                var searchTimer;

                var table = $('#progressTable').DataTable({
                    processing: true,
                    serverSide: true,
                    searching: true,
                    ajax: {
                        url: '{{ $dtProgressUrl }}',
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: function(d) {
                            d.action_filter = $('#action').val();
                            d.tanggal_dari = $('#tanggal_dari').val();
                            d.tanggal_sampai = $('#tanggal_sampai').val();
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'waktu_aksi',
                            name: 'actioned_at'
                        },
                        {
                            data: 'nama_pu',
                            name: 'nama_pu'
                        },
                        {
                            data: 'nik',
                            name: 'nik'
                        },
                        {
                            data: 'action_badge',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'status_badge',
                            name: 'status',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'nama_verifikator',
                            name: 'nama_verifikator',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'aksi',
                            name: 'aksi',
                            orderable: false,
                            searchable: false
                        },
                    ],
                    dom: 'rt<"d-flex justify-content-between align-items-center px-1 pt-2"ip>',
                    language: {
                        processing: '<div class="text-center py-4"><div class="spinner-border text-primary" style="width:2.5rem;height:2.5rem;" role="status"></div><p class="mt-2 text-muted fw-bold small">SABAR BOS...</p></div>',
                        emptyTable: '<div class="text-center text-muted py-4">Tidak ada data progress.</div>',
                        zeroRecords: '<div class="text-center text-muted py-4">Data tidak ditemukan.</div>',
                        info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                        infoEmpty: 'Tidak ada data',
                        paginate: {
                            previous: '‹',
                            next: '›'
                        },
                    },
                    pageLength: 15,
                    order: [
                        [1, 'desc']
                    ],
                });

                // Wire search input to DataTables global search
                $('#search').on('input', function() {
                    clearTimeout(searchTimer);
                    var val = this.value;
                    searchTimer = setTimeout(function() {
                        table.search(val).draw();
                    }, 400);
                });

                // Wire dropdown/date filters (custom params sent via ajax.data)
                $('#action, #tanggal_dari, #tanggal_sampai').on('change', function() {
                    table.ajax.reload();
                });
            });
        </script>
    @endpush
@endsection
