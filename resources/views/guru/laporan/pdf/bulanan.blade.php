<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Bulanan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 16px;
            text-transform: uppercase;
        }

        .info-table {
            margin-bottom: 15px;
            width: 100%;
        }

        .info-table td {
            padding: 3px 0;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table,
        th,
        td {
            border: 1px solid #333;
        }

        th {
            background-color: #4CAF50;
            color: white;
            padding: 8px 4px;
            font-weight: bold;
            font-size: 10px;
        }

        td {
            padding: 6px 4px;
            font-size: 10px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .bg-success {
            background-color: #d4edda;
        }

        .bg-warning {
            background-color: #fff3cd;
        }

        .bg-info {
            background-color: #cfe2ff;
        }

        .bg-danger {
            background-color: #f8d7da;
        }

        .footer {
            margin-top: 30px;
            font-size: 10px;
        }

        .footer-signature {
            float: right;
            text-align: center;
            margin-top: 50px;
        }

        .summary-box {
            border: 2px solid #4CAF50;
            padding: 10px;
            margin-bottom: 15px;
            background-color: #f8f9fa;
        }

        .summary-item {
            display: inline-block;
            margin-right: 20px;
            font-weight: bold;
            font-size: 11px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Rekap Kehadiran Bulanan</h2>
        <p style="margin: 5px 0; font-size: 12px;">{{ $jadwal->mataPelajaran->nama_mapel }} -
            {{ $jadwal->kelas->nama_kelas }}</p>
    </div>

    <table class="info-table" style="border: none;">
        <tr>
            <td style="width: 150px; border: none;"><strong>Mata Pelajaran</strong></td>
            <td style="border: none;">: {{ $jadwal->mataPelajaran->nama_mapel }}</td>
            <td style="width: 120px; border: none;"><strong>Bulan/Tahun</strong></td>
            <td style="border: none;">: {{ \Carbon\Carbon::create()->month((int)$bulan)->format('F') }} {{ $tahun }}</td>
        </tr>
        <tr>
            <td style="border: none;"><strong>Kelas</strong></td>
            <td style="border: none;">: {{ $jadwal->kelas->nama_kelas }}</td>
            <td style="border: none;"><strong>Total Pertemuan</strong></td>
            <td style="border: none;">: {{ $pertemuans->count() }} kali</td>
        </tr>
        <tr>
            <td style="border: none;"><strong>Guru Pengampu</strong></td>
            <td style="border: none;">: {{ $guru->name }}</td>
            <td style="border: none;"><strong>NIP</strong></td>
            <td style="border: none;">: {{ $guru->nip }}</td>
        </tr>
    </table>

    <div class="summary-box">
        <strong>RINGKASAN KEHADIRAN:</strong><br><br>
        <span class="summary-item">Total Hadir: {{ array_sum(array_column($rekap, 'hadir')) }}</span>
        <span class="summary-item">Total Izin: {{ array_sum(array_column($rekap, 'izin')) }}</span>
        <span class="summary-item">Total Sakit: {{ array_sum(array_column($rekap, 'sakit')) }}</span>
        <span class="summary-item">Total Alpa: {{ array_sum(array_column($rekap, 'alpa')) }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 4%;">No</th>
                <th rowspan="2" style="width: 12%;">NIS</th>
                <th rowspan="2" style="width: 30%;">Nama Siswa</th>
                <th colspan="4" style="text-align: center;">Rekapitulasi Kehadiran</th>
                <th rowspan="2" style="width: 8%;">Total<br>Pertemuan</th>
                <th rowspan="2" style="width: 8%;">Persentase<br>Hadir</th>
                <th rowspan="2" style="width: 10%;">Keterangan</th>
            </tr>
            <tr>
                <th style="width: 7%;">Hadir<br>(H)</th>
                <th style="width: 7%;">Izin<br>(I)</th>
                <th style="width: 7%;">Sakit<br>(S)</th>
                <th style="width: 7%;">Alpa<br>(A)</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($rekap as $data)
                @php
                    $persentase = $data['total'] > 0 ?
                        round(($data['hadir'] / $data['total']) * 100, 1) : 0;
                    $keterangan = $persentase >= 80 ? 'Baik' : ($persentase >= 60 ? 'Cukup' : 'Kurang');
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $data['siswa']->nis }}</td>
                    <td>{{ $data['siswa']->nama }}</td>
                    <td class="text-center bg-success"><strong>{{ $data['hadir'] }}</strong></td>
                    <td class="text-center bg-warning"><strong>{{ $data['izin'] }}</strong></td>
                    <td class="text-center bg-info"><strong>{{ $data['sakit'] }}</strong></td>
                    <td class="text-center bg-danger"><strong>{{ $data['alpa'] }}</strong></td>
                    <td class="text-center"><strong>{{ $data['total'] }}</strong></td>
                    <td class="text-center"><strong>{{ $persentase }}%</strong></td>
                    <td class="text-center">{{ $keterangan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div style="float: left; width: 50%;">
            <p><strong>Keterangan:</strong></p>
            <ul style="margin: 5px 0; padding-left: 20px;">
                <li>H = Hadir</li>
                <li>I = Izin</li>
                <li>S = Sakit</li>
                <li>A = Alpa/Tanpa Keterangan</li>
            </ul>
            <p style="margin-top: 10px;"><em>Dicetak pada: {{ now()->format('d F Y H:i') }} WIB</em></p>
        </div>

        <div class="footer-signature">
            <p>Jakarta, {{ now()->format('d F Y') }}</p>
            <p>Guru Mata Pelajaran,</p>
            <br><br><br>
            <p><strong><u>{{ $guru->name }}</u></strong></p>
            <p>NIP: {{ $guru->nip }}</p>
        </div>
    </div>
</body>

</html>