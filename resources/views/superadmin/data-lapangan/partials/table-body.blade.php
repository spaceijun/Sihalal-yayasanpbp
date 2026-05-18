@forelse ($dataLapangans as $dataLapangan)
    <tr class="{{ $dataLapangan->is_being_edited && $dataLapangan->edit_expires_at?->isFuture() ? 'is-editing' : '' }}">

        {{-- Checkbox untuk bulk payment --}}
        <td style="width:40px;text-align:center;">
            @if (
                $dataLapangan->status == 'TERBIT SH' &&
                    $dataLapangan->status_pembayaran == 'PENGAJUAN' &&
                    optional($dataLapangan->enumerator)->status === 'Aktif')
                <input type="checkbox" class="row-checkbox adm-checkbox" value="{{ $dataLapangan->hashed_id }}"
                    data-id="{{ $dataLapangan->hashed_id }}">
            @endif
        </td>

        <td><span class="adm-rownum">{{ ++$i }}</span></td>

        <td class="adm-mono" style="font-size:11.5px;white-space:nowrap;">
            {{ \Carbon\Carbon::parse($dataLapangan->created_at)->translatedFormat('d M Y') }}
        </td>

        <td class="adm-mono" style="font-size:12px;white-space:nowrap;">
            {{ $dataLapangan->no_registrasi ?? '—' }}
        </td>

        <td style="font-size:12.5px;color:var(--adm-text-muted);">
            {{ $dataLapangan->enumerator->nama_lengkap ?? '—' }}
        </td>
        <td>
            <div style="font-weight:600;font-size:13px;color:var(--adm-text-dark);">
                <a href="{{ route('superadmin.data-lapangans.show', $dataLapangan->hashed_id) }}"
                    style="color:inherit;text-decoration:none;">
                    {{ $dataLapangan->nama_pu }}
                </a>
            </div>
            @if ($dataLapangan->is_being_edited && $dataLapangan->edit_expires_at?->isFuture())
                <span class="adm-badge adm-badge-pending" style="margin-top:3px;display:inline-flex;">
                    🔒 Diedit oleh {{ $dataLapangan->editedBy->name ?? 'Seseorang' }}
                </span>
            @endif
        </td>

        <td class="adm-mono" style="font-size:12px;">{{ $dataLapangan->nik }}</td>

        <td class="tc">
            @php $s = $dataLapangan->status; @endphp
            @if ($s === 'PENDING')
                <span class="adm-badge adm-badge-pending"><span class="dot"></span>Pending</span>
            @elseif ($s === 'TERVERIFIKASI')
                <span class="adm-badge" style="background:#F1F5F9;color:#475569;border:1px solid #CBD5E1;"><span
                        class="dot" style="background:#64748B;"></span>Terverifikasi</span>
            @elseif ($s === 'PROGRESS OSS')
                <span class="adm-badge adm-badge-info"><span class="dot"></span>Progress OSS</span>
            @elseif ($s === 'PROGRESS SIHALAL')
                <span class="adm-badge" style="background:#EFF6FF;color:#2563EB;border:1px solid #BFDBFE;"><span
                        class="dot" style="background:#2563EB;"></span>Progress SiHalal</span>
            @elseif ($s === 'TERBIT SH')
                <span class="adm-badge adm-badge-success"><span class="dot"></span>Terbit SH</span>
            @elseif ($s === 'DITOLAK')
                <span class="adm-badge adm-badge-danger"><span class="dot"></span>Ditolak</span>
            @elseif ($s === 'REVISI')
                <span class="adm-badge" style="background:#FFF7ED;color:#C2410C;border:1px solid #FED7AA;"><span
                        class="dot" style="background:#C2410C;"></span>Revisi</span>
            @endif
        </td>

        <td class="tc">
            @php $p = $dataLapangan->status_pembayaran; @endphp
            @if ($p === 'PENDING')
                <span class="adm-badge adm-badge-pending"><span class="dot"></span>Pending</span>
            @elseif ($p === 'PENGAJUAN')
                <span class="adm-badge adm-badge-info"><span class="dot"></span>Pengajuan</span>
            @elseif ($p === 'DIBAYAR')
                <span class="adm-badge adm-badge-success"><span class="dot"></span>Dibayar</span>
            @endif
        </td>

        <td class="tc">
            @php
                $tagihan = \Carbon\Carbon::parse($dataLapangan->created_at)->lt(\Carbon\Carbon::create(2026, 5, 1))
                    ? 50000
                    : 60000;
            @endphp
            @if ($dataLapangan->status_pembayaran === 'DIBAYAR')
                <span style="font-size:13px;color:var(--adm-text-muted);letter-spacing:1px;">—</span>
            @else
                <span style="font-size:12.5px;font-weight:600;color:var(--adm-text-dark);">
                    Rp {{ number_format($tagihan, 0, ',', '.') }}
                </span>
            @endif
        </td>

        <td class="tc">
            <div class="adm-actions" style="justify-content:center;gap:4px;">
                <a class="adm-btn primary icon-only"
                    href="{{ route('superadmin.data-lapangans.show', $dataLapangan->hashed_id) }}"
                    title="Lihat Detail">
                    <svg viewBox="0 0 24 24">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                </a>

                @if (
                    $dataLapangan->status == 'TERBIT SH' &&
                        $dataLapangan->status_pembayaran == 'PENGAJUAN' &&
                        optional($dataLapangan->enumerator)->status === 'Aktif')
                    <form
                        action="{{ route('superadmin.data-lapangans.update-status-payment', $dataLapangan->hashed_id) }}"
                        method="POST" class="d-inline">
                        @csrf @method('PATCH')
                        <button type="submit" class="adm-btn success icon-only" title="Tandai Dibayar">
                            <svg viewBox="0 0 24 24">
                                <line x1="12" y1="1" x2="12" y2="23" />
                                <path d="M17 5H9.5a3.5 3.5 0 1 0 0 7h5a3.5 3.5 0 1 1 0 7H6" />
                            </svg>
                        </button>
                    </form>
                @endif

                @if ($dataLapangan->is_being_edited && $dataLapangan->edit_expires_at?->isFuture())
                    <button class="adm-btn warning icon-only btn-force-unlock" data-id="{{ $dataLapangan->id }}"
                        title="Paksa buka kunci">
                        <svg viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0 1 9.9-1" />
                        </svg>
                    </button>
                @endif

                @if ($dataLapangan->status == 'PENDING' || $dataLapangan->status == 'DITOLAK')
                    <form action="{{ route('superadmin.data-lapangans.destroy', $dataLapangan->hashed_id) }}"
                        method="POST" class="delete-form d-inline" data-id="{{ $dataLapangan->id }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="adm-btn danger icon-only" title="Hapus Data">
                            <svg viewBox="0 0 24 24">
                                <polyline points="3 6 5 6 21 6" />
                                <path d="M19 6l-1 14H6L5 6" />
                                <path d="M10 11v6" />
                                <path d="M14 11v6" />
                                <path d="M9 6V4h6v2" />
                            </svg>
                        </button>
                    </form>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="10">
            <div class="adm-empty">
                <svg viewBox="0 0 24 24">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                    <circle cx="12" cy="10" r="3" />
                </svg>
                <p>Belum ada data lapangan.</p>
            </div>
        </td>
    </tr>
@endforelse
