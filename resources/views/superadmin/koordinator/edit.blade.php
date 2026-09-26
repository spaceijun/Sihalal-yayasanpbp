@extends('layouts.app')
@section('template_title') Edit Koordinator @endsection
@section('content')
<div class="adm-page">
    @include('layouts.messages')
    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Edit Koordinator</h1>
            <p>Update informasi <strong>{{ $koordinator->nama_lengkap }}</strong></p>
        </div>
        <a href="{{ route($routePrefix . '.koordinators.index') }}" class="adm-btn-secondary">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg> Kembali
        </a>
    </div>

    <form id="formKoordinator" method="POST" action="{{ route($routePrefix . '.koordinators.update', $koordinator->hashed_id) }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        @include('superadmin.koordinator.form')
        <div class="adm-form-actions">
            <button type="submit" class="adm-btn-primary" id="btnSimpanKoordinator">
                <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Simpan Perubahan
            </button>
            <a href="{{ route($routePrefix . '.koordinators.index') }}" class="adm-btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('formKoordinator').addEventListener('submit', function () {
        const btn = document.getElementById('btnSimpanKoordinator');
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...`;
    });
</script>
@endpush
