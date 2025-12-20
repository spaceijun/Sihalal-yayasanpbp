@extends('layouts.app')

@section('template_title')
    {{ __('Update') }} Enumerator
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-animate">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} Enumerator</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('superadmin.enumerators.update', $enumerator->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('superadmin.enumerator.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
