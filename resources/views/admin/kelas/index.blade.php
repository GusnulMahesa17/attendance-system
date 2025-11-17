@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Data Kelas</h2>
        <a href="{{ route('admin.kelas.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Kelas
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Kelas</th>
                        <th>Tingkat</th>
                        <th>Jurusan</th>
                        <th>Tahun Ajaran</th>
                        <th>Jumlah Siswa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kelas as $k)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $k->nama_kelas }}</td>
                        <td>{{ $k->tingkat }}</td>
                        <td>{{ $k->jurusan ?? '-' }}</td>
                        <td>{{ $k->tahun_ajaran }}</td>
                        <td>
                            <span class="badge bg-info">{{ $k->siswas_count }} siswa</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.kelas.show', $k) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.kelas.edit', $k) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.kelas.destroy', $k) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Yakin ingin menghapus?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada data kelas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $kelas->links() }}
        </div>
    </div>
</div>
@endsection