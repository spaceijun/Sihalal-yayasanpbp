<div class="row padding-1 p-1">
    <div class="col-md-12">

        {{-- Version --}}
        <div class="form-group mb-2">
            <label for="version" class="form-label">{{ __('Version') }}</label>
            <input type="text" name="version" class="form-control @error('version') is-invalid @enderror"
                value="{{ old('version', $appVersion?->version) }}" id="version" placeholder="contoh: 1.2.0">
            {!! $errors->first('version', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
        </div>

        {{-- Build Number --}}
        <div class="form-group mb-2">
            <label for="build_number" class="form-label">{{ __('Build Number') }}</label>
            <input type="number" name="build_number" class="form-control @error('build_number') is-invalid @enderror"
                value="{{ old('build_number', $appVersion?->build_number) }}" id="build_number" placeholder="contoh: 3">
            <small class="text-muted">Harus lebih tinggi dari versi sebelumnya.</small>
            {!! $errors->first('build_number', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
        </div>

        {{-- Changelog --}}
        <div class="form-group mb-2">
            <label for="changelog" class="form-label">{{ __('Changelog') }}</label>
            <textarea name="changelog" rows="3" class="form-control @error('changelog') is-invalid @enderror" id="changelog"
                placeholder="contoh: Tambah fitur laporan, fix bug login">{{ old('changelog', $appVersion?->changelog) }}</textarea>
            {!! $errors->first('changelog', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
        </div>

        {{-- Force Update --}}
        <div class="form-group mb-2">
            <label class="form-label d-block">{{ __('Force Update') }}</label>
            <div class="form-check form-switch">
                <input type="hidden" name="force_update" value="0">
                <input type="checkbox" name="force_update" value="1"
                    class="form-check-input @error('force_update') is-invalid @enderror" id="force_update"
                    {{ old('force_update', $appVersion?->force_update) ? 'checked' : '' }}>
                <label class="form-check-label" for="force_update">
                    Wajibkan user update (tidak bisa skip)
                </label>
            </div>
            <small class="text-muted">Aktifkan hanya untuk update kritis.</small>
            {!! $errors->first('force_update', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
        </div>

        {{-- Download URL --}}
        <div class="form-group mb-2">
            <label for="download_url" class="form-label">{{ __('Download URL') }}</label>
            <input type="url" name="download_url" class="form-control @error('download_url') is-invalid @enderror"
                value="{{ old('download_url', $appVersion?->download_url) }}" id="download_url"
                placeholder="https://domain.com/storage/apk/app-v1.2.0.apk">
            {!! $errors->first('download_url', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
        </div>

    </div>

    <div class="col-md-12 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>
