@extends('layouts.app')
@section('template_title') Pengumuman @endsection

@section('content')
<div class="adm-page">
    @include('layouts.messages')

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Pengumuman</h1>
            <p>Informasi &amp; pengumuman terbaru dari Admin Umum</p>
        </div>
    </div>

    @if ($pengumumen->isEmpty())
        <div class="adm-card" style="padding:40px 20px;">
            <div class="adm-empty">
                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                <p>Belum ada pengumuman.</p>
            </div>
        </div>
    @else
        <div class="ko-pengumuman-grid">
            @foreach ($pengumumen as $p)
                <a href="{{ route('koordinator.pengumuman.show', $p->hashed_id) }}" class="ko-pengumuman-card">
                    <div class="ko-pengumuman-thumb">
                        @if ($p->foto)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($p->foto) }}" alt="{{ $p->judul }}">
                        @else
                            <div class="ko-pengumuman-thumb-fallback">
                                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            </div>
                        @endif
                    </div>
                    <div class="ko-pengumuman-body">
                        <span class="adm-mono" style="font-size:11px;font-weight:600;color:var(--adm-blue);">{{ $p->nomor }}</span>
                        <h3>{{ $p->judul }}</h3>
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:10px;">
                            @if ($p->jenis === 'OSS')
                                <span class="adm-badge adm-badge-oss">OSS</span>
                            @elseif ($p->jenis === 'SIHALAL')
                                <span class="adm-badge adm-badge-sihalal">SIHALAL</span>
                            @else
                                <span class="adm-badge adm-badge-info">{{ $p->jenis }}</span>
                            @endif
                            <span style="font-size:11px;color:var(--adm-text-muted);">{{ $p->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div style="margin-top:20px;">
            {{ $pengumumen->links() }}
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .ko-pengumuman-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; }
    .ko-pengumuman-card { background: #fff; border: 1px solid var(--adm-border); border-radius: var(--adm-radius); overflow: hidden; text-decoration: none; color: inherit; display: block; transition: box-shadow .18s, transform .18s; }
    .ko-pengumuman-card:hover { box-shadow: var(--adm-shadow-md, 0 6px 20px rgba(0,0,0,.08)); transform: translateY(-2px); color: inherit; }
    .ko-pengumuman-thumb { width: 100%; height: 150px; background: var(--adm-bg-light); overflow: hidden; }
    .ko-pengumuman-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .ko-pengumuman-thumb-fallback { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; }
    .ko-pengumuman-thumb-fallback svg { width: 36px; height: 36px; stroke: var(--adm-text-faint); fill: none; stroke-width: 1.5; }
    .ko-pengumuman-body { padding: 14px 16px; }
    .ko-pengumuman-body h3 { font-size: 14px; font-weight: 700; color: var(--adm-text-dark); margin: 6px 0 0; line-height: 1.4; }
</style>
@endpush
