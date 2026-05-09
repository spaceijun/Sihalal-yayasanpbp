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
        <div class="em-header-actions">
            <button class="em-btn-add" id="btnAddEmail">
                <i class="bx bx-plus"></i> Tambah Email
            </button>
            <button class="em-refresh-btn" id="btnRefresh">
                <i class="bx bx-refresh" id="refreshIcon"></i> Refresh
            </button>
        </div>
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
                        style="width:0%" data-width="{{ $diskInfo['percent'] }}"></div>
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
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="emailTableBody">
                        @forelse($emails as $i => $em)
                            <tr data-email="{{ strtolower($em['email']) }}">
                                <td class="td-no">{{ $i + 1 }}</td>
                                <td class="td-email">
                                    <div class="em-email-info">
                                        <div class="em-email-addr">{{ $em['email'] }}</div>
                                        <div class="em-email-domain">{{ $em['domain'] }}</div>
                                    </div>
                                </td>
                                <td class="td-pass">
                                    <div class="em-pass-wrap">
                                        <span class="em-pass-text" data-visible="0">
                                            <span class="em-pass-masked">••••••••</span>
                                            <span class="em-pass-plain" style="display:none">
                                                <em class="text-muted" style="font-style:normal;font-size:11px">Gunakan
                                                    Reset Password</em>
                                            </span>
                                        </span>
                                        <button class="em-toggle-btn" title="Tampilkan/Sembunyikan">
                                            <i class="bx bx-show"></i>
                                        </button>
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
                                <td class="td-action">
                                    <button class="em-action-btn btn-reset" data-email="{{ $em['email'] }}"
                                        title="Reset Password">
                                        <i class="bx bx-key"></i> Reset
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="em-no-data">Belum ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ─── NOTE ─── --}}
    <div class="em-note">
        <i class="bx bx-info-circle"></i>
        <span>cPanel tidak mengekspos password email via API. Gunakan fitur <strong>Reset Password</strong> untuk mengatur
            ulang password akun email.</span>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Tambah Email --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="em-modal-overlay" id="modalAdd">
        <div class="em-modal">
            <div class="em-modal-header">
                <div class="em-modal-icon em-modal-icon-add"><i class="bx bx-envelope-plus"></i></div>
                <div>
                    <div class="em-modal-title">Tambah Akun Email</div>
                    <div class="em-modal-sub">Domain: {{ env('CPANEL_DOMAIN') }}</div>
                </div>
                <button class="em-modal-close" data-close="modalAdd">&times;</button>
            </div>
            <div class="em-modal-body">
                <div class="em-field">
                    <label>Username Email <span class="em-required">*</span></label>
                    <div class="em-input-suffix-wrap">
                        <input type="text" id="addEmailLogin" class="em-input" placeholder="contoh: nama.user"
                            autocomplete="off" spellcheck="false">
                        <span class="em-input-suffix">@{{ env('CPANEL_DOMAIN') }}</span>
                    </div>
                    <div class="em-field-hint">Hanya huruf, angka, titik, underscore, dan strip.</div>
                </div>
                <div class="em-field">
                    <label>Password <span class="em-required">*</span></label>
                    <div class="em-input-pw-wrap">
                        <input type="password" id="addEmailPassword" class="em-input" placeholder="Min. 8 karakter"
                            autocomplete="new-password">
                        <button type="button" class="em-pw-toggle" data-target="addEmailPassword">
                            <i class="bx bx-show"></i>
                        </button>
                    </div>
                    <div class="em-strength-bar-wrap">
                        <div class="em-strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="em-field-hint" id="strengthLabel">—</div>
                </div>
                <div class="em-field">
                    <label>Kuota Disk (MB)</label>
                    <input type="number" id="addEmailQuota" class="em-input" value="250" min="0"
                        max="51200">
                    <div class="em-field-hint">Isi 0 untuk Unlimited.</div>
                </div>
                <div class="em-modal-alert" id="addAlert" style="display:none"></div>
            </div>
            <div class="em-modal-footer">
                <button class="em-btn-cancel" data-close="modalAdd">Batal</button>
                <button class="em-btn-submit" id="btnSubmitAdd">
                    <span class="btn-label"><i class="bx bx-plus"></i> Buat Email</span>
                    <span class="btn-loading" style="display:none"><i class="bx bx-loader-alt bx-spin"></i>
                        Memproses...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Reset Password --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="em-modal-overlay" id="modalReset">
        <div class="em-modal">
            <div class="em-modal-header">
                <div class="em-modal-icon em-modal-icon-reset"><i class="bx bx-key"></i></div>
                <div>
                    <div class="em-modal-title">Reset Password</div>
                    <div class="em-modal-sub" id="resetEmailLabel">—</div>
                </div>
                <button class="em-modal-close" data-close="modalReset">&times;</button>
            </div>
            <div class="em-modal-body">
                <input type="hidden" id="resetEmailTarget">
                <div class="em-field">
                    <label>Password Baru <span class="em-required">*</span></label>
                    <div class="em-input-pw-wrap">
                        <input type="password" id="resetPassword" class="em-input" placeholder="Min. 8 karakter"
                            autocomplete="new-password">
                        <button type="button" class="em-pw-toggle" data-target="resetPassword">
                            <i class="bx bx-show"></i>
                        </button>
                    </div>
                    <div class="em-strength-bar-wrap">
                        <div class="em-strength-bar" id="resetStrengthBar"></div>
                    </div>
                    <div class="em-field-hint" id="resetStrengthLabel">—</div>
                </div>
                <div class="em-field">
                    <label>Konfirmasi Password <span class="em-required">*</span></label>
                    <div class="em-input-pw-wrap">
                        <input type="password" id="resetPasswordConfirm" class="em-input"
                            placeholder="Ulangi password baru" autocomplete="new-password">
                        <button type="button" class="em-pw-toggle" data-target="resetPasswordConfirm">
                            <i class="bx bx-show"></i>
                        </button>
                    </div>
                </div>
                <div class="em-modal-alert" id="resetAlert" style="display:none"></div>
            </div>
            <div class="em-modal-footer">
                <button class="em-btn-cancel" data-close="modalReset">Batal</button>
                <button class="em-btn-submit em-btn-reset-submit" id="btnSubmitReset">
                    <span class="btn-label"><i class="bx bx-key"></i> Simpan Password</span>
                    <span class="btn-loading" style="display:none"><i class="bx bx-loader-alt bx-spin"></i>
                        Memproses...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Toast --}}
    <div class="em-toast" id="emToast"></div>

    {{-- ─── STYLES ─── --}}
    <style>
        :root {
            --em-green: #059669;
            --em-red: #e11d48;
            --em-amber: #d97706;
            --em-blue: #3b7cf4;
            --em-indigo: #4f46e5;
        }

        /* Header */
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

        .em-header-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        /* Buttons */
        .em-btn-add {
            display: flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, var(--accent-indigo, #4f46e5), var(--accent-violet, #7c3aed));
            border: none;
            border-radius: 10px;
            padding: 9px 18px;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            cursor: pointer;
            transition: all .2s;
            box-shadow: 0 3px 12px rgba(99, 102, 241, .3);
        }

        .em-btn-add:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(99, 102, 241, .4);
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

        /* Alert */
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

        /* Disk banner */
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

        /* Stats row */
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

        /* Table wrap */
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

        /* Table */
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
            padding: 10px 16px;
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
            padding: 11px 16px;
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

        /* Email cell (no avatar) */
        .em-email-info {
            display: flex;
            flex-direction: column;
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

        /* Password cell */
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

        /* Mini bar */
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

        /* Colors */
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

        /* Status badge */
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

        /* Action button */
        .em-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid;
            cursor: pointer;
            transition: all .2s;
            white-space: nowrap;
        }

        .btn-reset {
            background: rgba(59, 124, 244, .08);
            color: var(--em-blue);
            border-color: rgba(59, 124, 244, .25);
        }

        .btn-reset:hover {
            background: var(--em-blue);
            color: #fff;
        }

        /* Empty & no-data */
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

        /* Note */
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

        /* Hidden row */
        tr.em-hidden {
            display: none;
        }

        /* ─── MODAL ─── */
        .em-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 20, 40, .45);
            backdrop-filter: blur(4px);
            z-index: 1050;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .em-modal-overlay.open {
            display: flex;
            animation: fadeIn .2s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0
            }

            to {
                opacity: 1
            }
        }

        .em-modal {
            background: var(--bg-card, #fff);
            border-radius: 20px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .18);
            overflow: hidden;
            animation: slideUp .25s ease;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0
            }

            to {
                transform: translateY(0);
                opacity: 1
            }
        }

        .em-modal-header {
            padding: 20px 24px 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            border-bottom: 1px solid var(--border, #e8ecf4);
        }

        .em-modal-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .em-modal-icon-add {
            background: linear-gradient(135deg, var(--accent-indigo, #4f46e5), var(--accent-violet, #7c3aed));
            color: #fff;
            box-shadow: 0 4px 12px rgba(99, 102, 241, .3);
        }

        .em-modal-icon-reset {
            background: linear-gradient(135deg, #0284c7, #0ea5e9);
            color: #fff;
            box-shadow: 0 4px 12px rgba(2, 132, 199, .3);
        }

        .em-modal-title {
            font-size: 16px;
            font-weight: 800;
            color: var(--text-primary, #1a2040);
        }

        .em-modal-sub {
            font-size: 12px;
            color: var(--text-muted, #9aa0b8);
            margin-top: 1px;
        }

        .em-modal-close {
            margin-left: auto;
            background: none;
            border: none;
            font-size: 22px;
            line-height: 1;
            color: var(--text-muted, #9aa0b8);
            cursor: pointer;
            transition: color .2s;
            flex-shrink: 0;
        }

        .em-modal-close:hover {
            color: var(--text-primary, #1a2040);
        }

        .em-modal-body {
            padding: 20px 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .em-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .em-field label {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-secondary, #5a6380);
            letter-spacing: .3px;
        }

        .em-required {
            color: var(--em-red);
        }

        .em-input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border, #e8ecf4);
            border-radius: 10px;
            font-size: 13px;
            font-family: inherit;
            color: var(--text-primary, #1a2040);
            background: var(--bg-card, #fff);
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .em-input:focus {
            border-color: var(--accent-indigo, #4f46e5);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, .1);
        }

        .em-field-hint {
            font-size: 11px;
            color: var(--text-muted, #9aa0b8);
        }

        .em-input-suffix-wrap {
            display: flex;
            align-items: center;
            border: 1px solid var(--border, #e8ecf4);
            border-radius: 10px;
            overflow: hidden;
            transition: border-color .2s, box-shadow .2s;
        }

        .em-input-suffix-wrap:focus-within {
            border-color: var(--accent-indigo, #4f46e5);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, .1);
        }

        .em-input-suffix-wrap .em-input {
            border: none;
            border-radius: 0;
            box-shadow: none;
            flex: 1;
        }

        .em-input-suffix-wrap .em-input:focus {
            box-shadow: none;
        }

        .em-input-suffix {
            padding: 0 12px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted, #9aa0b8);
            background: #f7f9fd;
            border-left: 1px solid var(--border, #e8ecf4);
            white-space: nowrap;
            height: 100%;
            display: flex;
            align-items: center;
        }

        .em-input-pw-wrap {
            position: relative;
        }

        .em-input-pw-wrap .em-input {
            padding-right: 40px;
        }

        .em-pw-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted, #9aa0b8);
            font-size: 16px;
            display: flex;
            align-items: center;
            transition: color .2s;
        }

        .em-pw-toggle:hover {
            color: var(--accent-indigo, #4f46e5);
        }

        /* Strength bar */
        .em-strength-bar-wrap {
            height: 4px;
            background: var(--border, #e8ecf4);
            border-radius: 4px;
            overflow: hidden;
            margin-top: 2px;
        }

        .em-strength-bar {
            height: 100%;
            border-radius: 4px;
            width: 0%;
            transition: width .4s, background .4s;
        }

        /* Modal alert */
        .em-modal-alert {
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
        }

        .em-modal-alert.is-error {
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #fecdd3;
        }

        .em-modal-alert.is-success {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        /* Modal footer */
        .em-modal-footer {
            padding: 16px 24px 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-top: 1px solid var(--border, #e8ecf4);
        }

        .em-btn-cancel {
            padding: 9px 18px;
            border-radius: 10px;
            border: 1px solid var(--border, #e8ecf4);
            background: transparent;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary, #5a6380);
            cursor: pointer;
            transition: all .2s;
        }

        .em-btn-cancel:hover {
            border-color: var(--border-bright, #d0d7eb);
            background: #f7f9fd;
        }

        .em-btn-submit {
            padding: 9px 20px;
            border-radius: 10px;
            border: none;
            background: linear-gradient(135deg, var(--accent-indigo, #4f46e5), var(--accent-violet, #7c3aed));
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            cursor: pointer;
            transition: all .2s;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 3px 10px rgba(99, 102, 241, .3);
        }

        .em-btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 16px rgba(99, 102, 241, .4);
        }

        .em-btn-submit:disabled {
            opacity: .65;
            cursor: not-allowed;
            transform: none;
        }

        .em-btn-reset-submit {
            background: linear-gradient(135deg, #0284c7, #0ea5e9);
            box-shadow: 0 3px 10px rgba(2, 132, 199, .3);
        }

        .em-btn-reset-submit:hover {
            box-shadow: 0 5px 16px rgba(2, 132, 199, .4);
        }

        /* Toast */
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
    </style>

    {{-- ─── SCRIPTS ─── --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

            // ── Animate bars ───────────────────────────────────────────────
            document.querySelectorAll('[data-width]').forEach(el => {
                setTimeout(() => el.style.width = Math.min(parseFloat(el.dataset.width) || 0, 100) + '%',
                    200);
            });

            // ── Show/Hide password (table) ─────────────────────────────────
            document.querySelectorAll('.em-toggle-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const wrap = this.closest('.em-pass-wrap');
                    const textEl = wrap.querySelector('.em-pass-text');
                    const masked = wrap.querySelector('.em-pass-masked');
                    const plain = wrap.querySelector('.em-pass-plain');
                    const visible = textEl.dataset.visible === '1';
                    const icon = this.querySelector('i');
                    masked.style.display = visible ? '' : 'none';
                    plain.style.display = visible ? 'none' : '';
                    icon.className = visible ? 'bx bx-show' : 'bx bx-hide';
                    textEl.dataset.visible = visible ? '0' : '1';
                });
            });

            // ── Search ─────────────────────────────────────────────────────
            document.getElementById('emailSearch')?.addEventListener('input', function() {
                const q = this.value.toLowerCase().trim();
                let count = 0;
                document.querySelectorAll('#emailTableBody tr[data-email]').forEach(row => {
                    const match = !q || row.dataset.email.includes(q);
                    row.classList.toggle('em-hidden', !match);
                    if (match) count++;
                });
                const badge = document.getElementById('badgeCount');
                if (badge) badge.textContent = count + ' akun';
            });

            // ── Refresh ────────────────────────────────────────────────────
            document.getElementById('btnRefresh')?.addEventListener('click', function() {
                const icon = document.getElementById('refreshIcon');
                icon.classList.add('spinning');
                this.disabled = true;
                fetch('{{ route('superadmin.cpanel.emails.api') }}')
                    .then(r => r.json())
                    .then(data => {
                        if (data.error) showToast('Error: ' + data.error, true);
                        else {
                            showToast('Data berhasil diperbarui!');
                            setTimeout(() => location.reload(), 800);
                        }
                    })
                    .catch(() => showToast('Gagal terhubung ke server', true))
                    .finally(() => {
                        icon.classList.remove('spinning');
                        this.disabled = false;
                    });
            });

            // ── Modal helpers ──────────────────────────────────────────────
            function openModal(id) {
                document.getElementById(id).classList.add('open');
            }

            function closeModal(id) {
                document.getElementById(id).classList.remove('open');
            }

            document.querySelectorAll('[data-close]').forEach(el => {
                el.addEventListener('click', () => closeModal(el.dataset.close));
            });
            document.querySelectorAll('.em-modal-overlay').forEach(overlay => {
                overlay.addEventListener('click', e => {
                    if (e.target === overlay) overlay.classList.remove('open');
                });
            });

            // ── Password toggle (modal) ────────────────────────────────────
            document.querySelectorAll('.em-pw-toggle').forEach(btn => {
                btn.addEventListener('click', function() {
                    const inp = document.getElementById(this.dataset.target);
                    const icon = this.querySelector('i');
                    if (inp.type === 'password') {
                        inp.type = 'text';
                        icon.className = 'bx bx-hide';
                    } else {
                        inp.type = 'password';
                        icon.className = 'bx bx-show';
                    }
                });
            });

            // ── Password strength ──────────────────────────────────────────
            function passwordStrength(pw) {
                let score = 0;
                if (pw.length >= 8) score++;
                if (pw.length >= 12) score++;
                if (/[A-Z]/.test(pw)) score++;
                if (/[0-9]/.test(pw)) score++;
                if (/[^A-Za-z0-9]/.test(pw)) score++;
                return score;
            }

            function updateStrength(pw, barEl, labelEl) {
                const s = passwordStrength(pw);
                const pct = pw.length === 0 ? 0 : Math.round((s / 5) * 100);
                const color = s <= 1 ? '#e11d48' : s <= 2 ? '#d97706' : s <= 3 ? '#f59e0b' : s <= 4 ? '#10b981' :
                    '#059669';
                const label = pw.length === 0 ? '—' : s <= 1 ? 'Sangat Lemah' : s <= 2 ? 'Lemah' : s <= 3 ?
                    'Cukup' : s <= 4 ? 'Kuat' : 'Sangat Kuat';
                barEl.style.width = pct + '%';
                barEl.style.background = color;
                labelEl.textContent = label;
                labelEl.style.color = pw.length === 0 ? '' : color;
            }

            document.getElementById('addEmailPassword')?.addEventListener('input', function() {
                updateStrength(this.value, document.getElementById('strengthBar'), document.getElementById(
                    'strengthLabel'));
            });
            document.getElementById('resetPassword')?.addEventListener('input', function() {
                updateStrength(this.value, document.getElementById('resetStrengthBar'), document
                    .getElementById('resetStrengthLabel'));
            });

            // ── Alert modal helper ─────────────────────────────────────────
            function showModalAlert(elId, msg, type = 'error') {
                const el = document.getElementById(elId);
                el.textContent = msg;
                el.className = 'em-modal-alert ' + (type === 'error' ? 'is-error' : 'is-success');
                el.style.display = 'block';
            }

            function hideModalAlert(elId) {
                const el = document.getElementById(elId);
                el.style.display = 'none';
                el.textContent = '';
            }

            function setLoading(btn, loading) {
                btn.querySelector('.btn-label').style.display = loading ? 'none' : '';
                btn.querySelector('.btn-loading').style.display = loading ? '' : 'none';
                btn.disabled = loading;
            }

            // ── TAMBAH EMAIL ───────────────────────────────────────────────
            document.getElementById('btnAddEmail')?.addEventListener('click', () => {
                document.getElementById('addEmailLogin').value = '';
                document.getElementById('addEmailPassword').value = '';
                document.getElementById('addEmailQuota').value = '250';
                document.getElementById('strengthBar').style.width = '0%';
                document.getElementById('strengthLabel').textContent = '—';
                hideModalAlert('addAlert');
                openModal('modalAdd');
                setTimeout(() => document.getElementById('addEmailLogin').focus(), 200);
            });

            document.getElementById('btnSubmitAdd')?.addEventListener('click', function() {
                const login = document.getElementById('addEmailLogin').value.trim();
                const password = document.getElementById('addEmailPassword').value;
                const quota = document.getElementById('addEmailQuota').value;

                hideModalAlert('addAlert');

                if (!login) return showModalAlert('addAlert', 'Username email tidak boleh kosong.');
                if (!/^[a-zA-Z0-9._\-]+$/.test(login)) return showModalAlert('addAlert',
                    'Username hanya boleh mengandung huruf, angka, titik, underscore, dan strip.');
                if (password.length < 8) return showModalAlert('addAlert', 'Password minimal 8 karakter.');

                setLoading(this, true);

                fetch('{{ route('superadmin.cpanel.emails.add') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF
                        },
                        body: JSON.stringify({
                            email: login,
                            password,
                            quota
                        }),
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            showModalAlert('addAlert', data.message, 'success');
                            setTimeout(() => {
                                closeModal('modalAdd');
                                location.reload();
                            }, 1200);
                        } else {
                            showModalAlert('addAlert', data.message || 'Gagal membuat email.');
                        }
                    })
                    .catch(() => showModalAlert('addAlert', 'Terjadi kesalahan jaringan.'))
                    .finally(() => setLoading(this, false));
            });

            // ── RESET PASSWORD ─────────────────────────────────────────────
            document.querySelectorAll('.btn-reset').forEach(btn => {
                btn.addEventListener('click', function() {
                    const email = this.dataset.email;
                    document.getElementById('resetEmailTarget').value = email;
                    document.getElementById('resetEmailLabel').textContent = email;
                    document.getElementById('resetPassword').value = '';
                    document.getElementById('resetPasswordConfirm').value = '';
                    document.getElementById('resetStrengthBar').style.width = '0%';
                    document.getElementById('resetStrengthLabel').textContent = '—';
                    hideModalAlert('resetAlert');
                    openModal('modalReset');
                    setTimeout(() => document.getElementById('resetPassword').focus(), 200);
                });
            });

            document.getElementById('btnSubmitReset')?.addEventListener('click', function() {
                const email = document.getElementById('resetEmailTarget').value;
                const password = document.getElementById('resetPassword').value;
                const confirm = document.getElementById('resetPasswordConfirm').value;

                hideModalAlert('resetAlert');

                if (password.length < 8) return showModalAlert('resetAlert',
                    'Password minimal 8 karakter.');
                if (password !== confirm) return showModalAlert('resetAlert',
                    'Konfirmasi password tidak cocok.');

                setLoading(this, true);

                fetch('{{ route('superadmin.cpanel.emails.reset-password') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF
                        },
                        body: JSON.stringify({
                            email,
                            password
                        }),
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            showModalAlert('resetAlert', data.message, 'success');
                            setTimeout(() => closeModal('modalReset'), 1500);
                        } else {
                            showModalAlert('resetAlert', data.message || 'Gagal reset password.');
                        }
                    })
                    .catch(() => showModalAlert('resetAlert', 'Terjadi kesalahan jaringan.'))
                    .finally(() => setLoading(this, false));
            });

            // ── Toast ──────────────────────────────────────────────────────
            function showToast(msg, isError = false) {
                const toast = document.getElementById('emToast');
                toast.innerHTML = `<i class="bx ${isError ? 'bx-error-circle' : 'bx-check-circle'}"></i> ${msg}`;
                toast.style.background = isError ? '#be123c' : '#1a2040';
                toast.classList.add('show');
                setTimeout(() => toast.classList.remove('show'), 2800);
            }
        });
    </script>
@endsection
