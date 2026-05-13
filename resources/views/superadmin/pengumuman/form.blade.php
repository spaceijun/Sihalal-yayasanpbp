{{-- ── NOMOR PENGUMUMAN (auto) ── --}}
<div class="adm-field">
    <label class="adm-label" for="nomor">Nomor Pengumuman</label>
    <input type="text" name="nomor" id="nomor" class="adm-input @error('nomor') is-invalid @enderror"
        value="{{ old('nomor', $nextNomor ?? 'YPBP-KH/' . now()->format('m') . '/' . now()->format('Y') . '/001') }}"
        readonly
        style="background:var(--adm-bg-muted);cursor:not-allowed;color:var(--adm-text-muted);font-family:var(--adm-font-mono,monospace);font-size:13px;">
    <span class="adm-hint">Nomor dibuat otomatis oleh sistem.</span>
    @error('nomor')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── JENIS PENGUMUMAN ── --}}
<div class="adm-field">
    <label class="adm-label" for="jenis">Jenis Pengumuman <span class="req">*</span></label>
    <select name="jenis" id="jenis" class="adm-field-select @error('jenis') is-invalid @enderror">
        <option value="">-- Pilih Jenis --</option>
        <option value="SIHALAL" {{ old('jenis', $pengumuman?->jenis) == 'SIHALAL' ? 'selected' : '' }}>SIHALAL</option>
        <option value="OSS" {{ old('jenis', $pengumuman?->jenis) == 'OSS' ? 'selected' : '' }}>OSS</option>
        <option value="PENDAMPING" {{ old('jenis', $pengumuman?->jenis) == 'PENDAMPING' ? 'selected' : '' }}>PENDAMPING
        </option>
    </select>
    @error('jenis')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── JUDUL ── --}}
<div class="adm-field full-span">
    <label class="adm-label" for="judul">Judul Pengumuman <span class="req">*</span></label>
    <input type="text" name="judul" id="judul" class="adm-input @error('judul') is-invalid @enderror"
        value="{{ old('judul', $pengumuman?->judul) }}" placeholder="Judul pengumuman yang jelas dan deskriptif">
    @error('judul')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── FOTO / LAMPIRAN ── --}}
<div class="adm-field full-span">
    <label class="adm-label" for="foto">Lampiran / Foto <span
            style="font-weight:400;color:var(--adm-text-muted);">(PDF, JPG, PNG)</span></label>
    <input type="file" name="foto" id="foto" class="adm-input @error('foto') is-invalid @enderror"
        accept=".jpeg,.png,.jpg,.gif,.svg,.pdf">
    @if ($pengumuman?->foto)
        <span class="adm-hint">
            File saat ini:
            <a href="{{ asset('storage/' . $pengumuman->foto) }}" target="_blank" style="color:var(--adm-blue);">Lihat
                lampiran</a>
            — Upload baru untuk mengganti.
        </span>
    @endif
    @error('foto')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── ISI PENGUMUMAN (ClassicEditor) ── --}}
<div class="adm-field full-span">
    <label class="adm-label" for="deskripsi">Isi Pengumuman <span class="req">*</span></label>
    <textarea name="deskripsi" id="deskripsi" class="adm-textarea @error('deskripsi') is-invalid @enderror" rows="6"
        placeholder="Tulis isi pengumuman di sini...">{{ old('deskripsi', $pengumuman?->deskripsi) }}</textarea>
    @error('deskripsi')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

<script>
    ClassicEditor.create(document.querySelector('#deskripsi')).catch(error => console.error(error));
</script>
