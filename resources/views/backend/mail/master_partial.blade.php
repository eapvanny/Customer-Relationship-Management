<!DOCTYPE HTML>
<html lang="{{ app()->getLocale() }}">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="keywords" content="school,college,management,result,exam,attendace,hostel,admission,events">
    <meta name="author" content="H.R.Shadhin">
    <title>@yield('pageTitle')</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!--styles -->

    <!-- Child Page css goes here  -->
    @yield("extraStyle")
     <!-- Child Page css -->


</head>

<body style="font-family: 'Source Sans Pro', sans-serif, 'Siemreap';">

    <!-- page header -->
    @include('backend.mail.partail.header')
    <!-- / page header -->

	<!-- BEGIN CHILD PAGE-->
	@yield('pageContent')
	<!-- END CHILD PAGE-->


	<!-- footer -->
    @include('backend.mail.partail.footer')
	<!-- / footer -->



    <!-- Extra js from child page -->
    @yield("extraScript")
    <!-- END JAVASCRIPT -->
    @stack('scripts')
</body>
</html>
