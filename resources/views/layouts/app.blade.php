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
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- //summernote --}}
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

            <a id="mobile_btn" class="mobile_btn" href="#sidebar">
                <span class="bar-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </a>

            <!-- Header Menu -->
            <ul class="nav user-menu">


                <li class="nav-item nav-searchinputs">

                </li>


                <li class="nav-item dropdown">
                    <a href="javascript:void(0);" class="nav-link userset dropdown-toggle" title="Profil"
                        id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="user-info">

                            <span class="user-detail">
                                {{-- <span class="user-name">{{ auth()->user()->name ?? 'Pengguna' }}</span> --}}
                                <span class="user-name">{{ auth()->user()->name ?? 'Pengguna' }}</span>
                                <span class="user-role">
                                    {{ implode(', ', auth()->user()->getRoleNames()->toArray() ?? []) }}
                                </span>
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

            <!-- Mobile Menu -->
            <div class="dropdown mobile-user-menu">
                <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                    aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="profile.html">My Profile</a>
                    <a class="dropdown-item" href="general-settings.html">Settings</a>
                    <a class="dropdown-item" href="signin.html">Logout</a>
                </div>
            </div>
            <!-- /Mobile Menu -->
        </div>
        <!-- /Header Menu -->

        <!-- Mobile Menu -->
        <div class="dropdown mobile-user-menu">
            <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="profile.html">My Profile</a>
                <a class="dropdown-item" href="general-settings.html">Settings</a>
                <a class="dropdown-item" href="signin.html">Logout</a>
            </div>
        </div>
        <!-- /Mobile Menu -->
    </div>
    <!-- /Header -->

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-inner slimscroll">
            <div id="sidebar-menu" class="sidebar-menu">
                <ul>
                    <li class="submenu-open d-flex flex-column align-items-center py-4 mb-3"
                        style="background: #f8f9fa; border-radius: 14px;">
                        <div class="position-relative mb-2">
                            @php
                                $imgSize = 90;
                            @endphp
                            @if (Auth::user() && Auth::user()->foto)
                                <img src="{{ asset('storage/upload/foto/' . Auth::user()->foto) }}" alt="Foto Profil"
                                    width="{{ $imgSize }}" height="{{ $imgSize }}" class="shadow"
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
                            <span class="fw-bold text-dark" style="font-size: 1.1rem;">
                                {{ Str::limit(Auth::user()->name ?? 'User', 16) }}
                            </span>
                            @if (Auth::user() && Auth::user()->email)
                                <div class="text-muted small" style="font-size: 0.9rem;">
                                    {{ Str::limit(Auth::user()->email, 22) }}</div>
                            @endif
                        </div>
                    </li>
                    <li class="submenu-open">
                        <h6 class="submenu-hdr">Dashboard</h6>
                        <ul>
                            <li
                                class="{{ Request::segment(1) == '' || Request::segment(1) == 'home' ? 'active' : '' }}">
                                <a href="{{ route('home') }}">
                                    <i data-feather="home"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="submenu-open">
                        <h6 class="submenu-hdr">Form Pengajuan</h6>
                        <ul>
                            @can('permintaan-list')
                                <li class="{{ Request::segment(1) == 'permintaan-pembelian' ? 'active' : '' }}">
                                    <a href="{{ route('pp.index') }}">
                                        <i data-feather="file-text"></i>
                                        <span>Permintaan Pembelian</span>
                                    </a>
                                </li>
                            @endcan
                            @can('pengajuan-pembelian-list')
                                <li class="{{ Request::segment(1) == 'ajukan-pembelian' ? 'active' : '' }}">
                                    <a href="{{ route('ajukan.index') }}">
                                        <i data-feather="edit"></i>
                                        <span>Ajukan Pembelian</span>
                                    </a>
                                </li>
                            @endcan
                            @can('rekomendasi-list')
                                <li class="{{ Request::segment(1) == 'rekomendasi' ? 'active' : '' }}">
                                    <a href="{{ route('rekomendasi.index') }}">
                                        <i data-feather="thumbs-up"></i>
                                        <span>Rekomendasi</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                    @can('laporan-rekomendasi')
                        <li class="submenu-open">
                            <h6 class="submenu-hdr">Laporan</h6>
                            <ul>
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        class="{{ Request::segment(1) == 'laporan' ? 'active subdrop' : '' }}">
                                        <i data-feather="bar-chart-2"></i>
                                        <span>Laporan</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li>
                                            <a href="{{ route('rekomendasi.laporan') }}"
                                                class="{{ Request::segment(2) == 'rekomendasi-ccp' ? 'active' : '' }}">
                                                <i data-feather="check-circle"></i>
                                                <span>Rekomendasi CCP</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('laporan.history') }}"
                                                class="{{ Request::segment(2) == 'history' ? 'active' : '' }}">
                                                <i data-feather="book-open"></i>
                                                <span>History Pembelian Alat</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    @endcan


                    @can('perencanaan-dan-anggaran')
                        <li class="submenu-open">
                            <h6 class="submenu-hdr">Perencanaan dan Anggaran</h6>
                            <ul>
                                <li class="{{ Request::segment(1) == 'rkap' ? 'active' : '' }}">
                                    <a href="{{ route('rkap.index') }}">
                                        <i data-feather="target"></i>
                                        <span>RKAP</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endcan
                    @can('kelola-pengguna')
                        <li class="submenu-open">
                            <h6 class="submenu-hdr">Kelola Pengguna</h6>
                            <ul>
                                @can('user-list')
                                    <li class="{{ Request::segment(1) == 'users' ? 'active' : '' }}">
                                        <a href="{{ route('users.index') }}">
                                            <i data-feather="user"></i>
                                            <span>Akun</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('role-list')
                                    <li class="{{ Request::segment(1) == 'roles' ? 'active' : '' }}">
                                        <a href="{{ route('roles.index') }}">
                                            <i data-feather="shield"></i>
                                            <span>Role</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('permission-list')
                                    <li class="{{ Request::segment(1) == 'permission' ? 'active' : '' }}">
                                        <a href="{{ route('permission.index') }}">
                                            <i data-feather="lock"></i>
                                            <span>Permission</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcan
                    @can('pengaturan-pengajuan')
                        <li class="submenu-open">
                            <h6 class="submenu-hdr">Pengaturan</h6>
                            <ul>
                                <li class="{{ Request::segment(1) == 'pengaturan' ? 'active' : '' }}">
                                    <a href="{{ route('pengaturan.index') }}">
                                        <i data-feather="settings"></i>
                                        <span>Pengaturan Pengajuan</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endcan
                    @can('data-master')
                        <li class="submenu-open">
                            <h6 class="submenu-hdr">Master Data</h6>
                            <ul>
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        class="{{ Request::segment(1) == 'master' ? 'active subdrop' : '' }}">
                                        <i data-feather="database"></i>
                                        <span>Master Data</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @can('perusahaan-list')
                                            <li>
                                                <a href="{{ route('perusahaan.index') }}"
                                                    class="{{ Request::segment(2) == 'perusahaan' ? 'active' : '' }}">
                                                    <i data-feather="briefcase"></i>
                                                    <span>Perusahaan</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('departemen-list')
                                            <li>
                                                <a href="{{ route('departemen.index') }}"
                                                    class="{{ Request::segment(2) == 'departemen' ? 'active' : '' }}">
                                                    <i data-feather="grid"></i>
                                                    <span>Departemen</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('jabatan-list')
                                            <li>
                                                <a href="{{ route('jabatan.index') }}"
                                                    class="{{ Request::segment(2) == 'jabatan' ? 'active' : '' }}">
                                                    <i data-feather="grid"></i>
                                                    <span>Jabatan</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('satuan-barang-list')
                                            <li>
                                                <a href="{{ route('satuan.index') }}"
                                                    class="{{ Request::segment(2) == 'satuan' ? 'active' : '' }}">
                                                    <i data-feather="tag"></i>
                                                    <span>Satuan Barang</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('master-merk-list')
                                            <li>
                                                <a href="{{ route('merk.index') }}"
                                                    class="{{ Request::segment(2) == 'merk' ? 'active' : '' }}">
                                                    <i data-feather="award"></i>
                                                    <span>Merek</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('barang-list')
                                            <li>
                                                <a href="{{ route('barang.index') }}"
                                                    class="{{ Request::segment(2) == 'barang' ? 'active' : '' }}">
                                                    <i data-feather="box"></i>
                                                    <span>Barang</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('vendor-list')
                                            <li>
                                                <a href="{{ route('vendor.index') }}"
                                                    class="{{ Request::segment(2) == 'vendor' ? 'active' : '' }}">
                                                    <i data-feather="truck"></i>
                                                    <span>Vendor</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('parameter-list')
                                            <li>
                                                <a href="{{ route('parameter.index') }}"
                                                    class="{{ Request::segment(2) == 'parameter' ? 'active' : '' }}">
                                                    <i data-feather="sliders"></i>
                                                    <span>Parameter</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('nama-form-list')
                                            <li>
                                                <a href="{{ route('nama-form.index') }}"
                                                    class="{{ Request::segment(2) == 'form' ? 'active' : '' }}">
                                                    <i data-feather="file-text"></i>
                                                    <span>Master Form</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('master-approval-list')
                                            <li>
                                                <a href="{{ route('master-approval.index') }}"
                                                    class="{{ Request::segment(2) == 'pengaturan-approval' ? 'active' : '' }}">
                                                    <i data-feather="file-text"></i>
                                                    <span>Master Approval</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('jenis-pengajuan-list')
                                            <li>
                                                <a href="{{ route('jenis-pengajuan.index') }}"
                                                    class="{{ Request::segment(2) == 'jenis-pengajuan' ? 'active' : '' }}">
                                                    <i data-feather="list"></i>
                                                    <span>Master Jenis Pengajuan</span>
                                                </a>
                                            </li>
                                        @endcan
                                        {{-- Tambahkan menu master data lain di sini jika diperlukan --}}
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

    <!-- Sidebar -->
    <div class="sidebar collapsed-sidebar" id="collapsed-sidebar">
        <div class="sidebar-inner slimscroll">
            <div id="sidebar-menu-2" class="sidebar-menu sidebar-menu-three">
                <aside id="aside" class="ui-aside">
                    <ul class="tab nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="tablinks nav-link" href="#home" id="home-tab" data-bs-toggle="tab"
                                data-bs-target="#home" role="tab" aria-selected="true">
                                <img src="{{ asset('') }}assets/img/icons/menu-icon.svg" alt="">
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="tablinks nav-link" href="#messages" id="messages-tab" data-bs-toggle="tab"
                                data-bs-target="#product" role="tab" aria-selected="false">
                                <img src="{{ asset('') }}assets/img/icons/product.svg" alt="">
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="tablinks nav-link" href="#profile" id="profile-tab" data-bs-toggle="tab"
                                data-bs-target="#sales" role="tab" aria-selected="false">
                                <img src="{{ asset('') }}assets/img/icons/sales1.svg" alt="">
                            </a>
                        </li>

                        <li class="nav-item" role="presentation">
                            <a class="tablinks nav-link" href="#report" id="report-tab" data-bs-toggle="tab"
                                data-bs-target="#purchase" role="tab" aria-selected="true">
                                <img src="{{ asset('') }}assets/img/icons/purchase1.svg" alt="">
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="tablinks nav-link" href="#set" id="set-tab" data-bs-toggle="tab"
                                data-bs-target="#user" role="tab" aria-selected="true">
                                <img src="{{ asset('') }}assets/img/icons/users1.svg" alt="">
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="tablinks nav-link" href="#set2" id="set-tab2" data-bs-toggle="tab"
                                data-bs-target="#employee" role="tab" aria-selected="true">
                                <img src="{{ asset('') }}assets/img/icons/calendars.svg" alt="">
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="tablinks nav-link" href="#set3" id="set-tab3" data-bs-toggle="tab"
                                data-bs-target="#report" role="tab" aria-selected="true">
                                <img src="{{ asset('') }}assets/img/icons/printer.svg" alt="">
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="tablinks nav-link active" href="#set4" id="set-tab4" data-bs-toggle="tab"
                                data-bs-target="#document" role="tab" aria-selected="true">
                                <i data-feather="user"></i>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="tablinks nav-link" href="#set5" id="set-tab6" data-bs-toggle="tab"
                                data-bs-target="#permission" role="tab" aria-selected="true">
                                <i data-feather="file-text"></i>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="tablinks nav-link" href="#set6" id="set-tab5" data-bs-toggle="tab"
                                data-bs-target="#settings" role="tab" aria-selected="true">
                                <i data-feather="settings"></i>
                            </a>
                        </li>
                    </ul>
                </aside>
                <div class="tab-content tab-content-four pt-2">
                    <ul class="tab-pane" id="home" aria-labelledby="home-tab">
                        <li class="submenu">
                            <a href="javascript:void(0);"><span>Dashboard</span> <span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="index.html">Admin Dashboard</a></li>
                                <li><a href="sales-dashboard.html">Sales Dashboard</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><span>Application</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="chat.html">Chat</a></li>
                                <li class="submenu submenu-two"><a href="javascript:void(0);"><span>Call</span><span
                                            class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="video-call.html">Video Call</a></li>
                                        <li><a href="audio-call.html">Audio Call</a></li>
                                        <li><a href="call-history.html">Call History</a></li>
                                    </ul>
                                </li>
                                <li><a href="calendar.html">Calendar</a></li>
                                <li><a href="email.html">Email</a></li>
                                <li><a href="todo.html">To Do</a></li>
                                <li><a href="notes.html">Notes</a></li>
                                <li><a href="file-manager.html">File Manager</a></li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="tab-pane" id="product" aria-labelledby="messages-tab">
                        <li><a href="product-list.html"><span>Products</span></a></li>
                        <li><a href="add-product.html"><span>Create Product</span></a></li>
                        <li><a href="expired-products.html"><span>Expired Products</span></a></li>
                        <li><a href="low-stocks.html"><span>Low Stocks</span></a></li>
                        <li><a href="category-list.html"><span>Category</span></a></li>
                        <li><a href="sub-categories.html"><span>Sub Category</span></a></li>
                        <li><a href="brand-list.html"><span>Brands</span></a></li>
                        <li><a href="units.html"><span>Units</span></a></li>
                        <li><a href="varriant-attributes.html"><span>Variant Attributes</span></a></li>
                        <li><a href="warranty.html"><span>Warranties</span></a></li>
                        <li><a href="barcode.html"><span>Print Barcode</span></a></li>
                        <li><a href="qrcode.html"><span>Print QR Code</span></a></li>
                    </ul>
                    <ul class="tab-pane" id="sales" aria-labelledby="profile-tab">
                        <li><a href="sales-list.html"><span>Sales</span></a></li>
                        <li><a href="invoice-report.html"><span>Invoices</span></a></li>
                        <li><a href="sales-returns.html"><span>Sales Return</span></a></li>
                        <li><a href="quotation-list.html"><span>Quotation</span></a></li>
                        <li><a href="pos.html"><span>POS</span></a></li>
                        <li><a href="coupons.html"><span>Coupons</span></a></li>
                    </ul>
                    <ul class="tab-pane" id="purchase" aria-labelledby="report-tab">
                        <li><a href="purchase-list.html"><span>Purchases</span></a></li>
                        <li><a href="purchase-order-report.html"><span>Purchase Order</span></a></li>
                        <li><a href="purchase-returns.html"><span>Purchase Return</span></a></li>
                        <li><a href="manage-stocks.html"><span>Manage Stock</span></a></li>
                        <li><a href="stock-adjustment.html"><span>Stock Adjustment</span></a></li>
                        <li><a href="stock-transfer.html"><span>Stock Transfer</span></a></li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><span>Expenses</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="expense-list.html">Expenses</a></li>
                                <li><a href="expense-category.html">Expense Category</a></li>
                            </ul>
                        </li>

                    </ul>
                    <ul class="tab-pane" id="user" aria-labelledby="set-tab">

                        <li><a href="customers.html"><span>Customers</span></a></li>
                        <li><a href="suppliers.html"><span>Suppliers</span></a></li>
                        <li><a href="store-list.html"><span>Stores</span></a></li>
                        <li><a href="warehouse.html"><span>Warehouses</span></a></li>

                    </ul>
                    <ul class="tab-pane" id="employee" aria-labelledby="set-tab2">
                        <li><a href="employees-grid.html"><span>Employees</span></a></li>
                        <li><a href="department-grid.html"><span>Departments</span></a></li>
                        <li><a href="designation.html"><span>Designation</span></a></li>
                        <li><a href="shift.html"><span>Shifts</span></a></li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><span>Attendence</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="attendance-employee.html">Employee Attendence</a></li>
                                <li><a href="attendance-admin.html">Admin Attendence</a></li>
                            </ul>
                        <li class="submenu">
                            <a href="javascript:void(0);"><span>Leaves</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="leaves-admin.html">Admin Leaves</a></li>
                                <li><a href="leaves-employee.html">Employee Leaves</a></li>
                                <li><a href="leave-types.html">Leave Types</a></li>
                            </ul>
                        </li>
                        <li><a href="holidays.html"><span>Holidays</span></a></li>
                        <li class="submenu">
                            <a href="payroll-list.html"><span>Payroll</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="payroll-list.html">Employee Salary</a></li>
                                <li><a href="payslip.html">Payslip</a></li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="tab-pane" id="report" aria-labelledby="set-tab3">
                        <li><a href="sales-report.html"><span>Sales Report</span></a></li>
                        <li><a href="purchase-report.html"><span>Purchase report</span></a></li>
                        <li><a href="inventory-report.html"><span>Inventory Report</span></a></li>
                        <li><a href="invoice-report.html"><span>Invoice Report</span></a></li>
                        <li><a href="supplier-report.html"><span>Supplier Report</span></a></li>
                        <li><a href="customer-report.html"><span>Customer Report</span></a></li>
                        <li><a href="expense-report.html"><span>Expense Report</span></a></li>
                        <li><a href="income-report.html"><span>Income Report</span></a></li>
                        <li><a href="tax-reports.html"><span>Tax Report</span></a></li>
                        <li><a href="profit-and-loss.html"><span>Profit & Loss</span></a></li>
                    </ul>
                    <ul class="tab-pane" id="permission" aria-labelledby="set-tab4">
                        <li><a href="users.html"><span>Users</span></a></li>
                        <li><a href="roles-permissions.html"><span>Roles & Permissions</span></a></li>
                        <li><a href="delete-account.html"><span>Delete Account Request</span></a></li>

                        <li class="submenu">
                            <a href="javascript:void(0);">
                                <span>Base UI</span><span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="ui-alerts.html">Alerts</a></li>
                                <li><a href="ui-accordion.html">Accordion</a></li>
                                <li><a href="ui-avatar.html">Avatar</a></li>
                                <li><a href="ui-badges.html">Badges</a></li>
                                <li><a href="ui-borders.html">Border</a></li>
                                <li><a href="ui-buttons.html">Buttons</a></li>
                                <li><a href="ui-buttons-group.html">Button Group</a></li>
                                <li><a href="ui-breadcrumb.html">Breadcrumb</a></li>
                                <li><a href="ui-cards.html">Card</a></li>
                                <li><a href="ui-carousel.html">Carousel</a></li>
                                <li><a href="ui-colors.html">Colors</a></li>
                                <li><a href="ui-dropdowns.html">Dropdowns</a></li>
                                <li><a href="ui-grid.html">Grid</a></li>
                                <li><a href="ui-images.html">Images</a></li>
                                <li><a href="ui-lightbox.html">Lightbox</a></li>
                                <li><a href="ui-media.html">Media</a></li>
                                <li><a href="ui-modals.html">Modals</a></li>
                                <li><a href="ui-offcanvas.html">Offcanvas</a></li>
                                <li><a href="ui-pagination.html">Pagination</a></li>
                                <li><a href="ui-popovers.html">Popovers</a></li>
                                <li><a href="ui-progress.html">Progress</a></li>
                                <li><a href="ui-placeholders.html">Placeholders</a></li>
                                <li><a href="ui-rangeslider.html">Range Slider</a></li>
                                <li><a href="ui-spinner.html">Spinner</a></li>
                                <li><a href="ui-sweetalerts.html">Sweet Alerts</a></li>
                                <li><a href="ui-nav-tabs.html">Tabs</a></li>
                                <li><a href="ui-toasts.html">Toasts</a></li>
                                <li><a href="ui-tooltips.html">Tooltips</a></li>
                                <li><a href="ui-typography.html">Typography</a></li>
                                <li><a href="ui-video.html">Video</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);">
                                <span>Advanced UI</span><span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="ribbon.html">Ribbon</a></li>
                                <li><a href="clipboard.html">Clipboard</a></li>
                                <li><a href="drag-drop.html">Drag & Drop</a></li>
                                <li><a href="rangeslider.html">Range Slider</a></li>
                                <li><a href="rating.html">Rating</a></li>
                                <li><a href="text-editor.html">Text Editor</a></li>
                                <li><a href="counter.html">Counter</a></li>
                                <li><a href="scrollbar.html">Scrollbar</a></li>
                                <li><a href="stickynote.html">Sticky Note</a></li>
                                <li><a href="timeline.html">Timeline</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><span>Charts</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="chart-apex.html">Apex Charts</a></li>
                                <li><a href="chart-c3.html">Chart C3</a></li>
                                <li><a href="chart-js.html">Chart Js</a></li>
                                <li><a href="chart-morris.html">Morris Charts</a></li>
                                <li><a href="chart-flot.html">Flot Charts</a></li>
                                <li><a href="chart-peity.html">Peity Charts</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><span>Icons</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="icon-fontawesome.html">Fontawesome Icons</a></li>
                                <li><a href="icon-feather.html">Feather Icons</a></li>
                                <li><a href="icon-ionic.html">Ionic Icons</a></li>
                                <li><a href="icon-material.html">Material Icons</a></li>
                                <li><a href="icon-pe7.html">Pe7 Icons</a></li>
                                <li><a href="icon-simpleline.html">Simpleline Icons</a></li>
                                <li><a href="icon-themify.html">Themify Icons</a></li>
                                <li><a href="icon-weather.html">Weather Icons</a></li>
                                <li><a href="icon-typicon.html">Typicon Icons</a></li>
                                <li><a href="icon-flag.html">Flag Icons</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);">
                                <span>Forms</span><span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li class="submenu submenu-two">
                                    <a href="javascript:void(0);">Form Elements<span
                                            class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="form-basic-inputs.html">Basic Inputs</a></li>
                                        <li><a href="form-checkbox-radios.html">Checkbox & Radios</a></li>
                                        <li><a href="form-input-groups.html">Input Groups</a></li>
                                        <li><a href="form-grid-gutters.html">Grid & Gutters</a></li>
                                        <li><a href="form-select.html">Form Select</a></li>
                                        <li><a href="form-mask.html">Input Masks</a></li>
                                        <li><a href="form-fileupload.html">File Uploads</a></li>
                                    </ul>
                                </li>
                                <li class="submenu submenu-two">
                                    <a href="javascript:void(0);">Layouts<span
                                            class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="form-horizontal.html">Horizontal Form</a></li>
                                        <li><a href="form-vertical.html">Vertical Form</a></li>
                                        <li><a href="form-floating-labels.html">Floating Labels</a></li>
                                    </ul>
                                </li>
                                <li><a href="form-validation.html">Form Validation</a></li>
                                <li><a href="form-select2.html">Select2</a></li>
                                <li><a href="form-wizard.html">Form Wizard</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><span>Tables</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="tables-basic.html">Basic Tables </a></li>
                                <li><a href="data-tables.html">Data Table </a></li>
                            </ul>
                        </li>

                    </ul>
                    <ul class="tab-pane active" id="document" aria-labelledby="set-tab5">
                        <li><a href="profile.html"><span>Profile</span></a></li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><span>Authentication</span><span
                                    class="menu-arrow"></span></a>
                            <ul>
                                <li class="submenu submenu-two"><a href="javascript:void(0);">Login<span
                                            class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="signin.html">Cover</a></li>
                                        <li><a href="signin-2.html">Illustration</a></li>
                                        <li><a href="signin-3.html">Basic</a></li>
                                    </ul>
                                </li>
                                <li class="submenu submenu-two"><a href="javascript:void(0);">Register<span
                                            class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="register.html">Cover</a></li>
                                        <li><a href="register-2.html">Illustration</a></li>
                                        <li><a href="register-3.html">Basic</a></li>
                                    </ul>
                                </li>
                                <li class="submenu submenu-two"><a href="javascript:void(0);">Forgot Password<span
                                            class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="forgot-password.html">Cover</a></li>
                                        <li><a href="forgot-password-2.html">Illustration</a></li>
                                        <li><a href="forgot-password-3.html">Basic</a></li>
                                    </ul>
                                </li>
                                <li class="submenu submenu-two"><a href="javascript:void(0);">Reset Password<span
                                            class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="reset-password.html">Cover</a></li>
                                        <li><a href="reset-password-2.html">Illustration</a></li>
                                        <li><a href="reset-password-3.html">Basic</a></li>
                                    </ul>
                                </li>
                                <li class="submenu submenu-two"><a href="javascript:void(0);">Email
                                        Verification<span class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="email-verification.html">Cover</a></li>
                                        <li><a href="email-verification-2.html">Illustration</a></li>
                                        <li><a href="email-verification-3.html">Basic</a></li>
                                    </ul>
                                </li>
                                <li class="submenu submenu-two"><a href="javascript:void(0);">2 Step
                                        Verification<span class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="two-step-verification.html">Cover</a></li>
                                        <li><a href="two-step-verification-2.html">Illustration</a></li>
                                        <li><a href="two-step-verification-3.html">Basic</a></li>
                                    </ul>
                                </li>
                                <li><a href="lock-screen.html">Lock Screen</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><span>Error Pages</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="error-404.html">404 Error </a></li>
                                <li><a href="error-500.html">500 Error </a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><span>Places</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="countries.html">Countries</a></li>
                                <li><a href="states.html">States</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="blank-page.html" class="active"><span>Blank Page</span> </a>
                        </li>
                        <li>
                            <a href="coming-soon.html"><span>Coming Soon</span> </a>
                        </li>
                        <li>
                            <a href="under-maintenance.html"><span>Under Maintenance</span> </a>
                        </li>
                    </ul>
                    <ul class="tab-pane" id="settings" aria-labelledby="set-tab6">
                        <li class="submenu">
                            <a href="javascript:void(0);"><span>General Settings</span><span
                                    class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="general-settings.html">Profile</a></li>
                                <li><a href="security-settings.html">Security</a></li>
                                <li><a href="notification.html">Notifications</a></li>
                                <li><a href="connected-apps.html">Connected Apps</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><span>Website Settings</span><span
                                    class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="system-settings.html">System Settings</a></li>
                                <li><a href="company-settings.html">Company Settings </a></li>
                                <li><a href="localization-settings.html">Localization</a></li>
                                <li><a href="prefixes.html">Prefixes</a></li>
                                <li><a href="preference.html">Preference</a></li>
                                <li><a href="appearance.html">Appearance</a></li>
                                <li><a href="social-authentication.html">Social Authentication</a></li>
                                <li><a href="language-settings.html">Language</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><span>App Settings</span><span
                                    class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="invoice-settings.html">Invoice</a></li>
                                <li><a href="printer-settings.html">Printer</a></li>
                                <li><a href="pos-settings.html">POS</a></li>
                                <li><a href="custom-fields.html">Custom Fields</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><span>System Settings</span><span
                                    class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="email-settings.html">Email</a></li>
                                <li><a href="sms-gateway.html">SMS Gateways</a></li>
                                <li><a href="otp-settings.html">OTP</a></li>
                                <li><a href="gdpr-settings.html">GDPR Cookies</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><span>Financial Settings</span><span
                                    class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="payment-gateway-settings.html">Payment Gateway</a></li>
                                <li><a href="bank-settings-grid.html">Bank Accounts</a></li>
                                <li><a href="tax-rates.html">Tax Rates</a></li>
                                <li><a href="currency-settings.html">Currencies</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><span>Other Settings</span><span
                                    class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="storage-settings.html">Storage</a></li>
                                <li><a href="ban-ip-address.html">Ban IP Address</a></li>
                            </ul>
                        </li>
                        <li><a href="javascript:void(0);"><span>Documentation</span></a></li>
                        <li><a href="javascript:void(0);"><span>Changelog v2.0.3</span></a></li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><span>Multi Level</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="javascript:void(0);">Level 1.1</a></li>
                                <li class="submenu submenu-two"><a href="javascript:void(0);">Level 1.2<span
                                            class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="javascript:void(0);">Level 2.1</a></li>
                                        <li class="submenu submenu-two submenu-three"><a
                                                href="javascript:void(0);">Level 2.2<span
                                                    class="menu-arrow inside-submenu inside-submenu-two"></span></a>
                                            <ul>
                                                <li><a href="javascript:void(0);">Level 3.1</a></li>
                                                <li><a href="javascript:void(0);">Level 3.2</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


    <div class="page-wrapper pagehead">
        <div class="content">
            @yield('content')
        </div>
    </div>
    <footer class="footer bg-white border-top shadow-sm"
        style="position: fixed; bottom: 0; left: 0; width: 100%; z-index: 999; margin-top: 0; padding-top: 8px; padding-bottom: 8px; font-size: 0.88rem;">
        <div class="container text-center">
            <span class="fw-semibold text-secondary" style="font-size: 0.92em;">
                &copy; {{ date('Y') }} {{ env('APP_NAME', 'CCP') }}
            </span>
            <br>
            <span class="text-muted" style="font-size: 0.9em;">
                Dikembangkan dengan <i class="fas fa-heart text-danger"></i> oleh
                <a href="https://dih-digital.com/" target="_blank" rel="noopener">PT DIGITAL INDONESIA HEBAT</a>
            </span>
        </div>
    </footer>
    <div style="height: 70px;"></div>

    </div>



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
