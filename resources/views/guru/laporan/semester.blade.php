@extends('layouts.app')

@section('content')
<div class="py-4">
    <h2 class="mb-4">Laporan Kehadiran Semester</h2>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Semester</label>
                    <select name="semester" class="form-select" onchange="this.form.submit()">
                        <option value="ganjil" {{ $semester == 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="genap" {{ $semester == 'genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tahun Ajaran</label>
                    <select name="tahun_ajaran" class="form-select" onchange="this.form.submit()">
                        @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ $tahunAjaran == $y ? 'selected' : '' }}>
                            {{ $y }}/{{ $y + 1 }}
                        </option>
                        @endfor
                    </select>
                </div>
            </form>
        </div>
    </div>

    @if(!empty($rekap))
    <div class="card mb-3">
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3">
                    <h4 class="text-success">{{ array_sum(array_column($rekap, 'hadir')) }}</h4>
                    <small>Total Hadir</small>
                </div>
                <div class="col-md-3">
                    <h4 class="text-warning">{{ array_sum(array_column($rekap, 'izin')) }}</h4>
                    <small>Total Izin</small>
                </div>
                <div class="col-md-3">
                    <h4 class="text-info">{{ array_sum(array_column($rekap, 'sakit')) }}</h4>
                    <small>Total Sakit</small>
                </div>
                <div class="col-md-3">
                    <h4 class="text-danger">{{ array_sum(array_column($rekap, 'alpa')) }}</h4>
                    <small>Total Alpa</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Rekap Kehadiran Siswa - Semester {{ ucfirst($semester) }}</h5>
            <button onclick="window.print()" class="btn btn-light btn-sm">
                <i class="bi bi-printer"></i> Cetak
            </button>
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
                            <th class="text-center">Persentase</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekap as $data)
                        @php
                            $persentase = $data['total'] > 0 ? 
                                round(($data['hadir'] / $data['total']) * 100, 1) : 0;
                            $status = $persentase >= 80 ? 'Baik' : ($persentase >= 60 ? 'Cukup' : 'Kurang');
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
                            <td class="text-center">
                                <span class="badge {{ $persentase >= 80 ? 'bg-success' : ($persentase >= 60 ? 'bg-warning' : 'bg-danger') }}">
                                    {{ $status }}
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
        Tidak ada data kehadiran untuk semester yang dipilih
    </div>
    @endif
</div>

<style>
@media print {
    .sidebar, .btn, .no-print {
        display: none !important;
    }
}
</style>
@endsection