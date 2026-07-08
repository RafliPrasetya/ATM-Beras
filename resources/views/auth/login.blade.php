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

                <form id="loginForm" action="/login" method="POST">

                    @csrf

                    <div class="mb-3 text-start">

                        <input type="text" id="username" name="username" class="form-control custom-input @error('username') is-invalid @enderror" 
                            placeholder="Username" value="{{ old('username') }}">
                        <div id="username-error" class="invalid-feedback d-none mt-2">
                            <i class="bi bi-exclamation-circle-fill me-1"></i>
                            <span class="error-text"></span>
                        </div>
                        @error('username')
                            <div class="invalid-feedback d-block mt-2 server-error">
                                <i class="bi bi-exclamation-circle-fill me-1"></i>
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="mb-4 password-wrapper text-start">

                        <input type="password" id="password" name="password" class="form-control custom-input @error('password') is-invalid @enderror"
                            placeholder="Password">

                        <i class="bi bi-eye-slash toggle-password" onclick="togglePassword()"></i>
                        <div id="password-error" class="invalid-feedback d-none mt-2">
                            <i class="bi bi-exclamation-circle-fill me-1"></i>
                            <span class="error-text"></span>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block mt-2 server-error">
                                <i class="bi bi-exclamation-circle-fill me-1"></i>
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <button type="submit" class="btn-login" id="loginBtn">

                        <span id="btnText">
                            Login
                        </span>

                        <span id="btnSpinner" style="display:none; align-items:center; justify-content:center;">
                            <svg class="spinner-svg" viewBox="0 0 50 50">
                                <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
                            </svg>
                        </span>

                        <span id="btnSuccess" style="display:none; align-items:center; justify-content:center;">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            Berhasil
                        </span>

                    </button>

                </form>
                <script>
                    document
                        .getElementById('loginForm')
                        .addEventListener('submit', function(e) {
                            e.preventDefault();

                            // Clear previous errors
                            document.querySelectorAll('.server-error').forEach(el => el.remove());
                            document.querySelectorAll('.invalid-feedback').forEach(el => {
                                el.classList.add('d-none');
                                el.classList.remove('d-block');
                            });
                            document.querySelectorAll('.custom-input').forEach(el => {
                                el.classList.remove('is-invalid');
                            });

                            const username = this.username.value.trim();
                            const password = this.password.value.trim();

                            const btn = document.getElementById('loginBtn');
                            const btnText = document.getElementById('btnText');
                            const btnSpinner = document.getElementById('btnSpinner');
                            const btnSuccess = document.getElementById('btnSuccess');

                            // Disable button and show custom loading spinner
                            btn.disabled = true;
                            btnText.style.display = 'none';
                            btnSuccess.style.display = 'none';
                            btnSpinner.style.display = 'inline-flex';

                            fetch('/login', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                                },
                                body: JSON.stringify({
                                    username: username,
                                    password: password,
                                    ajax: true
                                })
                            })
                            .then(async response => {
                                const data = await response.json();
                                if (!response.ok) {
                                    throw data;
                                }
                                return data;
                            })
                            .then(data => {
                                // Login Success: transition from spinner to success text
                                btnSpinner.style.display = 'none';
                                btnSuccess.style.display = 'inline-flex';
                                btn.classList.add('btn-success-active');

                                setTimeout(() => {
                                    window.location.href = data.redirect;
                                }, 1500);
                            })
                            .catch(error => {
                                // Reset button state
                                btn.disabled = false;
                                btnText.style.display = 'inline';
                                btnSpinner.style.display = 'none';
                                btnSuccess.style.display = 'none';

                                if (error && error.errors) {
                                    const errors = error.errors;
                                    for (const field in errors) {
                                        const input = document.getElementById(field);
                                        const errorDiv = document.getElementById(`${field}-error`);
                                        if (input && errorDiv) {
                                            input.classList.add('is-invalid');
                                            errorDiv.querySelector('.error-text').textContent = errors[field][0];
                                            errorDiv.classList.remove('d-none');
                                            errorDiv.classList.add('d-block');
                                        }
                                    }
                                } else {
                                    console.error('Login error:', error);
                                }
                            });
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
