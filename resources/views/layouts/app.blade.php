<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="AB Proc - Sistem Administrasi dan Pengadaan">
    <meta name="keywords" content="AB Proc, administrasi, pengadaan, sistem, manajemen, modern, html5, responsive">
    <meta name="author" content="AB Proc Team">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ env('APP_NAME', 'CCP') }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('') }}assets/img/favicon.png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('') }}assets/css/bootstrap.min.css">

    <!-- animation CSS -->
    <link rel="stylesheet" href="{{ asset('') }}assets/css/animate.css">

    <!-- Datatable CSS -->
    <link rel="stylesheet" href="{{ asset('') }}assets/css/dataTables.bootstrap5.min.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{ asset('') }}assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="{{ asset('') }}assets/plugins/fontawesome/css/all.min.css">
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('') }}assets/plugins/select2/css/select2.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('') }}assets/css/style.css">
    <link rel="stylesheet" href="{{ asset('assets/css/custom/abproc.css') }}">
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @stack('css')
    <style>
        /* ==========================================================
   SIDEBAR DREAMPOS — NAVY + PUTIH BOLD (VERSİ ANTI-KALAH)
   ========================================================== */

        /* 1) Container NAVY */
        html body .main-wrapper .sidebar,
        html body .main-wrapper .sidebar .sidebar-inner,
        html body .main-wrapper #sidebar-menu {
            background-color: #1e3a5f !important;
            border: none !important;
        }

        /* 2) Bersihkan garis/border bawaan */
        html body .main-wrapper .sidebar-menu ul,
        html body .main-wrapper .sidebar-menu ul li,
        html body .main-wrapper .sidebar-menu ul li a {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }

        /* 3) Judul grup */
        html body .main-wrapper .sidebar-menu .submenu-hdr {
            color: #9fb6d4 !important;
            font-size: .72rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: .09em !important;
            margin: 20px 0 6px !important;
            padding: 0 16px !important;
        }

        /* 4) Layout link menu */
        html body .main-wrapper .sidebar-menu ul li a {
            padding: 9px 12px !important;
            margin: 1px 6px !important;
            border-radius: 8px !important;
            font-size: .9rem !important;
            white-space: normal !important;
            overflow: visible !important;
            transition: background-color .18s ease !important;
        }

        /* 5) TEKS MENU: PUTIH + BOLD (termasuk span di dalamnya) */
        html body .main-wrapper .sidebar-menu ul li a,
        html body .main-wrapper .sidebar-menu ul li a span {
            color: #ffffff !important;
            font-weight: 700 !important;
        }

        /* 6) Icon putih lembut */
        html body .main-wrapper .sidebar-menu ul li a i,
        html body .main-wrapper .sidebar-menu ul li a svg,
        html body .main-wrapper .sidebar-menu ul li a .feather,
        html body .main-wrapper .sidebar svg.feather {
            color: rgba(255, 255, 255, .85) !important;
            stroke: rgba(255, 255, 255, .85) !important;
        }

        /* 7) Hover */
        html body .main-wrapper .sidebar-menu ul li a:hover {
            background-color: rgba(255, 255, 255, .12) !important;
        }

        html body .main-wrapper .sidebar-menu ul li a:hover,
        html body .main-wrapper .sidebar-menu ul li a:hover span {
            color: #ffffff !important;
        }

        html body .main-wrapper .sidebar-menu ul li a:hover i,
        html body .main-wrapper .sidebar-menu ul li a:hover svg,
        html body .main-wrapper .sidebar-menu ul li a:hover .feather {
            color: #ffffff !important;
            stroke: #ffffff !important;
        }

        /* 8) MENU AKTIF: azure selaras navy (teks putih, BUKAN oranye) */
        html body .main-wrapper .sidebar-menu ul li.active>a {
            background-color: #4dabf7 !important;
        }

        html body .main-wrapper .sidebar-menu ul li.active>a,
        html body .main-wrapper .sidebar-menu ul li.active>a span {
            color: #ffffff !important;
        }

        html body .main-wrapper .sidebar-menu ul li.active>a i,
        html body .main-wrapper .sidebar-menu ul li.active>a svg,
        html body .main-wrapper .sidebar-menu ul li.active>a .feather {
            color: #ffffff !important;
            stroke: #ffffff !important;
        }

        /* 9) Induk submenu terbuka */
        html body .main-wrapper .sidebar-menu ul li>a.subdrop {
            background-color: rgba(255, 255, 255, .14) !important;
        }

        html body .main-wrapper .sidebar-menu ul li>a.subdrop,
        html body .main-wrapper .sidebar-menu ul li>a.subdrop span {
            color: #ffffff !important;
        }

        /* 10) Panah submenu */
        html body .main-wrapper .sidebar-menu .menu-arrow {
            color: rgba(255, 255, 255, .65) !important;
        }

        html body .main-wrapper .sidebar-menu .menu-arrow::before,
        html body .main-wrapper .sidebar-menu .menu-arrow::after {
            border-color: rgba(255, 255, 255, .65) !important;
        }

        html body .main-wrapper .sidebar-menu li.active .menu-arrow,
        html body .main-wrapper .sidebar-menu li a.subdrop .menu-arrow {
            color: #ffffff !important;
        }

        /* 11) Submenu level 2: TANPA GARIS, tanpa bullet */
        html body .main-wrapper .sidebar-menu ul ul {
            list-style: none !important;
            margin: 4px 6px 6px 16px !important;
            padding-left: 8px !important;
            border: none !important;
        }

        html body .main-wrapper .sidebar-menu ul ul li::before,
        html body .main-wrapper .sidebar-menu ul ul li a::before {
            display: none !important;
            content: none !important;
        }

        html body .main-wrapper .sidebar-menu ul ul li a {
            padding: 7px 8px !important;
            margin: 1px 0 !important;
            font-size: .84rem !important;
            border-radius: 6px !important;
        }

        html body .main-wrapper .sidebar-menu ul ul li a,
        html body .main-wrapper .sidebar-menu ul ul li a span {
            color: rgba(255, 255, 255, .88) !important;
            font-weight: 600 !important;
        }

        html body .main-wrapper .sidebar-menu ul ul li a:hover {
            background-color: rgba(255, 255, 255, .10) !important;
        }

        html body .main-wrapper .sidebar-menu ul ul li.active>a {
            background-color: #4dabf7 !important;
        }

        html body .main-wrapper .sidebar-menu ul ul li.active>a,
        html body .main-wrapper .sidebar-menu ul ul li.active>a span {
            color: #ffffff !important;
        }

        /* 12) Card profil user */
        html body .main-wrapper .sidebar-menu>ul>li.submenu-open.d-flex {
            background-color: #162c47 !important;
            border: 1px solid rgba(255, 255, 255, .14) !important;
            border-radius: 12px !important;
            margin: 10px 8px 2px !important;
        }

        html body .main-wrapper .sidebar-menu>ul>li.submenu-open.d-flex .text-dark,
        html body .main-wrapper .sidebar-menu>ul>li.submenu-open.d-flex .fw-bold {
            color: #ffffff !important;
        }

        html body .main-wrapper .sidebar-menu>ul>li.submenu-open.d-flex .text-muted {
            color: #b9cde6 !important;
        }

        /* 13) Scrollbar halus */
        html body .main-wrapper .sidebar .slimScrollBar {
            background: rgba(255, 255, 255, .25) !important;
            width: 4px !important;
            border-radius: 4px !important;
            opacity: 1 !important;
        }
    </style>
    <style>
        .btn-ticket-trouble {
            background: linear-gradient(135deg, #4e73df, #224abe);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 8px 18px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(78, 115, 223, 0.35);
            transition: all 0.3s ease;
        }

        .btn-ticket-trouble:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(78, 115, 223, 0.45);
            color: white;
        }

        /* Animasi pulse untuk badge notifikasi */
        .badge.rounded-pill {
            animation: badgePulse 2s infinite;
        }

        @keyframes badgePulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        /* Hover effect untuk row approval */
        .approval-card {
            cursor: pointer;
        }

        .approval-card:hover {
            background-color: #f8f9fa;
        }

        .btn-info-alur {
            transition: all 0.3s ease;
        }

        .btn-info-alur:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(13, 202, 240, 0.4);
            background-color: #0dcaf0;
            border-color: #0dcaf0;
            color: #fff;
        }

        /* Fix agar modal muncul di atas sidebar/header */
        .modal-backdrop {
            z-index: 1040;
        }

        #alurPengajuanModal {
            z-index: 1050;
        }

        /* ========================================== */
        /* PERBAIKAN RESPONSIVE SIDEBAR UNTUK MOBILE  */
        /* ========================================== */
        @media (max-width: 991.98px) {
            .sidebar {
                left: -280px !important;
                width: 280px !important;
                transition: all 0.3s ease-in-out;
                z-index: 1050 !important;
                position: fixed !important;
                top: 0 !important;
                bottom: 0 !important;
                overflow-y: auto !important;
            }

            body.slide-nav .sidebar {
                left: 0 !important;
                box-shadow: 5px 0 15px rgba(0, 0, 0, 0.2);
            }

            body.slide-nav .main-wrapper::before {
                content: "";
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1040;
                transition: all 0.3s ease-in-out;
            }

            .header {
                z-index: 1060 !important;
                position: relative;
            }

            #mobile_btn {
                z-index: 1070 !important;
                position: relative;
                display: flex !important;
            }
        }

        @media (min-width: 992px) {
            .sidebar {
                left: 0 !important;
                position: fixed !important;
            }

            #mobile_btn {
                display: none !important;
            }
        }
    </style>
    @stack('css')
</head>

<body>

    <div class="main-wrapper">

        <!-- Header -->
        <div class="header">

            <!-- Logo -->
            <div class="header-left active">
                <a href="{{ route('home') }}" class="logo logo-normal">
                    <img src="{{ asset('assets/img/ccp/icon/mainlogo2.png') }}" alt=""
                        style="height: 80px; width: auto;">
                </a>
                <a href="{{ route('home') }}" class="logo logo-white">
                    <img src="{{ asset('assets/img/ccp/icon/mainlogo2.png') }}" alt=""
                        style="height: 80px; width: auto;">
                </a>
                <a href="{{ route('home') }}" class="logo-small">
                    <img src="{{ asset('assets/img/ccp/icon/mainlogo2.png') }}" alt=""
                        style="height: 70px; width: auto;">
                </a>
                <a id="toggle_btn" href="javascript:void(0);">
                    <i data-feather="chevrons-left" class="feather-16"></i>
                </a>
            </div>
            <!-- /Logo -->

            <!-- Tombol Mobile Hamburger -->
            <a id="mobile_btn" class="mobile_btn" href="#sidebar" style="z-index: 1070 !important; position: relative;">
                <span class="bar-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </a>

            <!-- Header Menu -->
            <ul class="nav user-menu">
                <!-- CDN FontAwesome -->
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
                <link rel="stylesheet"
                    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

                <!-- Button Pemicu Modal Info Alur -->
                <li class="nav-item">
                    <button type="button"
                        class="btn btn-outline-info btn-sm px-3 rounded-pill btn-info-alur animate__animated animate__pulse animate__infinite"
                        data-bs-toggle="modal" data-bs-target="#alurPengajuanModal" data-bs-backdrop="false"
                        style="--animate-duration: 1.2s;">
                        <i class="fa-solid fa-circle-info me-1"></i> Info Alur
                    </button>
                </li>

                <!-- Modal Content Info Alur -->
                <div class="modal fade" id="alurPengajuanModal" tabindex="-1" aria-hidden="true"
                    data-bs-backdrop="false">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                            <!-- HEADER MODAL -->
                            <div class="modal-header bg-primary text-white border-0 px-4 py-3">
                                <h5 class="modal-title fw-bold mb-0">
                                    <i class="fa-solid fa-diagram-project me-2"></i>Alur Pengajuan ABProc v2
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <!-- BODY MODAL -->
                            <div class="modal-body px-4 py-4">
                                <div class="alert alert-info d-flex align-items-center border-0 bg-light mb-4">
                                    <i class="fa-solid fa-lightbulb fa-2x me-3 text-secondary"></i>
                                    <div>
                                        <strong>Panduan Terbaru</strong>
                                        <p class="mb-0 small text-muted">Mohon baca perubahan alur di bawah ini agar
                                            pengajuan tidak tertunda.</p>
                                    </div>
                                </div>

                                <h6 class="fw-bold mb-3"><i class="fa-solid fa-list-check me-2"></i>Perubahan
                                    Signifikan:</h6>
                                <div class="list-group list-group-flush mb-4">
                                    <div class="list-group-item px-0 py-3 border-bottom">
                                        <div class="d-flex align-items-start">
                                            <div class="me-3">
                                                <span
                                                    class="badge bg-secondary-subtle text-secondary rounded-circle p-2">
                                                    <i class="fa-solid fa-signature"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-1">Penandatanganan FUI</h6>
                                                <p class="text-muted mb-1 small">FUI ditandatangani <strong>SETELAH
                                                        Presentasi</strong> dilakukan.</p>
                                                <span class="badge bg-danger-subtle text-danger small">
                                                    <i class="fa-solid fa-triangle-exclamation me-1"></i>Berubah dari
                                                    alur lama
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="list-group-item px-0 py-3 border-bottom">
                                        <div class="d-flex align-items-start">
                                            <div class="me-3">
                                                <span
                                                    class="badge bg-secondary-subtle text-secondary rounded-circle p-2">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-1">Lembar Disposisi</h6>
                                                <p class="text-muted mb-0 small">Tidak ada lagi lembar disposisi.
                                                    Proses lebih ringkas.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="list-group-item px-0 py-3">
                                        <div class="d-flex align-items-start">
                                            <div class="me-3">
                                                <span
                                                    class="badge bg-secondary-subtle text-secondary rounded-circle p-2">
                                                    <i class="fa-solid fa-users"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-1">Penandatangan HTA/GPA</h6>
                                                <p class="text-muted mb-0 small">Jumlah pejabat penandatangan
                                                    disesuaikan jenis pengajuan.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-light rounded-3 p-4">
                                    <h6 class="fw-bold text-center mb-4"><i class="fa-solid fa-route me-1"></i> Alur
                                        Singkat</h6>
                                    <div class="d-flex justify-content-center gap-4 mb-4">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-primary me-2"
                                                style="width:14px;height:14px;padding:0;">&nbsp;</span>
                                            <small class="fw-semibold">Pengaju</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-secondary me-2"
                                                style="width:14px;height:14px;padding:0;">&nbsp;</span>
                                            <small class="fw-semibold">Tim CCP</small>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-start justify-content-between text-center w-100 gap-1">
                                        <div class="flex-fill">
                                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                                style="width:42px;height:42px;"><i
                                                    class="fa-solid fa-file-invoice"></i></div>
                                            <div class="small fw-semibold">Permintaan</div>
                                            <span class="badge bg-primary bg-opacity-10 text-primary mt-1"
                                                style="font-size:10px;">Pengaju</span>
                                        </div>
                                        <div class="pt-2"><i class="fa-solid fa-chevron-right text-muted"></i></div>
                                        <div class="flex-fill">
                                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                                style="width:42px;height:42px;"><i
                                                    class="fa-solid fa-paper-plane"></i></div>
                                            <div class="small fw-semibold">Pengajuan</div>
                                            <span class="badge bg-primary bg-opacity-10 text-primary mt-1"
                                                style="font-size:10px;">Pengaju</span>
                                        </div>
                                        <div class="pt-2"><i class="fa-solid fa-chevron-right text-muted"></i></div>
                                        <div class="flex-fill">
                                            <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                                style="width:42px;height:42px;"><i
                                                    class="fa-solid fa-clipboard-check"></i></div>
                                            <div class="small fw-semibold">Review CCP</div>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary mt-1"
                                                style="font-size:10px;">Tim CCP</span>
                                        </div>
                                        <div class="pt-2"><i class="fa-solid fa-chevron-right text-muted"></i></div>
                                        <div class="flex-fill">
                                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                                style="width:42px;height:42px;"><i
                                                    class="fa-solid fa-file-signature"></i></div>
                                            <div class="small fw-semibold">Simpan FUI & FS</div>
                                            <span class="badge bg-primary bg-opacity-10 text-primary mt-1"
                                                style="font-size:10px;">Pengaju</span>
                                        </div>
                                        <div class="pt-2"><i class="fa-solid fa-chevron-right text-muted"></i></div>
                                        <div class="flex-fill">
                                            <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                                style="width:42px;height:42px;"><i
                                                    class="fa-solid fa-chalkboard-user"></i></div>
                                            <div class="small fw-semibold">Presentasi Komite</div>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary mt-1"
                                                style="font-size:10px;">Tim CCP</span>
                                        </div>
                                        <div class="pt-2"><i class="fa-solid fa-chevron-right text-muted"></i></div>
                                        <div class="flex-fill">
                                            <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                                style="width:42px;height:42px;"><i
                                                    class="fa-solid fa-circle-check"></i></div>
                                            <div class="small fw-semibold">Selesaikan</div>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary mt-1"
                                                style="font-size:10px;">Tim CCP</span>
                                        </div>
                                        <div class="pt-2"><i class="fa-solid fa-chevron-right text-muted"></i></div>
                                        <div class="flex-fill">
                                            <div class="bg-dark text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                                style="width:42px;height:42px;"><i
                                                    class="fa-solid fa-flag-checkered"></i></div>
                                            <div class="small fw-semibold">Selesai</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- FOOTER MODAL -->
                            <div class="modal-footer border-0 bg-light px-4 py-3 justify-content-end d-flex">
                                <button type="button" class="btn btn-light px-4 fw-semibold me-2"
                                    data-bs-dismiss="modal">
                                    <i class="fa-solid fa-xmark me-1"></i> Tutup
                                </button>
                                <button type="button" class="btn btn-primary px-4 fw-semibold"
                                    data-bs-dismiss="modal">
                                    <i class="fa-solid fa-check-circle me-1"></i> Saya Mengerti
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Modal Content Info Alur -->

                <li class="nav-item nav-searchinputs">
                    {{-- <button class="btn btn-ticket-trouble" onclick="window.open('{{ route('ticket.index') }}', '_blank');" type="button">
                        <i class="bi bi-headset"></i> Buat Ticket Trouble
                    </button> --}}
                </li>

                <li class="nav-item dropdown">
                    <a href="javascript:void(0);" class="nav-link userset dropdown-toggle" title="Profil"
                        id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="user-info">
                            <span class="user-detail">
                                <span class="user-name">{{ auth()->user()->name ?? 'Pengguna' }}</span>
                                <span
                                    class="user-role">{{ implode(', ', auth()->user()->getRoleNames()->toArray() ?? []) }}</span>
                            </span>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('users.show', encrypt(auth()->id())) }}">
                                <i class="fas fa-user me-2"></i> Profil Saya
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <button class="btn btn-danger" style="margin-left: 10px;" title="Keluar"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2" style="width: 18px; height: 18px;"></i>Keluar
                    </button>
                </li>
            </ul>
            <!-- /Header Menu -->

            <!-- Mobile Menu Dropdown (Hanya 1, duplikat dihapus) -->
            <div class="dropdown mobile-user-menu">
                <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="fa fa-ellipsis-v"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="{{ route('users.show', encrypt(auth()->id())) }}">My Profile</a>
                    <a class="dropdown-item" href="javascript:void(0);">Settings</a>
                    <a class="dropdown-item" href="javascript:void(0);"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                </div>
            </div>
            <!-- /Mobile Menu -->
        </div>
        <!-- /Header -->

        <!-- Sidebar Utama -->
        <div class="sidebar" id="sidebar" style="background: #212529;">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="submenu-open d-flex flex-column align-items-center py-4 mb-3"
                            style="background: #343a40; border-radius: 14px;">
                            <div class="position-relative mb-2">
                                @php $imgSize = 90; @endphp
                                @if (Auth::user() && Auth::user()->foto)
                                    <img src="{{ asset('storage/upload/foto/' . Auth::user()->foto) }}"
                                        alt="Foto Profil" width="{{ $imgSize }}" height="{{ $imgSize }}"
                                        class="shadow"
                                        style="width: {{ $imgSize }}px !important; height: {{ $imgSize }}px !important; object-fit: cover; border-radius: 8px; border: 3px solid #e0e0e0;">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'User') }}&background=6c757d&color=fff&size={{ $imgSize }}"
                                        alt="Foto Profil Default" width="{{ $imgSize }}"
                                        height="{{ $imgSize }}" class="shadow"
                                        style="width: {{ $imgSize }}px !important; height: {{ $imgSize }}px !important; object-fit: cover; border-radius: 8px; border: 3px solid #e0e0e0;">
                                @endif
                                <span class="position-absolute bottom-0 end-0 p-1 bg-white rounded-circle border"
                                    style="box-shadow: 0 1px 5px rgba(0,0,0,0.08);">
                                    <i class="fa fa-user-circle text-secondary" style="font-size:1rem;"></i>
                                </span>
                            </div>
                            <div class="text-center">
                                <span class="fw-bold text-white"
                                    style="font-size: 1.1rem;">{{ Str::limit(Auth::user()->name ?? 'User', 16) }}</span>
                                @if (Auth::user() && Auth::user()->email)
                                    <div class="small text-white-50" style="font-size: 0.9rem;">
                                        {{ Str::limit(Auth::user()->email, 22) }}</div>
                                @endif
                            </div>
                        </li>

                        <li class="submenu-open">
                            <h6 class="submenu-hdr fw-bold text-white">Dashboard</h6>
                            <ul>
                                <li
                                    class="{{ Request::segment(1) == '' || Request::segment(1) == 'home' ? 'active' : '' }}">
                                    <a href="{{ route('home') }}" class="fw-bold text-white"><i
                                            data-feather="home"></i><span>Dashboard</span></a>
                                </li>
                                <li class="{{ request()->routeIs('approval-saya.*') ? 'active' : '' }}">
                                    <a href="{{ route('approval-saya.index') }}"
                                        class="nav-link fw-bold text-white {{ request()->routeIs('approval-saya.*') ? 'active' : '' }}">
                                        <i class="fa fa-tasks me-2"></i><span>Approval Saya</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="submenu-open">
                            <h6 class="submenu-hdr fw-bold text-white">Form Pengajuan</h6>
                            <ul>
                                @can('permintaan-list')
                                    <li class="{{ Request::segment(1) == 'permintaan-pembelian' ? 'active' : '' }}">
                                        <a href="{{ route('pp.index') }}" class="fw-bold text-white"><i
                                                data-feather="file-text"></i><span>Permintaan Pembelian</span></a>
                                    </li>
                                @endcan
                                @can('pengajuan-pembelian-list')
                                    <li class="{{ Request::segment(1) == 'ajukan-pembelian' ? 'active' : '' }}">
                                        <a href="{{ route('ajukan.index') }}" class="fw-bold text-white"><i
                                                data-feather="edit"></i><span>Ajukan
                                                Pembelian</span></a>
                                    </li>
                                @endcan
                                @can('rekomendasi-list')
                                    <li class="{{ Request::segment(1) == 'rekomendasi' ? 'active' : '' }}">
                                        <a href="{{ route('rekomendasi.index') }}" class="fw-bold text-white"><i
                                                data-feather="thumbs-up"></i><span>Rekomendasi</span></a>
                                    </li>
                                @endcan
                            </ul>
                        </li>

                        @can('laporan-rekomendasi')
                            <li class="submenu-open">
                                <h6 class="submenu-hdr fw-bold text-white">Laporan</h6>
                                <ul>
                                    <li class="submenu">
                                        <a href="javascript:void(0);"
                                            class="fw-bold text-white {{ Request::segment(1) == 'laporan' ? 'active subdrop' : '' }}">
                                            <i data-feather="bar-chart-2"></i><span>Laporan</span><span
                                                class="menu-arrow"></span>
                                        </a>
                                        <ul>
                                            @can('laporan-rekomendasi-ccp')
                                                <li>
                                                    <a href="{{ route('rekomendasi.laporan') }}"
                                                        class="fw-bold text-white {{ Request::segment(2) == 'rekomendasi-ccp' ? 'active' : '' }}">
                                                        <i data-feather="check-circle"></i><span>Rekomendasi CCP</span>
                                                    </a>
                                                </li>
                                            @endcan
                                            <li>
                                                <a href="{{ route('laporan.history') }}"
                                                    class="fw-bold text-white {{ Request::segment(2) == 'history' ? 'active' : '' }}">
                                                    <i data-feather="book-open"></i><span>History Pembelian Alat</span>
                                                </a>
                                            </li>
                                            @can('laporan-total-pembelian')
                                                <li>
                                                    <a href="{{ route('laporan.total-pembelian') }}"
                                                        class="fw-bold text-white {{ Request::segment(2) == 'total-pembelian' ? 'active' : '' }}">
                                                        <i data-feather="dollar-sign"></i><span>Total Pembelian</span>
                                                    </a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        @endcan

                        @can('perencanaan-dan-anggaran')
                            <li class="submenu-open">
                                <h6 class="submenu-hdr fw-bold text-white">Perencanaan dan Anggaran</h6>
                                <ul>
                                    <li class="{{ Request::segment(1) == 'rkap' ? 'active' : '' }}">
                                        <a href="{{ route('rkap.index') }}" class="fw-bold text-white"><i
                                                data-feather="target"></i><span>RKAP</span></a>
                                    </li>
                                </ul>
                            </li>
                        @endcan

                        @can('kelola-pengguna')
                            <li class="submenu-open">
                                <h6 class="submenu-hdr fw-bold text-white">Kelola Pengguna</h6>
                                <ul>
                                    @can('user-list')
                                        <li class="{{ Request::segment(1) == 'users' ? 'active' : '' }}">
                                            <a href="{{ route('users.index') }}" class="fw-bold text-white"><i
                                                    data-feather="user"></i><span>Akun</span></a>
                                        </li>
                                    @endcan
                                    @can('role-list')
                                        <li class="{{ Request::segment(1) == 'roles' ? 'active' : '' }}">
                                            <a href="{{ route('roles.index') }}" class="fw-bold text-white"><i
                                                    data-feather="shield"></i><span>Role</span></a>
                                        </li>
                                    @endcan
                                    @can('permission-list')
                                        <li class="{{ Request::segment(1) == 'permission' ? 'active' : '' }}">
                                            <a href="{{ route('permission.index') }}" class="fw-bold text-white"><i
                                                    data-feather="lock"></i><span>Permission</span></a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endcan

                        @can('pengaturan-pengajuan')
                            <li class="submenu-open">
                                <h6 class="submenu-hdr fw-bold text-white">Pengaturan</h6>
                                <ul>
                                    <li class="{{ Request::segment(1) == 'pengaturan' ? 'active' : '' }}">
                                        <a href="{{ route('pengaturan.index') }}" class="fw-bold text-white"><i
                                                data-feather="settings"></i><span>Pengaturan Pengajuan</span></a>
                                    </li>
                                </ul>
                            </li>
                        @endcan

                        @can('data-master')
                            <li class="submenu-open">
                                <h6 class="submenu-hdr fw-bold text-white">Master Data</h6>
                                <ul>
                                    <li class="submenu">
                                        <a href="javascript:void(0);"
                                            class="fw-bold text-white {{ Request::segment(1) == 'master' ? 'active subdrop' : '' }}">
                                            <i data-feather="database"></i><span>Master Data</span><span
                                                class="menu-arrow"></span>
                                        </a>
                                        <ul>
                                            @can('perusahaan-list')
                                                <li><a href="{{ route('perusahaan.index') }}"
                                                        class="fw-bold text-white {{ Request::segment(2) == 'perusahaan' ? 'active' : '' }}"><i
                                                            data-feather="briefcase"></i><span>Perusahaan</span></a></li>
                                            @endcan
                                            @can('departemen-list')
                                                <li><a href="{{ route('departemen.index') }}"
                                                        class="fw-bold text-white {{ Request::segment(2) == 'departemen' ? 'active' : '' }}"><i
                                                            data-feather="grid"></i><span>Departemen</span></a></li>
                                            @endcan
                                            @can('jabatan-list')
                                                <li><a href="{{ route('jabatan.index') }}"
                                                        class="fw-bold text-white {{ Request::segment(2) == 'jabatan' ? 'active' : '' }}"><i
                                                            data-feather="grid"></i><span>Jabatan</span></a></li>
                                            @endcan
                                            @can('satuan-barang-list')
                                                <li><a href="{{ route('satuan.index') }}"
                                                        class="fw-bold text-white {{ Request::segment(2) == 'satuan' ? 'active' : '' }}"><i
                                                            data-feather="tag"></i><span>Satuan Barang</span></a></li>
                                            @endcan
                                            @can('master-merk-list')
                                                <li><a href="{{ route('merk.index') }}"
                                                        class="fw-bold text-white {{ Request::segment(2) == 'merk' ? 'active' : '' }}"><i
                                                            data-feather="award"></i><span>Merek</span></a></li>
                                            @endcan
                                            @can('barang-list')
                                                <li><a href="{{ route('barang.index') }}"
                                                        class="fw-bold text-white {{ Request::segment(2) == 'barang' ? 'active' : '' }}"><i
                                                            data-feather="box"></i><span>Barang</span></a></li>
                                            @endcan
                                            @can('vendor-list')
                                                <li><a href="{{ route('vendor.index') }}"
                                                        class="fw-bold text-white {{ Request::segment(2) == 'vendor' ? 'active' : '' }}"><i
                                                            data-feather="truck"></i><span>Vendor</span></a></li>
                                            @endcan
                                            @can('parameter-list')
                                                <li><a href="{{ route('parameter.index') }}"
                                                        class="fw-bold text-white {{ Request::segment(2) == 'parameter' ? 'active' : '' }}"><i
                                                            data-feather="sliders"></i><span>Parameter</span></a></li>
                                            @endcan
                                            @can('nama-form-list')
                                                <li><a href="{{ route('nama-form.index') }}"
                                                        class="fw-bold text-white {{ Request::segment(2) == 'form' ? 'active' : '' }}"><i
                                                            data-feather="file-text"></i><span>Master Form</span></a></li>
                                            @endcan
                                            @can('master-approval-list')
                                                <li><a href="{{ route('master-approval.index') }}"
                                                        class="fw-bold text-white {{ Request::segment(2) == 'pengaturan-approval' ? 'active' : '' }}"><i
                                                            data-feather="file-text"></i><span>Master Approval</span></a></li>
                                            @endcan
                                            @can('jenis-pengajuan-list')
                                                <li><a href="{{ route('jenis-pengajuan.index') }}"
                                                        class="fw-bold text-white {{ Request::segment(2) == 'jenis-pengajuan' ? 'active' : '' }}"><i
                                                            data-feather="list"></i><span>Master Jenis Pengajuan</span></a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        @endcan
                    </ul>
                </div>
            </div>
        </div>

        <!-- /Sidebar -->

        <!-- BAGIAN collapsed-sidebar YANG MEMBUAT KONFLIK TELAH DIHAPUS SEPENUHNYA DI SINI -->

        <div class="page-wrapper pagehead">
            <div class="content">
                @yield('content')
            </div>
        </div>

        <footer class="footer border-top shadow-sm"
            style="background-color: #ffffff !important; border-top: 1px solid rgba(255,255,255,.14) !important; color: #fff; position: fixed; bottom: 0; left: 0; width: 100%; z-index: 999; margin-top: 0; padding-top: 8px; padding-bottom: 8px; font-size: 0.88rem;">
            {{-- 1e3a5f --}}
            <div class="container text-center">
                <span class="fw-semibold" style="font-size: 0.92em; color: #1e3a5f !important;">
                    &copy; {{ date('Y') }} {{ env('APP_NAME', 'CCP') }}
                </span>
                <br>
                <span style="font-size: 0.9em; color: #1e3a5f !important;">
                    Dikembangkan dengan <i class="fas fa-heart" style="color: #f00909;"></i> oleh
                    <a href="https://dih-digital.com/" target="_blank" rel="noopener"
                        style="color: #1e3a5f; text-decoration: none; font-weight: 600;">PT DIGITAL INDONESIA HEBAT</a>
                </span>
            </div>


        </footer>
        <div style="height: 70px;"></div>

    </div>
    <!-- /main-wrapper -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="{{ asset('') }}assets/plugins/select2/js/select2.min.js"></script>
    <!-- Feather Icon JS -->
    <script src="{{ asset('') }}assets/js/feather.min.js"></script>
    <!-- Slimscroll JS -->
    <script src="{{ asset('') }}assets/js/jquery.slimscroll.min.js"></script>
    <script src="{{ asset('') }}assets/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('') }}assets/js/dataTables.bootstrap5.min.js"></script>
    <!-- Bootstrap Core JS -->
    <script src="{{ asset('') }}assets/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script src="{{ asset('') }}assets/js/theme-script.js"></script>
    <script src="{{ asset('') }}assets/js/script.js"></script>
    <script src="{{ asset('') }}assets/js/custom-select2.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- SCRIPT PENDUKUNG MODAL -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modalElement = document.getElementById('alurPengajuanModal');
            if (modalElement) {
                var myModal = new bootstrap.Modal(modalElement);
                modalElement.addEventListener('hidden.bs.modal', function() {
                    var backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());
                });
            }
        });
    </script>

    <!-- SCRIPT KHUSUS UNTUK MEMPERBAIKI SIDEBAR MOBILE -->
    <script>
        $(document).ready(function() {
            // 1. Fungsi Toggle Sidebar Mobile
            $('#mobile_btn').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $('body').toggleClass('slide-nav');
            });

            // 2. Tutup sidebar saat mengklik area luar (overlay) di mobile
            $(document).on('click', function(e) {
                if ($(window).width() <= 991) {
                    if (!$(e.target).closest('#sidebar').length && !$(e.target).closest('#mobile_btn')
                        .length) {
                        $('body').removeClass('slide-nav');
                    }
                }
            });

            // 3. Pastikan sidebar tertutup otomatis saat resize layar ke desktop
            $(window).on('resize', function() {
                if ($(window).width() > 991) {
                    $('body').removeClass('slide-nav');
                }
            });
        });
    </script>

    <!-- SCRIPT SESSION TIMEOUT -->
    <script>
        const sessionLifetime = {{ config('session.lifetime') }} * 60 * 1000;
        const warningTime = 60 * 1000;

        setTimeout(function() {
            Swal.fire({
                title: 'Sesi Hampir Berakhir',
                text: 'Sesi Anda akan segera berakhir. Lanjutkan?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Lanjutkan',
                cancelButtonText: 'Keluar',
                allowOutsideClick: false,
                allowEscapeKey: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                } else {
                    window.location.href = "{{ route('logout') }}";
                }
            });
        }, sessionLifetime - warningTime);

        setTimeout(function() {
            window.location.href = "{{ route('logout') }}";
        }, sessionLifetime);
    </script>

    @stack('js')
</body>

</html>
