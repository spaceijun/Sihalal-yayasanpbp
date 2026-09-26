@extends('layouts.app')
@section('template_title') Tambah Verifikator Payment @endsection
@section('content')
<div class="adm-page">
    @include('layouts.messages')
    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Tambah Verifikator Payment</h1>
            <p>Catat data pembayaran verifikator baru</p>
        </div>
        <a href="{{ route('superadmin.verifikator-payments.index') }}" class="adm-btn-secondary">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg> Kembali
        </a>
    </div>
    <div class="adm-form-section">
        <div class="adm-form-section-header">
            <svg viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
            Informasi Payment
        </div>
        <form id="formVerifikatorPayment" method="POST" action="{{ route('superadmin.verifikator-payments.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="adm-form-body">
                <div class="adm-form-grid cols-2" style="gap:14px;">
                    @include('superadmin.verifikator-payment.form')
                </div>
            </div>
            <div class="adm-form-actions">
                <button type="submit" class="adm-btn-primary" id="btnSimpanVerifikatorPayment">
                    <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Simpan
                </button>
                <a href="{{ route('superadmin.verifikator-payments.index') }}" class="adm-btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('formVerifikatorPayment').addEventListener('submit', function () {
        const btn = document.getElementById('btnSimpanVerifikatorPayment');
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...`;
    });
</script>
@endpush
