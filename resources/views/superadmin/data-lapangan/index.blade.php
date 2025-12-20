@extends('layouts.app')

@section('template_title')
    Data Lapangans
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
                                {{ __('Data Lapangans') }}
                            </span>

                            <div class="float-right">
                                <a href="{{ route('superadmin.data-lapangans.create') }}"
                                    class="btn btn-primary btn-sm float-right" data-placement="left">
                                    {{ __('Create New') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Form Search -->
                    <div class="card-body bg-white border-bottom">
                        <form action="{{ route('superadmin.data-lapangans.index') }}" method="GET">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="nama_pu" class="form-label">Nama PU</label>
                                    <input type="text" class="form-control" id="nama_pu" name="nama_pu"
                                        placeholder="Cari berdasarkan nama PU..." value="{{ request('nama_pu') }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="" class="form-label">Status</label>
                                    <select class="form-control" id="" name="status">
                                        <option value="">Semua Status</option>
                                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif
                                        </option>
                                        <option value="tidak_aktif"
                                            {{ request('status') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                            Pending</option>
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="las la-search"></i> Cari
                                    </button>
                                    <a href="{{ route('superadmin.data-lapangans.index') }}" class="btn btn-secondary">
                                        <i class="las la-redo"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>
                                        <th>Pendamping</th>
                                        <th>Nama PU</th>
                                        <th>NIK</th>
                                        <th>Alamat</th>
                                        <th>Koordinat</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($dataLapangans as $dataLapangan)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $dataLapangan->enumerator->nama_lengkap }}</td>
                                            <td>{{ $dataLapangan->nama_pu }}</td>
                                            <td>{{ $dataLapangan->nik }}</td>
                                            <td>{{ $dataLapangan->alamat }}, {{ $dataLapangan->rt }},
                                                {{ $dataLapangan->rw }}</td>
                                            <td>{{ $dataLapangan->titik_koordinat }}</td>
                                            <td>
                                                @if (isset($dataLapangan->status))
                                                    <span
                                                        class="badge bg-{{ $dataLapangan->status == 'aktif' ? 'success' : ($dataLapangan->status == 'pending' ? 'warning' : 'secondary') }}">
                                                        {{ ucfirst(str_replace('_', ' ', $dataLapangan->status)) }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <form
                                                    action="{{ route('superadmin.data-lapangans.destroy', $dataLapangan->id) }}"
                                                    method="POST">
                                                    <a class="btn btn-sm btn-primary"
                                                        href="{{ route('superadmin.data-lapangans.show', $dataLapangan->id) }}"><i
                                                            class="las la-eye"></i> {{ __('Show') }}</a>

                                                    <a class="btn btn-sm btn-success"
                                                        href="{{ route('superadmin.data-lapangans.edit', $dataLapangan->id) }}"><i
                                                            class="las la-edit"></i> {{ __('Edit') }}</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="event.preventDefault(); confirm('Are you sure to delete?') ? this.closest('form').submit() : false;"><i
                                                            class="las la-trash"></i> {{ __('Delete') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @include('superadmin.data-lapangan.partials.modal-data-lapangan')
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
