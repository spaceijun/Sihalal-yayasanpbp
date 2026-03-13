<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="verifikator_id" class="form-label">{{ __('Verifikator Id') }}</label>
            <input type="text" name="verifikator_id" class="form-control @error('verifikator_id') is-invalid @enderror" value="{{ old('verifikator_id', $verifikatorPayment?->verifikator_id) }}" id="verifikator_id" placeholder="Verifikator Id">
            {!! $errors->first('verifikator_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="jumlah_data" class="form-label">{{ __('Jumlah Data') }}</label>
            <input type="text" name="jumlah_data" class="form-control @error('jumlah_data') is-invalid @enderror" value="{{ old('jumlah_data', $verifikatorPayment?->jumlah_data) }}" id="jumlah_data" placeholder="Jumlah Data">
            {!! $errors->first('jumlah_data', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="total_nominal" class="form-label">{{ __('Total Nominal') }}</label>
            <input type="text" name="total_nominal" class="form-control @error('total_nominal') is-invalid @enderror" value="{{ old('total_nominal', $verifikatorPayment?->total_nominal) }}" id="total_nominal" placeholder="Total Nominal">
            {!! $errors->first('total_nominal', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="periode_dari" class="form-label">{{ __('Periode Dari') }}</label>
            <input type="text" name="periode_dari" class="form-control @error('periode_dari') is-invalid @enderror" value="{{ old('periode_dari', $verifikatorPayment?->periode_dari) }}" id="periode_dari" placeholder="Periode Dari">
            {!! $errors->first('periode_dari', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="periode_sampai" class="form-label">{{ __('Periode Sampai') }}</label>
            <input type="text" name="periode_sampai" class="form-control @error('periode_sampai') is-invalid @enderror" value="{{ old('periode_sampai', $verifikatorPayment?->periode_sampai) }}" id="periode_sampai" placeholder="Periode Sampai">
            {!! $errors->first('periode_sampai', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="paid_at" class="form-label">{{ __('Paid At') }}</label>
            <input type="text" name="paid_at" class="form-control @error('paid_at') is-invalid @enderror" value="{{ old('paid_at', $verifikatorPayment?->paid_at) }}" id="paid_at" placeholder="Paid At">
            {!! $errors->first('paid_at', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>