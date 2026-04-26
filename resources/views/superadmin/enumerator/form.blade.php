{{-- ── KOORDINATOR ── --}}
<div class="adm-field">
    <label class="adm-label" for="koordinator_id">Koordinator</label>
    <select name="koordinator_id" id="koordinator_id"
        class="adm-field-select @error('koordinator_id') is-invalid @enderror">
        <option value="">-- Pilih Koordinator --</option>
        @foreach ($koordinators as $koordinator)
            <option value="{{ $koordinator->id }}"
                {{ old('koordinator_id', $enumerator?->koordinator_id) == $koordinator->id ? 'selected' : '' }}>
                {{ $koordinator->nama_lengkap }}
            </option>
        @endforeach
    </select>
    @error('koordinator_id') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── NAMA LENGKAP ── --}}
<div class="adm-field">
    <label class="adm-label" for="nama_lengkap">Nama Lengkap <span class="req">*</span></label>
    <input type="text" name="nama_lengkap" id="nama_lengkap"
        class="adm-input @error('nama_lengkap') is-invalid @enderror"
        value="{{ old('nama_lengkap', $enumerator?->nama_lengkap) }}"
        placeholder="Nama lengkap enumerator">
    @error('nama_lengkap') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── TELEPHONE ── --}}
<div class="adm-field">
    <label class="adm-label" for="telephone">Telephone <span class="req">*</span></label>
    <input type="text" name="telephone" id="telephone"
        class="adm-input @error('telephone') is-invalid @enderror"
        value="{{ old('telephone', $enumerator?->telephone) }}"
        placeholder="08xxxxxxxxxx">
    @error('telephone') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── STATUS ── --}}
<div class="adm-field">
    <label class="adm-label" for="status">Status <span class="req">*</span></label>
    <select name="status" id="status" class="adm-field-select @error('status') is-invalid @enderror">
        <option value="">-- Pilih Status --</option>
        <option value="Aktif" {{ old('status', $enumerator?->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
        <option value="Tidak Aktif" {{ old('status', $enumerator?->status) == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
    </select>
    @error('status') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── FOTO DIRI ── --}}
<div class="adm-field">
    <label class="adm-label" for="foto_diri">Foto Diri</label>
    <input type="file" name="foto_diri" id="foto_diri"
        class="adm-input @error('foto_diri') is-invalid @enderror"
        accept="image/*">
    @error('foto_diri') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── ALAMAT ── --}}
<div class="adm-field">
    <label class="adm-label" for="alamat">Alamat</label>
    <input type="text" name="alamat" id="alamat"
        class="adm-input @error('alamat') is-invalid @enderror"
        value="{{ old('alamat', $enumerator?->alamat) }}"
        placeholder="Alamat lengkap">
    @error('alamat') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── BANK ── --}}
<div class="adm-field">
    <label class="adm-label" for="bank_id">Nama Bank</label>
    <select name="bank_id" id="bank_id"
        class="adm-field-select select2-bank @error('bank_id') is-invalid @enderror">
        <option value="">-- Pilih Bank --</option>
        @foreach ($banks as $bank)
            <option value="{{ $bank->id }}"
                {{ old('bank_id', $enumerator?->bank_id) == $bank->id ? 'selected' : '' }}>
                {{ $bank->name }}
            </option>
        @endforeach
    </select>
    @error('bank_id') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── NO REKENING ── --}}
<div class="adm-field">
    <label class="adm-label" for="no_rekening">No. Rekening</label>
    <input type="text" name="no_rekening" id="no_rekening"
        class="adm-input adm-mono @error('no_rekening') is-invalid @enderror"
        value="{{ old('no_rekening', $enumerator?->no_rekening) }}"
        placeholder="Nomor rekening">
    @error('no_rekening') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── NAMA REKENING ── --}}
<div class="adm-field">
    <label class="adm-label" for="nama_rekening">Nama Rekening</label>
    <input type="text" name="nama_rekening" id="nama_rekening"
        class="adm-input @error('nama_rekening') is-invalid @enderror"
        value="{{ old('nama_rekening', $enumerator?->nama_rekening) }}"
        placeholder="Nama pemilik rekening">
    @error('nama_rekening') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<style>
    .select2-container .select2-selection--single {
        height: 38px; padding: 5px 10px;
        border: 1px solid var(--adm-border-mid);
        border-radius: var(--adm-radius-sm);
        background: var(--adm-bg-input);
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5; color: var(--adm-text-dark); padding-left: 0;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 38px; }
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: var(--adm-blue);
        box-shadow: 0 0 0 3px rgba(26,95,200,.08);
    }
    .select2-dropdown { border: 1px solid var(--adm-border-mid); border-radius: var(--adm-radius-sm); }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background: var(--adm-blue); }
</style>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-bank').select2({
            placeholder: '-- Cari / Pilih Bank --',
            allowClear: true, width: '100%',
            language: { noResults: () => 'Bank tidak ditemukan', searching: () => 'Mencari...' }
        });
    });
</script>
