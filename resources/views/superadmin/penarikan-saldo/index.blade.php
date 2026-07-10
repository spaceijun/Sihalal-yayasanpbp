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
                <span class="adm-count-badge">{{ $penarikan->total() }}</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="adm-table">
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
                <tbody>
                    @forelse($penarikan as $idx => $p)
                    <tr>
                        <td><span class="adm-rownum">{{ $penarikan->firstItem() + $idx }}</span></td>
                        <td>
                            <div class="adm-name-cell">
                                <div class="adm-avatar" style="background:var(--adm-blue-lt);color:var(--adm-blue);">
                                    {{ strtoupper(substr($p->dataEntry->nama_lengkap, 0, 2)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600;font-size:13px;">{{ $p->dataEntry->nama_lengkap }}</div>
                                    <div style="font-size:11.5px;color:var(--adm-text-muted);">{{ $p->dataEntry->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="adm-mono" style="font-size:12.5px;">
                            {{ $p->tanggal_pengajuan->format('d M Y, H:i') }}
                        </td>
                        <td class="tc">
                            <span class="adm-badge adm-badge-info">{{ $p->penagihans->count() }} Tagihan</span>
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
                            @if($p->catatan_de)
                                <span style="font-size:12px;color:var(--adm-text-muted);">{{ $p->catatan_de }}</span>
                            @else
                                <span style="color:var(--adm-text-faint);">—</span>
                            @endif
                        </td>
                        <td class="tc">
                            @if(in_array($p->status, ['Menunggu', 'Diproses']))
                                <div class="adm-actions" style="justify-content:center;gap:5px;">
                                    <button type="button" class="adm-btn success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalSetujui{{ $p->id }}"
                                        style="font-size:11.5px;padding:5px 10px;">
                                        <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                        Setujui
                                    </button>
                                    <button type="button" class="adm-btn danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalTolak{{ $p->id }}"
                                        style="font-size:11.5px;padding:5px 10px;">
                                        <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        Tolak
                                    </button>
                                </div>
                            @elseif($p->status === 'Disetujui')
                                @if($p->catatan_admin)
                                    <span data-bs-toggle="tooltip" title="{{ $p->catatan_admin }}"
                                        style="cursor:help;color:var(--adm-blue);display:inline-flex;">
                                        <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="12" y1="8" x2="12" y2="12"/>
                                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                                        </svg>
                                    </span>
                                @else
                                    <span style="color:var(--adm-text-faint);">—</span>
                                @endif
                            @else
                                @if($p->catatan_admin)
                                    <span data-bs-toggle="tooltip" title="{{ $p->catatan_admin }}"
                                        style="cursor:help;color:var(--adm-blue);display:inline-flex;">
                                        <svg viewBox="0 0 24 24" style="width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="12" y1="8" x2="12" y2="12"/>
                                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                                        </svg>
                                    </span>
                                @else
                                    <span style="color:var(--adm-text-faint);">—</span>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="adm-empty">
                                <svg viewBox="0 0 24 24">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                </svg>
                                <p>Belum ada pengajuan penarikan saldo.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="adm-card-footer">
            <span class="adm-footer-info">
                Menampilkan {{ $penarikan->firstItem() ?? 0 }}–{{ $penarikan->lastItem() ?? 0 }}
                dari {{ $penarikan->total() }} pengajuan
            </span>
            @include('layouts.pagination', ['paginator' => $penarikan])
        </div>
    </div>

</div>

{{-- ══ MODALS SETUJUI & TOLAK ══ --}}
@foreach($penarikan as $p)
    @if(in_array($p->status, ['Menunggu', 'Diproses']))

        {{-- Modal Setujui --}}
        <div class="modal fade" id="modalSetujui{{ $p->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('superadmin.penarikan-saldo.setujui', $p) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <svg viewBox="0 0 24 24" style="width:18px;height:18px;stroke:var(--adm-green);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;margin-right:6px;vertical-align:-3px;">
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
                                    <p style="margin:0;font-size:13px;"><strong>Data Entry:</strong> {{ $p->dataEntry->nama_lengkap }}</p>
                                    <p style="margin:4px 0 0;font-size:13px;"><strong>Nominal:</strong> Rp {{ number_format($p->nominal, 0, ',', '.') }}</p>
                                    <p style="margin:4px 0 0;font-size:13px;"><strong>Tagihan:</strong> {{ $p->penagihans->count() }} tagihan akan ditandai Dibayar</p>
                                </div>
                            </div>
                            <div class="adm-field">
                                <label class="adm-label" for="catatan_setujui_{{ $p->id }}">
                                    Catatan <span style="font-weight:400;color:var(--adm-text-muted);">(opsional)</span>
                                </label>
                                <textarea name="catatan_admin" id="catatan_setujui_{{ $p->id }}"
                                    class="adm-textarea" rows="3"
                                    placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="adm-btn-primary"
                                style="background:linear-gradient(135deg,var(--adm-green),#15803d);">
                                <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                Setujui & Bayar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Tolak --}}
        <div class="modal fade" id="modalTolak{{ $p->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('superadmin.penarikan-saldo.tolak', $p) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <svg viewBox="0 0 24 24" style="width:18px;height:18px;stroke:var(--adm-red);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;margin-right:6px;vertical-align:-3px;">
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
                                    <p style="margin:0;font-size:13px;"><strong>Data Entry:</strong> {{ $p->dataEntry->nama_lengkap }}</p>
                                    <p style="margin:4px 0 0;font-size:13px;"><strong>Nominal:</strong> Rp {{ number_format($p->nominal, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <div class="adm-field">
                                <label class="adm-label" for="catatan_tolak_{{ $p->id }}">
                                    Alasan Penolakan <span class="req">*</span>
                                </label>
                                <textarea name="catatan_admin" id="catatan_tolak_{{ $p->id }}"
                                    class="adm-textarea" rows="3"
                                    placeholder="Masukkan alasan penolakan..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="adm-btn-primary"
                                style="background:linear-gradient(135deg,var(--adm-red),#b91c1c);">
                                <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                Tolak Penarikan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @endif
@endforeach

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

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        new bootstrap.Tooltip(el);
    });
});
</script>

@endsection
