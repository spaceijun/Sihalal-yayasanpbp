<li class="nav-item">
    <a href="{{ url('koordinator/dashboard') }}"
        class="nav-link {{ $current_url == 'koordinator/dashboard' ? 'active' : '' }}">
        <i data-feather="home"></i>Dashboard
    </a>
</li>
<li class="menu-title"><span data-key="t-menu">Menu Utama</span></li>
<li class="nav-item">
    <a href="{{ url('koordinator/enumerator') }}"
        class="nav-link {{ $current_url == 'koordinator/enumerator' ? 'active' : '' }}">
        <i data-feather="users"></i>Data Enumerator
    </a>
</li>
<li class="nav-item">
    <a href="{{ url('koordinator/data-lapangan') }}"
        class="nav-link {{ $current_url == 'koordinator/data-lapangan' ? 'active' : '' }}">
        <i data-feather="map"></i>Data Lapangan
    </a>
</li>
<li class="nav-item">
    <a href="{{ url('koordinator/pengumuman') }}"
        class="nav-link {{ $current_url == 'koordinator/pengumuman' ? 'active' : '' }}">
        <i data-feather="bell"></i>Pengumuman
    </a>
</li>
<li class="nav-item">
    <a href="{{ url('koordinator/tiket') }}"
        class="nav-link {{ $current_url == 'koordinator/tiket' ? 'active' : '' }}">
        <i data-feather="message-square"></i>Tiket
    </a>
</li>
