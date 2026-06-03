@extends('layouts.app')

@section('content')
    @push('css')
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap');

            :root {
                --primary: #2563eb;
                --primary-light: #dbeafe;
                --success: #059669;
                --success-light: #d1fae5;
                --danger: #dc2626;
                --danger-light: #fee2e2;
                --warning: #d97706;
                --warning-light: #fef3c7;
                --neutral: #64748b;
                --neutral-light: #f1f5f9;
                --surface: #ffffff;
                --border: #e2e8f0;
                --text-primary: #0f172a;
                --text-secondary: #64748b;
                --text-muted: #94a3b8;
                --shadow-sm: 0 1px 3px rgba(0, 0, 0, .06), 0 1px 2px rgba(0, 0, 0, .04);
                --shadow-md: 0 4px 16px rgba(0, 0, 0, .08), 0 2px 6px rgba(0, 0, 0, .04);
                --shadow-lg: 0 12px 40px rgba(0, 0, 0, .10), 0 4px 12px rgba(0, 0, 0, .05);
                --radius: 14px;
                --radius-sm: 8px;
            }

            body,
            .page-wrapper {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }

            /* ── Page Header ── */
            .trk-page-header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 12px;
                margin-bottom: 28px;
            }

            .trk-page-header h3 {
                font-size: 1.45rem;
                font-weight: 700;
                color: var(--text-primary);
                margin: 0 0 6px;
                letter-spacing: -.4px;
            }

            .trk-breadcrumb {
                display: flex;
                align-items: center;
                gap: 6px;
                list-style: none;
                margin: 0;
                padding: 0;
                font-size: .8rem;
                color: var(--text-muted);
            }

            .trk-breadcrumb li+li::before {
                content: '/';
                margin-right: 6px;
                color: var(--border);
            }

            .trk-breadcrumb a {
                color: var(--primary);
                text-decoration: none;
                font-weight: 500;
            }

            .trk-breadcrumb li.active {
                color: var(--text-secondary);
            }

            .btn-close-tab {
                display: inline-flex;
                align-items: center;
                gap: 7px;
                padding: 9px 18px;
                background: #ef4444;
                /* solid warna merah */
                border: 1.5px solid #ef4444;
                border-radius: var(--radius-sm);
                font-size: .85rem;
                font-weight: 600;
                color: #fff;
                /* putih agar kontras di atas merah */
                cursor: pointer;
                transition: all .18s;
                white-space: nowrap;
            }

            .btn-close-tab:hover {
                background: #b91c1c;
                /* merah lebih gelap ketika hover */
                color: #fff;
                /* tetap putih */
                transform: translateY(-1px);
                box-shadow: var(--shadow-sm);
            }

            /* ── Info Sidebar ── */
            .info-card {
                background: var(--surface);
                border: 1.5px solid var(--border);
                border-radius: var(--radius);
                box-shadow: var(--shadow-sm);
                overflow: hidden;
                position: sticky;
                top: 24px;
            }

            .info-card-header {
                background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
                padding: 18px 20px;
                display: flex;
                align-items: center;
                gap: 10px;
                color: #fff;
                font-weight: bold;
            }

            .info-card-header svg {
                opacity: .85;
                flex-shrink: 0;
            }

            .info-card-header h5 {
                margin: 0;
                font-size: .95rem;
                font-weight: 600;
                letter-spacing: -.2px;
            }

            .info-rows {
                padding: 6px 0;
            }

            .info-row {
                display: flex;
                flex-direction: column;
                gap: 2px;
                padding: 13px 20px;
                border-bottom: 1px solid var(--border);
                transition: background .15s;
            }

            .info-row:last-child {
                border-bottom: none;
            }

            .info-row:hover {
                background: #f8fafc;
            }

            .info-row-label {
                font-size: .72rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .6px;
                color: var(--text-muted);
            }

            .info-row-value {
                font-size: .9rem;
                font-weight: 500;
                color: var(--text-primary);
            }

            .info-row-value.mono {
                font-family: 'JetBrains Mono', monospace;
                font-size: .82rem;
                font-weight: 500;
                color: var(--primary);
                background: var(--primary-light);
                display: inline-block;
                padding: 2px 8px;
                border-radius: 5px;
            }

            .status-pill {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 3px 10px;
                border-radius: 20px;
                font-size: .78rem;
                font-weight: 600;
            }

            .status-pill.approve {
                background: var(--success-light);
                color: var(--success);
            }

            .status-pill.tolak {
                background: var(--danger-light);
                color: var(--danger);
            }

            .status-pill.default {
                background: var(--primary-light);
                color: var(--primary);
            }

            .status-pill::before {
                content: '';
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background: currentColor;
                display: inline-block;
            }

            /* ── Timeline Card ── */
            .trk-card {
                background: var(--surface);
                border: 1.5px solid var(--border);
                border-radius: var(--radius);
                box-shadow: var(--shadow-sm);
                overflow: hidden;
                margin-bottom: 20px;
            }

            .trk-card-header {
                padding: 18px 24px;
                border-bottom: 1.5px solid var(--border);
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .trk-card-header h5 {
                margin: 0;
                font-size: 1rem;
                font-weight: 700;
                color: var(--text-primary);
                letter-spacing: -.3px;
            }

            .trk-card-header .count-badge {
                margin-left: auto;
                background: var(--primary-light);
                color: var(--primary);
                font-size: .75rem;
                font-weight: 700;
                padding: 2px 9px;
                border-radius: 20px;
            }

            .trk-card-body {
                padding: 28px 24px;
            }

            /* ── Timeline ── */
            .timeline-list {
                list-style: none;
                padding: 0;
                margin: 0;
                position: relative;
            }

            .timeline-list::before {
                content: '';
                position: absolute;
                left: 22px;
                top: 12px;
                bottom: 12px;
                width: 2px;
                background: linear-gradient(to bottom, var(--primary) 0%, var(--border) 100%);
                border-radius: 2px;
            }

            .tl-item {
                display: flex;
                gap: 20px;
                padding-bottom: 32px;
                position: relative;
                animation: fadeSlideUp .35s ease both;
            }

            .tl-item:last-child {
                padding-bottom: 0;
            }

            @keyframes fadeSlideUp {
                from {
                    opacity: 0;
                    transform: translateY(14px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .tl-item:nth-child(1) {
                animation-delay: .05s;
            }

            .tl-item:nth-child(2) {
                animation-delay: .10s;
            }

            .tl-item:nth-child(3) {
                animation-delay: .15s;
            }

            .tl-item:nth-child(4) {
                animation-delay: .20s;
            }

            .tl-item:nth-child(5) {
                animation-delay: .25s;
            }

            .tl-item:nth-child(6) {
                animation-delay: .30s;
            }

            .tl-dot-wrap {
                flex-shrink: 0;
                width: 46px;
                display: flex;
                align-items: flex-start;
                justify-content: center;
                padding-top: 2px;
            }

            .tl-dot {
                width: 20px;
                height: 20px;
                border-radius: 50%;
                border: 3px solid var(--surface);
                box-shadow: 0 0 0 2px currentColor, var(--shadow-sm);
                position: relative;
                flex-shrink: 0;
                z-index: 1;
                transition: transform .2s;
            }

            .tl-item:hover .tl-dot {
                transform: scale(1.15);
            }

            .tl-dot.approve {
                color: var(--success);
                background: var(--success);
            }

            .tl-dot.tolak {
                color: var(--danger);
                background: var(--danger);
            }

            .tl-dot.default {
                color: var(--neutral);
                background: var(--neutral);
            }

            /* pulse on last dot */
            .tl-item:first-child .tl-dot::after {
                content: '';
                position: absolute;
                inset: -5px;
                border-radius: 50%;
                border: 2px solid currentColor;
                opacity: 0;
                animation: pulse 1.8s ease-out infinite;
            }

            @keyframes pulse {
                0% {
                    transform: scale(.8);
                    opacity: .6;
                }

                100% {
                    transform: scale(1.6);
                    opacity: 0;
                }
            }

            .tl-content {
                flex: 1;
                background: #ffffff;
                border: 1.5px solid var(--border);
                border-radius: var(--radius-sm);
                padding: 14px 18px;
                transition: box-shadow .2s, border-color .2s, transform .2s;
            }

            .tl-item:hover .tl-content {
                box-shadow: var(--shadow-md);
                border-color: #cbd5e1;
                transform: translateY(-2px);
            }

            .tl-item:first-child .tl-content {
                background: linear-gradient(135deg, #f0f9ff 0%, #f8fafc 100%);
                border-color: #bae6fd;
            }

            .tl-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 8px;
                margin-bottom: 10px;
            }

            .tl-jenis {
                font-size: .9rem;
                font-weight: 700;
                color: var(--text-primary);
                letter-spacing: -.2px;
            }

            .tl-time {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                font-size: .75rem;
                font-family: 'JetBrains Mono', monospace;
                color: #111111;
                background: var(--neutral-light);
                padding: 3px 9px;
                border-radius: 5px;
                /* font-weight: bold; */
            }

            .tl-meta {
                display: flex;
                align-items: center;
                gap: 6px;
                margin-bottom: 8px;
            }

            .tl-avatar {
                width: 22px;
                height: 22px;
                border-radius: 50%;
                background: linear-gradient(135deg, #2563eb, #7c3aed);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: .6rem;
                font-weight: 700;
                color: #fff;
                flex-shrink: 0;
            }

            .tl-user {
                font-size: .82rem;
                font-weight: 600;
                color: var(--text-secondary);
            }

            .tl-keterangan {
                font-size: .82rem;
                color: var(--text-secondary);
                line-height: 1.55;
                padding: 8px 12px;
                background: rgba(255, 255, 255, .7);
                border-left: 3px solid var(--border);
                border-radius: 0 5px 5px 0;
            }

            /* ── Empty State ── */
            .trk-empty {
                text-align: center;
                padding: 48px 24px;
            }

            .trk-empty-icon {
                width: 64px;
                height: 64px;
                background: var(--neutral-light);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 16px;
                color: var(--text-muted);
            }

            .trk-empty h6 {
                font-weight: 700;
                color: var(--text-secondary);
                margin-bottom: 6px;
            }

            .trk-empty p {
                font-size: .83rem;
                color: var(--text-muted);
                margin: 0;
            }

            /* ── Item Table Card ── */
            .items-card {
                background: var(--surface);
                border: 1.5px solid var(--border);
                border-radius: var(--radius);
                box-shadow: var(--shadow-sm);
                overflow: hidden;
            }

            .items-card-header {
                padding: 16px 24px;
                border-bottom: 1.5px solid var(--border);
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .items-card-header h6 {
                margin: 0;
                font-size: .95rem;
                font-weight: 700;
                color: var(--text-primary);
            }

            .items-table {
                width: 100%;
                border-collapse: collapse;
                font-size: .85rem;
            }

            .items-table thead tr {
                background: #f8fafc;
                border-bottom: 1.5px solid var(--border);
            }

            .items-table thead th {
                padding: 11px 16px;
                font-size: .72rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: .6px;
                color: var(--text-muted);
                text-align: left;
            }

            .items-table tbody tr {
                border-bottom: 1px solid var(--border);
                transition: background .15s;
            }

            .items-table tbody tr:last-child {
                border-bottom: none;
            }

            .items-table tbody tr:hover {
                background: #f8fafc;
            }

            .items-table td {
                padding: 12px 16px;
                color: var(--text-primary);
                vertical-align: middle;
            }

            .item-no {
                width: 28px;
                height: 28px;
                background: var(--primary-light);
                color: var(--primary);
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: .72rem;
                font-weight: 700;
            }

            .qty-badge {
                display: inline-block;
                background: var(--neutral-light);
                color: var(--text-secondary);
                font-size: .8rem;
                font-weight: 600;
                padding: 2px 9px;
                border-radius: 5px;
                font-family: 'JetBrains Mono', monospace;
            }

            .empty-items {
                padding: 32px;
                text-align: center;
                font-size: .85rem;
                color: var(--text-muted);
            }

            /* ── Icon helpers ── */
            .icon-xs {
                width: 16px;
                height: 16px;
                flex-shrink: 0;
            }

            .icon-sm {
                width: 18px;
                height: 18px;
                flex-shrink: 0;
            }
        </style>
    @endpush
    <!-- Page Header -->
    <div class="trk-page-header">
        <div>
            <h3>Tracking Proses Pengajuan</h3>
            <ul class="trk-breadcrumb">
                <li><a href="{{ route('home') }}">Dashboard</a></li>
                <li>Tracking Pengajuan</li>
                <li class="active">Detail Tracking</li>
            </ul>
        </div>
        <button class="btn-close-tab" onclick="window.close();">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon-xs" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
            Tutup Tab
        </button>
    </div>

    <div class="row g-4">
        <!-- ── Sidebar Info ── -->
        <div class="col-xl-3 col-md-12">
            <div class="info-card">
                <div class="info-card-header">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon-sm" viewBox="0 0 24 24" fill="none"
                        stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="16" y1="13" x2="8" y2="13" />
                        <line x1="16" y1="17" x2="8" y2="17" />
                        <polyline points="10 9 9 9 8 9" />
                    </svg>
                    <h5>Informasi Pengajuan</h5>
                </div>
                <div class="info-rows">
                    <div class="info-row">
                        <span class="info-row-label">No. Pengajuan</span>
                        <span class="fw-bold">{{ $pengajuan->KodePengajuan ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-row-label">Perusahaan</span>
                        <span class="info-row-value">{{ $pengajuan->getPerusahaan->NamaLengkap ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-row-label">Jenis Permintaan</span>
                        <span class="info-row-value">
                            @if (($pengajuan->Jenis ?? null) == 1)
                                Medis
                            @else
                                Umum / Proyek
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-row-label">Status Saat Ini</span>
                        <span class="info-row-value">
                            @php
                                $status = strtolower($pengajuan->Status ?? '');
                                $pillClass = str_contains($status, 'approve')
                                    ? 'approve'
                                    : (str_contains($status, 'tolak')
                                        ? 'tolak'
                                        : 'default');
                            @endphp
                            <span class="status-pill {{ $pillClass }}">{{ $pengajuan->Status ?? '-' }}</span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-row-label">Tanggal Dibuat</span>
                        <span
                            class="info-row-value">{{ \Carbon\Carbon::parse($pengajuan->created_at)->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Main Content ── -->
        <div class="col-xl-9 col-md-12">

            <!-- Timeline Card -->
            <div class="trk-card">
                <div class="trk-card-header">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon-sm" viewBox="0 0 24 24" fill="none"
                        stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                    </svg>
                    <h5>Riwayat Tracking Pengajuan</h5>
                    @if ($pengajuan->getTracking->count())
                        <span class="count-badge">{{ $pengajuan->getTracking->count() }} proses</span>
                    @endif
                </div>
                <div class="trk-card-body">
                    @if ($pengajuan->getTracking->count())
                        <ul class="timeline-list">
                            @foreach ($pengajuan->getTracking as $track)
                                @php
                                    $jenis = strtolower($track->Jenis);
                                    $dotClass = str_contains($jenis, 'tolak')
                                        ? 'tolak'
                                        : (str_contains($jenis, 'approve')
                                            ? 'approve'
                                            : 'default');
                                    $initials = collect(explode(' ', $track->UserCreate))
                                        ->take(2)
                                        ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                                        ->join('');
                                @endphp
                                <li class="tl-item">
                                    <div class="tl-dot-wrap">
                                        <div class="tl-dot {{ $dotClass }}"></div>
                                    </div>
                                    <div class="tl-content">
                                        <div class="tl-top">
                                            <span class="tl-jenis">{{ $track->Jenis }}</span>
                                            <span class="tl-time">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon-xs" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10" />
                                                    <polyline points="12 6 12 12 16 14" />
                                                </svg>
                                                {{ \Carbon\Carbon::parse($track->created_at)->format('d M Y, H:i') }}
                                            </span>
                                        </div>
                                        <div class="tl-meta">
                                            <div class="tl-avatar">{{ $initials }}</div>
                                            <span class="tl-user">{{ $track->UserCreate }}</span>
                                        </div>
                                        @if ($track->Keterangan)
                                            <div class="tl-keterangan">{{ $track->Keterangan }}</div>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="trk-empty">
                            <div class="trk-empty-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                            </div>
                            <h6>Belum Ada Riwayat</h6>
                            <p>Belum ada riwayat tracking untuk pengajuan ini.</p>
                        </div>
                    @endif
                </div>
            </div>



        </div>
    </div>

@endsection
