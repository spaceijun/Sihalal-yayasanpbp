@extends('layouts.app')
@section('template_title')
    Verifikasi KTP — Pencocokan Foto Pendamping
@endsection

@section('content')
    <div class="adm-page">

        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Verifikasi KTP</h1>
                <p>Upload foto KTP — AI akan mencocokkan dengan foto pendamping di seluruh database dan menampilkan 3 yang
                    paling mirip</p>
            </div>
            <a href="{{ url($routePrefix . '/data-lapangans') }}" class="adm-btn-secondary">
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

        @if (empty($geminiApiKey))
            <div class="adm-alert adm-alert-warning" style="margin-bottom:16px;">
                <svg viewBox="0 0 24 24" style="width:18px;height:18px;flex-shrink:0;">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                    <line x1="12" y1="9" x2="12" y2="13" />
                    <line x1="12" y1="17" x2="12.01" y2="17" />
                </svg>
                <span>
                    <strong>GEMINI_API_KEY belum dikonfigurasi.</strong>
                    Silakan atur di
                    <a href="{{ route($routePrefix . '.settings.index') }}" style="color:inherit;font-weight:700;">Setting
                        Website → Tab API Keys</a>.
                </span>
            </div>
        @endif

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start;">

            {{-- ── FORM UPLOAD ── --}}
            <div class="adm-card">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                            <line x1="12" y1="18" x2="12" y2="12" />
                            <line x1="9" y1="15" x2="15" y2="15" />
                        </svg>
                        Upload Foto KTP
                    </div>
                </div>
                <div style="padding:20px 24px;">

                    {{-- Info --}}
                    <div class="kv-info-box" style="margin-bottom:20px;">
                        <div class="kv-info-item">
                            <svg viewBox="0 0 24 24" style="width:20px;height:20px;color:var(--adm-blue);flex-shrink:0;">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M12 16v-4" />
                                <path d="M12 8h.01" />
                            </svg>
                            <div>
                                <div class="kv-info-label">Cara Kerja</div>
                                <div class="kv-info-desc">Upload foto KTP → AI Gemini membandingkan pasfoto KTP dengan
                                    setiap foto pendamping di database → Tampilkan top 3 paling mirip beserta skor
                                    kepercayaan</div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route($routePrefix . '.ktp-verifikasi.verify') }}" method="POST"
                        enctype="multipart/form-data" id="ktpForm">
                        @csrf

                        {{-- Drop Zone --}}
                        <div class="adm-field" style="margin-bottom:20px;">
                            <label class="adm-label">Foto KTP <span class="req">*</span></label>
                            <div class="kv-drop-zone" id="dropZone">
                                <input type="file" name="foto_ktp" id="ktpInput" accept="image/jpeg,image/png"
                                    style="display:none;">
                                <div class="kv-drop-content" id="dropContent">
                                    <svg viewBox="0 0 24 24"
                                        style="width:48px;height:48px;color:var(--adm-text-faint);margin-bottom:12px;">
                                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2" />
                                        <line x1="1" y1="10" x2="23" y2="10" />
                                    </svg>
                                    <p style="color:var(--adm-text-mid);margin:0 0 6px;font-size:14px;font-weight:600;">
                                        Klik atau seret foto KTP ke sini
                                    </p>
                                    <p style="color:var(--adm-text-faint);margin:0;font-size:12px;">
                                        JPG, PNG · Maksimal 5MB · Pasfoto KTP berlatar merah/biru
                                    </p>
                                </div>
                                <div class="kv-preview" id="previewContainer" style="display:none;">
                                    <img id="previewImg" src="" alt="Preview KTP"
                                        style="max-height:240px;max-width:100%;border-radius:8px;object-fit:contain;box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                                    <button type="button" class="kv-remove-btn" id="removeBtn">
                                        <svg viewBox="0 0 24 24" style="width:13px;height:13px;">
                                            <line x1="18" y1="6" x2="6" y2="18" />
                                            <line x1="6" y1="6" x2="18" y2="18" />
                                        </svg>
                                        Ganti Foto
                                    </button>
                                </div>
                            </div>
                            @error('foto_ktp')
                                <span class="adm-error-msg">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="adm-btn-primary" id="submitBtn" disabled
                            style="width:100%;justify-content:center;gap:8px;">
                            <svg viewBox="0 0 24 24" style="width:16px;height:16px;" id="submitIcon">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            <svg viewBox="0 0 24 24"
                                style="width:16px;height:16px;display:none;animation:kv-spin 1s linear infinite;"
                                id="loadingIcon">
                                <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                            </svg>
                            <span id="submitText">Pilih foto KTP terlebih dahulu</span>
                        </button>

                        <p
                            style="font-size:11.5px;color:var(--adm-text-faint);text-align:center;margin-top:10px;line-height:1.5;">
                            ⚠️ Proses ini akan memakan waktu beberapa menit tergantung jumlah data di database
                        </p>
                    </form>
                </div>
            </div>

            {{-- ── CARA KERJA & KETERANGAN ── --}}
            <div style="display:flex;flex-direction:column;gap:16px;">

                <div class="adm-card">
                    <div class="adm-card-header">
                        <div class="adm-card-title">
                            <svg viewBox="0 0 24 24">
                                <path
                                    d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22" />
                            </svg>
                            Proses AI Verifikasi KTP
                        </div>
                    </div>
                    <div style="padding:20px 24px;">
                        <div class="kv-steps">
                            <div class="kv-step">
                                <div class="kv-step-num">1</div>
                                <div>
                                    <div class="kv-step-title">Ekstraksi Data KTP</div>
                                    <div class="kv-step-desc">AI membaca nama, NIK, jenis kelamin, dan pasfoto dari foto
                                        KTP yang diunggah sebagai referensi utama</div>
                                </div>
                            </div>
                            <div class="kv-step">
                                <div class="kv-step-num">2</div>
                                <div>
                                    <div class="kv-step-title">Analisis Biometrik 6 Dimensi</div>
                                    <div class="kv-step-desc">Membandingkan kesesuaian gender, geometri wajah, area mata,
                                        hidung & mulut, tanda khusus kulit, dan bentuk rambut/telinga</div>
                                </div>
                            </div>
                            <div class="kv-step">
                                <div class="kv-step-num">3</div>
                                <div>
                                    <div class="kv-step-title">Skor Kemiripan & Status</div>
                                    <div class="kv-step-desc">Setiap foto pendamping mendapatkan skor 0-100% dan status:
                                        Terverifikasi / Tidak Cocok / Keraguan Tinggi</div>
                                </div>
                            </div>
                            <div class="kv-step">
                                <div class="kv-step-num">4</div>
                                <div>
                                    <div class="kv-step-title">Top 3 Terbaik</div>
                                    <div class="kv-step-desc">Hasil diurutkan dan hanya 3 foto pendamping dengan skor
                                        tertinggi yang ditampilkan beserta analisis detail</div>
                                </div>
                            </div>
                        </div>

                        <div class="kv-disclaimer">
                            <svg viewBox="0 0 24 24" style="width:15px;height:15px;flex-shrink:0;">
                                <path
                                    d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                                <line x1="12" y1="9" x2="12" y2="13" />
                                <line x1="12" y1="17" x2="12.01" y2="17" />
                            </svg>
                            <span>Hasil merupakan estimasi AI forensik. Selalu lakukan verifikasi manual sebelum mengambil
                                keputusan resmi. Skor ≥75% mengindikasikan kemungkinan kecocokan tinggi.</span>
                        </div>
                    </div>
                </div>

                <div class="adm-card">
                    <div class="adm-card-header">
                        <div class="adm-card-title">
                            <svg viewBox="0 0 24 24">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                            </svg>
                            Status API Gemini
                        </div>
                    </div>
                    <div style="padding:16px 24px;">
                        @if (!empty($geminiApiKey))
                            <div
                                style="display:flex;align-items:center;gap:10px;padding:12px 16px;background:#ECFDF5;border:1px solid #6EE7B7;border-radius:8px;">
                                <svg viewBox="0 0 24 24" style="width:18px;height:18px;color:#059669;flex-shrink:0;">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                    <polyline points="22 4 12 14.01 9 11.01" />
                                </svg>
                                <div>
                                    <div style="font-size:13px;font-weight:700;color:#065F46;">API Key Terdeteksi</div>
                                    <div style="font-size:12px;color:#6B7280;margin-top:1px;">
                                        {{ substr($geminiApiKey, 0, 8) }}••••••••{{ substr($geminiApiKey, -4) }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div
                                style="display:flex;align-items:center;gap:10px;padding:12px 16px;background:#FEF2F2;border:1px solid #FECACA;border-radius:8px;">
                                <svg viewBox="0 0 24 24" style="width:18px;height:18px;color:#DC2626;flex-shrink:0;">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="15" y1="9" x2="9" y2="15" />
                                    <line x1="9" y1="9" x2="15" y2="15" />
                                </svg>
                                <div>
                                    <div style="font-size:13px;font-weight:700;color:#7F1D1D;">API Key Belum Dikonfigurasi
                                    </div>
                                    <div style="font-size:12px;color:#6B7280;margin-top:1px;">
                                        <a href="{{ route($routePrefix . '.settings.index') }}"
                                            style="color:var(--adm-blue);">Atur di Setting Website →</a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        @keyframes kv-spin {
            to {
                transform: rotate(360deg);
            }
        }

        .kv-info-box {
            background: var(--adm-blue-lt);
            border: 1px solid rgba(37, 99, 235, 0.2);
            border-radius: 10px;
            padding: 14px 16px;
        }

        .kv-info-item {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .kv-info-label {
            font-size: 12px;
            font-weight: 700;
            color: var(--adm-blue);
            margin-bottom: 4px;
        }

        .kv-info-desc {
            font-size: 12.5px;
            color: var(--adm-text-mid);
            line-height: 1.55;
        }

        /* Drop Zone */
        .kv-drop-zone {
            border: 2px dashed var(--adm-border-mid);
            border-radius: 12px;
            padding: 32px 20px;
            text-align: center;
            cursor: pointer;
            transition: border-color .2s, background .2s;
            background: var(--adm-bg-faint);
            position: relative;
        }

        .kv-drop-zone:hover,
        .kv-drop-zone.dragover {
            border-color: var(--adm-blue);
            background: var(--adm-blue-lt);
        }

        .kv-drop-content {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .kv-preview {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .kv-remove-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #dc2626;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 6px;
            padding: 5px 12px;
            cursor: pointer;
            transition: background .15s;
        }

        .kv-remove-btn:hover {
            background: #fee2e2;
        }

        /* Steps */
        .kv-steps {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-bottom: 18px;
        }

        .kv-step {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .kv-step-num {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            flex-shrink: 0;
            background: linear-gradient(135deg, var(--adm-blue), #1d4ed8);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);
        }

        .kv-step-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--adm-text-dark);
            margin-bottom: 2px;
        }

        .kv-step-desc {
            font-size: 12px;
            color: var(--adm-text-mid);
            line-height: 1.5;
        }

        .kv-disclaimer {
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
        const dropZone = document.getElementById('dropZone');
        const ktpInput = document.getElementById('ktpInput');
        const dropContent = document.getElementById('dropContent');
        const prevCont = document.getElementById('previewContainer');
        const previewImg = document.getElementById('previewImg');
        const removeBtn = document.getElementById('removeBtn');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        let hasPhoto = false;

        dropZone.addEventListener('click', () => ktpInput.click());
        dropZone.addEventListener('dragover', e => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            if (e.dataTransfer.files[0]) loadPreview(e.dataTransfer.files[0]);
        });
        ktpInput.addEventListener('change', () => {
            if (ktpInput.files[0]) loadPreview(ktpInput.files[0]);
        });
        removeBtn.addEventListener('click', e => {
            e.stopPropagation();
            ktpInput.value = '';
            prevCont.style.display = 'none';
            dropContent.style.display = 'flex';
            hasPhoto = false;
            updateBtn();
        });

        function loadPreview(file) {
            const reader = new FileReader();
            reader.onload = ev => {
                previewImg.src = ev.target.result;
                dropContent.style.display = 'none';
                prevCont.style.display = 'flex';
                hasPhoto = true;
                updateBtn();
            };
            reader.readAsDataURL(file);
            const dt = new DataTransfer();
            dt.items.add(file);
            ktpInput.files = dt.files;
        }

        function updateBtn() {
            submitBtn.disabled = !hasPhoto;
            submitText.textContent = hasPhoto ? 'Mulai Verifikasi KTP' : 'Pilih foto KTP terlebih dahulu';
        }

        document.getElementById('ktpForm').addEventListener('submit', function() {
            submitBtn.disabled = true;
            document.getElementById('submitIcon').style.display = 'none';
            document.getElementById('loadingIcon').style.display = 'inline';
            submitText.textContent = 'Memproses... harap tunggu beberapa menit';
        });
    </script>
@endsection
