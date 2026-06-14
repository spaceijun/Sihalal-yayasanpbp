@extends('layouts.app')
@section('template_title')
    Detail Pelamar: {{ $recruitment->nama_lengkap }}
@endsection

@section('content')
    <style>
        /* Modern Profile Sidebar layout */
        .rc-detail-grid {
            display: grid;
            grid-template-columns: 340px 1fr;
            gap: 20px;
            align-items: start;
            margin-bottom: 24px;
        }

        .rc-profile-card {
            background: #fff;
            border: 1px solid var(--adm-border);
            border-radius: var(--adm-radius);
            box-shadow: var(--adm-shadow-sm);
            padding: 24px 20px;
            text-align: center;
            position: relative;
        }

        .rc-profile-avatar {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--adm-blue) 0%, var(--adm-blue-dk) 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 700;
            font-family: 'Sora', sans-serif;
            margin: 0 auto 14px;
            box-shadow: 0 4px 12px rgba(26, 95, 200, 0.22);
        }

        .rc-profile-name {
            font-family: 'Sora', sans-serif;
            font-size: 17px;
            font-weight: 700;
            color: var(--adm-text-dark);
            margin: 0 0 4px;
            line-height: 1.3;
        }

        .rc-profile-position {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--adm-text-muted);
            margin-bottom: 12px;
        }

        .rc-profile-status {
            display: inline-block;
            margin-bottom: 20px;
        }

        .rc-section-title {
            font-family: 'Sora', sans-serif;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--adm-text-muted);
            margin: 20px 0 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid var(--adm-border);
            text-align: left;
        }

        .rc-action-panel {
            padding: 16px;
            background: var(--adm-bg-light, #f8f9fc);
            border-radius: var(--adm-radius-sm);
            border: 1px solid var(--adm-border-mid);
            margin-top: 20px;
            text-align: left;
        }

        .rc-action-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--adm-text-dark);
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        /* Requirements Cards */
        .rc-req-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 14px;
        }

        .rc-req-card {
            background: #fff;
            border: 1px solid var(--adm-border);
            border-radius: var(--adm-radius-sm);
            padding: 14px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 8px;
            transition: transform 0.15s, box-shadow 0.15s;
        }

        .rc-req-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--adm-shadow-sm);
            border-color: var(--adm-border-mid);
        }

        .rc-req-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .rc-req-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--adm-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .rc-req-val {
            font-size: 13px;
            color: var(--adm-text-dark);
            font-weight: 500;
            line-height: 1.4;
        }

        .rc-doc-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: var(--adm-blue-lt);
            border: 1px solid rgba(26, 95, 200, 0.12);
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            color: var(--adm-blue);
            text-decoration: none;
            transition: background 0.12s;
        }

        .rc-doc-link:hover {
            background: var(--adm-blue);
            color: #fff;
        }

        .rc-doc-link:hover svg {
            stroke: #fff;
        }

        .rc-doc-link svg {
            width: 14px;
            height: 14px;
            stroke: var(--adm-blue);
            fill: none;
            stroke-width: 2;
        }

        .rc-image-preview {
            position: relative;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid var(--adm-border-mid);
            background: var(--adm-bg-light);
            margin-top: 6px;
            cursor: pointer;
        }

        .rc-image-preview img {
            display: block;
            width: 100%;
            height: auto;
            max-height: 130px;
            object-fit: cover;
            transition: transform 0.2s;
        }

        .rc-image-preview:hover img {
            transform: scale(1.05);
        }

        .rc-detail-section {
            background: #fff;
            border: 1px solid var(--adm-border);
            border-radius: var(--adm-radius);
            box-shadow: var(--adm-shadow-sm);
            padding: 20px;
            margin-bottom: 20px;
        }

        .rc-section-header {
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            font-weight: 700;
            color: var(--adm-text-dark);
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--adm-border);
        }

        .rc-section-header svg {
            width: 16px;
            height: 16px;
            stroke: var(--adm-blue);
            fill: none;
            stroke-width: 2;
        }

        @media (max-width: 768px) {
            .rc-detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="adm-page">
        @include('layouts.messages')

        {{-- Page Header --}}
        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Detail Data Pelamar</h1>
                <p>Verifikasi informasi dan kelayakan dokumen pendaftaran pelamar</p>
            </div>
            <div>
                @if ($recruitment->recruitmentPost)
                    <a href="{{ route($routePrefix . '.recruitment-posts.show', $recruitment->recruitmentPost->hashed_id) }}"
                        class="adm-btn-secondary">
                        <svg viewBox="0 0 24 24">
                            <polyline points="15 18 9 12 15 6" />
                        </svg> Kembali ke Lowongan
                    </a>
                @else
                    <a href="{{ route($routePrefix . '.recruitment-posts.index') }}" class="adm-btn-secondary">
                        <svg viewBox="0 0 24 24">
                            <polyline points="15 18 9 12 15 6" />
                        </svg> Kembali
                    </a>
                @endif
            </div>
        </div>

        {{-- Dashboard Content Grid --}}
        <div class="rc-detail-grid">

            {{-- Left Sidebar Profile Card --}}
            <div>
                <div class="rc-profile-card">
                    <div class="rc-profile-avatar">
                        {{ strtoupper(substr($recruitment->nama_lengkap, 0, 2)) }}
                    </div>
                    <h2 class="rc-profile-name">{{ $recruitment->nama_lengkap }}</h2>
                    <p class="rc-profile-position">
                        {{ $recruitment->recruitmentPost->nama_loker ?? $recruitment->recruit_type }}</p>

                    <div class="rc-profile-status">
                        @if ($recruitment->status === 'Diterima')
                            <span class="adm-badge adm-badge-success"><span class="dot"></span>Diterima</span>
                        @elseif($recruitment->status === 'Ditolak')
                            <span class="adm-badge adm-badge-danger"><span class="dot"></span>Ditolak</span>
                        @else
                            <span class="adm-badge adm-badge-pending"><span class="dot"></span>Melamar</span>
                        @endif
                    </div>

                    {{-- Quick Information List --}}
                    <div class="rc-section-title">Informasi Utama</div>
                    <div class="adm-info-list" style="text-align: left;">
                        <div class="adm-info-row">
                            <span class="adm-info-key">NIK</span>
                            <span class="adm-info-val adm-mono">{{ $recruitment->nik ?? '-' }}</span>
                        </div>
                        <div class="adm-info-row">
                            <span class="adm-info-key">Telepon</span>
                            <span class="adm-info-val adm-mono">{{ $recruitment->telephone }}</span>
                        </div>
                        <div class="adm-info-row">
                            <span class="adm-info-key">Gender</span>
                            <span class="adm-info-val">{{ $recruitment->jenis_kelamin ?? '-' }}</span>
                        </div>
                        <div class="adm-info-row">
                            <span class="adm-info-key">Pendidikan</span>
                            <span class="adm-info-val">{{ $recruitment->pendidikan_terakhir }}</span>
                        </div>
                        @if ($recruitment->recruit_type === 'PENDAMPING')
                            <div class="adm-info-row">
                                <span class="adm-info-key">Koordinator</span>
                                <span class="adm-info-val">
                                    @if ($recruitment->koordinator)
                                        <span
                                            style="color:var(--adm-green);font-weight:700;">{{ $recruitment->koordinator->nama_lengkap }}</span>
                                    @else
                                        <span style="color:var(--adm-text-faint);">Belum ditentukan</span>
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>

                    @if ($recruitment->status === 'Ditolak' && $recruitment->alasan_penolakan)
                        <div class="rc-action-panel"
                            style="background:var(--adm-red-lt);border-color:rgba(220, 38, 38, 0.12);">
                            <div class="rc-action-title" style="color:var(--adm-red);">Alasan Penolakan</div>
                            <p style="font-size:12.5px;color:var(--adm-red);margin:0;line-height:1.4;">
                                {{ $recruitment->alasan_penolakan }}</p>
                        </div>
                    @endif

                    {{-- Actions Panel --}}
                    @if ($recruitment->status === 'Melamar')
                        <div class="rc-action-panel">
                            <div class="rc-action-title">Keputusan Lamaran</div>
                            <div style="display:flex;flex-direction:column;gap:8px;margin-top:10px;">
                                <button type="button" class="adm-btn-primary adm-btn-success" id="btnTerima"
                                    style="justify-content:center;">
                                    <svg viewBox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg> Terima Pelamar
                                </button>
                                <button type="button" class="adm-btn-primary adm-btn-danger" id="btnTolak"
                                    style="justify-content:center;background:linear-gradient(135deg, var(--adm-red) 0%, #a61c1c 100%);box-shadow: 0 2px 8px rgba(220, 38, 38, 0.25);">
                                    <svg viewBox="0 0 24 24">
                                        <line x1="18" y1="6" x2="6" y2="18" />
                                        <line x1="6" y1="6" x2="18" y2="18" />
                                    </svg> Tolak Pelamar
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right Main Content --}}
            <div>
                {{-- Alamat & Pengalaman Card --}}
                <div class="rc-detail-section">
                    <div class="rc-section-header">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 2a10 10 0 0 0-10 10c0 5.25 10 12 10 12s10-6.75 10-12a10 10 0 0 0-10-10z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        Alamat & Pengalaman Kerja
                    </div>
                    <div class="adm-info-list" style="gap:14px;">
                        <div>
                            <span
                                style="font-size:11.5px;font-weight:700;color:var(--adm-text-muted);text-transform:uppercase;display:block;margin-bottom:4px;">Alamat
                                Lengkap</span>
                            <div style="font-size:13.5px;line-height:1.5;color:var(--adm-text-mid);">
                                {{ $recruitment->alamat_lengkap }}</div>
                        </div>
                        <div style="border-top: 1px solid var(--adm-border);padding-top:14px;">
                            <span
                                style="font-size:11.5px;font-weight:700;color:var(--adm-text-muted);text-transform:uppercase;display:block;margin-bottom:4px;">Pengalaman
                                Kerja / Organisasi</span>
                            <div style="font-size:13.5px;line-height:1.6;color:var(--adm-text-mid);white-space:pre-line;">
                                {{ $recruitment->pengalaman }}</div>
                        </div>
                    </div>
                </div>

                {{-- Persyaratan & Dokumen Card --}}
                <div class="rc-detail-section">
                    <div class="rc-section-header">
                        <svg viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                            <line x1="16" y1="13" x2="8" y2="13" />
                            <line x1="16" y1="17" x2="8" y2="17" />
                            <polyline points="10 9 9 9 8 9" />
                        </svg>
                        Persyaratan & Berkas Pendaftaran
                    </div>

                    @if ($recruitment->recruitmentPost && $recruitment->recruitmentPost->requirements)
                        @php
                            $coreFields = [
                                'nama_lengkap',
                                'telephone',
                                'alamat_lengkap',
                                'pengalaman',
                                'rekomendasi',
                                'pendidikan_terakhir',
                                'foto_diri',
                                'foto_ktp',
                                'foto_ijasah',
                                'pakta_integritas',
                                'nik',
                                'jenis_kelamin',
                                'type_entry',
                            ];
                        @endphp
                        <div class="rc-req-grid">
                            @foreach ($recruitment->recruitmentPost->requirements as $req)
                                @php
                                    $fieldKey = $req['field_key'];
                                    $value = in_array($fieldKey, $coreFields)
                                        ? $recruitment->$fieldKey
                                        : $recruitment->answers[$fieldKey] ?? null;
                                    // Lewati field utama jika sudah ditampilkan di sidebar kiri
                                    if (
                                        in_array($fieldKey, [
                                            'nama_lengkap',
                                            'telephone',
                                            'alamat_lengkap',
                                            'pengalaman',
                                            'pendidikan_terakhir',
                                            'nik',
                                            'jenis_kelamin',
                                        ])
                                    ) {
                                        continue;
                                    }
                                @endphp
                                <div class="rc-req-card">
                                    <div class="rc-req-header">
                                        <span class="rc-req-label">{{ $req['label'] }}</span>
                                        <span class="adm-badge"
                                            style="font-size:9.5px;padding:1px 6px;background:var(--adm-bg-light);border:1px solid var(--adm-border);">{{ strtoupper($req['type']) }}</span>
                                    </div>
                                    <div class="rc-req-val">
                                        @if ($req['type'] === 'file')
                                            @if ($value)
                                                <a href="{{ asset('storage/' . $value) }}" target="_blank"
                                                    class="rc-doc-link">
                                                    <svg viewBox="0 0 24 24">
                                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                                        <polyline points="7 10 12 15 17 10" />
                                                        <line x1="12" y1="15" x2="12"
                                                            y2="3" />
                                                    </svg>
                                                    Unduh / Lihat Dokumen
                                                </a>
                                                {{-- Preview Gambar --}}
                                                @if (in_array(strtolower(pathinfo($value, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']))
                                                    <div class="rc-image-preview"
                                                        onclick="window.open('{{ asset('storage/' . $value) }}', '_blank')">
                                                        <img src="{{ asset('storage/' . $value) }}"
                                                            alt="{{ $req['label'] }}">
                                                    </div>
                                                @endif
                                            @else
                                                <span
                                                    style="color:var(--adm-text-faint);font-style:italic;font-size:12.5px;">Tidak
                                                    diunggah</span>
                                            @endif
                                        @elseif($req['type'] === 'checkbox')
                                            @if ($value === '1' || $value === 1 || $value === 'on')
                                                <span class="adm-badge adm-badge-success" style="font-size:11px;"><span
                                                        class="dot"></span>Ya / Setuju</span>
                                            @else
                                                <span class="adm-badge adm-badge-nonaktif" style="font-size:11px;"><span
                                                        class="dot"></span>Tidak Setuju</span>
                                            @endif
                                        @else
                                            <span style="font-weight:600;">{{ $value ?? '-' }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Fallback lama --}}
                        <div class="rc-req-grid">
                            @foreach (['foto_diri' => 'Foto Diri', 'foto_ktp' => 'Foto KTP', 'foto_ijasah' => 'Foto Ijazah', 'rekomendasi' => 'Surat Rekomendasi', 'pakta_integritas' => 'Pakta Integritas'] as $key => $label)
                                @if ($recruitment->$key)
                                    <div class="rc-req-card">
                                        <div class="rc-req-header">
                                            <span class="rc-req-label">{{ $label }}</span>
                                            <span class="adm-badge"
                                                style="font-size:9.5px;padding:1px 6px;background:var(--adm-bg-light);border:1px solid var(--adm-border);">FILE</span>
                                        </div>
                                        <div class="rc-req-val">
                                            <a href="{{ asset('storage/' . $recruitment->$key) }}" target="_blank"
                                                class="rc-doc-link">
                                                <svg viewBox="0 0 24 24">
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                                    <polyline points="7 10 12 15 17 10" />
                                                    <line x1="12" y1="15" x2="12" y2="3" />
                                                </svg>
                                                Lihat Berkas
                                            </a>
                                            @if (in_array(strtolower(pathinfo($recruitment->$key, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']))
                                                <div class="rc-image-preview"
                                                    onclick="window.open('{{ asset('storage/' . $recruitment->$key) }}', '_blank')">
                                                    <img src="{{ asset('storage/' . $recruitment->$key) }}"
                                                        alt="{{ $label }}">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modals Tindakan Status --}}
    @if ($recruitment->status === 'Melamar')
        {{-- Modal Terima --}}
        <div class="modal fade" id="modalTerima" tabindex="-1" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content"
                    style="border-radius:12px;border:none;box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                    <form action="{{ route($routePrefix . '.recruitments.update-status-v2', $recruitment->hashed_id) }}"
                        method="POST">
                        @csrf
                        <input type="hidden" name="status" value="Diterima">
                        <div class="modal-header" style="border-bottom:1px solid #f3f4f6;padding:16px 20px;">
                            <h5 class="modal-title" style="font-weight:700;color:#fff;font-size:16px;margin:0;">Terima
                                Pelamar</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                style="background:none;border:none;font-size:22px;color:#fff;cursor:pointer;line-height:1;">&times;</button>
                        </div>
                        <div class="modal-body" style="padding:20px;">
                            <p style="font-size:14px;color:var(--adm-text-mid);margin-bottom:16px;">Apakah Anda yakin ingin
                                menerima pelamar <strong>{{ $recruitment->nama_lengkap }}</strong>?</p>

                            @if ($recruitment->recruit_type === 'PENDAMPING')
                                <div class="adm-field">
                                    <label class="adm-label">Pilih Koordinator <span class="req">*</span></label>
                                    <select name="koordinator_id" class="adm-field-select" required>
                                        <option value="">-- Pilih Koordinator --</option>
                                        @foreach ($koordinators as $koordinator)
                                            <option value="{{ $koordinator->id }}">{{ $koordinator->nama_lengkap }}
                                                ({{ $koordinator->kecamatan ?? 'No Kecamatan' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer"
                            style="border-top:1px solid #f3f4f6;padding:12px 20px;display:flex;justify-content:flex-end;gap:10px;">
                            <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal"
                                style="padding:8px 16px;">Batal</button>
                            <button type="submit" class="adm-btn-primary adm-btn-success"
                                style="padding:8px 20px;height:36px;">Ya, Terima</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Tolak --}}
        <div class="modal fade" id="modalTolak" tabindex="-1" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content"
                    style="border-radius:12px;border:none;box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                    <form action="{{ route($routePrefix . '.recruitments.update-status-v2', $recruitment->hashed_id) }}"
                        method="POST">
                        @csrf
                        <input type="hidden" name="status" value="Ditolak">
                        <div class="modal-header"
                            style="border-bottom:1px solid #f3f4f6;padding:16px 20px;background:linear-gradient(135deg, var(--adm-red) 0%, #a61c1c 100%);">
                            <h5 class="modal-title" style="font-weight:700;color:#fff;font-size:16px;margin:0;">Tolak
                                Pelamar</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                style="background:none;border:none;font-size:22px;color:#fff;cursor:pointer;line-height:1;">&times;</button>
                        </div>
                        <div class="modal-body" style="padding:20px;">
                            <p style="font-size:14px;color:var(--adm-text-mid);margin-bottom:16px;">Apakah Anda yakin ingin
                                menolak pelamar <strong>{{ $recruitment->nama_lengkap }}</strong>?</p>
                            <div class="adm-field">
                                <label class="adm-label">Alasan Penolakan <span class="req">*</span></label>
                                <textarea name="alasan_penolakan" class="adm-textarea" rows="4" required
                                    placeholder="Tuliskan alasan penolakan..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer"
                            style="border-top:1px solid #f3f4f6;padding:12px 20px;display:flex;justify-content:flex-end;gap:10px;">
                            <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal"
                                style="padding:8px 16px;">Batal</button>
                            <button type="submit" class="adm-btn-primary adm-btn-danger"
                                style="padding:8px 20px;height:36px;background:linear-gradient(135deg, var(--adm-red) 0%, #a61c1c 100%);">Ya,
                                Tolak</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    @if ($recruitment->status === 'Melamar')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modalTerima = new bootstrap.Modal(document.getElementById('modalTerima'));
                const modalTolak = new bootstrap.Modal(document.getElementById('modalTolak'));

                document.getElementById('btnTerima').addEventListener('click', function() {
                    modalTerima.show();
                });

                document.getElementById('btnTolak').addEventListener('click', function() {
                    modalTolak.show();
                });
            });
        </script>
    @endif
@endpush
