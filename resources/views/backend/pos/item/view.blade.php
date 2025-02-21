<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Item @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')

    <!-- Section header -->
    <section class="content-header">
        <div class="btn-group">
            {{-- @if($item->created_by==auth()->user()->id) --}}
            <a href="{{URL::route('pos.item.edit',$item->id)}}" class="btn-ta btn-sm-ta"><i class="fa fa-edit"></i> Edit</a>
            {{-- @endif --}}
        </div>

        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li> {{ __('POS') }} </li>
            <li><a href="{{URL::route('pos.item.index')}}"> {{ __('Item') }} </a></li>
            <li class="active">View</li>
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
                                                    <label for=""> {{ __('Name:') }} </label>
                                                </div>
                                                <div class="col-md-3">
                                                        <p>: {{$item->name}}</p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label for=""> {{ __('Default Price') }} </label>
                                                </div>
                                                <div class="col-md-3">
                                                    <p for="">: {{$item->default_price}}</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for=""> {{ __('Status') }} </label>
                                                </div>
                                                <div class="col-md-3">
                                                    <p for="">: {{($item->status==1)?"Active":"Inactive"}}</p>
                                                </div>
                                                @if ($item && $item->option->isEmpty())
                                                    <div class="col-md-3">
                                                        <label for=""> {{ __('Quantity.') }} </label>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <p for="">: {{$item->qty}}</p>
                                                    </div>
                                                @endif
                                                <div class="col-md-3">
                                                    <label for=""> {{ __('Description.') }} </label>
                                                </div>
                                                <div class="col-md-3">
                                                    <p for="">: {{$item->description}}</p>
                                                </div>
                                            </div>
                                            
                                            @if(count($item->option))
                                              <div class="row">
                                                <div class="col-md-12">
                                                    <div class="panel panel-primary">
                                                        <div class="panel-heading">
                                                            <h3 class="panel-title"> {{ __('Option') }} </h3>
                                                        </div>
                                                        <div class="panel-body">
                                                            <div class="col-md-4">
                                                                <div class="form-group has-feedback">
                                                                    <label for="option_name"> {{ __('Name') }} </label>
                                                                    <input disabled type="text" name="option_name" class="form-control" value="{{$item && count($item->option) ? $item->option[0]->name : ''}}">
                                                                    <span class="text-danger">{{ $errors->first('option_name') }}</span>
                                                                </div>
                                                            </div>
                                                            <table id="myTable" class=" table order-list" readonly="true">
                                                                <thead>
                                                                <tr>
                                                                    <td> {{ __('Value') }} </td>
                                                                    <td>Pri {{ __('Price') }} </td>
                                                                    <td> {{ __('Quantity') }} </td>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @if($item && $item->option)
                                                                    @php
                                                                        $options = $item->option->where('deleted_at',NULL);
                                                                    @endphp
                                                                    @foreach($options as $op)
                                                                        <tr>
                                                                            <td class="col-sm-3">
                                                                                <input disabled type="text" name="value[]"  class="form-control" value="{{$op->value}}"/>
                                                                            </td>
                                                                            <td class="col-sm-3">
                                                                                <input disabled type="number" name="price[]"  class="form-control" value="{{$op->price}}"/>
                                                                            </td>
                                                                            <td class="col-sm-3">
                                                                                <input disabled type="number" name="qty[]"  class="form-control" value="{{$op->qty}}"/>
                                                                            </td>

                                                                            <td class="col-sm-2"><a class="deleteRow"></a>

                                                                            </td>
                                                                        </tr>

                                                                    @endforeach
                                                                @endif
                                                                </tbody>
                                                                <tfoot>

                                                                <tr>
                                                                </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                             @endif
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

@endsection
<!-- END PAGE CONTENT-->

<!-- BEGIN PAGE JS-->
@section('extraScript')

    <script type="text/javascript">
        $(document).ready(function () {
            Generic.initCommonPageJS();
        });
    </script>
@endsection
<!-- END PAGE JS-->
