<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login| HelpDesk</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('images/Hi-Tech_Water_Logo.png') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('portal/Login_v2/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portal/Login_v2/fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portal/Login_v2/fonts/iconic/css/material-design-iconic-font.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('portal/Login_v2/vendor/animate/animate.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portal/Login_v2/vendor/css-hamburgers/hamburgers.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portal/Login_v2/vendor/animsition/css/animsition.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('portal/Login_v2/vendor/select2/select2.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portal/Login_v2/vendor/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('portal/Login_v2/css/util.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('portal/Login_v2/css/main.css') }}">
</head>

<body>
    <div id="backgroundLogin">
        <div class="filter"></div> <!-- Semi-transparent overlay -->
        <div class="limiter">
            <div class="container-login100">
                <div class="wrap-login100">
                    <form class="login100-form validate-form" novalidate id="loginForm"
                        action="{{ URL::Route('login') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <span class="login100-form-title p-b-26">
                            <img class="logo-hitech" src="{{ asset('images/Hi-Tech_Water_Logo.png') }}" alt="">
                        </span>

                        @if ($errors->first('g-recaptcha-response'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ $errors->first('g-recaptcha-response') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="wrap-input100 validate-input {{ $errors->first('username') ? 'alert-validate' : '' }}"
                            data-validate="{{ $errors->first('username') }}">
                            <input class="input100" type="text" name="username" placeholder="Username"
                                value="{{ old('username', Cookie::get('remember_username')) }}">
                            <span class="text-danger">{{ $errors->first('username') }}</span>
                        </div>
                        <div class="wrap-input100 validate-input {{ $errors->first('password') ? 'alert-validate' : '' }}"
                            data-validate="{{ $errors->first('password') }}">
                            <span class="btn-show-pass">
                                <i class="zmdi zmdi-eye"></i>
                            </span>
                            <input class="input100" type="password" name="password" placeholder="Password"
                                value="{{ old('password', Cookie::get('remember_password')) }}">
                            <span class="text-danger">{{ $errors->first('password') }}</span>
                        </div>

                        <div class="flex-sb-m w-full p-b-20">
                            <div class="contact100-form-checkbox">
                                <input class="input-checkbox100" id="ckb1" type="checkbox" name="remember"
                                    {{ Cookie::get('remember_username') ? 'checked' : '' }}>
                                <span class="txt2"> {{ __('Remember me') }} </span>
                            </div>
                            <div>
                                <a href="#" class="txt2 d-none">
                                    {{ __('Forgot Password?') }}
                                </a>
                            </div>
                        </div>

                        <div class="container-login100-form-btn">
                            <div class="wrap-login100-form-btn">
                                <div class="login100-form-bgbtn"></div>
                                <button type="submit" class="login100-form-btn g-recaptcha"
                                    data-sitekey="{{ config('services.recaptcha.site_key') }}" data-callback='onSubmit'
                                    data-action='submit'>
                                    Login
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="dropDownSelect1"></div>

    <script src="{{ asset('portal/Login_v2/vendor/jquery/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('portal/Login_v2/vendor/animsition/js/animsition.min.js') }}"></script>
    <script src="{{ asset('portal/Login_v2/vendor/bootstrap/js/popper.js') }}"></script>
    <script src="{{ asset('portal/Login_v2/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('portal/Login_v2/vendor/select2/select2.min.js') }}"></script>
    <script src="{{ asset('portal/Login_v2/vendor/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ asset('portal/Login_v2/vendor/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('portal/Login_v2/vendor/countdowntime/countdowntime.js') }}"></script>
    <script src="{{ asset('portal/Login_v2/js/main.js') }}"></script>
    <script src="https://www.google.com/recaptcha/api.js"></script>
    <script type="text/javascript">
        $('#loginForm input[type="text"]').on('keypress', function(event) {
            if (event.key === 'Enter' || event.keyCode === 13) {
                event.preventDefault();
                $(".g-recaptcha").click();
            }
        });

        function onSubmit(token) {
            document.getElementById("loginForm").submit();
        }
        window.addEventListener("pageshow", function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
</body>

</html>
