@forelse ($recruitments as $recruitment)
    <tr>
        <td><span class="adm-rownum">{{ ($recruitments->currentPage() - 1) * $recruitments->perPage() + $loop->iteration }}</span></td>
        <td style="font-size:12.5px;color:var(--adm-text-muted);">{{ $recruitment->koordinator->nama_lengkap ?? '—' }}</td>
        <td>
            <div class="adm-name-cell">
                <div class="adm-avatar" style="background:var(--adm-blue-lt);color:var(--adm-blue);">
                    {{ strtoupper(substr($recruitment->nama_lengkap, 0, 2)) }}
                </div>
                <a href="{{ route('superadmin.recruitments.show', $recruitment->hashed_id) }}"
                    style="font-weight:600;font-size:13px;color:var(--adm-text-dark);text-decoration:none;">
                    {{ $recruitment->nama_lengkap }}
                </a>
            </div>
        </td>
        <td class="adm-mono" style="font-size:12.5px;">{{ $recruitment->telephone }}</td>
        <td style="font-size:12.5px;">
            @if ($recruitment->rekomendasi)
                <span class="adm-badge adm-badge-success">{{ $recruitment->rekomendasi }}</span>
            @else
                <span style="color:var(--adm-text-faint);">—</span>
            @endif
        </td>
        <td class="tc">
            @if ($recruitment->status === 'Diterima')
                <span class="adm-badge adm-badge-success"><span class="dot"></span>Diterima</span>
            @elseif ($recruitment->status === 'Ditolak')
                <span class="adm-badge adm-badge-danger"><span class="dot"></span>Ditolak</span>
            @else
                <span class="adm-badge adm-badge-pending"><span class="dot"></span>Melamar</span>
            @endif
        </td>
        <td class="tc">
            <div class="adm-actions">
                <a class="adm-btn primary icon-only"
                    href="{{ route('superadmin.recruitments.show', $recruitment->hashed_id) }}"
                    title="Detail">
                    <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </a>
                <button type="button" class="adm-btn danger icon-only delete-btn"
                    data-id="{{ $recruitment->id }}" title="Hapus">
                    <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7">
            <div class="adm-empty">
                <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                <p>Belum ada data recruitment.</p>
            </div>
        </td>
    </tr>
@endforelse
