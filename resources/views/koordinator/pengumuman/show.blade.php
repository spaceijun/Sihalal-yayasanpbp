@extends('layouts.app')
@section('template_title') {{ $pengumuman->judul }} @endsection

@section('content')
<div class="adm-page">
    @include('layouts.messages')

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>{{ $pengumuman->judul }}</h1>
            <p class="adm-mono" style="font-size:12px;">{{ $pengumuman->nomor }}</p>
        </div>
        <a href="{{ route('koordinator.pengumuman.index') }}" class="adm-btn-secondary">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg> Kembali
        </a>
    </div>

    <div class="adm-card">
        @if ($pengumuman->foto)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($pengumuman->foto) }}" alt="{{ $pengumuman->judul }}"
                style="width:100%;max-height:320px;object-fit:cover;">
        @endif
        <div style="padding:20px 24px;">
            <div style="display:flex;gap:10px;align-items:center;margin-bottom:16px;">
                @if ($pengumuman->jenis === 'OSS')
                    <span class="adm-badge adm-badge-oss">OSS</span>
                @elseif ($pengumuman->jenis === 'SIHALAL')
                    <span class="adm-badge adm-badge-sihalal">SIHALAL</span>
                @else
                    <span class="adm-badge adm-badge-info">{{ $pengumuman->jenis }}</span>
                @endif
                <span style="font-size:12px;color:var(--adm-text-muted);">{{ $pengumuman->created_at->isoFormat('dddd, D MMMM YYYY') }}</span>
            </div>
            <div class="ko-pengumuman-content">
                {!! $pengumuman->deskripsi !!}
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .ko-pengumuman-content { font-size: 14px; line-height: 1.7; color: var(--adm-text-dark); }
    .ko-pengumuman-content img { max-width: 100%; height: auto; border-radius: var(--adm-radius-sm); }
</style>
@endpush
