<li class="nav-item">
    <a href="{{ url('koordinator/dashboard') }}"
        class="nav-link {{ $current_url == 'koordinator/dashboard' ? 'active' : '' }}">
        <i data-feather="home"></i>Beranda
    </a>
</li>
<li class="menu-title"><span data-key="t-menu">Menu Utama</span></li>
<li class="nav-item">
    <a href="{{ url('koordinator/data-pendamping') }}"
        class="nav-link {{ $current_url == 'koordinator/data-pendamping' ? 'active' : '' }}">
        <i data-feather="user"></i>Data Pendamping
    </a>
</li>

<li class="nav-item">
    <a href="{{ url('koordinator/data-lapangan') }}"
        class="nav-link {{ $current_url == 'koordinator/data-lapangan' ? 'active' : '' }}">
        <i data-feather="user"></i>Data Lapangan
    </a>
</li>
<li class="nav-item">
    <a href="{{ url('koordinator/cashflow') }}"
        class="nav-link {{ $current_url == 'koordinator/cashflow' ? 'active' : '' }}">
        <i data-feather="activity"></i>Cashflow
    </a>
</li>
