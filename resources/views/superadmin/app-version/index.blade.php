@extends('layouts.app')
@section('template_title')
    App Versions
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                @include('layouts.messages')
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <span id="card_title" class="fw-semibold">
                                <i class="las la-mobile-alt me-1"></i> {{ __('App Versions') }}
                            </span>
                            <div>
                                @if ($appVersions->isEmpty())
                                    <a href="{{ route('superadmin.app-versions.create') }}" class="btn btn-primary btn-sm">
                                        <i class="las la-plus"></i> {{ __('Create New') }}
                                    </a>
                                @else
                                    <a href="{{ route('superadmin.app-versions.edit', $appVersions->first()->id) }}"
                                        class="btn btn-success btn-sm">
                                        <i class="las la-edit"></i> {{ __('Update Versi') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        <div class="row g-3">

                            {{-- Kolom kiri: stats + tabel --}}
                            <div class="col-md-9">

                                @unless ($appVersions->isEmpty())
                                    @php $latest = $appVersions->first(); @endphp
                                    <div class="row g-2 mb-3">
                                        <div class="col-4">
                                            <div class="p-3 rounded bg-light">
                                                <div class="text-muted small">Versi Aktif</div>
                                                <div class="fs-5 fw-semibold">{{ $latest->version }}</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-3 rounded bg-light">
                                                <div class="text-muted small">Build Number</div>
                                                <div class="fs-5 fw-semibold">{{ $latest->build_number }}</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-3 rounded bg-light">
                                                <div class="text-muted small">Force Update</div>
                                                <div class="mt-1">
                                                    @if ($latest->force_update)
                                                        <span class="badge bg-danger">Wajib</span>
                                                    @else
                                                        <span class="badge bg-secondary">Opsional</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endunless

                                <div class="table-responsive">
                                    <table class="table table-striped table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Version</th>
                                                <th>Build</th>
                                                <th>Changelog</th>
                                                <th>Force Update</th>
                                                <th>Download URL</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($appVersions as $appVersion)
                                                <tr>
                                                    <td>{{ ++$i }}</td>
                                                    <td>
                                                        <span class="badge bg-primary bg-opacity-10 text-primary">
                                                            v{{ $appVersion->version }}
                                                        </span>
                                                    </td>
                                                    <td class="fw-semibold">{{ $appVersion->build_number }}</td>
                                                    <td class="text-muted small">{{ $appVersion->changelog }}</td>
                                                    <td>
                                                        @if ($appVersion->force_update)
                                                            <span class="badge bg-danger">Wajib</span>
                                                        @else
                                                            <span class="badge bg-secondary">Opsional</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{-- Tombol download dengan tooltip URL lengkap --}}
                                                        <a href="{{ $appVersion->download_url }}" target="_blank"
                                                            class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="{{ $appVersion->download_url }}">
                                                            <i class="las la-download"></i>
                                                            <span>Download APK</span>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-4">
                                                        <div class="text-muted">
                                                            <i class="las la-inbox la-3x mb-2 d-block"></i>
                                                            <p class="mb-0">{{ __('No data available') }}</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                            </div>

                            {{-- Kolom kanan: phone mockup --}}
                            <div class="col-md-3 d-flex flex-column align-items-center justify-content-start gap-3 pt-2">
                                <div
                                    style="width:110px; height:210px; background:#2c2c2a; border-radius:22px; padding:8px 7px; border: 3px solid #444;">
                                    <div
                                        style="background:#e6f1fb; border-radius:14px; height:100%; display:flex; flex-direction:column; overflow:hidden;">
                                        <div
                                            style="width:28px; height:5px; background:#2c2c2a; border-radius:4px; margin: 7px auto;">
                                        </div>
                                        <div
                                            style="padding:8px; flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:6px;">
                                            <div
                                                style="width:30px; height:30px; background:#185FA5; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                    stroke="white" stroke-width="2.5">
                                                    <polyline points="16 3 21 3 21 8" />
                                                    <line x1="4" y1="20" x2="21" y2="3" />
                                                </svg>
                                            </div>
                                            <div
                                                style="background:white; border-radius:7px; padding:7px; width:100%; border: 0.5px solid #B5D4F4;">
                                                <div
                                                    style="font-size:7px; font-weight:600; color:#185FA5; margin-bottom:3px;">
                                                    Update Tersedia</div>
                                                <div style="font-size:6px; color:#888; margin-bottom:5px; line-height:1.4;">
                                                    Ada versi terbaru Kawulo Halal.</div>
                                                <div
                                                    style="background:#185FA5; color:white; font-size:6px; padding:3px 5px; border-radius:3px; text-align:center;">
                                                    Download Sekarang</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @unless ($appVersions->isEmpty())
                                    <div class="w-100 p-2 rounded bg-light text-center">
                                        <div class="text-muted" style="font-size:11px;">Versi terbaru</div>
                                        <div class="fw-semibold" style="font-size:13px;">
                                            {{ $appVersions->first()->version }} (build
                                            {{ $appVersions->first()->build_number }})
                                        </div>
                                    </div>
                                    <div class="w-100 p-2 rounded bg-light text-center">
                                        <div class="text-muted" style="font-size:11px;">Terakhir diupdate</div>
                                        <div class="fw-semibold" style="font-size:13px;">
                                            {{ $appVersions->first()->updated_at->diffForHumans() }}
                                        </div>
                                    </div>
                                @endunless
                            </div>

                        </div>
                    </div>

                    {{-- Pagination di dalam card, sebelum penutup --}}
                    @if ($appVersions->hasPages())
                        <div class="card-footer bg-white border-top">
                            @include('layouts.pagination', ['paginator' => $appVersions])
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            new bootstrap.Tooltip(el)
        })
    </script>
@endsection
