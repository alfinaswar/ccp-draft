<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Server Error | ABPROC</title>
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
            --red: #ef4444;
            --red-light: #fee2e2;
            --orange: #f59e0b;
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
            display: flex;
            flex-direction: column;
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

        /* Blobs */
        .blob {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
        }

        .blob-1 {
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(239, 68, 68, 0.08) 0%, transparent 70%);
            top: -100px;
            right: -100px;
        }

        .blob-2 {
            width: 280px;
            height: 280px;
            background: radial-gradient(circle, rgba(30, 45, 64, 0.06) 0%, transparent 70%);
            bottom: -80px;
            left: -80px;
        }

        /* Main */
        main {
            flex: 1;
            padding-top: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
            padding-bottom: 1.5rem;
        }

        .card {
            background: var(--white);
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.07);
            padding: 3rem 2.5rem;
            max-width: 520px;
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

        /* Alert strip */
        .alert-strip {
            background: var(--red-light);
            border-left: 4px solid var(--red);
            border-radius: 10px;
            padding: 10px 16px;
            margin-bottom: 1.75rem;
            display: flex;
            align-items: center;
            gap: 10px;
            text-align: left;
            animation: fadeUp 0.55s 0.04s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .alert-strip svg {
            flex-shrink: 0;
        }

        .alert-strip-text {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--red);
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
            animation: fadeUp 0.55s 0.08s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .icon-wrap::after {
            content: '';
            position: absolute;
            bottom: -5px;
            right: -5px;
            width: 28px;
            height: 28px;
            background: var(--red);
            border-radius: 50%;
            border: 3px solid var(--white);
        }

        .icon-wrap svg {
            width: 52px;
            height: 52px;
        }

        /* Code */
        .code {
            font-size: clamp(4rem, 16vw, 6.5rem);
            font-weight: 900;
            line-height: 1;
            letter-spacing: -0.03em;
            color: var(--navy);
            animation: fadeUp 0.55s 0.11s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .code em {
            font-style: normal;
            color: var(--red);
        }

        .pill {
            display: inline-block;
            background: var(--red-light);
            color: var(--red);
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            padding: 4px 14px;
            border-radius: 100px;
            border: 1px solid rgba(239, 68, 68, 0.3);
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
            margin-bottom: 1.75rem;
            animation: fadeUp 0.55s 0.2s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        /* Info box */
        .info-box {
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 14px 16px;
            text-align: left;
            margin-bottom: 1.75rem;
            animation: fadeUp 0.55s 0.22s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .info-box-title {
            font-size: 0.75rem;
            font-weight: 800;
            color: var(--navy);
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .info-row {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: 0.8rem;
            color: var(--muted);
            margin-bottom: 6px;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-row svg {
            flex-shrink: 0;
            margin-top: 1px;
            color: var(--red);
        }

        hr {
            border: none;
            height: 1px;
            background: var(--border);
            margin-bottom: 1.75rem;
            animation: fadeUp 0.55s 0.23s cubic-bezier(0.16, 1, 0.3, 1) both;
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

        .btn-red {
            background: var(--red);
            color: #fff;
            box-shadow: 0 4px 14px rgba(239, 68, 68, 0.2);
        }

        .btn-red:hover {
            background: #dc2626;
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.3);
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

        /* Footer sticky bottom */
        .site-footer {
            background: var(--navy);
            color: #94a3b8;
            font-size: 0.75rem;
            text-align: center;
            padding: 14px 1.5rem;
            flex-shrink: 0;
        }

        .site-footer .heart {
            color: #e11d48;
        }

        /* Actions - always one row */
        .actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: nowrap;
            animation: fadeUp 0.55s 0.26s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @media (max-width: 480px) {
            .card {
                padding: 2.25rem 1.5rem;
            }
        }
    </style>
</head>

<body>

    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <nav class="topbar">
        <div class="brand">
            {{-- <div class="brand-icon">🏥</div> --}}
            <span class="brand-text">RS AWAL BROS</span>
        </div>
    </nav>

    <main>
        <div class="card">

            <!-- Alert strip -->
            <div class="alert-strip">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ef4444"
                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z" />
                    <line x1="12" y1="9" x2="12" y2="13" />
                    <line x1="12" y1="17" x2="12.01" y2="17" />
                </svg>
                <span class="alert-strip-text">Terjadi kesalahan pada server. Tim teknis telah diberitahu.</span>
            </div>

            <div class="icon-wrap">
                <!-- Server error icon -->
                <svg viewBox="0 0 52 52" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="8" y="10" width="36" height="12" rx="3" stroke="white" stroke-width="3.5" />
                    <rect x="8" y="26" width="36" height="12" rx="3" stroke="white" stroke-width="3.5" />
                    <circle cx="38" cy="16" r="3" fill="#ef4444" />
                    <circle cx="38" cy="32" r="3" fill="#ef4444" />
                    <line x1="26" y1="42" x2="26" y2="50" stroke="#ef4444" stroke-width="3"
                        stroke-linecap="round" />
                    <line x1="22" y1="50" x2="30" y2="50" stroke="#ef4444" stroke-width="3"
                        stroke-linecap="round" />
                </svg>
            </div>

            <div class="code">5<em>0</em>0</div>
            <div class="pill">Internal Server Error</div>

            <h1>Server Mengalami Gangguan</h1>
            <p class="desc">
                Maaf, server kami sedang mengalami masalah teknis.<br>
                Silakan coba beberapa saat lagi atau hubungi administrator.
            </p>

            <!-- Info checklist -->
            <div class="info-box">
                <div class="info-box-title">Yang bisa kamu lakukan:</div>
                <div class="info-row">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round">
                        <polyline points="9 18 15 12 9 6" />
                    </svg>
                    Refresh halaman ini setelah beberapa menit
                </div>
                <div class="info-row">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round">
                        <polyline points="9 18 15 12 9 6" />
                    </svg>
                    Bersihkan cache browser dan coba lagi
                </div>
                <div class="info-row">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round">
                        <polyline points="9 18 15 12 9 6" />
                    </svg>
                    Hubungi tim IT jika masalah terus berlanjut
                </div>
            </div>

            <hr>

            <div class="actions">
                <button onclick="location.reload()" class="btn btn-red">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 2v6h-6" />
                        <path d="M3 12a9 9 0 0 1 15-6.7L21 8" />
                        <path d="M3 22v-6h6" />
                        <path d="M21 12a9 9 0 0 1-15 6.7L3 16" />
                    </svg>
                    Coba Lagi
                </button>
                <a href="{{ route('home') }}" class="btn btn-navy">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                        <polyline points="9 22 9 12 15 12 15 22" />
                    </svg>
                    Kembali ke Dashboard
                </a>
            </div>

        </div>
    </main>

    <footer class="site-footer">
        © 2026 SISTEM ABPROC &nbsp;·&nbsp; Dikembangkan dengan <span class="heart">♥</span> oleh PT DIGITAL INDONESIA
        HEBAT
    </footer>

</body>

</html>
