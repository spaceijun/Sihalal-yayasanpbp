@extends('layouts.guest')
@section('title', 'Form Data Lapangan')
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

        .fh-root {
            min-height: 100vh;
            background-color: #EEF3FA;
            background-image:
                radial-gradient(ellipse 70% 50% at 10% 5%, rgba(180, 210, 255, 0.5) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 90% 90%, rgba(160, 220, 200, 0.25) 0%, transparent 55%);
            font-family: 'Plus Jakarta Sans', sans-serif;
            padding: 2rem 1rem 3rem;
            position: relative;
        }

        .fh-root::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: radial-gradient(circle, rgba(100, 140, 200, 0.1) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
            z-index: 0;
        }

        .fh-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
            animation: fhFloat 14s ease-in-out infinite;
        }

        .fh-orb-1 {
            width: 380px;
            height: 380px;
            background: rgba(130, 180, 255, 0.2);
            top: -80px;
            left: -80px;
        }

        .fh-orb-2 {
            width: 260px;
            height: 260px;
            background: rgba(100, 210, 180, 0.16);
            bottom: -60px;
            right: -60px;
            animation-delay: -6s;
        }

        @keyframes fhFloat {

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

        .fh-wrap {
            position: relative;
            z-index: 1;
            max-width: 1060px;
            margin: 0 auto;
            display: flex;
            gap: 1.5rem;
            align-items: flex-start;
        }

        /* ── LEFT PANEL ── */
        .fh-left {
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

        .fh-left::before {
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

        .fh-left::after {
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

        .fh-brand {
            display: flex;
            align-items: center;
            gap: 11px;
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
        }

        .fh-brand-icon {
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

        .fh-brand-icon svg {
            width: 20px;
            height: 20px;
        }

        .fh-brand-name {
            font-family: 'Sora', sans-serif;
            font-size: 12.5px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.95);
            line-height: 1.4;
        }

        .fh-brand-name small {
            display: block;
            font-size: 11px;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.42);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .fh-left-title {
            font-family: 'Sora', sans-serif;
            font-size: 20px;
            font-weight: 600;
            color: #fff;
            line-height: 1.4;
            margin-bottom: 0.6rem;
            position: relative;
            z-index: 1;
        }

        .fh-left-title em {
            font-style: normal;
            color: #7DD3C8;
        }

        .fh-left-desc {
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.5);
            line-height: 1.7;
            margin-bottom: 1.75rem;
            position: relative;
            z-index: 1;
        }

        .fh-steps {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .fh-step {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .fh-step-num {
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

        .fh-step-text {
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.65);
            line-height: 1.6;
        }

        .fh-step-text strong {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            display: block;
        }

        .fh-info-box {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.11);
            border-radius: 13px;
            padding: 1.1rem 1.25rem;
            position: relative;
            z-index: 1;
            margin-bottom: 1rem;
        }

        .fh-info-title {
            font-size: 11px;
            font-weight: 600;
            color: #7DD3C8;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 8px;
        }

        .fh-info-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
        }

        .fh-info-row:last-child {
            margin-bottom: 0;
        }

        .fh-info-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #7DD3C8;
            flex-shrink: 0;
        }

        .fh-info-text {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.55);
            line-height: 1.5;
        }

        .fh-quote {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.11);
            border-radius: 13px;
            padding: 1.1rem 1.25rem;
            position: relative;
            z-index: 1;
        }

        .fh-quote-mark {
            font-size: 26px;
            color: #7DD3C8;
            font-family: Georgia, serif;
            line-height: 1;
            margin-bottom: 6px;
        }

        .fh-quote-text {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            line-height: 1.7;
            font-style: italic;
        }

        .fh-quote-author {
            margin-top: 8px;
            font-size: 10.5px;
            color: rgba(255, 255, 255, 0.28);
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        /* ── RIGHT PANEL ── */
        .fh-right {
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

        .fh-form-header {
            margin-bottom: 1.5rem;
        }

        .fh-form-header h2 {
            font-family: 'Sora', sans-serif;
            font-size: 20px;
            font-weight: 600;
            color: #0F1F40;
            margin-bottom: 4px;
        }

        .fh-form-header p {
            font-size: 13.5px;
            color: #8A99B3;
        }

        .fh-divider {
            height: 1px;
            background: #EDF0F7;
            margin-bottom: 1.75rem;
        }

        .fh-section-title {
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

        .fh-section-title::after {
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

        /* Fields */
        .fh-field {
            margin-bottom: 1.1rem;
        }

        .fh-label {
            display: block;
            font-size: 11.5px;
            font-weight: 600;
            color: #6B7A99;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            margin-bottom: 6px;
        }

        .fh-label .req {
            color: #EF4444;
            margin-left: 2px;
        }

        .fh-input,
        .fh-select,
        .fh-textarea {
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

        .fh-input,
        .fh-select {
            height: 44px;
            padding: 0 14px;
        }

        .fh-textarea {
            padding: 12px 14px;
            resize: vertical;
            min-height: 90px;
            line-height: 1.6;
        }

        .fh-input::placeholder,
        .fh-textarea::placeholder {
            color: #B0BCCE;
        }

        .fh-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23B0BCCE' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 36px;
            cursor: pointer;
        }

        .fh-input:focus,
        .fh-select:focus,
        .fh-textarea:focus {
            border-color: #1A5FC8;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(26, 95, 200, 0.1);
        }

        .fh-input.is-invalid,
        .fh-select.is-invalid,
        .fh-textarea.is-invalid {
            border-color: #FCA5A5;
            background: #FEF2F2;
        }

        .fh-hint {
            font-size: 11.5px;
            color: #B0BCCE;
            margin-top: 4px;
            display: block;
        }

        .fh-error {
            font-size: 12px;
            color: #EF4444;
            margin-top: 4px;
            display: block;
        }

        /* Search Pendamping */
        .fh-search-wrap {
            position: relative;
        }

        .fh-search-dropdown {
            position: absolute;
            width: 100%;
            background: #fff;
            border: 1px solid #E0E7F0;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(60, 100, 180, 0.1);
            margin-top: 4px;
            z-index: 1000;
            overflow: hidden;
            max-height: 200px;
            overflow-y: auto;
            display: none;
        }

        .fh-search-item {
            padding: 10px 14px;
            font-size: 13.5px;
            color: #0F1F40;
            cursor: pointer;
            transition: background 0.15s;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .fh-search-item:hover {
            background: #EEF4FF;
        }

        .fh-search-item .item-name {
            font-weight: 500;
        }

        .fh-search-item .item-badge {
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 600;
        }

        .fh-search-item .item-badge.aktif {
            background: #EBF9F5;
            color: #0F6E56;
            border: 1px solid #A7DDD0;
        }

        .fh-search-item .item-badge.tidak {
            background: #FEF2F2;
            color: #B91C1C;
            border: 1px solid #FECACA;
        }

        .fh-selected-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #EBF5FF;
            border: 1px solid #BAD7F5;
            border-radius: 10px;
            padding: 10px 14px;
            margin-top: 6px;
        }

        .fh-selected-box svg {
            width: 15px;
            height: 15px;
            stroke: #1552A0;
            fill: none;
            stroke-width: 2;
            flex-shrink: 0;
        }

        .fh-selected-text {
            font-size: 13px;
            color: #1552A0;
            font-weight: 500;
        }

        .fh-alert-inactive {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-radius: 10px;
            padding: 12px 14px;
            margin-top: 6px;
        }

        .fh-alert-inactive svg {
            width: 16px;
            height: 16px;
            stroke: #EF4444;
            fill: none;
            stroke-width: 2;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .fh-alert-inactive-text {
            font-size: 13px;
            color: #B91C1C;
            line-height: 1.6;
        }

        /* NIK counter */
        .fh-nik-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 4px;
        }

        .fh-nik-count {
            font-size: 11.5px;
            color: #B0BCCE;
        }

        .fh-nik-status {
            font-size: 11.5px;
        }

        .fh-nik-status.ok {
            color: #059669;
        }

        .fh-nik-status.err {
            color: #EF4444;
        }

        /* File input */
        .fh-file-input {
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

        .fh-file-input:hover {
            border-color: #1A5FC8;
            background: #EEF4FF;
        }

        .fh-file-input:focus {
            outline: none;
            border-color: #1A5FC8;
            box-shadow: 0 0 0 3px rgba(26, 95, 200, 0.1);
        }

        .fh-file-input.is-invalid {
            border-color: #FCA5A5;
            background: #FEF2F2;
        }

        /* Photo grid */
        .fh-photo-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        /* Form lock overlay */
        .fh-form-locked {
            opacity: 0.4;
            pointer-events: none;
            user-select: none;
        }

        /* Submit */
        .fh-submit {
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
            transition: all 0.2s;
            box-shadow: 0 4px 16px rgba(26, 95, 200, 0.28);
            position: relative;
            overflow: hidden;
        }

        .fh-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 28px rgba(26, 95, 200, 0.36);
        }

        .fh-submit:active {
            transform: translateY(0);
        }

        .fh-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .fh-submit svg {
            width: 17px;
            height: 17px;
            fill: none;
            stroke: rgba(255, 255, 255, 0.85);
            stroke-width: 2;
        }

        .fh-spinner {
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

        .fh-back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #1A5FC8;
            text-decoration: none;
            margin-top: 1.25rem;
        }

        .fh-back-link svg {
            width: 14px;
            height: 14px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
        }

        .fh-back-link:hover {
            text-decoration: underline;
        }

        .fh-footer {
            text-align: center;
            margin-top: 1.75rem;
            font-size: 11.5px;
            color: #B0BCCE;
        }

        @media (max-width: 768px) {
            .fh-wrap {
                flex-direction: column;
            }

            .fh-left {
                width: 100%;
                position: static;
            }

            .fh-steps,
            .fh-info-box {
                display: none;
            }

            .fh-photo-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="fh-root">
        <div class="fh-orb fh-orb-1"></div>
        <div class="fh-orb fh-orb-2"></div>

        <div class="fh-wrap">

            {{-- ── LEFT PANEL ── --}}
            <div class="fh-left">
                <div class="fh-brand">
                    <div class="fh-brand-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.9)" stroke-width="1.8">
                            <path d="M12 2L2 7l10 5 10-5-10-5z" />
                            <path d="M2 17l10 5 10-5" />
                            <path d="M2 12l10 5 10-5" />
                        </svg>
                    </div>
                    <div class="fh-brand-name">
                        Kawulo Halal
                        <small>Sertifikasi Produk Halal Untuk UMKM Low-Risk</small>
                    </div>
                </div>

                <p class="fh-left-title">Form Data<br><em>Lapangan</em></p>
                <p class="fh-left-desc">Isi data lapangan dengan lengkap dan akurat agar proses sertifikasi berjalan lancar.
                </p>

                <div class="fh-steps">
                    <div class="fh-step">
                        <div class="fh-step-num">1</div>
                        <div class="fh-step-text">
                            <strong>Pilih Pendamping</strong>
                            Cari dan pilih nama pendamping aktif
                        </div>
                    </div>
                    <div class="fh-step">
                        <div class="fh-step-num">2</div>
                        <div class="fh-step-text">
                            <strong>Data Pelaku Usaha</strong>
                            Isi nama PU, produk, NIK & alamat
                        </div>
                    </div>
                    <div class="fh-step">
                        <div class="fh-step-num">3</div>
                        <div class="fh-step-text">
                            <strong>Upload Dokumentasi</strong>
                            Foto KTP, rumah, pendamping & produk
                        </div>
                    </div>
                    <div class="fh-step">
                        <div class="fh-step-num">4</div>
                        <div class="fh-step-text">
                            <strong>Simpan Data</strong>
                            Tinjau kembali dan simpan data lapangan
                        </div>
                    </div>
                </div>

                <div class="fh-info-box">
                    <div class="fh-info-title">Ketentuan Foto</div>
                    <div class="fh-info-row">
                        <div class="fh-info-dot"></div>
                        <div class="fh-info-text">Format: JPG, PNG, JPEG</div>
                    </div>
                    <div class="fh-info-row">
                        <div class="fh-info-dot"></div>
                        <div class="fh-info-text">Ukuran maksimal 10MB per foto</div>
                    </div>
                    <div class="fh-info-row">
                        <div class="fh-info-dot"></div>
                        <div class="fh-info-text">Foto harus jelas dan tidak buram</div>
                    </div>
                    <div class="fh-info-row">
                        <div class="fh-info-dot"></div>
                        <div class="fh-info-text">NIK harus tepat 16 digit angka</div>
                    </div>
                </div>

                <div class="fh-quote">
                    <div class="fh-quote-mark">"</div>
                    <p class="fh-quote-text">Data yang akurat adalah kunci keberhasilan program sertifikasi halal.</p>
                    <p class="fh-quote-author">— Kawulo Halal</p>
                </div>
            </div>

            {{-- ── RIGHT PANEL ── --}}
            <div class="fh-right">

                <div class="fh-form-header">
                    <h2>Form Data Lapangan</h2>
                    <p>Lengkapi semua data dengan benar dan teliti</p>
                </div>
                <div class="fh-divider"></div>

                {{-- SUCCESS --}}
                @if (session('success'))
                    <div class="alert-success-modern" id="alertSuccess">
                        <svg viewBox="0 0 24 24">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                        <div class="alt-text"><strong>Berhasil!</strong> {{ session('success') }}</div>
                        <button class="alt-close" onclick="this.closest('.alert-success-modern').remove()">&times;</button>
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
                        <button class="alt-close" onclick="this.closest('.alert-danger-modern').remove()">&times;</button>
                    </div>
                @endif

                <form method="POST" action="{{ route('formulir.halal.store') }}" enctype="multipart/form-data"
                    id="formDataLapangan" novalidate>
                    @csrf

                    {{-- SECTION 1: PENDAMPING --}}
                    <div class="fh-section-title">Pendamping</div>

                    <div class="fh-field">
                        <label class="fh-label">Nama Pendamping <span class="req">*</span></label>

                        <div class="fh-search-wrap">
                            <input type="text" id="enumerator_search" class="fh-input"
                                placeholder="Ketik untuk mencari pendamping..." autocomplete="off">
                            <div id="search_results" class="fh-search-dropdown"></div>
                        </div>

                        {{-- Hidden Select --}}
                        <select id="enumerator_id" name="enumerator_id"
                            class="fh-select @error('enumerator_id') is-invalid @enderror" required style="display:none;">
                            <option value="">-- Pilih Pendamping --</option>
                            @foreach ($enumerators as $enumerator)
                                <option value="{{ $enumerator->id }}" data-name="{{ $enumerator->nama_lengkap }}"
                                    data-status="{{ $enumerator->status }}"
                                    {{ old('enumerator_id') == $enumerator->id ? 'selected' : '' }}>
                                    {{ $enumerator->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Selected display --}}
                        <div id="selected_enumerator" style="display:none;">
                            <div class="fh-selected-box">
                                <svg viewBox="0 0 24 24">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                                <span class="fh-selected-text">Terpilih: <span id="selected_name"></span></span>
                            </div>
                        </div>

                        {{-- Alert tidak aktif --}}
                        <div id="alert_tidak_aktif" style="display:none;">
                            <div class="fh-alert-inactive">
                                <svg viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <div class="fh-alert-inactive-text">
                                    <strong>Pendamping Tidak Aktif</strong><br>
                                    Pendamping <strong id="nama_tidak_aktif"></strong> sedang berstatus tidak aktif
                                    karena tidak memenuhi target minimal 20 data lapangan dalam 30 hari terakhir.
                                    Silakan pilih pendamping lain atau hubungi koordinator.
                                </div>
                            </div>
                        </div>

                        @error('enumerator_id')
                            <span class="fh-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- SECTION 2: DATA PU --}}
                    <div class="fh-section-title" style="margin-top:1.5rem;">Data Pelaku Usaha</div>

                    <div id="formFields">

                        <div class="fh-field">
                            <label class="fh-label" for="nama_pu">Nama PU <span class="req">*</span></label>
                            <input type="text" id="nama_pu" name="nama_pu"
                                class="fh-input @error('nama_pu') is-invalid @enderror" value="{{ old('nama_pu') }}"
                                required autofocus placeholder="Masukkan nama pelaku usaha"
                                style="text-transform:uppercase;">
                            <span class="fh-hint">Nama akan otomatis diubah ke huruf besar</span>
                            @error('nama_pu')
                                <span class="fh-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="fh-field">
                            <label class="fh-label" for="nama_produk">Nama Produk <span class="req">*</span></label>
                            <input type="text" id="nama_produk" name="nama_produk"
                                class="fh-input @error('nama_produk') is-invalid @enderror"
                                value="{{ old('nama_produk') }}" required placeholder="Masukkan nama produk">
                            @error('nama_produk')
                                <span class="fh-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="fh-field">
                            <label class="fh-label" for="telephone">Nomor Telepon <span class="req">*</span></label>
                            <input type="text" id="telephone" name="telephone"
                                class="fh-input @error('telephone') is-invalid @enderror" value="{{ old('telephone') }}"
                                required placeholder="Contoh: 081234567890" maxlength="15" inputmode="numeric">
                            <span class="fh-hint">Nomor telepon aktif (10–15 digit)</span>
                            @error('telephone')
                                <span class="fh-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="fh-field">
                            <label class="fh-label" for="nik">NIK <span class="req">*</span></label>
                            <input type="text" id="nik" name="nik"
                                class="fh-input @error('nik') is-invalid @enderror" value="{{ old('nik') }}" required
                                placeholder="Masukkan NIK (16 digit)" maxlength="16" inputmode="numeric">
                            <div class="fh-nik-row">
                                <span class="fh-nik-count" id="nikCounter">0/16 digit</span>
                                <span class="fh-nik-status err" id="nikStatus">Belum lengkap</span>
                            </div>
                            @error('nik')
                                <span class="fh-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="fh-field">
                            <label class="fh-label" for="alamat">Alamat <span class="req">*</span></label>
                            <textarea id="alamat" name="alamat" class="fh-textarea @error('alamat') is-invalid @enderror" required
                                placeholder="Masukkan alamat lengkap pelaku usaha">{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <span class="fh-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- SECTION 3: DOKUMENTASI --}}
                        <div class="fh-section-title" style="margin-top:1.5rem;">Upload Dokumentasi</div>

                        <div class="fh-photo-grid">
                            <div class="fh-field">
                                <label class="fh-label" for="foto_ktp">Foto KTP <span class="req">*</span></label>
                                <input type="file" id="foto_ktp" name="foto_ktp"
                                    class="fh-file-input @error('foto_ktp') is-invalid @enderror" accept="image/*"
                                    required>
                                <span class="fh-hint">JPG/PNG. Maks 10MB</span>
                                @error('foto_ktp')
                                    <span class="fh-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="fh-field">
                                <label class="fh-label" for="foto_rumah">Foto Rumah <span class="req">*</span></label>
                                <input type="file" id="foto_rumah" name="foto_rumah"
                                    class="fh-file-input @error('foto_rumah') is-invalid @enderror" accept="image/*"
                                    required>
                                <span class="fh-hint">JPG/PNG. Maks 10MB</span>
                                @error('foto_rumah')
                                    <span class="fh-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="fh-field">
                                <label class="fh-label" for="foto_pendamping">Foto Pendamping <span
                                        class="req">*</span></label>
                                <input type="file" id="foto_pendamping" name="foto_pendamping"
                                    class="fh-file-input @error('foto_pendamping') is-invalid @enderror" accept="image/*"
                                    required>
                                <span class="fh-hint">JPG/PNG. Maks 10MB</span>
                                @error('foto_pendamping')
                                    <span class="fh-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="fh-field">
                                <label class="fh-label" for="foto_produk">Foto Produk <span
                                        class="req">*</span></label>
                                <input type="file" id="foto_produk" name="foto_produk"
                                    class="fh-file-input @error('foto_produk') is-invalid @enderror" accept="image/*"
                                    required>
                                <span class="fh-hint">JPG/PNG. Maks 10MB</span>
                                @error('foto_produk')
                                    <span class="fh-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div style="margin-top:2rem;">
                            <button class="fh-submit" type="submit" id="submitBtn">
                                <svg viewBox="0 0 24 24">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                    <polyline points="17 21 17 13 7 13 7 21" />
                                    <polyline points="7 3 7 8 15 8" />
                                </svg>
                                Simpan Data
                            </button>
                        </div>

                    </div>
                    {{-- end #formFields --}}

                </form>

                <div style="text-align:center;">
                    <a href="{{ route('superadmin.data-lapangans.index') }}" class="fh-back-link">
                        <svg viewBox="0 0 24 24">
                            <polyline points="15 18 9 12 15 6" />
                        </svg>
                        Kembali ke List
                    </a>
                </div>

                <div class="fh-footer">
                    &copy;
                    <script>
                        document.write(new Date().getFullYear())
                    </script> Kawulo Halal. All rights reserved.
                </div>
            </div>

        </div>
    </div>

    <script src="{{ asset('assets/js/form-halal.js') }}"></script>
    <script>
        (function() {
            'use strict';

            const enumeratorStatusMap = {
                @foreach ($enumerators as $enumerator)
                    {{ $enumerator->id }}: "{{ $enumerator->status }}",
                @endforeach
            };

            const selectEl = document.getElementById('enumerator_id');
            const alertTidakAktif = document.getElementById('alert_tidak_aktif');
            const namaTidakAktif = document.getElementById('nama_tidak_aktif');
            const formFields = document.getElementById('formFields');
            const selectedEnumeratorEl = document.getElementById('selected_enumerator');
            const selectedNameEl = document.getElementById('selected_name');
            const submitBtn = document.getElementById('submitBtn');

            /* Nama uppercase */
            const namaPU = document.getElementById('nama_pu');
            if (namaPU) {
                namaPU.addEventListener('input', function() {
                    const p = this.selectionStart;
                    this.value = this.value.toUpperCase();
                    try {
                        this.setSelectionRange(p, p);
                    } catch (_) {}
                });
            }

            /* NIK counter */
            const nikInput = document.getElementById('nik');
            const nikCounter = document.getElementById('nikCounter');
            const nikStatus = document.getElementById('nikStatus');

            if (nikInput) {
                nikInput.addEventListener('input', function() {
                    this.value = this.value.replace(/\D/g, '').slice(0, 16);
                    const len = this.value.length;
                    nikCounter.textContent = len + '/16 digit';
                    if (len === 16) {
                        nikStatus.textContent = 'Lengkap';
                        nikStatus.className = 'fh-nik-status ok';
                        nikInput.classList.remove('is-invalid');
                    } else {
                        nikStatus.textContent = 'Belum lengkap';
                        nikStatus.className = 'fh-nik-status err';
                    }
                });
            }

            /* Telephone digits only */
            const telInput = document.getElementById('telephone');
            if (telInput) {
                telInput.addEventListener('input', function() {
                    this.value = this.value.replace(/\D/g, '').slice(0, 15);
                });
            }

            /* Lock / unlock form */
            function lockForm() {
                formFields.querySelectorAll('input, textarea, select, button[type="submit"]')
                    .forEach(el => el.disabled = true);
                formFields.classList.add('fh-form-locked');
            }

            function unlockForm() {
                formFields.querySelectorAll('input, textarea, select, button[type="submit"]')
                    .forEach(el => el.disabled = false);
                formFields.classList.remove('fh-form-locked');
            }

            /* Check status */
            function checkEnumeratorStatus(enumeratorId, namaEnumerator) {
                if (!enumeratorId) {
                    selectedEnumeratorEl.style.display = 'none';
                    alertTidakAktif.style.display = 'none';
                    unlockForm();
                    return;
                }

                selectedNameEl.textContent = namaEnumerator;
                selectedEnumeratorEl.style.display = 'block';

                const status = enumeratorStatusMap[enumeratorId];
                if (status === 'Tidak Aktif') {
                    namaTidakAktif.textContent = namaEnumerator;
                    alertTidakAktif.style.display = 'block';
                    lockForm();
                } else {
                    alertTidakAktif.style.display = 'none';
                    unlockForm();
                }
            }

            selectEl.addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                checkEnumeratorStatus(this.value, opt ? opt.dataset.name : '');
            });

            /* Intercept programmatic value changes from form-halal.js */
            const originalDescriptor = Object.getOwnPropertyDescriptor(HTMLSelectElement.prototype, 'value');
            Object.defineProperty(selectEl, 'value', {
                set(newVal) {
                    originalDescriptor.set.call(this, newVal);
                    const opt = this.options[this.selectedIndex];
                    checkEnumeratorStatus(newVal, opt ? opt.dataset.name : '');
                },
                get() {
                    return originalDescriptor.get.call(this);
                }
            });

            /* Init on load */
            document.addEventListener('DOMContentLoaded', function() {
                if (selectEl.value) {
                    const opt = selectEl.options[selectEl.selectedIndex];
                    checkEnumeratorStatus(selectEl.value, opt ? opt.dataset.name : '');
                }

                /* Re-show alert tidak aktif jika auto-dismiss dari layout */
                setTimeout(function() {
                    if (selectEl.value && enumeratorStatusMap[selectEl.value] === 'Tidak Aktif') {
                        alertTidakAktif.style.display = 'block';
                    }
                }, 6000);
            });

            /* File validation */
            const MAX_FILE = 10 * 1024 * 1024;
            ['foto_ktp', 'foto_rumah', 'foto_pendamping', 'foto_produk'].forEach(id => {
                const inp = document.getElementById(id);
                if (!inp) return;
                inp.addEventListener('change', function() {
                    const f = this.files[0];
                    if (!f) return;
                    if (f.size > MAX_FILE) {
                        this.value = '';
                        this.classList.add('is-invalid');
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                text: 'Ukuran file maksimal 10MB!',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        }
                        return;
                    }
                    this.classList.remove('is-invalid');
                });
            });

            /* Submit loader */
            const form = document.getElementById('formDataLapangan');
            if (form) {
                form.addEventListener('submit', function() {
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<div class="fh-spinner"></div> Menyimpan...';
                    }
                });
            }

            /* Page cache reset */
            window.addEventListener('pageshow', function(e) {
                if (e.persisted && submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML =
                        '<svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="rgba(255,255,255,0.85)" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Simpan Data';
                }
            });

            /* Auto-dismiss alerts */
            setTimeout(() => {
                ['alertSuccess', 'alertError'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.style.transition = 'opacity 0.4s';
                        el.style.opacity = '0';
                        setTimeout(() => el?.remove(), 400);
                    }
                });
            }, 5000);

        })();
    </script>

@endsection
