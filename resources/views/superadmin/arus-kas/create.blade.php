@extends('layouts.app')

@section('template_title')
    {{ __('Create') }} Cashflow
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col">

                <div class="card card-animate">
                    <div class="card-header">
                        <span class="card-title">{{ __('Create') }} Cashflow</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('superadmin.arus-kas.store') }}" role="form"
                            enctype="multipart/form-data">
                            @csrf

                            @include('superadmin.arus-kas.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
