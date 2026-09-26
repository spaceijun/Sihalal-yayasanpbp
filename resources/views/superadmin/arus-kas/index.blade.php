@extends('layouts.app')
@section('template_title')
    Arus Kas
@endsection

@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        {{-- ── PAGE HEADER ── --}}
        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Arus Kas</h1>
                <p>Kelola data pemasukan, pengeluaran, dan kas</p>
            </div>
            <div style="display:flex;gap:8px;">
                <a href="{{ route($routePrefix . '.cashflow.index') }}" class="adm-btn-secondary">
                    <svg viewBox="0 0 24 24">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
                        <polyline points="17 6 23 6 23 12" />
                    </svg>
                    Laporan
                </a>
                <a href="{{ route($routePrefix . '.arus-kas.create') }}" class="adm-btn-primary">
                    <svg viewBox="0 0 24 24">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Tambah Transaksi
                </a>
            </div>
        </div>

        {{-- ── TABLE CARD ── --}}
        <div class="adm-card">
            <div class="adm-card-header">
                <div class="adm-card-title">
                    <svg viewBox="0 0 24 24">
                        <line x1="12" y1="1" x2="12" y2="23" />
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                    </svg>
                    Daftar Transaksi
                    <span class="adm-count-badge">{{ $totalCashflows }}</span>
                </div>
            </div>

            <div class="table-responsive">
                <table id="cashflowTable" class="adm-table w-100">
                    <thead>
                        <tr>
                            <th style="width:44px">#</th>
                            <th>Tipe</th>
                            <th class="tr">Jumlah</th>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th class="tc" style="width:120px">Aksi</th>
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
        window.dataTableInstance = $('#cashflowTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route($routePrefix . '.arus-kas.index') }}',
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'tc' },
                { data: 'tipe_badge', name: 'tipe', orderable: true },
                { data: 'jumlah_fmt', name: 'jumlah', className: 'tr adm-mono', orderable: true },
                { data: 'tanggal_fmt', name: 'tanggal', orderable: true },
                { data: 'keterangan_fmt', name: 'keterangan', orderable: false },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'tc' },
            ],
            columnDefs: [
                { targets: [1, 2, 4, 5], render: null }, // raw HTML columns
            ],
            createdRow: function (row, data) {
                $(row).find('td:eq(1)').html(data.tipe_badge);
                $(row).find('td:eq(2)').html(data.jumlah_fmt);
                $(row).find('td:eq(4)').html(data.keterangan_fmt);
                $(row).find('td:eq(5)').html(data.aksi);
            },
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ – _END_ dari _TOTAL_ transaksi',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total)',
                paginate: { previous: '‹', next: '›' },
                zeroRecords: 'Tidak ada transaksi ditemukan',
                emptyTable: 'Belum ada data transaksi',
                processing: '<div class="spinner-border text-primary" role="status"></div>',
            },
            pageLength: 15,
            order: [[3, 'desc']],
            responsive: true,
        });
    });

    function confirmDeleteCashflow(hashedId, label) {
        Swal.fire({
            title: 'Hapus Transaksi?',
            html: `Data transaksi <strong>${label}</strong> akan dihapus permanen.`,
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
                const response = await fetch(`{{ url($routePrefix . '/arus-kas') }}/${hashedId}`, {
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
                    title: data.message || 'Transaksi berhasil dihapus!',
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
