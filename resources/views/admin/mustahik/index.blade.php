@extends('layouts.admin')

@section('content')
    @php
        $activeOnly = $activeOnly ?? false;
        $pageTitle = $pageTitle ?? 'Daftar Mustahik';
        $mustahikStats = $mustahikStats ?? [
            'total' => $mustahiks->total(),
            'aktif' => $mustahiks->total(),
            'nonaktif' => 0,
        ];
    @endphp

    <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .spinning {
            animation: spin 1s linear infinite;
            display: inline-block;
        }

        .table-loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99;
            border-radius: 14px;
            backdrop-filter: blur(2px);
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
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .modal-riwayat .modal-body {
            max-height: 85vh;
            overflow-y: auto;
            background: #f8fafc;
            padding: 30px !important;
        }

        .riwayat-wrapper {
            background: #fff;
            border: 1px solid #edf0f4;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }

        .riwayat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #edf0f4;
        }

        .riwayat-header h5 {
            color: #111827;
            font-size: 1.25rem;
        }

        .riwayat-filter {
            background: #f8fafc;
            padding: 6px;
            border-radius: 12px;
            border: 1px solid #edf0f4;
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .riwayat-filter .btn {
            border-radius: 8px !important;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 8px 16px;
            border: none;
            transition: all 0.2s ease;
        }

        .riwayat-filter .btn-outline-secondary {
            color: #6b7280;
            background: transparent;
        }

        .riwayat-filter .btn-outline-secondary:hover {
            color: #111827;
            background: #e5e7eb;
        }

        .riwayat-filter .btn-dark, .riwayat-filter .active-filter {
            background: #111827 !important;
            color: #fff !important;
            box-shadow: 0 2px 4px rgba(17, 24, 39, 0.2);
        }

        .download-btn {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px !important;
            background: #fff !important;
            border: 1px solid #edf0f4 !important;
            color: #4b5563 !important;
        }

        .download-btn:hover {
            background: #f3f4f6 !important;
            color: #111827 !important;
        }

        .info-card {
            background: #ffffff;
            border: 1px solid #edf0f4;
            border-radius: 16px;
            padding: 24px;
            height: 100%;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            transition: all 0.3s ease;
        }

        .info-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }

        .info-card p {
            margin-bottom: 1.25rem !important;
            border-bottom: 1px dashed #edf0f4;
            padding-bottom: 0.75rem;
        }

        .info-card p:last-child {
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 0 !important;
        }

        .info-card small {
            color: #6b7280;
            display: block;
            margin-bottom: 6px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
        }

        .info-card strong {
            color: #111827;
            font-size: 1.05rem;
            font-weight: 600;
        }

        .summary-box {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 1px solid #bfdbfe;
            border-radius: 14px;
            padding: 20px;
            text-align: center;
        }

        .summary-box small {
            color: #3b82f6 !important;
            font-size: 0.75rem !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            display: block;
            margin-bottom: 8px !important;
        }

        .summary-box h4 {
            margin: 0;
            color: #1d4ed8;
            font-weight: 800;
            font-size: 1.5rem;
        }

        .riwayat-table {
            border: 1px solid #edf0f4;
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 0;
            width: 100%;
        }

        .riwayat-table thead th {
            background: #f8fafc;
            padding: 16px;
            white-space: nowrap;
            font-weight: 600;
            border: none;
            color: #4b5563;
            font-size: 0.9rem;
        }

        .riwayat-table tbody td {
            padding: 16px;
            vertical-align: middle;
            border-bottom: 1px solid #edf0f4;
            color: #111827;
            font-size: 0.95rem;
        }

        .riwayat-table tbody tr:last-child td {
            border-bottom: none;
        }

        .riwayat-table tbody tr:hover {
            background: #f9fafb;
        }

        .badge-jumlah {
            background: #dcfce7;
            color: #166534;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-block;
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


        .mustahik-summary {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .summary-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-height: 34px;
            padding: 7px 11px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: #f8fafc;
            color: #4b5563;
            font-size: 13px;
            font-weight: 600;
        }

        .summary-pill strong {
            color: #111827;
            font-size: 14px;
        }

        .mustahik-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 10px;
        }

        .view-switcher {
            display: inline-flex;
            gap: 6px;
            padding: 4px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #f8fafc;
        }

        .view-switcher .btn {
            min-height: 36px;
            border: 0;
            border-radius: 8px;
            color: #4b5563;
            font-weight: 600;
        }

        .view-switcher .active {
            background: #343454;
            color: #fff;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-width: 88px;
            justify-content: center;
        }

        .status-badge i {
            font-size: 8px;
        }

        .table-responsive {
            border: 1px solid #edf0f4;
            border-radius: 14px;
            max-height: 60vh;
            overflow-y: auto;
            overflow-x: auto;
        }

        .mesin-table {
            margin-bottom: 0;
        }

        .mesin-table thead th {
            background: #f8fafc;
            padding-top: 14px;
            padding-bottom: 14px;
            white-space: nowrap;
        }

        .mesin-table tbody td {
            padding-top: 14px;
            padding-bottom: 14px;
        }

        .mesin-table tbody tr:hover {
            background: #f9fafb;
        }

        .btn-icon {
            width: 42px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        @media (max-width: 768px) {
            .mustahik-actions {
                justify-content: flex-start;
                width: 100%;
            }

            .view-switcher {
                width: 100%;
            }

            .view-switcher .btn {
                flex: 1;
            }
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

        /* Action Button Styling */
        .btn-action {
            background: #ffffff;
            border: 1px solid #edf0f4;
            color: #4b5563;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }

        .btn-action:hover {
            background: #111827;
            color: #ffffff;
            border-color: #111827;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(17, 24, 39, 0.15);
        }

        .btn-action:active {
            transform: translateY(0);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
    </style>
    <div class="page-card">

        <div class="admin-page-header">

            <div class="admin-title-block">
                <h4 class="admin-page-title">
                    {{ $pageTitle }}
                </h4>
                <p class="admin-page-subtitle">
                    Kelola data penerima manfaat (mustahik), jatah beras, dan riwayat pengambilan.
                </p>

                <div id="mustahikSummary" class="mustahik-summary">
                    <span class="summary-pill">
                        Aktif
                        <strong>{{ number_format($mustahikStats['aktif']) }}</strong>
                    </span>
                    <span class="summary-pill">
                        Nonaktif
                        <strong>{{ number_format($mustahikStats['nonaktif']) }}</strong>
                    </span>
                    <span class="summary-pill">
                        Total
                        <strong>{{ number_format($mustahikStats['total']) }}</strong>
                    </span>
                </div>
            </div>

            <div class="mustahik-actions">
                <div class="view-switcher">
                    <a href="{{ route('mustahik.active') }}"
                        class="btn {{ $activeOnly ? 'active' : '' }}">
                        Aktif
                    </a>
                    <a href="{{ route('mustahik.index') }}"
                        class="btn {{ $activeOnly ? '' : 'active' }}">
                        Keseluruhan
                    </a>
                </div>

                <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalFilterMustahik">

                    <i class="bi bi-funnel"></i>

                    Filter Data

                </button>

                <span id="filterCountBadge" class="badge bg-warning text-dark d-none">

                    0 Data

                </span>

                <a id="btnRefreshMustahik" href="{{ $activeOnly ? route('mustahik.active') : route('mustahik.index') }}"
                    class="btn btn-primary btn-icon">

                    <i class="bi bi-arrow-clockwise"></i>

                </a>

                <button class="btn btn-tambah" data-bs-toggle="modal" data-bs-target="#modalTambahMustahik">

                    <i class="bi bi-plus-lg me-1"></i>

                    Tambah

                </button>


            </div>

        </div>

        <div class="position-relative">
            <div id="mustahikTableWrapper">
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

                                <th>Status</th>

                                <th width="100">Aksi</th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($mustahiks as $mustahik)
                                <tr class="mustahik-row" data-nama="{{ strtolower($mustahik->nama) }}"
                                    data-alamat="{{ strtolower($mustahik->alamat) }}"
                                    data-kecamatan="{{ strtolower($mustahik->village?->district?->name) }}"
                                    data-desa="{{ strtolower($mustahik->village?->name) }}"
                                    data-jatah="{{ $mustahik->jatah_beras_gram }}"
                                    data-status="{{ $mustahik->status }}">

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
                                        @if ($mustahik->status === 'aktif')
                                            <span class="badge-on status-badge">
                                                <i class="bi bi-circle-fill"></i>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="badge-off status-badge">
                                                <i class="bi bi-circle-fill"></i>
                                                Nonaktif
                                            </span>
                                        @endif
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

                                    <td colspan="10" class="py-4 text-center">

                                        Belum ada data mustahik

                                </td>

                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-end">

                {{ $mustahiks->links('pagination::bootstrap-5') }}

                </div>
            </div>
            <div id="mustahikTableSpinner" class="table-loading-overlay d-none">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
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

                            <div class="mb-3">
                                @if ($mustahik->status === 'aktif')
                                    <span class="badge-on status-badge">
                                        <i class="bi bi-circle-fill"></i>
                                        Aktif
                                    </span>
                                @else
                                    <span class="badge-off status-badge">
                                        <i class="bi bi-circle-fill"></i>
                                        Nonaktif
                                    </span>
                                @endif
                            </div>

                            <div class="mb-3 row g-2">

                                <div class="col-6">

                                    <button class="btn btn-primary w-100" data-bs-toggle="modal"
                                        data-bs-target="#modalEditMustahik{{ $mustahik->id }}">

                                        Edit

                                    </button>

                                </div>

                                <div class="col-6">

                                    <form action="{{ route('mustahik.update-status', $mustahik->id) }}" method="POST"
                                        class="status-form" data-status-action="{{ $mustahik->status === 'aktif' ? 'nonaktif' : 'aktif' }}"
                                        data-mustahik-name="{{ $mustahik->nama }}">

                                        @csrf
                                        @method('PATCH')

                                        <input type="hidden" name="status"
                                            value="{{ $mustahik->status === 'aktif' ? 'nonaktif' : 'aktif' }}">

                                        <button type="submit"
                                            class="btn {{ $mustahik->status === 'aktif' ? 'btn-warning' : 'btn-success' }} w-100">

                                            {{ $mustahik->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}

                                        </button>

                                    </form>

                                </div>

                            </div>



                            <button class="mb-3 btn btn-success w-100" data-bs-toggle="modal"
                                data-bs-target="#modalJatah{{ $mustahik->id }}">

                                Tambah Jatah

                            </button>

                            <button class="btn btn-dark w-100" data-bs-toggle="modal" data-bs-dismiss="modal"
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

                                <div class="riwayat-header">

                                    <h5 class="mb-0 fw-bold">

                                        Riwayat Pengambilan

                                    </h5>

                                    <input type="hidden" id="filterRiwayat{{ $mustahik->id }}" value="all">

                                    <div class="riwayat-filter" id="filterGroup{{ $mustahik->id }}">

                                        <button class="btn btn-outline-secondary filter-btn"
                                            onclick="filterHistory({{ $mustahik->id }},7,this)">

                                            1 Minggu

                                        </button>

                                        <button class="btn btn-outline-secondary filter-btn"
                                            onclick="filterHistory({{ $mustahik->id }},30,this)">

                                            1 Bulan

                                        </button>

                                        <button class="btn btn-outline-secondary filter-btn"
                                            onclick="filterHistory({{ $mustahik->id }},180,this)">

                                            6 Bulan

                                        </button>

                                        <button class="btn btn-outline-secondary filter-btn active-filter"
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
                                             value="{{ $mustahik->nama }}" required
                                             placeholder="Masukkan nama lengkap mustahik">

                                    </div>

                                    <div class="mb-3 col-md-6">

                                         <label>ID RFID</label>

                                         <input type="text" name="rfid_uid" class="form-control"
                                             value="{{ $mustahik->rfid_uid }}" required
                                             placeholder="Tempelkan kartu RFID atau masukkan UID">

                                    </div>

                                    <div class="mb-3 col-md-6">

                                         <label>NIK</label>

                                         <input type="text" name="nik" class="form-control"
                                             value="{{ $mustahik->nik }}" required
                                             maxlength="16" minlength="16" pattern="[0-9]{16}"
                                             oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                             placeholder="Masukkan 16 digit NIK">

                                    </div>

                                    <div class="mb-3 col-md-6">

                                         <label>No HP</label>

                                         <input type="text" name="no_hp" class="form-control"
                                             value="{{ $mustahik->no_hp }}"
                                             placeholder="Masukkan No HP Mustahik">

                                    </div>

                                    <div class="mb-3 col-md-12">

                                         <label>Alamat Lengkap</label>

                                         <textarea name="alamat" class="form-control" required
                                             placeholder="Masukkan alamat lengkap (Jalan, RT/RW, Dusun)">{{ $mustahik->alamat }}</textarea>

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

                                     <input type="text" name="nama" class="form-control" required
                                         placeholder="Masukkan nama lengkap mustahik">

                                </div>

                                {{-- Provinsi --}}
                                <div class="mb-3 col-md-6">

                                    <label>Provinsi</label>

                                    <select id="province" class="form-control">

                                        <option value="">
                                            Pilih Provinsi
                                        </option>

                                        @foreach ($provinces as $province)
                                            <option value="{{ $province->id }}" {{ $province->id == 35 ? 'selected' : '' }}>
                                                {{ $province->name }}
                                            </option>
                                        @endforeach

                                    </select>

                                </div>

                                {{-- RFID --}}
                                <div class="mb-3 col-md-6">

                                     <label>ID RFID</label>

                                     <input type="text" name="rfid_uid" class="form-control" required
                                         placeholder="Tempelkan kartu RFID atau masukkan UID">

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

                                     <input type="text" name="nik" class="form-control" required
                                         maxlength="16" minlength="16" pattern="[0-9]{16}"
                                         oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                         placeholder="Masukkan 16 digit NIK">

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

                                     <input type="text" name="no_hp" class="form-control"
                                         placeholder="Masukkan No HP Mustahik">

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

                                     <textarea name="alamat" rows="3" class="form-control" required
                                         placeholder="Masukkan alamat lengkap (Jalan, RT/RW, Dusun)"></textarea>

                                </div>

                                {{-- Jatah --}}
                                <div class="mb-3 col-md-12">

                                    <label>Jatah Beras (Gram)</label>

                                    <input type="number" name="jatah_beras_gram" class="form-control"
                                        placeholder="Kosongkan jika belum ada jatah">

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

                        <div class="mb-3 col-md-6">

                            <label>Status</label>

                            <select id="filterStatus" class="form-control" {{ $activeOnly ? 'disabled' : '' }}>
                                <option value="">Semua Status</option>
                                <option value="aktif" {{ $activeOnly ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>

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

        // Konfigurasi default wilayah (Jawa Timur & Banyuwangi) - mudah dikembalikan jika tidak dibutuhkan
        const ENABLE_DEFAULT_LOCATION = true;
        const DEFAULT_PROVINCE_ID = '35';   // Jawa Timur
        const DEFAULT_REGENCY_ID = '3510';  // Kabupaten Banyuwangi

        function applyDefaultLocation() {
            if (!ENABLE_DEFAULT_LOCATION) return;

            const provinceElem = document.getElementById('province');
            if (provinceElem && DEFAULT_PROVINCE_ID) {
                provinceElem.value = DEFAULT_PROVINCE_ID;

                fetch('/regencies/' + DEFAULT_PROVINCE_ID)
                    .then(res => res.json())
                    .then(data => {
                        let html = '<option value="">Pilih Kabupaten</option>';
                        data.forEach(item => {
                            const isSelected = item.id == DEFAULT_REGENCY_ID ? 'selected' : '';
                            html += `<option value="${item.id}" ${isSelected}>${item.name}</option>`;
                        });
                        const regencyElem = document.getElementById('regency');
                        if (regencyElem) {
                            regencyElem.innerHTML = html;
                        }

                        if (DEFAULT_REGENCY_ID) {
                            return fetch('/districts/' + DEFAULT_REGENCY_ID);
                        }
                    })
                    .then(res => res ? res.json() : null)
                    .then(data => {
                        if (!data) return;
                        let html = '<option value="">Pilih Kecamatan</option>';
                        data.forEach(item => {
                            html += `<option value="${item.id}">${item.name}</option>`;
                        });
                        const districtElem = document.getElementById('district');
                        if (districtElem) {
                            districtElem.innerHTML = html;
                        }
                        const villageElem = document.getElementById('village');
                        if (villageElem) {
                            villageElem.innerHTML = '<option value="">Pilih Desa</option>';
                        }
                    });
            }
        }

        document.addEventListener('DOMContentLoaded', applyDefaultLocation);
        const modalTambahElem = document.getElementById('modalTambahMustahik');
        if (modalTambahElem) {
            modalTambahElem.addEventListener('show.bs.modal', applyDefaultLocation);
        }
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
        document.querySelectorAll('.status-form')
            .forEach(form => {

                form.addEventListener('submit', function(e) {

                    e.preventDefault();

                    const action =
                        form.dataset.statusAction;

                    const name =
                        form.dataset.mustahikName;

                    Swal.fire({
                        title: action === 'aktif' ? 'Aktifkan Mustahik?' : 'Nonaktifkan Mustahik?',
                        text: action === 'aktif' ?
                            name + ' akan muncul lagi di daftar mustahik aktif.' :
                            name + ' akan dipindahkan dari daftar mustahik aktif.',
                        icon: action === 'aktif' ? 'question' : 'warning',
                        showCancelButton: true,
                        confirmButtonColor: action === 'aktif' ? '#198754' : '#f59e0b',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: action === 'aktif' ? 'Ya, Aktifkan' : 'Ya, Nonaktifkan',
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
                btn.classList.remove('active-filter');
            });

            // Tombol aktif
            button.classList.add('active-filter');

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

            const status =
                document
                .getElementById(
                    'filterStatus'
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
                    ) &&
                    (
                        status === '' ||
                        row.dataset.status === status
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
                    '#modalFilterMustahik input, #modalFilterMustahik select'
                )
                .forEach(input => {

                    input.value = '';

                });

            const filterStatus =
                document.getElementById(
                    'filterStatus'
                );

            if (filterStatus.disabled) {
                filterStatus.value = 'aktif';
            }

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

        document.getElementById('btnRefreshMustahik').addEventListener('click', function(e) {
            e.preventDefault();
            const btn = this;
            const icon = btn.querySelector('i');
            const wrapper = document.getElementById('mustahikTableWrapper');
            const summary = document.getElementById('mustahikSummary');
            const spinner = document.getElementById('mustahikTableSpinner');
            
            if (icon) icon.classList.add('spinning');
            if (wrapper) wrapper.style.opacity = '0.5';
            if (summary) summary.style.opacity = '0.5';
            if (spinner) spinner.classList.remove('d-none');
            
            fetch(window.location.href)
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    const newWrapper = doc.getElementById('mustahikTableWrapper');
                    if (newWrapper && wrapper) {
                        wrapper.innerHTML = newWrapper.innerHTML;
                    }
                    
                    const newSummary = doc.getElementById('mustahikSummary');
                    if (newSummary && summary) {
                        summary.innerHTML = newSummary.innerHTML;
                    }
                })
                .catch(err => console.error('Gagal menyegarkan data:', err))
                .finally(() => {
                    if (icon) icon.classList.remove('spinning');
                    if (spinner) spinner.classList.add('d-none');
                    if (wrapper) {
                        wrapper.style.opacity = '1';
                        wrapper.style.transition = 'opacity 0.2s';
                    }
                    if (summary) {
                        summary.style.opacity = '1';
                        summary.style.transition = 'opacity 0.2s';
                    }
                });
        });
    </script>
@endsection
