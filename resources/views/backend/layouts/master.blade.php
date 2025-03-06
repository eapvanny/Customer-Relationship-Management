<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
@php

    /*@if(isset(auth()->user()->organization->short_name) && !empty(auth()->user()->organization->short_name)){{auth()->user()->organization->short_name}} @else HiTech @endif*/
    $title = (isset(auth()->user()->organization->short_name) && !empty(auth()->user()->organization->short_name))?auth()->user()->organization->short_name : 'HiTech';
    //dd($title);
@endphp
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="@if(isset(auth()->user()->organization->name) && !empty(auth()->user()->organization->name)){{auth()->user()->organization->name}}@else HiTech @endif">
    <meta name="keywords" content="school,college,management,result,exam,attendace,hostel,admission,events">
    <meta name="author" content="H.R.Shadhin">
    <title>{{$title}} | @yield('pageTitle')</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon png -->
    <link rel="icon" href="@if(isset(auth()->user()->organization->favicon) && !empty(auth()->user()->organization->favicon)){{asset('storage/logo/'.auth()->user()->organization->favicon)}} @else{{ asset('images/Hi-Tech_Water_Logo.png') }}@endif" type="image/png">
    <!-- Pace loading -->
    <script src="{{ asset(mix('/js/pace.js')) }}"></script>
    <link href="{{ asset(mix('/css/pace.css')) }}" rel="stylesheet" type="text/css">
    <script src="{{asset('js/swal.js')}}"></script>
    <script src="{{ asset('js/chart.js') }}"></script>
    <!-- Leaflet JS -->
    <script src="{{asset('js/leaflet.js')}}"></script>
    <!-- vendor libraries CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="{{ asset(mix('/css/vendor.css')) }}" rel="stylesheet" type="text/css">
    <!-- theme CSS -->
    <link href="{{ asset(mix('/css/theme.css')) }}" rel="stylesheet" type="text/css">
    <!-- view CSS -->
    <link href="{{ asset('/portal/css/view.css?v=1') }}" rel="stylesheet" type="text/css">
    <!-- dropzone CSS -->
    <link href="{{ asset('/portal/css/dropzone.css') }}" rel="stylesheet" type="text/css">
    <!-- app CSS -->
    <link href="{{ asset(mix('/css/app.css')) }}" rel="stylesheet" type="text/css">

    <!-- print CSS -->
    <link href="{{ asset(mix('/css/print.css')) }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('/css/owl.carousel.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('/css/owl.theme.default.css') }}" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script>
        var hash = '{{session('user_session_sha1')}}';

    </script>
    <!-- Child Page css goes here  -->
@yield("extraStyle")
@yield('dropzone-css')
<!-- Child Page css -->
</head>

<body class="hold-transition skin-blue sidebar-mini fixed @yield('bodyCssClass')">
{{--<div class="overlay-loader">--}}
{{--<div class="loader" ></div>--}}
{{--</div>--}}
<div class="ajax-loader">
    <img class="loader2" src="{{ asset('/images/loader.svg') }}" alt="">
</div>

<!-- Site wrapper -->
<div class="wrapper">
    <!-- page header -->
@include('backend.partial.header')
<!-- / page header -->

    <!-- page aside left side bar -->
@include('backend.partial.leftsidebar')
<!-- / page aside left side bar -->
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper ">

        <!-- Message -->
        @if (Session::has('success') || Session::has('error') || Session::has('warning'))
            <div class="m-4 alert @if (Session::has('success')) alert-success @elseif(Session::has('error')) alert-danger @else alert-warning @endif alert-dismissible fade show mb-2" role="alert">
                @if (Session::has('success'))
                    <i class="icon fa fa-check pt-2 pe-2"></i>{!! __(Session::get('success')) !!}
                @elseif(Session::has('error'))
                    <i class="icon fa fa-ban"></i>{!! __(Session::get('error')) !!}
                @else
                    <i class="icon fa fa-warning"></i>{!!  __(Session::get('warning')) !!}
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

        @endif
        <x-backend.import-alert/>
    <!-- ./Message -->
        <!-- BEGIN CHILD PAGE-->
    @yield('pageContent')
    <!-- END CHILD PAGE-->
    </div>
    <!-- /.content-wrapper -->

    <!-- footer -->
@include('backend.partial.footer')
<!-- / footer -->

    <!-- page aside right side bar -->
@include('backend.partial.rightsidebar')
<!-- / page aside right side bar -->

</div>
<!-- ./wrapper -->
<!-- webpack menifest js -->
<script src="{{ asset(mix('/js/manifest.js')) }}"></script>
<!-- vendor libaries js -->
<script src="{{ asset(mix('/js/vendor.js')) }}"></script>
<!-- theme js -->
<script src="{{ asset(mix('/js/theme.js')) }}"></script>
<!-- app js -->
<script src="{{ asset(mix('/js/app.js')) }}"></script>
<script src="{{asset(mix('/js/owl.carousel.min.js'))}}"></script>
<script src="{{url('https://www.gstatic.com/firebasejs/5.6.0/firebase.js')}}"></script>
<!-- dropzone js -->
@php
    $routeName = Route::currentRouteName();

    $filterFieldDataByOrg = Config::get('fields-filter-by-org-change');
    //dd($filterFieldDataByOrg[$routeName]);
    if(isset($filterFieldDataByOrg[$routeName])){
@endphp
<script type='text/javascript'>
    $(document).ready(function(){
        //filter on load
        var currentSelect = $("select[name='org_id']").val();
        if(currentSelect > 0){
            fieldFilterData($("select[name='org_id']"));
        }
        @php
            if(Illuminate\Support\Str::contains($routeName,'edit')){
                //in page edit, we don't support to change org
        @endphp
        $("select[name='org_id']").attr('readOnly', true);
        @php
            }else{
        @endphp
        //filter on change
        $("select[name='org_id']").change(function(){
            fieldFilterData(this);
        });
        @php
            }
        @endphp


    });
    function fieldFilterData(selectObj){
        var org_id = $(selectObj).val();
        org_id = org_id?org_id:0;
        $.ajax({
            type:'GET',
            url:'/fields-data-by-org-change/'+org_id,
            success:function(data){
                if(data.success){

                    $.each(data.data, function (key, fieldData) {
                        var $selectField = $("select[name='"+key+"']");
                        let keyname = key.replace("_id", "").replace("_item","");
                        let pick = 'Pick a '+keyname+'...';
                        if(fieldData.length){
                            var oldSelected = $selectField.val();
                            //clean up old data. TODO: copy the first empty option to use
                            var $select = $selectField.empty();
                            if($selectField.length > 0 && fieldData.length > 0){
                                //insert empty option. TODO: use the copy empty option instead
                                $('<option/>').appendTo($select);
                                //append list data
                                for (var i = 0; i < fieldData.length; i++) {
                                    var o = $('<option/>', { value: fieldData[i].id })
                                        .text(fieldData[i].name);
                                    //set selected
                                    if(oldSelected == fieldData[i].id){
                                        o.prop('selected', true);
                                    }
                                    o.appendTo($select);
                                }
                                //re-init select2
                                var $field = $select.select2("destroy");
                                $field.select2({placeholder: pick});
                            }else{
                                $select.select2({placeholder: pick});

                            }
                        }else {
                            $selectField.empty().select2({placeholder: pick});
                        }

                    });
                }else{
                    alert("Failed to get field's data");
                }
            }
        });
    }
</script>
@php
    }
@endphp

<script type="text/javascript">
    var DatatableSignal = function(){
        this.dispatcher = $({});
    };
    DatatableSignal.prototype = {
        row: null,
        success: null,
        response_data: null,
        message: null,
        dtSaved: function(row, success, response_data, message) {
            this.row = row;
            this.success = success;
            this.response_data = response_data;
            this.message = message;

            this.dispatcher.trigger("dtSaved");
        }
    };
    var datatableSignal = new DatatableSignal();



    $(document).ready(function(){



        @if (Session::has('success'))
            function myFunctionHidepopup() {
                $(".alert-success").slideUp(500, function() {

                });
            }
            var timeoutId = setTimeout(myFunctionHidepopup, 3000);
        @endif
        $('.select2').select2();

        var options = [];
        @if ( isset($organizations_filter_list) )

            @foreach ($organizations_filter_list as $org_filter)
                options[{{$org_filter->id}}] = @if ($org_filter->id==$org_filter->parent_org){{1}}@else{{2}}@endif;
            @endforeach

        @endif

        function formatOption(option) {
            var optionWithImage;
            if (options[option.id] != null) {
                if (options[option.id]==1) {
                    optionWithImage = $(
                        '<span><i class="fa fa-sitemap"></i> ' + option.text + '</span>'
                    );
                }else {
                    optionWithImage = $(
                        '<span><i class="fa fa-sitemap" style="color: transparent;"></i><i class="fa-regular fa-circle-dot" style="font-size: 12px;"></i> ' + option.text + '</span>'
                    );
                }
            }else {
                optionWithImage = $(
                    '<span>' + option.text + '</span>'
                );
            }


            return optionWithImage;
        }
        function formatOptionSelect(option) {
            return option.text;
        }
        $('.select_org_filter').select2({
            templateResult: formatOption,
            templateSelection: formatOptionSelect
        });

        $('.organization_filter').on('change', function() {
            if ($(this).val() > 0) {
                var baseURL = "{{ URL::to('/') }}";
                window.location.href = baseURL + "/user/set_org/" + $(this).val();
            }
        });

        $('.slide_cards_reviews').owlCarousel({
            autoplay: true,
            autoplayTimeout: 3000, // Set autoplay timeout in milliseconds (default: 5000)
            autoplayHoverPause: true, // Pause autoplay on hover (default: false)
            autoplaySpeed: 2500,
            loop: false,
            margin: 10,
            responsiveClass: true,
            nav: false,
            responsive: {
                0: {
                items: 1
                },
                945: {
                items: 2
                },
                1260: {
                items: 3
                }
                ,
                1600: {
                items: 4
                }
            }
        });
        var timeoutId = null;
        $(window).on("scroll", function() {
            var scrollPosition = $(window).scrollTop(); // Get current scroll position
            if (timeoutId) {
                clearTimeout(timeoutId);
            }
            timeoutId = setTimeout(function() {
                $(".wrap-outter-header-title").each(function() {
                var elementOffset = $(this).offset().top-10; // Get element's top position

                    if (scrollPosition  > elementOffset) {
                        if (!$(this).find(".action-btn-top").hasClass("fly_action_btn")) {
                            $(this).find(".action-btn-top").addClass("fly_action_btn");
                            $(this).find(".action-btn-top").addClass("shadow-sm");
                            $(this).find(".action-btn-top").removeClass("none_fly_action_btn");
                            $(this).find(".action-btn-top").css("top","0");
                            $(this).find(".action-btn-top").animate({ top: $(".main-header").height() }, 300);
                        }
                    }else {
                        if (scrollPosition >  $(this).offset().top - $(this).height() - 10) {
                            $(this).find(".action-btn-top").animate({ top: 0 }, 300);
                        } else {
                            $(this).find(".action-btn-top").css("top","0");
                        }

                        $(this).find(".action-btn-top").removeClass("fly_action_btn");
                        $(this).find(".action-btn-top").removeClass("shadow-sm");
                        $(this).find(".action-btn-top").addClass("none_fly_action_btn");
                        $(this).find(".action-btn-top").removeAttr("style");

                    }
                });
            }, 50);
        });

    });
    $(document).on('click', '.dt-inline-edit', function(e){
        // console.log($(this).html());

        if ($(this).closest('td').find('.inline-input-div').length > 0){
            $(this).closest('td').find('.inline-input-div .value').focus();
            return;
        }
        $(this).addClass('d-none');
        var row = $(this).closest('tr').index();
        var orig_div = $(this);
        var data_value = $(this).attr("data-value");
        var data_key = $(this).attr("data-key");
        var data_post_url = $(this).attr("data-post-url");
        var style_max_width = "";
        var data_style_width = $(this).attr("data-style-width");
        if (typeof data_style_width !== 'undefined') {
            style_max_width = "width: 200px;";
        }
        var input_div = $('<div class="input-group input-group-sm mb-3 inline-input-div" style="min-width: 120px;' + style_max_width + '">\
            <input type="text" class="form-control rounded-0 value" value="' + data_value + '">\
            <button class="input-group-text btn btn-sm text-success dt-save" ><i class="fa-solid fa-check"></i></button>\
            <button class="input-group-text btn btn-sm text-danger dt-discard" ><i class="fa-solid fa-xmark"></i></button>\
        </div>');

        $(input_div).insertAfter($(this));
        $(input_div).find("input").select();
        $(input_div).find(".dt-discard").click(function(e){
            $(input_div).remove();
            $(orig_div).removeClass('d-none');
        });

        $(input_div).find('.dt-save').click(function(e){
            data_value = $(input_div).find('.value').val();
            dtInlineSave(data_post_url, data_key, data_value, function(success, response_data, message) {
                if (success) {
                    toastr.options.progressBar = false;
                    toastr.success(message);
                    $(input_div).closest('.inline-input-div').remove();
                    $(orig_div).removeClass('d-none');
                    $(orig_div).find('.value_html').html(response_data.html);
                    $(orig_div).attr("data-value", response_data.value);
                }else{
                    toastr.error(message);
                }
                datatableSignal.dtSaved(row, success, response_data, message);
            });
        });
        // $(input_div).find('.value').keypress(function(event){

        //     var keycode = (event.keyCode ? event.keyCode : event.which);
        //     console.log(keycode);
        //     if(keycode == '13'){
        //         $(input_div).find('.dt-save').click();
        //         var td_index = $(this).closest('td').index();
        //         var next_tr = $(this).closest('tr').next('tr');
        //         if (next_tr != null){
        //             $(next_tr).children('td').eq(td_index).find('.dt-inline-edit').click();
        //         }
        //     }
        //     if (keycode == 9) {
        //         console.log('test');
        //         $(input_div).find('.dt-save').click();
        //         $(this).closest('tr').find('td').each(function (index) {
        //             console.log(index);
        //         });

        //         // var dt_inline_edit_index = $(this).closest('td').find('.dt-inline-edit').index();

        //         // var this_tr = $(this).closest('tr').find('.dt-inline-edit').eq(dt_inline_edit_index + 1);
        //         // if (this_tr != null){
        //         //     $(this_tr).click();
        //         // }
        //     }
        //     if (event.key == "Escape") {
        //         $(input_div).find('.dt-discard').click();
        //     }
        // });
    });

    $(document).on('keypress', '.inline-input-div .value', function(event){

        var input_div = $(this).closest('.inline-input-div');
        var keycode = (event.keyCode ? event.keyCode : event.which);

        if(keycode == '13'){ //Enter
            $(input_div).find('.dt-save').click();
            var td_index = $(this).closest('td').index();
            var next_tr = $(this).closest('tr').next('tr');
            if (next_tr != null){
                $(next_tr).children('td').eq(td_index).find('.dt-inline-edit').focus();
            }
        }
        if (keycode == 9) { //Tab
            event.preventDefault();
        }
    });
    $(document).on('keydown', '.inline-input-div .value', function(event){

        var input_div = $(this).closest('.inline-input-div');
        var keycode = (event.keyCode ? event.keyCode : event.which);

        if (keycode == 9) {

            $(input_div).find('.dt-save').click();
            $(this).closest('td').nextAll().each(function (index) {
                $(this).find('.dt-inline-edit').focus();
                event.preventDefault();
                return false;
            });
        }

        if (keycode == 27) { //Escape
            $(input_div).find('.dt-discard').click();
            event.preventDefault();
            return false;
        }
    });

    $(document).on('keydown', '.dt-inline-edit', function() {

        var keycode = (event.keyCode ? event.keyCode : event.which);

        if (keycode != 9 && keycode != 13 && keycode != 27 && keycode != 39 && keycode != 37 && keycode != 40 && keycode != 38) {
            $(this).click();
        }
        if (keycode == 39) {
            moveToRightColumn($(this).closest('td'));
            event.preventDefault();
        } else if (keycode == 37) {
            moveToLeftColumn($(this).closest('td'));
            event.preventDefault();
        } else if (keycode == 40) {
            moveToLowerColumn($(this).closest('td'));
            event.preventDefault();
        } else if (keycode == 38) {
            moveToUpperColumn($(this).closest('td'));
            event.preventDefault();
        }

    });


    function moveToLeftColumn(current_td)
    {
        $(current_td).prevAll().each(function (index) {
            $(this).find('.dt-inline-edit').focus();
            console.log('left', $(this).index());
            return false;
        });
    }
    function moveToRightColumn(current_td)
    {
        $(current_td).nextAll().each(function (index) {
            $(this).find('.dt-inline-edit').focus();
            console.log('right', $(this).index());
            return false;
        });
    }
    function moveToUpperColumn(current_td)
    {
        var td_index = $(current_td).index();
        $(current_td).closest('tr').prevAll().each(function (index) {
            $(this).find('td').eq(td_index).find('.dt-inline-edit').focus();

            return false;
        });
    }
    function moveToLowerColumn(current_td)
    {
        var td_index = $(current_td).index();
        $(current_td).closest('tr').nextAll().each(function (index) {
            $(this).find('td').eq(td_index).find('.dt-inline-edit').focus();
            event.preventDefault();
            return false;
        });
    }
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var firebaseConfig = {
        apiKey: "AIzaSyARqRuy-HAZAm9gz2sJBqDUD8kT6lfwbdc",
        authDomain: "sms-cisc.firebaseapp.com",
        databaseURL: "https://sms-cisc.firebaseio.com",
        storageBucket: "sms-cisc.appspot.com",
        messagingSenderId: "2803547147",
    };

    firebase.initializeApp(firebaseConfig);

    const messaging = firebase.messaging();
    messaging
        .requestPermission()
        .then(function () {
            // Notification permission granted.
            // get the token in the form of promise
            console.log('Notification permission granted');
            return messaging.getToken()
        })
        .then(function(token) {
            $.ajax({
                type:'POST',
                url:"<?php echo url('insertTokenNotification'); ?>",
                data:{
                    token_id     :token
                },
                success:function(data){

                }

            });

        })
        .catch(function (err) {
            // ErrElem.innerHTML =  ErrElem.innerHTML + "; " + err
            console.log("Unable to get permission to notify.", err);
        });

    messaging.onMessage(function(payload) {
        const notificationTitle = payload.notification.title;
        const notificationOptions = {
            body: payload.notification.body,
        };
        if (!("Notification" in window)) {
            console.log("This browser does not support system notifications");
        }
        // Let's check whether notification permissions have already been granted
        else if (Notification.permission === "granted") {
            // If it's okay let's create a notification
            var notification = new Notification(notificationTitle,notificationOptions);
            notification.onclick = function(event) {
                event.preventDefault(); // prevent the browser from focusing the Notification's tab
                window.open(payload.notification.click_action,'_blank');
                notification.close();
            }
        }
    });


    function dtInlineSave(post_url, key, value, callback)
    {
        $.ajax({
            type: "POST",
            url: post_url,
            data: {'_method': 'PUT', 'key' : key, 'value' : value},
            dataType: 'json',
            async: true,
            success: function (response) {
                if( response.status === true ) {

                    if (callback !== undefined){
                        callback(true, response.data, response.message);
                    }
                } else {

                    if (callback !== undefined){
                        callback(false, response.data, response.message);
                    }
                }

            }
        });
    }
</script>
<!-- Extra js from child page -->
@yield("extraScript")
@stack('scripts')
@yield('dropzone-script')
<!-- END JAVASCRIPT -->

</body>

</html>
