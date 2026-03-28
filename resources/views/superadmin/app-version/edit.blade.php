@extends('layouts.app')

@section('template_title')
    {{ __('Update') }} App Version
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-animate">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} App Version</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('superadmin.app-versions.update', $appVersion->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('superadmin.app-version.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
