<div class="topbar">

    <!-- LEFT -->
    <div class="topbar-left">

        <img src="{{ asset('/poli_lazismu.png') }}" class="logo-lazismu" alt="Lazismu">

        <button id="toggleSidebar" class="hamburger-btn">

            <i class="bi bi-list"></i>

        </button>

    </div>

    <!-- RIGHT -->
    <div class="topbar-right">

        <div class="dropdown">

            <button class="setting-btn" data-bs-toggle="dropdown">

                <i class="bi bi-gear fs-4"></i>

            </button>

            <div class="dropdown-menu dropdown-menu-end admin-menu">

                <div class="admin-header">
                    <h6>{{ session('admin_name') }}</h6>
                    <small>Administrator</small>
                </div>

                <hr>

                <a href="{{ route('logout') }}" class="dropdown-item logout-btn">

                    <i class="bi bi-box-arrow-right"></i>
                    Logout

                </a>

            </div>

        </div>

    </div>

</div>
