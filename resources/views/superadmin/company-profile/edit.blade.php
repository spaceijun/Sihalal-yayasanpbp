@extends('layouts.app')
@section('title', 'Edit ' . ucfirst($profile->page) . ' - Company Profile')

@section('content')
<div class="adm-page">
    <!-- Page Header -->
    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Edit Halaman: {{ ucfirst($profile->page) }}</h1>
            <p>Kelola konten dan pengaturan halaman company profile</p>
        </div>
        <a href="{{ route($routePrefix . '.company-profile.index') }}" class="adm-btn-secondary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"/>
                <polyline points="12 19 5 12 12 5"/>
            </svg>
            Kembali
        </a>
    </div>

    <!-- Messages -->
    @include('layouts.messages')

    <form action="{{ route($routePrefix . '.company-profile.update', $profile->page) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Page Settings -->
        <div class="adm-form-section">
            <div class="adm-form-section-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
                Pengaturan Halaman
            </div>
            <div class="adm-form-body">
                <div class="adm-form-grid">
                    <div class="adm-field">
                        <label for="title" class="adm-label">Judul Halaman</label>
                        <input type="text" class="adm-input" id="title" name="title"
                               value="{{ old('title', $profile->title) }}" required>
                    </div>
                    <div class="adm-field">
                        <label for="meta_keywords" class="adm-label">Meta Keywords</label>
                        <input type="text" class="adm-input" id="meta_keywords" name="meta_keywords"
                               value="{{ old('meta_keywords', $profile->meta_keywords) }}"
                               placeholder="Pisahkan dengan koma">
                        <span class="adm-hint">Pisahkan dengan koma</span>
                    </div>
                </div>
                <div class="adm-field mt-3">
                    <label for="meta_description" class="adm-label">Meta Description</label>
                    <textarea class="adm-textarea" id="meta_description" name="meta_description" rows="2"
                              maxlength="160">{{ old('meta_description', $profile->meta_description) }}</textarea>
                    <span class="adm-hint">Maksimal 160 karakter</span>
                </div>
                <div class="adm-form-actions mt-3">
                    <button type="submit" class="adm-btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg>
                        Simpan Pengaturan
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Sections -->
    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <line x1="3" y1="9" x2="21" y2="9"/>
                    <line x1="9" y1="21" x2="9" y2="9"/>
                </svg>
                Sections
            </div>
            <button type="button" class="adm-btn-primary adm-btn-success" data-bs-toggle="modal"
                    data-bs-target="#addSectionModal">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Tambah Section
            </button>
        </div>
        <div class="table-responsive">
            @if($sections->isEmpty())
                <div class="adm-empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    <p>Belum ada section</p>
                    <p class="text-muted">Tambahkan section untuk menampilkan konten.</p>
                </div>
            @else
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Key</th>
                            <th>Judul</th>
                            <th style="width: 100px;">Status</th>
                            <th class="tr" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sections as $section)
                        <tr>
                            <td><span class="adm-rownum">{{ $loop->iteration }}</span></td>
                            <td><code class="adm-mono">{{ $section->section_key }}</code></td>
                            <td>{{ Str::limit($section->title, 40) ?? '-' }}</td>
                            <td>
                                @if($section->is_active)
                                    <span class="adm-badge adm-badge-success">
                                        <span class="dot"></span>Aktif
                                    </span>
                                @else
                                    <span class="adm-badge adm-badge-nonaktif">
                                        <span class="dot"></span>Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="tr">
                                <div class="adm-actions">
                                    <button type="button" class="adm-btn primary"
                                            onclick="editSection({{ $section->id }})">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                    </button>
                                    <form action="{{ route($routePrefix . '.company-profile.sections.destroy', [$profile->page, $section->id]) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Yakin hapus section ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="adm-btn danger">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>

<!-- Add Section Modal -->
<div class="modal fade adm-modal" id="addSectionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route($routePrefix . '.company-profile.sections.store', $profile->page) }}" method="POST"
                  enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Tambah Section Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="adm-form-grid">
                        <div class="adm-field">
                            <label for="section_key" class="adm-label">Tipe Section</label>
                            <select class="adm-field-select" id="section_key" name="section_key" required>
                                <option value="">-- Pilih Tipe --</option>
                                @foreach($availableSections as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="adm-field">
                            <label for="sort_order" class="adm-label">Urutan</label>
                            <input type="number" class="adm-input" id="sort_order" name="sort_order"
                                   value="0" min="0">
                        </div>
                    </div>
                    <div class="adm-field mt-3">
                        <label for="title" class="adm-label">Judul</label>
                        <input type="text" class="adm-input" id="title" name="title">
                    </div>
                    <div class="adm-field mt-3">
                        <label for="content" class="adm-label">Konten</label>
                        <textarea class="adm-textarea" id="content" name="content" rows="4"></textarea>
                    </div>
                    <div class="adm-field mt-3">
                        <label for="image" class="adm-label">Gambar</label>
                        <input type="file" class="adm-input" id="image" name="image" accept="image/*">
                    </div>
                    <div class="adm-form-grid mt-3">
                        <div class="adm-field">
                            <label for="link" class="adm-label">Link</label>
                            <input type="url" class="adm-input" id="link" name="link">
                        </div>
                        <div class="adm-field">
                            <label for="link_text" class="adm-label">Text Link</label>
                            <input type="text" class="adm-input" id="link_text" name="link_text">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="adm-btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Section Modal -->
<div class="modal fade adm-modal" id="editSectionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editSectionForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                        Edit Section
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="editSectionBody">
                    <!-- Content loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="adm-btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editSection(sectionId) {
    // This would typically load section data via AJAX
    // For now, we'll redirect to a dedicated edit page
    alert('Fitur edit section akan segera hadir. Gunakan section baru untuk saat ini.');
}
</script>
@endpush
