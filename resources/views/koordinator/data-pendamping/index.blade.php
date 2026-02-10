@extends('layouts.app')

@section('template_title')
    Enumerators
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                @include('layouts.messages')
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Data Pendamping') }}
                            </span>

                            <div class="float-right">
                                {{-- <a href="{{ route('superadmin.enumerators.create') }}"
                                    class="btn btn-primary btn-sm float-right" data-placement="left">
                                    {{ __('Create New') }} --}}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>

                                        <th>Nama Lengkap</th>
                                        <th>Telephone</th>
                                        <th>Terbit SH</th>
                                        <th>Dibayar</th>
                                        {{-- <th>Alamat</th> --}}
                                        <th>Status</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($enumerators as $enumerator)
                                        @php
                                            $totalTerbitSh =
                                                $terbitSh->firstWhere('enumerator_id', $enumerator->id)?->total ?? 0;
                                            $totalDibayar =
                                                $dataDibayar->firstWhere('enumerator_id', $enumerator->id)?->total ?? 0;
                                        @endphp
                                        <tr>
                                            <td>{{ $enumerators->firstItem() + $loop->index }}</td>

                                            <td>{{ $enumerator->nama_lengkap }}</td>
                                            <td>{{ $enumerator->telephone }}</td>
                                            <td>{{ $totalTerbitSh }}</td>
                                            <td>{{ $totalDibayar }}</td>
                                            {{-- <td>{{ $enumerator->alamat }}</td> --}}
                                            <td>{{ $enumerator->status }}</td>

                                            <td>
                                                <a class="btn btn-sm btn-success"
                                                    href="{{ route('koordinator.data-pendamping.show', $enumerator->id) }}"><i
                                                        class="las la-eye"></i> {{ __('Detail') }}</a>
                                                <a class="btn btn-sm btn-primary"
                                                    href="{{ route('koordinator.data-pendamping.data-lapangan', $enumerator->id) }}">
                                                    <i class="las la-list"></i> {{ __('Data Lapangan') }}
                                                </a>
                                            </td>
                                        </tr>
                                        {{-- @include('superadmin.enumerator.partials.modal-enumerator') --}}
                                    @empty
                                        <tr>
                                            <td colspan="100%" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="las la-inbox la-3x mb-2"></i>
                                                    <p class="mb-0">{{ __('No data available') }}</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @include('layouts.pagination', ['paginator' => $enumerators])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
