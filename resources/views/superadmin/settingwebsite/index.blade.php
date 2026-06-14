@extends('layouts.app')
@section('title', 'Pengaturan')@section('template_title')
Setting Website
@endsection

@section('content')
<div class="adm-page container-fluid">

    {{-- ── PAGE HEADER ────────────────────────────── --}}
    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Pengaturan</h1>
            <p>Kelola konfigurasi website dan environment aplikasi</p>
        </div>
    </div>

    {{-- ── FLASH ALERTS ─────────────────────────── --}}
    @if (session('success'))
        <div class="adm-alert adm-alert-success" role="alert">
            <svg viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="adm-alert adm-alert-danger" role="alert">
            <svg viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- ── MAIN CARD ───────────────────────────────── --}}
    <div class="adm-card">

        {{-- Tab Header --}}
        <div class="adm-card-header" style="border-bottom:none; padding-bottom:0;">
            <div class="adm-tabs" id="settingTabs" role="tablist">
                <button class="adm-tab-btn active" role="tab" data-tab="website" aria-selected="true">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M2 12h20M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20" />
                    </svg>
                    Website
                </button>
                <button class="adm-tab-btn" role="tab" data-tab="env" aria-selected="false">
                    <svg viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Environment
                    <span class="adm-tab-pill">.env</span>
                </button>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- TAB: WEBSITE                               --}}
        {{-- ========================================== --}}
        <div id="tab-website" class="adm-tab-pane active">
            <form action="{{ route($routePrefix . '.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div style="padding: 20px 20px 0;">

                    {{-- Informasi Website --}}
                    <div class="adm-form-section">
                        <div class="adm-form-section-header">
                            <svg viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Informasi Website
                        </div>
                        <div class="adm-form-body">
                            <div class="adm-form-grid cols-1">
                                <div class="adm-field">
                                    <label class="adm-label" for="title">Nama Website <span
                                            class="req">*</span></label>
                                    <input type="text" id="title" name="title"
                                        class="adm-input @error('title') is-invalid @enderror"
                                        value="{{ old('title', $setting->title) }}" placeholder="Contoh: Kawulo Halal">
                                    @error('title')
                                        <span class="adm-error-msg">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label" for="description">Deskripsi Website</label>
                                    <textarea id="description" name="description" rows="3"
                                        class="adm-textarea @error('description') is-invalid @enderror"
                                        placeholder="Deskripsi singkat tentang website Anda">{{ old('description', $setting->description) }}</textarea>
                                    @error('description')
                                        <span class="adm-error-msg">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Media --}}
                    <div class="adm-form-section">
                        <div class="adm-form-section-header">
                            <svg viewBox="0 0 24 24">
                                <rect x="3" y="3" width="18" height="18" rx="2" />
                                <circle cx="8.5" cy="8.5" r="1.5" />
                                <path d="M21 15l-5-5L5 21" />
                            </svg>
                            Media
                        </div>
                        <div class="adm-form-body">
                            <div class="adm-form-grid">

                                {{-- Favicon --}}
                                <div class="adm-field">
                                    <label class="adm-label">Favicon</label>
                                    <div class="adm-upload-zone" onclick="document.getElementById('favicon').click()">
                                        <img src="{{ $setting->favicon ? Storage::url($setting->favicon) : '' }}"
                                            id="favicon-preview"
                                            class="adm-upload-img {{ $setting->favicon ? 'visible' : '' }}"
                                            alt="Favicon Preview">
                                        <div class="adm-upload-icon {{ $setting->favicon ? 'hidden' : '' }}"
                                            id="favicon-icon">
                                            <svg viewBox="0 0 24 24">
                                                <rect x="3" y="3" width="18" height="18" rx="2" />
                                                <circle cx="8.5" cy="8.5" r="1.5" />
                                                <path d="M21 15l-5-5L5 21" />
                                            </svg>
                                        </div>
                                        <span class="adm-upload-label">Klik untuk upload favicon</span>
                                        <span class="adm-upload-sub">ICO, PNG, JPG, GIF · Maks. 2MB</span>
                                        <input type="file" id="favicon" name="favicon" style="display:none;"
                                            accept=".ico,.png,.jpg,.jpeg,.gif"
                                            onchange="previewImg(this,'favicon-preview','favicon-icon')">
                                    </div>
                                    @error('favicon')
                                        <span class="adm-error-msg">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Logo --}}
                                <div class="adm-field">
                                    <label class="adm-label">Logo</label>
                                    <div class="adm-upload-zone" onclick="document.getElementById('logo').click()">
                                        <img src="{{ $setting->logo ? Storage::url($setting->logo) : '' }}"
                                            id="logo-preview"
                                            class="adm-upload-img {{ $setting->logo ? 'visible' : '' }}"
                                            style="width:auto;max-width:120px;height:56px;" alt="Logo Preview">
                                        <div class="adm-upload-icon {{ $setting->logo ? 'hidden' : '' }}"
                                            id="logo-icon">
                                            <svg viewBox="0 0 24 24">
                                                <rect x="3" y="3" width="18" height="18" rx="2" />
                                                <circle cx="8.5" cy="8.5" r="1.5" />
                                                <path d="M21 15l-5-5L5 21" />
                                            </svg>
                                        </div>
                                        <span class="adm-upload-label">Klik untuk upload logo</span>
                                        <span class="adm-upload-sub">PNG, JPG, GIF · Maks. 2MB</span>
                                        <input type="file" id="logo" name="logo" style="display:none;"
                                            accept=".png,.jpg,.jpeg,.gif"
                                            onchange="previewImg(this,'logo-preview','logo-icon')">
                                    </div>
                                    @error('logo')
                                        <span class="adm-error-msg">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <div class="adm-form-actions">
                    <button type="submit" class="adm-btn-primary">
                        <svg viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Simpan Pengaturan
                    </button>
                    <a href="{{ route($routePrefix . '.settings.index') }}" class="adm-btn-secondary">Batal</a>
                </div>

            </form>
        </div>{{-- /tab-website --}}


        {{-- ========================================== --}}
        {{-- TAB: ENVIRONMENT                           --}}
        {{-- ========================================== --}}
        <div id="tab-env" class="adm-tab-pane">

            <div style="padding: 20px 20px 0;">
                <div class="adm-alert adm-alert-warning">
                    <svg viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                    </svg>
                    <span>
                        <strong>Hati-hati!</strong> Perubahan pada <code>.env</code> langsung mempengaruhi aplikasi.
                        Key bertanda <span class="env-protected-badge"
                            style="display:inline-flex;height:20px;font-size:10px;">
                            <svg viewBox="0 0 24 24">
                                <rect x="3" y="11" width="18" height="11" rx="2" />
                                <path d="M7 11V7a5 5 0 0110 0v4" />
                            </svg>
                            Protected
                        </span> tidak dapat diubah demi keamanan session.
                    </span>
                </div>
            </div>

            <form action="{{ route($routePrefix . '.settings.env.update') }}" method="POST" id="envForm">
                @csrf
                @method('PUT')

                {{-- Filter Bar --}}
                <div class="adm-filter-bar">
                    <div class="adm-filter-group" style="flex:1;">
                        <span class="adm-filter-label">Cari Key</span>
                        <div class="adm-search-shell" style="width:100%;">
                            <svg class="adm-search-icon" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8" />
                                <path d="M21 21l-4.35-4.35" />
                            </svg>
                            <input type="text" id="envSearch" class="adm-search-input"
                                style="width:100%;max-width:340px;" placeholder="Contoh: DB_HOST, APP_ENV, MAIL_...">
                        </div>
                    </div>
                </div>

                {{-- Env Rows --}}
                <div style="padding: 0 20px 20px;" id="envContainer">

                    @php
                        $keyColors = [
                            'APP' => 'c-blue',
                            'SANCTUM' => 'c-blue',
                            'VITE' => 'c-blue',
                            'DB' => 'c-red',
                            'REDIS' => 'c-red',
                            'MAIL' => 'c-cyan',
                            'CACHE' => 'c-green',
                            'SESSION' => 'c-amber',
                            'QUEUE' => 'c-gray',
                            'BROADCAST' => 'c-gray',
                            'FILESYSTEM' => 'c-gray',
                            'LOG' => 'c-gray',
                            'MEMCACHED' => 'c-gray',
                            'PHP' => 'c-indigo',
                            'BCRYPT' => 'c-indigo',
                            'MAINTENANCE' => 'c-amber',
                            'AWS' => 'c-amber',
                            'KAWULOHALAL' => 'c-green',
                            'SID' => 'c-cyan',
                            'FIREBASE' => 'c-amber',
                        ];

                        $readonlyKeys = [
                            'APP_KEY',
                            'SESSION_DRIVER',
                            'SESSION_DOMAIN',
                            'DB_CONNECTION',
                            'DB_HOST',
                            'DB_PORT',
                            'DB_DATABASE',
                            'DB_USERNAME',
                            'DB_PASSWORD',
                        ];

                        $sensitiveKeys = [
                            'MAIL_PASSWORD',
                            'REDIS_PASSWORD',
                            'AWS_SECRET_ACCESS_KEY',
                            'KAWULOHALAL_API_KEY',
                            'SID_AUTH_KEY',
                        ];

                        $booleanKeys = ['APP_DEBUG', 'SESSION_ENCRYPT', 'AWS_USE_PATH_STYLE_ENDPOINT'];

                        $enumKeys = [
                            'APP_ENV' => ['production', 'local', 'testing', 'staging'],
                            'LOG_CHANNEL' => ['stack', 'single', 'daily', 'slack', 'syslog', 'errorlog', 'null'],
                            'LOG_LEVEL' => [
                                'debug',
                                'info',
                                'notice',
                                'warning',
                                'error',
                                'critical',
                                'alert',
                                'emergency',
                            ],
                            'QUEUE_CONNECTION' => ['sync', 'database', 'redis', 'beanstalkd', 'sqs', 'null'],
                            'CACHE_STORE' => ['file', 'database', 'redis', 'memcached', 'array', 'null'],
                            'FILESYSTEM_DISK' => ['local', 'public', 's3'],
                            'BROADCAST_CONNECTION' => ['log', 'pusher', 'ably', 'redis', 'null'],
                            'MAIL_MAILER' => ['log', 'smtp', 'mailgun', 'ses', 'postmark', 'sendmail', 'null'],
                            'REDIS_CLIENT' => ['phpredis', 'predis'],
                        ];

                        $getColor = function ($key) use ($keyColors) {
                            return $keyColors[explode('_', $key)[0]] ?? 'c-gray';
                        };
                    @endphp

                    @foreach ($envContent as $item)

                        @if ($item['type'] === 'comment')
                            @if (trim($item['raw']) !== '')
                                <div class="env-section-divider env-row-item" data-key="">
                                    <span
                                        class="env-section-divider-label">{{ ltrim(trim($item['raw']), '#') }}</span>
                                    <div class="env-section-divider-line"></div>
                                </div>
                            @endif
                        @else
                            @php
                                $key = $item['key'];
                                $value = trim($item['value'], '"\'');
                                $color = $getColor($key);
                                $isReadonly = in_array($key, $readonlyKeys);
                                $isSensitive = in_array($key, $sensitiveKeys);
                                $isBool = in_array($key, $booleanKeys);
                                $enumOpts = $enumKeys[$key] ?? null;
                            @endphp

                            <div class="env-row env-row-item" data-key="{{ strtolower($key) }}">

                                {{-- Key badge --}}
                                <div class="env-key-cell">
                                    <span class="env-key-badge {{ $color }}">{{ $key }}</span>
                                </div>

                                {{-- Value input --}}
                                <div>
                                    @if ($isReadonly)
                                        {{-- READONLY: key kritis yang dilindungi --}}
                                        <div class="env-pw-wrap">
                                            <input type="text" value="{{ $value }}" class="env-input"
                                                readonly title="Key ini dilindungi dan tidak dapat diubah via UI">
                                            <span class="env-protected-badge">
                                                <svg viewBox="0 0 24 24">
                                                    <rect x="3" y="11" width="18" height="11"
                                                        rx="2" />
                                                    <path d="M7 11V7a5 5 0 0110 0v4" />
                                                </svg>
                                                Protected
                                            </span>
                                        </div>
                                    @elseif($isBool)
                                        <select name="env[{{ $key }}]" class="env-select">
                                            <option value="true" @selected(strtolower($value) === 'true')>true</option>
                                            <option value="false" @selected(strtolower($value) === 'false')>false</option>
                                        </select>
                                    @elseif($enumOpts)
                                        <select name="env[{{ $key }}]" class="env-select">
                                            @foreach ($enumOpts as $opt)
                                                <option value="{{ $opt }}" @selected($value === $opt)>
                                                    {{ $opt }}</option>
                                            @endforeach
                                        </select>
                                    @elseif($isSensitive)
                                        <div class="env-pw-wrap">
                                            <input type="password" name="env[{{ $key }}]"
                                                value="{{ $value }}" class="env-input"
                                                id="env_{{ $key }}" autocomplete="off">
                                            <button type="button" class="env-pw-toggle"
                                                data-target="env_{{ $key }}" title="Tampilkan/sembunyikan">
                                                <svg viewBox="0 0 24 24" class="icon-eye">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                    <circle cx="12" cy="12" r="3" />
                                                </svg>
                                                <svg viewBox="0 0 24 24" class="icon-eye-off" style="display:none;">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24M1 1l22 22" />
                                                </svg>
                                            </button>
                                        </div>
                                    @else
                                        <input type="text" name="env[{{ $key }}]"
                                            value="{{ $value }}" class="env-input" autocomplete="off">
                                    @endif
                                </div>

                            </div>
                        @endif

                    @endforeach

                    <div class="env-no-result" id="envNoResult">
                        <svg viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <path d="M21 21l-4.35-4.35" />
                        </svg>
                        <p>Tidak ada key yang cocok dengan pencarian Anda</p>
                    </div>

                </div>{{-- /envContainer --}}

                {{-- Form Actions --}}
                <div class="adm-form-actions">
                    <button type="submit" class="adm-btn-primary adm-btn-success" onclick="return confirmSaveEnv()">
                        <svg viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Simpan Konfigurasi .env
                    </button>
                    <span style="font-size:12px; color:var(--adm-text-faint);">
                        Perubahan langsung berlaku setelah disimpan
                    </span>
                </div>

            </form>
        </div>{{-- /tab-env --}}

    </div>{{-- /adm-card --}}
</div>{{-- /adm-page --}}

<style>
    .adm-tabs {
        display: flex;
        gap: 2px;
        border-bottom: 2px solid var(--adm-border-mid);
        margin-bottom: 0;
    }

    .adm-tab-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        height: 40px;
        padding: 0 18px;
        background: transparent;
        border: none;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 13px;
        font-weight: 600;
        color: var(--adm-text-muted);
        cursor: pointer;
        transition: color .15s, border-color .15s;
    }

    .adm-tab-btn svg {
        width: 15px;
        height: 15px;
        stroke: currentColor;
        fill: none;
        stroke-width: 2;
    }

    .adm-tab-btn:hover {
        color: var(--adm-text-dark);
    }

    .adm-tab-btn.active {
        color: var(--adm-blue);
        border-bottom-color: var(--adm-blue);
    }

    .adm-tab-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 2px 7px;
        background: var(--adm-amber-lt);
        color: var(--adm-amber);
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .04em;
    }

    .adm-tab-pane {
        display: none;
    }

    .adm-tab-pane.active {
        display: block;
    }

    /* ── UPLOAD ZONE ─────────────────────────────── */
    .adm-upload-zone {
        border: 2px dashed var(--adm-border-mid);
        border-radius: var(--adm-radius-sm);
        background: var(--adm-bg-input);
        padding: 18px 14px;
        text-align: center;
        cursor: pointer;
        transition: border-color .18s, background .18s;
    }

    .adm-upload-zone:hover {
        border-color: var(--adm-blue);
        background: var(--adm-blue-lt);
    }

    .adm-upload-img {
        width: 56px;
        height: 56px;
        object-fit: contain;
        border-radius: 8px;
        margin-bottom: 8px;
        display: none;
    }

    .adm-upload-img.visible {
        display: block;
        margin: 0 auto 8px;
    }

    .adm-upload-icon {
        margin-bottom: 8px;
        display: flex;
        justify-content: center;
    }

    .adm-upload-icon svg {
        width: 28px;
        height: 28px;
        stroke: var(--adm-text-faint);
        fill: none;
        stroke-width: 1.5;
    }

    .adm-upload-icon.hidden {
        display: none;
    }

    .adm-upload-label {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--adm-text-mid);
        display: block;
        margin-bottom: 2px;
    }

    .adm-upload-sub {
        font-size: 11px;
        color: var(--adm-text-faint);
    }

    /* ── ENV ROWS ────────────────────────────────── */
    .env-section-divider {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 18px 0 6px;
    }

    .env-section-divider-label {
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: var(--adm-text-muted);
        white-space: nowrap;
    }

    .env-section-divider-line {
        flex: 1;
        height: 1px;
        background: var(--adm-border);
    }

    .env-row {
        display: grid;
        grid-template-columns: 290px 1fr;
        align-items: center;
        gap: 12px;
        padding: 7px 0;
        border-bottom: 1px solid var(--adm-border);
    }

    .env-row:last-of-type {
        border-bottom: none;
    }

    .env-key-cell {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .env-key-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 6px;
        font-family: 'DM Mono', 'Courier New', monospace;
        font-size: 11.5px;
        font-weight: 500;
        white-space: nowrap;
    }

    .env-key-badge.c-blue {
        background: var(--adm-blue-lt);
        color: var(--adm-blue);
    }

    .env-key-badge.c-red {
        background: var(--adm-red-lt);
        color: var(--adm-red);
    }

    .env-key-badge.c-green {
        background: var(--adm-green-lt);
        color: var(--adm-green);
    }

    .env-key-badge.c-amber {
        background: var(--adm-amber-lt);
        color: var(--adm-amber);
    }

    .env-key-badge.c-cyan {
        background: var(--adm-cyan-lt);
        color: var(--adm-cyan);
    }

    .env-key-badge.c-indigo {
        background: var(--adm-indigo-lt);
        color: var(--adm-indigo);
    }

    .env-key-badge.c-gray {
        background: var(--adm-bg-light);
        color: var(--adm-text-muted);
        border: 1px solid var(--adm-border-mid);
    }

    .env-input {
        width: 100%;
        background: var(--adm-bg-input);
        border: 1px solid var(--adm-border-mid);
        border-radius: var(--adm-radius-sm);
        padding: 0 10px;
        height: 32px;
        font-size: 12.5px;
        font-family: 'DM Mono', 'Courier New', monospace;
        color: var(--adm-text-dark);
        outline: none;
        transition: border-color .18s, box-shadow .18s, background .18s;
    }

    .env-input:focus {
        border-color: var(--adm-blue);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(26, 95, 200, .08);
    }

    .env-input[readonly] {
        background: var(--adm-bg-light);
        color: var(--adm-text-faint);
        cursor: not-allowed;
        border-style: dashed;
    }

    .env-select {
        width: 100%;
        background: var(--adm-bg-input);
        border: 1px solid var(--adm-border-mid);
        border-radius: var(--adm-radius-sm);
        padding: 0 10px;
        height: 32px;
        font-size: 12.5px;
        font-family: 'DM Mono', 'Courier New', monospace;
        color: var(--adm-text-dark);
        outline: none;
        cursor: pointer;
        transition: border-color .18s;
    }

    .env-select:focus {
        border-color: var(--adm-blue);
        box-shadow: 0 0 0 3px rgba(26, 95, 200, .08);
    }

    .env-pw-wrap {
        display: flex;
        gap: 4px;
    }

    .env-pw-wrap .env-input {
        flex: 1;
    }

    .env-pw-toggle {
        width: 32px;
        height: 32px;
        border: 1px solid var(--adm-border-mid);
        border-radius: var(--adm-radius-sm);
        background: var(--adm-bg-input);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: var(--adm-text-muted);
        transition: background .13s, color .13s;
    }

    .env-pw-toggle:hover {
        background: var(--adm-bg-light);
        color: var(--adm-text-dark);
    }

    .env-pw-toggle svg {
        width: 14px;
        height: 14px;
        stroke: currentColor;
        fill: none;
        stroke-width: 2;
    }

    /* Protected badge */
    .env-protected-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 0 10px;
        height: 32px;
        font-size: 11px;
        font-weight: 600;
        color: var(--adm-text-faint);
        white-space: nowrap;
        border: 1px dashed var(--adm-border-mid);
        border-radius: var(--adm-radius-sm);
        background: var(--adm-bg-light);
        flex-shrink: 0;
    }

    .env-protected-badge svg {
        width: 11px;
        height: 11px;
        stroke: currentColor;
        fill: none;
        stroke-width: 2;
    }

    .env-no-result {
        padding: 48px 20px;
        text-align: center;
        color: var(--adm-text-faint);
        display: none;
    }

    .env-no-result svg {
        width: 40px;
        height: 40px;
        stroke: var(--adm-border-mid);
        fill: none;
        stroke-width: 1.5;
        display: block;
        margin: 0 auto 10px;
    }

    .env-no-result p {
        font-size: 13px;
        margin: 0;
    }

    @media (max-width:640px) {
        .env-row {
            grid-template-columns: 1fr;
            gap: 4px;
        }
    }
</style>


<script>
    /* ── Preview gambar upload ── */
    function previewImg(input, previewId, iconId) {
        if (!input.files || !input.files[0]) return;
        const r = new FileReader();
        r.onload = e => {
            const img = document.getElementById(previewId);
            const icon = document.getElementById(iconId);
            img.src = e.target.result;
            img.classList.add('visible');
            if (icon) icon.classList.add('hidden');
        };
        r.readAsDataURL(input.files[0]);
    }

    /* ── Tab switching ── */
    document.querySelectorAll('.adm-tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tab = this.dataset.tab;
            document.querySelectorAll('.adm-tab-btn').forEach(b => {
                b.classList.toggle('active', b === this);
                b.setAttribute('aria-selected', b === this ? 'true' : 'false');
            });
            document.querySelectorAll('.adm-tab-pane').forEach(p => {
                p.classList.toggle('active', p.id === 'tab-' + tab);
            });
            sessionStorage.setItem('activeSettingTab', tab);
        });
    });

    /* ── Pertahankan tab aktif setelah redirect ── */
    document.addEventListener('DOMContentLoaded', function() {
        const saved = sessionStorage.getItem('activeSettingTab');
        if (saved) {
            const btn = document.querySelector(`.adm-tab-btn[data-tab="${saved}"]`);
            if (btn) btn.click();
        }
    });

    /* ── Toggle password visibility ── */
    document.querySelectorAll('.env-pw-toggle').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = document.getElementById(this.dataset.target);
            const iconEye = this.querySelector('.icon-eye');
            const iconOff = this.querySelector('.icon-eye-off');
            if (input.type === 'password') {
                input.type = 'text';
                iconEye.style.display = 'none';
                iconOff.style.display = '';
            } else {
                input.type = 'password';
                iconEye.style.display = '';
                iconOff.style.display = 'none';
            }
        });
    });

    /* ── Search env keys ── */
    document.getElementById('envSearch').addEventListener('input', function() {
        const q = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('.env-row-item[data-key]');
        let visible = 0;

        rows.forEach(row => {
            const isSection = row.classList.contains('env-section-divider');
            if (isSection) {
                row.style.display = q ? 'none' : '';
                return;
            }
            const match = !q || (row.dataset.key && row.dataset.key.includes(q));
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        if (q) {
            document.querySelectorAll('.env-section-divider').forEach(div => {
                let next = div.nextElementSibling;
                let has = false;
                while (next && !next.classList.contains('env-section-divider')) {
                    if (next.style.display !== 'none' && next.dataset.key) has = true;
                    next = next.nextElementSibling;
                }
                div.style.display = has ? '' : 'none';
            });
        }

        document.getElementById('envNoResult').style.display =
            (!q || visible > 0) ? 'none' : 'block';
    });

    /* ── Konfirmasi simpan .env ── */
    function confirmSaveEnv() {
        return confirm(
            'Simpan perubahan pada file .env?\n\n' +
            'Pastikan semua nilai sudah benar.\n' +
            'Perubahan langsung berlaku pada aplikasi.'
        );
    }
</script>
@endsection
