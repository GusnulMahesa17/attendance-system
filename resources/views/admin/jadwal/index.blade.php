@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Jadwal Mengajar</h2>
            <p class="text-muted mb-0">Kelola jadwal pelajaran untuk setiap kelas</p>
        </div>
        <a href="{{ route('admin.jadwal.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Jadwal
        </a>
    </div>

    <!-- Quick Links -->
    <div class="card mb-3">
        <div class="card-body">
            <h6>Lihat Jadwal per Kelas:</h6>
            <div class="d-flex flex-wrap gap-2">
                @foreach(\App\Models\Kelas::all() as $k)
                <a href="{{ route('admin.jadwal.by-kelas', $k) }}" class="btn btn-sm btn-outline-primary">
                    {{ $k->nama_kelas }}
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Guru</th>
                            <th>Hari</th>
                            <th>Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwals as $jadwal)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $jadwal->kelas->nama_kelas }}</span>
                            </td>
                            <td>{{ $jadwal->mataPelajaran->nama_mapel }}</td>
                            <td>
                                <i class="bi bi-person-badge"></i> {{ $jadwal->guru->name }}
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $jadwal->hari }}</span>
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.jadwal.edit', $jadwal) }}" 
                                       class="btn btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.jadwal.destroy', $jadwal) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-calendar-x display-1 text-muted"></i>
                                <p class="text-muted mt-3">Belum ada jadwal</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $jadwals->links() }}
        </div>
    </div>
</div>
@endsection