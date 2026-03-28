@extends('layouts.app')

@section('template_title')
    {{ $appVersion->name ?? __('Show') . " " . __('App Version') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col">
                <div class="card card-animate">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} App Version</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('app-versions.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Version:</strong>
                                    {{ $appVersion->version }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Build Number:</strong>
                                    {{ $appVersion->build_number }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Changelog:</strong>
                                    {{ $appVersion->changelog }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Force Update:</strong>
                                    {{ $appVersion->force_update }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Download Url:</strong>
                                    {{ $appVersion->download_url }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
