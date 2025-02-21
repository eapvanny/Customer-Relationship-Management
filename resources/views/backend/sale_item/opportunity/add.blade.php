<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Opportunity @endsection
<!-- End block -->
@section('extraStyle')

@endsection
<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            {{ __('Opportunity') }}
            <small>@if($opportunity) Update @else {{ __('Add New') }} @endif</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li> {{ __('Sale Item') }} </li>
            <li><a href="{{URL::route('saleitem.opportunity.index')}}"> {{ __('Opportunity') }} </a></li>
            <li class="active">@if($opportunity) Update @else Add @endif</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">

        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <form novalidate="novalidate" id="entryForm" action=" @if($opportunity) {{URL::Route('saleitem.opportunity.update', $opportunity->id)}} @else {{URL::Route('saleitem.opportunity.store')}} @endif " method="post" enctype="multipart/form-data" autocomplete="off">
                        <div class="box-body">
                            @csrf
                            @if($opportunity)  {{ method_field('PATCH') }} @endif
                            <!-- Organization -->
                            @auth
                            {{-- @if(auth()->user()->newRole->role_id === AppHelper::USER_SUPER_ADMIN) --}}
                            @if(!$opportunity)
                             {!! AppHelper::selectOrg($errors, $opportunity->org_id ?? 0,$role_ref_id) !!}
                            @else
                                {!! AppHelper::selectOrg($errors, $opportunity->org_id ?? 0,$role_ref_id,array('readonly'=>true)) !!}
                            @endif
                            {{-- @endif --}}
                            @endauth
                            <!-- End organization -->
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group has-feedback">
                                        <label for="title"> {{ __('Name') }} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Name" value="@if($opportunity){{$opportunity->name}}@else{{old('name')}}@endif" required="">
                                            <span class="fa fa-info form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('name') }}</span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group has-feedback">
                                        <label for="is_closed"> {{ __('Is Closed') }}
                                        </label>

                                        {!! Form::select('is_closed', \AppHelper::IS_CLOSED, $opportunity ? $opportunity->is_closed : 0 , ['class' => 'form-control select2']) !!}
                                        <span class="form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('is_closed') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group has-feedback">
                                        <label for="section_id"> {{ __('Is Won') }}
                                        </label>

                                        {!! Form::select('is_won', \AppHelper::IS_WON, $opportunity ? $opportunity->is_won : 0  , ['class' => 'form-control select2']) !!}
                                        <span class="form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('is_won') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group has-feedback">
                                        <label for="type"> {{ __('Type') }}
                                            <span class="text-danger">*</span>
                                        </label>

                                        {!! Form::select('type', \AppHelper::OPPORTUNITY_TYPE, $opportunity ? $opportunity->type : 0  , ['placeholder'=>'Pick a Type...','class' => 'form-control select2','required'=>'true']) !!}
                                        <span class="form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('type') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="description"> {{ __('Description') }} <span class="text-danger"></span></label>
                                        <textarea name="description" class="form-control" maxlength="500" >@if($opportunity){{ $opportunity->description }}@else{{ old('description') }} @endif</textarea>
                                        <span class="fa fa-location-arrow form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('description') }}</span>
                                    </div>
                                </div>

                                @php
                                    if ($opportunity) {
                                        $close_date=null;
                                        if($opportunity->close_date)
                                            $close_date = \Carbon\Carbon::createFromFormat('Y-m-d', $opportunity->close_date);
                                    }
                                @endphp
                                <div class="col-md-3">
                                    <div class="form-group has-feedback">
                                        <label for="start_date"> {{ __('Close Date') }} </label>
                                        <div class="input-group">
                                            <input type="text" readonly="" class="form-control date_picker" name="close_date" placeholder="Close Date" value="@if($opportunity && $close_date){{$close_date->format('d/m/Y')}}@else{{old('close_date')}}@endif">
                                            <span class="fa fa-calendar form-control-feedback"></span>
                                        </div>
                                        <span class="text-danger">{{ $errors->first('close_date') }}</span>
                                    </div>

                                </div>
                                <div class="col-md-3">
                                    <div class="form-group has-feedback">
                                        <label for="currency"> {{ __('Currency') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        {!! Form::select('currency', getCurrency(), $opportunity ? $opportunity->currency : 'USD'  , ['class' => 'form-control select2','required'=>'true','readonly'=>true]) !!}
                                        <span class="form-control-feedback"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-3">
                                    <div class="form-group has-feedback">
                                        <label for="buyer_type"> {{ __('Buyer Type') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        {!! Form::select('buyer_type', \AppHelper::BUYER_TYPE, $opportunity ? $buyer_type : 0  , ['placeholder'=>'Pick a Buyer Type...','class' => 'form-control select2','required'=>'true']) !!}
                                        <span class="form-control-feedback"></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group has-feedback">
                                        <label for="buyer_id"> {{ __('Choose Buyer') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        {!! Form::select('buyer_id', $opportunity ? $buyer : [], $opportunity ? $buyer_selected : null , ['placeholder'=>'Pick a Buyer...','class' => 'form-control select2','required'=>'true']) !!}
                                        <span class="form-control-feedback"></span>
                                    </div>
                                </div>
                            </div>
                                <div class="col-md-12">
                                    <div class="panel panel-primary">
                                        <div class="panel-heading"> {{ __('Product Items') }} </div>
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group has-feedback">
                                                        <label class="my-1 mr-2"> {{ __('Product') }} </label>
                                                        {!! Form::select('product_name', $products, null, ['placeholder'=>'---Choose Product---','class' => 'form-control select2']) !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group has-feedback">
                                                        <label class="my-1 mr-2"> {{ __('Quantity') }} </label>
                                                        <input name="qty" class="form-control" type="number" min="1" value="1" placeholder="Quantity">
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <a style="margin-top: 22px;" id="btn_add_product" class="form-control btn btn-primary"><i class="fa fa-plus"></i> &nbsp;&nbsp;&nbsp; {{ __('Add') }} </a>
                                                </div>
                                            </div>

                                            <div class="row" style="padding-top : 10px;">
                                                {{-- <input type="hidden" id="strDetail" @if($user_org) value="{{json_encode($user_org)}}" @else value="{{json_encode([])}}"  @endif> --}}
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table id="table-product" class="table table-bordered table-striped list_view_table display responsive no-wrap" width="100%">
                                                            <thead>
                                                            <tr>
                                                                <th width="5%">#</th>
                                                                <th width="30%"> {{ __('Name') }} </th>
                                                                <th width="10%"> {{ __('Quantity') }} </th>
                                                                <th width="25%"> {{ __('Unit Cost') }}($)</th>
                                                                <th width="10%"> {{ __('Discount') }}(%)</th>
                                                                <th width="25%"> {{ __('Line Total') }}($)</th>
                                                                <th class="notexport" width="10%"> {{ __('Action') }} </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                                @include('backend.inc.list-product-render-ajax')
                                                            </tbody>

                                                        </table>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <hr>
                                                    <div class="card" style="width: 18rem;">
                                                        <div class="card-header">
                                                            {{ __('Summary') }}
                                                        </div>
                                                        <ul class="list-group list-group-flush">
                                                            <li class="list-group-item" id="total_qty"> {{ __('Total Quantity') }} : {{$opportunity ? $opportunity->quantity : 0}}</li>
                                                            <li class="list-group-item" id="total_price"> {{ __('Total Price') }} :  {{$opportunity ? money($opportunity->amount, 'USD',true) : 0}}</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>

                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <a href="{{URL::route('saleitem.opportunity.index')}}" class="btn btn-default"> {{ __('Cancel') }} </a>
                            <button type="submit" class="btn btn-info pull-right text-white"><i class="fa @if($opportunity) fa-refresh @else fa-plus-circle @endif"></i> @if($opportunity) Update @else {{ __('Add') }} @endif</button>

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
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('button[type="submit"]').on("click",function(e) {
                e.preventDefault();
                $.ajax(
                    {
                        url: "{{route('ajax.cart.has')}}",
                        type: 'get',
                    }).done(
                    function(data)
                    {
                        if(data.is_has)
                            $("#entryForm").submit();
                        else
                            toastr.warning('Please add product!');

                    }
                );


            });
            //add item

            let body=$('.panel-body');


//add product
            body.on("click","#btn_add_product",function(){
                let product_val = $('select[name="product_name"]').val();
                // let product_text = $('select[name="product_name"] option:selected').text();
                let qty = $('input[name="qty"]').val();

                if(product_val){
                    $.ajax(
                        {
                            url: "{{route('ajax.add.product')}}",
                            type: 'POST',
                            data:{id:product_val,qty:qty},
                        }).done(
                        function(data)
                        {
                            $('tbody').html(data.html);
                            $('#total_qty').html('Total Quantity : '+data.total_qty);
                            $('#total_price').html('Total Price : '+data.total_price);
                            toastr.success("Item has been Added successfully!");
                        }
                    );
                }else {

                    toastr.warning('Please choose one item.');
                    return false;
                }
            });
            body.on("click","#btnDelete",function(e){
                e.preventDefault();
                let href = $(this).attr('href');
                let current =  $(this);
                $.ajax(
                    {
                        url: href,
                        type: 'GET',
                    }).done(
                    function(data)
                    {
                        // current.parents('tr').remove();
                        $('tbody').html(data.html);
                        $('#total_qty').html('Total Quantity : '+data.total_qty);
                        $('#total_price').html('Total Price : '+data.total_price);
                        toastr.warning('Item has been deleted!');
                    }
                );

            });

            //end add item
//update qty
            $(document).on('change', '.edit_quantity', function() {
                var row = $(this).closest('tr');
                var quantity = $(this).val();
                var price = row.find('input.edit_price').val();
                // var sub_total = row.find('input.sub_total');
                var id = row.data('id');
                var discount = row.find('input.edit_discount').val();

                    $.ajax(
                        {
                            url: "{{route('ajax.add.product')}}",
                            type: 'POST',
                            data:{id:id,price:price,quantity:quantity,discount:discount,is_update:true},
                        }).done(
                        function(data)
                        {
                            $('tbody').html(data.html);
                            $('#total_qty').html('Total Quantity : '+data.total_qty);
                            $('#total_price').html('Total Price : '+data.total_price);
                        }
                    );
            });

            //update price
            $(document).on('change', '.edit_price', function() {
                var price = $(this).val();
                var row = $(this).closest('tr');
                var quantity = row.find('input.edit_quantity').val();
                var discount = row.find('input.edit_discount').val();
                var id = row.data('id');
                $.ajax(
                    {
                        url: "{{route('ajax.add.product')}}",
                        type: 'POST',
                        data:{id:id,price:price,quantity:quantity,discount:discount,is_update:true},
                    }).done(
                    function(data)
                    {
                        $('tbody').html(data.html);
                        $('#total_qty').html('Total Quantity : '+data.total_qty);
                        $('#total_price').html('Total Price : '+data.total_price);
                    }
                );
            });
            // update discount
            $(document).on('change', '.edit_discount', function() {
                var discount = $(this).val();
                var row = $(this).closest('tr');
                var quantity = row.find('input.edit_quantity').val();
                var price = row.find('input.edit_price').val();
                var id = row.data('id');
                $.ajax(
                    {
                        url: "{{route('ajax.add.product')}}",
                        type: 'POST',
                        data:{id:id,price:price,quantity:quantity,discount:discount,is_update:true},
                    }).done(
                    function(data)
                    {
                        $('tbody').html(data.html);
                        $('#total_qty').html('Total Quantity : '+data.total_qty);
                        $('#total_price').html('Total Price : '+data.total_price);
                    }
                );
            });
            let row_number = 1;
            $("#add_row").click(function(e){
                e.preventDefault();
                let new_row_number = row_number - 1;

                var noOfDivs = $('.product0').length;
                var clonedDiv = $('.product0').first().clone(true);
                clonedDiv.insertBefore("#tool-placeholder");
                clonedDiv.attr('id', 'product' + noOfDivs);

                row_number++;
            });

            $("#delete_row").click(function(e){
                e.preventDefault();
                if(row_number > 1){
                    $("#product" + (row_number - 1)).html('');
                    row_number--;
                }
            });
            $('tbody .product-name').on('change', function () {
                let p_id = $(this).val();
                let getUrl = "saleitem/product/"+p_id;

                axios.get(getUrl)
                    .then((response) => {
                      console.log(response.data);

                    }).catch((error) => {
                    let status = error.response.statusText;
                    toastr.error(status);

                });
            });

            Generic.initCommonPageJS();
            $('select[name="org_id"]').on('change', function () {
                $('select[name="buyer_id"]').empty().select2({placeholder: 'Pick a Buyer...'});
                $('select[name="buyer_type"]').val(null);
                $('select[name="buyer_type"]').select2().trigger('change');
                let org_id = $('select[name="org_id"]').val();
                if(org_id){
                    getItem(org_id, function (res={}) {
                        $.ajax(
                            {
                                url: "{{route('ajax.cart.clear')}}",
                                type: 'GET',
                            }).done(
                            function(data)
                            {
                                $('tbody').html(data.html);
                                $('#total_qty').html('Total Quantity : '+data.total_qty);
                                $('#total_price').html('Total Price : '+data.total_price);
                            }
                        );
                        if (Object.keys(res).length){
                            $('select[name="product_name"]').empty().prepend('<option selected=""></option>').select2({placeholder: '---Choose Product---', data: res});
                        }
                        else{
                            $('select[name="product_name"]').empty().select2({placeholder: 'Pick a Buyer...'});
                        }

                    });
                }

            });
            $('select[name="buyer_type"]').on('change', function () {
                let buyer_type = $(this).val();

                $('select[name="buyer_id"]').empty().select2({placeholder: 'Pick a Buyer...'});
                if($('select[name="org_id"]').length){
                    let org_id = $('select[name="org_id"]').val();
                    if(org_id){
                        Generic.loaderStart();
                        getBuyer(buyer_type,org_id, function (res={}) {
                            if (Object.keys(res).length){
                                $('select[name="buyer_id"]').empty().prepend('<option selected=""></option>').select2({placeholder: 'Pick a Buyer...', data: res});
                            }
                            else{
                                // clear subject list dropdown
                                if(buyer_type){
                                    $('select[name="buyer_id"]').empty().select2({placeholder: 'Pick a Buyer...'});
                                    toastr.warning('This Type don\'t have buyer!');
                                }
                            }
                            Generic.loaderStop();
                        });

                    }else {
                        if(buyer_type){
                            $('select[name="buyer_id"]').empty().select2({placeholder: 'Pick a Buyer...'});
                            toastr.warning('Fill up Organization first!');
                            return false;
                        }
                    }

                }else {
                    Generic.loaderStart();
                    let org_id = "{{getAuthUser()->org_id}}";
                    getBuyer(buyer_type,org_id, function (res={}) {
                        if (Object.keys(res).length){
                            $('select[name="buyer_id"]').empty().prepend('<option selected=""></option>').select2({placeholder: 'Pick a Buyer...', data: res});
                        }
                        else{
                            // clear subject list dropdown
                            if(buyer_type){
                                $('select[name="buyer_id"]').empty().select2({placeholder: 'Pick a Buyer...'});
                                toastr.warning('This Type don\'t have buyer!');
                            }
                        }
                        Generic.loaderStop();
                    });
                }

            });
            function  getBuyer(buyer_type, org_id=0, cb) {
                let getUrl = "{!! route('get.buyer') !!}" + "?buyer_type=" + buyer_type+"&org_id="+org_id;

                axios.get(getUrl)
                    .then((response) => {
                        cb(response.data);

                    }).catch((error) => {
                    let status = error.response.statusText;
                    toastr.error(status);
                    cb();

                });

            }
            function  getItem(org_id, cb) {
                let getUrl = "{!! route('saleitem.product.index') !!}" + "?org_id=" + org_id+"&is_selection=1";

                axios.get(getUrl)
                    .then((response) => {
                        cb(response.data);

                    }).catch((error) => {
                    let status = error.response.statusText;
                    toastr.error(status);
                    cb();

                });

            }
        });
    </script>
@endsection
<!-- END PAGE JS-->
