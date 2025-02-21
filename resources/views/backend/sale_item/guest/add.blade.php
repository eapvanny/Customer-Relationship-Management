<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Guest @endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            {{ __('Guest') }}
            <small>@if($guest) Update @else {{ __('Add New') }}  @endif</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li> {{ __('Sale Item') }} </li>
            <li><a href="{{URL::route('guest.index')}}"> {{ __('Guest') }} </a></li>
            <li class="active">@if($guest) Update @else Add @endif</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <form novalidate id="entryForm" action="@if($guest) {{URL::Route('guest.update',$guest->id)}}@else{{URL::Route('guest.store')}}@endif" method="post" enctype="multipart/form-data" autocomplete="off">
                        <div class="box-body">
                            @csrf
                            @if($guest)  {{ method_field('PATCH') }} @endif
                            <!-- Organization -->
                            @auth
                                {!! AppHelper::selectOrg($errors, $guest->org_id ?? 0,$role_ref_id) !!}
                            @endauth
                            <!-- End organization -->
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group has-feedback">
                                        <label for="name"> {{ __('First Name') }} <span class="text-danger">*</span></label>
                                        <input autofocus type="text" class="form-control" name="first_name" placeholder="first name" value="@if($guest){{ $guest->first_name }}@else{{old('first_name')}}@endif" required minlength="2" maxlength="255">
                                        <span class="fa fa-info form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('first_name') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group has-feedback">
                                        <label for="name"> {{ __('Last Name') }} <span class="text-danger">*</span></label>
                                        <input autofocus type="text" class="form-control" name="last_name" placeholder="last name" value="@if($guest){{ $guest->last_name }}@else{{old('last_name')}}@endif" required minlength="2" maxlength="255">
                                        <span class="fa fa-info form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('last_name') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group has-feedback">
                                        <label for="gender"> {{ __('Gender') }} <span class="text-danger">*</span>
                                            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="select gender type"></i>
                                        </label>
                                        {!! Form::select('gender', AppHelper::GENDER, $gender , ['class' => 'form-control select2', 'required' => 'true']) !!}
                                        <span class="form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('gender') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group has-feedback">
                                        <label for="email"> {{ __('Email') }} </label>
                                        <input  type="email" class="form-control" name="email"  placeholder="email address" value="@if($guest){{$guest->email}}@else{{old('email')}}@endif" maxlength="100">
                                        <span class="fa fa-envelope form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('email') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group has-feedback">
                                        <label for="phone_no"> {{ __('Phone/Mobile No.') }} </label>
                                        <input  type="text" class="form-control" name="phone"  placeholder="phone or mobile number" value="@if($guest){{$guest->phone}}@else{{old('phone')}}@endif" maxlength="15">
                                        <span class="fa fa-phone form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('phone') }}</span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group has-feedback">
                                        <label for="dob"> {{ __('Date of birth') }} </label>
                                        <input type='text' class="form-control date_picker2"  readonly name="dob" placeholder="date" value="@if($guest){{ old('dob')??$guest->dob }}@else{{old('dob')}}@endif"  minlength="10" maxlength="255" />
                                        <span class="fa fa-calendar form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('dob') }}</span>
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="description"> {{ __('Address') }} </label>
                                        <textarea name="address" class="form-control" maxlength="500" >@if($guest){{ $guest->address }}@else{{ old('address') }} @endif</textarea>
                                        <span class="fa fa-location-arrow form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('address') }}</span>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- /.box-body -->
                        <div class="box-footer">
                            <a href="{{URL::route('guest.index')}}" class="btn btn-default"> {{ __('Cancel') }} </a>
                            <button type="submit" class="btn btn-info pull-right text-white"><i class="fa @if($guest) fa-refresh @else fa-plus-circle @endif"></i> @if($guest) Update @else {{ __('Add') }} @endif</button>

                        </div>
                    </form>
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
            Generic.initCommonPageJS();
        });
    </script>
@endsection
<!-- END PAGE JS-->
