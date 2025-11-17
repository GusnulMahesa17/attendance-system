@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Jadwal Kelas: {{ $kelas->nama_kelas }}</h2>
            <p class="text-muted mb-0">Tahun Ajaran {{ $kelas->tahun_ajaran }}</p>
        </div>
        <a href="{{ route('admin.jadwal.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>Hari</th>
                            <th>Mata Pelajaran</th>
                            <th>Guru</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $hari_list = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                        @endphp
                        
                        @foreach($hari_list as $hari)
                            @if(isset($jadwals[$hari]) && $jadwals[$hari]->count() > 0)
                                @foreach($jadwals[$hari] as $index => $jadwal)
                                <tr>
                                    @if($index == 0)
                                    <td rowspan="{{ $jadwals[$hari]->count() }}" class="align-middle">
                                        <strong>{{ $hari }}</strong>
                                    </td>
                                    @endif
                                    <td>{{ $jadwal->mataPelajaran->nama_mapel }}</td>
                                    <td>{{ $jadwal->guru->name }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td><strong>{{ $hari }}</strong></td>
                                    <td colspan="3" class="text-center text-muted">Tidak ada jadwal</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection