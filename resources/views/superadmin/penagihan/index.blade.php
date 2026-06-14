@extends('layouts.app')

@section('template_title')
    Manajemen Penagihan
@endsection

@section('content')
<div class="adm-page">
    @include('layouts.messages')

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Manajemen Penagihan</h1>
            <p>Kelola tagihan data entry — approve atau tolak pengajuan pembayaran</p>
        </div>
    </div>

    {{-- ── STAT CARDS ── --}}
    <div class="adm-stats">

        <div class="adm-stat">
            <div class="adm-stat-label">Pending</div>
            <div class="adm-stat-value is-warn counter-value" data-target="{{ $totalMenunggu }}">0</div>
            <div class="adm-stat-sub">Menunggu persetujuan</div>
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
            <div class="adm-stat-label">Total Dibayar</div>
            <div class="adm-stat-value counter-value" data-target="{{ $totalDibayar }}" data-prefix="Rp ">Rp 0</div>
            <div class="adm-stat-sub">Sudah terbayar</div>
        </div>

    </div>

    {{-- ── TABEL PENAGIHAN ── --}}
    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                Daftar Tagihan Data Entry
                <span class="adm-count-badge">{{ $penagihans->total() }}</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th style="width:44px">#</th>
                        <th>Nama Data Entry</th>
                        <th>Tgl Tagihan</th>
                        <th class="tc">Jml Data</th>
                        <th class="tc">Jml Paket</th>
                        <th class="tr">Nominal</th>
                        <th class="tc">Status</th>
                        <th class="tc">Tgl Dibayar</th>
                        <th class="tc" style="width:140px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($penagihans as $index => $penagihan)
                        <tr>
                            <td><span class="adm-rownum">{{ $penagihans->firstItem() + $index }}</span></td>
                            <td>
                                <div class="adm-name-cell">
                                    <div class="adm-avatar" style="background:var(--adm-blue-lt);color:var(--adm-blue);">
                                        {{ strtoupper(substr($penagihan->dataEntry->nama_lengkap, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:600;font-size:13px;">{{ $penagihan->dataEntry->nama_lengkap }}</div>
                                        <div style="font-size:11.5px;color:var(--adm-text-muted);">{{ $penagihan->dataEntry->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="adm-mono" style="font-size:12.5px;">
                                {{ $penagihan->tanggal_tagihan->format('d M Y') }}
                            </td>
                            <td class="tc">
                                <span class="adm-badge adm-badge-info">{{ $penagihan->jumlah_data }} Data</span>
                            </td>
                            <td class="tc">
                                <span class="adm-badge" style="background:#F0F9FF;color:#0369A1;border:1px solid #BAE6FD;">
                                    {{ $penagihan->jumlah_paket }}x Paket
                                </span>
                            </td>
                            <td class="tr adm-mono" style="font-weight:700;color:var(--adm-green);font-size:13px;">
                                Rp {{ number_format($penagihan->nominal, 0, ',', '.') }}
                            </td>
                            <td class="tc">
                                @switch($penagihan->status)
                                    @case('Menunggu')
                                        <span class="adm-badge adm-badge-pending"><span class="dot"></span>Menunggu</span>
                                        @break
                                    @case('Diproses')
                                        <span class="adm-badge adm-badge-info"><span class="dot"></span>Diproses</span>
                                        @break
                                    @case('Dibayar')
                                        <span class="adm-badge adm-badge-success"><span class="dot"></span>Dibayar</span>
                                        @break
                                    @case('Ditolak')
                                        <span class="adm-badge adm-badge-danger"><span class="dot"></span>Ditolak</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="tc adm-mono" style="font-size:12px;">
                                @if ($penagihan->tanggal_dibayar)
                                    {{ $penagihan->tanggal_dibayar->format('d M Y') }}
                                @else
                                    <span style="color:var(--adm-text-faint);">—</span>
                                @endif
                            </td>
                            <td class="tc">
                                @if (in_array($penagihan->status, ['Menunggu', 'Diproses']))
                                    <div class="adm-actions" style="justify-content:center;gap:5px;">
                                        <button type="button" class="adm-btn success"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalApprove{{ $penagihan->id }}"
                                            style="font-size:11.5px;padding:5px 10px;">
                                            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                            Approve
                                        </button>
                                        <button type="button" class="adm-btn danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalTolak{{ $penagihan->id }}"
                                            style="font-size:11.5px;padding:5px 10px;">
                                            <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                            Tolak
                                        </button>
                                    </div>
                                @else
                                    @if ($penagihan->catatan)
                                        <span data-bs-toggle="tooltip" title="{{ $penagihan->catatan }}"
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
                            <td colspan="9">
                                <div class="adm-empty">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                    </svg>
                                    <p>Belum ada tagihan masuk.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="adm-card-footer">
            <span class="adm-footer-info">
                Menampilkan {{ $penagihans->firstItem() ?? 0 }}–{{ $penagihans->lastItem() ?? 0 }}
                dari {{ $penagihans->total() }} tagihan
            </span>
            @include('layouts.pagination', ['paginator' => $penagihans])
        </div>
    </div>

</div>{{-- /adm-page --}}

{{-- ══ MODALS APPROVE & TOLAK ══ --}}
@foreach ($penagihans as $penagihan)
    @if (in_array($penagihan->status, ['Menunggu', 'Diproses']))

        {{-- Modal Approve --}}
        <div class="modal fade" id="modalApprove{{ $penagihan->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route($routePrefix . '.penagihan.approve', $penagihan) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <svg viewBox="0 0 24 24" style="width:18px;height:18px;stroke:var(--adm-green);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;margin-right:6px;vertical-align:-3px;">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                                Approve Tagihan
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" style="padding:20px 24px;">
                            <div class="adm-alert adm-alert-success" style="margin-bottom:16px;">
                                <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                <div>
                                    <p style="margin:0;font-size:13px;"><strong>Data Entry:</strong> {{ $penagihan->dataEntry->nama_lengkap }}</p>
                                    <p style="margin:4px 0 0;font-size:13px;"><strong>Jumlah Data:</strong> {{ $penagihan->jumlah_data }} data ({{ $penagihan->jumlah_paket }} paket)</p>
                                    <p style="margin:4px 0 0;font-size:13px;"><strong>Nominal:</strong> Rp {{ number_format($penagihan->nominal, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <div class="adm-field">
                                <label class="adm-label" for="catatan_approve_{{ $penagihan->id }}">
                                    Catatan <span style="font-weight:400;color:var(--adm-text-muted);">(opsional)</span>
                                </label>
                                <textarea name="catatan" id="catatan_approve_{{ $penagihan->id }}"
                                    class="adm-textarea" rows="3"
                                    placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="adm-btn-primary"
                                style="background:linear-gradient(135deg,var(--adm-green),#15803d);">
                                <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                Konfirmasi Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Tolak --}}
        <div class="modal fade" id="modalTolak{{ $penagihan->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route($routePrefix . '.penagihan.tolak', $penagihan) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <svg viewBox="0 0 24 24" style="width:18px;height:18px;stroke:var(--adm-red);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;margin-right:6px;vertical-align:-3px;">
                                    <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                                </svg>
                                Tolak Tagihan
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" style="padding:20px 24px;">
                            <div class="adm-alert adm-alert-danger" style="margin-bottom:16px;">
                                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                <div>
                                    <p style="margin:0;font-size:13px;"><strong>Data Entry:</strong> {{ $penagihan->dataEntry->nama_lengkap }}</p>
                                    <p style="margin:4px 0 0;font-size:13px;"><strong>Nominal:</strong> Rp {{ number_format($penagihan->nominal, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <div class="adm-field">
                                <label class="adm-label" for="catatan_tolak_{{ $penagihan->id }}">
                                    Alasan Penolakan <span class="req">*</span>
                                </label>
                                <textarea name="catatan" id="catatan_tolak_{{ $penagihan->id }}"
                                    class="adm-textarea" rows="3"
                                    placeholder="Masukkan alasan penolakan..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="adm-btn-primary"
                                style="background:linear-gradient(135deg,var(--adm-red),#b91c1c);">
                                <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                Tolak Tagihan
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
        // Counter animation — support prefix "Rp "
        document.querySelectorAll('.counter-value').forEach(function (el) {
            const target = parseFloat(el.dataset.target) || 0;
            const prefix = el.dataset.prefix || '';
            const duration = 900;
            const steps = Math.ceil(duration / 16);
            const inc = target / steps;
            let current = 0;
            const timer = setInterval(function () {
                current = Math.min(current + inc, target);
                el.textContent = prefix + Math.round(current).toLocaleString('id-ID');
                if (current >= target) clearInterval(timer);
            }, 16);
        });

        // Bootstrap Tooltip init
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
            new bootstrap.Tooltip(el);
        });
    });
</script>

@endsection
