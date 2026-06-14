@extends('layouts.app')
@section('title', 'Detail Pesan Kontak')

@section('content')
<div class="adm-page">
    <!-- Page Header -->
    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Detail Pesan</h1>
            <p>Lihat detail pesan kontak</p>
        </div>
        <a href="{{ route($routePrefix . '.contact-messages.index') }}" class="adm-btn-secondary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"/>
                <polyline points="12 19 5 12 12 5"/>
            </svg>
            Kembali
        </a>
    </div>

    <!-- Messages -->
    @include('layouts.messages')

    <div class="row">
        <!-- Message Details -->
        <div class="col-lg-8">
            <div class="adm-card">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        Isi Pesan
                    </div>
                    <span class="adm-badge {{ $message->status === 'pending' ? 'adm-badge-pending' : ($message->status === 'read' ? 'adm-badge-info' : 'adm-badge-success') }}">
                        <span class="dot"></span>
                        {{ ucfirst($message->status) }}
                    </span>
                </div>
                <div class="adm-card-body" style="padding: 20px;">
                    <div class="mb-4">
                        <h5 class="fw-bold">{{ $message->subject ?? 'Tanpa Subjek' }}</h5>
                        <p class="text-muted" style="white-space: pre-wrap;">{{ $message->message }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sender Info & Actions -->
        <div class="col-lg-4">
            <!-- Sender Info -->
            <div class="adm-card">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        Info Pengirim
                    </div>
                </div>
                <div class="adm-card-body" style="padding: 20px;">
                    <div class="adm-info-list">
                        <div class="adm-info-row">
                            <span class="adm-info-key">Nama</span>
                            <span class="adm-info-val">{{ $message->name }}</span>
                        </div>
                        <div class="adm-info-row">
                            <span class="adm-info-key">Email</span>
                            <span class="adm-info-val">
                                <a href="mailto:{{ $message->email }}" class="text-primary">{{ $message->email }}</a>
                            </span>
                        </div>
                        @if($message->phone)
                        <div class="adm-info-row">
                            <span class="adm-info-key">Telepon</span>
                            <span class="adm-info-val">
                                <a href="tel:{{ $message->phone }}" class="text-primary">{{ $message->phone }}</a>
                            </span>
                        </div>
                        @endif
                        <div class="adm-info-row">
                            <span class="adm-info-key">Dikirim</span>
                            <span class="adm-info-val">{{ $message->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="adm-card">
                <div class="adm-card-header">
                    <div class="adm-card-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                        </svg>
                        Aksi
                    </div>
                </div>
                <div class="adm-card-body" style="padding: 20px;">
                    <form action="{{ route($routePrefix . '.contact-messages.update-status', $message->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="adm-field">
                            <label for="status" class="adm-label">Update Status</label>
                            <select class="adm-field-select" id="status" name="status">
                                <option value="pending" {{ $message->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="read" {{ $message->status === 'read' ? 'selected' : '' }}>Read</option>
                                <option value="replied" {{ $message->status === 'replied' ? 'selected' : '' }}>Replied</option>
                                <option value="archived" {{ $message->status === 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>
                        <button type="submit" class="adm-btn-primary w-100 mt-3">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>
                            Update Status
                        </button>
                    </form>

                    <hr style="border-color: var(--adm-border); margin: 16px 0;">

                    <a href="mailto:{{ $message->email }}" class="adm-btn-primary adm-btn-success w-100 mb-2">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                        Balas via Email
                    </a>

                    <form action="{{ route($routePrefix . '.contact-messages.destroy', $message->id) }}" method="POST"
                          class="form-delete">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="adm-btn danger w-100">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                            </svg>
                            Hapus Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Intercept form deletion with SweetAlert2
    $(document).on('submit', '.form-delete', function(e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Pesan masuk ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#74788d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger w-xs me-2',
                cancelButton: 'btn btn-light w-xs'
            },
            buttonsStyling: false
        }).then(function(result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
