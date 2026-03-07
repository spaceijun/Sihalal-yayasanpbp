<li class="nav-item">
    <a href="{{ url('enumerator/dashboard') }}"
        class="nav-link {{ $current_url == 'enumerator/dashboard' ? 'active' : '' }}">
        <i data-feather="home"></i>Beranda
    </a>
</li>
<li class="menu-title"><span data-key="t-menu">Menu Utama</span></li>
<li class="nav-item">
    <a href="{{ url('enumerator/pengajuan') }}"
        class="nav-link {{ $current_url == 'enumerator/pengajuan' ? 'active' : '' }}">
        <i data-feather="user"></i>Tambah Pengajuan
    </a>
</li>

<li class="nav-item">
    <a href="{{ url('enumerator/riwayat-pengajuan') }}"
        class="nav-link {{ $current_url == 'enumerator/riwayat-pengajuan' ? 'active' : '' }}">
        <i data-feather="user"></i>Riwayat Pengajuan
    </a>
</li>
<li class="nav-item">
    <a href="{{ url('enumerator/cashflow') }}"
        class="nav-link {{ $current_url == 'enumerator/cashflow' ? 'active' : '' }}">
        <i data-feather="activity"></i>Cashflow
    </a>
</li>
<li class="nav-item">
    <a href="{{ url('enumerator/komplain') }}"
        class="nav-link {{ $current_url == 'enumerator/komplain' ? 'active' : '' }}">
        <i data-feather="users"></i>Komplain
    </a>
</li>
