@extends('layouts.app')
@section('template_title')
    Detail Transaksi
@endsection

@section('content')
<div class="adm-page">

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Detail Transaksi</h1>
            <p>Informasi lengkap transaksi kas</p>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('superadmin.arus-kas.edit', $cashflow->hashed_id) }}" class="adm-btn-primary">
                <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit
            </a>
            <a href="{{ route('superadmin.arus-kas.index') }}" class="adm-btn-secondary">
                <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                Kembali
            </a>
        </div>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                Informasi Transaksi
            </div>
        </div>
        <div style="padding:0 20px;">
            <div class="adm-info-list">
                <div class="adm-info-row">
                    <span class="adm-info-key">Tipe</span>
                    <span class="adm-info-val">
                        @if ($cashflow->tipe == 'Pemasukan')
                            <span class="adm-badge adm-badge-success"><span class="dot"></span>Pemasukan</span>
                        @elseif($cashflow->tipe == 'Pengeluaran')
                            <span class="adm-badge adm-badge-danger"><span class="dot"></span>Pengeluaran</span>
                        @else
                            <span class="adm-badge adm-badge-pending"><span class="dot"></span>{{ $cashflow->tipe }}</span>
                        @endif
                    </span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Jumlah</span>
                    <span class="adm-info-val adm-mono" style="font-size:16px;">
                        Rp {{ number_format($cashflow->jumlah, 0, ',', '.') }}
                    </span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Tanggal</span>
                    <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">
                        {{ \Carbon\Carbon::parse($cashflow->tanggal)->format('d M Y') }}
                    </span>
                </div>
                <div class="adm-info-row" style="align-items:flex-start;">
                    <span class="adm-info-key">Keterangan</span>
                    <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);text-align:right;max-width:60%;">
                        {!! $cashflow->keterangan ?: '-' !!}
                    </span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Dibuat</span>
                    <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-muted);">
                        {{ $cashflow->created_at->format('d M Y, H:i') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
