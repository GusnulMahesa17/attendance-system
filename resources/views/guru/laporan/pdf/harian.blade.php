<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Harian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 18px;
        }
        .info-table {
            margin-bottom: 15px;
            width: 100%;
        }
        .info-table td {
            padding: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #333;
        }
        th {
            background-color: #4CAF50;
            color: white;
            padding: 8px;
            font-weight: bold;
        }
        td {
            padding: 6px 8px;
        }
        .pertemuan-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .pertemuan-header {
            background-color: #2196F3;
            color: white;
            padding: 8px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .status-hadir { background-color: #d4edda; }
        .status-izin { background-color: #fff3cd; }
        .status-sakit { background-color: #cfe2ff; }
        .status-alpa { background-color: #f8d7da; }
        .summary {
            display: inline-block;
            margin-right: 15px;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN KEHADIRAN HARIAN</h2>
        <p style="margin: 5px 0;">{{ auth()->user()->name }} - NIP: {{ auth()->user()->nip }}</p>
    </div>

    <table class="info-table" style="border: none;">
        <tr>
            <td style="width: 150px; border: none;"><strong>Tanggal</strong></td>
            <td style="border: none;">: {{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}</td>
        </tr>
        @if($kelas)
        <tr>
            <td style="border: none;"><strong>Kelas</strong></td>
            <td style="border: none;">: {{ $kelas->nama_kelas }}</td>
        </tr>
        @endif
        <tr>
            <td style="border: none;"><strong>Guru</strong></td>
            <td style="border: none;">: {{ $guru->name }}</td>
        </tr>
    </table>

    @foreach($pertemuans as $pertemuan)
    <div class="pertemuan-section">
        <div class="pertemuan-header">
            {{ $pertemuan->jadwal->mataPelajaran->nama_mapel }} - 
            {{ $pertemuan->jadwal->kelas->nama_kelas }} 
            (Pertemuan ke-{{ $pertemuan->pertemuan_ke }})
        </div>

        <p><strong>Deskripsi:</strong> {{ $pertemuan->deskripsi ?? '-' }}</p>

        <div style="margin-bottom: 10px;">
            <span class="summary" style="background-color: #d4edda;">
                Hadir: {{ $pertemuan->absensis->where('status', 'hadir')->count() }}
            </span>
            <span class="summary" style="background-color: #fff3cd;">
                Izin: {{ $pertemuan->absensis->where('status', 'izin')->count() }}
            </span>
            <span class="summary" style="background-color: #cfe2ff;">
                Sakit: {{ $pertemuan->absensis->where('status', 'sakit')->count() }}
            </span>
            <span class="summary" style="background-color: #f8d7da;">
                Alpa: {{ $pertemuan->absensis->where('status', 'alpa')->count() }}
            </span>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">NIS</th>
                    <th style="width: 35%;">Nama Siswa</th>
                    <th style="width: 15%;">Status</th>
                    <th style="width: 30%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pertemuan->absensis as $absensi)
                <tr class="status-{{ $absensi->status }}">
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td>{{ $absensi->siswa->nis }}</td>
                    <td>{{ $absensi->siswa->nama }}</td>
                    <td style="text-align: center; font-weight: bold;">
                        {{ strtoupper($absensi->status) }}
                    </td>
                    <td>{{ $absensi->keterangan ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
        <p style="margin-top: 40px;">
            <strong>{{ $guru->name }}</strong><br>
            NIP: {{ $guru->nip }}
        </p>
    </div>
</body>
</html>