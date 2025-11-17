@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="mb-4">
        <h2>Buat Pertemuan Baru</h2>
        <p class="text-muted">
            {{ $jadwal->mataPelajaran->nama_mapel }} - {{ $jadwal->kelas->nama_kelas }}
        </p>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('guru.absensi.pertemuan.store', $jadwal) }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Pertemuan</label>
                        <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" 
                               value="{{ old('tanggal', date('Y-m-d')) }}" required>
                        @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pertemuan Ke</label>
                        <input type="number" name="pertemuan_ke" class="form-control @error('pertemuan_ke') is-invalid @enderror" 
                               value="{{ old('pertemuan_ke', $pertemuanKe) }}" min="1" required>
                        @error('pertemuan_ke')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Semester</label>
                        <select name="semester" class="form-select @error('semester') is-invalid @enderror" required>
                            <option value="ganjil" {{ old('semester') == 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="genap" {{ old('semester') == 'genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                        @error('semester')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tahun Ajaran</label>
                        <input type="number" name="tahun_ajaran" class="form-control @error('tahun_ajaran') is-invalid @enderror" 
                               value="{{ old('tahun_ajaran', date('Y')) }}" min="2020" max="2030" required>
                        @error('tahun_ajaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Deskripsi / Materi Pembelajaran</label>
                        <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" 
                                  rows="4" required placeholder="Contoh: Pengenalan konsep integral tak tentu, latihan soal...">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> 
                    Setelah pertemuan dibuat, sistem akan otomatis membuatkan daftar absensi untuk semua siswa di kelas 
                    <strong>{{ $jadwal->kelas->nama_kelas }}</strong> ({{ $jadwal->kelas->siswas->count() }} siswa).
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Buat Pertemuan & Input Absensi
                    </button>
                    <a href="{{ route('guru.absensi.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection