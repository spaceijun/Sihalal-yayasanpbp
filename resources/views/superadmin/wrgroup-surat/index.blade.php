@extends('layouts.app')
@section('template_title')
    Surat WRGROUP
@endsection

@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Surat WRGROUP</h1>
                <p>Arsip surat yang diajukan Kawulo Halal ke WRGROUP — status disinkronkan otomatis</p>
            </div>
            <a href="{{ route('superadmin.wrgroup-surat.create') }}" class="adm-btn-primary">
                <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Buat Surat
            </a>
        </div>

        <div class="adm-card">
            <div class="adm-card-header">
                <div class="adm-card-title">
                    <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Riwayat Surat
                </div>
            </div>
            <div class="table-responsive">
                <table class="adm-table w-100">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Perihal</th>
                            <th>Nomor</th>
                            <th class="tc">Status</th>
                            <th class="tc" style="width:90px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftar as $s)
                            <tr>
                                <td>{{ $s->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $s->jenis_nama }}</td>
                                <td>{{ $s->perihal }}</td>
                                <td>{{ $s->nomor ?: '—' }}</td>
                                <td class="tc"><span class="adm-badge {{ $s->status_badge_class }}">{{ $s->status_label }}</span></td>
                                <td class="tc">
                                    <a href="{{ route('superadmin.wrgroup-surat.show', $s->hashed_id) }}" class="adm-btn primary icon-only" title="Detail">
                                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" style="text-align:center;color:var(--adm-text-faint);">Belum ada surat yang diajukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
