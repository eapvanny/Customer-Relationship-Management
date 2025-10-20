@extends('backend.layouts.master')

@section('pageTitle')
    Region Management
@endsection

@section('pageContent')
<!-- Section header -->
<section class="content-header">
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }}</a></li>
        <li class="active">{{ __('Region Management') }}</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="wrap-outter-header-title">
                 <h4>
                    {{ __('Region List') }}
                </h4>
                <div class="action-btn-top none_fly_action_btn">
                    <a href="{{ route('region.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus-circle"></i>
                        {{ __('Add New') }}
                    </a>
                </div>
            </div>
            <div class="wrap-outter-box">
                <div class="box box-info">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-12">
                                <a class="btn btn-success btn-sm" href="{{ route('region.export') }}"><i
                                        class="fa-solid fa-download"></i> {{ __('Export') }}</a>
                            </div>
                        </div>
                        <div class="table-responsive mt-4">
                            <table id="datatable" class="table table-bordered table-striped list_view_table display responsive no-wrap datatable-server"
                                    width="100%">
                                <thead>
                                    <tr>
                                        <th>{{__("No")}}</th>
                                        <th>{{ __('Region Name') }}</th>
                                        <th>{{__("SD's Name")}}</th>
                                        <th>{{__("SM's Name")}}</th>
                                        <th>{{ __('Region Mananger') }}</th>
                                        <th>{{ __('SE Code') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Create By') }}</th>
                                        <th style="max-width: 82px">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($regions as $key => $item)
                                    <tr>
                                        <th>{{$key+1}}</th>
                                        <td>{{$item->region_name}}</td>
                                        <td>{{$item->sd_name}}</td>
                                        <td>{{$item->sm_name}}</td>
                                        <td>{{$item->rg_manager_kh . ' - ' . $item->rg_manager_en}}</td>
                                        <td>{{$item->se_code}}</td>

                                        <td>
                                            <p class="badge {{ $item->active_status == 1 ? 'text-bg-primary' : 'text-bg-danger' }}">{{ $item->active_status == 1 ? __("Active") : __("Inactive") }}</p>
                                        </td>
                                        <td class="text-start">
                                            @if (auth()->user()->user_lang == 'en')
                                                {{$item->user->family_name_latin . ' '. $item->user->name_latin}}
                                            @else
                                                {{$item->user->family_name . ' '. $item->user->name}}
                                            @endif
                                            <span class="d-block text-muted">
                                                {{ Carbon\Carbon::parse($item->created_at)->format('d-m-Y h:i:s A') }}
                                            </span>
                                        </td>
                                        <td>
                                            @hasTypePermission('update region')
                                                <a href="{{ route('region.edit', $item->id) }}"><i class="fa fa-edit"></i></a>
                                            @else
                                                -
                                            @endHasTypePermission
                                        </td>
                                    </tr>
                                    @endforeach

                                </tbody>
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
        $('#datatable').DataTable();

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
