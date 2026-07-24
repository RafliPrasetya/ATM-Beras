@extends('layouts.admin')

@section('content')
    <style>
        .laporan-box {
            border: 1px solid #dcdcdc;
            border-radius: 20px;
            padding: 25px;
            background: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }

        .btn-warning {
            background-color: #F08519 !important;
            border-color: #F08519 !important;
            color: #fff !important;
            font-weight: 500;
        }

        .btn-warning:hover {
            background-color: #d9720f !important;
            border-color: #d9720f !important;
        }

        .btn-danger {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-danger:hover {
            background-color: #bb2d3b !important;
            border-color: #b02a37 !important;
            color: #fff !important;
        }

        .table-responsive {
            border: 1px solid #edf0f4;
            border-radius: 14px;
            max-height: 65vh;
            overflow-y: auto;
            overflow-x: auto;
            margin-bottom: 0;
            width: 100%;
            position: relative;
        }

        .table thead th {
            background-color: #f8fafc !important;
            padding: 16px;
            white-space: nowrap;
            font-weight: 600;
            border: none;
            color: #4b5563;
            font-size: 0.9rem;
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: inset 0 -1px 0 #edf0f4;
        }

        .table tbody td {
            padding: 16px;
            vertical-align: middle;
            border-bottom: 1px solid #edf0f4;
            color: #111827;
            font-size: 0.95rem;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .table tbody tr:hover {
            background: #f9fafb;
        }

        .badge-success {
            background-color: #dcfce7;
            color: #166534;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-block;
        }
    </style>

    <div class="page-card">
        <div class="p-4">
            <div class="laporan-box">

                <div class="mb-4 row align-items-center">

                    <div class="col-md-4">
                        <h4 class="mb-0 fw-bold">
                            Riwayat Pengambilan
                        </h4>
                    </div>

                    <div class="col-md-8">
                        <div class="gap-2 d-flex align-items-center flex-wrap justify-content-md-end">
                            <input type="date" class="form-control" style="max-width: 180px;" id="tanggal_awal" value="{{ $tanggalAwal }}">
                            <input type="date" class="form-control" style="max-width: 180px;" id="tanggal_akhir" value="{{ $tanggalAkhir }}">
                            <button class="text-white btn btn-warning px-4" onclick="filterLaporanPengambilan()">
                                Cari
                            </button>
                            <button class="btn btn-danger px-4" onclick="downloadLaporanPengambilan()">
                                Download PDF
                            </button>
                        </div>
                    </div>

                </div>

                <hr class="my-4">

                <div class="table-responsive">
                    <table class="table align-middle table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Mustahik</th>
                                <th>Mesin</th>
                                <th>Jumlah Ambil</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr class="laporan-row" data-gram="{{ $transaction->jumlah_ambil_gram }}"
                                    data-date="{{ \Carbon\Carbon::parse($transaction->tanggal_pengambilan)->format('Y-m-d') }}">
                                    <td>
                                        {{ \Carbon\Carbon::parse($transaction->tanggal_pengambilan)->format('d M Y H:i') }} WIB
                                    </td>
                                    <td>
                                        {{ $transaction->mustahik->nama ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $transaction->machine->machine_code ?? '-' }}
                                    </td>
                                    <td>
                                        {{ number_format($transaction->jumlah_ambil_gram) }} gram
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-5 text-center text-muted">
                                        <i class="bi bi-inbox display-4 d-block mb-3" style="opacity: 0.3;"></i>
                                        Belum ada data transaksi
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <hr class="my-4">

                <div class="row fw-semibold text-secondary">
                    <div class="col-md-6 mb-2 mb-md-0">
                        Total Transaksi : 
                        <span id="totalTransaksi" class="text-dark fw-bold">
                            {{ $totalTransaksi }}
                        </span>
                    </div>
                    <div class="col-md-6 text-md-end">
                        Total Beras : 
                        <span id="totalBeras" class="text-dark fw-bold">
                            {{ number_format($totalBeras) }}
                        </span> gram
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function filterLaporanPengambilan() {
            const tanggalAwal = document.getElementById('tanggal_awal').value;
            const tanggalAkhir = document.getElementById('tanggal_akhir').value;
            let url = "{{ route('mustahik.riwayat') }}";
            let params = [];
            if (tanggalAwal) params.push('tanggal_awal=' + encodeURIComponent(tanggalAwal));
            if (tanggalAkhir) params.push('tanggal_akhir=' + encodeURIComponent(tanggalAkhir));
            if (params.length > 0) {
                url += '?' + params.join('&');
            }
            window.location.href = url;
        }

        function downloadLaporanPengambilan() {
            const tanggalAwal = document.getElementById('tanggal_awal').value;
            const tanggalAkhir = document.getElementById('tanggal_akhir').value;

            let url = "{{ route('laporan.pengambilan.pdf') }}";
            url += '?tanggal_awal=' + tanggalAwal + '&tanggal_akhir=' + tanggalAkhir;

            window.open(url, '_blank');
        }
    </script>
@endsection
