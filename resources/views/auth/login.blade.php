<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="AB Proc - Sistem Administrasi dan Pengadaan">
    <meta name="keywords" content="AB Proc, administrasi, pengadaan, sistem, manajemen, modern, html5, responsive">
    <meta name="author" content="AB Proc Team">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ env('APP_NAME', 'CCP') }} - Login</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('') }}assets/img/favicon.png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('') }}as  sets/css/bootstrap.min.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{ asset('') }}assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="{{ asset('') }}assets/plugins/fontawesome/css/all.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('') }}assets/css/style.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body class="account-page">

    <div id="global-loader">
        <div class="whirly-loader"> </div>
    </div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <div class="account-content">
            <div class="login-wrapper">
                <div class="login-content">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="login-userset">
                            <div class="text-center mb-4">
                                <img src="{{ asset('assets/img/logo/logoawalbros.png') }}" alt="img"
                                    style="width: 280px; height: auto;">
                            </div>

                            <div class="login-userheading text-center">
                                <h3>Selamat Datang di AB Proc</h3>
                                <h4>Silakan masuk untuk melanjutkan</h4>
                            </div>


                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="form-login">
                                <label for="email">Alamat Email</label>
                                <div class="form-addons">
                                    <input type="email" name="email" id="email" class="form-control"
                                        value="{{ old('email') }}" placeholder="Email" required autofocus>
                                    <img src="{{ asset('') }}assets/img/icons/mail.svg" alt="img">
                                </div>
                            </div>
                            <div class="form-login">
                                <label for="password">Kata Sandi</label>
                                <div class="pass-group">
                                    <input type="password" placeholder="Kata Sandi" name="password" id="password"
                                        class="pass-input" required>
                                    <span class="fas toggle-password fa-eye-slash"></span>
                                </div>
                            </div>
                            <div class="form-login authentication-check">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="custom-control custom-checkbox">
                                            <label class="checkboxs ps-4 mb-0 pb-0 line-height-1">
                                                <input type="checkbox" name="remember"
                                                    {{ old('remember') ? 'checked' : '' }}>
                                                <span class="checkmarks"></span>Ingat saya
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <div class="g-recaptcha w-100"
                                    data-sitekey="{{ config('services.recaptcha.site_key') }}" style="width: 100%;">
                                </div>
                                @error('g-recaptcha-response')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-login d-flex flex-column align-items-center">
                                <button type="submit" class="btn btn-login mb-2">Masuk</button>
                                <a href="{{ route('password.request') }}" class="text-primary small"><strong>Reset
                                        Password</strong></a>

                            </div>


                        </div>
                    </form>

                </div>
                <div class="login-img">
                    <img src="{{ asset('') }}assets/img/authentication/login02.png" alt="img">
                </div>
            </div>
        </div>
    </div>
    <!-- /Main Wrapper -->

    <div class="customizer-links" id="setdata">
        <ul class="sticky-sidebar">
            <li class="sidebar-icons">
                <a href="#" class="navigation-add" data-bs-toggle="tooltip" data-bs-placement="left"
                    data-bs-original-title="Theme">
                    <i data-feather="settings" class="feather-five"></i>
                </a>
            </li>
        </ul>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('') }}assets/js/jquery-3.7.1.min.js"></script>

    <!-- Feather Icon JS -->
    <script src="{{ asset('') }}assets/js/feather.min.js"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('') }}assets/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script src="{{ asset('') }}assets/js/theme-script.js"></script>
    <script src="{{ asset('') }}assets/js/script.js"></script>

</body>

</html>
