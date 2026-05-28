<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login | {{ config('app.name', 'Wingko Londo') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('sneat-1.0.0/assets/vendor/fonts/boxicons.css') }}">

    <style>
        :root {
            --ink: #2b211b;
            --muted: #7b6657;
            --line: #e5d8ca;
            --panel: #ffffff;
            --soft: #fbf6ef;
            --primary: #8b4a25;
            --primary-dark: #673619;
            --accent: #c98542;
            --danger: #d92d20;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: "Figtree", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--ink);
            background: var(--soft);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .login-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(360px, 0.92fr) minmax(420px, 1.08fr);
        }

        .brand-panel {
            position: relative;
            display: flex;
            min-height: 100vh;
            padding: 42px;
            overflow: hidden;
            color: #fff8ee;
            background:
                radial-gradient(circle at 20% 18%, rgba(255, 226, 173, 0.26) 0 10%, transparent 11%),
                radial-gradient(circle at 84% 26%, rgba(201, 133, 66, 0.24) 0 12%, transparent 13%),
                linear-gradient(135deg, #4b2614 0%, #7a3f1f 48%, #b06c32 100%);
        }

        .brand-panel::before,
        .brand-panel::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }

        .brand-panel::before {
            width: 360px;
            height: 360px;
            right: -110px;
            top: 95px;
            background:
                radial-gradient(circle, rgba(255, 232, 189, 0.84) 0 35%, rgba(157, 82, 38, 0.92) 36% 67%, rgba(92, 45, 22, 0.22) 68% 100%);
            opacity: 0.42;
        }

        .brand-panel::after {
            width: 230px;
            height: 230px;
            left: -58px;
            bottom: 86px;
            background:
                radial-gradient(circle, rgba(255, 244, 224, 0.78) 0 32%, rgba(190, 119, 57, 0.86) 33% 69%, rgba(79, 38, 18, 0.18) 70% 100%);
            opacity: 0.38;
        }

        .brand-content {
            position: relative;
            z-index: 1;
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 36px;
        }

        .brand-mark {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 20px;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.24);
        }

        .brand-copy {
            max-width: 520px;
        }

        .brand-copy h1 {
            margin: 0;
            font-size: 44px;
            line-height: 1.08;
            letter-spacing: 0;
        }

        .brand-copy p {
            margin: 18px 0 0;
            color: rgba(255, 255, 255, 0.82);
            font-size: 16px;
            line-height: 1.7;
        }

        .brand-metrics {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            max-width: 520px;
        }

        .metric {
            padding: 16px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .metric strong {
            display: block;
            font-size: 20px;
            line-height: 1.2;
        }

        .metric span {
            display: block;
            margin-top: 6px;
            color: rgba(255, 255, 255, 0.72);
            font-size: 12px;
            line-height: 1.35;
        }

        .form-panel {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 36px;
        }

        .login-card {
            width: min(100%, 460px);
            padding: 34px;
            border-radius: 8px;
            background: var(--panel);
            border: 1px solid rgba(223, 228, 238, 0.92);
            box-shadow: 0 22px 70px rgba(32, 33, 36, 0.10);
        }

        .mobile-mark {
            display: none;
            align-items: center;
            gap: 10px;
            margin-bottom: 28px;
            font-weight: 700;
            color: var(--ink);
        }

        .mobile-mark .brand-icon {
            color: var(--primary);
            background: #fff2df;
            border-color: #f1d6b6;
        }

        .login-title {
            margin: 0;
            font-size: 28px;
            line-height: 1.2;
            letter-spacing: 0;
        }

        .login-subtitle {
            margin: 10px 0 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
        }

        .status-message {
            margin-top: 22px;
            padding: 12px 14px;
            border-radius: 8px;
            color: #067647;
            background: #ecfdf3;
            border: 1px solid #abefc6;
            font-size: 14px;
        }

        .field-group {
            margin-top: 22px;
        }

        .field-label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #343741;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #8792a2;
            font-size: 19px;
        }

        .form-input {
            width: 100%;
            height: 48px;
            padding: 0 14px 0 44px;
            border: 1px solid var(--line);
            border-radius: 8px;
            outline: none;
            color: var(--ink);
            background: #fff;
            font-size: 14px;
            transition: border-color 160ms ease, box-shadow 160ms ease;
        }

        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(139, 74, 37, 0.14);
        }

        .form-input.is-invalid {
            border-color: var(--danger);
        }

        .field-error {
            margin-top: 7px;
            color: var(--danger);
            font-size: 13px;
            line-height: 1.45;
        }

        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-top: 18px;
        }

        .remember {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-size: 14px;
        }

        .remember input {
            width: 16px;
            height: 16px;
            margin: 0;
            accent-color: var(--primary);
        }

        .text-link {
            color: var(--primary);
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
        }

        .text-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .login-button {
            width: 100%;
            height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            margin-top: 26px;
            border: 0;
            border-radius: 8px;
            color: #fff;
            background: var(--primary);
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 12px 28px rgba(139, 74, 37, 0.26);
            transition: background 160ms ease, transform 160ms ease, box-shadow 160ms ease;
        }

        .login-button:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 14px 32px rgba(103, 54, 25, 0.30);
        }

        .login-footnote {
            margin: 22px 0 0;
            color: #8a94a6;
            font-size: 12px;
            line-height: 1.6;
            text-align: center;
        }

        @media (max-width: 960px) {
            .login-shell {
                grid-template-columns: 1fr;
            }

            .brand-panel {
                display: none;
            }

            .form-panel {
                min-height: 100vh;
                padding: 24px;
                background:
                    radial-gradient(circle at 85% 12%, rgba(201, 133, 66, 0.18) 0 15%, transparent 16%),
                    radial-gradient(circle at 16% 88%, rgba(139, 74, 37, 0.14) 0 14%, transparent 15%),
                    linear-gradient(180deg, #fff8ee 0%, #fbf6ef 100%);
            }

            .mobile-mark {
                display: inline-flex;
            }
        }

        @media (max-width: 520px) {
            .form-panel {
                padding: 18px;
            }

            .login-card {
                padding: 24px;
            }

            .login-title {
                font-size: 24px;
            }

            .form-row {
                align-items: flex-start;
                flex-direction: column;
                gap: 12px;
            }
        }
    </style>
</head>
<body>
    <main class="login-shell">
        <section class="brand-panel" aria-label="Wingko Londo">
            <div class="brand-content">
                <a class="brand-mark" href="{{ url('/') }}">
                    <span class="brand-icon"><i class="bx bx-bowl-hot"></i></span>
                    <span>{{ config('app.name', 'Wingko Londo') }}</span>
                </a>

                <div class="brand-copy">
                    <h1>Manajemen produksi Wingko Londo.</h1>
                    <p>Masuk untuk mengatur pesanan, stok bahan, produksi, distribusi, retur, dan piutang dalam satu alur kerja.</p>
                </div>

                <div class="brand-metrics" aria-label="Ringkasan modul">
                    <div class="metric">
                        <strong>Wingko</strong>
                        <span>Produk kelapa dan ketan</span>
                    </div>
                    <div class="metric">
                        <strong>Stok</strong>
                        <span>Produk dan bahan baku</span>
                    </div>
                    <div class="metric">
                        <strong>Retur</strong>
                        <span>Pickup dan refund</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="form-panel">
            <div class="login-card">
                <a class="mobile-mark" href="{{ url('/') }}">
                    <span class="brand-icon"><i class="bx bx-bowl-hot"></i></span>
                    <span>{{ config('app.name', 'Wingko Londo') }}</span>
                </a>

                <h2 class="login-title">Selamat datang kembali</h2>
                <p class="login-subtitle">Gunakan akun yang sudah terdaftar untuk melanjutkan ke dashboard.</p>

                @if (session('status'))
                    <div class="status-message">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="field-group">
                        <label class="field-label" for="email">Email</label>
                        <div class="input-wrap">
                            <i class="bx bx-envelope"></i>
                            <input
                                id="email"
                                class="form-input @error('email') is-invalid @enderror"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="nama@email.com">
                        </div>
                        @error('email')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label class="field-label" for="password">Password</label>
                        <div class="input-wrap">
                            <i class="bx bx-lock-alt"></i>
                            <input
                                id="password"
                                class="form-input @error('password') is-invalid @enderror"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="Masukkan password">
                        </div>
                        @error('password')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-row">
                        <label class="remember" for="remember_me">
                            <input id="remember_me" type="checkbox" name="remember">
                            <span>Ingat saya</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-link" href="{{ route('password.request') }}">Lupa password?</a>
                        @endif
                    </div>

                    <button class="login-button" type="submit">
                        <i class="bx bx-log-in-circle"></i>
                        <span>Masuk</span>
                    </button>
                </form>

                <p class="login-footnote">Akses hanya untuk pengguna yang memiliki akun aktif.</p>
            </div>
        </section>
    </main>
</body>
</html>
