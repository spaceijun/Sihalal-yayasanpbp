@extends('layouts.app')
@section('template_title')
    Data Revisi
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                @include('layouts.messages')

                <!-- Alert Container untuk notifikasi -->
                <div id="alertContainer"></div>

                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">
                                {{ __('Data Revisi') }}
                            </span>
                            <div class="float-right">
                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#sendAllModal">
                                    <i class="las la-paper-plane"></i> Kirim Semua Notifikasi
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>
                                        <th>Created</th>
                                        <th>Nama Pendamping</th>
                                        <th>Nama PU</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($dataLapangans as $dataLapangan)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $dataLapangan->created_at }}</td>
                                            <td>{{ $dataLapangan->enumerator->nama_lengkap }}</td>
                                            <td>{{ $dataLapangan->nama_pu }}</td>
                                            <td><span class="badge bg-danger">{{ $dataLapangan->status }}</span></td>
                                            <td>{{ $dataLapangan->keterangan }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary send-notification-btn"
                                                    data-bs-toggle="modal" data-bs-target="#sendModal"
                                                    data-id="{{ $dataLapangan->id }}"
                                                    data-nama="{{ $dataLapangan->nama_pu }}">
                                                    <i class="las la-paper-plane"></i> Kirim WA
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="las la-inbox la-3x mb-2"></i>
                                                    <p class="mb-0">{{ __('No data available') }}</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @include('layouts.pagination', ['paginator' => $dataLapangans])
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Kirim Notifikasi Single -->
    <div id="sendModal" class="modal fade" tabindex="-1" aria-labelledby="sendModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendModalLabel">Konfirmasi Pengiriman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Apakah Anda yakin ingin mengirim notifikasi revisi ke <strong
                            id="namaPU"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="confirmSendBtn">
                        <i class="las la-paper-plane"></i> Kirim Notifikasi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Kirim Semua Notifikasi -->
    <div id="sendAllModal" class="modal fade" tabindex="-1" aria-labelledby="sendAllModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendAllModalLabel">Konfirmasi Pengiriman Massal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Apakah Anda yakin ingin mengirim notifikasi ke <strong>semua pendamping</strong>?
                    </p>
                    <p class="text-muted"><i class="las la-info-circle"></i> Proses ini mungkin memakan waktu beberapa
                        menit.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="confirmSendAllBtn">
                        <i class="las la-paper-plane"></i> Kirim Semua
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let currentId = null;
            let currentNama = null;

            // Fungsi untuk menampilkan alert Bootstrap
            function showAlert(message, type = 'success') {
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const iconClass = type === 'success' ? 'la-check-circle' : 'la-exclamation-circle';

                const alertHtml = `
                    <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                        <i class="las ${iconClass} me-2"></i>
                        <strong>${type === 'success' ? 'Sukses!' : 'Error!'}</strong> ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;

                $('#alertContainer').html(alertHtml);

                // Scroll ke atas untuk melihat alert
                $('html, body').animate({
                    scrollTop: 0
                }, 'fast');
            }

            // Set data untuk modal single notification
            $('.send-notification-btn').on('click', function() {
                currentId = $(this).data('id');
                currentNama = $(this).data('nama');
                $('#namaPU').text(currentNama);
            });

            // Kirim notifikasi per data
            $('#confirmSendBtn').on('click', function() {
                const button = $(this);
                button.prop('disabled', true).html('<i class="las la-spinner la-spin"></i> Mengirim...');

                $.ajax({
                    url: `/superadmin/data-revisi/${currentId}/send-notification`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#sendModal').modal('hide');
                        if (response.success) {
                            showAlert(response.message, 'success');
                        } else {
                            showAlert(response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        $('#sendModal').modal('hide');
                        const message = xhr.responseJSON?.message || 'Terjadi kesalahan';
                        showAlert(message, 'error');
                    },
                    complete: function() {
                        button.prop('disabled', false).html(
                            '<i class="las la-paper-plane"></i> Kirim Notifikasi');
                    }
                });
            });

            // Kirim semua notifikasi
            $('#confirmSendAllBtn').on('click', function() {
                const button = $(this);
                button.prop('disabled', true).html('<i class="las la-spinner la-spin"></i> Mengirim...');

                $.ajax({
                    url: '/superadmin/data-revisi/send-all-notifications',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#sendAllModal').modal('hide');
                        if (response.success) {
                            let message = response.message;
                            if (response.data && response.data.failed > 0) {
                                message += '<br><small>Gagal kirim ke: ' + response.data
                                    .failed_data.join(', ') + '</small>';
                            }
                            showAlert(message, 'success');
                        } else {
                            showAlert(response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        $('#sendAllModal').modal('hide');
                        const message = xhr.responseJSON?.message || 'Terjadi kesalahan';
                        showAlert(message, 'error');
                    },
                    complete: function() {
                        button.prop('disabled', false).html(
                            '<i class="las la-paper-plane"></i> Kirim Semua');
                    }
                });
            });
        });
    </script>
@endsection
