@extends('layouts.app')
@section('template_title')
    Verifikasi KTP — Pencocokan Foto
@endsection

@section('content')
<div class="adm-page">

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Verifikasi KTP</h1>
            <p>Upload foto KTP + file ZIP berisi foto pendamping — AI akan membandingkan dan menemukan 3 foto paling mirip</p>
        </div>
        <a href="{{ url($routePrefix . '/data-lapangans') }}" class="adm-btn-secondary">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            Kembali
        </a>
    </div>

    {{-- Alert Error --}}
    @if (session('error'))
        <div class="adm-alert adm-alert-danger" style="margin-bottom:16px;">
            <svg viewBox="0 0 24 24" style="width:18px;height:18px;flex-shrink:0;">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12" y2="16"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="adm-alert adm-alert-danger" style="margin-bottom:16px;">
            <svg viewBox="0 0 24 24" style="width:18px;height:18px;flex-shrink:0;">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12" y2="16"/>
            </svg>
            <ul style="margin:0;padding-left:16px;">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (empty($geminiApiKey))
        <div class="adm-alert adm-alert-warning" style="margin-bottom:16px;">
            <svg viewBox="0 0 24 24" style="width:18px;height:18px;flex-shrink:0;">
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            <strong>Gemini API Key belum dikonfigurasi.</strong>
            <a href="{{ route($routePrefix . '.settings.index') }}" style="color:inherit;font-weight:700;margin-left:4px;">Atur di Setting Website → Tab API Keys →</a>
        </div>
    @endif

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start;">

        {{-- ── FORM UPLOAD ── --}}
        <div class="adm-card">
            <div class="adm-card-header">
                <div class="adm-card-title">
                    <svg viewBox="0 0 24 24">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    Upload File
                </div>
            </div>
            <div style="padding:20px 24px;">

                <form action="{{ route($routePrefix . '.ktp-verifikasi.verify') }}" method="POST"
                      enctype="multipart/form-data" id="ktpForm">
                    @csrf

                    {{-- KTP Upload --}}
                    <div class="adm-field" style="margin-bottom:20px;">
                        <label class="adm-label">
                            <svg viewBox="0 0 24 24" style="width:14px;height:14px;display:inline;margin-right:4px;">
                                <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                            </svg>
                            Foto KTP <span class="req">*</span>
                        </label>
                        <div class="kv-drop-zone" id="dropZoneKtp" onclick="document.getElementById('ktpInput').click()">
                            <input type="file" name="foto_ktp" id="ktpInput" accept="image/jpeg,image/png" style="display:none;">
                            <div class="kv-drop-content" id="dropContentKtp">
                                <svg viewBox="0 0 24 24" style="width:36px;height:36px;color:var(--adm-text-faint);margin-bottom:8px;">
                                    <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                                </svg>
                                <p style="color:var(--adm-text-mid);margin:0 0 4px;font-size:13px;font-weight:600;">Klik untuk pilih foto KTP</p>
                                <p style="color:var(--adm-text-faint);margin:0;font-size:11.5px;">JPG, PNG · Maks 5MB</p>
                            </div>
                            <div class="kv-preview" id="previewKtpContainer" style="display:none;">
                                <img id="previewKtpImg" src="" alt="Preview KTP"
                                     style="max-height:160px;max-width:100%;border-radius:8px;object-fit:contain;box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                                <button type="button" class="kv-remove-btn" id="removeKtpBtn">
                                    <svg viewBox="0 0 24 24" style="width:12px;height:12px;">
                                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                    </svg>
                                    Ganti
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- ZIP Upload --}}
                    <div class="adm-field" style="margin-bottom:24px;">
                        <label class="adm-label">
                            <svg viewBox="0 0 24 24" style="width:14px;height:14px;display:inline;margin-right:4px;">
                                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/>
                                <line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                            File ZIP Foto Pendamping <span class="req">*</span>
                        </label>
                        <div class="kv-drop-zone kv-drop-zip" id="dropZoneZip"
                             onclick="document.getElementById('zipInput').click()"
                             ondragover="event.preventDefault();this.classList.add('dragover')"
                             ondragleave="this.classList.remove('dragover')"
                             ondrop="handleZipDrop(event)">
                            <input type="file" name="zip_fotos" id="zipInput" accept=".zip" style="display:none;">
                            <div class="kv-drop-content" id="dropContentZip">
                                <svg viewBox="0 0 24 24" style="width:36px;height:36px;color:var(--adm-text-faint);margin-bottom:8px;">
                                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/>
                                    <line x1="12" y1="3" x2="12" y2="15"/>
                                </svg>
                                <p style="color:var(--adm-text-mid);margin:0 0 4px;font-size:13px;font-weight:600;">Klik atau seret file ZIP ke sini</p>
                                <p style="color:var(--adm-text-faint);margin:0;font-size:11.5px;">Format ZIP · Maks 50MB · Berisi foto JPG/PNG</p>
                            </div>
                            <div id="zipFileInfo" style="display:none;padding:12px;text-align:center;">
                                <svg viewBox="0 0 24 24" style="width:32px;height:32px;color:#7C3AED;margin-bottom:6px;">
                                    <path d="M13 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V9z"/>
                                    <polyline points="13 2 13 9 20 9"/>
                                </svg>
                                <div id="zipFileName" style="font-size:13px;font-weight:700;color:var(--adm-text-dark);margin-bottom:4px;"></div>
                                <div id="zipFileSize" style="font-size:11.5px;color:var(--adm-text-faint);margin-bottom:8px;"></div>
                                <button type="button" class="kv-remove-btn" id="removeZipBtn"
                                        style="border-color:#7C3AED33;color:#7C3AED;background:#F5F3FF;">
                                    <svg viewBox="0 0 24 24" style="width:12px;height:12px;">
                                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                    </svg>
                                    Ganti ZIP
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="adm-btn-primary" id="submitBtn" disabled
                            style="width:100%;justify-content:center;gap:8px;">
                        <svg viewBox="0 0 24 24" style="width:16px;height:16px;" id="submitIcon">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <svg viewBox="0 0 24 24" style="width:16px;height:16px;display:none;animation:kv-spin 1s linear infinite;" id="loadingIcon">
                            <path d="M21 12a9 9 0 11-6.219-8.56"/>
                        </svg>
                        <span id="submitText">Pilih foto KTP dan file ZIP terlebih dahulu</span>
                    </button>

                    <p style="font-size:11.5px;color:var(--adm-text-faint);text-align:center;margin-top:10px;line-height:1.5;">
                        ⚠️ Proses memakan waktu sesuai jumlah foto dalam ZIP — setiap foto dianalisis satu per satu oleh AI
                    </p>
                </form>
            </div>
        </div>

        {{-- ── PANDUAN & STATUS ── --}}
        <div style="display:flex;flex-direction:column;gap:16px;">

            <div class="adm-card">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 16v-4"/><path d="M12 8h.01"/>
                        </svg>
                        Cara Kerja
                    </div>
                </div>
                <div style="padding:16px 24px 20px;">
                    <div class="kv-steps">
                        <div class="kv-step">
                            <div class="kv-step-num">1</div>
                            <div>
                                <div class="kv-step-title">Upload KTP + ZIP</div>
                                <div class="kv-step-desc">Upload foto KTP sebagai referensi, dan file ZIP berisi kumpulan foto pendamping yang ingin dibandingkan</div>
                            </div>
                        </div>
                        <div class="kv-step">
                            <div class="kv-step-num">2</div>
                            <div>
                                <div class="kv-step-title">Ekstrak Otomatis</div>
                                <div class="kv-step-desc">Sistem mengekstrak semua foto dari dalam ZIP. Format yang didukung: JPG, PNG, WebP — subfolder didukung</div>
                            </div>
                        </div>
                        <div class="kv-step">
                            <div class="kv-step-num">3</div>
                            <div>
                                <div class="kv-step-title">Analisis Biometrik AI</div>
                                <div class="kv-step-desc">Gemini 3.5 Flash membandingkan pasfoto KTP dengan setiap foto dari ZIP menggunakan 6 dimensi analisis wajah forensik</div>
                            </div>
                        </div>
                        <div class="kv-step">
                            <div class="kv-step-num">4</div>
                            <div>
                                <div class="kv-step-title">Top 3 Hasil</div>
                                <div class="kv-step-desc">Ditampilkan 3 foto dengan skor kemiripan tertinggi beserta status verifikasi dan analisis justifikasi AI</div>
                            </div>
                        </div>
                    </div>

                    <div class="kv-tip-box">
                        <svg viewBox="0 0 24 24" style="width:15px;height:15px;flex-shrink:0;color:#7C3AED;">
                            <circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>
                        </svg>
                        <span><strong>Tips:</strong> Pastikan foto dalam ZIP memiliki nama file yang deskriptif (misal: nama_orang.jpg) agar mudah diidentifikasi pada hasil.</span>
                    </div>
                </div>
            </div>

            <div class="adm-card">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24">
                            <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 11-7.778 7.778 5.5 5.5 0 017.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
                        </svg>
                        Status API Gemini
                    </div>
                </div>
                <div style="padding:14px 20px;">
                    @if (!empty($geminiApiKey))
                        <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:#ECFDF5;border:1px solid #6EE7B7;border-radius:8px;">
                            <svg viewBox="0 0 24 24" style="width:18px;height:18px;color:#059669;flex-shrink:0;">
                                <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            <div>
                                <div style="font-size:13px;font-weight:700;color:#065F46;">API Key Aktif · gemini-3.5-flash</div>
                                <div style="font-size:11.5px;color:#6B7280;margin-top:1px;font-family:monospace;">
                                    {{ substr($geminiApiKey, 0, 6) }}••••{{ substr($geminiApiKey, -4) }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:#FEF2F2;border:1px solid #FECACA;border-radius:8px;">
                            <svg viewBox="0 0 24 24" style="width:18px;height:18px;color:#DC2626;flex-shrink:0;">
                                <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                            </svg>
                            <div>
                                <div style="font-size:13px;font-weight:700;color:#7F1D1D;">API Key Belum Dikonfigurasi</div>
                                <div style="font-size:11.5px;margin-top:1px;">
                                    <a href="{{ route($routePrefix . '.settings.index') }}" style="color:var(--adm-blue);">Atur di Setting Website →</a>
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
@keyframes kv-spin { to { transform: rotate(360deg); } }

.kv-drop-zone {
    border: 2px dashed var(--adm-border-mid);
    border-radius: 12px;
    padding: 24px 16px;
    text-align: center;
    cursor: pointer;
    transition: border-color .2s, background .2s;
    background: var(--adm-bg-faint);
}
.kv-drop-zone:hover, .kv-drop-zone.dragover {
    border-color: var(--adm-blue);
    background: var(--adm-blue-lt);
}
.kv-drop-zip:hover, .kv-drop-zip.dragover {
    border-color: #7C3AED;
    background: #F5F3FF;
}
.kv-drop-content { display: flex; flex-direction: column; align-items: center; }
.kv-preview { display: flex; flex-direction: column; align-items: center; gap: 10px; }
.kv-remove-btn {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11.5px; color: #dc2626; background: #fef2f2;
    border: 1px solid #fecaca; border-radius: 6px;
    padding: 4px 10px; cursor: pointer; transition: background .15s;
}
.kv-remove-btn:hover { background: #fee2e2; }

.kv-steps { display: flex; flex-direction: column; gap: 12px; margin-bottom: 14px; }
.kv-step { display: flex; gap: 10px; align-items: flex-start; }
.kv-step-num {
    width: 24px; height: 24px; border-radius: 50%; flex-shrink: 0;
    background: linear-gradient(135deg,var(--adm-blue),#1d4ed8); color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700; box-shadow: 0 2px 6px rgba(37,99,235,0.3);
}
.kv-step-title { font-size: 12.5px; font-weight: 700; color: var(--adm-text-dark); margin-bottom: 2px; }
.kv-step-desc  { font-size: 11.5px; color: var(--adm-text-mid); line-height: 1.5; }

.kv-tip-box {
    display: flex; gap: 8px; align-items: flex-start;
    background: #F5F3FF; border: 1px solid #DDD6FE;
    border-radius: 8px; padding: 10px 14px;
    font-size: 12px; color: #4C1D95; line-height: 1.5;
}
</style>

<script>
// ── KTP ──
const ktpInput   = document.getElementById('ktpInput');
const dropKtp    = document.getElementById('dropZoneKtp');
const dropCtKtp  = document.getElementById('dropContentKtp');
const prevKtpCon = document.getElementById('previewKtpContainer');
const prevKtpImg = document.getElementById('previewKtpImg');
const removeKtp  = document.getElementById('removeKtpBtn');

ktpInput.addEventListener('change', () => { if (ktpInput.files[0]) loadKtpPreview(ktpInput.files[0]); });
removeKtp.addEventListener('click', e => {
    e.stopPropagation();
    ktpInput.value = '';
    prevKtpCon.style.display = 'none';
    dropCtKtp.style.display  = 'flex';
    ktpOk = false; updateBtn();
});
function loadKtpPreview(file) {
    const r = new FileReader();
    r.onload = ev => { prevKtpImg.src = ev.target.result; dropCtKtp.style.display='none'; prevKtpCon.style.display='flex'; ktpOk=true; updateBtn(); };
    r.readAsDataURL(file);
    const dt = new DataTransfer(); dt.items.add(file); ktpInput.files = dt.files;
}

// ── ZIP ──
const zipInput  = document.getElementById('zipInput');
const dropZip   = document.getElementById('dropZoneZip');
const dropCtZip = document.getElementById('dropContentZip');
const zipInfo   = document.getElementById('zipFileInfo');
const zipName   = document.getElementById('zipFileName');
const zipSize   = document.getElementById('zipFileSize');
const removeZip = document.getElementById('removeZipBtn');

zipInput.addEventListener('change', () => { if (zipInput.files[0]) loadZipInfo(zipInput.files[0]); });
removeZip.addEventListener('click', e => {
    e.stopPropagation();
    zipInput.value = '';
    zipInfo.style.display   = 'none';
    dropCtZip.style.display = 'flex';
    zipOk = false; updateBtn();
});
function handleZipDrop(e) {
    e.preventDefault();
    dropZip.classList.remove('dragover');
    const f = e.dataTransfer.files[0];
    if (f && f.name.toLowerCase().endsWith('.zip')) loadZipInfo(f);
}
function loadZipInfo(file) {
    zipName.textContent = file.name;
    zipSize.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
    dropCtZip.style.display = 'none';
    zipInfo.style.display   = 'block';
    const dt = new DataTransfer(); dt.items.add(file); zipInput.files = dt.files;
    zipOk = true; updateBtn();
}

// ── SUBMIT STATE ──
let ktpOk = false, zipOk = false;
const submitBtn  = document.getElementById('submitBtn');
const submitText = document.getElementById('submitText');
function updateBtn() {
    submitBtn.disabled = !(ktpOk && zipOk);
    if (!ktpOk && !zipOk)  submitText.textContent = 'Pilih foto KTP dan file ZIP terlebih dahulu';
    else if (!ktpOk)        submitText.textContent = 'Pilih foto KTP terlebih dahulu';
    else if (!zipOk)        submitText.textContent = 'Pilih file ZIP terlebih dahulu';
    else                    submitText.textContent = 'Mulai Verifikasi';
}

document.getElementById('ktpForm').addEventListener('submit', function () {
    submitBtn.disabled = true;
    document.getElementById('submitIcon').style.display  = 'none';
    document.getElementById('loadingIcon').style.display = 'inline';
    submitText.textContent = 'Mengekstrak ZIP dan memproses... harap tunggu';
});
</script>
@endsection
