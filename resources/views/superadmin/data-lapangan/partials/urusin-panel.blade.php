{{-- ══════════════════════════════════════════════════════════════════
     PANEL: Verifikasi Final & Integrasi Urusin Secara Online
     Lihat .agent/workflows/data-entry-integrasi.md
     ══════════════════════════════════════════════════════════════════ --}}
<div class="dl-card" style="margin-bottom:1.25rem;">
    <div class="dl-card-head">
        <div class="dl-card-head-left">
            <div class="dl-card-icon" style="background:#ECFDF5;">
                <i class="las la-store" style="color:#16A34A;"></i>
            </div>
            <span class="dl-card-title">Data Usaha untuk Pengajuan NIB &amp; Sertifikat Halal</span>
        </div>
        <span class="dl-badge {{ $dataLapangan->urusin_data_usaha_lengkap ? 'dl-badge-terbit' : 'dl-badge-pending' }}">
            {{ $dataLapangan->urusin_data_usaha_lengkap ? 'Lengkap' : 'Belum Lengkap' }}
        </span>
    </div>
    <div class="dl-card-body">
        <p style="font-size:12.5px;color:var(--dl-muted);margin:0 0 12px;">
            Field ini dibutuhkan untuk mengirim data ke Urusin Secara Online (belum ada di form
            input Enumerator) — wajib diisi sebelum verifikasi final bisa disetujui.
        </p>
        <form id="formDataUsaha" data-url="{{ route($routePrefix . '.data-lapangans.update-data-usaha', $dataLapangan->hashed_id) }}">
            @csrf
            <div class="row g-2">
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12px;font-weight:600;">Nama Usaha</label>
                    <input type="text" name="nama_usaha" class="form-control form-control-sm" value="{{ $dataLapangan->nama_usaha }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12px;font-weight:600;">Jenis Usaha</label>
                    <input type="text" name="jenis_usaha" class="form-control form-control-sm" value="{{ $dataLapangan->jenis_usaha }}" placeholder="Contoh: Perdagangan" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12px;font-weight:600;">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-control form-control-sm" value="{{ $dataLapangan->tempat_lahir }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12px;font-weight:600;">Modal Usaha (Rp)</label>
                    <input type="number" name="modal_usaha" class="form-control form-control-sm" value="{{ $dataLapangan->modal_usaha }}" min="0" required>
                </div>
                <div class="col-12">
                    <label class="form-label" style="font-size:12px;font-weight:600;">Alamat Usaha</label>
                    <input type="text" name="alamat_usaha" class="form-control form-control-sm" value="{{ $dataLapangan->alamat_usaha }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label" style="font-size:12px;font-weight:600;">Provinsi</label>
                    <select name="provinsi_kode" id="uwProvinsi" class="form-select form-select-sm" required
                        data-selected="{{ $dataLapangan->provinsi_kode }}">
                        <option value="">Pilih...</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size:12px;font-weight:600;">Kabupaten/Kota</label>
                    <select name="kabupaten_kode" id="uwKabupaten" class="form-select form-select-sm" required
                        data-selected="{{ $dataLapangan->kabupaten_kode }}" disabled>
                        <option value="">Pilih Provinsi dulu</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size:12px;font-weight:600;">Kecamatan</label>
                    <select name="kecamatan_kode" id="uwKecamatan" class="form-select form-select-sm" required
                        data-selected="{{ $dataLapangan->kecamatan_kode }}" disabled>
                        <option value="">Pilih Kabupaten dulu</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size:12px;font-weight:600;">Kelurahan/Desa</label>
                    <select name="kelurahan_kode" id="uwKelurahan" class="form-select form-select-sm" required
                        data-selected="{{ $dataLapangan->kelurahan_kode }}" disabled>
                        <option value="">Pilih Kecamatan dulu</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label" style="font-size:12px;font-weight:600;">Jenis Produk (Halal)</label>
                    <input type="text" name="jenis_produk_halal" class="form-control form-control-sm" value="{{ $dataLapangan->jenis_produk_halal }}" placeholder="Contoh: Makanan Ringan" required>
                    <span style="font-size:11px;color:var(--dl-muted);">Berlaku untuk semua produk yang terdaftar.</span>
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12px;font-weight:600;">Bahan Utama (Halal)</label>
                    <input type="text" name="bahan_utama_halal" class="form-control form-control-sm" value="{{ $dataLapangan->bahan_utama_halal }}" placeholder="Contoh: Tempe, Tepung" required>
                </div>
            </div>
            <div style="margin-top:12px;">
                <button type="submit" class="dl-btn dl-btn-primary dl-btn-sm">
                    <i class="las la-save"></i> Simpan Data Usaha
                </button>
            </div>
        </form>
    </div>
</div>

<div class="dl-card" style="margin-bottom:1.25rem;">
    <div class="dl-card-head">
        <div class="dl-card-head-left">
            <div class="dl-card-icon" style="background:#EEF4FF;">
                <i class="las la-check-double" style="color:var(--dl-blue);"></i>
            </div>
            <span class="dl-card-title">Verifikasi Final (Admin Umum/Superadmin)</span>
        </div>
        @php
            $vfMap = ['Terverifikasi' => 'dl-badge-terbit', 'Perlu Koreksi' => 'dl-badge-ditolak', 'Belum' => 'dl-badge-pending'];
        @endphp
        <span class="dl-badge {{ $vfMap[$dataLapangan->verifikasi_final] ?? 'dl-badge-pending' }}">
            {{ $dataLapangan->verifikasi_final }}
        </span>
    </div>
    <div class="dl-card-body">
        @if ($dataLapangan->verifikasi_koordinator !== 'Terverifikasi')
            <div style="display:flex;gap:10px;padding:12px 14px;background:#FFFBEB;border:1px solid #FDE68A;border-radius:10px;">
                <i class="las la-exclamation-triangle" style="color:#D97706;font-size:16px;flex-shrink:0;margin-top:2px;"></i>
                <div style="font-size:13px;color:#78350F;">Menunggu verifikasi Koordinator (tahap 1) terlebih dahulu — status saat ini: <strong>{{ $dataLapangan->verifikasi_koordinator }}</strong>.</div>
            </div>
        @else
            @if ($dataLapangan->catatan_final)
                <p style="font-size:13px;color:var(--dl-text);margin:0 0 8px;"><strong>Catatan:</strong> {{ $dataLapangan->catatan_final }}</p>
            @endif
            @if ($dataLapangan->verified_at_final)
                <p style="font-size:12px;color:var(--dl-muted);margin:0 0 12px;">
                    Diverifikasi oleh {{ $dataLapangan->verifiedByFinal->name ?? '-' }} pada
                    {{ $dataLapangan->verified_at_final->format('d M Y, H:i') }}
                </p>
            @endif
            <button type="button" class="dl-btn dl-btn-primary dl-btn-sm" data-bs-toggle="modal" data-bs-target="#modalVerifikasiFinal">
                <i class="las la-check-double"></i> {{ $dataLapangan->verifikasi_final === 'Belum' ? 'Verifikasi Final' : 'Ubah Verifikasi Final' }}
            </button>
        @endif
    </div>
</div>

@if ($dataLapangan->verifikasi_final === 'Terverifikasi')
<div class="dl-card" style="margin-bottom:1.25rem;">
    <div class="dl-card-head">
        <div class="dl-card-head-left">
            <div class="dl-card-icon" style="background:#F5F0FF;">
                <i class="las la-cloud-upload-alt" style="color:#7C3AED;"></i>
            </div>
            <span class="dl-card-title">Status Pengajuan — Urusin Secara Online</span>
        </div>
        @if ($dataLapangan->urusin_nib_status === 'gagal_kirim' || $dataLapangan->urusin_halal_status === 'gagal_kirim')
            <button type="button" class="dl-btn dl-btn-danger dl-btn-sm" id="btnRetryUrusin"
                data-url="{{ route($routePrefix . '.data-lapangans.retry-urusin', $dataLapangan->hashed_id) }}">
                <i class="las la-redo"></i> Kirim Ulang
            </button>
        @endif
    </div>
    <div class="dl-card-body">
        <div class="dl-entry-row">
            <div>
                <div class="dl-entry-name">NIB (OSS)</div>
                <div class="dl-entry-meta">
                    {{ $dataLapangan->urusin_nib_submission_id ?? 'Belum dikirim' }}
                </div>
            </div>
            <span class="dl-badge {{ $dataLapangan->urusin_nib_status === 'completed' ? 'dl-badge-terbit' : ($dataLapangan->urusin_nib_status === 'gagal_kirim' ? 'dl-badge-ditolak' : 'dl-badge-pending') }}">
                {{ $dataLapangan->urusin_nib_status ?? '—' }}
            </span>
        </div>
        @if ($dataLapangan->urusin_nib_document_path)
            <a href="{{ asset('storage/' . $dataLapangan->urusin_nib_document_path) }}" target="_blank" class="dl-btn dl-btn-success dl-btn-sm" style="margin:8px 0 4px;">
                <i class="las la-download"></i> Unduh Dokumen NIB
            </a>
        @endif
        @if ($dataLapangan->urusin_nib_status === 'gagal_kirim' && $dataLapangan->urusin_gagal_pesan)
            <div style="margin-top:8px;padding:8px 10px;background:#FEF2F2;border:1px solid #FECACA;border-radius:6px;font-size:11.5px;color:#7F1D1D;">
                <i class="las la-exclamation-circle"></i> {{ $dataLapangan->urusin_gagal_pesan }}
            </div>
        @endif

        <div style="height:1px;background:var(--dl-border);margin:1rem 0;"></div>

        <div class="dl-entry-row">
            <div>
                <div class="dl-entry-name">Sertifikat Halal</div>
                <div class="dl-entry-meta">
                    {{ $dataLapangan->urusin_halal_submission_id ?? 'Belum dikirim' }}
                </div>
            </div>
            <span class="dl-badge {{ $dataLapangan->urusin_halal_status === 'completed' ? 'dl-badge-terbit' : ($dataLapangan->urusin_halal_status === 'gagal_kirim' ? 'dl-badge-ditolak' : 'dl-badge-pending') }}">
                {{ $dataLapangan->urusin_halal_status ?? '—' }}
            </span>
        </div>
        @if ($dataLapangan->urusin_halal_document_path)
            <a href="{{ asset('storage/' . $dataLapangan->urusin_halal_document_path) }}" target="_blank" class="dl-btn dl-btn-success dl-btn-sm" style="margin:8px 0 4px;">
                <i class="las la-download"></i> Unduh Sertifikat Halal
            </a>
        @endif
        @if ($dataLapangan->urusin_halal_status === 'gagal_kirim' && $dataLapangan->urusin_gagal_pesan)
            <div style="margin-top:8px;padding:8px 10px;background:#FEF2F2;border:1px solid #FECACA;border-radius:6px;font-size:11.5px;color:#7F1D1D;">
                <i class="las la-exclamation-circle"></i> {{ $dataLapangan->urusin_gagal_pesan }}
            </div>
        @endif

        @if ($dataLapangan->urusin_last_synced_at)
            <p style="font-size:11px;color:var(--dl-muted);margin:10px 0 0;">Terakhir disinkronkan: {{ $dataLapangan->urusin_last_synced_at->format('d M Y, H:i') }}</p>
        @endif
    </div>
</div>
@endif

{{-- Modal Verifikasi Final --}}
<div class="modal fade dl-modal" id="modalVerifikasiFinal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="las la-check-double" style="color:var(--dl-blue);"></i> Verifikasi Final</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div style="display:flex;gap:10px;padding:12px 14px;background:#EFF6FF;border:1px solid #BFDBFE;border-radius:10px;margin-bottom:1rem;">
                    <i class="las la-info-circle" style="color:#2563EB;font-size:16px;flex-shrink:0;margin-top:2px;"></i>
                    <div style="font-size:12.5px;color:#1E40AF;">Jika disetujui, data akan langsung dikirim ke Urusin Secara Online (NIB &amp; Sertifikat Halal). Pastikan "Data Usaha untuk Pengajuan" sudah lengkap.</div>
                </div>
                <div style="margin-bottom:.85rem;">
                    <label class="form-label" style="font-size:12px;font-weight:600;">Keputusan</label>
                    <select id="vfKeputusan" class="form-select form-select-sm">
                        <option value="Terverifikasi">Terverifikasi — Kirim ke Urusin Secara Online</option>
                        <option value="Perlu Koreksi">Perlu Koreksi — Kembalikan</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" style="font-size:12px;font-weight:600;">Catatan (opsional)</label>
                    <textarea id="vfCatatan" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="dl-btn dl-btn-ghost" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="dl-btn dl-btn-primary" id="btnSimpanVerifikasiFinal"
                    data-url="{{ route($routePrefix . '.data-lapangans.verifikasi-final', $dataLapangan->hashed_id) }}">
                    <i class="las la-save"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const provinsiSel = document.getElementById('uwProvinsi');
    const kabupatenSel = document.getElementById('uwKabupaten');
    const kecamatanSel = document.getElementById('uwKecamatan');
    const kelurahanSel = document.getElementById('uwKelurahan');

    if (!provinsiSel) return; // panel tidak ada di halaman ini

    function fillSelect(select, items, selectedValue, placeholder) {
        select.innerHTML = '<option value="">' + placeholder + '</option>';
        items.forEach(function (item) {
            const opt = document.createElement('option');
            opt.value = item.code;
            opt.textContent = item.name;
            if (selectedValue && String(item.code) === String(selectedValue)) opt.selected = true;
            select.appendChild(opt);
        });
    }

    async function loadProvinces() {
        const res = await fetch('{{ route("api.wilayah.provinces") }}');
        const json = await res.json();
        fillSelect(provinsiSel, json.data || [], provinsiSel.dataset.selected, 'Pilih Provinsi...');
        if (provinsiSel.dataset.selected) await loadRegencies(provinsiSel.dataset.selected, kabupatenSel.dataset.selected);
    }

    async function loadRegencies(code, selected) {
        kabupatenSel.disabled = false;
        const res = await fetch('{{ route("api.wilayah.regencies") }}?code=' + encodeURIComponent(code));
        const json = await res.json();
        fillSelect(kabupatenSel, json.data || [], selected, 'Pilih Kabupaten/Kota...');
        if (selected) await loadDistricts(selected, kecamatanSel.dataset.selected);
    }

    async function loadDistricts(code, selected) {
        kecamatanSel.disabled = false;
        const res = await fetch('{{ route("api.wilayah.districts") }}?code=' + encodeURIComponent(code));
        const json = await res.json();
        fillSelect(kecamatanSel, json.data || [], selected, 'Pilih Kecamatan...');
        if (selected) await loadVillages(selected, kelurahanSel.dataset.selected);
    }

    async function loadVillages(code, selected) {
        kelurahanSel.disabled = false;
        const res = await fetch('{{ route("api.wilayah.villages") }}?code=' + encodeURIComponent(code));
        const json = await res.json();
        fillSelect(kelurahanSel, json.data || [], selected, 'Pilih Kelurahan/Desa...');
    }

    provinsiSel.addEventListener('change', function () {
        kabupatenSel.innerHTML = '<option value="">Pilih...</option>';
        kecamatanSel.innerHTML = '<option value="">Pilih Kabupaten dulu</option>';
        kelurahanSel.innerHTML = '<option value="">Pilih Kecamatan dulu</option>';
        kecamatanSel.disabled = true;
        kelurahanSel.disabled = true;
        if (this.value) loadRegencies(this.value, null);
    });
    kabupatenSel.addEventListener('change', function () {
        kecamatanSel.innerHTML = '<option value="">Pilih...</option>';
        kelurahanSel.innerHTML = '<option value="">Pilih Kecamatan dulu</option>';
        kelurahanSel.disabled = true;
        if (this.value) loadDistricts(this.value, null);
    });
    kecamatanSel.addEventListener('change', function () {
        kelurahanSel.innerHTML = '<option value="">Pilih...</option>';
        if (this.value) loadVillages(this.value, null);
    });

    loadProvinces();

    document.getElementById('formDataUsaha')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const form = this;
        const btn = form.querySelector('button[type=submit]');
        const originalHTML = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';

        const formData = new FormData(form);
        try {
            const response = await fetch(form.dataset.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData,
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Gagal menyimpan data usaha');

            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 2500, timerProgressBar: true });
            setTimeout(() => location.reload(), 900);
        } catch (err) {
            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: err.message, showConfirmButton: false, timer: 3000 });
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    });

    document.getElementById('btnSimpanVerifikasiFinal')?.addEventListener('click', async function () {
        const btn = this;
        const originalHTML = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';

        try {
            const response = await fetch(btn.dataset.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    verifikasi_final: document.getElementById('vfKeputusan').value,
                    catatan_final: document.getElementById('vfCatatan').value,
                }),
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Gagal menyimpan verifikasi final');

            bootstrap.Modal.getInstance(document.getElementById('modalVerifikasiFinal'))?.hide();
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 3000, timerProgressBar: true });
            setTimeout(() => location.reload(), 1200);
        } catch (err) {
            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: err.message, showConfirmButton: false, timer: 3000 });
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    });

    document.getElementById('btnRetryUrusin')?.addEventListener('click', async function () {
        const btn = this;
        const originalHTML = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Mengirim...';

        try {
            const response = await fetch(btn.dataset.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });
            const data = await response.json();

            Swal.fire({ toast: true, position: 'top-end', icon: data.success ? 'success' : 'error', title: data.message, showConfirmButton: false, timer: 3000, timerProgressBar: true });
            if (data.success) setTimeout(() => location.reload(), 1200);
        } catch (err) {
            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: err.message, showConfirmButton: false, timer: 3000 });
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    });
})();
</script>
@endpush
