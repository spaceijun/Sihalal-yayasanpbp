@extends('layouts.app')
@section('template_title')
    {{ $post->nama_loker }} – Daftar Pelamar
@endsection

@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        <div class="adm-header">
            <div class="adm-header-left">
                <h1>{{ $post->nama_loker }}</h1>
                <p>
                    <span class="adm-badge adm-badge-info">{{ $post->posisi }}</span>
                    @if ($post->is_active)
                        <span class="adm-badge adm-badge-success" style="margin-left:6px;"><span
                                class="dot"></span>Aktif</span>
                    @else
                        <span class="adm-badge adm-badge-nonaktif" style="margin-left:6px;"><span
                                class="dot"></span>Nonaktif</span>
                    @endif
                </p>
            </div>
            <div style="display:flex;gap:8px;">
                @if ($post->is_active)
                    <button type="button" class="adm-btn" id="btnCopyLink" style="font-size:12px;padding:6px 14px;">
                        <svg viewBox="0 0 24 24" style="width:13px;height:13px;">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                        </svg>
                        Copy Link
                    </button>
                @endif
                <a href="{{ route('superadmin.recruitment-posts.edit', $post->hashed_id) }}" class="adm-btn success"
                    style="font-size:12px;padding:6px 14px;">
                    <svg viewBox="0 0 24 24" style="width:13px;height:13px;">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                    Edit Lowongan
                </a>
                <a href="{{ route('superadmin.recruitment-posts.index') }}" class="adm-btn-secondary">
                    <svg viewBox="0 0 24 24">
                        <polyline points="15 18 9 12 15 6" />
                    </svg> Kembali
                </a>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start;margin-bottom:16px;">
            {{-- Info Lowongan --}}
            <div class="adm-card">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24">
                            <rect x="2" y="7" width="20" height="14" rx="2" />
                            <path d="M16 7V5a2 2 0 0 0-4 0v2" />
                        </svg>
                        Detail Lowongan
                    </div>
                </div>
                <div style="padding:0 20px;">
                    <div class="adm-info-list">
                        <div class="adm-info-row">
                            <span class="adm-info-key">Slug / URL</span>
                            <span class="adm-info-val adm-mono" style="font-size:12px;">{{ $post->slug }}</span>
                        </div>
                        @if ($post->deskripsi)
                            <div class="adm-info-row">
                                <span class="adm-info-key">Deskripsi</span>
                                <span class="adm-info-val"
                                    style="font-weight:400;color:var(--adm-text-mid);">{{ $post->deskripsi }}</span>
                            </div>
                        @endif
                        @if ($post->tanggal_buka)
                            <div class="adm-info-row">
                                <span class="adm-info-key">Buka</span>
                                <span class="adm-info-val">{{ $post->tanggal_buka->format('d M Y, H:i') }}</span>
                            </div>
                        @endif
                        @if ($post->tanggal_tutup)
                            <div class="adm-info-row">
                                <span class="adm-info-key">Tutup</span>
                                <span class="adm-info-val">{{ $post->tanggal_tutup->format('d M Y, H:i') }}</span>
                            </div>
                        @endif
                        <div class="adm-info-row">
                            <span class="adm-info-key">Total Pelamar</span>
                            <span class="adm-info-val"><strong>{{ $post->recruitments->count() }}</strong> pelamar</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Jobdesk & Requirements --}}
            <div class="adm-card">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24">
                            <path d="M9 11l3 3L22 4" />
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                        </svg>
                        Syarat & Jobdesk
                    </div>
                </div>
                <div style="padding:0 20px 20px;">
                    @if ($post->jobdesk)
                        <div style="margin-bottom:14px;">
                            <p style="font-size:12px;font-weight:600;color:var(--adm-text-muted);margin-bottom:6px;">JOBDESK
                            </p>
                            <div style="font-size:13px;line-height:1.7;white-space:pre-line;color:var(--adm-text-mid);">
                                {{ $post->jobdesk }}</div>
                        </div>
                    @endif
                    @if ($post->requirements && count($post->requirements) > 0)
                        <p style="font-size:12px;font-weight:600;color:var(--adm-text-muted);margin-bottom:8px;">SYARAT
                            PENDAFTARAN ({{ count($post->requirements) }} field)</p>
                        <div style="display:flex;flex-direction:column;gap:6px;">
                            @foreach ($post->requirements as $req)
                                <div
                                    style="display:flex;align-items:center;gap:8px;padding:8px 10px;background:var(--adm-bg-muted);border-radius:6px;">
                                    <span
                                        style="font-size:11px;font-weight:700;color:var(--adm-blue);min-width:16px;">{{ $loop->iteration }}.</span>
                                    <span style="font-size:13px;font-weight:500;flex-grow:1;">{{ $req['label'] }}</span>
                                    <span class="adm-badge"
                                        style="font-size:10px;padding:2px 7px;background:var(--adm-bg-card,#fff);">{{ $req['type'] }}</span>
                                    @if ($req['required'] ?? false)
                                        <span style="font-size:10px;color:var(--adm-red);font-weight:700;">*</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p style="font-size:13px;color:var(--adm-text-faint);">Belum ada syarat yang dikonfigurasi.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Daftar Pelamar --}}
        <div class="adm-card">
            <div class="adm-card-header">
                <div class="adm-card-title">
                    <svg viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                    </svg>
                    Daftar Pelamar
                </div>
            </div>
            <div class="table-responsive">
                <table class="adm-table w-100">
                    <thead>
                        <tr>
                            <th style="width:40px">#</th>
                            <th>Nama Lengkap</th>
                            <th>Telephone</th>
                            @if ($post->posisi === 'PENDAMPING')
                                <th>Koordinator</th>
                            @endif
                            <th class="tc">Status</th>
                            <th>Tanggal Daftar</th>
                            <th class="tc" style="width:100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($post->recruitments->load('koordinator') as $r)
                            <tr>
                                <td class="tc" style="padding-left:20px;">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="adm-name-cell">
                                        <a href="{{ route('superadmin.recruitments.show', $r->hashed_id) }}"
                                            style="font-weight:600;font-size:13px;color:var(--adm-text-dark);text-decoration:none;">{{ $r->nama_lengkap }}</a>
                                    </div>
                                </td>
                                <td class="adm-mono">{{ $r->telephone }}</td>
                                @if ($post->posisi === 'PENDAMPING')
                                    <td>{{ $r->koordinator->nama_lengkap ?? '—' }}</td>
                                @endif
                                <td class="tc">
                                    @if ($r->status === 'Diterima')
                                        <span class="adm-badge adm-badge-success"><span
                                                class="dot"></span>Diterima</span>
                                    @elseif($r->status === 'Ditolak')
                                        <span class="adm-badge adm-badge-danger"><span class="dot"></span>Ditolak</span>
                                    @else
                                        <span class="adm-badge adm-badge-pending"><span class="dot"></span>Melamar</span>
                                    @endif
                                </td>
                                <td class="adm-mono" style="font-size:12px;">{{ $r->created_at->format('d/m/Y H:i') }}</td>
                                <td class="tc" style="padding-right:20px;">
                                    <div class="adm-actions" style="justify-content:center;">
                                        <a class="adm-btn primary icon-only"
                                            href="{{ route('superadmin.recruitments.show', $r->hashed_id) }}"
                                            title="Detail">
                                            <svg viewBox="0 0 24 24">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $post->posisi === 'PENDAMPING' ? 7 : 6 }}"
                                    style="text-align:center;padding:30px;color:var(--adm-text-faint);">Belum ada pelamar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($post->is_active)
        <script>
            document.getElementById('btnCopyLink').addEventListener('click', function() {
                const url = '{{ $post->public_url }}';
                navigator.clipboard.writeText(url).then(() => {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4500,
                        timerProgressBar: true,
                        didOpen: function (toast) {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    });
                    Toast.fire({
                        icon: 'success',
                        title: 'Link pendaftaran berhasil disalin!'
                    });
                });
            });
        </script>
    @endif
@endsection
