@extends('landing.layouts.app')

@section('title', 'ATM Beras Rogojampi')

@section('content')

    <main>

        {{-- Hero Section --}}
        <section id="beranda" class="hero-section">

            <div class="hero-content">

                <div class="hero-text">

                    <div class="hero-badge">
                        Program Berbagi Zakat & Amal Lazismu Rogojampi
                    </div>

                    <h1>
                        Mengalirkan Kebaikan, Membantu Sesama Melalui ATM Beras Mandiri
                    </h1>

                    <p>
                        Hadir untuk mempermudah mustahik dan jamaah memeriksa jatah serta jadwal pengambilan beras secara mandiri, sekaligus menjadi jembatan berkah bagi donatur untuk berbagi secara adil dan transparan.
                    </p>

                    <div class="hero-actions">
                        <a href="#cek-status" class="btn-hero-primary">
                            Cek Jadwal Pengambilan
                        </a>

                        <a href="#berita" class="btn-hero-secondary">
                            Lihat Berita Kegiatan
                        </a>
                    </div>

                    <div class="hero-features">

                        <div class="hero-feature-item">
                            <strong>Ambil Mandiri</strong>
                            <span>Cukup tempel kartu untuk mengambil jatah beras keluarga Anda.</span>
                        </div>

                        <div class="hero-feature-item">
                            <strong>Penyaluran Adil</strong>
                            <span>Menjamin setiap keluarga menerima jatah beras yang sama rata.</span>
                        </div>

                        <div class="hero-feature-item">
                            <strong>Aman & Nyata</strong>
                            <span>Bantuan tersalurkan langsung secara teratur dan transparan bagi semua.</span>
                        </div>

                    </div>

                </div>

                <div class="hero-visual">

                    <div class="hero-image-wrapper">
                        <img src="{{ asset('images/hero-atm-beras.png') }}" alt="ATM Beras Rogojampi - Penyaluran Zakat & Amal" class="hero-illustration">
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
                    Layanan Mustahik & Jamaah
                </span>

                <h2>
                    Cari Tahu Status & Jadwal Ambil Beras Anda
                </h2>

                <p>
                    Masukkan NIK KTP Anda untuk melihat status penerimaan, ketersediaan jatah beras keluarga Anda, serta informasi jadwal pengambilan terdekat di mesin ATM Beras.
                </p>
            </div>

            <div class="check-card">

                <div class="check-form-area">

                    <h3>
                        Cek Status Penerima
                    </h3>

                    <p>
                        Gunakan NIK KTP yang sudah terdaftar pada layanan ATM Beras Rogojampi.
                    </p>

                    <form action="{{ route('landing.check') }}" method="POST" class="check-form">
                        @csrf

                        <div class="form-group">
                            <label for="keyword">
                                NIK KTP Penerima
                            </label>

                            <input type="text" id="keyword" name="keyword" value="{{ old('keyword', $keyword ?? '') }}"
                                placeholder="Masukkan NIK KTP Anda">

                            @error('keyword')
                                <small class="form-error">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>

                        <button type="submit" class="btn-check-submit">
                            Cek Status Jatah
                        </button>
                    </form>

                    <small class="check-note">
                        Layanan pengecekan ini ditujukan untuk mempermudah mustahik memantau jatah bantuan mereka secara mandiri.
                    </small>

                </div>

                <div class="check-info-area">

                    <div class="info-box active">
                        <div class="info-icon">
                            KTP
                        </div>

                        <div>
                            <h4>
                                Verifikasi Cepat
                            </h4>

                            <p>
                                Cukup gunakan NIK KTP untuk melihat keanggotaan dan keaktifan Anda dengan mudah.
                            </p>
                        </div>
                    </div>

                    <div class="info-box">
                        <div class="info-icon">
                            GR
                        </div>

                        <div>
                            <h4>
                                Sisa Jatah Beras
                            </h4>

                            <p>
                                Pantau sisa jatah beras yang masih tersedia untuk didistribusikan ke keluarga Anda.
                            </p>
                        </div>
                    </div>

                    <div class="info-box">
                        <div class="info-icon">
                            JAM
                        </div>

                        <div>
                            <h4>
                                Jadwal Pengambilan
                            </h4>

                            <p>
                                Ketahui waktu mesin ATM dibuka agar Anda dapat mengambil beras tepat waktu tanpa antre lama.
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
                    Berita Seputar Program ATM Beras
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
                        Layanan penyaluran bantuan beras mandiri yang dikelola oleh Lazismu Rogojampi. Membantu mengalirkan zakat, infak, dan sedekah dari para donatur kepada jamaah dan mustahik secara amanah, adil, dan transparan.
                    </p>

                </div>

                {{-- Menu --}}
                <div class="footer-menu-area">

                    <h4>
                        Menu
                    </h4>

                    <ul>
                        <li>
                            <a href="#beranda">
                                Beranda
                            </a>
                        </li>

                        <li>
                            <a href="#cek-status">
                                Cek Status & Jadwal Penerima
                            </a>
                        </li>

                        <li>
                            <a href="#berita">
                                Berita
                            </a>
                        </li>

                        <li>
                            <a href="#tentang">
                                Tentang Kami
                            </a>
                        </li>
                    </ul>

                </div>

                {{-- Program Kebaikan --}}
                <div class="footer-menu-area">

                    <h4>
                        Program Kebaikan
                    </h4>

                    <ul>
                        <li>
                            <span>
                                Cek Jatah Bantuan Mandiri
                            </span>
                        </li>

                        <li>
                            <span>
                                Pembagian Beras Adil
                            </span>
                        </li>

                        <li>
                            <span>
                                Penyaluran Zakat Transparan
                            </span>
                        </li>

                        <li>
                            <span>
                                Dukungan Mustahik Rogojampi
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
                            Jl. KH. Hasyim Asy'hari No.40, Pancoran Kulon, Rogojampi, Kec. Rogojampi, Kabupaten Banyuwangi, Jawa Timur 68462
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
            $mustahikIsActive = $mustahikResult->status === 'aktif';

            // Jadwal mesin
            $scheduleText = !$mustahikIsActive
                ? 'Mustahik nonaktif, tidak dapat melakukan transaksi di ATM Beras'
                : ($pickupMachine
                    ? \Carbon\Carbon::parse($pickupMachine->jadwal_mulai)->format('d M Y H:i') .
                        ' - ' .
                        \Carbon\Carbon::parse($pickupMachine->jadwal_selesai)->format('d M Y H:i')
                    : 'Belum ada jadwal aktif');

            $machineIsActive = $pickupMachine !== null;
            $trackingIsAvailable = $mustahikIsActive && $machineIsActive;
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
                                    @if ($mustahikIsActive)
                                        Sisa Jatah:
                                        {{ number_format($mustahikResult->jatah_beras_gram) }}
                                        gram
                                    @else
                                        Status penerima tidak aktif
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="tracking-header-action">
                            <span class="tracking-status {{ $trackingIsAvailable ? 'status-active' : 'status-inactive' }}">
                                @if (!$mustahikIsActive)
                                    Mustahik Nonaktif
                                @else
                                    {{ $machineIsActive ? 'Aktif' : 'Belum Terjadwal' }}
                                @endif
                            </span>

                            <button type="button" class="tracking-close" data-bs-dismiss="modal">
                                ×
                            </button>
                        </div>

                    </div>

                    <div class="tracking-modal-body">

                        @unless ($mustahikIsActive)
                            <div class="tracking-warning-card">
                                <strong>
                                    Mustahik ini sedang nonaktif.
                                </strong>
                                <span>
                                    Data masih terdaftar, tetapi tidak dapat melakukan transaksi pengambilan beras di ATM
                                    Beras sampai statusnya diaktifkan kembali oleh admin.
                                </span>
                            </div>
                        @endunless

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
