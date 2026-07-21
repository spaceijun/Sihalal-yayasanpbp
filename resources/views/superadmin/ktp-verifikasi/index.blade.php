@extends('layouts.app')
@section('template_title')
    Verifikasi KTP — Pencocokan Foto
@endsection

@section('content')
<div class="adm-page">

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Verifikasi KTP</h1>
            <p>Upload 1–5 foto KTP + file ZIP berisi foto pendamping — AI akan mencocokkan setiap KTP dengan 3 foto paling mirip</p>
        </div>
        <a href="{{ url($routePrefix . '/data-lapangans') }}" class="adm-btn-secondary">
            <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2;"><polyline points="15 18 9 12 15 6"/></svg>
            Kembali
        </a>
    </div>

    @if (session('error'))
        <div class="adm-alert adm-alert-danger" style="margin-bottom:16px;">
            <svg viewBox="0 0 24 24" style="width:18px;height:18px;flex-shrink:0;stroke:currentColor;fill:none;stroke-width:2;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="adm-alert adm-alert-danger" style="margin-bottom:16px;">
            <svg viewBox="0 0 24 24" style="width:18px;height:18px;flex-shrink:0;stroke:currentColor;fill:none;stroke-width:2;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <ul style="margin:0;padding-left:14px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div style="display:grid;grid-template-columns:1fr 380px;gap:16px;align-items:start;">

        {{-- ── FORM ── --}}
        <form action="{{ route($routePrefix . '.ktp-verifikasi.verify') }}" method="POST"
              enctype="multipart/form-data" id="ktpForm">
            @csrf

            {{-- KTP Multi-upload --}}
            <div class="adm-card" style="margin-bottom:14px;">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24" style="stroke:currentColor;fill:none;stroke-width:2;"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        Foto KTP <span style="font-size:11px;color:var(--adm-text-faint);font-weight:400;">(Maks 5 KTP, JPG/PNG, masing-masing ≤5MB)</span>
                    </div>
                    <button type="button" id="addKtpBtn" onclick="addKtpSlot()"
                            style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--adm-blue);
                                   background:var(--adm-blue-lt);border:1px solid var(--adm-blue)33;
                                   border-radius:8px;padding:5px 12px;cursor:pointer;font-weight:600;">
                        <svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2.5;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Tambah KTP
                    </button>
                </div>
                <div style="padding:16px 20px;">
                    <div id="ktpSlotContainer" style="display:flex;flex-direction:column;gap:10px;"></div>
                    <div id="ktpCountInfo" style="font-size:11.5px;color:var(--adm-text-faint);text-align:center;margin-top:10px;">
                        <span id="ktpCountText">0</span> / 5 KTP ditambahkan
                    </div>
                </div>
            </div>

            {{-- ZIP Upload --}}
            <div class="adm-card" style="margin-bottom:14px;">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24" style="stroke:currentColor;fill:none;stroke-width:2;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        File ZIP Foto Pendamping <span style="font-size:11px;color:var(--adm-text-faint);font-weight:400;">(Maks 100MB)</span>
                    </div>
                </div>
                <div style="padding:12px 20px 16px;">
                    <div class="kv-drop-zone" id="dropZoneZip"
                         onclick="document.getElementById('zipInput').click()"
                         ondragover="event.preventDefault();this.classList.add('dragover')"
                         ondragleave="this.classList.remove('dragover')"
                         ondrop="handleZipDrop(event)">
                        <input type="file" name="zip_fotos" id="zipInput" accept=".zip" style="display:none;">

                        <div id="dropCtZip" style="display:flex;flex-direction:column;align-items:center;gap:4px;">
                            <svg viewBox="0 0 24 24" style="width:32px;height:32px;color:#7C3AED;stroke:currentColor;fill:none;stroke-width:1.5;"><path d="M13 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
                            <p style="color:var(--adm-text-mid);margin:4px 0 0;font-size:13px;font-weight:600;">Klik atau seret file ZIP ke sini</p>
                            <p style="color:var(--adm-text-faint);margin:0;font-size:11.5px;">Format ZIP · Maks 100MB · Berisi foto JPG/PNG</p>
                        </div>

                        <div id="zipInfo" style="display:none;text-align:center;">
                            <svg viewBox="0 0 24 24" style="width:28px;height:28px;color:#7C3AED;stroke:currentColor;fill:none;stroke-width:1.5;margin-bottom:4px;"><path d="M13 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
                            <div id="zipFileName" style="font-size:13px;font-weight:700;color:var(--adm-text-dark);"></div>
                            <div id="zipFileSize" style="font-size:11px;color:var(--adm-text-faint);margin:2px 0 8px;"></div>
                            <button type="button" onclick="clearZip(event)"
                                    style="font-size:11.5px;color:#7C3AED;background:#F5F3FF;border:1px solid #DDD6FE;
                                           border-radius:6px;padding:4px 10px;cursor:pointer;">
                                ✕ Ganti ZIP
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit" id="submitBtn" disabled
                    class="adm-btn-primary"
                    style="width:100%;justify-content:center;gap:8px;padding:12px;">
                <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2;" id="submitIcon">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2;display:none;animation:kv-spin 1s linear infinite;" id="loadingIcon">
                    <path d="M21 12a9 9 0 11-6.219-8.56"/>
                </svg>
                <span id="submitText">Tambah KTP dan pilih file ZIP terlebih dahulu</span>
            </button>
            <p style="font-size:11px;color:var(--adm-text-faint);text-align:center;margin-top:8px;">
                ⚠️ Setiap KTP × setiap foto ZIP = 1 proses AI — estimasi: 5 KTP × 90 foto ≈ 6–12 menit
            </p>
        </form>

        {{-- ── INFO PANEL ── --}}
        <div style="display:flex;flex-direction:column;gap:14px;">

            {{-- Cara kerja --}}
            <div class="adm-card">
                <div class="adm-card-header">
                    <div class="adm-card-title" style="font-size:13px;">
                        <svg viewBox="0 0 24 24" style="stroke:currentColor;fill:none;stroke-width:2;"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                        Cara Kerja
                    </div>
                </div>
                <div style="padding:14px 18px 18px;">
                    <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:14px;">
                        @foreach([
                            ['Upload 1–5 KTP', 'Setiap KTP adalah identitas yang akan dicari kecocokannya secara independen'],
                            ['Upload ZIP Foto', 'ZIP berisi kumpulan foto pendamping yang akan dibandingkan dengan semua KTP'],
                            ['AI Analisis (Background)', 'Setiap pasang KTP×Foto dikirim ke Gemini 3.5 Flash untuk analisis biometrik forensik 6 dimensi'],
                            ['Top 3 per KTP', 'Setiap KTP mendapatkan 3 foto dengan skor kemiripan tertinggi + download ZIP hasil'],
                        ] as $i => $step)
                        <div style="display:flex;gap:10px;align-items:flex-start;">
                            <div style="width:22px;height:22px;border-radius:50%;flex-shrink:0;
                                        background:linear-gradient(135deg,var(--adm-blue),#1d4ed8);
                                        color:#fff;display:flex;align-items:center;justify-content:center;
                                        font-size:11px;font-weight:700;box-shadow:0 2px 6px rgba(37,99,235,.3);">
                                {{ $i + 1 }}
                            </div>
                            <div>
                                <div style="font-size:12.5px;font-weight:700;color:var(--adm-text-dark);margin-bottom:2px;">{{ $step[0] }}</div>
                                <div style="font-size:11.5px;color:var(--adm-text-mid);line-height:1.5;">{{ $step[1] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div style="background:#F5F3FF;border:1px solid #DDD6FE;border-radius:8px;padding:10px 12px;
                                display:flex;gap:8px;font-size:11.5px;color:#5B21B6;line-height:1.5;">
                        <svg viewBox="0 0 24 24" style="width:15px;height:15px;flex-shrink:0;stroke:currentColor;fill:none;stroke-width:2;"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                        <span><strong>Download ZIP</strong> tersedia setelah verifikasi selesai — berisi foto KTP referensi + top 3 kandidat tiap KTP dalam folder terpisah.</span>
                    </div>
                </div>
            </div>

            {{-- Status API --}}
            <div class="adm-card">
                <div class="adm-card-header">
                    <div class="adm-card-title" style="font-size:13px;">
                        <svg viewBox="0 0 24 24" style="stroke:currentColor;fill:none;stroke-width:2;"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 11-7.778 7.778 5.5 5.5 0 017.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
                        Status API Gemini
                    </div>
                </div>
                <div style="padding:12px 16px;">
                    @if (!empty($geminiApiKey))
                        <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:#ECFDF5;border:1px solid #6EE7B7;border-radius:8px;">
                            <svg viewBox="0 0 24 24" style="width:18px;height:18px;color:#059669;flex-shrink:0;stroke:currentColor;fill:none;stroke-width:2;"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            <div>
                                <div style="font-size:13px;font-weight:700;color:#065F46;">API Key Aktif · gemini-3.5-flash</div>
                                <div style="font-size:11px;color:#6B7280;font-family:monospace;">{{ substr($geminiApiKey,0,6) }}••••{{ substr($geminiApiKey,-4) }}</div>
                            </div>
                        </div>
                    @else
                        <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:#FEF2F2;border:1px solid #FECACA;border-radius:8px;">
                            <svg viewBox="0 0 24 24" style="width:18px;height:18px;color:#DC2626;flex-shrink:0;stroke:currentColor;fill:none;stroke-width:2;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            <div style="font-size:13px;font-weight:700;color:#7F1D1D;">API Key Belum Dikonfigurasi</div>
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
    border-radius: 10px;
    padding: 20px 16px;
    text-align: center;
    cursor: pointer;
    transition: border-color .2s, background .2s;
    background: var(--adm-bg-faint);
}
.kv-drop-zone:hover, .kv-drop-zone.dragover {
    border-color: #7C3AED;
    background: #F5F3FF;
}

.kv-ktp-slot {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    background: var(--adm-bg-faint);
    border: 1.5px solid var(--adm-border-light);
    border-radius: 10px;
    transition: border-color .2s;
}
.kv-ktp-slot.has-file {
    border-color: var(--adm-blue);
    background: var(--adm-blue-lt);
}
.kv-ktp-slot .kv-ktp-preview {
    width: 48px;
    height: 48px;
    object-fit: cover;
    border-radius: 6px;
    flex-shrink: 0;
    border: 1.5px solid var(--adm-blue)44;
}
.kv-ktp-slot .kv-ktp-placeholder {
    width: 48px;
    height: 48px;
    border-radius: 6px;
    background: var(--adm-border-light);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
</style>

<script>
let ktpCount   = 0;
let zipOk      = false;
const MAX_KTP  = 5;

// ── KTP SLOT MANAGEMENT ─────────────────────────────────────────────────────
function addKtpSlot() {
    if (ktpCount >= MAX_KTP) return;

    const idx = ktpCount++;
    const slot = document.createElement('div');
    slot.className = 'kv-ktp-slot';
    slot.id = `ktpSlot_${idx}`;
    slot.innerHTML = `
        <div class="kv-ktp-placeholder" id="ktpPlaceholder_${idx}">
            <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:#94A3B8;fill:none;stroke-width:1.5;">
                <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
            </svg>
        </div>
        <img id="ktpPreviewImg_${idx}" class="kv-ktp-preview" src="" alt="" style="display:none;">
        <div style="flex:1;min-width:0;">
            <div style="font-size:12px;font-weight:700;color:var(--adm-text-dark);margin-bottom:2px;">KTP ${idx + 1}</div>
            <div id="ktpSlotLabel_${idx}" style="font-size:11.5px;color:var(--adm-text-faint);">Belum dipilih</div>
        </div>
        <input type="file" name="foto_ktp[]" id="ktpInput_${idx}" accept="image/jpeg,image/png"
               style="display:none;" onchange="onKtpChange(${idx})">
        <button type="button" onclick="document.getElementById('ktpInput_${idx}').click()"
                style="font-size:11.5px;color:var(--adm-blue);background:var(--adm-blue-lt);
                       border:1px solid var(--adm-blue)33;border-radius:6px;padding:5px 10px;cursor:pointer;white-space:nowrap;">
            Pilih Foto
        </button>
        <button type="button" onclick="removeKtpSlot(${idx})"
                style="font-size:11px;color:#DC2626;background:#FEF2F2;border:1px solid #FECACA;
                       border-radius:6px;padding:5px 8px;cursor:pointer;flex-shrink:0;">
            ✕
        </button>
    `;
    document.getElementById('ktpSlotContainer').appendChild(slot);
    updateKtpCount();
}

function removeKtpSlot(idx) {
    const slot = document.getElementById(`ktpSlot_${idx}`);
    if (slot) slot.remove();
    ktpCount--;
    updateKtpCount();
    checkSubmit();
}

function onKtpChange(idx) {
    const input     = document.getElementById(`ktpInput_${idx}`);
    const slot      = document.getElementById(`ktpSlot_${idx}`);
    const placeholder = document.getElementById(`ktpPlaceholder_${idx}`);
    const preview   = document.getElementById(`ktpPreviewImg_${idx}`);
    const label     = document.getElementById(`ktpSlotLabel_${idx}`);

    if (!input.files[0]) return;

    const file = input.files[0];
    label.textContent = file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
    label.style.color = 'var(--adm-blue)';
    slot.classList.add('has-file');

    const reader = new FileReader();
    reader.onload = e => {
        preview.src = e.target.result;
        preview.style.display = 'block';
        placeholder.style.display = 'none';
    };
    reader.readAsDataURL(file);

    checkSubmit();
}

function updateKtpCount() {
    document.getElementById('ktpCountText').textContent = ktpCount;
    document.getElementById('addKtpBtn').disabled = ktpCount >= MAX_KTP;
    document.getElementById('addKtpBtn').style.opacity = ktpCount >= MAX_KTP ? '.5' : '1';
}

// ── ZIP ──────────────────────────────────────────────────────────────────────
const zipInput = document.getElementById('zipInput');
zipInput.addEventListener('change', () => { if (zipInput.files[0]) loadZipInfo(zipInput.files[0]); });

function handleZipDrop(e) {
    e.preventDefault();
    document.getElementById('dropZoneZip').classList.remove('dragover');
    const f = e.dataTransfer.files[0];
    if (f && f.name.toLowerCase().endsWith('.zip')) {
        const dt = new DataTransfer(); dt.items.add(f); zipInput.files = dt.files;
        loadZipInfo(f);
    }
}

function loadZipInfo(file) {
    document.getElementById('zipFileName').textContent = file.name;
    document.getElementById('zipFileSize').textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
    document.getElementById('dropCtZip').style.display = 'none';
    document.getElementById('zipInfo').style.display   = 'block';
    zipOk = true;
    checkSubmit();
}

function clearZip(e) {
    e.stopPropagation();
    zipInput.value = '';
    document.getElementById('dropCtZip').style.display = 'flex';
    document.getElementById('zipInfo').style.display   = 'none';
    zipOk = false;
    checkSubmit();
}

// ── SUBMIT STATE ─────────────────────────────────────────────────────────────
function checkSubmit() {
    const filledKtp = document.querySelectorAll('.kv-ktp-slot.has-file').length;
    const ready     = filledKtp > 0 && zipOk;

    document.getElementById('submitBtn').disabled = !ready;
    if (!ready) {
        if (filledKtp === 0 && !zipOk) document.getElementById('submitText').textContent = 'Tambah KTP dan pilih file ZIP terlebih dahulu';
        else if (filledKtp === 0)      document.getElementById('submitText').textContent = 'Tambah dan pilih foto KTP';
        else                           document.getElementById('submitText').textContent = 'Pilih file ZIP terlebih dahulu';
    } else {
        document.getElementById('submitText').textContent = `Mulai Verifikasi — ${filledKtp} KTP × Foto dalam ZIP`;
    }
}

document.getElementById('ktpForm').addEventListener('submit', function () {
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitIcon').style.display  = 'none';
    document.getElementById('loadingIcon').style.display = 'inline';
    document.getElementById('submitText').textContent = 'Mengekstrak ZIP dan memproses... harap tunggu';
});

// Tambah 1 slot awal saat load
addKtpSlot();
</script>
@endsection
