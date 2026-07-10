@extends('layouts.app')

@section('template_title')
    Riwayat Tagihan Data Entry
@endsection

@section('content')
<div class="adm-page">
    @include('layouts.messages')

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Riwayat Tagihan Data Entry</h1>
            <p>Tagihan dibuat otomatis setiap 15 data diterima. Pencairan melalui menu
                <a href="{{ route('superadmin.penarikan-saldo.index') }}" style="color:var(--adm-blue);font-weight:600;">Penarikan Saldo</a>.
            </p>
        </div>
        <div class="adm-header-right">
            <a href="{{ route('superadmin.penarikan-saldo.index') }}" class="adm-btn-primary" style="text-decoration:none;">
                <svg viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                Kelola Penarikan Saldo
            </a>
        </div>
    </div>

    {{-- ── STAT CARDS ── --}}
    <div class="adm-stats">

        <div class="adm-stat">
            <div class="adm-stat-label">Belum Diajukan</div>
            <div class="adm-stat-value is-warn counter-value" data-target="{{ $totalCountBelumDiajukan }}">0</div>
            <div class="adm-stat-sub">Tagihan menunggu diajukan DE</div>
        </div>

        <div class="adm-stat">
            <div class="adm-stat-label">Nominal Belum Diajukan</div>
            <div class="adm-stat-value counter-value" style="color:var(--adm-blue);font-size:18px;"
                data-target="{{ $totalNominalBelumDiajukan }}" data-prefix="Rp ">Rp 0</div>
            <div class="adm-stat-sub">Saldo menunggu penarikan</div>
        </div>

        <div class="adm-stat">
            <div class="adm-stat-label">Total Tagihan</div>
            <div class="adm-stat-value counter-value" data-target="{{ $penagihans->total() }}">0</div>
            <div class="adm-stat-sub">Semua tagihan</div>
        </div>

        <div class="adm-stat is-accent">
            <div class="adm-stat-label">Total Dibayar</div>
            <div class="adm-stat-value counter-value" data-target="{{ $totalDibayar }}" data-prefix="Rp ">Rp 0</div>
            <div class="adm-stat-sub">Sudah dicairkan</div>
        </div>

    </div>

    {{-- ── TABEL TAGIHAN ── --}}
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
                        <th class="tc">Status Tagihan</th>
                        <th class="tc">Status Penarikan</th>
                        <th class="tc">Tgl Dibayar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($penagihans as $index => $penagihan)
                        @php
                            // Cek apakah tagihan ini sudah masuk penarikan aktif/selesai
                            $penarikanAktif = $penagihan->penarikan
                                ->whereIn('status', ['Menunggu', 'Diproses', 'Disetujui'])
                                ->first();
                        @endphp
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
                            {{-- Status Tagihan --}}
                            <td class="tc">
                                @switch($penagihan->status)
                                    @case('Menunggu')
                                        <span class="adm-badge adm-badge-pending"><span class="dot"></span>Belum Ditarik</span>
                                        @break
                                    @case('Dibayar')
                                        <span class="adm-badge adm-badge-success"><span class="dot"></span>Dibayar</span>
                                        @break
                                    @case('Ditolak')
                                        <span class="adm-badge adm-badge-danger"><span class="dot"></span>Ditolak</span>
                                        @break
                                    @default
                                        <span class="adm-badge">{{ $penagihan->status }}</span>
                                @endswitch
                            </td>
                            {{-- Status Penarikan --}}
                            <td class="tc">
                                @if($penagihan->status === 'Dibayar')
                                    <span class="adm-badge adm-badge-success" style="font-size:11px;">
                                        <span class="dot"></span>Sudah Dicairkan
                                    </span>
                                @elseif($penarikanAktif)
                                    @switch($penarikanAktif->status)
                                        @case('Menunggu')
                                            <span class="adm-badge adm-badge-pending" style="font-size:11px;">
                                                <span class="dot"></span>Diajukan
                                            </span>
                                            @break
                                        @case('Diproses')
                                            <span class="adm-badge adm-badge-info" style="font-size:11px;">
                                                <span class="dot"></span>Diproses
                                            </span>
                                            @break
                                        @case('Disetujui')
                                            <span class="adm-badge adm-badge-success" style="font-size:11px;">
                                                <span class="dot"></span>Disetujui
                                            </span>
                                            @break
                                    @endswitch
                                @else
                                    <span style="color:var(--adm-text-faint);font-size:12px;">Belum diajukan</span>
                                @endif
                            </td>
                            <td class="tc adm-mono" style="font-size:12px;">
                                @if ($penagihan->tanggal_dibayar)
                                    {{ $penagihan->tanggal_dibayar->format('d M Y') }}
                                @else
                                    <span style="color:var(--adm-text-faint);">—</span>
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
    });
</script>

@endsection
