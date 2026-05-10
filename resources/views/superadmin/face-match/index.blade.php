@extends('layouts.app')
@section('template_title')
    Pencocokan Wajah Otomatis
@endsection
@section('content')
    <div class="adm-page">

        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Pencocokan Wajah</h1>
                <p>Upload foto wajah — sistem akan men-scan seluruh foto pendamping dari semua enumerator secara otomatis
                </p>
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

            {{-- Form --}}
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

                    {{-- Info scope scan --}}
                    <div class="fm-scope-box" style="margin-bottom:20px;">
                        <div class="fm-scope-item">
                            <svg viewBox="0 0 24 24" style="width:18px;height:18px;color:var(--adm-blue);flex-shrink:0;">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                            <div>
                                <div class="fm-scope-val">{{ number_format($totalEnumerator) }}</div>
                                <div class="fm-scope-label">Enumerator akan di-scan</div>
                            </div>
                        </div>
                        <div class="fm-scope-divider"></div>
                        <div class="fm-scope-item">
                            <svg viewBox="0 0 24 24" style="width:18px;height:18px;color:var(--adm-blue);flex-shrink:0;">
                                <rect x="3" y="3" width="18" height="18" rx="2" />
                                <circle cx="8.5" cy="8.5" r="1.5" />
                                <polyline points="21 15 16 10 5 21" />
                            </svg>
                            <div>
                                <div class="fm-scope-val">{{ number_format($totalFoto) }}</div>
                                <div class="fm-scope-label">Total foto pendamping</div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('superadmin.face-match.match') }}" method="POST" enctype="multipart/form-data"
                        id="faceMatchForm">
                        @csrf

                        {{-- Upload foto --}}
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
                                    <p style="color:var(--adm-text-mid);margin:0 0 8px;font-size:14px;">
                                        Klik atau seret foto ke sini
                                    </p>
                                    <p style="color:var(--adm-text-faint);margin:0;font-size:12px;">
                                        JPG, PNG · Maks. 5MB
                                    </p>
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
                                <div class="fm-step-title">Upload Foto Wajah</div>
                                <div class="fm-step-desc">Upload foto KTP atau foto wajah yang ingin dicari kecocokannya di
                                    seluruh data.</div>
                            </div>
                        </div>
                        <div class="fm-step">
                            <div class="fm-step-num">2</div>
                            <div>
                                <div class="fm-step-title">Scan Semua Enumerator</div>
                                <div class="fm-step-desc">Sistem secara otomatis memindai
                                    <strong>{{ number_format($totalFoto) }} foto</strong> dari
                                    <strong>{{ $totalEnumerator }} enumerator</strong> secara paralel menggunakan AI.</div>
                            </div>
                        </div>
                        <div class="fm-step">
                            <div class="fm-step-num">3</div>
                            <div>
                                <div class="fm-step-title">Filter ≥80% Kemiripan</div>
                                <div class="fm-step-desc">Claude AI menilai setiap foto dan menyaring yang memiliki
                                    kemiripan wajah di atas 80%.</div>
                            </div>
                        </div>
                        <div class="fm-step">
                            <div class="fm-step-num">4</div>
                            <div>
                                <div class="fm-step-title">Tampilkan Top 3 Terbaik</div>
                                <div class="fm-step-desc">Hanya 3 foto dengan confidence tertinggi yang ditampilkan sebagai
                                    hasil akhir.</div>
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

        .fm-scope-box {
            display: flex;
            align-items: center;
            gap: 0;
            background: var(--adm-blue-lt);
            border: 1px solid var(--adm-blue);
            border-radius: 10px;
            overflow: hidden;
        }

        .fm-scope-item {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 16px;
        }

        .fm-scope-divider {
            width: 1px;
            height: 40px;
            background: var(--adm-blue);
            opacity: .3;
            flex-shrink: 0;
        }

        .fm-scope-val {
            font-size: 20px;
            font-weight: 700;
            color: var(--adm-blue);
            line-height: 1.1;
        }

        .fm-scope-label {
            font-size: 11px;
            color: var(--adm-text-mid);
            margin-top: 1px;
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

        dropZone.addEventListener('click', () => fotoInput.click());
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

        document.getElementById('faceMatchForm').addEventListener('submit', function() {
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('submitIcon').style.display = 'none';
            document.getElementById('loadingIcon').style.display = 'inline';
            document.getElementById('submitText').textContent = 'Memproses... harap tunggu';
        });
    </script>
@endsection
