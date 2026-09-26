@extends('layouts.app')
@section('template_title') Data Lapangan @endsection

@section('content')
<div class="adm-page">
    @include('layouts.messages')

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Data Lapangan</h1>
            <p>Verifikasi data lapangan dari enumerator di bawah koordinasi Anda</p>
        </div>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Daftar Data Lapangan
            </div>
        </div>

        <div class="adm-filter-bar">
            <div class="adm-filter-group">
                <span class="adm-filter-label">Verifikasi</span>
                <select id="filterVerifikasi" class="adm-select" style="min-width:170px;">
                    <option value="">Semua Status</option>
                    <option value="Belum">Belum Diverifikasi</option>
                    <option value="Terverifikasi">Terverifikasi</option>
                    <option value="Perlu Koreksi">Perlu Koreksi</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table id="dataLapanganTable" class="adm-table w-100">
                <thead>
                    <tr>
                        <th style="width:44px">#</th>
                        <th>No. Reg</th>
                        <th>Nama PU</th>
                        <th>Enumerator</th>
                        <th class="tc">Status</th>
                        <th class="tc" style="width:56px">Foto</th>
                        <th class="tc">Verifikasi</th>
                        <th>Tanggal</th>
                        <th class="tc" style="width:90px">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

{{-- ══ MODAL VERIFIKASI (shared, diisi via JS) ══ --}}
<div class="modal fade adm-modal" id="modalVerifikasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    Verifikasi Data Lapangan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="adm-info-list" style="border:none;margin-bottom:16px;">
                    <div class="adm-info-row">
                        <span class="adm-info-key">No. Registrasi</span>
                        <span class="adm-info-val" id="vNoReg"></span>
                    </div>
                    <div class="adm-info-row">
                        <span class="adm-info-key">Nama PU</span>
                        <span class="adm-info-val" id="vNamaPu"></span>
                    </div>
                </div>

                <div class="adm-field">
                    <label class="adm-label">Keputusan <span class="req">*</span></label>
                    <select id="vKeputusan" class="adm-field-select">
                        <option value="Terverifikasi">Terverifikasi — Data Valid</option>
                        <option value="Perlu Koreksi">Perlu Koreksi — Ada Masalah</option>
                    </select>
                </div>
                <div class="adm-field" style="margin-top:12px;">
                    <label class="adm-label">Catatan <span style="font-weight:400;color:var(--adm-text-muted);">(opsional)</span></label>
                    <textarea id="vCatatan" class="adm-textarea" rows="3" placeholder="Catatan untuk enumerator..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="adm-btn-primary" id="btnSimpanVerifikasi">
                    <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Simpan Verifikasi
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        window.dataTableInstance = $('#dataLapanganTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('koordinator.data-lapangan.data') }}',
                type: 'GET',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: function (d) { d.verifikasi = $('#filterVerifikasi').val(); }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'tc' },
                { data: 'no_reg_cell', name: 'no_registrasi' },
                { data: 'nama_pu', name: 'nama_pu' },
                { data: 'enumerator_nama', name: 'enumerator.nama_lengkap' },
                { data: 'status_badge', name: 'status', className: 'tc' },
                { data: 'foto_badge', name: 'foto_badge', orderable: false, searchable: false, className: 'tc' },
                { data: 'verifikasi_badge', name: 'verifikasi_koordinator', className: 'tc' },
                { data: 'tanggal_fmt', name: 'created_at' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'tc' },
            ],
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ – _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                paginate: { previous: '‹', next: '›' },
                zeroRecords: 'Tidak ada data lapangan ditemukan',
                emptyTable: 'Belum ada data lapangan',
                processing: '<div class="spinner-border text-primary" role="status"></div>',
            },
            pageLength: 15,
            order: [[6, 'desc']],
            responsive: true,
        });

        $('#filterVerifikasi').on('change', () => window.dataTableInstance.ajax.reload());
    });

    let currentVerifikasiHashedId = null;

    function bukaModalVerifikasi(hashedId, noReg, namaPu) {
        currentVerifikasiHashedId = hashedId;
        document.getElementById('vNoReg').textContent = noReg;
        document.getElementById('vNamaPu').textContent = namaPu;
        document.getElementById('vKeputusan').value = 'Terverifikasi';
        document.getElementById('vCatatan').value = '';
        new bootstrap.Modal(document.getElementById('modalVerifikasi')).show();
    }

    document.getElementById('btnSimpanVerifikasi').addEventListener('click', async function () {
        if (!currentVerifikasiHashedId) return;
        const btn = this;
        const originalHTML = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Menyimpan...`;

        try {
            const response = await fetch(`{{ url('koordinator/data-lapangan') }}/${currentVerifikasiHashedId}/verifikasi`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    verifikasi_koordinator: document.getElementById('vKeputusan').value,
                    catatan_koordinator: document.getElementById('vCatatan').value,
                }),
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Gagal menyimpan verifikasi');

            bootstrap.Modal.getInstance(document.getElementById('modalVerifikasi')).hide();
            Swal.fire({
                toast: true, position: 'top-end', icon: 'success',
                title: data.message || 'Verifikasi berhasil disimpan!',
                showConfirmButton: false, timer: 2500, timerProgressBar: true,
            });
            window.dataTableInstance.ajax.reload(null, false);
        } catch (err) {
            Swal.fire({
                toast: true, position: 'top-end', icon: 'error',
                title: err.message || 'Gagal menyimpan verifikasi',
                showConfirmButton: false, timer: 3000,
            });
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    });
</script>
@endpush
