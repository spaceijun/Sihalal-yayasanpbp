@extends('layouts.app')
@section('template_title')
    Verifikator Payment
@endsection

@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Verifikator Payment</h1>
                <p>Kelola data pembayaran verifikator</p>
            </div>
            <a href="{{ route('superadmin.verifikator-payments.create') }}" class="adm-btn-primary">
                <svg viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Tambah Payment
            </a>
        </div>

        <div class="adm-card">
            <div class="adm-card-header">
                <div class="adm-card-title">
                    <svg viewBox="0 0 24 24">
                        <rect x="2" y="5" width="20" height="14" rx="2" />
                        <line x1="2" y1="10" x2="22" y2="10" />
                    </svg>
                    Daftar Verifikator Payment
                </div>
            </div>

            <div class="table-responsive">
                <table id="verifikatorPaymentTable" class="adm-table w-100">
                    <thead>
                        <tr>
                            <th style="width:44px">#</th>
                            <th>Verifikator ID</th>
                            <th class="tr">Jumlah Data</th>
                            <th class="tr">Total Nominal</th>
                            <th>Periode Dari</th>
                            <th>Periode Sampai</th>
                            <th>Paid At</th>
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
        document.addEventListener('DOMContentLoaded', function() {
            window.dataTableInstance = $('#verifikatorPaymentTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('superadmin.verifikator-payments.index') }}',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'tc' },
                    { data: 'verifikator_id', name: 'verifikator_id' },
                    { data: 'jumlah_data', name: 'jumlah_data', className: 'tr' },
                    { data: 'total_nominal', name: 'total_nominal', className: 'tr' },
                    { data: 'periode_dari', name: 'periode_dari' },
                    { data: 'periode_sampai', name: 'periode_sampai' },
                    { data: 'paid_at', name: 'paid_at' },
                    { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'tc' },
                ],
                columnDefs: [
                    { targets: [7], render: null }, // raw HTML column
                ],
                createdRow: function(row, data) {
                    $(row).find('td:eq(7)').html(data.aksi);
                },
                language: {
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ – _END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data',
                    infoFiltered: '(difilter dari _MAX_ total)',
                    paginate: {
                        previous: '‹',
                        next: '›'
                    },
                    zeroRecords: 'Data tidak ditemukan',
                    emptyTable: 'Belum ada data verifikator payment',
                    processing: '<div class="spinner-border text-primary" role="status"></div>',
                },
                pageLength: 15,
                order: [
                    [1, 'asc']
                ],
                responsive: true,
            });
        });

        function confirmDeleteVerifikatorPayment(id, label) {
            Swal.fire({
                title: 'Hapus Data?',
                html: `Data <strong>${label}</strong> akan dihapus permanen.`,
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
                    const response = await fetch(`{{ url('superadmin/verifikator-payments') }}/${id}`, {
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
                        title: data.message || 'Data berhasil dihapus!',
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
