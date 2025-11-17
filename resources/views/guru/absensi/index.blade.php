@extends('layouts.app')

@section('content')
<div class="py-4">
    <h2 class="mb-4">Jadwal Mengajar</h2>

    <div class="row">
        @foreach($jadwals as $jadwal)
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="text-white mb-0">{{ $jadwal->mataPelajaran->nama_mapel }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-people-fill text-primary"></i>
                        <strong>Kelas:</strong> {{ $jadwal->kelas->nama_kelas }}
                    </div>
                    <div class="mb-3">
                        <i class="bi bi-calendar-week text-success"></i>
                        <strong>Hari:</strong> {{ $jadwal->hari }}
                    </div>
                    <div class="mb-3">
                        <i class="bi bi-clock text-warning"></i>
                        <strong>Waktu:</strong> 
                        {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                    </div>
                    <div class="mb-3">
                        <i class="bi bi-list-ol text-info"></i>
                        <strong>Total Pertemuan:</strong> 
                        <span class="badge bg-info">{{ $jadwal->pertemuans->count() }}</span>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('guru.absensi.pertemuan.create', $jadwal) }}" 
                       class="btn btn-primary w-100">
                        <i class="bi bi-plus-circle"></i> Buat Pertemuan Baru
                    </a>
                    
                    @if($jadwal->pertemuans->count() > 0)
                    <div class="mt-2">
                        <small class="text-muted">Pertemuan terakhir: 
                            {{ $jadwal->pertemuans->sortByDesc('pertemuan_ke')->first()->tanggal->format('d/m/Y') }}
                        </small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach

        @if($jadwals->isEmpty())
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Belum ada jadwal mengajar yang ditugaskan.
            </div>
        </div>
        @endif
    </div>
</div>
@endsection