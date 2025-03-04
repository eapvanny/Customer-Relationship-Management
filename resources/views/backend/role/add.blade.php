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
            <li><a href="{{ URL::route('role.index') }}"> {{ __('Role') }} </a></li>
            <li class="active">
                @if ($role)
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
            action="@if ($role) {{ URL::Route('role.update', $role->id) }} @else {{ URL::Route('role.store') }} @endif"
            method="post" enctype="multipart/form-data" autocomplete="off">
            <div class="row">
                <div class="col-md-12">
                    <div class="wrap-outter-header-title">
                        <h1>
                            {{ __('Role') }}
                            <small>
                                @if ($role)
                                    {{ __('Update') }}
                                @else
                                    {{ __('Add New') }}
                                @endif
                            </small>
                        </h1>

                        <div class="box-tools pull-right">
                            <a href="{{ URL::route('role.index') }}" class="btn btn-default">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-info pull-right text-white"><i
                                    class="fa @if ($role) fa-refresh @else fa-plus-circle @endif"></i>
                                @if ($role)
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
                <div class="box-body">
                    @csrf
                    @if ($role)
                        @method('PUT')
                    @endif
                    <div class="row">
                        {{-- <div class="col-md-4">
                            <div class="form-group has-feedback">
                                <label for="name"> {{ __('Name') }} <span class="text-danger">*</span></label>
                                <input autofocus type="text" class="form-control" name="name" placeholder="name"
                                    value="@if ($role) {{ $role->name }}@else{{ old('name') }} @endif"
                                    required minlength="2" maxlength="25">
                                <span class="fa fa-info form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                            </div>
                        </div> --}}
                        <div class="col-md-4">
                            <div class="form-group has-feedback">
                                <label for="role_id"> {{ __('Role') }}
                                    <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom"
                                        title="{{ __('Set a user role') }}"></i>
                                    <span class="text-danger">*</span>@if ($role)
                                    <span class="text-danger">{{ __('(Role can not be changed)') }}</span>
                                @endif
                                </label>
                                {!! Form::select('role_id', $all_role, old('role_id', $role ? $role->id : null), array_merge([
                                    'placeholder' => __('Select Role'),
                                    'class' => 'form-control select2',
                                    'required' => true,
                                ], $role ? ['disabled' => 'disabled'] : [])) !!}
                                <span class="form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('role_id') }}</span>
                            </div>
                        </div>
                        <hr>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="fw-bold">{{ __('Manage Permissions') }}</label>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="all-dashboard">
                                            <label class="form-check-label"
                                                for="all-dashboard">{{ __('Manage All dashboard') }}</label>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        @if ($permissions->count() > 0)
                                            @php $counter = 0; @endphp
                                            @foreach ($permissions as $permission)
                                                @if ($counter % 4 == 0 && $counter > 0)
                                    </div>
                                    <div class="row">
                                        @endif
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input {{ in_array($permission->name, $hasPermission) ? 'checked' : '' }}
                                                    class="form-check-input permission-checkbox" type="checkbox"
                                                    id="permission-{{ $permission->id }}" name="permissions[]"
                                                    value="{{ $permission->id }}">

                                                <label class="form-check-label" for="permission-{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        </div>
                                        @php $counter++; @endphp
                                        @endforeach
                                        @endif
                                    </div>
                                </div>
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

            $('#dashboard-access').change(function() {
                updateLabelText();
            });

            $('#all-dashboard').change(function() {
                let isChecked = $(this).is(':checked');
                $('.permission-checkbox').prop('checked', isChecked).trigger('change');
            });

            $('.permission-checkbox').change(function() {
                let allChecked = $('.permission-checkbox').length === $('.permission-checkbox:checked')
                    .length;
                $('#all-dashboard').prop('checked', allChecked);
            });

            $(document).ready(function() {
                let allChecked = $('.permission-checkbox').length === $('.permission-checkbox:checked')
                    .length;
                $('#all-dashboard').prop('checked', allChecked);
            });
        });
    </script>
@endsection
<!-- END PAGE JS-->
