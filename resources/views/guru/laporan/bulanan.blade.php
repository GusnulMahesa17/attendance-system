@extends('layouts.app')

@section('content')
<div class="py-4">
    <h2 class="mb-4">Laporan Kehadiran Bulanan</h2>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-select" onchange="this.form.submit()">
                        @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($i)->format('F') }}
                        </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-select" onchange="this.form.submit()">
                        @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                        @endfor
                    </select>
                </div>
            </form>
        </div>
    </div>

    @if(!empty($rekap))
    <div class="card">
        <div class="card-header bg-primary text-white">
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
                            <th>Kelas</th>
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
                            <td>{{ $data['siswa']->kelas->nama_kelas }}</td>
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
</div>
@endsection