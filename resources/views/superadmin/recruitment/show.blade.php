@extends('layouts.app')
@section('template_title') {{ $recruitment->nama_lengkap ?? 'Detail Recruitment' }} @endsection
@section('content')
<div class="adm-page">
    @include('layouts.messages')

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Detail Pelamar</h1>
            <p>Informasi lengkap dan manajemen status lamaran</p>
        </div>
        <a href="{{ route('superadmin.recruitments.index') }}" class="adm-btn-secondary">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg> Kembali
        </a>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start;">

        {{-- ── Card 1: Data Pelamar ── --}}
        <div class="adm-card">
            <div class="adm-card-header">
                <div class="adm-card-title">
                    <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    Data Pelamar
                </div>
            </div>
            <div style="padding:0 20px;">
                <div class="adm-info-list">
                    <div class="adm-info-row">
                        <span class="adm-info-key">Posisi Dilamar</span>
                        <span class="adm-info-val"><span class="adm-badge adm-badge-info">{{ $recruitment->recruit_type }}</span></span>
                    </div>
                    @if ($recruitment->recruit_type === 'DATA ENTRY')
                    <div class="adm-info-row">
                        <span class="adm-info-key">Tipe Entry</span>
                        <span class="adm-info-val">{{ $recruitment->type_entry }}</span>
                    </div>
                    @endif
                    @if ($recruitment->koordinator_id)
                    <div class="adm-info-row">
                        <span class="adm-info-key">Koordinator</span>
                        <span class="adm-info-val">{{ $recruitment->koordinator->nama_lengkap ?? '-' }}</span>
                    </div>
                    @endif
                    <div class="adm-info-row">
                        <span class="adm-info-key">Nama Lengkap</span>
                        <span class="adm-info-val">{{ $recruitment->nama_lengkap }}</span>
                    </div>
                    <div class="adm-info-row">
                        <span class="adm-info-key">NIK</span>
                        <span class="adm-info-val adm-mono" style="font-size:12.5px;">{{ $recruitment->nik }}</span>
                    </div>
                    <div class="adm-info-row">
                        <span class="adm-info-key">Jenis Kelamin</span>
                        <span class="adm-info-val" style="font-weight:400;">{{ $recruitment->jenis_kelamin }}</span>
                    </div>
                    <div class="adm-info-row">
                        <span class="adm-info-key">Telephone</span>
                        <span class="adm-info-val adm-mono">
                            <a href="tel:{{ $recruitment->telephone }}" style="color:var(--adm-blue);text-decoration:none;">{{ $recruitment->telephone }}</a>
                        </span>
                    </div>
                    <div class="adm-info-row">
                        <span class="adm-info-key">Alamat</span>
                        <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $recruitment->alamat_lengkap }}</span>
                    </div>
                    <div class="adm-info-row">
                        <span class="adm-info-key">Pendidikan</span>
                        <span class="adm-info-val"><span class="adm-badge adm-badge-info">{{ $recruitment->pendidikan_terakhir }}</span></span>
                    </div>
                    <div class="adm-info-row">
                        <span class="adm-info-key">Pengalaman</span>
                        <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $recruitment->pengalaman ?: '—' }}</span>
                    </div>
                    <div class="adm-info-row">
                        <span class="adm-info-key">Rekomendasi</span>
                        <span class="adm-info-val">
                            @if ($recruitment->rekomendasi)
                                <span class="adm-badge adm-badge-success">{{ $recruitment->rekomendasi }}</span>
                            @else
                                <span style="color:var(--adm-text-faint);font-size:13px;">Tidak ada rekomendasi</span>
                            @endif
                        </span>
                    </div>
                    <div class="adm-info-row">
                        <span class="adm-info-key">Status Lamaran</span>
                        <span class="adm-info-val">
                            @if ($recruitment->status === 'Melamar')
                                <span class="adm-badge adm-badge-pending"><span class="dot"></span>Melamar</span>
                            @elseif ($recruitment->status === 'Diterima')
                                <span class="adm-badge adm-badge-success"><span class="dot"></span>Diterima</span>
                            @elseif ($recruitment->status === 'Ditolak')
                                <span class="adm-badge adm-badge-danger"><span class="dot"></span>Ditolak</span>
                            @endif
                        </span>
                    </div>
                    <div class="adm-info-row">
                        <span class="adm-info-key">Tanggal Melamar</span>
                        <span class="adm-info-val adm-mono" style="font-size:12px;font-weight:400;">
                            {{ $recruitment->created_at->format('d M Y, H:i') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:16px;">

            {{-- ── Card 2: Update Status ── --}}
            <div class="adm-card">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Update Status Lamaran
                    </div>
                </div>
                <div style="padding:0 20px 20px;">
                    <form action="{{ route('superadmin.recruitments.update-status', $recruitment->hashed_id) }}" method="POST">
                        @csrf
                        <div style="display:grid;grid-template-columns:1fr auto;gap:10px;align-items:flex-end;margin-bottom:14px;">
                            <div class="adm-field" style="margin-bottom:0;">
                                <label class="adm-label" for="status-1">Status Lamaran <span class="req">*</span></label>
                                <select name="status" id="status-1" class="adm-field-select" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="Melamar" {{ $recruitment->status == 'Melamar' ? 'selected' : '' }}>Melamar</option>
                                    <option value="Diterima" {{ $recruitment->status == 'Diterima' ? 'selected' : '' }}>Diterima</option>
                                    <option value="Ditolak" {{ $recruitment->status == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            <button type="submit" class="adm-btn-primary" style="height:38px;white-space:nowrap;">
                                <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Update
                            </button>
                        </div>

                        @if ($recruitment->recruit_type == 'PENDAMPING')
                        <div id="koordinatorWrapper" style="display:{{ $recruitment->status == 'Diterima' ? 'block' : 'none' }};">
                            <div class="adm-field">
                                <label class="adm-label" for="koordinator_id">Pilih Koordinator <span class="req">*</span></label>
                                <select name="koordinator_id" id="koordinator_id" class="adm-field-select">
                                    <option value="">-- Pilih Koordinator --</option>
                                    @foreach ($koordinators as $koordinator)
                                        <option value="{{ $koordinator->id }}" {{ $recruitment->koordinator_id == $koordinator->id ? 'selected' : '' }}>
                                            {{ $koordinator->nama_lengkap }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="adm-hint">Wajib diisi jika status diterima.</span>
                            </div>
                        </div>
                        @endif

                        <div id="alasanPenolakanWrapper" style="display:{{ $recruitment->status == 'Ditolak' ? 'block' : 'none' }};">
                            <div class="adm-field">
                                <label class="adm-label" for="alasan_penolakan">Alasan Penolakan <span class="req">*</span></label>
                                <textarea name="alasan_penolakan" id="alasan_penolakan" class="adm-textarea" rows="3"
                                    placeholder="Masukkan alasan penolakan...">{{ old('alasan_penolakan', $recruitment->alasan_penolakan ?? '') }}</textarea>
                                <span class="adm-hint">Wajib diisi jika status ditolak.</span>
                            </div>
                        </div>
                    </form>

                    @if ($recruitment->alasan_penolakan && $recruitment->status == 'Ditolak')
                        <div class="adm-alert adm-alert-danger" style="margin-top:14px;">
                            <svg viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            <div><strong>Alasan Penolakan:</strong><p style="margin:4px 0 0;font-size:13px;">{{ $recruitment->alasan_penolakan }}</p></div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── Card 3: Dokumentasi Foto ── --}}
            <div class="adm-card">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        Dokumentasi Foto
                    </div>
                    <button type="button" class="adm-btn" data-bs-toggle="modal" data-bs-target="#modalKolaseFoto"
                        style="font-size:12px;padding:5px 12px;">
                        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                        Lihat Kolase
                    </button>
                </div>
                <div style="padding:0 20px 20px;display:flex;flex-direction:column;gap:10px;">
                    {{-- Foto Diri --}}
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:var(--adm-bg-muted);border-radius:8px;">
                        <div style="font-size:13px;font-weight:600;">Foto Diri (3×4)</div>
                        <div style="display:flex;gap:6px;">
                            <button type="button" class="adm-btn primary" style="font-size:11px;padding:4px 10px;" data-bs-toggle="modal" data-bs-target="#modalFotoDiri">
                                <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> Lihat
                            </button>
                            <a href="{{ route('superadmin.recruitments.download-foto', [$recruitment->hashed_id, 'foto_diri']) }}" class="adm-btn success" style="font-size:11px;padding:4px 10px;">
                                <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Download
                            </a>
                        </div>
                    </div>
                    {{-- Foto KTP --}}
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:var(--adm-bg-muted);border-radius:8px;">
                        <div style="font-size:13px;font-weight:600;">Foto KTP</div>
                        <div style="display:flex;gap:6px;">
                            <button type="button" class="adm-btn primary" style="font-size:11px;padding:4px 10px;" data-bs-toggle="modal" data-bs-target="#modalFotoKTP">
                                <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> Lihat
                            </button>
                            <a href="{{ route('superadmin.recruitments.download-foto', [$recruitment->hashed_id, 'foto_ktp']) }}" class="adm-btn success" style="font-size:11px;padding:4px 10px;">
                                <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Download
                            </a>
                        </div>
                    </div>
                    {{-- Foto Ijasah --}}
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:var(--adm-bg-muted);border-radius:8px;">
                        <div style="font-size:13px;font-weight:600;">File Ijasah</div>
                        <a href="{{ route('superadmin.recruitments.download-foto', [$recruitment->hashed_id, 'foto_ijasah']) }}" class="adm-btn success" style="font-size:11px;padding:4px 10px;">
                            <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Download
                        </a>
                    </div>
                    {{-- Pakta Integritas --}}
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:var(--adm-bg-muted);border-radius:8px;">
                        <div style="font-size:13px;font-weight:600;">Pakta Integritas</div>
                        <a href="{{ route('superadmin.recruitments.download-foto', [$recruitment->hashed_id, 'pakta_integritas']) }}" class="adm-btn success" style="font-size:11px;padding:4px 10px;">
                            <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Download
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Kolase --}}
<div class="modal fade" id="modalKolaseFoto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kolase Dokumentasi Foto – {{ $recruitment->nama_lengkap }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3" id="collageContent">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-header bg-light py-2 px-3"><small class="fw-bold">Foto Diri (3x4)</small></div>
                            <img src="{{ asset('storage/' . $recruitment->foto_diri) }}" alt="Foto Diri" class="card-img-bottom collage-img"
                                style="height:400px;object-fit:cover;cursor:pointer;"
                                onclick="viewFullImage('{{ asset('storage/' . $recruitment->foto_diri) }}', 'Foto Diri')">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-header bg-light py-2 px-3"><small class="fw-bold">Foto KTP</small></div>
                            <img src="{{ asset('storage/' . $recruitment->foto_ktp) }}" alt="Foto KTP" class="card-img-bottom collage-img"
                                style="height:400px;object-fit:cover;cursor:pointer;"
                                onclick="viewFullImage('{{ asset('storage/' . $recruitment->foto_ktp) }}', 'Foto KTP')">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="adm-btn success" onclick="downloadCollage()">
                    <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Download Kolase
                </button>
                <button type="button" class="adm-btn primary" onclick="printCollage()">
                    <svg viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg> Print
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Full Image --}}
<div class="modal fade" id="modalFullImage" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fullImageTitle">Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-3">
                <img id="fullImageSrc" src="" alt="Full Image" class="img-fluid rounded" style="max-height:600px;">
            </div>
            <div class="modal-footer">
                <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="adm-btn success" onclick="downloadSingleImage()">Download Foto</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Foto Diri --}}
<div class="modal fade" id="modalFotoDiri" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Foto Diri (3x4)</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body text-center p-3">
                <img src="{{ asset('storage/' . $recruitment->foto_diri) }}" alt="Foto Diri" class="img-fluid rounded" style="max-height:500px;">
            </div>
            <div class="modal-footer"><button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Tutup</button></div>
        </div>
    </div>
</div>

{{-- Modal Foto KTP --}}
<div class="modal fade" id="modalFotoKTP" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Foto KTP</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body text-center p-3">
                <img src="{{ asset('storage/' . $recruitment->foto_ktp) }}" alt="Foto KTP" class="img-fluid rounded" style="max-height:500px;">
            </div>
            <div class="modal-footer"><button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Tutup</button></div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    // Show/Hide Koordinator & Alasan Penolakan
    document.getElementById('status-1').addEventListener('change', function() {
        const recruitType = '{{ $recruitment->recruit_type }}';
        const alasanWrapper = document.getElementById('alasanPenolakanWrapper');
        const alasanTextarea = document.getElementById('alasan_penolakan');
        if (recruitType === 'PENDAMPING') {
            const koordinatorWrapper = document.getElementById('koordinatorWrapper');
            const koordinatorSelect = document.getElementById('koordinator_id');
            if (this.value === 'Diterima') { koordinatorWrapper.style.display = 'block'; koordinatorSelect.required = true; }
            else { koordinatorWrapper.style.display = 'none'; koordinatorSelect.required = false; }
        }
        if (this.value === 'Ditolak') { alasanWrapper.style.display = 'block'; alasanTextarea.required = true; }
        else { alasanWrapper.style.display = 'none'; alasanTextarea.required = false; }
    });

    function viewFullImage(src, title) {
        document.getElementById('fullImageSrc').src = src;
        document.getElementById('fullImageTitle').textContent = title;
        new bootstrap.Modal(document.getElementById('modalFullImage')).show();
    }

    function downloadSingleImage() {
        const imgSrc = document.getElementById('fullImageSrc').src;
        const imgTitle = document.getElementById('fullImageTitle').textContent;
        fetch(imgSrc).then(r => r.blob()).then(blob => {
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url; a.download = imgTitle.replace(/\s+/g, '_') + '.jpg';
            document.body.appendChild(a); a.click();
            URL.revokeObjectURL(url); document.body.removeChild(a);
        }).catch(() => alert('Gagal mendownload gambar'));
    }

    function downloadCollage() {
        const el = document.getElementById('collageContent');
        const nama = '{{ $recruitment->nama_lengkap }}';
        const loading = Object.assign(document.createElement('div'), {
            innerHTML: 'Memproses download...',
            style: 'position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:rgba(0,0,0,.8);color:#fff;padding:20px;border-radius:10px;z-index:9999;'
        });
        document.body.appendChild(loading);
        html2canvas(el, { scale: 2, useCORS: true, backgroundColor: '#fff' }).then(canvas => {
            canvas.toBlob(blob => {
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url; a.download = 'Kolase_' + nama.replace(/\s+/g, '_') + '.jpg';
                document.body.appendChild(a); a.click();
                URL.revokeObjectURL(url); document.body.removeChild(a);
                document.body.removeChild(loading);
            }, 'image/jpeg', 0.95);
        }).catch(() => { alert('Gagal membuat kolase'); document.body.removeChild(loading); });
    }

    function printCollage() {
        const content = document.getElementById('collageContent').innerHTML;
        const w = window.open('', '', 'height=600,width=800');
        w.document.write('<html><head><title>Kolase Foto</title>');
        w.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">');
        w.document.write('<style>.collage-img{height:400px!important;object-fit:cover}.card{break-inside:avoid}@media print{body{-webkit-print-color-adjust:exact}}</style>');
        w.document.write('</head><body>');
        w.document.write('<h3 class="text-center mb-4">Dokumentasi Foto – {{ $recruitment->nama_lengkap }}</h3>');
        w.document.write(content);
        w.document.write('</body></html>');
        w.document.close(); w.focus();
        setTimeout(() => { w.print(); w.close(); }, 250);
    }
</script>
@endsection
