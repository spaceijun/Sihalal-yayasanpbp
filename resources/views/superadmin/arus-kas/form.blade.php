{{-- ── TIPE ── --}}
<div class="adm-field">
    <label class="adm-label" for="tipe">Tipe <span class="req">*</span></label>
    <select name="tipe" id="tipe" class="adm-field-select @error('tipe') is-invalid @enderror">
        <option value="">-- Pilih Tipe --</option>
        <option value="Pemasukan" {{ old('tipe', $cashflows?->tipe) == 'Pemasukan' ? 'selected' : '' }}>Pemasukan</option>
        <option value="Pengeluaran" {{ old('tipe', $cashflows?->tipe) == 'Pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
        <option value="Kas" {{ old('tipe', $cashflows?->tipe) == 'Kas' ? 'selected' : '' }}>Kas</option>
    </select>
    @error('tipe') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── TANGGAL ── --}}
<div class="adm-field">
    <label class="adm-label" for="tanggal">Tanggal <span class="req">*</span></label>
    <input type="date" name="tanggal" id="tanggal"
        class="adm-input @error('tanggal') is-invalid @enderror"
        value="{{ old('tanggal', $cashflow?->tanggal) }}">
    @error('tanggal') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── JUMLAH ── --}}
<div class="adm-field">
    <label class="adm-label" for="jumlah">Jumlah <span class="req">*</span></label>
    <input type="number" name="jumlah" id="jumlah"
        class="adm-input @error('jumlah') is-invalid @enderror"
        value="{{ old('jumlah', $cashflow?->jumlah) }}"
        placeholder="contoh: 500000">
    @error('jumlah') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

{{-- ── KETERANGAN ── --}}
<div class="adm-field full-span">
    <label class="adm-label" for="keterangan">Keterangan</label>
    <textarea name="keterangan" id="keterangan" rows="5"
        class="adm-textarea @error('keterangan') is-invalid @enderror"
        placeholder="Keterangan transaksi...">{{ old('keterangan', $cashflow?->keterangan) }}</textarea>
    @error('keterangan') <span class="adm-error-msg">{{ $message }}</span> @enderror
</div>

<script>
    if (typeof ClassicEditor !== 'undefined') {
        ClassicEditor.create(document.querySelector('#keterangan'), {
            toolbar: ['heading','|','bold','italic','link','bulletedList','numberedList','|','blockQuote','insertTable','|','undo','redo']
        }).catch(error => console.error(error));
    }
</script>
