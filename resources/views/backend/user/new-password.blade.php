<!DOCTYPE html>
<html lang="en">

<head>
    <title>Forget Password</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--===============================================================================================-->
    {{-- <link rel="icon" type="image/png" href="{{asset('portal/Login_v2/images/icons/favicon.ico')}}"/>	 --}}
    <link rel="icon" type="image/png" href="{{ asset('images/Hi-Tech_Water_Logo.png') }}" />
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('portal/Login_v2/vendor/bootstrap/css/bootstrap.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portal/Login_v2/fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portal/Login_v2/fonts/iconic/css/material-design-iconic-font.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('portal/Login_v2/vendor/animate/animate.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portal/Login_v2/vendor/css-hamburgers/hamburgers.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portal/Login_v2/vendor/animsition/css/animsition.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('portal/Login_v2/vendor/select2/select2.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portal/Login_v2/vendor/daterangepicker/daterangepicker.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('portal/Login_v2/css/util.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('portal/Login_v2/css/main.css') }}">
    <!--===============================================================================================-->

</head>

<body>
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form class="login100-form validate-form" novalidate id="loginForm"
                    action="{{ URL::Route('reset.password.post') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <span class="login100-form-title p-b-26">
                        <img class="logo-hitech" src="{{ asset('images/Hi-Tech_Water_Logo.png') }}" alt="">
                    </span>
                    <input type="text" hidden name="token" value="{{ $token }}">
                    <div class="wrap-input100 validate-input {{ $errors->first('email') ? 'alert-validate' : '' }} "
                        data-validate = "{{ $errors->first('email') }}">
                        <input class="input100" type="email" name="email" placeholder="Email">
                        {{-- <span class="focus-input100" data-placeholder="Username"></span> --}}
                        <span class="text-danger">{{ $errors->first('email') }}</span>
                    </div>

                    <div class="wrap-input100 validate-input {{ $errors->first('password') ? 'alert-validate' : '' }}"
                        data-validate="{{ $errors->first('password') }}">
                        <span class="btn-show-pass">
                            <i class="zmdi zmdi-eye"></i>
                        </span>
                        <input class="input100" type="password" name="password" placeholder="Enter New Password">
                        {{-- <span class="focus-input100" data-placeholder="Password"></span> --}}
                        {{-- <span class="text-danger">{{ $errors->first('password') }}</span> --}}
                    </div>

                    <div class="wrap-input100 validate-input {{ $errors->first('password_confirmation') ? 'alert-validate' : '' }}"
                        data-validate="{{ $errors->first('password_confirmation') }}">
                        <span class="btn-show-pass">
                            <i class="zmdi zmdi-eye"></i>
                        </span>
                        <input class="input100" type="password" name="password_confirmation"
                            placeholder="Confirm Password">
                        <span class="text-danger">{{ $errors->first('password_confirmation') }}</span>

                    </div>

                    <div class="container-login100-form-btn">
                        <div class="wrap-login100-form-btn">
                            <div class="login100-form-bgbtn"></div>
                            <button type="submit" class="login100-form-btn g-recaptcha"
                                data-sitekey="{{ config('services.recaptcha.site_key') }}" data-callback='onSubmit'
                                data-action='submit'>
                                Forget Password
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>


    <div id="dropDownSelect1"></div>

    <!--===============================================================================================-->
    <script src="{{ asset('portal/Login_v2/vendor/jquery/jquery-3.2.1.min.js') }}"></script>
    <!--===============================================================================================-->
    <script src="{{ asset('portal/Login_v2/vendor/animsition/js/animsition.min.js') }}"></script>
    <!--===============================================================================================-->
    <script src="{{ asset('portal/Login_v2/vendor/bootstrap/js/popper.js') }}"></script>
    <script src="{{ asset('portal/Login_v2/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <!--===============================================================================================-->
    <script src="{{ asset('portal/Login_v2/vendor/select2/select2.min.js') }}"></script>
    <!--===============================================================================================-->
    <script src="{{ asset('portal/Login_v2/vendor/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ asset('portal/Login_v2/vendor/daterangepicker/daterangepicker.js') }}"></script>
    <!--===============================================================================================-->
    <script src="{{ asset('portal/Login_v2/vendor/countdowntime/countdowntime.js') }}"></script>
    <!--===============================================================================================-->
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
                // Reload the page if it is loaded from cache (i.e., when the back button is used)
                window.location.reload();
            }
        });
    </script>
</body>

</html>
