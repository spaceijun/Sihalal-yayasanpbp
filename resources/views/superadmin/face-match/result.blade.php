@extends('layouts.app')
@section('template_title')
    Hasil Pencocokan Wajah
@endsection
@section('content')
    <div class="adm-page">

        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Hasil Pencocokan Wajah</h1>
                <p>Ditemukan {{ count($results) }} data yang dianalisis oleh AI</p>
            </div>
            <div style="display:flex;gap:8px;">
                <a href="{{ route('superadmin.face-match.index') }}" class="adm-btn-primary">
                    <svg viewBox="0 0 24 24" style="width:16px;height:16px;">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    Cari Lagi
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
        @php
            $matchCount = collect($results)->where('match', true)->count();
            $noMatchCount = collect($results)->where('match', false)->count();
            $topMatch = collect($results)->where('match', true)->first();
        @endphp

        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px;">
            <div class="adm-stat-card">
                <div class="adm-stat-label">Total Dianalisis</div>
                <div class="adm-stat-val">{{ count($results) }}</div>
            </div>
            <div class="adm-stat-card" style="border-top:3px solid #16a34a;">
                <div class="adm-stat-label">Kemungkinan Cocok</div>
                <div class="adm-stat-val" style="color:#16a34a;">{{ $matchCount }}</div>
            </div>
            <div class="adm-stat-card" style="border-top:3px solid #dc2626;">
                <div class="adm-stat-label">Tidak Cocok</div>
                <div class="adm-stat-val" style="color:#dc2626;">{{ $noMatchCount }}</div>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:280px 1fr;gap:16px;align-items:start;">

            {{-- Foto Query --}}
            <div class="adm-card" style="position:sticky;top:16px;">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24">
                            <rect x="3" y="3" width="18" height="18" rx="2" />
                            <circle cx="8.5" cy="8.5" r="1.5" />
                            <polyline points="21 15 16 10 5 21" />
                        </svg>
                        Foto yang Dicari
                    </div>
                </div>
                <div style="padding:16px 20px;text-align:center;">
                    @if ($queryUrl)
                        <img src="{{ $queryUrl }}" alt="Foto Query"
                            style="width:100%;max-height:220px;object-fit:contain;border-radius:10px;border:1px solid var(--adm-border);">
                    @else
                        <div
                            style="width:100%;height:180px;background:var(--adm-bg-faint);border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--adm-text-faint);font-size:13px;">
                            Preview tidak tersedia
                        </div>
                    @endif

                    @if ($topMatch)
                        <div
                            style="margin-top:14px;padding:10px 14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;text-align:left;">
                            <div style="font-size:11px;color:#166534;font-weight:600;margin-bottom:4px;">KEMUNGKINAN TERBAIK
                            </div>
                            <div style="font-size:14px;font-weight:600;color:#15803d;">{{ $topMatch['data']->nama_pu }}
                            </div>
                            <div style="font-size:12px;color:#166534;">NIK: {{ $topMatch['data']->nik }}</div>
                            <div style="font-size:12px;color:#166534;">{{ $topMatch['confidence'] }}% kemiripan</div>
                        </div>
                    @else
                        <div
                            style="margin-top:14px;padding:10px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;">
                            <div style="font-size:13px;color:#991b1b;text-align:center;">Tidak ada kecocokan ditemukan</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Hasil --}}
            <div>
                {{-- Filter tab --}}
                <div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;">
                    <button class="fm-filter-btn active" onclick="filterResults('all', this)">
                        Semua ({{ count($results) }})
                    </button>
                    <button class="fm-filter-btn" onclick="filterResults('match', this)">
                        <svg viewBox="0 0 24 24" style="width:14px;height:14px;">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                        Cocok ({{ $matchCount }})
                    </button>
                    <button class="fm-filter-btn" onclick="filterResults('nomatch', this)">
                        <svg viewBox="0 0 24 24" style="width:14px;height:14px;">
                            <line x1="18" y1="6" x2="6" y2="18" />
                            <line x1="6" y1="6" x2="18" y2="18" />
                        </svg>
                        Tidak Cocok ({{ $noMatchCount }})
                    </button>
                </div>

                {{-- Result cards --}}
                <div style="display:flex;flex-direction:column;gap:10px;" id="resultContainer">
                    @forelse($results as $result)
                        <div class="fm-result-card {{ $result['match'] ? 'fm-match' : 'fm-nomatch' }}"
                            data-match="{{ $result['match'] ? 'match' : 'nomatch' }}">

                            {{-- Foto pendamping --}}
                            <div class="fm-result-photo">
                                <img src="{{ $result['foto_url'] }}" alt="{{ $result['data']->nama_pu }}"
                                    onerror="this.parentElement.innerHTML='<div style=\'width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:22px;color:var(--adm-text-faint);\'>?</div>'">
                            </div>

                            {{-- Info --}}
                            <div class="fm-result-info">
                                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:4px;">
                                    <span class="fm-result-name">{{ $result['data']->nama_pu }}</span>
                                    @if ($result['match'])
                                        <span class="adm-badge fm-badge-match">
                                            <svg viewBox="0 0 24 24" style="width:11px;height:11px;">
                                                <polyline points="20 6 9 17 4 12" />
                                            </svg>
                                            Kemungkinan Cocok
                                        </span>
                                    @else
                                        <span class="adm-badge fm-badge-nomatch">
                                            <svg viewBox="0 0 24 24" style="width:11px;height:11px;">
                                                <line x1="18" y1="6" x2="6" y2="18" />
                                                <line x1="6" y1="6" x2="18" y2="18" />
                                            </svg>
                                            Tidak Cocok
                                        </span>
                                    @endif
                                </div>

                                <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:10px;">
                                    @if ($result['data']->nik)
                                        <span style="font-size:12px;color:var(--adm-text-faint);">
                                            <svg viewBox="0 0 24 24" style="width:12px;height:12px;vertical-align:-1px;">
                                                <rect x="2" y="5" width="20" height="14" rx="2" />
                                                <line x1="2" y1="10" x2="22" y2="10" />
                                            </svg>
                                            NIK: {{ $result['data']->nik }}
                                        </span>
                                    @endif
                                    @if ($result['data']->telephone)
                                        <span style="font-size:12px;color:var(--adm-text-faint);">
                                            <svg viewBox="0 0 24 24" style="width:12px;height:12px;vertical-align:-1px;">
                                                <path
                                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.81a16 16 0 0 0 6.29 6.29l.96-.96a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                                            </svg>
                                            {{ $result['data']->telephone }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Confidence bar --}}
                                <div style="margin-bottom:8px;">
                                    <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                                        <span style="font-size:12px;color:var(--adm-text-mid);">Tingkat Kemiripan</span>
                                        <span
                                            style="font-size:12px;font-weight:600;color:{{ $result['confidence'] >= 70 ? '#16a34a' : ($result['confidence'] >= 40 ? '#d97706' : '#dc2626') }};">
                                            {{ $result['confidence'] }}%
                                        </span>
                                    </div>
                                    <div class="fm-conf-bar-bg">
                                        <div class="fm-conf-bar-fill"
                                            style="width:{{ $result['confidence'] }}%;background:{{ $result['confidence'] >= 70 ? '#16a34a' : ($result['confidence'] >= 40 ? '#f59e0b' : '#dc2626') }};">
                                        </div>
                                    </div>
                                </div>

                                <div style="font-size:12.5px;color:var(--adm-text-mid);line-height:1.5;font-style:italic;">
                                    "{{ $result['reason'] }}"
                                </div>
                            </div>

                            {{-- Aksi --}}
                            <div class="fm-result-actions">
                                <a href="{{ route('superadmin.data-lapangan.show', $result['data']->id) }}"
                                    class="adm-btn-secondary" style="padding:6px 14px;font-size:12px;white-space:nowrap;">
                                    <svg viewBox="0 0 24 24" style="width:13px;height:13px;">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="adm-card" style="padding:48px;text-align:center;">
                            <svg viewBox="0 0 24 24"
                                style="width:40px;height:40px;color:var(--adm-text-faint);margin:0 auto 12px;display:block;">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            <div style="font-size:15px;font-weight:600;color:var(--adm-text-dark);margin-bottom:6px;">Tidak
                                Ada Hasil</div>
                            <div style="font-size:13px;color:var(--adm-text-faint);">Tidak ditemukan data foto yang bisa
                                dianalisis.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Disclaimer --}}
        <div
            style="margin-top:20px;padding:12px 16px;background:#fffbeb;border:1px solid #fcd34d;border-radius:10px;display:flex;gap:10px;align-items:flex-start;">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;flex-shrink:0;color:#92400e;margin-top:1px;">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                <line x1="12" y1="9" x2="12" y2="13" />
                <line x1="12" y1="17" x2="12.01" y2="17" />
            </svg>
            <span style="font-size:13px;color:#92400e;line-height:1.6;">
                <strong>Disclaimer:</strong> Hasil pencocokan wajah ini dihasilkan oleh AI (Claude) dan bersifat estimasi.
                Tingkat akurasi dapat bervariasi tergantung kualitas foto. Selalu lakukan verifikasi manual sebelum
                mengambil keputusan resmi.
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

        .fm-filter-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            font-size: 13px;
            border-radius: 8px;
            cursor: pointer;
            border: 1px solid var(--adm-border);
            background: var(--adm-card-bg);
            color: var(--adm-text-mid);
            transition: all .15s;
        }

        .fm-filter-btn:hover {
            border-color: var(--adm-blue);
            color: var(--adm-blue);
        }

        .fm-filter-btn.active {
            background: var(--adm-blue);
            color: #fff;
            border-color: var(--adm-blue);
        }

        .fm-result-card {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            background: var(--adm-card-bg);
            border: 1px solid var(--adm-border);
            border-radius: 12px;
            padding: 14px 16px;
            transition: transform .15s, box-shadow .15s;
        }

        .fm-result-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, .07);
        }

        .fm-result-card.fm-match {
            border-left: 4px solid #16a34a;
        }

        .fm-result-card.fm-nomatch {
            border-left: 4px solid var(--adm-border);
        }

        .fm-result-photo {
            width: 72px;
            height: 84px;
            flex-shrink: 0;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--adm-border);
            background: var(--adm-bg-faint);
        }

        .fm-result-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .fm-result-info {
            flex: 1;
            min-width: 0;
        }

        .fm-result-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--adm-text-dark);
        }

        .fm-result-actions {
            flex-shrink: 0;
            display: flex;
            align-items: center;
        }

        .fm-conf-bar-bg {
            height: 6px;
            background: var(--adm-bg-faint);
            border-radius: 99px;
            overflow: hidden;
        }

        .fm-conf-bar-fill {
            height: 100%;
            border-radius: 99px;
            transition: width .6s ease;
        }

        .fm-badge-match {
            background: #f0fdf4 !important;
            color: #166534 !important;
        }

        .fm-badge-nomatch {
            background: #fef2f2 !important;
            color: #991b1b !important;
        }
    </style>

    <script>
        function filterResults(filter, btn) {
            document.querySelectorAll('.fm-filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            document.querySelectorAll('.fm-result-card').forEach(card => {
                card.style.display = (filter === 'all' || card.dataset.match === filter) ? 'flex' : 'none';
            });
        }
    </script>
@endsection
