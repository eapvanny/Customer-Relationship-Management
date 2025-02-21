<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Product @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')

    <!-- Section header -->
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li> {{ __('Sale Item') }} </li>
            <li><a href="{{URL::route('saleitem.product.index')}}"> {{ __('Product') }} </a></li>
            <li class="active"> {{ __('View') }} </li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content main-contents">
            <div class="row">
                <div class="col-md-12">
                    <div class="wrap-outter-header-title">
                        <h1>
                                {{ __('Product') }}
                                <small> {{ __('Details') }} </small>
                            </h1>
                        <div class="box-tools pull-right">
                            <div class="btn-group">
                                {{-- @if($product->created_by==auth()->user()->id) --}}
                                <a href="{{URL::route('saleitem.product.edit',$product->id)}}" class="btn-pr btn-sm-pr"><i class="fa fa-edit"></i> {{ __('Edit') }} </a>
                                {{-- @endif --}}
                            </div>
                        </div>
                    </div>
                    <div class="wrap-outter-box">
                        {{-- <div id="printableArea">
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
                                                            <p>: {{$product->name}}</p>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for=""> {{ __('Code') }} </label>
                                                    </div>
                                                    <div class="col-md-3">
                                                            <p>: {{$product->code}}</p>
                                                    </div>
                                                </div>
                                                <div class="row">

                                                    <div class="col-md-3">
                                                        <label for=""> {{ __('Status') }} </label>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <p for="">: {{($product->status==1)?"Active":"Deactive"}}</p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label for=""> {{ __('Description.') }} </label>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <p for="">: {{$product->description}}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="box box-info">
                                    <div class="box-body box-profile">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <ul class="list-group list-group-unbordered">
                                                    <li class="list-group-item" style="background-color: #FFF;">
                                                        <strong>{{ __('Name') }}:</strong>
                                                        <span class="pull-right text-secondary ms-2 ">@if(app()->currentLocale() == ' kh'){{ $product->name}}@else{{ $product->name_latin}}@endif</span>
                                                    </li>
                                                    <li class="list-group-item" style="background-color: #FFF;">
                                                        <strong>{{ __('Code') }}:</strong>
                                                        <span class="pull-right text-secondary ms-2 ">{{ $product->code}}</span>
                                                    </li>
                                                    <li class="list-group-item" style="background-color: #FFF;">
                                                        <strong>{{ __('Category') }}:</strong>
                                                        <span class="pull-right text-secondary ms-2 ">{{ __(AppHelper::PRODUCT_CATEGORIES[$product->category])}}</span>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-lg-6">
                                                <ul class="list-group list-group-unbordered">
                                                    <li class="list-group-item" style="background-color: #FFF;">
                                                        <strong>{{ __('Status') }}:</strong>
                                                        <span class="pull-right text-secondary ms-2 ">
                                                            {{($product->status==1)? __('Active'): __('Deactive')}}
                                                        </span>
                                                    </li>
                                                    <li class="list-group-item" style="background-color: #FFF;">
                                                        <strong>{{ __('Description') }}:</strong>
                                                        <span class="pull-right text-secondary ms-2 ">{{ $product->description}}</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-major" type="button" role="tab" aria-controls="tab-major" aria-selected="true">{{ __('Major') }}</button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link d-none" data-bs-toggle="tab" data-bs-target="#tab-grade-level" type="button" role="tab" aria-controls="tab-grade-level" aria-selected="true">{{ __('Grade Levels') }}</button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" id="tab-fee" data-bs-toggle="tab" data-bs-target="#tab-fee-type" type="button" role="tab" aria-controls="tab-fee-type" aria-selected="false">{{ __('Fee Types') }}</button>
                                    </li>
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane py-4 fade show active" id="tab-major" role="tabpanel" aria-labelledby="tab-major">
                                        @include('backend.sale_item.product.tabs.major',['productGradeLevels' => $productGradeLevels])
                                    </div>
                                    <div class="tab-pane py-4 fade show" id="tab-grade-level" role="tabpanel" aria-labelledby="tab-grade-level">
                                        @include('backend.sale_item.product.tabs.grade_level',['productGradeLevels' => $productGradeLevels])
                                    </div>
                                    <div class="tab-pane py-4 fade" id="tab-fee-type" role="tabpanel" aria-labelledby="tab-fee-type">
                                        @include('backend.sale_item.product.tabs.fee_types',['feeTypes' => $feeTypes])
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
            var table = $('#addGradeLevelTable').DataTable({
                pageLength: 25,
                lengthChange: false,
                responsive: true,
            });
            var table = $('#addMajorTable').DataTable({
                pageLength: 25,
                lengthChange: false,
                responsive: true,
            });
            var productGlTable = $('#productGLTable').DataTable({
                pageLength: 25,
                lengthChange: false,
                responsive: true,
            });
            var productGlTable = $('#productMJTable').DataTable({
                pageLength: 25,
                lengthChange: false,
                responsive: true,
            });
            var feeTypeTable = $('#feeTypeTable').DataTable({
                pageLength: 25,
                lengthChange: false,
                responsive: true,
            });

        });
        $(document).on('submit', '.form-delete-gl', function (e) {
            e.preventDefault();
            var that = this;
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
                    that.submit();
                }
            });
        });
        $(document).on('submit', '.form-delete-feetype', function (e) {
            e.preventDefault();
            var that = this;
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
                    that.submit();
                }
            });
        });

        $(document).on('click', '.set-main-fee', function(e) {
            e.preventDefault(); // Prevent the default anchor click behavior

            var feeId = $(this).data('id'); // Get the ID from the button

            $.ajax({
                url: "{{ route('fee_type.set_main_fee', '') }}/" + feeId, // Construct the URL
                type: 'GET', // Use the appropriate HTTP method (POST)
                data: {
                    feeId:feeId
                },
                success: function(response) {
                    if (response.success) {
                            response.feeTypes.forEach(function(feeType) {
                                var button = $('.set-main-fee[data-id="' + feeType.id + '"]');

                                if (feeType.is_main) {
                                    button.replaceWith('<span class="main-fee-text">{{ __('Main Fee') }}</span>');
                                } else {
                                    // For non-main fee types, update button text accordingly
                                    if (button.length) {
                                        button.text('{{ __('Set Main Fee') }}').prop('disabled', false);
                                    } else {
                                        // If the button was replaced, create a new button
                                        var newButton = $('<a href="#" class="btn btn-primary py-1 px-3 btn-sm set-main-fee" data-id="' + feeType.id + '">' + '{{ __('Set Main Fee') }}' + '</a>');
                                        $(button).after(newButton); // Insert the new button after the span
                                    }
                                }
                            });
                            $('#feeTypeTables').DataTable().ajax.reload();
                    } else {
                        alert('Error:');
                    }
                },
                error: function(xhr) {
                    
                }
            });
        });


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            var isDataTableInitialized = false;

            // Function to initialize DataTable
            function initializeDataTable() {
                // Destroy existing instance if it exists
                if ($.fn.DataTable.isDataTable('#feeTypeTables')) {
                    $('#feeTypeTables').DataTable().clear().destroy();
                }

                // Initialize DataTable
                var table = $('#feeTypeTables').DataTable({
                    processing: true,
                    serverSide: true,
                    bLengthChange: true,
                    ajax: {
                        url: "{{ route('feetype.product') }}", // Your route for fetching data
                        data: function(d) {
                            d.product_id = {{ $product->id }}; // Always include the product ID
                            d.academic_year_id = $('#academic_year_id').val(); 
                        }
                    },
                    columns: [
                        { data: 'id', name: 'id' },
                        { data: 'code', name: 'code' },
                        { data: 'name', name: 'name' },
                        { data: 'amount', name: 'amount' },
                        { data: 'academic_year', name: 'academic_year' },
                        { data: 'customer_nationality', name: 'customer_nationality', orderable: false },
                        { data: 'main_fee', name: 'main_fee' },
                        { data: 'status', name: 'status' },
                        { data: 'actions', name: 'actions', orderable: false }
                    ],
                    language: {
                        emptyTable: "No fee types available."
                    }
                });

                isDataTableInitialized = true; // Set the flag to true after initialization
            }

            // Event handler for the tab click
            $('#tab-fee').on('click', function() {
                // Initialize DataTable only if it hasn't been initialized yet
                if (!isDataTableInitialized) {
                    initializeDataTable();
                } else {
                    // Optionally reload data if needed
                    $('#feeTypeTables').DataTable().ajax.reload(); // Reloads the existing DataTable
                }
            });
        });


        $('#academic_year_id').change(function() {
            var academicYearId = $(this).val(); // Get the selected academic year ID
            if (academicYearId) {
                $.ajax({
                    url: "{{ route('feetype.product') }}", // The route to handle the request
                    method: "GET",
                    data: {
                        product_id: {{ $product->id }}, // Include the product ID
                        academic_year_id: academicYearId // Include the selected academic year ID
                    },
                    success: function(response) {
                        if (response.data && response.data.length > 0) {
                            var table = $('#feeTypeTables').DataTable();
                            table.clear().rows.add(response.data).draw();
                        } else {
                            // Clear the table if no data is found
                            var table = $('#feeTypeTables').DataTable();
                            table.clear().draw(); // Clear the DataTable if no records found
                        }
                    },
                    error: function(xhr) {
                    }
                });
            } else {
                $('#feeTypeTables').DataTable().ajax.reload();
            }
        });
    </script>
@endsection
<!-- END PAGE JS-->
