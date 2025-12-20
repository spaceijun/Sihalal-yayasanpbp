<div class="row padding-1 p-1">
    <div class="col-md-12">

        <div class="form-group mb-2 mb20">
            <label for="tipe" class="form-label">{{ __('Tipe') }}</label>
            <select name="tipe" class="form-control @error('tipe') is-invalid @enderror" id="tipe">
                <option value="">-- Pilih Tipe --</option>
                <option value="Pemasukan" {{ old('tipe', $cashflow?->tipe) == 'Pemasukan' ? 'selected' : '' }}>Pemasukan
                </option>
                <option value="Pengeluaran" {{ old('tipe', $cashflow?->tipe) == 'Pengeluaran' ? 'selected' : '' }}>
                    Pengeluaran</option>
                <option value="Kas" {{ old('tipe', $cashflow?->tipe) == 'Kas' ? 'selected' : '' }}>Kas</option>
            </select>
            {!! $errors->first('tipe', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="jumlah" class="form-label">{{ __('Jumlah') }}</label>
            <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror"
                value="{{ old('jumlah', $cashflow?->jumlah) }}" id="jumlah" placeholder="Jumlah">
            {!! $errors->first('jumlah', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="keterangan" class="form-label">{{ __('Keterangan') }}</label>
            <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" id="keterangan"
                rows="5" placeholder="Keterangan">{{ old('keterangan', $cashflow?->keterangan) }}</textarea>
            {!! $errors->first('keterangan', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>

<!-- CKEditor 5 CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#keterangan'), {
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
                'blockQuote', 'insertTable', '|',
                'undo', 'redo'
            ]
        })
        .catch(error => {
            console.error(error);
        });
</script>
