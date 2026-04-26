{{-- ── DATA LAPANGAN ID ── --}}
<div class="adm-field">
    <label class="adm-label" for="data_lapangan_id">ID Data Lapangan <span class="req">*</span></label>
    <input type="text" name="data_lapangan_id" id="data_lapangan_id"
        class="adm-input adm-mono @error('data_lapangan_id') is-invalid @enderror"
        value="{{ old('data_lapangan_id', $spotcheck?->data_lapangan_id) }}"
        placeholder="ID referensi data lapangan">
    @error('data_lapangan_id') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── NAMA SPOTCHECK ── --}}
<div class="adm-field">
    <label class="adm-label" for="nama_spotcheck">Nama Spotcheck <span class="req">*</span></label>
    <input type="text" name="nama_spotcheck" id="nama_spotcheck"
        class="adm-input @error('nama_spotcheck') is-invalid @enderror"
        value="{{ old('nama_spotcheck', $spotcheck?->nama_spotcheck) }}"
        placeholder="Nama petugas spotcheck">
    @error('nama_spotcheck') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── TANGGAL SPOTCHECK ── --}}
<div class="adm-field">
    <label class="adm-label" for="tanggal_spotcheck">Tanggal Spotcheck <span class="req">*</span></label>
    <input type="date" name="tanggal_spotcheck" id="tanggal_spotcheck"
        class="adm-input @error('tanggal_spotcheck') is-invalid @enderror"
        value="{{ old('tanggal_spotcheck', $spotcheck?->tanggal_spotcheck) }}">
    @error('tanggal_spotcheck') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── FOTO PU ── --}}
<div class="adm-field">
    <label class="adm-label" for="foto_pu">Foto PU</label>
    <input type="text" name="foto_pu" id="foto_pu"
        class="adm-input @error('foto_pu') is-invalid @enderror"
        value="{{ old('foto_pu', $spotcheck?->foto_pu) }}"
        placeholder="Path foto PU">
    @error('foto_pu') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── HASIL SPOTCHECK ── --}}
<div class="adm-field" style="grid-column:1/-1;">
    <label class="adm-label" for="hasil_spotcheck">Hasil Spotcheck</label>
    <textarea name="hasil_spotcheck" id="hasil_spotcheck" rows="4"
        class="adm-textarea @error('hasil_spotcheck') is-invalid @enderror"
        placeholder="Catatan hasil spotcheck di lapangan...">{{ old('hasil_spotcheck', $spotcheck?->hasil_spotcheck) }}</textarea>
    @error('hasil_spotcheck') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>