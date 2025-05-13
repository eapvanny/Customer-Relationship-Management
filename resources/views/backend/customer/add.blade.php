@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle')
    {{ __('Customer') }}
@endsection
<!-- End block -->
@section('extraStyle')
    <style>
        @media (max-width: 414px) {
            .btn-default .btn-info .btn-success {
                font-size: 10px !important;
            }
        }
    </style>
@endsection
<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li><a href="{{ URL::route('customer.index') }}"> {{ __('Customer') }} </a></li>
            <li class="active">
                @if ($customer)
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
        <form id="entryForm"
            action="@if ($customer) {{ URL::Route('customer.update', $customer->id) }} @else {{ URL::Route('customer.store') }} @endif"
            method="post" autocomplete="off">
            <div class="row">
                <div class="col-md-12">
                    <div class="wrap-outter-header-title">
                        <h1>{{ __('Customer') }}</h1>
                        <div class="action-btn-top none_fly_action_btn">
                            <a href="{{ URL::route('customer.index') }}" class="btn btn-default"> {{ __('Cancel') }} </a>
                            <button type="submit" class="submitClick btn btn-info pull-right text-white">
                                <i class="fa @if ($customer) fa-refresh @else fa-check-circle @endif"></i>
                                @if ($customer)
                                    {{ __('Update') }}
                                @else
                                    {{ __('Save') }}
                                @endif
                            </button>
                            @if (!$customer)
                                <button type="submit" class="submitClick submitAndContinue btn btn-success text-white">
                                    <i class="fa fa-plus-circle"></i> {{ __('Save & Add New') }}
                                </button>
                                <div class="boxfooter"></div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @csrf
            @if ($customer)
                @method('PUT')
            @endif
            <div class="wrap-outter-box">
                <div class="box box-info">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6 col-xl-6">
                                <div class="form-group has-feedback">
                                    <label for="area">{{ __('Area') }} <span class="text-danger">*</span></label>
                                    <select name="area" id="area" class="form-control select2" required>
                                        <option value="">{{ __('Select Area') }}</option>
                                        @foreach (\App\Http\Helpers\AppHelper::getAreas() as $area => $subItems)
                                            <optgroup label="{{ $area }}">
                                                @foreach ($subItems as $area_id => $subItem)
                                                    <option value="{{ $area_id }}"
                                                        @if (old('area', $customer->area_id ?? '') == $area_id) selected @endif>
                                                        {{ "$subItem" }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                    <span class="fa fa-info form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('area') }}</span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-xl-6">
                                <div class="form-group has-feedback">
                                    <label for="outlet"> {{ __('Outlet') }} <span class="text-danger">*</span></label>
                                    <textarea id="outlet" name="outlet" class="form-control" placeholder="" rows="1" maxlength="500" required>
@if ($customer)
{{ old('outlet') ?? $customer->outlet }}@else{{ old('outlet') }}
@endif
</textarea>
                                    <span class="fa fa-info form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('outlet') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-6">
                                <div class="form-group has-feedback">
                                    <label for="name">{{ __('Customer Name') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $customer->name ?? '') }}" required>
                                    <span class="fa fa-user form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-xl-6">
                                <div class="form-group has-feedback">
                                    <label for="phone"> {{ __('Phone Number') }}</label>
                                    <input type="tel" class="form-control" name="phone"
                                        placeholder="{{ __('Phone number') }}"
                                        value="@if ($customer) {{ $customer->phone }}@else{{ old('phone') }} @endif">
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
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Handle form submission for Save and Save & Add New
            $(".submitClick").on('click', function(event) {
                event.preventDefault();
                if ($(this).hasClass('submitAndContinue')) {
                    $(".boxfooter").append('<input type="hidden" name="saveandcontinue" value="1" />');
                } else {
                    $("input[name='saveandcontinue']").each(function() {
                        $(this).remove();
                    });
                }
                $("#entryForm").submit();
            });

            // Initialize Select2
            $('.select2').select2();
        });
    </script>
@endsection
<!-- END PAGE JS-->
