@extends('layouts.app')

@section('template_title')
    {{ $verifikator->name ?? __('Show') . " " . __('Verifikator') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col">
                <div class="card card-animate">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Verifikator</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('verifikators.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Nama Lengkap:</strong>
                                    {{ $verifikator->nama_lengkap }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Telephone:</strong>
                                    {{ $verifikator->telephone }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Alamat Lengkap:</strong>
                                    {{ $verifikator->alamat_lengkap }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Rate Per Data:</strong>
                                    {{ $verifikator->rate_per_data }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
