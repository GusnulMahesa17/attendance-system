@extends('layouts.app')

@section('content')
<div class="py-4">
    <h2 class="mb-4">Laporan Kehadiran Bulanan</h2>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-select">
                        @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($i)->format('F') }}
                        </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-select">
                        @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Pilih Jadwal (Mata Pelajaran & Kelas)</label>
                    <select name="jadwal_id" class="form-select">
                        <option value="">-- Semua Jadwal --</option>
                        @foreach($jadwal_list as $j)
                        <option value="{{ $j->id }}" {{ $jadwal_id == $j->id ? 'selected' : '' }}>
                            {{ $j->mataPelajaran->nama_mapel }} - {{ $j->kelas->nama_kelas }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    @if($jadwal_id)
                    <a href="{{ route('guru.laporan.bulanan.pdf', ['bulan' => $bulan, 'tahun' => $tahun, 'jadwal_id' => $jadwal_id]) }}" 
                       class="btn btn-danger flex-fill" target="_blank">
                        <i class="bi bi-file-pdf"></i> Export PDF
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if(isset($jadwal))
        {{-- Tampilan untuk jadwal spesifik --}}
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    {{ $jadwal->mataPelajaran->nama_mapel }} - {{ $jadwal->kelas->nama_kelas }}
                </h5>
                <small>Total Pertemuan: {{ $pertemuans->count() }}</small>
            </div>
        </div>

        @if(!empty($rekap))
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Rekap Kehadiran Siswa</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th class="text-center">Hadir</th>
                                <th class="text-center">Izin</th>
                                <th class="text-center">Sakit</th>
                                <th class="text-center">Alpa</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rekap as $data)
                            @php
                                $persentase = $data['total'] > 0 ? 
                                    round(($data['hadir'] / $data['total']) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $data['siswa']->nis }}</td>
                                <td>{{ $data['siswa']->nama }}</td>
                                <td class="text-center">
                                    <span class="badge bg-success">{{ $data['hadir'] }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning">{{ $data['izin'] }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $data['sakit'] }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger">{{ $data['alpa'] }}</span>
                                </td>
                                <td class="text-center">
                                    <strong>{{ $data['total'] }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $persentase >= 80 ? 'bg-success' : ($persentase >= 60 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ $persentase }}%
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        <div class="alert alert-info">
            Tidak ada data kehadiran untuk periode yang dipilih
        </div>
        @endif

    @elseif(isset($allRekap) && count($allRekap) > 0)
        {{-- Tampilan untuk semua jadwal --}}
        @foreach($allRekap as $item)
        <div class="card mb-3">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">
                        {{ $item['jadwal']->mataPelajaran->nama_mapel }} - {{ $item['jadwal']->kelas->nama_kelas }}
                    </h5>
                    <small>Total Pertemuan: {{ $item['pertemuans']->count() }}</small>
                </div>
                <a href="{{ route('guru.laporan.bulanan', ['bulan' => $bulan, 'tahun' => $tahun, 'jadwal_id' => $item['jadwal']->id]) }}" 
                   class="btn btn-light btn-sm">
                    <i class="bi bi-eye"></i> Lihat Detail
                </a>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-3">
                        <h4 class="text-success">{{ array_sum(array_column($item['rekap'], 'hadir')) }}</h4>
                        <small>Total Hadir</small>
                    </div>
                    <div class="col-3">
                        <h4 class="text-warning">{{ array_sum(array_column($item['rekap'], 'izin')) }}</h4>
                        <small>Total Izin</small>
                    </div>
                    <div class="col-3">
                        <h4 class="text-info">{{ array_sum(array_column($item['rekap'], 'sakit')) }}</h4>
                        <small>Total Sakit</small>
                    </div>
                    <div class="col-3">
                        <h4 class="text-danger">{{ array_sum(array_column($item['rekap'], 'alpa')) }}</h4>
                        <small>Total Alpa</small>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

    @else
        <div class="alert alert-info">
            Tidak ada data kehadiran untuk periode yang dipilih
        </div>
    @endif
</div>
@endsection
