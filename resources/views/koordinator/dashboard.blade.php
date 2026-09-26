@extends('layouts.app')
@section('template_title') Dashboard Koordinator @endsection

@section('content')
<div class="adm-page">
    @include('layouts.messages')

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Selamat Datang, {{ $koordinator->nama_lengkap }}</h1>
            <p>Ringkasan performa enumerator di bawah koordinasi Anda</p>
        </div>
    </div>

    <div class="adm-stats">
        <div class="adm-stat is-accent">
            <div class="adm-stat-label">Total Enumerator</div>
            <div class="adm-stat-value">{{ $stats['totalEnumerator'] }}</div>
            <div class="adm-stat-sub">Terdaftar di bawah Anda</div>
        </div>
        <div class="adm-stat">
            <div class="adm-stat-label">Enumerator Aktif</div>
            <div class="adm-stat-value is-success">{{ $stats['enumeratorAktif'] }}</div>
            <div class="adm-stat-sub">Status aktif</div>
        </div>
        <div class="adm-stat">
            <div class="adm-stat-label">Data Bulan Ini</div>
            <div class="adm-stat-value">{{ $stats['dataBulanIni'] }}</div>
            <div class="adm-stat-sub">{{ now()->isoFormat('MMMM YYYY') }}</div>
        </div>
        <div class="adm-stat">
            <div class="adm-stat-label">Total Terbit SH</div>
            <div class="adm-stat-value is-success">{{ $stats['dataTerbitSH'] }}</div>
            <div class="adm-stat-sub">Sudah terbit sertifikat</div>
        </div>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                KPI Enumerator Bulan Ini
            </div>
        </div>
        <div class="table-responsive">
            <table class="adm-table w-100">
                <thead>
                    <tr>
                        <th style="width:44px">#</th>
                        <th>Nama Enumerator</th>
                        <th class="tc">Status</th>
                        <th class="tc">Data Bulan Ini</th>
                        <th class="tc">Target</th>
                        <th style="width:180px">Progress</th>
                        <th class="tc">KPI</th>
                        <th class="tc">Terbit SH</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($enumerators as $idx => $e)
                        <tr>
                            <td><span class="adm-rownum">{{ $idx + 1 }}</span></td>
                            <td style="font-weight:600;">{{ $e->nama_lengkap }}</td>
                            <td class="tc">
                                @if ($e->status === 'Aktif')
                                    <span class="adm-badge adm-badge-success">Aktif</span>
                                @else
                                    <span class="adm-badge adm-badge-nonaktif">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="tc" style="font-weight:600;">{{ $e->data_bulan_ini }}</td>
                            <td class="tc" style="color:var(--adm-text-muted);">{{ $e->target_bulanan }}</td>
                            <td>
                                <div class="ko-progress-track">
                                    <div class="ko-progress-fill {{ $e->progress_percent >= 100 ? 'is-done' : '' }}" style="width:{{ $e->progress_percent }}%;"></div>
                                </div>
                                <span style="font-size:11px;color:var(--adm-text-muted);">{{ $e->progress_percent }}%</span>
                            </td>
                            <td class="tc">
                                @if ($e->progress_percent >= 100)
                                    <span class="adm-badge adm-badge-success">Tercapai</span>
                                @else
                                    <span class="adm-badge adm-badge-pending">Belum</span>
                                @endif
                            </td>
                            <td class="tc">{{ $e->terbit_sh_bulan_ini }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="adm-empty">
                                    <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                    <p>Belum ada enumerator di bawah koordinasi Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .ko-progress-track { width: 100%; height: 8px; border-radius: 6px; background: var(--adm-bg-input); overflow: hidden; margin-bottom: 3px; }
    .ko-progress-fill { height: 100%; border-radius: 6px; background: var(--adm-blue); transition: width .3s; }
    .ko-progress-fill.is-done { background: var(--adm-green); }
</style>
@endpush
