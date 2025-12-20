@extends('layouts.app')

@section('template_title')
    {{ __('Update') }} Koordinator
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-animate">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} Koordinator</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('superadmin.koordinators.update', $koordinator->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('superadmin.koordinator.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
