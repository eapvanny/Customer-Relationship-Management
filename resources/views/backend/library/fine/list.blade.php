<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Invoice @endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->
@php
    $iclass                   = request()->list_class??"";
    $section_id               = request()->list_section??"";
    $student_id               = request()->list_regi_id??"";
    $not_format_date_from     = request()->date_from ??"";
    $not_format_date_upto     = request()->date_upto ??"";
    $params_array             = Request::query();
    $full_url                 = route('library.fine.index',$params_array);
@endphp

@section('extraStyle')
    <style>
        @media screen and (max-width: 768px) {
            div#datatabble_filter {
                padding: 10px;
                float: right;
            }
            .get_filter{
                margin-bottom: 10px;
            }
        }
    </style>
@endsection
<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            {{ __('Library Fine') }}
            <small> {{ __('Collection') }} </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('user.dashboard') }}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li><a href="{{ URL::route('library.fine.index') }}"><i class="fa icon-library"></i> {{ __('Library') }} </a></li>
            <li class="active"> {{ __('Fee') }} </li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header">
                        <div class="box-tools pull-right">
                            <a class="btn btn-info text-white" href="{{ URL::route('library.fine.create') }}"><i class="fa fa-plus-circle"></i> {{ __('Add New') }} </a>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                        <fieldset>
                                                <legend> {{ __('Filters:') }} </legend>
                                                <form action="" method="get" enctype="multipart/form-data">
                                                    <input type="hidden" name="filter" value="1">
                                                    <div class="row">
                                                            @if(AppHelper::getInstituteCategory() == 'college')
                                                            <div class="col-md-3">
                                                            <div class="form-group has-feedback">
                                                                    {!! Form::select('academic_year', $academic_years, $acYear , ['placeholder' => 'Pick a year...','class' => 'form-control select2', 'required' => 'true']) !!}
                                                                </div>
                                                            </div>
                                                            @endif
                                                            {!! AppHelper::selectOrgOfUser($seleted_org,true) !!}
                                                            <div class="col-md-3">
                                                                <label for="class_id"> {{ __('Class') }} </label>
                                                                <div class="form-group has-feedback">
                                                                    {!! Form::select('list_class', $classes, $iclass , ['placeholder' => 'Pick a class...','class' => 'form-control select2']) !!}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label for="section_id"> {{ __('Section') }} </label>
                                                                <div class="form-group has-feedback">
                                                                    {!! Form::select('list_section', $sections, $section_id , ['placeholder' => 'Pick a section...','class' => 'form-control select2', 'id' => 'section_list_filter']) !!}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label for="student_id"> {{ __('Student Registration') }} </label>
                                                                <div class="form-group has-feedback">
                                                                    {!! Form::select('list_regi_id',$students ,$student_id, ['placeholder' => 'Pick a section...','class' => 'form-control select2']) !!}
                                                                </div>
                                                            </div>
                                                            {{-- <div class="{{AppHelper::getInstituteCategory() == 'college'?'col-md-3':'col-md-4'}}">
                                                                <label for="class_id">Class</label>
                                                                <div class="form-group has-feedback">
                                                                    {!! Form::select('list_class', $classes, $iclass , ['placeholder' => 'Pick a class...','class' => 'form-control select2']) !!}
                                                                </div>
                                                            </div>
                                                            <div class="{{AppHelper::getInstituteCategory() == 'college'?'col-md-3':'col-md-4'}}">
                                                                <label for="section_id">Section</label>
                                                                <div class="form-group has-feedback">
                                                                    {!! Form::select('list_section', $sections, $section_id , ['placeholder' => 'Pick a section...','class' => 'form-control select2', 'id' => 'section_list_filter']) !!}
                                                                </div>
                                                            </div>
                                                            <div class="{{AppHelper::getInstituteCategory() == 'college'?'col-md-3':'col-md-4'}}">
                                                                <label for="student_id">Student</label>
                                                                <div class="form-group has-feedback">
                                                                    {!! Form::select('list_regi_id',$students ,$student_id, ['placeholder' => 'Pick a section...','class' => 'form-control select2']) !!}
                                                                </div>
                                                            </div> --}}
                                                    </div>
                                                    <div class="row">
                                                            <div class="col-md-2">
                                                                <div class="form-group has-feedback has-success">
                                                                    <label for="date_from"> {{ __('Date From') }} <span class="text-danger">*</span></label>
                                                                    <div class="input-group" style="display: block">
                                                                        <input type="text" readonly="" class="form-control date_picker" name="date_from" placeholder="date" value="{{$not_format_date_from}}">
                                                                        <span class="fa fa-calendar form-control-feedback"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group has-feedback">
                                                                    <label for="date_upto"> {{ __('Date Upto') }} <span class="text-danger">*</span></label>
                                                                    <div class="input-group" style="display: block">
                                                                        <input type="text" readonly="" class="form-control date_picker" name="date_upto" placeholder="date" value="{{$not_format_date_upto}}">
                                                                        <span class="fa fa-calendar form-control-feedback"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <button type="submit" class="btn btn-info pull-left get_filter" style="margin-top: 24px;" id="getListSearch"><i class="fa icon-invoice"></i> {{ __('Get List') }} </button>
                                                                </div>
                                                            </div>
                                                    </div>
                                                </form>
                                        </fieldset>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="datatabble" class="table table-bordered table-striped list_view_table display responsive no-wrap datatable-server" width="100%">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th class="notexport" width="7%"> {{ __('Student') }} </th>
                                            <th width="15%"> {{ __('Book Name') }} </th>
                                            <th width="7%"> {{ __('Collection Date') }} </th>
                                            <th width="15%"> {{ __('Invoice No.') }} </th>
                                            <th width="15%"> {{ __('Quantity') }} </th>
                                            <th width="10%"> {{ __('Fine Amount') }} </th>
                                            <th class="notexport" width="15%"> {{ __('Action') }} </th>
                                        </tr>
                                    </thead>
                                <tbody>
                                    {{--@foreach ($fines as $key => $item)--}}
                                        {{--<tr>--}}
                                            {{--<td>--}}
                                                {{--{{$loop->iteration}}--}}
                                            {{--</td>--}}
                                            {{--<td>{{$item->student()->first()['name']}}</td>--}}
                                            {{--@php--}}
                                                 {{--$col_d = \Carbon\Carbon::createFromFormat('Y-m-d', $item->collection_date);--}}
                                            {{--@endphp--}}
                                            {{--<td>{{$col_d->format('d/m/Y')}}</td>--}}
                                            {{--<td>{{$item->invoice()->first()->invoice_no}}</td>--}}
                                            {{--<td>{{$item->fine_amount}}</td>--}}
                                            {{--<td>--}}
                                                {{--<div class="btn-group">--}}
                                                    {{--<form  class="myAction" method="POST" action="{{URL::route('library.fine.destroy',$item->id)}}">--}}
                                                        {{--@csrf--}}
                                                        {{--{{ method_field('delete') }}--}}
                                                        {{--<input type="hidden" name="hiddenId" value="{{$item->id}}">--}}
                                                        {{--<button type="submit" class="btn btn-danger btn-sm" title="Delete">--}}
                                                            {{--<i class="fa fa-fw fa-trash"></i>--}}
                                                        {{--</button>--}}
                                                    {{--</form>--}}
                                                {{--</div>--}}
                                            {{--</td>--}}
                                        {{--</tr>--}}
                                    {{--@endforeach--}}
                                </tbody>

                            </table>
                            </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="btn-group">
                        <form id="myAction" method="POST">
                            @csrf
                            <input name="_method" type="hidden" value="DELETE">
                        </form>
                    </div>

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
            window.section_list_url = '{{URL::Route("academic.section")}}';
            // window.student_list_url = '{{URL::Route("student.list_by_fitler")}}';
            window.student_list_url = '{{URL::Route("registration.list_by_fitler")}}';
            window.paymentListUrl = '{{URL::Route("payment.index")}}';
            window.list_invoice = '{{URL::Route("invoice.index")}}';
            window.baseUrl = '{{url('/')}}';
            // Academic.studentInit();
            window.excludeFilterComlumns = [0,4,5,6,7];
            Generic.initCommonPageJS();
            Generic.initDeleteDialog();
            $('select[name="list_class"]').on('change', function () {
                let class_id = $(this).val();
                getListSection(class_id);
            });

            $('select[name="list_section"]').on('change', function () {
                let class_id = $('select[name="list_class"]').val();
                let section_id = $(this).val();
                let ac_year = $('select[name="academic_year"]').val();
                getListStudent(class_id,section_id,ac_year,'student');
            });

            function getListSection(class_id) {
                let getUrl = window.section_list_url + "?class=" + class_id;
                if (class_id) {
                    Generic.loaderStart();
                    axios.get(getUrl)
                        .then((response) => {
                            if (Object.keys(response.data).length) {
                                $('select[name="list_section"]').empty().prepend('<option selected=""></option>').select2({allowClear: true,placeholder: 'Pick a section...', data: response.data});
                            }
                            else {
                                $('select[name="list_section"]').empty().select2({placeholder: 'Pick a section...'});
                                toastr.error('This class have no section!');
                            }
                            Generic.loaderStop();
                        }).catch((error) => {
                        let status = error.response.statusText;
                        toastr.error(status);
                        Generic.loaderStop();

                    });
                }
                else {
                    // clear section list dropdown
                    $('select[name="list_section"]').empty().select2({placeholder: 'Pick a section...'});
                }
            }

            function getListStudent(class_id,section_id,ac_year = 0,param) {
                let getUrl = window.student_list_url + "?class=" + class_id +"&section=" + section_id + "&academic_year" +ac_year + "&" +param;
                if (class_id && section_id) {
                    Generic.loaderStart();
                    axios.get(getUrl)
                        .then((response) => {
                            if (Object.keys(response.data).length) {
                                $('select[name="list_regi_id"]').empty().prepend('<option selected=""></option>').select2({allowClear: true,placeholder: 'Pick a section...', data: response.data});
                            }
                            else {
                                $('select[name="list_regi_id"]').empty().select2({placeholder: 'Pick a section...'});
                                toastr.error('This class have no section!');
                            }
                            Generic.loaderStop();
                        }).catch((error) => {
                        let status = error.response.statusText;
                        toastr.error(status);
                        Generic.loaderStop();

                    });
                }
                else {
                    // clear section list dropdown
                    $('select[name="list_regi_id"]').empty().select2({placeholder: 'Pick a section...'});
                }
            }


            //payment related js
            $("#listDataInvoice").on("click",".btnPaymentDeatils",function(e){

                let pk = $(this).attr('data-pk');
                let getUrl = window.paymentListUrl+"?invoice="+pk;

                $('#paymentListTable tbody').empty();
                Generic.loaderStart();
                axios.get(getUrl).then((response) => {
                    if(response.data.success) {
                        console.log( response.data.payments);
                        response.data.payments.forEach(function(payment, index) {
                            let trRow = '<tr>\n' + '<td>' + (index + 1) + '</td>\n' + ' <td>' + payment.payment_date + '</td>\n';
                                if (payment.payment_method == 1) {
                                    trRow+='<td>Cash</td>\n';
                                }else if(payment.payment_method == 2){
                                    trRow+='<td>Bank</td>\n';
                                }else if(payment.payment_method == 3){
                                    trRow+='<td>Mobile Banking</td>\n';
                                }
                                trRow+='<td>$' + payment.amount.toFixed(2) + ' ' + '' + '</td>\n' + '<td>\n';

                                if(payment.payment_reference) {
                                        trRow += payment.payment_reference;
                                    }
                                    trRow += '</td>\n' + '<td>\n';
                                    if(payment.doc) {
                                        trRow += '<a href="/storage/payment/document/' + payment.doc + '" target="_blank" title="document" class="btn-link"><i class="fa fa-download"></i></a>\n';
                                    }
                                    // trRow += '</td>\n' + ' <td>\n' + '<a href="" target="_blank" title="Print" class="btn btn-primary btn-xs"><i class="fa fa-print"></i></a>\n' + '<a href="#" title="Delete" class="btn btn-danger btn-xs btnPaymentDelete" data-pk="' + payment.id + '"><i class="fa fa-times-circle"></i></a>\n' + '</td>\n' + '</tr>';
                                    trRow += '</td>\n' + ' <td>\n' + '<a href="/payment/'+payment.id+'?print_it=1" target="_blank" title="Print" class="btn btn-primary btn-xs"><i class="fa fa-print"></i></a>\n' + '<a href="#" title="Delete" class="btn btn-danger btn-xs btnPaymentDelete" data-pk="' + payment.id + '"><i class="fa fa-times-circle"></i></a>\n' + '</td>\n' + '</tr>';
                                    $('#paymentListTable tbody').append(trRow);

                        });
                        $('#modalPaymentDetails').modal('show');
                    }else{
                        $('#modalPaymentDetails').modal('show');
                    }
                    Generic.loaderStop();

                }).catch((error) => {
                        let status = error.response.statusText;
                        toastr.error(status);
                        Generic.loaderStop();
                });
            });
            $('html').on('click', 'a.btnPaymentDelete', function() {
                let that = this;
                let pk = $(this).attr('data-pk');
                let postUrl = window.baseUrl+"/payment/"+pk;
                Generic.loaderStart();
                axios.delete(postUrl).then((response) => {
                     console.log(response);
                    if(response.data.success) {
                        toastr.info(response.data.message);
                        $(this).closest('tr').remove();
                    }
                    Generic.loaderStop();
                }).catch((error) => {
                    let status = error.response.statusText;
                    toastr.error(status);
                    Generic.loaderStop();
                });
            });

            $('#modalPaymentDetails').on('hidden.bs.modal', function () {
                $('#listDataInvoice').DataTable().ajax.reload(null, false);
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var t = $('#datatabble').DataTable({
                processing: false,
                serverSide: true,
                ajax:{
                    url: "{!! $full_url !!}",
                },
                columns:[
                    {
                        data: 'id',
                        name: 'id',
                    },
                    {
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'book_name',
                        name: 'book_name',
                    },
                    {
                        data: 'col_date',
                        name: 'col_date',
                    },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no',
                    },
                    {
                        data: 'qty',
                        name: 'qty',
                    },
                    {
                        data: 'fine_amount',
                        name: 'fine_amount',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false
                    }
                ],
                "fnDrawCallback": function() {
                    $('#datatabble input.statusChange').bootstrapToggle({
                        on: "Return",
                        off: "Not Return"
                    });
                }
            });

            // t.on( 'order.dt search.dt', function () {
            //     t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            //         cell.innerHTML = i+1;
            //     } );
            // } ).draw();
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
    </script>
@endsection
<!-- END PAGE JS-->
