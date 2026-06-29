@extends('layouts.app')

@section('template_title')
    Galeri Foto — {{ $enumerator->nama_lengkap }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col">
                <div class="card card-animate">

                    {{-- Header --}}
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="card-title fw-bold">
                            Galeri Foto Enumerator — {{ $enumerator->nama_lengkap }}
                            <small class="text-muted">(No. Reg: {{ $enumerator->no_registrasi }})</small>
                        </span>
                        <a class="btn btn-primary btn-sm" href="{{ route($routePrefix . '.enumerators.show', $enumerator->hashed_id) }}">
                            ← Back
                        </a>
                    </div>

                    <div class="card-body bg-white">

                        {{-- Info Enumerator --}}
                        <div class="row mb-4">
                            <div class="col-md-2 text-center">
                                @if ($enumerator->foto_diri)
                                    <img src="{{ asset('storage/' . $enumerator->foto_diri) }}"
                                        class="rounded-circle img-thumbnail"
                                        style="width:90px; height:90px; object-fit:cover;">
                                @else
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white mx-auto"
                                        style="width:90px; height:90px; font-size:2rem;">
                                        {{ strtoupper(substr($enumerator->nama_lengkap, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-10">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td width="150"><strong>Nama Lengkap</strong></td>
                                        <td>: {{ $enumerator->nama_lengkap }}</td>
                                        <td width="150"><strong>Koordinator</strong></td>
                                        <td>: {{ $enumerator->koordinator->nama_lengkap ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>No. Registrasi</strong></td>
                                        <td>: {{ $enumerator->no_registrasi }}</td>
                                        <td><strong>Status</strong></td>
                                        <td>:
                                            <span
                                                class="badge {{ $enumerator->status === 'Aktif' ? 'bg-success' : 'bg-danger' }}">
                                                {{ $enumerator->status }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Data Lapangan</strong></td>
                                        <td colspan="3">: {{ $enumerator->dataLapangans->count() }} entri</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <hr>

                        {{-- TAB + tombol Download ZIP --}}
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-0">
                            <ul class="nav nav-tabs mb-0 flex-grow-1" id="galleryTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="pendamping-tab" data-bs-toggle="tab"
                                        data-bs-target="#pendamping" type="button" role="tab">
                                        <i class="las la-user me-1"></i>
                                        Foto Pendamping
                                        <span class="badge bg-primary ms-1">
                                            {{ $enumerator->dataLapangans->whereNotNull('foto_pendamping')->count() }}
                                        </span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="produk-tab" data-bs-toggle="tab" data-bs-target="#produk"
                                        type="button" role="tab">
                                        <i class="las la-box me-1"></i>
                                        Foto Produk
                                        <span class="badge bg-success ms-1">
                                            {{ $enumerator->dataLapangans->whereNotNull('foto_produk')->count() }}
                                        </span>
                                    </button>
                                </li>
                            </ul>

                            {{-- Tombol Download ZIP --}}
                            <div class="d-flex gap-2 mb-1 flex-wrap">
                                <a id="btn-zip-all"
                                    href="{{ route($routePrefix . '.enumerators.download-zip', $enumerator->hashed_id) }}?type=all"
                                    class="btn btn-dark btn-sm zip-btn"
                                    title="Download semua foto (ZIP)">
                                    <i class="las la-file-archive me-1"></i>
                                    <span class="btn-label">Semua Foto</span>
                                    <span class="spinner-border spinner-border-sm d-none ms-1" role="status"></span>
                                </a>
                                <a id="btn-zip-pendamping"
                                    href="{{ route($routePrefix . '.enumerators.download-zip', $enumerator->hashed_id) }}?type=foto_pendamping"
                                    class="btn btn-outline-primary btn-sm zip-btn"
                                    title="Download foto pendamping (ZIP)">
                                    <i class="las la-file-archive me-1"></i>
                                    <span class="btn-label">ZIP Pendamping</span>
                                    <span class="spinner-border spinner-border-sm d-none ms-1" role="status"></span>
                                </a>
                                <a id="btn-zip-produk"
                                    href="{{ route($routePrefix . '.enumerators.download-zip', $enumerator->hashed_id) }}?type=foto_produk"
                                    class="btn btn-outline-success btn-sm zip-btn"
                                    title="Download foto produk (ZIP)">
                                    <i class="las la-file-archive me-1"></i>
                                    <span class="btn-label">ZIP Produk</span>
                                    <span class="spinner-border spinner-border-sm d-none ms-1" role="status"></span>
                                </a>
                            </div>
                        </div>
                        <div class="mb-3"></div>

                        <div class="tab-content" id="galleryTabContent">

                            {{-- TAB: FOTO PENDAMPING --}}
                            <div class="tab-pane fade show active" id="pendamping" role="tabpanel">
                                @php
                                    $pendampingData = $enumerator->dataLapangans->whereNotNull('foto_pendamping');
                                @endphp

                                @if ($pendampingData->isEmpty())
                                    <div class="text-center py-5 text-muted">
                                        <i class="las la-image" style="font-size:3rem;"></i>
                                        <p class="mt-2">Belum ada foto pendamping.</p>
                                    </div>
                                @else
                                    <div class="row g-3">
                                        @foreach ($pendampingData as $data)
                                            <div class="col-6 col-md-3 col-lg-2">
                                                <div class="card h-100 border shadow-sm">
                                                    {{-- Thumbnail klik → modal --}}
                                                    <div class="position-relative" style="cursor:pointer;"
                                                        onclick="openModal(
                                                        '{{ asset('storage/' . $data->foto_pendamping) }}',
                                                        '{{ $data->nama_pu ?? '-' }}',
                                                        '{{ $data->nama_produk ?? '-' }}',
                                                        '{{ $data->status }}',
                                                        '{{ route($routePrefix . '.enumerators.download-foto-entry', [$enumerator->hashed_id, $data->id, 'foto_pendamping']) }}')">
                                                        <img src="{{ asset('storage/' . $data->foto_pendamping) }}"
                                                            class="card-img-top" style="height:160px; object-fit:cover;"
                                                            alt="Foto Pendamping">
                                                        <div class="position-absolute top-0 end-0 m-1">
                                                            <span class="badge bg-dark bg-opacity-75">
                                                                <i class="las la-search-plus"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-2">
                                                        <p class="mb-0 small fw-semibold text-truncate"
                                                            title="{{ $data->nama_pu }}">
                                                            {{ $data->nama_pu ?? '-' }}
                                                        </p>
                                                        <p class="mb-0 small text-muted text-truncate"
                                                            title="{{ $data->nama_produk }}">
                                                            {{ $data->nama_produk ?? '-' }}
                                                        </p>
                                                        <div class="d-flex justify-content-between align-items-center mt-1">
                                                            <span
                                                                class="badge
                                                        {{ $data->status === 'Terverifikasi'
                                                            ? 'bg-success'
                                                            : ($data->status === 'Ditolak'
                                                                ? 'bg-danger'
                                                                : 'bg-warning text-dark') }}">
                                                                {{ $data->status }}
                                                            </span>
                                                            {{-- Tombol Download --}}
                                                            <a href="{{ route($routePrefix . '.enumerators.download-foto-entry', [$enumerator->hashed_id, $data->id, 'foto_pendamping']) }}"
                                                                class="btn btn-outline-secondary btn-sm py-0 px-1"
                                                                title="Download foto">
                                                                <i class="las la-download"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- TAB: FOTO PRODUK --}}
                            <div class="tab-pane fade" id="produk" role="tabpanel">
                                @php
                                    $produkData = $enumerator->dataLapangans->whereNotNull('foto_produk');
                                @endphp

                                @if ($produkData->isEmpty())
                                    <div class="text-center py-5 text-muted">
                                        <i class="las la-image" style="font-size:3rem;"></i>
                                        <p class="mt-2">Belum ada foto produk.</p>
                                    </div>
                                @else
                                    <div class="row g-3">
                                        @foreach ($produkData as $data)
                                            <div class="col-6 col-md-3 col-lg-2">
                                                <div class="card h-100 border shadow-sm">
                                                    {{-- Thumbnail klik → modal --}}
                                                    <div class="position-relative" style="cursor:pointer;"
                                                        onclick="openModal(
                                                    '{{ asset('storage/' . $data->foto_produk) }}',
                                                    '{{ $data->nama_produk ?? '-' }}',
                                                    '{{ $data->nama_pu ?? '-' }}',
                                                    '{{ $data->status }}',
                                                    '{{ asset('storage/' . $data->foto_produk) }}'
                                                 )">
                                                        <img src="{{ asset('storage/' . $data->foto_produk) }}"
                                                            class="card-img-top" style="height:160px; object-fit:cover;"
                                                            alt="Foto Produk">
                                                        <div class="position-absolute top-0 end-0 m-1">
                                                            <span class="badge bg-dark bg-opacity-75">
                                                                <i class="las la-search-plus"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-2">
                                                        <p class="mb-0 small fw-semibold text-truncate"
                                                            title="{{ $data->nama_produk }}">
                                                            {{ $data->nama_produk ?? '-' }}
                                                        </p>
                                                        <p class="mb-0 small text-muted text-truncate"
                                                            title="{{ $data->nama_pu }}">
                                                            {{ $data->nama_pu ?? '-' }}
                                                        </p>
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mt-1">
                                                            <span
                                                                class="badge
                                                        {{ $data->status === 'Terverifikasi'
                                                            ? 'bg-success'
                                                            : ($data->status === 'Ditolak'
                                                                ? 'bg-danger'
                                                                : 'bg-warning text-dark') }}">
                                                                {{ $data->status }}
                                                            </span>
                                                            {{-- Tombol Download --}}
                                                            <a href="{{ route($routePrefix . '.enumerators.download-foto-entry', [$enumerator->hashed_id, $data->id, 'foto_produk']) }}"
                                                                class="btn btn-outline-secondary btn-sm py-0 px-1"
                                                                title="Download foto">
                                                                <i class="las la-download"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                        </div>{{-- end tab-content --}}
                    </div>{{-- end card-body --}}
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== MODAL ZOOM ===================== --}}
    <div class="modal fade" id="fotoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <div>
                        <h6 class="modal-title mb-0 fw-bold" id="modalTitle">-</h6>
                        <small class="text-muted" id="modalSubtitle">-</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0 text-center bg-dark">
                    <img id="modalImage" src="" alt="Foto"
                        style="max-height:70vh; max-width:100%; object-fit:contain;">
                </div>
                <div class="modal-footer py-2 d-flex justify-content-between align-items-center">
                    <span id="modalBadge" class="badge">-</span>
                    <a id="modalDownload" href="#" download class="btn btn-success btn-sm">
                        <i class="las la-download me-1"></i> Download
                    </a>
                </div>
            </div>
        </div>
    </div>


    <script>
        function openModal(imgUrl, title, subtitle, status, downloadUrl) {
            document.getElementById('modalImage').src = imgUrl;
            document.getElementById('modalTitle').innerText = title;
            document.getElementById('modalSubtitle').innerText = subtitle;
            document.getElementById('modalDownload').href = downloadUrl;

            const badge = document.getElementById('modalBadge');
            badge.innerText = status;
            badge.className = 'badge';
            if (status === 'Terverifikasi') {
                badge.classList.add('bg-success');
            } else if (status === 'Ditolak') {
                badge.classList.add('bg-danger');
            } else {
                badge.classList.add('bg-warning', 'text-dark');
            }

            const modal = new bootstrap.Modal(document.getElementById('fotoModal'));
            modal.show();
        }

        // Loading spinner untuk tombol ZIP
        document.querySelectorAll('.zip-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const label   = btn.querySelector('.btn-label');
                const spinner = btn.querySelector('.spinner-border');

                // Tampilkan spinner, ubah teks
                label.textContent = 'Menyiapkan...';
                spinner.classList.remove('d-none');
                btn.classList.add('disabled');

                // Kembalikan tombol setelah 8 detik
                // (browser sudah mulai unduh sebelum ini)
                setTimeout(function () {
                    const origLabels = {
                        'btn-zip-all'        : 'Semua Foto',
                        'btn-zip-pendamping' : 'ZIP Pendamping',
                        'btn-zip-produk'     : 'ZIP Produk',
                    };
                    label.textContent = origLabels[btn.id] || 'Download ZIP';
                    spinner.classList.add('d-none');
                    btn.classList.remove('disabled');
                }, 8000);
            });
        });
    </script>
@endsection
