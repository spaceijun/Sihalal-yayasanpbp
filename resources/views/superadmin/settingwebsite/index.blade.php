@extends('layouts.app')
@section('template_title') Setting Website @endsection
@section('content')
<div class="adm-page">
    @include('layouts.messages')

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Setting Website</h1>
            <p>Konfigurasi tampilan dan identitas website</p>
        </div>
    </div>

    <div class="adm-form-section">
        <div class="adm-form-section-header">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            Pengaturan Umum
        </div>

        <form action="{{ route('superadmin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="adm-form-body">
                <div class="adm-form-grid cols-2" style="gap:14px;">

                    {{-- Judul Website --}}
                    <div class="adm-field" style="grid-column:1/-1;">
                        <label class="adm-label" for="title">Judul Website <span class="req">*</span></label>
                        <input type="text" id="title" name="title"
                            class="adm-input @error('title') is-invalid @enderror"
                            value="{{ old('title', $setting->title) }}"
                            placeholder="SiHalal — Yayasan PBP" required>
                        @error('title') <span class="adm-error-msg">{{ $message }}</span> @enderror
                    </div>

                    {{-- Deskripsi Website --}}
                    <div class="adm-field" style="grid-column:1/-1;">
                        <label class="adm-label" for="description">Deskripsi Website</label>
                        <textarea id="description" name="description" rows="3"
                            class="adm-textarea @error('description') is-invalid @enderror"
                            placeholder="Deskripsi singkat website untuk meta tag...">{{ old('description', $setting->description) }}</textarea>
                        @error('description') <span class="adm-error-msg">{{ $message }}</span> @enderror
                    </div>

                    {{-- Favicon --}}
                    <div class="adm-field">
                        <label class="adm-label" for="favicon">Favicon</label>
                        <input type="file" id="favicon" name="favicon"
                            class="adm-input @error('favicon') is-invalid @enderror"
                            accept="image/*">
                        <span class="adm-hint">Format PNG/ICO. Ukuran ideal 32×32 atau 16×16 px.</span>
                        @error('favicon') <span class="adm-error-msg">{{ $message }}</span> @enderror
                        @if ($setting->favicon)
                            <div style="margin-top:8px;display:flex;align-items:center;gap:8px;">
                                <img src="{{ asset('storage/' . $setting->favicon) }}" alt="Favicon" height="32"
                                    style="border:1px solid var(--adm-border);border-radius:4px;padding:2px;">
                                <span style="font-size:12px;color:var(--adm-text-muted);">Favicon saat ini</span>
                            </div>
                        @endif
                    </div>

                    {{-- Logo --}}
                    <div class="adm-field">
                        <label class="adm-label" for="logo">Logo Website</label>
                        <input type="file" id="logo" name="logo"
                            class="adm-input @error('logo') is-invalid @enderror"
                            accept="image/*">
                        <span class="adm-hint">Format PNG/JPG/GIF. Maks 2 MB.</span>
                        @error('logo') <span class="adm-error-msg">{{ $message }}</span> @enderror
                        @if ($setting->logo)
                            <div style="margin-top:8px;">
                                <img src="{{ asset('storage/' . $setting->logo) }}" alt="Logo" height="60"
                                    style="border:1px solid var(--adm-border);border-radius:6px;padding:4px;background:#f8f9fb;">
                                <div style="font-size:12px;color:var(--adm-text-muted);margin-top:4px;">Logo saat ini</div>
                            </div>
                        @endif
                    </div>

                </div>
            </div>

            <div class="adm-form-actions">
                <button type="submit" class="adm-btn-primary">
                    <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
