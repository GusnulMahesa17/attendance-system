@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="mb-4">
        <h2>Edit Mata Pelajaran</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.mapel.index') }}">Mata Pelajaran</a>
                </li>
                <li class="breadcrumb-item active">Edit: {{ $mapel->nama_mapel }}</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="bi bi-pencil"></i> Form Edit Mata Pelajaran</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.mapel.update', $mapel) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Kode Mata Pelajaran <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="kode_mapel" 
                                       class="form-control @error('kode_mapel') is-invalid @enderror" 
                                       value="{{ old('kode_mapel', $mapel->kode_mapel) }}" 
                                       required>
                                @error('kode_mapel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Nama Mata Pelajaran <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="nama_mapel" 
                                       class="form-control @error('nama_mapel') is-invalid @enderror" 
                                       value="{{ old('nama_mapel', $mapel->nama_mapel) }}" 
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
                                    <option value="{{ $guru->id }}" 
                                            {{ old('guru_id', $mapel->guru_id) == $guru->id ? 'selected' : '' }}>
                                        {{ $guru->name }} ({{ $guru->nip }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('guru_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Durasi (Menit) <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       name="durasi_menit" 
                                       class="form-control @error('durasi_menit') is-invalid @enderror" 
                                       value="{{ old('durasi_menit', $mapel->durasi_menit) }}" 
                                       min="30"
                                       step="15"
                                       required>
                                @error('durasi_menit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Hari</label>
                                <select name="hari" class="form-select @error('hari') is-invalid @enderror">
                                    <option value="">-- Pilih Hari --</option>
                                    <option value="Senin" {{ old('hari', $mapel->hari) == 'Senin' ? 'selected' : '' }}>Senin</option>
                                    <option value="Selasa" {{ old('hari', $mapel->hari) == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                                    <option value="Rabu" {{ old('hari', $mapel->hari) == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                                    <option value="Kamis" {{ old('hari', $mapel->hari) == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                                    <option value="Jumat" {{ old('hari', $mapel->hari) == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                                    <option value="Sabtu" {{ old('hari', $mapel->hari) == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
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
                                       value="{{ old('jam_mulai', $mapel->jam_mulai ? \Carbon\Carbon::parse($mapel->jam_mulai)->format('H:i') : '') }}">
                                @error('jam_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jam Selesai</label>
                                <input type="time" 
                                       name="jam_selesai" 
                                       class="form-control @error('jam_selesai') is-invalid @enderror" 
                                       value="{{ old('jam_selesai', $mapel->jam_selesai ? \Carbon\Carbon::parse($mapel->jam_selesai)->format('H:i') : '') }}">
                                @error('jam_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save"></i> Update
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
                    <h5 class="card-title"><i class="bi bi-info-circle"></i> Informasi</h5>
                    <hr>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Dibuat:</strong></td>
                            <td>{{ $mapel->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Diupdate:</strong></td>
                            <td>{{ $mapel->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card shadow-sm bg-danger text-white mt-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-exclamation-triangle"></i> Zona Bahaya</h5>
                    <hr>
                    <p class="small">Menghapus mata pelajaran akan menghapus semua data terkait termasuk jadwal dan pertemuan.</p>
                    <form action="{{ route('admin.mapel.destroy', $mapel) }}" 
                          method="POST"
                          onsubmit="return confirm('PERINGATAN!\n\nMenghapus mata pelajaran ini akan menghapus:\n- Semua jadwal terkait\n- Semua pertemuan\n- Semua data absensi\n\nYakin ingin melanjutkan?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-light w-100">
                            <i class="bi bi-trash"></i> Hapus Mata Pelajaran
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection