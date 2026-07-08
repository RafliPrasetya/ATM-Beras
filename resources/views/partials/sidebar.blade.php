<div class="sidebar">

    <div class="menu-section">

        <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active-parent' : '' }}">

            <span class="menu-label">
                <i class="bi bi-speedometer2"></i>
                <span class="menu-text">
                    Dashboard
                </span>
            </span>

        </a>

        <hr>

        <a href="#" class="menu-item {{ request()->routeIs('mesin.*') ? 'active-parent' : '' }}"
            data-submenu="mesin">

            <span class="menu-label">
                <i class="bi bi-pc-display"></i>
                <span class="menu-text">Mesin</span>
            </span>

            <i class="bi bi-chevron-down menu-arrow"></i>

        </a>

        <div class="submenu {{ request()->routeIs('mesin.*') ? 'show' : '' }}" id="submenu-mesin">

            <a href="{{ route('mesin.index') }}"
                class="{{ request()->routeIs('mesin.index') ? 'submenu-active' : '' }}">
                Daftar Mesin
            </a>

        </div>

        <hr>

        <a href="#" class="menu-item {{ request()->routeIs('mustahik.*') ? 'active-parent' : '' }}"
            data-submenu="mustahik">

            <span class="menu-label">
                <i class="bi bi-people"></i>
                <span class="menu-text">Mustahik</span>
            </span>

            <i class="bi bi-chevron-down menu-arrow"></i>

        </a>

        <div class="submenu {{ request()->routeIs('mustahik.*') ? 'show' : '' }}" id="submenu-mustahik">

            <a href="{{ route('mustahik.index') }}"
                class="{{ request()->routeIs('mustahik.index') || request()->routeIs('mustahik.active') ? 'submenu-active' : '' }}">
                Daftar Mustahik
            </a>

            <a href="{{ route('mustahik.riwayat') }}"
                class="{{ request()->routeIs('mustahik.riwayat') ? 'submenu-active' : '' }}">
                Riwayat Pengambilan
            </a>

        </div>

        <hr>

        <a href="#" class="menu-item {{ request()->routeIs('berita.*') ? 'active-parent' : '' }}"
            data-submenu="berita">

            <span class="menu-label">

                <i class="bi bi-newspaper"></i>

                <span class="menu-text">
                    Konten
                </span>

            </span>

            <i class="bi bi-chevron-down menu-arrow"></i>

        </a>

        <div class="submenu {{ request()->routeIs('berita.*') ? 'show' : '' }}" id="submenu-berita">

            <a href="{{ route('berita.index') }}"
                class="{{ request()->routeIs('berita.index') ? 'submenu-active' : '' }}">

                Kelola Berita

            </a>

            {{-- <a href="#">
                Informasi
            </a>

            <a href="#">
                Kegiatan
            </a> --}}

        </div>

        <hr>

        <a href="#" class="menu-item {{ request()->routeIs('admin-management.*') ? 'active-parent' : '' }}"
            data-submenu="admin">

            <span class="menu-label">
                <i class="bi bi-person-gear"></i>
                <span class="menu-text">Admin</span>
            </span>

            <i class="bi bi-chevron-down menu-arrow"></i>

        </a>

        <div class="submenu {{ request()->routeIs('admin-management.*') ? 'show' : '' }}" id="submenu-admin">

            <a href="{{ route('admin-management.index') }}"
                class="{{ request()->routeIs('admin-management.index') ? 'submenu-active' : '' }}">
                Kelola Admin
            </a>

        </div>

    </div>

</div>
