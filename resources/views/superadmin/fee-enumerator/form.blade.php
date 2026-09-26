<div class="adm-form-section">
    <div class="adm-form-section-header">
        <svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        Konfigurasi Fee
    </div>
    <div class="adm-form-body">
        <div class="adm-form-grid cols-2">
            <div class="adm-field">
                <label class="adm-label" for="skala">Skala <span class="req">*</span></label>
                <select name="skala" id="skala" class="adm-field-select @error('skala') is-invalid @enderror">
                    <option value="">-- Pilih Skala --</option>
                    <option value="Global" {{ old('skala', $feeEnumerator?->skala) == 'Global' ? 'selected' : '' }}>Global (berlaku untuk semua koordinator)</option>
                    <option value="Provinsi" {{ old('skala', $feeEnumerator?->skala) == 'Provinsi' ? 'selected' : '' }}>Provinsi (override khusus)</option>
                </select>
                @error('skala')
                    <span class="adm-error-msg">{{ $message }}</span>
                @enderror
            </div>
            <div class="adm-field" id="provinsi_wrap" style="{{ old('skala', $feeEnumerator?->skala) === 'Provinsi' ? '' : 'display:none;' }}">
                <label class="adm-label" for="provinsi">Provinsi <span class="req">*</span></label>
                <select id="provinsi" name="provinsi" class="adm-field-select @error('provinsi') is-invalid @enderror" data-selected="{{ old('provinsi', $feeEnumerator?->provinsi) }}">
                    <option value="">-- Pilih Provinsi --</option>
                </select>
                @error('provinsi')
                    <span class="adm-error-msg">{{ $message }}</span>
                @enderror
            </div>

            <div class="adm-field">
                <label class="adm-label" for="tipe_fee">Tipe Fee <span class="req">*</span></label>
                <select name="tipe_fee" id="tipe_fee" class="adm-field-select @error('tipe_fee') is-invalid @enderror">
                    <option value="">-- Pilih Tipe Fee --</option>
                    <option value="Bulan" {{ old('tipe_fee', $feeEnumerator?->tipe_fee) == 'Bulan' ? 'selected' : '' }}>Bulan (per bulan)</option>
                    <option value="Target" {{ old('tipe_fee', $feeEnumerator?->tipe_fee) == 'Target' ? 'selected' : '' }}>Target (per N data)</option>
                </select>
                @error('tipe_fee')
                    <span class="adm-error-msg">{{ $message }}</span>
                @enderror
            </div>
            <div class="adm-field" id="target_data_wrap" style="{{ old('tipe_fee', $feeEnumerator?->tipe_fee) === 'Target' ? '' : 'display:none;' }}">
                <label class="adm-label" for="target_data">Target Data <span class="req">*</span></label>
                <input type="number" name="target_data" id="target_data" class="adm-input @error('target_data') is-invalid @enderror"
                    value="{{ old('target_data', $feeEnumerator?->target_data) }}" min="1" placeholder="Contoh: 20">
                @error('target_data')
                    <span class="adm-error-msg">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="adm-form-grid cols-1" style="margin-top:14px;">
            <div class="adm-field">
                <label class="adm-label" for="nominal_fee">Nominal Fee (Rp) <span class="req">*</span></label>
                <input type="number" name="nominal_fee" id="nominal_fee" class="adm-input @error('nominal_fee') is-invalid @enderror"
                    value="{{ old('nominal_fee', $feeEnumerator?->nominal_fee) }}" min="0" placeholder="Contoh: 50000">
                @error('nominal_fee')
                    <span class="adm-error-msg">{{ $message }}</span>
                @enderror
            </div>
            <div class="adm-field">
                <label class="adm-label" for="keterangan">Keterangan <span style="font-weight:400;color:var(--adm-text-muted);">(opsional)</span></label>
                <textarea name="keterangan" id="keterangan" class="adm-textarea @error('keterangan') is-invalid @enderror"
                    rows="3" placeholder="Catatan tambahan mengenai fee ini...">{{ old('keterangan', $feeEnumerator?->keterangan) }}</textarea>
                @error('keterangan')
                    <span class="adm-error-msg">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="adm-form-section">
    <div class="adm-form-section-header">
        <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        Status
    </div>
    <div class="adm-form-body">
        <div class="adm-form-grid cols-1">
            <div class="adm-field" style="flex-direction:row;align-items:center;gap:10px;">
                <input type="checkbox" name="is_aktif" id="is_aktif" value="1" style="width:18px;height:18px;"
                    {{ old('is_aktif', $feeEnumerator?->exists ? $feeEnumerator->is_aktif : true) ? 'checked' : '' }}>
                <label class="adm-label" for="is_aktif" style="margin:0;">Aktifkan fee ini</label>
            </div>
            <span class="adm-hint">Jika diaktifkan, fee lain dengan skala &amp; tipe yang sama akan otomatis dinonaktifkan.</span>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        document.getElementById('skala').addEventListener('change', function () {
            document.getElementById('provinsi_wrap').style.display = this.value === 'Provinsi' ? '' : 'none';
        });

        document.getElementById('tipe_fee').addEventListener('change', function () {
            document.getElementById('target_data_wrap').style.display = this.value === 'Target' ? '' : 'none';
        });

        async function loadProvincesInto(selectId) {
            const select = document.getElementById(selectId);
            if (!select) return;
            try {
                const res = await fetch('/api/wilayah/provinces');
                const json = await res.json();
                const data = json.success ? json.data : [];
                select.innerHTML = '<option value="">-- Pilih Provinsi --</option>';
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.code;
                    option.setAttribute('data-name', item.name);
                    option.textContent = item.name;
                    select.appendChild(option);
                });

                const saved = select.dataset.selected;
                if (saved) {
                    const nameLower = saved.toLowerCase().trim();
                    for (let i = 0; i < select.options.length; i++) {
                        const optText = (select.options[i].text || '').toLowerCase().trim();
                        if (optText === nameLower || optText.includes(nameLower) || nameLower.includes(optText)) {
                            select.value = select.options[i].value;
                            break;
                        }
                    }
                }
            } catch (e) {
                console.error('Gagal memuat data provinsi:', e);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            loadProvincesInto('provinsi');

            const form = document.getElementById('formFeeEnumerator');
            if (form) {
                form.addEventListener('submit', function () {
                    const sel = document.getElementById('provinsi');
                    if (!sel || !sel.value) return;
                    const selectedOpt = sel.options[sel.selectedIndex];
                    const textName = selectedOpt ? (selectedOpt.getAttribute('data-name') || selectedOpt.textContent) : '';
                    if (textName && textName.trim()) {
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'provinsi';
                        hidden.value = textName.trim();
                        sel.parentNode.appendChild(hidden);
                        sel.removeAttribute('name');
                    }
                });
            }
        });
    })();
</script>
@endpush
