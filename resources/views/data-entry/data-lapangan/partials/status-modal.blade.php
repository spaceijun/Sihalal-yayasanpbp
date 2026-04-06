<div id="modalUpdateStatusHalal" class="modal fade" tabindex="-1" aria-labelledby="modalUpdateStatusHalalLabel"
    aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUpdateStatusHalalLabel">Update Status ke Progress SIHALAL</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('data-entry.datalapangan.update-status', $dataLapangan->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    {{-- Danger Alert --}}
                    <div class="alert alert-danger alert-dismissible bg-danger text-white alert-label-icon fade show material-shadow"
                        role="alert">
                        <i class="ri-error-warning-line label-icon"></i>
                        <strong>PERHATIKAN!</strong> - Status akan diupdate dari
                        <strong>PROGRESS OSS</strong> menjadi <strong>PROGRESS SIHALAL</strong>
                    </div>

                    {{-- Pernyataan Pemohon --}}
                    <div class="border rounded p-3 bg-light">
                        <p class="text-muted small fw-semibold text-uppercase mb-2">Pernyataan Pemohon</p>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="checkPernyataanSihalal"
                                onchange="toggleSubmitSihalal(this)" required>
                            <label class="form-check-label" for="checkPernyataanSihalal">
                                Saya sudah melakukan pengajuan data pada website SIHALAL dengan benar.
                                Jika ada revisi data di kemudian hari, saya sanggup untuk merevisi data.
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSubmitSihalal" class="btn btn-primary" disabled>
                        <i class="fas fa-arrow-right me-2"></i>Update ke Progress SIHALAL
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleSubmitSihalal(checkbox) {
        const btn = document.getElementById('btnSubmitSihalal');
        btn.disabled = !checkbox.checked;
    }
</script>
