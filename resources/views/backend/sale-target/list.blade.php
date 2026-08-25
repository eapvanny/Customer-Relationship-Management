@extends('backend.layouts.master')

@section('pageTitle')
    Target List
@endsection

@section('pageContent')
<!-- Section header -->
<section class="content-header">
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }}</a></li>
        <li class="active">{{ __('Target') }}</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="wrap-outter-header-title">
                 <h4>
                    {{ __('Target List') }}
                </h4>
                <div class="action-btn-top none_fly_action_btn">
                    <button id="filters" class="btn btn-outline-secondary" data-bs-toggle="collapse"
                        data-bs-target="#filterContainer">
                        <i class="fa-solid fa-filter"></i> {{ __('Filter') }}
                    </button>
                    {{-- @hasTypePermission('create target')
                        <a href="{{ route('target.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus-circle"></i> 
                            {{ __('Add New') }}
                        </a>
                    @endHasTypePermission --}}
                </div>
            </div>
            <div class="wrap-outter-box">
                <div class="box box-info">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <form action="{{ route('target.index') }}" method="GET" id="filterForm">
                                    <div class="wrap_filter_form @if (!$is_filter) collapse @endif"
                                        id="filterContainer">
                                        <a id="close_filter" class="btn btn-outline-secondary btn-sm">
                                            <i class="fa-solid fa-xmark"></i>
                                        </a>
                                        <div class="row">
                                            <div class="col-xl-4">
                                                <div class="form-group">
                                                    <label for="date1">{{ __('From Date') }}</label>
                                                    <input type="date" name="date1" id="date1"
                                                        class="form-control" value="{{ request('date1') }}">
                                                </div>
                                            </div>
                                            <div class="col-xl-4">
                                                <div class="form-group">
                                                    <label for="date2">{{ __('To Date') }}</label>
                                                    <input type="date" name="date2" id="date2"
                                                        class="form-control" value="{{ request('date2') }}">
                                                </div>
                                            </div>
                                            <div class="col-xl-4">
                                                <div class="form-group">
                                                    <label for="user_id">{{ __('Filter By Employee') }}</label>
                                                    {!! Form::select('user_id', $employees, request('user_id'), [
                                                        'placeholder' => __('Select employee'),
                                                        'id' => 'user_id',
                                                        'class' => 'form-control select2',
                                                    ]) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 mt-2">
                                                <button id="apply_filter"
                                                    class="btn btn-outline-secondary btn-sm float-end" type="submit">
                                                    <i class="fa-solid fa-magnifying-glass"></i> {{ __('Apply') }}
                                                </button>
                                                <a href="{{ route('target.index') }}"
                                                    class="btn btn-outline-secondary btn-sm float-end me-1">
                                                    <i class="fa-solid fa-xmark"></i> {{ __('Cancel') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            {{-- <div class="col-12">
                                <a class="btn btn-success btn-sm" href="{{ route('customer.export') }}"><i
                                        class="fa-solid fa-download"></i> {{ __('Export') }}</a>
                            </div> --}}
                        </div>
                        <div class="table-responsive mt-4">
                            <table id="datatable" class="table table-bordered table-striped list_view_table display responsive no-wrap datatable-server"
                                    width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Employee') }}</th>
                                        <th>{{ __('Actual Sales') }}</th>
                                        <th>{{ __("Rank") }}</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="btn-group">
                        <form id="myAction" method="POST">
                            @csrf
                            <input name="_method" type="hidden" value="DELETE">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('extraScript')
<script>
    $(document).ready(function() {
        const table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,   // IMPORTANT
            ajax: {
                url: "{!! route('target.index', Request::query()) !!}",
                type: "GET",
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                data: function(d) {
                    d.search_value = d.search.value;
                    d.date1 = "{{ request('date1') }}";
                    d.date2 = "{{ request('date2') }}";
                    d.user_id = "{{ request('user_id') }}";
                },
                error: function(xhr, error, thrown) {
                    console.log('AJAX Error:', xhr.responseText);
                }
            },
            pageLength: 10,     // 10 per page
            lengthMenu: [10, 25, 50, 100],

            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'employee', name: 'employee' },
                { data: 'sale_target', name: 'sale_target' },
                { data: 'rank', name: 'rank' },
            ]
        });
        // Close filter panel
        $('#close_filter').click(function() {
            $("#filters").trigger('click');
        });

        // Delete customer
        $('#datatable').on('click', '.delete', function(e) {
            e.preventDefault();
            let action = $(this).attr('href');

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
                    // Create a form dynamically to submit DELETE request
                    let form = $('<form>', {
                        'method': 'POST',
                        'action': action
                    });

                    form.append(
                        $('<input>', {
                            'type': 'hidden',
                            'name': '_method',
                            'value': 'DELETE'
                        }),
                        $('<input>', {
                            'type': 'hidden',
                            'name': '_token',
                            'value': '{{ csrf_token() }}'
                        })
                    );

                    $('body').append(form);
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
