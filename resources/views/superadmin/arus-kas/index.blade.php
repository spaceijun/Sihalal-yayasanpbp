@extends('layouts.app')
@section('template_title')
    Arus Kas
@endsection

@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        {{-- ── PAGE HEADER ── --}}
        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Arus Kas</h1>
                <p>Kelola data pemasukan, pengeluaran, dan kas</p>
            </div>
            <div style="display:flex;gap:8px;">
                <a href="{{ route($routePrefix . '.cashflow.index') }}" class="adm-btn-secondary">
                    <svg viewBox="0 0 24 24">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
                        <polyline points="17 6 23 6 23 12" />
                    </svg>
                    Laporan
                </a>
                <a href="{{ route($routePrefix . '.arus-kas.create') }}" class="adm-btn-primary">
                    <svg viewBox="0 0 24 24">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Tambah Transaksi
                </a>
            </div>
        </div>

        {{-- ── TABLE CARD ── --}}
        <div class="adm-card">
            <div class="adm-card-header">
                <div class="adm-card-title">
                    <svg viewBox="0 0 24 24">
                        <line x1="12" y1="1" x2="12" y2="23" />
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                    </svg>
                    Daftar Transaksi
                    <span class="adm-count-badge">{{ $cashflows->total() }}</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th style="width:44px">#</th>
                            <th>Tipe</th>
                            <th class="tr">Jumlah</th>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th class="tc" style="width:120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cashflows as $cashflow)
                            <tr>
                                <td><span class="adm-rownum">{{ ++$i }}</span></td>
                                <td>
                                    @if ($cashflow->tipe == 'Pemasukan')
                                        <span class="adm-badge adm-badge-success"><span
                                                class="dot"></span>Pemasukan</span>
                                    @elseif($cashflow->tipe == 'Pengeluaran')
                                        <span class="adm-badge adm-badge-danger"><span
                                                class="dot"></span>Pengeluaran</span>
                                    @elseif($cashflow->tipe == 'Kas')
                                        <span class="adm-badge adm-badge-pending"><span class="dot"></span>Kas</span>
                                    @else
                                        <span class="adm-badge adm-badge-info">{{ $cashflow->tipe }}</span>
                                    @endif
                                </td>
                                <td class="tr adm-mono" style="font-weight:600;color:var(--adm-text-dark);">
                                    Rp {{ number_format($cashflow->jumlah, 0, ',', '.') }}
                                </td>
                                <td style="color:var(--adm-text-muted);font-size:12.5px;">
                                    {{ \Carbon\Carbon::parse($cashflow->tanggal)->format('d M Y') }}
                                </td>
                                <td style="max-width:260px;font-size:12.5px;color:var(--adm-text-muted);">
                                    {!! $cashflow->keterangan !!}
                                </td>
                                <td class="tc">
                                    <div class="adm-actions">
                                        <a class="adm-btn primary icon-only"
                                            href="{{ route($routePrefix . '.arus-kas.edit', $cashflow->hashed_id) }}" title="Edit">
                                            <svg viewBox="0 0 24 24">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route($routePrefix . '.arus-kas.destroy', $cashflow->hashed_id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="adm-btn danger icon-only" title="Hapus"
                                                onclick="return confirm('Yakin hapus transaksi ini?')">
                                                <svg viewBox="0 0 24 24">
                                                    <polyline points="3 6 5 6 21 6" />
                                                    <path d="M19 6l-1 14H6L5 6" />
                                                    <path d="M10 11v6" />
                                                    <path d="M14 11v6" />
                                                    <path d="M9 6V4h6v2" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="adm-empty">
                                        <svg viewBox="0 0 24 24">
                                            <line x1="12" y1="1" x2="12" y2="23" />
                                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                                        </svg>
                                        <p>Belum ada data transaksi.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="adm-card-footer">
                <span class="adm-footer-info">
                    Menampilkan {{ $cashflows->firstItem() ?? 0 }}–{{ $cashflows->lastItem() ?? 0 }}
                    dari {{ $cashflows->total() }} transaksi
                </span>
                @include('layouts.pagination', ['paginator' => $cashflows])
            </div>
        </div>
    </div>
@endsection
