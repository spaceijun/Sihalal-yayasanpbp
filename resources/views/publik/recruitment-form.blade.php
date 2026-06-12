@extends('layouts.guest')
@section('title', $post->nama_loker . ' – Form Pendaftaran')
@section('content')

    <style>
        /* ── Form Recruitment specific layout — shared styles in public-pages.css ── */

        /* Page root */
        .rc-root {
            min-height: 100vh;
            background-color: #EEF3FA;
            background-image:
                radial-gradient(ellipse 70% 50% at 10% 5%, rgba(180, 210, 255, 0.5) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 90% 90%, rgba(160, 220, 200, 0.25) 0%, transparent 55%);
            font-family: 'Plus Jakarta Sans', sans-serif;
            padding: 2rem 1rem 3rem;
            position: relative;
        }

        .rc-root::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: radial-gradient(circle, rgba(100, 140, 200, 0.1) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
            z-index: 0;
        }

        /* Orbs reuse pubFloat from public-pages.css */
        .rc-orb { position:fixed; border-radius:50%; filter:blur(80px); pointer-events:none; z-index:0; animation:pubFloat 14s ease-in-out infinite; }
        .rc-orb-1 { width:380px; height:380px; background:rgba(130,180,255,.2); top:-80px; left:-80px; }
        .rc-orb-2 { width:260px; height:260px; background:rgba(100,210,180,.16); bottom:-60px; right:-60px; animation-delay:-6s; }

        .rc-wrap {
            position: relative;
            z-index: 1;
            max-width: 1060px;
            margin: 0 auto;
            display: flex;
            gap: 1.5rem;
            align-items: flex-start;
        }

        /* ── Layout ── */
        .rc-left {
            width: 310px;
            flex-shrink: 0;
            position: sticky;
            top: 2rem;
            background: linear-gradient(145deg, #1A5FC8 0%, #1040A0 55%, #0C2E78 100%);
            border-radius: 20px;
            padding: 2rem 1.75rem;
            box-shadow: 0 20px 50px rgba(16, 64, 160, 0.25);
            animation: cardIn 0.55s cubic-bezier(0.16, 1, 0.3, 1) both;
            overflow: hidden;
        }

        .rc-left::before {
            content: '';
            position: absolute;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.06);
            top: -80px;
            right: -80px;
            pointer-events: none;
        }

        .rc-left::after {
            content: '';
            position: absolute;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.04);
            bottom: 20px;
            left: -50px;
            pointer-events: none;
        }

        @keyframes cardIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .rc-brand {
            display: flex;
            align-items: center;
            gap: 11px;
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
        }

        .rc-brand-icon {
            width: 42px;
            height: 42px;
            background: rgba(255, 255, 255, 0.13);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .rc-brand-icon svg {
            width: 20px;
            height: 20px;
        }

        .rc-brand-name {
            font-family: 'Sora', sans-serif;
            font-size: 12.5px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.95);
            line-height: 1.4;
        }

        .rc-brand-name small {
            display: block;
            font-size: 11px;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.42);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .rc-left-title {
            font-family: 'Sora', sans-serif;
            font-size: 20px;
            font-weight: 600;
            color: #fff;
            line-height: 1.4;
            margin-bottom: 0.6rem;
            position: relative;
            z-index: 1;
        }

        .rc-left-title em {
            font-style: normal;
            color: #7DD3C8;
        }

        .rc-left-desc {
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.65);
            line-height: 1.7;
            margin-bottom: 1.75rem;
            position: relative;
            z-index: 1;
        }

        /* Step list */
        .rc-steps {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
        }

        .rc-step {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .rc-step-num {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
            color: #7DD3C8;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .rc-step-text {
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.65);
            line-height: 1.6;
        }

        .rc-step-text strong {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            display: block;
        }

        /* Quote */
        .rc-quote {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.11);
            border-radius: 13px;
            padding: 1.1rem 1.25rem;
            position: relative;
            z-index: 1;
        }

        .rc-quote-mark {
            font-size: 26px;
            color: #7DD3C8;
            font-family: Georgia, serif;
            line-height: 1;
            margin-bottom: 6px;
        }

        .rc-quote-text {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            line-height: 1.7;
            font-style: italic;
        }

        .rc-quote-author {
            margin-top: 8px;
            font-size: 10.5px;
            color: rgba(255, 255, 255, 0.28);
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        /* Right panel */
        .rc-right { flex:1; min-width:0; background:#fff; border-radius:20px; padding:2.5rem 2.25rem; box-shadow:0 0 0 1px rgba(100,140,210,.12),0 24px 60px rgba(60,100,180,.1); animation:pubCardIn .55s cubic-bezier(.16,1,.3,1) .08s both; }

        /* Form header */
        .rc-form-header { margin-bottom:1.5rem; }
        .rc-form-header h2 { font-family:'Sora',sans-serif; font-size:20px; font-weight:600; color:#0F1F40; margin-bottom:4px; }
        .rc-form-header p { font-size:13.5px; color:#8A99B3; }
        .rc-divider { height:1px; background:#EDF0F7; margin-bottom:1.75rem; }
        .rc-section-title { font-family:'Sora',sans-serif; font-size:13px; font-weight:600; color:#1A5FC8; text-transform:uppercase; letter-spacing:.07em; margin-bottom:1rem; display:flex; align-items:center; gap:8px; }
        .rc-section-title::after { content:''; flex:1; height:1px; background:#EDF0F7; }

        /* Dynamic form grid layout */
        .rc-form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        /* Fields */
        .rc-field {
            grid-column: span 2;
            margin-bottom: 1.1rem;
        }

        .rc-field-half {
            grid-column: span 1;
        }

        @media (max-width: 768px) {
            .rc-form-grid {
                grid-template-columns: 1fr;
            }
            .rc-field, .rc-field-half {
                grid-column: span 1;
            }
        }

        .rc-label { display:block; font-size:11.5px; font-weight:600; color:#6B7A99; text-transform:uppercase; letter-spacing:.07em; margin-bottom:6px; }
        .rc-label .req { color:#EF4444; margin-left:2px; }
        .rc-input,.rc-select,.rc-textarea { width:100%; background:#F5F7FB; border:1px solid #E0E7F0; border-radius:10px; font-size:14px; color:#0F1F40; font-family:'Plus Jakarta Sans',sans-serif; outline:none; transition:border-color .2s,background .2s,box-shadow .2s; }
        .rc-input,.rc-select { height:44px; padding:0 14px; }
        .rc-textarea { padding:12px 14px; resize:vertical; min-height:90px; line-height:1.6; }
        .rc-input::placeholder,.rc-textarea::placeholder { color:#B0BCCE; }
        .rc-select { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23B0BCCE' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 14px center; padding-right:36px; cursor:pointer; }
        .rc-input:focus,.rc-select:focus,.rc-textarea:focus { border-color:#1A5FC8; background:#fff; box-shadow:0 0 0 3px rgba(26,95,200,.1); }
        .rc-input.is-invalid,.rc-select.is-invalid,.rc-textarea.is-invalid { border-color:#FCA5A5!important; background:#FEF2F2!important; }
        .rc-input.is-valid,.rc-select.is-valid,.rc-textarea.is-valid { border-color:#6EE7B7!important; background:#F0FDF9!important; }
        .rc-error-msg { display:none; align-items:center; gap:5px; font-size:12px; color:#EF4444; margin-top:5px; }
        .rc-error-msg.show { display:flex; }
        .rc-error-msg svg { width:12px; height:12px; flex-shrink:0; }
        .rc-hint { font-size:11.5px; color:#B0BCCE; margin-top:4px; display:block; }
        .rc-field-footer { display:flex; justify-content:space-between; align-items:center; margin-top:4px; }
        .rc-char-count { font-size:11px; color:#B0BCCE; }
        .rc-char-count.warn { color:#F59E0B; }
        .rc-char-count.over { color:#EF4444; }

        /* Radio */
        .rc-radio-group { display:flex; gap:.75rem; flex-wrap:wrap; }
        .rc-radio-option { display:flex; align-items:center; gap:9px; cursor:pointer; padding:9px 16px; border:1px solid #E0E7F0; border-radius:10px; background:#F5F7FB; transition:all .2s; user-select:none; }
        .rc-radio-option:hover,.rc-radio-option.selected { border-color:#1A5FC8; background:#EEF4FF; }
        .rc-radio-option input[type="radio"] { display:none; }
        .rc-radio-visual { width:18px; height:18px; border:2px solid #C8D3E8; border-radius:50%; background:#fff; display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:all .2s; }
        .rc-radio-visual::after { content:''; width:8px; height:8px; border-radius:50%; background:#1A5FC8; opacity:0; transform:scale(0); transition:all .2s; }
        .rc-radio-option.selected .rc-radio-visual { border-color:#1A5FC8; }
        .rc-radio-option.selected .rc-radio-visual::after { opacity:1; transform:scale(1); }
        .rc-radio-label { font-size:13.5px; color:#3A4A6B; font-weight:500; }
        .rc-radio-option.selected .rc-radio-label { color:#1A5FC8; font-weight:600; }

        /* Checkbox Box style */
        .rc-checkbox-wrap {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 14px;
            background: #F5F7FB;
            border: 1px solid #E0E7F0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .rc-checkbox-wrap:hover {
            border-color: #1A5FC8;
            background: #EEF4FF;
        }
        .rc-checkbox-wrap input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #1A5FC8;
            margin-top: 1px;
            flex-shrink: 0;
            cursor: pointer;
        }
        .rc-checkbox-wrap span {
            font-size: 13.5px;
            color: #3A4A6B;
            line-height: 1.5;
            font-weight: 500;
        }

        /* File zone */
        .rc-file-zone { width:100%; background:#F5F7FB; border:1.5px dashed #C8D3E8; border-radius:10px; padding:14px; text-align:center; cursor:pointer; transition:all .2s; position:relative; }
        .rc-file-zone:hover,.rc-file-zone.drag-over { border-color:#1A5FC8; background:#EEF4FF; }
        .rc-file-zone.has-file { border-color:#6EE7B7; border-style:solid; background:#F0FDF9; }
        .rc-file-zone.is-invalid { border-color:#FCA5A5; background:#FEF2F2; }
        .rc-file-zone input[type="file"] { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }
        .rc-file-label { font-size:13px; color:#6B7A99; pointer-events:none; }
        .rc-file-label strong { display:block; color:#0F1F40; font-weight:500; margin-bottom:2px; }
        .rc-file-label small { font-size:11.5px; color:#B0BCCE; }
        .rc-file-zone.has-file .rc-file-label { color:#0F6E56; }
        .rc-file-zone.has-file .rc-file-label strong { color:#065F46; }

        /* Progress bar */
        .rc-progress-wrap {
            margin-bottom: 1.5rem;
        }

        .rc-progress-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .rc-progress-label {
            font-size: 12px;
            color: #8A99B3;
        }

        .rc-progress-pct {
            font-size: 12px;
            font-weight: 600;
            color: #1A5FC8;
        }

        .rc-progress-bar {
            height: 4px;
            background: #EDF0F7;
            border-radius: 4px;
            overflow: hidden;
        }

        .rc-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #1A5FC8, #1D9E75);
            border-radius: 4px;
            transition: width 0.4s ease;
            width: 0%;
        }

        /* Fee info box */
        .rc-fee-box {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            border-radius: 10px;
            padding: 12px 14px;
            margin-top: 10px;
            font-size: 13px;
            line-height: 1.6;
            animation: slideDown 0.25s ease;
        }

        .rc-fee-box svg {
            width: 16px;
            height: 16px;
            fill: none;
            stroke-width: 2;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .rc-fee-box.oss {
            background: #EBF5FF;
            border: 1px solid #BAD7F5;
            color: #1552A0;
        }

        .rc-fee-box.oss svg {
            stroke: #1A5FC8;
        }

        .rc-fee-box.sihalal {
            background: #FFFBEB;
            border: 1px solid #FDDCAB;
            color: #924C0A;
        }

        .rc-fee-box.sihalal svg {
            stroke: #D97706;
        }

        /* Download buttons */
        .rc-dl-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 6px;
        }

        .rc-dl-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 500;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            border: 1px solid;
        }

        .rc-dl-btn svg {
            width: 14px;
            height: 14px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
        }

        .rc-dl-btn.pendamping {
            background: #EEF4FF;
            color: #1A5FC8;
            border-color: #C0D4F5;
        }

        .rc-dl-btn.pendamping:hover,
        .rc-dl-btn.pendamping.active {
            background: #1A5FC8;
            color: #fff;
            border-color: #1A5FC8;
        }

        .rc-dl-btn.dataentry {
            background: #F5F7FB;
            color: #6B7A99;
            border-color: #E0E7F0;
        }

        .rc-dl-btn.dataentry:hover,
        .rc-dl-btn.dataentry.active {
            background: #3A4A6B;
            color: #fff;
            border-color: #3A4A6B;
        }

        .rc-dl-btn.adminumum {
            background: #F0FDF4;
            color: #16A34A;
            border-color: #BBF7D0;
        }

        .rc-dl-btn.adminumum:hover,
        .rc-dl-btn.adminumum.active {
            background: #16A34A;
            color: #fff;
            border-color: #16A34A;
        }

        /* Submit */
        .rc-submit {
            width: 100%;
            height: 48px;
            background: linear-gradient(135deg, #1A5FC8 0%, #1040A0 100%);
            border: none;
            border-radius: 11px;
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Sora', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            letter-spacing: 0.01em;
            transition: all 0.2s;
            box-shadow: 0 4px 16px rgba(26, 95, 200, 0.28);
            position: relative;
            overflow: hidden;
        }

        .rc-submit::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
            opacity: 0;
            transition: opacity 0.2s;
        }

        .rc-submit:hover::before {
            opacity: 1;
        }

        .rc-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 28px rgba(26, 95, 200, 0.36);
        }

        .rc-submit:active {
            transform: translateY(0);
        }

        .rc-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .rc-submit svg {
            width: 17px;
            height: 17px;
            fill: none;
            stroke: rgba(255, 255, 255, 0.85);
            stroke-width: 2;
        }

        /* Spinner */
        .rc-spinner {
            width: 17px;
            height: 17px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            display: none;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Modern alerts */
        .alert-success-modern, .alert-danger-modern {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            position: relative;
            font-size: 14px;
            line-height: 1.5;
            animation: slideDown 0.3s ease;
        }
        .alert-success-modern {
            background: #ECFDF5;
            border: 1px solid #A7F3D0;
            color: #065F46;
        }
        .alert-success-modern svg {
            width: 20px;
            height: 20px;
            stroke: #059669;
            stroke-width: 2;
            fill: none;
            flex-shrink: 0;
        }
        .alert-danger-modern {
            background: #FEF2F2;
            border: 1px solid #FCA5A5;
            color: #991B1B;
        }
        .alert-danger-modern svg {
            width: 20px;
            height: 20px;
            stroke: #DC2626;
            stroke-width: 2;
            fill: none;
            flex-shrink: 0;
        }
        .alt-text {
            flex: 1;
        }
        .alt-close {
            background: none;
            border: none;
            font-size: 20px;
            line-height: 1;
            color: currentColor;
            opacity: 0.6;
            cursor: pointer;
            padding: 0 4px;
            transition: opacity 0.2s;
        }
        .alt-close:hover {
            opacity: 1;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Footer */
        .rc-footer {
            text-align: center;
            margin-top: 1.75rem;
            font-size: 11.5px;
            color: #B0BCCE;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .rc-wrap {
                flex-direction: column;
            }

            .rc-left {
                width: 100%;
                position: static;
            }

            .rc-steps {
                display: none;
            }
        }
    </style>

    <div class="rc-root">
        <div class="rc-orb rc-orb-1"></div>
        <div class="rc-orb rc-orb-2"></div>

        <div class="rc-wrap">

            {{-- ── LEFT PANEL ── --}}
            <div class="rc-left">
                <div class="rc-brand">
                    <div class="rc-brand-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.9)" stroke-width="1.8">
                            <path d="M12 2L2 7l10 5 10-5-10-5z" />
                            <path d="M2 17l10 5 10-5" />
                            <path d="M2 12l10 5 10-5" />
                        </svg>
                    </div>
                    <div class="rc-brand-name">
                        Kawulo Halal
                        <small>Sertifikasi Produk Halal Untuk UMKM Low-Risk</small>
                    </div>
                </div>

                <p class="rc-left-title">Form<br><em>Recruitment</em></p>
                <p class="rc-left-desc">
                    {{ $post->nama_loker }}<br>
                    <small style="color: rgba(255,255,255,0.6); display: block; margin-top: 8px;">
                        {{ Str::limit($post->deskripsi, 120) }}
                    </small>
                </p>

                <div class="rc-steps">
                    <div class="rc-step">
                        <div class="rc-step-num">1</div>
                        <div class="rc-step-text">
                            <strong>Posisi Lowongan</strong>
                            Melamar untuk posisi: {{ $post->posisi }}
                        </div>
                    </div>
                    <div class="rc-step">
                        <div class="rc-step-num">2</div>
                        <div class="rc-step-text">
                            <strong>Isi Data Diri</strong>
                            Lengkapi informasi pribadi Anda dengan akurat sesuai KTP
                        </div>
                    </div>
                    <div class="rc-step">
                        <div class="rc-step-num">3</div>
                        <div class="rc-step-text">
                            <strong>Upload Dokumen</strong>
                            Siapkan dokumen pendukung yang diperlukan
                        </div>
                    </div>
                    <div class="rc-step">
                        <div class="rc-step-num">4</div>
                        <div class="rc-step-text">
                            <strong>Kirim Lamaran</strong>
                            Tinjau kembali lalu kirimkan lamaran Anda
                        </div>
                    </div>
                </div>

                <div class="rc-quote">
                    <div class="rc-quote-mark">"</div>
                    <p class="rc-quote-text">Pengalaman dan dedikasi adalah kunci kesuksesan bersama kami.</p>
                    <p class="rc-quote-author">— Kawulo Halal</p>
                </div>
            </div>

            {{-- ── RIGHT PANEL ── --}}
            <div class="rc-right">

                {{-- Progress Bar --}}
                <div class="rc-progress-wrap">
                    <div class="rc-progress-header">
                        <span class="rc-progress-label">Kelengkapan formulir</span>
                        <span class="rc-progress-pct" id="rcProgressPct">0%</span>
                    </div>
                    <div class="rc-progress-bar">
                        <div class="rc-progress-fill" id="rcProgressFill"></div>
                    </div>
                </div>

                <div class="rc-form-header">
                    <h2>Formulir Pendaftaran</h2>
                    <p>Lengkapi semua data diri Anda dengan benar dan teliti untuk lowongan <strong>{{ $post->nama_loker }}</strong></p>
                </div>
                <div class="rc-divider"></div>

                {{-- SUCCESS --}}
                @if (session('success'))
                    <div class="alert-success-modern" id="alertSuccess">
                        <svg viewBox="0 0 24 24">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                        <div class="alt-text"><strong>Berhasil!</strong> {{ session('success') }}</div>
                        <button class="alt-close" onclick="this.closest('[id]').remove()">&times;</button>
                    </div>
                @endif

                {{-- ERROR --}}
                @if (session('error'))
                    <div class="alert-danger-modern" id="alertError">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        <div class="alt-text"><strong>Gagal!</strong> {{ session('error') }}</div>
                        <button class="alt-close" onclick="this.closest('[id]').remove()">&times;</button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert-danger-modern" id="alertErrors">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        <div class="alt-text">
                            <strong>Terdapat kesalahan pada form:</strong>
                            <ul style="margin: 8px 0 0; padding-left: 20px;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button class="alt-close" onclick="this.closest('[id]').remove()">&times;</button>
                    </div>
                @endif

                <form method="POST" action="{{ route('recruitment.form.submit', $post->slug) }}" enctype="multipart/form-data"
                    id="formRecruitment" novalidate>
                    @csrf

                    @php
                        $requirements = $post->requirements ?? [];
                        $halfWidthKeys = ['nik', 'jenis_kelamin', 'pendidikan_terakhir', 'rekomendasi', 'foto_diri', 'foto_ktp'];
                    @endphp

                    <div class="rc-form-grid">
                        @forelse($requirements as $req)
                            @php
                                $key = $req['field_key'];
                                $label = $req['label'];
                                $type = $req['type'];
                                $required = $req['required'] ?? false;
                                $hint = $req['hint'] ?? null;
                                $options = $req['options'] ?? [];
                                $accept = $req['accept'] ?? '*/*';
                                $isHalf = in_array($key, $halfWidthKeys);
                            @endphp

                            {{-- Render Template Download button if the field is pakta_integritas --}}
                            @if($key === 'pakta_integritas')
                                <div class="rc-field">
                                    <label class="rc-label">Unduh Template Pakta Integritas</label>
                                    <div class="rc-dl-group">
                                        @if($post->template_pakta_integritas)
                                            <a href="{{ asset('storage/' . $post->template_pakta_integritas) }}" download class="rc-dl-btn pendamping active">
                                                <svg viewBox="0 0 24 24">
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                                    <polyline points="7 10 12 15 17 10" />
                                                    <line x1="12" y1="15" x2="12" y2="3" />
                                                </svg>
                                                Unduh Template Pakta
                                            </a>
                                        @else
                                            @if($post->posisi === 'PENDAMPING')
                                                <a href="{{ asset('assets/files/pakta-integritas-pendamping.docx') }}" download class="rc-dl-btn pendamping active">
                                                    <svg viewBox="0 0 24 24">
                                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                                        <polyline points="7 10 12 15 17 10" />
                                                        <line x1="12" y1="15" x2="12" y2="3" />
                                                    </svg>
                                                    Pakta — Pendamping
                                                </a>
                                            @elseif($post->posisi === 'DATA ENTRY')
                                                <a href="{{ asset('assets/files/pakta-integritas-data-entry.docx') }}" download class="rc-dl-btn dataentry active">
                                                    <svg viewBox="0 0 24 24">
                                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                                        <polyline points="7 10 12 15 17 10" />
                                                        <line x1="12" y1="15" x2="12" y2="3" />
                                                    </svg>
                                                    Pakta — Data Entry
                                                </a>
                                            @elseif($post->posisi === 'ADMIN UMUM')
                                                <a href="{{ asset('assets/files/pakta-integritas-admin-umum.docx') }}" download class="rc-dl-btn adminumum active">
                                                    <svg viewBox="0 0 24 24">
                                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                                        <polyline points="7 10 12 15 17 10" />
                                                        <line x1="12" y1="15" x2="12" y2="3" />
                                                    </svg>
                                                    Pakta — Admin Umum
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                    <span class="rc-hint">Unduh template sesuai posisi, tandatangani, lalu unggah kembali di bagian bawah.</span>
                                </div>
                            @endif

                            {{-- Render Form Field --}}
                            <div class="rc-field {{ $isHalf ? 'rc-field-half' : '' }}">
                                @if($type === 'checkbox')
                                    <label class="rc-checkbox-wrap" for="{{ $key }}">
                                        <input type="checkbox" name="{{ $key }}" id="{{ $key }}" value="1" {{ old($key) ? 'checked' : '' }} @if($required) required @endif>
                                        <span>{{ $label }}@if($required)<span class="req">*</span>@endif</span>
                                    </label>
                                    <div class="rc-error-msg" id="err_{{ $key }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="12" y1="8" x2="12" y2="12"/>
                                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                                        </svg>
                                        Persetujuan {{ $label }} wajib dicentang.
                                    </div>

                                @elseif($type === 'radio')
                                    <label class="rc-label">{{ $label }}@if($required)<span class="req">*</span>@endif</label>
                                    <div class="rc-radio-group">
                                        @foreach($options as $opt)
                                            <label class="rc-radio-option">
                                                <input type="radio" name="{{ $key }}" value="{{ $opt }}" {{ old($key) === $opt ? 'checked' : '' }} @if($required) required @endif>
                                                <span class="rc-radio-visual"></span>
                                                <span class="rc-radio-label">{{ $opt }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div class="rc-error-msg" id="err_{{ $key }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="12" y1="8" x2="12" y2="12"/>
                                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                                        </svg>
                                        Pilih salah satu opsi untuk {{ $label }}.
                                    </div>

                                @elseif($type === 'select')
                                    <label class="rc-label" for="{{ $key }}">{{ $label }}@if($required)<span class="req">*</span>@endif</label>
                                    <select id="{{ $key }}" name="{{ $key }}" class="rc-select @error($key) is-invalid @enderror" @if($required) required @endif>
                                        <option value="">-- Pilih --</option>
                                        @foreach($options as $opt)
                                            <option value="{{ $opt }}" {{ old($key) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                    <div class="rc-error-msg" id="err_{{ $key }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="12" y1="8" x2="12" y2="12"/>
                                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                                        </svg>
                                        Silakan pilih {{ $label }}.
                                    </div>

                                    @if($key === 'type_entry')
                                        <div id="alertOSS" class="rc-fee-box oss" style="display:none;">
                                            <svg viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="10"/>
                                                <line x1="12" y1="8" x2="12" y2="12"/>
                                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                                            </svg>
                                            <div><strong>Fee OSS: Rp100.000</strong>
                                                <small>Per 15 data yang berhasil diproses. Pahami alur kerja OSS sebelum mendaftar.</small>
                                            </div>
                                        </div>
                                        <div id="alertSIHALAL" class="rc-fee-box sihalal" style="display:none;">
                                            <svg viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="10"/>
                                                <line x1="12" y1="8" x2="12" y2="12"/>
                                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                                            </svg>
                                            <div><strong>Fee SIHALAL: Rp150.000</strong>
                                                <small>Per 15 data yang berhasil diproses. Pahami alur kerja SIHALAL sebelum mendaftar.</small>
                                            </div>
                                        </div>
                                    @endif

                                @elseif($type === 'file')
                                    <label class="rc-label" for="{{ $key }}">{{ $label }}@if($required)<span class="req">*</span>@endif</label>
                                    <div class="rc-file-zone @error($key) is-invalid @enderror" id="zone_{{ $key }}">
                                        <input type="file" id="{{ $key }}" name="{{ $key }}" accept="{{ $accept }}" @if($required) required @endif>
                                        <div class="rc-file-label" id="lbl_{{ $key }}">
                                            <strong>Klik atau seret file</strong>
                                            <small>{{ strtoupper(str_replace('image/', '', str_replace('application/', '', $accept))) }} · maks 5MB</small>
                                        </div>
                                    </div>
                                    <div class="rc-error-msg" id="err_{{ $key }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="12" y1="8" x2="12" y2="12"/>
                                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                                        </svg>
                                        <span id="msg_{{ $key }}">{{ $label }} wajib diupload (maksimal 5MB).</span>
                                    </div>

                                @elseif($type === 'textarea')
                                    <label class="rc-label" for="{{ $key }}">{{ $label }}@if($required)<span class="req">*</span>@endif</label>
                                    <textarea id="{{ $key }}" name="{{ $key }}" class="rc-textarea @error($key) is-invalid @enderror" @if($required) required @endif placeholder="Tuliskan {{ strtolower($label) }}...">{{ old($key) }}</textarea>
                                    <div class="rc-field-footer">
                                        @if($key === 'alamat_lengkap')
                                            <span class="rc-hint" style="margin-top:0;">Minimal 10 karakter</span>
                                            <span class="rc-char-count" id="cc_{{ $key }}">0/500</span>
                                        @elseif($key === 'pengalaman')
                                            <span class="rc-hint" style="margin-top:0;">Minimal 20 karakter</span>
                                            <span class="rc-char-count" id="cc_{{ $key }}">0/1000</span>
                                        @else
                                            <span class="rc-hint" style="margin-top:0;"></span>
                                            <span class="rc-char-count" id="cc_{{ $key }}">0/1000</span>
                                        @endif
                                    </div>
                                    <div class="rc-error-msg" id="err_{{ $key }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="12" y1="8" x2="12" y2="12"/>
                                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                                        </svg>
                                        {{ $label }} wajib diisi dengan benar.
                                    </div>

                                @else
                                    <label class="rc-label" for="{{ $key }}">{{ $label }}@if($required)<span class="req">*</span>@endif</label>
                                    <input type="text" id="{{ $key }}" name="{{ $key }}" class="rc-input @error($key) is-invalid @enderror" value="{{ old($key) }}" @if($required) required @endif placeholder="Masukkan {{ strtolower($label) }}">
                                    @if($key === 'nama_lengkap')
                                        <span class="rc-hint">Nama akan otomatis diubah ke huruf besar</span>
                                    @elseif($key === 'nik')
                                        <span class="rc-hint">Sesuai KTP — 16 digit angka</span>
                                    @elseif($key === 'telephone')
                                        <span class="rc-hint">Nomor telepon aktif (10–15 digit)</span>
                                    @endif
                                    <div class="rc-error-msg" id="err_{{ $key }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="12" y1="8" x2="12" y2="12"/>
                                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                                        </svg>
                                        {{ $label }} tidak valid.
                                    </div>
                                @endif

                                @if($hint)
                                    <span class="rc-hint">{{ $hint }}</span>
                                @endif
                            </div>
                        @empty
                            <p style="color:#B0BCCE;text-align:center;padding:20px;grid-column: span 2;">Form pendaftaran belum dikonfigurasi.</p>
                        @endforelse
                    </div>

                    <input type="hidden" name="status" value="Melamar">

                    {{-- Submit --}}
                    @if (count($requirements) > 0)
                        <div style="margin-top:2rem;">
                            <button class="rc-submit" type="submit" id="submitBtn">
                                <div class="rc-spinner" id="rcSpinner"></div>
                                <svg id="submitIcon" viewBox="0 0 24 24">
                                    <line x1="22" y1="2" x2="11" y2="13" />
                                    <polygon points="22 2 15 22 11 13 2 9 22 2" />
                                </svg>
                                <span id="submitLabel">Kirim Lamaran</span>
                            </button>
                        </div>
                    @endif

                </form>

                <div class="rc-footer">
                    &copy;
                    <script>
                        document.write(new Date().getFullYear())
                    </script> Kawulo Halal. All rights reserved.
                </div>
            </div>

        </div>
    </div>

    <script>
        (function() {
            'use strict';

            const MAX_FILE = 5 * 1024 * 1024; // 5MB limit
            const IMG_TYPES = ['image/jpeg', 'image/jpg', 'image/png'];
            const MIX_TYPES = [...IMG_TYPES, 'application/pdf'];

            function $(id) {
                return document.getElementById(id);
            }

            /* ── Track uploaded files ── */
            const uploadedFiles = {};

            /* ── Progress tracking ── */
            function calcProgress() {
                const requiredFields = document.querySelectorAll('#formRecruitment [required]');
                if (requiredFields.length === 0) return 100;
                
                // Deduplicate radio groups in count
                let totalCount = 0;
                const countedRadioNames = [];
                requiredFields.forEach(field => {
                    if (field.type === 'radio') {
                        const name = field.getAttribute('name');
                        if (!countedRadioNames.includes(name)) {
                            countedRadioNames.push(name);
                            totalCount++;
                        }
                    } else {
                        totalCount++;
                    }
                });

                // Deduplicate radio groups in done
                let doneCount = 0;
                const completedRadioNames = [];
                requiredFields.forEach(field => {
                    if (field.type === 'radio') {
                        const name = field.getAttribute('name');
                        if (!completedRadioNames.includes(name)) {
                            const checked = document.querySelector(`input[name="${name}"]:checked`);
                            if (checked) {
                                doneCount++;
                            }
                            completedRadioNames.push(name);
                        }
                    } else if (field.type === 'checkbox') {
                        if (field.checked) doneCount++;
                    } else if (field.type === 'file') {
                        if (uploadedFiles[field.id]) doneCount++;
                    } else {
                        if (field.value.trim().length > 0) {
                            if (field.id === 'nama_lengkap') {
                                if (field.value.trim().length >= 3) doneCount++;
                            } else if (field.id === 'nik') {
                                if (/^\d{16}$/.test(field.value)) doneCount++;
                            } else if (field.id === 'telephone') {
                                if (field.value.length >= 10 && field.value.length <= 15) doneCount++;
                            } else if (field.id === 'alamat_lengkap') {
                                if (field.value.trim().length >= 10) doneCount++;
                            } else if (field.id === 'pengalaman') {
                                if (field.value.trim().length >= 20) doneCount++;
                            } else {
                                doneCount++;
                            }
                        }
                    }
                });

                return Math.round((doneCount / totalCount) * 100);
            }

            function updateProgress() {
                const pct = calcProgress();
                const fill = $('rcProgressFill');
                const label = $('rcProgressPct');
                if (fill) fill.style.width = pct + '%';
                if (label) label.textContent = pct + '%';
            }

            /* ── Error helpers ── */
            function showErr(errId, visible) {
                const el = $(errId);
                if (!el) return;
                el.classList.toggle('show', visible);
            }

            function setFieldState(inputEl, valid) {
                if (!inputEl) return;
                inputEl.classList.toggle('is-valid', valid);
                inputEl.classList.toggle('is-invalid', !valid);
            }

            /* ── Toast (SweetAlert2 if loaded, else native alert) ── */
            function toast(type, msg) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: type,
                        text: msg,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3500,
                        timerProgressBar: true
                    });
                } else {
                    alert(msg);
                }
            }

            function scrollToFirstErr(el) {
                if (!el) return;
                el.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                if (typeof el.focus === 'function') el.focus();
            }

            /* ── Validation function ── */
            function validateField(input) {
                const key = input.id || input.name;
                const errId = 'err_' + key;
                let valid = true;

                if (input.required) {
                    if (input.type === 'radio') {
                        const name = input.getAttribute('name');
                        valid = !!document.querySelector(`input[name="${name}"]:checked`);
                    } else if (input.type === 'checkbox') {
                        valid = input.checked;
                    } else {
                        valid = input.value.trim().length > 0;
                    }
                }

                // Custom validation rules
                if (valid) {
                    if (key === 'nama_lengkap') {
                        const pos = input.selectionStart;
                        input.value = input.value.toUpperCase();
                        try {
                            input.setSelectionRange(pos, pos);
                        } catch (_) {}
                        valid = input.value.trim().length >= 3;
                    } else if (key === 'nik') {
                        input.value = input.value.replace(/\D/g, '').slice(0, 16);
                        valid = /^\d{16}$/.test(input.value);
                    } else if (key === 'telephone') {
                        input.value = input.value.replace(/\D/g, '').slice(0, 15);
                        valid = input.value.length >= 10 && input.value.length <= 15;
                    } else if (key === 'alamat_lengkap') {
                        valid = input.value.trim().length >= 10;
                        const cc = $('cc_' + key);
                        if (cc) cc.textContent = input.value.length + '/500';
                    } else if (key === 'pengalaman') {
                        valid = input.value.trim().length >= 20;
                        const cc = $('cc_' + key);
                        if (cc) cc.textContent = input.value.length + '/1000';
                    }
                }

                setFieldState(input, valid);
                showErr(errId, !valid);
                return valid;
            }

            /* ── Radio styling listener ── */
            function syncRadioStyles() {
                document.querySelectorAll('#formRecruitment input[type="radio"]').forEach(radio => {
                    const opt = radio.closest('.rc-radio-option');
                    if (opt) opt.classList.toggle('selected', radio.checked);
                });
            }

            document.querySelectorAll('#formRecruitment input[type="radio"]').forEach(r => {
                r.addEventListener('change', () => {
                    syncRadioStyles();
                    validateField(r);
                    updateProgress();
                });
            });

            /* ── Add input & change listeners dynamically ── */
            const inputs = document.querySelectorAll('#formRecruitment input, #formRecruitment select, #formRecruitment textarea');
            inputs.forEach(input => {
                if (input.type === 'file') return;

                input.addEventListener('input', function() {
                    validateField(input);
                    updateProgress();
                });

                input.addEventListener('change', function() {
                    validateField(input);
                    updateProgress();
                });
            });

            /* ── Type Entry Show/Hide alert info ── */
            const typeEntrySelect = $('type_entry');
            if (typeEntrySelect) {
                typeEntrySelect.addEventListener('change', function() {
                    const v = this.value;
                    const alertOSS = $('alertOSS');
                    const alertSIHALAL = $('alertSIHALAL');
                    if (alertOSS) alertOSS.style.display = v === 'OSS' ? 'flex' : 'none';
                    if (alertSIHALAL) alertSIHALAL.style.display = v === 'SIHALAL' ? 'flex' : 'none';
                });
            }

            /* ── File upload helper ── */
            function setupFileZone(config) {
                const {
                    zoneId,
                    inputId,
                    lblId,
                    errId,
                    msgId,
                    allowedTypes,
                    label
                } = config;
                const zone = $(zoneId);
                const input = $(inputId);
                const lbl = $(lblId);
                if (!zone || !input || !lbl) return;

                function processFile(file) {
                    if (!file) return;

                    if (file.size > MAX_FILE) {
                        $(msgId).textContent = 'Ukuran file "' + label + '" maksimal 5MB!';
                        showErr(errId, true);
                        zone.classList.add('is-invalid');
                        zone.classList.remove('has-file');
                        uploadedFiles[inputId] = null;
                        updateProgress();
                        toast('error', 'Ukuran file "' + label + '" maksimal 5MB!');
                        return;
                    }

                    if (!allowedTypes.includes(file.type)) {
                        const ext = allowedTypes.includes('application/pdf') ? 'JPG, PNG, atau PDF' : 'JPG atau PNG';
                        $(msgId).textContent = 'Format file tidak valid! "' + label + '" harus ' + ext;
                        showErr(errId, true);
                        zone.classList.add('is-invalid');
                        zone.classList.remove('has-file');
                        uploadedFiles[inputId] = null;
                        updateProgress();
                        toast('error', 'Format file "' + label + '" tidak valid!');
                        return;
                    }

                    uploadedFiles[inputId] = file;
                    zone.classList.remove('is-invalid');
                    zone.classList.add('has-file');
                    showErr(errId, false);

                    const isPdf = file.type === 'application/pdf';
                    const size = file.size > 1024 * 1024 ?
                        (file.size / 1024 / 1024).toFixed(1) + ' MB' :
                        Math.round(file.size / 1024) + ' KB';

                    lbl.innerHTML =
                        '<strong>' + file.name + '</strong>' +
                        '<small>' + size + (isPdf ? ' · PDF' : ' · Gambar') + ' — klik untuk ganti</small>';

                    updateProgress();
                }

                input.addEventListener('change', function() {
                    processFile(this.files[0]);
                });

                // Drag & drop
                zone.addEventListener('dragover', e => {
                    e.preventDefault();
                    zone.classList.add('drag-over');
                });
                zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
                zone.addEventListener('drop', e => {
                    e.preventDefault();
                    zone.classList.remove('drag-over');
                    processFile(e.dataTransfer.files[0]);
                });
            }

            // Dynamically setup all file zones
            document.querySelectorAll('#formRecruitment input[type="file"]').forEach(fileInput => {
                const key = fileInput.id;
                const accept = fileInput.getAttribute('accept') || '*/*';
                let allowedTypes = IMG_TYPES;
                if (accept.includes('pdf')) {
                    allowedTypes = MIX_TYPES;
                }

                setupFileZone({
                    zoneId: 'zone_' + key,
                    inputId: key,
                    lblId: 'lbl_' + key,
                    errId: 'err_' + key,
                    msgId: 'msg_' + key,
                    allowedTypes: allowedTypes,
                    label: fileInput.closest('.rc-field').querySelector('.rc-label').textContent.replace('*', '').trim()
                });
            });

            /* ── Form submit ── */
            $('formRecruitment')?.addEventListener('submit', function(e) {
                let formValid = true;
                let firstErr = null;

                const inputs = document.querySelectorAll('#formRecruitment input, #formRecruitment select, #formRecruitment textarea');
                inputs.forEach(input => {
                    const key = input.id || input.name;
                    const errId = 'err_' + key;

                    if (input.type === 'file') {
                        if (input.required && !uploadedFiles[key]) {
                            const zone = $('zone_' + key);
                            if (zone) zone.classList.add('is-invalid');
                            showErr(errId, true);
                            if (!firstErr) firstErr = zone;
                            formValid = false;
                        }
                    } else {
                        const isFieldValid = validateField(input);
                        if (!isFieldValid) {
                            if (!firstErr) firstErr = input;
                            formValid = false;
                        }
                    }
                });

                if (!formValid) {
                    e.preventDefault();
                    scrollToFirstErr(firstErr);
                    toast('error', 'Mohon lengkapi semua data yang diperlukan!');
                    return;
                }

                // All valid — show loading state
                const btn = $('submitBtn');
                if (btn) {
                    btn.disabled = true;
                    const spinner = $('rcSpinner');
                    const icon = $('submitIcon');
                    const label = $('submitLabel');
                    if (spinner) spinner.style.display = 'block';
                    if (icon) icon.style.display = 'none';
                    if (label) label.textContent = 'Mengirim...';
                }
            });

            /* ── Restore button on back-navigation (bfcache) ── */
            window.addEventListener('pageshow', function(e) {
                if (!e.persisted) return;
                const btn = $('submitBtn');
                if (!btn) return;
                btn.disabled = false;
                const spinner = $('rcSpinner');
                const icon = $('submitIcon');
                const label = $('submitLabel');
                if (spinner) spinner.style.display = 'none';
                if (icon) icon.style.display = '';
                if (label) label.textContent = 'Kirim Lamaran';
            });

            /* ── Auto close session alerts after 5s ── */
            setTimeout(() => {
                const alerts = document.querySelectorAll('#alertSuccess, #alertError, #alertErrors');
                alerts.forEach(el => {
                    if (!el) return;
                    el.style.transition = 'opacity 0.4s';
                    el.style.opacity = '0';
                    setTimeout(() => el.remove(), 400);
                });
            }, 5000);

            /* ── Init: populate char counters & progress from old() values ── */
            (function init() {
                // Populate char counts and validate filled fields
                const textareas = document.querySelectorAll('#formRecruitment textarea');
                textareas.forEach(ta => {
                    const key = ta.id;
                    if (ta.value) {
                        const cc = $('cc_' + key);
                        if (cc) cc.textContent = ta.value.length + '/' + (key === 'alamat_lengkap' ? '500' : '1000');
                    }
                });

                const allInputs = document.querySelectorAll('#formRecruitment input, #formRecruitment select, #formRecruitment textarea');
                allInputs.forEach(input => {
                    if (input.type !== 'file' && input.type !== 'hidden') {
                        if (input.value) {
                            validateField(input);
                        }
                    }
                });

                // Init type entry fee box visibility if pre-filled
                if (typeEntrySelect && typeEntrySelect.value) {
                    const v = typeEntrySelect.value;
                    const alertOSS = $('alertOSS');
                    const alertSIHALAL = $('alertSIHALAL');
                    if (alertOSS) alertOSS.style.display = v === 'OSS' ? 'flex' : 'none';
                    if (alertSIHALAL) alertSIHALAL.style.display = v === 'SIHALAL' ? 'flex' : 'none';
                }

                syncRadioStyles();
                updateProgress();
            })();

        })();
    </script>

@endsection
