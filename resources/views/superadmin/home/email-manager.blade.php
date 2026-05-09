@extends('layouts.app')
@section('template_title')
    Email Manager — cPanel
@endsection

@section('content')

    {{-- ─── HEADER ─── --}}
    <div class="em-header">
        <div class="em-header-left">
            <div class="em-header-icon"><i class="ri-mail-settings-line"></i></div>
            <div>
                <div class="em-header-title">Email Manager</div>
                <div class="em-header-sub">Kelola akun email cPanel — {{ env('CPANEL_DOMAIN') }}</div>
            </div>
        </div>
        <button class="em-refresh-btn" id="btnRefresh">
            <i class="bx bx-refresh" id="refreshIcon"></i>
            Refresh
        </button>
    </div>

    {{-- ─── ERROR STATE ─── --}}
    @if ($error)
        <div class="em-alert em-alert-error">
            <i class="bx bx-error-circle"></i>
            <div>
                <strong>Gagal terhubung ke cPanel</strong>
                <p>{{ $error }}</p>
            </div>
        </div>
    @endif

    {{-- ─── DISK USAGE BANNER ─── --}}
    @if (!empty($diskInfo))
        <div class="em-disk-banner">
            <div class="em-disk-info">
                <div class="em-disk-label"><i class="bx bx-hdd"></i> Disk Usage Domain</div>
                <div class="em-disk-values">
                    <span class="em-disk-used">{{ $diskInfo['used'] }}</span>
                    <span class="em-disk-sep">/</span>
                    <span class="em-disk-limit">{{ $diskInfo['limit'] }}</span>
                </div>
            </div>
            <div class="em-disk-bar-wrap">
                <div class="em-disk-bar-track">
                    <div class="em-disk-bar-fill {{ $diskInfo['percent'] > 80 ? 'fill-danger' : ($diskInfo['percent'] > 60 ? 'fill-warn' : 'fill-ok') }}"
                        style="width: 0%" data-width="{{ $diskInfo['percent'] }}"></div>
                </div>
                <span
                    class="em-disk-pct {{ $diskInfo['percent'] > 80 ? 'pct-danger' : ($diskInfo['percent'] > 60 ? 'pct-warn' : 'pct-ok') }}">
                    {{ $diskInfo['percent'] }}%
                </span>
            </div>
        </div>
    @endif

    {{-- ─── STATS ROW ─── --}}
    <div class="em-stats-row">
        <div class="em-stat-pill">
            <i class="bx bx-envelope"></i>
            <span id="statTotal">{{ count($emails) }}</span> Total Email
        </div>
        <div class="em-stat-pill em-stat-active">
            <i class="bx bx-check-circle"></i>
            <span id="statActive">{{ collect($emails)->where('suspended', false)->count() }}</span> Aktif
        </div>
        <div class="em-stat-pill em-stat-suspended">
            <i class="bx bx-pause-circle"></i>
            <span id="statSuspended">{{ collect($emails)->where('suspended', true)->count() }}</span> Suspended
        </div>

        {{-- Search --}}
        <div class="em-search-wrap ms-auto">
            <i class="bx bx-search em-search-icon"></i>
            <input type="text" id="emailSearch" class="em-search" placeholder="Cari email...">
        </div>
    </div>

    {{-- ─── EMAIL TABLE ─── --}}
    <div class="em-table-wrap">
        <div class="em-table-header">
            <h5><i class="bx bx-list-ul me-2"></i>Daftar Akun Email</h5>
            <span class="em-badge" id="badgeCount">{{ count($emails) }} akun</span>
        </div>

        @if (count($emails) === 0 && !$error)
            <div class="em-empty">
                <i class="bx bx-envelope-open"></i>
                <p>Belum ada akun email terdaftar di domain ini.</p>
            </div>
        @else
            <div class="table-responsive">
                <table id="emailTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Alamat Email</th>
                            <th>Password</th>
                            <th>Disk Digunakan</th>
                            <th>Kuota</th>
                            <th>Penggunaan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="emailTableBody">
                        @forelse($emails as $i => $em)
                            <tr data-email="{{ strtolower($em['email']) }}">
                                <td class="td-no">{{ $i + 1 }}</td>
                                <td class="td-email">
                                    <div class="em-email-wrap">
                                        <div class="em-avatar">{{ strtoupper(substr($em['login'], 0, 1)) }}</div>
                                        <div>
                                            <div class="em-email-addr">{{ $em['email'] }}</div>
                                            <div class="em-email-domain">{{ $em['domain'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="td-pass">
                                    <div class="em-pass-wrap">
                                        <span class="em-pass-text" data-visible="0">
                                            @if ($em['password'])
                                                <span class="em-pass-masked">••••••••</span>
                                                <span class="em-pass-plain"
                                                    style="display:none">{{ $em['password'] }}</span>
                                            @else
                                                <span class="em-pass-masked">••••••••</span>
                                                <span class="em-pass-plain" style="display:none"><em
                                                        class="text-muted">Tidak tersedia via API</em></span>
                                            @endif
                                        </span>
                                        <button class="em-toggle-btn" title="Tampilkan/Sembunyikan password">
                                            <i class="bx bx-show"></i>
                                        </button>
                                        @if ($em['password'])
                                            <button class="em-copy-btn" data-val="{{ $em['password'] }}"
                                                title="Salin password">
                                                <i class="bx bx-copy"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                                <td class="td-disk">{{ $em['disk_used'] }}</td>
                                <td class="td-quota">{{ $em['disk_quota'] }}</td>
                                <td class="td-usage">
                                    <div class="em-usage-wrap">
                                        <div class="em-mini-bar-track">
                                            <div class="em-mini-bar-fill {{ $em['disk_pct'] > 80 ? 'fill-danger' : ($em['disk_pct'] > 60 ? 'fill-warn' : 'fill-ok') }}"
                                                style="width:0%" data-width="{{ $em['disk_pct'] }}"></div>
                                        </div>
                                        <span
                                            class="em-mini-pct {{ $em['disk_pct'] > 80 ? 'pct-danger' : ($em['disk_pct'] > 60 ? 'pct-warn' : '') }}">
                                            {{ $em['disk_pct'] }}%
                                        </span>
                                    </div>
                                </td>
                                <td class="td-status">
                                    @if ($em['suspended'])
                                        <span class="em-status em-status-suspended">
                                            <i class="bx bx-pause-circle"></i> Suspended
                                        </span>
                                    @else
                                        <span class="em-status em-status-active">
                                            <i class="bx bx-check-circle"></i> Aktif
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="em-no-data">Belum ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ─── NOTE cPanel password ─── --}}
    <div class="em-note">
        <i class="bx bx-info-circle"></i>
        <span>cPanel tidak mengekspos password email via API (keamanan). Untuk menampilkan password, simpan secara manual di
            database atau file konfigurasi terpisah.</span>
    </div>

    {{-- ─── STYLES ─── --}}
    <style>
        /* ── Root (inherit dari dashboard) ── */
        :root {
            --em-green: #059669;
            --em-red: #e11d48;
            --em-amber: #d97706;
            --em-blue: #3b7cf4;
            --em-indigo: #4f46e5;
        }

        /* ── Header ── */
        .em-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .em-header-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .em-header-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--accent-indigo, #4f46e5), var(--accent-violet, #7c3aed));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #fff;
            box-shadow: 0 4px 16px rgba(99, 102, 241, .3);
            flex-shrink: 0;
        }

        .em-header-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-primary, #1a2040);
            letter-spacing: -.4px;
        }

        .em-header-sub {
            font-size: 12px;
            color: var(--text-muted, #9aa0b8);
            margin-top: 2px;
        }

        .em-refresh-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            background: var(--bg-card, #fff);
            border: 1px solid var(--border, #e8ecf4);
            border-radius: 10px;
            padding: 9px 16px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary, #5a6380);
            cursor: pointer;
            transition: all .2s;
            box-shadow: 0 1px 4px rgba(80, 100, 160, .06);
        }

        .em-refresh-btn:hover {
            border-color: var(--accent-indigo, #4f46e5);
            color: var(--accent-indigo, #4f46e5);
            transform: translateY(-1px);
        }

        .em-refresh-btn .bx-refresh.spinning {
            animation: spin .7s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ── Alert ── */
        .em-alert {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .em-alert i {
            font-size: 20px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .em-alert strong {
            display: block;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .em-alert p {
            margin: 0;
            opacity: .8;
        }

        .em-alert-error {
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #fecdd3;
        }

        /* ── Disk Banner ── */
        .em-disk-banner {
            background: var(--bg-card, #fff);
            border: 1px solid var(--border, #e8ecf4);
            border-radius: 14px;
            padding: 18px 22px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-card, 0 2px 12px rgba(80, 100, 160, .08));
            display: flex;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
        }

        .em-disk-info {
            flex-shrink: 0;
        }

        .em-disk-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--text-muted, #9aa0b8);
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .em-disk-values {
            display: flex;
            align-items: baseline;
            gap: 4px;
        }

        .em-disk-used {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-primary, #1a2040);
            letter-spacing: -.5px;
        }

        .em-disk-sep {
            font-size: 16px;
            color: var(--text-muted, #9aa0b8);
        }

        .em-disk-limit {
            font-size: 14px;
            color: var(--text-secondary, #5a6380);
        }

        .em-disk-bar-wrap {
            flex: 1;
            min-width: 200px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .em-disk-bar-track {
            flex: 1;
            height: 8px;
            border-radius: 8px;
            background: var(--border, #e8ecf4);
            overflow: hidden;
        }

        .em-disk-bar-fill {
            height: 100%;
            border-radius: 8px;
            transition: width 1.2s cubic-bezier(.22, 1, .36, 1);
        }

        .em-disk-pct {
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
        }

        /* ── Stats Row ── */
        .em-stats-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 16px;
        }

        .em-stat-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--bg-card, #fff);
            border: 1px solid var(--border, #e8ecf4);
            border-radius: 30px;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary, #5a6380);
            box-shadow: 0 1px 4px rgba(80, 100, 160, .05);
        }

        .em-stat-pill i {
            font-size: 14px;
        }

        .em-stat-pill span {
            font-size: 14px;
            font-weight: 800;
            color: var(--text-primary, #1a2040);
        }

        .em-stat-active {
            border-color: rgba(5, 150, 105, .2);
        }

        .em-stat-active i,
        .em-stat-active span {
            color: var(--em-green);
        }

        .em-stat-suspended {
            border-color: rgba(217, 119, 6, .2);
        }

        .em-stat-suspended i,
        .em-stat-suspended span {
            color: var(--em-amber);
        }

        /* ── Search ── */
        .em-search-wrap {
            position: relative;
        }

        .em-search-icon {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted, #9aa0b8);
            font-size: 15px;
            pointer-events: none;
        }

        .em-search {
            background: var(--bg-card, #fff);
            border: 1px solid var(--border, #e8ecf4);
            border-radius: 30px;
            padding: 7px 14px 7px 32px;
            font-size: 12px;
            font-family: inherit;
            color: var(--text-primary, #1a2040);
            outline: none;
            transition: border-color .2s;
            width: 220px;
        }

        .em-search:focus {
            border-color: var(--accent-indigo, #4f46e5);
        }

        /* ── Table Wrap ── */
        .em-table-wrap {
            background: var(--bg-card, #fff);
            border: 1px solid var(--border, #e8ecf4);
            border-radius: 16px;
            box-shadow: var(--shadow-card, 0 2px 12px rgba(80, 100, 160, .08));
            overflow: hidden;
            margin-bottom: 16px;
        }

        .em-table-header {
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border, #e8ecf4);
        }

        .em-table-header h5 {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary, #1a2040);
            margin: 0;
        }

        .em-badge {
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 20px;
            font-weight: 600;
            background: rgba(79, 70, 229, .08);
            color: var(--accent-indigo, #4f46e5);
            border: 1px solid rgba(79, 70, 229, .18);
        }

        /* ── Table ── */
        #emailTable {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        #emailTable thead th {
            background: #f7f9fd;
            color: var(--text-muted, #9aa0b8);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 10px 20px;
            border-bottom: 1px solid var(--border, #e8ecf4);
            white-space: nowrap;
        }

        #emailTable tbody tr {
            border-bottom: 1px solid #f1f4fb;
            transition: background .15s;
        }

        #emailTable tbody tr:hover {
            background: #f7f9fd;
        }

        #emailTable tbody tr:last-child {
            border-bottom: none;
        }

        #emailTable tbody td {
            padding: 12px 20px;
            vertical-align: middle;
            color: var(--text-secondary, #5a6380);
        }

        .td-no {
            color: var(--text-muted, #9aa0b8) !important;
            font-size: 12px;
            width: 40px;
        }

        .td-disk,
        .td-quota {
            font-weight: 600;
            color: var(--text-primary, #1a2040) !important;
        }

        /* ── Email cell ── */
        .em-email-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .em-avatar {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            background: linear-gradient(135deg, var(--accent-indigo, #4f46e5), var(--accent-violet, #7c3aed));
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .em-email-addr {
            font-weight: 600;
            color: var(--text-primary, #1a2040);
            font-size: 13px;
        }

        .em-email-domain {
            font-size: 11px;
            color: var(--text-muted, #9aa0b8);
        }

        /* ── Password cell ── */
        .em-pass-wrap {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .em-pass-text {
            font-family: 'Courier New', monospace;
            font-size: 13px;
            letter-spacing: 1px;
            color: var(--text-secondary, #5a6380);
        }

        .em-toggle-btn,
        .em-copy-btn {
            width: 28px;
            height: 28px;
            border-radius: 7px;
            border: 1px solid var(--border, #e8ecf4);
            background: transparent;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted, #9aa0b8);
            font-size: 14px;
            transition: all .2s;
            flex-shrink: 0;
        }

        .em-toggle-btn:hover {
            border-color: var(--accent-indigo, #4f46e5);
            color: var(--accent-indigo, #4f46e5);
        }

        .em-copy-btn:hover {
            border-color: var(--em-green);
            color: var(--em-green);
        }

        /* ── Mini bar ── */
        .em-usage-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 110px;
        }

        .em-mini-bar-track {
            flex: 1;
            height: 5px;
            border-radius: 5px;
            background: var(--border, #e8ecf4);
            overflow: hidden;
        }

        .em-mini-bar-fill {
            height: 100%;
            border-radius: 5px;
            transition: width 1.2s cubic-bezier(.22, 1, .36, 1);
        }

        .em-mini-pct {
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
            color: var(--text-secondary, #5a6380);
        }

        /* ── Colors ── */
        .fill-ok {
            background: var(--em-green);
        }

        .fill-warn {
            background: var(--em-amber);
        }

        .fill-danger {
            background: var(--em-red);
        }

        .pct-ok {
            color: var(--em-green);
        }

        .pct-warn {
            color: var(--em-amber);
        }

        .pct-danger {
            color: var(--em-red);
        }

        /* ── Status badge ── */
        .em-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
        }

        .em-status-active {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .em-status-suspended {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        /* ── Empty & No-data ── */
        .em-empty {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted, #9aa0b8);
        }

        .em-empty i {
            font-size: 40px;
            display: block;
            margin-bottom: 12px;
        }

        .em-empty p {
            font-size: 13px;
        }

        .em-no-data {
            text-align: center;
            color: var(--text-muted, #9aa0b8);
            padding: 40px;
            font-size: 13px;
        }

        /* ── Note ── */
        .em-note {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: 11.5px;
            color: var(--text-muted, #9aa0b8);
            background: #f7f9fd;
            border: 1px solid var(--border, #e8ecf4);
            border-radius: 10px;
            padding: 12px 16px;
        }

        .em-note i {
            font-size: 15px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* ── Toast copy ── */
        .em-toast {
            position: fixed;
            bottom: 28px;
            right: 28px;
            background: #1a2040;
            color: #fff;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .18);
            z-index: 9999;
            opacity: 0;
            transform: translateY(12px);
            transition: opacity .25s, transform .25s;
            pointer-events: none;
        }

        .em-toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* ── Hidden row ── */
        tr.em-hidden {
            display: none;
        }
    </style>

    {{-- ─── SCRIPTS ─── --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ── Animate disk bars ──────────────────────────────────────────
            document.querySelectorAll('[data-width]').forEach(el => {
                const w = parseFloat(el.getAttribute('data-width')) || 0;
                setTimeout(() => el.style.width = Math.min(w, 100) + '%', 200);
            });

            // ── Show/Hide password ─────────────────────────────────────────
            document.querySelectorAll('.em-toggle-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const wrap = this.closest('.em-pass-wrap');
                    const textEl = wrap.querySelector('.em-pass-text');
                    const masked = wrap.querySelector('.em-pass-masked');
                    const plain = wrap.querySelector('.em-pass-plain');
                    const visible = textEl.getAttribute('data-visible') === '1';
                    const icon = this.querySelector('i');

                    if (visible) {
                        masked.style.display = '';
                        plain.style.display = 'none';
                        icon.className = 'bx bx-show';
                        textEl.setAttribute('data-visible', '0');
                    } else {
                        masked.style.display = 'none';
                        plain.style.display = '';
                        icon.className = 'bx bx-hide';
                        textEl.setAttribute('data-visible', '1');
                    }
                });
            });

            // ── Copy password ──────────────────────────────────────────────
            document.querySelectorAll('.em-copy-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const val = this.getAttribute('data-val') || '';
                    navigator.clipboard.writeText(val).then(() => showToast('Password disalin!'));
                });
            });

            // ── Search filter ──────────────────────────────────────────────
            const searchInput = document.getElementById('emailSearch');
            searchInput && searchInput.addEventListener('input', function() {
                const q = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('#emailTableBody tr[data-email]');
                let count = 0;
                rows.forEach(row => {
                    const match = !q || row.getAttribute('data-email').includes(q);
                    row.classList.toggle('em-hidden', !match);
                    if (match) count++;
                });
                const badge = document.getElementById('badgeCount');
                if (badge) badge.textContent = count + ' akun';
            });

            // ── Refresh ────────────────────────────────────────────────────
            const btnRefresh = document.getElementById('btnRefresh');
            const refreshIcon = document.getElementById('refreshIcon');
            btnRefresh && btnRefresh.addEventListener('click', function() {
                refreshIcon.classList.add('spinning');
                this.disabled = true;
                fetch('{{ route('superadmin.cpanel.emails.api') }}')
                    .then(r => r.json())
                    .then(data => {
                        if (data.error) {
                            showToast('Error: ' + data.error, true);
                        } else {
                            showToast('Data berhasil diperbarui!');
                            setTimeout(() => location.reload(), 800);
                        }
                    })
                    .catch(() => showToast('Gagal terhubung ke server', true))
                    .finally(() => {
                        refreshIcon.classList.remove('spinning');
                        btnRefresh.disabled = false;
                    });
            });

            // ── Toast ──────────────────────────────────────────────────────
            function showToast(msg, isError = false) {
                let toast = document.getElementById('emToast');
                if (!toast) {
                    toast = document.createElement('div');
                    toast.id = 'emToast';
                    toast.className = 'em-toast';
                    document.body.appendChild(toast);
                }
                toast.innerHTML = `<i class="bx ${isError ? 'bx-error-circle' : 'bx-check-circle'}"></i> ${msg}`;
                toast.style.background = isError ? '#be123c' : '#1a2040';
                toast.classList.add('show');
                setTimeout(() => toast.classList.remove('show'), 2800);
            }
        });
    </script>
@endsection
