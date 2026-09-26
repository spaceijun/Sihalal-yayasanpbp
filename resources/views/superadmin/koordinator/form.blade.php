{{-- ══════════════════════════════════════ SECTION 1: AKUN LOGIN ══════════════════════════════════════ --}}
<div class="adm-form-section">
    <div class="adm-form-section-header">
        <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        Akun Login
    </div>
    <div class="adm-form-body">
        <div class="adm-form-grid cols-2">
            <div class="adm-field">
                <label class="adm-label" for="email">Email <span class="req">*</span></label>
                <input type="email" name="email" id="email" class="adm-input @error('email') is-invalid @enderror"
                    value="{{ old('email', $koordinator?->email) }}" placeholder="email@domain.com">
                @error('email')
                    <span class="adm-error-msg">{{ $message }}</span>
                @enderror
            </div>

            <div class="adm-field">
                <label class="adm-label" for="password">
                    Password
                    @unless($koordinator?->exists)
                        <span class="req">*</span>
                    @endunless
                </label>
                <input type="password" name="password" id="password" class="adm-input @error('password') is-invalid @enderror"
                    placeholder="{{ $koordinator?->exists ? 'Kosongkan jika tidak ingin mengubah password' : 'Minimal 8 karakter' }}">
                @if ($koordinator?->exists)
                    <span class="adm-hint">Kosongkan jika tidak ingin mengubah password</span>
                @else
                    <span class="adm-hint" style="color:var(--adm-red);">* Wajib diisi, minimal 8 karakter</span>
                @endif
                @error('password')
                    <span class="adm-error-msg">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════ SECTION 2: DATA PRIBADI ══════════════════════════════════════ --}}
<div class="adm-form-section">
    <div class="adm-form-section-header">
        <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Data Pribadi
    </div>
    <div class="adm-form-body">
        <div class="adm-form-grid cols-2">
            <div class="adm-field">
                <label class="adm-label" for="foto_ktp">
                    Foto KTP
                    @unless($koordinator?->exists)
                        <span class="req">*</span>
                    @endunless
                </label>
                <div class="adm-upload-zone" onclick="document.getElementById('foto_ktp').click()">
                    <img src="{{ $koordinator?->foto_ktp ? \Illuminate\Support\Facades\Storage::url($koordinator->foto_ktp) : '' }}"
                        id="foto_ktp-preview" class="adm-upload-img {{ $koordinator?->foto_ktp ? 'visible' : '' }}" alt="Preview Foto KTP">
                    <div class="adm-upload-icon {{ $koordinator?->foto_ktp ? 'hidden' : '' }}" id="foto_ktp-icon">
                        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    </div>
                    <span class="adm-upload-label">Klik untuk unggah foto KTP</span>
                    <span class="adm-upload-sub">JPG, PNG · Maks. 2MB</span>
                    <input type="file" id="foto_ktp" name="foto_ktp" style="display:none;" accept="image/*"
                        onchange="previewImg(this,'foto_ktp-preview','foto_ktp-icon')">
                </div>
                @error('foto_ktp')
                    <span class="adm-error-msg">{{ $message }}</span>
                @enderror
            </div>

            <div class="adm-field">
                <label class="adm-label" for="foto_formal">
                    Foto Formal
                    @unless($koordinator?->exists)
                        <span class="req">*</span>
                    @endunless
                </label>
                <div class="adm-upload-zone" onclick="document.getElementById('foto_formal').click()">
                    <img src="{{ $koordinator?->foto_formal ? \Illuminate\Support\Facades\Storage::url($koordinator->foto_formal) : '' }}"
                        id="foto_formal-preview" class="adm-upload-img {{ $koordinator?->foto_formal ? 'visible' : '' }}" alt="Preview Foto Formal">
                    <div class="adm-upload-icon {{ $koordinator?->foto_formal ? 'hidden' : '' }}" id="foto_formal-icon">
                        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    </div>
                    <span class="adm-upload-label">Klik untuk unggah foto formal</span>
                    <span class="adm-upload-sub">JPG, PNG · Maks. 2MB</span>
                    <input type="file" id="foto_formal" name="foto_formal" style="display:none;" accept="image/*"
                        onchange="previewImg(this,'foto_formal-preview','foto_formal-icon')">
                </div>
                @error('foto_formal')
                    <span class="adm-error-msg">{{ $message }}</span>
                @enderror
            </div>

            <div class="adm-field">
                <label class="adm-label" for="nama_lengkap">Nama Lengkap <span class="req">*</span></label>
                <input type="text" name="nama_lengkap" id="nama_lengkap"
                    class="adm-input @error('nama_lengkap') is-invalid @enderror"
                    value="{{ old('nama_lengkap', $koordinator?->nama_lengkap) }}" placeholder="Nama lengkap koordinator">
                @error('nama_lengkap')
                    <span class="adm-error-msg">{{ $message }}</span>
                @enderror
            </div>

            <div class="adm-field">
                <label class="adm-label" for="telephone">Telephone <span class="req">*</span></label>
                <input type="text" name="telephone" id="telephone" class="adm-input @error('telephone') is-invalid @enderror"
                    value="{{ old('telephone', $koordinator?->telephone) }}" placeholder="08xxxxxxxxxx">
                @error('telephone')
                    <span class="adm-error-msg">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════ SECTION 3: ALAMAT SESUAI KTP ══════════════════════════════════════ --}}
<div class="adm-form-section">
    <div class="adm-form-section-header">
        <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        Alamat Sesuai KTP
    </div>
    <div class="adm-form-body">
        <div class="adm-form-grid cols-3">
            <div class="adm-field">
                <label class="adm-label" for="provinsi_ktp">Provinsi <span class="req">*</span></label>
                <select id="provinsi_ktp" name="provinsi_ktp" class="adm-field-select @error('provinsi_ktp') is-invalid @enderror" data-selected="{{ old('provinsi_ktp', $koordinator?->provinsi_ktp) }}">
                    <option value="">-- Pilih Provinsi --</option>
                </select>
                @error('provinsi_ktp')
                    <span class="adm-error-msg">{{ $message }}</span>
                @enderror
            </div>
            <div class="adm-field">
                <label class="adm-label" for="kabupaten_ktp">Kabupaten/Kota <span class="req">*</span></label>
                <select id="kabupaten_ktp" name="kabupaten_ktp" class="adm-field-select @error('kabupaten_ktp') is-invalid @enderror" data-selected="{{ old('kabupaten_ktp', $koordinator?->kabupaten_ktp) }}" disabled>
                    <option value="">-- Pilih Provinsi Dulu --</option>
                </select>
                @error('kabupaten_ktp')
                    <span class="adm-error-msg">{{ $message }}</span>
                @enderror
            </div>
            <div class="adm-field">
                <label class="adm-label" for="kecamatan_ktp">Kecamatan <span class="req">*</span></label>
                <select id="kecamatan_ktp" name="kecamatan_ktp" class="adm-field-select @error('kecamatan_ktp') is-invalid @enderror" data-selected="{{ old('kecamatan_ktp', $koordinator?->kecamatan_ktp) }}" disabled>
                    <option value="">-- Pilih Kabupaten Dulu --</option>
                </select>
                @error('kecamatan_ktp')
                    <span class="adm-error-msg">{{ $message }}</span>
                @enderror
            </div>
            <div class="adm-field">
                <label class="adm-label" for="desa_ktp">Desa/Kelurahan <span class="req">*</span></label>
                <select id="desa_ktp" name="desa_ktp" class="adm-field-select @error('desa_ktp') is-invalid @enderror" data-selected="{{ old('desa_ktp', $koordinator?->desa_ktp) }}" disabled>
                    <option value="">-- Pilih Kecamatan Dulu --</option>
                </select>
                @error('desa_ktp')
                    <span class="adm-error-msg">{{ $message }}</span>
                @enderror
            </div>
            <div class="adm-field">
                <label class="adm-label" for="rt_ktp">RT <span class="req">*</span></label>
                <input type="text" name="rt_ktp" id="rt_ktp" class="adm-input adm-mono @error('rt_ktp') is-invalid @enderror"
                    value="{{ old('rt_ktp', $koordinator?->rt_ktp) }}" placeholder="001" maxlength="5">
                @error('rt_ktp')
                    <span class="adm-error-msg">{{ $message }}</span>
                @enderror
            </div>
            <div class="adm-field">
                <label class="adm-label" for="rw_ktp">RW <span class="req">*</span></label>
                <input type="text" name="rw_ktp" id="rw_ktp" class="adm-input adm-mono @error('rw_ktp') is-invalid @enderror"
                    value="{{ old('rw_ktp', $koordinator?->rw_ktp) }}" placeholder="001" maxlength="5">
                @error('rw_ktp')
                    <span class="adm-error-msg">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="adm-form-grid cols-1" style="margin-top:14px;">
            <div class="adm-field">
                <label class="adm-label" for="alamat_lengkap_ktp">Alamat Lengkap <span class="req">*</span></label>
                <textarea name="alamat_lengkap_ktp" id="alamat_lengkap_ktp" class="adm-textarea @error('alamat_lengkap_ktp') is-invalid @enderror"
                    rows="2" placeholder="Nama jalan, nomor rumah, dll.">{{ old('alamat_lengkap_ktp', $koordinator?->alamat_lengkap_ktp) }}</textarea>
                @error('alamat_lengkap_ktp')
                    <span class="adm-error-msg">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════ SECTION 4: WILAYAH KERJA ══════════════════════════════════════ --}}
<div class="adm-form-section">
    <div class="adm-form-section-header">
        <svg viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        Wilayah Kerja
    </div>
    <div class="adm-form-body">
        <div class="adm-form-grid cols-3">
            <div class="adm-field">
                <label class="adm-label" for="tipe_wilayah_kerja">Tipe Wilayah Kerja <span class="req">*</span></label>
                <select name="tipe_wilayah_kerja" id="tipe_wilayah_kerja" class="adm-field-select @error('tipe_wilayah_kerja') is-invalid @enderror">
                    <option value="">-- Pilih Tipe --</option>
                    <option value="Provinsi" {{ old('tipe_wilayah_kerja', $koordinator?->tipe_wilayah_kerja) == 'Provinsi' ? 'selected' : '' }}>Provinsi (seluruh provinsi)</option>
                    <option value="Kabupaten" {{ old('tipe_wilayah_kerja', $koordinator?->tipe_wilayah_kerja) == 'Kabupaten' ? 'selected' : '' }}>Kabupaten (spesifik satu kabupaten)</option>
                </select>
                @error('tipe_wilayah_kerja')
                    <span class="adm-error-msg">{{ $message }}</span>
                @enderror
            </div>
            <div class="adm-field">
                <label class="adm-label" for="provinsi_kerja">Provinsi Kerja <span class="req">*</span></label>
                <select id="provinsi_kerja" name="provinsi_kerja" class="adm-field-select @error('provinsi_kerja') is-invalid @enderror" data-selected="{{ old('provinsi_kerja', $koordinator?->provinsi_kerja) }}">
                    <option value="">-- Pilih Provinsi --</option>
                </select>
                @error('provinsi_kerja')
                    <span class="adm-error-msg">{{ $message }}</span>
                @enderror
            </div>
            <div class="adm-field" id="kabupaten_kerja_wrap" style="{{ old('tipe_wilayah_kerja', $koordinator?->tipe_wilayah_kerja) === 'Kabupaten' ? '' : 'display:none;' }}">
                <label class="adm-label" for="kabupaten_kerja">Kabupaten Kerja <span class="req">*</span></label>
                <select id="kabupaten_kerja" name="kabupaten_kerja" class="adm-field-select @error('kabupaten_kerja') is-invalid @enderror" data-selected="{{ old('kabupaten_kerja', $koordinator?->kabupaten_kerja) }}" disabled>
                    <option value="">-- Pilih Provinsi Kerja Dulu --</option>
                </select>
                @error('kabupaten_kerja')
                    <span class="adm-error-msg">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════ SECTION 5: INFORMASI LAINNYA ══════════════════════════════════════ --}}
<div class="adm-form-section">
    <div class="adm-form-section-header">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        Informasi Lainnya
    </div>
    <div class="adm-form-body">
        <div class="adm-form-grid cols-2">
            <div class="adm-field">
                <label class="adm-label" for="tanggal_mulai">Tanggal Mulai <span class="req">*</span></label>
                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="adm-input @error('tanggal_mulai') is-invalid @enderror"
                    value="{{ old('tanggal_mulai', optional($koordinator?->tanggal_mulai)->format('Y-m-d')) }}">
                @error('tanggal_mulai')
                    <span class="adm-error-msg">{{ $message }}</span>
                @enderror
            </div>
            <div class="adm-field">
                <label class="adm-label" for="status">Status <span class="req">*</span></label>
                <select name="status" id="status" class="adm-field-select @error('status') is-invalid @enderror">
                    <option value="">-- Pilih Status --</option>
                    <option value="Aktif" {{ old('status', $koordinator?->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Tidak Aktif" {{ old('status', $koordinator?->status) == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    <option value="Blacklist" {{ old('status', $koordinator?->status) == 'Blacklist' ? 'selected' : '' }}>Blacklist</option>
                </select>
                @error('status')
                    <span class="adm-error-msg">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .adm-upload-zone { border: 2px dashed var(--adm-border-mid); border-radius: var(--adm-radius-sm); background: var(--adm-bg-input); padding: 18px 14px; text-align: center; cursor: pointer; transition: border-color .18s, background .18s; }
    .adm-upload-zone:hover { border-color: var(--adm-blue); background: var(--adm-blue-lt); }
    .adm-upload-img { width: 96px; height: 96px; object-fit: cover; border-radius: 8px; margin: 0 auto 8px; display: none; }
    .adm-upload-img.visible { display: block; }
    .adm-upload-icon { margin-bottom: 8px; display: flex; justify-content: center; }
    .adm-upload-icon svg { width: 28px; height: 28px; stroke: var(--adm-text-faint); fill: none; stroke-width: 1.5; }
    .adm-upload-icon.hidden { display: none; }
    .adm-upload-label { font-size: 12.5px; font-weight: 600; color: var(--adm-text-mid); display: block; margin-bottom: 2px; }
    .adm-upload-sub { font-size: 11px; color: var(--adm-text-faint); }
</style>
@endpush

@push('scripts')
<script>
    /* ── Preview foto upload ── */
    function previewImg(input, previewId, iconId) {
        if (!input.files || !input.files[0]) return;
        const r = new FileReader();
        r.onload = e => {
            const img = document.getElementById(previewId);
            const icon = document.getElementById(iconId);
            img.src = e.target.result;
            img.classList.add('visible');
            if (icon) icon.classList.add('hidden');
        };
        r.readAsDataURL(input.files[0]);
    }

    /* ── Cascading Wilayah (Provinsi -> Kabupaten -> Kecamatan -> Desa) ── */
    (function () {
        const wilayahCache = { provinces: null, regencies: {}, districts: {}, villages: {} };

        async function fetchRegion(url, cacheBucket, cacheKey) {
            if (cacheKey !== undefined && cacheBucket[cacheKey]) return cacheBucket[cacheKey];
            try {
                const res = await fetch(url);
                const json = await res.json();
                const data = json.success ? json.data : [];
                if (cacheKey !== undefined) cacheBucket[cacheKey] = data;
                return data;
            } catch (e) {
                console.error('Gagal memuat data wilayah:', e);
                return [];
            }
        }

        function populateSelect(selectId, data, placeholder) {
            const select = document.getElementById(selectId);
            if (!select) return;
            select.innerHTML = `<option value="">${placeholder}</option>`;
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.code;
                option.setAttribute('data-name', item.name);
                option.textContent = item.name;
                select.appendChild(option);
            });
            select.dispatchEvent(new Event('wilayah:loaded'));
        }

        function resetSelect(selectId, placeholder) {
            const select = document.getElementById(selectId);
            if (!select) return;
            select.innerHTML = `<option value="">${placeholder}</option>`;
            select.disabled = true;
        }

        function selectWilayahByName(selectId, name, callback) {
            const select = document.getElementById(selectId);
            if (!select || !name) { callback(); return; }

            function tryMatch() {
                const options = select.options;
                const nameLower = name.toLowerCase().trim();
                for (let i = 0; i < options.length; i++) {
                    const optText = (options[i].text || '').toLowerCase().trim();
                    if (optText === nameLower || optText.includes(nameLower) || nameLower.includes(optText)) {
                        select.value = options[i].value;
                        select.dispatchEvent(new Event('change'));
                        setTimeout(callback, 500);
                        return true;
                    }
                }
                return false;
            }

            if (select.options.length > 1) {
                if (!tryMatch()) callback();
            } else {
                select.addEventListener('wilayah:loaded', function handler() {
                    select.removeEventListener('wilayah:loaded', handler);
                    if (!tryMatch()) callback();
                }, { once: true });
            }
        }

        async function loadProvinces(selectId) {
            const data = await fetchRegion('/api/wilayah/provinces', wilayahCache, 'provinces');
            populateSelect(selectId, data, '-- Pilih Provinsi --');
        }

        async function loadRegencies(provinceCode, selectId, downstreamIds) {
            if (!provinceCode) return;
            const data = await fetchRegion(`/api/wilayah/regencies?code=${provinceCode}`, wilayahCache.regencies, provinceCode);
            populateSelect(selectId, data, '-- Pilih Kabupaten/Kota --');
            document.getElementById(selectId).disabled = false;
            downstreamIds.forEach(id => resetSelect(id, '-- Pilih Kabupaten Dulu --'));
        }

        async function loadDistricts(regencyCode, selectId, downstreamIds) {
            if (!regencyCode) return;
            const data = await fetchRegion(`/api/wilayah/districts?code=${regencyCode}`, wilayahCache.districts, regencyCode);
            populateSelect(selectId, data, '-- Pilih Kecamatan --');
            document.getElementById(selectId).disabled = false;
            downstreamIds.forEach(id => resetSelect(id, '-- Pilih Kecamatan Dulu --'));
        }

        async function loadVillages(districtCode, selectId) {
            if (!districtCode) return;
            const data = await fetchRegion(`/api/wilayah/villages?code=${districtCode}`, wilayahCache.villages, districtCode);
            populateSelect(selectId, data, '-- Pilih Desa/Kelurahan --');
            document.getElementById(selectId).disabled = false;
        }

        function initKtpCascade() {
            const provinsiSel = document.getElementById('provinsi_ktp');
            if (!provinsiSel) return;

            loadProvinces('provinsi_ktp').then(() => {
                const savedProvinsi = provinsiSel.dataset.selected;
                if (!savedProvinsi) return;
                selectWilayahByName('provinsi_ktp', savedProvinsi, () => {
                    const savedKabupaten = document.getElementById('kabupaten_ktp').dataset.selected;
                    selectWilayahByName('kabupaten_ktp', savedKabupaten, () => {
                        const savedKecamatan = document.getElementById('kecamatan_ktp').dataset.selected;
                        selectWilayahByName('kecamatan_ktp', savedKecamatan, () => {
                            const savedDesa = document.getElementById('desa_ktp').dataset.selected;
                            selectWilayahByName('desa_ktp', savedDesa, () => {});
                        });
                    });
                });
            });

            provinsiSel.addEventListener('change', function () {
                loadRegencies(this.value, 'kabupaten_ktp', ['kecamatan_ktp', 'desa_ktp']);
            });
            document.getElementById('kabupaten_ktp').addEventListener('change', function () {
                loadDistricts(this.value, 'kecamatan_ktp', ['desa_ktp']);
            });
            document.getElementById('kecamatan_ktp').addEventListener('change', function () {
                loadVillages(this.value, 'desa_ktp');
            });
        }

        function initWilayahKerjaCascade() {
            const provinsiKerjaSel = document.getElementById('provinsi_kerja');
            if (!provinsiKerjaSel) return;

            loadProvinces('provinsi_kerja').then(() => {
                const saved = provinsiKerjaSel.dataset.selected;
                if (!saved) return;
                selectWilayahByName('provinsi_kerja', saved, () => {
                    const savedKab = document.getElementById('kabupaten_kerja').dataset.selected;
                    if (savedKab) selectWilayahByName('kabupaten_kerja', savedKab, () => {});
                });
            });

            provinsiKerjaSel.addEventListener('change', function () {
                loadRegencies(this.value, 'kabupaten_kerja', []);
            });
        }

        function initTipeWilayahToggle() {
            const tipeSel = document.getElementById('tipe_wilayah_kerja');
            const kabWrap = document.getElementById('kabupaten_kerja_wrap');
            if (!tipeSel || !kabWrap) return;

            tipeSel.addEventListener('change', function () {
                kabWrap.style.display = this.value === 'Kabupaten' ? '' : 'none';
                if (this.value !== 'Kabupaten') {
                    document.getElementById('kabupaten_kerja').value = '';
                }
            });
        }

        function initRtRwPad() {
            ['rt_ktp', 'rw_ktp'].forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;
                el.addEventListener('blur', function () {
                    const v = this.value.replace(/\D/g, '');
                    this.value = v ? v.padStart(3, '0').substring(0, 5) : '';
                });
            });
        }

        function convertWilayahValuesToNamesBeforeSubmit(formId) {
            const form = document.getElementById(formId);
            if (!form) return;
            form.addEventListener('submit', function () {
                ['provinsi_ktp', 'kabupaten_ktp', 'kecamatan_ktp', 'desa_ktp', 'provinsi_kerja', 'kabupaten_kerja'].forEach(function (id) {
                    const sel = document.getElementById(id);
                    if (!sel || !sel.value) return;
                    const selectedOpt = sel.options[sel.selectedIndex];
                    const textName = selectedOpt
                        ? (selectedOpt.getAttribute('data-name') || selectedOpt.textContent)
                        : '';
                    if (textName && textName.trim()) {
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = id;
                        hidden.value = textName.trim();
                        sel.parentNode.appendChild(hidden);
                        sel.removeAttribute('name'); // cegah duplikasi saat submit
                    }
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            initKtpCascade();
            initWilayahKerjaCascade();
            initTipeWilayahToggle();
            initRtRwPad();
            convertWilayahValuesToNamesBeforeSubmit('formKoordinator');
        });
    })();
</script>
@endpush
