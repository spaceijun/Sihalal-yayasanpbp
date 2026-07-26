@extends('layouts.guest')
@section('title', 'Form Data Lapangan')
@section('content')

    <style>
        .fh-root {
            min-height: 100vh;
            background-color: #EEF3FA;
            background-image: radial-gradient(ellipse 70% 50% at 10% 5%, rgba(180, 210, 255, .5) 0%, transparent 60%), radial-gradient(ellipse 50% 40% at 90% 90%, rgba(160, 220, 200, .25) 0%, transparent 55%);
            font-family: 'Plus Jakarta Sans', sans-serif;
            padding: 2rem 1rem 3rem;
            position: relative;
        }

        .fh-root::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: radial-gradient(circle, rgba(100, 140, 200, .1) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
            z-index: 0;
        }

        /* Orbs */
        .fh-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
            animation: pubFloat 14s ease-in-out infinite;
        }

        .fh-orb-1 {
            width: 380px;
            height: 380px;
            background: rgba(130, 180, 255, .2);
            top: -80px;
            left: -80px;
        }

        .fh-orb-2 {
            width: 260px;
            height: 260px;
            background: rgba(100, 210, 180, .16);
            bottom: -60px;
            right: -60px;
            animation-delay: -6s;
        }

        /* Layout */
        .fh-wrap {
            position: relative;
            z-index: 1;
            max-width: 1060px;
            margin: 0 auto;
            display: flex;
            gap: 1.5rem;
            align-items: flex-start;
        }

        /* Left panel */
        .fh-left {
            width: 310px;
            flex-shrink: 0;
            position: sticky;
            top: 2rem;
            background: linear-gradient(145deg, #1A5FC8 0%, #1040A0 55%, #0C2E78 100%);
            border-radius: 20px;
            padding: 2rem 1.75rem;
            box-shadow: 0 20px 50px rgba(16, 64, 160, .25);
            animation: pubCardIn .55s cubic-bezier(.16, 1, .3, 1) both;
            overflow: hidden;
        }

        .fh-left::before {
            content: '';
            position: absolute;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .06);
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
            background: rgba(255, 255, 255, .04);
            bottom: 20px;
            left: -50px;
            pointer-events: none;
        }

        /* Brand */
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
            background: rgba(255, 255, 255, .13);
            border: 1px solid rgba(255, 255, 255, .2);
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
            color: rgba(255, 255, 255, .95);
            line-height: 1.4;
        }

        .fh-brand-name small {
            display: block;
            font-size: 11px;
            font-weight: 400;
            color: rgba(255, 255, 255, .42);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .fh-left-title {
            font-family: 'Sora', sans-serif;
            font-size: 20px;
            font-weight: 600;
            color: #fff;
            line-height: 1.4;
            margin-bottom: .6rem;
            position: relative;
            z-index: 1;
        }

        .fh-left-title em {
            font-style: normal;
            color: #7DD3C8;
        }

        .fh-left-desc {
            font-size: 12.5px;
            color: rgba(255, 255, 255, .5);
            line-height: 1.7;
            margin-bottom: 1.75rem;
            position: relative;
            z-index: 1;
        }

        /* Steps */
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
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .2);
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
            color: rgba(255, 255, 255, .65);
            line-height: 1.6;
        }

        .fh-step-text strong {
            color: rgba(255, 255, 255, .9);
            font-weight: 500;
            display: block;
        }

        /* Info box */
        .fh-info-box {
            background: rgba(255, 255, 255, .07);
            border: 1px solid rgba(255, 255, 255, .11);
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
            letter-spacing: .08em;
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
            color: rgba(255, 255, 255, .55);
            line-height: 1.5;
        }

        /* Quote */
        .fh-quote {
            background: rgba(255, 255, 255, .07);
            border: 1px solid rgba(255, 255, 255, .11);
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
            color: rgba(255, 255, 255, .6);
            line-height: 1.7;
            font-style: italic;
        }

        .fh-quote-author {
            margin-top: 8px;
            font-size: 10.5px;
            color: rgba(255, 255, 255, .28);
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        /* Right panel */
        .fh-right {
            flex: 1;
            min-width: 0;
            background: #fff;
            border-radius: 20px;
            padding: 2.5rem 2.25rem;
            box-shadow: 0 0 0 1px rgba(100, 140, 210, .12), 0 24px 60px rgba(60, 100, 180, .1);
            animation: pubCardIn .55s cubic-bezier(.16, 1, .3, 1) .08s both;
        }

        /* Form header */
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
            letter-spacing: .07em;
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
            letter-spacing: .07em;
            margin-bottom: 6px;
        }

        .fh-label .req,
        .fh-field .req {
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
            transition: border-color .2s, background .2s, box-shadow .2s;
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
            box-shadow: 0 0 0 3px rgba(26, 95, 200, .1);
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

        /* Search pendamping */
        .fh-search-wrap {
            position: relative;
        }

        .fh-search-dropdown {
            position: absolute;
            width: 100%;
            background: #fff;
            border: 1px solid #E0E7F0;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(60, 100, 180, .1);
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
            transition: background .15s;
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
            transition: border-color .2s, background .2s;
        }

        .fh-file-input:hover {
            border-color: #1A5FC8;
            background: #EEF4FF;
        }

        .fh-file-input:focus {
            outline: none;
            border-color: #1A5FC8;
            box-shadow: 0 0 0 3px rgba(26, 95, 200, .1);
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

        /* Produk list */
        .fh-produk-list {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .fh-produk-item {
            background: #F8FAFF;
            border: 1px solid #E0E7F0;
            border-radius: 14px;
            padding: 1.1rem 1.25rem;
            margin-bottom: 10px;
            position: relative;
            animation: itemSlideIn .28s cubic-bezier(.16, 1, .3, 1) both;
        }

        @keyframes itemSlideIn {
            from {
                opacity: 0;
                transform: translateY(-10px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        .fh-produk-item-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: .85rem;
        }

        .fh-produk-item-title {
            font-size: 12px;
            font-weight: 600;
            color: #1A5FC8;
            display: flex;
            align-items: center;
            gap: 7px;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .fh-produk-item-title .produk-num {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #1A5FC8;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
        }

        .fh-produk-item-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        /* Buttons */
        .fh-btn-remove {
            display: flex;
            align-items: center;
            gap: 5px;
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 12px;
            color: #EF4444;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            transition: all .2s;
        }

        .fh-btn-remove:hover {
            background: #EF4444;
            color: #fff;
            border-color: #EF4444;
        }

        .fh-btn-remove svg {
            width: 13px;
            height: 13px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
        }

        .fh-btn-add-produk {
            display: flex;
            align-items: center;
            gap: 7px;
            width: 100%;
            padding: 10px 16px;
            background: #F5F7FB;
            border: 1.5px dashed #C8D3E8;
            border-radius: 10px;
            font-size: 13.5px;
            color: #6B7A99;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            transition: all .2s;
            margin-bottom: 1.25rem;
        }

        .fh-btn-add-produk:hover {
            border-color: #1A5FC8;
            color: #1A5FC8;
            background: #EEF4FF;
        }

        .fh-btn-add-produk svg {
            width: 16px;
            height: 16px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
        }

        .fh-max-notice {
            font-size: 12px;
            color: #F59E0B;
            display: none;
            margin-bottom: 1rem;
        }

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
            transition: all .2s;
            box-shadow: 0 4px 16px rgba(26, 95, 200, .28);
            position: relative;
            overflow: hidden;
        }

        .fh-submit::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, .1) 0%, transparent 100%);
            opacity: 0;
            transition: opacity .2s;
        }

        .fh-submit:hover::before {
            opacity: 1;
        }

        .fh-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 28px rgba(26, 95, 200, .36);
        }

        .fh-submit:active {
            transform: translateY(0);
        }

        .fh-submit:disabled {
            opacity: .7;
            cursor: not-allowed;
            transform: none;
        }

        .fh-submit svg {
            width: 17px;
            height: 17px;
            fill: none;
            stroke: rgba(255, 255, 255, .85);
            stroke-width: 2;
        }

        /* Back link & footer */
        .fh-back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #8A99B3;
            text-decoration: none;
            transition: color .2s;
            margin-top: 1rem;
        }

        .fh-back-link:hover {
            color: #1A5FC8;
        }

        .fh-back-link svg {
            width: 14px;
            height: 14px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
        }

        .fh-footer {
            text-align: center;
            margin-top: 1.75rem;
            font-size: 11.5px;
            color: #B0BCCE;
        }

        /* Responsive */
        @media (max-width:768px) {
            .fh-wrap {
                flex-direction: column;
            }

            .fh-left {
                width: 100%;
                position: static;
            }

            .fh-info-box {
                display: none;
            }

            .fh-photo-grid,
            .fh-produk-item-grid {
                grid-template-columns: 1fr;
            }

            #ktpPreviewContainer {
                flex-direction: column;
                align-items: stretch !important;
            }

            #ktpPreviewContainer #btnScanKtp {
                width: 100% !important;
            }
        }

        /* Scan button */
        .fh-btn-scan {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            height: 42px;
            width: 100%;
            background: linear-gradient(135deg, #1A5FC8 0%, #1040A0 100%);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            transition: all .2s;
            box-shadow: 0 3px 10px rgba(26, 95, 200, .2);
        }

        .fh-btn-scan:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(26, 95, 200, .3);
        }

        .fh-btn-scan:disabled {
            opacity: .6;
            cursor: not-allowed;
            transform: none;
        }

        .fh-btn-scan .spinner {
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255,255,255,.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* OCR result overlay */
        .ocr-result-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: opacity .3s, visibility .3s;
        }

        .ocr-result-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .ocr-result-box {
            background: #fff;
            border-radius: 16px;
            padding: 1.5rem;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0,0,0,.3);
        }

        .ocr-result-title {
            font-family: 'Sora', sans-serif;
            font-size: 16px;
            font-weight: 600;
            color: #0F1F40;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ocr-result-title svg {
            width: 20px;
            height: 20px;
            stroke: #059669;
        }

        .ocr-field {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #EDF0F7;
            font-size: 13px;
        }

        .ocr-field:last-child {
            border-bottom: none;
        }

        .ocr-field-label {
            color: #6B7A99;
        }

        .ocr-field-value {
            color: #0F1F40;
            font-weight: 500;
            text-align: right;
            max-width: 60%;
        }

        .ocr-field-value.success {
            color: #059669;
        }

        .ocr-field-value.empty {
            color: #B0BCCE;
            font-style: italic;
        }

        .ocr-actions {
            display: flex;
            gap: 8px;
            margin-top: 1rem;
        }

        .ocr-actions button {
            flex: 1;
            padding: 10px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s;
        }

        .ocr-btn-apply {
            background: #059669;
            color: #fff;
            border: none;
        }

        .ocr-btn-apply:hover {
            background: #047857;
        }

        .ocr-btn-retry {
            background: #F5F7FB;
            color: #6B7A99;
            border: 1px solid #E0E7F0;
        }

        .ocr-btn-retry:hover {
            background: #EDF0F7;
        }

        /* Loading overlay */
        .loading-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.6);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            opacity: 0;
            visibility: hidden;
            transition: opacity .3s, visibility .3s;
        }

        .loading-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255,255,255,.2);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .8s linear infinite;
            margin-bottom: 1rem;
        }

        .loading-text {
            color: #fff;
            font-size: 14px;
            font-weight: 500;
        }
    </style>

        <div class="fh-root">
            <div class="fh-orb fh-orb-1"></div>
            <div class="fh-orb fh-orb-2"></div>

            <div class="fh-wrap">

                {{-- â”€â”€ LEFT PANEL â”€â”€ --}}
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
                    <p class="fh-left-desc">Isi data lapangan dengan lengkap dan akurat agar proses sertifikasi berjalan
                        lancar.
                    </p>

                    <div class="fh-steps">
                        <div class="fh-step">
                            <div class="fh-step-num">1</div>
                            <div class="fh-step-text"><strong>Pilih Pendamping</strong>Cari dan pilih nama pendamping aktif
                            </div>
                        </div>
                        <div class="fh-step">
                            <div class="fh-step-num">2</div>
                            <div class="fh-step-text"><strong>Upload & Scan KTP</strong>Pindai KTP untuk mengisi biodata otomatis
                            </div>
                        </div>
                        <div class="fh-step">
                            <div class="fh-step-num">3</div>
                            <div class="fh-step-text"><strong>Biodata & Alamat PU</strong>Periksa NIK, Nama, Alamat & Wilayah
                            </div>
                        </div>
                        <div class="fh-step">
                            <div class="fh-step-num">4</div>
                            <div class="fh-step-text"><strong>Produk & Foto Lain</strong>Unggah foto produk, rumah & pendamping
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
                        <div class="fh-info-row">
                            <div class="fh-info-dot"></div>
                            <div class="fh-info-text">Maksimal 5 produk per pendaftaran</div>
                        </div>
                    </div>

                    <div class="fh-quote">
                        <div class="fh-quote-mark">"</div>
                        <p class="fh-quote-text">Data yang akurat adalah kunci keberhasilan program sertifikasi halal.</p>
                        <p class="fh-quote-author">â€” Kawulo Halal</p>
                    </div>
                </div>

                {{-- â”€â”€ RIGHT PANEL â”€â”€ --}}
                <div class="fh-right">

                    <div class="fh-form-header">
                        <h2>Form Data Lapangan</h2>
                        <p>Lengkapi semua data dengan benar dan teliti</p>
                    </div>
                    <div class="fh-divider"></div>

                    @if (session('success'))
                        <div class="alert-success-modern" id="alertSuccess">
                            <svg viewBox="0 0 24 24">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                <polyline points="22 4 12 14.01 9 11.01" />
                            </svg>
                            <div class="alt-text"><strong>Berhasil!</strong> {{ session('success') }}</div>
                            <button class="alt-close"
                                onclick="this.closest('.alert-success-modern').remove()">&times;</button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert-danger-modern" id="alertError">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            <div class="alt-text"><strong>Gagal!</strong> {{ session('error') }}</div>
                            <button class="alt-close"
                                onclick="this.closest('.alert-danger-modern').remove()">&times;</button>
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
                            <select id="enumerator_id" name="enumerator_id"
                                class="fh-select @error('enumerator_id') is-invalid @enderror" required
                                style="display:none;">
                                <option value="">-- Pilih Pendamping --</option>
                                @foreach ($enumerators as $enumerator)
                                    <option value="{{ $enumerator->id }}" data-name="{{ $enumerator->nama_lengkap }}"
                                        data-status="{{ $enumerator->status }}"
                                        {{ old('enumerator_id') == $enumerator->id ? 'selected' : '' }}>
                                        {{ $enumerator->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="selected_enumerator" style="display:none;">
                                <div class="fh-selected-box">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                        <circle cx="12" cy="7" r="4" />
                                    </svg>
                                    <span class="fh-selected-text">Terpilih: <span id="selected_name"></span></span>
                                </div>
                            </div>
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

                        {{-- SECTION 2: UPLOAD & SCAN KTP --}}
                        <div class="fh-section-title" style="margin-top:1.5rem;">
                            <span>Upload & Scan KTP</span>
                            <span style="font-size:10.5px; background:#EEF4FF; color:#1A5FC8; border:1px solid #D0E2FF; padding:2px 8px; border-radius:12px; font-weight:600; text-transform:none; letter-spacing:0;">Disarankan Pertama</span>
                        </div>

                        <div class="ktp-scan-card" style="background: #F8FAFF; border: 1px solid #E0E7F0; border-radius: 14px; padding: 1.25rem; margin-bottom: 1.5rem;">
                            <div style="display: flex; align-items: flex-start; gap: 12px; margin-bottom: 0.85rem;">
                                <div style="width: 38px; height: 38px; border-radius: 10px; background: #EEF4FF; border: 1px solid #D0E2FF; color: #1A5FC8; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                        <circle cx="8.5" cy="8.5" r="1.5"/>
                                        <polyline points="21 15 16 10 5 21"/>
                                    </svg>
                                </div>
                                <div style="flex: 1;">
                                    <div style="font-family: 'Sora', sans-serif; font-size: 13.5px; font-weight: 600; color: #0F1F40; margin-bottom: 2px;">
                                        Upload Foto KTP Pelaku Usaha <span class="req">*</span>
                                    </div>
                                    <p style="font-size: 12px; color: #6B7A99; margin: 0; line-height: 1.5;">
                                        Unggah foto KTP terlebih dahulu agar data NIK, Nama, Tanggal Lahir & Alamat terisi otomatis.
                                    </p>
                                </div>
                            </div>

                            <div class="fh-field" style="margin-bottom: 0;">
                                <input type="file" id="foto_ktp" name="foto_ktp"
                                    class="fh-file-input @error('foto_ktp') is-invalid @enderror" accept="image/*"
                                    required>
                                <span class="fh-hint">Format: JPG/PNG, Maksimal 10MB. Foto harus jelas dan tidak buram.</span>
                                @error('foto_ktp')
                                    <span class="fh-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- KTP Preview & Scan Button --}}
                            <div id="ktpPreviewContainer" style="display: none; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #EDF0F7; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <img id="ktpPreviewImg" src="" alt="Preview KTP" style="width: 64px; height: 42px; object-fit: cover; border-radius: 6px; border: 1px solid #E0E7F0;">
                                    <div>
                                        <span id="ktpFileName" style="font-size: 12.5px; font-weight: 600; color: #0F1F40; display: block; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"></span>
                                        <span style="font-size: 11px; color: #1A5FC8; font-weight: 500;">Foto KTP Siap Dipindai</span>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" id="btnScanKtp" class="fh-btn-scan" style="width: auto; padding: 0 1.15rem; height: 38px; font-size: 12.5px;">
                                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 7V4h3M20 7V4h-3M4 17v3h3M20 17v3h-3" />
                                            <line x1="4" y1="12" x2="20" y2="12" stroke-dasharray="2 2" />
                                        </svg>
                                        Pindai KTP (AI Scan)
                                    </button>
                                </div>
                            </div>

                            <div id="ktpScanSuccessBadge" style="display: none; margin-top: 0.75rem; background: #EBF5FF; border: 1px solid #BAD7F5; border-radius: 10px; padding: 10px 14px; align-items: center; gap: 10px; font-size: 12.5px; color: #1552A0;">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#1A5FC8" stroke-width="2" style="flex-shrink:0;">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <div>
                                    <strong>Data KTP Berhasil Dipindai!</strong><br>
                                    <span style="font-size: 11.5px; color: #4A6385;">Informasi NIK, Nama, Tanggal Lahir, Alamat, dan Wilayah telah terisi otomatis pada form di bawah.</span>
                                </div>
                            </div>
                        </div>

                        {{-- SECTION 3: DATA PU --}}
                        <div class="fh-section-title" style="margin-top:1.5rem; display:flex; align-items:center; justify-content:space-between;">
                            <span>Data Pelaku Usaha</span>
                            <span style="font-size:11px; color:#8A99B3; font-weight:400; text-transform:none; letter-spacing:0;">Terisi otomatis dari Scan KTP</span>
                        </div>

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
                                <label class="fh-label" for="telephone">Nomor Telepon <span
                                        class="req">*</span></label>
                                <input type="text" id="telephone" name="telephone"
                                    class="fh-input @error('telephone') is-invalid @enderror"
                                    value="{{ old('telephone') }}" required placeholder="Contoh: 081234567890"
                                    maxlength="15" inputmode="numeric">
                                <span class="fh-hint">Nomor telepon aktif (10–15 digit)</span>
                                @error('telephone')
                                    <span class="fh-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="fh-field">
                                <label class="fh-label" for="nik">NIK <span class="req">*</span></label>
                                <input type="text" id="nik" name="nik"
                                    class="fh-input @error('nik') is-invalid @enderror" value="{{ old('nik') }}"
                                    required placeholder="Masukkan NIK (16 digit)" maxlength="16" inputmode="numeric">
                                <div class="fh-nik-row">
                                    <span class="fh-nik-count" id="nikCounter">0/16 digit</span>
                                    <span class="fh-nik-status err" id="nikStatus">Belum lengkap</span>
                                </div>
                                @error('nik')
                                    <span class="fh-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="fh-field">
                                <label class="fh-label" for="tanggal_lahir">Tanggal Lahir</label>
                                <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                                    class="fh-input @error('tanggal_lahir') is-invalid @enderror"
                                    value="{{ old('tanggal_lahir') }}"
                                    placeholder="Pilih tanggal lahir">
                                @error('tanggal_lahir')
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

                            {{-- SECTION 3B: ALAMAT LENGKAP (Wilayah Indonesia) --}}
                            <div class="fh-section-title" style="margin-top:1.5rem;">Alamat Lengkap (Wilayah)</div>

                            <div class="fh-field">
                                <label class="fh-label" for="provinsi">Provinsi <span class="req">*</span></label>
                                <select id="provinsi" name="provinsi" class="fh-select @error('provinsi') is-invalid @enderror" required>
                                    <option value="">-- Pilih Provinsi --</option>
                                </select>
                                <span class="fh-hint">Pilih provinsi sesuai KTP</span>
                                @error('provinsi')
                                    <span class="fh-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="fh-field">
                                <label class="fh-label" for="kabupaten">Kabupaten/Kota <span class="req">*</span></label>
                                <select id="kabupaten" name="kabupaten" class="fh-select @error('kabupaten') is-invalid @enderror" required disabled>
                                    <option value="">-- Pilih Kabupaten/Kota --</option>
                                </select>
                                @error('kabupaten')
                                    <span class="fh-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="fh-field">
                                <label class="fh-label" for="kecamatan">Kecamatan <span class="req">*</span></label>
                                <select id="kecamatan" name="kecamatan" class="fh-select @error('kecamatan') is-invalid @enderror" required disabled>
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                                @error('kecamatan')
                                    <span class="fh-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="fh-field">
                                <label class="fh-label" for="kelurahan">Desa/Kelurahan <span class="req">*</span></label>
                                <select id="kelurahan" name="kelurahan" class="fh-select @error('kelurahan') is-invalid @enderror" required disabled>
                                    <option value="">-- Pilih Desa/Kelurahan --</option>
                                </select>
                                @error('kelurahan')
                                    <span class="fh-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px;">
                                <div class="fh-field">
                                    <label class="fh-label" for="rt">RT</label>
                                    <input type="text" id="rt" name="rt" class="fh-input @error('rt') is-invalid @enderror"
                                        value="{{ old('rt') }}" placeholder="001" maxlength="3" inputmode="numeric">
                                    @error('rt')
                                        <span class="fh-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="fh-field">
                                    <label class="fh-label" for="rw">RW</label>
                                    <input type="text" id="rw" name="rw" class="fh-input @error('rw') is-invalid @enderror"
                                        value="{{ old('rw') }}" placeholder="005" maxlength="3" inputmode="numeric">
                                    @error('rw')
                                        <span class="fh-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="fh-field">
                                    <label class="fh-label" for="kode_pos">Kode Pos</label>
                                    <input type="text" id="kode_pos" name="kode_pos" class="fh-input @error('kode_pos') is-invalid @enderror"
                                        value="{{ old('kode_pos') }}" placeholder="12345" maxlength="5" inputmode="numeric">
                                    @error('kode_pos')
                                        <span class="fh-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- SECTION 4: PRODUK --}}
                            <div class="fh-section-title" style="margin-top:1.5rem;">Produk</div>

                            {{-- Produk Utama --}}
                            <div class="fh-produk-item" style="border-color:#BAD7F5; background:#F0F6FF;">
                                <div class="fh-produk-item-header">
                                    <div class="fh-produk-item-title">
                                        <div class="produk-num">1</div>
                                        Produk Utama
                                    </div>
                                </div>
                                <div class="fh-produk-item-grid">
                                    <div class="fh-field" style="margin-bottom:0;">
                                        <label class="fh-label" for="nama_produk">Nama Produk <span
                                                class="req">*</span></label>
                                        <input type="text" id="nama_produk" name="nama_produk"
                                            class="fh-input @error('nama_produk') is-invalid @enderror"
                                            value="{{ old('nama_produk') }}" required placeholder="Nama produk utama">
                                        @error('nama_produk')
                                            <span class="fh-error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="fh-field" style="margin-bottom:0;">
                                        <label class="fh-label" for="foto_produk">Foto Produk <span
                                                class="req">*</span></label>
                                        <input type="file" id="foto_produk" name="foto_produk"
                                            class="fh-file-input @error('foto_produk') is-invalid @enderror"
                                            accept="image/*" required>
                                        <span class="fh-hint">JPG/PNG. Maks 10MB</span>
                                        @error('foto_produk')
                                            <span class="fh-error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Produk Tambahan (dinamis) --}}
                            <div id="produkTambahanList" class="fh-produk-list"></div>

                            {{-- Tombol Tambah Produk --}}
                            <button type="button" class="fh-btn-add-produk" id="btnAddProduk">
                                <svg viewBox="0 0 24 24">
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                                Tambah Produk Lain
                            </button>
                            <p class="fh-max-notice" id="maxProdukNotice">Maksimal 5 produk telah tercapai.</p>

                            {{-- SECTION 5: DOKUMENTASI LAINNYA --}}
                            <div class="fh-section-title" style="margin-top:0.5rem;">Dokumentasi Tambahan</div>

                            <div class="fh-photo-grid">
                                <div class="fh-field">
                                    <label class="fh-label" for="foto_rumah">Foto Rumah <span
                                            class="req">*</span></label>
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
                                        class="fh-file-input @error('foto_pendamping') is-invalid @enderror"
                                        accept="image/*" required>
                                    <span class="fh-hint">JPG/PNG. Maks 10MB</span>
                                    @error('foto_pendamping')
                                        <span class="fh-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="fh-field">
                                    <label class="fh-label" for="foto_proses">
                                        Foto Proses
                                        <span
                                            style="font-size:10px;font-weight:400;color:#B0BCCE;text-transform:none;letter-spacing:0;margin-left:4px;">(opsional)</span>
                                    </label>
                                    <input type="file" id="foto_proses" name="foto_proses"
                                        class="fh-file-input @error('foto_proses') is-invalid @enderror"
                                        accept="image/*">
                                    <span class="fh-hint">JPG/PNG. Maks 10MB — boleh dikosongkan</span>
                                    @error('foto_proses')
                                        <span class="fh-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div style="margin-top:1.5rem;">
                                <button class="fh-submit" type="submit" id="submitBtn">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                        <polyline points="17 21 17 13 7 13 7 21" />
                                        <polyline points="7 3 7 8 15 8" />
                                    </svg>
                                    Simpan Data
                                </button>
                            </div>

                        </div>{{-- end #formFields --}}

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
                        <script>document.write(new Date().getFullYear())</script> Kawulo Halal. All rights reserved.
                    </div>
                </div>

            </div>
        </div>

        {{-- Loading Overlay --}}
        <div class="loading-overlay" id="loadingOverlay">
            <div class="loading-spinner"></div>
            <div class="loading-text" id="loadingText">Memindai KTP...</div>
        </div>

        {{-- OCR Result Overlay --}}
        <div class="ocr-result-overlay" id="ocrResultOverlay">
            <div class="ocr-result-box">
                <div class="ocr-result-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Hasil Pemindaian KTP
                </div>
                <div id="ocrResultContent">
                    {{-- Dynamically filled by JS --}}
                </div>
                <div class="ocr-actions">
                    <button type="button" class="ocr-btn-retry" onclick="closeOcrResult()">Tutup</button>
                    <button type="button" class="ocr-btn-apply" onclick="applyOcrResult()">Terapkan Data</button>
                </div>
            </div>
        </div>

        {{-- Inject enumerator status map dari PHP ke JS --}}
        <script>
            const enumeratorStatusMap = {
                @foreach ($enumerators as $enumerator)
                    {{ $enumerator->id }}: "{{ $enumerator->status }}",
                @endforeach
            };

            // OCR Result Data (will be populated after scan)
            let ocrResultData = null;

            // Show loading overlay
            function showLoading(text = 'Memindai KTP...') {
                document.getElementById('loadingText').textContent = text;
                document.getElementById('loadingOverlay').classList.add('active');
            }

            // Hide loading overlay
            function hideLoading() {
                document.getElementById('loadingOverlay').classList.remove('active');
            }

            // Show OCR result
            function showOcrResult(data) {
                ocrResultData = data;
                const content = document.getElementById('ocrResultContent');
                const fields = [
                    { label: 'NIK', key: 'nik' },
                    { label: 'Nama', key: 'nama' },
                    { label: 'Tempat Lahir', key: 'tempat_lahir' },
                    { label: 'Tanggal Lahir', key: 'tanggal_lahir' },
                    { label: 'Jenis Kelamin', key: 'jenis_kelamin' },
                    { label: 'Alamat', key: 'alamat' },
                    { label: 'RT/RW', key: 'rt', suffix: '/', key2: 'rw' },
                    { label: 'Kelurahan', key: 'kelurahan' },
                    { label: 'Kecamatan', key: 'kecamatan' },
                    { label: 'Kabupaten', key: 'kabupaten' },
                    { label: 'Provinsi', key: 'provinsi' },
                    { label: 'Kode Pos', key: 'kode_pos' },
                ];

                let html = '';
                fields.forEach(field => {
                    let value = data[field.key] || '-';
                    if (field.key2 && data[field.key2]) {
                        value = (data[field.key] || '-') + field.suffix + data[field.key2];
                    }
                    const isEmpty = value === '-' || value === '';
                    html += `
                        <div class="ocr-field">
                            <span class="ocr-field-label">${field.label}</span>
                            <span class="ocr-field-value ${isEmpty ? 'empty' : 'success'}">${isEmpty ? 'Tidak terdeteksi' : value}</span>
                        </div>
                    `;
                });
                content.innerHTML = html;
                document.getElementById('ocrResultOverlay').classList.add('active');
            }

            // Close OCR result overlay
            function closeOcrResult() {
                document.getElementById('ocrResultOverlay').classList.remove('active');
            }

            // Apply OCR result to form
            function applyOcrResult() {
                if (!ocrResultData) return;

                // Apply NIK
                if (ocrResultData.nik) {
                    document.getElementById('nik').value = ocrResultData.nik;
                    document.getElementById('nik').dispatchEvent(new Event('input'));
                }

                // Apply Nama
                if (ocrResultData.nama) {
                    document.getElementById('nama_pu').value = ocrResultData.nama.toUpperCase();
                }

                // Apply Alamat
                if (ocrResultData.alamat) {
                    document.getElementById('alamat').value = ocrResultData.alamat;
                }

                // Apply Tanggal Lahir — Gemini mengembalikan format dd-mm-yyyy atau dd/mm/yyyy
                // HTML date input butuh format yyyy-mm-dd
                if (ocrResultData.tanggal_lahir) {
                    try {
                        const rawTgl = ocrResultData.tanggal_lahir.trim();
                        // Match pola dd-mm-yyyy atau dd/mm/yyyy
                        const tglMatch = rawTgl.match(/^(\d{1,2})[\-\/](\d{1,2})[\-\/](\d{4})$/);
                        if (tglMatch) {
                            const dd = tglMatch[1].padStart(2, '0');
                            const mm = tglMatch[2].padStart(2, '0');
                            const yyyy = tglMatch[3];
                            document.getElementById('tanggal_lahir').value = `${yyyy}-${mm}-${dd}`;
                        } else {
                            // Coba langsung jika sudah format yyyy-mm-dd
                            document.getElementById('tanggal_lahir').value = rawTgl;
                        }
                    } catch(e) {
                        console.warn('Format tanggal lahir tidak dikenali:', ocrResultData.tanggal_lahir);
                    }
                }

                // Apply RT/RW
                if (ocrResultData.rt) {
                    document.getElementById('rt').value = ocrResultData.rt;
                }
                if (ocrResultData.rw) {
                    document.getElementById('rw').value = ocrResultData.rw;
                }

                // Apply Provinsi (with cascade)
                if (ocrResultData.provinsi) {
                    selectWilayahByName('provinsi', ocrResultData.provinsi, function() {
                        // After province selected, select kabupaten
                        if (ocrResultData.kabupaten) {
                            selectWilayahByName('kabupaten', ocrResultData.kabupaten, function() {
                                // After kabupaten selected, select kecamatan
                                if (ocrResultData.kecamatan) {
                                    selectWilayahByName('kecamatan', ocrResultData.kecamatan, function() {
                                        // After kecamatan selected, select kelurahan
                                        if (ocrResultData.kelurahan) {
                                            selectWilayahByName('kelurahan', ocrResultData.kelurahan, function() {
                                                // Auto-fetch kode_pos based on selected village
                                                fetchKodePos();
                                            });
                                        }
                                    });
                                }
                            });
                        }
                    });
                }

                closeOcrResult();
                hideLoading();

                // Tampilkan badge sukses scan KTP
                const successBadge = document.getElementById('ktpScanSuccessBadge');
                if (successBadge) {
                    successBadge.style.display = 'flex';
                }

                // Scroll halus ke field biodata
                const formFields = document.getElementById('formFields');
                if (formFields) {
                    formFields.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }

            // Pilih wilayah berdasarkan nama (helper untuk OCR apply)
            // Menggunakan event 'wilayah:loaded' agar tidak ada race condition
            function selectWilayahByName(selectId, name, callback) {
                const select = document.getElementById(selectId);

                function tryMatch() {
                    const options = select.options;
                    const nameLower = name.toLowerCase().trim();
                    for (let i = 0; i < options.length; i++) {
                        const optText = (options[i].text || '').toLowerCase().trim();
                        if (optText === nameLower || optText.includes(nameLower) || nameLower.includes(optText)) {
                            select.value = options[i].value;
                            select.dispatchEvent(new Event('change'));
                            // Tunggu sampai opsi berikutnya selesai dimuat
                            setTimeout(callback, 700);
                            return true;
                        }
                    }
                    return false;
                }

                // Jika select sudah punya opsi (>1), langsung match
                if (select.options.length > 1) {
                    if (!tryMatch()) {
                        console.warn('Wilayah tidak ditemukan:', selectId, name);
                        callback();
                    }
                } else {
                    // Tunggu event 'wilayah:loaded' yang akan di-dispatch setelah populateSelect
                    select.addEventListener('wilayah:loaded', function handler() {
                        select.removeEventListener('wilayah:loaded', handler);
                        if (!tryMatch()) {
                            console.warn('Wilayah tidak ditemukan:', selectId, name);
                            callback();
                        }
                    }, { once: true });
                }
            }

            // Scan KTP function
            async function scanKtp() {
                const fileInput = document.getElementById('foto_ktp');
                const file = fileInput.files[0];

                if (!file) {
                    alert('Silakan upload foto KTP terlebih dahulu');
                    return;
                }

                showLoading('Memindai KTP dengan AI...');

                const formData = new FormData();
                formData.append('foto_ktp', file);

                try {
                    const response = await fetch('{{ route('api.scan-ktp') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: formData
                    });

                    const result = await response.json();
                    hideLoading();

                    if (result.success) {
                        showOcrResult(result.data);
                    } else {
                        alert(result.message || 'Gagal memindai KTP');
                    }
                } catch (error) {
                    hideLoading();
                    console.error('OCR Error:', error);
                    alert('Terjadi kesalahan saat memindai KTP');
                }
            }

            // Wilayah cascade functions
            let wilayahCache = {
                provinces: [],
                regencies: {},
                districts: {},
                villages: {}
            };

            async function loadProvinces() {
                try {
                    const response = await fetch('/api/wilayah/provinces');
                    const result = await response.json();
                    if (result.success) {
                        wilayahCache.provinces = result.data;
                        populateSelect('provinsi', result.data);
                    }
                } catch (error) {
                    console.error('Error loading provinces:', error);
                }
            }

            async function loadRegencies(provinceCode) {
                if (!provinceCode) return;

                try {
                    const response = await fetch(`/api/wilayah/regencies?code=${provinceCode}`);
                    const result = await response.json();
                    if (result.success) {
                        wilayahCache.regencies[provinceCode] = result.data;
                        populateSelect('kabupaten', result.data);
                        document.getElementById('kabupaten').disabled = false;
                        // Reset downstream selects
                        resetSelect('kecamatan');
                        resetSelect('kelurahan');
                    }
                } catch (error) {
                    console.error('Error loading regencies:', error);
                }
            }

            async function loadDistricts(regencyCode) {
                if (!regencyCode) return;

                try {
                    const response = await fetch(`/api/wilayah/districts?code=${regencyCode}`);
                    const result = await response.json();
                    if (result.success) {
                        wilayahCache.districts[regencyCode] = result.data;
                        populateSelect('kecamatan', result.data);
                        document.getElementById('kecamatan').disabled = false;
                        resetSelect('kelurahan');
                    }
                } catch (error) {
                    console.error('Error loading districts:', error);
                }
            }

            async function loadVillages(districtCode) {
                if (!districtCode) return;

                try {
                    const response = await fetch(`/api/wilayah/villages?code=${districtCode}`);
                    const result = await response.json();
                    if (result.success) {
                        wilayahCache.villages[districtCode] = result.data;
                        populateSelect('kelurahan', result.data);
                        document.getElementById('kelurahan').disabled = false;
                        // Auto-fetch kode_pos when villages are loaded (for OCR auto-fill)
                        setTimeout(fetchKodePos, 100);
                    }
                } catch (error) {
                    console.error('Error loading villages:', error);
                }
            }

            /**
             * Fetch kode_pos from API based on selected village, kecamatan, and kabupaten.
             * Auto-fills the kode_pos input field.
             * Has retry logic for when dropdown options are not yet populated.
             */
            async function fetchKodePos(retries = 0) {
                const kelurahanSelect = document.getElementById('kelurahan');
                const selectedOption = kelurahanSelect.options[kelurahanSelect.selectedIndex];

                // If no option selected or placeholder is shown, skip
                if (!selectedOption || !selectedOption.value || selectedOption.value === '') {
                    return;
                }

                const kelurahan = selectedOption.getAttribute('data-name') || selectedOption.textContent;

                // If placeholder text, retry once after short delay
                if (!kelurahan || kelurahan === '-- Pilih --' || kelurahan === '-- Pilih --') {
                    if (retries < 3) {
                        await new Promise(r => setTimeout(r, 300));
                        return fetchKodePos(retries + 1);
                    }
                    return;
                }

                // Get kecamatan name from selected option
                const kecamatanSelect = document.getElementById('kecamatan');
                const kecamatanOption = kecamatanSelect.options[kecamatanSelect.selectedIndex];
                const kecamatan = kecamatanOption ? (kecamatanOption.getAttribute('data-name') || kecamatanOption.textContent) : '';

                // Get kabupaten name from selected option
                const kabupatenSelect = document.getElementById('kabupaten');
                const kabupatenOption = kabupatenSelect.options[kabupatenSelect.selectedIndex];
                const kabupaten = kabupatenOption ? (kabupatenOption.getAttribute('data-name') || kabupatenOption.textContent) : '';

                try {
                    const params = new URLSearchParams({
                        kelurahan: kelurahan,
                        kecamatan: kecamatan || '',
                        kabupaten: kabupaten || ''
                    });

                    const response = await fetch(`/api/wilayah/kodepos?${params}`);
                    const result = await response.json();

                    if (result.success && result.data.found && result.data.kode_pos) {
                        document.getElementById('kode_pos').value = result.data.kode_pos;
                    }
                } catch (error) {
                    console.error('Error fetching kode pos:', error);
                }
            }

            function populateSelect(selectId, data) {
                const select = document.getElementById(selectId);
                select.innerHTML = '<option value="">-- Pilih --</option>';
                data.forEach(item => {
                    const option = document.createElement('option');
                    // value = code (untuk trigger cascade API)
                    // data-name = text name (yang dikirim ke DB via hidden input)
                    option.value = item.code;
                    option.setAttribute('data-name', item.name);
                    option.textContent = item.name;
                    select.appendChild(option);
                });
                // Dispatch event agar selectWilayahByName tahu data sudah siap
                select.dispatchEvent(new Event('wilayah:loaded'));
            }

            function resetSelect(selectId) {
                const select = document.getElementById(selectId);
                select.innerHTML = '<option value="">-- Pilih --</option>';
                select.disabled = true;
            }

            function padRtRw(value) {
                if (!value) return '';
                value = value.replace(/\D/g, '');
                return value.padStart(3, '0').substring(0, 3);
            }

            // Event Listeners
            document.addEventListener('DOMContentLoaded', function() {
                // Load provinces on page load
                loadProvinces();

                // Provinsi change -> load regencies
                document.getElementById('provinsi').addEventListener('change', function() {
                    loadRegencies(this.value);
                });

                // Kabupaten change -> load districts
                document.getElementById('kabupaten').addEventListener('change', function() {
                    loadDistricts(this.value);
                });

                // Kecamatan change -> load villages
                document.getElementById('kecamatan').addEventListener('change', function() {
                    loadVillages(this.value);
                });

                // Kelurahan change -> auto-fetch kode_pos
                document.getElementById('kelurahan').addEventListener('change', function() {
                    fetchKodePos();
                });

                // RT/RW auto-pad to 3 digits
                document.getElementById('rt').addEventListener('blur', function() {
                    this.value = padRtRw(this.value);
                });
                document.getElementById('rw').addEventListener('blur', function() {
                    this.value = padRtRw(this.value);
                });

                // Show scan button & image preview when KTP photo is uploaded
                document.getElementById('foto_ktp').addEventListener('change', function() {
                    const file = this.files[0];
                    const previewContainer = document.getElementById('ktpPreviewContainer');
                    const previewImg = document.getElementById('ktpPreviewImg');
                    const fileNameSpan = document.getElementById('ktpFileName');

                    if (file) {
                        fileNameSpan.textContent = file.name;
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImg.src = e.target.result;
                            previewContainer.style.display = 'flex';
                        };
                        reader.readAsDataURL(file);
                    } else {
                        previewContainer.style.display = 'none';
                    }
                });

                // Scan button click
                document.getElementById('btnScanKtp').addEventListener('click', scanKtp);

                // PENTING: sebelum form di-submit, ganti value select wilayah
                // dari kode (digunakan untuk cascade API) ke nama teks (yang disimpan ke DB)
                document.getElementById('formDataLapangan').addEventListener('submit', function() {
                    ['provinsi', 'kabupaten', 'kecamatan', 'kelurahan'].forEach(function(id) {
                        const sel = document.getElementById(id);
                        if (!sel || !sel.value) return;
                        const selectedOpt = sel.options[sel.selectedIndex];
                        const textName = selectedOpt
                            ? (selectedOpt.getAttribute('data-name') || selectedOpt.textContent)
                            : '';
                        if (textName && textName.trim()) {
                            const hidden = document.createElement('input');
                            hidden.type  = 'hidden';
                            hidden.name  = id;
                            hidden.value = textName.trim();
                            sel.parentNode.appendChild(hidden);
                            sel.removeAttribute('name'); // cegah duplikasi saat submit
                        }
                    });
                });
            });
        </script>
        <script src="{{ asset('assets/js/form-halal.js') }}"></script>
    </div>{{-- end .fh-root --}}
@endsection
