@extends('layouts.app')

@section('content')
    <div class="py-4">
        <h2 class="mb-4">Laporan Kehadiran Harian</h2>

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Pilih Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Pilih Kelas</label>
                        <select name="kelas_id" class="form-control">
                            <option value="">Semua Kelas</option>
                            @foreach($kelas_list as $kelas)
                                <option value="{{ $kelas->id }}" {{ $kelas_id == $kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 align-self-end">
                        <button class="btn btn-primary w-100">
                            Tampilkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @foreach($pertemuans as $pertemuan)
            <div class="card mb-3">


                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <!-- Kiri: Judul -->
                        <div>
                            <h5 class="mb-0">
                                {{ $pertemuan->jadwal->mataPelajaran->nama_mapel }} -
                                {{ $pertemuan->jadwal->kelas->nama_kelas }}
                            </h5>
                            <small>Pertemuan ke-{{ $pertemuan->pertemuan_ke }}</small>
                        </div>

                        <!-- Kanan: Tombol -->
                        <div class="d-flex gap-2">
                            <a href="{{ route('guru.laporan.harian.pdf', [
                'tanggal' => $tanggal,
                'kelas_id' => $kelas_id
            ]) }}" class="btn btn-danger btn-sm">
                                Export PDF
                            </a>

                            <a href="{{ route('guru.absensi.input', $pertemuan) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil-square"></i> Edit Absensi
                            </a>
                        </div>
                    </div>
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

                    <!-- Detail Absensi -->
                    <div class="mt-3">
                        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse"
                            data-bs-target="#detail-{{ $pertemuan->id }}">
                            <i class="bi bi-eye"></i> Lihat Detail Siswa
                        </button>

                        <div class="collapse mt-2" id="detail-{{ $pertemuan->id }}">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 5%">#</th>
                                            <th style="width: 15%">NIS</th>
                                            <th style="width: 35%">Nama Siswa</th>
                                            <th style="width: 15%">Status</th>
                                            <th style="width: 30%">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pertemuan->absensis as $absensi)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $absensi->siswa->nis }}</td>
                                                <td>{{ $absensi->siswa->nama }}</td>
                                                <td>
                                                    @if($absensi->status == 'hadir')
                                                        <span class="badge bg-success">✓ Hadir</span>
                                                    @elseif($absensi->status == 'izin')
                                                        <span class="badge bg-warning">📝 Izin</span>
                                                    @elseif($absensi->status == 'sakit')
                                                        <span class="badge bg-info">🏥 Sakit</span>
                                                    @else
                                                        <span class="badge bg-danger">✗ Alpa</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small>{{ $absensi->keterangan ?? '-' }}</small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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