@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4">
        <div class="mka-wrapper">

            {{-- Alert jika data rekening belum lengkap --}}
            @if (empty($dataEntry->bank_id) || empty($dataEntry->no_rekening) || empty($dataEntry->nama_rekening))
                <div class="mka-alert" id="alertRekening">
                    <div class="mka-alert__icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                    </div>
                    <div class="mka-alert__body">
                        <p class="mka-alert__title">Data rekening belum lengkap</p>
                        <p class="mka-alert__desc">Harap lengkapi informasi bank dan rekening Anda agar proses pembayaran
                            dapat
                            berjalan lancar.</p>
                    </div>
                    <button class="mka-alert__close" onclick="document.getElementById('alertRekening').style.display='none'"
                        aria-label="Tutup">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18" />
                            <line x1="6" y1="6" x2="18" y2="18" />
                        </svg>
                    </button>
                </div>
            @endif

            @include('layouts.messages')

            {{-- Profile Header --}}
            <div class="mka-profile-card">
                <div class="mka-avatar">
                    {{ strtoupper(substr($dataEntry->nama_lengkap ?? auth()->user()->name, 0, 2)) }}
                </div>
                <div class="mka-profile-info">
                    <h2 class="mka-profile-name">{{ $dataEntry->nama_lengkap ?? auth()->user()->name }}</h2>
                    <p class="mka-profile-email">{{ $dataEntry->email ?? auth()->user()->email }}</p>
                    @if (!empty($dataEntry->telephone))
                        <p class="mka-profile-phone">{{ $dataEntry->telephone }}</p>
                    @endif
                </div>
                <div class="mka-profile-badges">
                    <span class="mka-badge mka-badge--{{ strtolower($dataEntry->status ?? 'aktif') }}">
                        {{ $dataEntry->status ?? 'Aktif' }}
                    </span>
                    @if (!empty($dataEntry->entry_type))
                        <span class="mka-badge mka-badge--entry">{{ $dataEntry->entry_type }}</span>
                    @endif
                </div>
            </div>

            {{-- Informasi Akun (read-only) --}}
            <div class="mka-section">
                <div class="mka-section__header">
                    <div class="mka-section__icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                    </div>
                    <span class="mka-section__title">Informasi akun</span>
                </div>
                <div class="mka-section__body">
                    <div class="mka-info-list">
                        <div class="mka-info-row">
                            <span class="mka-info-label">Nama lengkap</span>
                            <span class="mka-info-value">{{ $dataEntry->nama_lengkap ?? '-' }}</span>
                        </div>
                        <div class="mka-info-row">
                            <span class="mka-info-label">Email</span>
                            <span class="mka-info-value">{{ $dataEntry->email ?? auth()->user()->email }}</span>
                        </div>
                        <div class="mka-info-row">
                            <span class="mka-info-label">Telephone</span>
                            <span class="mka-info-value">{{ $dataEntry->telephone ?? '-' }}</span>
                        </div>
                        <div class="mka-info-row">
                            <span class="mka-info-label">Alamat</span>
                            <span class="mka-info-value mka-info-value--wrap">{{ $dataEntry->alamat ?? '-' }}</span>
                        </div>
                        <div class="mka-info-row">
                            <span class="mka-info-label">Tipe entry</span>
                            <span class="mka-info-value">{{ $dataEntry->entry_type ?? '-' }}</span>
                        </div>
                    </div>
                </div>
                <div class="mka-section__footer">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                    Informasi ini hanya dapat diubah oleh administrator.
                </div>
            </div>

            {{-- Form Rekening --}}
            <div class="mka-section">
                <div class="mka-section__header">
                    <div class="mka-section__icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="5" width="20" height="14" rx="2" />
                            <line x1="2" y1="10" x2="22" y2="10" />
                        </svg>
                    </div>
                    <span class="mka-section__title">Informasi rekening</span>
                    @if (!empty($dataEntry->bank_id) && !empty($dataEntry->no_rekening) && !empty($dataEntry->nama_rekening))
                        <span class="mka-badge mka-badge--aktif" style="margin-left:auto;">Lengkap</span>
                    @else
                        <span class="mka-badge mka-badge--incomplete" style="margin-left:auto;">Belum lengkap</span>
                    @endif
                </div>
                <div class="mka-section__body">
                    <form method="POST" action="{{ route('data-entry.manajemen-akun.update') }}" id="formRekening">
                        @csrf

                        <div class="mka-field mka-field--full">
                            <label for="bank_id" class="mka-label">Bank</label>
                            <select class="mka-select @error('bank_id') mka-select--error @enderror" id="bank_id"
                                name="bank_id">
                                <option value="">-- Pilih bank --</option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}"
                                        {{ old('bank_id', $dataEntry->bank_id) == $bank->id ? 'selected' : '' }}>
                                        {{ $bank->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('bank_id')
                                <span class="mka-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mka-field-row">
                            <div class="mka-field">
                                <label for="no_rekening" class="mka-label">Nomor rekening</label>
                                <input type="text" class="mka-input @error('no_rekening') mka-input--error @enderror"
                                    id="no_rekening" name="no_rekening"
                                    value="{{ old('no_rekening', $dataEntry->no_rekening) }}"
                                    placeholder="Contoh: 1234567890">
                                @error('no_rekening')
                                    <span class="mka-error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mka-field">
                                <label for="nama_rekening" class="mka-label">Nama rekening</label>
                                <input type="text" class="mka-input @error('nama_rekening') mka-input--error @enderror"
                                    id="nama_rekening" name="nama_rekening"
                                    value="{{ old('nama_rekening', $dataEntry->nama_rekening) }}"
                                    placeholder="Sesuai buku tabungan">
                                @error('nama_rekening')
                                    <span class="mka-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    </form>
                </div>
                <div class="mka-section__actions">
                    <button type="button" class="mka-btn mka-btn--ghost" onclick="resetForm()">Reset</button>
                    <button type="submit" form="formRekening" class="mka-btn mka-btn--primary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                            <polyline points="17 21 17 13 7 13 7 21" />
                            <polyline points="7 3 7 8 15 8" />
                        </svg>
                        Simpan rekening
                    </button>
                </div>
            </div>

        </div>
    </div>

    <style>
        /* =====================
                   Wrapper & Layout
                   ===================== */
        .mka-wrapper {
            width: 100%;
            margin: 1.5rem 0;
            font-family: 'Figtree', 'Segoe UI', sans-serif;
        }

        /* =====================
                   Alert
                   ===================== */
        .mka-alert {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-left: 4px solid #f59e0b;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        .mka-alert__icon {
            flex-shrink: 0;
            color: #d97706;
            margin-top: 1px;
        }

        .mka-alert__body {
            flex: 1;
        }

        .mka-alert__title {
            font-size: 13px;
            font-weight: 600;
            color: #92400e;
            margin: 0 0 2px;
        }

        .mka-alert__desc {
            font-size: 12.5px;
            color: #b45309;
            margin: 0;
            line-height: 1.5;
        }

        .mka-alert__close {
            flex-shrink: 0;
            background: transparent;
            border: none;
            cursor: pointer;
            color: #b45309;
            padding: 2px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            transition: background 0.15s;
        }

        .mka-alert__close:hover {
            background: #fef3c7;
        }

        /* =====================
                   Profile Card
                   ===================== */
        .mka-profile-card {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 1.25rem 1.5rem;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            margin-bottom: 1.25rem;
        }

        .mka-avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #ede9fe;
            color: #6d28d9;
            font-size: 17px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            letter-spacing: 0.5px;
        }

        .mka-profile-info {
            flex: 1;
            min-width: 0;
        }

        .mka-profile-name {
            font-size: 15px;
            font-weight: 600;
            color: #111827;
            margin: 0 0 3px;
        }

        .mka-profile-email {
            font-size: 13px;
            color: #6b7280;
            margin: 0 0 1px;
        }

        .mka-profile-phone {
            font-size: 12.5px;
            color: #9ca3af;
            margin: 0;
        }

        .mka-profile-badges {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 6px;
        }

        /* =====================
                   Badges
                   ===================== */
        .mka-badge {
            display: inline-flex;
            align-items: center;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 999px;
            white-space: nowrap;
            letter-spacing: 0.02em;
        }

        .mka-badge--aktif {
            background: #d1fae5;
            color: #065f46;
        }

        .mka-badge--tidak-aktif {
            background: #fee2e2;
            color: #991b1b;
        }

        .mka-badge--entry {
            background: #ede9fe;
            color: #5b21b6;
        }

        .mka-badge--incomplete {
            background: #fff7ed;
            color: #c2410c;
        }

        /* =====================
                   Section Card
                   ===================== */
        .mka-section {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 1.25rem;
        }

        .mka-section__header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 20px;
            border-bottom: 1px solid #f3f4f6;
            background: #fafafa;
        }

        .mka-section__icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: #fff;
            border: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            flex-shrink: 0;
        }

        .mka-section__title {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .mka-section__body {
            padding: 1.25rem 1.5rem;
        }

        .mka-section__footer {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            background: #f9fafb;
            border-top: 1px solid #f3f4f6;
            font-size: 12px;
            color: #9ca3af;
        }

        .mka-section__actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            border-top: 1px solid #f3f4f6;
            background: #fafafa;
        }

        /* =====================
                   Info List
                   ===================== */
        .mka-info-list {
            display: flex;
            flex-direction: column;
        }

        .mka-info-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            padding: 11px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .mka-info-row:last-child {
            border-bottom: none;
        }

        .mka-info-label {
            font-size: 13px;
            color: #6b7280;
            white-space: nowrap;
            padding-top: 1px;
        }

        .mka-info-value {
            font-size: 13px;
            font-weight: 500;
            color: #111827;
            text-align: right;
        }

        .mka-info-value--wrap {
            text-align: right;
            white-space: normal;
            max-width: 380px;
            line-height: 1.5;
        }

        /* =====================
                   Form Fields
                   ===================== */
        .mka-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1;
        }

        .mka-field--full {
            margin-bottom: 1rem;
        }

        .mka-field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .mka-label {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            letter-spacing: 0.02em;
        }

        .mka-input,
        .mka-select {
            height: 40px;
            padding: 0 12px;
            font-size: 14px;
            color: #111827;
            background: #fff;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
            width: 100%;
            box-sizing: border-box;
            font-family: inherit;
            appearance: none;
            -webkit-appearance: none;
        }

        .mka-select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 36px;
        }

        .mka-input:focus,
        .mka-select:focus {
            border-color: #6d28d9;
            box-shadow: 0 0 0 3px rgba(109, 40, 217, 0.1);
        }

        .mka-input--error,
        .mka-select--error {
            border-color: #ef4444;
        }

        .mka-input--error:focus,
        .mka-select--error:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .mka-error {
            font-size: 12px;
            color: #ef4444;
            margin-top: 2px;
        }

        /* =====================
                   Buttons
                   ===================== */
        .mka-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            height: 38px;
            padding: 0 18px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.15s;
            font-family: inherit;
            letter-spacing: 0.01em;
        }

        .mka-btn--ghost {
            background: transparent;
            color: #6b7280;
            border: 1px solid #d1d5db;
        }

        .mka-btn--ghost:hover {
            background: #f9fafb;
            color: #374151;
        }

        .mka-btn--primary {
            background: #5b21b6;
            color: #fff;
            border: 1px solid #5b21b6;
        }

        .mka-btn--primary:hover {
            background: #4c1d95;
            border-color: #4c1d95;
        }

        .mka-btn--primary:active {
            transform: scale(0.98);
        }

        /* =====================
                   Dark mode
                   ===================== */
        @media (prefers-color-scheme: dark) {

            .mka-profile-card,
            .mka-section {
                background: #1f2937;
                border-color: #374151;
            }

            .mka-section__header,
            .mka-section__footer,
            .mka-section__actions {
                background: #111827;
                border-color: #374151;
            }

            .mka-profile-name,
            .mka-info-value {
                color: #f9fafb;
            }

            .mka-profile-email,
            .mka-profile-phone,
            .mka-info-label,
            .mka-section__title {
                color: #9ca3af;
            }

            .mka-section__footer {
                color: #6b7280;
            }

            .mka-info-row {
                border-color: #374151;
            }

            .mka-input,
            .mka-select {
                background: #111827;
                border-color: #374151;
                color: #f9fafb;
            }

            .mka-input:focus,
            .mka-select:focus {
                border-color: #7c3aed;
                box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.15);
            }

            .mka-label {
                color: #d1d5db;
            }

            .mka-section__icon {
                background: #1f2937;
                border-color: #374151;
            }

            .mka-avatar {
                background: #2e1065;
                color: #a78bfa;
            }

            .mka-alert {
                background: #1c1300;
                border-color: #92400e;
                border-left-color: #d97706;
            }

            .mka-alert__title {
                color: #fcd34d;
            }

            .mka-alert__desc {
                color: #fbbf24;
            }

            .mka-alert__close {
                color: #fbbf24;
            }

            .mka-alert__close:hover {
                background: #292000;
            }

            .mka-btn--ghost {
                border-color: #374151;
                color: #9ca3af;
            }

            .mka-btn--ghost:hover {
                background: #374151;
                color: #f9fafb;
            }
        }

        /* =====================
                   Responsive
                   ===================== */
        @media (max-width: 580px) {
            .mka-profile-card {
                flex-wrap: wrap;
            }

            .mka-profile-badges {
                flex-direction: row;
                margin-left: auto;
            }

            .mka-field-row {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script>
        function resetForm() {
            document.getElementById('bank_id').value = '';
            document.getElementById('no_rekening').value = '';
            document.getElementById('nama_rekening').value = '';
        }
    </script>
@endsection
