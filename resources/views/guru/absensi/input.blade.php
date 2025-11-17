@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="mb-4">
        <h2>Input Absensi</h2>
        <div class="card">
            <div class="card-body">
                <h5>{{ $pertemuan->jadwal->mataPelajaran->nama_mapel }}</h5>
                <p class="mb-1">Kelas: {{ $pertemuan->jadwal->kelas->nama_kelas }}</p>
                <p class="mb-1">Pertemuan ke-{{ $pertemuan->pertemuan_ke }}</p>
                <p class="mb-0">Tanggal: {{ $pertemuan->tanggal->format('d F Y') }}</p>
            </div>
        </div>
    </div>

    <form action="{{ route('guru.absensi.update', $pertemuan) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Deskripsi Pertemuan</h5>
            </div>
            <div class="card-body">
                <textarea name="deskripsi" class="form-control" rows="3" 
                          placeholder="Tuliskan materi atau aktivitas pembelajaran hari ini...">{{ $pertemuan->deskripsi }}</textarea>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Daftar Kehadiran Siswa</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 15%">NIS</th>
                                <th style="width: 25%">Nama Siswa</th>
                                <th style="width: 20%">Status</th>
                                <th style="width: 35%">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pertemuan->absensis as $absensi)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $absensi->siswa->nis }}</td>
                                <td>{{ $absensi->siswa->nama }}</td>
                                <td>
                                    <input type="hidden" name="absensi[{{ $loop->index }}][id]" value="{{ $absensi->id }}">
                                    <select name="absensi[{{ $loop->index }}][status]" class="form-select form-select-sm">
                                        <option value="hadir" {{ $absensi->status == 'hadir' ? 'selected' : '' }}>
                                            ✓ Hadir
                                        </option>
                                        <option value="izin" {{ $absensi->status == 'izin' ? 'selected' : '' }}>
                                            📝 Izin
                                        </option>
                                        <option value="sakit" {{ $absensi->status == 'sakit' ? 'selected' : '' }}>
                                            🏥 Sakit
                                        </option>
                                        <option value="alpa" {{ $absensi->status == 'alpa' ? 'selected' : '' }}>
                                            ✗ Alpa
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="absensi[{{ $loop->index }}][keterangan]" 
                                           class="form-control form-control-sm" 
                                           value="{{ $absensi->keterangan }}"
                                           placeholder="Keterangan (opsional)">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-save"></i> Simpan Absensi
            </button>
            <a href="{{ route('guru.absensi.index') }}" class="btn btn-secondary btn-lg">Kembali</a>
        </div>
    </form>
</div>
@endsection