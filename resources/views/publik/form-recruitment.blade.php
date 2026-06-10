@extends('layouts.guest')
@section('title', 'Form Recruitment')
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
        .rc-wrap { position:relative; z-index:1; max-width:1060px; margin:0 auto; display:flex; gap:1.5rem; align-items:flex-start; }

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
            color: rgba(255, 255, 255, 0.5);
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

        /* Fields */
        .rc-field { margin-bottom:1.1rem; }
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

        /* Row 2 col */
        .rc-row2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
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

            .rc-row2 {
                grid-template-columns: 1fr;
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
                <p class="rc-left-desc">Bergabunglah dengan tim kami dan jadilah bagian dari program yang berdampak nyata.
                </p>

                <div class="rc-steps">
                    <div class="rc-step">
                        <div class="rc-step-num">1</div>
                        <div class="rc-step-text">
                            <strong>Pilih Posisi</strong>
                            Tentukan posisi yang Anda lamar: Pendamping atau Data Entry
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
                            Siapkan foto diri, KTP, ijazah & pakta integritas
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
                    <p>Lengkapi semua data diri Anda dengan benar dan teliti</p>
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

                <form method="POST" action="{{ route('recruitment.store') }}" enctype="multipart/form-data"
                    id="formRecruitment" novalidate>
                    @csrf

                    {{-- ───────────────────────────── --}}
                    {{-- SECTION 1: POSISI --}}
                    {{-- ───────────────────────────── --}}
                    <div class="rc-section-title">Posisi Dilamar</div>

                    <div class="rc-field">
                        <label class="rc-label">Pilih Posisi <span class="req">*</span></label>
                        <div class="rc-radio-group" id="radioGroup">
                            <label class="rc-radio-option" id="lblPendamping">
                                <input type="radio" name="recruit_type" id="type_pendamping" value="PENDAMPING" required>
                                <span class="rc-radio-visual"></span>
                                <span class="rc-radio-label">Pendamping</span>
                            </label>
                            <label class="rc-radio-option" id="lblDataEntry">
                                <input type="radio" name="recruit_type" id="type_data_entry" value="DATA ENTRY">
                                <span class="rc-radio-visual"></span>
                                <span class="rc-radio-label">Data Entry</span>
                            </label>
                            <label class="rc-radio-option" id="lblAdminUmum">
                                <input type="radio" name="recruit_type" id="type_admin_umum" value="ADMIN UMUM">
                                <span class="rc-radio-visual"></span>
                                <span class="rc-radio-label">Admin Umum</span>
                            </label>
                        </div>
                        <div class="rc-error-msg" id="errPosisi">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            Pilih posisi yang Anda lamar
                        </div>
                        @error('recruit_type')
                            <span class="rc-error-msg show">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Tipe Entry (conditional) --}}
                    <div class="rc-field" id="typeEntryWrapper" style="display:none;">
                        <label class="rc-label" for="type_entry">Tipe Entry <span class="req">*</span></label>
                        <select id="type_entry" name="type_entry"
                            class="rc-select @error('type_entry') is-invalid @enderror">
                            <option value="">-- Pilih Tipe Entry --</option>
                            <option value="OSS">OSS</option>
                            <option value="SIHALAL">SIHALAL</option>
                        </select>
                        <div class="rc-error-msg" id="errTypeEntry">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            Pilih tipe entry terlebih dahulu
                        </div>
                        @error('type_entry')
                            <span class="rc-error-msg show">{{ $message }}</span>
                        @enderror

                        <div id="alertOSS" class="rc-fee-box oss" style="display:none;">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            <div><strong>Fee OSS: Rp100.000</strong>
                                <small>Per 15 data yang berhasil diproses. Pahami alur kerja OSS sebelum mendaftar.</small>
                            </div>
                        </div>
                        <div id="alertSIHALAL" class="rc-fee-box sihalal" style="display:none;">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            <div><strong>Fee SIHALAL: Rp150.000</strong>
                                <small>Per 15 data yang berhasil diproses. Pahami alur kerja SIHALAL sebelum
                                    mendaftar.</small>
                            </div>
                        </div>
                    </div>

                    {{-- ───────────────────────────── --}}
                    {{-- SECTION 2: DATA DIRI --}}
                    {{-- ───────────────────────────── --}}
                    <div class="rc-section-title" style="margin-top:1.5rem;">Data Diri</div>

                    <div class="rc-field">
                        <label class="rc-label" for="nama_lengkap">Nama Lengkap <span class="req">*</span></label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap"
                            class="rc-input @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap') }}"
                            required autofocus placeholder="Masukkan nama lengkap" autocomplete="name" maxlength="255"
                            style="text-transform:uppercase;">
                        <span class="rc-hint">Nama akan otomatis diubah ke huruf besar</span>
                        <div class="rc-error-msg" id="errNama">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            Nama lengkap wajib diisi (minimal 3 karakter)
                        </div>
                        @error('nama_lengkap')
                            <span class="rc-error-msg show">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="rc-row2">
                        <div class="rc-field">
                            <label class="rc-label" for="nik">NIK <span class="req">*</span></label>
                            <input type="text" id="nik" name="nik"
                                class="rc-input @error('nik') is-invalid @enderror" value="{{ old('nik') }}" required
                                placeholder="16 digit NIK" maxlength="16" minlength="16" inputmode="numeric"
                                pattern="\d{16}" autocomplete="off">
                            <span class="rc-hint">Sesuai KTP — 16 digit angka</span>
                            <div class="rc-error-msg" id="errNik">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                NIK harus tepat 16 digit angka
                            </div>
                            @error('nik')
                                <span class="rc-error-msg show">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="rc-field">
                            <label class="rc-label" for="jenis_kelamin">Jenis Kelamin <span
                                    class="req">*</span></label>
                            <select id="jenis_kelamin" name="jenis_kelamin"
                                class="rc-select @error('jenis_kelamin') is-invalid @enderror" required>
                                <option value="">-- Pilih --</option>
                                <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>
                                    Laki-laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>
                                    Perempuan</option>
                            </select>
                            <div class="rc-error-msg" id="errJK">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                Pilih jenis kelamin
                            </div>
                            @error('jenis_kelamin')
                                <span class="rc-error-msg show">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="rc-field">
                        <label class="rc-label" for="telephone">No. Telepon <span class="req">*</span></label>
                        <input type="text" id="telephone" name="telephone"
                            class="rc-input @error('telephone') is-invalid @enderror" value="{{ old('telephone') }}"
                            required placeholder="Contoh: 081234567890" maxlength="15" inputmode="numeric"
                            autocomplete="tel">
                        <span class="rc-hint">Nomor telepon aktif (10–15 digit)</span>
                        <div class="rc-error-msg" id="errTelp">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            Nomor telepon harus 10–15 digit angka
                        </div>
                        @error('telephone')
                            <span class="rc-error-msg show">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="rc-field">
                        <label class="rc-label" for="alamat_lengkap">Alamat Lengkap <span class="req">*</span></label>
                        <textarea id="alamat_lengkap" name="alamat_lengkap" class="rc-textarea @error('alamat_lengkap') is-invalid @enderror"
                            required placeholder="Masukkan alamat lengkap sesuai KTP" maxlength="500">{{ old('alamat_lengkap') }}</textarea>
                        <div class="rc-field-footer">
                            <span class="rc-hint" style="margin-top:0;">Minimal 10 karakter</span>
                            <span class="rc-char-count" id="ccAlamat">0/500</span>
                        </div>
                        <div class="rc-error-msg" id="errAlamat">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            Alamat lengkap wajib diisi (minimal 10 karakter)
                        </div>
                        @error('alamat_lengkap')
                            <span class="rc-error-msg show">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="rc-row2">
                        <div class="rc-field">
                            <label class="rc-label" for="pendidikan_terakhir">Pendidikan Terakhir <span
                                    class="req">*</span></label>
                            <select id="pendidikan_terakhir" name="pendidikan_terakhir"
                                class="rc-select @error('pendidikan_terakhir') is-invalid @enderror" required>
                                <option value="">-- Pilih --</option>
                                @php
                                    $pendidikanList = [
                                        'SD / Paket A / Sederajat',
                                        'SMP / Paket B / Sederajat',
                                        'SMA / SMK / Paket C / Sederajat',
                                        'D1',
                                        'D2',
                                        'D3',
                                        'S1',
                                        'S2',
                                        'S3',
                                    ];
                                @endphp
                                @foreach ($pendidikanList as $pendidikan)
                                    <option value="{{ $pendidikan }}"
                                        {{ old('pendidikan_terakhir') == $pendidikan ? 'selected' : '' }}>
                                        {{ $pendidikan }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="rc-error-msg" id="errPendidikan">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                Pilih pendidikan terakhir
                            </div>
                            @error('pendidikan_terakhir')
                                <span class="rc-error-msg show">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="rc-field">
                            <label class="rc-label" for="rekomendasi">
                                Rekomendasi
                                <span
                                    style="color:#B0BCCE;font-weight:400;text-transform:none;letter-spacing:0;">(opsional)</span>
                            </label>
                            <select id="rekomendasi" name="rekomendasi"
                                class="rc-select @error('rekomendasi') is-invalid @enderror">
                                <option value="">-- Tidak ada --</option>
                                @if (isset($daftarRekomendasi) && $daftarRekomendasi->count())
                                    @foreach ($daftarRekomendasi as $rec)
                                        <option value="{{ $rec->nama_lengkap }}"
                                            {{ old('rekomendasi') == $rec->nama_lengkap ? 'selected' : '' }}>
                                            {{ $rec->nama_lengkap }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>Tidak ada data rekomendasi</option>
                                @endif
                            </select>
                            <span class="rc-hint">Jika tidak ada, kosongkan saja</span>
                            @error('rekomendasi')
                                <span class="rc-error-msg show">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="rc-field">
                        <label class="rc-label" for="pengalaman">Pengalaman Kerja <span class="req">*</span></label>
                        <textarea id="pengalaman" name="pengalaman" class="rc-textarea @error('pengalaman') is-invalid @enderror" required
                            placeholder="Jelaskan pengalaman kerja Anda yang relevan" maxlength="1000" style="min-height:100px;">{{ old('pengalaman') }}</textarea>
                        <div class="rc-field-footer">
                            <span class="rc-hint" style="margin-top:0;">Minimal 20 karakter</span>
                            <span class="rc-char-count" id="ccPengalaman">0/1000</span>
                        </div>
                        <div class="rc-error-msg" id="errPengalaman">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            Pengalaman kerja wajib diisi (minimal 20 karakter)
                        </div>
                        @error('pengalaman')
                            <span class="rc-error-msg show">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- ───────────────────────────── --}}
                    {{-- SECTION 3: DOKUMEN --}}
                    {{-- ───────────────────────────── --}}
                    <div class="rc-section-title" style="margin-top:1.5rem;">Upload Dokumen</div>

                    <div class="rc-row2">
                        <div class="rc-field">
                            <label class="rc-label" for="foto_diri">Foto Diri (3×4) <span class="req">*</span></label>
                            <div class="rc-file-zone @error('foto_diri') is-invalid @enderror" id="zoneFotoDiri">
                                <input type="file" id="foto_diri" name="foto_diri"
                                    accept="image/jpeg,image/jpg,image/png" required>
                                <div class="rc-file-label" id="lblFotoDiri">
                                    <strong>Klik atau seret file</strong>
                                    <small>JPG, PNG · maks 10MB</small>
                                </div>
                            </div>
                            <div class="rc-error-msg" id="errFotoDiri">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <span id="msgFotoDiri">Foto diri wajib diupload</span>
                            </div>
                            @error('foto_diri')
                                <span class="rc-error-msg show">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="rc-field">
                            <label class="rc-label" for="foto_ktp">Foto KTP <span class="req">*</span></label>
                            <div class="rc-file-zone @error('foto_ktp') is-invalid @enderror" id="zoneFotoKTP">
                                <input type="file" id="foto_ktp" name="foto_ktp"
                                    accept="image/jpeg,image/jpg,image/png" required>
                                <div class="rc-file-label" id="lblFotoKTP">
                                    <strong>Klik atau seret file</strong>
                                    <small>JPG, PNG · maks 10MB</small>
                                </div>
                            </div>
                            <div class="rc-error-msg" id="errFotoKTP">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <span id="msgFotoKTP">Foto KTP wajib diupload</span>
                            </div>
                            @error('foto_ktp')
                                <span class="rc-error-msg show">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="rc-field">
                        <label class="rc-label" for="foto_ijasah">Foto Ijazah <span class="req">*</span></label>
                        <div class="rc-file-zone @error('foto_ijasah') is-invalid @enderror" id="zoneFotoIjazah">
                            <input type="file" id="foto_ijasah" name="foto_ijasah"
                                accept="image/jpeg,image/jpg,image/png,application/pdf" required>
                            <div class="rc-file-label" id="lblFotoIjazah">
                                <strong>Klik atau seret file</strong>
                                <small>JPG, PNG, PDF · maks 10MB</small>
                            </div>
                        </div>
                        <div class="rc-error-msg" id="errFotoIjazah">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            <span id="msgFotoIjazah">Foto ijazah wajib diupload</span>
                        </div>
                        @error('foto_ijasah')
                            <span class="rc-error-msg show">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- ───────────────────────────── --}}
                    {{-- SECTION 4: PAKTA INTEGRITAS --}}
                    {{-- ───────────────────────────── --}}
                    <div class="rc-section-title" style="margin-top:1.5rem;">Pakta Integritas</div>

                    <div class="rc-field">
                        <label class="rc-label">Unduh Template</label>
                        <div class="rc-dl-group">
                            <a href="{{ asset('assets/files/pakta-integritas-pendamping.docx') }}" download
                                class="rc-dl-btn pendamping" id="btnDownloadPendamping">
                                <svg viewBox="0 0 24 24">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                    <polyline points="7 10 12 15 17 10" />
                                    <line x1="12" y1="15" x2="12" y2="3" />
                                </svg>
                                Pakta — Pendamping
                            </a>
                            <a href="{{ asset('assets/files/pakta-integritas-data-entry.docx') }}" download
                                class="rc-dl-btn dataentry" id="btnDownloadDataEntry">
                                <svg viewBox="0 0 24 24">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                    <polyline points="7 10 12 15 17 10" />
                                    <line x1="12" y1="15" x2="12" y2="3" />
                                </svg>
                                Pakta — Data Entry
                            </a>
                            <a href="{{ asset('assets/files/pakta-integritas-admin-umum.docx') }}" download
                                class="rc-dl-btn adminumum" id="btnDownloadAdminUmum">
                                <svg viewBox="0 0 24 24">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                    <polyline points="7 10 12 15 17 10" />
                                    <line x1="12" y1="15" x2="12" y2="3" />
                                </svg>
                                Pakta — Admin Umum
                            </a>
                        </div>
                        <span class="rc-hint">Unduh sesuai posisi, tanda tangani, lalu upload di bawah</span>
                    </div>

                    <div class="rc-field">
                        <label class="rc-label" for="pakta_integritas">
                            Upload Pakta Integritas (sudah ditandatangani) <span class="req">*</span>
                        </label>
                        <div class="rc-file-zone @error('pakta_integritas') is-invalid @enderror" id="zonePakta">
                            <input type="file" id="pakta_integritas" name="pakta_integritas"
                                accept="image/jpeg,image/jpg,image/png,application/pdf" required>
                            <div class="rc-file-label" id="lblPakta">
                                <strong>Klik atau seret file</strong>
                                <small>JPG, PNG, PDF · maks 10MB</small>
                            </div>
                        </div>
                        <div class="rc-error-msg" id="errPakta">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            <span id="msgPakta">Pakta integritas wajib diupload</span>
                        </div>
                        @error('pakta_integritas')
                            <span class="rc-error-msg show">{{ $message }}</span>
                        @enderror
                    </div>

                    <input type="hidden" name="status" value="Melamar">

                    {{-- Submit --}}
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

            const MAX_FILE = 10 * 1024 * 1024;
            const IMG_TYPES = ['image/jpeg', 'image/jpg', 'image/png'];
            const MIX_TYPES = [...IMG_TYPES, 'application/pdf'];

            function $(id) {
                return document.getElementById(id);
            }

            /* ── Track uploaded files ── */
            const uploadedFiles = {
                foto_diri: null,
                foto_ktp: null,
                foto_ijasah: null,
                pakta_integritas: null,
            };

            /* ── Progress tracking ── */
            const TOTAL_FIELDS =
            12; // recruit_type, nama, nik, jk, telp, alamat, pendidikan, pengalaman, foto_diri, foto_ktp, ijazah, pakta

            function calcProgress() {
                let done = 0;
                if (document.querySelector('input[name="recruit_type"]:checked')) done++;
                if ($('nama_lengkap')?.value.trim().length >= 3) done++;
                if (/^\d{16}$/.test($('nik')?.value || '')) done++;
                if ($('jenis_kelamin')?.value) done++;
                const t = $('telephone')?.value || '';
                if (t.length >= 10 && t.length <= 15) done++;
                if ($('alamat_lengkap')?.value.trim().length >= 10) done++;
                if ($('pendidikan_terakhir')?.value) done++;
                if ($('pengalaman')?.value.trim().length >= 20) done++;
                if (uploadedFiles.foto_diri) done++;
                if (uploadedFiles.foto_ktp) done++;
                if (uploadedFiles.foto_ijasah) done++;
                if (uploadedFiles.pakta_integritas) done++;
                return Math.round((done / TOTAL_FIELDS) * 100);
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

            /* ── Radio: Posisi ── */
            function syncRadioStyles() {
                document.querySelectorAll('input[name="recruit_type"]').forEach(radio => {
                    const opt = radio.closest('.rc-radio-option');
                    if (!opt) return;
                    opt.classList.toggle('selected', radio.checked);
                });
            }

            function handleRecruitTypeChange() {
                syncRadioStyles();
                const sel = document.querySelector('input[name="recruit_type"]:checked');
                const isDE = sel && sel.value === 'DATA ENTRY';
                const wrapper = $('typeEntryWrapper');
                if (wrapper) wrapper.style.display = isDE ? 'block' : 'none';
                if (!isDE && $('type_entry')) {
                    $('type_entry').value = '';
                    $('alertOSS').style.display = 'none';
                    $('alertSIHALAL').style.display = 'none';
                }
                showErr('errPosisi', false);

                // Highlight download buttons
                const bp = $('btnDownloadPendamping');
                const bd = $('btnDownloadDataEntry');
                const ba = $('btnDownloadAdminUmum');
                if (bp) bp.className = 'rc-dl-btn pendamping' + (sel?.value === 'PENDAMPING' ? ' active' : '');
                if (bd) bd.className = 'rc-dl-btn dataentry' + (sel?.value === 'DATA ENTRY' ? ' active' : '');
                if (ba) ba.className = 'rc-dl-btn adminumum' + (sel?.value === 'ADMIN UMUM' ? ' active' : '');
                updateProgress();
            }

            document.querySelectorAll('input[name="recruit_type"]').forEach(r =>
                r.addEventListener('change', handleRecruitTypeChange)
            );

            /* ── Type Entry ── */
            $('type_entry')?.addEventListener('change', function() {
                const v = this.value;
                $('alertOSS').style.display = v === 'OSS' ? 'flex' : 'none';
                $('alertSIHALAL').style.display = v === 'SIHALAL' ? 'flex' : 'none';
                setFieldState(this, !!v);
                showErr('errTypeEntry', !v);
                updateProgress();
            });

            /* ── Nama Lengkap: uppercase + live validation ── */
            $('nama_lengkap')?.addEventListener('input', function() {
                const pos = this.selectionStart;
                this.value = this.value.toUpperCase();
                try {
                    this.setSelectionRange(pos, pos);
                } catch (_) {}
                const valid = this.value.trim().length >= 3;
                setFieldState(this, valid);
                showErr('errNama', !valid && this.value.length > 0);
                updateProgress();
            });

            /* ── NIK: digits only ── */
            $('nik')?.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 16);
                const valid = /^\d{16}$/.test(this.value);
                setFieldState(this, valid);
                showErr('errNik', this.value.length > 0 && !valid);
                updateProgress();
            });
            $('nik')?.addEventListener('keypress', e => {
                if (!/\d/.test(e.key)) e.preventDefault();
            });
            $('nik')?.addEventListener('paste', e => {
                e.preventDefault();
                $('nik').value = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '')
                    .slice(0, 16);
                $('nik').dispatchEvent(new Event('input'));
            });

            /* ── Jenis Kelamin ── */
            $('jenis_kelamin')?.addEventListener('change', function() {
                setFieldState(this, !!this.value);
                showErr('errJK', !this.value);
                updateProgress();
            });

            /* ── Telephone: digits only ── */
            $('telephone')?.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 15);
                const valid = this.value.length >= 10 && this.value.length <= 15;
                setFieldState(this, valid);
                showErr('errTelp', this.value.length > 0 && !valid);
                updateProgress();
            });
            $('telephone')?.addEventListener('keypress', e => {
                if (!/\d/.test(e.key)) e.preventDefault();
            });

            /* ── Alamat: char counter + validation ── */
            $('alamat_lengkap')?.addEventListener('input', function() {
                const len = this.value.length;
                const cc = $('ccAlamat');
                if (cc) {
                    cc.textContent = len + '/500';
                    cc.className = 'rc-char-count' + (len > 450 ? ' warn' : '');
                }
                const valid = this.value.trim().length >= 10;
                setFieldState(this, valid);
                showErr('errAlamat', this.value.length > 0 && !valid);
                updateProgress();
            });

            /* ── Pendidikan ── */
            $('pendidikan_terakhir')?.addEventListener('change', function() {
                setFieldState(this, !!this.value);
                showErr('errPendidikan', !this.value);
                updateProgress();
            });

            /* ── Pengalaman: char counter + validation ── */
            $('pengalaman')?.addEventListener('input', function() {
                const len = this.value.length;
                const cc = $('ccPengalaman');
                if (cc) {
                    cc.textContent = len + '/1000';
                    cc.className = 'rc-char-count' + (len > 900 ? ' warn' : '');
                }
                const valid = this.value.trim().length >= 20;
                setFieldState(this, valid);
                showErr('errPengalaman', this.value.length > 0 && !valid);
                updateProgress();
            });

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
                        $(msgId).textContent = 'Ukuran file "' + label + '" maksimal 10MB!';
                        showErr(errId, true);
                        zone.classList.add('is-invalid');
                        zone.classList.remove('has-file');
                        uploadedFiles[inputId] = null;
                        updateProgress();
                        toast('error', 'Ukuran file "' + label + '" maksimal 10MB!');
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

            setupFileZone({
                zoneId: 'zoneFotoDiri',
                inputId: 'foto_diri',
                lblId: 'lblFotoDiri',
                errId: 'errFotoDiri',
                msgId: 'msgFotoDiri',
                allowedTypes: IMG_TYPES,
                label: 'Foto Diri'
            });
            setupFileZone({
                zoneId: 'zoneFotoKTP',
                inputId: 'foto_ktp',
                lblId: 'lblFotoKTP',
                errId: 'errFotoKTP',
                msgId: 'msgFotoKTP',
                allowedTypes: IMG_TYPES,
                label: 'Foto KTP'
            });
            setupFileZone({
                zoneId: 'zoneFotoIjazah',
                inputId: 'foto_ijasah',
                lblId: 'lblFotoIjazah',
                errId: 'errFotoIjazah',
                msgId: 'msgFotoIjazah',
                allowedTypes: MIX_TYPES,
                label: 'Foto Ijazah'
            });
            setupFileZone({
                zoneId: 'zonePakta',
                inputId: 'pakta_integritas',
                lblId: 'lblPakta',
                errId: 'errPakta',
                msgId: 'msgPakta',
                allowedTypes: MIX_TYPES,
                label: 'Pakta Integritas'
            });

            /* ── Form submit ── */
            $('formRecruitment')?.addEventListener('submit', function(e) {
                let valid = true;
                let firstErr = null;

                function fail(fieldId, errId, focusEl) {
                    showErr(errId, true);
                    const fieldEl = $(fieldId);
                    if (fieldEl) setFieldState(fieldEl, false);
                    if (!firstErr) firstErr = focusEl || fieldEl;
                    valid = false;
                }

                // Posisi
                const selRec = document.querySelector('input[name="recruit_type"]:checked');
                if (!selRec) {
                    showErr('errPosisi', true);
                    if (!firstErr) firstErr = $('lblPendamping');
                    valid = false;
                }

                // Tipe entry (only if DATA ENTRY)
                if (selRec?.value === 'DATA ENTRY' && !$('type_entry').value) {
                    fail('type_entry', 'errTypeEntry');
                }

                // Nama
                if (!$('nama_lengkap').value.trim() || $('nama_lengkap').value.trim().length < 3) {
                    fail('nama_lengkap', 'errNama');
                }

                // NIK
                if (!/^\d{16}$/.test($('nik').value)) {
                    fail('nik', 'errNik');
                }

                // Jenis kelamin
                if (!$('jenis_kelamin').value) {
                    fail('jenis_kelamin', 'errJK');
                }

                // Telephone
                const tel = $('telephone').value;
                if (tel.length < 10 || tel.length > 15) {
                    fail('telephone', 'errTelp');
                }

                // Alamat
                if ($('alamat_lengkap').value.trim().length < 10) {
                    fail('alamat_lengkap', 'errAlamat');
                }

                // Pendidikan
                if (!$('pendidikan_terakhir').value) {
                    fail('pendidikan_terakhir', 'errPendidikan');
                }

                // Pengalaman
                if ($('pengalaman').value.trim().length < 20) {
                    fail('pengalaman', 'errPengalaman');
                }

                // Files
                const fileChecks = [{
                        key: 'foto_diri',
                        errId: 'errFotoDiri',
                        msgId: 'msgFotoDiri',
                        zoneId: 'zoneFotoDiri',
                        label: 'Foto diri'
                    },
                    {
                        key: 'foto_ktp',
                        errId: 'errFotoKTP',
                        msgId: 'msgFotoKTP',
                        zoneId: 'zoneFotoKTP',
                        label: 'Foto KTP'
                    },
                    {
                        key: 'foto_ijasah',
                        errId: 'errFotoIjazah',
                        msgId: 'msgFotoIjazah',
                        zoneId: 'zoneFotoIjazah',
                        label: 'Foto ijazah'
                    },
                    {
                        key: 'pakta_integritas',
                        errId: 'errPakta',
                        msgId: 'msgPakta',
                        zoneId: 'zonePakta',
                        label: 'Pakta integritas'
                    },
                ];

                fileChecks.forEach(fc => {
                    if (!uploadedFiles[fc.key]) {
                        $(fc.msgId).textContent = fc.label + ' wajib diupload';
                        showErr(fc.errId, true);
                        $(fc.zoneId).classList.add('is-invalid');
                        if (!firstErr) firstErr = $(fc.zoneId);
                        valid = false;
                    }
                });

                if (!valid) {
                    e.preventDefault();
                    scrollToFirstErr(firstErr);
                    toast('error', 'Mohon lengkapi semua data yang diperlukan!');
                    return;
                }

                // All valid — show loading state
                const btn = $('submitBtn');
                btn.disabled = true;
                $('rcSpinner').style.display = 'block';
                $('submitIcon').style.display = 'none';
                $('submitLabel').textContent = 'Mengirim...';
            });

            /* ── Restore button on back-navigation (bfcache) ── */
            window.addEventListener('pageshow', function(e) {
                if (!e.persisted) return;
                const btn = $('submitBtn');
                if (!btn) return;
                btn.disabled = false;
                $('rcSpinner').style.display = 'none';
                $('submitIcon').style.display = '';
                $('submitLabel').textContent = 'Kirim Lamaran';
            });

            /* ── Auto close session alerts after 5s ── */
            setTimeout(() => {
                [$('alertSuccess'), $('alertError')].forEach(el => {
                    if (!el) return;
                    el.style.transition = 'opacity 0.4s';
                    el.style.opacity = '0';
                    setTimeout(() => el.remove(), 400);
                });
            }, 5000);

            /* ── Init: populate char counters & progress from old() values ── */
            (function init() {
                const alamat = $('alamat_lengkap');
                if (alamat?.value) {
                    $('ccAlamat').textContent = alamat.value.length + '/500';
                }
                const pengalaman = $('pengalaman');
                if (pengalaman?.value) {
                    $('ccPengalaman').textContent = pengalaman.value.length + '/1000';
                }
                syncRadioStyles();
                updateProgress();
            })();

        })();
    </script>

@endsection
