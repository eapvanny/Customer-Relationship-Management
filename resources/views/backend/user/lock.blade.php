<!-- Master page  -->
@extends('backend.layouts.front_master')

<!-- Page title -->
@section('pageTitle')
    Lock Screen
@endsection
<!-- End block -->
@section('extraStyle')
    <style>
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            background-color: #ffebee;
            /* Light red background for errors, can change for other types */
            border: 1px solid #ffcdd2;
            /* Slightly darker border for contrast */
            color: #d32f2f;
            /* Dark red text for errors, readable and professional */
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            /* Subtle shadow for depth */
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 20px auto;
            max-width: 400px;
            /* Keeps it centered and not too wide */
            transition: opacity 0.3s ease-in-out;
            /* Smooth fade for hiding */
        }

        .alert-danger .icon {
            color: #d32f2f;
            /* Match icon color to text */
            font-size: 18px;
            margin-right: 8px;
        }

        .alert-success {
            background-color: #e8f5e9;
            /* Light green for success */
            border-color: #c8e6c9;
            color: #2e7d32;
            /* Dark green text */
        }

        .alert-success .icon {
            color: #2e7d32;
        }

        .alert-warning {
            background-color: #fff3e0;
            /* Light orange for warnings */
            border-color: #ffe0b2;
            color: #f57c00;
            /* Dark orange text */
        }

        .alert-warning .icon {
            color: #f57c00;
        }

        .alert h5 {
            margin: auto;
        }

        /* Animation for appearing */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert {
            animation: slideDown 0.3s ease-out;
        }

        /* Ensure the alert stays visible initially */
        .alert[style*="opacity: 0"] {
            display: none;
        }
    </style>
@endsection
<!-- Page body extra class -->
@section('bodyCssClass')
    hold-transition lockscreen
@endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <div class="lockscreen-wrapper">
        <div class="lockscreen-footer text-center">
            @if (Session::has('success') || Session::has('error') || Session::has('warning'))
                <div class="row">
                    <div
                        class="alert @if (Session::has('success')) alert-success @elseif(Session::has('error')) alert-danger @else alert-warning @endif alert-dismissible">
                        @if (Session::has('success'))
                            <h5><i class="icon fa fa-check"></i>{{ Session::get('success') }}</h5>
                        @elseif(Session::has('error'))
                            <h5><i class="icon fa fa-ban"></i>{{ Session::get('error') }}</h5>
                        @else
                            <h5><i class="icon fa fa-warning"></i>{{ Session::get('warning') }}</h5>
                        @endif
                        </h5>
                    </div>
                </div>
            @endif
        </div>
        <div class="lockscreen-logo">
            <a href="/">
                <img src="{{ asset('images/Hi-Tech_Water_Logo.png') }}" style="width: 150px;">
            </a>
        </div>
        <!-- User name -->
        <div class="lockscreen-name mb-3">{{ $username }}</div>

        <!-- START LOCK SCREEN ITEM -->
        <div class="lockscreen-item">
            <!-- lockscreen image -->
            <div class="lockscreen-image">
                <img src="{{ $photo ? asset('storage/' . $photo) : asset('images/avatar.png') }}" alt="User Image">
            </div>

            <!-- /.lockscreen-image -->

            <!-- lockscreen credentials (contains the form) -->
            <form class="lockscreen-credentials" action="{{ URL::route('unlock') }}" method="post"
                enctype="multipart/form-data">
                <div class="input-group">
                    <input autofocus type="password" name="password" class="form-control" placeholder="password" required>
                    <input type="hidden" class="form-control" name="username" value="{{ $username }}">
                    @csrf
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center">
                        <i class="fa fa-arrow-right text-muted"></i>
                    </button>
                </div>
            </form>
            <!-- /.lockscreen credentials -->

        </div>
        <!-- /.lockscreen-item -->
        <div class="help-block text-center">
            {{ __('Enter your password to retrieve your session') }}
        </div>
        <div class="text-center mt-3 text-decoration-none">
            <a class="text-decoration-none" href="{{ URL::route('login') }}"
                style="background-color: dodgerblue; padding: 8px 10px; color:white; border-radius: 4px;">
                {{ __('SignIn') }} </a>
        </div>
    </div>
@endsection
<!-- END PAGE CONTENT-->

<!-- BEGIN PAGE JS-->
@section('extraScript')
    <script type="text/javascript">
        $(document).ready(function() {
            setTimeout(function() {
                const alert = document.querySelector('.alert');
                if (alert) {
                    alert.style.transition = 'opacity 0.3s ease-in-out';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 250);
                }
            }, 2500);
        });
    </script>
@endsection
<!-- END PAGE JS-->
