@extends('layouts.app')
@section('template_title', $pageTitle)

@push('styles')
    <style>
        /* Pulse animation for status dot */
        @keyframes pulse {
            0% {
                transform: scale(0.95);
                opacity: 0.5;
            }

            50% {
                transform: scale(1.2);
                opacity: 1;
            }

            100% {
                transform: scale(0.95);
                opacity: 0.5;
            }
        }

        /* Spinner animation */
        .spin {
            animation: _spin 1s linear infinite;
            display: inline-block;
        }

        @keyframes _spin {
            to {
                transform: rotate(360deg);
            }
        }

        .adm-quick-card {
            display: flex;
            align-items: center;
            gap: 16px;
            background: #fff;
            border: 1px solid var(--adm-border);
            border-radius: var(--adm-radius);
            padding: 16px 20px;
            text-decoration: none;
            color: inherit;
            box-shadow: var(--adm-shadow-sm);
            transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
        }

        .adm-quick-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--adm-shadow-md);
            border-color: var(--adm-border-mid);
            color: inherit;
        }

        .adm-quick-card .icon-wrap {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .adm-quick-card .icon-wrap svg {
            width: 20px;
            height: 20px;
            fill: none;
            stroke-width: 2;
        }

        .adm-quick-card .icon-wrap.bg-blue {
            background: var(--adm-blue-lt);
        }

        .adm-quick-card .icon-wrap.bg-blue svg {
            stroke: var(--adm-blue);
        }

        .adm-quick-card .icon-wrap.bg-green {
            background: var(--adm-green-lt);
        }

        .adm-quick-card .icon-wrap.bg-green svg {
            stroke: var(--adm-green);
        }

        .adm-quick-card .icon-wrap.bg-indigo {
            background: var(--adm-indigo-lt);
        }

        .adm-quick-card .icon-wrap.bg-indigo svg {
            stroke: var(--adm-indigo);
        }

        .adm-quick-card .card-info h3 {
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            font-weight: 700;
            color: var(--adm-text-dark);
            margin: 0 0 2px 0;
        }

        .adm-quick-card .card-info p {
            font-size: 12px;
            color: var(--adm-text-muted);
            margin: 0;
        }

        .adm-form-result {
            border-radius: 6px;
            margin: 0 20px 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
        }

        .adm-form-result-success {
            background: var(--adm-green-lt);
            color: var(--adm-green);
            border: 1px solid var(--adm-green);
        }

        .adm-form-result-error {
            background: var(--adm-red-lt);
            color: var(--adm-red);
            border: 1px solid var(--adm-red);
        }
    </style>
@endpush

@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        <!-- Header Section -->
        <div class="adm-header">
            <div class="adm-header-left">
                <h1>
                    <svg viewBox="0 0 24 24"
                        style="display:inline-block;width:22px;height:22px;stroke:var(--adm-blue);fill:none;stroke-width:2;vertical-align:-4px;margin-right:6px;">
                        <path
                            d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
                    </svg>
                    {{ $pageTitle }}
                </h1>
                @isset($breadcrumbs)
                    <nav style="margin-top: 4px; font-size: 13px; color: var(--adm-text-muted);">
                        <ol style="display: flex; align-items: center; gap: 6px; list-style: none; padding: 0; margin: 0;">
                            @foreach ($breadcrumbs as $breadcrumb)
                                @if ($loop->last)
                                    <span
                                        style="color: var(--adm-text-dark); font-weight: 500;">{{ $breadcrumb['title'] }}</span>
                                @else
                                    <a href="{{ $breadcrumb['url'] }}"
                                        style="color: var(--adm-text-muted); text-decoration: none;"
                                        class="hover:text-dark">{{ $breadcrumb['title'] }}</a>
                                    <svg style="width: 12px; height: 12px; stroke: var(--adm-text-faint); fill: none; stroke-width: 2;"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                @endif
                            @endforeach
                        </ol>
                    </nav>
                @endisset
            </div>
        </div>

        <!-- Alert Messages -->
        @if (session('success'))
            <div class="adm-alert adm-alert-success">
                <svg viewBox="0 0 24 24">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                    <polyline points="22 4 12 14.01 9 11.01" />
                </svg>
                <div>
                    <strong>Berhasil!</strong>
                    <div>{{ session('success') }}</div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="adm-alert adm-alert-danger">
                <svg viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="15" y1="9" x2="9" y2="15" />
                    <line x1="9" y1="9" x2="15" y2="15" />
                </svg>
                <div>
                    <strong>Error!</strong>
                    <div>{{ session('error') }}</div>
                </div>
            </div>
        @endif

        <!-- Status Card -->
        <div class="adm-card" style="margin-bottom: 20px;">
            <div class="adm-card-header"
                style="display: flex; align-items: center; justify-content: space-between; padding: 15px 20px; border-bottom: none; background: #fff;">
                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span
                            class="adm-badge {{ $healthStatus['status'] === 'online' ? 'adm-badge-success' : 'adm-badge-danger' }}"
                            style="padding: 4px 12px; font-size: 12px;">
                            <span class="dot"
                                style="{{ $healthStatus['status'] === 'online' ? 'animation: pulse 1.5s infinite;' : '' }}"></span>
                            Status: {{ ucfirst($healthStatus['status']) }}
                        </span>
                    </div>
                    <span style="font-size: 13px; color: var(--adm-text-mid);">{{ $healthStatus['message'] }}</span>
                </div>
                <button onclick="testConnection(event)" class="adm-btn-secondary" id="btnTestConn">
                    <svg viewBox="0 0 24 24">
                        <polyline points="23 4 23 10 17 10" />
                        <path d="M20.49 15a9 9 0 11-2.12-9.36L23 10" />
                    </svg>
                    Test Koneksi
                </button>
            </div>
        </div>

        <!-- Configuration Form -->
        <div class="adm-form-section">
            <div class="adm-form-section-header">
                <svg viewBox="0 0 24 24">
                    <path
                        d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.1a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" />
                    <circle cx="12" cy="12" r="3" />
                </svg>
                Konfigurasi WhatsApp Gateway
            </div>
            <form method="POST" action="{{ route('superadmin.wa-gateway-config.update') }}">
                @csrf
                @method('PUT')

                <div class="adm-form-body">
                    <div class="adm-form-grid cols-1" style="gap: 16px;">
                        <!-- WA Gateway URL -->
                        <div class="adm-field">
                            <label for="wa_gateway_url" class="adm-label">
                                WA Gateway URL <span class="req">*</span>
                            </label>
                            <input type="url" name="wa_gateway_url" id="wa_gateway_url"
                                value="{{ old('wa_gateway_url', $configs['wa_gateway_url'] ?? 'http://localhost:3000') }}"
                                class="adm-input @error('wa_gateway_url') is-invalid @enderror"
                                placeholder="http://localhost:3000" required>
                            @error('wa_gateway_url')
                                <span class="adm-error-msg">{{ $message }}</span>
                            @enderror
                            <span class="adm-hint">URL untuk Node.js Baileys WhatsApp service.</span>
                        </div>

                        <!-- Base URL -->
                        <div class="adm-field">
                            <label for="base_url" class="adm-label">
                                Kawalaku Gateway API URL <span class="req">*</span>
                            </label>
                            <input type="url" name="base_url" id="base_url"
                                value="{{ old('base_url', $configs['base_url'] ?? 'http://kawalakugateway.test') }}"
                                class="adm-input @error('base_url') is-invalid @enderror"
                                placeholder="http://kawalakugateway.test" required>
                            @error('base_url')
                                <span class="adm-error-msg">{{ $message }}</span>
                            @enderror
                            <span class="adm-hint">URL untuk Kawalaku Gateway Laravel API.</span>
                        </div>

                        <!-- API Key -->
                        <div class="adm-field">
                            <label for="api_key" class="adm-label">
                                API Key
                            </label>
                            <input type="text" name="api_key" id="api_key"
                                value="{{ old('api_key', $configs['api_key'] ?? '') }}"
                                class="adm-input @error('api_key') is-invalid @enderror"
                                placeholder="kawagate-xxxxx-xxxxx-xxxxx" autocomplete="off">
                            @error('api_key')
                                <span class="adm-error-msg">{{ $message }}</span>
                            @enderror
                            <span class="adm-hint">API Key dari dashboard Kawalaku Gateway.</span>
                        </div>

                        <!-- Default Media URL -->
                        <div class="adm-field">
                            <label for="default_media_url" class="adm-label">
                                URL Media Default
                            </label>
                            <input type="url" name="default_media_url" id="default_media_url"
                                value="{{ old('default_media_url', $configs['default_media_url'] ?? 'https://kawulohalal.id/assets/logo.png') }}"
                                class="adm-input @error('default_media_url') is-invalid @enderror"
                                placeholder="https://kawulohalal.id/assets/logo.png">
                            @error('default_media_url')
                                <span class="adm-error-msg">{{ $message }}</span>
                            @enderror
                            <span class="adm-hint">URL default untuk image/media notification.</span>
                        </div>

                        <!-- Enabled Toggle -->
                        <div class="adm-field"
                            style="flex-direction: row; align-items: center; gap: 8px; margin-top: 6px;">
                            <input type="checkbox" name="enabled" id="enabled" value="1"
                                {{ old('enabled', $configs['enabled'] ?? true) ? 'checked' : '' }}
                                style="width: 17px; height: 17px; cursor: pointer;">
                            <label for="enabled" class="adm-label" style="cursor: pointer; margin-bottom: 0;">
                                Aktifkan WhatsApp Gateway
                            </label>
                        </div>
                    </div>
                </div>

                <div class="adm-form-actions" style="justify-content: flex-end;">
                    <button type="submit" class="adm-btn-primary">
                        <svg viewBox="0 0 24 24">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                            <polyline points="17 21 17 13 7 13 7 21" />
                            <polyline points="7 3 7 8 15 8" />
                        </svg>
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>

        <!-- Test Message Section -->
        <div class="adm-form-section" style="margin-top: 20px;">
            <div class="adm-form-section-header">
                <svg viewBox="0 0 24 24">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                </svg>
                Test Kirim Pesan
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px;">
                <!-- Test Text Message -->
                <div class="adm-card">
                    <div class="adm-card-header">
                        <div class="adm-card-title">
                            <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" /></svg>
                            Kirim Pesan Teks
                        </div>
                    </div>
                    <form id="formTestText" onsubmit="sendTestText(event)">
                        <div class="adm-form-body" style="padding: 20px;">
                            <div class="adm-field">
                                <label for="text_device_id" class="adm-label">
                                    Perangkat WA
                                </label>
                                <select name="device_id" id="text_device_id" class="adm-select">
                                    <option value="">Pilih perangkat</option>
                                    @foreach($devices as $device)
                                        <option value="{{ $device['hashed_id'] }}" data-source="{{ $device['source'] ?? 'local' }}">
                                            {{ $device['name'] }} {{ !empty($device['phone']) ? '- ' . $device['phone'] : '' }}
                                            @if(($device['source'] ?? 'local') === 'api')
                                                [API]
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @if(empty($devices))
                                    <span class="adm-hint" style="color: var(--adm-amber);">Belum ada perangkat terhubung.</span>
                                @endif
                            </div>
                            <div class="adm-field" style="margin-top: 12px;">
                                <label for="text_number" class="adm-label">
                                    Nomor Tujuan <span class="req">*</span>
                                </label>
                                <input type="text" name="number" id="text_number"
                                    class="adm-input"
                                    placeholder="6281234567890"
                                    required>
                                <span class="adm-hint">Format: 62xxxxxxxxxx (tanpa +)</span>
                            </div>
                            <div class="adm-field" style="margin-top: 12px;">
                                <label for="text_message" class="adm-label">
                                    Pesan <span class="req">*</span>
                                </label>
                                <textarea name="message" id="text_message" rows="4"
                                    class="adm-input"
                                    placeholder="Tulis pesan Anda di sini..."
                                    required></textarea>
                            </div>
                        </div>
                        <div class="adm-form-actions" style="padding: 0 20px 20px;">
                            <button type="submit" class="adm-btn-primary" id="btnSendText" style="width: 100%;" {{ empty($devices) ? 'disabled' : '' }}>
                                <svg viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2" /></svg>
                                Kirim Pesan Teks
                            </button>
                        </div>
                    </form>
                    <div id="resultText" class="adm-form-result" style="display: none; padding: 12px 20px;"></div>
                </div>

                <!-- Test Media Message -->
                <div class="adm-card">
                    <div class="adm-card-header">
                        <div class="adm-card-title">
                            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            Kirim Pesan Media
                        </div>
                    </div>
                    <form id="formTestMedia" onsubmit="sendTestMedia(event)">
                        <div class="adm-form-body" style="padding: 20px;">
                            <div class="adm-field">
                                <label for="media_device_id" class="adm-label">
                                    Perangkat WA
                                </label>
                                <select name="device_id" id="media_device_id" class="adm-select">
                                    <option value="">Pilih perangkat</option>
                                    @foreach($devices as $device)
                                        <option value="{{ $device['hashed_id'] }}" data-source="{{ $device['source'] ?? 'local' }}">
                                            {{ $device['name'] }} {{ !empty($device['phone']) ? '- ' . $device['phone'] : '' }}
                                            @if(($device['source'] ?? 'local') === 'api')
                                                [API]
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @if(empty($devices))
                                    <span class="adm-hint" style="color: var(--adm-amber);">Belum ada perangkat terhubung.</span>
                                @endif
                            </div>
                            <div class="adm-field" style="margin-top: 12px;">
                                <label for="media_number" class="adm-label">
                                    Nomor Tujuan <span class="req">*</span>
                                </label>
                                <input type="text" name="number" id="media_number"
                                    class="adm-input"
                                    placeholder="6281234567890"
                                    required>
                                <span class="adm-hint">Format: 62xxxxxxxxxx (tanpa +)</span>
                            </div>
                            <div class="adm-field" style="margin-top: 12px;">
                                <label for="media_type" class="adm-label">
                                    Tipe Media <span class="req">*</span>
                                </label>
                                <select name="media_type" id="media_type" class="adm-select" required {{ empty($devices) ? 'disabled' : '' }}>
                                    <option value="">Pilih tipe media</option>
                                    <option value="image">Image (Gambar)</option>
                                    <option value="video">Video</option>
                                    <option value="audio">Audio</option>
                                    <option value="document">Document (PDF/Dokumen)</option>
                                </select>
                            </div>
                            <div class="adm-field" style="margin-top: 12px;">
                                <label for="media_url" class="adm-label">
                                    URL Media <span class="req">*</span>
                                </label>
                                <input type="url" name="media_url" id="media_url"
                                    class="adm-input"
                                    placeholder="https://example.com/image.jpg"
                                    required {{ empty($devices) ? 'disabled' : '' }}>
                                <span class="adm-hint">URL publik untuk file media.</span>
                            </div>
                            <div class="adm-field" style="margin-top: 12px;">
                                <label for="media_caption" class="adm-label">
                                    Caption / Pesan
                                </label>
                                <textarea name="caption" id="media_caption" rows="3"
                                    class="adm-input"
                                    placeholder="Caption untuk media (opsional)..."></textarea>
                            </div>
                        </div>
                        <div class="adm-form-actions" style="padding: 0 20px 20px;">
                            <button type="submit" class="adm-btn-primary" id="btnSendMedia" style="width: 100%;" {{ empty($devices) ? 'disabled' : '' }}>
                                <svg viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2" /></svg>
                                Kirim Pesan Media
                            </button>
                        </div>
                    </form>
                    <div id="resultMedia" class="adm-form-result" style="display: none; padding: 12px 20px;"></div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="adm-grid-links"
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-top: 20px;">
            <a href="{{ route('superadmin.wa-devices.index') }}" class="adm-quick-card">
                <div class="icon-wrap bg-blue">
                    <svg viewBox="0 0 24 24">
                        <rect x="5" y="2" width="14" height="20" rx="2" ry="2" />
                        <line x1="12" y1="18" x2="12.01" y2="18" />
                    </svg>
                </div>
                <div class="card-info">
                    <h3>Kelola Perangkat</h3>
                    <p>Scan & tambah perangkat WA</p>
                </div>
            </a>

            <a href="{{ rtrim($configs['base_url'] ?? 'http://kawalakugateway.test', '/') }}/superadmin/dashboard"
                target="_blank" class="adm-quick-card">
                <div class="icon-wrap bg-green">
                    <svg viewBox="0 0 24 24">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2" />
                        <line x1="8" y1="21" x2="16" y2="21" />
                        <line x1="12" y1="17" x2="12" y2="21" />
                    </svg>
                </div>
                <div class="card-info">
                    <h3>Dashboard Gateway</h3>
                    <p>Kawalaku Gateway Dashboard</p>
                </div>
            </a>

            <a href="{{ rtrim($configs['base_url'] ?? 'http://kawalakugateway.test', '/') }}/superadmin/wa-messages"
                target="_blank" class="adm-quick-card">
                <div class="icon-wrap bg-indigo">
                    <svg viewBox="0 0 24 24">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                    </svg>
                </div>
                <div class="card-info">
                    <h3>Riwayat Pesan</h3>
                    <p>Lihat semua pesan WA</p>
                </div>
            </a>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function testConnection(event) {
            if (event) event.preventDefault();
            const btn = document.getElementById('btnTestConn');
            if (!btn) return;
            const originalText = btn.innerHTML;
            btn.innerHTML =
                '<svg class="spin" style="width: 14px; height: 14px; fill: none; stroke: currentColor; stroke-width: 2.2;" viewBox="0 0 24 24"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg> Menguji...';
            btn.disabled = true;

            fetch('{{ route('superadmin.wa-gateway-config.test-connection') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: data.message,
                                timer: 3000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            alert('✅ ' + data.message);
                            location.reload();
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Koneksi Gagal',
                                text: data.message
                            });
                        } else {
                            alert('❌ ' + data.message);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: 'Terjadi kesalahan: ' + error.message
                        });
                    } else {
                        alert('❌ Terjadi kesalahan: ' + error.message);
                    }
                })
                .finally(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
        }

        function sendTestText(event) {
            event.preventDefault();
            const form = event.target;
            const btn = document.getElementById('btnSendText');
            const resultDiv = document.getElementById('resultText');
            const originalText = btn.innerHTML;

            btn.innerHTML = '<svg class="spin" viewBox="0 0 24 24" style="width: 14px; height: 14px; fill: none; stroke: currentColor; stroke-width: 2.2;"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/></svg> Mengirim...';
            btn.disabled = true;
            resultDiv.style.display = 'none';

            const formData = new FormData(form);
            // Get device source from selected option
            const selectedOption = document.getElementById('text_device_id').selectedOptions[0];
            const deviceSource = selectedOption ? selectedOption.getAttribute('data-source') || 'local' : 'local';
            formData.append('device_source', deviceSource);

            fetch('{{ route('superadmin.wa-gateway-config.send-test-text') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                resultDiv.style.display = 'block';
                if (data.success) {
                    resultDiv.className = 'adm-form-result adm-form-result-success';
                    resultDiv.innerHTML = '<svg viewBox="0 0 24 24" style="width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> <strong>Berhasil!</strong> ' + data.message;
                    form.reset();
                } else {
                    resultDiv.className = 'adm-form-result adm-form-result-error';
                    resultDiv.innerHTML = '<svg viewBox="0 0 24 24" style="width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg> <strong>Gagal!</strong> ' + data.message;
                }
            })
            .catch(error => {
                resultDiv.style.display = 'block';
                resultDiv.className = 'adm-form-result adm-form-result-error';
                resultDiv.innerHTML = '<svg viewBox="0 0 24 24" style="width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg> <strong>Error!</strong> ' + error.message;
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }

        function sendTestMedia(event) {
            event.preventDefault();
            const form = event.target;
            const btn = document.getElementById('btnSendMedia');
            const resultDiv = document.getElementById('resultMedia');
            const originalText = btn.innerHTML;

            btn.innerHTML = '<svg class="spin" viewBox="0 0 24 24" style="width: 14px; height: 14px; fill: none; stroke: currentColor; stroke-width: 2.2;"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/></svg> Mengirim...';
            btn.disabled = true;
            resultDiv.style.display = 'none';

            const formData = new FormData(form);

            fetch('{{ route('superadmin.wa-gateway-config.send-test-media') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                resultDiv.style.display = 'block';
                if (data.success) {
                    resultDiv.className = 'adm-form-result adm-form-result-success';
                    resultDiv.innerHTML = '<svg viewBox="0 0 24 24" style="width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> <strong>Berhasil!</strong> ' + data.message;
                    form.reset();
                } else {
                    resultDiv.className = 'adm-form-result adm-form-result-error';
                    resultDiv.innerHTML = '<svg viewBox="0 0 24 24" style="width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg> <strong>Gagal!</strong> ' + data.message;
                }
            })
            .catch(error => {
                resultDiv.style.display = 'block';
                resultDiv.className = 'adm-form-result adm-form-result-error';
                resultDiv.innerHTML = '<svg viewBox="0 0 24 24" style="width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg> <strong>Error!</strong> ' + error.message;
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }
    </script>
@endpush
