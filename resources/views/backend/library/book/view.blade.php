@php

    $class = $book->i_classes_id>0?$book->class1()->first()->name:"All";

@endphp
<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') book Profile @endsection
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
            {{--<a href="#"  class="btn-ta btn-sm-ta btn-print btnPrintInformation"><i class="fa fa-print"></i> Print</a>--}}
        </div>
        <div class="btn-group">
            <a href="{{URL::route('library.book.edit',$book->id)}}" class="btn-ta btn-sm-ta"><i class="fa fa-edit"></i> {{ __('Edit') }} </a>
        </div>

        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li><a href="{{URL::route('library.book.index')}}"><i class="fa icon-book"></i> {{ __('book') }} </a></li>
            <li class="active"> {{ __('View') }} </li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content main-contents">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-body">
                        <div id="information">
                            <p class="text-info" style="font-size: 16px;border-bottom: 1px solid #eee;"> {{ __('Book Details:') }} </p>
                            <div class="row">
                                <div class="col-md-3">
                                    <label for=""> {{ __('ISBN No.') }} </label>
                                </div>
                                <div class="col-md-3">
                                    <p for="">: {{$book->code}}</p>
                                </div>
                                <div class="col-md-3">
                                    <label for=""> {{ __('Name') }} </label>
                                </div>
                                <div class="col-md-3">
                                    <p for="">: {{$book->name}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label for=""> {{ __('Author') }} </label>
                                </div>
                                <div class="col-md-3">
                                    <p for="">: {{$book->author}}</p>
                                </div>
                                <div class="col-md-3">
                                    <label for=""> {{ __('Type') }} </label>
                                </div>
                                <div class="col-md-3">
                                    <p for="">: {{$book->type}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label for=""> {{ __('Class') }} </label>
                                </div>
                                <div class="col-md-3">
                                    <p for="">: {{$class}}</p>
                                </div>
                                <div class="col-md-3">
                                    <label for=""> {{ __('Quantity') }} </label>
                                </div>
                                <div class="col-md-3">
                                    <p for="">: {{$book->qty}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label for=""> {{ __('Stock Quantity') }} </label>
                                </div>
                                <div class="col-md-3">
                                    <p for="">: {{$book->stockQty}}</p>
                                </div>
                                <div class="col-md-3">
                                    <label for=""> {{ __('Rack No.') }} </label>
                                </div>
                                <div class="col-md-3">
                                    <p for="">: {{$book->rack_no}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label for=""> {{ __('Row No.') }} </label>
                                </div>
                                <div class="col-md-3">
                                    <p for="">: {{$book->row_no}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label for=""> {{ __('Description') }} </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <p for="">{{$book->description}}</p>
                                </div>
                            </div>

                            <p class="text-info" style="font-size: 16px;border-bottom: 1px solid #eee;"> {{ __('File Attachment:') }} </p>
                            @can('library.book.show')
                                <div class="row">
                                    <div class="col-md-12">
                                        @if($book && $attachfile->count())
                                            @foreach($attachfile as $file_name)
                                                <div class="file_attach">
                                                    <a target="_blank" href="{{asset("storage/book").'/'.$file_name->path}}">{{$file_name->original_name}}</a>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            @endcan
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
        });
    </script>
@endsection
<!-- END PAGE JS-->
