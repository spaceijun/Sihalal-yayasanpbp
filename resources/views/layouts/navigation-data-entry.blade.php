<li class="nav-item">
    <a href="{{ url('data-entry/dashboard') }}"
        class="nav-link {{ $current_url == 'data-entry/dashboard' ? 'active' : '' }}">
        <i data-feather="home"></i>Beranda
    </a>
</li>
<li class="menu-title"><span data-key="t-menu">Menu Utama</span></li>
<li class="nav-item">
    <a href="{{ url('data-entry/data-lapangan') }}"
        class="nav-link {{ $current_url == 'data-entry/data-lapangan' ? 'active' : '' }}">
        <i data-feather="user"></i>Data Lapangan
    </a>
</li>

<li class="nav-item">
    <a href="{{ url('data-entry/progress') }}"
        class="nav-link {{ $current_url == 'data-entry/progress' ? 'active' : '' }}">
        <i data-feather="database"></i>My Progress
    </a>
</li>

<li class="nav-item">
    <a href="{{ url('data-entry/pengumumen') }}"
        class="nav-link {{ $current_url == 'data-entry/pengumumen' ? 'active' : '' }}">
        <i data-feather="file-text"></i>Pengumuman
    </a>
</li>
<li class="nav-item">
    <a href="{{ url('resep-makanan') }}" class="nav-link {{ $current_url == 'resep-makanan' ? 'active' : '' }}">
        <i data-feather="file-text"></i>Resep Makanan
    </a>
</li>
<li class="nav-item">
    <a href="{{ url('data-entry/manajemen-akun') }}"
        class="nav-link {{ $current_url == 'data-entry/manajemen-akun' ? 'active' : '' }}">
        <i data-feather="settings"></i>Setting Akun
    </a>
</li>
