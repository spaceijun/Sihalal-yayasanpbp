@extends('layouts.app')

@section('template_title')
    Data Lapangan
@endsection

@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        {{-- ── PAGE HEADER ── --}}
        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Data Lapangan</h1>
                <p>Kelola data lapangan pelaku usaha di lapangan secara real-time</p>
            </div>
            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                <button id="exportBtn" class="adm-btn success" style="gap:6px;">
                    <svg viewBox="0 0 24 24" style="stroke-width:2.2;">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="16" y1="13" x2="8" y2="13" />
                        <line x1="16" y1="17" x2="8" y2="17" />
                    </svg>
                    Export Excel
                </button>
                <a href="{{ route($routePrefix . '.data-lapangans.data-revisi') }}" class="adm-btn-secondary"
                    style="gap:6px;">
                    <svg viewBox="0 0 24 24">
                        <polyline points="1 4 1 10 7 10" />
                        <path d="M3.51 15a9 9 0 1 0 .49-3.5" />
                    </svg>
                    Data Revisi
                </a>
                @if ($routePrefix === 'superadmin')
                    <button id="btnOpenApproval" class="adm-btn"
                        style="gap:6px;background:linear-gradient(135deg,#1A5FC8,#0F3A8A);color:#fff;border:none;">
                        <svg viewBox="0 0 24 24"
                            style="width:15px;height:15px;fill:none;stroke:currentColor;stroke-width:2.2;">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                        Approval Pembayaran
                        <span id="pengajuanBadge"
                            style="display:none;background:#E11D48;color:#fff;border-radius:99px;font-size:10px;padding:1px 6px;font-weight:700;">0</span>
                    </button>
                @endif
                <a href="{{ route($routePrefix . '.data-lapangans.create') }}" class="adm-btn-primary" style="gap:6px;">
                    <svg viewBox="0 0 24 24">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Tambah
                </a>
            </div>
        </div>

        {{-- ── STATS / SUMMARY CARDS ── --}}
        <div class="adm-stats" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 20px;">
            {{-- TIDAK ADA PENGAJUAN --}}
            <div class="adm-stat" style="border-top: 4px solid #94A3B8; position: relative; overflow: hidden;">
                <div style="position: absolute; right: 12px; top: 12px; opacity: 0.08; color: #94A3B8;">
                    <svg viewBox="0 0 24 24"
                        style="width: 54px; height: 54px; fill: none; stroke: currentColor; stroke-width: 2.5;">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                </div>
                <div class="adm-stat-label" style="color: #64748B; font-weight: 700;">Belum Diajukan</div>
                <div class="adm-stat-value" style="color: #64748B; font-size: 28px;">{{ $paymentStats['tidak_ada_pengajuan_count'] }}</div>
                <div class="adm-stat-sub" style="font-weight: 600; color: var(--adm-text-mid);">
                    Rp {{ number_format($paymentStats['tidak_ada_pengajuan_total'], 0, ',', '.') }}
                </div>
            </div>

            {{-- PENGAJUAN --}}
            <div class="adm-stat" style="border-top: 4px solid var(--adm-blue); position: relative; overflow: hidden;">
                <div style="position: absolute; right: 12px; top: 12px; opacity: 0.08; color: var(--adm-blue);">
                    <svg viewBox="0 0 24 24"
                        style="width: 54px; height: 54px; fill: none; stroke: currentColor; stroke-width: 2.5;">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="16" y1="13" x2="8" y2="13" />
                        <line x1="16" y1="17" x2="8" y2="17" />
                    </svg>
                </div>
                <div class="adm-stat-label" style="color: var(--adm-blue); font-weight: 700;">Pengajuan</div>
                <div class="adm-stat-value" style="color: var(--adm-blue); font-size: 28px;">
                    {{ $paymentStats['pengajuan_count'] }}</div>
                <div class="adm-stat-sub" style="font-weight: 600; color: var(--adm-text-mid);">
                    Rp {{ number_format($paymentStats['pengajuan_total'], 0, ',', '.') }}
                </div>
            </div>

            {{-- DIBAYAR --}}
            <div class="adm-stat" style="border-top: 4px solid var(--adm-green); position: relative; overflow: hidden;">
                <div style="position: absolute; right: 12px; top: 12px; opacity: 0.08; color: var(--adm-green);">
                    <svg viewBox="0 0 24 24"
                        style="width: 54px; height: 54px; fill: none; stroke: currentColor; stroke-width: 2.5;">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                        <polyline points="22 4 12 14.01 9 11.01" />
                    </svg>
                </div>
                <div class="adm-stat-label" style="color: var(--adm-green); font-weight: 700;">Dibayar</div>
                <div class="adm-stat-value is-success" style="font-size: 28px;">{{ $paymentStats['dibayar_count'] }}</div>
                <div class="adm-stat-sub" style="font-weight: 600; color: var(--adm-text-mid);">
                    Rp {{ number_format($paymentStats['dibayar_total'], 0, ',', '.') }}
                </div>
            </div>

            {{-- DITOLAK --}}
            <div class="adm-stat" style="border-top: 4px solid var(--adm-rose); position: relative; overflow: hidden;">
                <div style="position: absolute; right: 12px; top: 12px; opacity: 0.08; color: var(--adm-rose);">
                    <svg viewBox="0 0 24 24"
                        style="width: 54px; height: 54px; fill: none; stroke: currentColor; stroke-width: 2.5;">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="15" y1="9" x2="9" y2="15" />
                        <line x1="9" y1="9" x2="15" y2="15" />
                    </svg>
                </div>
                <div class="adm-stat-label" style="color: var(--adm-rose); font-weight: 700;">Ditolak</div>
                <div class="adm-stat-value" style="color: var(--adm-rose); font-size: 28px;">{{ $paymentStats['ditolak_count'] }}</div>
                <div class="adm-stat-sub" style="font-weight: 600; color: var(--adm-text-mid);">
                    Rp {{ number_format($paymentStats['ditolak_total'], 0, ',', '.') }}
                </div>
            </div>
        </div>

        {{-- ── BULK ACTION BAR (Superadmin: Tandai Dibayar) ── --}}
        @if ($routePrefix === 'superadmin')
            <div id="bulkActionBar" class="d-none"
                style="display:none;align-items:center;gap:12px;padding:12px 18px;background:var(--adm-blue-lt);border:1px solid rgba(26,95,200,0.2);border-radius:var(--adm-radius);margin-bottom:16px;box-shadow:var(--adm-shadow-sm);">
                <div style="display:flex;align-items:center;gap:6px;">
                    <span class="adm-count-badge" id="selectedCount"
                        style="min-width:24px;height:24px;border-radius:50%;font-size:12px;">0</span>
                    <span style="font-weight:600;color:var(--adm-text-dark);font-size:13px;">data terpilih</span>
                </div>
                <div style="margin-left:auto;display:flex;gap:8px;">
                    <button id="btnCancelSelect" class="adm-btn-secondary"
                        style="font-size:12px;padding:0 12px;height:32px;">Batal</button>
                    <button id="btnBulkDibayar" class="adm-btn success"
                        style="font-size:12px;padding:0 14px;height:32px;background:linear-gradient(135deg,var(--adm-green),#127d62);border:none;box-shadow:none;">
                        <svg viewBox="0 0 24 24" style="width:14px;height:14px;">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                        Tandai Dibayar
                    </button>
                </div>
            </div>
        @endif

        {{-- ── BULK ACTION BAR (Admin Umum: Blast Ajukan) — DIHAPUS, kini enumerator ajukan mandiri --}}

        {{-- ── CARD CONTAINER ── --}}
        <div class="adm-card">
            <div class="adm-card-header">
                <div class="adm-card-title">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                    </svg>
                    Data Lapangan Sertifikasi Halal
                </div>
            </div>

            {{-- ── FILTER BAR ── --}}
            <div class="adm-filter-bar">
                {{-- Text Search --}}
                <div class="adm-filter-group">
                    <label class="adm-filter-label">Cari</label>
                    <div class="adm-search-shell">
                        <svg class="adm-search-icon" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                        <input type="text" id="dtSearch" class="adm-search-input" style="width:220px;"
                            placeholder="Nama PU, NIK, no. registrasi...">
                    </div>
                </div>
                {{-- Status --}}
                <div class="adm-filter-group" style="min-width: 180px;">
                    <label class="adm-filter-label">Status Survei</label>
                    <select id="filterStatus" class="adm-select" style="width: 100%;">
                        <option value="">Semua Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Terverifikasi">Terverifikasi</option>
                        <option value="Progress OSS">Progress OSS</option>
                        <option value="Progress SIHALAL">Progress SIHALAL</option>
                        <option value="Terbit SH">Terbit SH</option>
                        <option value="Ditolak">Ditolak</option>
                        <option value="Revisi">Revisi</option>
                    </select>
                </div>
                {{-- Status Pembayaran --}}
                <div class="adm-filter-group" style="min-width: 175px;">
                    <label class="adm-filter-label">Status Pembayaran</label>
                    <select id="filterPayment" class="adm-select" style="width: 100%;">
                        <option value="">Semua</option>
                        <option value="TIDAK ADA PENGAJUAN">Belum Diajukan</option>
                        <option value="PENGAJUAN">Pengajuan</option>
                        <option value="DIBAYAR">Dibayar</option>
                        <option value="DITOLAK">Ditolak</option>
                    </select>
                </div>
                {{-- Reset Button --}}
                <div style="display:flex;align-items:flex-end;">
                    <button id="resetFilters" class="adm-reset-btn" style="height: 34px;">
                        <svg viewBox="0 0 24 24">
                            <polyline points="1 4 1 10 7 10" />
                            <path d="M3.51 15a9 9 0 1 0 .49-3.5" />
                        </svg>
                        Reset Filter
                    </button>
                </div>
            </div>

            {{-- ── TABLE ── --}}
            <div class="table-responsive" style="padding: 0;">
                <table id="dataLapanganTable" class="adm-table w-100" style="margin: 0;">
                    <thead>
                        <tr>
                            <th style="width:40px;text-align:center;">
                                <input type="checkbox" id="checkAll" title="Pilih semua"
                                    style="cursor:pointer;transform:scale(1.15);">
                            </th>
                            <th style="width:44px" class="tc">#</th>
                            <th>Tanggal</th>
                            <th>Pendamping</th>
                            <th>Nama PU</th>
                            <th>NIK</th>
                            <th class="tc">Status</th>
                            <th class="tc">Payment</th>
                            <th class="tc">Tagihan</th>
                            <th class="tc" style="width:110px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── MODAL APPROVAL PEMBAYARAN (Superadmin) ── --}}
    @if ($routePrefix === 'superadmin')
        <div id="modalApprovalPembayaran" class="modal fade" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content"
                    style="border:none;border-radius:14px;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
                    <div class="modal-header"
                        style="background:linear-gradient(135deg,#1A5FC8,#0F3A8A);border-radius:14px 14px 0 0;padding:16px 20px;">
                        <h5 class="modal-title"
                            style="color:#fff;font-family:'Sora',sans-serif;font-size:15px;font-weight:700;display:flex;align-items:center;gap:8px;">
                            <svg viewBox="0 0 24 24"
                                style="width:18px;height:18px;fill:none;stroke:#fff;stroke-width:2.2;">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                <polyline points="22 4 12 14.01 9 11.01" />
                            </svg>
                            Approval Pembayaran — Data Belum Dibayar
                            <span id="approvalCountBadge"
                                style="background:rgba(255,255,255,0.25);border-radius:99px;padding:1px 10px;font-size:12px;">0
                                data</span>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" style="padding:0;">
                        <div
                            style="padding:14px 20px;background:#F8FAFC;border-bottom:1px solid #E2E8F0;display:flex;align-items:center;justify-content:space-between;">
                            <div style="font-size:13px;color:#475569;">Pilih data yang akan disetujui pembayarannya, lalu
                                klik <strong>Tandai Dibayar</strong>.</div>
                            <div style="font-size:14px;font-weight:700;color:#059669;">Total: <span
                                    id="approvalTotalNominal">Rp 0</span></div>
                        </div>
                        <div style="max-height:420px;overflow-y:auto;">
                            <table class="adm-table w-100" id="approvalTable" style="margin:0;font-size:13px;">
                                <thead>
                                    <tr>
                                        <th style="width:40px;text-align:center;">
                                            <input type="checkbox" id="checkAllApproval"
                                                style="cursor:pointer;transform:scale(1.1);">
                                        </th>
                                        <th>#</th>
                                        <th>No. Reg</th>
                                        <th>Nama PU</th>
                                        <th>NIK</th>
                                        <th>Pendamping</th>
                                        <th>Catatan Enumerator</th>
                                        <th class="tc">Nominal Tagihan</th>
                                    </tr>
                                </thead>
                                <tbody id="approvalTableBody">
                                    <tr>
                                        <td colspan="8" class="tc" style="padding:30px;color:#94A3B8;">Memuat
                                            data...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div
                            style="padding:12px 20px;background:#F8FAFC;border-top:1px solid #E2E8F0;display:flex;align-items:center;gap:8px;">
                            <span id="approvalSelectedInfo" style="font-size:12.5px;color:#64748B;">0 data dipilih</span>
                            <span style="font-size:12.5px;color:#64748B;">•</span>
                            <span id="approvalSelectedNominal" style="font-size:12.5px;font-weight:600;color:#059669;">Rp
                                0</span>
                        </div>
                    </div>
                    <div class="modal-footer"
                        style="border-top:1px solid #E2E8F0;padding:14px 20px;gap:8px;justify-content:space-between;">
                        <a href="{{ route('superadmin.data-lapangans.export-approval-pdf') }}" target="_blank"
                            class="adm-btn"
                            style="height:34px;font-size:12.5px;background:linear-gradient(135deg,#DC2626,#991B1B);color:#fff;border:none;gap:6px;text-decoration:none;display:inline-flex;align-items:center;">
                            <svg viewBox="0 0 24 24"
                                style="width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2.2;">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <polyline points="14 2 14 8 20 8" />
                                <line x1="16" y1="13" x2="8" y2="13" />
                                <line x1="16" y1="17" x2="8" y2="17" />
                            </svg>
                            Export PDF
                        </a>
                        <div style="display:flex;gap:8px;">
                            <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal"
                                style="height:34px;font-size:12.5px;">Tutup</button>
                            <button type="button" id="btnConfirmApproval" class="adm-btn"
                                style="height:34px;font-size:12.5px;background:linear-gradient(135deg,#059669,#065F46);color:#fff;border:none;gap:6px;"
                                disabled>
                                <svg viewBox="0 0 24 24"
                                    style="width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2.2;">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                                Tandai Dibayar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ── MODAL BULK PAYMENT ── --}}
    @if ($routePrefix === 'superadmin')
        <div id="modalBulkPayment" class="modal fade" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
                <div class="modal-content"
                    style="border:none;border-radius:var(--adm-radius);box-shadow:0 15px 30px rgba(0,0,0,0.15);overflow:hidden;">
                    <div class="modal-header"
                        style="background:#fff;border-bottom:1px solid var(--adm-border);padding:16px 20px;">
                        <h5 class="modal-title"
                            style="font-family:'Sora',sans-serif;font-size:15px;font-weight:700;color:var(--adm-text-dark);display:flex;align-items:center;gap:8px;">
                            <svg viewBox="0 0 24 24"
                                style="width:20px;height:20px;stroke:var(--adm-green);fill:none;stroke-width:2.2;">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                <polyline points="22 4 12 14.01 9 11.01" />
                            </svg>
                            Konfirmasi Pembayaran
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            style="font-size:12px;opacity:0.6;"></button>
                    </div>
                    <div class="modal-body" style="padding:24px 20px;background:#fcfdfe;">
                        <div style="text-align:center;margin-bottom:16px;">
                            <div
                                style="width:48px;height:48px;border-radius:50%;background:var(--adm-green-lt);color:var(--adm-green);display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px;">
                                <svg viewBox="0 0 24 24"
                                    style="width:24px;height:24px;fill:none;stroke:currentColor;stroke-width:2.2;">
                                    <rect x="2" y="4" width="20" height="16" rx="2" />
                                    <line x1="12" y1="10" x2="12" y2="14" />
                                    <line x1="8" y1="12" x2="16" y2="12" />
                                </svg>
                            </div>
                            <p style="margin:0;font-size:14px;color:var(--adm-text-dark);font-weight:600;">Tandai Dibayar
                            </p>
                            <p style="margin:4px 0 0;font-size:12.5px;color:var(--adm-text-muted);line-height:1.5;">
                                Anda akan menandai <strong id="modalSelectedCount"
                                    style="color:var(--adm-green);font-size:13.5px;">0</strong> data pelaku usaha sebagai
                                <strong>DIBAYAR</strong>.
                            </p>
                        </div>
                        <div
                            style="background:#fff;border:1px dashed var(--adm-border-mid);border-radius:8px;padding:12px 14px;font-size:11.5px;color:var(--adm-text-muted);line-height:1.6;">
                            <span
                                style="font-weight:700;color:var(--adm-text-dark);display:block;margin-bottom:2px;">Catatan
                                Penting:</span>
                            Proses ini akan mengaktifkan pembuatan cashflow pemasukan/pengeluaran otomatis dan mengirim
                            notifikasi WhatsApp kepada masing-masing Pendamping.
                        </div>
                    </div>
                    <div class="modal-footer"
                        style="background:#fff;border-top:1px solid var(--adm-border);padding:14px 20px;display:flex;justify-content:flex-end;gap:8px;">
                        <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal"
                            style="height:34px;font-size:12.5px;">Batal</button>
                        <button type="button" id="btnConfirmBulkDibayar" class="adm-btn-primary"
                            style="background:linear-gradient(135deg,var(--adm-green),#0f6e56);box-shadow:0 2px 8px rgba(15,110,86,0.2);height:34px;font-size:12.5px;border:none;">
                            Ya, Tandai Dibayar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Bulk Ajukan Admin Umum — DIHAPUS (pengajuan kini mandiri oleh enumerator via Flutter API) --}}

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const FORCE_UNLOCK_URL = '{{ url('superadmin/data-lapangans') }}';
                const BULK_PAYMENT_URL = '{{ route($routePrefix . '.data-lapangans.bulk-payment') }}';
                const EXPORT_URL = '{{ route($routePrefix . '.data-lapangans.export') }}';
                const CSRF_TOKEN = '{{ csrf_token() }}';

                function getCsrfToken() {
                    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                }

                const filterStatus = document.getElementById('filterStatus');
                const filterPayment = document.getElementById('filterPayment');

                function attachCheckboxHandlers() {
                    document.querySelectorAll('.row-checkbox').forEach(cb =>
                        cb.addEventListener('change', () => window.updateBulkBar && window.updateBulkBar()));
                }

                // ── Init DataTable ──
                const table = $('#dataLapanganTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route($routePrefix . '.data-lapangans.data') }}',
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': getCsrfToken()
                        },
                        data: function(d) {
                            d.status_filter = filterStatus.value;
                            d.payment_filter = filterPayment.value;
                        }
                    },
                    columns: [{
                            data: 'checkbox',
                            name: 'checkbox',
                            orderable: false,
                            searchable: false,
                            className: 'tc'
                        },
                        {
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            className: 'tc'
                        },
                        {
                            data: 'tanggal',
                            name: 'created_at',
                            className: ''
                        },
                        {
                            data: 'pendamping_cell',
                            name: 'enumerator_nama',
                            orderable: true,
                            className: ''
                        },
                        {
                            data: 'nama_pu',
                            name: 'nama_pu',
                            className: ''
                        },
                        {
                            data: 'nik',
                            name: 'nik',
                            className: 'adm-mono'
                        },
                        {
                            data: 'status_badge',
                            name: 'status',
                            className: 'tc'
                        },
                        {
                            data: 'payment_badge',
                            name: 'status_pembayaran',
                            className: 'tc'
                        },
                        {
                            data: 'tagihan_cell',
                            name: 'tagihan_cell',
                            orderable: false,
                            searchable: false,
                            className: 'tc'
                        },
                        {
                            data: 'aksi',
                            name: 'aksi',
                            orderable: false,
                            searchable: false,
                            className: 'tc'
                        },
                    ],
                    dom: 'rt<"adm-card-footer"ip>',
                    language: {
                        info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                        infoEmpty: 'Tidak ada data',
                        paginate: {
                            previous: '‹',
                            next: '›'
                        },
                        zeroRecords: 'Tidak ada data ditemukan',
                        emptyTable: '<div class="adm-empty"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><p>Belum ada data lapangan.</p></div>',
                        processing: '<div class="text-center py-3"><div class="spinner-border" style="color:var(--adm-blue);width:1.8rem;height:1.8rem;" role="status"></div></div>',
                    },
                    pageLength: 25,
                    order: [
                        [3, 'asc']
                    ],
                    responsive: true,
                    drawCallback: function() {
                        attachForceUnlockHandlers();
                        attachToggleUnlockHandlers();
                        attachCheckboxHandlers();
                        updateBulkBar();
                    },
                });

                // ── Search input — debounced ──
                let _searchTimer;
                document.getElementById('dtSearch').addEventListener('input', function() {
                    clearTimeout(_searchTimer);
                    const val = this.value;
                    _searchTimer = setTimeout(function() {
                        table.search(val).draw();
                    }, 400);
                });

                // ── Filter dropdowns → reload table ──
                filterStatus.addEventListener('change', () => table.ajax.reload(null, true));
                filterPayment.addEventListener('change', () => table.ajax.reload(null, true));

                document.getElementById('resetFilters').addEventListener('click', function() {
                    filterStatus.value = '';
                    filterPayment.value = '';
                    document.getElementById('dtSearch').value = '';
                    table.search('').ajax.reload(null, true);
                });

                // ── Export Excel ──
                document.getElementById('exportBtn').addEventListener('click', function() {
                    const params = new URLSearchParams();
                    const s = table.search();
                    if (s) params.append('search', s);
                    if (filterStatus.value) params.append('status', filterStatus.value);
                    if (filterPayment.value) params.append('status_pembayaran', filterPayment.value);
                    window.location.href = EXPORT_URL + (params.toString() ? '?' + params.toString() : '');
                });

                // ── Force Unlock ──
                function attachForceUnlockHandlers() {
                    document.querySelectorAll('.btn-force-unlock').forEach(btn => {
                        const nb = btn.cloneNode(true);
                        btn.parentNode.replaceChild(nb, btn);
                        nb.addEventListener('click', async function() {
                            if (!confirm('Yakin ingin membuka paksa kunci data ini?')) return;
                            const id = this.dataset.id;
                            this.disabled = true;
                            this.innerHTML =
                                '<span class="spinner-border spinner-border-sm"></span>';
                            try {
                                const res = await fetch(`${FORCE_UNLOCK_URL}/${id}/force-unlock`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': getCsrfToken(),
                                        'Accept': 'application/json'
                                    },
                                    credentials: 'same-origin'
                                });
                                const data = await res.json();
                                if (data.success) table.ajax.reload(null, false);
                                else {
                                    alert('Gagal unlock: ' + data.message);
                                    this.disabled = false;
                                }
                            } catch {
                                alert('Terjadi kesalahan saat unlock');
                                this.disabled = false;
                            }
                        });
                    });
                }

                // ── Toggle Unlock for Data Entry ──
                function attachToggleUnlockHandlers() {
                    document.querySelectorAll('.btn-toggle-unlock').forEach(btn => {
                        const nb = btn.cloneNode(true);
                        btn.parentNode.replaceChild(nb, btn);
                        nb.addEventListener('click', async function() {
                            const url = this.dataset.url;
                            this.disabled = true;
                            const origHtml = this.innerHTML;
                            this.innerHTML =
                                '<span class="spinner-border spinner-border-sm"></span>';
                            try {
                                const res = await fetch(url, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': CSRF_TOKEN,
                                        'Accept': 'application/json'
                                    },
                                    credentials: 'same-origin'
                                });
                                const data = await res.json();
                                if (data.success) {
                                    table.ajax.reload(null, false);
                                } else {
                                    alert('Gagal: ' + data.message);
                                    this.disabled = false;
                                    this.innerHTML = origHtml;
                                }
                            } catch {
                                alert('Terjadi kesalahan');
                                this.disabled = false;
                                this.innerHTML = origHtml;
                            }
                        });
                    });
                }

                // ── Check All (always available) ──
                document.getElementById('checkAll').addEventListener('change', function() {
                    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
                    if (window.updateBulkBar) window.updateBulkBar();
                });

                // ── Role detection ──
                const ROLE_PREFIX = '{{ $routePrefix }}';

                // ── Bulk Payment (Superadmin only) ──
                if (ROLE_PREFIX === 'superadmin') {
                    const bulkBar = document.getElementById('bulkActionBar');
                    const selectedCountEl = document.getElementById('selectedCount');
                    const btnBulkDibayar = document.getElementById('btnBulkDibayar');
                    const btnCancelSelect = document.getElementById('btnCancelSelect');
                    const modalBulkPayment = new bootstrap.Modal(document.getElementById('modalBulkPayment'));
                    const modalSelectedCount = document.getElementById('modalSelectedCount');
                    const btnConfirmBulk = document.getElementById('btnConfirmBulkDibayar');

                    window.updateBulkBar = function() {
                        const checked = document.querySelectorAll('.row-checkbox:checked');
                        const all = document.querySelectorAll('.row-checkbox');
                        const ca = document.getElementById('checkAll');
                        if (checked.length > 0) {
                            bulkBar.classList.remove('d-none');
                            bulkBar.style.display = 'flex';
                            selectedCountEl.textContent = checked.length;
                        } else {
                            bulkBar.classList.add('d-none');
                            bulkBar.style.display = 'none';
                        }
                        if (ca) {
                            ca.checked = all.length > 0 && checked.length === all.length;
                            ca.indeterminate = checked.length > 0 && checked.length < all.length;
                        }
                    };

                    btnBulkDibayar.addEventListener('click', function() {
                        const checked = document.querySelectorAll('.row-checkbox:checked');
                        if (!checked.length) return;
                        modalSelectedCount.textContent = checked.length;
                        modalBulkPayment.show();
                    });

                    btnConfirmBulk.addEventListener('click', async function() {
                        const checked = document.querySelectorAll('.row-checkbox:checked');
                        if (!checked.length) return;
                        const ids = Array.from(checked).map(cb => cb.value);
                        modalBulkPayment.hide();
                        btnBulkDibayar.disabled = true;
                        btnBulkDibayar.innerHTML =
                            '<span class="spinner-border spinner-border-sm"></span> Memproses...';
                        try {
                            const res = await fetch(BULK_PAYMENT_URL, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': getCsrfToken()
                                },
                                credentials: 'same-origin',
                                body: JSON.stringify({
                                    ids
                                })
                            });
                            const data = await res.json();
                            if (data.success) {
                                table.ajax.reload(null, false);
                                loadApprovalData();
                            } else alert(data.message || 'Gagal memperbarui data');
                        } catch {
                            alert('Terjadi kesalahan');
                        } finally {
                            btnBulkDibayar.disabled = false;
                            btnBulkDibayar.innerHTML =
                                '<svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2;"><polyline points="20 6 9 17 4 12"/></svg> Tandai Dibayar';
                        }
                    });

                    btnCancelSelect.addEventListener('click', function() {
                        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
                        window.updateBulkBar();
                    });

                    // ── Approval Modal (Superadmin) ──
                    const PENGAJUAN_DATA_URL = '{{ route('superadmin.data-lapangans.pengajuan-data') }}';
                    const modalApproval = new bootstrap.Modal(document.getElementById('modalApprovalPembayaran'));
                    let approvalItems = [];

                    async function loadApprovalData() {
                        try {
                            const res = await fetch(PENGAJUAN_DATA_URL, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': getCsrfToken()
                                },
                                credentials: 'same-origin'
                            });
                            const json = await res.json();
                            approvalItems = json.data || [];
                            const count = json.count || 0;
                            // Update badge
                            const badge = document.getElementById('pengajuanBadge');
                            if (count > 0) {
                                badge.textContent = count;
                                badge.style.display = 'inline';
                            } else {
                                badge.style.display = 'none';
                            }
                            document.getElementById('approvalCountBadge').textContent = count + ' data';
                            document.getElementById('approvalTotalNominal').textContent = json.total_fmt || 'Rp 0';
                            renderApprovalTable(approvalItems);
                        } catch (e) {
                            console.error(e);
                        }
                    }

                    function renderApprovalTable(items) {
                        const tbody = document.getElementById('approvalTableBody');
                        if (!items.length) {
                            tbody.innerHTML =
                                '<tr><td colspan="8" class="tc" style="padding:30px;color:#94A3B8;">Tidak ada data TERBIT SH yang memerlukan approval pembayaran.</td></tr>';
                            return;
                        }
                        tbody.innerHTML = items.map((d, i) => {
                            const isInaktif = d.enumerator_status === 'Tidak Aktif';

                            const checkboxCell = isInaktif
                                ? `<span title="Pembayaran ditahan — Pendamping Tidak Aktif" style="font-size:11px;color:#DC2626;font-weight:700;cursor:help;">&#x23F8; Hold</span>`
                                : `<input type="checkbox" class="approval-cb" data-id="${d.hashed_id}" data-nominal="${d.nominal}" style="cursor:pointer;transform:scale(1.1);">`;

                            const rowStyle   = isInaktif ? 'background:#FFF5F5;' : '';

                            const statusLabel = isInaktif
                                ? `<span style="font-size:10px;font-weight:700;background:#FEE2E2;color:#DC2626;border:1px solid #DC262633;border-radius:4px;padding:1px 5px;">Tidak Aktif</span>`
                                : `<span style="font-size:10px;font-weight:700;background:#DBEAFE;color:#2563EB;border:1px solid #2563EB33;border-radius:4px;padding:1px 5px;">PENGAJUAN</span>`;

                            const nominalCell = isInaktif
                                ? `<em style="font-size:11px;color:#94A3B8;">Ditahan</em>`
                                : `<span style="font-weight:700;color:#059669;">${d.nominal_fmt}</span>`;

                            const catatanCell = d.catatan_enumerator
                                ? `<span style="font-size:12px;color:#475569;font-style:italic;">${d.catatan_enumerator}</span>`
                                : `<span style="color:#CBD5E1;font-size:11px;">—</span>`;

                            return `
                            <tr style="${rowStyle}">
                                <td class="tc">${checkboxCell}</td>
                                <td>${i+1}</td>
                                <td style="font-size:12px;font-weight:600;">${d.no_registrasi || '-'}</td>
                                <td style="font-weight:600;">${d.nama_pu}</td>
                                <td style="font-family:monospace;font-size:12px;">${d.nik}</td>
                                <td style="font-size:12px;">${d.pendamping} ${statusLabel}</td>
                                <td style="font-size:12px;max-width:160px;">${catatanCell}</td>
                                <td class="tc">${nominalCell}</td>
                            </tr>`;
                        }).join('');
                        attachApprovalCheckboxes();
                    }

                    function attachApprovalCheckboxes() {
                        document.querySelectorAll('.approval-cb').forEach(cb => cb.addEventListener('change',
                            updateApprovalSummary));
                        document.getElementById('checkAllApproval').addEventListener('change', function() {
                            document.querySelectorAll('.approval-cb').forEach(cb => cb.checked = this.checked);
                            updateApprovalSummary();
                        });
                    }

                    function updateApprovalSummary() {
                        const checked = document.querySelectorAll('.approval-cb:checked');
                        const total = Array.from(checked).reduce((s, cb) => s + parseInt(cb.dataset.nominal), 0);
                        document.getElementById('approvalSelectedInfo').textContent = checked.length + ' data dipilih';
                        document.getElementById('approvalSelectedNominal').textContent = 'Rp ' + total.toLocaleString(
                            'id-ID');
                        document.getElementById('btnConfirmApproval').disabled = checked.length === 0;
                        const all = document.querySelectorAll('.approval-cb');
                        const chkAll = document.getElementById('checkAllApproval');
                        if (chkAll) {
                            chkAll.checked = all.length > 0 && checked.length === all.length;
                            chkAll.indeterminate = checked.length > 0 && checked.length < all.length;
                        }
                    }

                    document.getElementById('btnOpenApproval').addEventListener('click', function() {
                        loadApprovalData();
                        modalApproval.show();
                    });

                    document.getElementById('btnConfirmApproval').addEventListener('click', async function() {
                        const checked = document.querySelectorAll('.approval-cb:checked');
                        if (!checked.length) return;
                        const ids = Array.from(checked).map(cb => cb.dataset.id);
                        this.disabled = true;
                        this.innerHTML =
                            '<span class="spinner-border spinner-border-sm"></span> Memproses...';
                        try {
                            const res = await fetch(BULK_PAYMENT_URL, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': getCsrfToken()
                                },
                                credentials: 'same-origin',
                                body: JSON.stringify({
                                    ids
                                })
                            });
                            const data = await res.json();
                            if (data.success) {
                                table.ajax.reload(null, false);
                                await loadApprovalData();
                                renderApprovalTable(approvalItems);
                                updateApprovalSummary();
                                alert(data.message);
                            } else alert(data.message || 'Gagal');
                        } catch {
                            alert('Terjadi kesalahan');
                        } finally {
                            this.disabled = false;
                            this.innerHTML =
                                '<svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2.2;"><polyline points="20 6 9 17 4 12"/></svg> Tandai Dibayar';
                        }
                    });

                    // Load badge count on page load
                    loadApprovalData();

                } else {
                    // admin-umum: no bulk checkbox bar, updateBulkBar is no-op
                    window.updateBulkBar = function() {};
                }

                // Blast Ajukan Pembayaran Admin Umum — DIHAPUS (pengajuan mandiri via Flutter API)
            });
        </script>
    @endpush
@endsection
