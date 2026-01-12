@extends('layouts.app')

@section('template_title')
    Data Revisi
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
                                {{ __('Data Revisi') }}
                            </span>

                            <div class="float-right">
                            </div>
                        </div>
                    </div>
                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>
                                        <th>Created</th>
                                        <th>Nama Pendamping</th>
                                        <th>Nama PU</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($dataLapangans as $dataLapangan)
                                        <tr>
                                            <td>{{ ++$i }}</td>

                                            <td>{{ $dataLapangan->created_at }}</td>
                                            <td>{{ $dataLapangan->enumerator->nama_lengkap }}</td>
                                            <td>{{ $dataLapangan->nama_pu }}</td>
                                            <td> <span class="badge bg-warning">{{ $dataLapangan->status }}</span></td>
                                            <td>{{ $dataLapangan->keterangan }}</td>


                                        </tr>
                                        {{-- @include('superadmin.koordinator.partials.modal-koordinator') --}}
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
                        @include('layouts.pagination', ['paginator' => $dataLapangans])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
