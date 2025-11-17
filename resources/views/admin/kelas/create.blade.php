@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="mb-4">
        <h2>Tambah Kelas Baru</h2>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.kelas.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label">Nama Kelas</label>
                    <input type="text" name="nama_kelas" class="form-control @error('nama_kelas') is-invalid @enderror" 
                           value="{{ old('nama_kelas') }}" required>
                    @error('nama_kelas')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Tingkat</label>
                    <input type="text" name="tingkat" class="form-control @error('tingkat') is-invalid @enderror" 
                           value="{{ old('tingkat') }}" required>
                    @error('tingkat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Jurusan</label>
                    <input type="text" name="jurusan" class="form-control @error('jurusan') is-invalid @enderror" 
                           value="{{ old('jurusan') }}" required>
                    @error('jurusan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" class="form-control @error('tahun_ajaran') is-invalid @enderror" 
                           value="{{ old('tahun_ajaran') }}" required>
                    @error('tahun_ajaran')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection