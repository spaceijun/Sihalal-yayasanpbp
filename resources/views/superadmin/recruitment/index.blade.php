@extends('layouts.app')

@section('template_title')
    Recruitments
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
                                {{ __('Recruitments') }}
                            </span>

                            <div class="float-right">
                                <a href="{{ route('superadmin.recruitments.create') }}"
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

                                        <th>Koordinator</th>
                                        <th>Nama Lengkap</th>
                                        <th>Telephone</th>
                                        {{-- <th>Alamat Lengkap</th>
                                        <th>Pengalaman</th> --}}
                                        <th>Rekomendasi</th>
                                        {{-- <th>Pendidikan Terakhir</th>
                                        <th>Foto Diri</th>
                                        <th>Foto Ktp</th> --}}
                                        <th>Status</th>
                                        {{-- <th>Alasan Penolakan</th> --}}

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recruitments as $recruitment)
                                        <tr>
                                            <td>{{ ++$i }}</td>

                                            <td>{{ $recruitment->koordinator->nama_lengkap ?? 'N/A' }}</td>
                                            <td>{{ $recruitment->nama_lengkap }}</td>
                                            <td>{{ $recruitment->telephone }}</td>
                                            {{-- <td>{{ $recruitment->alamat_lengkap }}</td>
                                            <td>{{ $recruitment->pengalaman }}</td> --}}
                                            <td>{{ $recruitment->rekomendasi }}</td>
                                            {{-- <td>{{ $recruitment->pendidikan_terakhir }}</td>
                                            <td>{{ $recruitment->foto_diri }}</td>
                                            <td>{{ $recruitment->foto_ktp }}</td> --}}
                                            <td>{{ $recruitment->status }}</td>
                                            {{-- <td>{{ $recruitment->alasan_penolakan }}</td> --}}

                                            <td>
                                                <form
                                                    action="{{ route('superadmin.recruitments.destroy', $recruitment->id) }}"
                                                    method="POST">
                                                    {{-- <button type="button" class="btn btn-sm btn-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#showModal{{ $recruitment->id }}">
                                                        <i class="las la-eye"></i> {{ __('Show') }}
                                                    </button> --}}
                                                    <a class="btn btn-sm btn-primary"
                                                        href="{{ route('superadmin.recruitments.show', $recruitment->id) }}"><i
                                                            class="las la-eye"></i> {{ __('Show') }}</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="event.preventDefault(); confirm('Are you sure to delete?') ? this.closest('form').submit() : false;"><i
                                                            class="las la-trash"></i> {{ __('Delete') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                        {{-- @include('superadmin.recruitment.partials.modal-recruitment') --}}
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
                        @include('layouts.pagination', ['paginator' => $recruitments])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
