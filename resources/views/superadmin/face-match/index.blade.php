@extends('layouts.app')
@section('template_title')
    Deteksi Wajah Duplikat
@endsection
@section('content')
    <div class="adm-page">

        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Deteksi Wajah Duplikat</h1>
                <p>Scan seluruh enumerator untuk menemukan foto pendamping yang mirip (≥80%)</p>
            </div>
            <a href="{{ route('superadmin.dashboard') }}" class="adm-btn-secondary">
                <svg viewBox="0 0 24 24">
                    <polyline points="15 18 9 12 15 6" />
                </svg>
                Kembali
            </a>
        </div>

        @if (session('error'))
            <div class="adm-alert adm-alert-danger" style="margin-bottom:16px;">
                <svg viewBox="0 0 24 24" style="width:18px;height:18px;flex-shrink:0;">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="12" />
                    <line x1="12" y1="16" x2="12" y2="16" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

            {{-- Stat cards --}}
            <div style="grid-column:1/-1;display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
                <div class="adm-stat-card">
                    <div class="adm-stat-label">Total Enumerator</div>
                    <div class="adm-stat-val">{{ $totalEnumerator }}</div>
                </div>
                <div class="adm-stat-card">
                    <div class="adm-stat-label">Total Foto Pendamping</div>
                    <div class="adm-stat-val">{{ $totalFoto }}</div>
                </div>
                <div class="adm-stat-card">
                    <div class="adm-stat-label">Total Kombinasi Perbandingan</div>
                    <div class="adm-stat-val">{{ number_format($totalKombinasi) }}</div>
                </div>
            </div>

            {{-- Mulai Scan --}}
            <div class="adm-card">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                        Mulai Scan Duplikat
                    </div>
                </div>
                <div style="padding:20px 24px;">

                    <div class="fm-info-box" style="margin-bottom:20px;">
                        <svg viewBox="0 0 24 24" style="width:16px;height:16px;flex-shrink:0;color:var(--adm-blue);">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="16" x2="12" y2="12" />
                            <line x1="12" y1="8" x2="12.01" y2="8" />
                        </svg>
                        <div style="font-size:13px;color:var(--adm-text-mid);line-height:1.6;">
                            Sistem akan membandingkan <strong>semua kombinasi pasangan foto</strong> milik
                            setiap enumerator. Pasangan dengan kemiripan <strong>≥80%</strong> akan ditandai
                            sebagai duplikat.<br><br>
                            Estimasi waktu: <strong>~{{ $estimasiMenit }} menit</strong>
                            ({{ $totalKombinasi }} kombinasi, Tier 1 ~50 req/menit).
                        </div>
                    </div>

                    {{-- Daftar enumerator --}}
                    <div
                        style="margin-bottom:20px;max-height:260px;overflow-y:auto;border:1px solid var(--adm-border);border-radius:8px;">
                        <table style="width:100%;border-collapse:collapse;font-size:13px;">
                            <thead>
                                <tr style="background:var(--adm-bg-faint);position:sticky;top:0;">
                                    <th style="padding:8px 12px;text-align:left;font-weight:600;color:var(--adm-text-mid);">
                                        Enumerator</th>
                                    <th
                                        style="padding:8px 12px;text-align:center;font-weight:600;color:var(--adm-text-mid);">
                                        Foto</th>
                                    <th
                                        style="padding:8px 12px;text-align:center;font-weight:600;color:var(--adm-text-mid);">
                                        Kombinasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($enumerators as $enum)
                                    <tr style="border-top:1px solid var(--adm-border);">
                                        <td style="padding:8px 12px;color:var(--adm-text-dark);">{{ $enum->nama_lengkap }}
                                        </td>
                                        <td style="padding:8px 12px;text-align:center;color:var(--adm-text-mid);">
                                            {{ $enum->foto_count }}</td>
                                        <td style="padding:8px 12px;text-align:center;">
                                            @php $k = $enum->foto_count * ($enum->foto_count - 1) / 2; @endphp
                                            @if ($k > 0)
                                                <span
                                                    style="color:var(--adm-blue);font-weight:600;">{{ $k }}</span>
                                            @else
                                                <span style="color:var(--adm-text-faint);">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <form action="{{ route('superadmin.face-match.match') }}" method="POST" id="scanForm">
                        @csrf
                        <button type="submit" class="adm-btn-primary" style="width:100%;justify-content:center;"
                            id="submitBtn">
                            <svg viewBox="0 0 24 24" style="width:16px;height:16px;" id="submitIcon">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            <svg viewBox="0 0 24 24"
                                style="width:16px;height:16px;display:none;animation:spin 1s linear infinite;"
                                id="loadingIcon">
                                <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                            </svg>
                            <span id="submitText">Mulai Scan Semua Enumerator</span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Cara Kerja --}}
            <div class="adm-card">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 16v-4" />
                            <path d="M12 8h.01" />
                        </svg>
                        Cara Kerja
                    </div>
                </div>
                <div style="padding:20px 24px;">
                    <div class="fm-steps">
                        <div class="fm-step">
                            <div class="fm-step-num">1</div>
                            <div>
                                <div class="fm-step-title">Kelompokkan per Enumerator</div>
                                <div class="fm-step-desc">Semua foto pendamping dikelompokkan berdasarkan enumerator_id.
                                </div>
                            </div>
                        </div>
                        <div class="fm-step">
                            <div class="fm-step-num">2</div>
                            <div>
                                <div class="fm-step-title">Buat Kombinasi Pasangan</div>
                                <div class="fm-step-desc">Setiap pasangan unik dalam 1 enumerator dibandingkan (A↔B, A↔C,
                                    B↔C, dst).</div>
                            </div>
                        </div>
                        <div class="fm-step">
                            <div class="fm-step-num">3</div>
                            <div>
                                <div class="fm-step-title">Analisis AI per Pasangan</div>
                                <div class="fm-step-desc">Claude AI menilai kemiripan wajah tiap pasangan secara paralel
                                    lewat queue.</div>
                            </div>
                        </div>
                        <div class="fm-step">
                            <div class="fm-step-num">4</div>
                            <div>
                                <div class="fm-step-title">Tandai ≥80% sebagai Duplikat</div>
                                <div class="fm-step-desc">Hasil dikelompokkan per enumerator, hanya pasangan ≥80% yang
                                    ditampilkan.</div>
                            </div>
                        </div>
                    </div>

                    <div class="fm-disclaimer">
                        <svg viewBox="0 0 24 24" style="width:15px;height:15px;flex-shrink:0;">
                            <path
                                d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                            <line x1="12" y1="9" x2="12" y2="13" />
                            <line x1="12" y1="17" x2="12.01" y2="17" />
                        </svg>
                        <span>Hasil merupakan estimasi AI. Lakukan verifikasi manual sebelum mengambil keputusan
                            resmi.</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <style>
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .adm-stat-card {
            background: var(--adm-card-bg);
            border: 1px solid var(--adm-border);
            border-top: 3px solid var(--adm-blue);
            border-radius: 10px;
            padding: 16px 18px;
        }

        .adm-stat-label {
            font-size: 12px;
            color: var(--adm-text-faint);
            margin-bottom: 6px;
        }

        .adm-stat-val {
            font-size: 26px;
            font-weight: 700;
            color: var(--adm-text-dark);
        }

        .fm-info-box {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            background: var(--adm-blue-lt);
            border: 1px solid var(--adm-blue);
            border-radius: 8px;
            padding: 12px 14px;
            opacity: .85;
        }

        .fm-steps {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-bottom: 20px;
        }

        .fm-step {
            display: flex;
            gap: 14px;
            align-items: flex-start;
        }

        .fm-step-num {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            flex-shrink: 0;
            background: var(--adm-blue);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 600;
        }

        .fm-step-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--adm-text-dark);
            margin-bottom: 2px;
        }

        .fm-step-desc {
            font-size: 13px;
            color: var(--adm-text-mid);
            line-height: 1.5;
        }

        .fm-disclaimer {
            display: flex;
            gap: 8px;
            align-items: flex-start;
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 12px;
            color: #92400e;
            line-height: 1.5;
        }
    </style>

    <script>
        document.getElementById('scanForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            document.getElementById('submitIcon').style.display = 'none';
            document.getElementById('loadingIcon').style.display = 'inline';
            document.getElementById('submitText').textContent = 'Mendispatch jobs... harap tunggu';
        });
    </script>
@endsection
