@extends('layouts.app')

@section('template_title')
    {{ $dataEntry->name ?? __('Show') . " " . __('Data Entry') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col">
                <div class="card card-animate">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Data Entry</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('data-entries.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>User Id:</strong>
                                    {{ $dataEntry->user_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Nama Lengkap:</strong>
                                    {{ $dataEntry->nama_lengkap }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Email:</strong>
                                    {{ $dataEntry->email }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Telephone:</strong>
                                    {{ $dataEntry->telephone }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Alamat:</strong>
                                    {{ $dataEntry->alamat }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Status:</strong>
                                    {{ $dataEntry->status }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
