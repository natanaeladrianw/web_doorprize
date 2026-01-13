<!DOCTYPE html>
<html>

<head>
    <title>Laporan Pemenang Doorprize</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10pt;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 16pt;
        }

        .header p {
            margin: 5px 0;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 9pt;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Laporan Pemenang Doorprize</h1>
        <p>Dicetak pada: {{ now()->format('d F Y, H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 25%">Pemenang</th>
                <th style="width: 20%">NIP</th>
                <th style="width: 20%">Hadiah</th>
                <th style="width: 20%">Form</th>
                <th style="width: 20%">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($winners as $winner)
                @php
                    $wData = $winner->submission->submission_data ?? [];
                    $wName = null;
                    $wNip = null;
                    foreach ($wData as $key => $value) {
                        if (stripos($key, 'nama') !== false) {
                            $wName = $value;
                            break;
                        }
                        if (stripos($key, 'nip') !== false) {
                            $wNip = $value;
                            break;
                        }
                    }
                    if (!$wName) {
                        $wName = 'Peserta #' . $winner->submission->id;
                    }
                @endphp
                <tr>
                    <td style="text-align: center">{{ $loop->iteration }}</td>
                    <td>{{ $wName }}</td>
                    <td>{{ $wNip ?? '-' }}</td>
                    <td>{{ $winner->prize->name ?? ($winner->prize_name ?? 'Hadiah Dihapus') }}</td>
                    <td>{{ $winner->submission->form->title ?? '-' }}</td>
                    <td>{{ $winner->selected_at?->format('d M Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Sistem Doorprize</p>
    </div>
</body>

</html>
