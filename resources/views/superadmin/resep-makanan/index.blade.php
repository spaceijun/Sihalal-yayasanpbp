@extends('layouts.app')
@section('template_title')
    Resep Makanan
@endsection

@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Resep Makanan</h1>
                <p>Kelola data resep makanan dan minuman</p>
            </div>
            <a href="{{ route($routePrefix . '.resep-makanans.create') }}" class="adm-btn-primary">
                <svg viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Buat Resep Makanan
            </a>
        </div>

        <div class="adm-card">
            <div class="adm-card-header">
                <div class="adm-card-title">
                    <svg viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                    </svg>
                    Daftar Resep Makanan
                    <span class="adm-count-badge">{{ $total }}</span>
                </div>
            </div>

            <div class="table-responsive">
                <table id="resepMakananTable" class="adm-table w-100">
                    <thead>
                        <tr>
                            <th style="width:44px">#</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th class="tc" style="width:130px">Aksi</th>
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
        window.dataTableInstance = $('#resepMakananTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route($routePrefix . '.resep-makanans.index') }}',
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'tc' },
                { data: 'nama_link', name: 'nama_produk' },
                { data: 'kategori_badge', name: 'kategori' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'tc' },
            ],
            columnDefs: [
                { targets: [1, 2, 3], render: null }, // raw HTML columns
            ],
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ – _END_ dari _TOTAL_ resep makanan',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total)',
                paginate: { previous: '‹', next: '›' },
                zeroRecords: 'Tidak ada resep makanan ditemukan',
                emptyTable: 'Belum ada resep makanan yang dibuat',
                processing: '<div class="spinner-border text-primary" role="status"></div>',
            },
            pageLength: 15,
            order: [[1, 'asc']],
            responsive: true,
        });
    });

    function confirmDeleteResepMakanan(hashedId, nama) {
        Swal.fire({
            title: 'Hapus Resep Makanan?',
            html: `Resep <strong>${nama}</strong> akan dihapus permanen.`,
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
                const response = await fetch(`{{ url($routePrefix . '/resep-makanans') }}/${hashedId}`, {
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
                    title: data.message || 'Resep makanan berhasil dihapus!',
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
