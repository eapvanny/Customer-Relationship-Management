<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Order @endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->
@section('extraStyle')
    <style>
        .action_btn{
            padding-bottom: 30px;
            padding-right: 10px;
            padding-top: 20px;
        }
        a.btn.act_btn {
            margin-right: 8px;
        }
        .action_total{
            text-align: right;
            padding-right: 10px;
            padding-bottom: 30px;
            border-bottom: 2px solid #ecf0f5;
        }
        @media only screen and (max-width: 767px) {
            .fixed .content-wrapper, .fixed .right-side {
                padding-top: 50px !important;
            }
        }
    </style>
@endsection
<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
{{-- 

        <h1>

            Order
            <small>view</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class=""><a href="{{URL::route('pos.order.index')}}">Order</a></li><li class="active">view</li>
        </ol>
        <div class="alert custom_alert  alert-success  alert-dismissible hidden">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5 id="msg"><i class="icon fa fa-check"></i>Status Updated</h5>

        </div> --}}

    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header">
                        <form method="get" id="frm_search">
                            {{--{!! AppHelper::selectOrgOfUser($seleted_org) !!}--}}
                        </form>
                        <div class="row">
                            <div class="col-md-4">
                                <h4><b> {{ __('Payment Information') }} </b></h4>
                                <p><b> {{ __('Payment Method') }}</b> : {{$order->payment_method}} </p>
                                <p id="payment_status"><b> {{ __('Payment Status') }}</b> : {{ucwords(str_replace("_"," ",$order->payment_status))}} </p>
                            </div>
                            <div class="col-md-4">
                                <h4><b> {{ __('Customer Information') }} </b></h4>
                                <p><b> {{ __('Name') }}</b>           : {{$user->name??''}}</p>
                                <p><b> {{ __('Email') }}</b>          : {{$user->email??''}}</p>
                                <p><b> {{ __('Phone Number')}}</b>   : {{$user->phone_no??''}}</p>
                                {{-- <p><b>Payment status</b> : <a href="http://">Paid</a> </p> --}}
                            </div>
                            <div class="col-md-4 pull-right">
                                <h4><b> {{ __('Order Information') }} </b></h4>
                                <p><b> {{ __('Organization') }}</b> : {{!empty($organization) ?$organization->name :null}}</p>
                                <p><b> {{ __('Address') }}</b> : {{!empty($organization) ?$organization->address :null}}</p>
                                <p><b> {{ __('Order Date') }}</b> : {{date('d/m/Y', strtotime($order->created_at))}} </p>
                                <p><b> {{ __('Total') }}</b>          : {{$order->total}}</p>
                                {{-- <p><b>Sub total</b>      ''    : {{$order->sub_total}}</p> --}}
                                {{-- <h4 style="text-align: right"><b>Order Date: </b>{{date('d/m/Y', strtotime($order->created_at))}}</h4> --}}
                            </div>
                        </div>
                        <div class="row action_btn" id="html">
                            {!! $reader !!}
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="datatabble" class="table table-bordered table-striped list_view_table display responsive no-wrap datatable-server" width="100%">
                                <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%"> {{ __('Item Name') }} </th>
                                    <th width="10%"> {{ __('Status') }} </th>
                                    <th width="10%"> {{ __('Instock') }} </th>
                                    <th width="10%"> {{ __('Quantity') }} </th>
                                    <th width="10%"> {{ __('Option') }} </th>
                                    <th width="10%"> {{ __('Price') }} </th>
                                    <th width="10%"> {{ __('Subtotal') }} </th>
                                </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%"> {{ __('Item Name') }} </th>
                                    <th width="10%"> {{ __('Status') }} </th>
                                    <th width="10%"> {{ __('Instock') }} </th>
                                    <th width="10%"> {{ __('Quantity') }} </th>
                                    <th width="10%"> {{ __('Option') }} </th>
                                    <th width="10%"> {{ __('Price') }} </th>
                                    <th width="10%"> {{ __('Subtotal') }} </th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="row action_total">
                        <div class="col-md-6"></div>
                        <div class="col-md-6 left">
                            <h4><b> {{ __('Total:') }}</b> ${{$total}}</h4>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    {{-- <div class="row action_btn" id="html">
                       {!! $reader !!}
                    </div> --}}
                </div>
            </div>
        </div>

    </section>
    <!-- /.content -->
    @php
        $payment_method = ["CASH"=>"Cash"];
    @endphp
    <div class="modal" tabindex="-1" id="exampleModal" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"> {{ __('Add Payment') }} </h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group has-feedback">
                            <label for="role_id"> {{ __('Payment') }} <span class="text-danger">*</span></label>
                           {!! Form::select('payment_method', $payment_method,null, ['class' => 'form-control select2','required' => 'true','placeholder' => 'Pick a payment...','id' => 'paymentMethod']) !!}
                            <span class="form-control-feedback"></span>
                            <span class="text-danger">{{ $errors->first('menu_id') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary"> {{ __('Pay') }} </button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal"> {{ __('Close') }} </button>
            </div>
          </div>
        </div>
      </div>

@endsection
<!-- END PAGE CONTENT-->
<!-- BEGIN PAGE JS-->
@section('extraScript')
    <script type="text/javascript">
        var is_visible = false;
        @if($visible)
            is_visible = true;
        @endif
        $(document).ready(function () {
            window.changeExportColumnIndex = 5;
            window.excludeFilterComlumns = [0,1,6,7];
            Generic.initCommonPageJS();
            Generic.initDeleteDialog();
            window.filter_org = 1;
            Generic.initFilter();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }

            });
            $( function () {
                    if (localStorage.getItem("Status")) {
                        localStorage.removeItem("Status");
                        $(".custom_alert").removeClass('hidden');
                        $("#msg").html('Status updated.');

                    }
                }
            );

            $("#html").on("click", ".status_item", function (ev) {
                ev.preventDefault();
                var status   = this.getAttribute('data-val');
                var validate = 0;
                var isCashier= "{{$cashier}}";
                var stastusVal = "{{$order->status}}";
                var payment_status = "{{$order->payment_status}}"
                // check validation action when user is not cashier
                // user is not cashier can click only Cancel
                if (!isCashier&&status!='CANCELLED'){
                    validate = 1;
                }
                //end check validation action when user is not cashier

                if (status=='NEW'){
                    validate = 1;
                }


                // check validation action when user is cashier
                if (isCashier){
                    if (stastusVal=='NEW'){
                       if (status!='CANCELLED'&&status!='PENDING'&&status!='COMPLETE'&&status!='PAID') {
                           validate = 1;
                       }
                    }else if (stastusVal=='PENDING'){
                        if (status == 'CANCELLED' || status =='COMPLETE' || status== 'PAID' ) {
                            validate = 0;
                        }else{
                            validate = 1;
                        }
                    }else if(stastusVal=='COMPLETE'){
                        // if (status!='PAID') {
                        //     validate = 1;
                        // }
                    }else {
                        if (status) {
                            validate = 1;
                        }
                    }
                }
                // check validation action when user is cashier

                if (validate==0){
                    var url = $(this).attr('href');
                    let status = this.getAttribute('data-val');
                    let body = {
                        _token:"{{csrf_token()}}",
                        id:"{{$order->id}}",
                        status:status
                    };
                    if(status == "PAID"){
                        // $('#exampleModal').modal('show');
                        updateStatusOrder(url,body);
                    }else{
                        updateStatusOrder(url,body);
                    }
                }else {
                    alert('Invalid Value!');
                }

            });

            $('select[name="org_id"]').on('change', function () {
                let org_id = $(this).val();
                if (org_id.trim()) {
                    $('#frm_search').submit();
                }
            });

            $('#exampleModal').on('hidden.bs.modal', function () {
                $("#paymentMethod").val('').trigger('change')
            })


            var t = $('#datatabble').DataTable({
                processing: true,
                serverSide: true,
                paging: false,
                ajax:{
                    url: "{!! route('pos.order.show',['datatable'=>true,'id'=>$order->id])!!}"
                },
                columns:[
                    {
                        data: 'id',
                        name: 'id',
                    },
                    {
                        data: 'item_name',
                        name: 'item_name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                       
                    },
                    {
                        data: 'in_stock',
                        name: 'in_stock'
                      
                    },

                    {
                        data: 'qty',
                        name: 'qty'
                    },
                    {
                        data: 'option',
                        name: 'option'
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'subtotal',
                        name: 'subtotal'
                    },
                ],
                "fnDrawCallback": function() {
                    if(is_visible){
                        $('#datatabble').DataTable().columns([2,3]).visible(false);
                    }else{
                        $('#datatabble').DataTable().columns([2,3]).visible(true);
                    }
                }
            });

            $('#datatabble').delegate('.delete','click', function(e){
                let action = $(this).attr('href');
                console.log()
                $('#myAction').attr('action',action);
                e.preventDefault();
                swal({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this record!',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dd4848',
                    cancelButtonColor: '#8f8f8f',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, keep it'
                }).then((result) => {
                    if (result.value) {
                        $('#myAction').submit();
                    }
                });
            });
        });

        function updateStatusOrder(url,body){
            $.ajax({
                        type:'POST',
                        url : url,
                        data:body,
                        success:function(data) {
                            if (data.success){
                                $("#html").html(data.reader);
                                $("#payment_status").html("<b>Payment Status</b>"+" : "+data.payment_status);
                                toastr.success(data.message);
                                if(data.visible){
                                    $('#datatabble').DataTable().columns([2,3]).visible(false);
                                }else{
                                    $('#datatabble').DataTable().columns([2,3]).visible(true);
                                }
                                $('#datatabble').DataTable().ajax.reload();
                                // localStorage.setItem("Status",data.success)
                                // location.reload(true);
                            }else {
                                console.log(data.errors);
                                $.each(data.errors, function(key,val) {
                                    toastr.error(val);
                                });
                            }

                        }
                    });
        }
    </script>
@endsection
<!-- END PAGE JS-->
