@extends('layouts.app')
@section('template_title') Detail Koordinator @endsection

@section('content')
<style>
    .ko-detail-grid { display: grid; grid-template-columns: 340px 1fr; gap: 20px; align-items: start; }
    .ko-profile-card { background: #fff; border: 1px solid var(--adm-border); border-radius: var(--adm-radius); box-shadow: var(--adm-shadow-sm); padding: 24px 20px; text-align: center; }
    .ko-profile-avatar { width: 96px; height: 96px; border-radius: 50%; margin: 0 auto 14px; object-fit: cover; }
    .ko-profile-avatar.is-initials { background: linear-gradient(135deg, var(--adm-blue) 0%, var(--adm-blue-dk, #1040A0) 100%); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 26px; font-weight: 700; font-family: 'Sora', sans-serif; box-shadow: 0 4px 12px rgba(26, 95, 200, 0.22); }
    .ko-profile-name { font-family: 'Sora', sans-serif; font-size: 17px; font-weight: 700; color: var(--adm-text-dark); margin: 0 0 8px; line-height: 1.3; }
    .ko-profile-status { display: inline-block; margin-bottom: 18px; }
    .ko-section-title { font-family: 'Sora', sans-serif; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--adm-text-muted); margin: 18px 0 8px; padding-bottom: 6px; border-bottom: 1px solid var(--adm-border); text-align: left; }
    .ko-stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 20px; }
    .ko-doc-photo { width: 100%; max-width: 260px; border-radius: var(--adm-radius-sm); border: 1px solid var(--adm-border); display: block; }
    @media (max-width: 900px) { .ko-detail-grid { grid-template-columns: 1fr; } }
</style>

<div class="adm-page">
    @include('layouts.messages')

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Detail Koordinator</h1>
            <p>Informasi lengkap akun koordinator</p>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route($routePrefix . '.koordinators.edit', $koordinator->hashed_id) }}" class="adm-btn-primary">
                <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit
            </a>
            <a href="{{ route($routePrefix . '.koordinators.index') }}" class="adm-btn-secondary">
                <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg> Kembali
            </a>
        </div>
    </div>

    <div class="ko-detail-grid">
        {{-- ══ SIDEBAR ══ --}}
        <div class="ko-profile-card">
            @if ($koordinator->foto_formal)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($koordinator->foto_formal) }}" class="ko-profile-avatar" alt="Foto {{ $koordinator->nama_lengkap }}">
            @else
                <div class="ko-profile-avatar is-initials">{{ strtoupper(substr($koordinator->nama_lengkap, 0, 2)) }}</div>
            @endif

            <div class="ko-profile-name">{{ $koordinator->nama_lengkap }}</div>

            <div class="ko-profile-status">
                @if ($koordinator->status === 'Aktif')
                    <span class="adm-badge adm-badge-success">Aktif</span>
                @elseif ($koordinator->status === 'Blacklist')
                    <span class="adm-badge adm-badge-danger">Blacklist</span>
                @else
                    <span class="adm-badge adm-badge-nonaktif">Tidak Aktif</span>
                @endif
            </div>

            <div class="ko-section-title">Info Ringkas</div>
            <div class="adm-info-list" style="border:none;">
                <div class="adm-info-row">
                    <span class="adm-info-key">Email</span>
                    <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $koordinator->email }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Telepon</span>
                    <span class="adm-info-val adm-mono">{{ $koordinator->telephone }}</span>
                </div>
                <div class="adm-info-row">
                    <span class="adm-info-key">Tanggal Mulai</span>
                    <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">
                        {{ $koordinator->tanggal_mulai ? $koordinator->tanggal_mulai->format('d M Y') : '—' }}
                    </span>
                </div>
            </div>

            <div class="ko-section-title">Fee Aktif</div>
            <div class="adm-info-list" style="border:none;">
                @php $feeAktif = $koordinator->fee_aktif; @endphp
                <div class="adm-info-row">
                    <span class="adm-info-key">Fee</span>
                    <span class="adm-info-val">
                        @if ($feeAktif)
                            <span class="adm-mono">Rp {{ number_format($feeAktif->nominal_fee, 0, ',', '.') }}</span>
                            <small style="display:block;color:var(--adm-text-muted);font-weight:400;">
                                {{ $feeAktif->tipe_fee === 'Target' ? '/ '.$feeAktif->target_data.' data' : '/ Bulan' }}
                                &middot; <em>{{ $feeAktif->skala }}</em>
                            </small>
                        @else
                            <span style="color:var(--adm-text-faint);">Belum ada fee</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- ══ MAIN CONTENT ══ --}}
        <div>
            {{-- Statistik --}}
            <div class="ko-stats-grid">
                <div class="adm-stat is-accent">
                    <div class="adm-stat-label">Total Enumerator</div>
                    <div class="adm-stat-value">{{ $totalEnumerator }}</div>
                    <div class="adm-stat-sub">Di bawah koordinator ini</div>
                </div>
                <div class="adm-stat">
                    <div class="adm-stat-label">Total Data Lapangan</div>
                    <div class="adm-stat-value">{{ $totalDataLapangan }}</div>
                    <div class="adm-stat-sub">Keseluruhan data</div>
                </div>
                <div class="adm-stat">
                    <div class="adm-stat-label">Terbit SH</div>
                    <div class="adm-stat-value is-success">{{ $totalTerbitSh }}</div>
                    <div class="adm-stat-sub">Sudah terbit sertifikat</div>
                </div>
            </div>

            {{-- Alamat KTP --}}
            <div class="adm-card" style="margin-bottom:16px;">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        Alamat Sesuai KTP
                    </div>
                </div>
                <div style="padding:0 20px;">
                    <div class="adm-info-list">
                        <div class="adm-info-row">
                            <span class="adm-info-key">Provinsi</span>
                            <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $koordinator->provinsi_ktp ?: '—' }}</span>
                        </div>
                        <div class="adm-info-row">
                            <span class="adm-info-key">Kabupaten/Kota</span>
                            <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $koordinator->kabupaten_ktp ?: '—' }}</span>
                        </div>
                        <div class="adm-info-row">
                            <span class="adm-info-key">Kecamatan</span>
                            <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $koordinator->kecamatan_ktp ?: '—' }}</span>
                        </div>
                        <div class="adm-info-row">
                            <span class="adm-info-key">Desa/Kelurahan</span>
                            <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $koordinator->desa_ktp ?: '—' }}</span>
                        </div>
                        <div class="adm-info-row">
                            <span class="adm-info-key">RT/RW</span>
                            <span class="adm-info-val adm-mono">{{ $koordinator->rt_ktp ?: '—' }} / {{ $koordinator->rw_ktp ?: '—' }}</span>
                        </div>
                        <div class="adm-info-row">
                            <span class="adm-info-key">Alamat Lengkap</span>
                            <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $koordinator->alamat_lengkap_ktp ?: '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Wilayah Kerja --}}
            <div class="adm-card" style="margin-bottom:16px;">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                        Wilayah Kerja
                    </div>
                </div>
                <div style="padding:0 20px;">
                    <div class="adm-info-list">
                        <div class="adm-info-row">
                            <span class="adm-info-key">Tipe</span>
                            <span class="adm-info-val">
                                @if ($koordinator->tipe_wilayah_kerja)
                                    <span class="adm-badge adm-badge-indigo">{{ $koordinator->tipe_wilayah_kerja }}</span>
                                @else
                                    <span style="color:var(--adm-text-faint);">—</span>
                                @endif
                            </span>
                        </div>
                        <div class="adm-info-row">
                            <span class="adm-info-key">Provinsi Kerja</span>
                            <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $koordinator->provinsi_kerja ?: '—' }}</span>
                        </div>
                        @if ($koordinator->tipe_wilayah_kerja === 'Kabupaten')
                            <div class="adm-info-row">
                                <span class="adm-info-key">Kabupaten Kerja</span>
                                <span class="adm-info-val" style="font-weight:400;color:var(--adm-text-mid);">{{ $koordinator->kabupaten_kerja ?: '—' }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Foto Dokumen --}}
            <div class="adm-card">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        Foto Dokumen
                    </div>
                </div>
                <div style="padding:16px 20px;display:flex;gap:20px;flex-wrap:wrap;">
                    <div>
                        <div style="font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--adm-text-muted);margin-bottom:8px;">Foto KTP</div>
                        @if ($koordinator->foto_ktp)
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($koordinator->foto_ktp) }}" target="_blank" rel="noopener">
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($koordinator->foto_ktp) }}" class="ko-doc-photo" alt="Foto KTP">
                            </a>
                        @else
                            <span style="color:var(--adm-text-faint);font-size:13px;">Belum ada foto KTP</span>
                        @endif
                    </div>
                    <div>
                        <div style="font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--adm-text-muted);margin-bottom:8px;">Foto Formal</div>
                        @if ($koordinator->foto_formal)
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($koordinator->foto_formal) }}" target="_blank" rel="noopener">
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($koordinator->foto_formal) }}" class="ko-doc-photo" alt="Foto Formal">
                            </a>
                        @else
                            <span style="color:var(--adm-text-faint);font-size:13px;">Belum ada foto formal</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
