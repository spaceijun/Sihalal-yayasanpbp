<div id="scrollbar">
    <div class="container-fluid">
        <div id="two-column-menu"></div>
        @php
            $current_url = Request::path();
            $role = Auth::user()->role; // Mengambil role user yang login
        @endphp
        <ul class="navbar-nav" id="navbar-nav">
            @if ($role == 'superadmin')
                {{-- Menu Khusus Super Admin --}}
                <li class="nav-item">
                    <a href="{{ url('superadmin') }}" class="nav-link {{ $current_url == 'superadmin' ? 'active' : '' }}">
                        <i data-feather="home"></i>Dashboard
                    </a>
                </li>
                <li class="menu-title"><span data-key="t-menu">Human Resources</span></li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/koordinators') }}"
                        class="nav-link {{ $current_url == 'superadmin/koordinators' ? 'active' : '' }}">
                        <i data-feather="user"></i>Koordinator
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/enumerators') }}"
                        class="nav-link {{ $current_url == 'superadmin/enumerators' ? 'active' : '' }}">
                        <i data-feather="users"></i>Enumerator
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/data-lapangans') }}"
                        class="nav-link {{ $current_url == 'superadmin/data-lapangans' ? 'active' : '' }}">
                        <i data-feather="book"></i>Data Lapangan
                    </a>
                </li>
                <li class="menu-title"><span data-key="t-menu">Finance Management</span></li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/arus-kas') }}"
                        class="nav-link {{ $current_url == 'superadmin/arus-kas' ? 'active' : '' }}">
                        <i data-feather="dollar-sign"></i>Arus Kas
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('superadmin/cashflows') }}"
                        class="nav-link {{ $current_url == 'superadmin/cashflows' ? 'active' : '' }}">
                        <i data-feather="dollar-sign"></i>Cashflows
                    </a>
                </li>


                <li class="menu-title"><span data-key="t-menu">Settings</span></li>

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
            @elseif ($role == 'koordinator')
                @include('layouts.navigation-koordinator')
            @endif

            {{-- Menu yang tersedia untuk semua role --}}
            {{-- <li class="menu-title"><span data-key="t-menu">Umum</span></li>
          <li class="nav-item">
              <a href="{{ url('help') }}"
                  class="nav-link {{ $current_url == 'help' ? 'active' : '' }}">
                  <i data-feather="help-circle"></i>Bantuan
              </a>
          </li> --}}
        </ul>
    </div>
    <!-- Sidebar -->
</div>
