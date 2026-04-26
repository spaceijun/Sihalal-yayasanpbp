{{-- ── NAMA LENGKAP ── --}}
<div class="adm-field">
    <label class="adm-label" for="nama_lengkap">Nama Lengkap <span class="req">*</span></label>
    <input type="text" name="nama_lengkap" id="nama_lengkap"
        class="adm-input @error('nama_lengkap') is-invalid @enderror"
        value="{{ old('nama_lengkap', $verifikator?->nama_lengkap) }}"
        placeholder="Nama lengkap verifikator">
    @error('nama_lengkap') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── TELEPHONE ── --}}
<div class="adm-field">
    <label class="adm-label" for="telephone">Telephone <span class="req">*</span></label>
    <input type="text" name="telephone" id="telephone"
        class="adm-input @error('telephone') is-invalid @enderror"
        value="{{ old('telephone', $verifikator?->telephone) }}"
        placeholder="08xxxxxxxxxx">
    @error('telephone') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── ALAMAT ── --}}
<div class="adm-field">
    <label class="adm-label" for="alamat_lengkap">Alamat Lengkap</label>
    <input type="text" name="alamat_lengkap" id="alamat_lengkap"
        class="adm-input @error('alamat_lengkap') is-invalid @enderror"
        value="{{ old('alamat_lengkap', $verifikator?->alamat_lengkap) }}"
        placeholder="Alamat lengkap">
    @error('alamat_lengkap') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── RATE PER DATA ── --}}
<div class="adm-field">
    <label class="adm-label" for="rate_per_data">Rate Per Data (Rp) <span class="req">*</span></label>
    <input type="number" name="rate_per_data" id="rate_per_data"
        class="adm-input @error('rate_per_data') is-invalid @enderror"
        value="{{ old('rate_per_data', $verifikator?->rate_per_data) }}"
        placeholder="contoh: 25000">
    @error('rate_per_data') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>