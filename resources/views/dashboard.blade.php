<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kawulo Halal — Sertifikasi Produk Halal UMKM Low-Risk</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600&family=Sora:wght@400;600&display=swap"
        rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        :root {
            --blue-50: #EEF3FA;
            --blue-100: #B5D4F4;
            --blue-600: #1A5FC8;
            --blue-700: #1040A0;
            --blue-900: #0C2E78;
            --teal-400: #1D9E75;
            --teal-300: #5DCAA5;
            --gray-50: #F5F7FB;
            --gray-100: #E0E7F0;
            --gray-400: #8A99B3;
            --gray-600: #4A5568;
            --gray-800: #0F1F40;
            --white: #ffffff;
        }

        html {
            scroll-behavior: smooth
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--gray-800);
            background: #EEF3FA;
            overflow-x: hidden
        }

        /* ── NAV ── */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            background: rgba(238, 243, 250, 0.88);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(100, 140, 210, 0.12);
            padding: 0 2rem;
        }

        .nav-inner {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 66px
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #1A5FC8, #1040A0);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0
        }

        .brand-icon svg {
            width: 20px;
            height: 20px;
            stroke: white;
            fill: none;
            stroke-width: 1.8
        }

        .brand-name {
            font-family: 'Sora', sans-serif;
            font-size: 15px;
            font-weight: 600;
            color: var(--gray-800);
            line-height: 1.3
        }

        .brand-name small {
            display: block;
            font-size: 10px;
            font-weight: 400;
            color: var(--gray-400);
            font-family: 'Plus Jakarta Sans', sans-serif
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2rem
        }

        .nav-links a {
            font-size: 13.5px;
            color: var(--gray-400);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s
        }

        .nav-links a:hover {
            color: var(--blue-600)
        }

        .nav-cta {
            background: linear-gradient(135deg, #1A5FC8, #1040A0);
            color: white !important;
            padding: 9px 20px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Sora', sans-serif;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(26, 95, 200, 0.25);
        }

        .nav-cta:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(26, 95, 200, 0.38)
        }

        .nav-login {
            background: transparent;
            color: var(--gray-600) !important;
            padding: 9px 18px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Sora', sans-serif;
            text-decoration: none;
            transition: all 0.2s;
            border: 1.5px solid var(--gray-100);
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .nav-login:hover {
            border-color: rgba(26, 95, 200, 0.3);
            color: var(--blue-600) !important;
            background: rgba(26, 95, 200, 0.04)
        }

        /* ── HERO ── */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 7rem 2rem 5rem;
            position: relative;
            overflow: hidden;
            background-color: #EEF3FA;
            background-image:
                radial-gradient(ellipse 70% 60% at 15% 10%, rgba(180, 210, 255, 0.55) 0%, transparent 60%),
                radial-gradient(ellipse 50% 50% at 85% 85%, rgba(160, 220, 200, 0.3) 0%, transparent 55%),
                radial-gradient(ellipse 40% 40% at 70% 15%, rgba(200, 225, 255, 0.4) 0%, transparent 50%);
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(100, 140, 200, 0.12) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(70px);
            pointer-events: none;
            animation: floatOrb 14s ease-in-out infinite
        }

        .orb-1 {
            width: 380px;
            height: 380px;
            background: rgba(130, 180, 255, 0.22);
            top: -100px;
            left: -80px;
            animation-delay: 0s
        }

        .orb-2 {
            width: 280px;
            height: 280px;
            background: rgba(100, 210, 180, 0.18);
            bottom: -60px;
            right: -60px;
            animation-delay: -5s
        }

        .orb-3 {
            width: 200px;
            height: 200px;
            background: rgba(170, 200, 255, 0.2);
            top: 45%;
            right: 8%;
            animation-delay: -9s
        }

        @keyframes floatOrb {

            0%,
            100% {
                transform: translate(0, 0)
            }

            33% {
                transform: translate(18px, -18px)
            }

            66% {
                transform: translate(-10px, 12px)
            }
        }

        .hero-inner {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            position: relative;
            z-index: 1
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(26, 95, 200, 0.08);
            border: 1px solid rgba(26, 95, 200, 0.18);
            border-radius: 50px;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
            color: var(--blue-600);
            letter-spacing: 0.04em;
            margin-bottom: 1.25rem;
        }

        .hero-badge-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #1D9E75;
            flex-shrink: 0;
            animation: pulse 2s ease-in-out infinite
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: 0.4
            }
        }

        .hero h1 {
            font-family: 'Sora', sans-serif;
            font-size: 44px;
            font-weight: 600;
            color: var(--gray-800);
            line-height: 1.25;
            margin-bottom: 1.25rem
        }

        .hero h1 em {
            font-style: normal;
            color: var(--blue-600)
        }

        .hero h1 span {
            font-style: normal;
            color: #1D9E75
        }

        .hero-desc {
            font-size: 16px;
            color: var(--gray-400);
            line-height: 1.75;
            margin-bottom: 2rem;
            max-width: 480px
        }

        .hero-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap
        }

        .btn-primary {
            background: linear-gradient(135deg, #1A5FC8, #1040A0);
            color: white;
            padding: 13px 28px;
            border-radius: 10px;
            font-size: 14.5px;
            font-weight: 600;
            font-family: 'Sora', sans-serif;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 4px 16px rgba(26, 95, 200, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.12) 0%, transparent 100%);
            opacity: 0;
            transition: opacity 0.2s
        }

        .btn-primary:hover::before {
            opacity: 1
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(26, 95, 200, 0.42)
        }

        .btn-secondary {
            background: white;
            color: var(--blue-600);
            padding: 13px 28px;
            border-radius: 10px;
            font-size: 14.5px;
            font-weight: 600;
            font-family: 'Sora', sans-serif;
            text-decoration: none;
            transition: all 0.2s;
            border: 1.5px solid rgba(26, 95, 200, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-secondary:hover {
            border-color: var(--blue-600);
            background: #f0f5ff;
            transform: translateY(-1px)
        }

        .hero-stats {
            display: flex;
            gap: 2.5rem;
            margin-top: 2.5rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(100, 140, 210, 0.15)
        }

        .stat-item {}

        .stat-num {
            font-family: 'Sora', sans-serif;
            font-size: 28px;
            font-weight: 600;
            color: var(--gray-800)
        }

        .stat-label {
            font-size: 12px;
            color: var(--gray-400);
            margin-top: 2px
        }

        /* Hero Card */
        .hero-visual {
            position: relative
        }

        .hero-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 0 0 1px rgba(100, 140, 210, 0.12), 0 30px 60px rgba(60, 100, 180, 0.1);
            position: relative;
            overflow: hidden;
            animation: cardEntrance 0.7s cubic-bezier(0.16, 1, 0.3, 1) 0.3s both;
        }

        @keyframes cardEntrance {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.97)
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1)
            }
        }

        .hero-card::before {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 120px;
            height: 120px;
            background: rgba(26, 95, 200, 0.05);
            border-radius: 50%
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem
        }

        .card-title {
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: var(--gray-800)
        }

        .card-badge {
            background: #EBF9F5;
            color: #0F6E56;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            border: 1px solid rgba(29, 158, 117, 0.2)
        }

        .progress-list {
            display: flex;
            flex-direction: column;
            gap: 1rem
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 12.5px;
            margin-bottom: 6px
        }

        .progress-label span:first-child {
            color: var(--gray-600);
            font-weight: 500
        }

        .progress-label span:last-child {
            color: var(--gray-800);
            font-weight: 600
        }

        .progress-bar {
            height: 7px;
            background: var(--gray-50);
            border-radius: 10px;
            overflow: hidden
        }

        .progress-fill {
            height: 100%;
            border-radius: 10px;
            background: linear-gradient(90deg, #1A5FC8, #1D9E75)
        }

        .floating-tag {
            position: absolute;
            background: white;
            border-radius: 12px;
            padding: 10px 14px;
            box-shadow: 0 0 0 1px rgba(100, 140, 210, 0.15), 0 10px 30px rgba(60, 100, 180, 0.12);
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: floatTag 3s ease-in-out infinite;
        }

        @keyframes floatTag {

            0%,
            100% {
                transform: translateY(0)
            }

            50% {
                transform: translateY(-6px)
            }
        }

        .ft-1 {
            bottom: -16px;
            left: -24px;
            color: #0F6E56
        }

        .ft-2 {
            top: -16px;
            right: -20px;
            color: var(--blue-600);
            animation-delay: -1.5s
        }

        .ft-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0
        }

        .ft-dot.green {
            background: #1D9E75
        }

        .ft-dot.blue {
            background: #1A5FC8
        }

        /* ── SECTIONS BASE ── */
        section {
            padding: 5.5rem 2rem
        }

        .section-inner {
            max-width: 1100px;
            margin: 0 auto
        }

        .section-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(26, 95, 200, 0.07);
            border: 1px solid rgba(26, 95, 200, 0.14);
            border-radius: 50px;
            padding: 5px 14px;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--blue-600);
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        .section-title {
            font-family: 'Sora', sans-serif;
            font-size: 34px;
            font-weight: 600;
            color: var(--gray-800);
            line-height: 1.3;
            margin-bottom: 0.75rem
        }

        .section-title em {
            font-style: normal;
            color: var(--blue-600)
        }

        .section-desc {
            font-size: 15.5px;
            color: var(--gray-400);
            line-height: 1.75;
            max-width: 540px
        }

        .section-center {
            text-align: center
        }

        .section-center .section-desc {
            margin: 0 auto
        }

        /* ── SERVICES ── */
        .services-bg {
            background: white
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-top: 3rem
        }

        .service-card {
            background: var(--gray-50);
            border: 1px solid var(--gray-100);
            border-radius: 16px;
            padding: 1.75rem;
            transition: all 0.25s;
            position: relative;
            overflow: hidden;
        }

        .service-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #1A5FC8, #1D9E75);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .service-card:hover::after {
            transform: scaleX(1)
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(26, 95, 200, 0.1);
            border-color: rgba(26, 95, 200, 0.2);
            background: white
        }

        .service-icon {
            width: 50px;
            height: 50px;
            background: rgba(26, 95, 200, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            transition: background 0.25s
        }

        .service-card:hover .service-icon {
            background: rgba(26, 95, 200, 0.15)
        }

        .service-icon svg {
            width: 22px;
            height: 22px;
            stroke: var(--blue-600);
            fill: none;
            stroke-width: 1.8
        }

        .service-card h3 {
            font-family: 'Sora', sans-serif;
            font-size: 16px;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 0.6rem
        }

        .service-card p {
            font-size: 13.5px;
            color: var(--gray-400);
            line-height: 1.7
        }

        .service-card-tag {
            display: inline-block;
            background: rgba(29, 158, 117, 0.1);
            color: #0F6E56;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            margin-top: 1rem;
            border: 1px solid rgba(29, 158, 117, 0.18);
        }

        /* ── HOW IT WORKS ── */
        .steps-bg {
            background: #EEF3FA;
            background-image: radial-gradient(ellipse 60% 50% at 50% 0%, rgba(180, 210, 255, 0.35) 0%, transparent 60%);
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-top: 3rem;
            position: relative
        }

        .steps-grid::before {
            content: '';
            position: absolute;
            top: 26px;
            left: calc(12.5% + 26px);
            right: calc(12.5% + 26px);
            height: 2px;
            background: linear-gradient(90deg, rgba(26, 95, 200, 0.25), rgba(29, 158, 117, 0.25));
            z-index: 0;
        }

        .step-card {
            background: white;
            border: 1px solid rgba(100, 140, 210, 0.12);
            border-radius: 16px;
            padding: 1.75rem 1.25rem;
            text-align: center;
            position: relative;
            z-index: 1;
            transition: all 0.25s;
        }

        .step-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 36px rgba(26, 95, 200, 0.1)
        }

        .step-num {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1A5FC8, #1040A0);
            color: white;
            font-family: 'Sora', sans-serif;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            box-shadow: 0 4px 14px rgba(26, 95, 200, 0.3);
        }

        .step-card h3 {
            font-family: 'Sora', sans-serif;
            font-size: 14.5px;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 0.5rem
        }

        .step-card p {
            font-size: 13px;
            color: var(--gray-400);
            line-height: 1.65
        }

        /* ── WHY ── */
        .why-bg {
            background: white
        }

        .why-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5rem;
            align-items: center;
            margin-top: 3rem
        }

        .why-visual {
            background: linear-gradient(145deg, #1A5FC8, #0C2E78);
            border-radius: 20px;
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
        }

        .why-visual::before {
            content: '';
            position: absolute;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.06);
            top: -80px;
            right: -80px
        }

        .why-visual::after {
            content: '';
            position: absolute;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.04);
            bottom: 20px;
            left: -40px
        }

        .why-stat-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
            position: relative;
            z-index: 1
        }

        .why-stat {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 14px;
            padding: 1.5rem;
            text-align: center;
            transition: background 0.25s
        }

        .why-stat:hover {
            background: rgba(255, 255, 255, 0.16)
        }

        .why-stat-num {
            font-family: 'Sora', sans-serif;
            font-size: 30px;
            font-weight: 600;
            color: white
        }

        .why-stat-num em {
            font-style: normal;
            color: #7DD3C8
        }

        .why-stat-label {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 4px;
            line-height: 1.4
        }

        .why-list {
            display: flex;
            flex-direction: column;
            gap: 1.35rem
        }

        .why-item {
            display: flex;
            align-items: flex-start;
            gap: 14px
        }

        .why-check {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(26, 95, 200, 0.1);
            border: 1px solid rgba(26, 95, 200, 0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.2s;
        }

        .why-item:hover .why-check {
            background: rgba(26, 95, 200, 0.18);
            border-color: rgba(26, 95, 200, 0.35)
        }

        .why-check svg {
            width: 16px;
            height: 16px;
            stroke: var(--blue-600);
            fill: none;
            stroke-width: 2.5
        }

        .why-item-body h4 {
            font-family: 'Sora', sans-serif;
            font-size: 15px;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 4px
        }

        .why-item-body p {
            font-size: 13.5px;
            color: var(--gray-400);
            line-height: 1.65
        }

        /* ── PRICING ── */
        .pricing-bg {
            background: #EEF3FA;
            background-image: radial-gradient(ellipse 60% 50% at 50% 100%, rgba(180, 210, 255, 0.3) 0%, transparent 60%);
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-top: 3rem
        }

        .price-card {
            background: white;
            border: 1px solid rgba(100, 140, 210, 0.15);
            border-radius: 18px;
            padding: 2rem;
            transition: all 0.25s;
            position: relative;
        }

        .price-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 44px rgba(26, 95, 200, 0.1)
        }

        .price-card.featured {
            border: 2px solid var(--blue-600);
            box-shadow: 0 8px 30px rgba(26, 95, 200, 0.15);
        }

        .featured-badge {
            position: absolute;
            top: -13px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #1A5FC8, #1040A0);
            color: white;
            font-size: 11px;
            font-weight: 600;
            font-family: 'Sora', sans-serif;
            padding: 4px 16px;
            border-radius: 20px;
            white-space: nowrap;
            box-shadow: 0 4px 10px rgba(26, 95, 200, 0.3);
        }

        .price-name {
            font-family: 'Sora', sans-serif;
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-400);
            text-transform: uppercase;
            letter-spacing: 0.07em;
            margin-bottom: 0.5rem
        }

        .price-amount {
            font-family: 'Sora', sans-serif;
            font-size: 34px;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 4px
        }

        .price-amount small {
            font-size: 14px;
            color: var(--gray-400);
            font-weight: 400
        }

        .price-desc {
            font-size: 13px;
            color: var(--gray-400);
            margin-bottom: 1.5rem;
            line-height: 1.6;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--gray-100)
        }

        .price-features {
            display: flex;
            flex-direction: column;
            gap: 0.7rem;
            margin-bottom: 2rem
        }

        .price-feature {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13.5px;
            color: var(--gray-600)
        }

        .price-feature svg {
            width: 15px;
            height: 15px;
            stroke: #1D9E75;
            fill: none;
            stroke-width: 2.5;
            flex-shrink: 0;
            margin-top: 2px
        }

        .btn-price {
            display: block;
            text-align: center;
            padding: 12px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Sora', sans-serif;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-price-outline {
            background: transparent;
            color: var(--blue-600);
            border: 1.5px solid rgba(26, 95, 200, 0.3)
        }

        .btn-price-outline:hover {
            background: rgba(26, 95, 200, 0.06);
            border-color: var(--blue-600)
        }

        .btn-price-fill {
            background: linear-gradient(135deg, #1A5FC8, #1040A0);
            color: white;
            border: none;
            box-shadow: 0 4px 14px rgba(26, 95, 200, 0.3)
        }

        .btn-price-fill:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 22px rgba(26, 95, 200, 0.4)
        }

        /* ── TESTIMONIAL ── */
        .testi-bg {
            background: white
        }

        .testi-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-top: 3rem
        }

        .testi-card {
            background: var(--gray-50);
            border: 1px solid rgba(100, 140, 210, 0.12);
            border-radius: 16px;
            padding: 1.75rem;
            transition: all 0.25s;
        }

        .testi-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 36px rgba(26, 95, 200, 0.08);
            background: white
        }

        .quote-mark {
            font-size: 38px;
            line-height: 1;
            color: #7DD3C8;
            font-family: Georgia, serif;
            margin-bottom: 10px
        }

        .testi-text {
            font-size: 13.5px;
            color: var(--gray-600);
            line-height: 1.75;
            font-style: italic;
            margin-bottom: 1.25rem
        }

        .testi-author {
            display: flex;
            align-items: center;
            gap: 10px;
            border-top: 1px solid var(--gray-100);
            padding-top: 1rem
        }

        .author-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1A5FC8, #1D9E75);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Sora', sans-serif;
            font-size: 13px;
            font-weight: 600;
            color: white;
            flex-shrink: 0;
        }

        .author-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-800)
        }

        .author-biz {
            font-size: 11.5px;
            color: var(--gray-400);
            margin-top: 1px
        }

        .stars {
            color: #F59E0B;
            font-size: 13px;
            margin-bottom: 0.75rem;
            letter-spacing: 1px
        }

        /* ── FAQ ── */
        .faq-bg {
            background: #EEF3FA;
            background-image: radial-gradient(ellipse 60% 50% at 50% 50%, rgba(180, 210, 255, 0.3) 0%, transparent 65%);
        }

        .faq-wrap {
            max-width: 700px;
            margin: 3rem auto 0;
            display: flex;
            flex-direction: column;
            gap: 1rem
        }

        .faq-item {
            background: white;
            border: 1px solid rgba(100, 140, 210, 0.12);
            border-radius: 14px;
            overflow: hidden;
            transition: box-shadow 0.2s
        }

        .faq-item:hover {
            box-shadow: 0 8px 24px rgba(26, 95, 200, 0.08)
        }

        .faq-q {
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            font-family: 'Sora', sans-serif;
            font-size: 14.5px;
            font-weight: 600;
            color: var(--gray-800);
            gap: 1rem;
            user-select: none;
        }

        .faq-q:hover {
            color: var(--blue-600)
        }

        .faq-icon {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            background: rgba(26, 95, 200, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s
        }

        .faq-icon svg {
            width: 10px;
            height: 10px;
            stroke: var(--blue-600);
            fill: none;
            stroke-width: 2.5;
            transition: transform 0.3s
        }

        .faq-a {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s ease, padding 0.35s ease
        }

        .faq-item.open .faq-a {
            max-height: 200px
        }

        .faq-item.open .faq-icon {
            background: var(--blue-600)
        }

        .faq-item.open .faq-icon svg {
            stroke: white;
            transform: rotate(180deg)
        }

        .faq-a-inner {
            padding: 0 1.5rem 1.25rem;
            font-size: 13.5px;
            color: var(--gray-400);
            line-height: 1.75
        }

        /* ── CTA ── */
        .cta-section {
            background: linear-gradient(145deg, #1A5FC8, #0C2E78);
            padding: 6rem 2rem;
            position: relative;
            overflow: hidden
        }

        .cta-section::before {
            content: '';
            position: absolute;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.04);
            top: -180px;
            right: -120px
        }

        .cta-section::after {
            content: '';
            position: absolute;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.04);
            bottom: -90px;
            left: -70px
        }

        .cta-bg-dot {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(255, 255, 255, 0.06) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none
        }

        .cta-inner {
            max-width: 640px;
            margin: 0 auto;
            text-align: center;
            position: relative;
            z-index: 1
        }

        .cta-inner h2 {
            font-family: 'Sora', sans-serif;
            font-size: 36px;
            font-weight: 600;
            color: white;
            line-height: 1.35;
            margin-bottom: 1rem
        }

        .cta-inner h2 em {
            font-style: normal;
            color: #7DD3C8
        }

        .cta-inner p {
            font-size: 15.5px;
            color: rgba(255, 255, 255, 0.55);
            line-height: 1.75;
            margin-bottom: 2rem
        }

        .cta-actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap
        }

        .btn-white {
            background: white;
            color: var(--blue-600);
            padding: 13px 28px;
            border-radius: 10px;
            font-size: 14.5px;
            font-weight: 600;
            font-family: 'Sora', sans-serif;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .btn-white:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.18)
        }

        .btn-outline-white {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            padding: 13px 28px;
            border-radius: 10px;
            font-size: 14.5px;
            font-weight: 600;
            font-family: 'Sora', sans-serif;
            text-decoration: none;
            transition: all 0.2s;
            border: 1.5px solid rgba(255, 255, 255, 0.25);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-outline-white:hover {
            background: rgba(255, 255, 255, 0.18);
            border-color: rgba(255, 255, 255, 0.45)
        }

        .cta-trust {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2rem;
            margin-top: 2.5rem;
            flex-wrap: wrap
        }

        .cta-trust-item {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.45);
            font-weight: 500
        }

        .cta-trust-item svg {
            width: 14px;
            height: 14px;
            stroke: #5DCAA5;
            fill: none;
            stroke-width: 2.5
        }

        /* ── FOOTER ── */
        footer {
            background: var(--gray-800);
            padding: 4rem 2rem 2rem
        }

        .footer-inner {
            max-width: 1100px;
            margin: 0 auto
        }

        .footer-top {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem;
            padding-bottom: 3rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08)
        }

        .footer-brand-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 0.85rem
        }

        .footer-brand-icon {
            width: 38px;
            height: 38px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center
        }

        .footer-brand-icon svg {
            width: 18px;
            height: 18px;
            stroke: white;
            fill: none;
            stroke-width: 1.8
        }

        .footer-brand-name {
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: white;
            line-height: 1.3
        }

        .footer-brand-name small {
            display: block;
            font-size: 10px;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.35);
            font-family: 'Plus Jakarta Sans', sans-serif
        }

        .footer-desc {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.35);
            line-height: 1.75;
            max-width: 260px;
            margin-bottom: 1.5rem
        }

        .footer-social {
            display: flex;
            gap: 10px
        }

        .social-btn {
            width: 34px;
            height: 34px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.2s;
        }

        .social-btn:hover {
            background: rgba(255, 255, 255, 0.16);
            border-color: rgba(255, 255, 255, 0.25)
        }

        .social-btn svg {
            width: 15px;
            height: 15px;
            stroke: rgba(255, 255, 255, 0.5);
            fill: none;
            stroke-width: 1.8
        }

        .footer-col h4 {
            font-family: 'Sora', sans-serif;
            font-size: 11.5px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 1.15rem
        }

        .footer-col ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.65rem
        }

        .footer-col ul li a {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.3);
            text-decoration: none;
            transition: color 0.2s
        }

        .footer-col ul li a:hover {
            color: rgba(255, 255, 255, 0.75)
        }

        .footer-bottom {
            padding-top: 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem
        }

        .footer-copy {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.22)
        }

        .footer-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(29, 158, 117, 0.15);
            border: 1px solid rgba(29, 158, 117, 0.25);
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 11.5px;
            font-weight: 600;
            color: #5DCAA5;
        }

        .footer-badge svg {
            width: 13px;
            height: 13px;
            stroke: #5DCAA5;
            fill: none;
            stroke-width: 2.2;
            flex-shrink: 0
        }

        /* ── SCROLL ANIM ── */
        .fade-up {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.65s ease, transform 0.65s ease
        }

        .fade-up.visible {
            opacity: 1;
            transform: translateY(0)
        }

        /* ── RESPONSIVE ── */
        @media(max-width:960px) {
            .hero-inner {
                grid-template-columns: 1fr;
                gap: 3rem
            }

            .hero-visual {
                display: none
            }

            .hero h1 {
                font-size: 34px
            }

            .services-grid,
            .testi-grid {
                grid-template-columns: 1fr 1fr
            }

            .steps-grid {
                grid-template-columns: 1fr 1fr
            }

            .steps-grid::before {
                display: none
            }

            .why-grid {
                grid-template-columns: 1fr
            }

            .footer-top {
                grid-template-columns: 1fr 1fr
            }
        }

        @media(max-width:600px) {

            .services-grid,
            .testi-grid,
            .steps-grid {
                grid-template-columns: 1fr
            }

            .nav-links a:not(.nav-cta):not(.nav-login) {
                display: none
            }

            .hero h1 {
                font-size: 28px
            }

            .section-title {
                font-size: 26px
            }

            .cta-inner h2 {
                font-size: 26px
            }

            .footer-top {
                grid-template-columns: 1fr
            }

            .hero-stats {
                gap: 1.5rem
            }
        }
    </style>
</head>

<body>

    <!-- ═══════════════════════ NAVBAR ═══════════════════════ -->
    <nav>
        <div class="nav-inner">
            <a href="#beranda" class="brand">
                <div class="brand-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5z" />
                        <path d="M2 17l10 5 10-5" />
                        <path d="M2 12l10 5 10-5" />
                    </svg>
                </div>
                <div class="brand-name">Kawulo Halal<small>Sertifikasi Halal UMKM Low-Risk</small></div>
            </a>
            <div class="nav-links">
                <a href="#layanan">Layanan</a>
                <a href="#proses">Proses</a>
                <a href="#keunggulan">Keunggulan</a>
                <a href="#testimoni">Testimoni</a>
                <a href="/login" class="nav-login">
                    <svg viewBox="0 0 24 24"
                        style="width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                        <polyline points="10 17 15 12 10 7" />
                        <line x1="15" y1="12" x2="3" y2="12" />
                    </svg>
                    Masuk
                </a>
                <a href="#kontak" class="nav-cta">Daftar Gratis</a>
            </div>
        </div>
    </nav>

    <!-- ═══════════════════════ HERO ═══════════════════════ -->
    <section class="hero" id="beranda">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <div class="hero-inner">
            <div class="hero-content">
                <div class="hero-badge"><span class="hero-badge-dot"></span>100% Gratis untuk UMKM</div>
                <h1>Sertifikasi Halal <em>Mudah</em> untuk UMKM <span>Low-Risk</span></h1>
                <p class="hero-desc">Kawulo Halal hadir sebagai mitra konsultasi sertifikasi produk halal untuk pelaku
                    UMKM — sepenuhnya <strong>gratis</strong>. Proses cepat, transparan, dan didampingi oleh tenaga ahli
                    berpengalaman.</p>
                <div class="hero-actions">
                    <a href="#kontak" class="btn-primary">
                        <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:white;fill:none;stroke-width:2">
                            <path
                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.18 2 2 0 0 1 3.58 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.56a16 16 0 0 0 5.55 5.55l.91-.92a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21 16.92z" />
                        </svg>
                        Daftar Sekarang — Gratis!
                    </a>
                    <a href="#layanan" class="btn-secondary">
                        <svg viewBox="0 0 24 24"
                            style="width:16px;height:16px;stroke:var(--blue-600);fill:none;stroke-width:2">
                            <circle cx="11" cy="11" r="8" />
                            <path d="M21 21l-4.35-4.35" />
                        </svg>
                        Lihat Layanan
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-num">500+</div>
                        <div class="stat-label">UMKM Tersertifikasi</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-num">98%</div>
                        <div class="stat-label">Tingkat Keberhasilan</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-num">14 Hari</div>
                        <div class="stat-label">Rata-rata Proses</div>
                    </div>
                </div>
            </div>
            <div class="hero-visual">
                <div class="floating-tag ft-2">
                    <div class="ft-dot blue"></div>Diakui BPJPH Resmi
                </div>
                <div class="hero-card">
                    <div class="card-header">
                        <div class="card-title">Status Sertifikasi Halal</div>
                        <div class="card-badge">Live Tracking</div>
                    </div>
                    <div class="progress-list">
                        <div class="progress-item">
                            <div class="progress-label"><span>Kelengkapan Dokumen</span><span>100%</span></div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width:100%"></div>
                            </div>
                        </div>
                        <div class="progress-item">
                            <div class="progress-label"><span>Verifikasi Tim Kawulo</span><span>100%</span></div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width:100%"></div>
                            </div>
                        </div>
                        <div class="progress-item">
                            <div class="progress-label"><span>Pengajuan ke BPJPH</span><span>85%</span></div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width:85%"></div>
                            </div>
                        </div>
                        <div class="progress-item">
                            <div class="progress-label"><span>Penerbitan Sertifikat</span><span>20%</span></div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width:20%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="floating-tag ft-1">
                    <div class="ft-dot green"></div>Sertifikat Diterima!
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════ LAYANAN ═══════════════════════ -->
    <section class="services-bg" id="layanan">
        <div class="section-inner">
            <div class="fade-up">
                <div class="section-tag">Layanan Kami</div>
                <h2 class="section-title">Solusi <em>Lengkap</em> Sertifikasi Halal</h2>
                <p class="section-desc">Kami menyediakan layanan konsultasi menyeluruh mulai dari persiapan dokumen
                    hingga sertifikat terbit di tangan Anda.</p>
            </div>
            <div class="services-grid">
                <div class="service-card fade-up">
                    <div class="service-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                            <line x1="16" y1="13" x2="8" y2="13" />
                            <line x1="16" y1="17" x2="8" y2="17" />
                        </svg>
                    </div>
                    <h3>Konsultasi &amp; Persiapan Dokumen</h3>
                    <p>Pendampingan intensif dalam mempersiapkan seluruh dokumen persyaratan sertifikasi halal sesuai
                        regulasi BPJPH terbaru.</p>
                    <span class="service-card-tag">Sepenuhnya Gratis</span>
                </div>
                <div class="service-card fade-up">
                    <div class="service-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M9 11l3 3L22 4" />
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                        </svg>
                    </div>
                    <h3>Pendampingan Self-Declare</h3>
                    <p>Proses deklarasi mandiri bagi UMKM produk low-risk dipandu langsung oleh konsultan bersertifikat
                        resmi kami.</p>
                    <span class="service-card-tag">Khusus Low-Risk</span>
                </div>
                <div class="service-card fade-up">
                    <div class="service-icon">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 8v4l3 3" />
                        </svg>
                    </div>
                    <h3>Pemantauan Proses Real-Time</h3>
                    <p>Pantau seluruh progress pengajuan sertifikasi Anda melalui dashboard digital yang transparan dan
                        mudah digunakan.</p>
                    <span class="service-card-tag">Dashboard Digital</span>
                </div>
                <div class="service-card fade-up">
                    <div class="service-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </div>
                    <h3>Pelatihan Sistem Jaminan Halal</h3>
                    <p>Workshop dan pelatihan SJH bagi pemilik UMKM dan karyawan agar siap memenuhi standar halal secara
                        berkelanjutan.</p>
                    <span class="service-card-tag">Bersertifikat</span>
                </div>
                <div class="service-card fade-up">
                    <div class="service-icon">
                        <svg viewBox="0 0 24 24">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                        </svg>
                    </div>
                    <h3>Audit Internal Pra-Sertifikasi</h3>
                    <p>Simulasi audit internal untuk memastikan seluruh aspek produksi dan bahan baku memenuhi standar
                        halal sebelum pengajuan.</p>
                    <span class="service-card-tag">Pra-Audit</span>
                </div>
                <div class="service-card fade-up">
                    <div class="service-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                            <polyline points="22,6 12,13 2,6" />
                        </svg>
                    </div>
                    <h3>Layanan Purna Jual &amp; Pembaruan</h3>
                    <p>Dukungan pasca sertifikasi termasuk pengingat pembaruan sertifikat, konsultasi lanjutan, dan
                        bantuan penambahan produk baru.</p>
                    <span class="service-card-tag">After-Service</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════ PROSES ═══════════════════════ -->
    <section class="steps-bg" id="proses">
        <div class="section-inner">
            <div class="fade-up section-center">
                <div class="section-tag">Alur Proses</div>
                <h2 class="section-title">Hanya <em>4 Langkah</em> Mudah</h2>
                <p class="section-desc">Proses sertifikasi halal bersama Kawulo Halal dirancang sesederhana mungkin
                    agar usaha Anda tidak terhambat birokrasi.</p>
            </div>
            <div class="steps-grid">
                <div class="step-card fade-up">
                    <div class="step-num">1</div>
                    <h3>Konsultasi Awal</h3>
                    <p>Hubungi tim kami untuk analisis kebutuhan dan kelayakan produk Anda secara gratis tanpa syarat
                        apapun.</p>
                </div>
                <div class="step-card fade-up">
                    <div class="step-num">2</div>
                    <h3>Persiapan Dokumen</h3>
                    <p>Tim ahli kami membantu melengkapi seluruh dokumen persyaratan dengan cepat, tepat, dan akurat.
                    </p>
                </div>
                <div class="step-card fade-up">
                    <div class="step-num">3</div>
                    <h3>Pengajuan &amp; Verifikasi</h3>
                    <p>Kami mengajukan permohonan ke BPJPH dan mendampingi seluruh proses verifikasi hingga tuntas.</p>
                </div>
                <div class="step-card fade-up">
                    <div class="step-num">4</div>
                    <h3>Sertifikat Terbit</h3>
                    <p>Sertifikat halal resmi diterbitkan dan Anda siap memasarkan produk dengan label halal Indonesia.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════ KEUNGGULAN ═══════════════════════ -->
    <section class="why-bg" id="keunggulan">
        <div class="section-inner">
            <div class="why-grid">
                <div class="fade-up">
                    <div class="section-tag">Mengapa Kami</div>
                    <h2 class="section-title">Lebih dari Sekadar <em>Konsultan</em></h2>
                    <p class="section-desc" style="margin-bottom:2rem">Kami adalah mitra pertumbuhan bisnis halal
                        Anda. Berpengalaman, terpercaya, dan selalu siap mendampingi dari awal hingga akhir.</p>
                    <div class="why-list">
                        <div class="why-item">
                            <div class="why-check"><svg viewBox="0 0 24 24">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg></div>
                            <div class="why-item-body">
                                <h4>Tim Konsultan Bersertifikat BPJPH</h4>
                                <p>Seluruh konsultan kami memiliki sertifikasi resmi dan pengalaman langsung dalam
                                    proses sertifikasi halal nasional.</p>
                            </div>
                        </div>
                        <div class="why-item">
                            <div class="why-check"><svg viewBox="0 0 24 24">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg></div>
                            <div class="why-item-body">
                                <h4>Proses Transparan &amp; Terlacak</h4>
                                <p>Setiap tahapan dapat dipantau secara real-time. Tidak ada biaya tersembunyi, tidak
                                    ada ketidakpastian dalam prosesnya.</p>
                            </div>
                        </div>
                        <div class="why-item">
                            <div class="why-check"><svg viewBox="0 0 24 24">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg></div>
                            <div class="why-item-body">
                                <h4>Layanan 100% Gratis untuk UMKM</h4>
                                <p>Seluruh layanan konsultasi dan pendampingan sertifikasi halal kami tidak dipungut
                                    biaya apapun. Ini adalah bentuk nyata dukungan kami bagi UMKM Indonesia.</p>
                            </div>
                        </div>
                        <div class="why-item">
                            <div class="why-check"><svg viewBox="0 0 24 24">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg></div>
                            <div class="why-item-body">
                                <h4>Garansi Proses Selesai</h4>
                                <p>Kami berkomitmen mendampingi hingga sertifikat diterbitkan. Jika ada kendala teknis,
                                    tim kami yang akan menangani.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="fade-up">
                    <div class="why-visual">
                        <div class="why-stat-grid">
                            <div class="why-stat">
                                <div class="why-stat-num">500<em>+</em></div>
                                <div class="why-stat-label">UMKM Berhasil Tersertifikasi</div>
                            </div>
                            <div class="why-stat">
                                <div class="why-stat-num">98<em>%</em></div>
                                <div class="why-stat-label">Tingkat Keberhasilan Pengajuan</div>
                            </div>
                            <div class="why-stat">
                                <div class="why-stat-num">14<em>hr</em></div>
                                <div class="why-stat-label">Rata-rata Waktu Proses</div>
                            </div>
                            <div class="why-stat">
                                <div class="why-stat-num">5<em>★</em></div>
                                <div class="why-stat-label">Rating Kepuasan Klien</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════ TESTIMONI ═══════════════════════ -->
    <section class="testi-bg" id="testimoni">
        <div class="section-inner">
            <div class="fade-up section-center">
                <div class="section-tag">Testimoni</div>
                <h2 class="section-title">Kata Mereka yang Telah <em>Berhasil</em></h2>
                <p class="section-desc">Ratusan pelaku UMKM telah mempercayakan proses sertifikasi halalnya kepada
                    Kawulo Halal.</p>
            </div>
            <div class="testi-grid">
                <div class="testi-card fade-up">
                    <div class="stars">★★★★★</div>
                    <div class="quote-mark">"</div>
                    <p class="testi-text">Prosesnya jauh lebih mudah dari yang saya bayangkan. Tim Kawulo Halal sangat
                        membantu dari awal sampai sertifikat terbit. Usaha keripik saya sekarang bisa masuk minimarket!
                    </p>
                    <div class="testi-author">
                        <div class="author-avatar">SR</div>
                        <div>
                            <div class="author-name">Siti Rahayu</div>
                            <div class="author-biz">Keripik Singkong Barokah, Yogyakarta</div>
                        </div>
                    </div>
                </div>
                <div class="testi-card fade-up">
                    <div class="stars">★★★★★</div>
                    <div class="quote-mark">"</div>
                    <p class="testi-text">Awalnya bingung soal dokumen apa saja yang diperlukan. Ternyata semua dibantu
                        sama konsultannya. Harganya juga sangat terjangkau buat usaha kecil seperti saya.</p>
                    <div class="testi-author">
                        <div class="author-avatar">BH</div>
                        <div>
                            <div class="author-name">Budi Hartono</div>
                            <div class="author-biz">Sambal Bu Hartono, Surabaya</div>
                        </div>
                    </div>
                </div>
                <div class="testi-card fade-up">
                    <div class="stars">★★★★★</div>
                    <div class="quote-mark">"</div>
                    <p class="testi-text">Jualan boleh sederhana, tapi jaminan halal harus luar biasa. Kawulo Halal
                        membuktikan hal itu. Sertifikat terbit dalam 12 hari kerja, lebih cepat dari yang dijanjikan!
                    </p>
                    <div class="testi-author">
                        <div class="author-avatar">NA</div>
                        <div>
                            <div class="author-name">Nurul Aini</div>
                            <div class="author-biz">Kue Kering Nuri, Bandung</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════ FAQ ═══════════════════════ -->
    <section class="faq-bg">
        <div class="section-inner">
            <div class="fade-up section-center">
                <div class="section-tag">FAQ</div>
                <h2 class="section-title">Pertanyaan yang Sering <em>Ditanyakan</em></h2>
                <p class="section-desc">Temukan jawaban atas pertanyaan umum seputar proses sertifikasi halal bersama
                    kami.</p>
            </div>
            <div class="faq-wrap">
                <div class="faq-item fade-up">
                    <div class="faq-q" onclick="toggleFaq(this)">
                        Apa itu produk UMKM Low-Risk untuk sertifikasi halal?
                        <div class="faq-icon"><svg viewBox="0 0 24 24">
                                <polyline points="6 9 12 15 18 9" />
                            </svg></div>
                    </div>
                    <div class="faq-a">
                        <div class="faq-a-inner">Produk low-risk adalah produk makanan/minuman yang bahan bakunya sudah
                            jelas kehalalannya, tidak mengandung bahan yang diragukan, dan proses produksinya sederhana.
                            Contohnya: keripik, kue kering, sambal kemasan, minuman herbal, dan sejenisnya. Produk ini
                            dapat menggunakan jalur self-declare yang lebih cepat dan terjangkau.</div>
                    </div>
                </div>
                <div class="faq-item fade-up">
                    <div class="faq-q" onclick="toggleFaq(this)">
                        Berapa lama proses sertifikasi halal berlangsung?
                        <div class="faq-icon"><svg viewBox="0 0 24 24">
                                <polyline points="6 9 12 15 18 9" />
                            </svg></div>
                    </div>
                    <div class="faq-a">
                        <div class="faq-a-inner">Untuk produk low-risk dengan jalur self-declare, rata-rata proses
                            berlangsung 12–21 hari kerja sejak dokumen dinyatakan lengkap. Kawulo Halal memiliki
                            rata-rata penyelesaian 14 hari kerja berkat pengalaman dan jaringan yang kami miliki dengan
                            BPJPH.</div>
                    </div>
                </div>
                <div class="faq-item fade-up">
                    <div class="faq-q" onclick="toggleFaq(this)">
                        Apakah sertifikat halal wajib untuk semua UMKM?
                        <div class="faq-icon"><svg viewBox="0 0 24 24">
                                <polyline points="6 9 12 15 18 9" />
                            </svg></div>
                    </div>
                    <div class="faq-a">
                        <div class="faq-a-inner">Berdasarkan UU No. 33 Tahun 2014 dan PP No. 39 Tahun 2021, sertifikasi
                            halal secara bertahap menjadi wajib bagi produk makanan dan minuman yang beredar di
                            Indonesia. Pemerintah memberikan kemudahan bagi UMKM melalui skema self-declare dan subsidi
                            biaya sertifikasi. Segera daftarkan produk Anda untuk memenuhi kewajiban ini.</div>
                    </div>
                </div>
                <div class="faq-item fade-up">
                    <div class="faq-q" onclick="toggleFaq(this)">
                        Dokumen apa saja yang perlu disiapkan?
                        <div class="faq-icon"><svg viewBox="0 0 24 24">
                                <polyline points="6 9 12 15 18 9" />
                            </svg></div>
                    </div>
                    <div class="faq-a">
                        <div class="faq-a-inner">Dokumen utama yang diperlukan meliputi: NIB (Nomor Induk Berusaha),
                            data produk dan komposisi bahan baku, sertifikat halal bahan baku dari supplier, alur proses
                            produksi, dan data fasilitas produksi. Tim Kawulo Halal akan memandu persiapan setiap
                            dokumen secara detail agar tidak ada yang terlewat.</div>
                    </div>
                </div>
                <div class="faq-item fade-up">
                    <div class="faq-q" onclick="toggleFaq(this)">
                        Apakah layanan Kawulo Halal benar-benar gratis?
                        <div class="faq-icon"><svg viewBox="0 0 24 24">
                                <polyline points="6 9 12 15 18 9" />
                            </svg></div>
                    </div>
                    <div class="faq-a">
                        <div class="faq-a-inner">Ya, seluruh layanan konsultasi dan pendampingan sertifikasi halal dari
                            Kawulo Halal tidak dipungut biaya apapun. Ini adalah program yang kami jalankan sebagai
                            bentuk dukungan nyata kepada pelaku UMKM Indonesia. Biaya resmi BPJPH juga saat ini
                            disubsidi penuh oleh pemerintah untuk UMKM low-risk melalui jalur self-declare, sehingga
                            Anda dapat memperoleh sertifikat halal tanpa mengeluarkan biaya sama sekali.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════ CTA ═══════════════════════ -->
    <section class="cta-section" id="kontak">
        <div class="cta-bg-dot"></div>
        <div class="cta-inner">
            <h2>Siap Mendapatkan Sertifikat Halal <em>Secara Gratis?</em></h2>
            <p>Daftarkan usaha Anda sekarang dan nikmati seluruh layanan konsultasi sertifikasi halal kami tanpa biaya
                apapun. Tim kami siap mendampingi Anda.</p>
            <div class="cta-actions">
                <a href="https://wa.me/6281234567890" class="btn-white">
                    <svg viewBox="0 0 24 24"
                        style="width:16px;height:16px;stroke:var(--blue-600);fill:none;stroke-width:2">
                        <path
                            d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.18 2 2 0 0 1 3.58 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.56a16 16 0 0 0 5.55 5.55l.91-.92a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21 16.92z" />
                    </svg>
                    Daftar via WhatsApp
                </a>
                <a href="/login" class="btn-outline-white">
                    <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:white;fill:none;stroke-width:2">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                        <polyline points="10 17 15 12 10 7" />
                        <line x1="15" y1="12" x2="3" y2="12" />
                    </svg>
                    Sudah Punya Akun? Masuk
                </a>
            </div>
            <div class="cta-trust">
                <div class="cta-trust-item"><svg viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>Gratis sepenuhnya</div>
                <div class="cta-trust-item"><svg viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>Tidak ada biaya tersembunyi</div>
                <div class="cta-trust-item"><svg viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>Garansi proses selesai</div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════ FOOTER ═══════════════════════ -->
    <footer>
        <div class="footer-inner">
            <div class="footer-top">
                <div>
                    <div class="footer-brand-logo">
                        <div class="footer-brand-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 2L2 7l10 5 10-5-10-5z" />
                                <path d="M2 17l10 5 10-5" />
                                <path d="M2 12l10 5 10-5" />
                            </svg>
                        </div>
                        <div class="footer-brand-name">Kawulo Halal<small>Sertifikasi Halal UMKM Low-Risk</small></div>
                    </div>
                    <p class="footer-desc">Mitra konsultasi sertifikasi produk halal terpercaya untuk pelaku UMKM
                        Indonesia. Proses mudah, cepat, dan terjangkau bersama konsultan bersertifikat.</p>
                    <div class="footer-social">
                        <a href="#" class="social-btn" title="Instagram">
                            <svg viewBox="0 0 24 24">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />
                            </svg>
                        </a>
                        <a href="#" class="social-btn" title="WhatsApp">
                            <svg viewBox="0 0 24 24">
                                <path
                                    d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
                            </svg>
                        </a>
                        <a href="#" class="social-btn" title="Facebook">
                            <svg viewBox="0 0 24 24">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Layanan</h4>
                    <ul>
                        <li><a href="#">Konsultasi Halal</a></li>
                        <li><a href="#">Pendampingan Self-Declare</a></li>
                        <li><a href="#">Pelatihan SJH</a></li>
                        <li><a href="#">Audit Pra-Sertifikasi</a></li>
                        <li><a href="#">Layanan Purna Jual</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Informasi</h4>
                    <ul>
                        <li><a href="#">Tentang Kami</a></li>
                        <li><a href="#">Blog &amp; Artikel</a></li>
                        <li><a href="#">Regulasi Halal</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Kebijakan Privasi</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Kontak</h4>
                    <ul>
                        <li><a href="#">info@kawulohalal.id</a></li>
                        <li><a href="#">+62 812-3456-7890</a></li>
                        <li><a href="#">WhatsApp Chat</a></li>
                        <li><a href="#">Instagram</a></li>
                        <li><a href="#">Facebook</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <span class="footer-copy">&copy; 2026 Yayasan Permata Bakti Pertiwi. All rights reserved.</span>
                <div class="footer-badge">
                    <svg viewBox="0 0 24 24">
                        <path d="M9 11l3 3L22 4" />
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                    </svg>
                    Terakreditasi BPJPH
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Scroll fade-up
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) e.target.classList.add('visible')
            });
        }, {
            threshold: 0.1
        });
        document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));

        // FAQ toggle
        function toggleFaq(el) {
            const item = el.closest('.faq-item');
            const isOpen = item.classList.contains('open');
            document.querySelectorAll('.faq-item.open').forEach(i => i.classList.remove('open'));
            if (!isOpen) item.classList.add('open');
        }

        // Smooth active nav highlight
        const sections = document.querySelectorAll('section[id], div[id]');
        const navLinks = document.querySelectorAll('.nav-links a:not(.nav-cta)');
        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(s => {
                if (window.scrollY >= s.offsetTop - 100) current = s.getAttribute('id');
            });
            navLinks.forEach(a => {
                a.style.color = a.getAttribute('href') === '#' + current ? '#1A5FC8' : '';
            });
        });
    </script>
</body>

</html>
