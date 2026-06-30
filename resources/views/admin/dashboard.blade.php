@extends('layouts.admin')

@section('content')
    <div class="welcome-card">

        <div>

            <h2>Halo, Selamat Datang!</h2>

            <p class="welcome-subtitle">
                Aplikasi Website ATM Beras Rogojampi
            </p>

        </div>

        <img src="{{ asset('/storage/images/ic_dashboard_work.svg') }}">

    </div>

    <div class="stats">

        {{-- Jumlah Mesin --}}
        <div class="stat-card">

            <h5>Jumlah Mesin</h5>

            <div class="stat-content">

                <h2>{{ $totalMesin }}</h2>

                <img src="{{ asset('storage/images/ic_atm_total.svg') }}" alt="Jumlah Mesin">

            </div>

        </div>

        {{-- Mesin Aktif --}}
        <div class="stat-card">

            <h5>Mesin Aktif</h5>

            <div class="stat-content">

                <h2>{{ $mesinAktif }}</h2>

                <img src="{{ asset('storage/images/ic_atm_on.svg') }}" alt="Mesin Aktif">

            </div>

        </div>

        {{-- Mesin Tidak Aktif --}}
        <div class="stat-card">

            <h5>Mesin Tidak Aktif</h5>

            <div class="stat-content">

                <h2>{{ $mesinNonAktif }}</h2>

                <img src="{{ asset('storage/images/ic_atm_off.svg') }}" alt="Mesin Tidak Aktif">

            </div>

        </div>

        {{-- Jumlah Mustahik --}}
        <div class="stat-card">

            <h5>Jumlah Mustahik</h5>

            <div class="stat-content">

                <h2>{{ $totalMustahik }}</h2>

                <img src="{{ asset('storage/images/ic_muzakki_total.svg') }}" alt="Jumlah Mustahik">

            </div>

        </div>

    </div>
    <script>
        setInterval(() => {

            location.reload();

        }, 30000);
    </script>
@endsection
