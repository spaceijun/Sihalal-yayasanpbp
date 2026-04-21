@extends('layouts.app')

@section('template_title')
    {{ $enumerator->nama_lengkap ?? __('Show') . ' ' . __('Enumerator') }}
@endsection

@section('content')
    <section class="content container-fluid">

        {{-- ─── ALERTS ─── --}}
        @if (session('success'))
            <div class="alert-modern success mb-4">
                <i class="las la-check-circle" style="font-size:18px"></i>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert-modern error mb-4">
                <i class="las la-exclamation-circle" style="font-size:18px"></i>
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert-modern error mb-4" style="flex-direction:column;align-items:flex-start">
                <div style="display:flex;gap:8px;align-items:center">
                    <i class="las la-exclamation-triangle" style="font-size:18px"></i>
                    <strong>Terdapat kesalahan:</strong>
                </div>
                <ul class="mb-0 mt-1 ps-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ─── PAGE HEADER ─── --}}
        <div class="page-header">
            <div class="avatar-wrap">
                @if ($enumerator->foto_diri)
                    <img src="{{ asset('storage/' . $enumerator->foto_diri) }}" alt="{{ $enumerator->nama_lengkap }}">
                @else
                    <i class="las la-user avatar-fallback"></i>
                @endif
            </div>
            <div style="flex:1">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h1 class="ph-title">{{ $enumerator->nama_lengkap }}</h1>
                    <span
                        class="status-pill {{ $enumerator->status == 'Aktif' ? 'badge-status-aktif' : 'badge-status-nonaktif' }}">
                        {{ $enumerator->status }}
                    </span>
                </div>
                <div class="ph-sub">
                    <i class="las la-hashtag me-1"></i>{{ $enumerator->no_registrasi }}
                    &nbsp;·&nbsp;
                    <i class="las la-users me-1"></i>{{ $enumerator->koordinator->nama_lengkap ?? 'Tanpa Koordinator' }}
                </div>
            </div>
        </div>

        {{-- ─── MAIN GRID ─── --}}
        <div class="row g-4">

            {{-- LEFT: Info Enumerator --}}
            <div class="col-lg-7">
                <div class="info-card">
                    <div class="ic-header">
                        <div class="ic-icon"><i class="las la-user-tie"></i></div>
                        <h6>Informasi Pendamping</h6>
                    </div>
                    <div class="ic-body">

                        <div class="info-row">
                            <div class="ir-label">Koordinator</div>
                            <div class="ir-value">{{ $enumerator->koordinator->nama_lengkap ?? '-' }}</div>
                        </div>

                        <div class="info-row">
                            <div class="ir-label">Nama Lengkap</div>
                            <div class="ir-value">{{ $enumerator->nama_lengkap }}</div>
                        </div>

                        <div class="info-row">
                            <div class="ir-label">No. Registrasi</div>
                            <div class="ir-value">
                                <span class="pill pill-indigo">{{ $enumerator->no_registrasi }}</span>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="ir-label">No. Telepon</div>
                            <div class="ir-value">
                                <a href="tel:{{ $enumerator->telephone }}">
                                    <i class="las la-phone"></i>{{ $enumerator->telephone }}
                                </a>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="ir-label">Alamat</div>
                            <div class="ir-value muted">{{ $enumerator->alamat }}</div>
                        </div>

                        <div class="info-row">
                            <div class="ir-label">Terdaftar</div>
                            <div class="ir-value muted">
                                <i class="las la-calendar me-1" style="color:var(--text-muted)"></i>
                                {{ $enumerator->created_at->format('d M Y, H:i') }}
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="ir-label">Status</div>
                            <div class="ir-value">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    @if ($enumerator->status == 'Aktif')
                                        <span class="pill pill-green">
                                            <i class="las la-check-circle"></i> Aktif
                                        </span>
                                    @else
                                        <span class="pill pill-red">
                                            <i class="las la-times-circle"></i> Tidak Aktif
                                        </span>
                                        <form action="{{ route('superadmin.enumerators.aktivasi', $enumerator->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Aktifkan kembali {{ $enumerator->nama_lengkap }}?')">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn-aktivasi">
                                                <i class="las la-user-check"></i> Aktifkan Kembali
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                @if ($enumerator->status == 'Tidak Aktif')
                                    <div class="status-warning">
                                        <i class="las la-info-circle"></i>
                                        <span>Pendamping ini dinonaktifkan karena tidak memenuhi target minimal
                                            20 data lapangan dalam 30 hari terakhir.</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Rekening --}}
                <div class="info-card">
                    <div class="ic-header">
                        <div class="ic-icon" style="background:#ecfdf5;color:#059669"><i class="las la-university"></i>
                        </div>
                        <h6>Informasi Rekening</h6>
                    </div>
                    <div class="ic-body">

                        <div class="info-row">
                            <div class="ir-label">No. Rekening</div>
                            <div class="ir-value">
                                @if ($enumerator->no_rekening)
                                    <span
                                        style="font-family:'Space Grotesk',sans-serif;font-size:15px;font-weight:600;letter-spacing:1px">
                                        {{ $enumerator->no_rekening }}
                                    </span>
                                @else
                                    <span class="ir-value muted">Belum Tersedia</span>
                                @endif
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="ir-label">Nama Rekening</div>
                            <div class="ir-value {{ $enumerator->nama_rekening ? '' : 'muted' }}">
                                {{ $enumerator->nama_rekening ?? 'Belum Tersedia' }}
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="ir-label">Bank</div>
                            <div class="ir-value">
                                @if ($enumerator->bank)
                                    <span style="font-weight:600">{{ $enumerator->bank->name }}</span>
                                    <span class="pill pill-indigo ms-2" style="font-size:10px;padding:2px 8px">
                                        {{ $enumerator->bank->code }}
                                    </span>
                                @else
                                    <span class="muted">Belum Tersedia</span>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- RIGHT: Foto + Dokumen --}}
            <div class="col-lg-5">

                {{-- Foto --}}
                <div class="info-card">
                    <div class="ic-header">
                        <div class="ic-icon" style="background:#f0f9ff;color:#0284c7"><i class="las la-image"></i></div>
                        <h6>Foto Pendamping</h6>
                    </div>
                    <div class="foto-wrap">
                        <div class="foto-frame"
                            @if ($enumerator->foto_diri) data-bs-toggle="modal" data-bs-target="#modalFoto" @endif>
                            @if ($enumerator->foto_diri)
                                <img src="{{ asset('storage/' . $enumerator->foto_diri) }}"
                                    alt="{{ $enumerator->nama_lengkap }}">
                            @else
                                <i class="las la-user foto-fallback"></i>
                            @endif
                        </div>
                        <div class="foto-name">{{ $enumerator->nama_lengkap }}</div>
                        <div class="foto-reg">{{ $enumerator->no_registrasi }}</div>
                        @if ($enumerator->foto_diri)
                            <small style="font-size:11px;color:var(--text-muted);margin-top:6px">
                                <i class="las la-search-plus me-1"></i>Klik foto untuk memperbesar
                            </small>
                        @endif
                    </div>
                </div>

                {{-- Dokumen --}}
                <div class="info-card">
                    <div class="ic-header">
                        <div class="ic-icon" style="background:#fff7ed;color:#d97706"><i class="las la-file-alt"></i>
                        </div>
                        <h6>Dokumen &amp; Ekspor</h6>
                    </div>
                    <div class="ic-body">
                        <div class="action-grid">
                            <button type="button" class="action-btn btn-green" data-bs-toggle="modal"
                                data-bs-target="#modalSuratTugas">
                                <i class="las la-file-pdf"></i> Surat Tugas
                            </button>
                            <button type="button" class="action-btn btn-sky" onclick="downloadIdCard()">
                                <i class="las la-id-card"></i> ID Card
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </section>

    {{-- ─── MODAL: Foto ─── --}}
    <div class="modal fade" id="modalFoto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius:16px;border:none;overflow:hidden">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:14px 20px">
                    <div style="display:flex;align-items:center;gap:10px">
                        <div
                            style="width:32px;height:32px;background:#f0f9ff;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#0284c7">
                            <i class="las la-image"></i>
                        </div>
                        <h5 class="modal-title mb-0" style="font-size:14px;font-weight:700;color:var(--text-primary)">
                            Foto — {{ $enumerator->nama_lengkap }}
                        </h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4" style="background:#f7f9fd">
                    @if ($enumerator->foto_diri)
                        <img src="{{ asset('storage/' . $enumerator->foto_diri) }}"
                            alt="{{ $enumerator->nama_lengkap }}"
                            style="max-height:580px;max-width:100%;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,0.1)">
                    @endif
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);padding:12px 20px">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="las la-times me-1"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── MODAL: Surat Tugas ─── --}}
    <div class="modal fade" id="modalSuratTugas" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content" style="border-radius:16px;border:none;overflow:hidden">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:14px 20px">
                    <div style="display:flex;align-items:center;gap:10px">
                        <div
                            style="width:32px;height:32px;background:#ecfdf5;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#059669">
                            <i class="las la-file-alt"></i>
                        </div>
                        <h5 class="modal-title mb-0" style="font-size:14px;font-weight:700;color:var(--text-primary)">
                            Surat Tugas — {{ $enumerator->nama_lengkap }}
                        </h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0" style="max-height:72vh;overflow-y:auto;background:#f7f9fd">
                    <iframe id="suratTugasFrame" src="" style="width:100%;height:800px;border:none;display:block"
                        onload="suratTugasLoaded()"></iframe>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);padding:12px 20px;gap:8px">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="las la-times me-1"></i>Tutup
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="printSuratTugas()">
                        <i class="las la-print me-1"></i>Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── HIDDEN ID CARD ─── --}}
    {{-- ─── HIDDEN ID CARD ─── --}}
    <div id="idCardContainer" style="position:absolute;left:-9999px">
        <div
            style="width:590px;height:1004px;font-family:'Plus Jakarta Sans',Arial,sans-serif;border-radius:24px;overflow:hidden;background:#fff;position:relative">

            {{-- Header --}}
            <div
                style="background:linear-gradient(135deg,#2e0d6e 0%,#4c1d95 55%,#6d28d9 100%);padding:38px 40px 0;position:relative;overflow:hidden">

                {{-- Dekorasi lingkaran --}}
                <div
                    style="position:absolute;top:-50px;right:-50px;width:220px;height:220px;background:rgba(255,255,255,.05);border-radius:50%">
                </div>
                <div
                    style="position:absolute;top:20px;right:40px;width:100px;height:100px;background:rgba(255,255,255,.04);border-radius:50%">
                </div>
                <div
                    style="position:absolute;bottom:40px;left:-30px;width:140px;height:140px;background:rgba(255,255,255,.03);border-radius:50%">
                </div>

                {{-- Logo + Nama Org --}}
                <div style="display:flex;align-items:center;gap:16px;position:relative;z-index:2">
                    <img src="{{ asset('storage/' . ($settingWebsite->favicon ?? '')) }}"
                        style="width:58px;height:58px;border-radius:14px;background:rgba(255,255,255,.15);padding:6px;object-fit:contain">
                    <div
                        style="font-size:14px;font-weight:700;color:rgba(255,255,255,.92);text-transform:uppercase;letter-spacing:.8px;line-height:1.5">
                        Lembaga Pendamping<br>Proses Produk Halal<br>
                        <span style="color:rgba(255,255,255,.55);font-size:12px">Kawulo Halal</span>
                    </div>
                </div>

                {{-- Badge tipe kartu --}}
                <div
                    style="display:inline-block;background:rgba(255,255,255,.13);color:rgba(255,255,255,.82);font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;padding:5px 18px;border-radius:20px;margin-top:20px;position:relative;z-index:2;border:.5px solid rgba(255,255,255,.2)">
                    Kartu Pendamping
                </div>

                {{-- Foto dengan ring gradient --}}
                <div
                    style="width:160px;height:160px;margin:28px auto 0;background:linear-gradient(135deg,#f59e0b,#d946ef,#6366f1);border-radius:50%;padding:4px;position:relative;z-index:2">
                    <div
                        style="width:100%;height:100%;background:#dde4f8;border-radius:50%;overflow:hidden;display:flex;align-items:flex-end;justify-content:center">
                        @if ($enumerator->foto_diri)
                            <img src="{{ asset('storage/' . $enumerator->foto_diri) }}"
                                style="width:100%;height:100%;object-fit:cover;object-position:center top">
                        @else
                            <svg width="100" height="110" viewBox="0 0 100 110" xmlns="http://www.w3.org/2000/svg">
                                <ellipse cx="50" cy="38" rx="24" ry="26" fill="#6d5dcd"
                                    opacity=".6" />
                                <ellipse cx="50" cy="108" rx="46" ry="34" fill="#6d5dcd"
                                    opacity=".4" />
                            </svg>
                        @endif
                    </div>
                </div>

                {{-- Wave pemisah --}}
                <svg viewBox="0 0 590 48" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none"
                    style="width:100%;display:block;margin-top:-1px">
                    <path d="M0,24 C100,48 200,0 295,24 C390,48 490,0 590,24 L590,48 L0,48 Z" fill="#ffffff" />
                </svg>
            </div>

            {{-- Body --}}
            <div style="background:#fff;padding:8px 44px 28px;text-align:center">

                {{-- Nama --}}
                <p
                    style="font-size:28px;font-weight:800;color:#1a0e3a;text-transform:uppercase;letter-spacing:.5px;margin:0;line-height:1.15">
                    {{ strtoupper($enumerator->nama_lengkap) }}
                </p>
                <p
                    style="font-size:13px;font-weight:700;color:#7c3aed;text-transform:uppercase;letter-spacing:2px;margin:6px 0 0">
                    Pendamping Lapangan
                </p>

                {{-- Chip No. Registrasi --}}
                <div
                    style="display:inline-flex;align-items:center;gap:8px;background:#f0ebff;border:.5px solid #c4b5fd;border-radius:20px;padding:7px 20px;margin-top:16px;font-size:13px;font-weight:700;color:#5b21b6;letter-spacing:.6px">
                    <span
                        style="width:7px;height:7px;background:#7c3aed;border-radius:50%;display:inline-block;flex-shrink:0"></span>
                    {{ $enumerator->no_registrasi }}/KH-YPBP/{{ \Carbon\Carbon::parse($enumerator->created_at)->format('m') }}/{{ \Carbon\Carbon::parse($enumerator->created_at)->year }}
                </div>

                {{-- Status aktif/nonaktif --}}
                <div
                    style="display:flex;align-items:center;justify-content:center;gap:7px;margin-top:10px;font-size:11px;font-weight:700;color:{{ $enumerator->status == 'Aktif' ? '#059669' : '#dc2626' }};text-transform:uppercase;letter-spacing:1px">
                    <span
                        style="width:8px;height:8px;background:{{ $enumerator->status == 'Aktif' ? '#4ade80' : '#f87171' }};border-radius:50%;border:1.5px solid rgba(0,0,0,.08);display:inline-block"></span>
                    {{ $enumerator->status }}
                </div>

                {{-- Divider --}}
                <div style="border-top:.5px solid #ede9fe;margin:20px 0 18px"></div>

                {{-- Info Grid 2 kolom --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;text-align:left">

                    <div style="background:#faf8ff;border-radius:14px;padding:14px 16px;border:.5px solid #ede9fe">
                        <div
                            style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#a78bfa;margin-bottom:4px">
                            Koordinator</div>
                        <div style="font-size:14px;font-weight:700;color:#2e1065">
                            {{ $enumerator->koordinator->nama_lengkap ?? '-' }}</div>
                    </div>

                    <div style="background:#faf8ff;border-radius:14px;padding:14px 16px;border:.5px solid #ede9fe">
                        <div
                            style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#a78bfa;margin-bottom:4px">
                            No. Telepon</div>
                        <div style="font-size:14px;font-weight:700;color:#2e1065">{{ $enumerator->telephone }}</div>
                    </div>

                    <div
                        style="background:#faf8ff;border-radius:14px;padding:14px 16px;border:.5px solid #ede9fe;grid-column:span 2">
                        <div
                            style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#a78bfa;margin-bottom:4px">
                            Alamat</div>
                        <div style="font-size:13px;font-weight:600;color:#2e1065;line-height:1.4">
                            {{ $enumerator->alamat }}</div>
                    </div>

                </div>
            </div>

            {{-- Footer --}}
            <div
                style="position:absolute;bottom:0;left:0;width:100%;background:linear-gradient(135deg,#2e0d6e,#4c1d95);padding:20px 44px;box-sizing:border-box;display:flex;align-items:center;justify-content:space-between">
                <div>
                    <div
                        style="font-size:10px;color:rgba(255,255,255,.55);letter-spacing:.8px;text-transform:uppercase;font-weight:700">
                        Terdaftar Sejak</div>
                    <div style="font-size:15px;color:rgba(255,255,255,.92);font-weight:700;margin-top:3px">
                        {{ $enumerator->created_at->format('d M Y') }}</div>
                </div>
                <div style="text-align:center">
                    <div
                        style="font-size:10px;color:rgba(255,255,255,.4);letter-spacing:.8px;text-transform:uppercase;font-weight:700">
                        Masa Berlaku</div>
                    <div style="font-size:15px;color:rgba(255,255,255,.92);font-weight:700;margin-top:3px">
                        {{ \Carbon\Carbon::parse($enumerator->created_at)->addYear()->format('d M Y') }}</div>
                </div>
                <div style="text-align:right">
                    <div
                        style="font-size:10px;color:rgba(255,255,255,.55);letter-spacing:.8px;text-transform:uppercase;font-weight:700">
                        Diterbitkan oleh</div>
                    <div style="font-size:14px;color:rgba(255,255,255,.85);font-weight:700;margin-top:3px">kawulohalal.id
                    </div>
                </div>
            </div>

        </div>
    </div>

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --bg-base: #f3f5fb;
            --bg-card: #ffffff;
            --border: #e8ecf4;
            --border-bright: #d0d7eb;
            --text-primary: #1a2040;
            --text-secondary: #5a6380;
            --text-muted: #9aa0b8;
            --accent: #4f46e5;
            --accent-light: #eef2ff;
            --accent-cyan: #0284c7;
            --accent-green: #059669;
            --accent-rose: #e11d48;
            --accent-amber: #d97706;
            --shadow: 0 2px 12px rgba(80, 100, 160, 0.08), 0 1px 3px rgba(80, 100, 160, 0.05);
            --shadow-hover: 0 8px 28px rgba(80, 100, 160, 0.13);
            --radius: 16px;
            --radius-sm: 10px;
        }

        body,
        .page-content,
        .main-content {
            background: var(--bg-base) !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }

        /* ─── ALERTS ─── */
        .alert-modern {
            border: none;
            border-radius: var(--radius-sm);
            padding: 12px 18px;
            font-size: 13.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-modern.success {
            background: #ecfdf5;
            color: #065f46;
            border-left: 4px solid #059669;
        }

        .alert-modern.error {
            background: #fff1f2;
            color: #be123c;
            border-left: 4px solid #e11d48;
        }

        /* ─── PAGE HEADER ─── */
        .page-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }

        .page-header .avatar-wrap {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: var(--accent-light);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
            border: 2px solid #c7d2fe;
        }

        .page-header .avatar-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top;
        }

        .page-header .avatar-wrap .avatar-fallback {
            font-size: 26px;
            color: var(--accent);
        }

        .page-header .ph-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-primary);
            margin: 0;
            line-height: 1.2;
        }

        .page-header .ph-sub {
            font-size: 12.5px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        .page-header .badge-status-aktif {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .page-header .badge-status-nonaktif {
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #fecdd3;
        }

        .page-header .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 0.4px;
            padding: 4px 12px;
            border-radius: 20px;
        }

        .page-header .status-pill::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        /* ─── SECTION CARD ─── */
        .info-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .info-card .ic-header {
            padding: 14px 22px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid var(--border);
            background: #fafbff;
        }

        .info-card .ic-header .ic-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--accent-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            color: var(--accent);
            flex-shrink: 0;
        }

        .info-card .ic-header h6 {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
            letter-spacing: 0.2px;
        }

        .info-card .ic-body {
            padding: 20px 22px;
        }

        /* ─── INFO ROW ─── */
        .info-row {
            display: flex;
            align-items: flex-start;
            padding: 11px 0;
            border-bottom: 1px solid #f1f4fb;
            gap: 16px;
        }

        .info-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .info-row:first-child {
            padding-top: 0;
        }

        .info-row .ir-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: var(--text-muted);
            min-width: 148px;
            flex-shrink: 0;
            padding-top: 1px;
        }

        .info-row .ir-value {
            font-size: 13.5px;
            color: var(--text-primary);
            font-weight: 500;
            line-height: 1.45;
            flex: 1;
        }

        .info-row .ir-value.muted {
            color: var(--text-secondary);
            font-weight: 400;
        }

        .info-row .ir-value a {
            color: var(--accent);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .info-row .ir-value a:hover {
            text-decoration: underline;
        }

        /* ─── BADGE / PILL ─── */
        .pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11.5px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
            letter-spacing: 0.3px;
        }

        .pill-indigo {
            background: #eef2ff;
            color: #4338ca;
            border: 1px solid #c7d2fe;
        }

        .pill-green {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .pill-red {
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #fecdd3;
        }

        /* ─── FOTO CARD ─── */
        .foto-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 28px 22px;
        }

        .foto-frame {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            border: 3px solid #c7d2fe;
            overflow: hidden;
            background: var(--accent-light);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: box-shadow .2s, transform .2s;
            box-shadow: 0 4px 16px rgba(79, 70, 229, 0.12);
        }

        .foto-frame:hover {
            transform: scale(1.04);
            box-shadow: 0 8px 24px rgba(79, 70, 229, 0.2);
        }

        .foto-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top;
        }

        .foto-frame .foto-fallback {
            font-size: 64px;
            color: var(--accent);
            opacity: 0.4;
        }

        .foto-name {
            margin-top: 14px;
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
            text-align: center;
        }

        .foto-reg {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
            text-align: center;
        }

        /* ─── STATUS WARNING BOX ─── */
        .status-warning {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: var(--radius-sm);
            padding: 12px 16px;
            font-size: 12.5px;
            color: #92400e;
            display: flex;
            gap: 8px;
            align-items: flex-start;
            margin-top: 14px;
        }

        .status-warning i {
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* ─── ACTION BUTTONS ─── */
        .action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 18px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: transform .15s, box-shadow .15s, opacity .15s;
            text-decoration: none;
            width: 100%;
        }

        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
            opacity: .92;
        }

        .action-btn:active {
            transform: translateY(0);
        }

        .action-btn.btn-green {
            background: linear-gradient(135deg, #059669, #10b981);
            color: #fff;
        }

        .action-btn.btn-sky {
            background: linear-gradient(135deg, #0284c7, #38bdf8);
            color: #fff;
        }

        .action-btn.btn-indigo {
            background: linear-gradient(135deg, #4f46e5, #818cf8);
            color: #fff;
        }

        .action-btn.btn-amber {
            background: linear-gradient(135deg, #d97706, #f59e0b);
            color: #fff;
        }

        .action-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        /* ─── AKTIVASI BTN ─── */
        .btn-aktivasi {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 600;
            background: linear-gradient(135deg, #059669, #10b981);
            color: #fff;
            border: none;
            cursor: pointer;
            transition: opacity .15s, transform .15s;
        }

        .btn-aktivasi:hover {
            opacity: .88;
            transform: translateY(-1px);
        }

        /* ─── OVERRIDE Bootstrap ─── */
        .card {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }

        .container-fluid {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        // Auto-dismiss alerts after 5s
        setTimeout(() => {
            document.querySelectorAll('.alert-modern').forEach(el => {
                el.style.transition = 'opacity .4s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 400);
            });
        }, 5000);

        // Load surat tugas iframe on modal open
        document.getElementById('modalSuratTugas').addEventListener('show.bs.modal', function() {
            document.getElementById('suratTugasFrame').src =
                '{{ route('superadmin.enumerators.surat-tugas', $enumerator->id) }}';
        });

        function suratTugasLoaded() {
            console.log('Surat Tugas loaded');
        }

        function printSuratTugas() {
            document.getElementById('suratTugasFrame').contentWindow.print();
        }

        function downloadIdCard() {
            const el = document.getElementById('idCardContainer').children[0];
            const nama = '{{ $enumerator->nama_lengkap }}';

            // Loading overlay
            const overlay = document.createElement('div');
            overlay.style.cssText =
                'position:fixed;inset:0;background:rgba(26,32,64,.55);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;z-index:9999';
            overlay.innerHTML = `<div style="background:#fff;border-radius:16px;padding:28px 36px;text-align:center;box-shadow:0 8px 32px rgba(0,0,0,.18)">
            <div style="font-size:32px;margin-bottom:8px">⏳</div>
            <div style="font-size:14px;font-weight:600;color:#1a2040">Membuat ID Card...</div>
            <div style="font-size:12px;color:#9aa0b8;margin-top:4px">Mohon tunggu sebentar</div>
        </div>`;
            document.body.appendChild(overlay);

            html2canvas(el, {
                    scale: 2,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    width: 590,
                    height: 1004
                })
                .then(canvas => {
                    canvas.toBlob(blob => {
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'ID_Card_' + nama.replace(/\s+/g, '_') + '.jpg';
                        document.body.appendChild(a);
                        a.click();
                        URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                        document.body.removeChild(overlay);
                    }, 'image/jpeg', 0.95);
                })
                .catch(() => {
                    alert('Gagal membuat ID Card');
                    document.body.removeChild(overlay);
                });
        }
    </script>
@endsection
