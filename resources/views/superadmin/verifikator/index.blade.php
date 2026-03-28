@extends('layouts.app')

@section('template_title')
    Verifikators
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                @include('layouts.messages')
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">{{ __('Verifikators') }}</span>
                            <div class="float-right">
                                <a href="{{ route('superadmin.verifikators.create') }}"
                                    class="btn btn-primary btn-sm float-right">
                                    {{ __('Create New') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Search --}}
                    <div class="card-body bg-white border-bottom">
                        <form id="searchForm">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label for="search" class="form-label">Cari Nama Verifikator</label>
                                    <input type="text" class="form-control" id="search" name="search"
                                        placeholder="Cari berdasarkan nama verifikator..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" id="resetBtn" class="btn btn-secondary w-100">
                                        <i class="las la-redo-alt"></i> Reset Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-body bg-white">
                        {{-- Loading --}}
                        <div id="tableLoading" class="text-center py-5" style="display: none;">
                            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3 text-muted fw-bold">SABAR BOS...</p>
                        </div>

                        {{-- Table --}}
                        <div id="tableWrapper">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="thead">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Lengkap</th>
                                            <th>Telephone</th>
                                            <th>Alamat</th>
                                            <th>Rate / Data</th>
                                            <th class="text-center">Pending</th>
                                            <th class="text-center">Total Pending</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        @forelse ($verifikators as $verifikator)
                                            <tr>
                                                <td>{{ ++$i }}</td>
                                                <td><strong>{{ $verifikator->nama_lengkap }}</strong></td>
                                                <td>{{ $verifikator->telephone ?? '-' }}</td>
                                                <td style="max-width:160px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"
                                                    title="{{ $verifikator->alamat_lengkap }}">
                                                    {{ $verifikator->alamat_lengkap ?? '-' }}
                                                </td>
                                                <td>Rp {{ number_format($verifikator->rate_per_data, 0, ',', '.') }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-warning text-dark">
                                                        {{ $verifikator->total_belum_dibayar }} Data
                                                    </span>
                                                    @if ($verifikator->jumlah_belum_dibayar_progress > 0)
                                                        <br>
                                                        <small class="text-muted" style="font-size:10px;">
                                                            {{ $verifikator->jumlah_belum_dibayar }} lapangan
                                                            + {{ $verifikator->jumlah_belum_dibayar_progress }} progress
                                                        </small>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @php
                                                        $totalNominalGabungan =
                                                            $verifikator->total_belum_dibayar *
                                                            $verifikator->rate_per_data;
                                                    @endphp
                                                    @if ($totalNominalGabungan > 0)
                                                        <strong class="text-success">
                                                            Rp {{ number_format($totalNominalGabungan, 0, ',', '.') }}
                                                        </strong>
                                                    @else
                                                        <span class="text-muted">Rp 0</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex gap-1 justify-content-center flex-wrap">

                                                        {{-- BAYAR --}}
                                                        @if ($verifikator->total_belum_dibayar > 0)
                                                            <button type="button"
                                                                class="btn btn-sm btn-primary btn-open-bayar"
                                                                data-id="{{ $verifikator->id }}"
                                                                data-nama="{{ $verifikator->nama_lengkap }}"
                                                                data-jumlah="{{ $verifikator->total_belum_dibayar }}"
                                                                data-rate="Rp {{ number_format($verifikator->rate_per_data, 0, ',', '.') }}"
                                                                data-total="Rp {{ number_format($verifikator->total_belum_dibayar * $verifikator->rate_per_data, 0, ',', '.') }}"
                                                                data-action="{{ route('superadmin.verifikators.bayar', $verifikator->id) }}">
                                                                <i class="las la-money-bill-wave"></i> Bayar
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-sm btn-light"
                                                                title="Bayar" disabled>
                                                                <i class="las la-money-bill-wave"></i>
                                                            </button>
                                                        @endif

                                                        <a class="btn btn-sm btn-success"
                                                            href="{{ route('superadmin.verifikators.edit', $verifikator->id) }}"
                                                            title="Edit">
                                                            <i class="las la-edit"></i>
                                                        </a>

                                                        <button type="button" class="btn btn-sm btn-warning btn-kalkulasi"
                                                            data-id="{{ $verifikator->id }}"
                                                            data-nama="{{ $verifikator->nama_lengkap }}"
                                                            data-url="{{ route('superadmin.verifikators.kalkulasi', $verifikator->id) }}"
                                                            title="Kalkulasi">
                                                            <i class="las la-calculator"></i>
                                                        </button>

                                                        <button type="button"
                                                            class="btn btn-sm btn-secondary btn-open-riwayat"
                                                            data-id="{{ $verifikator->id }}"
                                                            data-nama="{{ $verifikator->nama_lengkap }}"
                                                            data-payments='@json($verifikator->verifikatorPayments->sortByDesc('paid_at')->values())' title="Riwayat">
                                                            <i class="las la-history"></i>
                                                        </button>

                                                        <form
                                                            action="{{ route('superadmin.verifikators.destroy', $verifikator->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                onclick="return confirm('Yakin hapus verifikator ini?')"
                                                                title="Hapus">
                                                                <i class="las la-trash"></i>
                                                            </button>
                                                        </form>

                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-5 text-muted">
                                                    <i class="las la-inbox"
                                                        style="font-size:40px; display:block; margin-bottom:8px;"></i>
                                                    <p class="mb-0 small">{{ __('No data available') }}</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div id="paginationWrapper">
                                @include('layouts.pagination', ['paginator' => $verifikators])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         MODAL BAYAR — satu modal, diisi JS
    ══════════════════════════════════════ --}}
    <div id="modalBayar" class="modal fade" tabindex="-1" aria-labelledby="modalBayarLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white fw-bold" id="modalBayarLabel">
                        <i class="las la-money-bill-wave me-1"></i> Konfirmasi Pembayaran
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">Periksa detail pembayaran sebelum dikonfirmasi.</p>

                    <table class="table table-sm table-bordered mb-3">
                        <tr>
                            <th class="bg-light" style="width:45%">Nama Verifikator</th>
                            <td id="bayar-nama" class="fw-semibold">-</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Jumlah Data</th>
                            <td id="bayar-jumlah">-</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Rate Per Data</th>
                            <td id="bayar-rate">-</td>
                        </tr>
                    </table>

                    <div class="alert alert-success d-flex justify-content-between align-items-center mb-3 py-3">
                        <span class="fw-bold">
                            <i class="las la-coins me-1"></i> Total Dibayarkan
                        </span>
                        <span class="fs-5 fw-bold" id="bayar-total">-</span>
                    </div>

                    <div class="alert alert-warning small mb-0 d-flex align-items-start gap-2">
                        <i class="las la-exclamation-triangle mt-1 flex-shrink-0"></i>
                        <span>Setelah dikonfirmasi, kalkulasi pending akan <strong>direset ke 0</strong> dan tidak dapat
                            diurungkan.</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="las la-times"></i> Batal
                    </button>
                    <form id="formBayar" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="las la-check-circle"></i> Konfirmasi Bayar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         MODAL RIWAYAT — satu modal, diisi JS
    ══════════════════════════════════════ --}}
    <div id="modalRiwayat" class="modal fade" tabindex="-1" aria-labelledby="modalRiwayatLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalRiwayatLabel">
                        <i class="las la-history me-1 text-muted"></i>
                        Riwayat Pembayaran — <span id="riwayat-nama" class="text-primary"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="riwayat-body"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="las la-times"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         MODAL KALKULASI — shared AJAX
    ══════════════════════════════════════ --}}
    <div id="kalkulasiModal" class="modal fade" tabindex="-1" aria-labelledby="kalkulasiModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="kalkulasiModalLabel">
                        <i class="las la-calculator me-1 text-warning"></i>
                        Kalkulasi — <span id="kalkulasiNama" class="text-warning"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="kalkulasiLoading" class="text-center py-5">
                        <div class="spinner-border text-warning" role="status" style="width:2.5rem; height:2.5rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted small fw-bold">Memuat data kalkulasi...</p>
                    </div>
                    <div id="kalkulasiContent" style="display:none;">

                        {{-- Summary --}}
                        <div class="row g-3 mb-4">
                            <div class="col-6 col-md-2">
                                <div class="card border text-center h-100">
                                    <div class="card-body py-3">
                                        <p class="text-muted mb-1"
                                            style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">
                                            Rate / Data</p>
                                        <p class="mb-0 fw-bold" id="kalk-rate"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-2">
                                <div class="card border text-center h-100">
                                    <div class="card-body py-3">
                                        <p class="text-muted mb-1"
                                            style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">
                                            Total Lapangan</p>
                                        <p class="mb-0 fw-bold fs-5" id="kalk-total"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-2">
                                <div class="card border border-warning text-center h-100"
                                    style="background:rgba(255,193,7,.08);">
                                    <div class="card-body py-3">
                                        <p class="text-muted mb-1"
                                            style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">
                                            Pending Lapangan</p>
                                        <p class="mb-0 fw-bold fs-5 text-warning" id="kalk-pending-lapangan"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-2">
                                <div class="card border border-info text-center h-100"
                                    style="background:rgba(13,202,240,.08);">
                                    <div class="card-body py-3">
                                        <p class="text-muted mb-1"
                                            style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">
                                            Pending Progress</p>
                                        <p class="mb-0 fw-bold fs-5 text-info" id="kalk-pending-progress"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-2">
                                <div class="card border border-success text-center h-100"
                                    style="background:rgba(25,135,84,.08);">
                                    <div class="card-body py-3">
                                        <p class="text-muted mb-1"
                                            style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">
                                            Total Pending</p>
                                        <p class="mb-0 fw-bold text-success" id="kalk-pending"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-2">
                                <div class="card border border-success text-center h-100"
                                    style="background:rgba(25,135,84,.08);">
                                    <div class="card-body py-3">
                                        <p class="text-muted mb-1"
                                            style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">
                                            Total Nominal</p>
                                        <p class="mb-0 fw-bold text-success" id="kalk-nominal"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tab Data Lapangan vs Progress --}}
                        <ul class="nav nav-tabs mb-3" id="kalkTabs">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-lapangan-link" href="#"
                                    onclick="switchKalkTab('lapangan'); return false;">
                                    <i class="las la-map-marker me-1"></i>Data Lapangan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-progress-link" href="#"
                                    onclick="switchKalkTab('progress'); return false;">
                                    <i class="las la-tasks me-1"></i>Data Entry Progress
                                </a>
                            </li>
                        </ul>

                        {{-- Filter --}}
                        <div class="d-flex justify-content-end mb-3">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-secondary btn-filter-kalk active"
                                    data-filter="semua">Semua</button>
                                <button class="btn btn-outline-warning btn-filter-kalk" data-filter="pending">Belum
                                    Dibayar</button>
                                <button class="btn btn-outline-success btn-filter-kalk" data-filter="lunas">Sudah
                                    Dibayar</button>
                            </div>
                        </div>

                        {{-- Tab: Data Lapangan --}}
                        <div id="kalkTabLapangan">
                            <p class="text-muted text-uppercase fw-bold mb-2"
                                style="font-size:11px; letter-spacing:.06em;">
                                <i class="las la-calendar-alt me-1"></i>Rekap Per Bulan — Data Lapangan
                            </p>
                            <div class="table-responsive mb-4">
                                <table class="table table-sm table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Bulan</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">Lunas</th>
                                            <th class="text-center">Pending</th>
                                            <th class="text-end">Nominal Pending</th>
                                        </tr>
                                    </thead>
                                    <tbody id="kalk-rekap-body"></tbody>
                                </table>
                            </div>

                            <p class="text-muted text-uppercase fw-bold mb-2"
                                style="font-size:11px; letter-spacing:.06em;">
                                <i class="las la-list me-1"></i>Detail Data Lapangan
                            </p>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama PU</th>
                                            <th>Tgl Verifikasi</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="kalk-data-body"></tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small id="kalk-pagination-info" class="text-muted"></small>
                                <div id="kalk-pagination-buttons" class="d-flex gap-1"></div>
                            </div>
                        </div>

                        {{-- Tab: Data Entry Progress --}}
                        <div id="kalkTabProgress" style="display:none;">
                            <p class="text-muted text-uppercase fw-bold mb-2"
                                style="font-size:11px; letter-spacing:.06em;">
                                <i class="las la-calendar-alt me-1"></i>Rekap Per Bulan — Data Entry Progress
                            </p>
                            <div class="table-responsive mb-4">
                                <table class="table table-sm table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Bulan</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">Lunas</th>
                                            <th class="text-center">Pending</th>
                                            <th class="text-end">Nominal Pending</th>
                                        </tr>
                                    </thead>
                                    <tbody id="kalk-rekap-progress-body"></tbody>
                                </table>
                            </div>

                            <p class="text-muted text-uppercase fw-bold mb-2"
                                style="font-size:11px; letter-spacing:.06em;">
                                <i class="las la-list me-1"></i>Detail Data Entry Progress
                            </p>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama PU</th>
                                            <th>Data Entry</th>
                                            <th>Type</th>
                                            <th>Tgl Verifikasi</th>
                                            <th class="text-center">Status Bayar</th>
                                        </tr>
                                    </thead>
                                    <tbody id="kalk-progress-body"></tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small id="kalk-pagination-progress-info" class="text-muted"></small>
                                <div id="kalk-pagination-progress-buttons" class="d-flex gap-1"></div>
                            </div>
                        </div>

                    </div>{{-- /kalkulasiContent --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="las la-times"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ── kalkState global agar switchKalkTab bisa akses ────────────────────────
        const kalkState = {
            url: '',
            filter: 'semua',
            pageLapangan: 1,
            pageProgress: 1,
            activeTab: 'lapangan',
        };

        function switchKalkTab(tab) {
            kalkState.activeTab = tab;
            document.getElementById('kalkTabLapangan').style.display = tab === 'lapangan' ? 'block' : 'none';
            document.getElementById('kalkTabProgress').style.display = tab === 'progress' ? 'block' : 'none';
            document.getElementById('tab-lapangan-link').classList.toggle('active', tab === 'lapangan');
            document.getElementById('tab-progress-link').classList.toggle('active', tab === 'progress');
        }

        document.addEventListener('DOMContentLoaded', function() {

            // ── Helpers ───────────────────────────────────────────────────────────
            const rupiah = n => 'Rp ' + Number(n).toLocaleString('id-ID');

            const formatTanggal = str => {
                if (!str) return '-';
                const d = new Date(str);
                if (isNaN(d)) return str;
                return d.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
            };

            function openModal(id) {
                const el = document.getElementById(id);
                if (!el) return;
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(el).show();
                    return;
                }
                if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
                    $(el).modal('show');
                    return;
                }
                el.style.display = 'block';
                el.classList.add('show');
                document.body.classList.add('modal-open');
                const bd = document.createElement('div');
                bd.className = 'modal-backdrop fade show';
                bd.id = 'manual-backdrop';
                document.body.appendChild(bd);
            }

            function closeModal(id) {
                const el = document.getElementById(id);
                if (!el) return;
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const m = bootstrap.Modal.getInstance(el);
                    if (m) m.hide();
                    return;
                }
                if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
                    $(el).modal('hide');
                    return;
                }
                el.style.display = 'none';
                el.classList.remove('show');
                document.body.classList.remove('modal-open');
                const bd = document.getElementById('manual-backdrop');
                if (bd) bd.remove();
            }

            document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const modal = this.closest('.modal');
                    if (modal) closeModal(modal.id);
                });
            });

            document.getElementById('resetBtn').addEventListener('click', function() {
                document.getElementById('search').value = '';
            });

            // ── Modal Bayar ───────────────────────────────────────────────────────
            document.querySelectorAll('.btn-open-bayar').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('bayar-nama').textContent = this.dataset.nama;
                    document.getElementById('bayar-jumlah').innerHTML =
                        `<span class="badge bg-warning text-dark">${this.dataset.jumlah} data</span>`;
                    document.getElementById('bayar-rate').textContent = this.dataset.rate;
                    document.getElementById('bayar-total').textContent = this.dataset.total;
                    document.getElementById('formBayar').action = this.dataset.action;
                    openModal('modalBayar');
                });
            });

            // ── Modal Riwayat ─────────────────────────────────────────────────────
            document.querySelectorAll('.btn-open-riwayat').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('riwayat-nama').textContent = this.dataset.nama;
                    let payments = [];
                    try {
                        payments = JSON.parse(this.dataset.payments);
                    } catch (e) {}
                    const body = document.getElementById('riwayat-body');

                    if (!payments.length) {
                        body.innerHTML = `
                        <div class="text-center py-5 text-muted">
                            <i class="las la-inbox" style="font-size:40px; display:block;"></i>
                            <p class="mt-2 mb-0 small">Belum ada riwayat pembayaran.</p>
                        </div>`;
                    } else {
                        let rows = '',
                            totalData = 0,
                            totalNominal = 0;
                        payments.forEach((p, idx) => {
                            const tgl = p.paid_at ?
                                new Date(p.paid_at).toLocaleDateString('id-ID', {
                                    day: '2-digit',
                                    month: 'short',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                }) :
                                '-';
                            totalData += parseInt(p.jumlah_data) || 0;
                            totalNominal += parseFloat(p.total_nominal) || 0;
                            rows += `
                            <tr>
                                <td>${idx + 1}</td>
                                <td>${tgl}</td>
                                <td class="text-center"><span class="badge bg-success">${p.jumlah_data} data</span></td>
                                <td class="text-end fw-bold text-success">${rupiah(p.total_nominal)}</td>
                            </tr>`;
                        });
                        body.innerHTML = `
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th><th>Tanggal Bayar</th>
                                        <th class="text-center">Jumlah Data</th>
                                        <th class="text-end">Total Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>${rows}</tbody>
                                <tfoot class="table-success">
                                    <tr>
                                        <td colspan="2" class="fw-bold">Total Keseluruhan</td>
                                        <td class="text-center fw-bold">${totalData} data</td>
                                        <td class="text-end fw-bold text-success">${rupiah(totalNominal)}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>`;
                    }
                    openModal('modalRiwayat');
                });
            });

            // ── Modal Kalkulasi ───────────────────────────────────────────────────
            document.querySelectorAll('.btn-kalkulasi').forEach(btn => {
                btn.addEventListener('click', function() {
                    kalkState.url = this.dataset.url;
                    kalkState.filter = 'semua';
                    kalkState.pageLapangan = 1;
                    kalkState.pageProgress = 1;
                    kalkState.activeTab = 'lapangan';
                    document.getElementById('kalkulasiNama').textContent = this.dataset.nama;
                    setKalkFilter('semua');
                    switchKalkTab('lapangan');
                    loadKalkulasi();
                    openModal('kalkulasiModal');
                });
            });

            document.querySelectorAll('.btn-filter-kalk').forEach(btn => {
                btn.addEventListener('click', function() {
                    kalkState.filter = this.dataset.filter;
                    kalkState.pageLapangan = 1;
                    kalkState.pageProgress = 1;
                    setKalkFilter(this.dataset.filter);
                    loadKalkulasi();
                });
            });

            function setKalkFilter(active) {
                document.querySelectorAll('.btn-filter-kalk').forEach(b => {
                    b.classList.toggle('active', b.dataset.filter === active);
                });
            }

            function loadKalkulasi() {
                document.getElementById('kalkulasiLoading').style.display = 'block';
                document.getElementById('kalkulasiContent').style.display = 'none';

                const params = new URLSearchParams({
                    filter: kalkState.filter,
                    page_lapangan: kalkState.pageLapangan,
                    page_progress: kalkState.pageProgress,
                });

                fetch(`${kalkState.url}?${params}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        renderKalkSummary(data.summary);
                        renderKalkRekap(data.rekap);
                        renderKalkData(data.dataLapangans);
                        renderKalkPagination(data.dataLapangans);
                        renderKalkRekapProgress(data.rekapProgress);
                        renderKalkProgress(data.dataProgress);
                        renderKalkProgressPagination(data.dataProgress);
                        document.getElementById('kalkulasiLoading').style.display = 'none';
                        document.getElementById('kalkulasiContent').style.display = 'block';
                    })
                    .catch(() => {
                        document.getElementById('kalkulasiLoading').innerHTML =
                            '<p class="text-danger text-center mt-4"><i class="las la-exclamation-circle"></i> Gagal memuat data.</p>';
                    });
            }

            function renderKalkSummary(s) {
                document.getElementById('kalk-rate').textContent = rupiah(s.rate_per_data);
                document.getElementById('kalk-total').textContent = s.total_data + ' data';
                document.getElementById('kalk-pending-lapangan').textContent = s.belum_dibayar_lapangan + ' data';
                document.getElementById('kalk-pending-progress').textContent = s.belum_dibayar_progress + ' data';
                document.getElementById('kalk-pending').textContent = s.belum_dibayar + ' data';
                document.getElementById('kalk-nominal').textContent = rupiah(s.total_nominal);
            }

            function renderKalkRekap(rekap) {
                const tbody = document.getElementById('kalk-rekap-body');
                if (!rekap.length) {
                    tbody.innerHTML =
                        `<tr><td colspan="5" class="text-center text-muted py-3">Belum ada data.</td></tr>`;
                    return;
                }
                tbody.innerHTML = rekap.map(r => `
                <tr>
                    <td class="fw-semibold">${r.bulan_label}</td>
                    <td class="text-center">${r.total}</td>
                    <td class="text-center"><span class="badge bg-success">${r.sudah_dibayar}</span></td>
                    <td class="text-center"><span class="badge bg-warning text-dark">${r.belum_dibayar}</span></td>
                    <td class="text-end fw-semibold text-success">${rupiah(r.nominal_pending)}</td>
                </tr>`).join('');
            }

            function renderKalkRekapProgress(rekap) {
                const tbody = document.getElementById('kalk-rekap-progress-body');
                if (!rekap.length) {
                    tbody.innerHTML =
                        `<tr><td colspan="5" class="text-center text-muted py-3">Belum ada data.</td></tr>`;
                    return;
                }
                tbody.innerHTML = rekap.map(r => `
                <tr>
                    <td class="fw-semibold">${r.bulan_label ?? '-'}</td>
                    <td class="text-center">${r.total}</td>
                    <td class="text-center"><span class="badge bg-success">${r.sudah_dibayar}</span></td>
                    <td class="text-center"><span class="badge bg-warning text-dark">${r.belum_dibayar}</span></td>
                    <td class="text-end fw-semibold text-success">${rupiah(r.nominal_pending)}</td>
                </tr>`).join('');
            }

            function renderKalkData(paginator) {
                const tbody = document.getElementById('kalk-data-body');
                const data = paginator.data;
                if (!data.length) {
                    tbody.innerHTML =
                        `<tr><td colspan="4" class="text-center text-muted py-3">Tidak ada data.</td></tr>`;
                    return;
                }
                tbody.innerHTML = data.map((d, i) => `
                <tr>
                    <td class="text-muted">${(paginator.current_page - 1) * paginator.per_page + i + 1}</td>
                    <td>${d.nama_pu ?? '-'}</td>
                    <td class="text-muted">${formatTanggal(d.tanggal_verifikasi)}</td>
                    <td class="text-center">
                        ${d.payment_id
                            ? '<span class="badge bg-success">Sudah Dibayar</span>'
                            : '<span class="badge bg-warning text-dark">Belum Dibayar</span>'}
                    </td>
                </tr>`).join('');
            }

            function renderKalkProgress(paginator) {
                const tbody = document.getElementById('kalk-progress-body');
                const data = paginator.data;
                if (!data.length) {
                    tbody.innerHTML =
                        `<tr><td colspan="6" class="text-center text-muted py-3">Tidak ada data.</td></tr>`;
                    return;
                }
                tbody.innerHTML = data.map((d, i) => {
                    const entryType = d.data_entry?.entry_type ?? '-';
                    const badgeType = entryType === 'OSS' ?
                        '<span class="badge bg-info">OSS</span>' :
                        entryType === 'SIHALAL' ?
                        '<span class="badge bg-primary">SIHALAL</span>' :
                        '<span class="badge bg-secondary">-</span>';
                    return `
                <tr>
                    <td class="text-muted">${(paginator.current_page - 1) * paginator.per_page + i + 1}</td>
                    <td>${d.data_lapangan?.nama_pu ?? '-'}</td>
                    <td>${d.data_entry?.user?.name ?? '-'}</td>
                    <td>${badgeType}</td>
                    <td class="text-muted">${formatTanggal(d.tanggal_verifikasi)}</td>
                    <td class="text-center">
                        ${d.payment_id
                            ? '<span class="badge bg-success">Sudah Dibayar</span>'
                            : '<span class="badge bg-warning text-dark">Belum Dibayar</span>'}
                    </td>
                </tr>`;
                }).join('');
            }

            function renderKalkPagination(paginator) {
                document.getElementById('kalk-pagination-info').textContent =
                    `Menampilkan ${paginator.from ?? 0}–${paginator.to ?? 0} dari ${paginator.total} data`;
                buildPaginationButtons('kalk-pagination-buttons', paginator,
                    pg => {
                        kalkState.pageLapangan = pg;
                        loadKalkulasi();
                    });
            }

            function renderKalkProgressPagination(paginator) {
                document.getElementById('kalk-pagination-progress-info').textContent =
                    `Menampilkan ${paginator.from ?? 0}–${paginator.to ?? 0} dari ${paginator.total} data`;
                buildPaginationButtons('kalk-pagination-progress-buttons', paginator,
                    pg => {
                        kalkState.pageProgress = pg;
                        loadKalkulasi();
                    });
            }

            function buildPaginationButtons(containerId, paginator, onPageChange) {
                const btns = document.getElementById(containerId);
                btns.innerHTML = '';
                const mkBtn = (label, disabled, active, onClick) => {
                    const b = document.createElement('button');
                    b.className = 'btn btn-sm ' + (active ? 'btn-secondary' : 'btn-outline-secondary');
                    b.innerHTML = label;
                    b.disabled = disabled;
                    if (!disabled) b.onclick = onClick;
                    return b;
                };
                btns.appendChild(mkBtn('&laquo;', paginator.current_page <= 1, false,
                    () => onPageChange(paginator.current_page - 1)));
                const cur = paginator.current_page,
                    max = paginator.last_page;
                let start = Math.max(1, cur - 2),
                    end = Math.min(max, start + 4);
                if (end - start < 4) start = Math.max(1, end - 4);
                for (let p = start; p <= end; p++) {
                    btns.appendChild(mkBtn(p, false, p === cur, () => onPageChange(p)));
                }
                btns.appendChild(mkBtn('&raquo;', paginator.current_page >= max, false,
                    () => onPageChange(paginator.current_page + 1)));
            }

        }); // DOMContentLoaded
    </script>
@endsection
