@extends('layouts.app')

@section('template_title')
    Penarikan Saldo Data Entry
@endsection

@section('content')
<div class="adm-page">
    @include('layouts.messages')

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Penarikan Saldo Data Entry</h1>
            <p>Kelola request penarikan saldo dari data entry — setujui atau tolak pengajuan</p>
        </div>
    </div>

    {{-- ── STAT CARDS ── --}}
    <div class="adm-stats">

        <div class="adm-stat">
            <div class="adm-stat-label">Menunggu</div>
            <div class="adm-stat-value is-warn counter-value" data-target="{{ $totalMenunggu }}">0</div>
            <div class="adm-stat-sub">Perlu ditinjau</div>
        </div>

        <div class="adm-stat">
            <div class="adm-stat-label">Diproses</div>
            <div class="adm-stat-value counter-value" style="color:var(--adm-blue)" data-target="{{ $totalDiproses }}">0</div>
            <div class="adm-stat-sub">Sedang diproses</div>
        </div>

        <div class="adm-stat">
            <div class="adm-stat-label">Ditolak</div>
            <div class="adm-stat-value is-danger counter-value" data-target="{{ $totalDitolak }}">0</div>
            <div class="adm-stat-sub">Pengajuan ditolak</div>
        </div>

        <div class="adm-stat is-accent">
            <div class="adm-stat-label">Total Disetujui</div>
            <div class="adm-stat-value counter-value" data-target="{{ $totalDisetujui }}" data-prefix="Rp ">Rp 0</div>
            <div class="adm-stat-sub">Sudah dicairkan</div>
        </div>

    </div>

    {{-- ── TABEL PENARIKAN ── --}}
    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
                Daftar Pengajuan Penarikan Saldo
            </div>
        </div>

        <div class="table-responsive">
            <table id="penarikanTable" class="adm-table w-100">
                <thead>
                    <tr>
                        <th style="width:44px">#</th>
                        <th>Data Entry</th>
                        <th>Tgl Pengajuan</th>
                        <th class="tc">Tagihan Dicakup</th>
                        <th class="tr">Nominal</th>
                        <th class="tc">Status</th>
                        <th>Catatan DE</th>
                        <th class="tc" style="width:160px">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div>

{{-- ══ MODAL SETUJUI (shared, diisi via JS) ══ --}}
<div class="modal fade adm-modal" id="modalSetujui" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formSetujui" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <svg viewBox="0 0 24 24" style="width:18px;height:18px;stroke:#fff;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;margin-right:6px;vertical-align:-3px;">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        Setujui Penarikan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:20px 24px;">
                    <div class="adm-alert adm-alert-success" style="margin-bottom:16px;">
                        <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <div>
                            <p style="margin:0;font-size:13px;"><strong>Data Entry:</strong> <span id="setujuiNama"></span></p>
                            <p style="margin:4px 0 0;font-size:13px;"><strong>Nominal:</strong> Rp <span id="setujuiNominal"></span></p>
                            <p style="margin:4px 0 0;font-size:13px;"><strong>Tagihan:</strong> <span id="setujuiJumlahTagihan"></span> tagihan akan ditandai Dibayar</p>
                        </div>
                    </div>
                    <div class="adm-field">
                        <label class="adm-label" for="catatan_setujui">
                            Catatan <span style="font-weight:400;color:var(--adm-text-muted);">(opsional)</span>
                        </label>
                        <textarea name="catatan_admin" id="catatan_setujui"
                            class="adm-textarea" rows="3"
                            placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="adm-btn-primary" id="btnSubmitSetujui"
                        style="background:linear-gradient(135deg,var(--adm-green),#15803d);">
                        <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        Setujui & Bayar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══ MODAL TOLAK (shared, diisi via JS) ══ --}}
<div class="modal fade adm-modal" id="modalTolak" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formTolak" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <svg viewBox="0 0 24 24" style="width:18px;height:18px;stroke:#fff;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;margin-right:6px;vertical-align:-3px;">
                            <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                        </svg>
                        Tolak Penarikan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:20px 24px;">
                    <div class="adm-alert adm-alert-danger" style="margin-bottom:16px;">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        <div>
                            <p style="margin:0;font-size:13px;"><strong>Data Entry:</strong> <span id="tolakNama"></span></p>
                            <p style="margin:4px 0 0;font-size:13px;"><strong>Nominal:</strong> Rp <span id="tolakNominal"></span></p>
                        </div>
                    </div>
                    <div class="adm-field">
                        <label class="adm-label" for="catatan_tolak">
                            Alasan Penolakan <span class="req">*</span>
                        </label>
                        <textarea name="catatan_admin" id="catatan_tolak"
                            class="adm-textarea" rows="3"
                            placeholder="Masukkan alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="adm-btn-primary" id="btnSubmitTolak"
                        style="background:linear-gradient(135deg,var(--adm-red),#b91c1c);">
                        <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        Tolak Penarikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.counter-value').forEach(function (el) {
        const target   = parseFloat(el.dataset.target) || 0;
        const prefix   = el.dataset.prefix || '';
        const duration = 900;
        const steps    = Math.ceil(duration / 16);
        const inc      = target / steps;
        let current    = 0;
        const timer = setInterval(function () {
            current = Math.min(current + inc, target);
            el.textContent = prefix + Math.round(current).toLocaleString('id-ID');
            if (current >= target) clearInterval(timer);
        }, 16);
    });

    window.dataTableInstance = $('#penarikanTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('superadmin.penarikan-saldo.data') }}',
            type: 'GET',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'tc' },
            { data: 'dataentry_cell', name: 'dataEntry.nama_lengkap' },
            { data: 'tanggal_fmt', name: 'tanggal_pengajuan', className: 'adm-mono' },
            { data: 'tagihan_badge', name: 'tagihan_badge', className: 'tc', orderable: false },
            { data: 'nominal_fmt', name: 'nominal', className: 'tr adm-mono' },
            { data: 'status_badge', name: 'status', className: 'tc' },
            { data: 'catatan_de_cell', name: 'catatan_de' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'tc' },
        ],
        language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ – _END_ dari _TOTAL_ pengajuan',
            infoEmpty: 'Tidak ada data',
            paginate: { previous: '‹', next: '›' },
            zeroRecords: 'Tidak ada pengajuan ditemukan',
            emptyTable: 'Belum ada pengajuan penarikan saldo.',
            processing: '<div class="spinner-border text-primary" role="status"></div>',
        },
        pageLength: 15,
        order: [[2, 'desc']],
        responsive: true,
    });
});

function bukaModalSetujui(id, nama, nominal, jumlahTagihan) {
    document.getElementById('setujuiNama').textContent = nama;
    document.getElementById('setujuiNominal').textContent = nominal;
    document.getElementById('setujuiJumlahTagihan').textContent = jumlahTagihan;
    document.getElementById('catatan_setujui').value = '';
    document.getElementById('formSetujui').dataset.id = id;
    new bootstrap.Modal(document.getElementById('modalSetujui')).show();
}

function bukaModalTolak(id, nama, nominal) {
    document.getElementById('tolakNama').textContent = nama;
    document.getElementById('tolakNominal').textContent = nominal;
    document.getElementById('catatan_tolak').value = '';
    document.getElementById('formTolak').dataset.id = id;
    new bootstrap.Modal(document.getElementById('modalTolak')).show();
}

async function submitPenarikanAction(form, btn, url) {
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Memproses...`;

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(Object.fromEntries(new FormData(form))),
        });

        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Terjadi kesalahan');

        bootstrap.Modal.getInstance(form.closest('.modal')).hide();

        Swal.fire({
            toast: true, position: 'top-end', icon: 'success',
            title: data.message || 'Berhasil!',
            showConfirmButton: false, timer: 3500, timerProgressBar: true,
        });

        window.dataTableInstance.ajax.reload(null, false);
    } catch (err) {
        Swal.fire({
            toast: true, position: 'top-end', icon: 'error',
            title: err.message || 'Gagal memproses permintaan',
            showConfirmButton: false, timer: 3500,
        });
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    }
}

document.getElementById('formSetujui').addEventListener('submit', function (e) {
    e.preventDefault();
    const id = this.dataset.id;
    submitPenarikanAction(this, document.getElementById('btnSubmitSetujui'),
        `{{ url('superadmin/penarikan-saldo') }}/${id}/setujui`);
});

document.getElementById('formTolak').addEventListener('submit', function (e) {
    e.preventDefault();
    const id = this.dataset.id;
    submitPenarikanAction(this, document.getElementById('btnSubmitTolak'),
        `{{ url('superadmin/penarikan-saldo') }}/${id}/tolak`);
});
</script>
@endpush
