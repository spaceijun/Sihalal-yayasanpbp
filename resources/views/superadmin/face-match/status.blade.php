@extends('layouts.app')
@section('template_title', 'Memproses Face Match')

@section('content')
    <div class="adm-page" style="max-width:780px;margin:0 auto;">

        {{-- Header --}}
        <div class="adm-header" style="margin-bottom:20px;">
            <div class="adm-header-left">
                <h1>Memproses Pencocokan Wajah</h1>
                <p>
                    Dimulai: {{ $meta['started_at'] }}
                    &mdash; Total: <strong>{{ number_format($meta['total']) }}</strong> foto
                </p>
            </div>
            <a href="{{ route('superadmin.face-match.index') }}" class="adm-btn-secondary"
                onclick="return confirm('Batalkan dan kembali ke halaman utama?')">
                <svg viewBox="0 0 24 24" style="width:15px;height:15px;">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
                Batalkan
            </a>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start;">

            {{-- Kolom kiri: progress + foto --}}
            <div style="display:flex;flex-direction:column;gap:14px;">

                {{-- Foto query --}}
                <div class="adm-card" style="padding:20px;text-align:center;">
                    <p style="font-size:12px;color:var(--adm-text-faint);margin:0 0 10px;">Foto yang dicari:</p>
                    <img src="{{ $meta['query_url'] }}" alt="Foto Query"
                        style="width:90px;height:90px;border-radius:50%;object-fit:cover;
                               border:3px solid var(--adm-blue);box-shadow:0 4px 12px rgba(0,0,0,.1);">
                </div>

                {{-- Progress card --}}
                <div class="adm-card" style="padding:20px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                        <span style="font-size:12px;color:var(--adm-text-faint);">Progress</span>
                        <span style="font-size:13px;font-weight:600;color:var(--adm-text-dark);">
                            <span id="processed">0</span> / <span id="total">{{ $meta['total'] }}</span> foto
                        </span>
                    </div>

                    <div class="fm-progress-track">
                        <div id="progress-bar" class="fm-progress-fill" style="width:0%"></div>
                    </div>

                    <div style="text-align:center;margin-top:14px;">
                        <span id="percentage" style="font-size:36px;font-weight:800;color:var(--adm-blue);">0%</span>
                    </div>
                </div>

                {{-- Match counter --}}
                <div class="adm-card" style="padding:16px 20px;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div id="match-icon" class="fm-match-icon fm-match-none">
                            <svg viewBox="0 0 24 24" style="width:20px;height:20px;">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                        </div>
                        <div>
                            <div style="font-size:22px;font-weight:800;color:var(--adm-text-dark);">
                                <span id="match-count">0</span>
                                <span style="font-size:13px;font-weight:400;color:var(--adm-text-faint);">/ 3 cocok</span>
                            </div>
                            <div style="font-size:12px;color:var(--adm-text-faint);">Foto ≥80% ditemukan</div>
                        </div>
                        <div id="match-badge" class="fm-limit-badge" style="display:none;margin-left:auto;">
                            ✓ Limit tercapai
                        </div>
                    </div>
                </div>

                {{-- Status --}}
                <div style="text-align:center;">
                    <span id="status-text" class="fm-status-badge fm-status-running">
                        <span class="fm-spinner"></span>
                        Sedang memproses...
                    </span>
                </div>

                {{-- Info gagal --}}
                <div id="failed-info" class="fm-warn-box" style="display:none;">
                    <svg viewBox="0 0 24 24" style="width:14px;height:14px;flex-shrink:0;">
                        <path
                            d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                        <line x1="12" y1="9" x2="12" y2="13" />
                        <line x1="12" y1="17" x2="12.01" y2="17" />
                    </svg>
                    <span><span id="failed-count">0</span> foto gagal diproses (dilewati otomatis).</span>
                </div>

            </div>

            {{-- Kolom kanan: activity feed --}}
            <div class="adm-card" style="display:flex;flex-direction:column;min-height:420px;">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                        </svg>
                        Aktivitas Terkini
                    </div>
                    <span id="activity-count" style="font-size:11px;color:var(--adm-text-faint);margin-left:auto;">
                        —
                    </span>
                </div>

                <div id="activity-feed"
                    style="flex:1;overflow-y:auto;padding:12px 16px;
                           display:flex;flex-direction:column;gap:8px;max-height:460px;">
                    {{-- Diisi JS --}}
                    <div id="activity-empty"
                        style="flex:1;display:flex;flex-direction:column;align-items:center;
                               justify-content:center;gap:8px;color:var(--adm-text-faint);">
                        <svg viewBox="0 0 24 24" style="width:32px;height:32px;opacity:.3;">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                        <span style="font-size:13px;">Menunggu hasil pertama...</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <style>
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes fadeSlideIn {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fm-progress-track {
            height: 12px;
            background: var(--adm-bg-faint);
            border-radius: 99px;
            overflow: hidden;
            border: 1px solid var(--adm-border);
        }

        .fm-progress-fill {
            height: 100%;
            background: var(--adm-blue);
            border-radius: 99px;
            transition: width .5s ease;
            background: linear-gradient(90deg, var(--adm-blue) 0%, #60a5fa 100%);
        }

        .fm-progress-fill.done {
            background: linear-gradient(90deg, #16a34a 0%, #4ade80 100%);
        }

        .fm-match-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .fm-match-none {
            background: #f1f5f9;
            color: var(--adm-text-faint);
        }

        .fm-match-found {
            background: #dcfce7;
            color: #16a34a;
        }

        .fm-match-full {
            background: #fef9c3;
            color: #ca8a04;
        }

        .fm-limit-badge {
            font-size: 11px;
            font-weight: 600;
            background: #fef9c3;
            color: #854d0e;
            border: 1px solid #fcd34d;
            border-radius: 99px;
            padding: 3px 10px;
        }

        .fm-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            font-weight: 500;
            padding: 7px 16px;
            border-radius: 99px;
        }

        .fm-status-running {
            background: #fef9c3;
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        .fm-status-done {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .fm-spinner {
            width: 13px;
            height: 13px;
            border: 2px solid currentColor;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin .8s linear infinite;
            flex-shrink: 0;
        }

        .fm-warn-box {
            display: flex;
            gap: 8px;
            align-items: flex-start;
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 12px;
            color: #92400e;
        }

        /* Activity item */
        .fm-activity-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 8px;
            border: 1px solid var(--adm-border);
            background: var(--adm-card-bg);
            animation: fadeSlideIn .25s ease;
            font-size: 12px;
        }

        .fm-activity-item.is-match {
            border-color: #86efac;
            background: #f0fdf4;
        }

        .fm-activity-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .fm-activity-dot.match {
            background: #16a34a;
        }

        .fm-activity-dot.nomatch {
            background: #d1d5db;
        }

        .fm-activity-name {
            font-weight: 600;
            color: var(--adm-text-dark);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 120px;
        }

        .fm-activity-enum {
            color: var(--adm-text-faint);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
        }

        .fm-activity-conf {
            font-weight: 700;
            flex-shrink: 0;
            font-size: 13px;
        }

        .fm-activity-conf.match {
            color: #16a34a;
        }

        .fm-activity-conf.nomatch {
            color: #9ca3af;
        }
    </style>

    <script>
        (function() {
            const POLL_URL = @json(route('superadmin.face-match.poll', ['key' => $sessionKey]));
            const RESULT_URL = @json(route('superadmin.face-match.result', ['key' => $sessionKey]));
            const INTERVAL = 2000;

            const elBar = document.getElementById('progress-bar');
            const elPct = document.getElementById('percentage');
            const elProcessed = document.getElementById('processed');
            const elTotal = document.getElementById('total');
            const elStatus = document.getElementById('status-text');
            const elFailed = document.getElementById('failed-info');
            const elFailedCnt = document.getElementById('failed-count');
            const elMatchCount = document.getElementById('match-count');
            const elMatchIcon = document.getElementById('match-icon');
            const elMatchBadge = document.getElementById('match-badge');
            const elFeed = document.getElementById('activity-feed');
            const elEmpty = document.getElementById('activity-empty');
            const elActCount = document.getElementById('activity-count');

            let timer = null;
            let lastRendered = 0; // jumlah item yang sudah dirender

            // ── Render activity feed ──────────────────────────────────────────
            function renderActivity(items) {
                if (!items || items.length === 0) return;

                // Hanya render item baru (dibanding render terakhir)
                const newItems = items.slice(lastRendered);
                if (newItems.length === 0) return;

                if (elEmpty) elEmpty.remove();

                newItems.forEach(item => {
                    const isMatch = item.match;
                    const confCls = isMatch ? 'match' : 'nomatch';
                    const confText = item.confidence + '%';

                    const div = document.createElement('div');
                    div.className = 'fm-activity-item' + (isMatch ? ' is-match' : '');
                    div.innerHTML = `
                        <span class="fm-activity-dot ${confCls}"></span>
                        <span class="fm-activity-name" title="${esc(item.nama_pu)}">${esc(item.nama_pu)}</span>
                        <span class="fm-activity-enum" title="${esc(item.nama_enumerator)}">
                            ${esc(item.nama_enumerator)}
                        </span>
                        <span class="fm-activity-conf ${confCls}">${confText}</span>
                        ${isMatch ? '<svg viewBox="0 0 24 24" style="width:13px;height:13px;color:#16a34a;flex-shrink:0;"><path d="M20 6L9 17l-5-5"/></svg>' : ''}
                    `;
                    // Prepend supaya terbaru di atas
                    elFeed.insertBefore(div, elFeed.firstChild);
                });

                lastRendered = items.length;
                elActCount.textContent = items.length + ' foto diproses';
            }

            // ── Update match counter & icon ───────────────────────────────────
            function updateMatchUI(count) {
                elMatchCount.textContent = count;

                elMatchIcon.classList.remove('fm-match-none', 'fm-match-found', 'fm-match-full');

                if (count === 0) {
                    elMatchIcon.classList.add('fm-match-none');
                    elMatchBadge.style.display = 'none';
                } else if (count >= 3) {
                    elMatchIcon.classList.add('fm-match-full');
                    elMatchBadge.style.display = 'inline-flex';
                    // Ganti ikon jadi bintang
                    elMatchIcon.innerHTML = `<svg viewBox="0 0 24 24" style="width:20px;height:20px;">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>`;
                } else {
                    elMatchIcon.classList.add('fm-match-found');
                    elMatchIcon.innerHTML = `<svg viewBox="0 0 24 24" style="width:20px;height:20px;">
                        <path d="M20 6L9 17l-5-5"/>
                    </svg>`;
                }
            }

            // ── Update UI utama ───────────────────────────────────────────────
            function updateUI(data) {
                const pct = data.percentage ?? 0;

                elBar.style.width = pct + '%';
                elPct.textContent = pct + '%';
                elProcessed.textContent = data.processed ?? 0;
                elTotal.textContent = data.total ?? 0;

                if ((data.failed ?? 0) > 0) {
                    elFailedCnt.textContent = data.failed;
                    elFailed.style.display = 'flex';
                }

                updateMatchUI(data.match_count ?? 0);
                renderActivity(data.recent_activity ?? []);

                if (data.finished) {
                    clearInterval(timer);

                    elBar.classList.add('done');
                    elStatus.className = 'fm-status-badge fm-status-done';
                    elStatus.innerHTML = `<svg viewBox="0 0 24 24" style="width:14px;height:14px;">
                        <path d="M20 6L9 17l-5-5"/></svg> Selesai! Mengalihkan ke hasil...`;

                    setTimeout(() => {
                        window.location.href = RESULT_URL;
                    }, 1400);
                }
            }

            // ── Poll ──────────────────────────────────────────────────────────
            async function poll() {
                try {
                    const res = await fetch(POLL_URL, {
                        headers: {
                            Accept: 'application/json'
                        }
                    });
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    updateUI(await res.json());
                } catch (err) {
                    console.warn('Poll error:', err);
                }
            }

            function esc(str) {
                return String(str ?? '-')
                    .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            }

            poll();
            timer = setInterval(poll, INTERVAL);
        })();
    </script>
@endsection
