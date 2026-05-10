@extends('layouts.app')
@section('template_title')
    Pencocokan Wajah
@endsection
@section('content')
    <div class="adm-page">

        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Pencocokan Wajah</h1>
                <p>Upload foto wajah untuk dicocokkan dengan data pendamping di database</p>
            </div>
            <div style="display:flex;gap:8px;">
                <a href="{{ route('superadmin.dashboard') }}" class="adm-btn-secondary">
                    <svg viewBox="0 0 24 24">
                        <polyline points="15 18 9 12 15 6" />
                    </svg>
                    Kembali
                </a>
            </div>
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

            {{-- Upload Form --}}
            <div class="adm-card">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <polyline points="17 8 12 3 7 8" />
                            <line x1="12" y1="3" x2="12" y2="15" />
                        </svg>
                        Upload Foto Wajah
                    </div>
                </div>
                <div style="padding:20px 24px;">

                    {{-- Info jumlah data --}}
                    <div class="fm-info-box" style="margin-bottom:20px;">
                        <svg viewBox="0 0 24 24" style="width:16px;height:16px;flex-shrink:0;color:var(--adm-blue);">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="16" x2="12" y2="12" />
                            <line x1="12" y1="8" x2="12.01" y2="8" />
                        </svg>
                        <div style="font-size:13px;color:var(--adm-text-mid);">
                            Akan mencocokkan dengan <strong>{{ $totalData }} foto pendamping</strong> di database.
                            @if ($totalData > 30)
                                Proses mungkin membutuhkan waktu beberapa menit.
                            @endif
                        </div>
                    </div>

                    <form action="{{ route('superadmin.face-match.match') }}" method="POST" enctype="multipart/form-data"
                        id="faceMatchForm">
                        @csrf

                        <div class="adm-form-group" style="margin-bottom:20px;">
                            <label class="adm-label">Foto Wajah (KTP atau foto langsung)</label>
                            <div class="fm-drop-zone" id="dropZone">
                                <input type="file" name="foto_query" id="fotoInput" accept="image/jpeg,image/png"
                                    style="display:none;">
                                <div class="fm-drop-content" id="dropContent">
                                    <svg viewBox="0 0 24 24"
                                        style="width:40px;height:40px;color:var(--adm-text-faint);margin-bottom:12px;">
                                        <rect x="3" y="3" width="18" height="18" rx="2" />
                                        <circle cx="8.5" cy="8.5" r="1.5" />
                                        <polyline points="21 15 16 10 5 21" />
                                    </svg>
                                    <p style="color:var(--adm-text-mid);margin:0 0 8px;font-size:14px;">Klik atau seret foto
                                        ke sini</p>
                                    <p style="color:var(--adm-text-faint);margin:0;font-size:12px;">JPG, PNG · Maks. 5MB</p>
                                </div>
                                <div class="fm-preview" id="previewContainer" style="display:none;">
                                    <img id="previewImg" src="" alt="Preview"
                                        style="max-height:200px;border-radius:8px;object-fit:contain;">
                                    <button type="button" class="fm-remove-btn" id="removeBtn">
                                        <svg viewBox="0 0 24 24" style="width:14px;height:14px;">
                                            <line x1="18" y1="6" x2="6" y2="18" />
                                            <line x1="6" y1="6" x2="18" y2="18" />
                                        </svg>
                                        Hapus
                                    </button>
                                </div>
                            </div>
                            @error('foto_query')
                                <span class="adm-error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="adm-form-group" style="margin-bottom:24px;">
                            <div class="fm-info-box">
                                <svg viewBox="0 0 24 24"
                                    style="width:16px;height:16px;flex-shrink:0;color:var(--adm-blue);">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="16" x2="12" y2="12" />
                                    <line x1="12" y1="8" x2="12.01" y2="8" />
                                </svg>
                                <div style="font-size:13px;color:var(--adm-text-mid);line-height:1.6;">
                                    <strong>Tips untuk hasil terbaik:</strong><br>
                                    • Pastikan wajah terlihat jelas dan tidak buram<br>
                                    • Foto KTP: area wajah akan diekstrak otomatis oleh AI<br>
                                    • Gunakan foto dengan pencahayaan yang cukup
                                </div>
                            </div>
                        </div>

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
                            <span id="submitText">Cari Kecocokan</span>
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
                                <div class="fm-step-title">Upload Foto</div>
                                <div class="fm-step-desc">Upload foto wajah yang ingin dicari. Bisa dari foto KTP atau foto
                                    biasa.</div>
                            </div>
                        </div>
                        <div class="fm-step">
                            <div class="fm-step-num">2</div>
                            <div>
                                <div class="fm-step-title">Resize Otomatis</div>
                                <div class="fm-step-desc">Sistem mengecilkan foto secara otomatis agar proses hemat memori
                                    dan tetap akurat.</div>
                            </div>
                        </div>
                        <div class="fm-step">
                            <div class="fm-step-num">3</div>
                            <div>
                                <div class="fm-step-title">Analisis AI per Foto</div>
                                <div class="fm-step-desc">Claude AI membandingkan fitur wajah satu per satu dengan setiap
                                    foto pendamping di database.</div>
                            </div>
                        </div>
                        <div class="fm-step">
                            <div class="fm-step-num">4</div>
                            <div>
                                <div class="fm-step-title">Hasil Terurut</div>
                                <div class="fm-step-desc">Hasil ditampilkan dari kemungkinan cocok tertinggi ke terendah
                                    untuk memudahkan verifikasi.</div>
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
                        <span>Hasil pencocokan merupakan estimasi AI dan bukan keputusan final. Gunakan sebagai alat bantu
                            verifikasi saja.</span>
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

        .fm-drop-zone {
            border: 2px dashed var(--adm-border);
            border-radius: 12px;
            padding: 32px 20px;
            text-align: center;
            cursor: pointer;
            transition: border-color .2s, background .2s;
            background: var(--adm-bg-faint);
        }

        .fm-drop-zone:hover,
        .fm-drop-zone.dragover {
            border-color: var(--adm-blue);
            background: var(--adm-blue-lt);
        }

        .fm-drop-content {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .fm-preview {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .fm-remove-btn {
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
        }

        .fm-remove-btn:hover {
            background: #fee2e2;
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
        const dropZone = document.getElementById('dropZone');
        const fotoInput = document.getElementById('fotoInput');
        const dropContent = document.getElementById('dropContent');
        const previewContainer = document.getElementById('previewContainer');
        const previewImg = document.getElementById('previewImg');
        const removeBtn = document.getElementById('removeBtn');
        const form = document.getElementById('faceMatchForm');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const submitIcon = document.getElementById('submitIcon');
        const loadingIcon = document.getElementById('loadingIcon');

        dropZone.addEventListener('click', () => fotoInput.click());
        dropZone.addEventListener('dragover', e => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            const file = e.dataTransfer.files[0];
            if (file) loadPreview(file);
        });

        fotoInput.addEventListener('change', () => {
            if (fotoInput.files[0]) loadPreview(fotoInput.files[0]);
        });

        removeBtn.addEventListener('click', e => {
            e.stopPropagation();
            fotoInput.value = '';
            previewContainer.style.display = 'none';
            dropContent.style.display = 'flex';
        });

        function loadPreview(file) {
            const reader = new FileReader();
            reader.onload = ev => {
                previewImg.src = ev.target.result;
                dropContent.style.display = 'none';
                previewContainer.style.display = 'flex';
            };
            reader.readAsDataURL(file);

            const dt = new DataTransfer();
            dt.items.add(file);
            fotoInput.files = dt.files;
        }

        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
            submitIcon.style.display = 'none';
            loadingIcon.style.display = 'inline';
            submitText.textContent = 'Memproses... harap tunggu';
        });
    </script>
@endsection
