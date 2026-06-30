<!DOCTYPE html>
<html>

<head>

    <style>
        body {
            font-family: sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 10px;
        }

        th {
            background: #F08519;
            color: white;
        }
    </style>

</head>

<body>

    <h2>
        Riwayat Pengambilan Beras
    </h2>

    <hr>

    <p>

        Nama :
        {{ $mustahik->nama }}

    </p>

    <p>

        RFID :
        {{ $mustahik->rfid_uid }}

    </p>

    <p>

        NIK :
        {{ $mustahik->nik }}

    </p>

    <table>

        <thead>

            <tr>

                <th>Tanggal</th>

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

</body>

</html>
