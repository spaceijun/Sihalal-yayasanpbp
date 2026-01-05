<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal fade" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="las la-exclamation-triangle text-danger"></i> Konfirmasi Hapus Data
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="las la-trash-alt text-danger" style="font-size: 3.5rem;"></i>
                </div>
                <h5 class="fs-15 text-center mb-2">
                    Apakah Anda yakin ingin menghapus data enumerator ini?
                </h5>
                <p class="text-muted text-center mb-0">
                    Data yang telah dihapus tidak dapat dikembalikan lagi. Pastikan Anda benar-benar yakin sebelum
                    melanjutkan proses penghapusan.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="las la-times"></i> Batal
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="las la-trash"></i> Hapus Data
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
