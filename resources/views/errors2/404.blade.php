<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Halaman Tidak Ditemukan | ABPROC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --navy: #1e2d40;
            --navy2: #243447;
            --orange: #f59e0b;
            --orange-light: #fef3c7;
            --orange-dim: rgba(245, 158, 11, 0.12);
            --teal: #0d9488;
            --bg: #f0f2f5;
            --white: #ffffff;
            --text: #1e2d40;
            --muted: #64748b;
            --border: #e2e8f0;
        }

        html,
        body {
            height: 100%;
            background: var(--bg);
            font-family: 'Nunito', sans-serif;
            color: var(--text);
        }

        /* Topbar */
        .topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: var(--navy);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            z-index: 100;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            background: var(--orange);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .brand-text {
            font-size: 0.95rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: 0.03em;
        }

        /* Decorative blobs */
        .blob {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
        }

        .blob-1 {
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.09) 0%, transparent 70%);
            top: -100px;
            right: -100px;
        }

        .blob-2 {
            width: 280px;
            height: 280px;
            background: radial-gradient(circle, rgba(13, 148, 136, 0.07) 0%, transparent 70%);
            bottom: -80px;
            left: -80px;
        }

        /* Main */
        main {
            min-height: 100vh;
            padding-top: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        .card {
            background: var(--white);
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.07);
            padding: 3rem 2.5rem;
            max-width: 500px;
            width: 100%;
            text-align: center;
            animation: fadeUp 0.55s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Icon */
        .icon-wrap {
            width: 100px;
            height: 100px;
            background: var(--navy);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.75rem;
            box-shadow: 0 8px 32px rgba(30, 45, 64, 0.18);
            position: relative;
            animation: fadeUp 0.55s 0.06s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .icon-wrap::after {
            content: '';
            position: absolute;
            bottom: -5px;
            right: -5px;
            width: 28px;
            height: 28px;
            background: var(--orange);
            border-radius: 50%;
            border: 3px solid var(--white);
        }

        .icon-wrap svg {
            width: 52px;
            height: 52px;
        }

        /* Error code */
        .code {
            font-size: clamp(4rem, 16vw, 6.5rem);
            font-weight: 900;
            line-height: 1;
            letter-spacing: -0.03em;
            color: var(--navy);
            animation: fadeUp 0.55s 0.1s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .code em {
            font-style: normal;
            color: var(--orange);
        }

        .pill {
            display: inline-block;
            background: var(--orange-light);
            color: var(--orange);
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            padding: 4px 14px;
            border-radius: 100px;
            border: 1px solid rgba(245, 158, 11, 0.35);
            margin: 0.75rem 0 1.25rem;
            animation: fadeUp 0.55s 0.14s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        h1 {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--navy);
            margin-bottom: 0.6rem;
            animation: fadeUp 0.55s 0.17s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .desc {
            font-size: 0.875rem;
            color: var(--muted);
            line-height: 1.75;
            margin-bottom: 2rem;
            animation: fadeUp 0.55s 0.2s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .desc code {
            background: #f1f5f9;
            color: var(--navy);
            font-size: 0.78rem;
            padding: 2px 8px;
            border-radius: 5px;
            font-family: monospace;
        }

        /* Divider */
        hr {
            border: none;
            height: 1px;
            background: var(--border);
            margin-bottom: 1.75rem;
            animation: fadeUp 0.55s 0.22s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        /* Actions */
        .actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeUp 0.55s 0.26s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 20px;
            border-radius: 8px;
            font-family: 'Nunito', sans-serif;
            font-size: 0.875rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease;
            border: none;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-navy {
            background: var(--navy);
            color: #fff;
            box-shadow: 0 4px 14px rgba(30, 45, 64, 0.2);
        }

        .btn-navy:hover {
            background: var(--navy2);
            box-shadow: 0 6px 20px rgba(30, 45, 64, 0.3);
        }

        .btn-outline {
            background: #fff;
            color: var(--muted);
            border: 1.5px solid var(--border);
        }

        .btn-outline:hover {
            color: var(--navy);
            border-color: #94a3b8;
        }

        /* Footer */
        .footer {
            margin-top: 1.75rem;
            font-size: 0.75rem;
            color: #94a3b8;
            animation: fadeUp 0.55s 0.3s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .footer .heart {
            color: #e11d48;
        }

        @media (max-width: 480px) {
            .card {
                padding: 2.25rem 1.5rem;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <nav class="topbar">
        <div class="brand">
            <span class="brand-text">RS AWAL BROS</span>
        </div>
    </nav>

    <main>
        <div class="card">

            <div class="icon-wrap">
                <!-- Magnifier + question mark -->
                <svg viewBox="0 0 52 52" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="22" cy="22" r="15" stroke="#f59e0b" stroke-width="4" />
                    <line x1="33" y1="33" x2="47" y2="47" stroke="#f59e0b" stroke-width="4"
                        stroke-linecap="round" />
                    <text x="15" y="29" font-family="Nunito,sans-serif" font-weight="900" font-size="16"
                        fill="white">?</text>
                </svg>
            </div>

            <div class="code">4<em>0</em>4</div>
            <div class="pill">Halaman Tidak Ditemukan</div>

            <h1>Ups! Halaman ini Tidak Ada</h1>
            <p class="desc">
                Halaman <code>/{{ request()->path() }}</code> tidak dapat ditemukan.<br>
                Mungkin URL salah ketik atau halaman sudah dipindahkan.
            </p>

            <hr>

            <div class="actions">
                <a href="{{ route('home') }}" class="btn btn-navy">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                        <polyline points="9 22 9 12 15 12 15 22" />
                    </svg>
                    Kembali ke Dashboard
                </a>
                <button onclick="history.back()" class="btn btn-outline">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m15 18-6-6 6-6" />
                    </svg>
                    Halaman Sebelumnya
                </button>
            </div>

            <div class="footer">
                © 2026 SISTEM ABPROC &nbsp;·&nbsp;<br> Dikembangkan dengan <span class="heart">♥</span> oleh PT DIGITAL
                INDONESIA HEBAT
            </div>

        </div>
    </main>

</body>

</html>
