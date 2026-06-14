@extends('layouts.app')

@section('template_title')
    Data Lapangans
@endsection

@section('content')
    <div class="row">
        <div class="col">
            @include('layouts.messages')
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span id="card_title">
                            {{ __('Data Lapangans') }}
                        </span>
                    </div>
                </div>

                {{-- Form Search / Filter --}}
                <div class="card-body bg-white border-bottom">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="search" class="form-label">Cari Nama PU / Pendamping</label>
                            <input type="text" class="form-control" id="dtSearch"
                                placeholder="Cari berdasarkan nama PU atau pendamping...">
                        </div>
                        <div class="col-md-3">
                            <label for="tanggal_dari" class="form-label">Tanggal Dari</label>
                            <input type="date" class="form-control" id="tanggal_dari">
                        </div>
                        <div class="col-md-3">
                            <label for="tanggal_sampai" class="form-label">Tanggal Sampai</label>
                            <input type="date" class="form-control" id="tanggal_sampai">
                        </div>
                    </div>
                </div>

                <div class="card-body bg-white">
                    <div class="table-responsive">
                        <table id="dataLapanganTable" class="table table-striped table-hover w-100">
                            <thead class="thead">
                                <tr>
                                    <th>No</th>
                                    <th>Pendamping</th>
                                    <th>Nama PU</th>
                                    <th>Nama Produk</th>
                                    <th>Status</th>
                                    <th>Email Lama</th>
                                    <th style="text-align:center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ================================
        // LOCK MECHANISM
        // ================================
        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                document.querySelector('input[name="_token"]')?.value;
        }

        let lockRenewer = null;
        const LOCK_URL = '/data-entry/data-lapangan';

        function getCurrentLockId() {
            return sessionStorage.getItem('currentLockId');
        }

        function setCurrentLockId(id) {
            if (id) sessionStorage.setItem('currentLockId', id);
            else sessionStorage.removeItem('currentLockId');
        }

        async function acquireLock(id) {
            const res = await fetch(`${LOCK_URL}/${id}/lock`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            });
            return await res.json();
        }

        async function releaseLock(id) {
            if (!id) return;
            await fetch(`${LOCK_URL}/${id}/lock`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            });
            setCurrentLockId(null);
            clearInterval(lockRenewer);
        }

        // Intercept klik tombol Show
        document.addEventListener('click', async function(e) {
            const btn = e.target.closest('.btn-show-data');
            if (!btn) return;

            e.preventDefault();
            const id = btn.dataset.id;
            const href = btn.getAttribute('href');

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            const result = await acquireLock(id);

            if (!result.success) {
                btn.disabled = false;
                btn.innerHTML = '<i class="las la-eye"></i> Show';
                alert('Data tidak tersedia saat ini.');
                return;
            }

            setCurrentLockId(id);
            lockRenewer = setInterval(() => acquireLock(id), 10 * 60 * 1000);
            window.location.href = href;
        });

        // Release lock saat tab ditutup / refresh
        window.addEventListener('beforeunload', function() {
            const lockId = getCurrentLockId();
            if (lockId) {
                navigator.sendBeacon(`${LOCK_URL}/${lockId}/unlock-beacon`);
                setCurrentLockId(null);
            }
        });

        // ================================
        // DATATABLES
        // ================================
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('dtSearch');
            const tanggalDari = document.getElementById('tanggal_dari');
            const tanggalSampai = document.getElementById('tanggal_sampai');
            let searchTimeout;

            const table = $('#dataLapanganTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('data-entry.data-lapangan.data') }}',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    data: function(d) {
                        // Pass date range filters as custom params
                        d.tanggal_dari = tanggalDari.value;
                        d.tanggal_sampai = tanggalSampai.value;
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'pendamping_cell',
                        name: 'enumerator_nama',
                        searchable: true
                    },
                    {
                        data: 'nama_pu',
                        name: 'nama_pu',
                        searchable: true
                    },
                    {
                        data: 'nama_produk_cell',
                        name: 'nama_produk',
                        searchable: false
                    },
                    {
                        data: 'status_badge',
                        name: 'status',
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'old_email_sihalal_cell',
                        name: 'old_email_sihalal',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                ],
                language: {
                    search: '',
                    searchPlaceholder: 'Cari...',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ – _END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data',
                    paginate: {
                        previous: '&lsaquo;',
                        next: '&rsaquo;'
                    },
                    zeroRecords: 'Tidak ada data ditemukan',
                    emptyTable: 'Belum ada data lapangan berstatus Terverifikasi',
                    processing: '<div class="text-center py-4"><div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;"></div><p class="mt-3 text-muted fw-bold">SABAR BOS...</p></div>',
                },
                pageLength: 20,
                order: [
                    [1, 'asc']
                ],
                // Hide the built-in search box — we use our own inputs
                dom: '<"row"<"col-sm-6"l><"col-sm-6">>rt<"row mt-2"<"col-sm-5"i><"col-sm-7"p>>',
            });

            // ── Custom search input (debounced) ──
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    table.search(this.value).draw();
                }, 450);
            });

            // ── Date range → reload ──
            tanggalDari.addEventListener('change', () => table.ajax.reload(null, true));
            tanggalSampai.addEventListener('change', () => table.ajax.reload(null, true));

            // ── Release lock + refresh on back button ──
            window.addEventListener('pageshow', function(event) {
                if (event.persisted || performance.navigation?.type === 2) {
                    const lockId = getCurrentLockId();
                    if (lockId) {
                        releaseLock(lockId).then(() => table.ajax.reload(null, false));
                    } else {
                        table.ajax.reload(null, false);
                    }
                }
            });
        });
    </script>
@endsection
