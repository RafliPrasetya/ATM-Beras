<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ATM Beras Rogojampi')</title>

    {{-- <link rel="icon" type="image/png" href="{{ asset('favicon1.png') }}"> --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    @vite(['resources/css/landing.css', 'resources/js/landing.js'])
</head>

<body>

    <div class="landing-wrapper">

        {{-- Navbar --}}
        <nav class="navbar navbar-expand-lg landing-navbar">
            <div class="p-0 container-fluid">

                <a class="navbar-brand navbar-logo-brand" href="{{ url('/') }}">
                    <img src="{{ asset('storage/images/poli_lazismu.png') }}" alt="Logo Politeknik dan Lazismu"
                        class="navbar-logo-img">
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#landingNavbar"
                    aria-controls="landingNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="landingNavbar">
                    <ul class="mx-auto mb-2 navbar-nav mb-lg-0">

                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/#beranda') }}">
                                Beranda
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/#cek-status') }}">
                                Cek Status & Jadwal Penerima
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/#berita') }}">
                                Berita
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/#tentang') }}">
                                Tentang Kami
                            </a>
                        </li>

                    </ul>

                    <a href="{{ url('/#cek-status') }}" class="btn-navbar-cta">
                        Cek Jadwal
                    </a>
                </div>

            </div>
        </nav>

        @yield('content')

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')

</body>

</html>
