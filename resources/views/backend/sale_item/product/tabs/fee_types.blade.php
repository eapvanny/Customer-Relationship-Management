<div class="box-header with-border ps-0 mb-3">
    <h3 class="box-title">{{ __('Fee Types') }} </h3>
    <div class="box-tools pull-right">
        @can('feetype.store')
            <a class="btn btn-primary" href="{{ URL::route('feetype.create', ['product_id' => $product->id]) }}"><i class="fa-regular fa-plus"></i>{{__('Add')}}</a>
        @endcan

    </div>
   
</div>
<div class="row mb-4">
    <div class="col-xl-3">
        <div class="form-group has-feedback">
            <x-backend.academic-year-select name="academic_year_id" value="{{ request()->academic_year_id }}" id="academic_year_id" placeholder="Please Choose"/>
        </div>
    </div>
</div>
@php
    $latin = app()->currentLocale() == 'kh' ? '' : '_in_latin';
@endphp
<table class="table table-bordered dataTable no-footer dtr-inline" style="width: 100%;" id="feeTypeTables">
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="10%"> {{ __('Code') }} </th>
            <th> {{ __('Name') }} </th>
            <th width="10%"> {{ __('Amount') }} </th>
            <th width="10%"> {{ __('Academic Year') }} </th>
            <th width="10%"> {{ __('Student Type') }} </th>
            <th width="10%"> {{ __('Main Fee')}}</th>
            <th width="10%"> {{ __('Status') }} </th>
            <th class="notexport" width="10%"> {{ __('Action') }} </th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
