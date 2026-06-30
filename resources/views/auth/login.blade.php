@extends('layouts.app')

@section('content')
    <div class="login-wrapper">

        {{-- LEFT SIDE --}}
        <div class="login-left">

            <div class="branding">

                <img src="{{ asset('storage/images/atm.png') }}" class="atm-icon" alt="ATM">

                <h1>
                    ATM Beras <br>
                    Poliwangi
                </h1>

                <div class="partner-logo">
                    <img src="{{ asset('storage/images/poli_lazismu.png') }}">
                </div>

            </div>

        </div>

        {{-- RIGHT SIDE --}}
        <div class="login-right">

            <div class="login-card">

                <h2>Selamat Datang</h2>

                <hr>

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <form id="loginForm" action="/login" method="POST">

                    @csrf

                    <div class="mb-3">

                        <input type="text" name="username" class="form-control custom-input" placeholder="Username"
                            required>

                    </div>

                    <div class="mb-4 password-wrapper">

                        <input type="password" id="password" name="password" class="form-control custom-input"
                            placeholder="Password" required>

                        <i class="bi bi-eye-slash toggle-password" onclick="togglePassword()"></i>

                    </div>

                    <button type="submit" class="btn-login" id="loginBtn">

                        <span id="btnText">
                            Login
                        </span>

                        <span id="btnLoading" style="display:none;">
                            <span class="spinner-border spinner-border-sm me-2" role="status">
                            </span>
                            Memproses Login...
                        </span>

                        <span id="btnSuccess" style="display:none;">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            Berhasil
                        </span>

                    </button>

                </form>
                <script>
                    document
                        .getElementById('loginForm')
                        .addEventListener('submit', function() {

                            const btn = document.getElementById('loginBtn');

                            btn.disabled = true;

                            document.getElementById('btnText').style.display = 'none';

                            document.getElementById('btnLoading').style.display = 'inline-flex';

                        });
                </script>

            </div>

        </div>

    </div>

    <script>
        function togglePassword() {

            const input = document.getElementById('password');

            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        }
    </script>
@endsection
