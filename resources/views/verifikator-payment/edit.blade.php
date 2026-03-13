@extends('layouts.app')

@section('template_title')
    {{ __('Update') }} Verifikator Payment
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-animate">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} Verifikator Payment</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('superadmin.verifikator-payments.update', $verifikatorPayment->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('superadmin.verifikator-payment.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
