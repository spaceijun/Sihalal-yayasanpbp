{{-- ── NAMA LENGKAP ── --}}
<div class="adm-field">
    <label class="adm-label" for="nama_lengkap">Nama Lengkap <span class="req">*</span></label>
    <input type="text" name="nama_lengkap" id="nama_lengkap"
        class="adm-input @error('nama_lengkap') is-invalid @enderror"
        value="{{ old('nama_lengkap', $dataEntry?->nama_lengkap) }}" placeholder="Nama lengkap data entry">
    @error('nama_lengkap')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── EMAIL ── --}}
<div class="adm-field">
    <label class="adm-label" for="email">Email <span class="req">*</span></label>
    <input type="email" name="email" id="email" class="adm-input @error('email') is-invalid @enderror"
        value="{{ old('email', $dataEntry?->email) }}" placeholder="email@domain.com">
    @error('email')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── TELEPHONE ── --}}
<div class="adm-field">
    <label class="adm-label" for="telephone">Telephone <span class="req">*</span></label>
    <input type="text" name="telephone" id="telephone" class="adm-input @error('telephone') is-invalid @enderror"
        value="{{ old('telephone', $dataEntry?->telephone) }}" placeholder="08xxxxxxxxxx">
    @error('telephone')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── ALAMAT ── --}}
<div class="adm-field">
    <label class="adm-label" for="alamat">Alamat</label>
    <input type="text" name="alamat" id="alamat" class="adm-input @error('alamat') is-invalid @enderror"
        value="{{ old('alamat', $dataEntry?->alamat) }}" placeholder="Alamat lengkap">
    @error('alamat')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── STATUS ── --}}
<div class="adm-field">
    <label class="adm-label" for="status-1">Status <span class="req">*</span></label>
    <select name="status" id="status-1" class="adm-field-select @error('status') is-invalid @enderror">
        <option value="">-- Pilih Status --</option>
        <option value="Aktif" {{ old('status', $dataEntry?->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
        <option value="Tidak Aktif" {{ old('status', $dataEntry?->status) == 'Tidak Aktif' ? 'selected' : '' }}>Tidak
            Aktif</option>
    </select>
    @error('status')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── ENTRY TYPE ── --}}
<div class="adm-field">
    <label class="adm-label" for="entry_type">Entry Type <span class="req">*</span></label>
    <select name="entry_type" id="entry_type" class="adm-field-select @error('entry_type') is-invalid @enderror"
        {{ isset($dataEntry->id) ? 'disabled' : '' }}>
        <option value="">-- Pilih Entry Type --</option>
        <option value="OSS" {{ old('entry_type', $dataEntry?->entry_type) == 'OSS' ? 'selected' : '' }}>OSS</option>
        <option value="SIHALAL" {{ old('entry_type', $dataEntry?->entry_type) == 'SIHALAL' ? 'selected' : '' }}>SIHALAL
        </option>
    </select>
    @if (isset($dataEntry->id))
        <input type="hidden" name="entry_type" value="{{ $dataEntry->entry_type }}">
        <span class="adm-hint">Entry type tidak dapat diubah setelah dibuat.</span>
    @endif
    @error('entry_type')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── KOORDINATOR (full span) ── --}}
<div class="adm-field full-span">
    <label class="adm-label">Koordinator</label>
    <select name="koordinator_ids[]" id="koordinator_ids"
        class="adm-field-select select2 @error('koordinator_ids') is-invalid @enderror" multiple
        style="height:auto;min-height:38px;">
        @foreach ($koordinators as $koordinator)
            <option value="{{ $koordinator->id }}"
                {{ in_array($koordinator->id, old('koordinator_ids', $selectedKoordinatorIds ?? [])) ? 'selected' : '' }}>
                {{ $koordinator->nama_lengkap }}
            </option>
        @endforeach
    </select>
    @error('koordinator_ids')
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

<style>
    .select2-container--default .select2-selection--multiple {
        background: var(--adm-bg-input);
        border: 1px solid var(--adm-border-mid);
        border-radius: var(--adm-radius-sm);
        padding: 4px 8px;
        min-height: 38px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background: var(--adm-blue);
        border: none;
        border-radius: 6px;
        color: #fff;
        padding: 2px 8px;
        font-size: 12px;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: rgba(255, 255, 255, .7);
        margin-right: 4px;
    }

    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: var(--adm-blue);
        box-shadow: 0 0 0 3px rgba(26, 95, 200, .08);
    }

    .select2-dropdown {
        border: 1px solid var(--adm-border-mid);
        border-radius: var(--adm-radius-sm);
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background: var(--adm-blue);
    }
</style>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: '-- Pilih Koordinator --',
            allowClear: true
        });
    });
</script>
