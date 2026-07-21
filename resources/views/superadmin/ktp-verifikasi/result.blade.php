@extends('layouts.app')
@section('template_title')
    Hasil Verifikasi KTP
@endsection

@section('content')
<div class="adm-page">

    {{-- Header --}}
    <div class="adm-header" style="margin-bottom:20px;">
        <div class="adm-header-left">
            <h1>Hasil Verifikasi KTP</h1>
            <p>Analisis selesai — setiap KTP mendapatkan 3 foto paling mirip dari ZIP</p>
        </div>
        <div style="display:flex;gap:10px;">
            {{-- Download ZIP --}}
            <a href="{{ route($routePrefix . '.ktp-verifikasi.download', $session->session_key) }}"
               class="adm-btn-primary" style="gap:8px;">
                <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2;">
                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Download Hasil (ZIP)
            </a>
            <a href="{{ route($routePrefix . '.ktp-verifikasi.index') }}" class="adm-btn-secondary">
                <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2;">
                    <polyline points="1 4 1 10 7 10"/>
                    <path d="M3.51 15a9 9 0 102.13-9.36L1 10"/>
                </svg>
                Verifikasi Baru
            </a>
        </div>
    </div>

    {{-- ── STATS ── --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;">

        <div class="adm-stat" style="border-top:4px solid var(--adm-blue);">
            <div class="adm-stat-label" style="color:var(--adm-blue);font-weight:700;">Jumlah KTP</div>
            <div class="adm-stat-value" style="font-size:28px;color:var(--adm-blue);">{{ $ktpCount }}</div>
            <div class="adm-stat-sub">KTP diproses</div>
        </div>

        <div class="adm-stat" style="border-top:4px solid #7C3AED;">
            <div class="adm-stat-label" style="color:#7C3AED;font-weight:700;">Total Proses AI</div>
            <div class="adm-stat-value" style="font-size:28px;color:#7C3AED;">{{ number_format($totalScanned) }}</div>
            <div class="adm-stat-sub">dari {{ number_format($totalPhotos) }} jobs</div>
        </div>

        <div class="adm-stat" style="border-top:4px solid #0891B2;">
            <div class="adm-stat-label" style="color:#0891B2;font-weight:700;">Foto per KTP</div>
            @php $zipCount = $ktpCount > 0 ? round($totalPhotos / $ktpCount) : 0; @endphp
            <div class="adm-stat-value" style="font-size:28px;color:#0891B2;">{{ $zipCount }}</div>
            <div class="adm-stat-sub">foto dalam ZIP</div>
        </div>

        @php
            $allConf = collect($ktpResults)->flatMap(fn($k) => collect($k['top_candidates'] ?? []))->pluck('confidence');
            $maxConf = $allConf->max() ?? 0;
            $topColor = $maxConf >= 75 ? '#059669' : ($maxConf >= 50 ? '#D97706' : '#DC2626');
        @endphp
        <div class="adm-stat" style="border-top:4px solid {{ $topColor }};">
            <div class="adm-stat-label" style="color:{{ $topColor }};font-weight:700;">Skor Tertinggi</div>
            <div class="adm-stat-value" style="font-size:28px;color:{{ $topColor }};">{{ $maxConf }}%</div>
            <div class="adm-stat-sub">dari semua KTP</div>
        </div>
    </div>

    {{-- ── TAB PER KTP ── --}}
    @if (count($ktpResults) > 1)
    <div style="display:flex;gap:0;border-bottom:2px solid var(--adm-border-light);margin-bottom:20px;overflow-x:auto;">
        @foreach ($ktpResults as $i => $ktp)
            @php $tabConf = collect($ktp['top_candidates'] ?? [])->max('confidence') ?? 0; @endphp
            <button type="button"
                    class="kv-tab{{ $i === 0 ? ' active' : '' }}"
                    onclick="switchTab({{ $i }})"
                    id="tab_{{ $i }}">
                <svg viewBox="0 0 24 24" style="width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2;"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                KTP {{ $i + 1 }}
                @if ($ktp['ktp_nama'])
                    <span style="opacity:.7;font-size:10px;">· {{ Str::limit($ktp['ktp_nama'], 12) }}</span>
                @endif
                <span style="font-size:10px;background:{{ $tabConf >= 75 ? '#DCFCE7' : ($tabConf >= 50 ? '#FEF3C7' : '#F1F5F9') }};
                             color:{{ $tabConf >= 75 ? '#059669' : ($tabConf >= 50 ? '#D97706' : '#64748B') }};
                             border-radius:10px;padding:1px 7px;font-weight:700;">
                    {{ $tabConf }}%
                </span>
            </button>
        @endforeach
    </div>
    @endif

    {{-- ── PANEL PER KTP ── --}}
    @foreach ($ktpResults as $i => $ktp)
        @php
            $topCandidates = $ktp['top_candidates'] ?? [];
            $ktpNama       = $ktp['ktp_nama'] ?? null;
            $ktpNik        = $ktp['ktp_nik']  ?? null;
            $ktpUrl        = $ktp['ktp_url']  ?? null;
        @endphp

        <div id="panel_{{ $i }}"
             class="kv-panel{{ $i > 0 ? ' hidden' : '' }}">

            <div style="display:grid;grid-template-columns:200px 1fr;gap:16px;align-items:start;">

                {{-- KTP Referensi --}}
                <div class="adm-card">
                    <div class="adm-card-header">
                        <div class="adm-card-title" style="font-size:12.5px;">
                            <svg viewBox="0 0 24 24" style="width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2;"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                            KTP {{ $i + 1 }} — Referensi
                        </div>
                    </div>
                    <div style="padding:12px;text-align:center;">
                        @php
                            $ktpDiskPath = $ktpUrl ? public_path(str_replace('/storage/', '/storage/app/public/', $ktpUrl)) : null;
                        @endphp
                        @if ($ktpUrl && $ktpDiskPath && file_exists($ktpDiskPath))
                            <img src="{{ asset($ktpUrl) }}" alt="KTP {{ $i + 1 }}"
                                 style="width:100%;border-radius:8px;object-fit:contain;box-shadow:0 4px 14px rgba(0,0,0,0.1);max-height:160px;">
                        @else
                            <div style="width:100%;height:110px;background:linear-gradient(135deg,#EFF6FF,#DBEAFE);border-radius:8px;
                                        display:flex;flex-direction:column;align-items:center;justify-content:center;border:2px dashed #BFDBFE;">
                                <svg viewBox="0 0 24 24" style="width:28px;height:28px;stroke:#93C5FD;fill:none;stroke-width:1.5;">
                                    <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                                </svg>
                                <div style="font-size:10px;color:#93C5FD;margin-top:4px;">File tidak tersedia</div>
                            </div>
                        @endif

                        @if ($ktpNama)
                            <div style="margin-top:8px;padding:8px;background:var(--adm-blue-lt);border-radius:7px;text-align:left;">
                                <div style="font-size:10px;color:var(--adm-blue);font-weight:700;letter-spacing:.4px;">NAMA</div>
                                <div style="font-size:12px;font-weight:700;color:var(--adm-text-dark);">{{ $ktpNama }}</div>
                                @if ($ktpNik)
                                    <div style="font-size:10px;font-family:monospace;color:var(--adm-text-muted);margin-top:2px;">{{ $ktpNik }}</div>
                                @endif
                            </div>
                        @endif

                        <div style="margin-top:8px;font-size:10.5px;color:var(--adm-text-faint);">
                            {{ $ktp['ktp_file'] ?? '-' }}
                        </div>
                    </div>
                </div>

                {{-- Top 3 Kandidat --}}
                <div>
                    @if (empty($topCandidates))
                        <div class="adm-card">
                            <div style="padding:48px 20px;text-align:center;">
                                <svg viewBox="0 0 24 24" style="width:40px;height:40px;color:var(--adm-text-faint);margin:0 auto 12px;display:block;stroke:currentColor;fill:none;stroke-width:1.5;">
                                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                                <p style="color:var(--adm-text-muted);font-size:13px;margin:0;">Tidak ada hasil untuk KTP ini.</p>
                            </div>
                        </div>
                    @else
                        <div style="display:flex;flex-direction:column;gap:12px;">
                            @foreach ($topCandidates as $rank => $candidate)
                                @php
                                    $conf      = (int)($candidate['confidence'] ?? 0);
                                    $status    = $candidate['status']      ?? 'Tidak Cocok';
                                    $justif    = $candidate['justifikasi'] ?? '-';
                                    $namaFile  = $candidate['nama_file']   ?? '-';
                                    $fotoB64   = $candidate['foto_base64'] ?? null;

                                    $confColor = $conf >= 75 ? '#059669' : ($conf >= 50 ? '#D97706' : '#DC2626');
                                    $confBg    = $conf >= 75 ? '#DCFCE7' : ($conf >= 50 ? '#FEF3C7' : '#FEE2E2');
                                    $border    = $confColor;

                                    $rankColors = ['#F59E0B','#94A3B8','#CD7C2F'];
                                    $rankBgs    = ['#FEF3C7','#F1F5F9','#FEF3C7'];
                                @endphp

                                <div class="adm-card kv-result-card"
                                     style="border-left:4px solid {{ $border }};{{ $rank === 0 ? 'box-shadow:0 4px 20px rgba(0,0,0,0.08);' : '' }}">
                                    <div style="padding:14px 18px;">
                                        <div style="display:flex;gap:12px;align-items:flex-start;">

                                            {{-- Rank --}}
                                            <div style="width:38px;height:38px;border-radius:50%;flex-shrink:0;
                                                        background:{{ $rankBgs[$rank] ?? '#F1F5F9' }};
                                                        display:flex;align-items:center;justify-content:center;
                                                        box-shadow:0 2px 6px rgba(0,0,0,.08);">
                                                <span style="font-size:13px;font-weight:800;color:{{ $rankColors[$rank] ?? '#64748B' }};">
                                                    #{{ $rank + 1 }}
                                                </span>
                                            </div>

                                            {{-- Foto Kandidat --}}
                                            <div style="flex-shrink:0;">
                                                @if ($fotoB64)
                                                    <img src="{{ $fotoB64 }}" alt="{{ $namaFile }}"
                                                         style="width:85px;height:85px;object-fit:cover;border-radius:8px;
                                                                border:2px solid {{ $border }};box-shadow:0 3px 8px rgba(0,0,0,.1);">
                                                @else
                                                    <div style="width:85px;height:85px;border-radius:8px;background:var(--adm-bg-faint);
                                                                border:2px dashed var(--adm-border-mid);
                                                                display:flex;align-items:center;justify-content:center;">
                                                        <svg viewBox="0 0 24 24" style="width:22px;height:22px;stroke:var(--adm-text-faint);fill:none;stroke-width:1.5;">
                                                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Detail --}}
                                            <div style="flex:1;min-width:0;">
                                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:7px;flex-wrap:wrap;">
                                                    <span style="font-size:12.5px;font-weight:700;font-family:monospace;color:var(--adm-text-dark);word-break:break-all;">
                                                        {{ $namaFile }}
                                                    </span>
                                                    <span style="font-size:10.5px;font-weight:700;white-space:nowrap;
                                                                 background:{{ $confBg }};color:{{ $confColor }};
                                                                 border:1px solid {{ $confColor }}33;border-radius:20px;padding:2px 9px;">
                                                        {{ $status }}
                                                    </span>
                                                </div>

                                                {{-- Skor Bar --}}
                                                <div style="margin-bottom:8px;">
                                                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                                                        <span style="font-size:10.5px;color:var(--adm-text-muted);">Skor Kemiripan</span>
                                                        <span style="font-size:17px;font-weight:800;color:{{ $confColor }};">{{ $conf }}%</span>
                                                    </div>
                                                    <div style="height:8px;background:var(--adm-bg-faint);border-radius:4px;overflow:hidden;">
                                                        <div style="height:100%;width:{{ $conf }}%;
                                                                    background:linear-gradient(90deg,{{ $confColor }},{{ $confColor }}AA);
                                                                    border-radius:4px;"></div>
                                                    </div>
                                                </div>

                                                {{-- Justifikasi --}}
                                                <div style="background:var(--adm-bg-faint);border-radius:7px;padding:8px 11px;border-left:3px solid {{ $border }}33;">
                                                    <div style="font-size:9.5px;font-weight:700;color:var(--adm-text-muted);letter-spacing:.5px;margin-bottom:3px;">ANALISIS AI:</div>
                                                    <div style="font-size:12px;color:var(--adm-text-mid);line-height:1.6;">{{ $justif }}</div>
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

            @if ($i < count($ktpResults) - 1)
                <div style="height:1px;background:var(--adm-border-light);margin:20px 0;"></div>
            @endif
        </div>
    @endforeach

    {{-- Download footer --}}
    <div style="margin-top:24px;padding:16px 20px;background:linear-gradient(135deg,#F5F3FF,#EFF6FF);
                border:1px solid #DDD6FE;border-radius:12px;display:flex;align-items:center;justify-content:between;gap:16px;flex-wrap:wrap;">
        <div>
            <div style="font-size:13px;font-weight:700;color:#4C1D95;margin-bottom:3px;">📦 Download Semua Hasil</div>
            <div style="font-size:12px;color:#6D28D9;line-height:1.5;">
                ZIP berisi folder per KTP, masing-masing berisi foto KTP referensi + 3 foto terbaik (dengan nama file: Rank1_95pct_namafile.jpg)
            </div>
        </div>
        <a href="{{ route($routePrefix . '.ktp-verifikasi.download', $session->session_key) }}"
           class="adm-btn-primary" style="gap:8px;white-space:nowrap;flex-shrink:0;">
            <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2;">
                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Download Hasil (ZIP)
        </a>
    </div>

</div>

<style>
.kv-result-card { transition: transform .2s, box-shadow .2s; }
.kv-result-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }

.kv-tab {
    display: flex; align-items: center; gap: 6px;
    padding: 9px 18px; font-size: 13px; font-weight: 600;
    color: var(--adm-text-muted); background: transparent;
    border: none; border-bottom: 3px solid transparent;
    cursor: pointer; white-space: nowrap; transition: color .2s, border-color .2s;
}
.kv-tab:hover { color: var(--adm-blue); }
.kv-tab.active { color: var(--adm-blue); border-bottom-color: var(--adm-blue); }
.kv-panel.hidden { display: none; }
</style>

<script>
function switchTab(idx) {
    document.querySelectorAll('.kv-tab').forEach((t,i) => t.classList.toggle('active', i === idx));
    document.querySelectorAll('.kv-panel').forEach((p,i) => p.classList.toggle('hidden', i !== idx));
}
</script>
@endsection
