@extends('layouts.app')
@section('template_title') Tambah Fee Enumerator @endsection
@section('content')
<div class="adm-page">
    @include('layouts.messages')
    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Tambah Fee Enumerator</h1>
            <p>Konfigurasi besaran fee baru untuk enumerator</p>
        </div>
        <a href="{{ route($routePrefix . '.fee-enumerator.index') }}" class="adm-btn-secondary">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg> Kembali
        </a>
    </div>

    <form id="formFeeEnumerator" method="POST" action="{{ route($routePrefix . '.fee-enumerator.store') }}">
        @csrf
        @include('superadmin.fee-enumerator.form')
        <div class="adm-form-actions">
            <button type="submit" class="adm-btn-primary" id="btnSimpanFee">
                <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Simpan Fee
            </button>
            <a href="{{ route($routePrefix . '.fee-enumerator.index') }}" class="adm-btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('formFeeEnumerator').addEventListener('submit', function () {
        const btn = document.getElementById('btnSimpanFee');
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...`;
    });
</script>
@endpush
