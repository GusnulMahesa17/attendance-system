<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sistem Absensi') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        :root {
            --bs-body-font-family: "Inter", sans-serif;
            --sidebar-width: 280px;
            --header-height: 70px;
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%);
            --primary-color: #6366f1;
            --primary-dark: #4f46e5;
            --bg-light: #f8fafc;
            --bg-white: #ffffff;
            --sidebar-bg: #f1f5f9;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
        }

        body {
            background-color: var(--bg-light);
            overflow-x: hidden;
        }

        /* Header Styles */
        .main-header {
            position: sticky;
            top: 0;
            left: 0;
            right: 0;
            height: var(--header-height);
            background: var(--bg-white);
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            z-index: 1020;
            display: flex;
            align-items: center;
            padding: 0 2rem;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-dark);
            font-size: 1.5rem;
            font-weight: 700;
            text-decoration: none;
        }

        .header-brand i {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hamburger-btn {
            background: transparent;
            border: none;
            padding: 0.5rem;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
            color: var(--text-dark);
        }

        .hamburger-btn:hover {
            background: var(--bg-light);
        }

        .hamburger-btn i {
            font-size: 1.5rem;
        }

        .header-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1rem;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            box-shadow: 2px 0 12px rgba(0, 0, 0, 0.06);
            z-index: 1040;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        /* Sidebar Header */
        .sidebar-header {
            padding: 2rem 1.5rem 1.5rem;
            background: var(--bg-white);
            border-bottom: 1px solid var(--border-color);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-dark);
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
        }

        .sidebar-brand i {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 2rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: var(--bg-light);
            border-radius: 0.75rem;
        }

        .user-info-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .user-info-text {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            color: var(--text-dark);
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 0.25rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            display: inline-block;
            background: var(--primary-gradient);
            color: white;
            padding: 0.15rem 0.6rem;
            border-radius: 0.5rem;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Navigation */
        .sidebar-nav {
            padding: 1.5rem 1rem;
        }

        .nav-section-title {
            color: var(--text-muted);
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0.5rem 1rem;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            margin-bottom: 0.25rem;
            border-radius: 0.75rem;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            position: relative;
        }

        .nav-link i {
            font-size: 1.25rem;
            width: 24px;
            text-align: center;
            transition: transform 0.2s ease;
        }

        .nav-link:hover {
            background: var(--bg-white);
            color: var(--primary-color);
            transform: translateX(4px);
        }

        .nav-link:hover i {
            transform: scale(1.1);
        }

        .nav-link.active {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .nav-link.active i {
            transform: scale(1.1);
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 60%;
            background: white;
            border-radius: 0 4px 4px 0;
        }

        .nav-link.logout {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            margin-top: 1.5rem;
        }

        .nav-link.logout:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            transform: translateX(0) scale(1.02);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        }

        /* Overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 1030;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .content-wrapper {
            padding: 2rem;
            max-width: 100%;
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 1.5rem;
        }

        /* Scrollbar Styling */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 10px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: var(--text-muted);
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .main-header {
                margin-left: 0;
            }

            .header-brand {
                display: none;
            }

            .content-wrapper {
                padding: 1.5rem 1rem;
                overflow-x: auto;
            }

            /* Smooth horizontal scroll for mobile */
            .content-wrapper {
                -webkit-overflow-scrolling: touch;
            }

            .content-wrapper > * {
                min-width: fit-content;
            }
        }

        @media (min-width: 992px) {
            .sidebar-overlay {
                display: none;
            }

            .hamburger-btn {
                display: none;
            }

            .header-brand {
                display: none;
            }
        }

        @media (max-width: 575.98px) {
            .main-header {
                padding: 0 1rem;
            }

            .content-wrapper {
                padding: 1rem 0.75rem;
            }
        }
    </style>
</head>

<body>
    @auth
        <!-- Sidebar Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <i class="bi bi-journal-bookmark-fill"></i>
                    <span>Absensi</span>
                </div>
                <div class="user-info">
                    <div class="user-info-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="user-info-text">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <span class="user-role">{{ ucfirst(auth()->user()->role) }}</span>
                    </div>
                </div>
            </div>

            <div class="sidebar-nav">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    @if(auth()->user()->isAdmin())
                        <div class="nav-section-title">Management</div>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}" href="{{ route('admin.guru.index') }}">
                                <i class="bi bi-person-badge-fill"></i>
                                <span>Data Guru</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}" href="{{ route('admin.kelas.index') }}">
                                <i class="bi bi-people-fill"></i>
                                <span>Data Kelas</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.mapel.*') ? 'active' : '' }}" href="{{ route('admin.mapel.index') }}">
                                <i class="bi bi-book-fill"></i>
                                <span>Mata Pelajaran</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.jadwal.*') ? 'active' : '' }}" href="{{ route('admin.jadwal.index') }}">
                                <i class="bi bi-calendar-week-fill"></i>
                                <span>Jadwal Mengajar</span>
                            </a>
                        </li>
                    @endif

                    @if(auth()->user()->isGuru())
                        <div class="nav-section-title">Aktivitas</div>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('guru.absensi.*') ? 'active' : '' }}" href="{{ route('guru.absensi.index') }}">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Absensi</span>
                            </a>
                        </li>
                        
                        <div class="nav-section-title">Laporan</div>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('guru.laporan.harian') ? 'active' : '' }}" href="{{ route('guru.laporan.harian') }}">
                                <i class="bi bi-calendar-day-fill"></i>
                                <span>Laporan Harian</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('guru.laporan.bulanan') ? 'active' : '' }}" href="{{ route('guru.laporan.bulanan') }}">
                                <i class="bi bi-calendar-month-fill"></i>
                                <span>Laporan Bulanan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('guru.laporan.semester') ? 'active' : '' }}" href="{{ route('guru.laporan.semester') }}">
                                <i class="bi bi-calendar-range-fill"></i>
                                <span>Laporan Semester</span>
                            </a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <a class="nav-link logout" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); confirmLogout();">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header Navbar -->
            <header class="main-header">
                <div class="header-left">
                    <button class="hamburger-btn" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <a href="{{ route('home') }}" class="header-brand">
                        <i class="bi bi-journal-bookmark-fill"></i>
                        <span>Absensi</span>
                    </a>
                </div>
                <div class="header-right">
                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </div>
            </header>

            <!-- Content Wrapper -->
            <div class="content-wrapper">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    @endauth

    @guest
        <div class="content-wrapper">
            @yield('content')
        </div>
    @endguest

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar Toggle Functionality
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function toggleSidebar() {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
            
            if (window.innerWidth < 992) {
                document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
            }
        }

        function closeSidebar() {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', toggleSidebar);
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebar);
        }

        // Close sidebar when clicking nav link on mobile
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    closeSidebar();
                }
            });
        });

        // Handle resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 992) {
                closeSidebar();
            }
        });

        // Logout confirmation
        function confirmLogout() {
            if (confirm('Apakah Anda yakin ingin keluar?')) {
                document.getElementById('logout-form').submit();
            }
        }

        // Set active nav link based on current URL
        document.addEventListener('DOMContentLoaded', () => {
            const currentPath = window.location.pathname;
            document.querySelectorAll('.sidebar .nav-link').forEach(link => {
                if (link.classList.contains('logout')) return;
                
                const linkPath = new URL(link.href).pathname;
                if (currentPath === linkPath || (linkPath !== '/' && currentPath.startsWith(linkPath))) {
                    link.classList.add('active');
                }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>