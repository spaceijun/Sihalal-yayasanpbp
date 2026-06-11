@extends('layouts.app')
@section('template_title')
    {{ $ticket->no_tiket ?? 'Detail Ticket Pendamping' }}
@endsection

@section('content')
    <div class="adm-page">
        <div class="container-fluid">

            {{-- PAGE HEADER --}}
            <div class="adm-header">
                <div class="adm-header-left">
                    <h1>
                        <svg viewBox="0 0 24 24"
                            style="display:inline-block;width:22px;height:22px;stroke:var(--adm-blue);fill:none;stroke-width:2;vertical-align:-4px;margin-right:6px">
                            <rect x="2" y="3" width="20" height="18" rx="3" />
                            <path d="M8 10h8M8 14h5" />
                        </svg>
                        Detail Ticket Pendamping
                    </h1>
                    <p>Informasi detail kendala lapangan dari pendamping/enumerator</p>
                </div>
                <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                    <a href="{{ route('superadmin.ticket-pendampings.index') }}" class="adm-btn-secondary">
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

            <div class="row g-3">
                {{-- LEFT COLUMN: TICKET DETAILS --}}
                <div class="col-lg-8">

                    {{-- IDENTITAS TIKET & ENUMERATOR --}}
                    <div class="adm-card" style="margin-bottom:16px;">
                        <div class="adm-card-header">
                            <div class="adm-card-title">
                                <svg viewBox="0 0 24 24">
                                    <path
                                        d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" />
                                    <circle cx="7" cy="7" r="1" />
                                </svg>
                                Identitas Tiket & Pengirim
                            </div>
                            @if ($ticket->status === 'Open')
                                <span class="adm-badge adm-badge-pending">
                                    <span class="dot"></span> Open
                                </span>
                            @elseif($ticket->status === 'Proses')
                                <span class="adm-badge adm-badge-info">
                                    <span class="dot"></span> Proses
                                </span>
                            @else
                                <span class="adm-badge adm-badge-success">
                                    <span class="dot"></span> Closed
                                </span>
                            @endif
                        </div>
                        <div style="padding:20px;">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div
                                        style="background:var(--adm-bg-light);border:1px solid var(--adm-border);border-radius:var(--adm-radius-sm);padding:12px 14px;">
                                        <div
                                            style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-faint);margin-bottom:4px;">
                                            No. Tiket
                                        </div>
                                        <div class="adm-mono" style="font-size:14px;font-weight:700;color:var(--adm-blue);">
                                            {{ $ticket->no_tiket }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div
                                        style="background:var(--adm-bg-light);border:1px solid var(--adm-border);border-radius:var(--adm-radius-sm);padding:12px 14px;">
                                        <div
                                            style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-faint);margin-bottom:4px;">
                                            Nama Enumerator
                                        </div>
                                        <div style="font-size:14px;font-weight:700;color:var(--adm-text-dark);">
                                            {{ $ticket->user?->enumerator?->nama_lengkap ?? ($ticket->user?->name ?? '—') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div
                                        style="background:var(--adm-bg-light);border:1px solid var(--adm-border);border-radius:var(--adm-radius-sm);padding:12px 14px;">
                                        <div
                                            style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-faint);margin-bottom:4px;">
                                            No Registrasi / HP
                                        </div>
                                        <div style="font-size:13px;font-weight:600;color:var(--adm-text-dark);">
                                            {{ $ticket->user?->enumerator?->no_reg ?? '—' }} / <span
                                                class="adm-mono">{{ $ticket->user?->enumerator?->telephone ?? '—' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div
                                        style="background:var(--adm-bg-light);border:1px solid var(--adm-border);border-radius:var(--adm-radius-sm);padding:12px 14px;">
                                        <div
                                            style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--adm-text-faint);margin-bottom:4px;">
                                            Email Akun
                                        </div>
                                        <div class="adm-mono"
                                            style="font-size:13px;font-weight:600;color:var(--adm-text-dark);">
                                            {{ $ticket->user?->email ?? '—' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DATA LAPANGAN TERKAIT --}}
                    @if ($ticket->dataLapangan)
                        <div class="adm-card" style="margin-bottom:16px;">
                            <div class="adm-card-header">
                                <div class="adm-card-title">
                                    <svg viewBox="0 0 24 24">
                                        <path
                                            d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z" />
                                    </svg>
                                    Data Lapangan Terkait (Pelaku Usaha)
                                </div>
                            </div>
                            <div style="padding:20px;">
                                <div
                                    style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;background:var(--adm-blue-lt);border:1px solid rgba(26,95,200,.12);border-radius:var(--adm-radius-sm);padding:14px 16px;margin-bottom:14px;">
                                    <div>
                                        <div
                                            style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--adm-blue);opacity:.8;margin-bottom:3px;">
                                            Nama Pelaku Usaha (PU)
                                        </div>
                                        <div
                                            style="font-size:15px;font-weight:700;color:var(--adm-text-dark);font-family:'Sora',sans-serif;">
                                            {{ $ticket->dataLapangan->nama_pu }}
                                        </div>
                                    </div>
                                    <a href="{{ route('superadmin.data-lapangans.show', $ticket->dataLapangan->hashed_id) }}"
                                        class="adm-btn primary" target="_blank">
                                        <svg viewBox="0 0 24 24">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                        Buka Detail Data Lapangan
                                    </a>
                                </div>

                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div
                                            style="font-size:11px;font-weight:600;color:var(--adm-text-muted);margin-bottom:3px;">
                                            NIK PU / NIB</div>
                                        <div style="font-size:13px;font-weight:600;color:var(--adm-text-dark);"
                                            class="adm-mono">
                                            {{ $ticket->dataLapangan->nik ?? '—' }}
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div
                                            style="font-size:11px;font-weight:600;color:var(--adm-text-muted);margin-bottom:3px;">
                                            Status Pengajuan</div>
                                        <div>
                                            @if ($ticket->dataLapangan->status === 'TERBIT SH')
                                                <span class="adm-badge adm-badge-success">Terbit SH</span>
                                            @elseif ($ticket->dataLapangan->status === 'REVISI')
                                                <span class="adm-badge adm-badge-pending">Revisi</span>
                                            @elseif ($ticket->dataLapangan->status === 'DITOLAK')
                                                <span class="adm-badge adm-badge-danger">Ditolak</span>
                                            @else
                                                <span
                                                    class="adm-badge adm-badge-info">{{ $ticket->dataLapangan->status }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div
                                            style="font-size:11px;font-weight:600;color:var(--adm-text-muted);margin-bottom:3px;">
                                            Alamat PU</div>
                                        <div style="font-size:12.5px;color:var(--adm-text-mid);line-height:1.5;">
                                            {{ $ticket->dataLapangan->alamat ?? '—' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- ISI KENDALA / DESKRIPSI --}}
                    <div class="adm-card">
                        <div class="adm-card-header">
                            <div class="adm-card-title">
                                <svg viewBox="0 0 24 24">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                    <polyline points="10 9 9 9 8 9" />
                                </svg>
                                Isi Kendala / Masalah Lapangan
                            </div>
                        </div>
                        <div style="padding:20px;">
                            <div
                                style="background:var(--adm-bg-light);border:1px solid var(--adm-border);border-radius:var(--adm-radius-sm);padding:18px;font-size:13.5px;color:var(--adm-text-mid);line-height:1.7;white-space:pre-wrap;min-height:100px;">
                                {{ $ticket->isi_kendala }}</div>
                        </div>
                    </div>

                </div>

                {{-- RIGHT COLUMN: STATUS & FORM ACTIONS --}}
                <div class="col-lg-4">

                    {{-- STATUS STEP TIMELINE --}}
                    <div class="adm-card" style="margin-bottom:16px;">
                        <div class="adm-card-header">
                            <div class="adm-card-title">
                                <svg viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 6 12 12 16 14" />
                                </svg>
                                Timeline Status
                            </div>
                        </div>
                        <div style="padding:20px;">
                            <div style="display:flex;flex-direction:column;gap:0;">

                                {{-- Open Step --}}
                                <div
                                    style="display:flex;align-items:flex-start;gap:12px;padding-bottom:18px;position:relative;">
                                    <div
                                        style="position:absolute;left:15px;top:30px;bottom:0;width:2px;background:{{ in_array($ticket->status, ['Proses', 'Closed']) ? 'var(--adm-green)' : 'var(--adm-border)' }};">
                                    </div>
                                    <div
                                        style="width:30px;height:30px;border-radius:50%;background:{{ in_array($ticket->status, ['Open', 'Proses', 'Closed']) ? 'var(--adm-amber)' : 'var(--adm-border-mid)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;z-index:1;color:#fff;">
                                        <svg viewBox="0 0 24 24"
                                            style="width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2.5;">
                                            <polyline points="20 6 9 17 4 12" />
                                        </svg>
                                    </div>
                                    <div style="padding-top:4px;">
                                        <div style="font-size:13px;font-weight:700;color:var(--adm-text-dark);">Open</div>
                                        <div style="font-size:11.5px;color:var(--adm-text-faint);">Kendala dilaporkan oleh
                                            enumerator</div>
                                    </div>
                                </div>

                                {{-- Proses Step --}}
                                <div
                                    style="display:flex;align-items:flex-start;gap:12px;padding-bottom:18px;position:relative;">
                                    <div
                                        style="position:absolute;left:15px;top:30px;bottom:0;width:2px;background:{{ $ticket->status === 'Closed' ? 'var(--adm-green)' : 'var(--adm-border)' }};">
                                    </div>
                                    <div
                                        style="width:30px;height:30px;border-radius:50%;background:{{ $ticket->status === 'Proses' ? 'var(--adm-blue)' : ($ticket->status === 'Closed' ? 'var(--adm-green)' : 'var(--adm-border-mid)') }};display:flex;align-items:center;justify-content:center;flex-shrink:0;z-index:1;color:#fff;">
                                        @if ($ticket->status === 'Proses')
                                            <svg viewBox="0 0 24 24"
                                                style="width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2.5;">
                                                <circle cx="12" cy="12" r="10" />
                                                <line x1="12" y1="8" x2="12" y2="12" />
                                                <line x1="12" y1="16" x2="12.01" y2="16" />
                                            </svg>
                                        @elseif ($ticket->status === 'Closed')
                                            <svg viewBox="0 0 24 24"
                                                style="width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2.5;">
                                                <polyline points="20 6 9 17 4 12" />
                                            </svg>
                                        @else
                                            <div
                                                style="width:8px;height:8px;border-radius:50%;background:#fff;opacity:.6;">
                                            </div>
                                        @endif
                                    </div>
                                    <div style="padding-top:4px;">
                                        <div style="font-size:13px;font-weight:700;color:var(--adm-text-dark);">Proses
                                        </div>
                                        <div style="font-size:11.5px;color:var(--adm-text-faint);">Sedang ditindaklanjuti
                                            oleh superadmin</div>
                                    </div>
                                </div>

                                {{-- Closed Step --}}
                                <div style="display:flex;align-items:flex-start;gap:12px;">
                                    <div
                                        style="width:30px;height:30px;border-radius:50%;background:{{ $ticket->status === 'Closed' ? 'var(--adm-green)' : 'var(--adm-border-mid)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#fff;">
                                        @if ($ticket->status === 'Closed')
                                            <svg viewBox="0 0 24 24"
                                                style="width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2.5;">
                                                <polyline points="20 6 9 17 4 12" />
                                            </svg>
                                        @else
                                            <div
                                                style="width:8px;height:8px;border-radius:50%;background:#fff;opacity:.6;">
                                            </div>
                                        @endif
                                    </div>
                                    <div style="padding-top:4px;">
                                        <div style="font-size:13px;font-weight:700;color:var(--adm-text-dark);">Closed
                                        </div>
                                        <div style="font-size:11.5px;color:var(--adm-text-faint);">Kendala telah diatasi
                                            dan ditutup</div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- UPDATE STATUS FORM --}}
                    <div class="adm-card" style="margin-bottom:16px;">
                        <div class="adm-card-header">
                            <div class="adm-card-title">
                                <svg viewBox="0 0 24 24">
                                    <polyline points="23 4 23 10 17 10" />
                                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10" />
                                </svg>
                                Tindakan & Status
                            </div>
                        </div>
                        <div style="padding:20px;">
                            <form action="{{ route('superadmin.ticket-pendampings.update-status', $ticket->hashed_id) }}"
                                method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="adm-field mb-3">
                                    <label class="adm-label">Ubah Status</label>
                                    <select name="status" class="adm-field-select w-100" required>
                                        <option value="Open" {{ $ticket->status === 'Open' ? 'selected' : '' }}>Open
                                        </option>
                                        <option value="Proses" {{ $ticket->status === 'Proses' ? 'selected' : '' }}>Proses
                                        </option>
                                        <option value="Closed" {{ $ticket->status === 'Closed' ? 'selected' : '' }}>Closed
                                        </option>
                                    </select>
                                </div>
                                <button type="submit" class="adm-btn-primary w-100"
                                    style="justify-content:center;height:38px;">
                                    Simpan Status
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- META INFO --}}
                    <div class="adm-card" style="margin-bottom:16px;">
                        <div class="adm-card-header">
                            <div class="adm-card-title">
                                <svg viewBox="0 0 24 24">
                                    <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z" />
                                    <path d="M12 16v-4" />
                                    <path d="M12 8h.01" />
                                </svg>
                                Meta Data
                            </div>
                        </div>
                        <div class="adm-info-list">
                            <div class="adm-info-row" style="padding:10px 16px;">
                                <span class="adm-info-key">Dibuat Pada</span>
                                <span class="adm-info-val" style="font-size:12.5px;font-weight:500;">
                                    {{ $ticket->created_at?->format('d M Y, H:i') ?? '—' }}
                                </span>
                            </div>
                            <div class="adm-info-row" style="padding:10px 16px;">
                                <span class="adm-info-key">Terakhir Update</span>
                                <span class="adm-info-val" style="font-size:12.5px;font-weight:500;">
                                    {{ $ticket->updated_at?->format('d M Y, H:i') ?? '—' }}
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
                            <p style="font-size:12px;color:var(--adm-text-muted);margin-bottom:12px;line-height:1.5;">
                                Menghapus tiket ini akan menghilangkan data pengaduan secara permanen.
                            </p>
                            <form action="{{ route('superadmin.ticket-pendampings.destroy', $ticket->hashed_id) }}"
                                method="POST"
                                onsubmit="return confirm('Apakah Anda benar-benar yakin ingin menghapus tiket ini? Tindakan ini tidak dapat dibatalkan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="adm-btn danger w-100"
                                    style="justify-content:center;height:34px;">
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
