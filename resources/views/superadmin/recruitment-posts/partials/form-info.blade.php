<div class="adm-field">
    <label class="adm-label">Nama Lowongan <span class="req">*</span></label>
    <input type="text" name="nama_loker" class="adm-input" required
        value="{{ old('nama_loker', $post->nama_loker ?? '') }}" placeholder="contoh: Pendamping Halal Batch 5">
</div>
<div class="adm-field">
    <label class="adm-label">Posisi <span class="req">*</span></label>
    <select name="posisi" class="adm-field-select" required id="selectPosisi">
        <option value="">-- Pilih Posisi --</option>
        <option value="PENDAMPING" {{ old('posisi', $post->posisi ?? '') == 'PENDAMPING' ? 'selected' : '' }}>Pendamping
            (Enumerator)</option>
        <option value="DATA ENTRY" {{ old('posisi', $post->posisi ?? '') == 'DATA ENTRY' ? 'selected' : '' }}>Data Entry
        </option>
        <option value="ADMIN UMUM" {{ old('posisi', $post->posisi ?? '') == 'ADMIN UMUM' ? 'selected' : '' }}>Admin Umum
        </option>
    </select>
    <span class="adm-hint" id="pendampingNote" style="display:none;color:var(--adm-blue);">
        ⚠ Posisi Pendamping: Jika diterima, superadmin akan memilih koordinator untuk pelamar.
    </span>
</div>
<div class="adm-field"
    style="display:flex;align-items:center;gap:12px;padding:12px;background:var(--adm-bg-muted);border-radius:8px;">
    <label class="adm-label" style="margin:0;cursor:pointer;" for="switchActive">Status Lowongan</label>
    <div style="flex-grow:1;"></div>
    <label class="adm-toggle" for="switchActive">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" id="switchActive" value="1"
            {{ old('is_active', $post->is_active ?? false) ? 'checked' : '' }}>
        <span class="adm-toggle-slider"></span>
    </label>
    <span id="activeLabel" style="font-size:12px;font-weight:600;color:var(--adm-green);">
        {{ old('is_active', $post->is_active ?? false) ? 'Aktif' : 'Nonaktif' }}
    </span>
</div>
<div class="adm-field">
    <label class="adm-label">Tanggal Buka</label>
    <input type="datetime-local" name="tanggal_buka" class="adm-input"
        value="{{ old('tanggal_buka', isset($post) && $post->tanggal_buka ? $post->tanggal_buka->format('Y-m-d\TH:i') : '') }}">
    <span class="adm-hint">Kosongkan jika tidak ada batas tanggal buka.</span>
</div>
<div class="adm-field">
    <label class="adm-label">Tanggal Tutup</label>
    <input type="datetime-local" name="tanggal_tutup" class="adm-input"
        value="{{ old('tanggal_tutup', isset($post) && $post->tanggal_tutup ? $post->tanggal_tutup->format('Y-m-d\TH:i') : '') }}">
    <span class="adm-hint">Kosongkan jika tidak ada batas tanggal tutup.</span>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const posisiSelect = document.getElementById('selectPosisi');
            const note = document.getElementById('pendampingNote');

            function checkPosisi() {
                note.style.display = posisiSelect.value === 'PENDAMPING' ? 'block' : 'none';
            }
            posisiSelect.addEventListener('change', checkPosisi);
            checkPosisi();

            // Toggle label aktif/nonaktif
            const switchActive = document.getElementById('switchActive');
            const activeLabel = document.getElementById('activeLabel');
            if (switchActive) {
                switchActive.addEventListener('change', function() {
                    activeLabel.textContent = this.checked ? 'Aktif' : 'Nonaktif';
                    activeLabel.style.color = this.checked ? 'var(--adm-green)' : 'var(--adm-text-muted)';
                });
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        .adm-toggle {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }

        .adm-toggle input[type="checkbox"] {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .adm-toggle input[type="hidden"] {
            display: none;
        }

        .adm-toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: var(--adm-border-mid);
            border-radius: 24px;
            transition: 0.3s;
        }

        .adm-toggle-slider:before {
            content: "";
            position: absolute;
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            border-radius: 50%;
            transition: 0.3s;
        }

        .adm-toggle input[type="checkbox"]:checked+.adm-toggle-slider {
            background-color: var(--adm-green, #2f9e44);
        }

        .adm-toggle input[type="checkbox"]:checked+.adm-toggle-slider:before {
            transform: translateX(20px);
        }
    </style>
@endpush
