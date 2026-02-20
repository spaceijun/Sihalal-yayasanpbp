@extends('layouts.app')

@section('template_title')
    Data Entries
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
                                {{ __('Data Entries') }}
                            </span>

                            <div class="float-right">
                                <a href="{{ route('superadmin.data-entries.create') }}"
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

                                        <th>Nama Lengkap</th>
                                        <th>Email</th>
                                        <th>Telephone</th>
                                        <th>Alamat</th>
                                        <th>Status</th>
                                        <th>Entry Type</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($dataEntries as $dataEntry)
                                        <tr>
                                            <td>{{ ++$i }}</td>

                                            <td>{{ $dataEntry->nama_lengkap }}</td>
                                            <td>{{ $dataEntry->email }}</td>
                                            <td>{{ $dataEntry->telephone }}</td>
                                            <td>{{ $dataEntry->alamat }}</td>
                                            <td>{{ $dataEntry->status }}</td>
                                            <td><span class="badge bg-primary">{{ $dataEntry->entry_type }}</span></td>

                                            <td>
                                                <form
                                                    action="{{ route('superadmin.data-entries.destroy', $dataEntry->id) }}"
                                                    method="POST">
                                                    {{-- <button type="button" class="btn btn-sm btn-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#showModal{{ $dataEntry->id }}">
                                                        <i class="las la-eye"></i> {{ __('Show') }}
                                                    </button> --}}
                                                    <a class="btn btn-sm btn-success"
                                                        href="{{ route('superadmin.data-entries.edit', $dataEntry->id) }}"><i
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
                        @include('layouts.pagination', ['paginator' => $dataEntries])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
