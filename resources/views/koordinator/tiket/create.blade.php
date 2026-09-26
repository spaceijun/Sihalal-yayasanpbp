@extends('layouts.app')
@section('template_title') Buat Tiket @endsection

@section('content')
<div class="adm-page">
    @include('layouts.messages')

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Buat Tiket Aduan</h1>
            <p>Sampaikan kendala atau laporan Anda kepada Admin</p>
        </div>
        <a href="{{ route('koordinator.tiket.index') }}" class="adm-btn-secondary">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg> Kembali
        </a>
    </div>

    <form id="formTiket" method="POST" action="{{ route('koordinator.tiket.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="adm-form-section">
            <div class="adm-form-section-header">
                <svg viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                Detail Aduan
            </div>
            <div class="adm-form-body">
                <div class="adm-form-grid cols-2">
                    <div class="adm-field">
                        <label class="adm-label" for="kategori">Kategori <span class="req">*</span></label>
                        <select name="kategori" id="kategori" class="adm-field-select @error('kategori') is-invalid @enderror">
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Data Lapangan" {{ old('kategori') == 'Data Lapangan' ? 'selected' : '' }}>Data Lapangan</option>
                            <option value="Enumerator" {{ old('kategori') == 'Enumerator' ? 'selected' : '' }}>Enumerator</option>
                            <option value="Teknis Lapangan" {{ old('kategori') == 'Teknis Lapangan' ? 'selected' : '' }}>Teknis Lapangan</option>
                            <option value="Lainnya" {{ old('kategori') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('kategori')
                            <span class="adm-error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="adm-field" id="refDataLapanganWrap" style="{{ old('kategori') === 'Data Lapangan' ? '' : 'display:none;' }}">
                        <label class="adm-label" for="ref_data_lapangan_id">Data Lapangan Terkait <span class="req">*</span></label>
                        <select name="ref_data_lapangan_id" id="ref_data_lapangan_id" class="adm-field-select select2-ref @error('ref_data_lapangan_id') is-invalid @enderror">
                            <option value="">-- Pilih Data Lapangan --</option>
                            @foreach ($dataLapangans as $dl)
                                <option value="{{ $dl->id }}" {{ old('ref_data_lapangan_id') == $dl->id ? 'selected' : '' }}>
                                    {{ $dl->no_registrasi }} — {{ $dl->nama_pu }}
                                </option>
                            @endforeach
                        </select>
                        @error('ref_data_lapangan_id')
                            <span class="adm-error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="adm-field" id="refEnumeratorWrap" style="{{ old('kategori') === 'Enumerator' ? '' : 'display:none;' }}">
                        <label class="adm-label" for="ref_enumerator_id">Enumerator Terkait <span class="req">*</span></label>
                        <select name="ref_enumerator_id" id="ref_enumerator_id" class="adm-field-select select2-ref @error('ref_enumerator_id') is-invalid @enderror">
                            <option value="">-- Pilih Enumerator --</option>
                            @foreach ($enumerators as $e)
                                <option value="{{ $e->id }}" {{ old('ref_enumerator_id') == $e->id ? 'selected' : '' }}>
                                    {{ $e->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                        @error('ref_enumerator_id')
                            <span class="adm-error-msg">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="adm-form-grid cols-1" style="margin-top:14px;">
                    <div class="adm-field">
                        <label class="adm-label" for="subject">Subjek <span class="req">*</span></label>
                        <input type="text" name="subject" id="subject" class="adm-input @error('subject') is-invalid @enderror"
                            value="{{ old('subject') }}" placeholder="Ringkasan singkat masalah Anda">
                        @error('subject')
                            <span class="adm-error-msg">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="adm-field">
                        <label class="adm-label" for="description">Deskripsi <span class="req">*</span></label>
                        <textarea name="description" id="description" class="adm-textarea @error('description') is-invalid @enderror"
                            rows="5" placeholder="Jelaskan detail kendala atau laporan Anda...">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="adm-error-msg">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="adm-field">
                        <label class="adm-label" for="file">Lampiran <span style="font-weight:400;color:var(--adm-text-muted);">(opsional, maks 2MB)</span></label>
                        <input type="file" name="file" id="file" class="adm-input @error('file') is-invalid @enderror" accept=".jpg,.jpeg,.png,.pdf">
                        @error('file')
                            <span class="adm-error-msg">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="adm-form-actions">
            <button type="submit" class="adm-btn-primary" id="btnSimpanTiket">
                <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Kirim Tiket
            </button>
            <a href="{{ route('koordinator.tiket.index') }}" class="adm-btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.select2-ref').select2({
            allowClear: true, width: '100%',
            language: { noResults: () => 'Tidak ditemukan', searching: () => 'Mencari...' },
        });
    });

    document.getElementById('kategori').addEventListener('change', function () {
        document.getElementById('refDataLapanganWrap').style.display = this.value === 'Data Lapangan' ? '' : 'none';
        document.getElementById('refEnumeratorWrap').style.display = this.value === 'Enumerator' ? '' : 'none';
    });

    document.getElementById('formTiket').addEventListener('submit', function () {
        const btn = document.getElementById('btnSimpanTiket');
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengirim...`;
    });
</script>
@endpush
