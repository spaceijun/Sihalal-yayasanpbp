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
                                {{ __('Enumerators') }}
                            </span>

                            <div class="float-right">
                                <a href="{{ route('superadmin.enumerators.create') }}"
                                    class="btn btn-primary btn-sm float-right" data-placement="left">
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

                                        <th>Koordinator Id</th>
                                        <th>Nama Lengkap</th>
                                        <th>Telephone</th>
                                        <th>Alamat</th>
                                        <th>Status</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($enumerators as $enumerator)
                                        <tr>
                                            <td>{{ ++$i }}</td>

                                            <td>{{ $enumerator->koordinator->nama_lengkap }}</td>
                                            <td>{{ $enumerator->nama_lengkap }}</td>
                                            <td>{{ $enumerator->telephone }}</td>
                                            <td>{{ $enumerator->alamat }}</td>
                                            <td>{{ $enumerator->status }}</td>

                                            <td>
                                                <form
                                                    action="{{ route('superadmin.enumerators.destroy', $enumerator->id) }}"
                                                    method="POST">
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#showModal{{ $enumerator->id }}">
                                                        <i class="las la-eye"></i> {{ __('Show') }}
                                                    </button>
                                                    <a class="btn btn-sm btn-success"
                                                        href="{{ route('superadmin.enumerators.edit', $enumerator->id) }}"><i
                                                            class="las la-edit"></i> {{ __('Edit') }}</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="event.preventDefault(); confirm('Are you sure to delete?') ? this.closest('form').submit() : false;"><i
                                                            class="las la-trash"></i> {{ __('Delete') }}</button>
                                                </form>
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
