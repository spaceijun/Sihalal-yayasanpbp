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
            <div style="display: flex; align-items: flex-end; flex-wrap: wrap; gap: 12px; width: 100%; margin: 0;">
                <div class="adm-filter-group" style="flex: 1; min-width: 200px;">
                    <span class="adm-filter-label">Cari Perangkat</span>
                    <div class="adm-search-shell">
                        <svg class="adm-search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text" id="filterSearch"
                            placeholder="Cari nama atau nomor telepon..."
                            class="adm-search-input" style="width: 100%;">
                    </div>
                </div>

                <div class="adm-filter-group" style="min-width: 160px;">
                    <span class="adm-filter-label">Status</span>
                    <select id="filterStatus" class="adm-select" style="width: 100%;">
                        <option value="">Semua Status</option>
                        <option value="connected">Terhubung</option>
                        <option value="connecting">Menghubungkan</option>
                        <option value="disconnected">Terputus</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table id="waDevicesTable" class="adm-table w-100">
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
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        window.dataTableInstance = $('#waDevicesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('superadmin.wa-devices.data') }}',
                type: 'GET',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: function (d) {
                    d.status = $('#filterStatus').val();
                    d.search = { value: $('#filterSearch').val() };
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'tc' },
                { data: 'name', name: 'name', render: (d) => `<span style="font-weight:600;color:var(--adm-text-dark);">${d}</span>` },
                { data: 'phone_fmt', name: 'phone' },
                { data: 'status_badge', name: 'status', orderable: false },
                { data: 'last_connected_fmt', name: 'last_connected_at' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'tc' },
            ],
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ – _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                paginate: { previous: '‹', next: '›' },
                zeroRecords: 'Tidak ada perangkat ditemukan',
                emptyTable: 'Belum ada perangkat WhatsApp',
                processing: '<div class="spinner-border text-primary" role="status"></div>',
            },
            pageLength: 15,
            order: [[1, 'asc']],
            responsive: true,
        });

        $('#filterStatus').on('change', () => window.dataTableInstance.ajax.reload());
        let searchTimeout;
        $('#filterSearch').on('keyup', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => window.dataTableInstance.ajax.reload(), 400);
        });
    });

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
                        });
                        window.dataTableInstance.ajax.reload(null, false);
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
                        });
                        window.dataTableInstance.ajax.reload(null, false);
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
