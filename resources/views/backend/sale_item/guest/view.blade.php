<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Guest Detail @endsection
<!-- End block -->

<!-- Page body extra css -->
@section('extraStyle')
    <style>
        @media print {
            @page {
                size:  A4 landscape;
                margin: 5px;
            }
        }
        .width-100{
            width: 99%;
        }
    </style>
@endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')

    <!-- Section header -->
    <section class="content-header">
        <div class="btn-group">
            @can('guest.edit')
            <a href="{{URL::route('guest.edit',$guest->id)}}" class="btn-ta btn-sm-ta"><i class="fa fa-edit"></i> {{ __('Edit') }} </a>
            @endcan
        </div>

        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li> {{ __('Sale Item') }} </li>
            <li><a href="{{URL::route('guest.index')}}"> {{ __('Guest') }} </a></li>
            <li class="active">View</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content main-contents">
        <div class="row">
            <div class="col-md-12">
                <div id="printableArea">
                    <div class="row">
                        @php
                            $tab = 'profile';
                            if(request()->has('tab'))
                            {
                             $tab = request()->get('tab');
                            }
                        @endphp
                        <div class="col-sm-12">
                            <div class="nav-tabs-custom">
                                <ul class="nav nav-tabs" id="nav_tab">
                                    <li class="active"><a href="#profile" data-toggle="tab">Detail</a></li>
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane active" id="profile">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for=""> {{ __('First Name') }} </label>
                                            </div>
                                            <div class="col-md-3">
                                                    <p>: {{$guest->first_name}}</p>
                                            </div>
                                            <div class="col-md-3">
                                                <label for=""> {{ __('Email') }} </label>
                                            </div>
                                            <div class="col-md-3">
                                                <p>: {{$guest->email}}</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for=""> {{ __('Last Name') }} </label>
                                            </div>
                                            <div class="col-md-3">
                                                <p>: {{$guest->last_name}}</p>
                                            </div>
                                            <div class="col-md-3">
                                                <label for=""> {{ __('Phone No.') }} </label>
                                            </div>
                                            <div class="col-md-3">
                                                <p for="">: {{$guest->phone}}</p>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for=""> {{ __('Gender') }} </label>
                                            </div>
                                            <div class="col-md-3">
                                                <p for="">: {{$guest->gender==1 ? 'Male':'Female'}}</p>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for=""> {{ __('Date Of Birth') }} </label>
                                            </div>
                                            <div class="col-md-3">
                                                <p for="">: {{$guest->dob}}</p>
                                            </div>
                                            <div class="col-md-3">
                                                <label for=""> {{ __('Address') }} </label>
                                            </div>
                                            <div class="col-md-3">
                                                <p for="">: {{$guest->address}}</p>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <!-- /.content -->

@endsection
<!-- END PAGE CONTENT-->

<!-- BEGIN PAGE JS-->
@section('extraScript')

    <script type="text/javascript">
        $(document).ready(function () {
            $('.btnPrintInformation').click(function () {
                $('ul.nav-tabs li:not(.active)').addClass('no-print');
                $('ul.nav-tabs li.active').removeClass('no-print');
                window.print();
            });
            Generic.initCommonPageJS();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

        });
    </script>
@endsection
<!-- END PAGE JS-->
