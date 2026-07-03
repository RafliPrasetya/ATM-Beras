<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'ATM Beras')</title>

    @vite(['resources/css/app.css', 'resources/css/dashboard.css', 'resources/js/app.js'])

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body>

    {{-- TOPBAR --}}
    @include('partials.topbar')

    <div class="app-wrapper">

        {{-- SIDEBAR --}}
        @include('partials.sidebar')

        {{-- CONTENT --}}
        <main class="main-content">

            @yield('content')

        </main>

    </div>
    {{-- @if (session('success'))
        <div class="top-0 p-3 toast-container position-fixed end-0">

            <div id="liveToast" class="border-0 toast text-bg-success" role="alert">

                <div class="d-flex">

                    <div class="toast-body">

                        <i class="bi bi-check-circle-fill"></i>
                        {{ session('success') }}

                    </div>

                    <button type="button" class="m-auto btn-close btn-close-white me-2"
                        data-bs-dismiss="toast"></button>

                </div>

            </div>

        </div>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const toastEl =
                    document.getElementById('liveToast');

                const toast =
                    new bootstrap.Toast(toastEl, {
                        delay: 3000
                    });

                toast.show();

            });
        </script>
    @endif --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });

            });
        </script>
    @endif
    <script>
        document.querySelectorAll('.has-submenu')
            .forEach(item => {

                item.addEventListener('click', function(e) {

                    e.preventDefault();

                    const submenu =
                        this.nextElementSibling;

                    submenu.classList.toggle('show');

                    this.classList.toggle('open');

                });

            });
    </script>
    <script>
        document.querySelectorAll('.menu-item[data-submenu]')
            .forEach(item => {

                item.addEventListener('click', function(e) {

                    e.preventDefault();

                    const submenuId =
                        'submenu-' +
                        this.dataset.submenu;

                    document
                        .getElementById(submenuId)
                        .classList
                        .toggle('show');

                    this
                        .classList
                        .toggle('active-parent');
                });

            });
    </script>
    <script>
        setInterval(() => {    
            fetch('{{ route('check-wa-notification') }}')
                .then(res => res.json())
                .then(data => {
                    if (data && data.status) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: data.status,
                            title: data.message,
                            showConfirmButton: false,
                            timer: 7000,
                            timerProgressBar: true
                        });
                    }
                })
                .catch(err => console.error(err));
        }, 15000);
    </script>
</body>

</html>
