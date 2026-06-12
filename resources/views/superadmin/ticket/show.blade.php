@extends('layouts.app')
@section('template_title')
    {{ $ticket->subject ?? __('Show') . ' ' . __('Ticket') }}
@endsection

@section('content')
    <div class="adm-page">
        <div class="container-fluid">

            {{-- PAGE HEADER --}}
            <div class="adm-header">
                <div class="adm-header-left">
                    <h1>
                        <svg viewBox="0 0 24 24"
                            style="display:inline-block;width:20px;height:20px;stroke:var(--adm-blue);fill:none;stroke-width:2;vertical-align:-3px;margin-right:6px">
                            <rect x="2" y="3" width="20" height="18" rx="3" />
                            <path d="M8 10h8M8 14h5" />
                        </svg>
                        Detail Tiket
                    </h1>
                    <p>Informasi lengkap dan status tiket dukungan</p>
                </div>
                <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                    @if ($ticket->status !== 'closed')
                        <form action="{{ route('superadmin.tickets.close', $ticket->hashed_id) }}" method="POST"
                            style="margin:0;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="adm-btn warning"
                                onclick="return confirm('Yakin ingin menandai tiket ini sebagai diselesaikan?')"
                                style="height:36px;padding:0 14px;font-size:13px;font-weight:600;">
                                <svg viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M15 9l-6 6M9 9l6 6" />
                                </svg>
                                Ticket Solved
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('superadmin.tickets.index') }}" class="adm-btn-secondary">
                        <svg viewBox="0 0 24 24"
                            style="width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2;">
                            <path d="M19 12H5M12 5l-7 7 7 7" />
                        </svg>
                        Kembali
                    </a>
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

            <div class="row g-3">
                {{-- LEFT: MAIN INFO --}}
                <div class="col-lg-8">

                    {{-- TICKET IDENTITY --}}
                    <div class="adm-card" style="margin-bottom:16px;">
                        <div class="adm-card-header">
                            <div class="adm-card-title">
                                <svg viewBox="0 0 24 24">
                                    <path
                                        d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" />
                                    <line x1="7" y1="7" x2="7.01" y2="7" />
                                </svg>
                                Identitas Tiket
                            </div>
                            {{-- Status Badge --}}
                            @if ($ticket->status === 'open')
                                <span class="adm-badge adm-badge-success">
                                    <span class="dot"></span> Open
                                </span>
                            @elseif($ticket->status === 'in_progress')
                                <span class="adm-badge adm-badge-pending">
                                    <span class="dot"></span> In Progress
                                </span>
                            @else
                                <span class="adm-badge adm-badge-nonaktif">
                                    <span class="dot"></span> Solved
                                </span>
                            @endif
                        </div>
                        <div style="padding:20px;">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div
                                        style="background:var(--adm-bg-light);border:1px solid var(--adm-border);border-radius:var(--adm-radius-sm);padding:14px 16px;">
                                        <div
                                            style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-faint);margin-bottom:6px;">
                                            No. Tiket</div>
                                        <div class="adm-mono" style="font-size:14px;font-weight:700;color:var(--adm-blue);">
                                            {{ $ticket->no_ticket }}</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div
                                        style="background:var(--adm-bg-light);border:1px solid var(--adm-border);border-radius:var(--adm-radius-sm);padding:14px 16px;">
                                        <div
                                            style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-faint);margin-bottom:6px;">
                                            Nama Lengkap</div>
                                        <div style="font-size:14px;font-weight:700;color:var(--adm-text-dark);">
                                            {{ $ticket->user->name }}</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div
                                        style="background:var(--adm-blue-lt);border:1px solid rgba(26,95,200,.15);border-radius:var(--adm-radius-sm);padding:14px 16px;">
                                        <div
                                            style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-blue);opacity:.7;margin-bottom:6px;">
                                            Subjek</div>
                                        <div
                                            style="font-size:15px;font-weight:700;color:var(--adm-text-dark);font-family:'Sora',sans-serif;">
                                            {{ $ticket->subject }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DESCRIPTION --}}
                    <div class="adm-card" style="margin-bottom:16px;">
                        <div class="adm-card-header">
                            <div class="adm-card-title">
                                <svg viewBox="0 0 24 24">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                    <polyline points="10 9 9 9 8 9" />
                                </svg>
                                Deskripsi
                            </div>
                        </div>
                        <div style="padding:20px;">
                            <div
                                style="background:var(--adm-bg-light);border:1px solid var(--adm-border);border-radius:var(--adm-radius-sm);padding:16px;font-size:13.5px;color:var(--adm-text-mid);line-height:1.7;min-height:80px;">
                                {!! $ticket->description !!}
                            </div>
                        </div>
                    </div>

                    {{-- FILE ATTACHMENT --}}
                    @if ($ticket->file)
                        <div class="adm-card">
                            <div class="adm-card-header">
                                <div class="adm-card-title">
                                    <svg viewBox="0 0 24 24">
                                        <path
                                            d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48" />
                                    </svg>
                                    Lampiran File
                                </div>
                            </div>
                            <div style="padding:20px;">
                                <div
                                    style="display:flex;align-items:center;gap:12px;background:var(--adm-bg-light);border:1px solid var(--adm-border);border-radius:var(--adm-radius-sm);padding:14px 16px;">
                                    <div
                                        style="width:38px;height:38px;background:var(--adm-blue-lt);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <svg viewBox="0 0 24 24"
                                            style="width:16px;height:16px;stroke:var(--adm-blue);fill:none;stroke-width:2;">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                            <polyline points="14 2 14 8 20 8" />
                                        </svg>
                                    </div>
                                    <div style="flex:1;min-width:0;">
                                        <div
                                            style="font-size:13px;font-weight:600;color:var(--adm-text-dark);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                            {{ $ticket->file }}</div>
                                        <div style="font-size:11.5px;color:var(--adm-text-faint);">File terlampir</div>
                                    </div>
                                    <a href="{{ asset('storage/' . $ticket->file) }}" target="_blank"
                                        class="adm-btn primary" style="flex-shrink:0;">
                                        <svg viewBox="0 0 24 24">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                            <polyline points="7 10 12 15 17 10" />
                                            <line x1="12" y1="15" x2="12" y2="3" />
                                        </svg>
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>

                {{-- RIGHT: STATUS & META --}}
                <div class="col-lg-4">

                    {{-- STATUS CARD --}}
                    <div class="adm-card" style="margin-bottom:16px;">
                        <div class="adm-card-header">
                            <div class="adm-card-title">
                                <svg viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 6 12 12 16 14" />
                                </svg>
                                Status Tiket
                            </div>
                        </div>
                        <div style="padding:20px;">
                            {{-- Status Steps --}}
                            <div style="display:flex;flex-direction:column;gap:0;">

                                {{-- Open --}}
                                <div
                                    style="display:flex;align-items:flex-start;gap:12px;padding-bottom:18px;position:relative;">
                                    <div
                                        style="position:absolute;left:15px;top:30px;bottom:0;width:2px;background:{{ in_array($ticket->status, ['in_progress', 'closed']) ? 'var(--adm-green)' : 'var(--adm-border)' }};">
                                    </div>
                                    <div
                                        style="width:30px;height:30px;border-radius:50%;background:{{ $ticket->status === 'open' ? 'var(--adm-green)' : (in_array($ticket->status, ['in_progress', 'closed']) ? 'var(--adm-green)' : 'var(--adm-border)') }};display:flex;align-items:center;justify-content:center;flex-shrink:0;z-index:1;">
                                        <svg viewBox="0 0 24 24"
                                            style="width:14px;height:14px;stroke:#fff;fill:none;stroke-width:2.5;">
                                            <polyline points="20 6 9 17 4 12" />
                                        </svg>
                                    </div>
                                    <div style="padding-top:4px;">
                                        <div style="font-size:13px;font-weight:700;color:var(--adm-text-dark);">Open</div>
                                        <div style="font-size:11.5px;color:var(--adm-text-faint);">Tiket berhasil dibuat
                                        </div>
                                    </div>
                                </div>

                                {{-- In Progress --}}
                                <div
                                    style="display:flex;align-items:flex-start;gap:12px;padding-bottom:18px;position:relative;">
                                    <div
                                        style="position:absolute;left:15px;top:30px;bottom:0;width:2px;background:{{ $ticket->status === 'closed' ? 'var(--adm-green)' : 'var(--adm-border)' }};">
                                    </div>
                                    <div
                                        style="width:30px;height:30px;border-radius:50%;background:{{ $ticket->status === 'in_progress' ? 'var(--adm-amber)' : ($ticket->status === 'closed' ? 'var(--adm-green)' : 'var(--adm-border-mid)') }};display:flex;align-items:center;justify-content:center;flex-shrink:0;z-index:1;">
                                        @if ($ticket->status === 'in_progress')
                                            <svg viewBox="0 0 24 24"
                                                style="width:14px;height:14px;stroke:#fff;fill:none;stroke-width:2.5;">
                                                <circle cx="12" cy="12" r="10" />
                                                <polyline points="12 6 12 12 16 14" />
                                            </svg>
                                        @elseif($ticket->status === 'closed')
                                            <svg viewBox="0 0 24 24"
                                                style="width:14px;height:14px;stroke:#fff;fill:none;stroke-width:2.5;">
                                                <polyline points="20 6 9 17 4 12" />
                                            </svg>
                                        @else
                                            <div
                                                style="width:8px;height:8px;border-radius:50%;background:#fff;opacity:.6;">
                                            </div>
                                        @endif
                                    </div>
                                    <div style="padding-top:4px;">
                                        <div
                                            style="font-size:13px;font-weight:700;color:{{ $ticket->status === 'in_progress' ? 'var(--adm-amber)' : 'var(--adm-text-dark)' }};">
                                            In Progress</div>
                                        <div style="font-size:11.5px;color:var(--adm-text-faint);">Sedang ditangani tim
                                        </div>
                                    </div>
                                </div>

                                {{-- Closed --}}
                                <div style="display:flex;align-items:flex-start;gap:12px;">
                                    <div
                                        style="width:30px;height:30px;border-radius:50%;background:{{ $ticket->status === 'closed' ? 'var(--adm-green)' : 'var(--adm-border-mid)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        @if ($ticket->status === 'closed')
                                            <svg viewBox="0 0 24 24"
                                                style="width:14px;height:14px;stroke:#fff;fill:none;stroke-width:2.5;">
                                                <polyline points="20 6 9 17 4 12" />
                                            </svg>
                                        @else
                                            <div
                                                style="width:8px;height:8px;border-radius:50%;background:#fff;opacity:.6;">
                                            </div>
                                        @endif
                                    </div>
                                    <div style="padding-top:4px;">
                                        <div
                                            style="font-size:13px;font-weight:700;color:{{ $ticket->status === 'closed' ? 'var(--adm-green)' : 'var(--adm-text-muted)' }};">
                                            Solved</div>
                                        <div style="font-size:11.5px;color:var(--adm-text-faint);">Tiket telah diselesaikan
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- META INFO --}}
                    <div class="adm-card" style="margin-bottom:16px;">
                        <div class="adm-card-header">
                            <div class="adm-card-title">
                                <svg viewBox="0 0 24 24">
                                    <circle cx="12" cy="8" r="4" />
                                    <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" />
                                </svg>
                                Informasi
                            </div>
                        </div>
                        <div class="adm-info-list" style="padding:0 4px;">
                            <div class="adm-info-row" style="padding:10px 16px;">
                                <span class="adm-info-key">Dibuat</span>
                                <span class="adm-info-val"
                                    style="font-size:12px;font-weight:500;">{{ $ticket->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            <div class="adm-info-row" style="padding:10px 16px;">
                                <span class="adm-info-key">Diperbarui</span>
                                <span class="adm-info-val"
                                    style="font-size:12px;font-weight:500;">{{ $ticket->updated_at->format('d M Y, H:i') }}</span>
                            </div>
                            <div class="adm-info-row" style="padding:10px 16px;">
                                <span class="adm-info-key">Lampiran</span>
                                <span class="adm-info-val">
                                    @if ($ticket->file)
                                        <span class="adm-badge adm-badge-info"><span class="dot"></span> Ada</span>
                                    @else
                                        <span class="adm-badge adm-badge-nonaktif">Tidak ada</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- DANGER ZONE --}}
                    <div class="adm-card" style="border-color:rgba(220,38,38,.2);">
                        <div class="adm-card-header"
                            style="background:var(--adm-red-lt);border-bottom-color:rgba(220,38,38,.15);">
                            <div class="adm-card-title" style="color:var(--adm-red);">
                                <svg viewBox="0 0 24 24" style="stroke:var(--adm-red);">
                                    <path
                                        d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                                    <line x1="12" y1="9" x2="12" y2="13" />
                                    <line x1="12" y1="17" x2="12.01" y2="17" />
                                </svg>
                                Danger Zone
                            </div>
                        </div>
                        <div style="padding:16px;">
                            <p style="font-size:12.5px;color:var(--adm-text-muted);margin-bottom:12px;line-height:1.5;">
                                Tindakan ini tidak dapat dibatalkan. Tiket akan dihapus secara permanen.</p>
                            <form action="{{ route('superadmin.tickets.destroy', $ticket->hashed_id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="adm-btn danger"
                                    style="width:100%;justify-content:center;height:34px;"
                                    onclick="return confirm('Yakin ingin menghapus tiket ini? Tindakan tidak dapat dibatalkan.')">
                                    <svg viewBox="0 0 24 24">
                                        <polyline points="3 6 5 6 21 6" />
                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                                        <path d="M10 11v6M14 11v6" />
                                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                                    </svg>
                                    Hapus Tiket
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
