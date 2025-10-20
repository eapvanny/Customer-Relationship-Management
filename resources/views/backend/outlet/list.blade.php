@extends('backend.layouts.master')

@section('pageTitle')
    Outlet
@endsection

@section('bodyCssClass')
@endsection

@php
    use App\Http\Helpers\AppHelper;
@endphp

@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }}</a></li>
            <li class="active">{{ __('Outlet Management') }}</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="wrap-outter-header-title">
                    <h4>{{ __('Outlet List') }}</h4>
                    <div class="box-tools pull-right">
                        <a class="btn btn-info text-white" href="{{ URL::route('outlet.create') }}"><i
                                class="fa fa-plus-circle"></i> {{ __('Add New') }}</a>
                    </div>
                </div>
                <div class="wrap-outter-box">
                    <div class="box box-info">
                        <div class="box-header"></div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="table-responsive mt-3">
                                <table id="datatabble"
                                    class="table table-bordered table-striped list_view_table display responsive no-wrap datatable-server"
                                    width="100%">
                                    <thead>
                                        <tr>
                                            <th>{{ __('#') }}</th>
                                            <th>{{ __('Region') }}</th>
                                            <th>{{ __('Outlet Name') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Created by') }}</th>
                                            <th class="notexport" style="min-width: 90px;">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($outlets as $key => $item)
                                            <tr>
                                                <th>{{ $key + 1 }}</th>
                                                <td class="text-start">{{ $item->region->region_name }} @if (auth()->user()->user_lang == 'en')
                                                        {{ $item->region->rg_manager_en }}
                                                    @else
                                                        {{ $item->region->rg_manager_kh }}
                                                    @endif {{ '( ' . $item->region->se_code . ' )' }}
                                                </td>
                                                <td class="text-start">{{ $item->name }}</td>
                                                <td class="text-start">
                                                    <p class="badge {{ $item->active_status == 1 ? 'text-bg-primary' : 'text-bg-danger' }}">{{ $item->active_status == 1 ? __("Active") : __("Inactive") }}</p>
                                                </td>
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
                                                    @hasTypePermission('edit depot management')
                                                        <a href="{{ route('outlet.edit', $item->id) }}"><i
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
        </div>
    </section>
    <!-- /.content -->
@endsection

@section('extraScript')
    <script type="text/javascript">
        $(document).ready(function() {
            Generic.initCommonPageJS();
            Generic.initDeleteDialog();
            window.filter_org = 1;
            Generic.initFilter();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#datatabble').dataTable();
        });
    </script>
@endsection
