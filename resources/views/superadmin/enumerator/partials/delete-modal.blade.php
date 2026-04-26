{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="modal fade adm-modal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <svg viewBox="0 0 24 24" style="width:18px;height:18px;stroke:var(--adm-red);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;margin-right:6px;vertical-align:-3px;">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                    Konfirmasi Hapus Data
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="text-align:center;padding:28px 24px 20px;">
                <div style="width:64px;height:64px;border-radius:50%;background:var(--adm-red-lt);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <svg viewBox="0 0 24 24" style="width:28px;height:28px;stroke:var(--adm-red);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;">
                        <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/>
                    </svg>
                </div>
                <h5 style="font-family:'Sora',sans-serif;font-weight:700;color:var(--adm-text-dark);margin-bottom:8px;">Yakin hapus data enumerator ini?</h5>
                <p style="font-size:13px;color:var(--adm-text-muted);margin:0;">Data yang telah dihapus tidak dapat dikembalikan lagi. Pastikan Anda benar-benar yakin sebelum melanjutkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="adm-btn-primary" id="confirmDeleteBtn"
                    style="background:linear-gradient(135deg,var(--adm-red),#b91c1c);">
                    <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>
                    Hapus Data
                </button>
            </div>
        </div>
    </div>
</div>
