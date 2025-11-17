@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Data Guru</h2>
        <a href="{{ route('admin.guru.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Guru
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>No. Telepon</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gurus as $guru)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $guru->nip }}</td>
                        <td>{{ $guru->name }}</td>
                        <td>{{ $guru->email }}</td>
                        <td>{{ $guru->phone ?? '-' }}</td>
                        <td>
                            <a href="{{ route('admin.guru.edit', $guru) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.guru.destroy', $guru) }}" method="POST" class="d-inline">
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
                        <td colspan="6" class="text-center">Belum ada data guru</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $gurus->links() }}
        </div>
    </div>
</div>
@endsection