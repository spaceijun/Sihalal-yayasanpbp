{{-- ── VERIFIKATOR ID ── --}}
<div class="adm-field">
    <label class="adm-label" for="verifikator_id">Verifikator ID <span class="req">*</span></label>
    <input type="text" name="verifikator_id" id="verifikator_id"
        class="adm-input @error('verifikator_id') is-invalid @enderror"
        value="{{ old('verifikator_id', $verifikatorPayment?->verifikator_id) }}" placeholder="ID Verifikator">
    @error('verifikator_id')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── JUMLAH DATA ── --}}
<div class="adm-field">
    <label class="adm-label" for="jumlah_data">Jumlah Data <span class="req">*</span></label>
    <input type="text" name="jumlah_data" id="jumlah_data"
        class="adm-input @error('jumlah_data') is-invalid @enderror"
        value="{{ old('jumlah_data', $verifikatorPayment?->jumlah_data) }}" placeholder="Jumlah Data">
    @error('jumlah_data')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── TOTAL NOMINAL ── --}}
<div class="adm-field">
    <label class="adm-label" for="total_nominal">Total Nominal <span class="req">*</span></label>
    <input type="text" name="total_nominal" id="total_nominal"
        class="adm-input @error('total_nominal') is-invalid @enderror"
        value="{{ old('total_nominal', $verifikatorPayment?->total_nominal) }}" placeholder="Total Nominal">
    @error('total_nominal')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── PERIODE DARI ── --}}
<div class="adm-field">
    <label class="adm-label" for="periode_dari">Periode Dari <span class="req">*</span></label>
    <input type="text" name="periode_dari" id="periode_dari"
        class="adm-input @error('periode_dari') is-invalid @enderror"
        value="{{ old('periode_dari', $verifikatorPayment?->periode_dari) }}" placeholder="Periode Dari">
    @error('periode_dari')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── PERIODE SAMPAI ── --}}
<div class="adm-field">
    <label class="adm-label" for="periode_sampai">Periode Sampai <span class="req">*</span></label>
    <input type="text" name="periode_sampai" id="periode_sampai"
        class="adm-input @error('periode_sampai') is-invalid @enderror"
        value="{{ old('periode_sampai', $verifikatorPayment?->periode_sampai) }}" placeholder="Periode Sampai">
    @error('periode_sampai')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>

{{-- ── PAID AT ── --}}
<div class="adm-field">
    <label class="adm-label" for="paid_at">Paid At <span class="req">*</span></label>
    <input type="text" name="paid_at" id="paid_at"
        class="adm-input @error('paid_at') is-invalid @enderror"
        value="{{ old('paid_at', $verifikatorPayment?->paid_at) }}" placeholder="Paid At">
    @error('paid_at')
        <span class="adm-error-msg">{{ $message }}</span>
    @enderror
</div>
