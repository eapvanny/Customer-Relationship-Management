<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle')
    User
@endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass')
@endsection
<!-- End block -->
@section('extraStyle')
    <style>
        fieldset .form-group {
            margin-bottom: 0px;
        }

        fieldset .iradio .error,
        fieldset .icheck .error {
            display: none !important;
        }

        @media (max-width: 600px) {
            .display-flex {
                display: inline-flex;
            }
        }

        @media (max-width: 768px) {
            .display-flex {
                display: inline-flex;
            }
        }

        .checkbox,
        .radio {
            display: inline-block;
        }

        .checkbox {
            margin-left: 10px;
        }

        legend {
            margin: 0;
            width: unset;
            font-weight: 700;
            font-size: 14px;
            color: #0059a1;
            display: block;
            padding-inline-start: 2px;
            padding-inline-end: 2px;
            border-width: initial;
            border-style: none;
            border-color: initial;
            border-image: initial;
        }

        fieldset {
            padding: 1em 0.625em 1em;
            border: 1px solid #9a9a9a;
            margin: 2px 2px;
            padding: .35em .625em .75em;
            margin-top: 4px;
        }
    </style>
@endsection
@php
    use App\Http\Helpers\AppHelper;
@endphp
<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            {{-- <li> {{ __('Administrator') }} </li> --}}
            <li><a href="{{ URL::route('user.index') }}"> {{ __('User') }} </a></li>
            <li class="active">
                @if ($user)
                    {{ __('Update') }}
                @else
                    {{ __('Add') }}
                @endif
            </li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">

        <form novalidate id="entryForm"
            action="@if ($user) {{ URL::Route('user.update', $user->id) }} @else {{ URL::Route('user.store') }} @endif"
            method="post" enctype="multipart/form-data" autocomplete="off">
            <div class="row">
                <div class="col-md-12">
                    <div class="wrap-outter-header-title">
                        <h1>
                            {{ __('User') }}
                            <small>
                                @if ($user)
                                    {{ __('Update') }}
                                @else
                                    {{ __('Add New') }}
                                @endif
                            </small>
                        </h1>

                        <div class="box-tools pull-right">
                            <a href="{{ URL::route('user.index') }}" class="btn btn-default">{{__('Cancel')}}</a>
                            <button type="submit" class="btn btn-info pull-right text-white"><i
                                    class="fa @if ($user) fa-refresh @else fa-plus-circle @endif"></i>
                                @if ($user)
                                    {{ __('Update') }}
                                @else
                                    {{ __('Add') }}
                                @endif
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="wrap-outter-box">
                <input id="org_detail" type="hidden" name="org_detail" value="">
                <div class="box-header">
                    <div class="callout callout-danger">
                        <p><b> {{ __('Note') }}:</b> {{ __('Create a role before create user if not exist') }} .</p>
                    </div>
                </div>
                <div class="box-body">
                    @csrf
                    @if ($user)
                        @method('PUT')
                    @endif

                    <!-- End organization -->
                    <div class="row">
                        {{-- <div class="col-md-4 col-xl-4">
                            <div class="form-group has-feedback">
                                <label for="department_id"> {{ __('Department') }} <span class="text-danger">*</span>
                                    <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom"
                                        title="Select Department"></i>
                                </label>
                                {!! Form::select('department_id', $departments, old('department_id', optional($user)->department_id), [
                                    'placeholder' => __('Select a department'),
                                    'id' => 'department_id',
                                    'name' => 'department_id',
                                    'class' => 'form-control select2',
                                    'required' => true,
                                ]) !!}
                                <span class="form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('department_id') }}</span>
                            </div>
                        </div> --}}
                        <div class="col-md-4">
                            <div class="form-group has-feedback">
                                <label for="name"> {{ __('Name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" placeholder="name"
                                    value="@if ($user) {{ $user->name }}@else{{ old('name') }} @endif"
                                    required minlength="2" maxlength="255">
                                <span class="fa fa-info form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group has-feedback">
                                <label for="phone_no"> {{ __('Phone No.') }}</label>
                                <input type="text" class="form-control" name="phone_no"
                                    placeholder="phone or mobile number"
                                    value="@if ($user) {{ $user->phone_no }}@else{{ old('phone_no') }} @endif"
                                    maxlength="15">
                                <span class="fa fa-phone form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('phone_no') }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group has-feedback">
                                <label for="id"> {{ __('User Role') }}
                                    <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom"
                                        title="" data-original-title="Set a user role"></i>
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::select('role_id', $roles, old('role_id', optional($user)->role_id), [
                                    'placeholder' => __('Select Role'),
                                    'class' => 'form-control select2',
                                    'required' => true,
                                ]) !!}


                                <span class="form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('role_id') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <div class="form-group has-feedback">
                                <label for="status"> {{ __('Status') }} <span class="text-danger">*</span></label>
                                <select name="status" class="form-select bg-light select2" id="status">
                                    <option value="1"
                                        {{ old('status', optional($user)->status) == 1 || is_null($user) ? 'selected' : '' }}>
                                        {{ __('Active') }} </option>
                                    <option value="0"
                                        {{ old('status', optional($user)->status) == 0 && !is_null($user) ? 'selected' : '' }}>
                                        {{ __('Inactive') }} </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 col-xl-4">
                            <div class="form-group has-feedback">
                                <label for="gender"> {{ __('Gender') }} <span class="text-danger">*</span></label>
                                @php
                                    $genderKey = $user ? $user->gender : null;
                                @endphp
                                {!! Form::select(
                                    'gender',
                                    [
                                        AppHelper::GENDER_MALE => __(AppHelper::GENDER[AppHelper::GENDER_MALE]),
                                        AppHelper::GENDER_FEMALE => __(AppHelper::GENDER[AppHelper::GENDER_FEMALE]),
                                    ],
                                    old('gender', $genderKey),
                                    [
                                        'class' => 'form-control select2',
                                        'required' => 'true',
                                        'placeholder' => __('Select Gender'),
                                        'id' => 'gender',
                                        'name' => 'gender',
                                    ],
                                ) !!}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group has-feedback">
                                <label for="email"> {{ __('Email') }} <span class=""></span></label>
                                <input type="email" class="form-control" name="email" placeholder="email address"
                                    value="@if ($user) {{ $user->email }}@else{{ old('email') }} @endif"
                                    maxlength="100">
                                <span class="fa fa-envelope form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('email') }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group has-feedback">
                                <label for="username"> {{ __('Username') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control"
                                    value="@if ($user) {{ $user->username }}@else{{ old('username') }} @endif"
                                    name="username" required minlength="5" maxlength="255" autocomplete="new-password">
                                <span class="glyphicon glyphicon-info-sign form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('username') }}</span>
                            </div>
                        </div>
                        @if (!$user)
                            <div class="col-md-4">
                                <div class="form-group has-feedback">
                                    <label for="password"> {{ __('Password') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="password" placeholder="Password"
                                        required minlength="6" maxlength="50" autocomplete="new-password">
                                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                </div>
                            </div>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group has-feedback">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="photo"> {{ __('Photo') }} <br /><span
                                                class="text-muted fst-italic">{{ __('(Files: jpeg, jpg, or png, min dimension: 50
                                                                                                                                                x 50 pixel, 2Mb max size)') }}</span></label>
                                        <input type="file" class="form-control" accept=".jpeg, .jpg, .png"
                                            name="photo" placeholder="{{ __('Photo image') }}">
                                        <span class="glyphicon glyphicon-open-file form-control-feedback"
                                            style="top:35px;"></span>
                                        <span class="text-danger">{{ $errors->first('photo') }}</span>
                                    </div>
                                    <div class="col-md-2">
                                        @if ($user && isset($user->photo))
                                            <img src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('images/avatar.jpg') }}"
                                                alt="Current Photo" style="max-height: 50px; margin-top: 40px">
                                            <input type="hidden" name="oldPhoto" value="{{ $user->photo }}">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
    <!-- /.content -->
@endsection
<!-- END PAGE CONTENT-->

<!-- BEGIN PAGE JS-->
@section('extraScript')
    <script type="text/javascript">
        $(document).ready(function() {
            Generic.initCommonPageJS();
        });
    </script>
@endsection
<!-- END PAGE JS-->
