<!DOCTYPE html>
<html>

<head>

    <title>
        Laporan Pengambilan Beras
    </title>

    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 8px;
        }

        th {
            background: #f2f2f2;
        }
    </style>

</head>

<body>

    <h2>
        Laporan Riwayat Pengambilan Beras
    </h2>

    <p>

        Periode :

        {{ $tanggalAwal ?: '-' }}

        s/d

        {{ $tanggalAkhir ?: '-' }}

    </p>

    <table>

        <thead>

            <tr>

                <th>Tanggal</th>

                <th>Mustahik</th>

                <!-- <th>RFID</th> -->

                <th>Mesin</th>

                <th>Jumlah Ambil</th>

            </tr>

        </thead>

        <tbody>

            @foreach ($transactions as $transaction)
                <tr>

                    <td>

                        {{ \Carbon\Carbon::parse($transaction->tanggal_pengambilan)->format('d M Y') }}

                    </td>

                    <td>
                        {{ $transaction->mustahik->nama }}
                    </td>

                    <!-- <td>
                        {{ $transaction->mustahik->rfid_uid }}
                    </td> -->

                    <td>
                        {{ $transaction->machine->machine_code }}
                    </td>

                    <td>

                        {{ number_format($transaction->jumlah_ambil_gram) }}

                        gram

                    </td>

                </tr>
            @endforeach

        </tbody>

    </table>

    <br>

    <strong>

        Total Transaksi :

        {{ $totalTransaksi }}

    </strong>

    <br>

    <strong>

        Total Beras :

        {{ number_format($totalBeras) }}

        gram

    </strong>

</body>

</html>
