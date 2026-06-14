@extends('layouts.app')

@section('title', 'Kelola Konten Company Profile')

@section('content')
    <div class="adm-page">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="adm-header">
                <div class="adm-header-left">
                    <h1>Kelola Konten Company Profile</h1>
                    <p>Kelola semua konten untuk halaman publik company profile</p>
                </div>
            </div>

            @include('layouts.messages')

            <!-- Nav tabs -->
            <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#pages"><i
                            class="ri-file-text-line me-1"></i> Halaman</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#statistics"><i
                            class="ri-bar-chart-line me-1"></i> Statistik</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#benefits"><i
                            class="ri-star-line me-1"></i> Keunggulan</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#testimonials"><i
                            class="ri-chat-quote-line me-1"></i> Testimoni</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#teams"><i
                            class="ri-team-line me-1"></i> Tim</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#histories"><i
                            class="ri-history-line me-1"></i> Sejarah</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#categories"><i
                            class="ri-folder-line me-1"></i> Kategori</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#social"><i
                            class="ri-share-line me-1"></i> Social Media</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#articles"><i
                            class="ri-article-line me-1"></i> Artikel</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#contacts"><i
                            class="ri-message-3-line me-1"></i> Pesan</a></li>
            </ul>

            <div class="tab-content">
                <!-- ==================== PAGES ==================== -->
                <div class="tab-pane active" id="pages">
                    <div class="adm-card">
                        <div class="adm-card-header">
                            <div class="adm-card-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                </svg>
                                Halaman Company Profile
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                @foreach ($pages ?? [] as $page)
                                    <div class="col-md-4 mb-4">
                                        <div class="adm-card h-100" style="margin-bottom: 0;">
                                            <div class="adm-card-body" style="padding: 16px;">
                                                <div class="d-flex align-items-center gap-3 mb-3">
                                                    <div class="adm-avatar"
                                                        style="background: var(--adm-blue-lt); color: var(--adm-blue);">
                                                        <i class="ri-file-text-line"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-bold text-capitalize">{{ $page->page }}</h6>
                                                        <small class="text-muted">{{ $page->sections->count() }}
                                                            sections</small>
                                                    </div>
                                                </div>
                                                <h5 class="mb-2" style="font-size: 1rem;">
                                                    {{ Str::limit($page->title, 40) }}</h5>
                                                <p class="text-muted small mb-0">
                                                    {{ Str::limit($page->meta_description ?? '', 60) }}</p>
                                            </div>
                                            <div class="adm-card-footer bg-transparent"
                                                style="padding: 12px 16px; border-top: 1px solid var(--adm-border);">
                                                <a href="{{ route($routePrefix . '.company-profile.edit', $page->page) }}"
                                                    class="adm-btn primary w-100">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2">
                                                        <path
                                                            d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                    </svg>
                                                    Edit Halaman
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== STATISTICS ==================== -->
                <div class="tab-pane" id="statistics">
                    <div class="adm-card">
                        <div class="adm-card-header">
                            <div class="adm-card-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="20" x2="18" y2="10" />
                                    <line x1="12" y1="20" x2="12" y2="4" />
                                    <line x1="6" y1="20" x2="6" y2="14" />
                                </svg>
                                Statistik Perusahaan
                            </div>
                            <button class="adm-btn-primary adm-btn-success" data-bs-toggle="modal"
                                data-bs-target="#addStatisticModal">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                                Tambah
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="adm-table w-100">
                                <thead>
                                    <tr>
                                        <th class="tc" style="width:44px">#</th>
                                        <th>Judul</th>
                                        <th class="tc">Nilai</th>
                                        <th class="tc">Icon</th>
                                        <th class="tc">Warna</th>
                                        <th class="tc">Status</th>
                                        <th class="tc" style="width:110px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stats ?? [] as $i => $stat)
                                        <tr>
                                            <td class="tc"><span class="adm-rownum">{{ $i + 1 }}</span></td>
                                            <td>{{ $stat->title }}</td>
                                            <td class="tc"><span
                                                    class="adm-badge adm-badge-success">{{ $stat->value }}{{ $stat->suffix }}</span>
                                            </td>
                                            <td class="tc"><i class="{{ $stat->icon }}"></i></td>
                                            <td class="tc"><span class="adm-badge"
                                                    style="background: {{ $stat->color }}; color: #fff;">{{ $stat->color }}</span>
                                            </td>
                                            <td class="tc">
                                                <form
                                                    action="{{ route($routePrefix . '.company-content.statistics.toggle', $stat->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="adm-btn {{ $stat->is_active ? 'success' : '' }}">
                                                        <i
                                                            class="ri-{{ $stat->is_active ? 'eye-line' : 'eye-off-line' }}"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="tc">
                                                <div class="adm-actions">
                                                    <button class="adm-btn primary" data-bs-toggle="modal"
                                                        data-bs-target="#editStatisticModal{{ $stat->id }}">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="2">
                                                            <path
                                                                d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                            <path
                                                                d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                        </svg>
                                                    </button>
                                                    <form
                                                        action="{{ route($routePrefix . '.company-content.statistics.destroy', $stat->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Hapus?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="adm-btn danger">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2">
                                                                <polyline points="3 6 5 6 21 6" />
                                                                <path
                                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ==================== BENEFITS ==================== -->
                <div class="tab-pane" id="benefits">
                    <div class="adm-card">
                        <div class="adm-card-header">
                            <div class="adm-card-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polygon
                                        points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                </svg>
                                Keunggulan Perusahaan
                            </div>
                            <button class="adm-btn-primary adm-btn-success" data-bs-toggle="modal"
                                data-bs-target="#addBenefitModal">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                                Tambah
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="adm-table w-100">
                                <thead>
                                    <tr>
                                        <th class="tc" style="width:44px">#</th>
                                        <th>Judul</th>
                                        <th class="tc">Icon</th>
                                        <th>Deskripsi</th>
                                        <th class="tc">Status</th>
                                        <th class="tc" style="width:110px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($benefits ?? [] as $i => $b)
                                        <tr>
                                            <td class="tc"><span class="adm-rownum">{{ $i + 1 }}</span></td>
                                            <td>{{ $b->title }}</td>
                                            <td class="tc"><i class="{{ $b->icon }} fs-4"></i></td>
                                            <td>{{ Str::limit($b->description, 50) }}</td>
                                            <td class="tc">
                                                <form
                                                    action="{{ route($routePrefix . '.company-content.benefits.toggle', $b->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="adm-btn {{ $b->is_active ? 'success' : '' }}">
                                                        <i
                                                            class="ri-{{ $b->is_active ? 'eye-line' : 'eye-off-line' }}"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="tc">
                                                <div class="adm-actions">
                                                    <button class="adm-btn primary" data-bs-toggle="modal"
                                                        data-bs-target="#editBenefitModal{{ $b->id }}">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="2">
                                                            <path
                                                                d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                            <path
                                                                d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                        </svg>
                                                    </button>
                                                    <form
                                                        action="{{ route($routePrefix . '.company-content.benefits.destroy', $b->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Hapus?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="adm-btn danger">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2">
                                                                <polyline points="3 6 5 6 21 6" />
                                                                <path
                                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ==================== TESTIMONIALS ==================== -->
                <div class="tab-pane" id="testimonials">
                    <div class="adm-card">
                        <div class="adm-card-header">
                            <div class="adm-card-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                </svg>
                                Testimoni
                            </div>
                            <button class="adm-btn-primary adm-btn-success" data-bs-toggle="modal"
                                data-bs-target="#addTestimonialModal">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                                Tambah
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="adm-table w-100">
                                <thead>
                                    <tr>
                                        <th class="tc" style="width:44px">#</th>
                                        <th>Nama</th>
                                        <th>Posisi</th>
                                        <th>Testimoni</th>
                                        <th class="tc">Rating</th>
                                        <th class="tc">Status</th>
                                        <th class="tc" style="width:110px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($testimonials ?? [] as $i => $t)
                                        <tr>
                                            <td class="tc"><span class="adm-rownum">{{ $i + 1 }}</span></td>
                                            <td>{{ $t->name }}</td>
                                            <td>{{ $t->position }}</td>
                                            <td>{{ Str::limit($t->testimonial, 60) }}</td>
                                            <td class="tc">
                                                @for ($j = 0; $j < 5; $j++)
                                                    <i
                                                        class="ri-star{{ $j < $t->rating ? '-fill' : '' }} text-warning"></i>
                                                @endfor
                                            </td>
                                            <td class="tc">
                                                <form
                                                    action="{{ route($routePrefix . '.company-content.testimonials.toggle', $t->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="adm-btn {{ $t->is_active ? 'success' : '' }}">
                                                        <i
                                                            class="ri-{{ $t->is_active ? 'eye-line' : 'eye-off-line' }}"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="tc">
                                                <div class="adm-actions">
                                                    <button class="adm-btn primary" data-bs-toggle="modal"
                                                        data-bs-target="#editTestimonialModal{{ $t->id }}">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="2">
                                                            <path
                                                                d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                            <path
                                                                d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                        </svg>
                                                    </button>
                                                    <form
                                                        action="{{ route($routePrefix . '.company-content.testimonials.destroy', $t->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Hapus?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="adm-btn danger">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2">
                                                                <polyline points="3 6 5 6 21 6" />
                                                                <path
                                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ==================== TEAMS ==================== -->
                <div class="tab-pane" id="teams">
                    <div class="adm-card">
                        <div class="adm-card-header">
                            <div class="adm-card-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>
                                Tim Manajemen
                            </div>
                            <button class="adm-btn-primary adm-btn-success" data-bs-toggle="modal"
                                data-bs-target="#addTeamModal">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                                Tambah
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="adm-table w-100">
                                <thead>
                                    <tr>
                                        <th class="tc" style="width:44px">#</th>
                                        <th class="tc">Foto</th>
                                        <th>Nama</th>
                                        <th>Posisi</th>
                                        <th class="tc">Status</th>
                                        <th class="tc" style="width:110px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($teams ?? [] as $i => $team)
                                        <tr>
                                            <td class="tc"><span class="adm-rownum">{{ $i + 1 }}</span></td>
                                            <td class="tc">
                                                @if ($team->photo)
                                                    <img src="{{ Storage::url($team->photo) }}"
                                                        alt="{{ $team->name }}" class="rounded-circle" width="40"
                                                        height="40">
                                                @else
                                                    <div class="adm-avatar mx-auto"
                                                        style="background: var(--adm-blue-lt); color: var(--adm-blue);">
                                                        {{ substr($team->name, 0, 1) }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $team->name }}</td>
                                            <td>{{ $team->position }}</td>
                                            <td class="tc">
                                                <form
                                                    action="{{ route($routePrefix . '.company-content.teams.toggle', $team->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="adm-btn {{ $team->is_active ? 'success' : '' }}">
                                                        <i
                                                            class="ri-{{ $team->is_active ? 'eye-line' : 'eye-off-line' }}"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="tc">
                                                <div class="adm-actions">
                                                    <button class="adm-btn primary" data-bs-toggle="modal"
                                                        data-bs-target="#editTeamModal{{ $team->id }}">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="2">
                                                            <path
                                                                d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                            <path
                                                                d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                        </svg>
                                                    </button>
                                                    <form
                                                        action="{{ route($routePrefix . '.company-content.teams.destroy', $team->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Hapus?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="adm-btn danger">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2">
                                                                <polyline points="3 6 5 6 21 6" />
                                                                <path
                                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ==================== HISTORIES ==================== -->
                <div class="tab-pane" id="histories">
                    <div class="adm-card">
                        <div class="adm-card-header">
                            <div class="adm-card-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 6 12 12 16 14" />
                                </svg>
                                Sejarah Perusahaan
                            </div>
                            <button class="adm-btn-primary adm-btn-success" data-bs-toggle="modal"
                                data-bs-target="#addHistoryModal">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                                Tambah
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="adm-table w-100">
                                <thead>
                                    <tr>
                                        <th class="tc" style="width:44px">#</th>
                                        <th class="tc">Tahun</th>
                                        <th>Judul</th>
                                        <th>Deskripsi</th>
                                        <th class="tc" style="width:110px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($histories ?? [] as $i => $h)
                                        <tr>
                                            <td class="tc"><span class="adm-rownum">{{ $i + 1 }}</span></td>
                                            <td class="tc"><span
                                                    class="adm-badge adm-badge-info">{{ $h->year }}</span></td>
                                            <td>{{ $h->title }}</td>
                                            <td>{{ Str::limit($h->description, 60) }}</td>
                                            <td class="tc">
                                                <div class="adm-actions">
                                                    <button class="adm-btn primary" data-bs-toggle="modal"
                                                        data-bs-target="#editHistoryModal{{ $h->id }}">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="2">
                                                            <path
                                                                d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                            <path
                                                                d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                        </svg>
                                                    </button>
                                                    <form
                                                        action="{{ route($routePrefix . '.company-content.histories.destroy', $h->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Hapus?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="adm-btn danger">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2">
                                                                <polyline points="3 6 5 6 21 6" />
                                                                <path
                                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ==================== CATEGORIES ==================== -->
                <div class="tab-pane" id="categories">
                    <div class="adm-card">
                        <div class="adm-card-header">
                            <div class="adm-card-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
                                </svg>
                                Kategori Artikel
                            </div>
                            <button class="adm-btn-primary adm-btn-success" data-bs-toggle="modal"
                                data-bs-target="#addCategoryModal">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                                Tambah
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="adm-table w-100">
                                <thead>
                                    <tr>
                                        <th class="tc" style="width:44px">#</th>
                                        <th>Nama</th>
                                        <th>Slug</th>
                                        <th class="tc">Icon</th>
                                        <th class="tc" style="width:110px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories ?? [] as $i => $c)
                                        <tr>
                                            <td class="tc"><span class="adm-rownum">{{ $i + 1 }}</span></td>
                                            <td>{{ $c->name }}</td>
                                            <td><code class="adm-mono">{{ $c->slug }}</code></td>
                                            <td class="tc"><i class="{{ $c->icon }}"></i></td>
                                            <td class="tc">
                                                <div class="adm-actions">
                                                    <button class="adm-btn primary" data-bs-toggle="modal"
                                                        data-bs-target="#editCategoryModal{{ $c->id }}">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="2">
                                                            <path
                                                                d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                            <path
                                                                d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                        </svg>
                                                    </button>
                                                    <form
                                                        action="{{ route($routePrefix . '.company-content.categories.destroy', $c->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Hapus?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="adm-btn danger">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2">
                                                                <polyline points="3 6 5 6 21 6" />
                                                                <path
                                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ==================== SOCIAL MEDIA ==================== -->
                <div class="tab-pane" id="social">
                    <div class="adm-card">
                        <div class="adm-card-header">
                            <div class="adm-card-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="18" cy="5" r="3" />
                                    <circle cx="6" cy="12" r="3" />
                                    <circle cx="18" cy="19" r="3" />
                                    <line x1="8.59" y1="13.51" x2="15.42" y2="17.49" />
                                    <line x1="15.41" y1="6.51" x2="8.59" y2="10.49" />
                                </svg>
                                Social Media
                            </div>
                            <button class="adm-btn-primary adm-btn-success" data-bs-toggle="modal"
                                data-bs-target="#addSocialModal">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                                Tambah
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="adm-table w-100">
                                <thead>
                                    <tr>
                                        <th class="tc" style="width:44px">#</th>
                                        <th>Platform</th>
                                        <th>URL</th>
                                        <th class="tc">Icon</th>
                                        <th class="tc">Status</th>
                                        <th class="tc" style="width:110px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($socialMedia ?? [] as $i => $s)
                                        <tr>
                                            <td class="tc"><span class="adm-rownum">{{ $i + 1 }}</span></td>
                                            <td>{{ $s->platform }}</td>
                                            <td><a href="{{ $s->url }}" target="_blank"
                                                    class="text-primary">{{ Str::limit($s->url, 30) }}</a></td>
                                            <td class="tc"><i class="{{ $s->icon }}"
                                                    style="color: {{ $s->color }};"></i></td>
                                            <td class="tc">
                                                <form
                                                    action="{{ route($routePrefix . '.company-content.social-media.toggle', $s->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="adm-btn {{ $s->is_active ? 'success' : '' }}">
                                                        <i
                                                            class="ri-{{ $s->is_active ? 'eye-line' : 'eye-off-line' }}"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="tc">
                                                <div class="adm-actions">
                                                    <button class="adm-btn primary" data-bs-toggle="modal"
                                                        data-bs-target="#editSocialModal{{ $s->id }}">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="2">
                                                            <path
                                                                d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                            <path
                                                                d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                        </svg>
                                                    </button>
                                                    <form
                                                        action="{{ route($routePrefix . '.company-content.social-media.destroy', $s->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Hapus?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="adm-btn danger">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2">
                                                                <polyline points="3 6 5 6 21 6" />
                                                                <path
                                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ==================== ARTICLES LINK ==================== -->
                <div class="tab-pane" id="articles">
                    <div class="adm-card">
                        <div class="adm-card-header">
                            <div class="adm-card-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                </svg>
                                Kelola Artikel
                            </div>
                            <a href="{{ route($routePrefix . '.articles.index') }}"
                                class="adm-btn-primary adm-btn-success">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                                Tambah Artikel Baru
                            </a>
                        </div>
                        <div class="adm-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <polyline points="14 2 14 8 20 8" />
                            </svg>
                            <p>Kelola Artikel</p>
                            <p class="text-muted">Klik tombol di bawah untuk mengelola artikel company profile.</p>
                            <a href="{{ route($routePrefix . '.articles.index') }}" class="adm-btn primary">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="3" />
                                    <path
                                        d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" />
                                </svg>
                                Kelola Artikel
                            </a>
                        </div>
                    </div>
                </div>

                <!-- ==================== CONTACTS LINK ==================== -->
                <div class="tab-pane" id="contacts">
                    <div class="adm-card">
                        <div class="adm-card-header">
                            <div class="adm-card-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                </svg>
                                Pesan Kontak
                            </div>
                            <a href="{{ route($routePrefix . '.contact-messages.index') }}"
                                class="adm-btn-primary adm-btn-success">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                Lihat Semua Pesan
                            </a>
                        </div>
                        <div class="adm-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                            </svg>
                            <p>Pesan Kontak</p>
                            <p class="text-muted">Klik tombol di bawah untuk melihat pesan yang masuk.</p>
                            <a href="{{ route($routePrefix . '.contact-messages.index') }}" class="adm-btn primary">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="3" />
                                    <path
                                        d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" />
                                </svg>
                                Kelola Pesan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== MODALS ==================== -->

    <!-- Add Statistic Modal -->
    <div class="modal fade adm-modal" id="addStatisticModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route($routePrefix . '.company-content.statistics.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            Tambah Statistik
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="adm-field">
                            <label class="adm-label">Judul</label>
                            <input type="text" name="title" class="adm-input" required>
                        </div>
                        <div class="adm-form-grid mt-3">
                            <div class="adm-field">
                                <label class="adm-label">Nilai</label>
                                <input type="text" name="value" class="adm-input" required>
                            </div>
                            <div class="adm-field">
                                <label class="adm-label">Suffix</label>
                                <input type="text" name="suffix" class="adm-input" value="+">
                            </div>
                        </div>
                        <div class="adm-form-grid mt-3">
                            <div class="adm-field">
                                <label class="adm-label">Icon (Remix)</label>
                                <input type="text" name="icon" class="adm-input" value="ri-bar-chart-line">
                            </div>
                            <div class="adm-field">
                                <label class="adm-label">Warna</label>
                                <input type="text" name="color" class="adm-input" value="#22c55e">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="adm-btn-primary adm-btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Benefit Modal -->
    <div class="modal fade adm-modal" id="addBenefitModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route($routePrefix . '.company-content.benefits.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            Tambah Keunggulan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="adm-field">
                            <label class="adm-label">Judul</label>
                            <input type="text" name="title" class="adm-input" required>
                        </div>
                        <div class="adm-field mt-3">
                            <label class="adm-label">Icon (Remix)</label>
                            <input type="text" name="icon" class="adm-input" value="ri-check-line">
                        </div>
                        <div class="adm-field mt-3">
                            <label class="adm-label">Deskripsi</label>
                            <textarea name="description" class="adm-textarea" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="adm-btn-primary adm-btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Testimonial Modal -->
    <div class="modal fade adm-modal" id="addTestimonialModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route($routePrefix . '.company-content.testimonials.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            Tambah Testimoni
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="adm-form-grid">
                            <div class="adm-field">
                                <label class="adm-label">Nama</label>
                                <input type="text" name="name" class="adm-input" required>
                            </div>
                            <div class="adm-field">
                                <label class="adm-label">Rating</label>
                                <select name="rating" class="adm-field-select">
                                    <option value="5">5 Bintang</option>
                                    <option value="4">4 Bintang</option>
                                    <option value="3">3 Bintang</option>
                                    <option value="2">2 Bintang</option>
                                    <option value="1">1 Bintang</option>
                                </select>
                            </div>
                        </div>
                        <div class="adm-form-grid mt-3">
                            <div class="adm-field">
                                <label class="adm-label">Posisi</label>
                                <input type="text" name="position" class="adm-input">
                            </div>
                            <div class="adm-field">
                                <label class="adm-label">Perusahaan</label>
                                <input type="text" name="company" class="adm-input">
                            </div>
                        </div>
                        <div class="adm-field mt-3">
                            <label class="adm-label">Testimoni</label>
                            <textarea name="testimonial" class="adm-textarea" rows="4" required></textarea>
                        </div>
                        <div class="adm-field mt-3">
                            <label class="adm-label">Foto</label>
                            <input type="file" name="photo" class="adm-input" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="adm-btn-primary adm-btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Team Modal -->
    <div class="modal fade adm-modal" id="addTeamModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route($routePrefix . '.company-content.teams.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            Tambah Tim
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="adm-field">
                            <label class="adm-label">Nama</label>
                            <input type="text" name="name" class="adm-input" required>
                        </div>
                        <div class="adm-field mt-3">
                            <label class="adm-label">Posisi</label>
                            <input type="text" name="position" class="adm-input" required>
                        </div>
                        <div class="adm-field mt-3">
                            <label class="adm-label">Deskripsi</label>
                            <textarea name="description" class="adm-textarea" rows="3"></textarea>
                        </div>
                        <div class="adm-form-grid mt-3">
                            <div class="adm-field">
                                <label class="adm-label">LinkedIn</label>
                                <input type="url" name="linkedin" class="adm-input">
                            </div>
                            <div class="adm-field">
                                <label class="adm-label">Twitter</label>
                                <input type="text" name="twitter" class="adm-input">
                            </div>
                        </div>
                        <div class="adm-field mt-3">
                            <label class="adm-label">Foto</label>
                            <input type="file" name="photo" class="adm-input" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="adm-btn-primary adm-btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add History Modal -->
    <div class="modal fade adm-modal" id="addHistoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route($routePrefix . '.company-content.histories.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            Tambah Sejarah
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="adm-form-grid">
                            <div class="adm-field">
                                <label class="adm-label">Tahun</label>
                                <input type="number" name="year" class="adm-input" min="1900" max="2100"
                                    required>
                            </div>
                            <div class="adm-field">
                                <label class="adm-label">Judul</label>
                                <input type="text" name="title" class="adm-input" required>
                            </div>
                        </div>
                        <div class="adm-field mt-3">
                            <label class="adm-label">Deskripsi</label>
                            <textarea name="description" class="adm-textarea" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="adm-btn-primary adm-btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade adm-modal" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route($routePrefix . '.company-content.categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            Tambah Kategori
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="adm-field">
                            <label class="adm-label">Nama</label>
                            <input type="text" name="name" class="adm-input" required>
                        </div>
                        <div class="adm-field mt-3">
                            <label class="adm-label">Icon (Remix)</label>
                            <input type="text" name="icon" class="adm-input">
                        </div>
                        <div class="adm-field mt-3">
                            <label class="adm-label">Deskripsi</label>
                            <textarea name="description" class="adm-textarea" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="adm-btn-primary adm-btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Social Modal -->
    <div class="modal fade adm-modal" id="addSocialModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route($routePrefix . '.company-content.social-media.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            Tambah Social Media
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="adm-field">
                            <label class="adm-label">Platform</label>
                            <input type="text" name="platform" class="adm-input" required>
                        </div>
                        <div class="adm-field mt-3">
                            <label class="adm-label">URL</label>
                            <input type="url" name="url" class="adm-input" required>
                        </div>
                        <div class="adm-form-grid mt-3">
                            <div class="adm-field">
                                <label class="adm-label">Icon (Remix)</label>
                                <input type="text" name="icon" class="adm-input" value="ri-share-line">
                            </div>
                            <div class="adm-field">
                                <label class="adm-label">Warna</label>
                                <input type="text" name="color" class="adm-input" value="#22c55e">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="adm-btn-primary adm-btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modals -->
    @foreach ($stats ?? [] as $stat)
        <div class="modal fade adm-modal" id="editStatisticModal{{ $stat->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route($routePrefix . '.company-content.statistics.update', $stat->id) }}"
                        method="POST">
                        @csrf @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                </svg>
                                Edit Statistik
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="adm-field">
                                <label class="adm-label">Judul</label>
                                <input type="text" name="title" class="adm-input" value="{{ $stat->title }}"
                                    required>
                            </div>
                            <div class="adm-form-grid mt-3">
                                <div class="adm-field">
                                    <label class="adm-label">Nilai</label>
                                    <input type="text" name="value" class="adm-input"
                                        value="{{ $stat->value }}" required>
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">Suffix</label>
                                    <input type="text" name="suffix" class="adm-input"
                                        value="{{ $stat->suffix }}">
                                </div>
                            </div>
                            <div class="adm-form-grid mt-3">
                                <div class="adm-field">
                                    <label class="adm-label">Icon</label>
                                    <input type="text" name="icon" class="adm-input"
                                        value="{{ $stat->icon }}">
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">Warna</label>
                                    <input type="text" name="color" class="adm-input"
                                        value="{{ $stat->color }}">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="adm-btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    @foreach ($benefits ?? [] as $b)
        <div class="modal fade adm-modal" id="editBenefitModal{{ $b->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route($routePrefix . '.company-content.benefits.update', $b->id) }}"
                        method="POST">
                        @csrf @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                </svg>
                                Edit Keunggulan
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="adm-field">
                                <label class="adm-label">Judul</label>
                                <input type="text" name="title" class="adm-input" value="{{ $b->title }}"
                                    required>
                            </div>
                            <div class="adm-field mt-3">
                                <label class="adm-label">Icon</label>
                                <input type="text" name="icon" class="adm-input" value="{{ $b->icon }}">
                            </div>
                            <div class="adm-field mt-3">
                                <label class="adm-label">Deskripsi</label>
                                <textarea name="description" class="adm-textarea" rows="3">{{ $b->description }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="adm-btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    @foreach ($testimonials ?? [] as $t)
        <div class="modal fade adm-modal" id="editTestimonialModal{{ $t->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route($routePrefix . '.company-content.testimonials.update', $t->id) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                </svg>
                                Edit Testimoni
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="adm-form-grid">
                                <div class="adm-field">
                                    <label class="adm-label">Nama</label>
                                    <input type="text" name="name" class="adm-input"
                                        value="{{ $t->name }}" required>
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">Rating</label>
                                    <select name="rating" class="adm-field-select">
                                        @for ($i = 5; $i >= 1; $i--)
                                            <option value="{{ $i }}"
                                                {{ $t->rating == $i ? 'selected' : '' }}>{{ $i }} Bintang
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="adm-form-grid mt-3">
                                <div class="adm-field">
                                    <label class="adm-label">Posisi</label>
                                    <input type="text" name="position" class="adm-input"
                                        value="{{ $t->position }}">
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">Perusahaan</label>
                                    <input type="text" name="company" class="adm-input"
                                        value="{{ $t->company }}">
                                </div>
                            </div>
                            <div class="adm-field mt-3">
                                <label class="adm-label">Testimoni</label>
                                <textarea name="testimonial" class="adm-textarea" rows="4" required>{{ $t->testimonial }}</textarea>
                            </div>
                            <div class="adm-field mt-3">
                                <label class="adm-label">Foto</label>
                                <input type="file" name="photo" class="adm-input" accept="image/*">
                                @if ($t->photo)
                                    <small class="adm-hint">Current: <img src="{{ Storage::url($t->photo) }}"
                                            height="40"></small>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="adm-btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    @foreach ($teams ?? [] as $team)
        <div class="modal fade adm-modal" id="editTeamModal{{ $team->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route($routePrefix . '.company-content.teams.update', $team->id) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                </svg>
                                Edit Tim
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="adm-field">
                                <label class="adm-label">Nama</label>
                                <input type="text" name="name" class="adm-input" value="{{ $team->name }}"
                                    required>
                            </div>
                            <div class="adm-field mt-3">
                                <label class="adm-label">Posisi</label>
                                <input type="text" name="position" class="adm-input"
                                    value="{{ $team->position }}" required>
                            </div>
                            <div class="adm-field mt-3">
                                <label class="adm-label">Deskripsi</label>
                                <textarea name="description" class="adm-textarea" rows="3">{{ $team->description }}</textarea>
                            </div>
                            <div class="adm-form-grid mt-3">
                                <div class="adm-field">
                                    <label class="adm-label">LinkedIn</label>
                                    <input type="url" name="linkedin" class="adm-input"
                                        value="{{ $team->linkedin }}">
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">Twitter</label>
                                    <input type="text" name="twitter" class="adm-input"
                                        value="{{ $team->twitter }}">
                                </div>
                            </div>
                            <div class="adm-field mt-3">
                                <label class="adm-label">Foto</label>
                                <input type="file" name="photo" class="adm-input" accept="image/*">
                                @if ($team->photo)
                                    <small class="adm-hint">Current: <img src="{{ Storage::url($team->photo) }}"
                                            height="40"></small>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="adm-btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    @foreach ($histories ?? [] as $h)
        <div class="modal fade adm-modal" id="editHistoryModal{{ $h->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route($routePrefix . '.company-content.histories.update', $h->id) }}"
                        method="POST">
                        @csrf @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                </svg>
                                Edit Sejarah
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="adm-form-grid">
                                <div class="adm-field">
                                    <label class="adm-label">Tahun</label>
                                    <input type="number" name="year" class="adm-input"
                                        value="{{ $h->year }}" min="1900" max="2100" required>
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">Judul</label>
                                    <input type="text" name="title" class="adm-input"
                                        value="{{ $h->title }}" required>
                                </div>
                            </div>
                            <div class="adm-field mt-3">
                                <label class="adm-label">Deskripsi</label>
                                <textarea name="description" class="adm-textarea" rows="4">{{ $h->description }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="adm-btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    @foreach ($categories ?? [] as $c)
        <div class="modal fade adm-modal" id="editCategoryModal{{ $c->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route($routePrefix . '.company-content.categories.update', $c->id) }}"
                        method="POST">
                        @csrf @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                </svg>
                                Edit Kategori
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="adm-field">
                                <label class="adm-label">Nama</label>
                                <input type="text" name="name" class="adm-input" value="{{ $c->name }}"
                                    required>
                            </div>
                            <div class="adm-field mt-3">
                                <label class="adm-label">Icon</label>
                                <input type="text" name="icon" class="adm-input"
                                    value="{{ $c->icon }}">
                            </div>
                            <div class="adm-field mt-3">
                                <label class="adm-label">Deskripsi</label>
                                <textarea name="description" class="adm-textarea" rows="3">{{ $c->description }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="adm-btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    @foreach ($socialMedia ?? [] as $s)
        <div class="modal fade adm-modal" id="editSocialModal{{ $s->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route($routePrefix . '.company-content.social-media.update', $s->id) }}"
                        method="POST">
                        @csrf @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                </svg>
                                Edit Social Media
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="adm-field">
                                <label class="adm-label">Platform</label>
                                <input type="text" name="platform" class="adm-input"
                                    value="{{ $s->platform }}" required>
                            </div>
                            <div class="adm-field mt-3">
                                <label class="adm-label">URL</label>
                                <input type="url" name="url" class="adm-input" value="{{ $s->url }}"
                                    required>
                            </div>
                            <div class="adm-form-grid mt-3">
                                <div class="adm-field">
                                    <label class="adm-label">Icon</label>
                                    <input type="text" name="icon" class="adm-input"
                                        value="{{ $s->icon }}">
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">Warna</label>
                                    <input type="text" name="color" class="adm-input"
                                        value="{{ $s->color }}">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="adm-btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection
