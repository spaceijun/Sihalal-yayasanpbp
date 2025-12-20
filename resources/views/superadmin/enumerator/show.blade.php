@extends('layouts.app')

@section('template_title')
    {{ $enumerator->name ?? __('Show') . " " . __('Enumerator') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col">
                <div class="card card-animate">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Enumerator</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('enumerators.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Koordinator Id:</strong>
                                    {{ $enumerator->koordinator_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Nama Lengkap:</strong>
                                    {{ $enumerator->nama_lengkap }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Telephone:</strong>
                                    {{ $enumerator->telephone }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Alamat:</strong>
                                    {{ $enumerator->alamat }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Status:</strong>
                                    {{ $enumerator->status }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
