@extends('layouts.app')
@section('template_title') Detail Tiket @endsection

@section('content')
<div class="adm-page">
    @include('layouts.messages')

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Detail Tiket</h1>
            <p class="adm-mono" style="font-size:12px;">{{ $ticket->no_ticket }}</p>
        </div>
        <a href="{{ route('koordinator.tiket.index') }}" class="adm-btn-secondary">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg> Kembali
        </a>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                {{ $ticket->subject }}
            </div>
        </div>
        <div style="padding:0 20px;">
            <div class="adm-info-list">
                <div class="adm-info-row">
                    <span class="adm-info-key">Status</span>
                    <span class="adm-info-val">
                        @switch($ticket->status)
                            @case('open')
                                <span class="adm-badge adm-badge-pending">Open</span>
                                @break
                            @case('in_progress')
                                <span class="adm-badge adm-badge-info">In Progress</span>
                                @break
                            @default
                                <span class="adm-badge adm-badge-success">Closed</span>
                        @endswitch
                    </span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Kategori</span>
                    <span class="adm-info-val"><span class="adm-badge adm-badge-indigo">{{ $ticket->kategori }}</span></span>
                </div>
                @if ($ticket->dataLapangan)
                    <div class="adm-info-row">
                        <span class="adm-info-key">Data Lapangan Terkait</span>
                        <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $ticket->dataLapangan->no_registrasi }} — {{ $ticket->dataLapangan->nama_pu }}</span>
                    </div>
                @endif
                @if ($ticket->enumerator)
                    <div class="adm-info-row">
                        <span class="adm-info-key">Enumerator Terkait</span>
                        <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $ticket->enumerator->nama_lengkap }}</span>
                    </div>
                @endif
                <div class="adm-info-row">
                    <span class="adm-info-key">Deskripsi</span>
                    <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);white-space:pre-line;">{{ $ticket->description }}</span>
                </div>
                @if ($ticket->file)
                    <div class="adm-info-row">
                        <span class="adm-info-key">Lampiran</span>
                        <span class="adm-info-val">
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($ticket->file) }}" target="_blank" rel="noopener" class="adm-btn-secondary" style="display:inline-flex;padding:6px 12px;font-size:12px;">
                                Lihat Lampiran
                            </a>
                        </span>
                    </div>
                @endif
                <div class="adm-info-row">
                    <span class="adm-info-key">Dibuat</span>
                    <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $ticket->created_at->format('d M Y, H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
