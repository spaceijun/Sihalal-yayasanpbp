@extends('layouts.app')
@section('template_title')
    Hasil Verifikasi KTP
@endsection

@section('content')
<div class="adm-page">

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Hasil Verifikasi KTP</h1>
            <p>Analisis biometrik selesai — menampilkan top 3 foto dari ZIP yang paling mirip</p>
        </div>
        <a href="{{ route($routePrefix . '.ktp-verifikasi.index') }}" class="adm-btn-secondary">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            Verifikasi Baru
        </a>
    </div>

    {{-- ── SUMMARY STATS ── --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;">

        <div class="adm-stat" style="border-top:4px solid var(--adm-blue);">
            <div class="adm-stat-label" style="color:var(--adm-blue);font-weight:700;">Identitas KTP</div>
            @if (!empty($ktpInfo))
                <div class="adm-stat-value" style="font-size:16px;color:var(--adm-text-dark);">{{ $ktpInfo['nama'] ?? '-' }}</div>
                <div class="adm-stat-sub" style="font-family:monospace;font-size:11px;">{{ $ktpInfo['nik'] ?? '-' }}</div>
            @else
                <div class="adm-stat-value" style="font-size:15px;color:var(--adm-text-muted);">Tidak terdeteksi</div>
            @endif
        </div>

        <div class="adm-stat" style="border-top:4px solid #7C3AED;">
            <div class="adm-stat-label" style="color:#7C3AED;font-weight:700;">Foto dalam ZIP</div>
            <div class="adm-stat-value" style="font-size:26px;color:#7C3AED;">{{ number_format($extractedCount ?? 0) }}</div>
            <div class="adm-stat-sub">foto diekstrak</div>
        </div>

        <div class="adm-stat" style="border-top:4px solid #0891B2;">
            <div class="adm-stat-label" style="color:#0891B2;font-weight:700;">Berhasil Dianalisis</div>
            <div class="adm-stat-value" style="font-size:26px;color:#0891B2;">{{ number_format($totalScanned) }}</div>
            <div class="adm-stat-sub">foto diproses AI</div>
        </div>

        <div class="adm-stat" style="border-top:4px solid {{ count($results) > 0 && $results[0]['confidence'] >= 75 ? 'var(--adm-green)' : 'var(--adm-rose)' }};">
            <div class="adm-stat-label" style="color:{{ count($results) > 0 && $results[0]['confidence'] >= 75 ? 'var(--adm-green)' : 'var(--adm-rose)' }};font-weight:700;">
                Kemiripan Tertinggi
            </div>
            @if (count($results) > 0)
                <div class="adm-stat-value" style="font-size:26px;color:{{ $results[0]['confidence'] >= 75 ? 'var(--adm-green)' : ($results[0]['confidence'] >= 50 ? '#D97706' : 'var(--adm-rose)') }};">
                    {{ $results[0]['confidence'] }}%
                </div>
                <div class="adm-stat-sub">{{ $results[0]['status'] }}</div>
            @else
                <div class="adm-stat-value" style="font-size:26px;color:var(--adm-text-muted);">0%</div>
                <div class="adm-stat-sub">Tidak ada hasil</div>
            @endif
        </div>
    </div>

    {{-- ── FOTO KTP + TOP 3 ── --}}
    <div style="display:grid;grid-template-columns:240px 1fr;gap:16px;align-items:start;">

        {{-- Foto KTP Referensi --}}
        <div class="adm-card">
            <div class="adm-card-header">
                <div class="adm-card-title">
                    <svg viewBox="0 0 24 24">
                        <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                    </svg>
                    Foto KTP
                </div>
            </div>
            <div style="padding:14px;text-align:center;">
                <img src="{{ $ktpUrl }}"
                     alt="Foto KTP"
                     style="width:100%;max-width:200px;border-radius:10px;object-fit:contain;box-shadow:0 4px 16px rgba(0,0,0,0.12);"
                     onerror="this.style.display='none'">
                @if (!empty($ktpInfo))
                    <div style="margin-top:10px;padding:8px 10px;background:var(--adm-blue-lt);border-radius:8px;text-align:left;">
                        <div style="font-size:10.5px;color:var(--adm-blue);font-weight:700;margin-bottom:3px;">DATA KTP</div>
                        <div style="font-size:12.5px;font-weight:600;color:var(--adm-text-dark);">{{ $ktpInfo['nama'] ?? '-' }}</div>
                        <div style="font-size:10.5px;font-family:monospace;color:var(--adm-text-muted);margin-top:2px;">{{ $ktpInfo['nik'] ?? '-' }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Top 3 Hasil --}}
        <div>
            @if (count($results) === 0)
                <div class="adm-card">
                    <div style="padding:60px 20px;text-align:center;">
                        <svg viewBox="0 0 24 24" style="width:48px;height:48px;color:var(--adm-text-faint);margin:0 auto 16px;display:block;">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <p style="color:var(--adm-text-muted);font-size:14px;margin:0;">
                            Tidak ada foto yang cocok dari {{ number_format($totalScanned) }} foto yang dianalisis.
                        </p>
                    </div>
                </div>
            @else
                <div style="display:flex;flex-direction:column;gap:14px;">
                    @foreach ($results as $i => $item)
                        @php
                            $conf  = $item['confidence'];
                            $confColor = $conf >= 75 ? 'var(--adm-green)' : ($conf >= 50 ? '#D97706' : 'var(--adm-rose)');
                            $confBg    = $conf >= 75 ? '#DCFCE7'          : ($conf >= 50 ? '#FEF3C7'  : '#FEE2E2');
                            $border    = $conf >= 75 ? 'var(--adm-green)' : ($conf >= 50 ? '#D97706'  : 'var(--adm-rose)');
                            $rankColors = ['#F59E0B','#94A3B8','#CD7C2F'];
                            $rankBg     = ['#FEF3C7','#F1F5F9','#FEF3C7'];
                        @endphp
                        <div class="adm-card kv-result-card"
                             style="border-left:4px solid {{ $border }};{{ $i === 0 ? 'box-shadow:0 4px 20px rgba(0,0,0,0.1);' : '' }}">
                            <div style="padding:16px 20px;">
                                <div style="display:flex;gap:14px;align-items:flex-start;">

                                    {{-- Rank --}}
                                    <div style="flex-shrink:0;width:40px;height:40px;border-radius:50%;background:{{ $rankBg[$i] ?? '#F1F5F9' }};display:flex;align-items:center;justify-content:center;box-shadow:0 2px 6px rgba(0,0,0,0.08);">
                                        <span style="font-size:16px;font-weight:800;color:{{ $rankColors[$i] ?? '#64748B' }};">#{{ $i + 1 }}</span>
                                    </div>

                                    {{-- Foto dari ZIP --}}
                                    <div style="flex-shrink:0;">
                                        @if (!empty($item['data']['foto_base64']))
                                            <img src="{{ $item['data']['foto_base64'] }}"
                                                 alt="{{ $item['data']['nama_file'] ?? 'Foto' }}"
                                                 style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:2px solid {{ $border }};box-shadow:0 2px 8px rgba(0,0,0,0.1);"
                                                 onerror="this.style.display='none'">
                                        @else
                                            <div style="width:80px;height:80px;border-radius:8px;background:var(--adm-bg-faint);display:flex;align-items:center;justify-content:center;">
                                                <svg viewBox="0 0 24 24" style="width:24px;height:24px;color:var(--adm-text-faint);">
                                                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Info --}}
                                    <div style="flex:1;min-width:0;">
                                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;flex-wrap:wrap;">
                                            <span style="font-size:14px;font-weight:700;color:var(--adm-text-dark);font-family:monospace;">
                                                {{ $item['data']['nama_file'] ?? '-' }}
                                            </span>
                                            <span style="font-size:10.5px;font-weight:700;background:{{ $confBg }};color:{{ $confColor }};border:1px solid {{ $confColor }}33;border-radius:20px;padding:2px 10px;">
                                                {{ $item['status'] }}
                                            </span>
                                        </div>

                                        {{-- Skor Bar --}}
                                        <div style="margin-bottom:8px;">
                                            <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                                                <span style="font-size:11px;color:var(--adm-text-muted);">Skor Kemiripan</span>
                                                <span style="font-size:14px;font-weight:800;color:{{ $confColor }};">{{ $conf }}%</span>
                                            </div>
                                            <div style="height:8px;background:var(--adm-bg-faint);border-radius:4px;overflow:hidden;">
                                                <div style="height:100%;width:{{ $conf }}%;background:linear-gradient(90deg,{{ $confColor }},{{ $confColor }}99);border-radius:4px;"></div>
                                            </div>
                                        </div>

                                        {{-- Justifikasi --}}
                                        <div style="background:var(--adm-bg-faint);border-radius:6px;padding:8px 12px;">
                                            <div style="font-size:10.5px;font-weight:700;color:var(--adm-text-muted);margin-bottom:2px;">ANALISIS AI:</div>
                                            <div style="font-size:12px;color:var(--adm-text-mid);line-height:1.5;">
                                                {{ $item['justifikasi'] ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>

<style>
.kv-result-card { transition: box-shadow .2s, transform .2s; }
.kv-result-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.12); transform: translateY(-2px); }
</style>
@endsection
