@extends('backend.layouts.master')

@section('pageTitle')
    POSM Management
@endsection

@section('pageContent')
<!-- Section header -->
<section class="content-header">
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }}</a></li>
        <li class="active">{{ __('POSM Management') }}</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="wrap-outter-header-title">
                 <h4>
                    {{ __('POSM List') }}
                </h4>
                <div class="action-btn-top none_fly_action_btn">
                    <a href="{{ route('posm.create') }}" class="btn btn-primary">
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
                                <a class="btn btn-success btn-sm" href="{{ route('posm.export') }}"><i
                                        class="fa-solid fa-download"></i> {{ __('Export') }}</a>
                            </div>
                        </div>
                        <div class="table-responsive mt-4">
                            <table id="datatable" class="table table-bordered table-striped list_view_table display responsive no-wrap datatable-server"
                                    width="100%">
                                <thead>
                                    <tr>
                                        <th>{{__("No")}}</th>
                                        <th>{{ __('POSM Code') }}</th>
                                        <th>{{ __('POSM (KH)') }}</th>
                                        <th>{{ __('POSM (EN)') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Create By') }}</th>
                                        <th style="max-width: 82px">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($posms as $key => $item)
                                    <tr>
                                        <th>{{$key+1}}</th>
                                        <td class="text-start">{{ $item->code ?? 'N/A'}}</td>
                                        <td class="text-start">{{ $item->name_kh}}</td>
                                        <td class="text-start">{{ $item->name_en}}</td>
                                        <td>
                                            <p class="badge {{ $item->status == 1 ? 'text-bg-primary' : 'text-bg-danger' }}">{{ $item->status == 1 ? __("Active") : __("Inactive") }}</p>
                                        </td>
                                        <td class="text-start">
                                            @if (auth()->user()->user_lang == 'en')
                                                {{$item->creator->family_name_latin . ' '. $item->creator->name_latin}}
                                            @else
                                                {{$item->creator->family_name . ' '. $item->creator->name}}
                                            @endif
                                            <span class="d-block text-muted">
                                                {{ Carbon\Carbon::parse($item->created_at)->format('d-M-Y h:i:s A') }}
                                            </span>
                                        </td>
                                        <td>
                                            @hasTypePermission('update posm')
                                                <a href="{{ route('posm.edit', $item->id) }}"><i class="fa fa-edit"></i></a>
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
