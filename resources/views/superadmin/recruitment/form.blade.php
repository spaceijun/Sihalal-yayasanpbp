<div class="row padding-1 p-1">
    <div class="col-md-12">

        {{-- <div class="form-group mb-2 mb20">
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
        </div> --}}
        <div class="form-group mb-2 mb20">
            <label for="nama_lengkap" class="form-label">{{ __('Nama Lengkap') }}</label>
            <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror"
                value="{{ old('nama_lengkap', $recruitment?->nama_lengkap) }}" id="nama_lengkap"
                placeholder="Nama Lengkap">
            {!! $errors->first('nama_lengkap', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="telephone" class="form-label">{{ __('Telephone') }}</label>
            <input type="number" name="telephone" class="form-control @error('telephone') is-invalid @enderror"
                value="{{ old('telephone', $recruitment?->telephone) }}" id="telephone" placeholder="Telephone">
            {!! $errors->first('telephone', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="alamat_lengkap" class="form-label">{{ __('Alamat Lengkap') }}</label>
            <input type="text" name="alamat_lengkap"
                class="form-control @error('alamat_lengkap') is-invalid @enderror"
                value="{{ old('alamat_lengkap', $recruitment?->alamat_lengkap) }}" id="alamat_lengkap"
                placeholder="Alamat Lengkap">
            {!! $errors->first(
                'alamat_lengkap',
                '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>',
            ) !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="pengalaman" class="form-label">{{ __('Pengalaman') }}</label>
            <input type="text" name="pengalaman" class="form-control @error('pengalaman') is-invalid @enderror"
                value="{{ old('pengalaman', $recruitment?->pengalaman) }}" id="pengalaman" placeholder="Pengalaman">
            {!! $errors->first('pengalaman', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="rekomendasi" class="form-label">{{ __('Rekomendasi') }}</label>
            <select name="rekomendasi" id="rekomendasi" class="form-control @error('rekomendasi') is-invalid @enderror">

                <option value="">-- Pilih Rekomendasi --</option>

                @php
                    $daftarRekomendasi = [
                        'Adi Tarman',
                        'M. Faizun Aziz',
                        'Ade Sofyan',
                        'Agil Praditya Putu Yazier',
                        'Ahmad Nurohim',
                        'Zaenal Arifin',
                    ];
                @endphp

                @foreach ($daftarRekomendasi as $nama)
                    <option value="{{ $nama }}"
                        {{ old('rekomendasi', $recruitment?->rekomendasi) == $nama ? 'selected' : '' }}>
                        {{ $nama }}
                    </option>
                @endforeach
            </select>
            <small class="text-danger">Jika tidak ada, kosongkan saja</small></small>

            {!! $errors->first('rekomendasi', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="pendidikan_terakhir" class="form-label">{{ __('Pendidikan Terakhir') }}</label>
            <select name="pendidikan_terakhir" id="pendidikan_terakhir"
                class="form-control @error('pendidikan_terakhir') is-invalid @enderror">

                <option value="">-- Pilih Pendidikan Terakhir --</option>

                @php
                    $pendidikanList = [
                        'SD / Paket A / Sederajat',
                        'SMP / Paket B / Sederajat',
                        'SMA / SMK / Paket C / Sederajat',
                        'D1',
                        'D2',
                        'D3',
                        'S1',
                        'S2',
                        'S3',
                    ];
                @endphp

                @foreach ($pendidikanList as $pendidikan)
                    <option value="{{ $pendidikan }}"
                        {{ old('pendidikan_terakhir', $recruitment?->pendidikan_terakhir) == $pendidikan ? 'selected' : '' }}>
                        {{ $pendidikan }}
                    </option>
                @endforeach
            </select>

            {!! $errors->first(
                'pendidikan_terakhir',
                '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>',
            ) !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="foto_diri" class="form-label">{{ __('Foto Diri') }}</label>
            <small class="text-danger">*Foto 3x4</small>
            <input type="file" name="foto_diri" class="form-control @error('foto_diri') is-invalid @enderror"
                value="{{ old('foto_diri', $recruitment?->foto_diri) }}" id="foto_diri" placeholder="Foto Diri">
            {!! $errors->first('foto_diri', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="foto_ktp" class="form-label">{{ __('Foto Ktp') }}</label>
            <input type="file" name="foto_ktp" class="form-control @error('foto_ktp') is-invalid @enderror"
                value="{{ old('foto_ktp', $recruitment?->foto_ktp) }}" id="foto_ktp" placeholder="Foto Ktp">
            {!! $errors->first('foto_ktp', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <input type="hidden" name="status" class="form-control @error('status') is-invalid @enderror" value="Melamar"
            id="status" placeholder="Status">
        {{-- <div class="form-group mb-2 mb20">
            <label for="alasan_penolakan" class="form-label">{{ __('Alasan Penolakan') }}</label>
            <input type="text" name="alasan_penolakan"
                class="form-control @error('alasan_penolakan') is-invalid @enderror"
                value="{{ old('alasan_penolakan', $recruitment?->alasan_penolakan) }}" id="alasan_penolakan"
                placeholder="Alasan Penolakan">
            {!! $errors->first(
                'alasan_penolakan',
                '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>',
            ) !!}
        </div> --}}


    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>
