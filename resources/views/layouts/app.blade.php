<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sistem Absensi') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        :root {
            --bs-body-font-family: "Poppins", sans-serif;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            @auth
                <nav class="col-md-2 d-md-block sidebar p-0 100vh">
                    <div class="position-sticky pt-3">
                        <div class="text-center mb-4">
                            <h4 class="text-white">📚 Absensi</h4>
                            <small class="text-white-50">{{ auth()->user()->name }}</small>
                            <br>
                            <span class="badge bg-light text-dark">{{ ucfirst(auth()->user()->role) }}</span>
                        </div>

                        <ul class="nav flex-column px-2">
                            <li class="nav-item">
                                <a class="nav-link rounded" href="{{ route('home') }}">
                                    <i class="bi bi-house-door"></i> Dashboard
                                </a>
                            </li>

                            @if(auth()->user()->isAdmin())
                                <li class="nav-item">
                                    <a class="nav-link rounded" href="{{ route('admin.guru.index') }}">
                                        <i class="bi bi-person-badge"></i> Data Guru
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link rounded" href="{{ route('admin.kelas.index') }}">
                                        <i class="bi bi-people"></i> Data Kelas
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link rounded" href="{{ route('admin.mapel.index') }}">
                                        <i class="bi bi-book"></i> Mata Pelajaran
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link rounded" href="{{ route('admin.jadwal.index') }}">
                                        <i class="bi bi-book"></i> jadwal Mengajar
                                    </a>
                                </li>
                            @endif

                            @if(auth()->user()->isGuru())
                                <li class="nav-item">
                                    <a class="nav-link rounded" href="{{ route('guru.absensi.index') }}">
                                        <i class="bi bi-check-circle"></i> Absensi
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link rounded" href="{{ route('guru.laporan.harian') }}">
                                        <i class="bi bi-calendar-day"></i> Laporan Harian
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link rounded" href="{{ route('guru.laporan.bulanan') }}">
                                        <i class="bi bi-calendar-month"></i> Laporan Bulanan
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link rounded" href="{{ route('guru.laporan.semester') }}">
                                        <i class="bi bi-calendar-range"></i> Laporan Semester
                                    </a>
                                </li>
                            @endif

                            <li class="nav-item mt-3 bg-danger rounded">
                                <a class="nav-link rounded" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); confirmLogout();">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>

                            <script>
                                function confirmLogout() {
                                    if (confirm('Are you sure you want to log out?')) {
                                        document.getElementById('logout-form').submit();
                                    }
                                }
                            </script>
                        </ul>
                    </div>
                </nav>
            @endauth

            <main class="col-md-10 ms-sm-auto px-md-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>

</html>