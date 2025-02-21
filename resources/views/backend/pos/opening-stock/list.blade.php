@php

@endphp

<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') POS @endsection
<!-- Page name -->
@section('pageName') POS @endsection
<!-- End block -->
@section('extraStyle')
<style>
    img{
    max-height:500px;
    max-width:500px;
  }
  input[type="number"]::-webkit-outer-spin-button, input[type="number"]::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }

    input[type="number"] {
        -moz-appearance: textfield;
    }
</style>
@endsection
<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            {{ __('Opening Stock') }}
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li> {{ __('POS') }} </li>
            <li class="active"> {{ __('Opening Stock') }} </li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <form novalidate="novalidate" id="entryForm" action=" " method="post" enctype="multipart/form-data" autocomplete="off">
                        <div class="box-body">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group has-feedback">
                                        <label for="name"> {{ __('Search item') }} </label>
                                        <input autofocus type="text" class="form-control" name="search_item" placeholder="Search item,menu,category,option" value="" id="search_item">
                                        <span class="fa fa-info form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('search_item') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group has-feedback">
                                        <div class="input-group">
                                        <input type="text" readonly="" class="form-control" id="filter_date" name="filter_date" placeholder="date" value="" required="" minlength="10" maxlength="11">
                                            <span class="fa fa-calendar form-control-feedback"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <div class="table-responsive">
                            <table id="datatabble" class="table table-bordered table-striped list_view_table display responsive no-wrap datatable-server" width="100%">
                                <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%"> {{ __('Organization') }} </th>
                                    <th width="10%"> {{ __('Item Name') }} </th>
                                    <th width="10%"> {{ __('Option') }} </th>
                                    <th width="10%"> {{ __('Quantity') }} </th>
                                    <th width="10%"> {{ __('Unit Cost') }} </th>
                                    <th width="10%"> {{ __('Total Cost') }} </th>
                                    <th width="10%"> {{ __('Created By') }} </th>

                                </tr>
                                </thead>
                                <tbody>

                                </tbody>

                            </table>
                        </div>
                   </div>
                </form>
                </div>
            </div>
         </div>
     </section>
<!-- /.content -->

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel"> {{ __('Import Quantity') }} </h5>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="name" id="item_name"> {{ __('Name') }} </label>
                <input type="number" class="form-control" id="quantity" required placeholder="Quantity" value="">
            </div>
            <div class="form-group">
                <label for="name"> {{ __('Unit Cost') }} </label>
                <input type="number" class="form-control" id="unitCost" placeholder="Unit Cost" value="">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" id="btnCloseQty" data-dismiss="modal"> {{ __('Close') }} </button>
          <button type="button" class="btn btn-primary" id="btnSaveQty"> {{ __('Save') }} </button>
        </div>
      </div>
    </div>
  </div>

@endsection
<!-- END PAGE CONTENT-->

<!-- BEGIN PAGE JS-->
@section('extraScript')
<script>
    var jsonBody = {};
</script>
<script type="text/javascript">
$(document).ready(function () {
    Generic.initCommonPageJS();
    Generic.initDeleteDialog();

    $("#filter_date").datetimepicker({
        format: "YYYY-MM-DD",
        viewMode: 'days',
        ignoreReadonly: true,
        defaultDate: new Date()
    }).on('dp.change', function (e) {
        let formatedValue = e.date.format(e.date._f);
        let Url = '{!! route('pos.opening-stock.index') !!}?s_date='+formatedValue;
        $('#datatabble').DataTable().ajax.url(Url).load();

     });
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    // Disable scroll when focused on a number input.
    $('html').on('focus', 'input[type=number]', function(e) {
        $(this).on('wheel', function(e) {
            e.preventDefault();
        });
    });

    // Restore scroll on number inputs.
    $('html').on('blur', 'input[type=number]', function(e) {
        $(this).off('wheel');
    });

    // Disable up and down keys.
    $('html').on('keydown', 'input[type=number]', function(e) {
        if ( e.which == 38 || e.which == 40 )
            e.preventDefault();
    });

    $( "#search_item" ).autocomplete({
        source: "{{ URL::route('pos.opening-stock.index') }}?auto_complete",
        minLength: 2,
        select: function(event, ui) {
           jsonBody = {
               "item_id":ui.item.id,
               "option_id":ui.item.option_id,
               "org_id": ui.item.org_id,
           };
           $("#item_name").html(ui.item.label+"<span class=text-danger>*</span>");
           $("#btnSaveQty").attr("disabled", false);
           $("#btnCloseQty").attr("disabled", false);
           $('#exampleModal').modal('show');
        },


    }).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
           return $( "<li></li>" )
               .data( "item.autocomplete", item )
               .append( "<a>" + "<img style='width:50px;height:50px' src='" + item.img + "' /> " + item.value+ "</a>" )
               .appendTo( ul );
     };

    var t = $('#datatabble').DataTable({
            processing: false,
            serverSide: true,
            ajax:{
                url: "{!!  route('pos.opening-stock.index') !!}",
            },
            columns:[
                {
                    data: 'id',
                    name: 'id',
                },
                {
                    data: 'org_id',
                    name: 'org_id'
                },
                {
                    data: 'item_id',
                    name: 'item_id'
                },
                {
                    data: 'option_id',
                    name: 'option_id'
                },
                {
                    data: 'qty',
                    name: 'qty'
                },
                {
                    data: 'unit_cost',
                    name: 'unit_cost'
                },
                {
                    data: 'total_cost',
                    name: 'total_cost'
                },
                {
                    data: 'created_by',
                    name: 'created_by'
                }
            ]
    });

    $("#btnSaveQty").on("click",function(e){
        e.preventDefault();
        jsonBody['qty'] = $("#quantity").val();
        jsonBody['unit_cost'] = $("#unitCost").val();
        $("#btnSaveQty").attr("disabled", true);
        $("#btnCloseQty").attr("disabled", true);
        Generic.loaderStart();
        $.ajax({
            type:'POST',
            url : '{!! route('pos.opening-stock.store') !!}',
            data:jsonBody,
            success:function(data) {
                if (data.success){
                    $('#exampleModal').modal('hide');
                    $('#filter_date').val(data.date);
                    toastr.success(data.message);
                    $('#datatabble').DataTable().ajax.url(data.Url).load();
                    Generic.loaderStop();
                }else {
                    $.each(data.errors, function(key,val) {
                        toastr.error(val);
                    });
                }

            }
        });
    });

    $('#exampleModal').on('hidden.bs.modal', function (e) {
        jsonBody ={};
        $("#item_name").text('');
        $("#quantity").val('');
        $("#unitCost").val('');
        $("#search_item").val('');
    });

});
</script>
@endsection
<!-- END PAGE JS-->
