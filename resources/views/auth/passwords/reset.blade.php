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
    <link rel="stylesheet" href="{{ asset('') }}assets/css/bootstrap.min.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{ asset('') }}assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="{{ asset('') }}assets/plugins/fontawesome/css/all.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('') }}assets/css/style.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body class="account-page">

    <div id="global-loader">
        <div class="whirly-loader"></div>
    </div>

    <div class="main-wrapper">
        <div class="account-content">
            <div class="login-wrapper">
                <!-- FORM -->
                <div class="login-content">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="login-userset">

                            <!-- LOGO -->
                            <div class="row">
                                <div class="col-md-12 text-center mb-4">
                                    <img src="{{ asset('assets/img/logo/logoawalbros.png') }}" style="width: 280px;"
                                        alt="Logo">
                                </div>
                            </div>

                            <!-- TITLE -->
                            <div class="row">
                                <div class="col-md-12 login-userheading">
                                    <h3>Reset Password</h3>
                                    <h4>Masukkan password baru Anda</h4>
                                </div>
                            </div>

                            <!-- GLOBAL ERROR -->
                            @if ($errors->any())
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- EMAIL -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-login">
                                        <label>Email</label>
                                        <div class="form-addons">
                                            <input type="email" name="email" value="{{ $email ?? old('email') }}"
                                                class="form-control @error('email') is-invalid @enderror"
                                                placeholder="Masukkan email" required autofocus>
                                            <img src="{{ asset('assets/img/icons/mail.svg') }}" alt="Email Icon">
                                        </div>
                                        @error('email')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- PASSWORD -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-login">
                                        <label>Password Baru</label>
                                        <div class="pass-group">
                                            <input type="password" name="password"
                                                class="pass-input form-control @error('password') is-invalid @enderror"
                                                placeholder="Masukkan password baru" required>
                                            <span class="fas toggle-password fa-eye-slash"></span>
                                        </div>
                                        @error('password')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- CONFIRM PASSWORD -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-login">
                                        <label>Konfirmasi Password</label>
                                        <div class="pass-group">
                                            <input type="password" name="password_confirmation"
                                                class="pass-input form-control" placeholder="Ulangi password" required>
                                            <span class="fas toggle-password fa-eye-slash"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- BUTTON -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-login">
                                        <button type="submit" class="btn btn-login w-100">
                                            Reset Password
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- BACK -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="text-center mt-3">
                                        <a href="{{ route('login') }}">← Kembali ke Login</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <!-- IMAGE -->
                <div class="login-img">
                    <img src="{{ asset('assets/img/authentication/login02.png') }}" alt="Login Image">
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="{{ asset('') }}assets/js/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('') }}assets/js/feather.min.js"></script>
    <script src="{{ asset('') }}assets/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('') }}assets/js/theme-script.js"></script>
    <script src="{{ asset('') }}assets/js/script.js"></script>
</body>

</html>
