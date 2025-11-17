@extends('layouts.app')

@section('content')
<div class="py-4">
    <h2 class="mb-4">Dashboard</h2>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <h3>Selamat Datang, {{ auth()->user()->name }}!</h3>
                    <p class="text-muted">Role: <span class="badge bg-primary">{{ ucfirst(auth()->user()->role) }}</span></p>
                    
                    <div class="mt-4">
                        @if(auth()->user()->isAdmin())
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h4>{{ \App\Models\User::where('role', 'guru')->count() }}</h4>
                                        <p>Total Guru</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h4>{{ \App\Models\Kelas::count() }}</h4>
                                        <p>Total Kelas</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h4>{{ \App\Models\Siswa::count() }}</h4>
                                        <p>Total Siswa</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(auth()->user()->isGuru())
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h4>{{ \App\Models\Jadwal::where('guru_id', auth()->id())->count() }}</h4>
                                        <p>Jadwal Mengajar</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h4>{{ \App\Models\Pertemuan::whereHas('jadwal', function($q) {
                                            $q->where('guru_id', auth()->id());
                                        })->count() }}</h4>
                                        <p>Total Pertemuan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection