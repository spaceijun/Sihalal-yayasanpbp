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
        <div class="adm-header-right">
            <a href="{{ route('superadmin.wa-devices.create') }}" class="adm-btn-primary">
                <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Perangkat
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="adm-stats">
        <div class="adm-stat">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div class="adm-stat-label">Total Perangkat</div>
                    <div class="adm-stat-value">{{ $statistics['total_devices'] }}</div>
                </div>
                <div style="padding: 10px; background: var(--adm-blue-lt); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; stroke: var(--adm-blue); fill: none; stroke-width: 2;"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                </div>
            </div>
        </div>
        <div class="adm-stat">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div class="adm-stat-label">Terhubung</div>
                    <div class="adm-stat-value is-success">{{ $statistics['connected'] }}</div>
                </div>
                <div style="padding: 10px; background: var(--adm-green-lt); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; stroke: var(--adm-green); fill: none; stroke-width: 2;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
            </div>
        </div>
        <div class="adm-stat">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div class="adm-stat-label">Menghubungkan</div>
                    <div class="adm-stat-value is-warn">{{ $statistics['connecting'] }}</div>
                </div>
                <div style="padding: 10px; background: var(--adm-amber-lt); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; stroke: var(--adm-amber); fill: none; stroke-width: 2;"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg>
                </div>
            </div>
        </div>
        <div class="adm-stat">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div class="adm-stat-label">Terputus</div>
                    <div class="adm-stat-value is-danger">{{ $statistics['disconnected'] }}</div>
                </div>
                <div style="padding: 10px; background: var(--adm-red-lt); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; stroke: var(--adm-red); fill: none; stroke-width: 2;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                </div>
            </div>
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

    <!-- Filter & Table Card -->
    <div class="adm-card">
        <div class="adm-filter-bar">
            <form method="GET" action="{{ route('superadmin.wa-devices.index') }}" style="display: flex; align-items: flex-end; flex-wrap: wrap; gap: 12px; width: 100%; margin: 0;">
                <div class="adm-filter-group" style="flex: 1; min-width: 200px;">
                    <span class="adm-filter-label">Cari Perangkat</span>
                    <div class="adm-search-shell">
                        <svg class="adm-search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                            placeholder="Cari nama atau nomor telepon..."
                            class="adm-search-input" style="width: 100%;">
                    </div>
                </div>

                <div class="adm-filter-group" style="min-width: 160px;">
                    <span class="adm-filter-label">Status</span>
                    <select name="status" class="adm-select" style="width: 100%;">
                        <option value="">Semua Status</option>
                        <option value="connected" {{ ($filters['status'] ?? '') === 'connected' ? 'selected' : '' }}>Terhubung</option>
                        <option value="connecting" {{ ($filters['status'] ?? '') === 'connecting' ? 'selected' : '' }}>Menghubungkan</option>
                        <option value="disconnected" {{ ($filters['status'] ?? '') === 'disconnected' ? 'selected' : '' }}>Terputus</option>
                    </select>
                </div>

                <div style="display: flex; align-items: center; gap: 8px;">
                    <button type="submit" class="adm-btn-primary" style="height: 34px;">
                        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        Filter
                    </button>
                    @if ($filters['search'] || $filters['status'])
                        <a href="{{ route('superadmin.wa-devices.index') }}" class="adm-reset-btn">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div style="overflow-x: auto;">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Nama</th>
                        <th>Nomor WhatsApp</th>
                        <th>Status</th>
                        <th>Terakhir Terhubung</th>
                        <th style="width: 160px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($devices as $index => $device)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td style="font-weight: 600; color: var(--adm-text-dark);">{{ $device->name }}</td>
                            <td>{{ $device->phone ?? '-' }}</td>
                            <td>
                                <span class="adm-badge {{ $device->isConnected() ? 'adm-badge-success' : (($device->status === 'connecting') ? 'adm-badge-warning' : 'adm-badge-danger') }}">
                                    {{ $device->getStatusText() }}
                                </span>
                            </td>
                            <td>
                                {{ $device->last_connected_at ? $device->last_connected_at->diffForHumans() : '-' }}
                            </td>
                            <td style="text-align: center;">
                                <div style="display: inline-flex; align-items: center; gap: 8px; justify-content: center;">
                                    <a href="{{ route('superadmin.wa-devices.show', $device->hashed_id) }}"
                                        class="adm-btn-secondary" style="width: 32px; height: 32px; padding: 0; justify-content: center; border-radius: 50%;" title="Detail">
                                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <button onclick="connectDevice('{{ $device->hashed_id }}')"
                                        class="adm-btn-secondary" style="width: 32px; height: 32px; padding: 0; justify-content: center; border-radius: 50%; color: var(--adm-green); border-color: var(--adm-green-lt); background: var(--adm-green-lt);"
                                        title="Hubungkan" {{ $device->isConnected() ? 'disabled' : '' }}>
                                        <svg viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                    </button>
                                    <button onclick="disconnectDevice('{{ $device->hashed_id }}')"
                                        class="adm-btn-secondary" style="width: 32px; height: 32px; padding: 0; justify-content: center; border-radius: 50%; color: var(--adm-red); border-color: var(--adm-red-lt); background: var(--adm-red-lt);"
                                        title="Putuskan" {{ !$device->isConnected() ? 'disabled' : '' }}>
                                        <svg viewBox="0 0 24 24"><line x1="1" y1="1" x2="23" y2="23"/><path d="M16.7 11.73a5 5 0 0 0-7.54-.54l-2.77 2.77a5 5 0 0 0 7.07 7.07l1.11-1.11"/><path d="M8 12a5 5 0 0 0 7.54.54l2.73-2.73a5 5 0 0 0-7.07-7.07l-1.07 1.07"/></svg>
                                    </button>
                                    <button onclick="deleteDevice('{{ $device->hashed_id }}', '{{ $device->name }}')"
                                        class="adm-btn-secondary" style="width: 32px; height: 32px; padding: 0; justify-content: center; border-radius: 50%; color: var(--adm-text-muted);" title="Hapus">
                                        <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="adm-empty">
                                    <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="9" x2="15" y2="15"/><line x1="15" y1="9" x2="9" y2="15"/></svg>
                                    <p style="font-weight: 600; color: var(--adm-text-mid); margin-top: 5px;">Tidak ada perangkat ditemukan</p>
                                    <p style="font-size: 12px; color: var(--adm-text-muted);">Klik "Tambah Perangkat" untuk menambahkan perangkat WhatsApp baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer for Pagination -->
        @if ($devices->hasPages())
            <div class="adm-card-footer">
                <div class="adm-footer-info">
                    Menampilkan {{ $devices->firstItem() }} - {{ $devices->lastItem() }} dari {{ $devices->total() }} data
                </div>
                <div class="adm-pagination">
                    {{ $devices->withQueryString()->links() }}
                </div>
            </div>
        @endif
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

    // Connect device
    function connectDevice(deviceId) {
        Swal.fire({
            title: 'Menghubungkan...',
            text: 'Silakan tunggu sebentar',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(`/superadmin/wa-devices/${deviceId}/connect`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = `/superadmin/wa-devices/${deviceId}`;
            } else {
                Swal.close();
                Toast.fire({
                    icon: 'error',
                    title: data.message || 'Gagal menghubungkan perangkat'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.close();
            Toast.fire({
                icon: 'error',
                title: 'Terjadi kesalahan saat menghubungkan perangkat'
            });
        });
    }

    // Disconnect device
    function disconnectDevice(deviceId) {
        Swal.fire({
            title: 'Putuskan Koneksi',
            text: 'Apakah Anda yakin ingin memutuskan koneksi perangkat ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#8a99b3',
            confirmButtonText: 'Ya, Putuskan!',
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

                fetch(`/superadmin/wa-devices/${deviceId}/disconnect`, {
                    method: 'POST',
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
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.close();
                    Toast.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan saat memutuskan perangkat'
                    });
                });
            }
        });
    }

    // Delete device
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
                            window.location.reload();
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
