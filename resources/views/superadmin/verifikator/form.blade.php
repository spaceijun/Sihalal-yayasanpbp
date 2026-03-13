<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="nama_lengkap" class="form-label">{{ __('Nama Lengkap') }}</label>
            <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap', $verifikator?->nama_lengkap) }}" id="nama_lengkap" placeholder="Nama Lengkap">
            {!! $errors->first('nama_lengkap', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="telephone" class="form-label">{{ __('Telephone') }}</label>
            <input type="text" name="telephone" class="form-control @error('telephone') is-invalid @enderror" value="{{ old('telephone', $verifikator?->telephone) }}" id="telephone" placeholder="Telephone">
            {!! $errors->first('telephone', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="alamat_lengkap" class="form-label">{{ __('Alamat Lengkap') }}</label>
            <input type="text" name="alamat_lengkap" class="form-control @error('alamat_lengkap') is-invalid @enderror" value="{{ old('alamat_lengkap', $verifikator?->alamat_lengkap) }}" id="alamat_lengkap" placeholder="Alamat Lengkap">
            {!! $errors->first('alamat_lengkap', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="rate_per_data" class="form-label">{{ __('Rate Per Data') }}</label>
            <input type="text" name="rate_per_data" class="form-control @error('rate_per_data') is-invalid @enderror" value="{{ old('rate_per_data', $verifikator?->rate_per_data) }}" id="rate_per_data" placeholder="Rate Per Data">
            {!! $errors->first('rate_per_data', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>