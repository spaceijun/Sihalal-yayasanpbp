@php
    use App\Models\DataEntry;
    $navDataEntry = DataEntry::where('user_id', Auth::id())->first();
    $ktpLengkap = $navDataEntry
        && !empty($navDataEntry->nik)
        && !empty($navDataEntry->nama_lengkap_ktp)
        && !empty($navDataEntry->pendidikan_terakhir);
@endphp

<li class="nav-item">
    @if ($ktpLengkap)
        <a href="{{ url('data-entry/dashboard') }}"
            class="nav-link {{ $current_url == 'data-entry/dashboard' ? 'active' : '' }}">
            <i data-feather="home"></i>Beranda
        </a>
    @else
        <a href="#" class="nav-link nav-link--locked" onclick="showKtpWarning(event)" title="Lengkapi data KTP dulu">
            <i data-feather="home"></i>Beranda
        </a>
    @endif
</li>
<li class="menu-title"><span data-key="t-menu">Menu Utama</span></li>
<li class="nav-item">
    @if ($ktpLengkap)
        <a href="{{ url('data-entry/data-lapangan') }}"
            class="nav-link {{ $current_url == 'data-entry/data-lapangan' ? 'active' : '' }}">
            <i data-feather="user"></i>Data Lapangan
        </a>
    @else
        <a href="#" class="nav-link nav-link--locked" onclick="showKtpWarning(event)" title="Lengkapi data KTP dulu">
            <i data-feather="user"></i>Data Lapangan
        </a>
    @endif
</li>

<li class="nav-item">
    @if ($ktpLengkap)
        <a href="{{ url('data-entry/progress') }}"
            class="nav-link {{ $current_url == 'data-entry/progress' ? 'active' : '' }}">
            <i data-feather="database"></i>My Progress
        </a>
    @else
        <a href="#" class="nav-link nav-link--locked" onclick="showKtpWarning(event)" title="Lengkapi data KTP dulu">
            <i data-feather="database"></i>My Progress
        </a>
    @endif
</li>

<li class="nav-item">
    @if ($ktpLengkap)
        <a href="{{ url('data-entry/pengumumen') }}"
            class="nav-link {{ $current_url == 'data-entry/pengumumen' ? 'active' : '' }}">
            <i data-feather="file-text"></i>Pengumuman
        </a>
    @else
        <a href="#" class="nav-link nav-link--locked" onclick="showKtpWarning(event)" title="Lengkapi data KTP dulu">
            <i data-feather="file-text"></i>Pengumuman
        </a>
    @endif
</li>
<li class="nav-item">
    @if ($ktpLengkap)
        <a href="{{ url('data-entry/tickets') }}"
            class="nav-link {{ $current_url == 'data-entry/tickets' ? 'active' : '' }}">
            <i data-feather="send"></i>Tiket
        </a>
    @else
        <a href="#" class="nav-link nav-link--locked" onclick="showKtpWarning(event)" title="Lengkapi data KTP dulu">
            <i data-feather="send"></i>Tiket
        </a>
    @endif
</li>

<li class="nav-item">
    @if ($ktpLengkap)
        <a href="{{ url('data-entry/tarik-saldo') }}"
            class="nav-link {{ $current_url == 'data-entry/tarik-saldo' ? 'active' : '' }}">
            <i data-feather="dollar-sign"></i>Tarik Saldo
        </a>
    @else
        <a href="#" class="nav-link nav-link--locked" onclick="showKtpWarning(event)" title="Lengkapi data KTP dulu">
            <i data-feather="dollar-sign"></i>Tarik Saldo
        </a>
    @endif
</li>

<li class="nav-item">
    @if ($ktpLengkap)
        <a href="{{ url('resep-makanan') }}" class="nav-link {{ $current_url == 'resep-makanan' ? 'active' : '' }}">
            <i data-feather="file-text"></i>Resep Makanan
        </a>
    @else
        <a href="#" class="nav-link nav-link--locked" onclick="showKtpWarning(event)" title="Lengkapi data KTP dulu">
            <i data-feather="file-text"></i>Resep Makanan
        </a>
    @endif
</li>

{{-- Setting Akun selalu bisa diakses --}}
<li class="nav-item">
    <a href="{{ url('data-entry/manajemen-akun') }}"
        class="nav-link {{ $current_url == 'data-entry/manajemen-akun' ? 'active' : '' }}">
        <i data-feather="settings"></i>Setting Akun
        @if (!$ktpLengkap)
            <span class="badge badge-soft-danger ms-1" style="font-size:9px;vertical-align:middle;">Wajib</span>
        @endif
    </a>
</li>

@if (!$ktpLengkap)
<style>
    .nav-link--locked {
        opacity: 0.4 !important;
        cursor: not-allowed !important;
        pointer-events: none !important;
    }
    /* Override pointer-events so onclick still fires */
    .nav-link--locked {
        pointer-events: auto !important;
    }
</style>
<script>
    function showKtpWarning(e) {
        e.preventDefault();
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Data Belum Lengkap',
                text: 'Harap lengkapi data KTP dan pendidikan terakhir Anda terlebih dahulu di menu Setting Akun.',
                confirmButtonText: 'Ke Setting Akun',
                confirmButtonColor: '#5b21b6',
                showCancelButton: true,
                cancelButtonText: 'Tutup',
            }).then(function(result) {
                if (result.isConfirmed) {
                    window.location.href = '{{ url("data-entry/manajemen-akun") }}';
                }
            });
        } else {
            alert('Harap lengkapi data KTP di Setting Akun terlebih dahulu.');
            window.location.href = '{{ url("data-entry/manajemen-akun") }}';
        }
    }
</script>
@endif
