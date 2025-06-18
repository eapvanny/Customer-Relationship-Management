<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle')
    Tickets
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
            <li><a href="{{ URL::route('permission.index') }}"> {{ __('Permission') }} </a></li>
            <li class="active">
                @if ($permission)
                    {{__('Update')}}
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
            action="@if ($permission) {{ URL::Route('permission.update', $permission->id) }} @else {{ URL::Route('permission.store') }} @endif"
            method="post" enctype="multipart/form-data" autocomplete="off">
            <div class="row">
                <div class="col-md-12">
                    <div class="wrap-outter-header-title">
                        <h4>
                            @if ($permission)
                                {{__('Update Permission')}}
                            @else
                                {{ __('Add New Permission') }}
                            @endif
                        </h4>   
                        <div class="box-tools pull-right">
                            <a href="{{ URL::route('permission.index') }}" class="btn btn-default">{{__('Cancel')}}</a>
                            <button type="submit" class="btn btn-info pull-right text-white"><i
                                    class="fa @if ($permission) fa-refresh @else fa-plus-circle @endif"></i>
                                @if ($permission)
                                    {{__('Update')}}
                                @else
                                    {{__('Add')}}
                                @endif
                            </button>
                            @if(!$permission)
                                <button type="submit" class="submitClick submitAndContinue btn btn-success text-white">
                                <i class="fa fa-plus-circle"></i> {{ __('Save & Add New') }}
                                </button>
                                <div class="boxfooter"></div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="wrap-outter-box">
                <input id="org_detail" type="hidden" name="org_detail" value="">
                <div class="box-body">
                    @csrf
                    @if ($permission)
                        @method('PUT')
                    @endif
                    <div class="row">
                        <div class="col-md-6 co-xl-6">
                            <div class="form-group has-feedback">
                                <label for="name"> {{ __('Name') }} <span class="text-danger">*</span></label>
                                <textarea name="name" class="form-control" placeholder="" rows="1" maxlength="500" required>@if($permission){{old('name')??$permission->name}}@else{{old('name')}}@endif</textarea>
                                <span class="fa fa-info form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6 co-xl-6">
                            <div class="form-group has-feedback">
                                <label for="type"> {{ __('Permission Type') }}
                                    <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom"
                                        title="" data-original-title="Set a permission type"></i>
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::select('type', $type, old('type', optional($permission)->type), [
                                    'placeholder' => __('Select Permission Type'),
                                    'id' => 'type',
                                    'class' => 'form-control select2',
                                    'required' => true,
                                ]) !!}
                                <span class="form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('type') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
        </form>
    </section>
@endsection
<!-- END PAGE CONTENT-->

<!-- BEGIN PAGE JS-->
@section('extraScript')
    <script type="text/javascript">
        $(document).ready(function() {
            Generic.initDeleteDialog();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function updateLabelText() {
                if ($('#dashboard-access').prop('checked')) {
                    $('#dashboard-access-label').text('User can access the dashboard.');
                } else {
                    $('#dashboard-access-label').text(
                    'Users will not have access to the dashboard unless enabled.');
                }
            }

            // Run the function once on page load
            updateLabelText();

            // Add event listener to the checkbox for change event
            $('#dashboard-access').change(function() {
                updateLabelText();
            });

            $('#all-dashboard').change(function() {

                // Check or uncheck all checkboxes based on "all-dashboard"
                let isChecked = $(this).is(':checked');
                $('.permission-checkbox').prop('checked', isChecked).trigger('change');
            });

            $('.permission-checkbox').change(function() {
                // If all checkboxes are checked, check "all-dashboard", otherwise uncheck it
                let allChecked = $('.permission-checkbox').length === $('.permission-checkbox:checked')
                    .length;
                $('#all-dashboard').prop('checked', allChecked);
            });

            $(".submitClick").on('click', function(){
                event.preventDefault();
                if ($(this).hasClass('submitAndContinue')) {
                    $(".boxfooter").append('<input type="hidden" name="saveandcontinue" value="1" />');
                }else {
                    $("input[name='saveandcontinue']").each(function(){
                        $(this).remove();
                    });
                }
                $("#entryForm").submit();
            });
        });
    </script>
@endsection
<!-- END PAGE JS-->
