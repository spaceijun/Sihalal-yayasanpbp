<div id="scrollbar">
    <div class="container-fluid">
        <div id="two-column-menu"></div>
        @php
            $current_url = Request::path();
            $role = Auth::user()->role;
        @endphp
        <ul class="navbar-nav" id="navbar-nav">
            @if ($role == 'superadmin')
                {{-- Dashboard --}}
                <li class="nav-item">
                    <a href="{{ url('superadmin') }}" class="nav-link {{ $current_url == 'superadmin' ? 'active' : '' }}">
                        <i data-feather="home"></i>Dashboard
                    </a>
                </li>

                {{-- Management --}}
                <li class="menu-title"><span data-key="t-menu">Management</span></li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/verifikators') }}"
                        class="nav-link {{ $current_url == 'superadmin/verifikators' ? 'active' : '' }}">
                        <i data-feather="check-circle"></i>Verifikator
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/koordinators') }}"
                        class="nav-link {{ $current_url == 'superadmin/koordinators' ? 'active' : '' }}">
                        <i data-feather="user"></i>Koordinator
                    </a>
                </li>

                {{-- Data Entry --}}
                <li class="menu-title"><span data-key="t-menu">Data Entry</span></li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/data-entries') }}"
                        class="nav-link {{ $current_url == 'superadmin/data-entries' ? 'active' : '' }}">
                        <i data-feather="file-text"></i>Data Entry
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/data-entry-progress') }}"
                        class="nav-link {{ $current_url == 'superadmin/data-entry-progress' ? 'active' : '' }}">
                        <i data-feather="trending-up"></i>Data Entry Progress
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/pengumumen') }}"
                        class="nav-link {{ $current_url == 'superadmin/pengumumen' ? 'active' : '' }}">
                        <i data-feather="align-justify"></i>Pengumuman Data Entry
                    </a>
                </li>

                {{-- Human Resources --}}
                <li class="menu-title"><span data-key="t-menu">Human Resources</span></li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/recruitments') }}"
                        class="nav-link {{ $current_url == 'superadmin/recruitments' ? 'active' : '' }}">
                        <i data-feather="user-plus"></i>Recruitment
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/enumerators') }}"
                        class="nav-link {{ $current_url == 'superadmin/enumerators' ? 'active' : '' }}">
                        <i data-feather="list"></i>Enumerator
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/data-lapangans') }}"
                        class="nav-link {{ $current_url == 'superadmin/data-lapangans' ? 'active' : '' }}">
                        <i data-feather="map-pin"></i>Data Lapangan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/laporan-harian') }}"
                        class="nav-link {{ $current_url == 'superadmin/laporan-harian' ? 'active' : '' }}">
                        <i data-feather="calendar"></i>Laporan Harian
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/tickets') }}"
                        class="nav-link {{ $current_url == 'superadmin/tickets' ? 'active' : '' }}">
                        <i data-feather="send"></i>Tickets
                    </a>
                </li>

                {{-- Finance Management --}}
                <li class="menu-title"><span data-key="t-menu">Finance Management</span></li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/penagihan') }}"
                        class="nav-link {{ $current_url == 'superadmin/penagihan' ? 'active' : '' }}">
                        <i data-feather="database"></i>Tagihan Data Entry
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/arus-kas') }}"
                        class="nav-link {{ $current_url == 'superadmin/arus-kas' ? 'active' : '' }}">
                        <i data-feather="dollar-sign"></i>Cashflows
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/cashflows') }}"
                        class="nav-link {{ $current_url == 'superadmin/cashflows' ? 'active' : '' }}">
                        <i data-feather="dollar-sign"></i>Report Cashflows
                    </a>
                </li>

                {{-- Master Data --}}
                <li class="menu-title"><span data-key="t-menu">Master Data</span></li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/resep-makanans') }}"
                        class="nav-link {{ $current_url == 'superadmin/resep-makanans' ? 'active' : '' }}">
                        <i data-feather="file-text"></i>Resep Makanan
                    </a>
                </li>

                {{-- Settings --}}
                <li class="menu-title"><span data-key="t-menu">Settings</span></li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/server-info') }}"
                        class="nav-link {{ $current_url == 'superadmin/server-info' ? 'active' : '' }}">
                        <i data-feather="cpu"></i>Server Manage
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/cpanel/emails') }}"
                        class="nav-link {{ $current_url == 'superadmin/cpanel/emails' ? 'active' : '' }}">
                        <i data-feather="mail"></i>Email Manage
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/diagnostic') }}"
                        class="nav-link {{ $current_url == 'superadmin/diagnostic' ? 'active' : '' }}">
                        <i data-feather="alert-triangle"></i>Troubleshooting
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/settings') }}"
                        class="nav-link {{ $current_url == 'superadmin/settings' ? 'active' : '' }}">
                        <i data-feather="settings"></i>Settings Website
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/users') }}"
                        class="nav-link {{ $current_url == 'superadmin/users' ? 'active' : '' }}">
                        <i data-feather="users"></i>Manajemen Pengguna
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/app-versions') }}"
                        class="nav-link {{ $current_url == 'superadmin/app-versions' ? 'active' : '' }}">
                        <i data-feather="smartphone"></i>App Versions
                    </a>
                </li>
            @elseif ($role == 'koordinator')
                @include('layouts.navigation-koordinator')
            @elseif ($role == 'data_entry')
                @include('layouts.navigation-data-entry')
            @elseif ($role == 'enumerator')
                @include('layouts.navigation-enumerator')
            @endif
        </ul>
    </div>
</div>
