@extends('layouts.app')
@section('template_title')
    App Versions
@endsection

@section('content')
<div class="adm-page">
    @include('layouts.messages')

    {{-- ── PAGE HEADER ── --}}
    <div class="adm-header">
        <div class="adm-header-left">
            <h1>App Version</h1>
            <p>Kelola versi aplikasi mobile Kawulo Halal</p>
        </div>
        @if ($appVersions->isEmpty())
            <a href="{{ route('superadmin.app-versions.create') }}" class="adm-btn-primary">
                <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Versi
            </a>
        @else
            <a href="{{ route('superadmin.app-versions.edit', $appVersions->first()->hashed_id) }}" class="adm-btn-primary adm-btn-success">
                <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Update Versi
            </a>
        @endif
    </div>

    {{-- ── STAT CARDS (only when data exists) ── --}}
    @unless ($appVersions->isEmpty())
        @php $latest = $appVersions->first(); @endphp
        <div class="adm-stats" style="grid-template-columns: repeat(3,1fr); max-width: 600px; margin-bottom:22px;">
            <div class="adm-stat is-accent">
                <div class="adm-stat-label">Versi Aktif</div>
                <div class="adm-stat-value">{{ $latest->version }}</div>
                <div class="adm-stat-sub">Saat ini</div>
            </div>
            <div class="adm-stat">
                <div class="adm-stat-label">Build Number</div>
                <div class="adm-stat-value">{{ $latest->build_number }}</div>
                <div class="adm-stat-sub">Build terbaru</div>
            </div>
            <div class="adm-stat">
                <div class="adm-stat-label">Force Update</div>
                <div class="adm-stat-value" style="font-size:18px; margin-top:4px;">
                    @if ($latest->force_update)
                        <span class="adm-badge adm-badge-danger">Wajib</span>
                    @else
                        <span class="adm-badge adm-badge-success">Opsional</span>
                    @endif
                </div>
                <div class="adm-stat-sub">{{ $latest->updated_at->diffForHumans() }}</div>
            </div>
        </div>
    @endunless

    {{-- ── TABLE + PHONE MOCKUP ── --}}
    <div class="row g-3">
        <div class="col-md-9">
            <div class="adm-card">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                        Riwayat Versi
                        <span class="adm-count-badge">{{ $appVersions->total() }}</span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th style="width:44px">#</th>
                                <th>Version</th>
                                <th>Build</th>
                                <th>Changelog</th>
                                <th class="tc">Force Update</th>
                                <th class="tc">Download</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($appVersions as $appVersion)
                                <tr>
                                    <td><span class="adm-rownum">{{ ++$i }}</span></td>
                                    <td>
                                        <span class="adm-badge adm-badge-info">v{{ $appVersion->version }}</span>
                                    </td>
                                    <td class="adm-mono">{{ $appVersion->build_number }}</td>
                                    <td style="max-width:220px; color:var(--adm-text-muted); font-size:12.5px;">
                                        {{ $appVersion->changelog }}
                                    </td>
                                    <td class="tc">
                                        @if ($appVersion->force_update)
                                            <span class="adm-badge adm-badge-danger"><span class="dot"></span>Wajib</span>
                                        @else
                                            <span class="adm-badge adm-badge-success">Opsional</span>
                                        @endif
                                    </td>
                                    <td class="tc">
                                        <a href="{{ $appVersion->download_url }}" target="_blank"
                                            class="adm-btn primary"
                                            title="{{ $appVersion->download_url }}">
                                            <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                            APK
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="adm-empty">
                                            <svg viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                                            <p>Belum ada data versi aplikasi.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($appVersions->hasPages())
                    <div class="adm-card-footer">
                        <span class="adm-footer-info">
                            Menampilkan {{ $appVersions->firstItem() }}–{{ $appVersions->lastItem() }}
                            dari {{ $appVersions->total() }} versi
                        </span>
                        @include('layouts.pagination', ['paginator' => $appVersions])
                    </div>
                @endif
            </div>
        </div>

        {{-- ── PHONE MOCKUP ── --}}
        <div class="col-md-3">
            <div class="adm-card" style="overflow:visible;">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/></svg>
                        Preview
                    </div>
                </div>
                <div style="padding:20px; display:flex; flex-direction:column; align-items:center; gap:14px;">
                    {{-- Phone mockup --}}
                    <div style="width:110px;height:210px;background:#1e1e1e;border-radius:22px;padding:8px 7px;border:3px solid #333;box-shadow:0 8px 24px rgba(0,0,0,.15);">
                        <div style="background:#e6f1fb;border-radius:14px;height:100%;display:flex;flex-direction:column;overflow:hidden;">
                            <div style="width:28px;height:5px;background:#1e1e1e;border-radius:4px;margin:7px auto;"></div>
                            <div style="padding:8px;flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;">
                                <div style="width:30px;height:30px;background:#1A5FC8;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5">
                                        <polyline points="16 3 21 3 21 8"/><line x1="4" y1="20" x2="21" y2="3"/>
                                    </svg>
                                </div>
                                <div style="background:white;border-radius:7px;padding:7px;width:100%;border:0.5px solid #B5D4F4;">
                                    <div style="font-size:7px;font-weight:600;color:#1A5FC8;margin-bottom:3px;">Update Tersedia</div>
                                    <div style="font-size:6px;color:#888;margin-bottom:5px;line-height:1.4;">Ada versi terbaru Kawulo Halal.</div>
                                    <div style="background:#1A5FC8;color:white;font-size:6px;padding:3px 5px;border-radius:3px;text-align:center;">Download Sekarang</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @unless ($appVersions->isEmpty())
                        <div style="text-align:center;width:100%;">
                            <div style="font-size:11px;color:var(--adm-text-muted);margin-bottom:2px;">Versi terbaru</div>
                            <div style="font-size:14px;font-weight:700;color:var(--adm-text-dark);">
                                {{ $appVersions->first()->version }}
                                <span style="font-size:11px;color:var(--adm-text-muted);">(build {{ $appVersions->first()->build_number }})</span>
                            </div>
                        </div>
                        <div style="text-align:center;width:100%;">
                            <div style="font-size:11px;color:var(--adm-text-muted);margin-bottom:2px;">Terakhir diupdate</div>
                            <div style="font-size:13px;font-weight:600;color:var(--adm-text-dark);">
                                {{ $appVersions->first()->updated_at->diffForHumans() }}
                            </div>
                        </div>
                    @endunless
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el))
</script>
@endsection
