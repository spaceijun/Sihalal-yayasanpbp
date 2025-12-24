@extends('layouts.app')

@section('template_title')
    {{ __('Update') }} Recruitment
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-animate">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} Recruitment</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('superadmin.recruitments.update', $recruitment->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('superadmin.recruitment.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
