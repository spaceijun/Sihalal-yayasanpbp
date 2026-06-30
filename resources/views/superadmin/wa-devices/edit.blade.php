@extends('layouts.app')
@section('template_title', $pageTitle)

@section('content')
<div class="adm-page">
    @include('layouts.messages')

    <!-- Header Section -->
    <div class="adm-header">
        <div class="adm-header-left">
            <h1>
                <svg viewBox="0 0 24 24" style="display:inline-block;width:22px;height:22px;stroke:var(--adm-blue);fill:none;stroke-width:2;vertical-align:-4px;margin-right:6px;">
                    <rect x="5" y="2" width="14" height="20" rx="2" ry="2"/>
                    <line x1="12" y1="18" x2="12.01" y2="18"/>
                </svg>
                {{ $pageTitle }}
            </h1>
            @isset($breadcrumbs)
                <nav style="margin-top: 4px; font-size: 13px; color: var(--adm-text-muted);">
                    <ol style="display: flex; align-items: center; gap: 6px; list-style: none; padding: 0; margin: 0;">
                        @foreach ($breadcrumbs as $breadcrumb)
                            @if ($loop->last)
                                <span style="color: var(--adm-text-dark); font-weight: 500;">{{ $breadcrumb['title'] }}</span>
                            @else
                                <a href="{{ $breadcrumb['url'] }}" style="color: var(--adm-text-muted); text-decoration: none;" class="hover:text-dark">{{ $breadcrumb['title'] }}</a>
                                <svg style="width: 12px; height: 12px; stroke: var(--adm-text-faint); fill: none; stroke-width: 2;" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            @endif
                        @endforeach
                    </ol>
                </nav>
            @endisset
        </div>
    </div>

    <!-- Form Section -->
    <div class="adm-form-section">
        <div class="adm-form-section-header">
            <svg viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            Ubah Detail Perangkat
        </div>
        <form method="POST" action="{{ route('superadmin.wa-devices.update', $device->hashed_id) }}">
            @csrf
            @method('PUT')

            <div class="adm-form-body">
                <div class="adm-form-grid cols-1" style="gap: 16px;">
                    <div class="adm-field">
                        <label for="name" class="adm-label">
                            Nama Perangkat <span class="req">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $device->name) }}"
                            class="adm-input @error('name') is-invalid @enderror"
                            placeholder="Contoh: WhatsApp Utama"
                            required>
                        @error('name')
                            <span class="adm-error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="adm-field">
                        <label for="phone" class="adm-label">
                            Nomor WhatsApp
                        </label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $device->phone) }}"
                            class="adm-input @error('phone') is-invalid @enderror"
                            placeholder="Contoh: 6281234567890">
                        @error('phone')
                            <span class="adm-error-msg">{{ $message }}</span>
                        @enderror
                        <span class="adm-hint">Nomor akan terisi otomatis setelah perangkat terhubung.</span>
                    </div>
                </div>
            </div>

            <div class="adm-form-actions">
                <a href="{{ route('superadmin.wa-devices.show', $device->hashed_id) }}" class="adm-btn-secondary">
                    Batal
                </a>
                <button type="submit" class="adm-btn-primary">
                    <svg viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
