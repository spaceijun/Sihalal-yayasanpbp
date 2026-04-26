@extends('layouts.app')
@section('template_title') Edit Verifikator @endsection
@section('content')
<div class="adm-page">
    @include('layouts.messages')
    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Edit Verifikator</h1>
            <p>Update informasi <strong>{{ $verifikator->nama_lengkap }}</strong></p>
        </div>
        <a href="{{ route('superadmin.verifikators.index') }}" class="adm-btn-secondary">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg> Kembali
        </a>
    </div>
    <div class="adm-form-section">
        <div class="adm-form-section-header">
            <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            Edit Informasi Verifikator
        </div>
        <form method="POST" action="{{ route('superadmin.verifikators.update', $verifikator->hashed_id) }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="adm-form-body">
                <div class="adm-form-grid cols-2" style="gap:14px;">
                    @include('superadmin.verifikator.form')
                </div>
            </div>
            <div class="adm-form-actions">
                <button type="submit" class="adm-btn-primary">
                    <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Simpan Perubahan
                </button>
                <a href="{{ route('superadmin.verifikators.index') }}" class="adm-btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

