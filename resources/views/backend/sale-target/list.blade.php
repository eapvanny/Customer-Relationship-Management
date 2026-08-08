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
                {{-- <div class="action-btn-top none_fly_action_btn">
                    @hasTypePermission('create target')
                        <a href="{{ route('target.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus-circle"></i> 
                            {{ __('Add New') }}
                        </a>
                    @endHasTypePermission
                </div> --}}
            </div>
            <div class="wrap-outter-box">
                <div class="box box-info">
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
                                        <th>{{ __('Sales Target') }}</th>
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
                type: "GET"
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
