{{-- ── NAMA LENGKAP ── --}}
<div class="adm-field">
    <label class="adm-label" for="nama_lengkap">Nama Lengkap <span class="req">*</span></label>
    <input type="text" name="nama_lengkap" id="nama_lengkap"
        class="adm-input @error('nama_lengkap') is-invalid @enderror"
        value="{{ old('nama_lengkap', $koordinator?->nama_lengkap) }}" placeholder="Nama lengkap koordinator">
    @error('nama_lengkap')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── EMAIL ── --}}
<div class="adm-field">
    <label class="adm-label" for="email">Email <span class="req">*</span></label>
    <input type="email" name="email" id="email" class="adm-input @error('email') is-invalid @enderror"
        value="{{ old('email', $koordinator?->email) }}" placeholder="email@domain.com">
    @error('email')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── TELEPHONE ── --}}
<div class="adm-field">
    <label class="adm-label" for="telephone">Telephone <span class="req">*</span></label>
    <input type="text" name="telephone" id="telephone" class="adm-input @error('telephone') is-invalid @enderror"
        value="{{ old('telephone', $koordinator?->telephone) }}" placeholder="08xxxxxxxxxx">
    @error('telephone')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── STATUS ── --}}
<div class="adm-field">
    <label class="adm-label" for="status-1">Status <span class="req">*</span></label>
    <select name="status" id="status-1" class="adm-field-select @error('status') is-invalid @enderror">
        <option value="">-- Pilih Status --</option>
        <option value="Aktif" {{ old('status', $koordinator?->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
        <option value="Tidak Aktif" {{ old('status', $koordinator?->status) == 'Tidak Aktif' ? 'selected' : '' }}>Tidak
            Aktif</option>
    </select>
    @error('status')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── FEE ENUM ── --}}
<div class="adm-field">
    <label class="adm-label" for="fee_enum">Fee Enumerator (Rp)</label>
    <input type="number" name="fee_enum" id="fee_enum" class="adm-input @error('fee_enum') is-invalid @enderror"
        value="{{ old('fee_enum', $koordinator?->fee_enum) }}" placeholder="contoh: 50000">
    @error('fee_enum')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── ALAMAT ── --}}
<div class="adm-field">
    <label class="adm-label" for="alamat">Alamat</label>
    <input type="text" name="alamat" id="alamat" class="adm-input @error('alamat') is-invalid @enderror"
        value="{{ old('alamat', $koordinator?->alamat) }}" placeholder="Alamat lengkap">
    @error('alamat')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── PASSWORD ── --}}
<div class="adm-field full-span">
    <label class="adm-label" for="password">Password Login</label>
    <input type="password" name="password" id="password" class="adm-input @error('password') is-invalid @enderror"
        placeholder="Kosongkan jika tidak ingin mengubah password">
    <span class="adm-hint" style="color:var(--adm-red);">* Kosongkan jika tidak ingin mengubah password</span>
    @error('password')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>
