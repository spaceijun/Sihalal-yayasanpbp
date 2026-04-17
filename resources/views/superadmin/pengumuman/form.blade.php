<div class="row padding-1 p-1">
    <div class="col-md-12">

        <div class="form-group mb-2 mb20">
            <label for="nomor" class="form-label">{{ __('Nomor Pengumuman') }}</label>
            <input type="text" name="nomor" class="form-control bg-light @error('nomor') is-invalid @enderror"
                value="{{ old('nomor', $nextNomor ?? 'YPBP-KH/' . now()->format('m') . '/' . now()->format('Y') . '/001') }}"
                id="nomor" readonly>
            {!! $errors->first('nomor', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="judul" class="form-label">{{ __('Judul Pengumuman') }}</label>
            <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror"
                value="{{ old('judul', $pengumuman?->judul) }}" id="judul" placeholder="Judul">
            {!! $errors->first('judul', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="jenis" class="form-label">{{ __('Jenis Pengumuman') }}</label>

            <select name="jenis" id="jenis" class="form-control @error('jenis') is-invalid @enderror">
                <option value="">-- Pilih Jenis --</option>

                <option value="SIHALAL" {{ old('jenis', $pengumuman?->jenis) == 'SIHALAL' ? 'selected' : '' }}>
                    SIHALAL
                </option>

                <option value="OSS" {{ old('jenis', $pengumuman?->jenis) == 'OSS' ? 'selected' : '' }}>
                    OSS
                </option>
            </select>

            {!! $errors->first('jenis', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="foto" class="form-label">{{ __('Foto') }}</label>
            <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror"
                value="{{ old('foto', $pengumuman?->foto) }}" id="foto" placeholder="Foto"
                accept=".jpeg,.png,.jpg,.gif,.svg,.pdf">
            {!! $errors->first('foto', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="deskripsi" class="form-label">{{ __('Isi Pengumuman') }}</label>
            <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi"
                placeholder="Isi Pengumuman" rows="5">{{ old('deskripsi', $pengumuman?->deskripsi) }}</textarea>
            {!! $errors->first('deskripsi', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>


<script>
    ClassicEditor
        .create(document.querySelector('#deskripsi'))
        .catch(error => {
            console.error(error);
        });
</script>
