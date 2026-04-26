@extends('layouts.app')
@section('template_title')
    Update App Version
@endsection

@section('content')
<div class="adm-page">
    @include('layouts.messages')

    {{-- ── PAGE HEADER ── --}}
    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Update Versi Aplikasi</h1>
            <p>Edit versi <strong>{{ $appVersion->version }}</strong> (build {{ $appVersion->build_number }})</p>
        </div>
        <a href="{{ route('superadmin.app-versions.index') }}" class="adm-btn-secondary">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            Kembali
        </a>
    </div>

    {{-- ── FORM CARD ── --}}
    <div class="adm-form-section">
        <div class="adm-form-section-header">
            <svg viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
            Edit Informasi Versi
        </div>
        <form method="POST" action="{{ route('superadmin.app-versions.update', $appVersion->hashed_id) }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="adm-form-body">
                <div class="adm-form-grid cols-2" style="gap:14px;">
                    @include('superadmin.app-version.form')
                </div>
            </div>
            <div class="adm-form-actions">
                <button type="submit" class="adm-btn-primary">
                    <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    Simpan Perubahan
                </button>
                <a href="{{ route('superadmin.app-versions.index') }}" class="adm-btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
