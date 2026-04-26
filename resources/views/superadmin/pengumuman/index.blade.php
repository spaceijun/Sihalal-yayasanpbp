@extends('layouts.app')
@section('template_title') Pengumuman Data Entry @endsection
@section('content')
<div class="adm-page">
    @include('layouts.messages')

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Pengumuman Data Entry</h1>
            <p>Kelola pengumuman untuk petugas data entry OSS dan SIHALAL</p>
        </div>
        <a href="{{ route('superadmin.pengumumen.create') }}" class="adm-btn-primary">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Pengumuman
        </a>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Daftar Pengumuman
                <span class="adm-count-badge">{{ $pengumumen->total() }}</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th style="width:44px">#</th>
                        <th>No. Pengumuman</th>
                        <th>Judul</th>
                        <th class="tc">Jenis</th>
                        <th class="tc" style="width:130px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengumumen as $pengumuman)
                        <tr>
                            <td><span class="adm-rownum">{{ ++$i }}</span></td>
                            <td>
                                <span class="adm-mono" style="font-size:11.5px;font-weight:600;color:var(--adm-blue);background:var(--adm-blue-lt);padding:3px 8px;border-radius:6px;">
                                    {{ $pengumuman->nomor }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('superadmin.pengumumen.show', $pengumuman->hashed_id) }}"
                                    style="font-weight:600;font-size:13px;color:var(--adm-text-dark);text-decoration:none;">
                                    {{ $pengumuman->judul }}
                                </a>
                            </td>
                            <td class="tc">
                                @if ($pengumuman->jenis === 'OSS')
                                    <span class="adm-badge adm-badge-oss">OSS</span>
                                @elseif ($pengumuman->jenis === 'SIHALAL')
                                    <span class="adm-badge adm-badge-sihalal">SIHALAL</span>
                                @else
                                    <span class="adm-badge adm-badge-info">{{ $pengumuman->jenis }}</span>
                                @endif
                            </td>
                            <td class="tc">
                                <div class="adm-actions">
                                    <a class="adm-btn primary icon-only"
                                        href="{{ route('superadmin.pengumumen.show', $pengumuman->hashed_id) }}"
                                        title="Detail">
                                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <a class="adm-btn success icon-only"
                                        href="{{ route('superadmin.pengumumen.edit', $pengumuman->hashed_id) }}"
                                        title="Edit">
                                        <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </a>
                                    <form action="{{ route('superadmin.pengumumen.destroy', $pengumuman->hashed_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="adm-btn danger icon-only" title="Hapus"
                                            onclick="return confirm('Yakin hapus pengumuman ini?')">
                                            <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="adm-empty">
                                    <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    <p>Belum ada pengumuman yang dibuat.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="adm-card-footer">
            <span class="adm-footer-info">
                Menampilkan {{ $pengumumen->firstItem() ?? 0 }}–{{ $pengumumen->lastItem() ?? 0 }}
                dari {{ $pengumumen->total() }} pengumuman
            </span>
            @include('layouts.pagination', ['paginator' => $pengumumen])
        </div>
    </div>
</div>
@endsection
