<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Opportunity @endsection
<!-- End block -->

<!-- Page body extra css -->
@section('extraStyle')
    <style>
        @media print {
            @page {
                size:  A4 landscape;
                margin: 5px;
            }
        }
        .width-100{
            width: 99%;
        }
    </style>
@endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')

    <!-- Section header -->
    <section class="content-header">
        <div class="btn-group">
            <a href="{{URL::route('saleitem.opportunity.edit',$opportunity->id)}}" class="btn-ta btn-sm-ta"><i class="fa fa-edit"></i> {{ __('Edit') }} </a>
        </div>

        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li> {{ __('Sale Item') }} </li>   
            <li><a href="{{URL::route('saleitem.opportunity.index')}}"> {{ __('curriculum') }} </a></li>
            <li class="active"> {{ __('View') }} </li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content main-contents">
        <div class="row">
            <div class="col-md-12">
                <div id="printableArea">
                    <div class="row">
                        @php
                            $tab = 'profile';
                            if(request()->has('tab'))
                            {
                             $tab = request()->get('tab');
                            }
                        @endphp
                        <div class="col-sm-12">
                            <div class="nav-tabs-custom">
                                <ul class="nav nav-tabs" id="nav_tab">
                                    <li class="{{$tab=='profile' ? 'active' : ''}}"><a href="#profile" data-toggle="tab"> {{ __('Detail') }} </a></li>
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane {{$tab=='profile' ? 'active' : ''}}" id="profile">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for=""> {{ __('Name') }} </label>
                                            </div>
                                            <div class="col-md-3">
                                                    <p>: {{$opportunity->name}}</p>
                                            </div>
                                            <div class="col-md-3">
                                                <label for=""> {{ __('Buyer Type') }} </label>
                                            </div>
                                            <div class="col-md-3">
                                                <p>: {{\App\Http\Helpers\AppHelper::BUYER_TYPE[$opportunity->buyer_type]}}</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for=""> {{ __('Description') }} </label>
                                            </div>
                                            <div class="col-md-3">
                                                <p for="">: {{$opportunity->description}}</p>
                                            </div>
                                            <div class="col-md-3">
                                                <label for=""> {{ __('Buyer') }} </label>
                                            </div>
                                            <div class="col-md-3">
                                                <p>: {{$buyer_name}}</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for=""> {{ __('Is Won') }} </label>
                                            </div>
                                            <div class="col-md-3">
                                                <p>: {{\App\Http\Helpers\AppHelper::IS_WON[$opportunity->is_won]}}</p>
                                            </div>
                                            <div class="col-md-3">
                                                <label for=""> {{ __('Is Closed') }} </label>
                                            </div>
                                            <div class="col-md-3">
                                                <p for="">: {{\App\Http\Helpers\AppHelper::IS_CLOSED[$opportunity->is_closed]}}</p>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for=""> {{ __('Amount') }} </label>
                                            </div>
                                            <div class="col-md-3">
                                                <p for="">: @money($opportunity->amount,'USD',true)</p>
                                            </div>
                                            <div class="col-md-3">
                                                <label for=""> {{ __('Quantity') }} </label>
                                            </div>
                                            <div class="col-md-3">
                                                <p for="">: {{$opportunity->quantity}}</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for=""> {{ __('Type') }} </label>
                                            </div>
                                            <div class="col-md-3">
                                                <p for="">: {{\App\Http\Helpers\AppHelper::OPPORTUNITY_TYPE[$opportunity->type]}}</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <hr>
                                                <h4> {{ __('Product Items') }} </h4>
                                                <div class="table-responsive">
                                                    <table id="table-product" class="table table-bordered table-striped list_view_table display responsive no-wrap" width="100%">
                                                        <thead>
                                                        <tr>
                                                            <th width="5%">#</th>
                                                            <th width="30%"> {{ __('Name') }} </th>
                                                            <th width="10%"> {{ __('Quantity') }} </th>
                                                            <th width="25%"> {{ __('Unit Cost') }}($)</th>
                                                            <th width="10%"> {{ _('Discount') }}(%)</th>
                                                            <th width="25%"> {{ __('Line Total') }}($)</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @php  $i=1;@endphp
                                                        @if(count($opportunity->opportunity_line_item))
                                                            @foreach($opportunity->opportunity_line_item as $item)
                                                                <tr>
                                                                    <td>{{$i}}</td>
                                                                    <td>{{$item->name}}</td>
                                                                    <td>{{$item->quantity}}</td>
                                                                    <td>{{$item->unit_price}}</td>
                                                                    <td>{{$item->discount}}</td>
                                                                    {{--        <td><input type="number" data-id="{{$item->id}}" class="form-control edit_price" value="{{$item->price}}" name="edit_price"></td>--}}
                                                                    <td>{{$item->quantity*$item->unit_price - ($item->quantity*$item->unit_price*$item->discount/100)}}</td>
                                                                </tr>
                                                                @php  $i++; @endphp
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="4"> {{ __('No item') }} </td>
                                                            </tr>
                                                        @endif

                                                        </tbody>

                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <!-- /.content -->

    <!-- Modal -->

    {{-- <div class="modal fade" id="modal_pickup" role="dialog">
        <div class="modal-dialog modal-lg width-100">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Pickup info.</h4>
                </div>
                <form id="frm_child_picker" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group has-feedback">
                                    <label for="name">Name<span class="text-danger">*</span></label>
                                    <input autofocus type="text" class="form-control" name="name" placeholder="name" value="{{old('name')}}" required minlength="2" maxlength="255">
                                    <span class="fa fa-info form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group has-feedback">
                                    <label for="gender">Gender<span class="text-danger">*</span>
                                        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="select gender type"></i>
                                    </label>
                                    <select class="form-control select2">
                                        <option value="1">Male</option>
                                        <option value="2">Female</option>
                                    </select>
                                    <span class="form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('gender') }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group has-feedback">
                                    <label for="dob">Date of birth<span class="text-danger">*</span></label>
                                    <input type='text' class="form-control date_picker2"  readonly name="dob" placeholder="date" value="" required minlength="10" maxlength="255" />
                                    <span class="fa fa-calendar form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('dob') }}</span>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group has-feedback">
                                    <label for="email">Email<span class="text-danger">*</span></label>
                                    <input  type="email" class="form-control" name="email"  placeholder="email address" value="{{old('email')}}" maxlength="100" required>
                                    <span class="fa fa-envelope form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group has-feedback">
                                    <label for="phone_no">Phone/Mobile No.<span class="text-danger">*</span></label>
                                    <input  type="text" class="form-control" name="phone_no" required placeholder="phone or mobile number" value="{{old('phone_no')}}" min="8" maxlength="15">
                                    <span class="fa fa-phone form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('phone_no') }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group has-feedback">
                                    <label for="id_card">ID Card No.<span class="text-danger">*</span></label>
                                    <input  type="text" class="form-control" name="id_card"  placeholder="id card number" value="{{old('id_card')}}" required minlength="4" maxlength="50">
                                    <span class="fa fa-id-card form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('id_card') }}</span>
                                </div>
                            </div>

                        </div>
                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-group has-feedback">
                                    <label for="address">Address</label>
                                    <textarea name="address" class="form-control"  maxlength="500" ></textarea>
                                    <span class="fa fa-location-arrow form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('address') }}</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-feedback">
                                    <label for="photo">Photo<br><span class="text-danger">[min 150 X 150 size and max 200kb]</span></label>
                                    <input  type="file" class="form-control" accept=".jpeg, .jpg, .png" name="photo" placeholder="Photo image">
                                    <span class="glyphicon glyphicon-open-file form-control-feedback" style="top:45px;"></span>
                                    <span class="text-danger">{{ $errors->first('photo') }}</span>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Create</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}

@endsection
<!-- END PAGE CONTENT-->

<!-- BEGIN PAGE JS-->
@section('extraScript')

    <script type="text/javascript">
        $(document).ready(function () {
            $('.btnPrintInformation').click(function () {
                $('ul.nav-tabs li:not(.active)').addClass('no-print');
                $('ul.nav-tabs li.active').removeClass('no-print');
                window.print();
            });
            Generic.initCommonPageJS();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            {{--$("#frm_child_picker").submit(function(event){--}}
                {{--event.preventDefault();--}}
                {{--$.ajax({--}}
                    {{--url: '{{route('curriculum.store')}}',--}}
                    {{--type: 'POST',--}}
                    {{--cache: false,--}}
                    {{--datatype: 'json',--}}
                    {{--data: $(this).serialize(),--}}

                    {{--success: function(response){--}}
                        {{--$('#modal_pickup').modal('toggle');--}}
                        {{--// $(this).trigger("reset");--}}
                    {{--},--}}
                    {{--error:function(){}--}}
                {{--});--}}
            {{--});--}}

// store the currently selected tab in the hash value
//             $("ul.nav-tabs > li > a").on("shown.bs.tab", function(e) {
//                 var id = $(e.target).attr("href").substr(1);
//                 window.location.hash = id;
//                e.preventDefault();
//             });
//             var hash = window.location.hash;
//             $('#nav_tab a[href="' + hash + '"]').tab('show');
        });
    </script>

    {{--<script>--}}
        {{--$( function() {--}}
            {{--$( "#id_card" ).autocomplete({--}}
                {{--source: "{{ URL::route('search-id-card') }}?picker",--}}
                {{--minLength: 2,--}}
                {{--select: function(event, ui) {--}}

                    {{--// $("#parents_id").val(ui.item.id)--}}

                {{--}--}}
            {{--});--}}
        {{--});--}}

    {{--</script>--}}
@endsection
<!-- END PAGE JS-->
