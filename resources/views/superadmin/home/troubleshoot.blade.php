@extends('layouts.app')
@section('template_title')
    System Troubleshooting
@endsection

@section('content')
    {{-- Only JetBrains Mono needed — Plus Jakarta Sans & Sora already loaded by admin-ui.css --}}
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        /* ── Terminal-specific vars (not in admin-ui.css) ── */
        :root {
            --term-bg: #0f1117;
            --term-green: #10d98a;
            --term-yellow: #f59e0b;
            --term-red: #f43f5e;
            --term-blue: #4f8ef7;
            --term-cyan: #06b6d4;
            --term-purple: #a78bfa;
            --term-gray: #6b7280;
            --term-white: #e2e8f0;
        }

        /* ── Spinner ── */
        .spin {
            animation: _spin 1s linear infinite;
            display: inline-block;
        }

        @keyframes _spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ── Timing bar ── */
        .diag-timing {
            display: flex;
            align-items: center;
            gap: 14px;
            background: #fff;
            border: 1px solid var(--adm-border);
            border-radius: var(--adm-radius);
            box-shadow: var(--adm-shadow-sm);
            padding: 12px 18px;
            margin-bottom: 20px;
        }

        .diag-timing-label {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--adm-text-muted);
            white-space: nowrap;
        }

        .diag-timing-bar {
            flex: 1;
            height: 5px;
            background: var(--adm-border);
            border-radius: 4px;
            overflow: hidden;
        }

        .diag-timing-fill {
            height: 100%;
            border-radius: 4px;
            background: linear-gradient(90deg, var(--adm-blue), #0ea5e9);
            transition: width 1s ease;
        }

        .diag-timing-val {
            font-family: 'Sora', sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: var(--adm-text-dark);
            white-space: nowrap;
        }

        /* ── Terminal card ── */
        .terminal-card {
            background: #fff;
            border: 1px solid var(--adm-border);
            border-radius: var(--adm-radius);
            box-shadow: var(--adm-shadow-sm);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .terminal-card-header {
            background: var(--adm-bg-light);
            border-bottom: 1px solid var(--adm-border);
            padding: 11px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .terminal-card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 700;
            color: var(--adm-text-dark);
            font-family: 'Sora', sans-serif;
        }

        .term-dots {
            display: flex;
            gap: 6px;
        }

        .term-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .term-dot.r {
            background: #f43f5e;
        }

        .term-dot.y {
            background: #f59e0b;
        }

        .term-dot.g {
            background: var(--term-green);
        }

        .terminal-body {
            background: var(--term-bg);
            padding: 20px 24px;
            min-height: 130px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12.5px;
            line-height: 1.85;
            color: var(--term-white);
            overflow-x: auto;
        }

        .cursor {
            display: inline-block;
            width: 8px;
            height: 13px;
            background: var(--term-green);
            vertical-align: middle;
            animation: _blink .8s step-end infinite;
        }

        @keyframes _blink {
            50% {
                opacity: 0;
            }
        }

        /* Terminal line colours */
        .t-ok {
            color: var(--term-green);
        }

        .t-warn {
            color: var(--term-yellow);
        }

        .t-err {
            color: var(--term-red);
        }

        .t-info {
            color: var(--term-blue);
        }

        .t-muted {
            color: var(--term-gray);
        }

        .t-cyan {
            color: var(--term-cyan);
        }

        .t-key {
            color: var(--term-cyan);
        }

        .t-head {
            color: var(--term-purple);
            font-weight: 600;
        }

        .t-prompt {
            color: var(--term-purple);
        }

        /* ── Section collapse cards ── */
        .check-section {
            background: #fff;
            border: 1px solid var(--adm-border);
            border-radius: var(--adm-radius);
            box-shadow: var(--adm-shadow-sm);
            overflow: hidden;
            margin-bottom: 14px;
        }

        .check-section-header {
            padding: 13px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--adm-border);
            cursor: pointer;
            user-select: none;
            transition: background .15s;
            background: var(--adm-bg-light);
        }

        .check-section-header:hover {
            background: #eef2fa;
        }

        .check-section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: 'Sora', sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: var(--adm-text-dark);
        }

        .cs-icon {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }

        .check-section-meta {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cs-badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid;
            white-space: nowrap;
        }

        .cs-badge.ok {
            background: var(--adm-green-lt);
            color: var(--adm-green);
            border-color: rgba(15, 110, 86, .2);
        }

        .cs-badge.warn {
            background: var(--adm-amber-lt);
            color: var(--adm-amber);
            border-color: rgba(184, 104, 0, .2);
        }

        .cs-badge.err {
            background: var(--adm-red-lt);
            color: var(--adm-red);
            border-color: rgba(220, 38, 38, .2);
        }

        .cs-badge.loading {
            background: var(--adm-blue-lt);
            color: var(--adm-blue);
            border-color: rgba(26, 95, 200, .2);
        }

        .cs-chevron {
            color: var(--adm-text-faint);
            font-size: 18px;
            transition: transform .22s;
        }

        .check-section-header.collapsed .cs-chevron {
            transform: rotate(-90deg);
        }

        /* ── Check rows ── */
        .check-row {
            display: flex;
            align-items: flex-start;
            gap: 11px;
            padding: 11px 18px;
            border-bottom: 1px solid var(--adm-border);
            font-size: 13px;
            transition: background .12s;
        }

        .check-row:last-child {
            border-bottom: none;
        }

        .check-row:hover {
            background: var(--adm-bg-light);
        }

        .check-row-icon {
            flex-shrink: 0;
            margin-top: 1px;
        }

        .check-row-icon i {
            font-size: 15px;
        }

        .check-row-icon .ok {
            color: var(--adm-green);
        }

        .check-row-icon .warn {
            color: var(--adm-amber);
        }

        .check-row-icon .err {
            color: var(--adm-red);
        }

        .check-row-icon .info {
            color: var(--adm-blue);
        }

        .check-row-content {
            flex: 1;
        }

        .check-row-label {
            color: var(--adm-text-mid);
            font-weight: 500;
            font-size: 13px;
        }

        .check-row-label strong {
            color: var(--adm-text-dark);
            font-weight: 700;
        }

        .check-row-detail {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11.5px;
            color: var(--adm-text-muted);
            margin-top: 2px;
        }

        /* ── Summary chips ── */
        .summary-bar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .sum-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 5px 13px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid;
        }

        .sum-chip .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .sum-chip.ok {
            background: var(--adm-green-lt);
            color: var(--adm-green);
            border-color: rgba(15, 110, 86, .2);
        }

        .sum-chip.warn {
            background: var(--adm-amber-lt);
            color: var(--adm-amber);
            border-color: rgba(184, 104, 0, .2);
        }

        .sum-chip.err {
            background: var(--adm-red-lt);
            color: var(--adm-red);
            border-color: rgba(220, 38, 38, .2);
        }

        .sum-chip.info {
            background: var(--adm-blue-lt);
            color: var(--adm-blue);
            border-color: rgba(26, 95, 200, .2);
        }

        /* ── Section label ── */
        .section-label {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--adm-text-faint);
            margin-bottom: 12px;
            margin-top: 8px;
            padding-left: 2px;
        }
    </style>

    <div class="adm-page">

        {{-- ── PAGE HEADER ── --}}
        <div class="adm-header">
            <div class="adm-header-left">
                <h1>
                    <svg style="display:inline;vertical-align:middle;margin-right:7px;margin-bottom:3px" width="19"
                        height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                    </svg>
                    System Troubleshooting
                </h1>
                <p>Periksa kesehatan sistem secara menyeluruh — server, PHP, SSL, cronjob, dan database.</p>
            </div>
            <button class="adm-btn-primary" id="btnRunDiag" onclick="runDiagnostic()">
                <svg viewBox="0 0 24 24">
                    <polygon points="5 3 19 12 5 21 5 3" />
                </svg>
                Jalankan Troubleshooting
            </button>
        </div>

        {{-- ── SUMMARY CHIPS ── --}}
        <div class="summary-bar" id="summaryBar" style="display:none">
            <div class="sum-chip ok">
                <div class="dot"></div> <span id="cnt-ok">0</span>&nbsp;Passed
            </div>
            <div class="sum-chip warn">
                <div class="dot"></div> <span id="cnt-warn">0</span>&nbsp;Warning
            </div>
            <div class="sum-chip err">
                <div class="dot"></div> <span id="cnt-err">0</span>&nbsp;Failed
            </div>
            <div class="sum-chip info">
                <div class="dot"></div> <span id="cnt-info">0</span>&nbsp;Info
            </div>
        </div>

        {{-- ── TIMING BAR ── --}}
        <div class="diag-timing" id="timingBar" style="display:none">
            <div class="diag-timing-label">
                <svg style="display:inline;vertical-align:middle;margin-right:4px" width="13" height="13"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                </svg>
                Waktu Eksekusi
            </div>
            <div class="diag-timing-bar">
                <div class="diag-timing-fill" id="timingFill" style="width:0%"></div>
            </div>
            <div class="diag-timing-val" id="timingVal">0.00s</div>
        </div>

        {{-- ── TERMINAL OUTPUT ── --}}
        <div class="section-label">Terminal Output</div>
        <div class="terminal-card">
            <div class="terminal-card-header">
                <div class="terminal-card-title">
                    <div class="term-dots">
                        <div class="term-dot r"></div>
                        <div class="term-dot y"></div>
                        <div class="term-dot g"></div>
                    </div>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--adm-blue)"
                        stroke-width="2">
                        <polyline points="4 17 10 11 4 5" />
                        <line x1="12" y1="19" x2="20" y2="19" />
                    </svg>
                    Analisa Sistem {{ config('app.name') }}
                </div>
                <button class="adm-btn" onclick="copyTerminal()">
                    <svg viewBox="0 0 24 24">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2" />
                        <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1" />
                    </svg>
                    Copy
                </button>
            </div>
            <div class="terminal-body" id="termOutput">
                <span class="t-muted">Tekan "Jalankan Troubleshooting" untuk memulai pemeriksaan sistem...</span>
                <br><span class="cursor"></span>
            </div>
        </div>

        {{-- ── STRUCTURED RESULTS ── --}}
        <div id="structuredResults" style="display:none">
            <div class="section-label">Detail Pemeriksaan</div>

            {{-- Cronjob --}}
            <div class="check-section">
                <div class="check-section-header" onclick="toggleSection('cron')">
                    <div class="check-section-title">
                        <div class="cs-icon" style="background:var(--adm-blue-lt);color:var(--adm-blue)"><i
                                class="bx bx-time-five"></i></div>
                        Cronjob Status
                    </div>
                    <div class="check-section-meta">
                        <div class="cs-badge loading" id="badge-cron">Checking...</div>
                        <i class="bx bx-chevron-down cs-chevron" id="chev-cron"></i>
                    </div>
                </div>
                <div class="check-section-body" id="body-cron"></div>
            </div>

            {{-- PHP --}}
            <div class="check-section">
                <div class="check-section-header" onclick="toggleSection('php')">
                    <div class="check-section-title">
                        <div class="cs-icon" style="background:var(--adm-cyan-lt);color:var(--adm-cyan)"><i
                                class="bx bx-code-curly"></i></div>
                        PHP & Extensions
                    </div>
                    <div class="check-section-meta">
                        <div class="cs-badge loading" id="badge-php">Checking...</div>
                        <i class="bx bx-chevron-down cs-chevron" id="chev-php"></i>
                    </div>
                </div>
                <div class="check-section-body" id="body-php"></div>
            </div>

            {{-- Filesystem --}}
            <div class="check-section">
                <div class="check-section-header" onclick="toggleSection('fs')">
                    <div class="check-section-title">
                        <div class="cs-icon" style="background:var(--adm-green-lt);color:var(--adm-green)"><i
                                class="bx bx-folder"></i></div>
                        Filesystem & Storage
                    </div>
                    <div class="check-section-meta">
                        <div class="cs-badge loading" id="badge-fs">Checking...</div>
                        <i class="bx bx-chevron-down cs-chevron" id="chev-fs"></i>
                    </div>
                </div>
                <div class="check-section-body" id="body-fs"></div>
            </div>

            {{-- SSL --}}
            <div class="check-section">
                <div class="check-section-header" onclick="toggleSection('ssl')">
                    <div class="check-section-title">
                        <div class="cs-icon" style="background:var(--adm-indigo-lt);color:var(--adm-indigo)"><i
                                class="bx bx-lock-alt"></i></div>
                        SSL Certificate
                    </div>
                    <div class="check-section-meta">
                        <div class="cs-badge loading" id="badge-ssl">Checking...</div>
                        <i class="bx bx-chevron-down cs-chevron" id="chev-ssl"></i>
                    </div>
                </div>
                <div class="check-section-body" id="body-ssl"></div>
            </div>

            {{-- Environment --}}
            <div class="check-section">
                <div class="check-section-header" onclick="toggleSection('env')">
                    <div class="check-section-title">
                        <div class="cs-icon" style="background:var(--adm-amber-lt);color:var(--adm-amber)"><i
                                class="bx bx-cog"></i></div>
                        Environment & Database
                    </div>
                    <div class="check-section-meta">
                        <div class="cs-badge loading" id="badge-env">Checking...</div>
                        <i class="bx bx-chevron-down cs-chevron" id="chev-env"></i>
                    </div>
                </div>
                <div class="check-section-body" id="body-env"></div>
            </div>

            {{-- Network --}}
            <div class="check-section">
                <div class="check-section-header" onclick="toggleSection('net')">
                    <div class="check-section-title">
                        <div class="cs-icon" style="background:var(--adm-blue-lt);color:var(--adm-blue)"><i
                                class="bx bx-wifi"></i></div>
                        Network & CURL
                    </div>
                    <div class="check-section-meta">
                        <div class="cs-badge loading" id="badge-net">Checking...</div>
                        <i class="bx bx-chevron-down cs-chevron" id="chev-net"></i>
                    </div>
                </div>
                <div class="check-section-body" id="body-net"></div>
            </div>

            {{-- Scheduled Tasks --}}
            <div class="check-section">
                <div class="check-section-header" onclick="toggleSection('sched')">
                    <div class="check-section-title">
                        <div class="cs-icon" style="background:var(--adm-green-lt);color:var(--adm-green)"><i
                                class="bx bx-calendar-check"></i></div>
                        Scheduled Tasks
                    </div>
                    <div class="check-section-meta">
                        <div class="cs-badge loading" id="badge-sched">Checking...</div>
                        <i class="bx bx-chevron-down cs-chevron" id="chev-sched"></i>
                    </div>
                </div>
                <div class="check-section-body" id="body-sched"></div>
            </div>

        </div>{{-- /structuredResults --}}

    </div>{{-- /adm-page --}}

    <script>
        // ─── State ─────────────────────────────────────────────
        let counters = {
            ok: 0,
            warn: 0,
            err: 0,
            info: 0
        };
        let termLines = [];
        let startTime, timingInterval;

        // ─── Section toggle ─────────────────────────────────────
        function toggleSection(key) {
            const body = document.getElementById('body-' + key);
            const hdr = body.previousElementSibling;
            if (body.style.display === 'none') {
                body.style.display = '';
                hdr.classList.remove('collapsed');
            } else {
                body.style.display = 'none';
                hdr.classList.add('collapsed');
            }
        }

        // ─── Terminal helpers ────────────────────────────────────
        function term(html) {
            termLines.push(html);
            const out = document.getElementById('termOutput');
            out.innerHTML = termLines.join('<br>') + '<br><span class="cursor"></span>';
            out.scrollTop = out.scrollHeight;
        }

        function termHead(text) {
            term(`<span class="t-prompt">❯</span> <span class="t-head">${text}</span>`);
        }

        function termLine(icon, cls, text) {
            term(`&nbsp;&nbsp;<span class="${cls}">${icon} ${text}</span>`);
        }

        function termSep() {
            term(`<span class="t-muted">─────────────────────────────────────────</span>`);
        }

        // ─── Row builder ─────────────────────────────────────────
        function addRow(sectionId, type, label, detail) {
            const map = {
                ok: {
                    cls: 'ok',
                    ico: 'bx bx-check-circle'
                },
                warn: {
                    cls: 'warn',
                    ico: 'bx bx-error'
                },
                err: {
                    cls: 'err',
                    ico: 'bx bx-x-circle'
                },
                info: {
                    cls: 'info',
                    ico: 'bx bx-info-circle'
                },
            };
            const m = map[type] || map.info;
            counters[type] = (counters[type] || 0) + 1;
            updateSummary();

            const row = document.createElement('div');
            row.className = 'check-row';
            row.innerHTML = `
        <div class="check-row-icon"><i class="${m.ico} ${m.cls}"></i></div>
        <div class="check-row-content">
            <div class="check-row-label">${label}</div>
            ${detail ? `<div class="check-row-detail">${detail}</div>` : ''}
        </div>`;
            document.getElementById('body-' + sectionId).appendChild(row);
        }

        function setBadge(key, type, text) {
            const b = document.getElementById('badge-' + key);
            b.className = `cs-badge ${type}`;
            b.textContent = text;
        }

        function updateSummary() {
            ['ok', 'warn', 'err', 'info'].forEach(k => document.getElementById('cnt-' + k).textContent = counters[k]);
        }

        // ─── Timing ──────────────────────────────────────────────
        function startTiming() {
            startTime = performance.now();
            document.getElementById('timingBar').style.display = 'flex';
            timingInterval = setInterval(() => {
                const el = (performance.now() - startTime) / 1000;
                document.getElementById('timingVal').textContent = el.toFixed(2) + 's';
                document.getElementById('timingFill').style.width = Math.min((el / 20) * 100, 95) + '%';
            }, 100);
        }

        function stopTiming() {
            clearInterval(timingInterval);
            const el = (performance.now() - startTime) / 1000;
            document.getElementById('timingVal').textContent = el.toFixed(2) + 's';
            document.getElementById('timingFill').style.width = '100%';
        }

        // ─── Copy terminal ────────────────────────────────────────
        function copyTerminal() {
            navigator.clipboard.writeText(document.getElementById('termOutput').innerText).then(() => {
                const btn = event.currentTarget;
                btn.innerHTML = `<svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Copied!`;
                setTimeout(() => {
                    btn.innerHTML =
                        `<svg viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg> Copy`;
                }, 2000);
            });
        }

        // ─── Run troubleshooting ───────────────────────────────────────
        async function runDiagnostic() {
            counters = {
                ok: 0,
                warn: 0,
                err: 0,
                info: 0
            };
            termLines = [];

            ['cron', 'php', 'fs', 'ssl', 'env', 'net', 'sched'].forEach(k => {
                document.getElementById('body-' + k).innerHTML = '';
                document.getElementById('body-' + k).style.display = '';
                document.getElementById('badge-' + k).className = 'cs-badge loading';
                document.getElementById('badge-' + k).textContent = 'Checking...';
                document.getElementById('chev-' + k).parentElement.classList.remove('collapsed');
            });
            updateSummary();

            const btn = document.getElementById('btnRunDiag');
            btn.disabled = true;
            btn.innerHTML =
                `<svg viewBox="0 0 24 24" class="spin" style="width:15px;height:15px;stroke:#fff;fill:none;stroke-width:2.2"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg> Checking...`;

            document.getElementById('summaryBar').style.display = 'flex';
            document.getElementById('structuredResults').style.display = 'block';
            startTiming();

            document.getElementById('termOutput').innerHTML = '';
            termLines = [];
            term(`<span class="t-muted">Initializing system troubleshooting...</span>`);
            await sleep(300);

            try {
                const res = await fetch('{{ route($routePrefix . '.diagnostic.run') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                await renderResults(await res.json());
            } catch (e) {
                term(`<span class="t-err">✗ Gagal terhubung ke server: ${e.message}</span>`);
                term(`<span class="t-muted">Pastikan route troubleshooting sudah terdaftar di routes/web.php.</span>`);
            }

            stopTiming();
            btn.disabled = false;
            btn.innerHTML =
                `<svg viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg> Jalankan Ulang`;
        }

        async function sleep(ms) {
            return new Promise(r => setTimeout(r, ms));
        }

        // ─── Render results ───────────────────────────────────────
        async function renderResults(data) {

            // Cronjob
            termSep();
            termHead('Checking Cronjobs...');
            await sleep(80);
            let cronWarn = 0;
            if (data.cronjobs?.length) {
                for (const cj of data.cronjobs) {
                    if (cj.working) {
                        termLine('✓', 't-ok',
                            `${cj.name}: berjalan <span class="t-muted">(${cj.last_run} menit lalu)</span>`);
                        addRow('cron', 'ok', `<strong>${cj.name}</strong> — Berjalan Normal`,
                            `Terakhir run: ${cj.last_run} menit yang lalu`);
                    } else {
                        termLine('⚠', 't-warn', `${cj.name}: tidak terdeteksi`);
                        addRow('cron', 'warn', `<strong>${cj.name}</strong> — Tidak Terdeteksi`, cj.note ||
                            'Belum pernah berjalan atau cache expired');
                        cronWarn++;
                    }
                }
            }
            setBadge('cron', cronWarn ? 'warn' : 'ok', cronWarn ? `${cronWarn} Warning` : 'All OK');

            // PHP
            termSep();
            termHead('Checking PHP version & extensions...');
            await sleep(80);
            if (data.php) {
                const ok = data.php.valid;
                termLine(ok ? '✓' : '✗', ok ? 't-ok' : 't-err',
                    `PHP ${data.php.version} — ${ok?'Valid':'Invalid'} (>= ${data.php.required})`);
                addRow('php', ok ? 'ok' : 'err', `<strong>PHP ${data.php.version}</strong>`,
                    `Required: >= ${data.php.required}`);
                let phpWarn = !ok;
                if (data.php.extensions) {
                    for (const [ext, enabled] of Object.entries(data.php.extensions)) {
                        termLine(enabled ? '✓' : '✗', enabled ? 't-ok' : 't-err',
                            `Extension <span class="t-cyan">${ext}</span> — ${enabled?'Enabled':'Missing'}`);
                        addRow('php', enabled ? 'ok' : 'err', `Extension: <strong>${ext}</strong>`, enabled ?
                            'Enabled' : 'Not found / disabled');
                        if (!enabled) phpWarn = true;
                    }
                }
                setBadge('php', phpWarn ? 'err' : 'ok', phpWarn ? 'Issues Found' : 'All OK');
            }

            // Filesystem
            termSep();
            termHead('Checking file permissions & storage...');
            await sleep(80);
            let fsOk = true;
            if (data.filesystem) {
                for (const item of data.filesystem) {
                    const ok = item.writable && item.exists;
                    termLine(ok ? '✓' : '✗', ok ? 't-ok' : 't-err',
                        `<span class="t-cyan">${item.path}</span> — ${ok?'Writable & exists':item.note}`);
                    addRow('fs', ok ? 'ok' : 'err', `<strong>${item.path}</strong>`, ok ? 'Writable and exists' : item
                        .note);
                    if (!ok) fsOk = false;
                }
            }
            if (data.storage_link !== undefined) {
                const slOk = data.storage_link;
                termLine(slOk ? '✓' : '⚠', slOk ? 't-ok' : 't-warn',
                    `Storage symlink — ${slOk?'exists':'missing (php artisan storage:link)'}`);
                addRow('fs', slOk ? 'ok' : 'warn', '<strong>Storage Symlink</strong>', slOk ? 'Storage link exists' :
                    'Missing — jalankan: php artisan storage:link');
                if (!slOk) fsOk = false;
            }
            setBadge('fs', fsOk ? 'ok' : 'err', fsOk ? 'All OK' : 'Issues Found');

            // SSL
            termSep();
            termHead('Checking SSL certificate...');
            await sleep(80);
            let sslStatus = 'ok';
            if (data.ssl) {
                const ssl = data.ssl;
                if (ssl.valid) {
                    termLine('✓', 't-ok', `SSL valid — <span class="t-muted">${ssl.common_name}</span>`);
                    termLine('i', 't-info',
                        `Berlaku hingga: <span class="t-cyan">${ssl.valid_to}</span> (${ssl.days_remaining} hari lagi)`
                    );
                    addRow('ssl', 'ok', `<strong>${ssl.common_name}</strong> — SSL Valid`,
                        `Issued by: ${ssl.issuer} &nbsp;|&nbsp; Exp: ${ssl.valid_to}`);
                    addRow('ssl', ssl.days_remaining < 30 ? 'warn' : 'info',
                        `Sisa Masa Berlaku: <strong>${ssl.days_remaining} hari</strong>`, ssl.days_remaining < 30 ?
                        '⚠ Segera perpanjang!' : 'Certificate masih aman');
                    if (ssl.days_remaining < 30) sslStatus = 'warn';
                } else {
                    termLine('✗', 't-err', `SSL tidak valid atau tidak ditemukan`);
                    addRow('ssl', 'err', '<strong>SSL Certificate</strong>', ssl.error || 'Tidak ditemukan');
                    sslStatus = 'err';
                }
            }
            setBadge('ssl', sslStatus, sslStatus === 'ok' ? 'Valid' : sslStatus === 'warn' ? 'Expiring Soon' :
                'Invalid');

            // Environment
            termSep();
            termHead('Checking .env & database...');
            await sleep(80);
            let envOk = true;
            if (data.env) {
                for (const [key, val] of Object.entries(data.env)) {
                    const ok = val.ok;
                    termLine(ok ? '✓' : '✗', ok ? 't-ok' : 't-err',
                        `<span class="t-key">${key}</span> — ${ok?'OK':'Missing/Invalid'}`);
                    addRow('env', ok ? 'ok' : 'err', `<strong>${key}</strong>`, ok ? 'OK' :
                        'Tidak ditemukan atau kosong');
                    if (!ok) envOk = false;
                }
            }
            if (data.database) {
                const dbOk = data.database.connected;
                termLine(dbOk ? '✓' : '✗', dbOk ? 't-ok' : 't-err',
                    `Database — ${dbOk?'Connected':'Failed'} <span class="t-muted">${data.database.name||''}</span>`
                );
                addRow('env', dbOk ? 'ok' : 'err', `<strong>Database Connection</strong>`, dbOk ?
                    `Connected — ${data.database.name}` : data.database.error || 'Connection failed');
                if (!dbOk) envOk = false;
            }
            setBadge('env', envOk ? 'ok' : 'err', envOk ? 'All OK' : 'Issues Found');

            // Network
            termSep();
            termHead('Testing CURL connection...');
            await sleep(80);
            let netOk = true;
            if (data.network) {
                for (const item of data.network) {
                    const ok = item.success;
                    termLine(ok ? '✓' : '✗', ok ? 't-ok' : 't-err',
                        `CURL → <span class="t-cyan">${item.url}</span> — HTTP ${item.code||'failed'}`);
                    addRow('net', ok ? 'ok' : 'err', `<strong>CURL → ${item.url}</strong>`, ok ? `HTTP ${item.code}` :
                        item.error || 'Gagal terhubung');
                    if (!ok) netOk = false;
                }
            }
            setBadge('net', netOk ? 'ok' : 'err', netOk ? 'Connected' : 'Failed');

            // Scheduled
            termSep();
            termHead('Checking scheduled tasks...');
            await sleep(80);
            if (data.scheduled_tasks?.length) {
                for (const t of data.scheduled_tasks) {
                    termLine('i', 't-info',
                        `<span class="t-muted">${t.expression}</span>&nbsp;&nbsp;<span class="t-cyan">${t.command}</span>&nbsp;&nbsp;<span class="t-muted">→ Next: ${t.next_due}</span>`
                    );
                    addRow('sched', 'info', `<strong>${t.command}</strong>`,
                        `Expression: ${t.expression} &nbsp;|&nbsp; Next due: ${t.next_due}`);
                }
                setBadge('sched', 'ok', `${data.scheduled_tasks.length} Tasks`);
            } else {
                termLine('⚠', 't-warn', 'Tidak ada scheduled task ditemukan');
                addRow('sched', 'warn', '<strong>Scheduled Tasks</strong>', 'Tidak ada task yang terdaftar');
                setBadge('sched', 'warn', 'No Tasks');
            }

            // Final
            termSep();
            const elapsed = ((performance.now() - startTime) / 1000).toFixed(2);
            term(`<span class="t-muted">Total time: <span class="t-ok">${elapsed} seconds</span></span>`);
            term(
                `<span class="t-ok">✓ Troubleshooting selesai — ${counters.ok} passed, ${counters.warn} warning, ${counters.err} failed</span>`
            );
        }
    </script>
@endsection
