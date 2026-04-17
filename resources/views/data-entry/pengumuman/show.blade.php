@extends('layouts.app')

@section('template_title')
    {{ $pengumuman->judul ?? __('Show') . ' Pengumuman' }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col">

                {{-- Topbar --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0" style="font-size:13px;">
                            <li class="breadcrumb-item">
                                <a href="{{ route('data-entry.pengumumen.index') }}">Pengumuman</a>
                            </li>
                            <li class="breadcrumb-item active">Detail</li>
                        </ol>
                    </nav>
                    <a href="{{ route('data-entry.pengumumen.index') }}"
                        class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                        <i class="la la-arrow-left"></i> Kembali
                    </a>
                </div>

                {{-- Card utama --}}
                <div class="card border" style="border-radius:12px; overflow:hidden;">

                    {{-- Header --}}
                    <div class="card-header bg-white d-flex align-items-center gap-3 py-3 px-4"
                        style="border-bottom: 0.5px solid rgba(0,0,0,0.08);">
                        <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-2"
                            style="width:36px; height:36px; flex-shrink:0;">
                            <i class="la la-file-alt text-primary" style="font-size:18px;"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-medium">Detail Pengumuman</h6>
                            <small class="text-muted">Informasi lengkap pengumuman</small>
                        </div>
                        <span class="badge ms-auto"
                            style="background:#E6F1FB; color:#185FA5; font-size:11px; font-weight:500; border-radius:999px; padding:4px 12px;">
                            Aktif
                        </span>
                    </div>

                    {{-- Body --}}
                    <div class="card-body bg-white p-4">

                        {{-- Row: Nomor & Jenis --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="p-3 rounded-2" style="background:#F8F8F7;">
                                    <p class="text-muted mb-1"
                                        style="font-size:11px; font-weight:500; letter-spacing:.05em; text-transform:uppercase;">
                                        <i class="la la-hashtag me-1"></i>Nomor Pengumuman
                                    </p>
                                    <p class="mb-0 fw-medium" style="font-size:14px;">{{ $pengumuman->nomor }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 rounded-2" style="background:#F8F8F7;">
                                    <p class="text-muted mb-1"
                                        style="font-size:11px; font-weight:500; letter-spacing:.05em; text-transform:uppercase;">
                                        <i class="la la-tag me-1"></i>Jenis Pengumuman
                                    </p>
                                    <p class="mb-0 fw-medium" style="font-size:14px;">{{ $pengumuman->jenis }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Judul --}}
                        <div class="p-3 rounded-2 mb-3" style="background:#F8F8F7;">
                            <p class="text-muted mb-1"
                                style="font-size:11px; font-weight:500; letter-spacing:.05em; text-transform:uppercase;">
                                <i class="la la-heading me-1"></i>Judul Pengumuman
                            </p>
                            <p class="mb-0 fw-medium" style="font-size:15px;">{{ $pengumuman->judul }}</p>
                        </div>

                        {{-- Lampiran PDF --}}
                        <div class="p-3 rounded-2 mb-3" style="background:#F8F8F7;">
                            <p class="text-muted mb-2"
                                style="font-size:11px; font-weight:500; letter-spacing:.05em; text-transform:uppercase;">
                                <i class="la la-paperclip me-1"></i>Lampiran / Foto
                            </p>
                            <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-2 border"
                                style="border-color: rgba(0,0,0,0.08) !important;">
                                <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0"
                                    style="width:36px; height:40px; background:#FCEBEB;">
                                    <i class="la la-file-pdf" style="font-size:20px; color:#A32D2D;"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="mb-0 fw-medium text-truncate" style="font-size:13px;">Dokumen PDF
                                    </p>
                                    <p class="mb-0 text-muted" style="font-size:11px;">{{ $pengumuman->judul }}</p>
                                </div>
                                @if ($pengumuman->foto)
                                    <a href="{{ asset('storage/' . $pengumuman->foto) }}" target="_blank"
                                        class="btn btn-sm ms-auto flex-shrink-0 d-flex align-items-center gap-1"
                                        style="background:#E6F1FB; color:#185FA5; border:none; font-size:12px; font-weight:500;">
                                        <i class="la la-external-link-alt"></i> Lihat PDF
                                    </a>
                                @else
                                    <span class="badge ms-auto flex-shrink-0"
                                        style="background:#F8F8F7; color:#0581ed; font-size:11px; font-weight:500; border-radius:999px; padding:4px 12px;">
                                        Tidak ada lampiran
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="p-3 rounded-2" style="background:#F8F8F7;">
                            <p class="text-muted mb-2"
                                style="font-size:11px; font-weight:500; letter-spacing:.05em; text-transform:uppercase;">
                                <i class="la la-align-left me-1"></i>Deskripsi
                            </p>
                            <p class="mb-0" style="font-size:14px; line-height:1.7;">{!! $pengumuman->deskripsi !!}</p>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
