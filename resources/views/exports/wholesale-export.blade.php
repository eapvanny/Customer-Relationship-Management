<h1>{{ $title }}</h1>
<p>Export Date : {{ now() }}</p>
<table id="datatabble"
    class="table table-bordered table-striped list_view_table display responsive no-wrap datatable-server">
    <thead>
        <tr>
            {{-- <th>{{ __('No') }}</th> --}}
            {{-- <th> {{ __('Staff Info') }} </th> --}}
            <th>{{ __('region') }}</th>
            <th>{{ __('province')}}</th>
            <th>{{ __('district')}}</th>
            <th>{{ __('commune')}}</th>
            <th>{{ __('sm_name') }}</th>
            <th>{{ __('rsm_name') }}</th>
            <th>{{ __('asm_name') }}</th>
            <th>{{ __('se_name') }}</th>
            {{-- <th>{{ __('SUP Name') }}</th> --}}
            <th>{{ __('se_code') }}</th>
            <th>{{ __('customer_code') }}</th>
            <th>{{ __('depot_contact') }}</th>
            <th>{{ __('depot_name') }}</th>
            {{-- <th>{{ __('retail_name') }}</th>
            <th>{{ __('retail_contact') }}</th> --}}
            <th>{{ __('outlet_type') }}</th>
            <th>{{ __('sale_kpi') }}</th>
            <th>{{ __('display_qty') }}</th>
            <th>{{ __('sku') }}</th>
            <th>{{ __('incentive') }}</th>
            {{-- <th>{{ __('Location') }}</th> --}}
            <th>{{ __('remark') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reports as $key => $item)
            <tr>
                {{-- <th>{{ $key + 1 }}</th>
                <td>
                    <p>ID: {{ $item->user->staff_id_card }}</p>
                    <p>{{ $item->user->family_name . ' ' . $item->user->name }}</p>
                </td> --}}
                <td> {{ $item->region }} </td>
                <td>{{ $item->province }}</td>
                <td>{{ $item->district }}</td>
                <td>{{ $item->commune }}</td>
                <td>{{ $item->sm_name }}</td>
                <td>{{ $item->rsm_name }}</td>
                <td>{{ $item->asm_name }}</td>
                {{-- <td>{{ $item->sup_name }}</td> --}}
                <td>{{ $item->se_name }}</td>
                <td>{{ $item->se_code }}</td>
                <td>{{ $item->customer_code }}</td>
                <td>{{ $item->depot_contact }}</td>
                <td>{{ $item->depot_name }}</td>
                {{-- <td>{{ $item->wholesale_name }}</td>
                <td>{{ $item->wholesale_contact }}</td> --}}
                <td>{{ $item->outlet_type }}</td>
                <td>{{ $item->sale_kpi }}</td>
                <td>{{ $item->display_qty }}</td>
                <td>{{ $item->sku }}</td>
                <td>{{ $item->incentive }}</td>
                {{-- <td>{{ $item->location }}</td> --}}
                <td>{{ $item->remark }}</td>
            </tr>
        @endforeach

    </tbody>
</table>
