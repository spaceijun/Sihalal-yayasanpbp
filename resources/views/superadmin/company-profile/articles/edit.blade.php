@extends('layouts.app')
@section('title', 'Edit Artikel')

@section('content')
    <div class="adm-page">
        <!-- Page Header -->
        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Edit Artikel</h1>
                <p>Edit artikel untuk company profile</p>
            </div>
            <a href="{{ route($routePrefix . '.articles.index') }}" class="adm-btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12" />
                    <polyline points="12 19 5 12 12 5" />
                </svg>
                Kembali
            </a>
        </div>

        <!-- Messages -->
        @include('layouts.messages')

        <form action="{{ route($routePrefix . '.articles.update', $article->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <div class="adm-form-section">
                        <div class="adm-form-section-header">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <polyline points="14 2 14 8 20 8" />
                            </svg>
                            Konten Artikel
                        </div>
                        <div class="adm-form-body">
                            <div class="adm-field">
                                <label for="title" class="adm-label">Judul Artikel <span class="req">*</span></label>
                                <input type="text" class="adm-input" id="title" name="title"
                                    value="{{ old('title', $article->title) }}" required>
                            </div>

                            <div class="adm-field mt-3">
                                <label for="slug" class="adm-label">Slug (URL)</label>
                                <input type="text" class="adm-input" id="slug" name="slug"
                                    value="{{ old('slug', $article->slug) }}">
                            </div>

                            <div class="adm-field mt-3">
                                <label for="excerpt" class="adm-label">Excerpt / Ringkasan</label>
                                <textarea class="adm-textarea" id="excerpt" name="excerpt" rows="2" maxlength="500">{{ old('excerpt', $article->excerpt) }}</textarea>
                            </div>

                            <div class="adm-field mt-3">
                                <label for="content" class="adm-label">Konten Artikel <span class="req">*</span></label>
                                <textarea class="adm-textarea" id="content" name="content" rows="15" required>{{ old('content', $article->content) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Publish Settings -->
                    <div class="adm-form-section">
                        <div class="adm-form-section-header">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                            Pengaturan Publish
                        </div>
                        <div class="adm-form-body">
                            <div class="adm-field">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_published" name="is_published"
                                        value="1" {{ old('is_published', $article->is_published) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_published">
                                        Publish artikel
                                    </label>
                                </div>
                            </div>

                            <div class="adm-field mt-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured"
                                        value="1" {{ old('is_featured', $article->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">
                                        Tampilkan di Featured
                                    </label>
                                </div>
                            </div>

                            <div class="adm-field mt-3">
                                <label for="published_at" class="adm-label">Tanggal Publish</label>
                                <input type="datetime-local" class="adm-input" id="published_at" name="published_at"
                                    value="{{ old('published_at', $article->published_at?->format('Y-m-d\TH:i')) }}">
                            </div>
                        </div>
                    </div>

                    <!-- Categorization -->
                    <div class="adm-form-section">
                        <div class="adm-form-section-header">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
                            </svg>
                            Kategori & Penulis
                        </div>
                        <div class="adm-form-body">
                            <div class="adm-field">
                                <label for="category" class="adm-label">Kategori</label>
                                <select class="adm-field-select" id="category" name="category">
                                    <option value="artikel"
                                        {{ old('category', $article->category) == 'artikel' ? 'selected' : '' }}>Artikel
                                    </option>
                                    <option value="berita"
                                        {{ old('category', $article->category) == 'berita' ? 'selected' : '' }}>Berita
                                    </option>
                                    <option value="tips"
                                        {{ old('category', $article->category) == 'tips' ? 'selected' : '' }}>Tips</option>
                                    <option value="panduan"
                                        {{ old('category', $article->category) == 'panduan' ? 'selected' : '' }}>Panduan
                                    </option>
                                </select>
                            </div>

                            <div class="adm-field mt-3">
                                <label for="author" class="adm-label">Penulis</label>
                                <input type="text" class="adm-input" id="author" name="author"
                                    value="{{ old('author', $article->author) }}">
                            </div>
                        </div>
                    </div>

                    <!-- Image -->
                    <div class="adm-form-section">
                        <div class="adm-form-section-header">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                <circle cx="8.5" cy="8.5" r="1.5" />
                                <polyline points="21 15 16 10 5 21" />
                            </svg>
                            Gambar
                        </div>
                        <div class="adm-form-body">
                            @if ($article->image)
                                <div class="mb-3">
                                    <img src="{{ Storage::url($article->image) }}" alt="Cover"
                                        class="img-fluid rounded mb-2" style="max-width: 100%;">
                                </div>
                            @endif
                            <div class="adm-field">
                                <label for="image"
                                    class="adm-label">{{ $article->image ? 'Ganti Gambar' : 'Upload Gambar' }}</label>
                                <input type="file" class="adm-input" id="image" name="image" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="adm-btn-primary w-100">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                            <polyline points="17 21 17 13 7 13 7 21" />
                            <polyline points="7 3 7 8 15 8" />
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
