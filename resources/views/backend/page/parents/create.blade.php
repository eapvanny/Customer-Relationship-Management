<fieldset>

    <legend>{{ __('Parent status') }}:</legend>
    <div class="radio iradio">
        <label>
            <input class="parent_status" type="radio" name="parent_status" value="all" @if($parent) {{$parent->parent_status == 'all' ? 'checked':''}} @else{{ old('parent_status') == 'all' || empty(old('parent_status')) ? 'checked' : ''}}@endif> {{ __('All') }}
        </label>
    </div>
    <div class="radio iradio">
        <label>
            <input class="parent_status" type="radio" name="parent_status" value="father" @if($parent) {{$parent->parent_status == 'father' ? 'checked':''}} @else{{ old('parent_status') == 'father' ? 'checked' : ''}}@endif> {{ __('Father') }}
        </label>
    </div>
    <div class="radio iradio">
        <label>
            <input class="parent_status" type="radio" name="parent_status" value="mother" @if($parent) {{$parent->parent_status == 'mother' ? 'checked':''}} @else{{ old('parent_status') == 'mother' ? 'checked' : ''}}@endif> {{ __('Mother') }}
        </label>
    </div>
</fieldset>
<fieldset class="father_block" @if($parent) {!! $parent->parent_status == 'mother' ? 'style="display: none;"':'' !!} @else{!! old('parent_status') == 'mother' ? 'style="display: none;"' : '' !!}@endif>
    <legend> {{ __('Father Info') }}.:</legend>
    <div class="row">
        <div class="col-md-6">
            <div class="col-md-6">
                <div class="form-group has-feedback">
                    <label for="father_name"> {{ __('Family Name') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="father_family_name" placeholder="{{ __('Family Name') }}" required value="@if($parent){{ old('father_family_name')??$parent->father_family_name }}@else{{old('father_family_name')}}@endif"  maxlength="255">
                    <span class="fa fa-info form-control-feedback"></span>
                    <span class="text-danger">{{ $errors->first('father_family_name') }}</span>
                </div>
            </div>
            <div class="form-group has-feedback">
                <label for="father_name"> {{ __('Given Name') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="father_name" placeholder="{{ __('Given Name') }}" required value="@if($parent){{ old('father_name')??$parent->father_name }}@else{{old('father_name')}}@endif"  maxlength="255">
                <span class="fa fa-info form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('father_name') }}</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group has-feedback">
                <label for="father_name"> {{ __('City') }} </label>
                <input type="text" class="form-control" name="father_city" placeholder="{{ __('City') }}" value="@if($parent){{ $parent->father_city }}@else{{old('father_city')}}@endif"  maxlength="255">
                <span class="fa fa-info form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('father_city') }}</span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group has-feedback">
                <label for="father_name"> {{ __('Country') }} </label>
                <input type="text" class="form-control" name="father_country" placeholder="{{ __('Country') }}" value="@if($parent){{ $parent->father_country }}@else{{old('father_country')}}@endif"  maxlength="255">
                <span class="fa fa-info form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('father_country') }}</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group has-feedback">
                <label for="father_phone_no"> {{ __('Phone/Mobile No') }}.<span class="text-danger">*</span></label>
                <input  type="number" class="form-control" name="father_phone_no" placeholder="{{ __('phone or mobile number') }}" required value="@if($parent){{old('father_phone_no')??$parent->father_phone_no}}@else{{old('father_phone_no')}}@endif" maxlength="15">
                <span class="fa fa-phone form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('father_phone_no') }}</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group has-feedback">
                <label for="father_email"> {{ __('Email') }} <span class="text-danger"></span></label>
                <input  type="email" class="form-control" name="father_email"  placeholder="{{ __('Email') }}" value="@if($parent){{$parent->father_email}}@else{{old('father_email')}}@endif" maxlength="100">
                <span class="fa fa-envelope form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('father_email') }}</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group has-feedback">
                <label for="father_phone_no"> {{ __('Passports No') }} </label>
                <input  type="text" class="form-control" name="father_passport" placeholder="{{ __('Passports No') }}" value="@if($parent){{$parent->father_passport}}@else{{old('father_passport')}}@endif" maxlength="15">
                <span class="fa fa-info form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('father_passport') }}</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group has-feedback">
                <label for="father_id_card"> {{ __('Father ID Card') }} <span class="text-danger">*</span></label>
                <input  type="text" class="form-control" name="father_id_card"  placeholder="{{ __('id card number') }}" required value="@if($parent){{old('father_id_card')??$parent->father_id_card}}@else{{old('father_id_card')}}@endif" minlength="1" maxlength="30">
                <span class="fa fa-id-card form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('father_id_card') }}</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group has-feedback">
                <label for="father_email"> {{ __('Nationality') }} <span class="text-danger"></span></label>
                <input  type="father_nationality" class="form-control" name="father_nationality"  placeholder="{{ __('Nationality') }}" value="@if($parent){{$parent->father_nationality}}@else{{old('father_nationality')}}@endif" maxlength="100">
                <span class="fa fa-info form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('father_nationality') }}</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group has-feedback">
                <label for="father_phone_no"> {{ __('Occupation') }} </label>
                <input  type="text" class="form-control" name="father_occupation" placeholder="{{ __('Occupation') }}" value="@if($parent){{$parent->father_occupation}}@else{{old('father_occupation')}}@endif" maxlength="15">
                <span class="fa fa-info form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('father_occupation') }}</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group has-feedback">
                <label for="father_email"> {{ __('Name of the Company/Organization') }} <span class="text-danger"></span></label>
                <input  type="father_nationality" class="form-control" name="father_company"  placeholder=" {{ __('Name of the Company/Organization') }} " value="@if($parent){{$parent->father_company}}@else{{old('father_company')}}@endif" maxlength="100">
                <span class="fa fa-info form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('father_company') }}</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group has-feedback">
                <label for="father_email"> {{ __('Company/Organization’s Address: City') }} <span class="text-danger"></span></label>
                <input  type="father_nationality" class="form-control" name="father_company_city"  placeholder=" {{ __('Company/Organization’s Address: City') }} " value="@if($parent){{$parent->father_company_city}}@else{{old('father_company_city')}}@endif" maxlength="100">
                <span class="fa fa-info form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('father_company_city') }}</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group has-feedback">
                <label for="father_email"> {{ __('Company/Organization’s Address: Country') }} <span class="text-danger"></span></label>
                <input  type="father_nationality" class="form-control" name="father_company_country"  placeholder="{{ __('Company/Organization’s Address: Country') }}" value="@if($parent){{$parent->father_company_country}}@else{{old('father_company_country')}}@endif" maxlength="100">
                <span class="fa fa-info form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('father_company_country') }}</span>
            </div>
        </div>
    </div>
        {{--<div class="form-group has-feedback">--}}
        {{--<label for="father_faces">Images for face recognition<span class="text-danger"></span></label>--}}
        {{--<input type="file" name="father_faces[]" class="form-control" multiple accept="image/*">--}}
        {{--<span class="text-danger">{{ $errors->first('father_faces') }}</span>--}}
        {{--<span class="text-danger">{{ $errors->first('father_faces.*') }}</span>--}}
        {{--</div>--}}
    <div class="row">
        <div class="col-md-6">
            <div class="form-group has-feedback">
                <label for="photo"> {{ __('Father Profile Photo') }} <span class="text-danger">[files: jpeg, jpg, png min:150x150 max-size: 2Mb]</span></label>
                <input  type="file" class="form-control" accept=".jpeg, .jpg, .png" name="father_profile_photo" placeholder="{{ __('Photo image') }}">
                @if($parent && isset($parent->profile_photo))
                    <input type="hidden" name="oldfather_profile_photo" value="{{$parent->profile_photo}}">
                @endif
                <span class="glyphicon glyphicon-open-file form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('father_profile_photo') }}</span>
            </div>
            <div class="form-group has-feedback">
                <label for="father_address"> {{ __('Permanent Address') }} </label>
                <textarea name="father_address" class="form-control" maxlength="500" >@if($parent){{ $parent->father_address }}@else{{ old('father_address') }} @endif</textarea>
                <span class="fa fa-location-arrow form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('father_address') }}</span>
            </div>
            <div class="form-group has-feedback">
                <label for="faces"> {{ __('CONSENT TO USE CHILD’S IMAGES') }} <span class="text-danger">*</span></label>
                {!! Form::select('father_consent_to_user_child_images', ['Not Consent','Consent'], $parent ? $parent->father_consent_to_user_child_images : null , ['placeholder' => 'Please Choose...','class' => 'form-control select2', 'required' => 'true']) !!}
                <span class="text-danger">{{ $errors->first('father_consent_to_user_child_images') }}</span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group has-feedback">
                <label for="faces"> {{ __('Father TraFather Photo') }} <span class="text-danger"></span></label>
                <div id="fatherFaces" class="dropzone">
                    <div class="dz-message" data-dz-message><i class="fa fa-upload" aria-hidden="true"></i> {{ __('Drop image here') }} <br><span class="text-danger">[files: jpeg, jpg, png min: 150x150 max-size: 200Kb]</span></div>
                    <div class="dz-default dz-message"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" @if(request()->is('student/*')) {!!'hidden'!!} @endif>
        @if(!$parent)
            <div class="col-md-3">
                <div class="form-group has-feedback">
                    <label for="father_username"> {{ __('Username') }} <span class="text-danger"></span></label>
                    <input  type="text" class="form-control" value="{{ old('father_username') }}" name="father_username">
                    <span class="glyphicon glyphicon-info-sign form-control-feedback"></span>
                    <span class="text-danger">{{ $errors->first('father_username') }}</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group has-feedback">
                    <label for="father_password"> {{ __('Password') }} <span class="text-danger"></span></label>
                    <input type="password" class="form-control" name="father_password"  value="{{ old('father_password') }}" placeholder="{{ __('Password') }}">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    <span class="text-danger">{{ $errors->first('father_password') }}</span>
                </div>
            </div>

        @else

            @if (!$parent->user_id)
                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label for="father_username"> {{ __('Username') }} <span class="text-danger"></span></label>
                        <input  type="text" class="form-control" value="{{old('father_username')}}" name="father_username">
                        <span class="glyphicon glyphicon-info-sign form-control-feedback"></span>
                        <span class="text-danger">{{ $errors->first('father_username') }}</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label for="father_password"> {{ __('Password') }} <span class="text-danger"></span></label>
                        <input type="password" class="form-control" name="father_password" placeholder=" {{ __('Password') }} ">
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        <span class="text-danger">{{ $errors->first('father_password') }}</span>
                    </div>
                </div>
            @endif

        @endif
    </div>
</fieldset>
<fieldset class="mather_block" @if($parent) {!! $parent->parent_status == 'father' ? 'style="display: none;"':'' !!} @else{!! old('parent_status') == 'father' ? 'style="display: none;"' : '' !!}@endif>
    <legend> {{ __('Mother Info') }}.:</legend>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group has-feedback">
                <label for="mother_name"> {{ __('Given Name') }} <span class="text-danger">*</span></label>
                <input  type="text" class="form-control" name="mother_name" placeholder=" {{ __('Given Name') }} " required value="@if($parent){{ old('mother_name')??$parent->mother_name }}@else{{old('mother_name')}}@endif" maxlength="255">
                <span class="fa fa-info form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('mother_name') }}</span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group has-feedback">
                <label for="mother_family_name"> {{ __('Family Name') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="mother_family_name" placeholder=" {{ __('Family Name') }} " required value="@if($parent){{ old('mother_family_name')??$parent->mother_family_name }}@else{{old('mother_family_name')}}@endif"  maxlength="255">
                <span class="fa fa-info form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('mother_family_name') }}</span>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group has-feedback">
                <label for="father_name"> {{ __('City') }} </label>
                <input type="text" class="form-control" name="mother_city" placeholder=" {{ __('City') }} " value="@if($parent){{ $parent->mother_city }}@else{{old('mother_city')}}@endif"  maxlength="255">
                <span class="fa fa-info form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('mother_city') }}</span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group has-feedback">
                <label for="father_name"> {{ __('Country') }} </label>
                <input type="text" class="form-control" name="mother_country" placeholder=" {{ __('Country') }} " value="@if($parent){{ $parent->mother_country }}@else{{old('mother_country')}}@endif"  maxlength="255">
                <span class="fa fa-info form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('mother_country') }}</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group has-feedback">
                <label for="mother_phone_no"> {{ __('Phone/Mobile No') }}.<span class="text-danger">*</span></label>
                <input  type="number" class="form-control" name="mother_phone_no"  placeholder="{{ __('phone or mobile number') }}" required value="@if($parent){{old('mother_phone_no')??$parent->mother_phone_no}}@else{{old('mother_phone_no')}}@endif"  maxlength="15">
                <span class="fa fa-phone form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('mother_phone_no') }}</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group has-feedback">
                <label for="mother_email"> {{ __('Email') }} <span class="text-danger"></span></label>
                <input  type="email" class="form-control" name="mother_email"  placeholder=" {{ __('Email') }} " value="@if($parent){{$parent->mother_email}}@else{{old('mother_email')}}@endif" maxlength="100">
                <span class="fa fa-envelope form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('mother_email') }}</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group has-feedback">
                <label for="father_phone_no"> {{ __('Passports No') }} </label>
                <input  type="text" class="form-control" name="mother_passport" placeholder=" {{ __('Passports No') }} " value="@if($parent){{$parent->mother_passport}}@else{{old('mother_passport')}}@endif" maxlength="15">
                <span class="fa fa-info form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('mother_passport') }}</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group has-feedback">
                <label for="mother_id_card"> {{ __('Mother ID Card') }} <span class="text-danger">*</span></label>
                <input  type="text" class="form-control" name="mother_id_card"  placeholder="{{ __('id card number') }}" required value="@if($parent){{old('mother_id_card')??$parent->mother_id_card}}@else{{old('mother_id_card')}}@endif"  minlength="1" maxlength="30">
                <span class="fa fa-id-card form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('mother_id_card') }}</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group has-feedback">
                <label for="mother_nationality"> {{ __('Nationality') }} <span class="text-danger"></span></label>
                <input  type="text" class="form-control" name="mother_nationality"  placeholder=" {{ __('Nationality') }} "  value="@if($parent){{$parent->mother_nationality}}@else{{old('mother_nationality')}}@endif" maxlength="100">
                <span class="fa fa-info form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('mother_nationality') }}</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group has-feedback">
                <label for="father_phone_no"> {{ __('Occupation') }} </label>
                <input  type="text" class="form-control" name="mother_occupation" placeholder=" {{ __('Occupation') }} " value="@if($parent){{$parent->mother_occupation}}@else{{old('mother_occupation')}}@endif" maxlength="15">
                <span class="fa fa-info form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('mother_occupation') }}</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group has-feedback">
                <label for="father_email"> {{ __('Name of the Company/Organization') }} <span class="text-danger"></span></label>
                <input  type="mother_company" class="form-control" name="mother_company"  placeholder=" {{ __('Name of the Company/Organization') }} "  value="@if($parent){{$parent->mother_company}}@else{{old('mother_company')}}@endif" maxlength="100">
                <span class="fa fa-info form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('mother_company') }}</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group has-feedback">
                <label for="father_email"> {{ __('Company/Organization’s Address: City') }} <span class="text-danger"></span></label>
                <input  type="father_nationality" class="form-control" name="mother_company_city"  placeholder=" {{ __('Company/Organization’s Address: City') }} " value="@if($parent){{$parent->mother_company_city}}@else{{old('mother_company_city')}}@endif" maxlength="100">
                <span class="fa fa-info form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('mother_company_city') }}</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group has-feedback">
                <label for="father_email"> {{ __('Company/Organization’s Address: Country') }} <span class="text-danger"></span></label>
                <input  type="father_nationality" class="form-control" name="mother_company_country"  placeholder=" {{ __('Company/Organization’s Address: Country') }} " value="@if($parent){{$parent->mother_company_country}}@else{{old('mother_company_country')}}@endif" maxlength="100">
                <span class="fa fa-info form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('mother_company_country') }}</span>
            </div>
        </div>
    </div>
        {{--<div class="form-group has-feedback">--}}
        {{--<label for="father_email">Images for face recognition<span class="text-danger"></span></label>--}}
        {{--<input type="file" name="mother_faces[]" class="form-control" multiple accept="image/*">--}}
        {{--<span class="text-danger">{{ $errors->first('mother_faces') }}</span>--}}
        {{--<span class="text-danger">{{ $errors->first('mother_faces.*') }}</span>--}}
        {{--</div>--}}

    <div class="row">
        <div class="col-md-6">
            <div class="form-group has-feedback">
                <label for="photo"> {{ __('Mother Profile Photo') }} <span class="text-danger">[files: jpeg, jpg, png min:150x150 max-size: 2Mb]</span></label>
                <input  type="file" class="form-control" accept=".jpeg, .jpg, .png" name="mother_profile_photo" placeholder="{{ __('Photo image') }}">
                @if($parent && isset($parent->profile_photo))
                    <input type="hidden" name="oldmother_profile_photo" value="{{$parent->profile_photo}}">
                @endif
                <span class="glyphicon glyphicon-open-file form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('mother_profile_photo') }}</span>
            </div>
            <div class="form-group has-feedback">
                <label for="mother_address"> {{ __('Permanent Address') }} </label>
                <textarea name="mother_address" class="form-control" maxlength="500" >@if($parent){{ $parent->mother_address }}@else{{ old('mother_address') }} @endif</textarea>
                <span class="fa fa-location-arrow form-control-feedback"></span>
                <span class="text-danger">{{ $errors->first('mother_address') }}</span>
            </div>
            <div class="form-group has-feedback">
                <label for="faces"> {{ __('CONSENT TO USE CHILD’S IMAGES') }} <span class="text-danger">*</span></label>
                {!! Form::select('mother_consent_to_user_child_images', ['Not Consent','Consent'], $parent ? $parent->mother_consent_to_user_child_images : null , ['placeholder' => 'Please Choose...','class' => 'form-control select2', 'required' => 'true']) !!}
                <span class="text-danger">{{ $errors->first('mother_consent_to_user_child_images') }}</span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group has-feedback">
                <label for="faces"> {{ __('Mother Train Face Photo') }} <span class="text-danger"></span></label>
                <div id="motherFaces" class="dropzone">
                    <div class="dz-message" data-dz-message><i class="fa fa-upload" aria-hidden="true"></i> {{ __('Drop image here') }} <br><span class="text-danger">[files: jpeg, jpg, png min: 150x150 max-size: 200Kb]</span></div>
                    <div class="dz-default dz-message"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" @if(request()->is('student/*')) {!!'hidden'!!} @endif>
        @if(!$parent)
            <div class="col-md-3">
                <div class="form-group has-feedback">
                    <label for="mother_username"> {{ __('Username') }} <span class="text-danger"></span></label>
                    <input  type="text" class="form-control" value="{{ old('mother_username') }}" name="mother_username" minlength="5" maxlength="255">
                    <span class="glyphicon glyphicon-info-sign form-control-feedback"></span>
                    <span class="text-danger">{{ $errors->first('mother_username') }}</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group has-feedback">
                    <label for="mother_password"> {{ __('Password') }} <span class="text-danger"></span></label>
                    <input type="password" class="form-control" name="mother_password"  value="{{ old('mother_password') }}" placeholder=" {{ __('Password') }} " minlength="6" maxlength="50">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    <span class="text-danger">{{ $errors->first('mother_password') }}</span>
                </div>
            </div>

        @else

            @if (!$parent->user_id)
                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label for="mother_username"> {{ __('Username') }} <span class="text-danger"></span></label>
                        <input  type="text" class="form-control" value="{{old('mother_username')}}" name="mother_username" minlength="5" maxlength="255">
                        <span class="glyphicon glyphicon-info-sign form-control-feedback"></span>
                        <span class="text-danger">{{ $errors->first('mother_username') }}</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label for="mother_password"> {{ __('Password') }} <span class="text-danger"></span></label>
                        <input type="password" class="form-control" name="mother_password" placeholder=" {{ __('Password') }} " minlength="6" maxlength="50">
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        <span class="text-danger">{{ $errors->first('mother_password') }}</span>
                    </div>
                </div>
            @endif

        @endif
    </div>
</fieldset>

<input type="hidden" id="f_temp_path" name="f_temp_path" value="f_parent_{{time()}}">
<input type="hidden" id="m_temp_path" name="m_temp_path" value="m_parent_{{time()}}">
<input type="hidden" id="is_delete" name="is_delete" value="1">
