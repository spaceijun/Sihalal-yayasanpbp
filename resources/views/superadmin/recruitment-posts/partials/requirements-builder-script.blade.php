@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            'use strict';

            // Existing requirements dari PHP (untuk mode edit)
            const existingRequirements = @json($post->requirements ?? []);
            let fieldIndex = 0;

            const container = document.getElementById('requirementsContainer');
            const emptyState = document.getElementById('emptyState');

            function updateEmptyState() {
                const rows = container.querySelectorAll('.req-row');
                emptyState.style.display = rows.length === 0 ? 'block' : 'none';
            }

            function getTypeLabel(type) {
                const labels = {
                    'text': 'Teks Singkat',
                    'textarea': 'Teks Panjang',
                    'file': 'Upload File',
                    'checkbox': 'Checkbox (Ya/Tidak)',
                    'select': 'Dropdown Pilihan',
                    'radio': 'Pilihan Ganda (Radio)',
                };
                return labels[type] || type;
            }

            function buildRow(req) {
                const idx = fieldIndex++;
                const row = document.createElement('div');
                row.className = 'req-row';
                row.dataset.idx = idx;
                row.style.cssText =
                    'background:var(--adm-bg-muted);border-radius:10px;padding:14px 16px;margin-bottom:10px;border:1px solid var(--adm-border);';

                const hasOptions = ['select', 'radio'].includes(req.type || '');
                const hasAccept = req.type === 'file';

                row.innerHTML = `
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
            <div style="font-size:12px;font-weight:700;color:var(--adm-blue);min-width:20px;">${idx + 1}</div>
            <div style="flex-grow:1;display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:8px;align-items:center;">
                <div>
                    <label style="font-size:11px;font-weight:600;color:var(--adm-text-muted);display:block;margin-bottom:3px;">Label Field</label>
                    <input type="text" name="requirements[${idx}][label]"
                        class="adm-input" style="height:32px;font-size:12.5px;"
                        placeholder="contoh: Nama Lengkap"
                        value="${escHtml(req.label || '')}" required>
                </div>
                <div>
                    <label style="font-size:11px;font-weight:600;color:var(--adm-text-muted);display:block;margin-bottom:3px;">Tipe Input</label>
                    <select name="requirements[${idx}][type]"
                        class="adm-field-select req-type-select" style="height:32px;font-size:12.5px;" data-idx="${idx}">
                        <option value="text"     ${(req.type||'text')==='text'     ?'selected':''}>Teks Singkat</option>
                        <option value="textarea" ${(req.type||'')==='textarea'     ?'selected':''}>Teks Panjang</option>
                        <option value="file"     ${(req.type||'')==='file'         ?'selected':''}>Upload File</option>
                        <option value="checkbox" ${(req.type||'')==='checkbox'     ?'selected':''}>Checkbox (Ya/Tidak)</option>
                        <option value="select"   ${(req.type||'')==='select'       ?'selected':''}>Dropdown</option>
                        <option value="radio"    ${(req.type||'')==='radio'        ?'selected':''}>Pilihan Ganda</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:11px;font-weight:600;color:var(--adm-text-muted);display:block;margin-bottom:3px;">Field Key</label>
                    <input type="text" name="requirements[${idx}][field_key]"
                        class="adm-input adm-mono" style="height:32px;font-size:11.5px;"
                        placeholder="auto"
                        value="${escHtml(req.field_key || '')}">
                </div>
                <div style="display:flex;align-items:flex-end;gap:6px;padding-bottom:1px;">
                    <label style="display:flex;align-items:center;gap:5px;font-size:12px;cursor:pointer;white-space:nowrap;padding-top:18px;">
                        <input type="checkbox" name="requirements[${idx}][required]" value="1"
                            ${req.required ? 'checked' : ''} style="width:14px;height:14px;">
                        Wajib
                    </label>
                    <button type="button" class="adm-btn danger icon-only btn-remove-req" data-idx="${idx}"
                        style="padding:5px;width:30px;height:30px;margin-top:18px;">
                        <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="req-extras" data-idx="${idx}">
            <div class="req-options-wrap" style="display:${hasOptions ? 'block' : 'none'};">
                <label style="font-size:11px;font-weight:600;color:var(--adm-text-muted);display:block;margin-bottom:3px;">Pilihan (satu per baris)</label>
                <textarea name="requirements[${idx}][options]"
                    class="adm-textarea" rows="3" style="font-size:12px;"
                    placeholder="Pilihan A&#10;Pilihan B&#10;Pilihan C">${escHtml((req.options || []).join('\n'))}</textarea>
            </div>
            <div class="req-accept-wrap" style="display:${hasAccept ? 'block' : 'none'};">
                <label style="font-size:11px;font-weight:600;color:var(--adm-text-muted);display:block;margin-bottom:3px;">Tipe File Diterima</label>
                <input type="text" name="requirements[${idx}][accept]"
                    class="adm-input" style="height:30px;font-size:12px;"
                    placeholder="contoh: image/*,application/pdf"
                    value="${escHtml(req.accept || 'image/*,application/pdf')}">
                <span style="font-size:11px;color:var(--adm-text-faint);">Gunakan MIME type. Contoh: image/*, application/pdf</span>
            </div>
            <div style="margin-top:8px;">
                <input type="text" name="requirements[${idx}][hint]"
                    class="adm-input" style="height:30px;font-size:12px;"
                    placeholder="Petunjuk opsional untuk pelamar..."
                    value="${escHtml(req.hint || '')}">
            </div>
        </div>`;

                // Listener tipe
                row.querySelector('.req-type-select').addEventListener('change', function() {
                    const type = this.value;
                    const extras = row.querySelector('.req-extras');
                    extras.querySelector('.req-options-wrap').style.display = ['select', 'radio'].includes(
                        type) ? 'block' : 'none';
                    extras.querySelector('.req-accept-wrap').style.display = type === 'file' ? 'block' :
                        'none';
                });

                // Hapus row
                row.querySelector('.btn-remove-req').addEventListener('click', function() {
                    row.remove();
                    updateEmptyState();
                    renumberRows();
                });

                return row;
            }

            function renumberRows() {
                container.querySelectorAll('.req-row').forEach((row, i) => {
                    const numEl = row.querySelector('[data-idx]');
                    row.querySelector('div[style*="color:var(--adm-blue)"]').textContent = i + 1;
                });
            }

            function escHtml(str) {
                return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g,
                    '&quot;');
            }

            // Load existing requirements (edit mode)
            if (existingRequirements && existingRequirements.length) {
                existingRequirements.forEach(req => {
                    container.appendChild(buildRow(req));
                });
                updateEmptyState();
            }

            // Tambah field baru
            document.getElementById('btnAddField').addEventListener('click', function() {
                container.appendChild(buildRow({}));
                updateEmptyState();
            });

            updateEmptyState();
        });
    </script>
@endpush
