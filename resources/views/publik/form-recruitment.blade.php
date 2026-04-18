@extends('layouts.guest')
@section('title', 'Form Recruitment')
@section('content')

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600&family=Sora:wght@400;600&display=swap');

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

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

        .rc-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
            animation: rcFloat 14s ease-in-out infinite;
        }

        .rc-orb-1 {
            width: 380px;
            height: 380px;
            background: rgba(130, 180, 255, 0.2);
            top: -80px;
            left: -80px;
            animation-delay: 0s;
        }

        .rc-orb-2 {
            width: 260px;
            height: 260px;
            background: rgba(100, 210, 180, 0.16);
            bottom: -60px;
            right: -60px;
            animation-delay: -6s;
        }

        @keyframes rcFloat {

            0%,
            100% {
                transform: translate(0, 0);
            }

            40% {
                transform: translate(16px, -16px);
            }

            70% {
                transform: translate(-10px, 10px);
            }
        }

        .rc-wrap {
            position: relative;
            z-index: 1;
            max-width: 1060px;
            margin: 0 auto;
            display: flex;
            gap: 1.5rem;
            align-items: flex-start;
        }

        /* ── LEFT PANEL (sticky) ── */
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

        /* ── RIGHT PANEL (form) ── */
        .rc-right {
            flex: 1;
            min-width: 0;
            background: #ffffff;
            border-radius: 20px;
            padding: 2.5rem 2.25rem;
            box-shadow:
                0 0 0 1px rgba(100, 140, 210, 0.12),
                0 24px 60px rgba(60, 100, 180, 0.1);
            animation: cardIn 0.55s cubic-bezier(0.16, 1, 0.3, 1) 0.08s both;
        }

        .rc-form-header {
            margin-bottom: 1.5rem;
        }

        .rc-form-header h2 {
            font-family: 'Sora', sans-serif;
            font-size: 20px;
            font-weight: 600;
            color: #0F1F40;
            margin-bottom: 4px;
        }

        .rc-form-header p {
            font-size: 13.5px;
            color: #8A99B3;
        }

        .rc-divider {
            height: 1px;
            background: #EDF0F7;
            margin-bottom: 1.75rem;
        }

        /* Section heading inside form */
        .rc-section-title {
            font-family: 'Sora', sans-serif;
            font-size: 13px;
            font-weight: 600;
            color: #1A5FC8;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .rc-section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #EDF0F7;
        }

        /* Alerts */
        .alert-success-modern {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #EBF9F5;
            border: 1px solid #A7DDD0;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 1.25rem;
            animation: slideDown 0.3s ease;
        }

        .alert-success-modern svg {
            width: 15px;
            height: 15px;
            stroke: #0F6E56;
            fill: none;
            stroke-width: 2;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .alert-success-modern .alt-text {
            font-size: 13px;
            color: #0A5240;
            flex: 1;
            line-height: 1.5;
        }

        .alert-danger-modern {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 1.25rem;
            animation: slideDown 0.3s ease;
        }

        .alert-danger-modern svg {
            width: 15px;
            height: 15px;
            stroke: #EF4444;
            fill: none;
            stroke-width: 2;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .alert-danger-modern .alt-text {
            font-size: 13px;
            color: #B91C1C;
            flex: 1;
            line-height: 1.5;
        }

        .alt-close {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            line-height: 1;
            color: inherit;
            opacity: 0.4;
            padding: 0;
        }

        .alt-close:hover {
            opacity: 0.8;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Field */
        .rc-field {
            margin-bottom: 1.1rem;
        }

        .rc-label {
            display: block;
            font-size: 11.5px;
            font-weight: 600;
            color: #6B7A99;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            margin-bottom: 6px;
        }

        .rc-label .req {
            color: #EF4444;
            margin-left: 2px;
        }

        .rc-input,
        .rc-select,
        .rc-textarea {
            width: 100%;
            background: #F5F7FB;
            border: 1px solid #E0E7F0;
            border-radius: 10px;
            font-size: 14px;
            color: #0F1F40;
            font-family: 'Plus Jakarta Sans', sans-serif;
            outline: none;
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
        }

        .rc-input,
        .rc-select {
            height: 44px;
            padding: 0 14px;
        }

        .rc-textarea {
            padding: 12px 14px;
            resize: vertical;
            min-height: 90px;
            line-height: 1.6;
        }

        .rc-input::placeholder,
        .rc-textarea::placeholder {
            color: #B0BCCE;
        }

        .rc-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23B0BCCE' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 36px;
            cursor: pointer;
        }

        .rc-input:focus,
        .rc-select:focus,
        .rc-textarea:focus {
            border-color: #1A5FC8;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(26, 95, 200, 0.1);
        }

        .rc-input.is-invalid,
        .rc-select.is-invalid,
        .rc-textarea.is-invalid {
            border-color: #FCA5A5;
            background: #FEF2F2;
        }

        .rc-hint {
            font-size: 11.5px;
            color: #B0BCCE;
            margin-top: 4px;
            display: block;
        }

        .rc-error {
            font-size: 12px;
            color: #EF4444;
            margin-top: 4px;
            display: block;
        }

        /* Radio group */
        .rc-radio-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .rc-radio-option {
            display: flex;
            align-items: center;
            gap: 9px;
            cursor: pointer;
        }

        .rc-radio-option input[type="radio"] {
            display: none;
        }

        .rc-radio-visual {
            width: 18px;
            height: 18px;
            border: 2px solid #C8D3E8;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.2s;
        }

        .rc-radio-visual::after {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #1A5FC8;
            opacity: 0;
            transform: scale(0);
            transition: all 0.2s;
        }

        .rc-radio-option input:checked+.rc-radio-visual {
            border-color: #1A5FC8;
        }

        .rc-radio-option input:checked+.rc-radio-visual::after {
            opacity: 1;
            transform: scale(1);
        }

        .rc-radio-label {
            font-size: 13.5px;
            color: #3A4A6B;
            font-weight: 500;
        }

        /* File input */
        .rc-file-input {
            width: 100%;
            background: #F5F7FB;
            border: 1.5px dashed #C8D3E8;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 13.5px;
            color: #6B7A99;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
        }

        .rc-file-input:hover {
            border-color: #1A5FC8;
            background: #EEF4FF;
        }

        .rc-file-input:focus {
            outline: none;
            border-color: #1A5FC8;
            box-shadow: 0 0 0 3px rgba(26, 95, 200, 0.1);
        }

        .rc-file-input.is-invalid {
            border-color: #FCA5A5;
            background: #FEF2F2;
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

        .rc-dl-btn.pendamping:hover {
            background: #1A5FC8;
            color: #fff;
            border-color: #1A5FC8;
        }

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

        .rc-dl-btn.dataentry:hover {
            background: #3A4A6B;
            color: #fff;
            border-color: #3A4A6B;
        }

        .rc-dl-btn.dataentry.active {
            background: #3A4A6B;
            color: #fff;
            border-color: #3A4A6B;
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
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
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

                    {{-- ──────────────────────────────────────── --}}
                    {{-- SECTION 1: POSISI --}}
                    {{-- ──────────────────────────────────────── --}}
                    <div class="rc-section-title">Posisi Dilamar</div>

                    <div class="rc-field">
                        <label class="rc-label">Pilih Posisi <span class="req">*</span></label>
                        <div class="rc-radio-group">
                            <label class="rc-radio-option">
                                <input type="radio" name="recruit_type" id="type_pendamping" value="PENDAMPING" required>
                                <span class="rc-radio-visual"></span>
                                <span class="rc-radio-label">Pendamping</span>
                            </label>
                            <label class="rc-radio-option">
                                <input type="radio" name="recruit_type" id="type_data_entry" value="DATA ENTRY">
                                <span class="rc-radio-visual"></span>
                                <span class="rc-radio-label">Data Entry</span>
                            </label>
                        </div>
                        @error('recruit_type')
                            <span class="rc-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Tipe Entry (conditional) --}}
                    <div class="rc-field d-none" id="typeEntryWrapper">
                        <label class="rc-label" for="type_entry">Tipe Entry <span class="req">*</span></label>
                        <select id="type_entry" name="type_entry"
                            class="rc-select @error('type_entry') is-invalid @enderror">
                            <option value="">-- Pilih Tipe Entry --</option>
                            <option value="OSS">OSS</option>
                            <option value="SIHALAL">SIHALAL</option>
                        </select>
                        @error('type_entry')
                            <span class="rc-error">{{ $message }}</span>
                        @enderror

                        <div id="alertOSS" class="rc-fee-box oss d-none">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            <div><strong>Fee OSS: Rp100.000</strong><br>
                                <small>Per 15 data yang berhasil diproses. Pahami alur kerja OSS sebelum mendaftar.</small>
                            </div>
                        </div>
                        <div id="alertSIHALAL" class="rc-fee-box sihalal d-none">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            <div><strong>Fee SIHALAL: Rp150.000</strong><br>
                                <small>Per 15 data yang berhasil diproses. Pahami alur kerja SIHALAL sebelum
                                    mendaftar.</small>
                            </div>
                        </div>
                    </div>

                    {{-- ──────────────────────────────────────── --}}
                    {{-- SECTION 2: DATA DIRI --}}
                    {{-- ──────────────────────────────────────── --}}
                    <div class="rc-section-title" style="margin-top:1.5rem;">Data Diri</div>

                    <div class="rc-field">
                        <label class="rc-label" for="nama_lengkap">Nama Lengkap <span class="req">*</span></label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap"
                            class="rc-input @error('nama_lengkap') is-invalid @enderror" required autofocus
                            placeholder="Masukkan nama lengkap" autocomplete="name" maxlength="255"
                            style="text-transform:uppercase;">
                        <span class="rc-hint">Nama akan otomatis diubah ke huruf besar</span>
                        @error('nama_lengkap')
                            <span class="rc-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="rc-field">
                        <label class="rc-label" for="nik">NIK <span class="req">*</span></label>
                        <input type="text" id="nik" name="nik"
                            class="rc-input @error('nik') is-invalid @enderror" required
                            placeholder="Masukkan 16 digit NIK" maxlength="16" minlength="16" inputmode="numeric"
                            pattern="\d{16}" autocomplete="off">
                        <span class="rc-hint">Sesuai KTP, 16 digit angka</span>
                        @error('nik')
                            <span class="rc-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="rc-field">
                        <label class="rc-label" for="jenis_kelamin">Jenis Kelamin <span class="req">*</span></label>
                        <select id="jenis_kelamin" name="jenis_kelamin"
                            class="rc-select @error('jenis_kelamin') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <span class="rc-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="rc-field">
                        <label class="rc-label" for="telephone">No. Telepon <span class="req">*</span></label>
                        <input type="text" id="telephone" name="telephone"
                            class="rc-input @error('telephone') is-invalid @enderror" required
                            placeholder="Contoh: 081234567890" maxlength="15" inputmode="numeric" autocomplete="tel">
                        <span class="rc-hint">Nomor telepon aktif (10–15 digit)</span>
                        @error('telephone')
                            <span class="rc-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="rc-field">
                        <label class="rc-label" for="alamat_lengkap">Alamat Lengkap <span class="req">*</span></label>
                        <textarea id="alamat_lengkap" name="alamat_lengkap" class="rc-textarea @error('alamat_lengkap') is-invalid @enderror"
                            required placeholder="Masukkan alamat lengkap" maxlength="500"></textarea>
                        @error('alamat_lengkap')
                            <span class="rc-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="rc-field">
                        <label class="rc-label" for="pendidikan_terakhir">Pendidikan Terakhir <span
                                class="req">*</span></label>
                        <select id="pendidikan_terakhir" name="pendidikan_terakhir"
                            class="rc-select @error('pendidikan_terakhir') is-invalid @enderror" required>
                            <option value="">-- Pilih Pendidikan Terakhir --</option>
                            @php $pendidikanList = ['SD / Paket A / Sederajat','SMP / Paket B / Sederajat','SMA / SMK / Paket C / Sederajat','D1','D2','D3','S1','S2','S3']; @endphp
                            @foreach ($pendidikanList as $pendidikan)
                                <option value="{{ $pendidikan }}">{{ $pendidikan }}</option>
                            @endforeach
                        </select>
                        @error('pendidikan_terakhir')
                            <span class="rc-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="rc-field">
                        <label class="rc-label" for="pengalaman">Pengalaman Kerja <span class="req">*</span></label>
                        <textarea id="pengalaman" name="pengalaman" class="rc-textarea @error('pengalaman') is-invalid @enderror" required
                            placeholder="Jelaskan pengalaman kerja Anda yang relevan" maxlength="1000"></textarea>
                        <span class="rc-hint">Tuliskan pengalaman kerja yang relevan dengan posisi yang dilamar</span>
                        @error('pengalaman')
                            <span class="rc-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="rc-field">
                        <label class="rc-label" for="rekomendasi">Rekomendasi <span
                                style="color:#B0BCCE;font-weight:400;text-transform:none;letter-spacing:0;">(opsional)</span></label>
                        <select id="rekomendasi" name="rekomendasi"
                            class="rc-select @error('rekomendasi') is-invalid @enderror">
                            <option value="">-- Pilih Rekomendasi (Opsional) --</option>
                            @if (isset($daftarRekomendasi) && $daftarRekomendasi->count())
                                @foreach ($daftarRekomendasi as $rekomendasi)
                                    <option value="{{ $rekomendasi->nama_lengkap }}">{{ $rekomendasi->nama_lengkap }}
                                    </option>
                                @endforeach
                            @else
                                <option value="" disabled>Tidak ada data rekomendasi</option>
                            @endif
                        </select>
                        <span class="rc-hint">Jika tidak ada, kosongkan saja</span>
                        @error('rekomendasi')
                            <span class="rc-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- ──────────────────────────────────────── --}}
                    {{-- SECTION 3: DOKUMEN --}}
                    {{-- ──────────────────────────────────────── --}}
                    <div class="rc-section-title" style="margin-top:1.5rem;">Upload Dokumen</div>

                    <div class="rc-field">
                        <label class="rc-label" for="foto_diri">Foto Diri (3×4) <span class="req">*</span></label>
                        <input type="file" id="foto_diri" name="foto_diri"
                            class="rc-file-input @error('foto_diri') is-invalid @enderror"
                            accept="image/jpeg,image/jpg,image/png" required>
                        <span class="rc-hint">Format: JPG, PNG, JPEG. Maks: 10MB</span>
                        @error('foto_diri')
                            <span class="rc-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="rc-field">
                        <label class="rc-label" for="foto_ktp">Foto KTP <span class="req">*</span></label>
                        <input type="file" id="foto_ktp" name="foto_ktp"
                            class="rc-file-input @error('foto_ktp') is-invalid @enderror"
                            accept="image/jpeg,image/jpg,image/png" required>
                        <span class="rc-hint">Format: JPG, PNG, JPEG. Maks: 10MB</span>
                        @error('foto_ktp')
                            <span class="rc-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="rc-field">
                        <label class="rc-label" for="foto_ijasah">Foto Ijazah <span class="req">*</span></label>
                        <input type="file" id="foto_ijasah" name="foto_ijasah"
                            class="rc-file-input @error('foto_ijasah') is-invalid @enderror"
                            accept="image/jpeg,image/jpg,image/png,application/pdf" required>
                        <span class="rc-hint">Format: JPG, PNG, JPEG, PDF. Maks: 10MB</span>
                        @error('foto_ijasah')
                            <span class="rc-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- ──────────────────────────────────────── --}}
                    {{-- SECTION 4: PAKTA INTEGRITAS --}}
                    {{-- ──────────────────────────────────────── --}}
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
                        </div>
                        <span class="rc-hint">Unduh sesuai tipe rekrutmen, tanda tangani, lalu upload di bawah</span>
                    </div>

                    <div class="rc-field">
                        <label class="rc-label" for="pakta_integritas">Upload Pakta Integritas (sudah ditandatangani)
                            <span class="req">*</span></label>
                        <input type="file" id="pakta_integritas" name="pakta_integritas"
                            class="rc-file-input @error('pakta_integritas') is-invalid @enderror"
                            accept="image/jpeg,image/jpg,image/png,application/pdf" required>
                        <span class="rc-hint">Format: JPG, PNG, JPEG, PDF. Maks: 10MB</span>
                        @error('pakta_integritas')
                            <span class="rc-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <input type="hidden" name="status" value="Melamar">

                    {{-- Submit --}}
                    <div style="margin-top:2rem;">
                        <button class="rc-submit" type="submit" id="submitBtn">
                            <svg viewBox="0 0 24 24">
                                <line x1="22" y1="2" x2="11" y2="13" />
                                <polygon points="22 2 15 22 11 13 2 9 22 2" />
                            </svg>
                            Kirim Lamaran
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

            const el = {
                form: $('formRecruitment'),
                submitBtn: $('submitBtn'),
                namaLengkap: $('nama_lengkap'),
                nik: $('nik'),
                telephone: $('telephone'),
                typeEntryWrapper: $('typeEntryWrapper'),
                typeEntrySelect: $('type_entry'),
                alertOSS: $('alertOSS'),
                alertSIHALAL: $('alertSIHALAL'),
                btnPendamping: $('btnDownloadPendamping'),
                btnDataEntry: $('btnDownloadDataEntry'),
                recruitTypeInputs: document.querySelectorAll('input[name="recruit_type"]'),
            };

            function show(node) {
                if (node) node.classList.remove('d-none');
            }

            function hide(node) {
                if (node) node.classList.add('d-none');
            }

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

            function focusInvalid(node) {
                if (!node) return;
                node.classList.add('is-invalid');
                node.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                node.focus();
            }

            /* ── Recruit type ── */
            function updateRecruitType() {
                const sel = document.querySelector('input[name="recruit_type"]:checked');
                const isDE = sel && sel.value === 'DATA ENTRY';

                if (isDE) {
                    show(el.typeEntryWrapper);
                    el.typeEntrySelect.setAttribute('required', '');
                } else {
                    hide(el.typeEntryWrapper);
                    el.typeEntrySelect.removeAttribute('required');
                    el.typeEntrySelect.value = '';
                    hide(el.alertOSS);
                    hide(el.alertSIHALAL);
                }

                if (el.btnPendamping && el.btnDataEntry) {
                    if (!sel) {
                        el.btnPendamping.className = 'rc-dl-btn pendamping';
                        el.btnDataEntry.className = 'rc-dl-btn dataentry';
                    } else if (sel.value === 'PENDAMPING') {
                        el.btnPendamping.className = 'rc-dl-btn pendamping active';
                        el.btnDataEntry.className = 'rc-dl-btn dataentry';
                    } else {
                        el.btnPendamping.className = 'rc-dl-btn pendamping';
                        el.btnDataEntry.className = 'rc-dl-btn dataentry active';
                    }
                }
            }

            function updateFeeAlert() {
                const v = el.typeEntrySelect ? el.typeEntrySelect.value : '';
                if (v === 'OSS') {
                    show(el.alertOSS);
                    hide(el.alertSIHALAL);
                } else if (v === 'SIHALAL') {
                    hide(el.alertOSS);
                    show(el.alertSIHALAL);
                } else {
                    hide(el.alertOSS);
                    hide(el.alertSIHALAL);
                }
            }

            el.recruitTypeInputs.forEach(i => i.addEventListener('change', updateRecruitType));
            if (el.typeEntrySelect) el.typeEntrySelect.addEventListener('change', updateFeeAlert);
            updateRecruitType();

            /* ── Nama uppercase ── */
            if (el.namaLengkap) {
                el.namaLengkap.addEventListener('input', function() {
                    const p = this.selectionStart;
                    this.value = this.value.toUpperCase();
                    try {
                        this.setSelectionRange(p, p);
                    } catch (_) {}
                });
            }

            /* ── NIK digits only ── */
            if (el.nik) {
                el.nik.addEventListener('input', function() {
                    this.value = this.value.replace(/\D/g, '').slice(0, 16);
                });
                el.nik.addEventListener('keypress', e => {
                    if (!/\d/.test(e.key)) e.preventDefault();
                });
                el.nik.addEventListener('paste', e => {
                    e.preventDefault();
                    el.nik.value = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '')
                        .slice(0, 16);
                });
            }

            /* ── Telephone digits only ── */
            if (el.telephone) {
                el.telephone.addEventListener('input', function() {
                    this.value = this.value.replace(/\D/g, '').slice(0, 15);
                });
                el.telephone.addEventListener('keypress', e => {
                    if (!/\d/.test(e.key)) e.preventDefault();
                });
            }

            /* ── File validation ── */
            function bindFile(id, types, label) {
                const inp = $(id);
                if (!inp) return;
                inp.addEventListener('change', function() {
                    const f = this.files[0];
                    if (!f) return;
                    if (f.size > MAX_FILE) {
                        toast('error', `Ukuran file "${label}" maksimal 10MB!`);
                        this.value = '';
                        this.classList.add('is-invalid');
                        return;
                    }
                    if (!types.includes(f.type)) {
                        toast('error', `Format file "${label}" tidak valid!`);
                        this.value = '';
                        this.classList.add('is-invalid');
                        return;
                    }
                    this.classList.remove('is-invalid');
                });
            }
            bindFile('foto_diri', IMG_TYPES, 'Foto Diri');
            bindFile('foto_ktp', IMG_TYPES, 'Foto KTP');
            bindFile('foto_ijasah', MIX_TYPES, 'Foto Ijazah');
            bindFile('pakta_integritas', MIX_TYPES, 'Pakta Integritas');

            /* ── Live validation reset ── */
            el.form?.querySelectorAll('.rc-input,.rc-select,.rc-textarea,.rc-file-input').forEach(inp => {
                inp.addEventListener('input', function() {
                    if (this.value) this.classList.remove('is-invalid');
                });
                inp.addEventListener('change', function() {
                    if (this.value) this.classList.remove('is-invalid');
                });
            });

            /* ── Form submit ── */
            if (el.form) {
                el.form.addEventListener('submit', function(e) {
                    const selRec = document.querySelector('input[name="recruit_type"]:checked');
                    if (!selRec) {
                        toast('error', 'Silakan pilih Posisi Dilamar!');
                        e.preventDefault();
                        return;
                    }
                    if (selRec.value === 'DATA ENTRY' && !el.typeEntrySelect.value) {
                        focusInvalid(el.typeEntrySelect);
                        toast('error', 'Silakan pilih Tipe Entry!');
                        e.preventDefault();
                        return;
                    }
                    const nikVal = el.nik ? el.nik.value.trim() : '';
                    if (!/^\d{16}$/.test(nikVal)) {
                        focusInvalid(el.nik);
                        toast('error', 'NIK harus tepat 16 digit angka!');
                        e.preventDefault();
                        return;
                    }
                    const tel = el.telephone ? el.telephone.value : '';
                    if (tel.length < 10 || tel.length > 15) {
                        focusInvalid(el.telephone);
                        toast('error', 'Nomor telepon harus antara 10–15 digit!');
                        e.preventDefault();
                        return;
                    }

                    el.submitBtn.disabled = true;
                    el.submitBtn.innerHTML = '<div class="rc-spinner"></div> Mengirim...';
                });
            }

            /* ── Page cache reset ── */
            window.addEventListener('pageshow', function(e) {
                if (e.persisted && el.submitBtn) {
                    el.submitBtn.disabled = false;
                    el.submitBtn.innerHTML =
                        '<svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="rgba(255,255,255,0.85)" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg> Kirim Lamaran';
                }
            });

            /* ── Auto close alerts ── */
            setTimeout(() => {
                [$('alertSuccess'), $('alertError')].forEach(el => {
                    if (el) el.style.transition = 'opacity 0.4s';
                    if (el) el.style.opacity = '0';
                    setTimeout(() => el?.remove(), 400);
                });
            }, 5000);

        })();
    </script>

@endsection
