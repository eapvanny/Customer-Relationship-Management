<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="wrap-outter-header-title">
                <h1>
                    {{ __($title) }}
                    <small> {{ __('List') }} </small>
                </h1>
                <div class="box-tools pull-right">
                    <button id="filters" class="btn btn-outline-secondary @if(!$isFilter){{'collapsed'}}@endif d-none" data-bs-toggle="collapse" aria-expanded="@if($isFilter){{ 'true' }}@else{{'false'}}@endif" data-bs-target="#filterContainer" >
                        <i class="fa-solid fa-filter"></i> {{ __('Filter') }}
                    </button>
                    {{ $topButtonContainer }}

                </div>
            </div>
            <div class="wrap-outter-box">
                <div class="box box-info">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <form action="{{ $filterFormAction }}" method="GET" id="filterForm">

                                    <div class="wrap_filter_form collapse @if($isFilter) {{ 'show' }} @endif" id="filterContainer">
                                        <a id="close_filter" href="{{ $filterFormAction }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="fa-solid fa-xmark"></i>
                                        </a>
                                        <div class="row">
                                            <div class="">
                                                <div class="row">
                                                    {{ $searchContainer }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 mt-2">
                                                @if ($displayFilterButtons)
                                                <button id="apply_filter" class="btn btn-outline-secondary btn-sm float-end" type="submit">
                                                    <i class="fa-solid fa-magnifying-glass"></i> {{ __('Apply') }}
                                                </button>
                                                <a href="{{ $filterFormAction }}" class="btn btn-outline-secondary btn-sm float-end me-1">
                                                    <i class="fa-solid fa-xmark"></i> {{ __('Cancel') }}
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>

                    <!-- /.box-header -->
                    <div class="box-body">

                        {{ $slot }}


                    </div>
                    <!-- /.box-body -->
                </div>
            </div>


        </div>
    </div>

</section>
