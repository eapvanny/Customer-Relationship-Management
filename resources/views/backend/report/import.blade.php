@extends('backend.layouts.master')

@section('pageTitle')
    Import Report
@endsection

@section('bodyCssClass')
@endsection

@section('extraStyle')
    <style>
       
    </style>
@endsection


@section('pageContent')
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li><a href="{{ URL::route('report.index') }}"> {{ __('Reports') }} </a></li>
            <li class="active">
                {{ __('Import Report') }}
            </li>
        </ol>
    </section>

    <section class="content">
        <form novalidate id="entryForm"
            action="{{ URL::Route('save.import') }}"
            method="post" enctype="multipart/form-data" autocomplete="off">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="wrap-outter-header-title">
                        <h4>
                            {{ __('Import Report') }}
                        </h4>
                        <div class="box-tools pull-right">
                            <a href="{{ URL::route('report.index') }}" class="btn btn-default">{{__('Cancel')}}</a>
                            <button type="submit" class="submitClick btn btn-info pull-right text-white" onclick="disableButtons(this)"><i
                                    class="fa fa-plus-circle"></i>
                                {{__('Save')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="wrap-outter-box">
                <input id="org_detail" type="hidden" name="org_detail" value="">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group has-feedback">
                                <label for="file">{{ __('Select Excel File') }} <span class="text-danger"> *</span></label>
                                <input type="file" name="file" id="file" class="form-control" required>
                                <span class="form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('file') }}</span>
                            </div>
                        </div>  
                        <div class="col-md-6">
                            <div class="form-group has-feedback">
                                <label for="date">{{ __('Select Date') }} <span class="text-danger"> *</span></label>
                                <input type="date" name="date" id="date" class="form-control" required>
                                <span class="form-control-feedback"></span>
                                <span class="text-danger">{{ $errors->first('date') }}</span>
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
        $(document).ready(function() {
            Generic.initDeleteDialog();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
         function disableButtons(button) {
            button.disabled = true;
            button.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';
            
            // Submit the form
            document.getElementById('entryForm').submit();
        }
    </script>
@endsection
