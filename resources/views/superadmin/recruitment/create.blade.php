@extends('layouts.app')
@section('template_title') Tambah Recruitment @endsection
@section('content')
<div class="adm-page">
    @include('layouts.messages')
    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Tambah Recruitment</h1>
            <p>Daftarkan data pelamar baru ke sistem</p>
        </div>
        <a href="{{ route('superadmin.recruitments.index') }}" class="adm-btn-secondary">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg> Kembali
        </a>
    </div>
    <div class="adm-form-section">
        <div class="adm-form-section-header">
            <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            Data Pelamar
        </div>
        <form method="POST" action="{{ route('superadmin.recruitments.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="adm-form-body">
                <div class="adm-form-grid cols-2" style="gap:14px;">
                    @include('superadmin.recruitment.form')
                </div>
            </div>
            <div class="adm-form-actions">
                <button type="submit" class="adm-btn-primary">
                    <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Simpan
                </button>
                <a href="{{ route('superadmin.recruitments.index') }}" class="adm-btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
