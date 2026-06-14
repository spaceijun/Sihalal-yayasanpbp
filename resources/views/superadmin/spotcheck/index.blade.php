@extends('layouts.app')
@section('template_title') Spotcheck @endsection
@section('content')
<div class="adm-page">
    @include('layouts.messages')

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Manajemen Spotcheck</h1>
            <p>Kelola data kunjungan spotcheck pelaku usaha</p>
        </div>
        <a href="{{ route($routePrefix . '.spotchecks.create') }}" class="adm-btn-primary">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Spotcheck
        </a>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                Daftar Spotcheck
                <span class="adm-count-badge">{{ $spotchecks->total() }}</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th style="width:44px">#</th>
                        <th>ID Data Lapangan</th>
                        <th>Nama Spotcheck</th>
                        <th>Tanggal</th>
                        <th>Foto PU</th>
                        <th>Hasil Spotcheck</th>
                        <th class="tc" style="width:110px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($spotchecks as $spotcheck)
                        <tr>
                            <td><span class="adm-rownum">{{ ++$i }}</span></td>
                            <td class="adm-mono" style="font-size:12px;">{{ $spotcheck->data_lapangan_id }}</td>
                            <td style="font-weight:600;font-size:13px;">{{ $spotcheck->nama_spotcheck }}</td>
                            <td class="adm-mono" style="font-size:12.5px;">{{ $spotcheck->tanggal_spotcheck }}</td>
                            <td style="font-size:12px;color:var(--adm-text-muted);max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                {{ $spotcheck->foto_pu ?: 'â€”' }}
                            </td>
                            <td style="font-size:12.5px;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                                title="{{ $spotcheck->hasil_spotcheck }}">
                                {{ $spotcheck->hasil_spotcheck ?: 'â€”' }}
                            </td>
                            <td class="tc">
                                <div class="adm-actions" style="justify-content:center;gap:4px;">
                                    <a class="adm-btn primary icon-only"
                                        href="{{ route($routePrefix . '.spotchecks.show', $spotcheck->hashed_id) }}" title="Lihat">
                                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <a class="adm-btn warning icon-only"
                                        href="{{ route($routePrefix . '.spotchecks.edit', $spotcheck->hashed_id) }}" title="Edit">
                                        <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </a>
                                    <form action="{{ route($routePrefix . '.spotchecks.destroy', $spotcheck->hashed_id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="adm-btn danger icon-only" title="Hapus"
                                            onclick="return confirm('Yakin hapus spotcheck ini?')">
                                            <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @include('superadmin.spotcheck.partials.modal-spotcheck')
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="adm-empty">
                                    <svg viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                    <p>Belum ada data spotcheck.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="adm-card-footer">
            <span class="adm-footer-info">
                Menampilkan {{ $spotchecks->firstItem() ?? 0 }}â€“{{ $spotchecks->lastItem() ?? 0 }}
                dari {{ $spotchecks->total() }} spotcheck
            </span>
            @include('layouts.pagination', ['paginator' => $spotchecks])
        </div>
    </div>
</div>
@endsection

