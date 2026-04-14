<div class="row padding-1 p-1">
    <div class="col-md-12">

        {{-- <div class="form-group mb-2 mb20">
            <label for="user_id" class="form-label">{{ __('User Id') }}</label>
            <input type="text" name="user_id" class="form-control @error('user_id') is-invalid @enderror"
                value="{{ old('user_id', $dataEntry?->user_id) }}" id="user_id" placeholder="User Id">
            {!! $errors->first('user_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div> --}}
        <div class="form-group mb-2 mb20">
            <label for="nama_lengkap" class="form-label">{{ __('Nama Lengkap') }}</label>
            <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror"
                value="{{ old('nama_lengkap', $dataEntry?->nama_lengkap) }}" id="nama_lengkap" placeholder="Nama Lengkap">
            {!! $errors->first('nama_lengkap', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input type="text" name="email" class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email', $dataEntry?->email) }}" id="email" placeholder="Email">
            {!! $errors->first('email', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="telephone" class="form-label">{{ __('Telephone') }}</label>
            <input type="text" name="telephone" class="form-control @error('telephone') is-invalid @enderror"
                value="{{ old('telephone', $dataEntry?->telephone) }}" id="telephone" placeholder="Telephone">
            {!! $errors->first('telephone', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="alamat" class="form-label">{{ __('Alamat') }}</label>
            <input type="text" name="alamat" class="form-control @error('alamat') is-invalid @enderror"
                value="{{ old('alamat', $dataEntry?->alamat) }}" id="alamat" placeholder="Alamat">
            {!! $errors->first('alamat', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <label for="" class="form-label">{{ __('Status') }}</label>
        <select name="status" class="form-control @error('status') is-invalid @enderror" id="">
            <option value="">{{ __('-- Pilih Status --') }}</option>
            <option value="Aktif" {{ old('status', $dataEntry?->status) == 'Aktif' ? 'selected' : '' }}>
                Aktif
            </option>
            <option value="Tidak Aktif" {{ old('status', $dataEntry?->status) == 'Tidak Aktif' ? 'selected' : '' }}>
                Tidak Aktif
            </option>
        </select>
        {!! $errors->first('status', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
    </div>
    <div class="form-group mb-2 mb20">
        <label for="" class="form-label">{{ __('Entry Type') }}</label>
        <select name="entry_type" class="form-control @error('entry_type') is-invalid @enderror"
            {{ isset($dataEntry->id) ? 'disabled' : '' }}>

            <option value="">-- Pilih Entry Type --</option>
            <option value="OSS" {{ old('entry_type', $dataEntry?->entry_type) == 'OSS' ? 'selected' : '' }}>OSS
            </option>
            <option value="SIHALAL" {{ old('entry_type', $dataEntry?->entry_type) == 'SIHALAL' ? 'selected' : '' }}>
                SIHALAL</option>
        </select>

        @if (isset($dataEntry->id))
            <input type="hidden" name="entry_type" value="{{ $dataEntry->entry_type }}">
        @endif

        {!! $errors->first('entry_type', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
    </div>
    <div class="form-group mb-2 mb20">
        <label class="form-label">{{ __('Koordinator') }}</label>
        <select name="koordinator_ids[]" class="form-control select2 @error('koordinator_ids') is-invalid @enderror"
            multiple>
            @foreach ($koordinators as $koordinator)
                <option value="{{ $koordinator->id }}"
                    {{ in_array($koordinator->id, old('koordinator_ids', $selectedKoordinatorIds ?? [])) ? 'selected' : '' }}>
                    {{ $koordinator->nama_lengkap }}
                </option>
            @endforeach
        </select>
        {!! $errors->first(
            'koordinator_ids',
            '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>',
        ) !!}
    </div>

    <div class="form-group mb-2 mb20">
        <label for="password" class="form-label">{{ __('Password Login') }}</label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
            value="{{ old('password', $dataEntry?->password) }}" id="password" placeholder="password">
        {!! $errors->first('password', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        <small class="text-danger">* Kosongkan jika tidak ingin mengubah password</small>
    </div>


</div>
<div class="col-md-12 mt20 mt-2">
    <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
</div>
</div>

<style>
    /* Sesuaikan Select2 dengan Bootstrap Velzone */
    .select2-container--default .select2-selection--multiple {
        background-color: #fff;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        padding: 0.275rem 0.5rem;
        min-height: calc(1.5em + 0.75rem + 2px);
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #405189;
        /* warna primary Velzone */
        border: none;
        border-radius: 0.25rem;
        color: #fff;
        padding: 2px 8px;
        font-size: 0.8rem;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #fff;
        margin-right: 5px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #ffcdd2;
        background-color: transparent;
    }

    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #405189;
        box-shadow: 0 0 0 0.25rem rgba(64, 81, 137, 0.25);
    }

    .select2-dropdown {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #40896c;
    }
</style>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: '-- Pilih Koordinator --',
            allowClear: true
        });
    });
</script>
