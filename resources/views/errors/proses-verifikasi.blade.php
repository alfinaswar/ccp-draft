<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Approval | ABPROC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@500;600;700;800&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
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
            --navy-2: #243447;
            --orange: #f59e0b;
            --orange-dark: #d97706;
            --orange-light: #fef3c7;
            --bg: #f0f2f5;
            --white: #ffffff;
            --text: #1e2d40;
            --muted: #64748b;
            --border: #e2e8f0;
            --border-strong: #cbd5e1;
        }

        html,
        body {
            height: 100%;
            background: var(--bg);
            font-family: 'Nunito', sans-serif;
            color: var(--text);
            -webkit-font-smoothing: antialiased;
        }

        /* TOPBAR */
        .topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 62px;
            background: var(--navy);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            z-index: 100;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-icon {
            width: 34px;
            height: 34px;
            background: var(--orange);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            font-weight: 900;
            color: white;
            flex-shrink: 0;
        }

        .brand-text {
            font-size: 0.95rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: 0.02em;
        }

        .brand-sub {
            font-size: 0.72rem;
            color: #94a3b8;
            font-weight: 500;
            margin-left: 0.5rem;
            letter-spacing: 0.05em;
        }

        /* MAIN */
        main {
            min-height: 100vh;
            padding: 110px 1.25rem 2rem;
            display: flex;
            align-items: flex-start;
            justify-content: center;
        }

        .card {
            background: var(--white);
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 40px rgba(30, 45, 64, 0.10);
            padding: 2.75rem 2rem 2.25rem;
            max-width: 480px;
            width: 100%;
            text-align: center;
            position: relative;
            overflow: hidden;
            animation: fadeUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--orange) 0%, var(--orange-dark) 100%);
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* STATUS BADGE */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--orange-light);
            color: var(--orange-dark);
            font-size: 0.74rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 6px 14px;
            border-radius: 100px;
            border: 1px solid rgba(245, 158, 11, 0.32);
            margin-bottom: 1.75rem;
        }

        .status-badge .dot {
            width: 8px;
            height: 8px;
            background: var(--orange);
            border-radius: 50%;
            animation: pulse 1.6s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.4); opacity: 0.55; }
        }

        /* ICON */
        .icon-wrap {
            width: 88px;
            height: 88px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-2) 100%);
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 8px 24px rgba(30, 45, 64, 0.22);
            position: relative;
        }

        .icon-wrap::after {
            content: '';
            position: absolute;
            bottom: -4px;
            right: -4px;
            width: 24px;
            height: 24px;
            background: var(--orange);
            border-radius: 50%;
            border: 3px solid var(--white);
        }

        .icon-wrap svg {
            width: 42px;
            height: 42px;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
        }

        /* TEXT */
        h1 {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--navy);
            margin-bottom: 0.6rem;
            letter-spacing: -0.01em;
            line-height: 1.3;
        }

        .desc {
            font-size: 0.95rem;
            color: var(--muted);
            line-height: 1.7;
            margin-bottom: 1.75rem;
        }

        .desc strong {
            color: var(--text);
            font-weight: 700;
        }

        /* INFO LIST */
        .info-list {
            background: var(--bg);
            border-radius: 12px;
            padding: 0.4rem 1rem;
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.7rem 0;
            border-bottom: 1px dashed var(--border);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-size: 0.82rem;
            color: var(--muted);
            font-weight: 600;
        }

        .info-value {
            font-size: 0.85rem;
            color: var(--text);
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
        }

        .info-value.highlight {
            color: var(--orange-dark);
        }

        /* ESTIMATE */
        .estimate {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f1f5f9;
            color: var(--navy);
            font-size: 0.82rem;
            font-weight: 700;
            padding: 10px 16px;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        .estimate svg {
            width: 16px;
            height: 16px;
            color: var(--orange-dark);
        }

        /* BUTTON */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 13px 20px;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            border: none;
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-2) 100%);
            color: white;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(30, 45, 64, 0.22);
            font-family: inherit;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(30, 45, 64, 0.30);
        }

        .btn svg {
            width: 16px;
            height: 16px;
        }

        /* FOOTER */
        .footer {
            margin-top: 1.75rem;
            font-size: 0.74rem;
            color: #94a3b8;
            line-height: 1.6;
        }

        .footer .heart {
            color: #e11d48;
        }

        .footer strong {
            color: var(--muted);
            font-weight: 700;
        }

        /* MOBILE */
        @media (max-width: 480px) {
            main {
                padding-top: 90px;
                padding-left: 0.9rem;
                padding-right: 0.9rem;
            }
            .card {
                padding: 2.25rem 1.35rem 1.75rem;
                border-radius: 16px;
            }
            h1 {
                font-size: 1.15rem;
            }
            .brand-sub {
                display: none;
            }
        }
    </style>
</head>

<body>

    <nav class="topbar">
        <div class="brand">
            <div class="brand-icon">AB</div>
            <span class="brand-text">RS AWAL BROS</span>
            <span class="brand-sub">ABPROC</span>
        </div>
    </nav>

    <main>
        <div class="card">

            <span class="status-badge">
                <span class="dot"></span>
                Sedang Diverifikasi
            </span>

            <div class="icon-wrap">
                <!-- Shield with clock icon - verification in progress -->
                <svg viewBox="0 0 52 52" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M26 6 L42 12 V24 C42 34 35 41 26 45 C17 41 10 34 10 24 V12 L26 6 Z"
                          stroke="#fff" stroke-width="2" fill="none" stroke-linejoin="round"/>
                    <path d="M26 10 L38 15 V24 C38 32 33 38 26 41 C19 38 14 32 14 24 V15 L26 10 Z"
                          fill="rgba(245, 158, 11, 0.18)"/>
                    <circle cx="26" cy="26" r="7" stroke="#fff" stroke-width="2" fill="none"/>
                    <polyline points="26 22 26 26 29 28" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                </svg>
            </div>

            <h1>Approval Anda Sedang Diverifikasi</h1>
            <p class="desc">
                Sistem approval sedang meninjau pengajuan Anda.<br>
                Mohon bersabar, proses sedang berlangsung.
            </p>


            <!-- Info singkat -->
            {{-- <div class="info-list">
                <div class="info-row">
                    <span class="info-label">Nomor Request</span>
                    <span class="info-value">#PRQ-2026-00842</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal Pengajuan</span>
                    <span class="info-value">06 Agu 2026, 14:32</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Approval Selanjutnya</span>
                    <span class="info-value highlight">Kepala Unit Logistik</span>
                </div>
            </div> --}}

            <!-- Estimasi -->
            {{-- <div class="estimate">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                Estimasi selesai: 1 × 24 jam kerja
            </div> --}}

            <button class="btn" onclick="window.close()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"/>
                    <polyline points="12 19 5 12 12 5"/>
                </svg>
                Tutup Halaman
            </button>


            <div class="footer">
                © 2026 SISTEM ABPROC<br>
                Dikembangkan dengan <span class="heart">♥</span> oleh <strong>PT DIGITAL INDONESIA HEBAT</strong>
            </div>

        </div>
    </main>

</body>

</html>
