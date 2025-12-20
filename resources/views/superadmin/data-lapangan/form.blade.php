<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="enumerator_id" class="form-label">{{ __('Enumerator Id') }}</label>
            <input type="text" name="enumerator_id" class="form-control @error('enumerator_id') is-invalid @enderror" value="{{ old('enumerator_id', $dataLapangan?->enumerator_id) }}" id="enumerator_id" placeholder="Enumerator Id">
            {!! $errors->first('enumerator_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="nama_pu" class="form-label">{{ __('Nama Pu') }}</label>
            <input type="text" name="nama_pu" class="form-control @error('nama_pu') is-invalid @enderror" value="{{ old('nama_pu', $dataLapangan?->nama_pu) }}" id="nama_pu" placeholder="Nama Pu">
            {!! $errors->first('nama_pu', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="nik" class="form-label">{{ __('Nik') }}</label>
            <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik', $dataLapangan?->nik) }}" id="nik" placeholder="Nik">
            {!! $errors->first('nik', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="rt" class="form-label">{{ __('Rt') }}</label>
            <input type="text" name="rt" class="form-control @error('rt') is-invalid @enderror" value="{{ old('rt', $dataLapangan?->rt) }}" id="rt" placeholder="Rt">
            {!! $errors->first('rt', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="rw" class="form-label">{{ __('Rw') }}</label>
            <input type="text" name="rw" class="form-control @error('rw') is-invalid @enderror" value="{{ old('rw', $dataLapangan?->rw) }}" id="rw" placeholder="Rw">
            {!! $errors->first('rw', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="alamat" class="form-label">{{ __('Alamat') }}</label>
            <input type="text" name="alamat" class="form-control @error('alamat') is-invalid @enderror" value="{{ old('alamat', $dataLapangan?->alamat) }}" id="alamat" placeholder="Alamat">
            {!! $errors->first('alamat', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="titik_koordinat" class="form-label">{{ __('Titik Koordinat') }}</label>
            <input type="text" name="titik_koordinat" class="form-control @error('titik_koordinat') is-invalid @enderror" value="{{ old('titik_koordinat', $dataLapangan?->titik_koordinat) }}" id="titik_koordinat" placeholder="Titik Koordinat">
            {!! $errors->first('titik_koordinat', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="foto_ktp" class="form-label">{{ __('Foto Ktp') }}</label>
            <input type="text" name="foto_ktp" class="form-control @error('foto_ktp') is-invalid @enderror" value="{{ old('foto_ktp', $dataLapangan?->foto_ktp) }}" id="foto_ktp" placeholder="Foto Ktp">
            {!! $errors->first('foto_ktp', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="foto_rumah" class="form-label">{{ __('Foto Rumah') }}</label>
            <input type="text" name="foto_rumah" class="form-control @error('foto_rumah') is-invalid @enderror" value="{{ old('foto_rumah', $dataLapangan?->foto_rumah) }}" id="foto_rumah" placeholder="Foto Rumah">
            {!! $errors->first('foto_rumah', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="foto_pendamping" class="form-label">{{ __('Foto Pendamping') }}</label>
            <input type="text" name="foto_pendamping" class="form-control @error('foto_pendamping') is-invalid @enderror" value="{{ old('foto_pendamping', $dataLapangan?->foto_pendamping) }}" id="foto_pendamping" placeholder="Foto Pendamping">
            {!! $errors->first('foto_pendamping', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="foto_proses" class="form-label">{{ __('Foto Proses') }}</label>
            <input type="text" name="foto_proses" class="form-control @error('foto_proses') is-invalid @enderror" value="{{ old('foto_proses', $dataLapangan?->foto_proses) }}" id="foto_proses" placeholder="Foto Proses">
            {!! $errors->first('foto_proses', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="foto_produk" class="form-label">{{ __('Foto Produk') }}</label>
            <input type="text" name="foto_produk" class="form-control @error('foto_produk') is-invalid @enderror" value="{{ old('foto_produk', $dataLapangan?->foto_produk) }}" id="foto_produk" placeholder="Foto Produk">
            {!! $errors->first('foto_produk', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>