@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="mb-4">
        <h2>Tambah Mata Pelajaran Baru</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.mapel.index') }}">Mata Pelajaran</a>
                </li>
                <li class="breadcrumb-item active">Tambah Baru</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-book"></i> Form Mata Pelajaran</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.mapel.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Kode Mata Pelajaran <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="kode_mapel" 
                                       class="form-control @error('kode_mapel') is-invalid @enderror" 
                                       value="{{ old('kode_mapel') }}" 
                                       placeholder="Contoh: MAT-01"
                                       required>
                                @error('kode_mapel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Kode unik untuk identifikasi mata pelajaran</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Nama Mata Pelajaran <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="nama_mapel" 
                                       class="form-control @error('nama_mapel') is-invalid @enderror" 
                                       value="{{ old('nama_mapel') }}" 
                                       placeholder="Contoh: Matematika"
                                       required>
                                @error('nama_mapel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Guru Pengampu</label>
                                <select name="guru_id" class="form-select @error('guru_id') is-invalid @enderror">
                                    <option value="">-- Pilih Guru --</option>
                                    @foreach($gurus as $guru)
                                    <option value="{{ $guru->id }}" {{ old('guru_id') == $guru->id ? 'selected' : '' }}>
                                        {{ $guru->name }} ({{ $guru->nip }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('guru_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Opsional - bisa diisi nanti</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Durasi (Menit) <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       name="durasi_menit" 
                                       class="form-control @error('durasi_menit') is-invalid @enderror" 
                                       value="{{ old('durasi_menit', 90) }}" 
                                       min="30"
                                       step="15"
                                       required>
                                @error('durasi_menit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Lama waktu per pertemuan (biasanya 90 menit)</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Hari</label>
                                <select name="hari" class="form-select @error('hari') is-invalid @enderror">
                                    <option value="">-- Pilih Hari --</option>
                                    <option value="Senin" {{ old('hari') == 'Senin' ? 'selected' : '' }}>Senin</option>
                                    <option value="Selasa" {{ old('hari') == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                                    <option value="Rabu" {{ old('hari') == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                                    <option value="Kamis" {{ old('hari') == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                                    <option value="Jumat" {{ old('hari') == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                                    <option value="Sabtu" {{ old('hari') == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                                </select>
                                @error('hari')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jam Mulai</label>
                                <input type="time" 
                                       name="jam_mulai" 
                                       class="form-control @error('jam_mulai') is-invalid @enderror" 
                                       value="{{ old('jam_mulai') }}">
                                @error('jam_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jam Selesai</label>
                                <input type="time" 
                                       name="jam_selesai" 
                                       class="form-control @error('jam_selesai') is-invalid @enderror" 
                                       value="{{ old('jam_selesai') }}">
                                @error('jam_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan
                            </button>
                            <a href="{{ route('admin.mapel.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm bg-light">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-info-circle"></i> Petunjuk</h5>
                    <hr>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i>
                            <strong>Kode Mapel</strong> harus unik
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i>
                            <strong>Guru Pengampu</strong> bisa diisi nanti
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i>
                            <strong>Durasi</strong> dalam satuan menit
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i>
                            <strong>Hari & Waktu</strong> opsional
                        </li>
                    </ul>

                    <div class="alert alert-info mt-3">
                        <small>
                            <i class="bi bi-lightbulb"></i>
                            Contoh format kode: MAT-01, FIS-02, BIO-01
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection