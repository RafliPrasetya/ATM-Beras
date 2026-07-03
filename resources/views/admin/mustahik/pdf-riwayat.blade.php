<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: sans-serif;
            font-size: 14px;
            color: #000;
        }

        .header-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 30px;
        }

        .info-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .info-table td.label {
            width: 120px;
        }

        .info-table td.colon {
            width: 20px;
            text-align: center;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .transaction-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .transaction-table th,
        .transaction-table td {
            padding: 8px 10px;
            text-align: left;
            border-bottom: 1px dashed #ccc;
        }

        .transaction-table th {
            border-bottom: 2px solid #000;
            border-top: 2px solid #000;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    <div class="header-title">DATA MUSTAHIK ATM BERAS</div>

    <table class="info-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="label">Nama</td>
            <td class="colon">:</td>
            <td>{{ $mustahik->nama }}</td>
        </tr>
        <tr>
            <td class="label">NIK</td>
            <td class="colon">:</td>
            <td>{{ $mustahik->nik }}</td>
        </tr>
        <tr>
            <td class="label">No HP</td>
            <td class="colon">:</td>
            <td>{{ $mustahik->no_hp ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Alamat</td>
            <td class="colon">:</td>
            <td>{{ $mustahik->alamat }}</td>
        </tr>
        <tr>
            <td class="label">Wilayah</td>
            <td class="colon">:</td>
            <td>
                {{ $mustahik->village?->district?->regency?->province?->name ?? '-' }} - 
                {{ $mustahik->village?->district?->regency?->name ?? '-' }} - 
                {{ $mustahik->village?->district?->name ?? '-' }} - 
                {{ $mustahik->village?->name ?? '-' }}
            </td>
        </tr>
        <tr>
            <td class="label">Jatah Beras</td>
            <td class="colon">:</td>
            <td>{{ number_format($mustahik->jatah_beras_gram, 0, ',', '.') }} gram</td>
        </tr>
    </table>

    <div class="section-title">Riwayat Pengambilan</div>

    <table class="transaction-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Mesin</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $transaction)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($transaction->tanggal_pengambilan)->format('d M Y') }}</td>
                    <td>{{ $transaction->machine->machine_code }}</td>
                    <td>{{ number_format($transaction->jumlah_ambil_gram, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            @if($transactions->isEmpty())
                <tr>
                    <td colspan="3" style="text-align:center;">Belum ada riwayat pengambilan</td>
                </tr>
            @endif
        </tbody>
    </table>

</body>
</html>
