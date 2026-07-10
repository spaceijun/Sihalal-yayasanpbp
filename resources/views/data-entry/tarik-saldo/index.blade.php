@extends('layouts.app')

@section('template_title')
    Tarik Saldo
@endsection

@section('content')
<div class="adm-page">
    @include('layouts.messages')

    {{-- ── HEADER ── --}}
    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Tarik Saldo</h1>
            <p>Ajukan penarikan saldo dari tagihan yang sudah terkumpul</p>
        </div>
    </div>

    {{-- ── STAT CARDS ── --}}
    <div class="adm-stats" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr));margin-bottom:24px;">

        <div class="adm-stat is-accent">
            <div class="adm-stat-label">Saldo Bisa Ditarik</div>
            <div class="adm-stat-value" style="font-size:20px;">
                Rp {{ number_format($totalBisaDitarik, 0, ',', '.') }}
            </div>
            <div class="adm-stat-sub">Dari {{ $penagihansMenunggu->count() }} tagihan</div>
        </div>

        <div class="adm-stat">
            <div class="adm-stat-label">Total Dibayar</div>
            <div class="adm-stat-value is-success" style="font-size:20px;">
                Rp {{ number_format($totalDibayar, 0, ',', '.') }}
            </div>
            <div class="adm-stat-sub">Sudah dicairkan</div>
        </div>

        <div class="adm-stat">
            <div class="adm-stat-label">Pengajuan Aktif</div>
            <div class="adm-stat-value is-warn" style="font-size:20px;">
                {{ $penarikan->whereIn('status', ['Menunggu','Diproses'])->count() }}
            </div>
            <div class="adm-stat-sub">Sedang diproses</div>
        </div>

    </div>

    <div class="row g-3">

        {{-- ── FORM AJUKAN PENARIKAN ── --}}
        <div class="col-lg-5">
            <div class="adm-card h-100">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        Ajukan Penarikan Saldo
                    </div>
                </div>
                <div style="padding:20px;">

                    @if($penagihansMenunggu->isEmpty())
                        <div class="adm-empty" style="padding:40px 20px;">
                            <svg viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                            <p>Belum ada saldo yang bisa ditarik.<br>
                            <span style="font-size:12px;color:var(--adm-text-muted);">Tagihan akan terbentuk otomatis setiap 15 data diterima.</span></p>
                        </div>

                    @elseif($penarikan->whereIn('status', ['Menunggu','Diproses'])->count() > 0)
                        <div class="adm-alert adm-alert-warning" style="margin-bottom:0;">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <div>
                                <strong>Ada pengajuan yang sedang diproses.</strong><br>
                                <span style="font-size:12px;">Tunggu hingga pengajuan sebelumnya selesai sebelum mengajukan lagi.</span>
                            </div>
                        </div>

                    @else
                        <form action="{{ route('data-entry.tarik-saldo.store') }}" method="POST" id="formTarikSaldo">
                            @csrf

                            <p style="font-size:13px;color:var(--adm-text-muted);margin-bottom:16px;">
                                Pilih tagihan yang ingin Anda cairkan. Centang semua untuk menarik seluruh saldo.
                            </p>

                            {{-- Daftar tagihan --}}
                            <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:16px;">
                                @foreach($penagihansMenunggu as $pen)
                                <label class="tagihan-item" for="pen_{{ $pen->id }}" style="display:flex;align-items:center;gap:10px;padding:12px;border:1px solid var(--adm-border);border-radius:8px;cursor:pointer;transition:background .15s;">
                                    <input type="checkbox" name="penagihan_ids[]" value="{{ $pen->id }}" id="pen_{{ $pen->id }}"
                                        class="tagihan-check form-check-input" style="width:16px;height:16px;flex-shrink:0;">
                                    <div style="flex:1;min-width:0;">
                                        <div style="font-size:13px;font-weight:600;color:var(--adm-text);">
                                            Rp {{ number_format($pen->nominal, 0, ',', '.') }}
                                        </div>
                                        <div style="font-size:11.5px;color:var(--adm-text-muted);">
                                            {{ $pen->jumlah_data }} data · {{ $pen->jumlah_paket }}x Paket ·
                                            {{ $pen->tanggal_tagihan->format('d M Y') }}
                                        </div>
                                    </div>
                                    <span class="adm-badge adm-badge-pending" style="font-size:11px;"><span class="dot"></span>Belum Ditarik</span>
                                </label>
                                @endforeach
                            </div>

                            {{-- Total yang dipilih --}}
                            <div style="background:var(--adm-blue-lt);border-radius:8px;padding:12px 16px;margin-bottom:16px;display:flex;justify-content:space-between;align-items:center;">
                                <span style="font-size:13px;color:var(--adm-blue);font-weight:600;">Total Dipilih</span>
                                <span style="font-size:16px;font-weight:700;color:var(--adm-blue);" id="totalDipilih">Rp 0</span>
                            </div>

                            {{-- Catatan --}}
                            <div class="adm-field" style="margin-bottom:16px;">
                                <label class="adm-label" for="catatan_de">Catatan <span style="font-weight:400;color:var(--adm-text-muted);">(opsional)</span></label>
                                <textarea name="catatan_de" id="catatan_de" class="adm-textarea" rows="2"
                                    placeholder="Catatan tambahan untuk admin..."></textarea>
                            </div>

                            <button type="submit" class="adm-btn-primary w-100" id="btnAjukan" disabled>
                                <svg viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                Ajukan Penarikan
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── RIWAYAT PENARIKAN ── --}}
        <div class="col-lg-7">
            <div class="adm-card h-100">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Riwayat Pengajuan
                        <span class="adm-count-badge">{{ $penarikan->count() }}</span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th style="width:44px">#</th>
                                <th>Tanggal Ajuan</th>
                                <th class="tr">Nominal</th>
                                <th class="tc">Status</th>
                                <th>Catatan Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($penarikan as $idx => $p)
                            <tr>
                                <td><span class="adm-rownum">{{ $idx + 1 }}</span></td>
                                <td class="adm-mono" style="font-size:12.5px;">
                                    {{ $p->tanggal_pengajuan->format('d M Y, H:i') }}
                                </td>
                                <td class="tr adm-mono" style="font-weight:700;color:var(--adm-green);font-size:13px;">
                                    Rp {{ number_format($p->nominal, 0, ',', '.') }}
                                </td>
                                <td class="tc">
                                    @switch($p->status)
                                        @case('Menunggu')
                                            <span class="adm-badge adm-badge-pending"><span class="dot"></span>Menunggu</span>
                                            @break
                                        @case('Diproses')
                                            <span class="adm-badge adm-badge-info"><span class="dot"></span>Diproses</span>
                                            @break
                                        @case('Disetujui')
                                            <span class="adm-badge adm-badge-success"><span class="dot"></span>Disetujui</span>
                                            @break
                                        @case('Ditolak')
                                            <span class="adm-badge adm-badge-danger"><span class="dot"></span>Ditolak</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    @if($p->catatan_admin)
                                        <span style="font-size:12px;color:var(--adm-text-muted);">{{ $p->catatan_admin }}</span>
                                    @else
                                        <span style="color:var(--adm-text-faint);">—</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">
                                    <div class="adm-empty">
                                        <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
                                        <p>Belum ada riwayat pengajuan.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checks    = document.querySelectorAll('.tagihan-check');
    const totalEl   = document.getElementById('totalDipilih');
    const btnAjukan = document.getElementById('btnAjukan');

    if (!checks.length) return;

    // Nominal per tagihan (diambil dari value checkbox → kita simpan di data-nominal)
    const nominalMap = {};
    @foreach($penagihansMenunggu as $pen)
    nominalMap[{{ $pen->id }}] = {{ $pen->nominal }};
    @endforeach

    function updateTotal() {
        let total = 0;
        checks.forEach(c => {
            if (c.checked) total += nominalMap[c.value] || 0;
        });
        totalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
        btnAjukan.disabled = total === 0;
    }

    checks.forEach(c => {
        c.addEventListener('change', function () {
            const label = this.closest('label');
            if (this.checked) {
                label.style.background = 'var(--adm-blue-lt)';
                label.style.borderColor = 'var(--adm-blue)';
            } else {
                label.style.background = '';
                label.style.borderColor = '';
            }
            updateTotal();
        });
    });
});
</script>
@endsection
