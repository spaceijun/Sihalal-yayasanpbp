@extends('layouts.app')
@section('template_title')
    Buat Surat — WRGROUP
@endsection

@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Buat Surat</h1>
                <p>Surat akan diajukan ke WRGROUP untuk diperiksa &amp; disetujui. Template dan kop resmi (WRGROUP + Kawulo Halal) sepenuhnya diatur WRGROUP.</p>
            </div>
            <a href="{{ route('superadmin.wrgroup-surat.index') }}" class="adm-btn-secondary">
                <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Kembali ke Arsip
            </a>
        </div>

        @if ($errorJenis)
            <div class="adm-alert adm-alert-danger" style="margin-bottom:16px;">
                Gagal mengambil daftar jenis surat dari WRGROUP: {{ $errorJenis }}
            </div>
        @elseif (empty($jenisList))
            <div class="adm-alert adm-alert-warning" style="margin-bottom:16px;">
                Belum ada jenis surat yang dibuka WRGROUP untuk diajukan pilar. Hubungi Superadmin WRGROUP.
            </div>
        @else
            <div class="adm-card">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Formulir Surat
                    </div>
                </div>
                <form action="{{ route('superadmin.wrgroup-surat.store') }}" method="POST" id="formBuatSurat" style="padding:16px;">
                    @csrf
                    <div class="adm-form-grid">
                        <div class="adm-field" style="grid-column:1/-1;">
                            <label class="adm-label">Jenis Surat <span class="req">*</span></label>
                            <select name="surat_jenis_kode" id="jenisSurat" class="adm-field-select" required>
                                <option value="">-- Pilih Jenis Surat --</option>
                                @foreach ($jenisList as $j)
                                    <option value="{{ $j['kode'] }}" {{ old('surat_jenis_kode') === $j['kode'] ? 'selected' : '' }}>{{ $j['nama'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="adm-field" style="grid-column:1/-1;">
                            <label class="adm-label">Perihal <span class="req">*</span></label>
                            <input type="text" name="perihal" class="adm-input" value="{{ old('perihal') }}" required>
                        </div>
                    </div>

                    <div id="fieldDinamis"></div>

                    <input type="hidden" name="jenis_nama" id="inputJenisNama">

                    <div style="margin-top:16px;">
                        <button type="submit" class="adm-btn-primary" id="btnAjukan">Ajukan ke WRGROUP</button>
                    </div>
                </form>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    const JENIS_DATA = @json($jenisList ?? []);

    function renderFieldDinamis(kode) {
        const jenis = JENIS_DATA.find(j => j.kode === kode);
        document.getElementById('inputJenisNama').value = jenis ? jenis.nama : '';

        const wrap = document.getElementById('fieldDinamis');
        wrap.innerHTML = '';
        if (!jenis) return;

        jenis.fields.slice().sort((a, b) => a.urutan - b.urutan).forEach(function (f) {
            const div = document.createElement('div');
            div.className = 'adm-field';
            div.style.marginTop = '12px';

            const label = document.createElement('label');
            label.className = 'adm-label';
            label.textContent = f.label + (f.wajib ? ' *' : '');
            div.appendChild(label);

            let input;
            if (f.tipe === 'paragraf') {
                input = document.createElement('textarea');
                input.className = 'adm-textarea';
                input.rows = 4;
            } else if (f.tipe === 'pilihan') {
                input = document.createElement('select');
                input.className = 'adm-field-select';
                input.innerHTML = '<option value="">-- Pilih --</option>' +
                    (f.opsi_pilihan || []).map(function (o) { return '<option value="' + o + '">' + o + '</option>'; }).join('');
            } else {
                input = document.createElement('input');
                input.className = 'adm-input';
                input.type = f.tipe === 'angka' ? 'number' : (f.tipe === 'tanggal' ? 'date' : 'text');
            }
            input.name = 'data_variabel[' + f.kode + ']';
            if (f.wajib) input.required = true;
            div.appendChild(input);

            wrap.appendChild(div);
        });
    }

    document.getElementById('jenisSurat')?.addEventListener('change', function () {
        renderFieldDinamis(this.value);
    });
    if (document.getElementById('jenisSurat')?.value) {
        renderFieldDinamis(document.getElementById('jenisSurat').value);
    }

    document.getElementById('formBuatSurat')?.addEventListener('submit', function () {
        const btn = document.getElementById('btnAjukan');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Mengajukan...';
    });
</script>
@endpush
