@extends('layouts.app')
@section('template_title')
    Detail Verifikator Payment
@endsection

@section('content')
<div class="adm-page">
    @include('layouts.messages')

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Detail Verifikator Payment</h1>
            <p>Informasi lengkap pembayaran verifikator #{{ $verifikatorPayment->id }}</p>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('superadmin.verifikator-payments.edit', $verifikatorPayment->id) }}" class="adm-btn-primary">
                <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit
            </a>
            <a href="{{ route('superadmin.verifikator-payments.index') }}" class="adm-btn-secondary">
                <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                Kembali
            </a>
        </div>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                Informasi Payment
            </div>
        </div>
        <div style="padding:0 20px;">
            <div class="adm-info-list">
                <div class="adm-info-row">
                    <span class="adm-info-key">Verifikator ID</span>
                    <span class="adm-info-val">{{ $verifikatorPayment->verifikator_id }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Jumlah Data</span>
                    <span class="adm-info-val">{{ $verifikatorPayment->jumlah_data }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Total Nominal</span>
                    <span class="adm-info-val">{{ $verifikatorPayment->total_nominal }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Periode Dari</span>
                    <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $verifikatorPayment->periode_dari }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Periode Sampai</span>
                    <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $verifikatorPayment->periode_sampai }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Paid At</span>
                    <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $verifikatorPayment->paid_at }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
