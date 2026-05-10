@extends('layouts.app')
@section('template_title')
    Hasil Deteksi Wajah Duplikat
@endsection
@section('content')
    <div class="adm-page">

        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Hasil Deteksi Wajah Duplikat</h1>
                <p>Pasangan foto dengan kemiripan ≥80% dikelompokkan per enumerator</p>
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
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px;">
            <div class="adm-stat-card">
                <div class="adm-stat-label">Total Kombinasi Dianalisis</div>
                <div class="adm-stat-val">{{ number_format($totalDianalisis) }}</div>
            </div>
            <div class="adm-stat-card" style="border-top-color:#dc2626;">
                <div class="adm-stat-label">Pasangan Duplikat (≥80%)</div>
                <div class="adm-stat-val" style="color:#dc2626;">{{ $totalDuplikat }}</div>
            </div>
            <div class="adm-stat-card" style="border-top-color:#d97706;">
                <div class="adm-stat-label">Enumerator Terindikasi</div>
                <div class="adm-stat-val" style="color:#d97706;">{{ $totalEnumerator }}</div>
            </div>
        </div>

        @if (count($grouped) === 0)
            {{-- Tidak ada duplikat --}}
            <div class="adm-card" style="padding:60px;text-align:center;">
                <svg viewBox="0 0 24 24" style="width:48px;height:48px;color:#16a34a;margin:0 auto 16px;display:block;">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                    <polyline points="22 4 12 14.01 9 11.01" />
                </svg>
                <div style="font-size:18px;font-weight:700;color:var(--adm-text-dark);margin-bottom:8px;">
                    Tidak Ditemukan Duplikat
                </div>
                <div style="font-size:14px;color:var(--adm-text-faint);">
                    Semua {{ number_format($totalDianalisis) }} kombinasi foto memiliki kemiripan di bawah 80%.
                </div>
            </div>
        @else
            {{-- Per enumerator --}}
            @foreach ($grouped as $enumeratorId => $group)
                <div class="adm-card" style="margin-bottom:16px;">
                    <div class="adm-card-header" style="background:#fef2f2;border-bottom:1px solid #fecaca;">
                        <div class="adm-card-title" style="color:#991b1b;">
                            <svg viewBox="0 0 24 24" style="color:#dc2626;">
                                <path
                                    d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                                <line x1="12" y1="9" x2="12" y2="13" />
                                <line x1="12" y1="17" x2="12.01" y2="17" />
                            </svg>
                            {{ $group['nama'] }}
                            <span
                                style="margin-left:8px;font-size:12px;font-weight:400;
                            background:#fecaca;color:#991b1b;padding:2px 10px;border-radius:99px;">
                                {{ count($group['pairs']) }} pasangan duplikat
                            </span>
                        </div>
                    </div>

                    <div style="padding:16px 20px;display:flex;flex-direction:column;gap:12px;">
                        @foreach ($group['pairs'] as $pair)
                            <div class="fm-pair-card">

                                {{-- Foto A --}}
                                <div class="fm-pair-person">
                                    <div class="fm-pair-photo">
                                        <img src="{{ asset('storage/' . $pair['data_a']['foto_pendamping']) }}"
                                            alt="{{ $pair['data_a']['nama_pu'] }}"
                                            onerror="this.parentElement.innerHTML='<div class=\'fm-photo-err\'>?</div>'">
                                    </div>
                                    <div class="fm-pair-info">
                                        <div class="fm-pair-name">{{ $pair['data_a']['nama_pu'] ?: '-' }}</div>
                                        @if ($pair['data_a']['nik'])
                                            <div class="fm-pair-meta">NIK: {{ $pair['data_a']['nik'] }}</div>
                                        @endif
                                        @if ($pair['data_a']['telephone'])
                                            <div class="fm-pair-meta">{{ $pair['data_a']['telephone'] }}</div>
                                        @endif
                                        <a href="{{ route('superadmin.data-lapangans.show', $pair['data_a']['id']) }}"
                                            class="adm-btn-secondary fm-detail-btn">
                                            <svg viewBox="0 0 24 24" style="width:12px;height:12px;">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>
                                            Detail
                                        </a>
                                    </div>
                                </div>

                                {{-- Confidence badge di tengah --}}
                                <div class="fm-pair-middle">
                                    <div class="fm-conf-circle"
                                        style="background:{{ $pair['confidence'] >= 90 ? '#dc2626' : ($pair['confidence'] >= 80 ? '#d97706' : '#6b7280') }};">
                                        {{ $pair['confidence'] }}%
                                    </div>
                                    <div class="fm-pair-reason">"{{ $pair['reason'] }}"</div>
                                </div>

                                {{-- Foto B --}}
                                <div class="fm-pair-person">
                                    <div class="fm-pair-photo">
                                        <img src="{{ asset('storage/' . $pair['data_b']['foto_pendamping']) }}"
                                            alt="{{ $pair['data_b']['nama_pu'] }}"
                                            onerror="this.parentElement.innerHTML='<div class=\'fm-photo-err\'>?</div>'">
                                    </div>
                                    <div class="fm-pair-info">
                                        <div class="fm-pair-name">{{ $pair['data_b']['nama_pu'] ?: '-' }}</div>
                                        @if ($pair['data_b']['nik'])
                                            <div class="fm-pair-meta">NIK: {{ $pair['data_b']['nik'] }}</div>
                                        @endif
                                        @if ($pair['data_b']['telephone'])
                                            <div class="fm-pair-meta">{{ $pair['data_b']['telephone'] }}</div>
                                        @endif
                                        <a href="{{ route('superadmin.data-lapangans.show', $pair['data_b']['id']) }}"
                                            class="adm-btn-secondary fm-detail-btn">
                                            <svg viewBox="0 0 24 24" style="width:12px;height:12px;">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>
                                            Detail
                                        </a>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif

        {{-- Disclaimer --}}
        <div
            style="margin-top:8px;padding:12px 16px;background:#fffbeb;border:1px solid #fcd34d;
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

        .fm-pair-card {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 16px;
            align-items: center;
            background: var(--adm-bg-faint);
            border: 1px solid var(--adm-border);
            border-radius: 12px;
            padding: 14px 16px;
        }

        .fm-pair-person {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .fm-pair-photo {
            width: 72px;
            height: 84px;
            flex-shrink: 0;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--adm-border);
            background: var(--adm-card-bg);
        }

        .fm-pair-photo img {
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
            font-size: 24px;
            color: var(--adm-text-faint);
        }

        .fm-pair-info {
            flex: 1;
            min-width: 0;
        }

        .fm-pair-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--adm-text-dark);
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .fm-pair-meta {
            font-size: 12px;
            color: var(--adm-text-faint);
            margin-bottom: 2px;
        }

        .fm-detail-btn {
            margin-top: 8px;
            padding: 4px 10px !important;
            font-size: 12px !important;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .fm-pair-middle {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            min-width: 90px;
        }

        .fm-conf-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .fm-pair-reason {
            font-size: 11px;
            color: var(--adm-text-faint);
            text-align: center;
            font-style: italic;
            line-height: 1.4;
            max-width: 110px;
        }
    </style>
@endsection
