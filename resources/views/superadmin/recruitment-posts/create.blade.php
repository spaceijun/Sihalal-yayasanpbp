@extends('layouts.app')
@section('template_title')
    Buat Lowongan Pekerjaan
@endsection

@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Buat Lowongan Pekerjaan</h1>
                <p>Isi detail lowongan dan konfigurasi form syarat pendaftaran</p>
            </div>
            <a href="{{ route($routePrefix . '.recruitment-posts.index') }}" class="adm-btn-secondary">
                <svg viewBox="0 0 24 24">
                    <polyline points="15 18 9 12 15 6" />
                </svg> Kembali
            </a>
        </div>

        <form action="{{ route($routePrefix . '.recruitment-posts.store') }}" method="POST" id="formLoker" enctype="multipart/form-data">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start;">

                {{-- Card Informasi Lowongan --}}
                <div class="adm-card">
                    <div class="adm-card-header">
                        <div class="adm-card-title">
                            <svg viewBox="0 0 24 24">
                                <rect x="2" y="7" width="20" height="14" rx="2" />
                                <path d="M16 7V5a2 2 0 0 0-4 0v2" />
                            </svg>
                            Informasi Lowongan
                        </div>
                    </div>
                    <div style="padding:0 20px 20px;">
                        @include('superadmin.recruitment-posts.partials.form-info')
                    </div>
                </div>

                {{-- Card Jobdesk --}}
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
                            Deskripsi & Jobdesk
                        </div>
                    </div>
                    <div style="padding:0 20px 20px;">
                        <div class="adm-field">
                            <label class="adm-label">Deskripsi Singkat</label>
                            <textarea name="deskripsi" class="adm-textarea" rows="3" placeholder="Deskripsi singkat lowongan pekerjaan...">{{ old('deskripsi') }}</textarea>
                        </div>
                        <div class="adm-field">
                            <label class="adm-label">Job Description / Jobdesk <span class="req">*</span></label>
                            <textarea name="jobdesk" class="adm-textarea" rows="6"
                                placeholder="- Melakukan survei lapangan&#10;- Mengumpulkan data UMKM&#10;- Membuat laporan harian">{{ old('jobdesk') }}</textarea>
                            <span class="adm-hint">Tuliskan tugas dan tanggung jawab posisi ini.</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Requirements Builder --}}
            <div class="adm-card" style="margin-top:16px;">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24">
                            <rect x="9" y="9" width="13" height="13" rx="2" />
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                        </svg>
                        Builder Syarat Pendaftaran
                    </div>
                    <button type="button" class="adm-btn primary" id="btnAddField"
                        style="font-size:12px;padding:5px 14px;">
                        <svg viewBox="0 0 24 24">
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        Tambah Field
                    </button>
                </div>
                <div style="padding:0 20px 20px;">
                    <p style="font-size:12.5px;color:var(--adm-text-muted);margin-bottom:16px;">
                        Konfigurasi field yang akan muncul di form pendaftaran. Pelamar akan mengisi form sesuai yang Anda
                        tentukan di sini.
                    </p>
                    <div id="requirementsContainer">
                        {{-- Field rows akan ditambah via JS --}}
                    </div>
                    <div id="emptyState"
                        style="text-align:center;padding:30px;color:var(--adm-text-faint);border:2px dashed var(--adm-border);border-radius:8px;">
                        <svg viewBox="0 0 24 24"
                            style="width:32px;height:32px;margin:0 auto 8px;display:block;fill:none;stroke:currentColor;stroke-width:1.5;">
                            <rect x="9" y="9" width="13" height="13" rx="2" />
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                        </svg>
                        <p style="font-size:13px;margin:0;">Belum ada field. Klik <strong>Tambah Field</strong> untuk mulai.
                        </p>
                    </div>
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:16px;">
                <a href="{{ route($routePrefix . '.recruitment-posts.index') }}" class="adm-btn-secondary">Batal</a>
                <button type="submit" class="adm-btn-primary">
                    <svg viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                    Simpan Lowongan
                </button>
            </div>
        </form>
    </div>
@endsection

@include('superadmin.recruitment-posts.partials.requirements-builder-script')
