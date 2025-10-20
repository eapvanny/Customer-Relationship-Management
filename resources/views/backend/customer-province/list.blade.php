@extends('backend.layouts.master')

@section('pageTitle')
    {{ __('Customers Management (Province)') }}
@endsection

@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }}</a></li>
            <li class="active">{{ __('Customer Management (Province)') }}</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="wrap-outter-header-title">
                    <h4>
                        {{ __('Customer List (Province)') }}
                    </h4>
                    <div class="action-btn-top none_fly_action_btn">
                        <a href="{{ route('cp.create') }}" class="btn btn-primary">
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
                                    <a class="btn btn-success btn-sm" href="{{ route('cp.export') }}"><i
                                            class="fa-solid fa-download"></i> {{ __('Export') }}</a>
                                </div>
                            </div>
                            <div class="table-responsive mt-4">
                                <table id="datatable"
                                    class="table table-bordered table-striped list_view_table display responsive no-wrap datatable-server"
                                    width="100%">
                                    <thead>
                                        <tr>
                                            <th>{{ __('No') }}</th>
                                            <th>{{ __('Customer Code') }}</th>
                                            <th>{{ __('Region') }}</th>
                                            <th>{{ __('Outlet') }}</th>
                                            <th>{{ __('Customer Name') }}</th>
                                            <th>{{ __('Customer Type') }}</th>
                                            <th>{{ __('Phone') }}</th>
                                            <th>{{ __('Created by') }}</th>
                                            <th style="max-width: 82px">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($customers as $key => $item)
                                            <tr>
                                                <th>{{ $key + 1 }}</th>
                                                <td class="text-start">{{ $item->code }}</td>
                                                <td class="text-start">{{ $item->region->region_name . ' - ' . $item->region->se_code }}</td>
                                                <td class="text-start">{{ $item->outlet->name }}</td>
                                                <td class="text-start">{{ $item->name }}</td>
                                                <td class="text-start">{{ \App\Http\Helpers\AppHelper::CUSTOMER_TYPE_PROVINCE[$item->customer_type] ?? 'N/A' }}
                                                </td>
                                                <td class="text-start">{{ $item->phone }}</td>
                                                <td class="text-start">
                                                    @if (auth()->user()->user_lang == 'en')
                                                        {{ $item->user->family_name_latin . ' ' . $item->user->name_latin }}
                                                    @else
                                                        {{ $item->user->family_name . ' ' . $item->user->name }}
                                                    @endif
                                                    <span
                                                        class="text-muted d-block">{{ Carbon\Carbon::parse($item->created_at)->format('d-m-Y h:i:s A') }}</span>
                                                </td>
                                                <td>
                                                    @hasTypePermission('update customer province')
                                                        <a href="{{ route('cp.edit', $item->id) }}"><i
                                                                class="fa fa-edit"></i></a>
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
            const table = $('#datatable').DataTable();
        });
    </script>
@endsection
