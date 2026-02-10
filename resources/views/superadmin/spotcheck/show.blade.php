@extends('layouts.app')

@section('template_title')
    {{ $spotcheck->name ?? __('Show') . " " . __('Spotcheck') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col">
                <div class="card card-animate">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Spotcheck</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('spotchecks.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Data Lapangan Id:</strong>
                                    {{ $spotcheck->data_lapangan_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Nama Spotcheck:</strong>
                                    {{ $spotcheck->nama_spotcheck }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Tanggal Spotcheck:</strong>
                                    {{ $spotcheck->tanggal_spotcheck }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Foto Pu:</strong>
                                    {{ $spotcheck->foto_pu }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Hasil Spotcheck:</strong>
                                    {{ $spotcheck->hasil_spotcheck }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
