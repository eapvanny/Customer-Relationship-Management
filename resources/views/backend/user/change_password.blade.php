<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle')
    Change Password
@endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass')
@endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Main content -->
    <section class="content-header">

        <ol class="breadcrumb">
            <li><a href="{{ URL::route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li class="active"> {{ __('Change Password') }} </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="wrap-outter-header-title">
                    <h1>
                        {{ __('Change Password') }}
                    </h1>
                </div>
                <div class="wrap-outter-box">
                    <div class="box-body">
                        <form novalidate id="changePasswordForm" action="{{ route('update_password') }}" method="post">
                            @csrf
                            <div class="form-group has-feedback">
                                <label for="oldpassword">{{ __('Old Password') }}</label>
                                <input type="password" class="form-control" name="old_password" placeholder="Old Password"
                                    required minlength="6" maxlength="50">
                                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('old_password') }}</span>
                            </div>

                            <div class="form-group has-feedback">
                                <label for="newpassword">{{ __('New Password') }}</label>
                                <input type="password" class="form-control" name="password" placeholder="New Password"
                                    required minlength="6" maxlength="50">
                                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('password') }}</span>
                            </div>

                            <div class="form-group has-feedback">
                                <label for="confirmpassword">{{ __('Confirm Password') }}</label>
                                <input type="password" class="form-control" name="password_confirmation"
                                    placeholder="Confirm Password" required minlength="6" maxlength="50">
                                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('password_confirmation') }}</span>
                            </div>

                            <br>
                            <a href="{{ route('dashboard.index') }}" class="btn btn-default btnCancel">{{__('Cancel')}}</a>
                            <button type="submit" class="btn btn-info pull-right text-white">
                                <i class="fa fa-refresh"></i> {{ __('Update') }}
                            </button>
                        </form>

                    </div>

                </div>

            </div>

            <div class="col-md-3"></div>

            <!-- /.box -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
@endsection
<!-- END PAGE CONTENT-->

<!-- BEGIN PAGE JS-->
@section('extraScript')
    <script type="text/javascript"></script>
@endsection
<!-- END PAGE JS-->
