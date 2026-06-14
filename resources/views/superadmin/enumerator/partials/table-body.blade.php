@forelse ($enumerators as $enumerator)
    <tr>
        <td>
            <span class="adm-mono" style="font-size:12px;font-weight:600;color:var(--adm-blue);">
                KH-{{ $enumerator->no_registrasi }}
            </span>
        </td>
        <td>
            <div class="adm-name-cell">
                <div class="adm-avatar" style="background:var(--adm-blue-lt);color:var(--adm-blue);">
                    {{ strtoupper(substr($enumerator->nama_lengkap, 0, 2)) }}
                </div>
                <strong style="font-size:13px;">{{ $enumerator->nama_lengkap }}</strong>
            </div>
        </td>
        <td class="tc">
            @php
                $jumlah = $enumerator->data_bulan_ini ?? 0;
                $kurang = $jumlah < 20;
            @endphp

            <div style="display:flex;flex-direction:column;align-items:center;gap:4px;">
                <span
                    style="
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-width:36px;
            height:28px;
            border-radius:8px;
            font-size:13px;
            font-weight:700;
            padding:0 10px;
            background: {{ $kurang ? 'var(--adm-red-lt, #fff0f0)' : 'var(--adm-green-lt, #f0fff4)' }};
            color: {{ $kurang ? 'var(--adm-red, #e03131)' : 'var(--adm-green, #2f9e44)' }};
            border: 1px solid {{ $kurang ? 'var(--adm-red, #e03131)' : 'var(--adm-green, #2f9e44)' }};">
                    {{ $jumlah }}
                </span>

                @if ($kurang)
                    <span style="font-size:10px;color:var(--adm-red,#e03131);font-weight:500;white-space:nowrap;">
                        ⚠ Kurang {{ 20 - $jumlah }} data
                    </span>
                @else
                    <span style="font-size:10px;color:var(--adm-green,#2f9e44);font-weight:500;">
                        ✓ Tercapai
                    </span>
                @endif
            </div>
        </td>
        <td style="font-size:12px;color:var(--adm-text-muted);">
            @if ($enumerator->bank && $enumerator->no_rekening && $enumerator->nama_rekening)
                {{ $enumerator->bank->name }}, {{ $enumerator->no_rekening }} an. {{ $enumerator->nama_rekening }}
            @else
                <span style="color:var(--adm-text-faint);">—</span>
            @endif
        </td>
        <td class="tc">
            @if ($enumerator->status === 'Aktif')
                <span class="adm-badge adm-badge-success">Aktif</span>
            @else
                <span class="adm-badge adm-badge-nonaktif">Tidak Aktif</span>
            @endif
        </td>
        <td class="tc">
            <div class="adm-actions" style="justify-content:center;flex-wrap:wrap;">
                @if (!$enumerator->user_id)
                    <button type="button" class="adm-btn warning btn-generate-user" data-id="{{ $enumerator->id }}"
                        data-nama="{{ $enumerator->nama_lengkap }}" data-hp="{{ $enumerator->telephone }}"
                        title="Generate akun user">
                        <svg viewBox="0 0 24 24">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="8.5" cy="7" r="4" />
                            <line x1="20" y1="8" x2="20" y2="14" />
                            <line x1="23" y1="11" x2="17" y2="11" />
                        </svg>
                        User
                    </button>
                @endif
                <a class="adm-btn icon-only"
                    href="{{ route($routePrefix . '.enumerators.gallery', $enumerator->hashed_id) }}" title="Galeri">
                    <svg viewBox="0 0 24 24">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <circle cx="8.5" cy="8.5" r="1.5" />
                        <polyline points="21 15 16 10 5 21" />
                    </svg>
                </a>
                <a class="adm-btn primary icon-only"
                    href="{{ route($routePrefix . '.enumerators.show', $enumerator->hashed_id) }}" title="Detail">
                    <svg viewBox="0 0 24 24">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                </a>
                <a class="adm-btn success icon-only"
                    href="{{ route($routePrefix . '.enumerators.edit', $enumerator->hashed_id) }}" title="Edit">
                    <svg viewBox="0 0 24 24">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                </a>
                <button type="button" class="adm-btn danger icon-only btn-delete" data-id="{{ $enumerator->id }}"
                    title="Hapus">
                    <svg viewBox="0 0 24 24">
                        <polyline points="3 6 5 6 21 6" />
                        <path d="M19 6l-1 14H6L5 6" />
                        <path d="M10 11v6" />
                        <path d="M14 11v6" />
                        <path d="M9 6V4h6v2" />
                    </svg>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6">
            <div class="adm-empty">
                <svg viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                </svg>
                <p>Belum ada data enumerator.</p>
            </div>
        </td>
    </tr>
@endforelse
