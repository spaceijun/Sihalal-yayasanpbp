@extends('layouts.app')

@section('template_title')
    {{ $cashflow->name ?? __('Show') . " " . __('Cashflow') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col">
                <div class="card card-animate">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Cashflow</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('cashflows.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Tipe:</strong>
                                    {{ $cashflow->tipe }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Jumlah:</strong>
                                    {{ $cashflow->jumlah }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Keterangan:</strong>
                                    {{ $cashflow->keterangan }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
