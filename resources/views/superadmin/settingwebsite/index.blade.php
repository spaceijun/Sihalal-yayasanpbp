@extends('layouts.app')
@section('template_title')
    Setting Website
@section('content')
    <div class="row">
        <div class="col">
            <div class="card">
                @include('layouts.messages')
                <div class="card-header">
                    <h4 class="card-title">Setting Website</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('superadmin.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div>
                            <label for="title" class="form-label">Judul Website</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                name="title" value="{{ old('title', $setting->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="description" class="form-label">Deskripsi Website</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="3">{{ old('description', $setting->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="favicon" class="form-label">Favicon</label>
                            <input class="form-control @error('favicon') is-invalid @enderror" type="file" id="favicon"
                                name="favicon">
                            @error('favicon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            @if ($setting->favicon)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $setting->favicon) }}" alt="Favicon" height="32">
                                    <p class="text-muted">Favicon saat ini</p>
                                </div>
                            @endif
                            <small class="form-text text-muted">Disarankan untuk menggunakan gambar dengan ukuran
                                32x32 atau 16x16 pixel.</small>
                        </div>
                        <div>
                            <label for="logo" class="form-label">Logo Website</label>
                            <input class="form-control @error('logo') is-invalid @enderror" type="file" id="logo"
                                name="logo">
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            @if ($setting->logo)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $setting->logo) }}" alt="Logo" height="100">
                                    <p class="text-muted">Logo saat ini</p>
                                </div>
                            @endif
                            <small class="form-text text-muted">Format yang didukung: PNG, JPG, JPEG, GIF. Ukuran
                                maksimal: 2MB.</small>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
