<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme no-print">
    <div class="app-brand demo ">
        <a href="{{ route('index') }}" class="app-brand-link">
            <span class="app-brand-logo demo" style="margin-left: -10px">
                <img src="{{asset('assets/img/CIS.png')}}" height="44" >
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto bg-dark">
            <i class="bx bx-transfer-alt bx-sm align-middle"></i>
        </a>
    </div>
    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1">
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Tes</span>
        </li>
        <li class="menu-item {{ request()->segment(1) == 'dashboard' ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Dashboards">Halaman Utama</div>
            </a>
        </li>
        @can('read konfigurasi')
        <li class="menu-item {{ request()->segment(1) == 'konfigurasi' ? 'open active' : '' }}">
            <a href="#" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Account">Konfigurasi</div>
            </a>
            <ul class="menu-sub">
                @can('read role')
                <li class="menu-item {{ request()->segment(2) == 'roles' ? 'active' : '' }}">
                    <a href="{{ route('roles.index') }}" class="menu-link">
                        <div>Roles</div>
                    </a>
                </li>
                @endcan
                @can('read apa')
                <li class="menu-item ">
                    <a href="" class="menu-link">
                        <div>Apa</div>
                    </a>
                </li>
                @endcan
                <li class="menu-item ">
                    <a href="" class="menu-link menu-toggle">
                        <div>tampil menu</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item ">
                            <a href="" class="menu-link">
                                <div>tampil sub-menu3</div>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>
        @endcan
    </ul>
</aside>
<!-- / Menu -->
