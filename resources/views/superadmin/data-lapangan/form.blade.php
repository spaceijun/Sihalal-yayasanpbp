{{-- ── ENUMERATOR ID ── --}}
<div class="adm-field">
    <label class="adm-label" for="enumerator_id">Enumerator ID</label>
    <input type="text" name="enumerator_id" id="enumerator_id"
        class="adm-input @error('enumerator_id') is-invalid @enderror"
        value="{{ old('enumerator_id', $dataLapangan?->enumerator_id) }}"
        placeholder="ID enumerator">
    @error('enumerator_id') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── NAMA PU ── --}}
<div class="adm-field">
    <label class="adm-label" for="nama_pu">Nama PU <span class="req">*</span></label>
    <input type="text" name="nama_pu" id="nama_pu"
        class="adm-input @error('nama_pu') is-invalid @enderror"
        value="{{ old('nama_pu', $dataLapangan?->nama_pu) }}"
        placeholder="Nama pelaku usaha">
    @error('nama_pu') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── NIK ── --}}
<div class="adm-field">
    <label class="adm-label" for="nik">NIK</label>
    <input type="text" name="nik" id="nik"
        class="adm-input adm-mono @error('nik') is-invalid @enderror"
        value="{{ old('nik', $dataLapangan?->nik) }}"
        placeholder="Nomor Induk Kependudukan">
    @error('nik') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── RT ── --}}
<div class="adm-field">
    <label class="adm-label" for="rt">RT</label>
    <input type="text" name="rt" id="rt"
        class="adm-input @error('rt') is-invalid @enderror"
        value="{{ old('rt', $dataLapangan?->rt) }}"
        placeholder="RT">
    @error('rt') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── RW ── --}}
<div class="adm-field">
    <label class="adm-label" for="rw">RW</label>
    <input type="text" name="rw" id="rw"
        class="adm-input @error('rw') is-invalid @enderror"
        value="{{ old('rw', $dataLapangan?->rw) }}"
        placeholder="RW">
    @error('rw') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── ALAMAT ── --}}
<div class="adm-field" style="grid-column:1/-1;">
    <label class="adm-label" for="alamat">Alamat Lengkap</label>
    <input type="text" name="alamat" id="alamat"
        class="adm-input @error('alamat') is-invalid @enderror"
        value="{{ old('alamat', $dataLapangan?->alamat) }}"
        placeholder="Alamat lengkap PU">
    @error('alamat') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── TITIK KOORDINAT ── --}}
<div class="adm-field" style="grid-column:1/-1;">
    <label class="adm-label" for="titik_koordinat">Titik Koordinat</label>
    <input type="text" name="titik_koordinat" id="titik_koordinat"
        class="adm-input adm-mono @error('titik_koordinat') is-invalid @enderror"
        value="{{ old('titik_koordinat', $dataLapangan?->titik_koordinat) }}"
        placeholder="-6.123456, 106.789012">
    @error('titik_koordinat') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── FOTO KTP ── --}}
<div class="adm-field">
    <label class="adm-label" for="foto_ktp">Foto KTP</label>
    <input type="text" name="foto_ktp" id="foto_ktp"
        class="adm-input @error('foto_ktp') is-invalid @enderror"
        value="{{ old('foto_ktp', $dataLapangan?->foto_ktp) }}"
        placeholder="Path foto KTP">
    @error('foto_ktp') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── FOTO RUMAH ── --}}
<div class="adm-field">
    <label class="adm-label" for="foto_rumah">Foto Rumah</label>
    <input type="text" name="foto_rumah" id="foto_rumah"
        class="adm-input @error('foto_rumah') is-invalid @enderror"
        value="{{ old('foto_rumah', $dataLapangan?->foto_rumah) }}"
        placeholder="Path foto rumah">
    @error('foto_rumah') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── FOTO PENDAMPING ── --}}
<div class="adm-field">
    <label class="adm-label" for="foto_pendamping">Foto Pendamping</label>
    <input type="text" name="foto_pendamping" id="foto_pendamping"
        class="adm-input @error('foto_pendamping') is-invalid @enderror"
        value="{{ old('foto_pendamping', $dataLapangan?->foto_pendamping) }}"
        placeholder="Path foto pendamping">
    @error('foto_pendamping') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── FOTO PROSES ── --}}
<div class="adm-field">
    <label class="adm-label" for="foto_proses">Foto Proses</label>
    <input type="text" name="foto_proses" id="foto_proses"
        class="adm-input @error('foto_proses') is-invalid @enderror"
        value="{{ old('foto_proses', $dataLapangan?->foto_proses) }}"
        placeholder="Path foto proses">
    @error('foto_proses') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── FOTO PRODUK ── --}}
<div class="adm-field">
    <label class="adm-label" for="foto_produk">Foto Produk</label>
    <input type="text" name="foto_produk" id="foto_produk"
        class="adm-input @error('foto_produk') is-invalid @enderror"
        value="{{ old('foto_produk', $dataLapangan?->foto_produk) }}"
        placeholder="Path foto produk">
    @error('foto_produk') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>