{{-- ── NAMA LENGKAP ── --}}
<div class="adm-field">
    <label class="adm-label" for="nama_lengkap">Nama Lengkap <span class="req">*</span></label>
    <input type="text" name="nama_lengkap" id="nama_lengkap"
        class="adm-input @error('nama_lengkap') is-invalid @enderror"
        value="{{ old('nama_lengkap', $recruitment?->nama_lengkap) }}"
        placeholder="Nama lengkap pelamar">
    @error('nama_lengkap') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── TELEPHONE ── --}}
<div class="adm-field">
    <label class="adm-label" for="telephone">Telephone <span class="req">*</span></label>
    <input type="number" name="telephone" id="telephone"
        class="adm-input @error('telephone') is-invalid @enderror"
        value="{{ old('telephone', $recruitment?->telephone) }}"
        placeholder="08xxxxxxxxxx">
    @error('telephone') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── ALAMAT LENGKAP ── --}}
<div class="adm-field">
    <label class="adm-label" for="alamat_lengkap">Alamat Lengkap</label>
    <input type="text" name="alamat_lengkap" id="alamat_lengkap"
        class="adm-input @error('alamat_lengkap') is-invalid @enderror"
        value="{{ old('alamat_lengkap', $recruitment?->alamat_lengkap) }}"
        placeholder="Alamat lengkap">
    @error('alamat_lengkap') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── PENGALAMAN ── --}}
<div class="adm-field">
    <label class="adm-label" for="pengalaman">Pengalaman</label>
    <input type="text" name="pengalaman" id="pengalaman"
        class="adm-input @error('pengalaman') is-invalid @enderror"
        value="{{ old('pengalaman', $recruitment?->pengalaman) }}"
        placeholder="Pengalaman kerja relevan">
    @error('pengalaman') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── PENDIDIKAN TERAKHIR ── --}}
<div class="adm-field">
    <label class="adm-label" for="pendidikan_terakhir">Pendidikan Terakhir</label>
    <select name="pendidikan_terakhir" id="pendidikan_terakhir"
        class="adm-field-select @error('pendidikan_terakhir') is-invalid @enderror">
        <option value="">-- Pilih Pendidikan Terakhir --</option>
        @php $pendidikanList = ['SD / Paket A / Sederajat','SMP / Paket B / Sederajat','SMA / SMK / Paket C / Sederajat','D1','D2','D3','S1','S2','S3']; @endphp
        @foreach ($pendidikanList as $pendidikan)
            <option value="{{ $pendidikan }}" {{ old('pendidikan_terakhir', $recruitment?->pendidikan_terakhir) == $pendidikan ? 'selected' : '' }}>
                {{ $pendidikan }}
            </option>
        @endforeach
    </select>
    @error('pendidikan_terakhir') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── REKOMENDASI ── --}}
<div class="adm-field">
    <label class="adm-label" for="rekomendasi">Rekomendasi</label>
    <select name="rekomendasi" id="rekomendasi"
        class="adm-field-select @error('rekomendasi') is-invalid @enderror">
        <option value="">-- Pilih Rekomendasi --</option>
        @php $daftarRekomendasi = ['Adi Tarman','M. Faizun Aziz','Ade Sofyan','Agil Praditya Putu Yazier','Ahmad Nurohim','Zaenal Arifin']; @endphp
        @foreach ($daftarRekomendasi as $nama)
            <option value="{{ $nama }}" {{ old('rekomendasi', $recruitment?->rekomendasi) == $nama ? 'selected' : '' }}>
                {{ $nama }}
            </option>
        @endforeach
    </select>
    <span class="adm-hint">Kosongkan jika tidak ada rekomendasi.</span>
    @error('rekomendasi') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── FOTO DIRI ── --}}
<div class="adm-field">
    <label class="adm-label" for="foto_diri">Foto Diri <span style="font-weight:400;color:var(--adm-text-muted);">(3×4)</span></label>
    <input type="file" name="foto_diri" id="foto_diri"
        class="adm-input @error('foto_diri') is-invalid @enderror"
        accept="image/*">
    @error('foto_diri') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── FOTO KTP ── --}}
<div class="adm-field">
    <label class="adm-label" for="foto_ktp">Foto KTP</label>
    <input type="file" name="foto_ktp" id="foto_ktp"
        class="adm-input @error('foto_ktp') is-invalid @enderror"
        accept="image/*">
    @error('foto_ktp') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- Hidden status default --}}
<input type="hidden" name="status" value="Melamar">
