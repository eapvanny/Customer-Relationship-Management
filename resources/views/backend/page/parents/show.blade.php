@if ($student)
    @if($isAddRegistration)
        @if ($student->parents->count() > 0)
            @php
                $mo = $student->parents->where('type','mother');
                $fa = $student->parents->where('type','father');

            @endphp
            <div class="row">
                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label for="search_father_idcard"> {{ __('Father ID Card No') }}.</label>
                            <input  type="text" class="form-control"  id="search_father_idcard" value="{{isset($fa->first()->id_card) ? $fa->first()->id_card : old('search_father_idcard')}}"  name="search_father_idcard"  placeholder="Father Id Card" readonly>
                            <span class="fa fa-id-card form-control-feedback"></span>
                            <span class="text-danger">{{ $errors->first('search_father_idcard') }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label for="search_mother_idcard"> {{ __('Mother ID Card No') }}.</label>
                            <input  type="text" class="form-control"  id="search_mother_idcard" value="{{isset($mo->first()->id_card) ? $mo->first()->id_card : old('search_mother_idcard')}}"  name="search_mother_idcard"  placeholder="Mother Id Card" readonly>
                            <span class="fa fa-id-card form-control-feedback"></span>
                            <span class="text-danger">{{ $errors->first('search_mother_idcard') }}</span>

                        </div>
                    </div>
            </div>
            <div class="row">
                <div class="col-md-6 diplay-father-name">
                    <div class="form-group has-feedback">
                        <label for="show_father_name">Father Name.</label>
                        <input  type="text" class="form-control" value="{{isset($fa->first()->name) ? $fa->first()->name : old('show_father_name')}}"  id="show_father_name"  name="show_father_name"  placeholder="Father Name" readonly>
                        <span class="fa fa-info form-control-feedback"></span>

                    </div>
                </div>
                <div class="col-md-6 diplay-mother-name">
                    <div class="form-group has-feedback">
                        <label for="show_mother_name">Mother Name.</label>
                        <input  type="text" class="form-control" value="{{isset($mo->first()->name) ? $mo->first()->name : old('show_mother_name')}}" id="show_mother_name"  name="show_mother_name"  placeholder="Mother Name" readonly>
                        <span class="fa fa-info form-control-feedback"></span>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group has-feedback">
                        <label for="show_father_email">Father Email.</label>
                        <input  type="email" class="form-control" name="show_father_email" id="show_father_email" value="{{isset($fa->first()->email) ? $fa->first()->email : old('show_father_email')}}" placeholder="Email" readonly>
                        <span class="fa fa-envelope form-control-feedback"></span>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group has-feedback">
                        <label for="show_mother_email">Mother Email.</label>
                        <input  type="email" class="form-control" name="show_mother_email" value="{{isset($mo->first()->email) ? $mo->first()->email : old('show_mother_email')}}" id="show_mother_email" placeholder="Email" readonly>
                        <span class="fa fa-envelope form-control-feedback"></span>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group has-feedback">
                        <label for="show_father_phone_no">Father Phone/Mobile No.</label>
                        <input  type="text" class="form-control" name="show_father_phone_no" value="{{isset($fa->first()->phone_no) ? $fa->first()->phone_no : old('show_father_phone_no')}}" id="show_father_phone_no" placeholder="phone or mobile number" readonly>
                        <span class="fa fa-phone form-control-feedback"></span>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group has-feedback">
                        <label for="show_mother_phone_no">Mother Phone/Mobile No.</label>
                        <input  type="text" class="form-control"  id="show_mother_phone_no" value="{{isset($mo->first()->phone_no) ? $mo->first()->phone_no : old('show_mother_phone_no')}}" name="show_mother_phone_no"  placeholder="phone or mobile number" readonly>
                        <span class="fa fa-phone form-control-feedback"></span>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group has-feedback">
                        <label for="show_present_address">Permanent Address</label>
                        <textarea name="show_father_address" id="show_father_address" class="form-control"  maxlength="500" readonly>{{isset($fa->first()->address) ? $fa->first()->address : old('show_present_address')}}</textarea>
                        <span class="fa fa-location-arrow form-control-feedback"></span>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group has-feedback">
                        <label for="show_present_address">Permanent Address</label>
                        <textarea name="show_mother_address" id="show_mother_address" class="form-control"  maxlength="500" readonly>{{isset($mo->first()->address) ? $mo->first()->address : old('show_present_address')}}</textarea>
                        <span class="fa fa-location-arrow form-control-feedback"></span>

                    </div>
                </div>
            </div>
            {{--<div class="row">--}}
                    {{--<div class="col-md-6">--}}
                            {{--<div class="form-group has-feedback">--}}
                                {{--<label for="photo">Father photo<span class="text-danger">[min 150 X 150 size and max 200kb]</span></label>--}}
                                {{--<input type="file" class="form-control" id="show_father_file" accept=".jpeg, .jpg, .png" name="show_father_file" readonly>--}}
                                {{--<span class="glyphicon glyphicon-open-file form-control-feedback"></span>--}}
                                {{--<img src="{{ isset($student->parents[0]['father_photo']) ? asset('storage/parents').'/'.$student->parents[0]['father_photo'] : ''}}" width="100px" id="display_images_father_photo" alt="" srcset="">--}}
                                {{----}}
                                {{--<span class="text-danger"></span>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="col-md-6">--}}
                            {{--<div class="form-group has-feedback">--}}
                                {{--<label for="photo">Mother photo<span class="text-danger">[min 150 X 150 size and max 200kb]</span></label>--}}
                                {{--<input type="file" class="form-control" id="show_mother_file" accept=".jpeg, .jpg, .png" name="show_mother_file" readonly>--}}
                                {{--<span class="glyphicon glyphicon-open-file form-control-feedback"></span>--}}
                                {{--<img src="{{ isset($student->parents[0]['mother_photo']) ? asset('storage/parents').'/'.$student->parents[0]['mother_photo'] : ''}}" id="display_images_mother_photo" width="100px" alt="" srcset="">--}}
                                {{--<span class="text-danger"></span>--}}
                            {{--</div>--}}
                        {{--</div>--}}
            {{--</div>--}}

        @else

            <div class="row">
                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label for="search_father_idcard">Father ID Card No.</label>
                            <input  type="text" class="form-control"  id="search_father_idcard" value="{{old('search_father_idcard')}}"  name="search_father_idcard"  placeholder="{{ __('Father Id Card') }}" readonly>
                            <span class="fa fa-id-card form-control-feedback"></span>
                            <span class="text-danger">{{ $errors->first('search_father_idcard') }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label for="search_mother_idcard">Mother ID Card No.</label>
                            <input  type="text" class="form-control"  id="search_mother_idcard" value="{{old('search_mother_idcard')}}"  name="search_mother_idcard"  placeholder="{{ __('Mother Id Card') }}" readonly>
                            <span class="fa fa-id-card form-control-feedback"></span>
                            <span class="text-danger">{{ $errors->first('search_mother_idcard') }}</span>

                        </div>
                    </div>
            </div>
            <div class="row">
                <div class="col-md-6 diplay-father-name">
                    <div class="form-group has-feedback">
                        <label for="show_father_name"> {{ __('Father Name') }}.</label>
                        <input  type="text" class="form-control" value=""  id="show_father_name"  name="show_father_name" value="{{old('show_father_name')}}"  placeholder=" {{ __('Father Name') }}" readonly>
                        <span class="fa fa-info form-control-feedback"></span>

                    </div>
                </div>
                <div class="col-md-6 diplay-mother-name">
                    <div class="form-group has-feedback">
                        <label for="show_mother_name"> {{ __('Mother Name') }}.</label>
                        <input  type="text" class="form-control" value="{{old('show_mother_name')}}" id="show_mother_name"  name="show_mother_name"  placeholder=" {{ __('Mother Name') }}"  readonly>
                        <span class="fa fa-info form-control-feedback"></span>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group has-feedback">
                        <label for="father_email"> {{ __('Father Email') }}.</label>
                        <input  type="email" class="form-control" name="show_father_email" id="show_father_email" value="{{old('show_father_email')}}" placeholder="{{ __('Email') }}" readonly>
                        <span class="fa fa-envelope form-control-feedback"></span>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group has-feedback">
                        <label for="mother_email"> {{ __('Mother Email') }}.</label>
                        <input  type="email" class="form-control" name="show_mother_email" value="{{old('show_mother_email')}}" id="show_mother_email" placeholder="{{ __('Email') }}" readonly >
                        <span class="fa fa-envelope form-control-feedback"></span>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group has-feedback">
                        <label for="show_father_phone_no"> {{ __('Father Phone/Mobile No') }}.</label>
                        <input  type="text" class="form-control" name="show_father_phone_no" value="{{old('show_father_phone_no')}}" id="show_father_phone_no" placeholder="{{ __('phone or mobile number') }}" readonly>
                        <span class="fa fa-phone form-control-feedback"></span>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group has-feedback">
                        <label for="mother_phone_no"> {{ __('Mother Phone/Mobile No') }}.</label>
                        <input  type="text" class="form-control"  id="show_mother_phone_no" value="{{old('show_mother_phone_no')}}" name="show_mother_phone_no"  placeholder="{{ __('phone or mobile number') }}" readonly>
                        <span class="fa fa-phone form-control-feedback"></span>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group has-feedback">
                        <label for="present_address"> {{ __('Permanent Address') }} </label>
                        <textarea name="show_father_address" id="show_father_address" class="form-control"  maxlength="500" readonly>{{old('present_address')}}</textarea>
                        <span class="fa fa-location-arrow form-control-feedback"></span>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group has-feedback">
                        <label for="present_address"> {{ __('Permanent Address') }} </label>
                        <textarea name="show_mother_address" id="show_mother_address" class="form-control"  maxlength="500" readonly>{{old('present_address')}}</textarea>
                        <span class="fa fa-location-arrow form-control-feedback"></span>

                    </div>
                </div>
            </div>
            {{--<div class="row">--}}
                    {{--<div class="col-md-6">--}}
                            {{--<div class="form-group has-feedback">--}}
                                {{--<label for="photo">Father photo<span class="text-danger">[min 150 X 150 size and max 200kb]</span></label>--}}
                                {{--<input type="file" class="form-control" id="show_father_file" accept=".jpeg, .jpg, .png" name="show_father_file" disabled>--}}
                                {{--<span class="glyphicon glyphicon-open-file form-control-feedback"></span>--}}
                                {{--<img src="" width="100px" id="display_images_father_photo" alt="" srcset="">--}}
                                {{----}}
                                {{--<span class="text-danger"></span>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="col-md-6">--}}
                            {{--<div class="form-group has-feedback">--}}
                                {{--<label for="photo">Mother photo<span class="text-danger">[min 150 X 150 size and max 200kb]</span></label>--}}
                                {{--<input type="file" class="form-control" id="show_mother_file" accept=".jpeg, .jpg, .png" name="show_mother_file" disabled>--}}
                                {{--<span class="glyphicon glyphicon-open-file form-control-feedback"></span>--}}
                                {{--<img src="" id="display_images_mother_photo" width="100px" alt="" srcset="">--}}
                                {{--<span class="text-danger"></span>--}}
                            {{--</div>--}}
                        {{--</div>--}}
            {{--</div> --}}

        @endif

    @else
        @if ($student->parents->count() > 0)
            @php
                $mo = $student->parents->where('type','mother');
                $fa = $student->parents->where('type','father');

            @endphp
            <div class="row">
                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label for="search_father_idcard"> {{ __('Father ID Card No') }}.</label>
                            <input  type="text" class="form-control"  id="search_father_idcard" value="{{isset($fa->first()->id_card) ? $fa->first()->id_card : old('search_father_idcard')}}"  name="search_father_idcard"  placeholder="{{ __('Father Id Card') }}">
                            <span class="fa fa-id-card form-control-feedback"></span>
                            <span class="text-danger">{{ $errors->first('search_father_idcard') }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label for="search_mother_idcard"> {{ __('Mother ID Card No') }}.</label>
                            <input  type="text" class="form-control"  id="search_mother_idcard" value="{{isset($mo->first()->id_card) ? $mo->first()->id_card : old('search_mother_idcard')}}"  name="search_mother_idcard"  placeholder="{{ __('Mother Id Card') }}">
                            <span class="fa fa-id-card form-control-feedback"></span>
                            <span class="text-danger">{{ $errors->first('search_mother_idcard') }}</span>

                        </div>
                    </div>
            </div>
            <div class="row">
                <div class="col-md-6 diplay-father-name">
                    <div class="form-group has-feedback">
                        <label for="show_father_name"> {{ __('Father Name') }}.</label>
                        <input  type="text" class="form-control" value="{{isset($fa->first()->name) ? $fa->first()->name : old('show_father_name')}}"  id="show_father_name"  name="show_father_name"  placeholder=" {{ __('Father Name') }}" readonly>
                        <span class="fa fa-info form-control-feedback"></span>

                    </div>
                </div>
                <div class="col-md-6 diplay-mother-name">
                    <div class="form-group has-feedback">
                        <label for="show_mother_name"> {{ __('Mother Name') }}.</label>
                        <input  type="text" class="form-control" value="{{isset($mo->first()->name) ? $mo->first()->name : old('show_mother_name')}}" id="show_mother_name"  name="show_mother_name"  placeholder=" {{ __('Mother Name') }}" readonly>
                        <span class="fa fa-info form-control-feedback"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group has-feedback">
                        <label for="show_father_email"> {{ __('Father Email') }}.</label>
                        <input  type="email" class="form-control" name="show_father_email" id="show_father_email" value="{{isset($fa->first()->email) ? $fa->first()->email : old('show_father_email')}}" placeholder="{{ __('Email') }}" readonly>
                        <span class="fa fa-envelope form-control-feedback"></span>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group has-feedback">
                        <label for="show_mother_email"> {{ __('Mother Email') }}.</label>
                        <input  type="email" class="form-control" name="show_mother_email" value="{{isset($mo->first()->email) ? $mo->first()->email : old('show_mother_email')}}" id="show_mother_email" placeholder="{{ __('Email') }}" readonly>
                        <span class="fa fa-envelope form-control-feedback"></span>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group has-feedback">
                        <label for="show_father_phone_no"> {{ __('Father Phone/Mobile No') }}.</label>
                        <input  type="text" class="form-control" name="show_father_phone_no" value="{{isset($fa->first()->phone_no) ? $fa->first()->phone_no : old('show_father_phone_no')}}" id="show_father_phone_no" placeholder=" {{ __('phone or mobile number') }}" readonly>
                        <span class="fa fa-phone form-control-feedback"></span>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group has-feedback">
                        <label for="show_mother_phone_no"> {{ __('Mother Phone/Mobile No') }}.</label>
                        <input  type="text" class="form-control"  id="show_mother_phone_no" value="{{isset($mo->first()->phone_no) ? $mo->first()->phone_no : old('show_mother_phone_no')}}" name="show_mother_phone_no"  placeholder=" {{ __('phone or mobile number') }}" readonly>
                        <span class="fa fa-phone form-control-feedback"></span>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group has-feedback">
                        <label for="show_present_address"> {{ __('Permanent Address') }} </label>
                        <textarea name="show_father_address" id="show_father_address" class="form-control"  maxlength="500" readonly>{{isset($fa->first()->address) ? $fa->first()->address : old('show_father_address')}}</textarea>
                        <span class="fa fa-location-arrow form-control-feedback"></span>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group has-feedback">
                        <label for="show_present_address"> {{ __('Permanent Address') }} </label>
                        <textarea name="show_mother_address" id="show_mother_address" class="form-control"  maxlength="500" readonly>{{isset($mo->first()->address) ? $mo->first()->address : old('show_mother_address')}}</textarea>
                        <span class="fa fa-location-arrow form-control-feedback"></span>

                    </div>
                </div>
            </div>
            {{--<div class="row">--}}
                    {{--<div class="col-md-6">--}}
                            {{--<div class="form-group has-feedback">--}}
                                {{--<label for="photo">Father photo<span class="text-danger">[min 150 X 150 size and max 200kb]</span></label>--}}
                                {{--<input type="file" class="form-control" id="show_father_file" accept=".jpeg, .jpg, .png" name="show_father_file" readonly>--}}
                                {{--<span class="glyphicon glyphicon-open-file form-control-feedback"></span>--}}
                                {{--<img src="{{ isset($student->parents[0]['father_photo']) ? asset('storage/parents').'/'.$student->parents[0]['father_photo'] : ''}}" width="100px" id="display_images_father_photo" alt="" srcset="">--}}
                                {{----}}
                                {{--<span class="text-danger"></span>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="col-md-6">--}}
                            {{--<div class="form-group has-feedback">--}}
                                {{--<label for="photo">Mother photo<span class="text-danger">[min 150 X 150 size and max 200kb]</span></label>--}}
                                {{--<input type="file" class="form-control" id="show_mother_file" accept=".jpeg, .jpg, .png" name="show_mother_file" readonly>--}}
                                {{--<span class="glyphicon glyphicon-open-file form-control-feedback"></span>--}}
                                {{--<img src="{{ isset($student->parents[0]['mother_photo']) ? asset('storage/parents').'/'.$student->parents[0]['mother_photo'] : ''}}" id="display_images_mother_photo" width="100px" alt="" srcset="">--}}
                                {{--<span class="text-danger"></span>--}}
                            {{--</div>--}}
                        {{--</div>--}}
            {{--</div>--}}

        @else

            <div class="row">
                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label for="search_father_idcard"> {{ __('Father ID Card No') }}.</label>
                            <input  type="text" class="form-control"  id="search_father_idcard" value="{{old('search_father_idcard')}}"  name="search_father_idcard"  placeholder=" {{ __('Father Id Card') }}">
                            <span class="fa fa-id-card form-control-feedback"></span>
                            <span class="text-danger">{{ $errors->first('search_father_idcard') }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label for="search_mother_idcard"> {{ __('Mother ID Card No') }}.</label>
                            <input  type="text" class="form-control"  id="search_mother_idcard" value="{{old('search_mother_idcard')}}"  name="search_mother_idcard"  placeholder=" {{ __('Mother Id Card') }}">
                            <span class="fa fa-id-card form-control-feedback"></span>
                            <span class="text-danger">{{ $errors->first('search_mother_idcard') }}</span>

                        </div>
                    </div>
            </div>
            <div class="row">
                <div class="col-md-6 diplay-father-name">
                    <div class="form-group has-feedback">
                        <label for="show_father_name"> {{ __('Father Name') }}.</label>
                        <input  type="text" class="form-control" value=""  id="show_father_name"  name="show_father_name" value="{{old('show_father_name')}}"  placeholder=" {{ __('Father Name') }}" readonly>
                        <span class="fa fa-info form-control-feedback"></span>

                    </div>
                </div>
                <div class="col-md-6 diplay-mother-name">
                    <div class="form-group has-feedback">
                        <label for="show_mother_name"> {{ __('Mother Name') }}.</label>
                        <input  type="text" class="form-control" value="{{old('show_mother_name')}}" id="show_mother_name"  name="show_mother_name"  placeholder=" {{ __('Mother Name') }}"  readonly>
                        <span class="fa fa-info form-control-feedback"></span>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group has-feedback">
                        <label for="father_email"> {{ __('Father Email') }}.</label>
                        <input  type="email" class="form-control" name="show_father_email" id="show_father_email" value="{{old('show_father_email')}}" placeholder=" {{ __('Email') }}" readonly>
                        <span class="fa fa-envelope form-control-feedback"></span>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group has-feedback">
                        <label for="mother_email"> {{ __('Mother Email') }}.</label>
                        <input  type="email" class="form-control" name="show_mother_email" value="{{old('show_mother_email')}}" id="show_mother_email" placeholder=" {{ __('Email') }}" readonly >
                        <span class="fa fa-envelope form-control-feedback"></span>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group has-feedback">
                        <label for="show_father_phone_no"> {{ __('Father Phone/Mobile No') }}.</label>
                        <input  type="text" class="form-control" name="show_father_phone_no" value="{{old('show_father_phone_no')}}" id="show_father_phone_no" placeholder=" {{ __('phone or mobile number') }}" readonly>
                        <span class="fa fa-phone form-control-feedback"></span>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group has-feedback">
                        <label for="mother_phone_no"> {{ __('Mother Phone/Mobile No') }}.</label>
                        <input  type="text" class="form-control"  id="show_mother_phone_no" value="{{old('show_mother_phone_no')}}" name="show_mother_phone_no"  placeholder=" {{ __('phone or mobile number') }}" readonly>
                        <span class="fa fa-phone form-control-feedback"></span>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group has-feedback">
                        <label for="present_address"> {{ __('Permanent Address') }} </label>
                        <textarea name="show_father_address" id="show_father_address" class="form-control"  maxlength="500" readonly>{{old('present_address')}}</textarea>
                        <span class="fa fa-location-arrow form-control-feedback"></span>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group has-feedback">
                        <label for="present_address"> {{ __('Permanent Address') }} </label>
                        <textarea name="show_mother_address" id="show_mother_address" class="form-control"  maxlength="500" readonly>{{old('present_address')}}</textarea>
                        <span class="fa fa-location-arrow form-control-feedback"></span>

                    </div>
                </div>
            </div>

        @endif
    @endif
@else
    <div class="row">
            <div class="col-md-6">
                <div class="form-group has-feedback">
                    <label for="search_father_idcard"> {{ __('Father ID Card No') }}.</label>
                    <input  type="text" class="form-control"  id="search_father_idcard" value="{{old('search_father_idcard')}}"  name="search_father_idcard"  placeholder=" {{ __('Father Id Card') }}">
                    <span class="fa fa-id-card form-control-feedback"></span>
                    <span class="text-danger">{{ $errors->first('search_father_idcard') }}</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group has-feedback">
                    <label for="search_mother_idcard"> {{ __('Mother ID Card No') }}.</label>
                    <input  type="text" class="form-control"  id="search_mother_idcard" value="{{old('search_mother_idcard')}}"  name="search_mother_idcard"  placeholder=" {{ __('Mother Id Card') }}">
                    <span class="fa fa-id-card form-control-feedback"></span>
                    <span class="text-danger">{{ $errors->first('search_mother_idcard') }}</span>

                </div>
            </div>
    </div>
    <div class="row">
        <div class="col-md-6 diplay-father-name">
            <div class="form-group has-feedback">
                <label for="show_father_name"> {{ __('Father Name') }}.</label>
                <input  type="text" class="form-control" id="show_father_name"  name="show_father_name" value="{{old('show_father_name')}}"  placeholder=" {{ __('Father Name') }}" readonly>
                <span class="fa fa-info form-control-feedback"></span>

            </div>
        </div>
        <div class="col-md-6 diplay-mother-name">
            <div class="form-group has-feedback">
                <label for="show_mother_name"> {{ __('Mother Name') }}.</label>
                <input  type="text" class="form-control" value="{{old('show_mother_name')}}" id="show_mother_name"  name="show_mother_name"  placeholder=" {{ __('Mother Name') }}"  readonly>
                <span class="fa fa-info form-control-feedback"></span>

            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group has-feedback">
                <label for="father_email"> {{ __('Father Email') }}.</label>
                <input  type="email" class="form-control" name="show_father_email" id="show_father_email" value="{{old('show_father_email')}}" placeholder=" {{ __('Email') }}" readonly>
                <span class="fa fa-envelope form-control-feedback"></span>

            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group has-feedback">
                <label for="mother_email"> {{ __('Mother Email') }}.</label>
                <input  type="email" class="form-control" name="show_mother_email" value="{{old('show_mother_email')}}" id="show_mother_email" placeholder=" {{ __('Email') }}" readonly >
                <span class="fa fa-envelope form-control-feedback"></span>

            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group has-feedback">
                <label for="show_father_phone_no"> {{ __('Father Phone/Mobile No') }}.</label>
                <input  type="text" class="form-control" name="show_father_phone_no" value="{{old('show_father_phone_no')}}" id="show_father_phone_no" placeholder=" {{ __('phone or mobile number') }}" readonly>
                <span class="fa fa-phone form-control-feedback"></span>

            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group has-feedback">
                <label for="mother_phone_no"> {{ __('Mother Phone/Mobile No') }}.</label>
                <input  type="text" class="form-control"  id="show_mother_phone_no" value="{{old('show_mother_phone_no')}}" name="show_mother_phone_no"  placeholder=" {{ __('phone or mobile number') }}" readonly>
                <span class="fa fa-phone form-control-feedback"></span>

            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group has-feedback">
                <label for="present_address"> {{ __('Permanent Address') }} </label>
                <textarea name="show_father_address" id="show_father_address" class="form-control"  maxlength="500" readonly>{{old('present_address')}}</textarea>
                <span class="fa fa-location-arrow form-control-feedback"></span>

            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group has-feedback">
                <label for="present_address"> {{ __('Permanent Address') }} </label>
                <textarea name="show_mother_address" id="show_mother_address" class="form-control"  maxlength="500" readonly>{{old('present_address')}}</textarea>
                <span class="fa fa-location-arrow form-control-feedback"></span>

            </div>
        </div>
    </div>

@endif

