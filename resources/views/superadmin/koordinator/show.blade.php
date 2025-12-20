@extends('layouts.app')

@section('template_title')
    {{ $koordinator->name ?? __('Show') . " " . __('Koordinator') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col">
                <div class="card card-animate">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Koordinator</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('koordinators.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>User Id:</strong>
                                    {{ $koordinator->user_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Nama Lengkap:</strong>
                                    {{ $koordinator->nama_lengkap }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Email:</strong>
                                    {{ $koordinator->email }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Telephone:</strong>
                                    {{ $koordinator->telephone }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Alamat:</strong>
                                    {{ $koordinator->alamat }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Status:</strong>
                                    {{ $koordinator->status }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
