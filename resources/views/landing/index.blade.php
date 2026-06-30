@extends('landing.layouts.app')

@section('title', 'ATM Beras Rogojampi')

@section('content')

    <main>

        {{-- Hero Section --}}
        <section id="beranda" class="hero-section">

            <div class="hero-content">

                <div class="hero-text">

                    <div class="hero-badge">
                        Sistem Berbasis IoT dan RFID
                    </div>

                    <h1>
                        ATM Beras Rogojampi untuk Distribusi Beras yang Lebih Terdata
                    </h1>

                    <p>
                        Website ini digunakan untuk membantu proses monitoring, pendataan penerima,
                        pengecekan jadwal, dan pengelolaan distribusi beras berbasis RFID secara lebih mudah.
                    </p>

                    <div class="hero-actions">
                        <a href="#cek-status" class="btn-hero-primary">
                            Cek Status Penerima
                        </a>

                        <a href="#berita" class="btn-hero-secondary">
                            Lihat Berita
                        </a>
                    </div>

                    <div class="hero-features">

                        <div class="hero-feature-item">
                            <strong>RFID</strong>
                            <span>Identifikasi penerima</span>
                        </div>

                        <div class="hero-feature-item">
                            <strong>IoT</strong>
                            <span>Monitoring mesin</span>
                        </div>

                        <div class="hero-feature-item">
                            <strong>Real-time</strong>
                            <span>Data lebih cepat</span>
                        </div>

                    </div>

                </div>

                <div class="hero-visual">

                    <div class="atm-card">

                        <div class="atm-top">
                            <span>ATM Beras</span>
                        </div>

                        <div class="atm-body">

                            <div class="atm-screen">
                                <span>Status Mesin</span>
                                <strong>Aktif</strong>
                            </div>

                            <div class="atm-panel">
                                <div></div>
                                <div></div>
                                <div></div>
                            </div>

                            <div class="atm-output">
                                <div class="rice-box"></div>
                            </div>

                        </div>

                        <div class="atm-footer">
                            RFID Reader
                        </div>

                    </div>

                    <div class="rice-shape rice-shape-one"></div>
                    <div class="rice-shape rice-shape-two"></div>

                </div>

            </div>

        </section>

        {{-- Cek Status & Jadwal Penerima --}}
        <section id="cek-status" class="check-section">

            <div class="check-header">
                <span class="section-label">
                    Self Tracking Mustahik
                </span>

                <h2>
                    Cek Status & Jadwal Penerima
                </h2>

                <p>
                    Masukkan NIK atau UID RFID untuk melihat data penerima, sisa jatah beras,
                    dan informasi jadwal pengambilan.
                </p>
            </div>

            <div class="check-card">

                <div class="check-form-area">

                    <h3>
                        Cari Data Penerima
                    </h3>

                    <p>
                        Gunakan NIK atau UID RFID yang sudah terdaftar pada sistem ATM Beras Rogojampi.
                    </p>

                    <form action="{{ route('landing.check') }}" method="POST" class="check-form">
                        @csrf

                        <div class="form-group">
                            <label for="keyword">
                                NIK / UID RFID
                            </label>

                            <input type="text" id="keyword" name="keyword" value="{{ old('keyword', $keyword ?? '') }}"
                                placeholder="Masukkan NIK atau UID RFID">

                            @error('keyword')
                                <small class="form-error">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>

                        <button type="submit" class="btn-check-submit">
                            Cari Data
                        </button>
                    </form>

                    <small class="check-note">
                        Data yang ditampilkan hanya digunakan untuk kebutuhan pengecekan status penerima.
                    </small>

                </div>

                <div class="check-info-area">

                    <div class="info-box active">
                        <div class="info-icon">
                            ID
                        </div>

                        <div>
                            <h4>
                                Identifikasi Penerima
                            </h4>

                            <p>
                                Sistem membaca data penerima berdasarkan NIK atau UID RFID.
                            </p>
                        </div>
                    </div>

                    <div class="info-box">
                        <div class="info-icon">
                            KG
                        </div>

                        <div>
                            <h4>
                                Sisa Jatah Beras
                            </h4>

                            <p>
                                Penerima dapat melihat informasi jatah beras yang tersedia.
                            </p>
                        </div>
                    </div>

                    <div class="info-box">
                        <div class="info-icon">
                            JD
                        </div>

                        <div>
                            <h4>
                                Jadwal Pengambilan
                            </h4>

                            <p>
                                Jadwal membantu penerima mengetahui waktu pengambilan beras.
                            </p>
                        </div>
                    </div>

                </div>

            </div>

        </section>

        {{-- Berita --}}
        <section id="berita" class="news-section">

            <div class="news-header">
                <span class="section-label">
                    Berita dan Informasi
                </span>

                <h2>
                    Berita Seputar ATM Beras
                </h2>

                <p>
                    Informasi terbaru terkait pengembangan sistem, distribusi beras, dan layanan ATM Beras Rogojampi.
                </p>
            </div>

            <div class="news-grid">

                @forelse (($landingNews ?? collect()) as $news)
                    <article class="news-card">

                        <div class="news-media">
                            @if ($news->gambar)
                                <img src="{{ asset('storage/' . $news->gambar) }}" alt="{{ $news->judul }}">
                            @else
                                <div class="news-placeholder">
                                    <span>Tidak ada gambar</span>
                                </div>
                            @endif
                        </div>

                        <div class="news-content">

                            <div class="news-meta">
                                <span class="news-badge">
                                    Berita
                                </span>

                                <small>
                                    {{ $news->created_at?->format('d M Y') ?? '-' }}
                                </small>
                            </div>

                            <h3 class="news-title">
                                {{ $news->judul }}
                            </h3>

                            <p class="news-excerpt">
                                {{ \Illuminate\Support\Str::limit(strip_tags($news->konten), 140) }}
                            </p>

                            <a href="{{ route('landing.berita.show', $news->id) }}" class="news-link">
                                Baca Selengkapnya
                            </a>

                        </div>

                    </article>
                @empty
                    <div class="news-empty">
                        Belum ada berita yang tersedia.
                    </div>
                @endforelse

            </div>

        </section>

        {{-- Tentang Kami / Footer --}}
        <section id="tentang" class="about-footer-section">

            <div class="about-footer-content">

                {{-- Brand --}}
                <div class="footer-brand-area">

                    <a href="{{ route('landing.index') }}" class="footer-brand footer-brand-logo-only">
                        <div class="footer-logo-image">
                            <img src="{{ asset('storage/images/Lazismu.png') }}" alt="Logo Lazismu Rogojampi">
                        </div>
                    </a>

                    <p class="footer-description">
                        Sistem ATM Beras berbasis IoT dan RFID yang dikembangkan untuk membantu
                        monitoring distribusi beras, pendataan mustahik, dan pencatatan riwayat
                        pengambilan secara lebih terstruktur.
                    </p>

                </div>

                {{-- Menu --}}
                <div class="footer-menu-area">

                    <h4>
                        Menu
                    </h4>

                    <ul>
                        <li>
                            <a href="Beranda">
                                Beranda
                            </a>
                        </li>

                        <li>
                            <a href="Cek-status">
                                Cek Status & Jadwal Penerima
                            </a>
                        </li>

                        <li>
                            <a href="Berita">
                                Berita
                            </a>
                        </li>

                        <li>
                            <a href="Tentang">
                                Tentang Kami
                            </a>
                        </li>
                    </ul>

                </div>

                {{-- Layanan --}}
                <div class="footer-menu-area">

                    <h4>
                        Layanan Sistem
                    </h4>

                    <ul>
                        <li>
                            <span>
                                Self Tracking Mustahik
                            </span>
                        </li>

                        <li>
                            <span>
                                Monitoring Mesin ATM Beras
                            </span>
                        </li>

                        <li>
                            <span>
                                Riwayat Pengambilan Beras
                            </span>
                        </li>

                        <li>
                            <span>
                                Manajemen Data Penerima
                            </span>
                        </li>
                    </ul>

                </div>

                {{-- Kontak --}}
                <div class="footer-contact-area">

                    <h4>
                        Kontak
                    </h4>

                    <div class="footer-contact-item">
                        <div class="footer-contact-icon">
                            <i class="bi bi-building"></i>
                        </div>

                        <span>
                            Kantor Layanan ATM Beras Rogojampi
                        </span>
                    </div>

                    <div class="footer-contact-item">
                        <div class="footer-contact-icon">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>

                        <span>
                            Rogojampi, Banyuwangi, Jawa Timur
                        </span>
                    </div>

                    <div class="footer-contact-item">
                        <div class="footer-contact-icon">
                            <i class="bi bi-whatsapp"></i>
                        </div>

                        <span>
                            +62 812-1234-5678
                        </span>
                    </div>

                </div>

            </div>

            <div class="footer-bottom">
                <p>
                    © 2026 ATM Beras Rogojampi. All Rights Reserved.
                </p>

                <span>
                    Sistem Monitoring ATM Beras Berbasis IoT
                </span>
            </div>

        </section>

    </main>

    {{-- Modal Hasil Tracking --}}
    @if ($mustahikResult ?? false)
        @php
            // Data ATM
            $machineCode = $pickupMachine?->machine_code ?? '-';
            $atmLocation = $pickupMachine?->lokasi_penempatan ?? 'Lokasi ATM belum tersedia';
            $atmVillageName = $pickupMachine?->village?->name ?? '-';
            $atmDistrictName = $pickupMachine?->village?->district?->name ?? '-';

            // Data mustahik
            $mustahikAddress = $mustahikResult->alamat ?? '-';
            $mustahikVillageName = $mustahikResult->village?->name ?? '-';
            $mustahikDistrictName = $mustahikResult->village?->district?->name ?? '-';

            // Jadwal mesin
            $scheduleText = $pickupMachine
                ? \Carbon\Carbon::parse($pickupMachine->jadwal_mulai)->format('d M Y H:i') .
                    ' - ' .
                    \Carbon\Carbon::parse($pickupMachine->jadwal_selesai)->format('d M Y H:i')
                : 'Belum ada jadwal aktif';

            $machineIsActive = $pickupMachine !== null;
        @endphp

        <div class="modal fade tracking-modal" id="trackingResultModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content tracking-modal-content">

                    <div class="tracking-modal-header">

                        <div class="tracking-user">
                            <div class="tracking-avatar">
                                {{ strtoupper(substr($mustahikResult->nama, 0, 1)) }}
                            </div>

                            <div>
                                <h3>
                                    {{ $mustahikResult->nama }}
                                </h3>

                                <p>
                                    Sisa Jatah:
                                    {{ number_format($mustahikResult->jatah_beras_gram) }}
                                    gram
                                </p>
                            </div>
                        </div>

                        <div class="tracking-header-action">
                            <span class="tracking-status {{ $machineIsActive ? 'status-active' : 'status-inactive' }}">
                                {{ $machineIsActive ? 'Aktif' : 'Belum Terjadwal' }}
                            </span>

                            <button type="button" class="tracking-close" data-bs-dismiss="modal">
                                ×
                            </button>
                        </div>

                    </div>

                    <div class="tracking-modal-body">

                        <div class="tracking-section">
                            <h5>
                                Alamat Mustahik
                            </h5>

                            <div class="tracking-info-card">
                                <div class="tracking-info-icon">
                                    🏠
                                </div>

                                <div>
                                    <strong>
                                        {{ $mustahikAddress }}
                                    </strong>

                                    <span>
                                        {{ $mustahikVillageName }}, {{ $mustahikDistrictName }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="tracking-section">
                            <h5>
                                Lokasi ATM Pengambilan
                            </h5>

                            <div class="tracking-info-card">
                                <div class="tracking-info-icon">
                                    📍
                                </div>

                                <div>
                                    <strong>
                                        {{ $atmLocation }}
                                    </strong>

                                    <span>
                                        {{ $atmVillageName }}, {{ $atmDistrictName }}
                                    </span>

                                    <span>
                                        Kode Mesin: {{ $machineCode }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="tracking-section">
                            <h5>
                                Jadwal Pengambilan
                            </h5>

                            <div class="tracking-info-card">
                                <div class="tracking-info-icon">
                                    📅
                                </div>

                                <div>
                                    <strong>
                                        {{ $scheduleText }}
                                    </strong>

                                    <span>
                                        Mesin: {{ $machineCode }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="tracking-section">
                            <h5>
                                Riwayat Pengambilan
                            </h5>

                            <div class="tracking-table-wrapper">
                                <table class="tracking-table">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Mesin</th>
                                            <th>Jumlah Ambil</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse (($recentTransactions ?? collect()) as $transaction)
                                            <tr>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($transaction->tanggal_pengambilan)->format('d M Y') }}
                                                </td>

                                                <td>
                                                    {{ $transaction->machine?->machine_code ?? '-' }}
                                                </td>

                                                <td>
                                                    {{ number_format($transaction->jumlah_ambil_gram) }}
                                                    gram
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">
                                                    Belum ada riwayat pengambilan
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tracking-footer">
                            <button type="button" class="btn-tracking-close" data-bs-dismiss="modal">
                                Tutup
                            </button>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    @endif

    {{-- Modal Data Tidak Ditemukan --}}
    @if ($notFound ?? false)
        <div class="modal fade tracking-modal" id="trackingNotFoundModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content tracking-modal-content not-found-modal">

                    <div class="text-center tracking-modal-body">

                        <div class="not-found-icon">
                            !
                        </div>

                        <h3>
                            Data Tidak Ditemukan
                        </h3>

                        <p>
                            Data penerima dengan NIK atau UID RFID
                            <strong>{{ $keyword ?? '' }}</strong>
                            tidak ditemukan.
                        </p>

                        <button type="button" class="btn-tracking-close" data-bs-dismiss="modal">
                            Tutup
                        </button>

                    </div>

                </div>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if ($mustahikResult ?? false)
                const resultModalElement = document.getElementById('trackingResultModal');

                if (resultModalElement) {
                    const resultModal = new bootstrap.Modal(resultModalElement);
                    resultModal.show();
                }
            @endif

            @if ($notFound ?? false)
                const notFoundModalElement = document.getElementById('trackingNotFoundModal');

                if (notFoundModalElement) {
                    const notFoundModal = new bootstrap.Modal(notFoundModalElement);
                    notFoundModal.show();
                }
            @endif
        });
    </script>
@endpush
