@extends('layouts.app')
@section('template_title')
    WRGROUP Super Apps
@endsection

@php
    $halt = $dashboard['halt'];
    $conn = $dashboard['connection'];
    $hb = $dashboard['heartbeat'];
@endphp

@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        <div class="adm-header">
            <div class="adm-header-left">
                <h1>WRGROUP Super Apps</h1>
                <p>Integrasi satu arah: Kawulo Halal melaporkan kasus sertifikasi lunas ke holding company WRGROUP</p>
            </div>
            <a href="{{ route('superadmin.wrgroup.komisi.index') }}" class="adm-btn-secondary">
                <svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                Kelola Pembayaran Komisi
            </a>
        </div>

        @if ($halt)
            <div class="adm-alert adm-alert-danger" style="margin-bottom:16px;">
                <strong>Pengiriman dihentikan (HTTP {{ $halt['http'] }}).</strong> {{ $halt['reason'] }}
                Sejak {{ \Illuminate\Support\Carbon::parse($halt['at'])->format('d/m/Y H:i') }}.
                <form action="{{ route('superadmin.wrgroup.resume') }}" method="POST" style="display:inline-block;margin-left:8px;">
                    @csrf
                    <button type="submit" class="adm-btn danger">Lanjutkan Pengiriman</button>
                </form>
            </div>
        @endif

        <div class="adm-stats">
            <div class="adm-stat {{ $dashboard['enabled'] && $dashboard['configured'] ? 'is-accent' : '' }}">
                <div class="adm-stat-label">Status Integrasi</div>
                <div class="adm-stat-value" style="font-size:20px;">
                    @if (! $dashboard['enabled'])
                        Nonaktif
                    @elseif (! $dashboard['configured'])
                        Kredensial Belum Lengkap
                    @elseif ($halt)
                        Dihentikan
                    @else
                        Aktif
                    @endif
                </div>
                <div class="adm-stat-sub">WRGROUP_ENABLED = {{ $dashboard['enabled'] ? 'true' : 'false' }}</div>
            </div>
            <div class="adm-stat">
                <div class="adm-stat-label">Heartbeat Terakhir</div>
                <div class="adm-stat-value" style="font-size:20px;">{{ $hb ? \Illuminate\Support\Carbon::parse($hb['at'])->format('d/m/Y H:i') : '—' }}</div>
                <div class="adm-stat-sub">{{ $hb['message'] ?? 'Belum pernah dikirim' }}</div>
            </div>
            <div class="adm-stat">
                <div class="adm-stat-label">Antrian Event</div>
                <div class="adm-stat-value">{{ $dashboard['counts']['pending'] }}</div>
                <div class="adm-stat-sub">Menunggu · {{ $dashboard['counts']['rejected'] }} ditolak · {{ $dashboard['counts']['failed'] }} gagal</div>
            </div>
            <div class="adm-stat">
                <div class="adm-stat-label">Terakhir Terkirim</div>
                <div class="adm-stat-value" style="font-size:20px;">{{ $dashboard['last_sent_at'] ? \Illuminate\Support\Carbon::parse($dashboard['last_sent_at'])->format('d/m/Y H:i') : '—' }}</div>
                <div class="adm-stat-sub">{{ $dashboard['counts']['sent'] }} event terkirim total</div>
            </div>
        </div>

        <div class="adm-card" style="margin-bottom:16px;">
            <div class="adm-card-header">
                <div class="adm-card-title">
                    <svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    Koneksi
                </div>
            </div>
            <div style="padding:16px;">
                <div class="adm-info-list">
                    <div class="adm-info-row"><span class="adm-info-key">URL API</span><span class="adm-info-val">{{ $conn['api_url'] }}</span></div>
                    <div class="adm-info-row"><span class="adm-info-key">Business ID</span><span class="adm-info-val">{{ $conn['business_id'] }}</span></div>
                    <div class="adm-info-row"><span class="adm-info-key">API Key</span><span class="adm-info-val">{{ $conn['api_key'] }}</span></div>
                    <div class="adm-info-row"><span class="adm-info-key">API Secret</span><span class="adm-info-val">{{ $conn['has_secret'] ? 'Terpasang' : 'Belum diisi' }}</span></div>
                    <div class="adm-info-row"><span class="adm-info-key">Tanggal Operasional</span><span class="adm-info-val">{{ $conn['start_date'] }}</span></div>
                </div>
                <div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap;">
                    <form action="{{ route('superadmin.wrgroup.heartbeat') }}" method="POST" id="formHeartbeat">
                        @csrf
                        <button type="submit" class="adm-btn-secondary" id="btnHeartbeat">Kirim Heartbeat</button>
                    </form>
                    <form action="{{ route('superadmin.wrgroup.process') }}" method="POST" id="formProcess">
                        @csrf
                        <button type="submit" class="adm-btn-secondary" id="btnProcess">Proses Antrian</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="adm-card" style="margin-bottom:16px;">
            <div class="adm-card-header">
                <div class="adm-card-title">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Laporan Nihil Triwulan
                </div>
            </div>
            <form action="{{ route('superadmin.wrgroup.nihil') }}" method="POST" style="padding:16px;display:flex;gap:8px;align-items:end;flex-wrap:wrap;">
                @csrf
                <div class="adm-field" style="margin:0;">
                    <label class="adm-label">Triwulan</label>
                    <select name="periode" class="adm-field-select" required>
                        @foreach ($dashboard['nihil_periods'] as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="adm-btn-primary" id="btnNihil">Laporkan Nihil</button>
            </form>
        </div>

        <div class="adm-card">
            <div class="adm-card-header">
                <div class="adm-card-title">
                    <svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    Riwayat Event
                </div>
            </div>
            <div style="padding:12px 16px;display:flex;gap:8px;flex-wrap:wrap;">
                <select id="filterStatus" class="adm-field-select" style="max-width:180px;">
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu</option>
                    <option value="sent">Terkirim</option>
                    <option value="rejected">Ditolak</option>
                    <option value="failed">Gagal</option>
                </select>
                <select id="filterEndpoint" class="adm-field-select" style="max-width:180px;">
                    <option value="">Semua Jenis</option>
                    <option value="invoice">Invoice</option>
                    <option value="payment">Pembayaran</option>
                    <option value="nihil">Laporan Nihil</option>
                </select>
            </div>
            <div class="table-responsive">
                <table id="wrgroupEventTable" class="adm-table w-100">
                    <thead>
                        <tr>
                            <th style="width:44px">#</th>
                            <th>Waktu</th>
                            <th>Jenis</th>
                            <th>Transaksi</th>
                            <th class="tc">Status</th>
                            <th class="tc">HTTP</th>
                            <th class="tc" style="width:90px">Aksi</th>
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
        window.dataTableInstance = $('#wrgroupEventTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('superadmin.wrgroup.data') }}',
                type: 'GET',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: function (d) {
                    d.status = $('#filterStatus').val();
                    d.endpoint = $('#filterEndpoint').val();
                },
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'tc' },
                { data: 'waktu', name: 'event_time' },
                { data: 'endpoint_label', name: 'endpoint' },
                { data: 'transaction_id', name: 'transaction_id' },
                { data: 'status_badge', name: 'status', className: 'tc' },
                { data: 'http', name: 'last_http_status', className: 'tc' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'tc' },
            ],
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ – _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                paginate: { previous: '‹', next: '›' },
                zeroRecords: 'Tidak ada event ditemukan',
                emptyTable: 'Belum ada event WRGROUP',
                processing: '<div class="spinner-border text-primary" role="status"></div>',
            },
            pageLength: 15,
            order: [[1, 'desc']],
            responsive: true,
        });

        $('#filterStatus, #filterEndpoint').on('change', function () {
            window.dataTableInstance.ajax.reload();
        });
    });

    function kirimUlangEvent(url) {
        Swal.fire({
            title: 'Kirim Ulang Event?',
            text: 'Event akan dikirim ulang ke WRGROUP dengan event_id yang sama.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim Ulang',
            cancelButtonText: 'Batal',
            reverseButtons: true,
        }).then((result) => {
            if (!result.isConfirmed) return;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            form.innerHTML = `@csrf`;
            document.body.appendChild(form);
            form.submit();
        });
    }

    ['formHeartbeat', 'formProcess'].forEach(function (id) {
        const form = document.getElementById(id);
        if (!form) return;
        form.addEventListener('submit', function () {
            const btn = form.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Memproses...`;
        });
    });

    document.querySelector('form[action="{{ route('superadmin.wrgroup.nihil') }}"]')?.addEventListener('submit', function () {
        const btn = document.getElementById('btnNihil');
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Memproses...`;
    });
</script>
@endpush
