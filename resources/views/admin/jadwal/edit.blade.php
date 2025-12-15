@extends('layouts.app')

@section('content')
    <div class="py-4">
        <h2 class="mb-4">Tambah Jadwal Baru</h2>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.jadwal.update', $jadwal) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">Kelas <span class="text-danger">*</span></label>
                                <select name="kelas_id" class="form-select @error('kelas_id') is-invalid @enderror"
                                    required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($kelas as $k)
                                        <option value="{{ $k->id }}" {{ old('kelas_id', $jadwal->kelas_id) == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kelas_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Mata Pelajaran <span class="text-danger">*</span></label>
                                <select name="mata_pelajaran_id"
                                id="mata_pelajaran"
                                    class="form-select @error('mata_pelajaran_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Mata Pelajaran --</option>
                                    @foreach($mapels as $mapel)
                                        <option value="{{ $mapel->id }}"  
                                        data-guru-id="{{ $mapel->guru_id }}"
                                        data-guru-nama="{{ $mapel->guru->name ?? '-' }}" 
                                        {{ old('mata_pelajaran_id', $jadwal->mata_pelajaran_id) == $mapel->id ? 'selected' : '' }}>
                                    {{ $mapel->nama_mapel }} ({{ $mapel->kode_mapel }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('mata_pelajaran_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Guru Pengajar</label>
                                <input type="text" id="guru_nama" class="form-control" readonly>
                            </div>

                            <input type="hidden" name="guru_id" id="guru_id">

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Hari <span class="text-danger">*</span></label>
                                    <select name="hari" class="form-select @error('hari') is-invalid @enderror" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="Senin" {{ old('hari', $jadwal->hari) == 'Senin' ? 'selected' : '' }}>Senin</option>
                                        <option value="Selasa" {{ old('hari', $jadwal->hari) == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                                        <option value="Rabu" {{ old('hari', $jadwal->hari) == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                                        <option value="Kamis" {{ old('hari', $jadwal->hari) == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                                        <option value="Jumat" {{ old('hari', $jadwal->hari) == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                                        <option value="Sabtu" {{ old('hari', $jadwal->hari) == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                                    </select>
                                    @error('hari')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                                    <input type="time" name="jam_mulai"
                                        class="form-control @error('jam_mulai') is-invalid @enderror"
                                        value="{{ old('jam_mulai', $jadwal->jam_mulai) }}" required>
                                    @error('jam_mulai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                                    <input type="time" name="jam_selesai"
                                        class="form-control @error('jam_selesai') is-invalid @enderror"
                                        value="{{ old('jam_selesai', $jadwal->jam_selesai) }}" required>
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
                                <a href="{{ route('admin.jadwal.index') }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="alert alert-info">
                    <h6><i class="bi bi-info-circle"></i> Catatan:</h6>
                    <ul class="small mb-0">
                        <li>Sistem akan otomatis mengecek bentrok jadwal</li>
                        <li>Pastikan waktu tidak overlap dengan jadwal lain</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Javascript to sync data-guru-id and data-guru-nama on jadwal mapel -->
    <script>
document.addEventListener('DOMContentLoaded', function () {
    const mapelSelect = document.getElementById('mata_pelajaran');
    const guruNamaInput = document.getElementById('guru_nama');
    const guruIdInput = document.getElementById('guru_id');

    function syncGuru() {
        const selected = mapelSelect.options[mapelSelect.selectedIndex];
        guruNamaInput.value = selected.getAttribute('data-guru-nama') || '';
        guruIdInput.value = selected.getAttribute('data-guru-id') || '';
    }

    mapelSelect.addEventListener('change', syncGuru);

    // PENTING: untuk halaman edit
    if (mapelSelect.value) {
        syncGuru();
    }
});
</script>


@endsection