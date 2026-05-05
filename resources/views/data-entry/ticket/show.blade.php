@extends('layouts.app')
@section('template_title')
    {{ __('Detail Ticket') }} — {{ $ticket->no_ticket }}
@endsection
@section('content')
    <section class="content container-fluid">
        <div class="row justify-content-center">
            <div class="card card-animate shadow-sm">

                {{-- HEADER --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fa fa-ticket-alt me-2"></i> Detail Ticket
                    </h5>
                    <a href="{{ route('data-entry.tickets.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left me-1"></i> {{ __('Back') }}
                    </a>
                </div>

                {{-- BODY --}}
                <div class="card-body bg-white p-4">

                    {{-- BADGE STATUS + NO TICKET --}}
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <span class="text-muted small">No. Ticket</span>
                            <h5 class="fw-bold mb-0">{{ $ticket->no_ticket }}</h5>
                        </div>
                        <span
                            class="badge rounded-pill fs-6
                            @if ($ticket->status === 'open') bg-success
                            @elseif($ticket->status === 'in_progress') bg-warning text-dark
                            @else bg-secondary @endif">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                    </div>

                    <hr class="mb-4">

                    {{-- INFO GRID --}}
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="p-3 rounded bg-light h-100">
                                <span class="text-muted small d-block mb-1">
                                    <i class="fa fa-user me-1"></i> Dibuat Oleh
                                </span>
                                <span class="fw-semibold">{{ $ticket->user->name ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded bg-light h-100">
                                <span class="text-muted small d-block mb-1">
                                    <i class="fa fa-calendar me-1"></i> Tanggal Dibuat
                                </span>
                                <span class="fw-semibold">{{ $ticket->created_at->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 rounded bg-light">
                                <span class="text-muted small d-block mb-1">
                                    <i class="fa fa-tag me-1"></i> Subject
                                </span>
                                <span class="fw-semibold">{{ $ticket->subject }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- DESCRIPTION --}}
                    <div class="mb-4">
                        <span class="text-muted small d-block mb-2">
                            <i class="fa fa-align-left me-1"></i> Description
                        </span>
                        <div class="p-3 rounded border bg-white">
                            {!! $ticket->description !!}
                        </div>
                    </div>

                    {{-- FILE --}}
                    @if ($ticket->file)
                        <div class="mb-2">
                            <span class="text-muted small d-block mb-2">
                                <i class="fa fa-paperclip me-1"></i> Lampiran
                            </span>
                            @php
                                $ext = strtolower(pathinfo($ticket->file, PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
                            @endphp
                            @if ($isImage)
                                <img src="{{ Storage::url($ticket->file) }}" alt="Lampiran" class="img-fluid rounded border"
                                    style="max-height: 300px;">
                            @else
                                <a href="{{ Storage::url($ticket->file) }}" target="_blank"
                                    class="btn btn-outline-primary btn-sm">
                                    <i class="fa fa-download me-1"></i> Download File
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="text-muted small">
                            <i class="fa fa-paperclip me-1"></i> Tidak ada lampiran.
                        </div>
                    @endif

                </div>

                {{-- FOOTER --}}
                <div class="card-footer bg-white d-flex justify-content-end gap-2">
                    <a href="{{ route('data-entry.tickets.edit', $ticket->hashed_id) }}" class="btn btn-warning btn-sm">
                        <i class="fa fa-edit me-1"></i> Edit
                    </a>
                    <form action="{{ route('data-entry.tickets.destroy', $ticket->hashed_id) }}" method="POST"
                        onsubmit="return confirm('Yakin ingin menghapus tiket ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fa fa-trash me-1"></i> Hapus
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </section>
@endsection
