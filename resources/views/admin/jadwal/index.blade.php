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
                <!-- Informasi Pagination -->
                @if($jadwals->total() > 0)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted">
                            Menampilkan {{ $jadwals->firstItem() }} - {{ $jadwals->lastItem() }} dari {{ $jadwals->total() }}
                            jadwal
                        </div>
                    </div>
                @endif

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
                                    <td>{{ ($jadwals->currentPage() - 1) * $jadwals->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $jadwal->kelas->nama_kelas }}</span>
                                    </td>
                                    <td>{{ $jadwal->mataPelajaran->nama_mapel }}</td>
                                    <td>
                                        <i class="bi bi-person-badge text-success"></i> {{ $jadwal->guru->name }}
                                        <br>
                                        <small class="text-muted">NIP:{{ $jadwal->guru->nip }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $jadwal->hari }}</span>
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.jadwal.edit', $jadwal) }}" class="btn btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.jadwal.destroy', $jadwal) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
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

                <!-- Pagination -->
                @if($jadwals->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Halaman {{ $jadwals->currentPage() }} dari {{ $jadwals->lastPage() }}
                        </div>

                        <nav aria-label="Page navigation">
                            <ul class="pagination mb-0">
                                {{-- Previous Page Link --}}
                                @if($jadwals->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <i class="bi bi-chevron-left"></i> Sebelumnya
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $jadwals->previousPageUrl() }}" rel="prev">
                                            <i class="bi bi-chevron-left"></i> Sebelumnya
                                        </a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach($jadwals->getUrlRange(1, $jadwals->lastPage()) as $page => $url)
                                    @if($page == $jadwals->currentPage())
                                        <li class="page-item active">
                                            <span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if($jadwals->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $jadwals->nextPageUrl() }}" rel="next">
                                            Berikutnya <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            Berikutnya <i class="bi bi-chevron-right"></i>
                                        </span>
                                    </li>
                                @endif
                            </ul>
                        </nav>

                        <div class="text-muted">
                            Total: {{ $jadwals->total() }} data
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Fungsi untuk mengubah jumlah item per halaman
                const perPageSelect = document.getElementById('perPageSelect');
                if (perPageSelect) {
                    perPageSelect.addEventListener('change', function () {
                        const perPage = this.value;
                        const url = new URL(window.location.href);
                        url.searchParams.set('per_page', perPage);
                        window.location.href = url.toString();
                    });
                }
            });
        </script>
    @endpush
@endsection