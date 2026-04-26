{{-- ── NAME ── --}}
<div class="adm-field">
    <label class="adm-label" for="name">Nama Lengkap <span class="req">*</span></label>
    <input type="text" name="name" id="name"
        class="adm-input @error('name') is-invalid @enderror"
        value="{{ old('name', $user?->name) }}"
        placeholder="Nama lengkap pengguna">
    @error('name') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── EMAIL ── --}}
<div class="adm-field">
    <label class="adm-label" for="email">Email <span class="req">*</span></label>
    <input type="email" name="email" id="email"
        class="adm-input @error('email') is-invalid @enderror"
        value="{{ old('email', $user?->email) }}"
        placeholder="email@example.com">
    @error('email') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── TELEPHONE ── --}}
<div class="adm-field">
    <label class="adm-label" for="telephone">Nomor Telepon</label>
    <input type="tel" name="telephone" id="telephone"
        class="adm-input adm-mono @error('telephone') is-invalid @enderror"
        value="{{ old('telephone', $user?->telephone) }}"
        placeholder="08xxxxxxxxxx">
    <span class="adm-hint">Minimal 11 digit, maksimal 15 digit</span>
    @error('telephone') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── ROLE ── --}}
<div class="adm-field">
    <label class="adm-label" for="role">Role <span class="req">*</span></label>
    <input type="text" name="role" id="role"
        class="adm-input @error('role') is-invalid @enderror"
        value="{{ old('role', $user?->role) }}"
        placeholder="admin / superadmin / user">
    @error('role') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── PASSWORD ── --}}
<div class="adm-field" style="grid-column:1/-1;">
    <label class="adm-label" for="password">Password</label>
    <input type="password" name="password" id="password"
        class="adm-input @error('password') is-invalid @enderror"
        placeholder="Masukkan password baru">
    <span class="adm-hint">Kosongkan jika tidak ingin mengubah password.</span>
    @error('password') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>
