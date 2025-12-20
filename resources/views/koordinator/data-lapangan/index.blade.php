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
                                {{-- <a href="{{ route('superadmin.data-lapangans.create') }}"
                                    class="btn btn-primary btn-sm float-right" data-placement="left">
                                    {{ __('Create New') }}
                                </a> --}}
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
                                        <option value="Progress OSS"
                                            {{ request('status') == 'Progress OSS' ? 'selected' : '' }}>Progress OSS
                                        </option>
                                        <option value="Progress SIHALAL"
                                            {{ request('status') == 'Progress SIHALAL' ? 'selected' : '' }}>Progress SIHALAL
                                        </option>
                                        <option value="Terbit SH" {{ request('status') == 'Terbit SH' ? 'selected' : '' }}>
                                            Terbit SH</option>
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
                                        <th>Status</th>
                                        <th>Status Pembayaran</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($dataLapangans as $dataLapangan)
                                        <tr>
                                            <td>{{ $dataLapangans->firstItem() + $loop->index }}</td>
                                            </td>
                                            <td>{{ $dataLapangan->enumerator->nama_lengkap }}</td>
                                            <td>{{ $dataLapangan->nama_pu }}</td>
                                            <td>{{ $dataLapangan->nik }}</td>
                                            <td>
                                                @if ($dataLapangan->status == 'PENDING')
                                                    <span
                                                        class="badge bg-warning text-dark">{{ $dataLapangan->status }}</span>
                                                @elseif($dataLapangan->status == 'PROGRESS OSS')
                                                    <span class="badge bg-info">{{ $dataLapangan->status }}</span>
                                                @elseif($dataLapangan->status == 'PROGRESS SIHALAL')
                                                    <span class="badge bg-primary">{{ $dataLapangan->status }}</span>
                                                @elseif($dataLapangan->status == 'TERBIT SH')
                                                    <span class="badge bg-success">{{ $dataLapangan->status }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($dataLapangan->status_pembayaran == 'PENDING')
                                                    <span
                                                        class="badge bg-warning text-dark">{{ $dataLapangan->status_pembayaran }}</span>
                                                @elseif($dataLapangan->status_pembayaran == 'PENGAJUAN')
                                                    <span
                                                        class="badge bg-info">{{ $dataLapangan->status_pembayaran }}</span>
                                                @elseif($dataLapangan->status_pembayaran == 'DIBAYAR')
                                                    <span
                                                        class="badge bg-success">{{ $dataLapangan->status_pembayaran }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a class="btn btn-sm btn-primary"
                                                    href="{{ route('koordinator.data-lapangan.show', $dataLapangan->id) }}"><i
                                                        class="las la-eye"></i> {{ __('Show') }}</a>
                                            </td>
                                        </tr>
                                        {{-- @include('superadmin.data-lapangan.partials.modal-data-lapangan') --}}
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
