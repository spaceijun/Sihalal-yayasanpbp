{{-- ── VERSION ── --}}
<div class="adm-field">
    <label class="adm-label" for="version">Version <span class="req">*</span></label>
    <input type="text" name="version" id="version"
        class="adm-input @error('version') is-invalid @enderror"
        value="{{ old('version', $appVersion?->version) }}"
        placeholder="contoh: 1.2.0">
    @error('version')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── BUILD NUMBER ── --}}
<div class="adm-field">
    <label class="adm-label" for="build_number">Build Number <span class="req">*</span></label>
    <input type="number" name="build_number" id="build_number"
        class="adm-input @error('build_number') is-invalid @enderror"
        value="{{ old('build_number', $appVersion?->build_number) }}"
        placeholder="contoh: 3">
    <span class="adm-hint">Harus lebih tinggi dari versi sebelumnya.</span>
    @error('build_number')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── CHANGELOG ── --}}
<div class="adm-field">
    <label class="adm-label" for="changelog">Changelog</label>
    <textarea name="changelog" id="changelog" rows="3"
        class="adm-textarea @error('changelog') is-invalid @enderror"
        placeholder="contoh: Tambah fitur laporan, fix bug login">{{ old('changelog', $appVersion?->changelog) }}</textarea>
    @error('changelog')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── FORCE UPDATE ── --}}
<div class="adm-field">
    <label class="adm-label">Force Update</label>
    <div style="display:flex; align-items:center; gap:10px; padding:10px 12px; background:var(--adm-bg-input); border:1px solid var(--adm-border-mid); border-radius:var(--adm-radius-sm);">
        <input type="hidden" name="force_update" value="0">
        <input type="checkbox" name="force_update" id="force_update" value="1"
            class="form-check-input m-0 @error('force_update') is-invalid @enderror"
            style="width:18px;height:18px;cursor:pointer;"
            {{ old('force_update', $appVersion?->force_update) ? 'checked' : '' }}>
        <label for="force_update" style="font-size:13px; color:var(--adm-text-mid); cursor:pointer; margin:0;">
            Wajibkan user update (tidak bisa skip)
        </label>
    </div>
    <span class="adm-hint">Aktifkan hanya untuk update kritis.</span>
    @error('force_update')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── DOWNLOAD URL ── --}}
<div class="adm-field">
    <label class="adm-label" for="download_url">Download URL <span class="req">*</span></label>
    <input type="url" name="download_url" id="download_url"
        class="adm-input @error('download_url') is-invalid @enderror"
        value="{{ old('download_url', $appVersion?->download_url) }}"
        placeholder="https://domain.com/storage/apk/app-v1.2.0.apk">
    @error('download_url')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>
