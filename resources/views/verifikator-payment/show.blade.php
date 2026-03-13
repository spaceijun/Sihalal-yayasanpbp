@extends('layouts.app')

@section('template_title')
    {{ $verifikatorPayment->name ?? __('Show') . " " . __('Verifikator Payment') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col">
                <div class="card card-animate">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Verifikator Payment</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('verifikator-payments.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Verifikator Id:</strong>
                                    {{ $verifikatorPayment->verifikator_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Jumlah Data:</strong>
                                    {{ $verifikatorPayment->jumlah_data }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Total Nominal:</strong>
                                    {{ $verifikatorPayment->total_nominal }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Periode Dari:</strong>
                                    {{ $verifikatorPayment->periode_dari }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Periode Sampai:</strong>
                                    {{ $verifikatorPayment->periode_sampai }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Paid At:</strong>
                                    {{ $verifikatorPayment->paid_at }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
