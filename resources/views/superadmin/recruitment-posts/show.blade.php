@extends('layouts.app')
@section('template_title')
    {{ $post->nama_loker }} – Detail Lowongan
@endsection

@section('content')
    <style>
        /* Modern layouts for the recruitment post details page */
        .post-details-grid {
            display: grid;
            grid-template-columns: 380px 1fr;
            gap: 20px;
            align-items: start;
            margin-bottom: 20px;
        }

        .req-list-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .req-list-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            background: var(--adm-bg-light, #f8f9fc);
            border: 1px solid var(--adm-border-mid);
            border-radius: var(--adm-radius-sm);
            font-size: 13px;
        }

        .req-list-num {
            font-weight: 700;
            color: var(--adm-blue);
            margin-right: 6px;
        }

        .req-list-label {
            font-weight: 500;
            color: var(--adm-text-dark);
            flex-grow: 1;
        }

        .jobdesk-container {
            font-size: 13.5px;
            line-height: 1.7;
            color: var(--adm-text-mid);
            white-space: pre-line;
            background: var(--adm-bg-light, #f8f9fc);
            border-radius: var(--adm-radius-sm);
            padding: 16px;
            border: 1px solid var(--adm-border-mid);
        }

        @media (max-width: 992px) {
            .post-details-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="adm-page">
        @include('layouts.messages')

        {{-- Page Header --}}
        <div class="adm-header">
            <div class="adm-header-left">
                <h1>{{ $post->nama_loker }}</h1>
                <p style="margin-top: 4px;">
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
                <a href="{{ route($routePrefix . '.recruitment-posts.edit', $post->hashed_id) }}" class="adm-btn success"
                    style="font-size:12px;padding:6px 14px;">
                    <svg viewBox="0 0 24 24" style="width:13px;height:13px;">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                    Edit Lowongan
                </a>
                <a href="{{ route($routePrefix . '.recruitment-posts.index') }}" class="adm-btn-secondary">
                    <svg viewBox="0 0 24 24">
                        <polyline points="15 18 9 12 15 6" />
                    </svg> Kembali
                </a>
            </div>
        </div>

        {{-- Stats Row --}}
        @php
            $recruitments = $post->recruitments;
            $countTotal = $recruitments->count();
            $countPending = $recruitments->where('status', 'Melamar')->count();
            $countSuccess = $recruitments->where('status', 'Diterima')->count();
            $countDanger = $recruitments->where('status', 'Ditolak')->count();
        @endphp
        <div class="adm-stats">
            <div class="adm-stat">
                <div class="adm-stat-label">Total Pelamar</div>
                <div class="adm-stat-value">{{ $countTotal }}</div>
                <div class="adm-stat-sub">Semua pendaftar masuk</div>
            </div>
            <div class="adm-stat">
                <div class="adm-stat-label">Belum Diproses</div>
                <div class="adm-stat-value is-warn">{{ $countPending }}</div>
                <div class="adm-stat-sub">Menunggu evaluasi</div>
            </div>
            <div class="adm-stat">
                <div class="adm-stat-label">Diterima</div>
                <div class="adm-stat-value is-success">{{ $countSuccess }}</div>
                <div class="adm-stat-sub">Lulus verifikasi</div>
            </div>
            <div class="adm-stat">
                <div class="adm-stat-label">Ditolak</div>
                <div class="adm-stat-value is-danger">{{ $countDanger }}</div>
                <div class="adm-stat-sub">Tidak memenuhi kriteria</div>
            </div>
        </div>

        {{-- Middle Layout Columns --}}
        <div class="post-details-grid">

            {{-- Left column: settings & requirements --}}
            <div>
                {{-- Detail Settings --}}
                <div class="adm-card">
                    <div class="adm-card-header">
                        <div class="adm-card-title">
                            <svg viewBox="0 0 24 24">
                                <rect x="2" y="7" width="20" height="14" rx="2" />
                                <path d="M16 7V5a2 2 0 0 0-4 0v2" />
                            </svg>
                            Konfigurasi Lowongan
                        </div>
                    </div>
                    <div style="padding:0 20px 10px;">
                        <div class="adm-info-list">
                            <div class="adm-info-row">
                                <span class="adm-info-key">Slug Link</span>
                                <span class="adm-info-val adm-mono">{{ $post->slug }}</span>
                            </div>
                            @if ($post->tanggal_buka)
                                <div class="adm-info-row">
                                    <span class="adm-info-key">Tanggal Buka</span>
                                    <span class="adm-info-val">{{ $post->tanggal_buka->format('d M Y - H:i') }}</span>
                                </div>
                            @else
                                <div class="adm-info-row">
                                    <span class="adm-info-key">Tanggal Buka</span>
                                    <span class="adm-info-val" style="color:var(--adm-text-faint);">Langsung Terbuka</span>
                                </div>
                            @endif
                            @if ($post->tanggal_tutup)
                                <div class="adm-info-row">
                                    <span class="adm-info-key">Tanggal Tutup</span>
                                    <span class="adm-info-val">{{ $post->tanggal_tutup->format('d M Y - H:i') }}</span>
                                </div>
                            @else
                                <div class="adm-info-row">
                                    <span class="adm-info-key">Tanggal Tutup</span>
                                    <span class="adm-info-val" style="color:var(--adm-text-faint);">Tanpa Batas Akhir</span>
                                </div>
                            @endif
                            @if ($post->template_pakta_integritas)
                                <div class="adm-info-row">
                                    <span class="adm-info-key">Template Pakta</span>
                                    <span class="adm-info-val">
                                        <a href="{{ asset('storage/' . $post->template_pakta_integritas) }}"
                                            target="_blank"
                                            style="color:var(--adm-blue);font-weight:700;text-decoration:none;">
                                            Unduh Template
                                        </a>
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Requirements list --}}
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
                            Builder Syarat Form
                        </div>
                        <span class="adm-count-badge">{{ count($post->requirements ?? []) }} Field</span>
                    </div>
                    <div style="padding:15px 20px 20px;">
                        @if ($post->requirements && count($post->requirements) > 0)
                            <div class="req-list-group">
                                @foreach ($post->requirements as $req)
                                    <div class="req-list-item">
                                        <div>
                                            <span class="req-list-num">{{ $loop->iteration }}.</span>
                                            <span class="req-list-label">{{ $req['label'] }}</span>
                                            @if ($req['required'] ?? false)
                                                <span style="color:var(--adm-red);font-weight:700;">*</span>
                                            @endif
                                        </div>
                                        <span class="adm-badge"
                                            style="font-size:9.5px;background:#fff;border:1px solid var(--adm-border);padding:1px 6px;">
                                            {{ strtoupper($req['type']) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div style="text-align:center;padding:12px;color:var(--adm-text-faint);font-size:13px;">Belum
                                ada syarat khusus yang dikonfigurasi.</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right column: description & jobdesk & applicants table --}}
            <div>
                <div class="adm-card">
                    <div class="adm-card-header">
                        <div class="adm-card-title">
                            <svg viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <polyline points="14 2 14 8 20 8" />
                            </svg>
                            Deskripsi & Jobdesk
                        </div>
                    </div>
                    <div style="padding:0 20px 20px;">
                        @if ($post->deskripsi)
                            <div style="margin-bottom:16px;">
                                <span
                                    style="font-size:11px;font-weight:700;color:var(--adm-text-muted);text-transform:uppercase;display:block;margin-bottom:6px;letter-spacing:0.05em;">Deskripsi
                                    Singkat</span>
                                <p style="font-size:13.5px;line-height:1.6;color:var(--adm-text-mid);margin:0;">
                                    {{ $post->deskripsi }}</p>
                            </div>
                        @endif

                        <div>
                            <span
                                style="font-size:11px;font-weight:700;color:var(--adm-text-muted);text-transform:uppercase;display:block;margin-bottom:6px;letter-spacing:0.05em;">Tugas
                                & Tanggung Jawab (Jobdesk)</span>
                            @if ($post->jobdesk)
                                <div class="jobdesk-container">{{ $post->jobdesk }}</div>
                            @else
                                <div style="color:var(--adm-text-faint);font-size:13px;font-style:italic;">Belum menuliskan
                                    tugas deskripsi pekerjaan.</div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Applicants List Table --}}
                <div class="adm-card">
                    <div class="adm-card-header">
                        <div class="adm-card-title">
                            <svg viewBox="0 0 24 24">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                            </svg>
                            Daftar Pelamar Pekerjaan
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="adm-table w-100">
                            <thead>
                                <tr>
                                    <th style="width:40px;padding-left:20px;">#</th>
                                    <th>Nama Lengkap</th>
                                    <th>No. Telephone</th>
                                    @if ($post->posisi === 'PENDAMPING')
                                        <th>Koordinator</th>
                                    @endif
                                    <th class="tc" style="width:120px;">Status</th>
                                    <th>Tanggal Daftar</th>
                                    <th class="tc" style="width:100px;padding-right:20px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($post->recruitments->load('koordinator') as $r)
                                    <tr>
                                        <td class="tc" style="padding-left:20px;">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="adm-name-cell">
                                                <a href="{{ route($routePrefix . '.recruitments.show', $r->hashed_id) }}"
                                                    style="font-weight:600;font-size:13.5px;color:var(--adm-text-dark);text-decoration:none;">{{ $r->nama_lengkap }}</a>
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
                                                <span class="adm-badge adm-badge-danger"><span
                                                        class="dot"></span>Ditolak</span>
                                            @else
                                                <span class="adm-badge adm-badge-pending"><span
                                                        class="dot"></span>Melamar</span>
                                            @endif
                                        </td>
                                        <td class="adm-mono" style="font-size:12px;">
                                            {{ $r->created_at->format('d M Y - H:i') }}
                                        </td>
                                        <td class="tc" style="padding-right:20px;">
                                            <div class="adm-actions" style="justify-content:center;">
                                                <a class="adm-btn primary icon-only"
                                                    href="{{ route($routePrefix . '.recruitments.show', $r->hashed_id) }}"
                                                    title="Buka Detail">
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
                                            style="text-align:center;padding:40px;color:var(--adm-text-faint);">Belum ada
                                            pelamar
                                            pendaftar di lowongan ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
                            didOpen: function(toast) {
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
