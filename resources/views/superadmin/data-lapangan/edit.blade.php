@extends('layouts.app')
@section('template_title') Edit Data Lapangan @endsection
@section('content')
<div class="adm-page">
    @include('layouts.messages')
    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Edit Data Lapangan</h1>
            <p>Update data survei lapangan <strong>{{ $dataLapangan->nama_pu }}</strong></p>
        </div>
        <a href="{{ route('superadmin.data-lapangans.index') }}" class="adm-btn-secondary">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg> Kembali
        </a>
    </div>
    <div class="adm-form-section">
        <div class="adm-form-section-header">
            <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            Edit Data Lapangan
        </div>
        <form method="POST" action="{{ route('superadmin.data-lapangans.update', $dataLapangan->hashed_id) }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="adm-form-body">
                <div class="adm-form-grid cols-2" style="gap:14px;">
                    @include('superadmin.data-lapangan.form')
                </div>
            </div>
            <div class="adm-form-actions">
                <button type="submit" class="adm-btn-primary">
                    <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Simpan Perubahan
                </button>
                <a href="{{ route('superadmin.data-lapangans.index') }}" class="adm-btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

