@extends('layouts.app')
@section('template_title')
    Pembayaran Komisi WRGROUP
@endsection

@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Pembayaran Komisi WRGROUP</h1>
                <p>Komisi dibayar langsung dari sini (transfer manual), lalu buktinya dilaporkan ke WRGROUP untuk diverifikasi</p>
            </div>
            <a href="{{ route('superadmin.wrgroup.index') }}" class="adm-btn-secondary">
                <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Kembali ke Monitoring
            </a>
        </div>

        @if ($errorPeriode)
            <div class="adm-alert adm-alert-danger" style="margin-bottom:16px;">
                Gagal mengambil daftar periode komisi dari WRGROUP: {{ $errorPeriode }}
            </div>
        @endif

        <div class="adm-card" style="margin-bottom:16px;">
            <div class="adm-card-header">
                <div class="adm-card-title">
                    <svg viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    Periode Komisi Tertunggak
                </div>
            </div>
            <div class="table-responsive">
                <table class="adm-table w-100">
                    <thead>
                        <tr>
                            <th>Periode</th>
                            <th class="tr">Nilai Komisi</th>
                            <th class="tr">Sisa Tagihan</th>
                            <th class="tc">Status</th>
                            <th class="tc" style="width:140px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($periodes as $p)
                            @php
                                $referensi = $p['hashed_id'] ?? $p['referensi'] ?? '';
                                $sisa = $p['sisa_tagihan'] ?? $p['nilai_final'] ?? $p['jumlah'] ?? 0;
                            @endphp
                            <tr>
                                <td>{{ $p['periode'] ?? '—' }}</td>
                                <td class="tr">Rp {{ number_format($p['nilai_final'] ?? $p['jumlah'] ?? 0, 0, ',', '.') }}</td>
                                <td class="tr">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                                <td class="tc"><span class="adm-badge adm-badge-pending">{{ $p['status'] ?? '—' }}</span></td>
                                <td class="tc">
                                    <button type="button" class="adm-btn primary" onclick="bukaFormBayar('{{ $referensi }}', '{{ $p['periode'] ?? '' }}')">
                                        Bayar
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="text-align:center;color:var(--adm-text-faint);">Tidak ada periode komisi tertunggak.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div id="formBayarWrapper" style="display:none;padding:16px;border-top:1px solid var(--adm-border, #e5e7eb);">
                <form id="formBayarKomisi" method="POST">
                    @csrf
                    <input type="hidden" name="periode" id="inputPeriode">
                    <h4 id="judulFormBayar" style="margin-bottom:12px;"></h4>
                    <div class="adm-form-grid">
                        <div class="adm-field">
                            <label class="adm-label">Jumlah Dibayar <span class="req">*</span></label>
                            <input type="number" name="jumlah" class="adm-input" min="1" step="1" required>
                        </div>
                        <div class="adm-field">
                            <label class="adm-label">Tanggal Transaksi Bank <span class="req">*</span></label>
                            <input type="date" name="tanggal_transaksi_bank" class="adm-input" required>
                        </div>
                        <div class="adm-field">
                            <label class="adm-label">Referensi Bank</label>
                            <input type="text" name="referensi_bank" class="adm-input" placeholder="Nomor referensi transfer">
                        </div>
                        <div class="adm-field">
                            <label class="adm-label">Bukti Setoran <span class="req">*</span></label>
                            <input type="file" name="bukti_setoran" class="adm-input" accept=".jpg,.jpeg,.png,.pdf" required>
                        </div>
                        <div class="adm-field" style="grid-column:1/-1;">
                            <label class="adm-label">Catatan</label>
                            <textarea name="catatan" class="adm-textarea" rows="2"></textarea>
                        </div>
                    </div>
                    <div style="margin-top:12px;display:flex;gap:8px;">
                        <button type="submit" class="adm-btn-primary" id="btnKirimBayar">Kirim ke WRGROUP</button>
                        <button type="button" class="adm-btn-secondary" onclick="tutupFormBayar()">Batal</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="adm-card">
            <div class="adm-card-header">
                <div class="adm-card-title">
                    <svg viewBox="0 0 24 24"><path d="M21 12a9 9 0 1 1-9-9c2.52 0 4.93 1 6.74 2.74L21 8"/><polyline points="21 3 21 8 16 8"/></svg>
                    Riwayat Pengajuan
                </div>
            </div>
            <div class="table-responsive">
                <table class="adm-table w-100">
                    <thead>
                        <tr>
                            <th>Tanggal Kirim</th>
                            <th>Periode</th>
                            <th class="tr">Jumlah</th>
                            <th>Dikirim Oleh</th>
                            <th class="tc">Status</th>
                            <th class="tc" style="width:90px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($riwayat as $r)
                            <tr>
                                <td>{{ $r->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $r->periode }}</td>
                                <td class="tr">Rp {{ number_format($r->jumlah, 0, ',', '.') }}</td>
                                <td>{{ $r->pengirim->name ?? '—' }}</td>
                                <td class="tc">
                                    @if ($r->berhasil === null)
                                        <span class="adm-badge adm-badge-pending">Belum Dikirim</span>
                                    @elseif ($r->berhasil)
                                        <span class="adm-badge adm-badge-success">Terkirim</span>
                                    @else
                                        <span class="adm-badge adm-badge-danger">Gagal &middot; HTTP {{ $r->http_status ?? '—' }}</span>
                                    @endif
                                </td>
                                <td class="tc">
                                    @if ($r->berhasil === false)
                                        <form action="{{ route('superadmin.wrgroup.komisi.ulangi', $r->hashed_id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="adm-btn warning">Coba Lagi</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" style="text-align:center;color:var(--adm-text-faint);">Belum ada pengajuan pembayaran komisi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function bukaFormBayar(referensi, periode) {
        document.getElementById('inputPeriode').value = periode;
        document.getElementById('judulFormBayar').textContent = 'Bayar Komisi Periode ' + periode;
        document.getElementById('formBayarKomisi').action = `{{ url('superadmin/wrgroup/komisi') }}/${referensi}/pembayaran`;
        document.getElementById('formBayarWrapper').style.display = 'block';
        document.getElementById('formBayarWrapper').scrollIntoView({ behavior: 'smooth' });
    }

    function tutupFormBayar() {
        document.getElementById('formBayarWrapper').style.display = 'none';
        document.getElementById('formBayarKomisi').reset();
    }

    document.getElementById('formBayarKomisi').addEventListener('submit', function () {
        const btn = document.getElementById('btnKirimBayar');
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Mengirim...`;
    });

    document.querySelectorAll('form[action*="/ulangi"]').forEach(function (form) {
        form.addEventListener('submit', function () {
            const btn = form.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span>`;
        });
    });
</script>
@endpush
