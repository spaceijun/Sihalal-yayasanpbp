<!DOCTYPE html>
<html lang="id" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<head>
    <meta charset="utf-8" />
    <title>{{ $post->nama_loker }} – Form Pendaftaran - Kawulo Halal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Layout config Js -->
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/public-pages.css') }}" rel="stylesheet" type="text/css" />
    <!-- Kawulo Halal Modern Theme -->
    <link href="{{ asset('assets/css/compro-ui.css') }}" rel="stylesheet" type="text/css" />

    <style>
        /* ── Page-specific styles (recruitment form only) ── */

        /* Hero — selaras dengan contact.blade.php */
        .hero-rc {
            background: linear-gradient(180deg, #EAF4FF 0%, #F4FAFF 50%, #FFFFFF 100%);
            padding-top: 9rem;
            padding-bottom: 4rem;
            position: relative;
            overflow: hidden;
        }

        .hero-rc .kh-blob {
            opacity: 0.5;
        }

        .hero-rc-title {
            font-size: clamp(2rem, 4vw, 3.25rem);
            font-weight: 800;
            line-height: 1.15;
            color: var(--kh-dark);
        }

        .hero-rc-title .highlight {
            background: var(--kh-gradient-text);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ── Form wrapper card — selaras .form-card di contact ── */
        .form-rc-card {
            background: #fff;
            border-radius: var(--kh-radius-lg);
            border: 1px solid #EAF1FB;
            padding: 2.5rem;
            box-shadow: var(--kh-shadow);
        }

        /* ── Side info card — selaras .contact-card ── */
        .rc-info-card {
            background: var(--kh-gradient-dark);
            border-radius: var(--kh-radius-lg);
            padding: 2rem 1.75rem;
            box-shadow: var(--kh-shadow-lg);
            position: sticky;
            top: 100px;
            overflow: hidden;
        }

        .rc-info-card::before {
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

        .rc-info-card::after {
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

        .rc-brand-name {
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
        }

        .rc-info-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.4;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .rc-info-title em {
            font-style: normal;
            color: var(--kh-accent);
        }

        .rc-info-desc {
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.65);
            line-height: 1.7;
            margin-bottom: 1.75rem;
            position: relative;
            z-index: 1;
        }

        /* Steps selaras dengan FAQ cards di contact */
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
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: var(--kh-accent);
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
            font-weight: 600;
            display: block;
        }

        /* Quote box */
        .rc-quote {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.11);
            border-radius: var(--kh-radius);
            padding: 1.1rem 1.25rem;
            position: relative;
            z-index: 1;
        }

        .rc-quote-mark {
            font-size: 28px;
            color: var(--kh-accent);
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

        /* ── Progress bar ── */
        .rc-progress-wrap {
            margin-bottom: 1.75rem;
        }

        .rc-progress-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .rc-progress-label {
            font-size: 12px;
            color: var(--kh-text-light);
            font-weight: 500;
        }

        .rc-progress-pct {
            font-size: 12px;
            font-weight: 700;
            color: var(--kh-primary);
        }

        .rc-progress-bar {
            height: 5px;
            background: var(--kh-sky);
            border-radius: 50px;
            overflow: hidden;
        }

        .rc-progress-fill {
            height: 100%;
            background: var(--kh-gradient);
            border-radius: 50px;
            width: 0%;
            transition: width 0.45s ease;
        }

        /* ── Section label — selaras contact form labels ── */
        .rc-section-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--kh-primary);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .rc-section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--kh-sky);
        }

        /* ── Fields selaras .form-kh di contact ── */
        .rc-field-wrap {
            margin-bottom: 1.1rem;
        }

        .rc-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--kh-dark);
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
            background: var(--kh-sky-2);
            border: 2px solid #EAF1FB;
            border-radius: 12px;
            font-size: 14px;
            color: var(--kh-text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            outline: none;
            transition: var(--kh-transition);
        }

        .rc-input,
        .rc-select {
            height: 46px;
            padding: 0 14px;
        }

        .rc-textarea {
            padding: 12px 14px;
            resize: vertical;
            min-height: 100px;
            line-height: 1.6;
        }

        .rc-input::placeholder,
        .rc-textarea::placeholder {
            color: var(--kh-text-light);
        }

        .rc-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%237A8AA3' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 38px;
            cursor: pointer;
        }

        .rc-input:focus,
        .rc-select:focus,
        .rc-textarea:focus {
            border-color: var(--kh-primary);
            background: var(--kh-white);
            box-shadow: 0 0 0 4px rgba(47, 143, 230, 0.12);
        }

        .rc-input.is-invalid,
        .rc-select.is-invalid,
        .rc-textarea.is-invalid {
            border-color: #FCA5A5 !important;
            background: #FEF2F2 !important;
        }

        .rc-input.is-valid,
        .rc-select.is-valid,
        .rc-textarea.is-valid {
            border-color: var(--kh-secondary) !important;
            background: rgba(25, 180, 160, 0.04) !important;
        }

        /* Error message */
        .rc-error-msg {
            display: none;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            color: #EF4444;
            margin-top: 5px;
        }

        .rc-error-msg.show {
            display: flex;
        }

        .rc-hint {
            font-size: 11.5px;
            color: var(--kh-text-light);
            margin-top: 4px;
            display: block;
        }

        .rc-char-count {
            font-size: 11px;
            color: var(--kh-text-light);
        }

        .rc-char-count.warn {
            color: #F59E0B;
        }

        .rc-char-count.over {
            color: #EF4444;
        }

        /* ── Radio — selaras kh tone ── */
        .rc-radio-group {
            display: flex;
            gap: 0.65rem;
            flex-wrap: wrap;
        }

        .rc-radio-option {
            display: flex;
            align-items: center;
            gap: 9px;
            cursor: pointer;
            padding: 10px 16px;
            border: 2px solid #EAF1FB;
            border-radius: 12px;
            background: var(--kh-sky-2);
            transition: var(--kh-transition);
            user-select: none;
        }

        .rc-radio-option:hover,
        .rc-radio-option.selected {
            border-color: var(--kh-primary);
            background: var(--kh-sky);
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
            transition: var(--kh-transition);
        }

        .rc-radio-visual::after {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--kh-primary);
            opacity: 0;
            transform: scale(0);
            transition: var(--kh-transition);
        }

        .rc-radio-option.selected .rc-radio-visual {
            border-color: var(--kh-primary);
        }

        .rc-radio-option.selected .rc-radio-visual::after {
            opacity: 1;
            transform: scale(1);
        }

        .rc-radio-label {
            font-size: 13.5px;
            color: var(--kh-text);
            font-weight: 500;
        }

        .rc-radio-option.selected .rc-radio-label {
            color: var(--kh-primary);
            font-weight: 600;
        }

        /* ── Checkbox ── */
        .rc-checkbox-wrap {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 14px;
            background: var(--kh-sky-2);
            border: 2px solid #EAF1FB;
            border-radius: 12px;
            cursor: pointer;
            transition: var(--kh-transition);
        }

        .rc-checkbox-wrap:hover {
            border-color: var(--kh-primary);
            background: var(--kh-sky);
        }

        .rc-checkbox-wrap input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--kh-primary);
            margin-top: 1px;
            flex-shrink: 0;
            cursor: pointer;
        }

        .rc-checkbox-wrap span {
            font-size: 13.5px;
            color: var(--kh-text);
            line-height: 1.5;
            font-weight: 500;
        }

        /* ── File zone ── */
        .rc-file-zone {
            width: 100%;
            background: var(--kh-sky-2);
            border: 2px dashed rgba(47, 143, 230, 0.28);
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            cursor: pointer;
            transition: var(--kh-transition);
            position: relative;
        }

        .rc-file-zone:hover,
        .rc-file-zone.drag-over {
            border-color: var(--kh-primary);
            background: var(--kh-sky);
        }

        .rc-file-zone.has-file {
            border-color: var(--kh-secondary);
            border-style: solid;
            background: rgba(25, 180, 160, 0.05);
        }

        .rc-file-zone.is-invalid {
            border-color: #FCA5A5;
            background: #FEF2F2;
        }

        .rc-file-zone input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .rc-file-label {
            font-size: 13px;
            color: var(--kh-text-light);
            pointer-events: none;
        }

        .rc-file-label strong {
            display: block;
            color: var(--kh-dark);
            font-weight: 600;
            margin-bottom: 2px;
        }

        .rc-file-label small {
            font-size: 11.5px;
            color: var(--kh-text-light);
        }

        .rc-file-zone.has-file .rc-file-label {
            color: #0F6E56;
        }

        .rc-file-zone.has-file .rc-file-label strong {
            color: #065F46;
        }

        /* ── Fee info box ── */
        .rc-fee-box {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            border-radius: 12px;
            padding: 12px 14px;
            margin-top: 10px;
            font-size: 13px;
            line-height: 1.6;
            animation: rcFadeDown 0.25s ease;
        }

        @keyframes rcFadeDown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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
            background: var(--kh-sky);
            border: 1px solid rgba(47, 143, 230, 0.25);
            color: var(--kh-primary-dark);
        }

        .rc-fee-box.oss svg {
            stroke: var(--kh-primary);
        }

        .rc-fee-box.sihalal {
            background: #FFFBEB;
            border: 1px solid #FDDCAB;
            color: #924C0A;
        }

        .rc-fee-box.sihalal svg {
            stroke: #D97706;
        }

        /* ── Download template buttons — selaras btn-kh-outline ── */
        .rc-dl-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 6px;
        }

        .rc-dl-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 12.5px;
            font-weight: 600;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            transition: var(--kh-transition);
            text-decoration: none;
            border: 2px solid;
        }

        .rc-dl-btn svg {
            width: 14px;
            height: 14px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
        }

        .rc-dl-btn.pendamping {
            background: var(--kh-sky);
            color: var(--kh-primary);
            border-color: rgba(47, 143, 230, 0.25);
        }

        .rc-dl-btn.pendamping:hover {
            background: var(--kh-primary);
            color: #fff;
            border-color: var(--kh-primary);
            transform: translateY(-2px);
            box-shadow: var(--kh-shadow);
        }

        .rc-dl-btn.dataentry {
            background: var(--kh-sky-2);
            color: var(--kh-text);
            border-color: #EAF1FB;
        }

        .rc-dl-btn.dataentry:hover {
            background: var(--kh-dark);
            color: #fff;
            border-color: var(--kh-dark);
            transform: translateY(-2px);
            box-shadow: var(--kh-shadow);
        }

        .rc-dl-btn.adminumum {
            background: rgba(25, 180, 160, 0.08);
            color: var(--kh-secondary);
            border-color: rgba(25, 180, 160, 0.30);
        }

        .rc-dl-btn.adminumum:hover {
            background: var(--kh-secondary);
            color: #fff;
            border-color: var(--kh-secondary);
            transform: translateY(-2px);
            box-shadow: var(--kh-shadow);
        }

        /* ── Alerts — selaras contact ── */
        .alert-kh-success {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            border-radius: var(--kh-radius);
            margin-bottom: 1.5rem;
            background: rgba(25, 180, 160, 0.08);
            border: 1px solid rgba(25, 180, 160, 0.30);
            color: #065F46;
            font-size: 14px;
            line-height: 1.5;
            animation: rcFadeDown 0.3s ease;
        }

        .alert-kh-success i {
            font-size: 1.2rem;
            color: var(--kh-secondary);
            flex-shrink: 0;
            margin-top: 1px;
        }

        .alert-kh-danger {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            border-radius: var(--kh-radius);
            margin-bottom: 1.5rem;
            background: #FEF2F2;
            border: 1px solid #FCA5A5;
            color: #991B1B;
            font-size: 14px;
            line-height: 1.5;
            animation: rcFadeDown 0.3s ease;
        }

        .alert-kh-danger i {
            font-size: 1.2rem;
            color: #DC2626;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .alert-body {
            flex: 1;
        }

        .alert-close-btn {
            background: none;
            border: none;
            font-size: 1.2rem;
            line-height: 1;
            color: currentColor;
            opacity: 0.5;
            cursor: pointer;
            padding: 0 4px;
            transition: opacity 0.2s;
            flex-shrink: 0;
        }

        .alert-close-btn:hover {
            opacity: 1;
        }

        /* ── Submit button — persis btn-kh ── */
        .btn-rc-submit {
            width: 100%;
            height: 50px;
            background: var(--kh-gradient);
            border: none;
            border-radius: var(--kh-radius);
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            transition: var(--kh-transition);
            box-shadow: var(--kh-shadow);
            position: relative;
            overflow: hidden;
        }

        .btn-rc-submit::before {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--kh-gradient-dark);
            opacity: 0;
            transition: opacity 0.35s ease;
        }

        .btn-rc-submit:hover::before {
            opacity: 1;
        }

        .btn-rc-submit:hover {
            color: #fff;
            transform: translateY(-3px);
            box-shadow: var(--kh-shadow-lg);
        }

        .btn-rc-submit:active {
            transform: translateY(-1px) scale(0.98);
        }

        .btn-rc-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .btn-rc-submit span,
        .btn-rc-submit i {
            position: relative;
            z-index: 1;
        }

        /* Spinner */
        .rc-spinner {
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spinRc 0.7s linear infinite;
            display: none;
            position: relative;
            z-index: 1;
        }

        @keyframes spinRc {
            to {
                transform: rotate(360deg);
            }
        }

        /* Divider */
        .rc-divider {
            height: 1px;
            background: var(--kh-sky);
            margin: 1.5rem 0;
        }

        /* Form grid */
        .rc-form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0 1.25rem;
        }

        .rc-col-full {
            grid-column: span 2;
        }

        .rc-col-half {
            grid-column: span 1;
        }

        @media (max-width: 600px) {
            .rc-form-row {
                grid-template-columns: 1fr;
            }

            .rc-col-full,
            .rc-col-half {
                grid-column: span 1;
            }
        }

        /* Responsive layout */
        @media (max-width: 991px) {
            .rc-info-card {
                position: static;
            }

            .rc-steps {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .hero-rc {
                padding-top: 7rem;
                padding-bottom: 3rem;
            }

            .form-rc-card {
                padding: 1.5rem;
            }

            .rc-info-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="layout-wrapper landing">

        @include('publik.company-profile.partials.navbar')

        <!-- ========== HERO — selaras hero-contact ========== -->
        <section class="hero-rc" id="hero">
            <div class="kh-blob kh-blob-1"></div>
            <div class="kh-blob kh-blob-2"></div>

            <div class="container position-relative text-center" style="z-index: 1;">
                <div class="row justify-content-center">
                    <div class="col-lg-7">
                        <div class="hero-badge mb-4 animate-fade-in-up" style="margin: 0 auto 1.5rem;">
                            <i class="ri-briefcase-line text-kh"></i>
                            <span class="small fw-semibold">Form Pendaftaran</span>
                        </div>
                        <h1 class="hero-rc-title mb-4 animate-fade-in-up delay-100">
                            Bergabung Bersama<br>
                            <span class="highlight">Tim Kawulo Halal</span>
                        </h1>
                        <p class="hero-lead lead mb-0 animate-fade-in-up delay-200"
                            style="max-width: 520px; margin: 0 auto;">
                            Melamar untuk posisi <strong>{{ $post->nama_loker }}</strong>. Lengkapi data diri Anda
                            dengan benar dan teliti.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== BREADCRUMB ========== -->
        <div class="bg-kh-soft border-bottom" style="border-color: rgba(47,143,230,0.08) !important;">
            <div class="container py-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item">
                            <a href="{{ route('publik.home') }}" class="text-decoration-none text-kh">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('publik.recruitment.index') }}"
                                class="text-decoration-none text-kh">Lowongan</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('publik.recruitment.show', $post->slug) }}"
                                class="text-decoration-none text-kh">{{ $post->nama_loker }}</a>
                        </li>
                        <li class="breadcrumb-item active text-muted" aria-current="page">Form Pendaftaran</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- ========== MAIN CONTENT ========== -->
        <section class="section" id="form-pendaftaran">
            <div class="container">
                <div class="row g-4 align-items-start">

                    {{-- ── LEFT: Info Card ── --}}
                    <div class="col-lg-4" data-aos="fade-right">
                        <div class="rc-info-card">
                            <div class="rc-brand">
                                <div class="rc-brand-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="rgba(255,255,255,0.9)" stroke-width="1.8">
                                        <path d="M12 2L2 7l10 5 10-5-10-5z" />
                                        <path d="M2 17l10 5 10-5" />
                                        <path d="M2 12l10 5 10-5" />
                                    </svg>
                                </div>
                                <div class="rc-brand-name">
                                    Kawulo Halal
                                    <small>Sertifikasi Halal untuk UMKM Low-Risk</small>
                                </div>
                            </div>

                            <p class="rc-info-title">Form<br><em>Recruitment</em></p>
                            <p class="rc-info-desc">
                                {{ $post->nama_loker }}
                                @if ($post->posisi)
                                    <span
                                        style="display:inline-block; margin-top:6px; padding:3px 10px;
                                        background:rgba(255,255,255,0.12); border-radius:50px;
                                        font-size:11px; font-weight:600; letter-spacing:0.05em;">
                                        {{ $post->posisi }}
                                    </span>
                                @endif
                                <small
                                    style="color:rgba(255,255,255,0.5); display:block; margin-top:10px; line-height:1.7;">
                                    {{ Str::limit($post->deskripsi, 130) }}
                                </small>
                            </p>

                            <div class="rc-steps">
                                <div class="rc-step">
                                    <div class="rc-step-num">1</div>
                                    <div class="rc-step-text">
                                        <strong>Posisi Lowongan</strong>
                                        Melamar untuk posisi: {{ $post->posisi ?? $post->nama_loker }}
                                    </div>
                                </div>
                                <div class="rc-step">
                                    <div class="rc-step-num">2</div>
                                    <div class="rc-step-text">
                                        <strong>Isi Data Diri</strong>
                                        Lengkapi informasi pribadi sesuai KTP dengan akurat
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
                                <p class="rc-quote-text">Pengalaman dan dedikasi adalah kunci kesuksesan bersama kami.
                                </p>
                                <p class="rc-quote-author">— Kawulo Halal</p>
                            </div>
                        </div>
                    </div>

                    {{-- ── RIGHT: Form Card ── --}}
                    <div class="col-lg-8" data-aos="fade-left" data-aos-delay="100">

                        {{-- Progress Bar --}}
                        <div class="rc-progress-wrap">
                            <div class="rc-progress-header">
                                <span class="rc-progress-label">
                                    <i class="ri-edit-line me-1 align-bottom"></i> Kelengkapan formulir
                                </span>
                                <span class="rc-progress-pct" id="rcProgressPct">0%</span>
                            </div>
                            <div class="rc-progress-bar">
                                <div class="rc-progress-fill" id="rcProgressFill"></div>
                            </div>
                        </div>

                        <div class="form-rc-card">

                            {{-- Header --}}
                            <div class="mb-4">
                                <span class="badge bg-kh-soft text-kh px-3 py-2 mb-3">
                                    <i class="ri-user-add-line me-1 align-bottom"></i> Formulir Pendaftaran
                                </span>
                                <h4 class="fw-bold mb-1" style="color: var(--kh-dark);">Data Diri Pelamar</h4>
                                <p class="text-muted small mb-0">Lengkapi semua kolom bertanda <span
                                        class="text-danger fw-semibold">*</span> dengan benar untuk lowongan
                                    <strong>{{ $post->nama_loker }}</strong>
                                </p>
                            </div>

                            <div class="rc-divider"></div>

                            {{-- Session Alerts --}}
                            @if (session('success'))
                                <div class="alert-kh-success" id="alertSuccess">
                                    <i class="ri-checkbox-circle-line"></i>
                                    <div class="alert-body"><strong>Berhasil!</strong> {{ session('success') }}</div>
                                    <button class="alert-close-btn"
                                        onclick="this.closest('.alert-kh-success').remove()">&times;</button>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert-kh-danger" id="alertError">
                                    <i class="ri-error-warning-line"></i>
                                    <div class="alert-body"><strong>Gagal!</strong> {{ session('error') }}</div>
                                    <button class="alert-close-btn"
                                        onclick="this.closest('.alert-kh-danger').remove()">&times;</button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert-kh-danger" id="alertErrors">
                                    <i class="ri-error-warning-line"></i>
                                    <div class="alert-body">
                                        <strong>Terdapat kesalahan pada form:</strong>
                                        <ul style="margin: 6px 0 0; padding-left: 18px;">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <button class="alert-close-btn"
                                        onclick="this.closest('.alert-kh-danger').remove()">&times;</button>
                                </div>
                            @endif

                            {{-- Form --}}
                            <form method="POST" action="{{ route('recruitment.form.submit', $post->slug) }}"
                                enctype="multipart/form-data" id="formRecruitment" novalidate>
                                @csrf

                                @php
                                    $requirements = is_array($post->requirements) ? $post->requirements : [];
                                    $halfWidthKeys = [
                                        'nik',
                                        'jenis_kelamin',
                                        'pendidikan_terakhir',
                                        'rekomendasi',
                                        'foto_diri',
                                        'foto_ktp',
                                    ];
                                    $currentSection = null;
                                    $sectionMap = [
                                        'nama_lengkap' => 'Data Pribadi',
                                        'nik' => 'Data Pribadi',
                                        'jenis_kelamin' => 'Data Pribadi',
                                        'telephone' => 'Data Pribadi',
                                        'alamat_lengkap' => 'Data Pribadi',
                                        'pendidikan_terakhir' => 'Kualifikasi',
                                        'pengalaman' => 'Kualifikasi',
                                        'rekomendasi' => 'Kualifikasi',
                                        'type_entry' => 'Kualifikasi',
                                        'foto_diri' => 'Dokumen',
                                        'foto_ktp' => 'Dokumen',
                                        'cv' => 'Dokumen',
                                        'pakta_integritas' => 'Dokumen',
                                        'persetujuan' => 'Pernyataan',
                                    ];
                                @endphp

                                <div class="rc-form-row">
                                    @forelse ($requirements as $req)
                                        @php
                                            // Guard: skip malformed requirement entries to prevent Undefined array key errors
                                            if (
                                                empty($req['field_key']) ||
                                                empty($req['label']) ||
                                                empty($req['type'])
                                            ) {
                                                continue;
                                            }
                                            $key = $req['field_key'];
                                            $label = $req['label'];
                                            $type = $req['type'];
                                            $required = $req['required'] ?? false;
                                            $hint = $req['hint'] ?? null;
                                            $options = $req['options'] ?? [];
                                            $accept = $req['accept'] ?? '*/*';
                                            $isHalf = in_array($key, $halfWidthKeys);
                                            $section = $sectionMap[$key] ?? null;
                                            // Clean up accept string for display (handle multi-MIME e.g. image/jpeg,application/pdf)
                                            $acceptDisplay = strtoupper(
                                                implode(
                                                    ', ',
                                                    array_map(function ($m) {
                                                        $m = trim($m);
                                                        $m = preg_replace('/^image\//', '', $m);
                                                        $m = preg_replace('/^application\//', '', $m);
                                                        return $m;
                                                    }, explode(',', $accept)),
                                                ),
                                            );
                                        @endphp

                                        {{-- Section heading --}}
                                        @if ($section && $section !== $currentSection)
                                            @php $currentSection = $section; @endphp
                                            <div class="rc-col-full"
                                                style="margin-top: {{ $loop->first ? '0' : '0.5rem' }};">
                                                <div class="rc-section-label">{{ $section }}</div>
                                            </div>
                                        @endif

                                        {{-- Template download for pakta_integritas --}}
                                        @if ($key === 'pakta_integritas')
                                            <div class="rc-col-full rc-field-wrap">
                                                <label class="rc-label">Unduh Template Pakta Integritas</label>
                                                <div class="rc-dl-group">
                                                    @if ($post->template_pakta_integritas)
                                                        <a href="{{ asset('storage/' . $post->template_pakta_integritas) }}"
                                                            download class="rc-dl-btn pendamping">
                                                            <svg viewBox="0 0 24 24">
                                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                                                <polyline points="7 10 12 15 17 10" />
                                                                <line x1="12" y1="15" x2="12"
                                                                    y2="3" />
                                                            </svg>
                                                            Unduh Template Pakta
                                                        </a>
                                                    @else
                                                        @if ($post->posisi === 'PENDAMPING')
                                                            <a href="{{ asset('assets/files/pakta-integritas-pendamping.docx') }}"
                                                                download class="rc-dl-btn pendamping">
                                                                <svg viewBox="0 0 24 24">
                                                                    <path
                                                                        d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                                                    <polyline points="7 10 12 15 17 10" />
                                                                    <line x1="12" y1="15"
                                                                        x2="12" y2="3" />
                                                                </svg>
                                                                Pakta — Pendamping
                                                            </a>
                                                        @elseif ($post->posisi === 'DATA ENTRY')
                                                            <a href="{{ asset('assets/files/pakta-integritas-data-entry.docx') }}"
                                                                download class="rc-dl-btn dataentry">
                                                                <svg viewBox="0 0 24 24">
                                                                    <path
                                                                        d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                                                    <polyline points="7 10 12 15 17 10" />
                                                                    <line x1="12" y1="15"
                                                                        x2="12" y2="3" />
                                                                </svg>
                                                                Pakta — Data Entry
                                                            </a>
                                                        @elseif ($post->posisi === 'ADMIN UMUM')
                                                            <a href="{{ asset('assets/files/pakta-integritas-admin-umum.docx') }}"
                                                                download class="rc-dl-btn adminumum">
                                                                <svg viewBox="0 0 24 24">
                                                                    <path
                                                                        d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                                                    <polyline points="7 10 12 15 17 10" />
                                                                    <line x1="12" y1="15"
                                                                        x2="12" y2="3" />
                                                                </svg>
                                                                Pakta — Admin Umum
                                                            </a>
                                                        @endif
                                                    @endif
                                                </div>
                                                <span class="rc-hint">Unduh template sesuai posisi, tandatangani, lalu
                                                    unggah di bawah.</span>
                                            </div>
                                        @endif

                                        {{-- Field --}}
                                        <div class="{{ $isHalf ? 'rc-col-half' : 'rc-col-full' }} rc-field-wrap">

                                            @if ($type === 'checkbox')
                                                <label class="rc-checkbox-wrap" for="{{ $key }}">
                                                    <input type="checkbox" name="{{ $key }}"
                                                        id="{{ $key }}" value="1"
                                                        {{ old($key) ? 'checked' : '' }}
                                                        @if ($required) required @endif>
                                                    <span>{{ $label }}@if ($required)
                                                            <span class="req">*</span>
                                                        @endif
                                                    </span>
                                                </label>
                                                <div class="rc-error-msg" id="err_{{ $key }}">
                                                    <i class="ri-error-warning-line" style="font-size:13px;"></i>
                                                    Persetujuan {{ $label }} wajib dicentang.
                                                </div>
                                            @elseif ($type === 'radio')
                                                <label class="rc-label">{{ $label }}@if ($required)
                                                        <span class="req">*</span>
                                                    @endif
                                                </label>
                                                <div class="rc-radio-group">
                                                    @foreach ($options as $opt)
                                                        <label
                                                            class="rc-radio-option {{ old($key) === $opt ? 'selected' : '' }}">
                                                            <input type="radio" name="{{ $key }}"
                                                                value="{{ $opt }}"
                                                                {{ old($key) === $opt ? 'checked' : '' }}
                                                                @if ($required) required @endif>
                                                            <span class="rc-radio-visual"></span>
                                                            <span class="rc-radio-label">{{ $opt }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                                <div class="rc-error-msg" id="err_{{ $key }}">
                                                    <i class="ri-error-warning-line" style="font-size:13px;"></i>
                                                    Pilih salah satu opsi untuk {{ $label }}.
                                                </div>
                                            @elseif ($type === 'select')
                                                <label class="rc-label"
                                                    for="{{ $key }}">{{ $label }}@if ($required)
                                                        <span class="req">*</span>
                                                    @endif
                                                </label>
                                                <select id="{{ $key }}" name="{{ $key }}"
                                                    class="rc-select @error($key) is-invalid @enderror"
                                                    @if ($required) required @endif>
                                                    <option value="">-- Pilih --</option>
                                                    @foreach ($options as $opt)
                                                        <option value="{{ $opt }}"
                                                            {{ old($key) === $opt ? 'selected' : '' }}>
                                                            {{ $opt }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="rc-error-msg" id="err_{{ $key }}">
                                                    <i class="ri-error-warning-line" style="font-size:13px;"></i>
                                                    Silakan pilih {{ $label }}.
                                                </div>

                                                @if ($key === 'type_entry')
                                                    <div id="alertOSS" class="rc-fee-box oss" style="display:none;">
                                                        <svg viewBox="0 0 24 24">
                                                            <circle cx="12" cy="12" r="10" />
                                                            <line x1="12" y1="8" x2="12"
                                                                y2="12" />
                                                            <line x1="12" y1="16" x2="12.01"
                                                                y2="16" />
                                                        </svg>
                                                        <div>
                                                            <strong>Fee OSS: Rp100.000</strong>
                                                            <small style="display:block;">Per 15 data yang berhasil
                                                                diproses. Pahami alur kerja OSS sebelum
                                                                mendaftar.</small>
                                                        </div>
                                                    </div>
                                                    <div id="alertSIHALAL" class="rc-fee-box sihalal"
                                                        style="display:none;">
                                                        <svg viewBox="0 0 24 24">
                                                            <circle cx="12" cy="12" r="10" />
                                                            <line x1="12" y1="8" x2="12"
                                                                y2="12" />
                                                            <line x1="12" y1="16" x2="12.01"
                                                                y2="16" />
                                                        </svg>
                                                        <div>
                                                            <strong>Fee SIHALAL: Rp150.000</strong>
                                                            <small style="display:block;">Per 15 data yang berhasil
                                                                diproses. Pahami alur kerja SIHALAL sebelum
                                                                mendaftar.</small>
                                                        </div>
                                                    </div>
                                                @endif
                                            @elseif ($type === 'file')
                                                <label class="rc-label"
                                                    for="{{ $key }}">{{ $label }}@if ($required)
                                                        <span class="req">*</span>
                                                    @endif
                                                </label>
                                                <div class="rc-file-zone @error($key) is-invalid @enderror"
                                                    id="zone_{{ $key }}">
                                                    <input type="file" id="{{ $key }}"
                                                        name="{{ $key }}" accept="{{ $accept }}"
                                                        @if ($required) required @endif>
                                                    <div class="rc-file-label" id="lbl_{{ $key }}">
                                                        <strong>
                                                            <i class="ri-upload-cloud-line"
                                                                style="font-size:1.3rem; color:var(--kh-primary); display:block; margin-bottom:4px;"></i>
                                                            Klik atau seret file ke sini
                                                        </strong>
                                                        <small>{{ $acceptDisplay }}
                                                            · maks 5MB</small>
                                                    </div>
                                                </div>
                                                <div class="rc-error-msg" id="err_{{ $key }}">
                                                    <i class="ri-error-warning-line" style="font-size:13px;"></i>
                                                    <span id="msg_{{ $key }}">{{ $label }} wajib
                                                        diupload (maksimal 5MB).</span>
                                                </div>
                                            @elseif ($type === 'textarea')
                                                <label class="rc-label"
                                                    for="{{ $key }}">{{ $label }}@if ($required)
                                                        <span class="req">*</span>
                                                    @endif
                                                </label>
                                                <textarea id="{{ $key }}" name="{{ $key }}" class="rc-textarea @error($key) is-invalid @enderror"
                                                    @if ($required) required @endif placeholder="Tuliskan {{ strtolower($label) }}...">{{ old($key) }}</textarea>
                                                <div
                                                    style="display:flex; justify-content:space-between; align-items:center; margin-top:4px;">
                                                    @if ($key === 'alamat_lengkap')
                                                        <span class="rc-hint" style="margin-top:0;">Minimal 10
                                                            karakter</span>
                                                        <span class="rc-char-count" id="cc_{{ $key }}">0 /
                                                            500</span>
                                                    @elseif ($key === 'pengalaman')
                                                        <span class="rc-hint" style="margin-top:0;">Minimal 20
                                                            karakter</span>
                                                        <span class="rc-char-count" id="cc_{{ $key }}">0 /
                                                            1000</span>
                                                    @else
                                                        <span></span>
                                                        <span class="rc-char-count" id="cc_{{ $key }}">0 /
                                                            1000</span>
                                                    @endif
                                                </div>
                                                <div class="rc-error-msg" id="err_{{ $key }}">
                                                    <i class="ri-error-warning-line" style="font-size:13px;"></i>
                                                    {{ $label }} wajib diisi dengan benar.
                                                </div>
                                            @else
                                                {{-- Text / tel / email / number ── --}}
                                                <label class="rc-label"
                                                    for="{{ $key }}">{{ $label }}@if ($required)
                                                        <span class="req">*</span>
                                                    @endif
                                                </label>
                                                <input type="text" id="{{ $key }}"
                                                    name="{{ $key }}"
                                                    class="rc-input @error($key) is-invalid @enderror"
                                                    value="{{ old($key) }}"
                                                    @if ($required) required @endif
                                                    placeholder="Masukkan {{ strtolower($label) }}">
                                                @if ($key === 'nama_lengkap')
                                                    <span class="rc-hint">Nama akan otomatis diubah ke huruf
                                                        besar</span>
                                                @elseif ($key === 'nik')
                                                    <span class="rc-hint">Sesuai KTP — 16 digit angka</span>
                                                @elseif ($key === 'telephone')
                                                    <span class="rc-hint">Nomor telepon aktif (10–15 digit)</span>
                                                @endif
                                                <div class="rc-error-msg" id="err_{{ $key }}">
                                                    <i class="ri-error-warning-line" style="font-size:13px;"></i>
                                                    {{ $label }} tidak valid.
                                                </div>
                                            @endif

                                            @if ($hint)
                                                <span class="rc-hint">{{ $hint }}</span>
                                            @endif

                                        </div>
                                        @empty
                                            <div class="rc-col-full"
                                                style="text-align:center; padding:2rem 0; color:var(--kh-text-light);">
                                                <i class="ri-information-line"
                                                    style="font-size:2rem; display:block; margin-bottom:0.5rem;"></i>
                                                Form pendaftaran belum dikonfigurasi.
                                            </div>
                                        @endforelse
                                    </div>

                                    <input type="hidden" name="status" value="Melamar">

                                    @if (count($requirements) > 0)
                                        <div style="margin-top: 2rem;">
                                            <button class="btn-rc-submit" type="submit" id="submitBtn">
                                                <div class="rc-spinner" id="rcSpinner"></div>
                                                <i class="ri-send-plane-line" id="submitIcon"></i>
                                                <span id="submitLabel">Kirim Lamaran</span>
                                            </button>
                                        </div>
                                    @endif

                                </form>
                            </div>

                            <p class="text-center text-muted small mt-3">
                                &copy; {{ date('Y') }} Kawulo Halal. All rights reserved.
                                &nbsp;·&nbsp;
                                <a href="{{ route('publik.home') }}" class="text-kh text-decoration-none">Kembali ke
                                    Beranda</a>
                            </p>

                        </div>
                    </div>
                </div>
            </section>

            <!-- ========== CTA — selaras contact ========== -->
            <section class="py-5 gradient-kh cta-section position-relative">
                <div class="container position-relative">
                    <div class="row align-items-center gy-4">
                        <div class="col-sm-8">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-lg flex-shrink-0">
                                    <div class="avatar-title bg-white bg-opacity-25 rounded-3">
                                        <i class="ri-briefcase-4-fill text-white fs-2"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-white mb-1">Ada pertanyaan seputar lowongan?</h4>
                                    <p class="text-white text-opacity-75 mb-0">Hubungi tim kami, kami siap membantu Anda.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 text-sm-end">
                            <a href="{{ route('publik.contact') }}"
                                class="btn btn-light btn-lg fw-bold shadow shine-hover">
                                <i class="ri-message-3-line align-bottom me-1"></i> Hubungi Kami
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            @include('publik.company-profile.partials.footer')
        </div>

        <!-- JAVASCRIPT -->
        <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
        <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
        <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
        <script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
        <script src="{{ asset('assets/js/plugins.js') }}"></script>
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script src="{{ asset('assets/js/compro.js') }}"></script>

        <script>
            (function() {
                'use strict';

                const MAX_FILE = 5 * 1024 * 1024;
                const IMG_TYPES = ['image/jpeg', 'image/jpg', 'image/png'];
                const MIX_TYPES = [...IMG_TYPES, 'application/pdf'];

                function $(id) {
                    return document.getElementById(id);
                }

                const uploadedFiles = {};

                /* ── Progress ── */
                function calcProgress() {
                    const required = document.querySelectorAll('#formRecruitment [required]');
                    if (!required.length) return 100;

                    let total = 0,
                        done = 0;
                    const seenRadio = [];

                    required.forEach(f => {
                        if (f.type === 'radio') {
                            if (seenRadio.includes(f.name)) return;
                            seenRadio.push(f.name);
                        }
                        total++;
                    });

                    const seenRadioDone = [];
                    required.forEach(f => {
                        if (f.type === 'radio') {
                            if (seenRadioDone.includes(f.name)) return;
                            seenRadioDone.push(f.name);
                            if (document.querySelector(`input[name="${f.name}"]:checked`)) done++;
                        } else if (f.type === 'checkbox') {
                            if (f.checked) done++;
                        } else if (f.type === 'file') {
                            if (uploadedFiles[f.id]) done++;
                        } else {
                            const v = f.value.trim();
                            if (!v) return;
                            if (f.id === 'nama_lengkap' && v.length >= 3) done++;
                            else if (f.id === 'nik' && /^\d{16}$/.test(v)) done++;
                            else if (f.id === 'telephone' && v.length >= 10 && v.length <= 15) done++;
                            else if (f.id === 'alamat_lengkap' && v.length >= 10) done++;
                            else if (f.id === 'pengalaman' && v.length >= 20) done++;
                            else if (!['nama_lengkap', 'nik', 'telephone', 'alamat_lengkap', 'pengalaman'].includes(
                                    f.id)) done++;
                        }
                    });

                    return total ? Math.round((done / total) * 100) : 0;
                }

                function updateProgress() {
                    const pct = calcProgress();
                    const fill = $('rcProgressFill');
                    const lbl = $('rcProgressPct');
                    if (fill) fill.style.width = pct + '%';
                    if (lbl) lbl.textContent = pct + '%';
                }

                /* ── Error helpers ── */
                function showErr(id, show) {
                    const el = $(id);
                    if (el) el.classList.toggle('show', show);
                }

                function setState(el, valid) {
                    if (!el) return;
                    el.classList.toggle('is-valid', valid);
                    el.classList.toggle('is-invalid', !valid);
                }

                /* ── Toast fallback ── */
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
                    }
                }

                function scrollToFirst(el) {
                    if (!el) return;
                    el.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    if (typeof el.focus === 'function') el.focus();
                }

                /* ── Field validation ── */
                function validateField(input) {
                    const key = input.id || input.name;
                    const errId = 'err_' + key;
                    let valid = true;

                    if (input.required) {
                        if (input.type === 'radio') {
                            valid = !!document.querySelector(`input[name="${input.name}"]:checked`);
                        } else if (input.type === 'checkbox') {
                            valid = input.checked;
                        } else {
                            valid = input.value.trim().length > 0;
                        }
                    }

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
                            if (cc) cc.textContent = input.value.length + ' / 500';
                        } else if (key === 'pengalaman') {
                            valid = input.value.trim().length >= 20;
                            const cc = $('cc_' + key);
                            if (cc) cc.textContent = input.value.length + ' / 1000';
                        } else {
                            const cc = $('cc_' + key);
                            if (cc) cc.textContent = input.value.length + ' / 1000';
                        }
                    }

                    setState(input, valid);
                    showErr(errId, !valid);
                    return valid;
                }

                /* ── Radio sync ── */
                function syncRadio() {
                    document.querySelectorAll('#formRecruitment input[type="radio"]').forEach(r => {
                        const opt = r.closest('.rc-radio-option');
                        if (opt) opt.classList.toggle('selected', r.checked);
                    });
                }

                document.querySelectorAll('#formRecruitment input[type="radio"]').forEach(r => {
                    r.addEventListener('change', () => {
                        syncRadio();
                        validateField(r);
                        updateProgress();
                    });
                });

                /* ── Input listeners ── */
                document.querySelectorAll(
                        '#formRecruitment input:not([type="file"]), #formRecruitment select, #formRecruitment textarea')
                    .forEach(el => {
                        el.addEventListener('input', () => {
                            validateField(el);
                            updateProgress();
                        });
                        el.addEventListener('change', () => {
                            validateField(el);
                            updateProgress();
                        });
                    });

                /* ── Type entry fee box ── */
                const typeEntry = $('type_entry');
                if (typeEntry) {
                    typeEntry.addEventListener('change', function() {
                        const v = this.value;
                        const oss = $('alertOSS');
                        const sihalal = $('alertSIHALAL');
                        if (oss) oss.style.display = v === 'OSS' ? 'flex' : 'none';
                        if (sihalal) sihalal.style.display = v === 'SIHALAL' ? 'flex' : 'none';
                    });
                }

                /* ── File zone setup ── */
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

                    function process(file) {
                        if (!file) return;

                        if (file.size > MAX_FILE) {
                            const msgEl = $(msgId);
                            if (msgEl) msgEl.textContent = `Ukuran file "${label}" maksimal 5MB!`;
                            showErr(errId, true);
                            zone.classList.add('is-invalid');
                            zone.classList.remove('has-file');
                            uploadedFiles[inputId] = null;
                            updateProgress();
                            toast('error', `Ukuran file "${label}" maksimal 5MB!`);
                            return;
                        }

                        if (!allowedTypes.includes(file.type)) {
                            const ext = allowedTypes.includes('application/pdf') ? 'JPG, PNG, atau PDF' : 'JPG atau PNG';
                            const msgEl = $(msgId);
                            if (msgEl) msgEl.textContent = `Format "${label}" harus ${ext}`;
                            showErr(errId, true);
                            zone.classList.add('is-invalid');
                            zone.classList.remove('has-file');
                            uploadedFiles[inputId] = null;
                            updateProgress();
                            toast('error', `Format file "${label}" tidak valid!`);
                            return;
                        }

                        uploadedFiles[inputId] = file;
                        zone.classList.remove('is-invalid');
                        zone.classList.add('has-file');
                        showErr(errId, false);

                        const size = file.size > 1048576 ?
                            (file.size / 1048576).toFixed(1) + ' MB' :
                            Math.round(file.size / 1024) + ' KB';
                        const ext = file.type === 'application/pdf' ? 'PDF' : 'Gambar';

                        lbl.innerHTML =
                            `<strong><i class="ri-check-line" style="color:var(--kh-secondary);"></i> ${file.name}</strong>` +
                            `<small>${size} · ${ext} — klik untuk ganti</small>`;

                        updateProgress();
                    }

                    input.addEventListener('change', function() {
                        process(this.files[0]);
                    });

                    zone.addEventListener('dragover', e => {
                        e.preventDefault();
                        zone.classList.add('drag-over');
                    });
                    zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
                    zone.addEventListener('drop', e => {
                        e.preventDefault();
                        zone.classList.remove('drag-over');
                        process(e.dataTransfer.files[0]);
                    });
                }

                document.querySelectorAll('#formRecruitment input[type="file"]').forEach(fi => {
                    const key = fi.id;
                    const accept = fi.getAttribute('accept') || '*/*';
                    setupFileZone({
                        zoneId: 'zone_' + key,
                        inputId: key,
                        lblId: 'lbl_' + key,
                        errId: 'err_' + key,
                        msgId: 'msg_' + key,
                        allowedTypes: accept.includes('pdf') ? MIX_TYPES : IMG_TYPES,
                        label: (fi.closest('.rc-field-wrap')?.querySelector('.rc-label')?.textContent ||
                                key)
                            .replace('*', '').trim(),
                    });
                });

                /* ── Submit ── */
                $('formRecruitment')?.addEventListener('submit', function(e) {
                    let valid = true,
                        first = null;

                    document.querySelectorAll(
                            '#formRecruitment input, #formRecruitment select, #formRecruitment textarea')
                        .forEach(input => {
                            const key = input.id || input.name;
                            const errId = 'err_' + key;

                            if (input.type === 'file') {
                                if (input.required && !uploadedFiles[key]) {
                                    const zone = $('zone_' + key);
                                    if (zone) zone.classList.add('is-invalid');
                                    showErr(errId, true);
                                    if (!first) first = zone;
                                    valid = false;
                                }
                            } else {
                                if (!validateField(input)) {
                                    if (!first) first = input;
                                    valid = false;
                                }
                            }
                        });

                    if (!valid) {
                        e.preventDefault();
                        scrollToFirst(first);
                        toast('error', 'Mohon lengkapi semua data yang diperlukan!');
                        return;
                    }

                    const btn = $('submitBtn');
                    const spinner = $('rcSpinner');
                    const icon = $('submitIcon');
                    const lbl = $('submitLabel');
                    if (btn) btn.disabled = true;
                    if (spinner) spinner.style.display = 'block';
                    if (icon) icon.style.display = 'none';
                    if (lbl) lbl.textContent = 'Mengirim...';
                });

                /* ── BFCache: restore submit button ── */
                window.addEventListener('pageshow', e => {
                    if (!e.persisted) return;
                    const btn = $('submitBtn');
                    const spinner = $('rcSpinner');
                    const icon = $('submitIcon');
                    const lbl = $('submitLabel');
                    if (btn) btn.disabled = false;
                    if (spinner) spinner.style.display = 'none';
                    if (icon) icon.style.display = '';
                    if (lbl) lbl.textContent = 'Kirim Lamaran';
                });

                /* ── Auto-dismiss session alerts ── */
                setTimeout(() => {
                    ['alertSuccess', 'alertError', 'alertErrors'].forEach(id => {
                        const el = $(id);
                        if (!el) return;
                        el.style.transition = 'opacity 0.4s';
                        el.style.opacity = '0';
                        setTimeout(() => el.remove(), 400);
                    });
                }, 5000);

                /* ── Init ── */
                (function init() {
                    document.querySelectorAll('#formRecruitment textarea').forEach(ta => {
                        if (!ta.value) return;
                        const cc = $('cc_' + ta.id);
                        if (cc) {
                            const max = ta.id === 'alamat_lengkap' ? 500 : 1000;
                            cc.textContent = ta.value.length + ' / ' + max;
                        }
                    });

                    document.querySelectorAll(
                            '#formRecruitment input:not([type="file"]):not([type="hidden"]), #formRecruitment select, #formRecruitment textarea'
                        )
                        .forEach(el => {
                            if (el.value) validateField(el);
                        });

                    if (typeEntry && typeEntry.value) {
                        const v = typeEntry.value;
                        const oss = $('alertOSS');
                        const sihalal = $('alertSIHALAL');
                        if (oss) oss.style.display = v === 'OSS' ? 'flex' : 'none';
                        if (sihalal) sihalal.style.display = v === 'SIHALAL' ? 'flex' : 'none';
                    }

                    syncRadio();
                    updateProgress();
                })();

            })();
        </script>

    </body>

    </html>
