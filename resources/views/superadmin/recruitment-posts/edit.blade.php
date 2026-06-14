@extends('layouts.app')
@section('template_title')
    Edit Lowongan – {{ $post->nama_loker }}
@endsection

@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Edit Lowongan</h1>
                <p>{{ $post->nama_loker }}</p>
            </div>
            <div style="display:flex;gap:8px;">
                @if ($post->is_active)
                    <a href="{{ $post->public_url }}" target="_blank" class="adm-btn" style="font-size:12px;padding:6px 14px;">
                        <svg viewBox="0 0 24 24" style="width:13px;height:13px;">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                            <polyline points="15 3 21 3 21 9" />
                            <line x1="10" y1="14" x2="21" y2="3" />
                        </svg>
                        Preview Form
                    </a>
                @endif
                <a href="{{ route($routePrefix . '.recruitment-posts.index') }}" class="adm-btn-secondary">
                    <svg viewBox="0 0 24 24">
                        <polyline points="15 18 9 12 15 6" />
                    </svg> Kembali
                </a>
            </div>
        </div>

        <form action="{{ route($routePrefix . '.recruitment-posts.update', $post->hashed_id) }}" method="POST" id="formLoker" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start;">

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
                        <div class="adm-field">
                            <label class="adm-label">Deskripsi Singkat</label>
                            <textarea name="deskripsi" class="adm-textarea" rows="3">{{ old('deskripsi', $post->deskripsi) }}</textarea>
                        </div>
                        <div class="adm-field">
                            <label class="adm-label">Job Description / Jobdesk</label>
                            <textarea name="jobdesk" class="adm-textarea" rows="6">{{ old('jobdesk', $post->jobdesk) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Requirements Builder --}}
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
                        Saat ini terdapat <strong>{{ count($post->requirements ?? []) }}</strong> field di form pendaftaran.
                    </p>
                    <div id="requirementsContainer"></div>
                    <div id="emptyState"
                        style="text-align:center;padding:30px;color:var(--adm-text-faint);border:2px dashed var(--adm-border);border-radius:8px;display:none;">
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
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection

@include('superadmin.recruitment-posts.partials.requirements-builder-script')
