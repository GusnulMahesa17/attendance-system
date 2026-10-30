@extends('layouts.app')

@section('content')
<style>
    .login-page {
        min-width: 0 !important;
        min-height: 100vh;
        margin: -1.5rem;
        background: #eef4ff;
        font-family: "Inter", sans-serif;
    }

    .login-shell {
        min-height: 100vh;
        display: grid;
        grid-template-columns: minmax(0, 1.08fr) minmax(420px, 0.92fr);
    }

    .login-visual {
        position: relative;
        min-height: 100vh;
        overflow: hidden;
        background: linear-gradient(145deg, rgba(19, 47, 120, .94), rgba(18, 93, 157, .82)), url("{{ asset('adzikra.jpg') }}") center/cover;
        color: #fff;
        display: flex;
        align-items: center;
    }

    .login-visual::after {
        content: "";
        position: absolute;
        width: 420px;
        height: 420px;
        border: 1px solid rgba(255,255,255,.15);
        border-radius: 50%;
        top: -170px;
        right: -120px;
        box-shadow: 0 0 0 70px rgba(255,255,255,.025), 0 0 0 140px rgba(255,255,255,.018);
    }

    .visual-content {
        position: relative;
        z-index: 1;
        max-width: 620px;
        padding: 64px clamp(32px, 6vw, 86px);
    }

    .brand-mark {
        width: 62px;
        height: 62px;
        display: grid;
        place-items: center;
        margin-bottom: 28px;
        border-radius: 18px;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.22);
        backdrop-filter: blur(10px);
        font-size: 29px;
    }

    .visual-content h1 {
        max-width: 560px;
        font-size: clamp(36px, 5vw, 68px);
        line-height: 1.02;
        letter-spacing: -0.045em;
        margin-bottom: 22px;
        font-weight: 700;
    }

    .visual-content p {
        max-width: 500px;
        color: rgba(255,255,255,.78);
        font-size: 17px;
        line-height: 1.7;
    }

    .login-panel {
        min-height: 100vh;
        display: grid;
        place-items: center;
        padding: 48px clamp(28px, 7vw, 92px);
        background: rgba(255,255,255,.96);
    }

    .login-card {
        width: 100%;
        max-width: 430px;
    }

    .login-card h2 {
        color: #12213d;
        font-size: 34px;
        font-weight: 700;
        letter-spacing: -0.035em;
        margin-bottom: 8px;
    }

    .login-subtitle {
        color: #75819a;
        margin-bottom: 34px;
    }

    .login-label {
        display: block;
        color: #263650;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .login-field {
        position: relative;
        margin-bottom: 20px;
    }

    .field-control {
        position: relative;
    }

    .login-field .form-control {
        height: 54px;
        padding: 0 48px;
        border: 1px solid #dbe3ef;
        border-radius: 12px;
        background: #fbfcff;
        color: #14233e;
        box-shadow: none;
    }

    .login-field .form-control:focus {
        border-color: #3976d8;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(57,118,216,.1);
    }

    .field-icon {
        position: absolute;
        left: 17px;
        top: 27px;
        transform: translateY(-50%);
        color: #91a0b8;
        font-size: 18px;
        z-index: 2;
        pointer-events: none;
    }

    .password-toggle {
        position: absolute;
        right: 13px;
        top: 27px;
        transform: translateY(-50%);
        width: 38px;
        height: 38px;
        border: 0;
        background: transparent;
        color: #91a0b8;
    }

    .login-submit {
        width: 100%;
        height: 54px;
        border: 0;
        border-radius: 12px;
        margin-top: 8px;
        background: linear-gradient(135deg, #1b61c5, #287ed1);
        color: #fff;
        font-weight: 600;
        box-shadow: 0 12px 24px rgba(38,112,201,.22);
        transition: transform .2s, box-shadow .2s;
    }

    .login-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 28px rgba(38,112,201,.28);
    }

    .login-footer {
        margin-top: 44px;
        color: #9aa5b8;
        font-size: 12px;
        text-align: center;
    }

    @media (max-width: 991px) {
        .login-page { margin: -1.5rem -1rem; }
        .login-shell { grid-template-columns: 1fr; }
        .login-visual { min-height: 240px; }
        .visual-content { padding: 40px 28px; }
        .visual-content h1 { font-size: 38px; }
        .visual-content p { display: none; }
        .brand-mark { width: 48px; height: 48px; margin-bottom: 16px; border-radius: 14px; }
        .login-panel { min-height: auto; padding: 52px 24px 32px; }
        .login-page .content-wrapper { min-width: 0 !important; }
    }
</style>

<div class="login-page">
    <div class="login-shell">
        <section class="login-visual" aria-label="Sistem Manajemen Absensi Siswa">
            <div class="visual-content">
                <div class="brand-mark"><i class="bi bi-journal-check"></i></div>
                <h1>Sistem Manajemen Absensi Siswa</h1>
                <p>Kelola kehadiran dan laporan akademik dengan proses yang cepat, rapi, dan mudah dipantau.</p>
            </div>
        </section>

        <main class="login-panel">
            <div class="login-card">
                <h2>Selamat datang</h2>
                <p class="login-subtitle">Masuk untuk melanjutkan ke sistem absensi.</p>

                @if ($errors->any())
                    <div class="alert alert-danger py-2" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="login-field">
                        <label for="email" class="login-label">Alamat email</label>
                        <div class="field-control">
                            <i class="bi bi-envelope field-icon"></i>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                   placeholder="nama@email.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="login-field">
                        <label for="password" class="login-label">Kata sandi</label>
                        <div class="field-control">
                            <i class="bi bi-lock field-icon"></i>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                   name="password" required autocomplete="current-password" placeholder="Masukkan kata sandi">
                            <button class="password-toggle" type="button" aria-label="Tampilkan kata sandi" aria-controls="password">
                                <i class="bi bi-eye"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="login-submit">
                        Masuk <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </form>

                <div class="login-footer">Copyright &copy; {{ date('Y') }} Adzikra. All rights reserved.</div>
            </div>
        </main>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.querySelector('.password-toggle')?.addEventListener('click', function () {
        const password = document.getElementById('password');
        const icon = this.querySelector('i');
        const visible = password.type === 'text';
        password.type = visible ? 'password' : 'text';
        icon.className = visible ? 'bi bi-eye' : 'bi bi-eye-slash';
        this.setAttribute('aria-label', visible ? 'Tampilkan kata sandi' : 'Sembunyikan kata sandi');
    });
</script>
@endsection
