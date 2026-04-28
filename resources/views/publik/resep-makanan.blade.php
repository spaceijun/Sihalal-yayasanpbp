@extends('layouts.guest')

@section('title', 'Resep Makanan Nusantara')

@section('content')
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --c-bg: #faf7f2;
            --c-paper: #fff9f0;
            --c-cream: #f5ede0;
            --c-warm: #ede0cc;
            --c-amber: #c9773a;
            --c-amber-dk: #a35e27;
            --c-amber-lt: #f2d4b6;
            --c-green: #3d6651;
            --c-green-lt: #dff0e8;
            --c-ink: #1a1208;
            --c-ink-70: #4a3a28;
            --c-ink-40: #9c8874;
            --c-ink-20: #d9ccbd;
            --font-display: 'Playfair Display', serif;
            --font-body: 'DM Sans', sans-serif;
            --font-mono: 'DM Mono', monospace;
            --radius-sm: 8px;
            --radius-md: 16px;
            --radius-lg: 24px;
            --radius-xl: 36px;
            --shadow-card: 0 2px 12px rgba(26, 18, 8, .07), 0 8px 32px rgba(26, 18, 8, .05);
            --shadow-hover: 0 12px 40px rgba(201, 119, 58, .18), 0 4px 16px rgba(26, 18, 8, .08);
            --shadow-modal: 0 32px 80px rgba(26, 18, 8, .35), 0 8px 24px rgba(26, 18, 8, .15);
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: var(--c-bg);
            color: var(--c-ink);
            font-family: var(--font-body);
            font-weight: 400;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
            background-size: 200px;
            opacity: 0.5;
        }

        /* ── Blobs ── */
        .bg-blobs {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
        }

        .blob-1 {
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(201, 119, 58, .12) 0%, transparent 70%);
            top: -200px;
            left: -200px;
        }

        .blob-2 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(61, 102, 81, .1) 0%, transparent 70%);
            bottom: -100px;
            right: -100px;
        }

        .blob-3 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(201, 119, 58, .08) 0%, transparent 70%);
            top: 50%;
            left: 60%;
        }

        /* ── Hero ── */
        .hero {
            position: relative;
            z-index: 10;
            min-height: 88vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 60px 24px 80px;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-family: var(--font-mono);
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--c-amber);
            background: var(--c-amber-lt);
            border: 1px solid rgba(201, 119, 58, .25);
            padding: 8px 20px;
            border-radius: 50px;
            margin-bottom: 32px;
        }

        .hero-eyebrow::before,
        .hero-eyebrow::after {
            content: '◆';
            font-size: 7px;
            opacity: .6;
        }

        .hero-title {
            font-family: var(--font-display);
            font-size: clamp(52px, 9vw, 100px);
            font-weight: 900;
            line-height: 1.0;
            color: var(--c-ink);
            margin-bottom: 12px;
            letter-spacing: -2px;
        }

        .hero-title em {
            font-style: italic;
            color: var(--c-amber);
            display: block;
        }

        .hero-divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 28px auto;
            width: fit-content;
        }

        .hero-divider span {
            width: 60px;
            height: 1px;
            background: var(--c-ink-20);
        }

        .hero-divider svg {
            color: var(--c-amber);
        }

        .hero-desc {
            font-size: 17px;
            color: var(--c-ink-70);
            max-width: 520px;
            margin: 0 auto 48px;
            line-height: 1.7;
            font-weight: 300;
        }

        .hero-cta {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--c-ink);
            color: var(--c-bg);
            font-size: 15px;
            font-weight: 600;
            padding: 16px 36px;
            border-radius: 50px;
            text-decoration: none;
            letter-spacing: .5px;
            transition: all .3s ease;
            position: relative;
            overflow: hidden;
        }

        .hero-cta::after {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--c-amber);
            transform: translateX(-101%);
            transition: transform .35s ease;
            z-index: 0;
        }

        .hero-cta:hover::after {
            transform: translateX(0);
        }

        .hero-cta span,
        .hero-cta svg {
            position: relative;
            z-index: 1;
        }

        .hero-cta:hover {
            color: #fff;
            box-shadow: 0 8px 32px rgba(201, 119, 58, .4);
        }

        .scroll-hint {
            position: absolute;
            bottom: 32px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            color: var(--c-ink-40);
            font-family: var(--font-mono);
            font-size: 10px;
            letter-spacing: 2px;
            text-transform: uppercase;
            animation: scrollBounce 2.5s ease-in-out infinite;
        }

        @keyframes scrollBounce {

            0%,
            100% {
                transform: translateX(-50%) translateY(0);
            }

            50% {
                transform: translateX(-50%) translateY(8px);
            }
        }

        /* ── Toolbar ── */
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(250, 247, 242, .85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--c-warm);
            padding: 16px 24px;
        }

        .toolbar-inner {
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .filters {
            display: flex;
            gap: 6px;
            background: var(--c-cream);
            padding: 6px;
            border-radius: 50px;
            border: 1px solid var(--c-warm);
        }

        .filter-btn {
            font-family: var(--font-body);
            font-size: 13px;
            font-weight: 600;
            color: var(--c-ink-70);
            background: transparent;
            border: none;
            padding: 9px 22px;
            border-radius: 50px;
            cursor: pointer;
            transition: all .25s ease;
        }

        .filter-btn:hover {
            color: var(--c-ink);
            background: rgba(201, 119, 58, .08);
        }

        .filter-btn.active {
            background: var(--c-amber);
            color: #fff;
            box-shadow: 0 4px 16px rgba(201, 119, 58, .35);
        }

        .search-wrap {
            position: relative;
            flex: 1;
            max-width: 340px;
        }

        .search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--c-ink-40);
            pointer-events: none;
        }

        .search-input {
            width: 100%;
            font-family: var(--font-body);
            font-size: 14px;
            color: var(--c-ink);
            background: #fff;
            border: 1.5px solid var(--c-warm);
            border-radius: 50px;
            padding: 11px 20px 11px 44px;
            outline: none;
            transition: all .25s ease;
        }

        .search-input::placeholder {
            color: var(--c-ink-40);
        }

        .search-input:focus {
            border-color: var(--c-amber);
            box-shadow: 0 0 0 4px rgba(201, 119, 58, .12);
        }

        .recipe-count {
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--c-ink-40);
            white-space: nowrap;
        }

        .recipe-count strong {
            color: var(--c-amber);
            font-weight: 600;
        }

        /* ════════════════════════════════════════
                   CARD — FIX: ukuran gambar tidak boleh
                   mempengaruhi layout card sama sekali
                   ════════════════════════════════════════ */
        .grid-wrap {
            position: relative;
            z-index: 10;
            max-width: 1280px;
            margin: 0 auto;
            padding: 48px 24px 96px;
        }

        .section-label {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 40px;
        }

        .section-label h2 {
            font-family: var(--font-display);
            font-size: 28px;
            font-weight: 700;
            color: var(--c-ink);
            white-space: nowrap;
        }

        .section-label-line {
            flex: 1;
            height: 1px;
            background: linear-gradient(to right, var(--c-warm), transparent);
        }

        .recipe-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 28px;
        }

        .recipe-card {
            background: var(--c-paper);
            border: 1px solid var(--c-warm);
            border-radius: var(--radius-lg);
            /* overflow:hidden agar gambar tidak bocor keluar rounded corner */
            overflow: hidden;
            cursor: pointer;
            transition: transform .35s cubic-bezier(.22, 1, .36, 1), box-shadow .35s cubic-bezier(.22, 1, .36, 1), border-color .35s ease;
            box-shadow: var(--shadow-card);
            /* Flex column — tapi JANGAN beri height, biarkan card tingginya
                       ditentukan oleh body+footer, bukan oleh gambar */
            display: flex;
            flex-direction: column;
        }

        .recipe-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
            border-color: var(--c-amber-lt);
        }

        /* ── Image wrap: FIXED 220px, tidak bisa stretch ── */
        .card-img-wrap {
            /* Tiga properti height untuk memastikan tidak ada yang bisa mengubahnya */
            height: 220px;
            min-height: 220px;
            max-height: 220px;
            /* flex-shrink:0 mencegah flex parent memperkecil elemen ini */
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
            background: var(--c-cream);
        }

        /* Gambar harus pakai absolute agar benar-benar terkungkung dalam box */
        .card-img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
            transition: transform .5s ease;
        }

        .recipe-card:hover .card-img {
            transform: scale(1.07);
        }

        /* Placeholder juga absolute */
        .card-img-placeholder {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 52px;
        }

        .card-category {
            position: absolute;
            top: 14px;
            left: 14px;
            z-index: 2;
            font-family: var(--font-mono);
            font-size: 10px;
            font-weight: 500;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--c-amber-dk);
            background: rgba(250, 247, 242, .92);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(201, 119, 58, .2);
            padding: 5px 12px;
            border-radius: 50px;
        }

        .card-img-wrap::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60px;
            z-index: 1;
            background: linear-gradient(to top, var(--c-paper), transparent);
        }

        .card-body {
            padding: 22px 24px 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .card-title {
            font-family: var(--font-display);
            font-size: 21px;
            font-weight: 700;
            color: var(--c-ink);
            line-height: 1.25;
            margin-bottom: 10px;
        }

        .card-excerpt {
            font-size: 13px;
            color: var(--c-ink-40);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.55;
            flex: 1;
        }

        .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 24px 18px;
            border-top: 1px solid var(--c-cream);
            flex-shrink: 0;
        }

        .card-meta {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-family: var(--font-mono);
            color: var(--c-ink-40);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .card-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            color: var(--c-amber);
            background: var(--c-amber-lt);
            border: none;
            border-radius: 50px;
            padding: 7px 16px;
            cursor: pointer;
            transition: all .25s ease;
        }

        .card-btn:hover {
            background: var(--c-amber);
            color: #fff;
            box-shadow: 0 4px 16px rgba(201, 119, 58, .3);
        }

        /* Semua card seragam — tidak ada featured card */

        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 80px 24px;
            color: var(--c-ink-40);
        }

        .empty-state svg {
            margin-bottom: 20px;
            opacity: .4;
        }

        .empty-state h3 {
            font-family: var(--font-display);
            font-size: 22px;
            color: var(--c-ink-70);
            margin-bottom: 8px;
        }

        .empty-state p {
            font-size: 14px;
        }

        /* ════════════════════════════════════════
                   MODAL — FIX SCROLL
                   Kunci utama scroll di dalam flex container:
                   1. Parent harus punya ukuran FIXED (bukan hanya max-height)
                   2. Child yang scroll harus punya min-height:0
                   ════════════════════════════════════════ */
        .modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 9000;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            background: rgba(15, 10, 5, .55);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            opacity: 0;
            pointer-events: none;
            transition: opacity .3s ease;
        }

        @media (min-width: 768px) {
            .modal-overlay {
                align-items: center;
                padding: 24px;
            }
        }

        .modal-overlay.active {
            opacity: 1;
            pointer-events: all;
        }

        .modal-sheet {
            width: 100%;
            max-width: 900px;
            /* height eksplisit wajib — max-height saja tidak cukup untuk flex scroll */
            height: 92vh;
            background: var(--c-paper);
            border-radius: var(--radius-xl) var(--radius-xl) 0 0;
            /* overflow:hidden di sheet agar animasi border-radius tidak bocor */
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow-modal);
            transform: translateY(60px);
            opacity: 0;
            transition: all .4s cubic-bezier(.32, 1.56, .64, 1);
        }

        @media (min-width: 768px) {
            .modal-sheet {
                border-radius: var(--radius-xl);
                flex-direction: row;
                height: 82vh;
                transform: translateY(30px) scale(.97);
            }
        }

        .modal-overlay.active .modal-sheet {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        /* Drag handle */
        .modal-handle {
            display: block;
            width: 40px;
            height: 4px;
            background: var(--c-warm);
            border-radius: 2px;
            margin: 14px auto 0;
            flex-shrink: 0;
        }

        @media (min-width: 768px) {
            .modal-handle {
                display: none;
            }
        }

        /* ── Left panel ── */
        .modal-left {
            position: relative;
            flex-shrink: 0;
            /* Mobile: tinggi fixed */
            height: 220px;
            overflow: hidden;
            background: var(--c-cream);
        }

        @media (min-width: 768px) {
            .modal-left {
                width: 320px;
                /* Desktop: isi penuh tinggi sheet */
                height: 100%;
                border-radius: var(--radius-xl) 0 0 var(--radius-xl);
            }
        }

        .modal-left img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .modal-left-placeholder {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 72px;
            background: linear-gradient(135deg, var(--c-cream), var(--c-warm));
        }

        .modal-left::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, transparent 35%, rgba(15, 10, 5, .75) 100%);
        }

        @media (min-width: 768px) {
            .modal-left::after {
                background: linear-gradient(to right, transparent 40%, rgba(255, 249, 240, .1) 100%);
            }
        }

        .modal-title-block {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px 22px 22px;
            z-index: 2;
        }

        @media (min-width: 768px) {
            .modal-title-block {
                padding: 24px 28px;
            }
        }

        .modal-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: var(--font-mono);
            font-size: 10px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .9);
            background: rgba(255, 255, 255, .15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, .2);
            padding: 5px 14px;
            border-radius: 50px;
            margin-bottom: 8px;
        }

        .modal-title {
            font-family: var(--font-display);
            font-size: clamp(20px, 4vw, 28px);
            font-weight: 900;
            color: #fff;
            line-height: 1.15;
            text-shadow: 0 2px 16px rgba(0, 0, 0, .35);
        }

        /* Close button */
        .modal-close {
            position: absolute;
            top: 14px;
            right: 14px;
            z-index: 20;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .18);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, .3);
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .25s ease;
        }

        .modal-close:hover {
            background: #fff;
            color: var(--c-ink);
            transform: rotate(90deg);
        }

        /* ── Right panel: INI YANG SCROLL ──
                   WAJIB: flex:1 1 0 + min-height:0 + min-width:0
                   Tanpa min-height:0, overflow-y:auto tidak pernah aktif
                   karena flex item tidak mau shrink di bawah content size-nya */
        .modal-right {
            flex: 1 1 0;
            min-height: 0;
            min-width: 0;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            /* smooth scroll iOS */
            padding: 24px 20px 40px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            scrollbar-width: thin;
            scrollbar-color: var(--c-warm) transparent;
        }

        @media (min-width: 768px) {
            .modal-right {
                padding: 32px 32px 48px;
            }
        }

        .modal-right::-webkit-scrollbar {
            width: 4px;
        }

        .modal-right::-webkit-scrollbar-thumb {
            background: var(--c-warm);
            border-radius: 4px;
        }

        /* ── Section blocks ── */
        .modal-section {
            border-radius: var(--radius-md);
            overflow: hidden;
            border: 1px solid var(--c-warm);
            flex-shrink: 0;
        }

        .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            border-bottom: 1px solid var(--c-warm);
        }

        .section-head.bahan {
            background: linear-gradient(135deg, var(--c-green-lt), #edfaf3);
        }

        .section-head.proses {
            background: linear-gradient(135deg, #fef3e8, var(--c-amber-lt));
        }

        .section-head-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .section-icon.bahan {
            background: rgba(61, 102, 81, .12);
            color: var(--c-green);
        }

        .section-icon.proses {
            background: rgba(201, 119, 58, .12);
            color: var(--c-amber);
        }

        .section-title {
            font-family: var(--font-display);
            font-size: 15px;
            font-weight: 700;
        }

        .section-title.bahan {
            color: var(--c-green);
        }

        .section-title.proses {
            color: var(--c-amber-dk);
        }

        .copy-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: var(--font-body);
            font-size: 12px;
            font-weight: 600;
            border: none;
            border-radius: 50px;
            padding: 7px 14px;
            cursor: pointer;
            transition: all .25s ease;
            white-space: nowrap;
        }

        .copy-btn.bahan {
            background: rgba(61, 102, 81, .1);
            color: var(--c-green);
        }

        .copy-btn.bahan:hover {
            background: var(--c-green);
            color: #fff;
            box-shadow: 0 4px 14px rgba(61, 102, 81, .3);
        }

        .copy-btn.proses {
            background: rgba(201, 119, 58, .1);
            color: var(--c-amber-dk);
        }

        .copy-btn.proses:hover {
            background: var(--c-amber);
            color: #fff;
            box-shadow: 0 4px 14px rgba(201, 119, 58, .3);
        }

        .copy-btn.copied {
            background: #16a34a !important;
            color: #fff !important;
        }

        .section-content {
            padding: 16px 18px;
            background: #fff;
            font-size: 14px;
            color: var(--c-ink-70);
            line-height: 1.75;
        }

        .section-content ul,
        .section-content ol {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .section-content ul li {
            position: relative;
            padding: 9px 12px 9px 44px;
            border-radius: var(--radius-sm);
            background: var(--c-bg);
            border: 1px solid var(--c-cream);
            color: var(--c-ink-70);
            transition: background .2s ease;
            line-height: 1.5;
        }

        .section-content ul li:hover {
            background: var(--c-green-lt);
            border-color: rgba(61, 102, 81, .2);
        }

        .section-content ul li::before {
            content: '✓';
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            width: 22px;
            height: 22px;
            border-radius: 6px;
            background: rgba(61, 102, 81, .1);
            border: 1.5px solid rgba(61, 102, 81, .25);
            color: var(--c-green);
            font-size: 11px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .section-content ol {
            counter-reset: step-counter;
            gap: 8px;
        }

        .section-content ol li {
            position: relative;
            padding: 11px 12px 11px 54px;
            border-radius: var(--radius-sm);
            background: var(--c-bg);
            border: 1px solid var(--c-cream);
            color: var(--c-ink-70);
            counter-increment: step-counter;
            transition: background .2s ease;
            line-height: 1.55;
        }

        .section-content ol li:hover {
            background: #fef3e8;
            border-color: rgba(201, 119, 58, .2);
        }

        .section-content ol li::before {
            content: counter(step-counter);
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 28px;
            height: 28px;
            line-height: 28px;
            text-align: center;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--c-amber), var(--c-amber-dk));
            color: #fff;
            font-size: 12px;
            font-weight: 800;
            font-family: var(--font-mono);
            box-shadow: 0 3px 8px rgba(201, 119, 58, .35);
        }

        .section-content p {
            margin-bottom: 8px;
        }

        .section-content p:last-child {
            margin-bottom: 0;
        }

        /* Plain-text fallback */
        .plain-lines {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .plain-lines .plain-item {
            padding: 9px 12px 9px 44px;
            border-radius: var(--radius-sm);
            background: var(--c-bg);
            border: 1px solid var(--c-cream);
            color: var(--c-ink-70);
            font-size: 14px;
            line-height: 1.6;
            position: relative;
            transition: background .2s ease;
        }

        .plain-lines.bahan .plain-item:hover {
            background: var(--c-green-lt);
            border-color: rgba(61, 102, 81, .2);
        }

        .plain-lines.proses .plain-item:hover {
            background: #fef3e8;
            border-color: rgba(201, 119, 58, .2);
        }

        .plain-lines.bahan .plain-item::before {
            content: '✓';
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            width: 22px;
            height: 22px;
            border-radius: 6px;
            background: rgba(61, 102, 81, .1);
            border: 1.5px solid rgba(61, 102, 81, .25);
            color: var(--c-green);
            font-size: 11px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .plain-lines.proses {
            counter-reset: plain-counter;
        }

        .plain-lines.proses .plain-item {
            padding-left: 54px;
            counter-increment: plain-counter;
        }

        .plain-lines.proses .plain-item::before {
            content: counter(plain-counter);
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--c-amber), var(--c-amber-dk));
            color: #fff;
            font-size: 12px;
            font-weight: 800;
            font-family: var(--font-mono);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 3px 8px rgba(201, 119, 58, .35);
        }

        /* ── Toast ── */
        .toast-area {
            position: fixed;
            bottom: 28px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 99999;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            pointer-events: none;
        }

        .toast {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--c-ink);
            color: var(--c-bg);
            font-family: var(--font-body);
            font-size: 13px;
            font-weight: 600;
            padding: 12px 22px;
            border-radius: 50px;
            box-shadow: 0 8px 32px rgba(26, 18, 8, .25);
            opacity: 0;
            transform: translateY(16px) scale(.94);
            transition: all .35s cubic-bezier(.34, 1.56, .64, 1);
            white-space: nowrap;
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        .toast.hide {
            opacity: 0;
            transform: translateY(8px) scale(.95);
            transition: all .25s ease;
        }

        .toast-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--c-amber);
            flex-shrink: 0;
        }

        /* ── Footer ── */
        .site-footer {
            margin-top: auto;
            position: relative;
            z-index: 10;
            border-top: 1px solid var(--c-warm);
            background: var(--c-cream);
            text-align: center;
            padding: 32px 24px;
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--c-ink-40);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .site-footer strong {
            color: var(--c-amber);
        }
    </style>

    <div class="bg-blobs" aria-hidden="true">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>
    <div class="toast-area" id="toastArea"></div>

    {{-- HERO --}}
    <section class="hero">
        <div class="hero-eyebrow">Kawulo Halal Present</div>
        <h1 class="hero-title">Warisan Rasa <em>Nusantara</em></h1>
        <div class="hero-divider">
            <span></span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                <path
                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6z"
                    opacity=".5" />
                <circle cx="12" cy="12" r="3" />
            </svg>
            <span></span>
        </div>
        <p class="hero-desc">Koleksi resep autentik makanan dan minuman khas Nusantara dari berbagai daerah ke meja makan,
            dengan
            sentuhan presentasi yang modern dan elegan. Anjay boskuhh!</p>
        <a href="#recipes" class="hero-cta">
            <span>Gasss</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M7 17l9.2-9.2M17 17V7H7" />
            </svg>
        </a>
        <div class="scroll-hint" aria-hidden="true">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5v14M5 12l7 7 7-7" />
            </svg>
            <span>Scroll</span>
        </div>
    </section>

    {{-- TOOLBAR --}}
    <div class="toolbar" id="recipes">
        <div class="toolbar-inner">
            <div class="filters">
                <button class="filter-btn active" data-filter="semua">Semua</button>
                <button class="filter-btn" data-filter="makanan">🍽 Makanan</button>
                <button class="filter-btn" data-filter="minuman">🥤 Minuman</button>
            </div>
            <div class="search-wrap">
                <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.35-4.35" />
                </svg>
                <input type="text" id="searchInput" class="search-input" placeholder="Cari nama atau bahan...">
            </div>
            <p class="recipe-count" id="recipeCount"><strong>{{ $reseps->count() }}</strong> resep tersedia</p>
        </div>
    </div>

    {{-- GRID --}}
    <div class="grid-wrap">
        <div class="section-label">
            <h2>Koleksi Resep</h2>
            <div class="section-label-line"></div>
        </div>
        <div class="recipe-grid" id="recipeGrid">
            @forelse($reseps as $resep)
                <article class="recipe-card" data-kategori="{{ strtolower($resep->kategori) }}"
                    data-nama="{{ strtolower($resep->nama_produk) }}"
                    data-bahan="{{ strtolower(strip_tags($resep->bahan_makanan)) }}"
                    onclick="openModal('modal-{{ $resep->id }}')" tabindex="0" role="button"
                    aria-label="Buka resep {{ $resep->nama_produk }}">
                    <div class="card-img-wrap">
                        <span class="card-category">{{ $resep->kategori }}</span>
                        @if ($resep->foto)
                            <img src="{{ asset('storage/' . $resep->foto) }}" class="card-img"
                                alt="{{ $resep->nama_produk }}" loading="lazy">
                        @else
                            <div class="card-img-placeholder">{{ $resep->kategori == 'makanan' ? '🍽️' : '🥤' }}</div>
                        @endif
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">{{ $resep->nama_produk }}</h3>
                        <p class="card-excerpt">{{ strip_tags($resep->bahan_makanan) }}</p>
                    </div>
                    <div class="card-footer">
                        <span class="card-meta">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path
                                    d="M14.5 10c-.83 0-1.5-.67-1.5-1.5v-5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5v5c0 .83-.67 1.5-1.5 1.5z" />
                                <path d="M20.5 10H19V8.5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z" />
                                <path
                                    d="M9.5 14c.83 0 1.5.67 1.5 1.5v5c0 .83-.67 1.5-1.5 1.5S8 21.33 8 20.5v-5c0-.83.67-1.5 1.5-1.5z" />
                                <path d="M3.5 14H5v1.5c0 .83-.67 1.5-1.5 1.5S2 16.33 2 15.5 2.67 14 3.5 14z" />
                                <path
                                    d="M14 14.5c0-.83.67-1.5 1.5-1.5h5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5h-5c-.83 0-1.5-.67-1.5-1.5z" />
                                <path d="M15.5 19H14v1.5c0 .83.67 1.5 1.5 1.5s1.5-.67 1.5-1.5-.67-1.5-1.5-1.5z" />
                                <path
                                    d="M10 9.5C10 8.67 9.33 8 8.5 8h-5C2.67 8 2 8.67 2 9.5S2.67 11 3.5 11h5c.83 0 1.5-.67 1.5-1.5z" />
                                <path d="M8.5 5H10V3.5C10 2.67 9.33 2 8.5 2S7 2.67 7 3.5 7.67 5 8.5 5z" />
                            </svg>
                            Resep
                        </span>
                        <button class="card-btn">
                            Lihat Resep
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <path d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </article>
            @empty
                <div class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    <h3>Belum Ada Resep</h3>
                    <p>Database resep masih kosong.</p>
                </div>
            @endforelse
            <div class="empty-state" id="emptySearch" style="display:none;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.5">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.35-4.35" />
                </svg>
                <h3>Tidak Ditemukan</h3>
                <p>Coba kata kunci atau filter lain.</p>
            </div>
        </div>
    </div>

    {{-- MODALS --}}
    @foreach ($reseps as $resep)
        <div class="modal-overlay" id="modal-{{ $resep->id }}" role="dialog" aria-modal="true"
            onclick="closeModal('modal-{{ $resep->id }}')">
            <div class="modal-sheet" onclick="event.stopPropagation()">
                <div class="modal-handle"></div>

                {{-- Kiri: Gambar --}}
                <div class="modal-left">
                    <button class="modal-close" onclick="closeModal('modal-{{ $resep->id }}')" aria-label="Tutup">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <line x1="18" y1="6" x2="6" y2="18" />
                            <line x1="6" y1="6" x2="18" y2="18" />
                        </svg>
                    </button>
                    @if ($resep->foto)
                        <img src="{{ asset('storage/' . $resep->foto) }}" alt="{{ $resep->nama_produk }}">
                    @else
                        <div class="modal-left-placeholder">{{ $resep->kategori == 'makanan' ? '🍽️' : '🥤' }}</div>
                    @endif
                    <div class="modal-title-block">
                        <div class="modal-tag">
                            <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor">
                                <circle cx="4" cy="4" r="4" />
                            </svg>
                            {{ $resep->kategori }}
                        </div>
                        <h2 class="modal-title">{{ $resep->nama_produk }}</h2>
                    </div>
                </div>

                {{-- Kanan: Konten (yang scroll) --}}
                <div class="modal-right">
                    {{-- Bahan --}}
                    <div class="modal-section">
                        <div class="section-head bahan">
                            <div class="section-head-left">
                                <span class="section-icon bahan">
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <path
                                            d="M20 16.2A9.05 9.05 0 0 1 12.05 22h-.1A9.05 9.05 0 0 1 3 12.95V3h18v13.2z" />
                                    </svg>
                                </span>
                                <h3 class="section-title bahan">Bahan-Bahan</h3>
                            </div>
                            <button class="copy-btn bahan"
                                onclick="copySection('bahan-{{ $resep->id }}', this, 'Bahan berhasil disalin!', event)">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <rect x="9" y="9" width="13" height="13" rx="2" />
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                                </svg>
                                Salin
                            </button>
                        </div>
                        <div class="section-content" id="bahan-{{ $resep->id }}">{!! $resep->bahan_makanan !!}</div>
                    </div>

                    {{-- Proses --}}
                    <div class="modal-section">
                        <div class="section-head proses">
                            <div class="section-head-left">
                                <span class="section-icon proses">
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                                    </svg>
                                </span>
                                <h3 class="section-title proses">Proses Pembuatan</h3>
                            </div>
                            <button class="copy-btn proses"
                                onclick="copySection('proses-{{ $resep->id }}', this, 'Proses berhasil disalin!', event)">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <rect x="9" y="9" width="13" height="13" rx="2" />
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                                </svg>
                                Salin
                            </button>
                        </div>
                        <div class="section-content" id="proses-{{ $resep->id }}">{!! $resep->proses_pembuatan !!}</div>
                    </div>
                </div>{{-- end modal-right --}}
            </div>{{-- end modal-sheet --}}
        </div>
    @endforeach

    {{-- FOOTER --}}
    <footer class="site-footer">
        <p>&copy; 2026 <strong>Kawulo Halal</strong> — All rights reserved.</p>
    </footer>

    <script>
        /* Toast */
        function showToast(msg) {
            const area = document.getElementById('toastArea');
            const t = document.createElement('div');
            t.className = 'toast';
            t.innerHTML = `<span class="toast-dot"></span>${msg}`;
            area.appendChild(t);
            requestAnimationFrame(() => requestAnimationFrame(() => t.classList.add('show')));
            setTimeout(() => {
                t.classList.add('hide');
                setTimeout(() => t.remove(), 300);
            }, 2500);
        }

        /* Copy */
        function copySection(elementId, btn, toastMsg, event) {
            if (event) event.stopPropagation();
            const el = document.getElementById(elementId);
            if (!el) return;

            let lines = [];
            const olItems = el.querySelectorAll('ol li');
            if (olItems.length) {
                olItems.forEach((li, i) => lines.push((i + 1) + '. ' + li.innerText.trim()));
            }
            if (!lines.length) {
                const ulItems = el.querySelectorAll('ul li');
                if (ulItems.length) {
                    ulItems.forEach(li => lines.push('- ' + li.innerText.trim()));
                }
            }
            if (!lines.length) {
                const plainItems = el.querySelectorAll('.plain-item');
                if (plainItems.length) {
                    const isProses = !!el.querySelector('.plain-lines.proses');
                    plainItems.forEach((item, i) => lines.push(isProses ? (i + 1) + '. ' + item.innerText.trim() : '- ' +
                        item.innerText.trim()));
                }
            }
            const text = lines.length ? lines.join('\n') : el.innerText.split('\n').map(l => l.trim()).filter(Boolean).join(
                '\n');
            if (!text) {
                showToast('Tidak ada teks.');
                return;
            }

            function showCopied() {
                const ori = btn.innerHTML;
                btn.classList.add('copied');
                btn.innerHTML =
                    `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Tersalin!`;
                showToast(toastMsg || 'Berhasil disalin!');
                setTimeout(() => {
                    btn.classList.remove('copied');
                    btn.innerHTML = ori;
                }, 2200);
            }

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(showCopied).catch(() => fallbackCopy(text, showCopied));
            } else {
                fallbackCopy(text, showCopied);
            }
        }

        function fallbackCopy(text, callback) {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.cssText = 'position:fixed;top:-9999px;left:-9999px;opacity:0';
            document.body.appendChild(ta);
            ta.focus();
            ta.select();
            try {
                if (document.execCommand('copy')) callback();
                else showToast('Gagal menyalin.');
            } catch {
                showToast('Gagal menyalin.');
            } finally {
                document.body.removeChild(ta);
            }
        }

        /* Filter & Search */
        const filterBtns = document.querySelectorAll('.filter-btn');
        const searchInput = document.getElementById('searchInput');
        const cards = document.querySelectorAll('.recipe-card');
        const emptySearch = document.getElementById('emptySearch');
        const countEl = document.getElementById('recipeCount');

        function applyFilter() {
            const active = document.querySelector('.filter-btn.active').dataset.filter;
            const q = searchInput.value.toLowerCase().trim();
            let visible = 0;
            cards.forEach(card => {
                const show = (active === 'semua' || card.dataset.kategori === active) &&
                    (card.dataset.nama.includes(q) || card.dataset.bahan.includes(q));
                card.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            if (emptySearch) emptySearch.style.display = (visible === 0 && cards.length > 0) ? 'block' : 'none';
            if (countEl) countEl.innerHTML = `<strong>${visible}</strong> resep tersedia`;
        }
        filterBtns.forEach(btn => btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            applyFilter();
        }));
        searchInput.addEventListener('input', applyFilter);

        /* Modal */
        function openModal(id) {
            const overlay = document.getElementById(id);
            if (!overlay) return;
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
            const right = overlay.querySelector('.modal-right');
            if (right) right.scrollTop = 0;
        }

        function closeModal(id) {
            document.getElementById(id)?.classList.remove('active');
            document.body.style.overflow = '';
        }
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.active').forEach(m => {
                    m.classList.remove('active');
                    document.body.style.overflow = '';
                });
            }
        });
        document.querySelectorAll('.recipe-card').forEach(card => {
            card.addEventListener('keydown', e => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    card.click();
                }
            });
        });
    </script>
@endsection
