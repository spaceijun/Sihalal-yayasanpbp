@extends('layouts.app')
@section('template_title')
    Hasil Pencocokan Wajah
@endsection
@section('content')
    <div class="adm-page">

        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Hasil Pencocokan Wajah</h1>
                <p>Top 3 foto dengan kemiripan ≥80% dari seluruh enumerator</p>
            </div>
            <div style="display:flex;gap:8px;">
                <a href="{{ route('superadmin.face-match.index') }}" class="adm-btn-primary">
                    <svg viewBox="0 0 24 24" style="width:16px;height:16px;">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    Scan Ulang
                </a>
                <a href="{{ route('superadmin.dashboard') }}" class="adm-btn-secondary">
                    <svg viewBox="0 0 24 24">
                        <polyline points="15 18 9 12 15 6" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        {{-- Ringkasan --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px;">
            <div class="adm-stat-card">
                <div class="adm-stat-label">Total Foto Dianalisis</div>
                <div class="adm-stat-val">{{ number_format($totalDianalisis) }}</div>
            </div>
            <div class="adm-stat-card" style="border-top-color:{{ $totalDitemukan > 0 ? '#dc2626' : '#16a34a' }};">
                <div class="adm-stat-label">Foto Cocok (≥80%)</div>
                <div class="adm-stat-val" style="color:{{ $totalDitemukan > 0 ? '#dc2626' : '#16a34a' }};">
                    {{ $totalDitemukan }}
                    @if ($totalDitemukan > 3)
                        <span style="font-size:13px;font-weight:400;color:var(--adm-text-faint);">
                            (ditampilkan 3 terbaik)
                        </span>
                    @endif
                </div>
            </div>
            <div class="adm-stat-card" style="border-top-color:#d97706;">
                <div class="adm-stat-label">Foto Referensi</div>
                <div style="margin-top:6px;">
                    <img src="{{ $queryUrl }}" alt="Query"
                        style="width:48px;height:48px;border-radius:50%;object-fit:cover;
                               border:2px solid var(--adm-blue);">
                </div>
            </div>
        </div>

        @if (count($results) === 0)
            {{-- Tidak ada kecocokan --}}
            <div class="adm-card" style="padding:60px;text-align:center;">
                <svg viewBox="0 0 24 24" style="width:48px;height:48px;color:#16a34a;margin:0 auto 16px;display:block;">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                    <polyline points="22 4 12 14.01 9 11.01" />
                </svg>
                <div style="font-size:18px;font-weight:700;color:var(--adm-text-dark);margin-bottom:8px;">
                    Tidak Ditemukan Kecocokan
                </div>
                <div style="font-size:14px;color:var(--adm-text-faint);">
                    Dari {{ number_format($totalDianalisis) }} foto yang dianalisis, tidak ada yang memiliki kemiripan ≥80%.
                </div>
            </div>
        @else
            {{-- Top 3 hasil --}}
            <div class="adm-card" style="margin-bottom:16px;">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                        </svg>
                        {{ count($results) }} Foto Paling Mirip
                    </div>
                </div>
                <div style="padding:16px 20px;display:flex;flex-direction:column;gap:12px;">

                    @foreach ($results as $index => $item)
                        @php
                            $rank       = $index + 1;
                            $confidence = $item['confidence'];
                            $data       = $item['data'];
                            $rankColors = ['#f59e0b','#6b7280','#cd7c2f']; // gold, silver, bronze
                            $rankLabels = ['🥇 Terbaik','🥈 Ke-2','🥉 Ke-3'];
                            $confColor  = $confidence >= 90 ? '#dc2626' : ($confidence >= 80 ? '#d97706' : '#6b7280');
                        @endphp

                        <div class="fm-result-card">

                            {{-- Rank badge --}}
                            <div class="fm-rank-badge" style="background:{{ $rankColors[$index] ?? '#6b7280' }};">
                                {{ $rankLabels[$index] ?? '#' . $rank }}
                            </div>

                            {{-- Foto pendamping --}}
                            <div class="fm-result-photo">
                                <img src="{{ asset('storage/' . $data['foto_pendamping']) }}"
                                    alt="{{ $data['nama_pu'] }}"
                                    onerror="this.parentElement.innerHTML='<div class=\'fm-photo-err\'>?</div>'">
                            </div>

                            {{-- Info orang --}}
                            <div class="fm-result-info" style="flex:1;">
                                <div class="fm-result-name">{{ $data['nama_pu'] ?: '-' }}</div>
                                @if ($data['nik'])
                                    <div class="fm-result-meta">
                                        <svg viewBox="0 0 24 24" style="width:12px;height:12px;">
                                            <rect x="2" y="5" width="20" height="14" rx="2" />
                                            <line x1="2" y1="10" x2="22" y2="10" />
                                        </svg>
                                        NIK: {{ $data['nik'] }}
                                    </div>
                                @endif
                                @if ($data['telephone'])
                                    <div class="fm-result-meta">
                                        <svg viewBox="0 0 24 24" style="width:12px;height:12px;">
                                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13" />
                                        </svg>
                                        {{ $data['telephone'] }}
                                    </div>
                                @endif
                                <div class="fm-result-meta" style="color:var(--adm-blue);font-weight:500;margin-top:4px;">
                                    <svg viewBox="0 0 24 24" style="width:12px;height:12px;">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                        <circle cx="9" cy="7" r="4" />
                                    </svg>
                                    {{ $data['nama_enumerator'] ?? 'Enumerator #' . ($data['enumerator_id'] ?? '-') }}
                                </div>
                                <a href="{{ route('superadmin.data-lapangans.show', $data['id']) }}"
                                    class="adm-btn-secondary fm-detail-btn" style="margin-top:10px;">
                                    <svg viewBox="0 0 24 24" style="width:12px;height:12px;">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    Lihat Detail
                                </a>
                            </div>

                            {{-- Confidence + alasan --}}
                            <div class="fm-result-conf">
                                <div class="fm-conf-circle" style="background:{{ $confColor }};">
                                    {{ $confidence }}%
                                </div>
                                <div class="fm-conf-label">Kemiripan</div>
                                <div class="fm-result-reason">"{{ $item['reason'] }}"</div>
                            </div>

                        </div>
                    @endforeach

                </div>
            </div>

            @if ($totalDitemukan > 3)
                <div style="text-align:center;padding:12px;font-size:13px;color:var(--adm-text-faint);">
                    <svg viewBox="0 0 24 24" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="16" x2="12" y2="12" />
                        <line x1="12" y1="8" x2="12.01" y2="8" />
                    </svg>
                    Ada {{ $totalDitemukan - 3 }} foto lain dengan kemiripan ≥80% yang tidak ditampilkan.
                    Hanya 3 terbaik yang disajikan.
                </div>
            @endif
        @endif

        {{-- Disclaimer --}}
        <div style="margin-top:8px;padding:12px 16px;background:#fffbeb;border:1px solid #fcd34d;
            border-radius:10px;display:flex;gap:10px;align-items:flex-start;">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;flex-shrink:0;color:#92400e;margin-top:1px;">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                <line x1="12" y1="9" x2="12" y2="13" />
                <line x1="12" y1="17" x2="12.01" y2="17" />
            </svg>
            <span style="font-size:13px;color:#92400e;line-height:1.6;">
                <strong>Disclaimer:</strong> Hasil pencocokan wajah dihasilkan oleh AI (Claude) dan bersifat estimasi.
                Lakukan verifikasi manual sebelum mengambil keputusan resmi.
            </span>
        </div>

    </div>

    <style>
        .adm-stat-card {
            background: var(--adm-card-bg);
            border: 1px solid var(--adm-border);
            border-top: 3px solid var(--adm-blue);
            border-radius: 10px;
            padding: 16px 18px;
        }

        .adm-stat-label {
            font-size: 12px;
            color: var(--adm-text-faint);
            margin-bottom: 6px;
        }

        .adm-stat-val {
            font-size: 26px;
            font-weight: 700;
            color: var(--adm-text-dark);
        }

        .fm-result-card {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            background: var(--adm-bg-faint);
            border: 1px solid var(--adm-border);
            border-radius: 12px;
            padding: 16px;
            position: relative;
        }

        .fm-rank-badge {
            position: absolute;
            top: -1px;
            left: -1px;
            font-size: 11px;
            font-weight: 700;
            color: #fff;
            padding: 3px 10px;
            border-radius: 12px 0 8px 0;
            letter-spacing: .3px;
        }

        .fm-result-photo {
            width: 80px;
            height: 96px;
            flex-shrink: 0;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--adm-border);
            background: var(--adm-card-bg);
            margin-top: 18px; /* offset rank badge */
        }

        .fm-result-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .fm-photo-err {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: var(--adm-text-faint);
        }

        .fm-result-info {
            margin-top: 18px;
        }

        .fm-result-name {
            font-size: 15px;
            font-weight: 700;
            color: var(--adm-text-dark);
            margin-bottom: 5px;
        }

        .fm-result-meta {
            font-size: 12px;
            color: var(--adm-text-faint);
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 2px;
        }

        .fm-detail-btn {
            padding: 4px 10px !important;
            font-size: 12px !important;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .fm-result-conf {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            min-width: 90px;
            margin-top: 18px;
        }

        .fm-conf-circle {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .fm-conf-label {
            font-size: 11px;
            color: var(--adm-text-faint);
            text-align: center;
        }

        .fm-result-reason {
            font-size: 11px;
            color: var(--adm-text-faint);
            text-align: center;
            font-style: italic;
            line-height: 1.4;
            max-width: 110px;
        }
    </style>
@endsection