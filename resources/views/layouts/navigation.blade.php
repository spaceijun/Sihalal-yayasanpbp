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
                <li class="nav-item">
                    <a href="{{ url('superadmin/tickets') }}"
                        class="nav-link {{ $current_url == 'superadmin/tickets' ? 'active' : '' }}">
                        <i data-feather="send"></i>Ticket Data Entry
                    </a>
                </li>

                {{-- Human Resources --}}
                <li class="menu-title"><span data-key="t-menu">Human Resources</span></li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/recruitment-posts') }}"
                        class="nav-link {{ Request::is('superadmin/recruitment-posts*') ? 'active' : '' }}">
                        <i data-feather="briefcase"></i>Lowongan Pekerjaan
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
                    <a href="{{ url('superadmin/ktp-verifikasi') }}"
                        class="nav-link {{ Request::is('superadmin/ktp-verifikasi*') ? 'active' : '' }}">
                        <i data-feather="shield"></i>Verifikasi KTP
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/face-match') }}"
                        class="nav-link {{ Request::is('superadmin/face-match*') ? 'active' : '' }}">
                        <i data-feather="search"></i>Pencocokan Wajah
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/peta-sebaran') }}"
                        class="nav-link {{ Request::is('superadmin/peta-sebaran*') ? 'active' : '' }}">
                        <i data-feather="map"></i>Peta Sebaran
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/ranking-pendamping') }}"
                        class="nav-link {{ $current_url == 'superadmin/ranking-pendamping' ? 'active' : '' }}">
                        <i data-feather="award"></i>Ranking Pendamping
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/laporan-harian') }}"
                        class="nav-link {{ $current_url == 'superadmin/laporan-harian' ? 'active' : '' }}">
                        <i data-feather="calendar"></i>Laporan Harian
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/ticket-pendampings') }}"
                        class="nav-link {{ Request::is('superadmin/ticket-pendampings*') ? 'active' : '' }}">
                        <i data-feather="message-square"></i>Ticket Pendamping
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
                    <a href="{{ url('superadmin/penarikan-saldo') }}"
                        class="nav-link {{ $current_url == 'superadmin/penarikan-saldo' ? 'active' : '' }}">
                        <i data-feather="dollar-sign"></i>Penarikan Saldo
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
                    <a href="{{ url('superadmin/company-profile') }}"
                        class="nav-link {{ Request::is('superadmin/company-profile*') ? 'active' : '' }}">
                        <i data-feather="globe"></i>Company Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/resep-makanans') }}"
                        class="nav-link {{ $current_url == 'superadmin/resep-makanans' ? 'active' : '' }}">
                        <i data-feather="file-text"></i>Resep Makanan
                    </a>
                </li>

                {{-- WA Gateway --}}
                <li class="menu-title"><span data-key="t-menu">WA Gateway</span></li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/wa-gateway-config') }}"
                        class="nav-link {{ Request::is('superadmin/wa-gateway-config*') ? 'active' : '' }}">
                        <i data-feather="settings"></i>Pengaturan WA Gateway
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/wa-devices') }}"
                        class="nav-link {{ Request::is('superadmin/wa-devices*') ? 'active' : '' }}">
                        <i data-feather="smartphone"></i>Scan Perangkat WA
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ config('services.kawulohalal.base_url', 'http://kawalakugateway.test') }}/superadmin/dashboard"
                        target="_blank" class="nav-link">
                        <i data-feather="monitor"></i>Dashboard Gateway
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ config('services.kawulohalal.base_url', 'http://kawalakugateway.test') }}/superadmin/wa-messages"
                        target="_blank" class="nav-link">
                        <i data-feather="message-circle"></i>Riwayat Pesan WA
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
                <li class="nav-item">
                    <a href="{{ route('superadmin.profile.edit') }}"
                        class="nav-link {{ Request::is('superadmin/profile*') ? 'active' : '' }}">
                        <i data-feather="user"></i>Ubah Profil
                    </a>
                </li>
            @elseif ($role == 'admin_umum')
                {{-- Dashboard --}}
                <li class="nav-item">
                    <a href="{{ url('admin-umum') }}"
                        class="nav-link {{ Request::is('admin-umum') || Request::is('admin-umum/dashboard') ? 'active' : '' }}">
                        <i data-feather="home"></i>Dashboard
                    </a>
                </li>

                {{-- Data Entry --}}
                <li class="menu-title"><span data-key="t-menu">Data Entry</span></li>
                <li class="nav-item">
                    <a href="{{ url('admin-umum/data-entry-progress') }}"
                        class="nav-link {{ Request::is('admin-umum/data-entry-progress*') ? 'active' : '' }}">
                        <i data-feather="trending-up"></i>Data Entry Progress
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('admin-umum/pengumumen') }}"
                        class="nav-link {{ Request::is('admin-umum/pengumumen*') ? 'active' : '' }}">
                        <i data-feather="align-justify"></i>Pengumuman Data Entry
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('admin-umum/tickets') }}"
                        class="nav-link {{ Request::is('admin-umum/tickets') ? 'active' : '' }}">
                        <i data-feather="send"></i>Ticket Data Entry
                    </a>
                </li>

                {{-- Human Resources --}}
                <li class="menu-title"><span data-key="t-menu">Human Resources</span></li>
                <li class="nav-item">
                    <a href="{{ url('admin-umum/enumerators') }}"
                        class="nav-link {{ Request::is('admin-umum/enumerators*') ? 'active' : '' }}">
                        <i data-feather="list"></i>Enumerator
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('admin-umum/data-lapangans') }}"
                        class="nav-link {{ Request::is('admin-umum/data-lapangans*') ? 'active' : '' }}">
                        <i data-feather="map-pin"></i>Data Lapangan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('admin-umum/peta-sebaran') }}"
                        class="nav-link {{ Request::is('admin-umum/peta-sebaran*') ? 'active' : '' }}">
                        <i data-feather="map"></i>Peta Sebaran
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('admin-umum/ranking-pendamping') }}"
                        class="nav-link {{ Request::is('admin-umum/ranking-pendamping*') ? 'active' : '' }}">
                        <i data-feather="award"></i>Ranking Pendamping
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('admin-umum/laporan-harian') }}"
                        class="nav-link {{ Request::is('admin-umum/laporan-harian*') ? 'active' : '' }}">
                        <i data-feather="calendar"></i>Laporan Harian
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('admin-umum/ticket-pendampings') }}"
                        class="nav-link {{ Request::is('admin-umum/ticket-pendampings*') ? 'active' : '' }}">
                        <i data-feather="message-square"></i>Ticket Pendamping
                    </a>
                </li>

                {{-- Master Data --}}
                <li class="menu-title"><span data-key="t-menu">Master Data</span></li>
                <li class="nav-item">
                    <a href="{{ url('admin-umum/resep-makanans') }}"
                        class="nav-link {{ Request::is('admin-umum/resep-makanans*') ? 'active' : '' }}">
                        <i data-feather="file-text"></i>Resep Makanan
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
