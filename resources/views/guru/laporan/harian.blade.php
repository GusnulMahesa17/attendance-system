@extends('layouts.app')

@section('content')
<div class="py-4">
    <h2 class="mb-4">Laporan Kehadiran Harian</h2>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Pilih Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" 
                           value="{{ $tanggal }}" onchange="this.form.submit()">
                </div>
            </form>
        </div>
    </div>

    @foreach($pertemuans as $pertemuan)
    <div class="card mb-3">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                {{ $pertemuan->jadwal->mataPelajaran->nama_mapel }} - 
                {{ $pertemuan->jadwal->kelas->nama_kelas }}
            </h5>
            <small>Pertemuan ke-{{ $pertemuan->pertemuan_ke }}</small>
        </div>
        <div class="card-body">
            <p><strong>Deskripsi:</strong> {{ $pertemuan->deskripsi ?? '-' }}</p>
            
            <div class="row text-center mt-3">
                <div class="col-3">
                    <div class="bg-success bg-opacity-10 p-3 rounded">
                        <h3 class="text-success">
                            {{ $pertemuan->absensis->where('status', 'hadir')->count() }}
                        </h3>
                        <small>Hadir</small>
                    </div>
                </div>
                <div class="col-3">
                    <div class="bg-warning bg-opacity-10 p-3 rounded">
                        <h3 class="text-warning">
                            {{ $pertemuan->absensis->where('status', 'izin')->count() }}
                        </h3>
                        <small>Izin</small>
                    </div>
                </div>
                <div class="col-3">
                    <div class="bg-info bg-opacity-10 p-3 rounded">
                        <h3 class="text-info">
                            {{ $pertemuan->absensis->where('status', 'sakit')->count() }}
                        </h3>
                        <small>Sakit</small>
                    </div>
                </div>
                <div class="col-3">
                    <div class="bg-danger bg-opacity-10 p-3 rounded">
                        <h3 class="text-danger">
                            {{ $pertemuan->absensis->where('status', 'alpa')->count() }}
                        </h3>
                        <small>Alpa</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    @if($pertemuans->isEmpty())
    <div class="alert alert-info">
        Tidak ada pertemuan pada tanggal {{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}
    </div>
    @endif
</div>
@endsection