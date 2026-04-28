{{-- ── NAMA PRODUK ── --}}
<div class="adm-field full-span">
    <label class="adm-label" for="nama_produk">Nama Produk <span class="req">*</span></label>
    <input type="text" name="nama_produk" id="nama_produk" class="adm-input @error('nama_produk') is-invalid @enderror"
        value="{{ old('nama_produk', $resepMakanan?->nama_produk) }}" placeholder="Nama produk resep makanan">
    @error('nama_produk')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── KATEGORI ── --}}
<div class="adm-field">
    <label class="adm-label" for="kategori">Kategori <span class="req">*</span></label>
    <select name="kategori" id="kategori" class="adm-field-select @error('kategori') is-invalid @enderror">
        <option value="" disabled {{ old('kategori', $resepMakanan?->kategori) === null ? 'selected' : '' }}>--
            Pilih Kategori --</option>
        <option value="makanan" {{ old('kategori', $resepMakanan?->kategori) === 'makanan' ? 'selected' : '' }}>Makanan
        </option>
        <option value="minuman" {{ old('kategori', $resepMakanan?->kategori) === 'minuman' ? 'selected' : '' }}>Minuman
        </option>
    </select>
    @error('kategori')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── FOTO ── --}}
<div class="adm-field">
    <label class="adm-label" for="foto">Foto <span style="font-weight:400;color:var(--adm-text-muted);">(JPG,
            PNG)</span></label>
    <input type="file" name="foto" id="foto" class="adm-input @error('foto') is-invalid @enderror"
        accept=".jpeg,.png,.jpg,.gif,.svg">
    @if ($resepMakanan?->foto)
        <span class="adm-hint">
            File saat ini:
            <a href="{{ asset('storage/' . $resepMakanan->foto) }}" target="_blank" style="color:var(--adm-blue);">Lihat
                foto</a>
            — Upload baru untuk mengganti.
        </span>
    @endif
    @error('foto')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── BAHAN MAKANAN (ClassicEditor) ── --}}
<div class="adm-field full-span">
    <label class="adm-label" for="bahan_makanan">Bahan Makanan <span class="req">*</span></label>
    <textarea name="bahan_makanan" id="bahan_makanan" class="adm-textarea @error('bahan_makanan') is-invalid @enderror"
        rows="6" placeholder="Tulis bahan-bahan makanan di sini...">{{ old('bahan_makanan', $resepMakanan?->bahan_makanan) }}</textarea>
    @error('bahan_makanan')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── PROSES PEMBUATAN (ClassicEditor) ── --}}
<div class="adm-field full-span">
    <label class="adm-label" for="proses_pembuatan">Proses Pembuatan <span class="req">*</span></label>
    <textarea name="proses_pembuatan" id="proses_pembuatan"
        class="adm-textarea @error('proses_pembuatan') is-invalid @enderror" rows="6"
        placeholder="Tulis proses pembuatan di sini...">{{ old('proses_pembuatan', $resepMakanan?->proses_pembuatan) }}</textarea>
    @error('proses_pembuatan')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

<script>
    ClassicEditor.create(document.querySelector('#bahan_makanan')).catch(error => console.error(error));
    ClassicEditor.create(document.querySelector('#proses_pembuatan')).catch(error => console.error(error));
</script>
