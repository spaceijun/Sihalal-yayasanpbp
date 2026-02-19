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
    <a href="javascript:void(0)" class="nav-link disabled"
        style="pointer-events: none; opacity: 0.5; cursor: not-allowed;">
        <i data-feather="activity"></i> Cashflow
    </a>
</li>
