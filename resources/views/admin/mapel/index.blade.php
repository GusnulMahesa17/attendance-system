@extends('layouts.app')

@section('content')
    <div class="py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Data Mata Pelajaran</h2>
                <p class="text-muted mb-0">Kelola mata pelajaran dan jadwal mengajar</p>
            </div>
            <a href="{{ route('admin.mapel.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Mata Pelajaran
            </a>
        </div>

        <!-- Filter & Search -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.mapel.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama atau kode mapel..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary w-100">
                            <i class="bi bi-search"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.mapel.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%">No</th>
                                <th style="width: 20%">Kode Mapel</th>
                                <th style="width: 25%">Nama Mata Pelajaran</th>
                                <th style="width: 25%">Guru Pengampu</th>
                                <th style="width: 25%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mapels as $mapel)
                                <tr>
                                    <td>{{ ($mapels->currentPage() - 1) * $mapels->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $mapel->kode_mapel }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $mapel->nama_mapel }}</strong>
                                    </td>
                                    <td>
                                        @if($mapel->guru)
                                            <div>
                                                <i class="bi bi-person-badge text-success"></i>
                                                {{ $mapel->guru->name }}
                                            </div>
                                            <small class="text-muted">NIP: {{ $mapel->guru->nip }}</small>
                                        @else
                                            <span class="badge bg-secondary">Belum ada guru</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.mapel.edit', $mapel) }}" class="btn btn-warning" 
                                                title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.mapel.destroy', $mapel) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Yakin ingin menghapus mata pelajaran ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="bi bi-inbox display-1 text-muted"></i>
                                        <p class="text-muted mt-3">Belum ada data mata pelajaran</p>
                                        <a href="{{ route('admin.mapel.create') }}" class="btn btn-primary">
                                            <i class="bi bi-plus-circle"></i> Tambah Mata Pelajaran
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $mapels->links() }}
                </div>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h3>{{ \App\Models\MataPelajaran::count() }}</h3>
                        <p class="mb-0">Total Mata Pelajaran</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h3>{{ \App\Models\MataPelajaran::whereNotNull('guru_id')->count() }}</h3>
                        <p class="mb-0">Sudah Ada Guru</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h3>{{ \App\Models\MataPelajaran::whereNull('guru_id')->count() }}</h3>
                        <p class="mb-0">Belum Ada Guru</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection