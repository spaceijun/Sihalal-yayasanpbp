@extends('layouts.app')
@section('template_title', $pageTitle)

@push('styles')
<style>
    /* Spinner animation */
    .spin {
        animation: _spin 1s linear infinite;
        display: inline-block;
    }
    @keyframes _spin {
        to { transform: rotate(360deg); }
    }

    /* Toggle Switches style */
    .adm-toggle-btn {
        position: relative;
        width: 44px;
        height: 22px;
        border-radius: 20px;
        border: none;
        cursor: pointer;
        transition: background-color 0.2s;
        padding: 0;
        display: inline-flex;
        align-items: center;
        flex-shrink: 0;
    }
    .toggle-active {
        background-color: var(--adm-green);
    }
    .toggle-inactive {
        background-color: var(--adm-text-faint);
    }
    .adm-toggle-dot {
        display: inline-block;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background-color: #fff;
        transition: transform 0.2s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.15);
    }
    .translate-x-toggle {
        transform: translateX(24px);
    }
    .translate-x-1 {
        transform: translateX(4px);
    }
    [x-cloak] { display: none !important; }
</style>
@endpush

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
        <div class="adm-header-right">
            <a href="{{ route('superadmin.wa-devices.index') }}" class="adm-btn-secondary">
                <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="adm-alert adm-alert-success">
            <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <div>
                <strong>Berhasil!</strong>
                <div>{{ session('success') }}</div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="adm-alert adm-alert-danger">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            <div>
                <strong>Error!</strong>
                <div>{{ session('error') }}</div>
            </div>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
        <!-- Device Info Card -->
        <div class="adm-card">
            <div class="adm-card-header">
                <div class="adm-card-title">
                    <svg viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                    Informasi Perangkat
                </div>
                @php
                    $statusClass = 'adm-badge-danger';
                    if ($device->isConnected()) {
                        $statusClass = 'adm-badge-success';
                    } elseif ($device->status === 'connecting') {
                        $statusClass = 'adm-badge-pending';
                    }
                @endphp
                <span class="adm-badge {{ $statusClass }}">
                    <span class="dot"></span>
                    {{ $device->getStatusText() }}
                </span>
            </div>

            <div class="adm-form-body" style="padding: 20px;">
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; justify-content: space-between; padding-bottom: 10px; border-bottom: 1px solid var(--adm-border);">
                        <span style="color: var(--adm-text-muted); font-size: 13px;">Nama</span>
                        <span style="font-weight: 600; color: var(--adm-text-dark); font-size: 13px;">{{ $device->name }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding-bottom: 10px; border-bottom: 1px solid var(--adm-border);">
                        <span style="color: var(--adm-text-muted); font-size: 13px;">Nomor WhatsApp</span>
                        <span style="font-weight: 600; color: var(--adm-text-dark); font-size: 13px;">{{ $device->phone ?? '-' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding-bottom: 10px; border-bottom: 1px solid var(--adm-border);">
                        <span style="color: var(--adm-text-muted); font-size: 13px;">Device ID</span>
                        <span style="font-family: monospace; font-size: 12px; color: var(--adm-text-mid);">{{ $device->hashed_id }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding-bottom: 10px; border-bottom: 1px solid var(--adm-border);">
                        <span style="color: var(--adm-text-muted); font-size: 13px;">Terakhir Terhubung</span>
                        <span style="font-weight: 600; color: var(--adm-text-dark); font-size: 13px;">
                            {{ $device->last_connected_at ? $device->last_connected_at->format('d M Y, H:i') : '-' }}
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding-bottom: 5px;">
                        <span style="color: var(--adm-text-muted); font-size: 13px;">Dibuat</span>
                        <span style="font-weight: 600; color: var(--adm-text-dark); font-size: 13px;">{{ $device->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 24px;">
                    <a href="{{ route('superadmin.wa-devices.edit', $device->hashed_id) }}" class="adm-btn-secondary" style="flex: 1; justify-content: center;">
                        <svg viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                        Edit
                    </a>
                    <button onclick="deleteDevice('{{ $device->hashed_id }}', '{{ $device->name }}')" class="adm-btn-secondary" style="flex: 1; justify-content: center; color: var(--adm-red); border-color: var(--adm-red-lt); background: var(--adm-red-lt);">
                        <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                        Hapus
                    </button>
                </div>
            </div>
        </div>

        <!-- QR Code Card -->
        <div class="adm-card" x-data="qrScanner()">
            <div class="adm-card-header">
                <div class="adm-card-title">
                    <svg viewBox="0 0 24 24"><path d="M4 4h6v6H4zm10 0h6v6h-6zM4 14h6v6H4zm10 0h6v6h-6z"/></svg>
                    Scan QR Code
                </div>
            </div>

            <div class="adm-form-body" style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 320px; padding: 20px;">
                
                <div x-show="loading" class="adm-loading" style="padding: 0;">
                    <svg class="spin" style="width: 32px; height: 32px; stroke: var(--adm-blue); fill: none; stroke-width: 2.2;" viewBox="0 0 24 24">
                        <line x1="12" y1="2" x2="12" y2="6"/>
                        <line x1="12" y1="18" x2="12" y2="22"/>
                        <line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/>
                        <line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/>
                        <line x1="2" y1="12" x2="6" y2="12"/>
                        <line x1="18" y1="12" x2="22" y2="12"/>
                        <line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/>
                        <line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/>
                    </svg>
                    <p style="margin: 0; font-size: 13px; color: var(--adm-text-mid); font-weight: 500;">Memuat QR Code...</p>
                </div>

                <div x-show="!loading && !connected" x-cloak style="display: flex; flex-direction: column; align-items: center; width: 100%;">
                    <div id="qr-code-container" style="padding: 16px; background: #fff; border: 2px dashed var(--adm-border-mid); border-radius: var(--adm-radius); display: flex; align-items: center; justify-content: center;">
                        <div class="flex flex-col items-center justify-center" style="width: 192px; height: 192px;" id="qr-placeholder">
                            <svg style="width: 48px; height: 48px; stroke: var(--adm-text-faint); fill: none; stroke-width: 1.5;" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                            <p style="margin-top: 8px; font-size: 11px; color: var(--adm-text-muted); text-align: center; max-width: 150px; line-height: 1.4;">QR Code akan muncul di sini setelah Anda mengklik tombol Scan</p>
                        </div>
                    </div>

                    <div style="margin-top: 16px; display: flex; flex-direction: column; gap: 4px; width: 100%; text-align: left; padding: 0 10px;">
                        <p style="font-size: 12px; color: var(--adm-text-mid); margin: 0;">1. Buka WhatsApp di HP Anda</p>
                        <p style="font-size: 12px; color: var(--adm-text-mid); margin: 0;">2. Ketuk Menu atau Setelan</p>
                        <p style="font-size: 12px; color: var(--adm-text-mid); margin: 0;">3. Ketuk Perangkat Tertaut</p>
                        <p style="font-size: 12px; color: var(--adm-text-mid); margin: 0;">4. Ketuk Tautkan Perangkat</p>
                        <p style="font-size: 12px; color: var(--adm-text-mid); margin: 0;">5. Arahkan HP Anda ke QR Code di atas</p>
                    </div>

                    <button @click="startScan()"
                        x-show="!scanning"
                        class="adm-btn-primary" style="margin-top: 20px; width: 100%; justify-content: center; height: 38px;">
                        <svg viewBox="0 0 24 24" style="stroke: #fff; fill: none; stroke-width: 2.2; width: 15px; height: 15px;"><path d="M3 7V5a2 2 0 0 1 2-2h2m10 0h2a2 2 0 0 1 2 2v2m0 10v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2"/></svg>
                        Mulai Scan QR Code
                    </button>
                </div>

                <div x-show="!loading && connected" x-cloak style="display: flex; flex-direction: column; align-items: center; width: 100%;">
                    <div style="padding: 16px; background: var(--adm-green-lt); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                        <svg viewBox="0 0 24 24" style="width: 44px; height: 44px; stroke: var(--adm-green); fill: none; stroke-width: 2.5;"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <p style="font-family: 'Sora', sans-serif; font-size: 16px; font-weight: 700; color: var(--adm-green); margin: 0 0 4px 0;">Perangkat Terhubung!</p>
                    <p style="font-size: 12.5px; color: var(--adm-text-muted); margin: 0 0 20px 0;">WhatsApp siap digunakan untuk mengirim notifikasi</p>

                    <button @click="disconnect()"
                        class="adm-btn-secondary" style="width: 100%; justify-content: center; color: var(--adm-red); border-color: var(--adm-red-lt); background: var(--adm-red-lt); height: 38px;">
                        <svg viewBox="0 0 24 24"><line x1="1" y1="1" x2="23" y2="23"/><path d="M16.7 11.73a5 5 0 0 0-7.54-.54l-2.77 2.77a5 5 0 0 0 7.07 7.07l1.11-1.11"/><path d="M8 12a5 5 0 0 0 7.54.54l2.73-2.73a5 5 0 0 0-7.07-7.07l-1.07 1.07"/></svg>
                        Putuskan Koneksi
                    </button>
                </div>

            </div>
            <input type="hidden" id="device-id" value="{{ $device->hashed_id }}">
        </div>
    </div>

    <!-- Device Features Card -->
    <div class="adm-card" x-data="deviceFeatures()" style="margin-top: 20px;">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.1a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                Pengaturan & Fitur Perangkat
            </div>
        </div>

        <div class="adm-form-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px;">
                
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; border: 1px solid var(--adm-border); border-radius: var(--adm-radius); background: var(--adm-bg-light);">
                    <div>
                        <p style="font-weight: 600; color: var(--adm-text-dark); font-size: 13.5px; margin: 0 0 2px 0;">Tolak Panggilan</p>
                        <p style="font-size: 11.5px; color: var(--adm-text-muted); margin: 0;">Otomatis tolak panggilan masuk</p>
                    </div>
                    <button @click="toggleFeature('reject_call')"
                        :class="features.reject_call ? 'toggle-active' : 'toggle-inactive'"
                        class="adm-toggle-btn">
                        <span :class="features.reject_call ? 'translate-x-toggle' : 'translate-x-1'" class="adm-toggle-dot"></span>
                    </button>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; border: 1px solid var(--adm-border); border-radius: var(--adm-radius); background: var(--adm-bg-light);">
                    <div>
                        <p style="font-weight: 600; color: var(--adm-text-dark); font-size: 13.5px; margin: 0 0 2px 0;">Status Online</p>
                        <p style="font-size: 11.5px; color: var(--adm-text-muted); margin: 0;">Tampilkan status selalu online</p>
                    </div>
                    <button @click="toggleFeature('available')"
                        :class="features.available ? 'toggle-active' : 'toggle-inactive'"
                        class="adm-toggle-btn">
                        <span :class="features.available ? 'translate-x-toggle' : 'translate-x-1'" class="adm-toggle-dot"></span>
                    </button>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; border: 1px solid var(--adm-border); border-radius: var(--adm-radius); background: var(--adm-bg-light);">
                    <div>
                        <p style="font-weight: 600; color: var(--adm-text-dark); font-size: 13.5px; margin: 0 0 2px 0;">Indikator Mengetik</p>
                        <p style="font-size: 11.5px; color: var(--adm-text-muted); margin: 0;">Tampilkan indikator pengetikan</p>
                    </div>
                    <button @click="toggleFeature('typing')"
                        :class="features.typing ? 'toggle-active' : 'toggle-inactive'"
                        class="adm-toggle-btn">
                        <span :class="features.typing ? 'translate-x-toggle' : 'translate-x-1'" class="adm-toggle-dot"></span>
                    </button>
                </div>

            </div>
        </div>
        <input type="hidden" id="device-features-id" value="{{ $device->hashed_id }}">
    </div>
</div>
@endsection

@push('scripts')
<script>
    // SweetAlert Toast definition
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4500,
        timerProgressBar: true,
        didOpen: function (toast) {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    // QR Scanner Alpine Component
    function qrScanner() {
        return {
            scanning: false,
            loading: false,
            connected: {{ $device->isConnected() ? 'true' : 'false' }},
            qrCode: null,

            init() {
                // Initialize based on device status
                if (this.connected) {
                    this.loading = false;
                }
            },

            async startScan() {
                this.scanning = true;
                this.loading = true;

                const deviceId = document.getElementById('device-id').value;

                try {
                    const response = await fetch(`/superadmin/wa-devices/${deviceId}/generate-qr`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        },
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.renderQRCode(data);
                        this.pollQRStatus(deviceId);
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: data.message || 'Gagal menghasilkan QR code'
                        });
                        this.loading = false;
                        this.scanning = false;
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Toast.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan saat memuat QR code'
                    });
                    this.loading = false;
                    this.scanning = false;
                }
            },

            renderQRCode(data) {
                this.loading = false;

                const container = document.getElementById('qr-placeholder');
                if (container) {
                    // Check if we have a base64 data URL from backend
                    if (data.qr_code_data_url) {
                        container.innerHTML = '<div id="qr-image"><img src="' + data.qr_code_data_url + '" alt="QR Code" style="width: 192px; height: 192px; image-rendering: pixelated;"></div>';
                    } else if (data.qr_code) {
                        // Fallback: show raw QR data for debugging (placeholder SVG)
                        container.innerHTML = '<div id="qr-image" style="width: 192px; height: 192px; display: flex; align-items: center; justify-content: center; background: #f0f0f0; border: 1px dashed #ccc; text-align: center; font-size: 11px; color: #666;">' +
                            '<div>QR Code:<br><code style="font-size: 10px; word-break: break-all;">' + data.qr_code.substring(0, 50) + '...</code></div>' +
                            '</div>';
                    } else {
                        container.innerHTML = '<div id="qr-image" style="width: 192px; height: 192px; display: flex; align-items: center; justify-content: center; background: #f0f0f0; border: 1px dashed #ccc;">' +
                            '<span style="color: #999;">QR Code tidak tersedia</span></div>';
                    }
                }
            },

            pollQRStatus(deviceId) {
                const poll = setInterval(async () => {
                    try {
                        const response = await fetch(`/superadmin/wa-devices/${deviceId}/status`);
                        const data = await response.json();

                        if (data.status === 'connected') {
                            clearInterval(poll);
                            this.connected = true;
                            this.scanning = false;
                            // Reload page to update UI
                            window.location.reload();
                        }
                    } catch (error) {
                        console.error('Poll error:', error);
                    }
                }, 3000);

                // Stop polling after 5 minutes
                setTimeout(() => clearInterval(poll), 300000);
            },

            async disconnect() {
                Swal.fire({
                    title: 'Putuskan Koneksi',
                    text: 'Apakah Anda yakin ingin memutuskan koneksi perangkat ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#8a99b3',
                    confirmButtonText: 'Ya, Putuskan!',
                    cancelButtonText: 'Batal'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Memproses...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        const deviceId = document.getElementById('device-id').value;

                        try {
                            const response = await fetch(`/superadmin/wa-devices/${deviceId}/disconnect`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                },
                            });

                            const data = await response.json();

                            if (data.success) {
                                Swal.close();
                                Toast.fire({
                                    icon: 'success',
                                    title: 'Koneksi berhasil diputuskan'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.close();
                                Toast.fire({
                                    icon: 'error',
                                    title: data.message || 'Gagal memutuskan perangkat'
                                });
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            Swal.close();
                            Toast.fire({
                                icon: 'error',
                                title: 'Terjadi kesalahan saat memutuskan perangkat'
                            });
                        }
                    }
                });
            }
        }
    }

    // Device Features Alpine Component
    function deviceFeatures() {
        return {
            features: {
                reject_call: {{ ($features['features']['reject_call'] ?? false) ? 'true' : 'false' }},
                available: {{ ($features['features']['available'] ?? true) ? 'true' : 'false' }},
                typing: {{ ($features['features']['typing'] ?? true) ? 'true' : 'false' }}
            },

            async toggleFeature(feature) {
                this.features[feature] = !this.features[feature];

                const deviceId = document.getElementById('device-features-id').value;

                try {
                    const response = await fetch(`/superadmin/wa-devices/${deviceId}/features`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            [feature]: this.features[feature]
                        }),
                    });

                    const data = await response.json();

                    if (!data.success) {
                        // Revert on failure
                        this.features[feature] = !this.features[feature];
                        Toast.fire({
                            icon: 'error',
                            title: data.message || 'Gagal memperbarui pengaturan'
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    // Revert on error
                    this.features[feature] = !this.features[feature];
                    Toast.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan saat memperbarui pengaturan'
                    });
                }
            }
        }
    }

    // Delete Device
    function deleteDevice(deviceId, deviceName) {
        Swal.fire({
            title: 'Hapus Perangkat',
            text: `Apakah Anda yakin ingin menghapus perangkat "${deviceName}"? Tindakan ini tidak dapat dibatalkan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#8a99b3',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`/superadmin/wa-devices/${deviceId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.close();
                        Toast.fire({
                            icon: 'success',
                            title: 'Perangkat berhasil dihapus'
                        }).then(() => {
                            window.location.href = '{{ route('superadmin.wa-devices.index') }}';
                        });
                    } else {
                        Swal.close();
                        Toast.fire({
                            icon: 'error',
                            title: data.message || 'Gagal menghapus perangkat'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.close();
                    Toast.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan saat menghapus perangkat'
                    });
                });
            }
        });
    }
</script>
@endpush
