@extends('layouts.app')
@section('template_title')
    Detail Pelamar: {{ $recruitment->nama_lengkap }}
@endsection

@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Detail Pelamar</h1>
                <p style="margin-top: 4px;">
                    <span class="adm-badge adm-badge-info">{{ $recruitment->recruitmentPost->nama_loker ?? $recruitment->recruit_type }}</span>
                    @if ($recruitment->status === 'Diterima')
                        <span class="adm-badge adm-badge-success" style="margin-left:6px;"><span class="dot"></span>Diterima</span>
                    @elseif($recruitment->status === 'Ditolak')
                        <span class="adm-badge adm-badge-danger" style="margin-left:6px;"><span class="dot"></span>Ditolak</span>
                    @else
                        <span class="adm-badge adm-badge-pending" style="margin-left:6px;"><span class="dot"></span>Melamar</span>
                    @endif
                </p>
            </div>
            <div style="display:flex;gap:8px;">
                @if($recruitment->recruitmentPost)
                    <a href="{{ route('superadmin.recruitment-posts.show', $recruitment->recruitmentPost->hashed_id) }}" class="adm-btn-secondary">
                        <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6" /></svg> Kembali ke Lowongan
                    </a>
                @else
                    <a href="{{ route('superadmin.recruitment-posts.index') }}" class="adm-btn-secondary">
                        <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6" /></svg> Kembali
                    </a>
                @endif
            </div>
        </div>

        <div style="display:grid;grid-template-columns: 1fr 1fr;gap:16px;align-items:start;margin-bottom:16px;">
            {{-- Data Diri & Core Fields --}}
            <div class="adm-card">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        Informasi Utama Pelamar
                    </div>
                </div>
                <div style="padding:0 20px 20px;">
                    <div class="adm-info-list">
                        <div class="adm-info-row">
                            <span class="adm-info-key">Nama Lengkap</span>
                            <span class="adm-info-val" style="font-weight:600;color:var(--adm-text-dark);">{{ $recruitment->nama_lengkap }}</span>
                        </div>
                        <div class="adm-info-row">
                            <span class="adm-info-key">NIK (KTP)</span>
                            <span class="adm-info-val adm-mono">{{ $recruitment->nik ?? '-' }}</span>
                        </div>
                        <div class="adm-info-row">
                            <span class="adm-info-key">Jenis Kelamin</span>
                            <span class="adm-info-val">{{ $recruitment->jenis_kelamin ?? '-' }}</span>
                        </div>
                        <div class="adm-info-row">
                            <span class="adm-info-key">No. Telephone</span>
                            <span class="adm-info-val adm-mono">{{ $recruitment->telephone }}</span>
                        </div>
                        <div class="adm-info-row">
                            <span class="adm-info-key">Pendidikan Terakhir</span>
                            <span class="adm-info-val">{{ $recruitment->pendidikan_terakhir }}</span>
                        </div>
                        <div class="adm-info-row">
                            <span class="adm-info-key">Alamat Lengkap</span>
                            <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $recruitment->alamat_lengkap }}</span>
                        </div>
                        <div class="adm-info-row">
                            <span class="adm-info-key">Pengalaman</span>
                            <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);white-space:pre-line;">{{ $recruitment->pengalaman }}</span>
                        </div>
                        @if ($recruitment->recruit_type === 'PENDAMPING')
                            <div class="adm-info-row">
                                <span class="adm-info-key">Koordinator</span>
                                <span class="adm-info-val">
                                    @if ($recruitment->koordinator)
                                        <strong style="color:var(--adm-blue);">{{ $recruitment->koordinator->nama_lengkap }}</strong>
                                    @else
                                        <span style="color:var(--adm-text-faint);">Belum dipilih</span>
                                    @endif
                                </span>
                            </div>
                        @endif
                        @if ($recruitment->status === 'Ditolak' && $recruitment->alasan_penolakan)
                            <div class="adm-info-row" style="border-top:1px dashed var(--adm-red-lt,#ffe8e6);margin-top:10px;padding-top:10px;">
                                <span class="adm-info-key" style="color:var(--adm-red);">Alasan Penolakan</span>
                                <span class="adm-info-val" style="font-weight:500;color:var(--adm-red);">{{ $recruitment->alasan_penolakan }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Dokumen & Dynamic Requirements --}}
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
                        Persyaratan & Berkas Pendaftaran
                    </div>
                </div>
                <div style="padding:0 20px 20px;">
                    @if ($recruitment->recruitmentPost && $recruitment->recruitmentPost->requirements)
                        @php
                            $coreFields = [
                                'nama_lengkap', 'telephone', 'alamat_lengkap', 'pengalaman',
                                'rekomendasi', 'pendidikan_terakhir', 'foto_diri', 'foto_ktp',
                                'foto_ijasah', 'pakta_integritas', 'nik', 'jenis_kelamin', 'type_entry',
                            ];
                        @endphp
                        <div style="display:flex;flex-direction:column;gap:12px;">
                            @foreach ($recruitment->recruitmentPost->requirements as $req)
                                @php
                                    $fieldKey = $req['field_key'];
                                    $value = in_array($fieldKey, $coreFields) ? $recruitment->$fieldKey : ($recruitment->answers[$fieldKey] ?? null);
                                @endphp
                                <div style="padding:12px;background:var(--adm-bg-muted,#f8f9fa);border-radius:8px;border:1px solid var(--adm-border-color,#e9ebec);">
                                    <div style="display:flex;justify-content:between;align-items:center;margin-bottom:6px;">
                                        <span style="font-size:12px;font-weight:600;color:var(--adm-text-muted);">{{ strtoupper($req['label']) }}</span>
                                        <span class="adm-badge" style="font-size:10px;background:#fff;border:1px solid #ddd;">{{ $req['type'] }}</span>
                                    </div>
                                    <div style="font-size:13px;color:var(--adm-text-dark);">
                                        @if ($req['type'] === 'file')
                                            @if ($value)
                                                <div style="display:flex;align-items:center;gap:10px;">
                                                    <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:var(--adm-blue);fill:none;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                                    <a href="{{ asset('storage/' . $value) }}" target="_blank" style="font-weight:600;color:var(--adm-blue);text-decoration:none;">Buka / Unduh Berkas</a>
                                                </div>
                                                {{-- Preview jika gambar --}}
                                                @if (in_array(strtolower(pathinfo($value, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']))
                                                    <div style="margin-top:8px;">
                                                        <img src="{{ asset('storage/' . $value) }}" alt="{{ $req['label'] }}" style="max-width:100%;max-height:120px;border-radius:6px;border:1px solid #ddd;">
                                                    </div>
                                                @endif
                                            @else
                                                <span style="color:var(--adm-text-faint);">Tidak ada file yang diunggah</span>
                                            @endif
                                        @elseif($req['type'] === 'checkbox')
                                            @if ($value === '1' || $value === 1 || $value === 'on')
                                                <span class="adm-badge adm-badge-success" style="font-size:11px;">Ya / Setuju</span>
                                            @else
                                                <span class="adm-badge adm-badge-nonaktif" style="font-size:11px;">Tidak / Tidak Setuju</span>
                                            @endif
                                        @else
                                            <span>{{ $value ?? '-' }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Fallback lama --}}
                        <div style="display:flex;flex-direction:column;gap:10px;">
                            @foreach(['foto_diri' => 'Foto Diri', 'foto_ktp' => 'Foto KTP', 'foto_ijasah' => 'Foto Ijazah', 'rekomendasi' => 'Surat Rekomendasi', 'pakta_integritas' => 'Pakta Integritas'] as $key => $label)
                                @if ($recruitment->$key)
                                    <div style="padding:10px;background:var(--adm-bg-muted,#f8f9fa);border-radius:6px;display:flex;justify-content:space-between;align-items:center;">
                                        <span style="font-size:13px;font-weight:500;">{{ $label }}</span>
                                        <a href="{{ asset('storage/' . $recruitment->$key) }}" target="_blank" class="adm-btn primary sm" style="font-size:11px;padding:4px 10px;">Buka</a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tindakan Persetujuan Pelamar --}}
        @if ($recruitment->status === 'Melamar')
            <div class="adm-card" style="margin-bottom:16px;">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24">
                            <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3" />
                        </svg>
                        Keputusan Status Lamaran
                    </div>
                </div>
                <div style="padding:0 20px 20px;display:flex;gap:12px;flex-wrap:wrap;">
                    <button type="button" class="adm-btn success" id="btnTerima" style="padding:10px 24px;">
                        <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12" /></svg> Terima Pelamar
                    </button>
                    <button type="button" class="adm-btn danger" id="btnTolak" style="padding:10px 24px;">
                        <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" /></svg> Tolak Pelamar
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- Modals Tindakan Status --}}
    @if ($recruitment->status === 'Melamar')
        {{-- Modal Terima --}}
        <div class="modal fade" id="modalTerima" tabindex="-1" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius:12px;border:none;box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <form action="{{ route('superadmin.recruitments.update-status-v2', $recruitment->hashed_id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="Diterima">
                        <div class="modal-header" style="border-bottom:1px solid #f3f4f6;padding:16px 20px;">
                            <h5 class="modal-title" style="font-weight:700;color:var(--adm-text-dark);font-size:16px;">Terima Pelamar</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="background:none;border:none;font-size:20px;cursor:pointer;">&times;</button>
                        </div>
                        <div class="modal-body" style="padding:20px;">
                            <p style="font-size:14px;color:var(--adm-text-mid);margin-bottom:16px;">Apakah Anda yakin ingin menerima pelamar <strong>{{ $recruitment->nama_lengkap }}</strong>?</p>

                            @if ($recruitment->recruit_type === 'PENDAMPING')
                                <div class="adm-field">
                                    <label class="adm-label">Pilih Koordinator <span class="req">*</span></label>
                                    <select name="koordinator_id" class="adm-input" required>
                                        <option value="">-- Pilih Koordinator --</option>
                                        @foreach($koordinators as $koordinator)
                                            <option value="{{ $koordinator->id }}">{{ $koordinator->nama_lengkap }} ({{ $koordinator->kecamatan ?? 'No Kecamatan' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer" style="border-top:1px solid #f3f4f6;padding:12px 20px;display:flex;justify-content:flex-end;gap:10px;">
                            <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal" style="padding:8px 16px;">Batal</button>
                            <button type="submit" class="adm-btn success" style="padding:8px 20px;">Ya, Terima</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Tolak --}}
        <div class="modal fade" id="modalTolak" tabindex="-1" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius:12px;border:none;box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <form action="{{ route('superadmin.recruitments.update-status-v2', $recruitment->hashed_id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="Ditolak">
                        <div class="modal-header" style="border-bottom:1px solid #f3f4f6;padding:16px 20px;">
                            <h5 class="modal-title" style="font-weight:700;color:var(--adm-text-dark);font-size:16px;">Tolak Pelamar</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="background:none;border:none;font-size:20px;cursor:pointer;">&times;</button>
                        </div>
                        <div class="modal-body" style="padding:20px;">
                            <p style="font-size:14px;color:var(--adm-text-mid);margin-bottom:16px;">Apakah Anda yakin ingin menolak pelamar <strong>{{ $recruitment->nama_lengkap }}</strong>?</p>
                            <div class="adm-field">
                                <label class="adm-label">Alasan Penolakan <span class="req">*</span></label>
                                <textarea name="alasan_penolakan" class="adm-input" rows="4" required placeholder="Tuliskan alasan penolakan..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer" style="border-top:1px solid #f3f4f6;padding:12px 20px;display:flex;justify-content:flex-end;gap:10px;">
                            <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal" style="padding:8px 16px;">Batal</button>
                            <button type="submit" class="adm-btn danger" style="padding:8px 20px;">Ya, Tolak</button>
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
            document.addEventListener('DOMContentLoaded', function () {
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
