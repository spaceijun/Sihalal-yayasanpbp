@extends('layouts.app')
@section('template_title')
    Data Entries
@endsection

@section('content')
<div class="adm-page">
    @include('layouts.messages')

    {{-- ── PAGE HEADER ── --}}
    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Data Entry</h1>
            <p>Kelola akun petugas data entry beserta koordinator dan rekening</p>
        </div>
        <a href="{{ route('superadmin.data-entries.create') }}" class="adm-btn-primary">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Data Entry
        </a>
    </div>

    {{-- ── TABLE CARD ── --}}
    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Daftar Data Entry
                <span class="adm-count-badge">{{ $dataEntries->total() }}</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th style="width:44px">#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telephone</th>
                        <th class="tc">Status</th>
                        <th class="tc">Entry Type</th>
                        <th>Rekening</th>
                        <th class="tc" style="width:100px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataEntries as $dataEntry)
                        <tr>
                            <td><span class="adm-rownum">{{ ++$i }}</span></td>
                            <td>
                                <div class="adm-name-cell">
                                    <div class="adm-avatar" style="background:var(--adm-blue-lt);color:var(--adm-blue);">
                                        {{ strtoupper(substr($dataEntry->nama_lengkap, 0, 2)) }}
                                    </div>
                                    <div>
                                        <strong>{{ $dataEntry->nama_lengkap }}</strong>
                                    </div>
                                </div>
                            </td>
                            <td style="font-size:12.5px;color:var(--adm-text-muted);">{{ $dataEntry->email }}</td>
                            <td class="adm-mono" style="font-size:12.5px;">{{ $dataEntry->telephone }}</td>
                            <td class="tc">
                                @if ($dataEntry->status === 'Aktif')
                                    <span class="adm-badge adm-badge-success">Aktif</span>
                                @else
                                    <span class="adm-badge adm-badge-nonaktif">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="tc">
                                @if ($dataEntry->entry_type === 'OSS')
                                    <span class="adm-badge adm-badge-oss">OSS</span>
                                @elseif ($dataEntry->entry_type === 'SIHALAL')
                                    <span class="adm-badge adm-badge-sihalal">SIHALAL</span>
                                @else
                                    <span class="adm-badge adm-badge-info">{{ $dataEntry->entry_type }}</span>
                                @endif
                            </td>
                            <td style="font-size:12px;color:var(--adm-text-muted);">
                                @if ($dataEntry->bank && $dataEntry->no_rekening && $dataEntry->nama_rekening)
                                    {{ $dataEntry->bank->name }}, {{ $dataEntry->no_rekening }} an. {{ $dataEntry->nama_rekening }}
                                @else
                                    <span style="color:var(--adm-text-faint);">—</span>
                                @endif
                            </td>
                            <td class="tc">
                                <div class="adm-actions">
                                    <a class="adm-btn primary icon-only"
                                        href="{{ route('superadmin.data-entries.edit', $dataEntry->hashed_id) }}"
                                        title="Edit">
                                        <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </a>
                                    <form action="{{ route('superadmin.data-entries.destroy', $dataEntry->hashed_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="adm-btn danger icon-only" title="Hapus"
                                            onclick="return confirm('Yakin hapus data entry ini?')">
                                            <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="adm-empty">
                                    <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                    <p>Belum ada data entry terdaftar.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="adm-card-footer">
            <span class="adm-footer-info">
                Menampilkan {{ $dataEntries->firstItem() ?? 0 }}–{{ $dataEntries->lastItem() ?? 0 }}
                dari {{ $dataEntries->total() }} data entry
            </span>
            @include('layouts.pagination', ['paginator' => $dataEntries])
        </div>
    </div>
</div>
@endsection
