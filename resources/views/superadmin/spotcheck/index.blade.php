@extends('layouts.app')

@section('template_title')
    Spotchecks
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
                                {{ __('Spotchecks') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('superadmin.spotchecks.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
                                  {{ __('Create New') }}
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
                                        
									<th >Data Lapangan Id</th>
									<th >Nama Spotcheck</th>
									<th >Tanggal Spotcheck</th>
									<th >Foto Pu</th>
									<th >Hasil Spotcheck</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($spotchecks as $spotcheck)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
										<td >{{ $spotcheck->data_lapangan_id }}</td>
										<td >{{ $spotcheck->nama_spotcheck }}</td>
										<td >{{ $spotcheck->tanggal_spotcheck }}</td>
										<td >{{ $spotcheck->foto_pu }}</td>
										<td >{{ $spotcheck->hasil_spotcheck }}</td>

                                            <td>
                                                <form action="{{ route('superadmin.spotchecks.destroy', $spotcheck->id) }}" method="POST">
                                                <button type="button" class="btn btn-sm btn-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#showModal{{ $spotcheck->id }}">
                                                        <i class="las la-eye"></i> {{ __('Show') }}
                                                    </button>                                                    
                                                    <a class="btn btn-sm btn-success" href="{{ route('superadmin.spotchecks.edit', $spotcheck->id) }}"><i class="las la-edit"></i> {{ __('Edit') }}</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="event.preventDefault(); confirm('Are you sure to delete?') ? this.closest('form').submit() : false;"><i class="las la-trash"></i> {{ __('Delete') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @include('superadmin.spotcheck.partials.modal-spotcheck')
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
                         @include('layouts.pagination', ['paginator' => $spotchecks])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
