@extends('layouts.app')

@section('template_title')
    Tickets
@endsection

@section('content')
    <div class="adm-page">
        <div class="container-fluid">

            {{-- PAGE HEADER --}}
            <div class="adm-header">
                <div class="adm-header-left">
                    <h1>
                        <svg viewBox="0 0 24 24"
                            style="display:inline-block;width:20px;height:20px;stroke:var(--adm-blue);fill:none;stroke-width:2;vertical-align:-3px;margin-right:6px;">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" />
                            <line x1="7" y1="7" x2="7.01" y2="7" />
                        </svg>
                        Manajemen Tiket
                    </h1>
                    <p>Kelola semua tiket dukungan pengguna</p>
                </div>
            </div>

            {{-- FLASH MESSAGES --}}
            @if (session('success'))
                <div class="adm-alert adm-alert-success">
                    <svg viewBox="0 0 24 24">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                        <polyline points="22 4 12 14.01 9 11.01" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="adm-alert adm-alert-danger">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- STAT CARDS --}}
            <div class="adm-stats" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px;">
                <div class="adm-stat is-accent">
                    <div class="adm-stat-label">Total Tiket</div>
                    <div class="adm-stat-value">{{ $tickets->total() }}</div>
                    <div class="adm-stat-sub">Semua tiket masuk</div>
                </div>
                <div class="adm-stat">
                    <div class="adm-stat-label">Open</div>
                    <div class="adm-stat-value is-success">{{ $tickets->getCollection()->where('status', 'open')->count() }}
                    </div>
                    <div class="adm-stat-sub">Halaman ini</div>
                </div>
                <div class="adm-stat">
                    <div class="adm-stat-label">In Progress</div>
                    <div class="adm-stat-value is-warn">
                        {{ $tickets->getCollection()->where('status', 'in_progress')->count() }}</div>
                    <div class="adm-stat-sub">Halaman ini</div>
                </div>
                <div class="adm-stat">
                    <div class="adm-stat-label">Solved</div>
                    <div class="adm-stat-value" style="color:var(--adm-text-muted);">
                        {{ $tickets->getCollection()->where('status', 'closed')->count() }}</div>
                    <div class="adm-stat-sub">Halaman ini</div>
                </div>
            </div>

            {{-- MAIN TABLE CARD --}}
            <div class="adm-card">

                {{-- FILTER BAR --}}
                <div class="adm-filter-bar">
                    <div class="adm-filter-group">
                        <span class="adm-filter-label">Cari</span>
                        <div class="adm-search-shell">
                            <svg class="adm-search-icon" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            <input type="text" class="adm-search-input" id="ticketSearch"
                                placeholder="No tiket, subjek, user..." />
                        </div>
                    </div>
                    <div class="adm-filter-group">
                        <span class="adm-filter-label">Status</span>
                        <select class="adm-select" id="statusFilter" style="width:140px;">
                            <option value="">Semua Status</option>
                            <option value="open">Open</option>
                            <option value="in_progress">In Progress</option>
                            <option value="closed">Solved</option>
                        </select>
                    </div>
                    <div class="adm-filter-group" style="margin-left:auto;">
                        <span class="adm-filter-label">&nbsp;</span>
                        <button class="adm-reset-btn" id="resetFilter">
                            <svg viewBox="0 0 24 24">
                                <polyline points="1 4 1 10 7 10" />
                                <path d="M3.51 15a9 9 0 1 0 .49-3.5" />
                            </svg>
                            Reset
                        </button>
                    </div>
                </div>

                {{-- TABLE --}}
                <div class="table-responsive">
                    <table class="adm-table" id="ticketsTable">
                        <thead>
                            <tr>
                                <th style="width:160px;">No. Tiket</th>
                                <th style="width:180px;">User</th>
                                <th>Subjek</th>
                                <th style="width:120px;" class="tc">Status</th>
                                <th style="width:160px;" class="tc">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="ticketsTbody">
                            @forelse ($tickets as $ticket)
                                <tr class="ticket-row" data-subject="{{ strtolower($ticket->subject) }}"
                                    data-no="{{ strtolower($ticket->no_ticket) }}"
                                    data-user="{{ strtolower($ticket->user->name) }}" data-status="{{ $ticket->status }}">
                                    <td>
                                        <span class="adm-mono"
                                            style="color:var(--adm-blue);font-weight:600;">{{ $ticket->no_ticket }}</span>
                                    </td>
                                    <td>
                                        <div class="adm-name-cell">
                                            <div class="adm-avatar"
                                                style="background:var(--adm-blue-lt);color:var(--adm-blue);">
                                                {{ strtoupper(substr($ticket->user->name, 0, 1)) }}
                                            </div>
                                            <span
                                                style="font-size:13px;font-weight:600;color:var(--adm-text-dark);">{{ $ticket->user->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            style="font-weight:600;color:var(--adm-text-dark);font-size:13px;">{{ $ticket->subject }}</span>
                                    </td>
                                    <td class="tc">
                                        @if ($ticket->status === 'open')
                                            <span class="adm-badge adm-badge-success"><span class="dot"></span>
                                                Open</span>
                                        @elseif($ticket->status === 'in_progress')
                                            <span class="adm-badge adm-badge-pending"><span class="dot"></span> In
                                                Progress</span>
                                        @else
                                            <span class="adm-badge adm-badge-nonaktif"><span class="dot"></span>
                                                Closed</span>
                                        @endif
                                    </td>
                                    <td class="tc">
                                        <div class="adm-actions">
                                            {{-- Show --}}
                                            <a href="{{ route('superadmin.tickets.show', $ticket->hashed_id) }}"
                                                class="adm-btn primary icon-only" title="Lihat Detail">
                                                <svg viewBox="0 0 24 24">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                    <circle cx="12" cy="12" r="3" />
                                                </svg>
                                            </a>
                                            {{-- Delete --}}
                                            <form action="{{ route('superadmin.tickets.destroy', $ticket->hashed_id) }}"
                                                method="POST" style="margin:0;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="adm-btn danger icon-only" title="Hapus"
                                                    onclick="event.preventDefault(); confirm('Yakin ingin menghapus tiket ini?') ? this.closest('form').submit() : false;">
                                                    <svg viewBox="0 0 24 24">
                                                        <polyline points="3 6 5 6 21 6" />
                                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                                                        <path d="M10 11v6M14 11v6" />
                                                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="emptyRow">
                                    <td colspan="5">
                                        <div class="adm-empty">
                                            <svg viewBox="0 0 24 24">
                                                <path
                                                    d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" />
                                                <line x1="7" y1="7" x2="7.01" y2="7" />
                                            </svg>
                                            <p>Belum ada tiket masuk</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- No results row (hidden by default, shown by JS filter) --}}
                    <div id="noResultsRow" style="display:none;">
                        <div class="adm-empty" style="padding:40px 20px;">
                            <svg viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            <p>Tidak ada tiket yang cocok</p>
                        </div>
                    </div>
                </div>

                {{-- FOOTER: PAGINATION --}}
                <div class="adm-card-footer">
                    <div class="adm-footer-info">
                        Menampilkan <strong>{{ $tickets->firstItem() ?? 0 }}–{{ $tickets->lastItem() ?? 0 }}</strong>
                        dari <strong>{{ $tickets->total() }}</strong> tiket
                    </div>
                    <div>
                        @include('layouts.pagination', ['paginator' => $tickets])
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
     SHOW MODALS
═══════════════════════════════════════════════ --}}
    @foreach ($tickets as $ticket)
        <div class="modal fade adm-modal" id="showModal{{ $ticket->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">

                    {{-- Modal Header --}}
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <svg viewBox="0 0 24 24">
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" />
                                <line x1="7" y1="7" x2="7.01" y2="7" />
                            </svg>
                            Detail Tiket
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="modal-body" style="padding:0;">

                        {{-- Header Info Strip --}}
                        <div
                            style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;padding:16px 20px;background:var(--adm-bg-light);border-bottom:1px solid var(--adm-border);">
                            <div>
                                <div
                                    style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-faint);margin-bottom:3px;">
                                    No. Tiket</div>
                                <div class="adm-mono" style="font-size:15px;font-weight:700;color:var(--adm-blue);">
                                    {{ $ticket->no_ticket }}</div>
                            </div>
                            <div>
                                @if ($ticket->status === 'open')
                                    <span class="adm-badge adm-badge-success"
                                        style="font-size:12px;padding:5px 12px;"><span class="dot"></span> Open</span>
                                @elseif($ticket->status === 'in_progress')
                                    <span class="adm-badge adm-badge-pending"
                                        style="font-size:12px;padding:5px 12px;"><span class="dot"></span> In
                                        Progress</span>
                                @else
                                    <span class="adm-badge adm-badge-nonaktif"
                                        style="font-size:12px;padding:5px 12px;"><span class="dot"></span>
                                        Closed</span>
                                @endif
                            </div>
                        </div>

                        <div style="padding:20px;">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="adm-field">
                                        <label class="adm-label">User</label>
                                        <div
                                            style="background:var(--adm-bg-input);border:1px solid var(--adm-border-mid);border-radius:var(--adm-radius-sm);padding:9px 12px;font-size:13px;font-weight:600;color:var(--adm-text-dark);">
                                            {{ $ticket->user->name }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="adm-field">
                                        <label class="adm-label">Subjek</label>
                                        <div
                                            style="background:var(--adm-blue-lt);border:1px solid rgba(26,95,200,.15);border-radius:var(--adm-radius-sm);padding:10px 12px;font-size:14px;font-weight:700;color:var(--adm-text-dark);font-family:'Sora',sans-serif;">
                                            {{ $ticket->subject }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="adm-field">
                                        <label class="adm-label">Deskripsi</label>
                                        <div
                                            style="background:var(--adm-bg-input);border:1px solid var(--adm-border-mid);border-radius:var(--adm-radius-sm);padding:12px;font-size:13.5px;color:var(--adm-text-mid);line-height:1.7;min-height:80px;">
                                            {{ $ticket->description }}
                                        </div>
                                    </div>
                                </div>
                                @if ($ticket->file)
                                    <div class="col-12">
                                        <div class="adm-field">
                                            <label class="adm-label">File</label>
                                            <div
                                                style="display:flex;align-items:center;gap:10px;background:var(--adm-bg-input);border:1px solid var(--adm-border-mid);border-radius:var(--adm-radius-sm);padding:10px 12px;">
                                                <div
                                                    style="width:32px;height:32px;background:var(--adm-blue-lt);border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                                    <svg viewBox="0 0 24 24"
                                                        style="width:14px;height:14px;stroke:var(--adm-blue);fill:none;stroke-width:2;">
                                                        <path
                                                            d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                                        <polyline points="14 2 14 8 20 8" />
                                                    </svg>
                                                </div>
                                                <span
                                                    style="flex:1;font-size:12.5px;color:var(--adm-text-mid);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $ticket->file }}</span>
                                                <a href="{{ asset('storage/' . $ticket->file) }}" target="_blank"
                                                    class="adm-btn primary" style="flex-shrink:0;">
                                                    <svg viewBox="0 0 24 24">
                                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                                        <polyline points="7 10 12 15 17 10" />
                                                        <line x1="12" y1="15" x2="12"
                                                            y2="3" />
                                                    </svg>
                                                    Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        @if ($ticket->status !== 'closed')
                            <form action="{{ route('superadmin.tickets.close', $ticket->hashed_id) }}" method="POST"
                                style="margin:0;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="adm-btn warning" style="height:34px;padding:0 14px;"
                                    onclick="return confirm('Yakin ingin menutup tiket ini?')">
                                    <svg viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10" />
                                        <path d="M15 9l-6 6M9 9l6 6" />
                                    </svg>
                                    Tutup Tiket
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('superadmin.tickets.show', $ticket->hashed_id) }}" class="adm-btn primary"
                            style="height:34px;padding:0 14px;">
                            <svg viewBox="0 0 24 24">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            Halaman Penuh
                        </a>
                        <button type="button" class="adm-btn" data-bs-dismiss="modal"
                            style="height:34px;padding:0 14px;">
                            Tutup
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach

    {{-- ═══════════════════════════════════════════════
     CLIENT-SIDE FILTER
═══════════════════════════════════════════════ --}}
    <script>
        (function() {
            const searchInput = document.getElementById('ticketSearch');
            const statusSelect = document.getElementById('statusFilter');
            const resetBtn = document.getElementById('resetFilter');
            const noResults = document.getElementById('noResultsRow');

            function filter() {
                const q = searchInput.value.toLowerCase().trim();
                const status = statusSelect.value;
                const rows = document.querySelectorAll('.ticket-row');
                let visible = 0;

                rows.forEach(row => {
                    const matchQ = !q ||
                        row.dataset.subject.includes(q) ||
                        row.dataset.no.includes(q) ||
                        row.dataset.user.includes(q);
                    const matchS = !status || row.dataset.status === status;

                    if (matchQ && matchS) {
                        row.style.display = '';
                        visible++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                noResults.style.display = visible === 0 ? 'block' : 'none';
            }

            searchInput.addEventListener('input', filter);
            statusSelect.addEventListener('change', filter);
            resetBtn.addEventListener('click', function() {
                searchInput.value = '';
                statusSelect.value = '';
                filter();
            });
        })();
    </script>
@endsection
