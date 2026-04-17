@extends('layouts.app')

@section('template_title')
    Pengumuman Data Entry
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
                                {{ __('Pengumuman Data Entry') }}
                            </span>

                            <div class="float-right">
                                <a href="{{ route('superadmin.pengumumen.create') }}"
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

                                        <th>No Pengumuman</th>
                                        <th>Judul Pengumuman</th>
                                        <th>Jenis Pengumuman</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pengumumen as $pengumuman)
                                        <tr>
                                            <td>{{ ++$i }}</td>

                                            <td><span class="badge badge-gradient-info">{{ $pengumuman->nomor }}</span>
                                            <td>{{ $pengumuman->judul }}</td>
                                            <td>{{ $pengumuman->jenis }}</td>

                                            <td>
                                                <form action="{{ route('superadmin.pengumumen.destroy', $pengumuman->id) }}"
                                                    method="POST">
                                                    <a class="btn btn-sm btn-primary"
                                                        href="{{ route('superadmin.pengumumen.show', $pengumuman->hashed_id) }}">
                                                        <i class="las la-eye"></i> {{ __('Detail') }}
                                                    </a>
                                                    <a class="btn btn-sm btn-success"
                                                        href="{{ route('superadmin.pengumumen.edit', $pengumuman->hashed_id) }}"><i
                                                            class="las la-edit"></i> {{ __('Edit') }}</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="event.preventDefault(); confirm('Are you sure to delete?') ? this.closest('form').submit() : false;"><i
                                                            class="las la-trash"></i> {{ __('Delete') }}</button>
                                                </form>
                                            </td>
                                        </tr>
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
                        @include('layouts.pagination', ['paginator' => $pengumumen])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
