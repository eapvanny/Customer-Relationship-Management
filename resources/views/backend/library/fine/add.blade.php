<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Fee @endsection
<!-- End block -->
@section('extraStyle')
    <style>
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
    </style>
@endsection
<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
                {{ __('Library Fine Collection') }}
            <small> {{ __('New') }} </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li> {{ __('Library') }} </li>
            <li><a href="{{URL::route('library.fine.index')}}"> {{ __('Fine') }} </a></li>
            <li class="active"> {{ __('Add') }} </li>
        </ol>
            
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <form novalidate id="entryForm" action="{{URL::Route('library.fine.store')}}" method="post" enctype="multipart/form-data" autocomplete="off">
                        <div class="box-body">
                            @csrf
                            <!-- Organization -->
                            @auth
                            {{-- @if(auth()->user()->newRole->role_id === AppHelper::USER_SUPER_ADMIN) --}}
                            {!! AppHelper::selectOrg($errors, $library->org_id ?? 0,$role_ref_id) !!}
                            {{-- @endif --}}
                            @endauth
                            <!-- End organization -->
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group has-feedback">
                                        <label for="regi_no"> {{ __('Student Regi No.') }} <span class="text-danger">*</span></label>
                                        <input type="hidden" name="regi_id" id="getRegiId" value="">
                                        <input type="text" class="form-control" name="regi_no" id="regi_no" placeholder="registration number" value="{{old('regi_no')}}" required="">
                                        <span class="fa fa-sort-numeric-asc form-control-feedback"></span>
                                        <span class="text-danger"></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group has-feedback">
                                        <label for="isbn_no_code"> {{ __('ISBN no./Code') }} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="isbn_no_code" name="isbn_no_code" placeholder="isbn number/code" value="{{old('isbn_no_code')}}" required="" minlength="1" maxlength="20">
                                            <span class="fa fa-book form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('isbn_no_code') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group has-feedback">
                                        <label for="collection_date"> {{ __('Collection Date') }} <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" readonly="" class="form-control date_picker" name="collection_date" placeholder="date" value="{{old('collection_date')}}" required="" minlength="10" maxlength="10">
                                            <span class="fa fa-calendar form-control-feedback"></span>
                                            <span class="text-danger"></span>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group has-feedback">
                                        <label for="fine_amount"> {{ __('Fine Amount') }} <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="fine_amount" placeholder="150" value="{{old('fine_amount')}}" required="" min="1">
                                        <span class="fa fa-sort-numeric-asc form-control-feedback"></span>
                                        <span class="text-danger"></span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group has-feedback">
                                        <label for="quantity"> {{ __('Quantity') }} <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="quantity" value="{{old('quantity')}}" min="0" max="10" required>
                                            <span class="fa fa-sort-numeric-asc form-control-feedback"></span>
                                        </div>
                                        <span class="text-danger">{{ $errors->first('quantity') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="description"> {{ __('Description') }} </label>
                                        <textarea name="description" class="form-control"></textarea>
                                        <span class="fa fa-info-circle form-control-feedback"></span>
                                        <span class="text-danger"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <a href="{{URL::route('library.fine.index')}}" class="btn btn-default"> {{ __('Cancel') }} </a>
                            <button type="submit" class="btn btn-info pull-right text-white"><i class="fa fa-plus-circle"></i> {{ __('Save') }} </button>
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
                source: "{{ URL::route('ajax.search.registration')}}?org_id=1",
                minLength: 3,
                select: function(event, ui) {
                    console.log(ui.item.length);
                    $("#getRegiId").val(ui.item.id);
                }
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
