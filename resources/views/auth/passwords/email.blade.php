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
    <style>
        /* Optional: You can tune your button & spinner position */
        .spinner-border {
            width: 1.5rem;
            height: 1.5rem;
        }
    </style>
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
                    <form method="POST" action="{{ route('password.email') }}" id="forgot-password-form">
                        @csrf

                        <div class="login-userset">

                            <!-- LOGO -->
                            <div class="text-center mb-4">
                                <img src="{{ asset('assets/img/logo/logoawalbros.png') }}" style="width: 280px;">
                            </div>

                            <!-- TITLE -->
                            <div class="login-userheading">
                                <h3>Lupa Password</h3>
                                <h4>Masukkan email untuk menerima link reset password</h4>
                            </div>

                            <!-- SUCCESS -->
                            @if (session('status'))
                                <div class="alert alert-success">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <!-- ERROR -->
                            @error('email')
                                <div class="alert alert-danger">
                                    {{ $message }}
                                </div>
                            @enderror

                            <!-- EMAIL -->
                            <div class="form-login">
                                <label>Email</label>
                                <div class="form-addons">
                                    <input type="email" name="email" value="{{ old('email') }}"
                                        class="form-control" placeholder="Masukkan email" required autofocus>
                                    <img src="{{ asset('assets/img/icons/mail.svg') }}">
                                </div>
                            </div>

                            <!-- BUTTON -->
                            <div class="form-login">
                                <button type="submit" class="btn btn-login w-100" id="submit-btn">
                                    <span id="btn-text">Kirim Link Reset</span>
                                    <span class="spinner-border spinner-border-sm align-middle ms-2 d-none"
                                        role="status" aria-hidden="true" id="spinner-submit"></span>
                                </button>
                            </div>

                            <!-- BACK -->
                            <div class="text-center mt-3">
                                <a href="{{ route('login') }}">Kembali ke Login</a>
                            </div>

                        </div>
                    </form>
                </div>

                <!-- IMAGE -->
                <div class="login-img">
                    <img src="{{ asset('assets/img/authentication/login02.png') }}">
                </div>

            </div>
        </div>
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

    <script>
        // Add loading when submit
        $(function() {
            $('#forgot-password-form').on('submit', function() {
                var $btn = $('#submit-btn');
                var $spinner = $('#spinner-submit');
                $btn.prop('disabled', true);
                $spinner.removeClass('d-none');
                $('#btn-text').text('Mengirim...');
            });
        });
    </script>

</body>

</html>
