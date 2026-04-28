@extends('layouts.app')
@section('template_title')
    Resep Makanan
@endsection

@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        <div class="adm-header">
            <div class="adm-header-left">
                <h1>Resep Makanan</h1>
                <p>Kelola data resep makanan dan minuman</p>
            </div>
            <a href="{{ route('superadmin.resep-makanans.create') }}" class="adm-btn-primary">
                <svg viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Buat Resep Makanan
            </a>
        </div>

        <div class="adm-card">
            <div class="adm-card-header">
                <div class="adm-card-title">
                    <svg viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                    </svg>
                    Daftar Resep Makanan
                    <span class="adm-count-badge">{{ $resepMakanans->total() }}</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th style="width:44px">#</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th class="tc" style="width:130px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($resepMakanans as $resepMakanan)
                            <tr>
                                <td><span class="adm-rownum">{{ ++$i }}</span></td>
                                <td>
                                    <a href="{{ route('superadmin.resep-makanans.show', $resepMakanan->hashed_id) }}"
                                        style="font-weight:600;font-size:13px;color:var(--adm-text-dark);text-decoration:none;">
                                        {{ $resepMakanan->nama_produk }}
                                    </a>
                                </td>
                                <td>
                                    <span class="adm-badge adm-badge-info"
                                        style="text-transform: capitalize;">{{ $resepMakanan->kategori }}</span>
                                </td>
                                <td class="tc">
                                    <div class="adm-actions">
                                        <a class="adm-btn primary icon-only"
                                            href="{{ route('superadmin.resep-makanans.show', $resepMakanan->hashed_id) }}"
                                            title="Detail">
                                            <svg viewBox="0 0 24 24">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>
                                        </a>
                                        <a class="adm-btn success icon-only"
                                            href="{{ route('superadmin.resep-makanans.edit', $resepMakanan->hashed_id) }}"
                                            title="Edit">
                                            <svg viewBox="0 0 24 24">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('superadmin.resep-makanans.destroy', $resepMakanan->hashed_id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="adm-btn danger icon-only" title="Hapus"
                                                onclick="return confirm('Yakin hapus resep makanan ini?')">
                                                <svg viewBox="0 0 24 24">
                                                    <polyline points="3 6 5 6 21 6" />
                                                    <path d="M19 6l-1 14H6L5 6" />
                                                    <path d="M10 11v6" />
                                                    <path d="M14 11v6" />
                                                    <path d="M9 6V4h6v2" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="adm-empty">
                                        <svg viewBox="0 0 24 24">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                            <polyline points="14 2 14 8 20 8" />
                                        </svg>
                                        <p>Belum ada resep makanan yang dibuat.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="adm-card-footer">
                <span class="adm-footer-info">
                    Menampilkan {{ $resepMakanans->firstItem() ?? 0 }}–{{ $resepMakanans->lastItem() ?? 0 }}
                    dari {{ $resepMakanans->total() }} resep makanan
                </span>
                @include('layouts.pagination', ['paginator' => $resepMakanans])
            </div>
        </div>
    </div>
@endsection
