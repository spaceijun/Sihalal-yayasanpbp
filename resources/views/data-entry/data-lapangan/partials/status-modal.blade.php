<div id="modalUpdateStatusHalal" class="modal fade" tabindex="-1" aria-labelledby="modalUpdateStatusHalalLabel"
    aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUpdateStatusHalalLabel">Update Status ke Progress SIHALAL</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('data-entry.datalapangan.update-status', $dataLapangan->hashed_id) }}"
                method="POST">
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

                    {{-- Hint email lama jika ada --}}
                    @if ($dataLapangan->old_email_sihalal)
                        <div class="alert alert-warning py-2 px-3 mb-2" style="font-size:12.5px;">
                            <i class="las la-history me-1"></i>
                            Email SIHALAL sebelumnya: <strong>{{ $dataLapangan->old_email_sihalal }}</strong>
                        </div>
                    @endif

                    {{-- Field Email SIHALAL --}}
                    <div class="mb-3">
                        <label for="email_sihalal" class="form-label fw-semibold">
                            Email SIHALAL <span class="text-danger">*</span>
                        </label>
                        <input type="email" class="form-control @error('email_sihalal') is-invalid @enderror"
                            id="email_sihalal" name="email_sihalal" value="{{ old('email_sihalal') }}"
                            placeholder="Masukkan email yang didaftarkan di SIHALAL" required>
                        @error('email_sihalal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Email yang digunakan untuk login di website SIHALAL.</small>
                    </div>

                    {{-- Field Pengajuan Lewat --}}
                    <div class="mb-3">
                        <label for="pengajuan_lewat" class="form-label fw-semibold">
                            Pengajuan Lewat <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('pengajuan_lewat') is-invalid @enderror" id="pengajuan_lewat"
                            name="pengajuan_lewat" required>
                            <option value="" disabled {{ old('pengajuan_lewat') ? '' : 'selected' }}>-- Pilih
                                Platform Pengajuan --</option>
                            <option value="PTSP HALAL" {{ old('pengajuan_lewat') === 'PTSP HALAL' ? 'selected' : '' }}>
                                PTSP HALAL</option>
                            <option value="HALALMAX" {{ old('pengajuan_lewat') === 'HALALMAX' ? 'selected' : '' }}>
                                HALALMAX</option>
                        </select>
                        @error('pengajuan_lewat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Platform yang digunakan untuk mengajukan data SIHALAL.</small>
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
                        <i class="las la-arrow-right me-2"></i>Update ke Progress SIHALAL
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
