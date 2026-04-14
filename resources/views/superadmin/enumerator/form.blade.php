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
        <div class="form-group mb-2 mb20">
            <label for="bank_id" class="form-label">{{ __('Nama Bank') }}</label>
            <select name="bank_id" class="form-control select2-bank @error('bank_id') is-invalid @enderror"
                id="bank_id">
                <option value="">-- Pilih Bank --</option>
                @foreach ($banks as $bank)
                    <option value="{{ $bank->id }}"
                        {{ old('bank_id', $enumerator?->bank_id) == $bank->id ? 'selected' : '' }}>
                        {{ $bank->name }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('bank_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="no_rekening" class="form-label">{{ __('No. Rekening') }}</label>
            <input type="text" name="no_rekening" class="form-control @error('no_rekening') is-invalid @enderror"
                value="{{ old('no_rekening', $enumerator?->no_rekening) }}" id="no_rekening"
                placeholder="Nomor Rekening">
            {!! $errors->first('no_rekening', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="nama_rekening" class="form-label">{{ __('Nama Rekening') }}</label>
            <input type="text" name="nama_rekening" class="form-control @error('nama_rekening') is-invalid @enderror"
                value="{{ old('nama_rekening', $enumerator?->nama_rekening) }}" id="nama_rekening"
                placeholder="Nama Rekening">
            {!! $errors->first(
                'nama_rekening',
                '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>',
            ) !!}
        </div>
    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Sesuaikan tinggi Select2 dengan Bootstrap */
    .select2-container .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px);
        padding: 0.375rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        color: #212529;
        padding-left: 0;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + 0.75rem + 2px);
    }

    /* Error state */
    .is-invalid+.select2-container .select2-selection--single {
        border-color: #dc3545;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-bank').select2({
            placeholder: '-- Cari / Pilih Bank --',
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() {
                    return 'Bank tidak ditemukan';
                },
                searching: function() {
                    return 'Mencari...';
                }
            }
        });
    });
</script>
