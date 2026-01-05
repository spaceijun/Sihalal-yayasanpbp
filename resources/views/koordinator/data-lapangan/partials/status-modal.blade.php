<!-- File: resources/views/koordinator/data-lapangan/partials/update-status-modal.blade.php -->

<div id="modalUpdateStatusHalal" class="modal fade" tabindex="-1" aria-labelledby="modalUpdateStatusHalalLabel"
    aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUpdateStatusHalalLabel">Update Status ke Progress SIHALAL</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('koordinator.datalapangan.update-status', $dataLapangan->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Danger Alert -->
                    <div class="alert alert-danger alert-dismissible bg-danger text-white alert-label-icon fade show material-shadow"
                        role="alert">
                        <i class="ri-error-warning-line label-icon"></i><strong>PERHATIKAN!</strong> - Status akan
                        diupdate dari <strong>PROGRESS OSS</strong> menjadi <strong>PROGRESS
                            SIHALAL</strong>
                    </div>

                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan <span
                                class="text-muted">(Opsional)</span></label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3"
                            placeholder="Masukkan keterangan tambahan jika diperlukan..."></textarea>
                        <small class="text-muted">Catatan: Keterangan ini akan disimpan sebagai histori perubahan
                            status.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-arrow-right me-2"></i>Update ke Progress SIHALAL
                    </button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
