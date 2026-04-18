@extends('layouts.guest')
@section('title', 'Konfirmasi Lamaran')
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

        .cc-root {
            min-height: 100vh;
            background-color: #EEF3FA;
            background-image:
                radial-gradient(ellipse 70% 50% at 10% 5%, rgba(180, 210, 255, 0.5) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 90% 90%, rgba(160, 220, 200, 0.25) 0%, transparent 55%);
            font-family: 'Plus Jakarta Sans', sans-serif;
            padding: 2rem 1rem 3rem;
            position: relative;
        }

        .cc-root::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: radial-gradient(circle, rgba(100, 140, 200, 0.1) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
            z-index: 0;
        }

        .cc-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
            animation: ccFloat 14s ease-in-out infinite;
        }

        .cc-orb-1 {
            width: 380px;
            height: 380px;
            background: rgba(130, 180, 255, 0.2);
            top: -80px;
            left: -80px;
        }

        .cc-orb-2 {
            width: 260px;
            height: 260px;
            background: rgba(100, 210, 180, 0.16);
            bottom: -60px;
            right: -60px;
            animation-delay: -6s;
        }

        @keyframes ccFloat {

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

        .cc-wrap {
            position: relative;
            z-index: 1;
            max-width: 1060px;
            margin: 0 auto;
            display: flex;
            gap: 1.5rem;
            align-items: flex-start;
        }

        /* ── LEFT PANEL ── */
        .cc-left {
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

        .cc-left::before {
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

        .cc-left::after {
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

        .cc-brand {
            display: flex;
            align-items: center;
            gap: 11px;
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
        }

        .cc-brand-icon {
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

        .cc-brand-icon svg {
            width: 20px;
            height: 20px;
        }

        .cc-brand-name {
            font-family: 'Sora', sans-serif;
            font-size: 12.5px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.95);
            line-height: 1.4;
        }

        .cc-brand-name small {
            display: block;
            font-size: 11px;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.42);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .cc-left-title {
            font-family: 'Sora', sans-serif;
            font-size: 20px;
            font-weight: 600;
            color: #fff;
            line-height: 1.4;
            margin-bottom: 0.6rem;
            position: relative;
            z-index: 1;
        }

        .cc-left-title em {
            font-style: normal;
            color: #7DD3C8;
        }

        .cc-left-desc {
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.5);
            line-height: 1.7;
            margin-bottom: 1.75rem;
            position: relative;
            z-index: 1;
        }

        /* Timeline */
        .cc-timeline {
            display: flex;
            flex-direction: column;
            gap: 0;
            position: relative;
            z-index: 1;
            margin-bottom: 2rem;
        }

        .cc-tl-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            position: relative;
        }

        .cc-tl-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: 12px;
            top: 26px;
            width: 2px;
            height: calc(100%);
            background: rgba(255, 255, 255, 0.12);
        }

        .cc-tl-dot {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 600;
            flex-shrink: 0;
        }

        .cc-tl-dot.done {
            background: #7DD3C8;
            color: #0C2E78;
        }

        .cc-tl-dot.active {
            background: rgba(255, 255, 255, 0.12);
            border: 1.5px solid rgba(255, 255, 255, 0.3);
            color: rgba(255, 255, 255, 0.6);
        }

        .cc-tl-body {
            padding-bottom: 16px;
        }

        .cc-tl-label {
            font-size: 12.5px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2px;
        }

        .cc-tl-sub {
            font-size: 11.5px;
            color: rgba(255, 255, 255, 0.45);
            line-height: 1.5;
        }

        /* Quote */
        .cc-quote {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.11);
            border-radius: 13px;
            padding: 1.1rem 1.25rem;
            position: relative;
            z-index: 1;
        }

        .cc-quote-mark {
            font-size: 26px;
            color: #7DD3C8;
            font-family: Georgia, serif;
            line-height: 1;
            margin-bottom: 6px;
        }

        .cc-quote-text {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            line-height: 1.7;
            font-style: italic;
        }

        .cc-quote-author {
            margin-top: 8px;
            font-size: 10.5px;
            color: rgba(255, 255, 255, 0.28);
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        /* ── RIGHT PANEL ── */
        .cc-right {
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

        .cc-success-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
        }

        .cc-success-icon svg {
            width: 34px;
            height: 34px;
            stroke: #059669;
            fill: none;
            stroke-width: 2.5;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .cc-conf-title {
            font-family: 'Sora', sans-serif;
            font-size: 22px;
            font-weight: 600;
            color: #0F1F40;
            text-align: center;
            margin-bottom: 6px;
        }

        .cc-conf-sub {
            font-size: 14px;
            color: #8A99B3;
            text-align: center;
            margin-bottom: 1.75rem;
            line-height: 1.6;
        }

        .cc-conf-sub strong {
            color: #0F1F40;
        }

        .cc-divider {
            height: 1px;
            background: #EDF0F7;
            margin-bottom: 1.75rem;
        }

        .cc-section-title {
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

        .cc-section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #EDF0F7;
        }

        .cc-data-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 1.75rem;
        }

        .cc-data-card {
            background: #F5F7FB;
            border: 1px solid #E0E7F0;
            border-radius: 12px;
            padding: 12px 14px;
        }

        .cc-data-label {
            font-size: 11px;
            font-weight: 600;
            color: #8A99B3;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 4px;
        }

        .cc-data-value {
            font-size: 14px;
            font-weight: 600;
            color: #0F1F40;
            line-height: 1.3;
        }

        .cc-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
        }

        .cc-badge.melamar {
            background: #EBF5FF;
            color: #1552A0;
            border: 1px solid #BAD7F5;
        }

        .cc-notice {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #FFFBEB;
            border: 1px solid #FDDCAB;
            border-radius: 12px;
            padding: 13px 14px;
            margin-bottom: 1.75rem;
            font-size: 13px;
            color: #924C0A;
            line-height: 1.6;
        }

        .cc-notice svg {
            width: 16px;
            height: 16px;
            stroke: #D97706;
            fill: none;
            stroke-width: 2;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .cc-wa-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, #25D366, #1DA851);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Sora', sans-serif;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 4px 16px rgba(37, 211, 102, 0.3);
        }

        .cc-wa-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 28px rgba(37, 211, 102, 0.4);
            color: #fff;
        }

        .cc-wa-btn svg {
            width: 20px;
            height: 20px;
            fill: #fff;
            flex-shrink: 0;
        }

        .cc-footer {
            text-align: center;
            margin-top: 1.75rem;
            font-size: 11.5px;
            color: #B0BCCE;
        }

        @media (max-width: 768px) {
            .cc-wrap {
                flex-direction: column;
            }

            .cc-left {
                width: 100%;
                position: static;
            }

            .cc-data-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="cc-root">
        <div class="cc-orb cc-orb-1"></div>
        <div class="cc-orb cc-orb-2"></div>

        <div class="cc-wrap">

            {{-- ── LEFT PANEL ── --}}
            <div class="cc-left">
                <div class="cc-brand">
                    <div class="cc-brand-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.9)" stroke-width="1.8">
                            <path d="M12 2L2 7l10 5 10-5-10-5z" />
                            <path d="M2 17l10 5 10-5" />
                            <path d="M2 12l10 5 10-5" />
                        </svg>
                    </div>
                    <div class="cc-brand-name">
                        Kawulo Halal
                        <small>Sertifikasi Produk Halal Untuk UMKM Low-Risk</small>
                    </div>
                </div>

                <p class="cc-left-title">Lamaran<br><em>Terkirim!</em></p>
                <p class="cc-left-desc">Proses pendaftaran Anda telah selesai. Berikut adalah tahapan selanjutnya.</p>

                <div class="cc-timeline">
                    <div class="cc-tl-item">
                        <div class="cc-tl-dot done">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="13"
                                height="13">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                        </div>
                        <div class="cc-tl-body">
                            <div class="cc-tl-label">Isi Formulir</div>
                            <div class="cc-tl-sub">Data diri berhasil dikirim</div>
                        </div>
                    </div>
                    <div class="cc-tl-item">
                        <div class="cc-tl-dot done">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="13"
                                height="13">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                        </div>
                        <div class="cc-tl-body">
                            <div class="cc-tl-label">Upload Dokumen</div>
                            <div class="cc-tl-sub">KTP, ijazah & pakta integritas</div>
                        </div>
                    </div>
                    <div class="cc-tl-item">
                        <div class="cc-tl-dot active">3</div>
                        <div class="cc-tl-body">
                            <div class="cc-tl-label">Konfirmasi WhatsApp</div>
                            <div class="cc-tl-sub">Kirim pesan konfirmasi ke tim kami</div>
                        </div>
                    </div>
                    <div class="cc-tl-item">
                        <div class="cc-tl-dot active">4</div>
                        <div class="cc-tl-body">
                            <div class="cc-tl-label">Proses Seleksi</div>
                            <div class="cc-tl-sub">Tim kami akan menghubungi Anda</div>
                        </div>
                    </div>
                </div>

                <div class="cc-quote">
                    <div class="cc-quote-mark">"</div>
                    <p class="cc-quote-text">Pengalaman dan dedikasi adalah kunci kesuksesan bersama kami.</p>
                    <p class="cc-quote-author">— Kawulo Halal</p>
                </div>
            </div>

            {{-- ── RIGHT PANEL ── --}}
            <div class="cc-right">

                <div class="cc-success-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                        <polyline points="22 4 12 14.01 9 11.01" />
                    </svg>
                </div>

                <h2 class="cc-conf-title">Lamaran Berhasil Dikirim!</h2>
                <p class="cc-conf-sub">
                    Terima kasih <strong>{{ $recruitment->nama_lengkap }}</strong>,<br>
                    data Anda telah kami terima dengan baik.
                </p>

                <div class="cc-divider"></div>

                <div class="cc-section-title">Ringkasan Lamaran</div>

                <div class="cc-data-grid">
                    <div class="cc-data-card">
                        <div class="cc-data-label">Posisi Dilamar</div>
                        <div class="cc-data-value">{{ $recruitment->recruit_type }}</div>
                    </div>
                    <div class="cc-data-card">
                        <div class="cc-data-label">No. Telepon</div>
                        <div class="cc-data-value">{{ $recruitment->telephone }}</div>
                    </div>
                    <div class="cc-data-card">
                        <div class="cc-data-label">Pendidikan Terakhir</div>
                        <div class="cc-data-value">{{ $recruitment->pendidikan_terakhir }}</div>
                    </div>
                    <div class="cc-data-card">
                        <div class="cc-data-label">Status</div>
                        <div class="cc-data-value">
                            <span class="cc-badge melamar">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    width="11" height="11">
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 8 12 12 14 14" />
                                </svg>
                                Melamar
                            </span>
                        </div>
                    </div>
                </div>

                <div class="cc-notice">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    <div>
                        <strong>Langkah selanjutnya:</strong> Klik tombol di bawah untuk mengirim pesan konfirmasi
                        ke tim Kawulo Halal melalui WhatsApp agar lamaran Anda segera diproses.
                    </div>
                </div>

                @php
                    $waNumber = '628976774482';
                    $pesan = urlencode(
                        "Halo, saya *{$recruitment->nama_lengkap}* ingin mengkonfirmasi pengisian Form Recruitment.\n\n" .
                            "*Posisi Dilamar:* {$recruitment->recruit_type}\n" .
                            "*No. Telepon:* {$recruitment->telephone}\n" .
                            "*Pendidikan:* {$recruitment->pendidikan_terakhir}\n" .
                            "*Jenis Kelamin:* {$recruitment->jenis_kelamin}\n" .
                            '*Rekomendasi:* ' .
                            ($recruitment->rekomendasi ?? '-') .
                            "\n" .
                            "*Status:* Melamar\n\n" .
                            'Mohon informasi proses selanjutnya. Terima kasih.',
                    );
                @endphp

                <a href="https://wa.me/{{ $waNumber }}?text={{ $pesan }}" target="_blank" class="cc-wa-btn">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                    </svg>
                    Konfirmasi ke WhatsApp
                </a>

                <div class="cc-footer">
                    &copy;
                    <script>
                        document.write(new Date().getFullYear())
                    </script> Kawulo Halal. All rights reserved.
                </div>
            </div>

        </div>
    </div>

@endsection
