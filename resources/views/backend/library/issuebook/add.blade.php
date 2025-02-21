<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Issue Book @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            {{ __('Issue Book') }}
            <small>@if($issuebook) Update @else {{ __('Add New') }} @endif</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li> {{ __('Library') }} </li>
            <li><a href="{{URL::route('library.issuebook.index')}}"> {{ __('Issue Book') }} </a></li>
            <li class="active">@if($issuebook) Update @else {{ __('Add') }} @endif</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
       
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <form novalidate="novalidate" id="entryForm" action=" @if($issuebook) {{URL::Route('library.issuebook.update', $issuebook->id)}} @else {{URL::Route('library.issuebook.store')}} @endif " method="post" enctype="multipart/form-data" autocomplete="off">
                        <div class="box-body">
                            @csrf
                            @if($issuebook)  {{ method_field('PATCH') }} @endif     
                            <!-- Organization -->
                            @auth
                            {{-- @if(auth()->user()->newRole->role_id === AppHelper::USER_SUPER_ADMIN) --}}
                            {!! AppHelper::selectOrg($errors, $issuebook->org_id ?? 0,$role_ref_id) !!}
                            {{-- @endif --}}
                            @endauth
                            <!-- End organization -->                                                
                            <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group has-feedback">
                                            <label for="regi_no"> {{ __('Student Regi No.') }} <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="regi_no" name="regi_no" placeholder="registration number" value="@if($issuebook){{$issuebook->regi_no}}@else{{old('regi_no')}}@endif" required="">
                                                <span class="fa fa-sort-numeric-asc form-control-feedback"></span>
                                            <span class="text-danger">{{ $errors->first('regi_no') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group has-feedback">
                                            <label for="isbn_no_code"> {{ __('ISBN no./Code') }} <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="isbn_no_code" name="isbn_no_code" placeholder="isbn number/code" value="@if($issuebook){{$issuebook->isbn_no_code}}@else{{old('isbn_no_code')}}@endif" required="" minlength="1" maxlength="20">
                                                <span class="fa fa-book form-control-feedback"></span>
                                            <span class="text-danger">{{ $errors->first('isbn_no_code') }}</span>
                                        </div>
                                    </div>
                                    @php
                                    if ($issuebook) {
                                        $is_d = \Carbon\Carbon::createFromFormat('Y-m-d', $issuebook->issue_date);
                                         $re_d = \Carbon\Carbon::createFromFormat('Y-m-d', $issuebook->return_date);
                                    }
                                         
                                    @endphp     
                                    <div class="col-md-2">
                                        <div class="form-group has-feedback">
                                            <label for="issue_date"> {{ __('Issue Date') }} <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" readonly="" class="form-control date_picker" name="issue_date" placeholder="date" value="@if($issuebook){{$is_d->format('d/m/Y')}}@else{{old('issue_date')}}@endif" required="">
                                                <span class="fa fa-calendar form-control-feedback"></span>
                                            </div>
                                            <span class="text-danger">{{ $errors->first('issue_date') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group has-feedback">
                                            <label for="return_date"> {{ __('Return Date') }} <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                
                                                <input type="text" readonly="" class="form-control date_picker" name="return_date" placeholder="date" value="@if($issuebook){{$re_d->format('d/m/Y')}}@else{{old('return_date')}}@endif" required="" >
                                                <span class="fa fa-calendar form-control-feedback"></span>
                                            </div>
                                            <span class="text-danger">{{ $errors->first('return_date') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group has-feedback">
                                            <label for="quantity"> {{ __('Quantity') }} <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="quantity" value="@if($issuebook){{$issuebook->qty}}@else{{old('quantity')}}@endif" min="1" max="10" required>
                                                <span class="fa fa-sort-numeric-asc form-control-feedback"></span>
                                            </div>
                                            <span class="text-danger">{{ $errors->first('quantity') }}</span>
                                        </div>
                                    </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <a href="{{URL::route('library.issuebook.index')}}" class="btn btn-default"> {{ __('Cancel') }} </a>
                            <button type="submit" class="btn btn-info pull-right text-white"><i class="fa @if($issuebook) fa-refresh @else fa-plus-circle @endif"></i> @if($issuebook) Update @else {{ __('Add') }} @endif</button>

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
            $( "#regi_no" ).autocomplete({
                source: "{{ URL::route('ajax.search.registration') }}",
                minLength: 2,
                // select: function(event, ui) {

                // }
            });

            $( "#isbn_no_code" ).autocomplete({
                source: "{{ URL::route('ajax.search.books.isbn') }}",
                minLength: 2,
                // select: function(event, ui) {

                // }
            });
        });
    </script>
@endsection
<!-- END PAGE JS-->
