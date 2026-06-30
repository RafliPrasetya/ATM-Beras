@extends('layouts.admin')

@section('content')
    <style>
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
        }

        .page-title {
            margin: 0;
            font-weight: 700;
        }

        .search-wrapper {
            position: relative;
            width: 350px;
        }

        .search-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }

        .search-input {
            width: 100%;
            height: 45px;
            border: none;
            border-radius: 12px;
            background: #f3f4f6;
            padding-left: 42px;
            padding-right: 15px;
            outline: none;
        }

        .search-input:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .15);
        }

        .modal-riwayat table th {
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        .modal-riwayat table td {
            vertical-align: middle;
        }

        .modal-riwayat .table {
            margin-bottom: 0;
        }

        /* =========================
                                                                                                                        MODAL RIWAYAT MUSTAHIK
                                                                                                                       ========================= */

        .modal-riwayat .modal-content {
            border: none;
            border-radius: 20px;
            overflow: hidden;
        }

        .modal-riwayat .modal-body {
            max-height: 80vh;
            overflow-y: auto;
            background: #f8fafc;
        }

        .riwayat-wrapper {
            background: #fff;
            border: 1px solid #dbe3ea;
            border-radius: 20px;
            padding: 25px;
        }

        .riwayat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 25px;
        }

        .riwayat-filter {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .riwayat-filter .btn {
            min-width: 90px;
            border-radius: 12px;
        }

        .info-card {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 15px;
            padding: 20px;
            height: 100%;
        }

        .info-card small {
            color: #6c757d;
            display: block;
            margin-bottom: 3px;
        }

        .info-card strong {
            color: #000000;
        }

        .riwayat-table {
            border-radius: 15px;
            overflow: hidden;
        }

        .riwayat-table thead {
            background: #f1f5f9;
        }

        .riwayat-table thead th {
            font-weight: 600;
            border: none;
            padding: 14px;
        }

        .riwayat-table tbody td {
            padding: 14px;
            vertical-align: middle;
        }

        .riwayat-table tbody tr:hover {
            background: #f8fafc;
        }

        .badge-jumlah {
            background: #dcfce7;
            color: #166534;
            padding: 8px 14px;
            border-radius: 30px;
            font-weight: 600;
        }

        .download-btn {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }

        .summary-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 15px;
            padding: 15px;
        }

        .summary-box h4 {
            margin: 0;
            color: #2563eb;
            font-weight: 700;
        }

        .filter-btn {
            transition: all .3s ease;
        }

        .active-filter {
            background: #F08519 !important;
            border-color: #F08519 !important;
            color: #fff !important;
        }

        .filter-btn:hover {
            background: #F08519 !important;
            border-color: #F08519 !important;
            color: white !important;
        }

        .filter-btn.active-filter {
            background: #F08519 !important;
            border-color: #F08519 !important;
            color: white !important;
        }

        .filter-active {
            background: #F08519 !important;
            border-color: #F08519 !important;
            color: #fff !important;
        }

        .pagination {
            margin-bottom: 0;
        }

        .pagination .page-link {
            border: none;
            color: #555;
        }

        .pagination .active .page-link {
            background: #F08519;
            border-color: #F08519;
        }

        .btn-tambah {
            background: #F08519;
            border-color: #F08519;
            color: #fff;
            font-weight: 500;
            transition: all .2s ease;
        }

        .btn-tambah:hover {
            background: #d9720f;
            border-color: #d9720f;
            color: #fff;
        }

        /* .btn-tambah:focus,
                .btn-tambah:active {
                    background: #c9670d !important;
                    border-color: #c9670d !important;
                    box-shadow: 0 0 0 0.2rem rgba(240, 133, 25, 0.25);
                } */

        /* .header-action {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            display: flex;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            gap: 12px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                        }

                                                                                                                                                                                                                                                                                                                                                                                                                                                                        .header-action .btn {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            min-width: 55px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            height: 45px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            border-radius: 10px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                        } */
    </style>
    <div class="page-card">

        <div class="page-header">

            <h4 class="page-title">
                Daftar Mustahik
            </h4>

            <div class="gap-2 d-flex align-items-center">

                <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalFilterMustahik">

                    <i class="bi bi-funnel"></i>

                    Filter Data

                </button>

                <span id="filterCountBadge" class="badge bg-warning text-dark d-none">

                    0 Data

                </span>

            </div>

            <div class="header-action">

                <a href="{{ route('mustahik.index') }}" class="btn btn-primary">

                    <i class="bi bi-arrow-clockwise"></i>

                </a>

                <button class="btn btn-tambah" data-bs-toggle="modal" data-bs-target="#modalTambahMustahik">

                    Tambah

                </button>

                <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalLaporanPengambilan">

                    <i class="bi bi-download"></i>

                </button>

            </div>

        </div>

        <div class="table-responsive">

            <table class="table mesin-table">

                <thead>

                    <tr>

                        <th>Nama Mustahik</th>

                        <th>ID Penerima</th>

                        <th>NIK</th>

                        <th>No HP</th>

                        <th>Alamat Lengkap</th>

                        <th>Kecamatan</th>

                        <th>Desa</th>

                        <th>Jatah Beras (Gram)</th>

                        <th width="100">Aksi</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($mustahiks as $mustahik)
                        <tr class="mustahik-row" data-nama="{{ strtolower($mustahik->nama) }}"
                            data-alamat="{{ strtolower($mustahik->alamat) }}"
                            data-kecamatan="{{ strtolower($mustahik->village?->district?->name) }}"
                            data-desa="{{ strtolower($mustahik->village?->name) }}"
                            data-jatah="{{ $mustahik->jatah_beras_gram }}">

                            <td>{{ $mustahik->nama }}</td>

                            <td>{{ $mustahik->rfid_uid }}</td>

                            <td>{{ $mustahik->nik }}</td>

                            <td>{{ $mustahik->no_hp }}</td>

                            <td>{{ $mustahik->alamat }}</td>

                            <td>
                                {{ $mustahik->village?->district?->name }}
                            </td>

                            <td>
                                {{ $mustahik->village?->name }}
                            </td>

                            <td>
                                {{ number_format($mustahik->jatah_beras_gram) }}
                            </td>

                            <td>

                                <button class="btn-action" data-bs-toggle="modal"
                                    data-bs-target="#modalMustahik{{ $mustahik->id }}">

                                    <i class="bi bi-three-dots"></i>

                                </button>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="9" class="py-4 text-center">

                                Belum ada data mustahik

                            </td>

                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3 d-flex justify-content-end">

                {{ $mustahiks->links() }}

            </div>
        </div>
        <div class="modal fade" id="modalLaporanPengambilan" tabindex="-1">

            <div class="modal-dialog modal-xl modal-dialog-centered">

                <div class="border-0 shadow modal-content">

                    <div class="p-4 modal-body">

                        <div
                            style="
                        border:1px solid #dcdcdc;
                        border-radius:20px;
                        padding:25px;
                    ">

                            <div class="mb-4 row align-items-center">

                                <div class="col-md-3">

                                    <h4 class="mb-0 fw-bold">

                                        Riwayat
                                        Pengambilan

                                    </h4>

                                </div>

                                <div class="col-md-9">

                                    <div class="gap-2 d-flex">

                                        <input type="date" class="form-control" id="tanggal_awal">

                                        <input type="date" class="form-control" id="tanggal_akhir">

                                        <button class="text-white btn btn-warning" onclick="filterLaporanPengambilan()">

                                            Cari

                                        </button>

                                        <button class="btn btn-danger" onclick="downloadLaporanPengambilan()">

                                            Download PDF

                                        </button>

                                    </div>

                                </div>

                            </div>

                            <hr>

                            <div class="table-responsive">

                                <table class="table align-middle table-hover">

                                    <thead>

                                        <tr>

                                            <th>Tanggal</th>

                                            <th>Mustahik</th>

                                            <th>RFID</th>

                                            <th>Mesin</th>

                                            <th>Jumlah Ambil</th>

                                            <th>Status</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        @forelse($transactions as $transaction)
                                            <tr class="laporan-row" data-gram="{{ $transaction->jumlah_ambil_gram }}"
                                                data-date="{{ \Carbon\Carbon::parse($transaction->tanggal_pengambilan)->format('Y-m-d') }}">

                                                <td>
                                                    {{ \Carbon\Carbon::parse($transaction->tanggal_pengambilan)->format('d M Y') }}
                                                </td>

                                                <td>

                                                    {{ $transaction->mustahik->nama ?? '-' }}

                                                </td>

                                                <td>

                                                    {{ $transaction->mustahik->rfid_uid ?? '-' }}

                                                </td>

                                                <td>

                                                    {{ $transaction->machine->machine_code ?? '-' }}

                                                </td>

                                                <td>

                                                    {{ number_format($transaction->jumlah_ambil_gram) }}

                                                    gram

                                                </td>

                                                <td>

                                                    <span class="badge bg-success">

                                                        Berhasil

                                                    </span>

                                                </td>

                                            </tr>

                                        @empty

                                            <tr>

                                                <td colspan="6" class="py-5 text-center">

                                                    Belum ada data transaksi

                                                </td>

                                            </tr>
                                        @endforelse

                                    </tbody>

                                </table>

                            </div>
                            <hr>

                            <div class="row">

                                <div class="col-md-6">

                                    <strong>

                                        Total Transaksi :

                                    </strong>

                                    <span id="totalTransaksi">

                                        {{ $transactions->count() }}

                                    </span>

                                </div>

                                <div class="col-md-6 text-end">

                                    <strong>

                                        Total Beras :

                                    </strong>

                                    <span id="totalBeras">

                                        {{ number_format($transactions->sum('jumlah_ambil_gram')) }}

                                    </span>

                                    gram

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>
        @foreach ($mustahiks as $mustahik)
            <div class="modal fade" id="modalMustahik{{ $mustahik->id }}" tabindex="-1">

                <div class="modal-dialog">

                    <div class="modal-content">

                        <div class="text-center modal-body">

                            <h4 class="mb-1">
                                {{ $mustahik->nama }}
                            </h4>

                            <p class="text-muted">
                                {{ $mustahik->rfid_uid }}
                            </p>

                            <div class="mb-3 row g-2">

                                <div class="col-6">

                                    <button class="btn btn-primary w-100" data-bs-toggle="modal"
                                        data-bs-target="#modalEditMustahik{{ $mustahik->id }}">

                                        Edit

                                    </button>

                                </div>

                                <div class="col-6">

                                    <form action="{{ route('mustahik.destroy', $mustahik->id) }}" method="POST"
                                        class="delete-form">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-danger w-100">

                                            Hapus

                                        </button>

                                    </form>

                                </div>

                            </div>

                            <button class="mb-3 btn btn-success w-100" data-bs-toggle="modal"
                                data-bs-target="#modalJatah{{ $mustahik->id }}">

                                Tambah Jatah

                            </button>

                            <button class="btn btn-dark w-100" data-bs-toggle="modal"
                                data-bs-target="#modalRiwayat{{ $mustahik->id }}">

                                Riwayat Pengambilan

                            </button>

                        </div>

                    </div>

                </div>

            </div>
            <div class="modal fade" id="modalRiwayat{{ $mustahik->id }}" tabindex="-1">

                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">

                    <div class="border-0 shadow modal-content modal-riwayat">

                        <div class="p-4 modal-body">

                            <div class="riwayat-wrapper">

                                {{-- HEADER --}}

                                <div class="mb-4 d-flex justify-content-between align-items-center">

                                    <h5 class="mb-0 fw-bold">

                                        Riwayat Pengambilan

                                    </h5>

                                    <input type="hidden" id="filterRiwayat{{ $mustahik->id }}" value="all">

                                    <div class="riwayat-filter" id="filterGroup{{ $mustahik->id }}">

                                        <button class="btn btn-outline-secondary rounded-pill btn-sm filter-btn"
                                            onclick="filterHistory({{ $mustahik->id }},7,this)">

                                            1 Minggu

                                        </button>

                                        <button class="btn btn-outline-secondary rounded-pill btn-sm filter-btn"
                                            onclick="filterHistory({{ $mustahik->id }},30,this)">

                                            1 Bulan

                                        </button>

                                        <button class="btn btn-outline-secondary rounded-pill btn-sm filter-btn"
                                            onclick="filterHistory({{ $mustahik->id }},180,this)">

                                            6 Bulan

                                        </button>

                                        <button class="btn btn-dark rounded-pill btn-sm filter-btn"
                                            onclick="filterHistory({{ $mustahik->id }},'all',this)">

                                            Semua

                                        </button>

                                        <button class="btn btn-secondary download-btn"
                                            onclick="downloadRiwayat({{ $mustahik->id }})">

                                            <i class="bi bi-download"></i>

                                        </button>

                                    </div>

                                </div>

                                {{-- INFORMASI MUSTAHIK --}}

                                <div class="mb-4 row">

                                    <div class="mb-3 col-md-4">
                                        <div class="info-card">

                                            <p class="mb-3">

                                                <small class="text-muted">

                                                    Nama

                                                </small>

                                                <br>

                                                <strong>

                                                    {{ $mustahik->nama }}

                                                </strong>

                                            </p>

                                            <p class="mb-3">

                                                <small class="text-muted">

                                                    RFID

                                                </small>

                                                <br>

                                                <strong>

                                                    {{ $mustahik->rfid_uid }}

                                                </strong>

                                            </p>

                                            <p class="mb-3">

                                                <small class="text-muted">

                                                    NIK

                                                </small>

                                                <br>

                                                <strong>

                                                    {{ $mustahik->nik }}

                                                </strong>

                                            </p>

                                            <p class="mb-0">

                                                <small class="text-muted">

                                                    Sisa Jatah (Gram)

                                                </small>

                                                <br>

                                                <strong>

                                                    {{ number_format($mustahik->jatah_beras_gram) }}

                                                </strong>

                                            </p>

                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <div class="info-card">

                                            <p class="mb-3">

                                                <small class="text-muted">

                                                    Provinsi

                                                </small>

                                                <br>

                                                <strong>

                                                    {{ $mustahik->village->district->regency->province->name }}

                                                </strong>

                                            </p>

                                            <p class="mb-3">

                                                <small class="text-muted">

                                                    Kabupaten

                                                </small>

                                                <br>

                                                <strong>

                                                    {{ $mustahik->village->district->regency->name }}

                                                </strong>

                                            </p>

                                            <p class="mb-3">

                                                <small class="text-muted">

                                                    Kecamatan

                                                </small>

                                                <br>

                                                <strong>

                                                    {{ $mustahik->village->district->name }}

                                                </strong>

                                            </p>

                                            <p class="mb-3">

                                                <small class="text-muted">

                                                    Desa

                                                </small>

                                                <br>

                                                <strong>

                                                    {{ $mustahik->village->name }}

                                                </strong>

                                            </p>

                                            <p class="mb-0">

                                                <small class="text-muted">

                                                    Alamat

                                                </small>

                                                <br>

                                                <strong>

                                                    {{ $mustahik->alamat }}

                                                </strong>

                                            </p>

                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <div class="info-card">

                                            <p class="mb-3">

                                                <small class="text-muted">

                                                    Pengambilan Terakhir

                                                </small>

                                                <br>

                                                <strong>

                                                    {{ optional($mustahik->transactions->sortByDesc('tanggal_pengambilan')->first())?->tanggal_pengambilan
                                                        ? \Carbon\Carbon::parse(
                                                            $mustahik->transactions->sortByDesc('tanggal_pengambilan')->first()->tanggal_pengambilan,
                                                        )->format('d M Y')
                                                        : '-' }}

                                                </strong>

                                            </p>

                                            <p class="mb-3">

                                                <small class="text-muted">

                                                    Total Pengambilan

                                                </small>

                                                <br>

                                                <strong>

                                                    {{ $mustahik->transactions->count() }}

                                                    Kali

                                                </strong>

                                            </p>
                                            <div class="mt-4 summary-box">

                                                <small>Total Beras Diambil</small>

                                                <h4>
                                                    {{ number_format($mustahik->transactions->sum('jumlah_ambil_gram')) }}
                                                    gram
                                                </h4>

                                            </div>
                                        </div>
                                    </div>
                                </div> {{-- END ROW INFORMASI --}}
                                <hr class="my-4">

                                <div class="table-responsive">

                                    <table class="table align-middle table-hover riwayat-table">

                                        <thead>

                                            <tr>

                                                <th width="35%">Tanggal</th>

                                                <th width="25%">Mesin</th>

                                                <th width="40%">Jumlah Ambil</th>

                                            </tr>

                                        </thead>

                                        <tbody id="historyBody{{ $mustahik->id }}">

                                            @forelse($mustahik->transactions->sortByDesc('tanggal_pengambilan') as $transaction)
                                                <tr
                                                    data-date="{{ \Carbon\Carbon::parse($transaction->tanggal_pengambilan)->format('Y-m-d') }}">

                                                    <td>
                                                        {{ \Carbon\Carbon::parse($transaction->tanggal_pengambilan)->format('d M Y') }}
                                                    </td>

                                                    <td>
                                                        {{ $transaction->machine->machine_code }}
                                                    </td>

                                                    <td>

                                                        <span class="badge-jumlah">

                                                            {{ number_format($transaction->jumlah_ambil_gram) }}

                                                            gram

                                                        </span>

                                                    </td>

                                                </tr>

                                            @empty

                                                <tr>

                                                    <td colspan="3" class="py-4 text-center text-muted">

                                                        Belum ada riwayat pengambilan

                                                    </td>

                                                </tr>
                                            @endforelse

                                        </tbody>

                                    </table>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="modal fade" id="modalJatah{{ $mustahik->id }}" tabindex="-1">

                <div class="modal-dialog">

                    <form action="{{ route('mustahik.tambah-jatah', $mustahik->id) }}" method="POST">

                        @csrf

                        <div class="modal-content">

                            <div class="modal-header">

                                <h5>Tambah Jatah Beras</h5>

                            </div>

                            <div class="modal-body">

                                <div class="mb-3">

                                    <label>Nama Mustahik</label>

                                    <input type="text" class="form-control" value="{{ $mustahik->nama }}" readonly>

                                </div>

                                <div class="mb-3">

                                    <label>Tambah Jatah (Gram)</label>

                                    <input type="number" name="jumlah" class="form-control" required>

                                </div>

                                {{-- <div class="mb-3">

                                    <label>Keterangan</label>

                                    <textarea name="keterangan" class="form-control"></textarea>

                                </div> --}}

                            </div>

                            <div class="modal-footer">

                                <button type="submit" class="btn btn-success">

                                    Tambah

                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>
            <div class="modal fade" id="modalEditMustahik{{ $mustahik->id }}" tabindex="-1">

                <div class="modal-dialog modal-lg">

                    <form action="{{ route('mustahik.update', $mustahik->id) }}" method="POST">

                        @csrf
                        @method('PUT')

                        <div class="modal-content">

                            <div class="modal-header">

                                <h5 class="modal-title">
                                    Edit Mustahik
                                </h5>

                                <button type="button" class="btn-close" data-bs-dismiss="modal">
                                </button>

                            </div>

                            <div class="modal-body">

                                <div class="row">

                                    <div class="mb-3 col-md-6">

                                        <label>Nama Mustahik</label>

                                        <input type="text" name="nama" class="form-control"
                                            value="{{ $mustahik->nama }}">

                                    </div>

                                    <div class="mb-3 col-md-6">

                                        <label>ID RFID</label>

                                        <input type="text" name="rfid_uid" class="form-control"
                                            value="{{ $mustahik->rfid_uid }}">

                                    </div>

                                    <div class="mb-3 col-md-6">

                                        <label>NIK</label>

                                        <input type="text" name="nik" class="form-control"
                                            value="{{ $mustahik->nik }}">

                                    </div>

                                    <div class="mb-3 col-md-6">

                                        <label>No HP</label>

                                        <input type="text" name="no_hp" class="form-control"
                                            value="{{ $mustahik->no_hp }}">

                                    </div>

                                    <div class="mb-3 col-md-12">

                                        <label>Alamat Lengkap</label>

                                        <textarea name="alamat" class="form-control">{{ $mustahik->alamat }}</textarea>

                                    </div>

                                    <div class="mb-3 col-md-6">

                                        <label>Provinsi</label>

                                        <select class="form-control edit-province-mustahik"
                                            data-mustahik="{{ $mustahik->id }}">

                                            @foreach ($provinces as $province)
                                                <option value="{{ $province->id }}"
                                                    {{ $province->id == $mustahik->village->district->regency->province->id ? 'selected' : '' }}>

                                                    {{ $province->name }}

                                                </option>
                                            @endforeach

                                        </select>

                                    </div>

                                    <div class="mb-3 col-md-6">

                                        <label>Kabupaten</label>

                                        <select class="form-control edit-regency-mustahik"
                                            data-mustahik="{{ $mustahik->id }}">

                                            <option value="{{ $mustahik->village->district->regency->id }}" selected>

                                                {{ $mustahik->village->district->regency->name }}

                                            </option>

                                        </select>

                                    </div>

                                    <div class="mb-3 col-md-6">

                                        <label>Kecamatan</label>

                                        <select class="form-control edit-district-mustahik"
                                            data-mustahik="{{ $mustahik->id }}">

                                            <option value="{{ $mustahik->village->district->id }}" selected>

                                                {{ $mustahik->village->district->name }}

                                            </option>

                                        </select>

                                    </div>

                                    <div class="mb-3 col-md-6">

                                        <label>Desa</label>

                                        <select name="village_id" class="form-control edit-village-mustahik"
                                            data-mustahik="{{ $mustahik->id }}">

                                            <option value="{{ $mustahik->village->id }}" selected>

                                                {{ $mustahik->village->name }}

                                            </option>

                                        </select>

                                    </div>

                                    {{-- <div class="mb-3 col-md-6">

                                                    <label>Jatah Beras (Gram)</label>

                                                    <input type="number" name="jatah_beras_gram" class="form-control"
                                                        value="{{ $mustahik->jatah_beras_gram }}">

                                                </div> --}}

                                </div>

                            </div>

                            <div class="modal-footer">

                                <button type="submit" class="btn btn-primary">

                                    Simpan Perubahan

                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>
        @endforeach
        <div class="modal fade" id="modalTambahMustahik" tabindex="-1">

            <div class="modal-dialog modal-lg">

                <form action="{{ route('mustahik.store') }}" method="POST">

                    @csrf

                    <div class="modal-content">

                        <div class="modal-header">

                            <h5 class="modal-title">
                                Tambah Mustahik
                            </h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                            </button>

                        </div>

                        <div class="modal-body">

                            <div class="row">

                                {{-- Nama --}}
                                <div class="mb-3 col-md-6">

                                    <label>Nama Mustahik</label>

                                    <input type="text" name="nama" class="form-control" required>

                                </div>

                                {{-- Provinsi --}}
                                <div class="mb-3 col-md-6">

                                    <label>Provinsi</label>

                                    <select id="province" class="form-control">

                                        <option value="">
                                            Pilih Provinsi
                                        </option>

                                        @foreach ($provinces as $province)
                                            <option value="{{ $province->id }}">
                                                {{ $province->name }}
                                            </option>
                                        @endforeach

                                    </select>

                                </div>

                                {{-- RFID --}}
                                <div class="mb-3 col-md-6">

                                    <label>ID RFID</label>

                                    <input type="text" name="rfid_uid" class="form-control" required>

                                </div>

                                {{-- Kabupaten --}}
                                <div class="mb-3 col-md-6">

                                    <label>Kabupaten</label>

                                    <select id="regency" class="form-control">

                                        <option value="">
                                            Pilih Kabupaten
                                        </option>

                                    </select>

                                </div>

                                {{-- NIK --}}
                                <div class="mb-3 col-md-6">

                                    <label>NIK</label>

                                    <input type="text" name="nik" class="form-control" required>

                                </div>

                                {{-- Kecamatan --}}
                                <div class="mb-3 col-md-6">

                                    <label>Kecamatan</label>

                                    <select id="district" class="form-control">

                                        <option value="">
                                            Pilih Kecamatan
                                        </option>

                                    </select>

                                </div>

                                {{-- No HP --}}
                                <div class="mb-3 col-md-6">

                                    <label>No HP / WhatsApp</label>

                                    <input type="text" name="no_hp" class="form-control">

                                </div>

                                {{-- Desa --}}
                                <div class="mb-3 col-md-6">

                                    <label>Desa</label>

                                    <select id="village" name="village_id" class="form-control">

                                        <option value="">
                                            Pilih Desa
                                        </option>

                                    </select>

                                </div>

                                {{-- Alamat --}}
                                <div class="mb-3 col-md-12">

                                    <label>Alamat Lengkap</label>

                                    <textarea name="alamat" rows="3" class="form-control" required></textarea>

                                </div>

                                {{-- Jatah --}}
                                <div class="mb-3 col-md-12">

                                    <label>Jatah Beras (Gram)</label>

                                    <input type="number" name="jatah_beras_gram" class="form-control"
                                        placeholder="Contoh : 5000" required>

                                </div>

                            </div>

                        </div>

                        <div class="modal-footer">

                            <button type="submit" class="btn btn-tambah">

                                Tambah Mustahik

                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

    <div class="modal fade" id="modalFilterMustahik" tabindex="-1">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="p-4 modal-body">

                    <div class="row">

                        <div class="mb-3 col-md-6">

                            <label>Nama</label>

                            <input type="text" id="filterNama" class="form-control">

                        </div>

                        <div class="mb-3 col-md-6">

                            <label>Alamat</label>

                            <input type="text" id="filterAlamat" class="form-control">

                        </div>

                        <div class="mb-3 col-md-6">

                            <label>Kecamatan</label>

                            <input type="text" id="filterKecamatan" class="form-control">

                        </div>

                        <div class="mb-3 col-md-6">

                            <label>Desa</label>

                            <input type="text" id="filterDesa" class="form-control">

                        </div>

                        <div class="mb-3 col-md-6">

                            <label>Jatah Beras</label>

                            <input type="number" id="filterJatah" class="form-control">

                        </div>

                    </div>

                    <div class="gap-2 mt-3 d-flex justify-content-end">

                        <button class="btn btn-light" onclick="resetFilterMustahik()">

                            Reset

                        </button>

                        <button class="btn btn-primary" onclick="filterMustahik()">

                            Cari

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>
    <script>
        document
            .getElementById('province')
            .addEventListener('change', function() {

                fetch('/regencies/' + this.value)

                    .then(res => res.json())

                    .then(data => {

                        let html =
                            '<option value="">Pilih Kabupaten</option>';

                        data.forEach(item => {

                            html += `
                        <option value="${item.id}">
                            ${item.name}
                        </option>
                    `;
                        });

                        document
                            .getElementById('regency')
                            .innerHTML = html;

                        document
                            .getElementById('district')
                            .innerHTML =
                            '<option value="">Pilih Kecamatan</option>';

                        document
                            .getElementById('village')
                            .innerHTML =
                            '<option value="">Pilih Desa</option>';
                    });

            });


        document
            .getElementById('regency')
            .addEventListener('change', function() {

                fetch('/districts/' + this.value)

                    .then(res => res.json())

                    .then(data => {

                        let html =
                            '<option value="">Pilih Kecamatan</option>';

                        data.forEach(item => {

                            html += `
                        <option value="${item.id}">
                            ${item.name}
                        </option>
                    `;
                        });

                        document
                            .getElementById('district')
                            .innerHTML = html;

                        document
                            .getElementById('village')
                            .innerHTML =
                            '<option value="">Pilih Desa</option>';
                    });

            });


        document
            .getElementById('district')
            .addEventListener('change', function() {

                fetch('/villages/' + this.value)

                    .then(res => res.json())

                    .then(data => {

                        let html =
                            '<option value="">Pilih Desa</option>';

                        data.forEach(item => {

                            html += `
                        <option value="${item.id}">
                            ${item.name}
                        </option>
                    `;
                        });

                        document
                            .getElementById('village')
                            .innerHTML = html;
                    });

            });
    </script>
    <script>
        document
            .querySelectorAll('.edit-province-mustahik')
            .forEach(function(province) {

                province.addEventListener('change', function() {

                    const id =
                        this.dataset.mustahik;

                    fetch('/regencies/' + this.value)

                        .then(res => res.json())

                        .then(data => {

                            let html =
                                '<option value="">Pilih Kabupaten</option>';

                            data.forEach(item => {

                                html += `
                    <option value="${item.id}">
                        ${item.name}
                    </option>
                `;

                            });

                            document.querySelector(
                                '.edit-regency-mustahik[data-mustahik="' +
                                id +
                                '"]'
                            ).innerHTML = html;

                        });

                });

            });

        document
            .querySelectorAll('.edit-regency-mustahik')
            .forEach(function(regency) {

                regency.addEventListener('change', function() {

                    const id =
                        this.dataset.mustahik;

                    fetch('/districts/' + this.value)

                        .then(res => res.json())

                        .then(data => {

                            let html =
                                '<option value="">Pilih Kecamatan</option>';

                            data.forEach(item => {

                                html += `
                    <option value="${item.id}">
                        ${item.name}
                    </option>
                `;

                            });

                            document.querySelector(
                                '.edit-district-mustahik[data-mustahik="' +
                                id +
                                '"]'
                            ).innerHTML = html;

                        });

                });

            });

        document
            .querySelectorAll('.edit-district-mustahik')
            .forEach(function(district) {

                district.addEventListener('change', function() {

                    const id =
                        this.dataset.mustahik;

                    fetch('/villages/' + this.value)

                        .then(res => res.json())

                        .then(data => {

                            let html =
                                '<option value="">Pilih Desa</option>';

                            data.forEach(item => {

                                html += `
                    <option value="${item.id}">
                        ${item.name}
                    </option>
                `;

                            });

                            document.querySelector(
                                '.edit-village-mustahik[data-mustahik="' +
                                id +
                                '"]'
                            ).innerHTML = html;

                        });

                });

            });
    </script>
    <script>
        document.querySelectorAll('.delete-form')
            .forEach(form => {

                form.addEventListener('submit', function(e) {

                    e.preventDefault();

                    Swal.fire({
                        title: 'Hapus Mustahik?',
                        text: 'Data yang dihapus tidak dapat dikembalikan.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal'
                    }).then((result) => {

                        if (result.isConfirmed) {
                            form.submit();
                        }

                    });

                });

            });
    </script>
    <script>
        function filterHistory(
            mustahikId,
            days,
            button
        ) {

            // Simpan filter aktif
            document.getElementById(
                'filterRiwayat' + mustahikId
            ).value = days;

            // Ambil semua tombol filter
            const buttons =
                document.querySelectorAll(
                    '#filterGroup' +
                    mustahikId +
                    ' .filter-btn'
                );

            // Reset warna tombol
            buttons.forEach(btn => {

                btn.style.backgroundColor = '';
                btn.style.borderColor = '';
                btn.style.color = '';

            });

            // Tombol aktif
            button.style.backgroundColor =
                '#F08519';

            button.style.borderColor =
                '#F08519';

            button.style.color =
                '#ffffff';

            // Filter tabel
            const rows =
                document.querySelectorAll(
                    '#historyBody' +
                    mustahikId +
                    ' tr[data-date]'
                );

            const today =
                new Date();

            rows.forEach(row => {

                const rowDate =
                    new Date(
                        row.dataset.date
                    );

                if (days === 'all') {

                    row.style.display = '';
                    return;

                }

                const diffDays =
                    (today - rowDate) /
                    (1000 * 60 * 60 * 24);

                row.style.display =
                    diffDays <= days ?
                    '' :
                    'none';

            });

        }

        function downloadRiwayat(id) {
            const filter =
                document.getElementById(
                    'filterRiwayat' + id
                ).value;

            window.open(
                '/mustahik/' +
                id +
                '/riwayat-pdf?filter=' +
                filter,
                '_blank'
            );
        }

        buttons.forEach(btn => {
            btn.classList.remove('filter-active');
        });

        button.classList.add('filter-active');
    </script>
    <script>
        function filterLaporanPengambilan() {
            const tanggalAwal =
                document.getElementById(
                    'tanggal_awal'
                ).value;

            const tanggalAkhir =
                document.getElementById(
                    'tanggal_akhir'
                ).value;

            const rows =
                document.querySelectorAll(
                    '.laporan-row'
                );

            let totalTransaksi = 0;
            let totalBeras = 0;

            rows.forEach(row => {

                const tanggal =
                    row.dataset.date;

                let tampil = true;

                if (
                    tanggalAwal &&
                    tanggal < tanggalAwal
                ) {
                    tampil = false;
                }

                if (
                    tanggalAkhir &&
                    tanggal > tanggalAkhir
                ) {
                    tampil = false;
                }

                if (tampil) {
                    row.style.display = '';

                    totalTransaksi++;

                    totalBeras += parseInt(
                        row.dataset.gram
                    );
                } else {
                    row.style.display = 'none';
                }

            });

            document.getElementById(
                    'totalTransaksi'
                ).innerText =
                totalTransaksi;

            document.getElementById(
                    'totalBeras'
                ).innerText =
                totalBeras.toLocaleString(
                    'id-ID'
                );
        }
    </script>
    <script>
        function downloadLaporanPengambilan() {
            const tanggalAwal =
                document.getElementById(
                    'tanggal_awal'
                ).value;

            const tanggalAkhir =
                document.getElementById(
                    'tanggal_akhir'
                ).value;

            let url =
                "{{ route('laporan.pengambilan.pdf') }}";

            url +=
                '?tanggal_awal=' +
                tanggalAwal +
                '&tanggal_akhir=' +
                tanggalAkhir;

            window.open(
                url,
                '_blank'
            );
        }
    </script>
    <script>
        function filterMustahik() {
            const nama =
                document
                .getElementById(
                    'filterNama'
                )
                .value
                .toLowerCase();

            const alamat =
                document
                .getElementById(
                    'filterAlamat'
                )
                .value
                .toLowerCase();

            const kecamatan =
                document
                .getElementById(
                    'filterKecamatan'
                )
                .value
                .toLowerCase();

            const desa =
                document
                .getElementById(
                    'filterDesa'
                )
                .value
                .toLowerCase();

            const jatah =
                document
                .getElementById(
                    'filterJatah'
                )
                .value;

            const rows =
                document.querySelectorAll(
                    '.mustahik-row'
                );

            let total = 0;

            rows.forEach(row => {

                const cocok =

                    row.dataset.nama.includes(nama) &&
                    row.dataset.alamat.includes(alamat) &&
                    row.dataset.kecamatan.includes(kecamatan) &&
                    row.dataset.desa.includes(desa) &&
                    (
                        jatah === '' ||
                        row.dataset.jatah == jatah
                    );

                if (cocok) {
                    row.style.display = '';
                    total++;
                } else {
                    row.style.display = 'none';
                }

            });

            const badge =
                document.getElementById(
                    'filterCountBadge'
                );

            badge.classList.remove(
                'd-none'
            );

            badge.innerHTML =
                total +
                ' Data';

            bootstrap.Modal
                .getInstance(
                    document.getElementById(
                        'modalFilterMustahik'
                    )
                )
                .hide();
        }

        function resetFilterMustahik() {
            document
                .querySelectorAll(
                    '#modalFilterMustahik input'
                )
                .forEach(input => {

                    input.value = '';

                });

            document
                .querySelectorAll(
                    '.mustahik-row'
                )
                .forEach(row => {

                    row.style.display = '';

                });

            document
                .getElementById(
                    'filterCountBadge'
                )
                .classList.add(
                    'd-none'
                );
        }
    </script>
@endsection
