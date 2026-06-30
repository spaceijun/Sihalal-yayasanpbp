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
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Perangkat WhatsApp Baru
        </div>
        <form method="POST" action="{{ route('superadmin.wa-devices.store') }}">
            @csrf

            <div class="adm-form-body">
                <div class="adm-form-grid cols-1">
                    <div class="adm-field">
                        <label for="name" class="adm-label">
                            Nama Perangkat <span class="req">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                            class="adm-input @error('name') is-invalid @enderror"
                            placeholder="Contoh: WhatsApp Utama"
                            required>
                        @error('name')
                            <span class="adm-error-msg">{{ $message }}</span>
                        @enderror
                        <span class="adm-hint">Berikan nama yang mudah diingat untuk perangkat ini.</span>
                    </div>
                </div>
            </div>

            <div class="adm-form-actions">
                <a href="{{ route('superadmin.wa-devices.index') }}" class="adm-btn-secondary">
                    <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                    Kembali
                </a>
                <button type="submit" class="adm-btn-primary">
                    <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Tambah Perangkat
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
