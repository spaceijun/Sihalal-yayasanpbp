@extends('layouts.app')
@section('template_title') Edit Pengumuman @endsection
@section('content')
<div class="adm-page">
    @include('layouts.messages')

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Edit Pengumuman</h1>
            <p>Update isi pengumuman <strong>{{ $pengumuman->nomor }}</strong></p>
        </div>
        <a href="{{ route($routePrefix . '.pengumumen.index') }}" class="adm-btn-secondary">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            Kembali
        </a>
    </div>

    <div class="adm-form-section">
        <div class="adm-form-section-header">
            <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Edit Pengumuman
        </div>
        <form method="POST" action="{{ route($routePrefix . '.pengumumen.update', $pengumuman->hashed_id) }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="adm-form-body">
                <div class="adm-form-grid cols-2" style="gap:14px;">
                    @include('superadmin.pengumuman.form')
                </div>
            </div>
            <div class="adm-form-actions">
                <button type="submit" class="adm-btn-primary">
                    <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    Simpan Perubahan
                </button>
                <a href="{{ route($routePrefix . '.pengumumen.index') }}" class="adm-btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
