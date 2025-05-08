@extends('backend.layouts.master')

@section('pageTitle')
    Roles
@endsection

@section('bodyCssClass')
@endsection

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

@section('pageContent')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }}</a></li>
            <li><a href="{{ URL::route('role.index') }}">{{ __('Role') }}</a></li>
            <li class="active">
                @if ($role)
                    {{ __('Update') }}
                @else
                    {{ __('Add') }}
                @endif
            </li>
        </ol>
    </section>

    <section class="content">
        <form novalidate id="entryForm"
            action="@if ($role) {{ URL::route('role.update', $role->id) }} @else {{ URL::route('role.store') }} @endif"
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
                        <div class="col-md-4">
                            <div class="form-group has-feedback">
                                <label for="role_id">{{ __('Role') }}
                                    <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom"
                                        title="{{ __('Set a user role') }}"></i>
                                    <span class="text-danger">*</span>@if ($role)
                                        <span class="text-danger">{{ __('(Role cannot be changed)') }}</span>
                                    @endif
                                </label>
                                {!! Form::select('role_id', $all_role, old('role_id', $role ? $role->id : null), array_merge([
                                    'placeholder' => __('Select Role'),
                                    'class' => 'form-control select2',
                                    'required' => true,
                                    'id' => 'role_id'
                                ], $role ? ['disabled' => 'disabled'] : [])) !!}
                                <span class="form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('role_id') }}</span>
                            </div>
                        </div>
                        <div class="col-md-4 co-xl-4">
                            <div class="form-group has-feedback">
                                <label for="type">{{ __('Role Type') }}
                                    <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom"
                                       title="{{ __('Set a role type') }}"></i>
                                    <span class="text-danger">*</span>
                                    @if ($role)
                                        <span class="text-danger">{{ __('(Role Type cannot be changed)') }}</span>
                                    @endif
                                </label>
                                <select name="type" id="type-select"
                                        class="form-control select2"
                                        {{ $role ? 'disabled' : 'required' }}>
                                    <option value="">{{ __('Select role type') }}</option>
                                    @foreach (AppHelper::USER_TYPE as $key => $label)
                                        <option value="{{ $key }}" {{ old('type', $typeGet) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @if ($role)
                                    <input type="hidden" name="type" value="{{ old('type', $typeGet) }}">
                                @endif
                                <span class="form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('type') }}</span>
                            </div>
                        </div>
                        <hr>
                        <div class="col-md-12">
                            <!-- All Permissions Section -->
                            <div class="form-group permission-section" id="all-permissions" style="display: none;">
                                <label class="fw-bold">{{ __('Manage All Permissions') }}</label>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="all-permissions-checkbox">
                                            <label class="form-check-label" for="all-permissions-checkbox">{{ __('Select All Permissions') }}</label>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        @if ($permissions->count() > 0)
                                            @php $counter = 0; @endphp
                                            @foreach ($permissions as $permission)
                                                @if ($counter % 4 == 0 && $counter > 0)
                                                    </div><div class="row">
                                                @endif
                                                <div class="col-md-3">
                                                    <div class="form-check">
                                                        <input {{ in_array($permission->id, $hasPermission) ? 'checked' : '' }}
                                                            class="form-check-input permission-checkbox-all" type="checkbox"
                                                            id="permission-all-{{ $permission->id }}" name="permissions[]"
                                                            value="{{ $permission->id }}">
                                                        <label class="form-check-label" for="permission-all-{{ $permission->id }}">
                                                            {{ $permission->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                                @php $counter++; @endphp
                                            @endforeach
                                        @else
                                            <div class="col-md-12">
                                                <p>No permissions available.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Sale Permissions Section -->
                            <div class="form-group permission-section" id="sale-permissions" style="display: none;">
                                <label class="fw-bold">{{ __('Manage Permissions for Sale') }}</label>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="all-dashboard-sale">
                                            <label class="form-check-label" for="all-dashboard-sale">{{ __('Select All Sale Permissions') }}</label>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        @php $salePermissions = $permissions->where('type', AppHelper::SALE); @endphp
                                        @if ($salePermissions->count() > 0)
                                            @php $counter = 0; @endphp
                                            @foreach ($salePermissions as $permission)
                                                @if ($counter % 4 == 0 && $counter > 0)
                                                    </div><div class="row">
                                                @endif
                                                <div class="col-md-3">
                                                    <div class="form-check">
                                                        <input {{ in_array($permission->id, $hasPermission) ? 'checked' : '' }}
                                                            class="form-check-input permission-checkbox-sale" type="checkbox"
                                                            id="permission-sale-{{ $permission->id }}" name="permissions[]"
                                                            value="{{ $permission->id }}">
                                                        <label class="form-check-label" for="permission-sale-{{ $permission->id }}">
                                                            {{ $permission->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                                @php $counter++; @endphp
                                            @endforeach
                                        @else
                                            <div class="col-md-12">
                                                <p>No permissions available for Sale.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- SE Permissions Section -->
                            <div class="form-group permission-section" id="se-permissions" style="display: none;">
                                <label class="fw-bold">{{ __('Manage Permissions for SE') }}</label>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="all-dashboard-se">
                                            <label class="form-check-label" for="all-dashboard-se">{{ __('Select All SE Permissions') }}</label>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        @php $sePermissions = $permissions->where('type', AppHelper::SE); @endphp
                                        @if ($sePermissions->count() > 0)
                                            @php $counter = 0; @endphp
                                            @foreach ($sePermissions as $permission)
                                                @if ($counter % 4 == 0 && $counter > 0)
                                                    </div><div class="row">
                                                @endif
                                                <div class="col-md-3">
                                                    <div class="form-check">
                                                        <input {{ in_array($permission->id, $hasPermission) ? 'checked' : '' }}
                                                            class="form-check-input permission-checkbox-se" type="checkbox"
                                                            id="permission-se-{{ $permission->id }}" name="permissions[]"
                                                            value="{{ $permission->id }}">
                                                        <label class="form-check-label" for="permission-se-{{ $permission->id }}">
                                                            {{ $permission->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                                @php $counter++; @endphp
                                            @endforeach
                                        @else
                                            <div class="col-md-12">
                                                <p>No permissions available for SE.</p>
                                            </div>
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
@endsection

@section('extraScript')
    <script type="text/javascript">
        $(document).ready(function () {
            // Function to toggle permissions based on selected type
            function togglePermissions() {
                var type = $('#type-select').val();
                $('.permission-section').hide(); // Hide all permission sections

                if (type === '1') { // All
                    $('#all-permissions').show();
                } else if (type === '2') { // Sale
                    $('#sale-permissions').show();
                } else if (type === '3') { // SE
                    $('#se-permissions').show();
                }
            }

            // Initial toggle based on pre-selected type (if any)
            togglePermissions();

            // Toggle permissions when the type selection changes
            $('#type-select').on('change', function () {
                togglePermissions();
            });

            // Select all permissions for "All"
            $('#all-permissions-checkbox').on('change', function () {
                $('.permission-checkbox-all').prop('checked', $(this).prop('checked'));
            });

            // Select all Sale permissions
            $('#all-dashboard-sale').on('change', function () {
                $('.permission-checkbox-sale').prop('checked', $(this).prop('checked'));
            });

            // Select all SE permissions
            $('#all-dashboard-se').on('change', function () {
                $('.permission-checkbox-se').prop('checked', $(this).prop('checked'));
            });
        });
    </script>
@endsection