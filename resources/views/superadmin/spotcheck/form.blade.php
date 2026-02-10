<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="data_lapangan_id" class="form-label">{{ __('Data Lapangan Id') }}</label>
            <input type="text" name="data_lapangan_id" class="form-control @error('data_lapangan_id') is-invalid @enderror" value="{{ old('data_lapangan_id', $spotcheck?->data_lapangan_id) }}" id="data_lapangan_id" placeholder="Data Lapangan Id">
            {!! $errors->first('data_lapangan_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="nama_spotcheck" class="form-label">{{ __('Nama Spotcheck') }}</label>
            <input type="text" name="nama_spotcheck" class="form-control @error('nama_spotcheck') is-invalid @enderror" value="{{ old('nama_spotcheck', $spotcheck?->nama_spotcheck) }}" id="nama_spotcheck" placeholder="Nama Spotcheck">
            {!! $errors->first('nama_spotcheck', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="tanggal_spotcheck" class="form-label">{{ __('Tanggal Spotcheck') }}</label>
            <input type="text" name="tanggal_spotcheck" class="form-control @error('tanggal_spotcheck') is-invalid @enderror" value="{{ old('tanggal_spotcheck', $spotcheck?->tanggal_spotcheck) }}" id="tanggal_spotcheck" placeholder="Tanggal Spotcheck">
            {!! $errors->first('tanggal_spotcheck', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="foto_pu" class="form-label">{{ __('Foto Pu') }}</label>
            <input type="text" name="foto_pu" class="form-control @error('foto_pu') is-invalid @enderror" value="{{ old('foto_pu', $spotcheck?->foto_pu) }}" id="foto_pu" placeholder="Foto Pu">
            {!! $errors->first('foto_pu', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="hasil_spotcheck" class="form-label">{{ __('Hasil Spotcheck') }}</label>
            <input type="text" name="hasil_spotcheck" class="form-control @error('hasil_spotcheck') is-invalid @enderror" value="{{ old('hasil_spotcheck', $spotcheck?->hasil_spotcheck) }}" id="hasil_spotcheck" placeholder="Hasil Spotcheck">
            {!! $errors->first('hasil_spotcheck', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>