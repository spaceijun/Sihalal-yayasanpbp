@extends('layouts.app')
@section('template_title')
    Ranking Pendamping
@endsection

@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        {{-- ── Header ─────────────────────────────────────────────────────── --}}
        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Ranking Pendamping</h1>
                <p>Top 10 pendamping (enumerator) berdasarkan total pengajuan yang berhasil diproses</p>
            </div>
            <div class="period-pills">
                <a href="{{ route('superadmin.ranking-pendamping.index') }}"
                    class="period-pill {{ $periode === 'all' ? 'active' : '' }}">Semua Waktu</a>
                <a href="{{ route('superadmin.ranking-pendamping.index', ['periode' => 'bulan_ini']) }}"
                    class="period-pill {{ $periode === 'bulan_ini' ? 'active' : '' }}">Bulan Ini</a>
                <a href="{{ route('superadmin.ranking-pendamping.index', ['periode' => 'tahun_ini']) }}"
                    class="period-pill {{ $periode === 'tahun_ini' ? 'active' : '' }}">Tahun Ini</a>
            </div>
        </div>

        {{-- ── Stat cards (pakai .adm-stats + .adm-stat dari design system) ── --}}
        <div class="adm-stats">

            <div class="adm-stat">
                <div class="adm-stat-label">Total Pendamping</div>
                <div class="adm-stat-value">{{ number_format($stats['total_enumerator']) }}</div>
                <div class="adm-stat-sub">Semua enumerator aktif</div>
            </div>

            <div class="adm-stat">
                <div class="adm-stat-label">Total Pengajuan (Top 10)</div>
                <div class="adm-stat-value">{{ number_format($stats['total_pengajuan']) }}</div>
                <div class="adm-stat-sub">Gabungan 10 teratas</div>
            </div>

            <div class="adm-stat">
                <div class="adm-stat-label">Terbit SH (Top 10)</div>
                <div class="adm-stat-value is-success">{{ number_format($stats['total_terbit_sh']) }}</div>
                <div class="adm-stat-sub">Sertifikat halal terbit</div>
            </div>

            <div class="adm-stat">
                <div class="adm-stat-label">Sedang Diproses (Top 10)</div>
                <div class="adm-stat-value is-warn">{{ number_format($stats['total_progress']) }}</div>
                <div class="adm-stat-sub">Belum selesai</div>
            </div>

        </div>

        @if ($enumerators->isEmpty())
            <div class="adm-card">
                <div class="adm-empty">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="8" r="4" />
                        <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" />
                    </svg>
                    <p>Belum ada data pendamping untuk periode ini.</p>
                </div>
            </div>
        @else
            {{-- ── Podium Top 3 ─────────────────────────────────────────────── --}}
            <div class="adm-card" style="margin-bottom:22px;">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24" style="stroke:#F59E0B !important;">
                            <path
                                d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.86L12 17.77l-6.18 3.23L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                        Hall of Fame – Top 3
                    </div>
                    <span class="adm-count-badge">{{ now()->isoFormat('MMMM Y') }}</span>
                </div>

                @php
                    $medals = ['🥇', '🥈', '🥉'];
                    $podiumOrder = [1, 0, 2]; // kiri=rank2, tengah=rank1, kanan=rank3
                    $top3Colors = ['#1a5fc8', '#6d28d9', '#0f6e56']; // sesuai --adm-blue/indigo/green
                @endphp

                <div style="padding:4px 20px 28px;">
                    <div class="podium-wrap">
                        @foreach ($podiumOrder as $pIdx => $eIdx)
                            @if (isset($enumerators[$eIdx]))
                                @php
                                    $e = $enumerators[$eIdx];
                                    $r = $e->rank;
                                @endphp
                                <div class="podium-item pd-rank-{{ $r }}">

                                    {{-- Avatar --}}
                                    <div class="podium-avatar" style="color:{{ $top3Colors[$eIdx] }};">
                                        {{ $e->inisial }}
                                    </div>

                                    {{-- Medal --}}
                                    <div class="podium-medal">{{ $medals[$r - 1] }}</div>

                                    {{-- Nama (kata pertama saja) --}}
                                    <div class="podium-name" title="{{ $e->nama_lengkap }}">
                                        {{ Str::before($e->nama_lengkap, ' ') }}
                                    </div>

                                    {{-- Wilayah --}}
                                    <div class="podium-wilayah">
                                        {{ optional($e->koordinator)->wilayah ?? '-' }}
                                    </div>

                                    {{-- Total --}}
                                    <div class="podium-total">{{ number_format($e->total_pengajuan) }}</div>

                                    {{-- Podium bar --}}
                                    <div class="podium-bar">
                                        <span class="podium-rank-lbl">#{{ $r }}</span>
                                    </div>

                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- Mini stats row bawah podium --}}
                <div style="display:flex;gap:0;border-top:1px solid var(--adm-border);">
                    @foreach ($podiumOrder as $pIdx => $eIdx)
                        @if (isset($enumerators[$eIdx]))
                            @php $e = $enumerators[$eIdx]; @endphp
                            <div
                                style="flex:1;padding:12px 16px;text-align:center;{{ $pIdx < 2 ? 'border-right:1px solid var(--adm-border);' : '' }}">
                                <div
                                    style="font-size:11px;color:var(--adm-text-muted);font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">
                                    {{ $e->nama_lengkap }}
                                </div>
                                <div style="display:flex;justify-content:center;gap:10px;flex-wrap:wrap;">
                                    <span class="adm-badge adm-badge-info">{{ $e->total_pengajuan }} Total</span>
                                    <span class="adm-badge adm-badge-terbit">{{ $e->terbit_sh }} Terbit SH</span>
                                    @if ($e->progress > 0)
                                        <span class="adm-badge adm-badge-pending">{{ $e->progress }} Proses</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- ── Rank 4–10 ────────────────────────────────────────────────── --}}
            <div class="adm-card">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24">
                            <line x1="8" y1="6" x2="21" y2="6" />
                            <line x1="8" y1="12" x2="21" y2="12" />
                            <line x1="8" y1="18" x2="21" y2="18" />
                            <line x1="3" y1="6" x2="3.01" y2="6" />
                            <line x1="3" y1="12" x2="3.01" y2="12" />
                            <line x1="3" y1="18" x2="3.01" y2="18" />
                        </svg>
                        Peringkat 4 – 10
                    </div>
                    <span class="adm-count-badge">{{ $enumerators->slice(3)->count() }} pendamping</span>
                </div>

                @php
                    $rowColors = [
                        ['bg' => 'rgba(26,95,200,.1)', 'fg' => '#1a5fc8'],
                        ['bg' => 'rgba(109,40,217,.1)', 'fg' => '#6d28d9'],
                        ['bg' => 'rgba(15,110,86,.1)', 'fg' => '#0f6e56'],
                        ['bg' => 'rgba(220,38,38,.1)', 'fg' => '#dc2626'],
                        ['bg' => 'rgba(15,110,86,.1)', 'fg' => '#0f6e56'],
                        ['bg' => 'rgba(234,88,12,.1)', 'fg' => '#ea580c'],
                        ['bg' => 'rgba(3,105,161,.1)', 'fg' => '#0369a1'],
                    ];
                @endphp

                @foreach ($enumerators->slice(3)->values() as $idx => $e)
                    @php $c = $rowColors[$idx % count($rowColors)]; @endphp
                    <div class="rank-row">

                        {{-- Nomor --}}
                        <div class="rank-num-badge">#{{ $e->rank }}</div>

                        {{-- Avatar --}}
                        <div class="rank-avatar" style="background:{{ $c['bg'] }};color:{{ $c['fg'] }};">
                            {{ $e->inisial }}
                        </div>

                        {{-- Info --}}
                        <div class="rank-body">
                            <div class="rank-name">{{ $e->nama_lengkap }}</div>
                            <div class="rank-sub">
                                <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                                {{ optional($e->koordinator)->wilayah ?? '-' }}
                            </div>
                            <div class="rank-badges">
                                <span class="adm-badge adm-badge-info">{{ number_format($e->total_pengajuan) }}
                                    Total</span>
                                <span class="adm-badge adm-badge-terbit">{{ number_format($e->terbit_sh) }} Terbit
                                    SH</span>
                                @if ($e->progress > 0)
                                    <span class="adm-badge adm-badge-pending">{{ $e->progress }} Proses</span>
                                @endif
                            </div>
                        </div>

                        {{-- Progress bar --}}
                        <div class="rank-progress">
                            <span class="rank-pct">{{ $e->progress_ratio }}%</span>
                            <div class="rank-bar-wrap">
                                <div class="rank-bar-fill" style="width:{{ $e->progress_ratio }}%;"></div>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <style>
        /* ── Podium ─────────────────────────────────────────────────────────────── */
        .podium-wrap {
            display: flex;
            align-items: flex-end;
            justify-content: center;
            gap: 16px;
            padding: 24px 40px 0;
        }

        .podium-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            max-width: 180px;
        }

        .podium-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: "Sora", sans-serif;
            font-weight: 800;
            font-size: 19px;
            margin-bottom: 8px;
            border: 3px solid;
        }

        .podium-medal {
            font-size: 24px;
            line-height: 1;
            margin-bottom: 5px;
        }

        .podium-name {
            font-size: 12.5px;
            font-weight: 700;
            text-align: center;
            color: var(--adm-text-dark);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
        }

        .podium-wilayah {
            font-size: 10.5px;
            color: var(--adm-text-muted);
            text-align: center;
            margin-top: 1px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
        }

        .podium-total {
            font-size: 15px;
            font-weight: 800;
            margin: 6px 0 10px;
        }

        .podium-bar {
            width: 100%;
            border-radius: 10px 10px 0 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .podium-rank-lbl {
            font-size: 28px;
            font-weight: 900;
            opacity: .25;
            font-family: "Sora", sans-serif;
        }

        /* Gold */
        .pd-rank-1 .podium-avatar {
            border-color: #F59E0B;
            background: #FFFBEB;
            color: #92400E;
        }

        .pd-rank-1 .podium-total {
            color: #D97706;
        }

        .pd-rank-1 .podium-bar {
            background: #FEF3C7;
            border-top: 2px solid rgba(245, 158, 11, .4);
            height: 110px;
        }

        .pd-rank-1 .podium-rank-lbl {
            color: #D97706;
        }

        /* Silver */
        .pd-rank-2 .podium-avatar {
            border-color: #94A3B8;
            background: #F8FAFC;
            color: #475569;
        }

        .pd-rank-2 .podium-total {
            color: #64748B;
        }

        .pd-rank-2 .podium-bar {
            background: #F1F5F9;
            border-top: 2px solid rgba(148, 163, 184, .4);
            height: 80px;
        }

        .pd-rank-2 .podium-rank-lbl {
            color: #94A3B8;
        }

        /* Bronze */
        .pd-rank-3 .podium-avatar {
            border-color: #CD7C2F;
            background: #FEF0E7;
            color: #92400E;
        }

        .pd-rank-3 .podium-total {
            color: #CD7C2F;
        }

        .pd-rank-3 .podium-bar {
            background: #FEF0E7;
            border-top: 2px solid rgba(205, 124, 47, .35);
            height: 64px;
        }

        .pd-rank-3 .podium-rank-lbl {
            color: #CD7C2F;
        }

        /* ── Rank list (4-10) ───────────────────────────────────────────────────── */
        .rank-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            border-bottom: 1px solid var(--adm-border);
            transition: background .14s;
        }

        .rank-row:last-child {
            border-bottom: none;
        }

        .rank-row:hover {
            background: var(--adm-bg-light);
        }

        .rank-num-badge {
            min-width: 38px;
            height: 38px;
            border-radius: var(--adm-radius-sm);
            background: var(--adm-bg-light);
            border: 1px solid var(--adm-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: "Sora", sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: var(--adm-text-muted);
            flex-shrink: 0;
        }

        .rank-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: "Sora", sans-serif;
            font-size: 13px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .rank-body {
            flex: 1;
            min-width: 0;
        }

        .rank-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--adm-text-dark);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .rank-sub {
            font-size: 11.5px;
            color: var(--adm-text-muted);
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 2px;
        }

        .rank-sub svg {
            flex-shrink: 0;
        }

        .rank-badges {
            display: flex;
            gap: 6px;
            margin-top: 6px;
            flex-wrap: wrap;
        }

        .rank-progress {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 5px;
            min-width: 72px;
        }

        .rank-pct {
            font-size: 11.5px;
            font-weight: 700;
            color: var(--adm-blue);
        }

        .rank-bar-wrap {
            width: 72px;
            height: 6px;
            border-radius: 99px;
            background: var(--adm-border-mid);
            overflow: hidden;
        }

        .rank-bar-fill {
            height: 100%;
            border-radius: 99px;
            background: var(--adm-blue);
        }

        /* ── Period pills ───────────────────────────────────────────────────────── */
        .period-pills {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            align-items: center;
        }

        .period-pill {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12.5px;
            font-weight: 600;
            border: 1.5px solid var(--adm-border-mid);
            background: #fff;
            color: var(--adm-text-muted);
            cursor: pointer;
            text-decoration: none;
            transition: all .15s;
            font-family: "Plus Jakarta Sans", sans-serif;
        }

        .period-pill:hover {
            background: var(--adm-bg-light);
            color: var(--adm-text-dark);
            text-decoration: none;
        }

        .period-pill.active {
            background: var(--adm-blue);
            border-color: var(--adm-blue);
            color: #fff;
        }

        .period-pill.active:hover {
            color: #fff;
        }
    </style>

@endsection
