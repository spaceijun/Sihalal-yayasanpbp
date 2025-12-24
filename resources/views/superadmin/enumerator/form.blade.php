<div class="row padding-1 p-1">
    <div class="col-md-12">

        <div class="form-group mb-2 mb20">
            <label for="koordinator_id" class="form-label">{{ __('Nama Koordinator') }}</label>
            <select name="koordinator_id" class="form-control @error('koordinator_id') is-invalid @enderror"
                id="koordinator_id">
                <option value="">-- Pilih Koordinator --</option>
                @foreach ($koordinators as $koordinator)
                    <option value="{{ $koordinator->id }}"
                        {{ old('koordinator_id', $enumerator?->koordinator_id) == $koordinator->id ? 'selected' : '' }}>
                        {{ $koordinator->nama_lengkap }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first(
                'koordinator_id',
                '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>',
            ) !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="nama_lengkap" class="form-label">{{ __('Nama Lengkap') }}</label>
            <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror"
                value="{{ old('nama_lengkap', $enumerator?->nama_lengkap) }}" id="nama_lengkap"
                placeholder="Nama Lengkap">
            {!! $errors->first('nama_lengkap', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="telephone" class="form-label">{{ __('Telephone') }}</label>
            <input type="text" name="telephone" class="form-control @error('telephone') is-invalid @enderror"
                value="{{ old('telephone', $enumerator?->telephone) }}" id="telephone" placeholder="Telephone">
            {!! $errors->first('telephone', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="foto_diri" class="form-label">{{ __('Foto Diri') }}</label>
            <input type="file" name="foto_diri" class="form-control @error('foto_diri') is-invalid @enderror"
                value="{{ old('foto_diri', $enumerator?->foto_diri) }}" id="foto_diri" placeholder="foto_diri">
            {!! $errors->first('foto_diri', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="alamat" class="form-label">{{ __('Alamat') }}</label>
            <input type="text" name="alamat" class="form-control @error('alamat') is-invalid @enderror"
                value="{{ old('alamat', $enumerator?->alamat) }}" id="alamat" placeholder="Alamat">
            {!! $errors->first('alamat', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="" class="form-label">{{ __('Status') }}</label>
            <select name="status" class="form-control @error('status') is-invalid @enderror" id="">
                <option value="">{{ __('-- Pilih Status --') }}</option>
                <option value="Aktif" {{ old('status', $enumerator?->status) == 'Aktif' ? 'selected' : '' }}>
                    Aktif
                </option>
                <option value="Tidak Aktif"
                    {{ old('status', $enumerator?->status) == 'Tidak Aktif' ? 'selected' : '' }}>
                    Tidak Aktif
                </option>
            </select>
            {!! $errors->first('status', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>
