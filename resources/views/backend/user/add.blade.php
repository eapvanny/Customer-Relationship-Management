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

        @media (max-width: 375px) {
            fieldset>#photo-preview {
                width: 200px !important;
                height: 210px !important;
            }
        }

        @media (max-width: 390px) {
            fieldset>#photo-preview {
                width: 200px !important;
                height: 210px !important;
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

        #map {
            height: 300px;
            width: 100%;
            margin-top: 10px;
        }

        .leaflet-touch .leaflet-control-attribution,
        .leaflet-touch .leaflet-control-layers,
        .leaflet-touch .leaflet-bar {
            display: none;
        }

        /* Add to existing extraStyle section */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
        }

        .loading-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .loading-content img {
            width: 50px;
            height: 50px;
            opacity: 0.4;
        }

        fieldset {
            padding: 1em 0.625em 1em;
            border: 1px solid #ddd;
            margin: 10px 0;
            padding: 0.35em 0.625em 0.75em;
            border-radius: 10px;
        }

        fieldset .form-group {
            margin-bottom: 0px;
        }

        .list-radio .form-group.has-error .help-block {
            position: absolute;
            width: 300px;
            bottom: -18px;
        }

        .list-time-schedule .error.help-block {
            position: absolute;
            width: 300px;
            bottom: -18px;
            color: #dd4b39;
            font-size: 12px;
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

        fieldset>#student-photo {
            overflow: hidden;
            cursor: pointer;
            width: 100%;
            height: 362px;
            background-color: #f5f5f5;
        }

        fieldset>#student-photo>#btn-upload-photo {
            min-width: 100px;
            min-height: 100px;
            background-color: #ddd;
            font-size: 25px;
        }

        fieldset>#photo-preview {
            height: 250px;
            width: 250px;
            position: absolute;
            object-fit: cover;
        }

        .fly_action_btn {
            z-index: 2;
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
                            <small class="toch">
                                @if ($user)
                                    {{ __('Update') }}
                                @else
                                    {{ __('Add New') }}
                                @endif
                            </small>
                        </h1>

                        <div class="box-tools pull-right">
                            <a href="{{ URL::route('user.index') }}" class="btn btn-default">{{ __('Cancel') }}</a>
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
                <div class="box-header d-none">
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
                        <div class="row">
                            <div class="form-group has-feedback">
                                <div class="row">
                                    <div class="row-span-12 col-sm-12 col-md-12 col-lg-12 col-xl-6 mt-1">
                                        <div class="form-group has-feedback position-relative">
                                            <input type="file" id="photo" name="photo" style="display: none"
                                                accept="image/*">
                                            <button type="button"
                                                class="btn btn-light text-secondary fs-5 position-absolute d-none m-2 end-0 z-1"
                                                id="btn-remove-photo"><i class="fa-solid fa-trash"></i></button>
                                            <fieldset id="photo-upload"
                                                class="p-0 d-flex align-items-center justify-content-center z-0 position-relative">
                                                <img class="rounded mx-auto d-block @if (!old('oldphoto') && !old('img-preview') && !isset($user)) {{ 'd-none' }} @endif z-1"
                                                    id="photo-preview" name="oldphoto"
                                                    src="@if (optional($user)->photo) {{ asset('storage/' . $user->photo) }}@else{{ old('oldphoto') }} @endif"
                                                    alt="photo">
                                                <input type="hidden" id="img-preview" name="oldphoto"
                                                    value="@if (optional($user)->photo) {{ asset($user->photo) }} @endif">
                                                <div class="d-flex align-items-center justify-content-center bg-transparent z-2  @if (!old('img-preview')) {{ 'opacity-100' }} @else {{ 'opacity-25' }} @endif"
                                                    id="student-photo">
                                                    <button class="btn p-3 rounded-circle" id="btn-upload-photo"
                                                        type="button" onclick="">
                                                        <i class="fa-solid fa-camera-retro"></i>
                                                    </button>
                                                </div>
                                                <label class="position-absolute bottom-0 text-center w-100 mb-2"
                                                    for="photo">
                                                    {{ __('User photo only accept jpg, png, jpeg images') }}
                                                </label>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-lg-12 col-xl-6">
                                        <div class="row">
                                            <div class="col-md-6 co-xl-6 col-lg-6">
                                                <div class="form-group has-feedback">
                                                    <label for="staff_id_card"> {{ __('Staff ID') }} <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="staff_id_card"
                                                        placeholder="staff_id_card"
                                                        value="@if ($user) {{ $user->staff_id_card }}@else{{ old('staff_id_card') }} @endif"
                                                        required minlength="3" maxlength="10">
                                                    <span class="fa fa-info form-control-feedback"></span>
                                                    <span class="text-danger">{{ $errors->first('staff_id_card') }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 co-xl-6 col-lg-6">
                                                <div class="form-group has-feedback">
                                                    <label for="phone_no"> {{ __('Phone No.') }} <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="phone_no"
                                                        value="@if ($user) {{ $user->phone_no }}@else{{ old('phone_no') }} @endif"
                                                        required>
                                                    <span class="fa fa-phone form-control-feedback"></span>
                                                    <span class="text-danger">{{ $errors->first('phone_no') }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 co-xl-6 col-lg-6">
                                                <div class="form-group has-feedback">
                                                    <label for="family_name"> {{ __('Family Name') }} <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="family_name"
                                                        placeholder="family_name"
                                                        value="@if ($user) {{ $user->family_name }}@else{{ old('family_name') }} @endif"
                                                        required minlength="2" maxlength="255">
                                                    <span class="fa fa-info form-control-feedback"></span>
                                                    <span class="text-danger">{{ $errors->first('family_name') }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 co-xl-6 col-lg-6">
                                                <div class="form-group has-feedback">
                                                    <label for="name"> {{ __('Name') }} <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="name"
                                                        value="@if ($user) {{ $user->name }}@else{{ old('name') }} @endif"
                                                        required minlength="2" maxlength="255">
                                                    <span class="fa fa-info form-control-feedback"></span>
                                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 co-xl-6 col-lg-6">
                                                <div class="form-group has-feedback">
                                                    <label for="family_name_latin"> {{ __('Family Name Latin') }} <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="family_name_latin"
                                                        value="@if ($user) {{ $user->family_name_latin }}@else{{ old('family_name_latin') }} @endif"
                                                        required minlength="2" maxlength="255">
                                                    <span class="fa fa-info form-control-feedback"></span>
                                                    <span
                                                        class="text-danger">{{ $errors->first('family_name_latin') }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 co-xl-6 col-lg-6">
                                                <div class="form-group has-feedback">
                                                    <label for="name_latin"> {{ __('Name Latin') }} <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="name_latin"
                                                        value="@if ($user) {{ $user->name_latin }}@else{{ old('name_latin') }} @endif"
                                                        required minlength="2" maxlength="255">
                                                    <span class="fa fa-info form-control-feedback"></span>
                                                    <span class="text-danger">{{ $errors->first('name_latin') }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 co-xl-6 col-lg-6">
                                                <div class="form-group has-feedback">
                                                    <label for="position"> {{ __('Position') }} <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="position"
                                                        value="@if ($user) {{ $user->position }}@else{{ old('position') }} @endif"
                                                        required>
                                                    <span class="fa fa-info form-control-feedback"></span>
                                                    <span class="text-danger">{{ $errors->first('position') }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 co-xl-6 col-lg-6">
                                                <div class="form-group has-feedback">
                                                    <label for="area"> {{ __('Area') }} <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="area"
                                                        value="@if ($user) {{ $user->area }}@else{{ old('area') }} @endif"
                                                        required>
                                                    <span class="fa fa-info form-control-feedback"></span>
                                                    <span class="text-danger">{{ $errors->first('area') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group has-feedback">
                                <label for="type">{{ __('User Type') }} <span class="text-danger">*</span></label>
                                {!! Form::select('type', $type, old('type', optional($user)->type), [
                                    'placeholder' => __('Select User Type'),
                                    'id' => 'type',
                                    'class' => 'form-control select2',
                                    'required' => true,
                                ]) !!}
                                <span class="form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('type') }}</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group has-feedback">
                                <label for="role_id">{{ __('User Role') }} <span class="text-danger">*</span></label>
                                {!! Form::select('role_id', [], old('role_id', optional($user)->role_id), [
                                    'placeholder' => __('Select User Type First'),
                                    'id' => 'role_id',
                                    'class' => 'form-control select2',
                                    'required' => true,
                                ]) !!}
                                <span class="form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('role_id') }}</span>
                            </div>
                        </div>

                        <div class="col-md-12 col-xl-6 col-lg-6 d-none" id="sup-section">
                            <div class="form-group has-feedback">
                                <label for="sup_id">{{ __('Supervisor') }} <span class="text-danger">*</span>
                                    <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom"
                                        title="Select Supervisor"></i>
                                </label>
                                {!! Form::select('sup_id', [], old('sup_id', optional($user)->sup_id), [
                                    'placeholder' => __('Select a Supervisor'),
                                    'id' => 'sup_id',
                                    'name' => 'sup_id',
                                    'class' => 'form-control select2',
                                ]) !!}
                                <span class="form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('sup_id') }}</span>
                            </div>
                        </div>

                        <div class="col-md-12 col-xl-6 col-lg-6 d-none" id="asm-section">
                            <div class="form-group has-feedback">
                                <label for="asm_id">{{ __('ASM') }} <span class="text-danger">*</span>
                                    <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom"
                                        title="Select ASM"></i>
                                </label>
                                {!! Form::select('asm_id', [], old('asm_id', optional($user)->asm_id), [
                                    'placeholder' => __('Select a Supervisor first'),
                                    'id' => 'asm_id',
                                    'name' => 'asm_id',
                                    'class' => 'form-control select2',
                                ]) !!}
                                <span class="form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('asm_id') }}</span>
                            </div>
                        </div>

                        <div class="col-md-12 col-xl-6 col-lg-6 d-none" id="rsm-section">
                            <div class="form-group has-feedback">
                                <label for="rsm_id">{{ __('RSM') }} <span class="text-danger">*</span>
                                    <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom"
                                        title="Select RSM"></i>
                                </label>
                                {!! Form::select('rsm_id', [], old('rsm_id', optional($user)->rsm_id), [
                                    'placeholder' => __('Select an ASM first'),
                                    'id' => 'rsm_id',
                                    'name' => 'rsm_id',
                                    'class' => 'form-control select2',
                                ]) !!}
                                <span class="form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('rsm_id') }}</span>
                            </div>
                        </div>

                        <div class="col-md-12 col-xl-6 col-lg-6 d-none" id="manager-section">
                            <div class="form-group has-feedback">
                                <label for="manager_id">{{ __('Manager') }} <span class="text-danger">*</span>
                                    <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom"
                                        title="Select Manager"></i>
                                </label>
                                {!! Form::select('manager_id', [], old('manager_id', optional($user)->manager_id), [
                                    'placeholder' => __('Select an RSM first'),
                                    'id' => 'manager_id',
                                    'name' => 'manager_id',
                                    'class' => 'form-control select2',
                                ]) !!}
                                <span class="form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('manager_id') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-xl-6">
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

                        <div class="col-md-6 col-xl-6">
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
                    </div>
                    <div class="row">
                        <div class="col-md-6 co-xl-6 col-lg-6">
                            <div class="form-group has-feedback">
                                <label for="email"> {{ __('Email') }} <span class=""></span></label>
                                <input type="email" class="form-control" name="email" placeholder="email address"
                                    value="@if ($user) {{ $user->email }}@else{{ old('email') }} @endif"
                                    maxlength="100">
                                {{-- <span class="fa fa-envelope form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('email') }}</span> --}}
                            </div>
                        </div>
                        <div class="col-md-6 co-xl-6 col-lg-6">
                            <div class="form-group has-feedback">
                                <label for="username"> {{ __('Username') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control"
                                    value="@if ($user) {{ $user->username }}@else{{ old('username') }} @endif"
                                    name="username" required minlength="2" maxlength="255" autocomplete="new-password">
                                <span class="glyphicon glyphicon-info-sign form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('username') }}</span>
                            </div>
                        </div>
                        @if (!$user)
                            <div class="col-md-6 co-xl-6 col-lg-6">
                                <div class="form-group has-feedback">
                                    <label for="password"> {{ __('Password') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="password" placeholder="Password"
                                        required minlength="6" maxlength="50" autocomplete="new-password">
                                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                </div>
                            </div>
                        @endif
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

            const SALE = "{{ AppHelper::SALE }}";
            const SE = "{{ AppHelper::SE }}";
            const EMPLOYEE = "{{ AppHelper::USER_EMPLOYEE }}";
            const ASM = "{{ AppHelper::USER_ASM }}";
            const SUP = "{{ AppHelper::USER_SUP }}";
            const RSM = "{{ AppHelper::USER_RSM }}";
            const MANAGER = "{{ AppHelper::USER_MANAGER }}";

            function toggleHierarchyFields() {
                const selectedType = $('#type').val();

                // Reset all role/hierarchy fields
                $('#role_id').empty().append('<option value="">{{ __('Select User Type First') }}</option>')
                    .trigger('change');
                $('#sup-section, #asm-section, #rsm-section, #manager-section').addClass('d-none');
                $('#sup_id, #asm_id, #rsm_id, #manager_id').empty().append(
                        '<option value="">{{ __('Select') }}</option>')
                    .val(null).trigger('change').prop('required', false);

                if (!selectedType) return;

                // Fetch roles for selected type
                $.ajax({
                    url: "{{ route('user.fetchRolesByType') }}",
                    method: 'GET',
                    data: {
                        type_id: selectedType
                    },
                    success: function(response) {
                        const roleSelect = $('#role_id');
                        roleSelect.empty().append('<option value="">{{ __('Select Role') }}</option>');
                        $.each(response.roles, function(id, name) {
                            roleSelect.append(`<option value="${id}">${name}</option>`);
                        });

                        const oldRole = "{{ old('role_id', optional($user)->role_id) }}";
                        if (oldRole) {
                            roleSelect.val(oldRole).trigger('change');
                        }
                    }
                });
            }

            function handleRoleChange() {
                const selectedType = $('#type').val();
                const selectedRole = $('#role_id').val();

                // Reset all hierarchy fields
                $('#sup-section, #asm-section, #rsm-section, #manager-section').addClass('d-none');
                $('#sup_id, #asm_id, #rsm_id, #manager_id').empty().append(
                        '<option value="">{{ __('Select') }}</option>')
                    .val(null).trigger('change').prop('required', false);

                if (selectedType == SALE || selectedType == SE) {
                    if (selectedRole == EMPLOYEE) {
                        // For Employee: show Supervisor first, then ASM, RSM, Manager
                        $('#sup-section, #asm-section, #rsm-section, #manager-section').removeClass('d-none');
                        $('#sup_id').prop('required', true);
                        $('#sup_id').empty().append('<option value="">{{ __('Select Supervisor') }}</option>');
                        $('#asm_id').empty().append(
                            '<option value="">{{ __('Select a Supervisor first') }}</option>');
                        $('#rsm_id').empty().append('<option value="">{{ __('Select ASM first') }}</option>');
                        $('#manager_id').empty().append('<option value="">{{ __('Select RSM first') }}</option>');

                        fetchSupervisors(selectedType, selectedRole);
                    } else if (selectedRole == SUP) {
                        // For Supervisor: show ASM, RSM, and Manager
                        $('#asm-section, #rsm-section, #manager-section').removeClass('d-none');
                        $('#asm_id, #rsm_id, #manager_id').prop('required', true);

                        $('#asm_id').empty().append('<option value="">{{ __('Select ASM') }}</option>');
                        $('#rsm_id').empty().append('<option value="">{{ __('Select ASM first') }}</option>');
                        $('#manager_id').empty().append('<option value="">{{ __('Select RSM first') }}</option>');

                        fetchAsms(selectedType, selectedRole);
                    } else if (selectedRole == ASM) {
                        // For ASM: show RSM and Manager
                        $('#rsm-section, #manager-section').removeClass('d-none');
                        $('#rsm_id, #manager_id').prop('required', true);

                        $('#rsm_id').empty().append('<option value="">{{ __('Select RSM') }}</option>');
                        $('#manager_id').empty().append('<option value="">{{ __('Select RSM first') }}</option>');

                        fetchRsms(selectedType, selectedRole);
                    } else if (selectedRole == RSM) {
                        // For RSM: show Manager only
                        $('#manager-section').removeClass('d-none');
                        $('#manager_id').prop('required', true);
                        $('#manager_id').empty().append('<option value="">{{ __('Select Manager') }}</option>');

                        fetchManagers(selectedType, selectedRole);
                    }
                }

                // Trigger existing values if present
                const oldSup = "{{ old('sup_id', optional($user)->sup_id) }}";
                const oldAsm = "{{ old('asm_id', optional($user)->asm_id) }}";
                const oldRsm = "{{ old('rsm_id', optional($user)->rsm_id) }}";
                const oldManager = "{{ old('manager_id', optional($user)->manager_id) }}";

                if (selectedRole == EMPLOYEE && oldSup) {
                    $('#sup_id').val(oldSup).trigger('change');
                }
                if ((selectedRole == SUP || selectedRole == EMPLOYEE) && oldAsm) {
                    $('#asm_id').val(oldAsm).trigger('change');
                }
                if ((selectedRole == ASM || selectedRole == SUP || selectedRole == EMPLOYEE) && oldRsm) {
                    $('#rsm_id').val(oldRsm).trigger('change');
                }
                if (oldManager) {
                    $('#manager_id').val(oldManager).trigger('change');
                }
            }

            function fetchSupervisors(typeId, roleId, asmId = null) {
                $.ajax({
                    url: "{{ route('user.fetchSupervisors') }}",
                    method: 'GET',
                    data: {
                        type_id: typeId,
                        role_id: roleId,
                        asm_id: asmId
                    },
                    success: function(response) {
                        const supSelect = $('#sup_id');
                        supSelect.empty().append(
                            '<option value="">{{ __('Select Supervisor') }}</option>');
                        $.each(response.supervisors, (id, name) => {
                            supSelect.append(`<option value="${id}">${name}</option>`);
                        });
                        const oldSup = "{{ old('sup_id', optional($user)->sup_id) }}";
                        if (oldSup) {
                            supSelect.val(oldSup).trigger('change');
                        }
                    }
                });
            }

            function fetchAsms(typeId, roleId, supId = null) {
                $.ajax({
                    url: "{{ route('user.fetchAsms') }}",
                    method: 'GET',
                    data: {
                        type_id: typeId,
                        role_id: roleId,
                        sup_id: supId
                    },
                    success: function(response) {
                        const asmSelect = $('#asm_id');
                        asmSelect.empty().append('<option value="">{{ __('Select ASM') }}</option>');
                        $.each(response.asms, (id, name) => {
                            asmSelect.append(`<option value="${id}">${name}</option>`);
                        });
                        const oldAsm = "{{ old('asm_id', optional($user)->asm_id) }}";
                        if (oldAsm) {
                            asmSelect.val(oldAsm).trigger('change');
                        }
                    }
                });
            }

            function fetchRsms(typeId, roleId, asmId = null) {
                $.ajax({
                    url: "{{ route('user.fetchRsms') }}",
                    method: 'GET',
                    data: {
                        type_id: typeId,
                        role_id: roleId,
                        asm_id: asmId
                    },
                    success: function(response) {
                        const rsmSelect = $('#rsm_id');
                        rsmSelect.empty().append('<option value="">{{ __('Select RSM') }}</option>');
                        $.each(response.rsms, (id, name) => {
                            rsmSelect.append(`<option value="${id}">${name}</option>`);
                        });
                        const oldRsm = "{{ old('rsm_id', optional($user)->rsm_id) }}";
                        if (oldRsm) {
                            rsmSelect.val(oldRsm).trigger('change');
                        }
                    }
                });
            }

            function fetchManagers(typeId, roleId, rsmId = null) {
                $.ajax({
                    url: "{{ route('user.fetchManagers') }}",
                    method: 'GET',
                    data: {
                        type_id: typeId,
                        role_id: roleId,
                        rsm_id: rsmId
                    },
                    success: function(response) {
                        const managerSelect = $('#manager_id');
                        managerSelect.empty().append(
                            '<option value="">{{ __('Select Manager') }}</option>');
                        $.each(response.managers, (id, name) => {
                            managerSelect.append(`<option value="${id}">${name}</option>`);
                        });
                        const oldManager = "{{ old('manager_id', optional($user)->manager_id) }}";
                        if (oldManager) {
                            managerSelect.val(oldManager).trigger('change');
                        }
                    }
                });
            }

            // Event handlers
            $('#sup_id').on('change', function() {
                const selectedSupId = $(this).val();
                const selectedType = $('#type').val();
                const selectedRole = $('#role_id').val();

                if (selectedRole == EMPLOYEE && selectedSupId) {
                    // For Employee: when Supervisor is selected, show and fetch ASMs
                    $('#asm-section').removeClass('d-none');
                    $('#asm_id').prop('required', true);
                    $('#asm_id').empty().append('<option value="">{{ __('Select ASM') }}</option>');
                    $('#rsm_id').empty().append('<option value="">{{ __('Select ASM first') }}</option>');
                    $('#manager_id').empty().append(
                        '<option value="">{{ __('Select RSM first') }}</option>');
                    fetchAsms(selectedType, selectedRole, selectedSupId);
                } else {
                    $('#asm-section').addClass('d-none');
                    $('#asm_id').prop('required', false);
                }
            });

            $('#asm_id').on('change', function() {
                const selectedAsmId = $(this).val();
                const selectedType = $('#type').val();
                const selectedRole = $('#role_id').val();

                if ((selectedRole == SUP || selectedRole == EMPLOYEE) && selectedAsmId) {
                    // For Supervisor or Employee: when ASM is selected, show and fetch RSM
                    $('#rsm-section').removeClass('d-none');
                    $('#rsm_id').prop('required', true);
                    $('#rsm_id').empty().append('<option value="">{{ __('Select RSM') }}</option>');
                    $('#manager_id').empty().append(
                        '<option value="">{{ __('Select RSM first') }}</option>');
                    fetchRsms(selectedType, selectedRole, selectedAsmId);
                } else {
                    $('#rsm-section').addClass('d-none');
                    $('#rsm_id').prop('required', false);
                }
            });

            $('#rsm_id').on('change', function() {
                const selectedRsmId = $(this).val();
                const selectedType = $('#type').val();
                const selectedRole = $('#role_id').val();

                if ((selectedRole == ASM || selectedRole == SUP || selectedRole == EMPLOYEE) &&
                    selectedRsmId) {
                    // For ASM, Supervisor, or Employee: when RSM is selected, show and fetch Manager
                    $('#manager-section').removeClass('d-none');
                    $('#manager_id').prop('required', true);
                    $('#manager_id').empty().append(
                        '<option value="">{{ __('Select Manager') }}</option>');
                    fetchManagers(selectedType, selectedRole, selectedRsmId);
                } else {
                    $('#manager-section').addClass('d-none');
                    $('#manager_id').prop('required', false);
                }
            });

            // Initial setup
            toggleHierarchyFields();

            // Events
            $('#type').on('change', toggleHierarchyFields);
            $('#role_id').on('change', handleRoleChange);

            // Photo upload handling (unchanged)
            $('#photo-upload').on('click', function() {
                $("#photo").trigger("click");
            });

            $('#btn-remove-photo').on('click', function() {
                $("#photo").val('');
                $('#img-preview').val('');
                $("#photo-preview").removeAttr('src').addClass('d-none');
                $('#btn-remove-photo').addClass('d-none');
                $('#btn-upload-photo').removeClass('d-none');
                $('#student-photo').removeClass('opacity-25').addClass('opacity-100');
            });

            $("#photo").change(function(e) {
                var file = e.target.files[0];
                if (!file) {
                    return;
                }
                var reader = new FileReader();
                reader.onload = function(event) {
                    $("#photo-preview").attr("src", event.target.result);
                    $('#img-preview').val(event.target.result);
                    $("#photo-preview").removeClass("d-none");
                    $('#btn-upload-photo').addClass('d-none');
                    $('#btn-remove-photo').removeClass('d-none');
                    $('#student-photo').removeClass('opacity-100').addClass('opacity-25');
                };
                reader.readAsDataURL(file);
            });

            // Hide/show image preview
            if ($("#photo-preview").attr("src")) {
                $('#btn-upload-photo').removeClass('d-none');
                $('#btn-remove-photo').addClass('d-none');
            } else {
                $('#btn-upload-photo').removeClass('d-none');
                $('#btn-remove-photo').addClass('d-none');
                $("#photo-preview").addClass('d-none');
            }
        });
    </script>
@endsection
<!-- END PAGE JS-->
