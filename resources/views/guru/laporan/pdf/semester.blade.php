<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Semester</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
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
            background-color: #2196F3;
            color: white;
            padding: 8px 4px;
            font-weight: bold;
            font-size: 9px;
        }

        td {
            padding: 5px 3px;
            font-size: 9px;
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
            margin-top: 40px;
        }

        .summary-box {
            border: 2px solid #2196F3;
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

        .catatan-box {
            border: 1px solid #ccc;
            padding: 8px;
            margin-top: 15px;
            background-color: #fffef0;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Rekap Kehadiran Semester {{ strtoupper($semester) }}</h2>
        <p style="margin: 5px 0; font-size: 12px;">{{ $jadwal->mataPelajaran->nama_mapel }} -
            {{ $jadwal->kelas->nama_kelas }}
        </p>
        <p style="margin: 5px 0; font-size: 11px;">Tahun Ajaran {{ $tahunAjaran }}/{{ $tahunAjaran + 1 }}</p>
    </div>

    <table class="info-table" style="border: none;">
        <tr>
            <td style="width: 150px; border: none;"><strong>Mata Pelajaran</strong></td>
            <td style="border: none;">: {{ $jadwal->mataPelajaran->nama_mapel }}</td>
            <td style="width: 120px; border: none;"><strong>Semester</strong></td>
            <td style="border: none;">: {{ ucfirst($semester) }}</td>
        </tr>
        <tr>
            <td style="border: none;"><strong>Kelas</strong></td>
            <td style="border: none;">: {{ $jadwal->kelas->nama_kelas }}</td>
            <td style="border: none;"><strong>Tahun Ajaran</strong></td>
            <td style="border: none;">: {{ $tahunAjaran }}/{{ $tahunAjaran + 1 }}</td>
        </tr>
        <tr>
            <td style="border: none;"><strong>Guru Pengampu</strong></td>
            <td style="border: none;">: {{ $guru->name }}</td>
            <td style="border: none;"><strong>Total Pertemuan</strong></td>
            <td style="border: none;">: {{ $pertemuans->count() }} kali</td>
        </tr>
        <tr>
            <td style="border: none;"><strong>NIP</strong></td>
            <td style="border: none;">: {{ $guru->nip }}</td>
            <td style="border: none;"><strong>Jumlah Siswa</strong></td>
            <td style="border: none;">: {{ count($rekap) }} orang</td>
        </tr>
    </table>

    <div class="summary-box">
        <strong>RINGKASAN KEHADIRAN SEMESTER:</strong><br><br>
        <span class="summary-item">Total Hadir: {{ array_sum(array_column($rekap, 'hadir')) }}</span>
        <span class="summary-item">Total Izin: {{ array_sum(array_column($rekap, 'izin')) }}</span>
        <span class="summary-item">Total Sakit: {{ array_sum(array_column($rekap, 'sakit')) }}</span>
        <span class="summary-item">Total Alpa: {{ array_sum(array_column($rekap, 'alpa')) }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 3%;">No</th>
                <th rowspan="2" style="width: 10%;">NIS</th>
                <th rowspan="2" style="width: 25%;">Nama Siswa</th>
                <th colspan="4" style="text-align: center;">Rekapitulasi Kehadiran</th>
                <th rowspan="2" style="width: 6%;">Total</th>
                <th rowspan="2" style="width: 7%;">%<br>Hadir</th>
                <th rowspan="2" style="width: 8%;">Status</th>
                <th rowspan="2" style="width: 15%;">Catatan</th>
            </tr>
            <tr>
                <th style="width: 5%;">H</th>
                <th style="width: 5%;">I</th>
                <th style="width: 5%;">S</th>
                <th style="width: 5%;">A</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($rekap as $data)
                @php
                    $persentase = $data['total'] > 0 ?
                        round(($data['hadir'] / $data['total']) * 100, 1) : 0;
                    $status = $persentase >= 80 ? 'Baik' : ($persentase >= 60 ? 'Cukup' : 'Kurang');

                    // Catatan otomatis
                    $catatan = '';
                    if ($data['alpa'] >= 3) {
                        $catatan = 'Perlu perhatian (Alpa ≥ 3x)';
                    } elseif ($persentase < 60) {
                        $catatan = 'Kehadiran rendah';
                    } elseif ($persentase >= 90) {
                        $catatan = 'Sangat baik';
                    }
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
                    <td class="text-center"><strong>{{ $status }}</strong></td>
                    <td style="font-size: 8px;">{{ $catatan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="catatan-box">
        <strong>Keterangan Status:</strong><br>
        <ul style="margin: 5px 0; padding-left: 20px; font-size: 9px;">
            <li><strong>Baik:</strong> Persentase kehadiran ≥ 80%</li>
            <li><strong>Cukup:</strong> Persentase kehadiran 60% - 79%</li>
            <li><strong>Kurang:</strong> Persentase kehadiran < 60%</li>
        </ul>
        <p style="margin: 5px 0; font-size: 9px;"><strong>Singkatan:</strong> H = Hadir, I = Izin, S = Sakit, A =
            Alpa/Tanpa Keterangan</p>
    </div>

    <div class="footer">
        <div style="float: left; width: 50%;">
            <p><em>Dicetak pada: {{ now()->format('d F Y H:i') }} WIB</em></p>
            <br>
            <p><strong>Catatan Guru:</strong></p>
            <p style="border-top: 1px solid #333; width: 80%; margin-top: 5px;"></p>
            <p style="border-top: 1px solid #333; width: 80%; margin-top: 10px;"></p>
            <p style="border-top: 1px solid #333; width: 80%; margin-top: 10px;"></p>
        </div>

        <table style="width:96%; margin:40px auto 0 auto; border:none;">
            <tr>
                <td style="width:48%; text-align:left; border:none; padding-left:10px;">
                    <p>Mengetahui,</p>
                    <p>Kepala Sekolah</p>
                    <br><br><br>
                    <p><strong><u>_______________________</u></strong></p>
                    <p>NIP: _______________________</p>
                </td>
                <td style="width:48%; text-align:right; border:none; padding-right:10px;">
                    <p>Jakarta, {{ now()->format('d F Y') }}</p>
                    <p>Guru Mata Pelajaran,</p>
                    <br><br><br>
                    <p><strong><u>{{ $guru->name }}</u></strong></p>
                    <p>NIP: {{ $guru->nip }}</p>
                </td>
            </tr>
        </table>

    </div>
</body>

</html>