@extends('layouts.app')
@section('template_title') Ubah Profil @endsection

@push('styles')
<style>
    /* ── Password Strength & Toggle styling (not present in admin-ui.css) ── */
    .pw-strength-bar {
        height: 4px;
        border-radius: 4px;
        background: var(--adm-border, #edf0f7);
        margin-top: 6px;
        overflow: hidden;
    }
    .pw-strength-fill {
        height: 100%;
        border-radius: 4px;
        width: 0%;
        transition: width .3s, background .3s;
    }
    .pw-strength-label {
        font-size: 11px;
        margin-top: 4px;
        font-weight: 600;
    }
    .adm-input-wrap {
        position: relative;
    }
    .adm-input-wrap .adm-input {
        padding-right: 40px;
    }
    .adm-toggle-pw {
        position: absolute;
        right: 11px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
        color: var(--adm-text-muted, #8a99b3);
        display: flex;
        align-items: center;
    }
    .adm-toggle-pw svg {
        width: 16px;
        height: 16px;
        stroke: currentColor;
        fill: none;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }
</style>
@endpush

@section('content')
<div class="adm-page">
    @include('layouts.messages')

    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Ubah Profil</h1>
            <p>Kelola informasi profil dan keamanan akun Anda</p>
        </div>
    </div>

    <div class="adm-form-grid cols-2" style="align-items: start; gap: 20px;">
        
        {{-- ── SEKSI INFORMASI PROFIL ── --}}
        <div class="adm-form-section">
            <div class="adm-form-section-header">
                <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Informasi Profil
            </div>
            <form action="{{ route('superadmin.profile.update') }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="adm-form-body">
                    <div class="adm-form-grid cols-1" style="gap: 14px;">
                        {{-- Nama --}}
                        <div class="adm-field">
                            <label class="adm-label" for="name">Nama Lengkap <span class="req">*</span></label>
                            <input type="text" name="name" id="name"
                                class="adm-input @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}"
                                placeholder="Nama lengkap Anda" required>
                            @error('name') <span class="adm-error-msg">{{ $message }}</span> @enderror
                        </div>

                        {{-- Email --}}
                        <div class="adm-field">
                            <label class="adm-label" for="email">Alamat Email <span class="req">*</span></label>
                            <input type="email" name="email" id="email"
                                class="adm-input @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}"
                                placeholder="email@example.com" required>
                            @error('email') <span class="adm-error-msg">{{ $message }}</span> @enderror
                        </div>

                        {{-- Role (Readonly) --}}
                        <div class="adm-field">
                            <label class="adm-label" for="role_display">Role</label>
                            <input type="text" id="role_display" class="adm-input"
                                value="{{ $user->role }}" readonly
                                style="background:var(--adm-bg-light,#f8f9fc); color:var(--adm-text-muted,#8a99b3); cursor:not-allowed;">
                            <span class="adm-hint">Role akun Anda bersifat permanen.</span>
                        </div>
                    </div>
                </div>
                <div class="adm-form-actions">
                    <button type="submit" class="adm-btn-primary">
                        <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- ── SEKSI UBAH PASSWORD ── --}}
        <div class="adm-form-section">
            <div class="adm-form-section-header">
                <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Ubah Password
            </div>
            <form action="{{ route('superadmin.profile.password.update') }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="adm-form-body">
                    <div class="adm-form-grid cols-1" style="gap: 14px;">
                        {{-- Password Baru --}}
                        <div class="adm-field">
                            <label class="adm-label" for="password">Password Baru <span class="req">*</span></label>
                            <div class="adm-input-wrap">
                                <input type="password" name="password" id="password"
                                    class="adm-input @error('password') is-invalid @enderror"
                                    placeholder="Masukkan password baru" required>
                                <button type="button" class="adm-toggle-pw" data-target="password" title="Tampilkan/Sembunyikan">
                                    <svg class="icon-eye" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg class="icon-eye-off" viewBox="0 0 24 24" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                </button>
                            </div>
                            <div class="pw-strength-bar" aria-hidden="true">
                                <div class="pw-strength-fill" id="pw-strength-fill"></div>
                            </div>
                            <span class="pw-strength-label" id="pw-strength-label"></span>
                            @error('password') <span class="adm-error-msg">{{ $message }}</span> @enderror
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div class="adm-field">
                            <label class="adm-label" for="password_confirmation">Konfirmasi Password Baru <span class="req">*</span></label>
                            <div class="adm-input-wrap">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="adm-input @error('password_confirmation') is-invalid @enderror"
                                    placeholder="Ulangi password baru" required>
                                <button type="button" class="adm-toggle-pw" data-target="password_confirmation" title="Tampilkan/Sembunyikan">
                                    <svg class="icon-eye" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg class="icon-eye-off" viewBox="0 0 24 24" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                </button>
                            </div>
                            @error('password_confirmation') <span class="adm-error-msg">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="adm-form-actions">
                    <button type="submit" class="adm-btn-primary">
                        <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Perbarui Password
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    // ── Password show/hide toggle ──────────────────────────────────────
    document.querySelectorAll('.adm-toggle-pw').forEach(btn => {
        btn.addEventListener('click', function () {
            const input   = document.getElementById(this.dataset.target);
            const iconEye    = this.querySelector('.icon-eye');
            const iconEyeOff = this.querySelector('.icon-eye-off');
            const isHidden = input.type === 'password';
            input.type        = isHidden ? 'text' : 'password';
            iconEye.style.display    = isHidden ? 'none'  : '';
            iconEyeOff.style.display = isHidden ? ''      : 'none';
        });
    });

    // ── Password strength indicator ────────────────────────────────────
    const pwInput  = document.getElementById('password');
    const fill     = document.getElementById('pw-strength-fill');
    const label    = document.getElementById('pw-strength-label');

    const levels = [
        { min: 0,  color: '#ef4444', text: '',                    width: '0%'   },
        { min: 1,  color: '#ef4444', text: 'Sangat lemah',        width: '20%'  },
        { min: 4,  color: '#f97316', text: 'Lemah',               width: '40%'  },
        { min: 6,  color: '#eab308', text: 'Cukup',               width: '60%'  },
        { min: 8,  color: '#22c55e', text: 'Kuat',                width: '80%'  },
        { min: 12, color: '#16a34a', text: 'Sangat kuat',         width: '100%' },
    ];

    function calcStrength(val) {
        let score = 0;
        if (val.length >= 8)                         score++;
        if (val.length >= 12)                        score++;
        if (/[A-Z]/.test(val))                       score++;
        if (/[a-z]/.test(val))                       score++;
        if (/[0-9]/.test(val))                       score++;
        if (/[^A-Za-z0-9]/.test(val))               score++;
        return score;
    }

    if (pwInput) {
        pwInput.addEventListener('input', function () {
            const score = calcStrength(this.value);
            const level = levels.slice().reverse().find(l => score >= l.min / 2) || levels[0];
            const pct   = Math.min(100, Math.round((score / 6) * 100));
            fill.style.width      = pct + '%';
            fill.style.background = pct === 0 ? '#e2e8f0' : level.color;
            label.textContent     = pct === 0 ? '' : level.text;
            label.style.color     = level.color;
        });
    }
})();
</script>
@endpush
