@extends('layouts.app')
@section('template_title')
    Fee Enumerator
@endsection

@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Fee Enumerator</h1>
                <p>Kelola besaran fee enumerator berdasarkan skala Global atau Provinsi</p>
            </div>
            <a href="{{ route($routePrefix . '.fee-enumerator.create') }}" class="adm-btn-primary">
                <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Fee
            </a>
        </div>

        <div class="adm-card">
            <div class="adm-card-header">
                <div class="adm-card-title">
                    <svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    Daftar Fee Enumerator
                </div>
            </div>

            <div class="table-responsive">
                <table id="feeEnumeratorTable" class="adm-table w-100">
                    <thead>
                        <tr>
                            <th style="width:44px">#</th>
                            <th>Skala</th>
                            <th>Tipe Fee</th>
                            <th class="tr">Nominal</th>
                            <th>Keterangan</th>
                            <th class="tc">Status</th>
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
        window.dataTableInstance = $('#feeEnumeratorTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route($routePrefix . '.fee-enumerator.data') }}',
                type: 'GET',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'tc' },
                { data: 'skala_badge', name: 'skala' },
                { data: 'tipe_fee_badge', name: 'tipe_fee' },
                { data: 'nominal_fmt', name: 'nominal_fee', className: 'tr' },
                { data: 'keterangan_fmt', name: 'keterangan' },
                { data: 'status_badge', name: 'is_aktif', className: 'tc' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'tc' },
            ],
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ – _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                paginate: { previous: '‹', next: '›' },
                zeroRecords: 'Tidak ada fee ditemukan',
                emptyTable: 'Belum ada fee enumerator dikonfigurasi',
                processing: '<div class="spinner-border text-primary" role="status"></div>',
            },
            pageLength: 15,
            order: [[1, 'asc']],
            responsive: true,
        });
    });

    async function toggleAktifFee(hashedId) {
        try {
            const response = await fetch(`{{ url($routePrefix . '/fee-enumerator') }}/${hashedId}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Gagal mengubah status');

            Swal.fire({
                toast: true, position: 'top-end', icon: 'success',
                title: data.message || 'Status berhasil diubah!',
                showConfirmButton: false, timer: 2500, timerProgressBar: true,
            });

            window.dataTableInstance.ajax.reload(null, false);
        } catch (err) {
            Swal.fire({
                toast: true, position: 'top-end', icon: 'error',
                title: err.message || 'Gagal mengubah status',
                showConfirmButton: false, timer: 3000,
            });
        }
    }

    function confirmDeleteFee(hashedId, label) {
        Swal.fire({
            title: 'Hapus Fee?',
            html: `Konfigurasi fee <strong>${label}</strong> akan dihapus permanen.`,
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
                const response = await fetch(`{{ url($routePrefix . '/fee-enumerator') }}/${hashedId}`, {
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
                    title: data.message || 'Fee berhasil dihapus!',
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
